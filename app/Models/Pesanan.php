<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Helper;
use PDOException;

class Pesanan {
    public static function all(?string $status = null, ?string $keyword = null): array {
        $sql = "SELECT ps.*, p.nama_pelanggan, p.no_telepon, p.alamat, a.nama_anggota,
                (SELECT COUNT(*) FROM detail_pesanan d WHERE d.id_pesanan = ps.id_pesanan) AS total_item,
                (SELECT GROUP_CONCAT(l.nama_layanan SEPARATOR ', ') 
                 FROM detail_pesanan dp 
                 JOIN layanan l ON dp.id_layanan = l.id_layanan 
                 WHERE dp.id_pesanan = ps.id_pesanan) AS daftar_layanan,
                COALESCE((SELECT SUM(dp.jumlah) FROM detail_pesanan dp WHERE dp.id_pesanan = ps.id_pesanan), 1) AS total_berat,
                COALESCE((SELECT l.satuan FROM detail_pesanan dp JOIN layanan l ON dp.id_layanan = l.id_layanan WHERE dp.id_pesanan = ps.id_pesanan LIMIT 1), 'kg') AS satuan,
                (SELECT mp.nama_metode FROM pembayaran pay JOIN metode_pembayaran mp ON pay.id_metode = mp.id_metode WHERE pay.id_pesanan = ps.id_pesanan ORDER BY pay.tanggal_pembayaran DESC LIMIT 1) AS metode_pembayaran
                FROM pesanan ps
                JOIN pelanggan p ON ps.id_pelanggan = p.id_pelanggan
                LEFT JOIN anggota a ON ps.id_anggota = a.id_anggota
                WHERE 1=1";
        
        $params = [];

        if ($status && $status !== 'Semua') {
            $sql .= " AND ps.status_cucian = :status";
            $params['status'] = $status;
        }

        if ($keyword) {
            $sql .= " AND (ps.id_pesanan LIKE :kw OR p.nama_pelanggan LIKE :kw OR p.no_telepon LIKE :kw)";
            $params['kw'] = '%' . $keyword . '%';
        }

        $sql .= " ORDER BY ps.tanggal_masuk DESC";

        return Database::query($sql, $params);
    }

    public static function findById(string $id): ?array {
        $sql = "SELECT ps.*, p.nama_pelanggan, p.no_telepon, p.alamat, a.nama_anggota
                FROM pesanan ps
                JOIN pelanggan p ON ps.id_pelanggan = p.id_pelanggan
                LEFT JOIN anggota a ON ps.id_anggota = a.id_anggota
                WHERE ps.id_pesanan = :id";
        return Database::fetch($sql, ['id' => $id]);
    }

    public static function findWithDetails(string $id): ?array {
        $order = self::findById($id);
        if (!$order) {
            return null;
        }

        // Ambil detail item layanan
        $sqlItems = "SELECT dp.*, l.nama_layanan, l.satuan, l.estimasi_waktu
                     FROM detail_pesanan dp
                     JOIN layanan l ON dp.id_layanan = l.id_layanan
                     WHERE dp.id_pesanan = :id";
        $order['items'] = Database::query($sqlItems, ['id' => $id]);

        // Ambil riwayat pembayaran
        $sqlPayments = "SELECT pb.*, mp.nama_metode, a.nama_anggota
                        FROM pembayaran pb
                        JOIN metode_pembayaran mp ON pb.id_metode = mp.id_metode
                        LEFT JOIN anggota a ON pb.id_anggota = a.id_anggota
                        WHERE pb.id_pesanan = :id
                        ORDER BY pb.tanggal_pembayaran ASC";
        $order['pembayaran'] = Database::query($sqlPayments, ['id' => $id]);

        // Ambil riwayat status timeline
        $sqlTimeline = "SELECT rs.*, a.nama_anggota
                        FROM riwayat_status rs
                        LEFT JOIN anggota a ON rs.id_anggota = a.id_anggota
                        WHERE rs.id_pesanan = :id
                        ORDER BY rs.waktu_status ASC";
        $order['timeline'] = Database::query($sqlTimeline, ['id' => $id]);

        return $order;
    }

    /**
     * Membuat pesanan baru dengan transaksi database atomic
     */
    public static function create(array $orderData, array $itemsData, ?array $paymentData = null): ?string {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            $idPesanan = Helper::generateInvoice();

            // Hitung tanggal keluar (deadline) berdasarkan max estimasi waktu layanan
            $maxJam = 24;
            foreach ($itemsData as $it) {
                if (isset($it['estimasi_waktu']) && (int)$it['estimasi_waktu'] > 0) {
                    $maxJam = max($maxJam, (int)$it['estimasi_waktu']);
                }
            }
            if (!empty($orderData['is_kilat'])) {
                $maxJam = min($maxJam, 6); // Kilat max 6 jam
            }
            $tanggalKeluar = date('Y-m-d H:i:s', strtotime("+{$maxJam} hours"));

            // 1. Insert Pesanan
            $sqlOrder = "INSERT INTO pesanan (
                            id_pesanan, tanggal_masuk, tanggal_keluar, status_cucian, 
                            total_harga, status_pembayaran, lokasi_rak, catatan_khusus, 
                            is_kilat, id_pelanggan, id_anggota
                         ) VALUES (
                            :id, CURRENT_TIMESTAMP, :tgl_keluar, :status_cucian,
                            :total, :status_bayar, :rak, :catatan,
                            :kilat, :pelanggan, :anggota
                         )";
            
            $stmt = $pdo->prepare($sqlOrder);
            $stmt->execute([
                'id'            => $idPesanan,
                'tgl_keluar'    => $tanggalKeluar,
                'status_cucian' => $orderData['status_cucian'] ?? 'Antrean',
                'total'         => $orderData['total_harga'],
                'status_bayar'  => $orderData['status_pembayaran'] ?? 'Belum Lunas',
                'rak'           => $orderData['lokasi_rak'] ?? null,
                'catatan'       => $orderData['catatan_khusus'] ?? '',
                'kilat'         => !empty($orderData['is_kilat']) ? 1 : 0,
                'pelanggan'     => $orderData['id_pelanggan'],
                'anggota'       => $orderData['id_anggota'] ?? null
            ]);

            // 2. Insert Detail Pesanan
            $sqlDetail = "INSERT INTO detail_pesanan (id_detail, jumlah, harga_satuan, id_pesanan, id_layanan)
                          VALUES (:id_det, :jml, :hrg, :id_pes, :id_lay)";
            $stmtDet = $pdo->prepare($sqlDetail);

            foreach ($itemsData as $item) {
                $idDet = Helper::generateId('DET-', 'detail_pesanan', 'id_detail', 3);
                $stmtDet->execute([
                    'id_det' => $idDet,
                    'jml'    => $item['jumlah'],
                    'hrg'    => $item['harga_satuan'],
                    'id_pes' => $idPesanan,
                    'id_lay' => $item['id_layanan']
                ]);
            }

            // 3. Insert Pembayaran (jika langsung bayar)
            if ($paymentData && !empty($paymentData['jumlah_bayar']) && (float)$paymentData['jumlah_bayar'] > 0) {
                $idPay = Helper::generateId('PAY-', 'pembayaran', 'id_pembayaran', 3);
                $sqlPay = "INSERT INTO pembayaran (id_pembayaran, tanggal_pembayaran, jumlah_bayar, uang_kembalian, id_pesanan, id_metode, id_anggota)
                           VALUES (:id, CURRENT_TIMESTAMP, :bayar, :kembali, :pesanan, :metode, :anggota)";
                $stmtPay = $pdo->prepare($sqlPay);
                $stmtPay->execute([
                    'id'      => $idPay,
                    'bayar'   => $paymentData['jumlah_bayar'],
                    'kembali' => $paymentData['uang_kembalian'] ?? 0,
                    'pesanan' => $idPesanan,
                    'metode'  => $paymentData['id_metode'] ?? 'MET-01',
                    'anggota' => $orderData['id_anggota'] ?? null
                ]);
            }

            // 4. Insert Riwayat Status Awal
            $idRw = Helper::generateId('RW-', 'riwayat_status', 'id_riwayat', 3);
            $sqlRw = "INSERT INTO riwayat_status (id_riwayat, status, waktu_status, keterangan, id_pesanan, id_anggota)
                      VALUES (:id, 'Antrean', CURRENT_TIMESTAMP, 'Pesanan baru diterima di kasir', :pesanan, :anggota)";
            $stmtRw = $pdo->prepare($sqlRw);
            $stmtRw->execute([
                'id'      => $idRw,
                'pesanan' => $idPesanan,
                'anggota' => $orderData['id_anggota'] ?? null
            ]);

            $pdo->commit();
            return $idPesanan;

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Order create error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update status pengerjaan cucian (Antrean -> Dicuci -> Disetrika -> Siap Diambil -> Selesai)
     */
    public static function updateStatus(string $id, string $newStatus, ?string $userId = null, ?string $note = null, ?string $rak = null): bool {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            $updateRakSql = "";
            $params = [
                'id'     => $id,
                'status' => $newStatus
            ];

            if ($rak !== null) {
                if ($rak === '' || $rak === 'clear' || $rak === false) {
                    $updateRakSql = ", lokasi_rak = NULL";
                } else {
                    $updateRakSql = ", lokasi_rak = :rak";
                    $params['rak'] = $rak;
                }
            }

            $updateKeluarSql = "";
            if ($newStatus === 'Selesai') {
                $updateKeluarSql = ", tanggal_keluar = CURRENT_TIMESTAMP";
            }

            $sql = "UPDATE pesanan SET status_cucian = :status {$updateRakSql} {$updateKeluarSql} WHERE id_pesanan = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            // Tambahkan ke riwayat_status
            $idRw = Helper::generateId('RW-', 'riwayat_status', 'id_riwayat', 3);
            $defaultNote = "Status diperbarui menjadi {$newStatus}";
            if ($rak) {
                $defaultNote .= " (Rak: {$rak})";
            }
            $keterangan = !empty($note) ? $note : $defaultNote;

            $sqlRw = "INSERT INTO riwayat_status (id_riwayat, status, waktu_status, keterangan, id_pesanan, id_anggota)
                      VALUES (:id, :status, CURRENT_TIMESTAMP, :ket, :pesanan, :anggota)";
            $stmtRw = $pdo->prepare($sqlRw);
            $stmtRw->execute([
                'id'      => $idRw,
                'status'  => $newStatus,
                'ket'     => $keterangan,
                'pesanan' => $id,
                'anggota' => $userId
            ]);

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return false;
        }
    }

    /**
     * Pelunasan pembayaran saat pesanan diambil
     */
    public static function bayarPelunasan(string $idPesanan, float $jumlahBayar, float $kembalian, string $metode, ?string $userId = null): bool {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            // Row-level lock untuk mencegah race condition / double payment akibat double click
            $stmtLock = $pdo->prepare("SELECT status_pembayaran FROM pesanan WHERE id_pesanan = :id FOR UPDATE");
            $stmtLock->execute(['id' => $idPesanan]);
            $currentOrder = $stmtLock->fetch();

            if ($currentOrder && strtolower($currentOrder['status_pembayaran']) === 'lunas') {
                $pdo->commit();
                return true; // Sudah lunas oleh proses concurrent lain
            }

            // Normalisasi metode pembayaran ke ID foreign key valid
            $methodMap = [
                'tunai'    => 'MET-01',
                'cash'     => 'MET-01',
                'transfer' => 'MET-02',
                'bank'     => 'MET-02',
                'qris'     => 'MET-03',
                'ewallet'  => 'MET-03',
                'nanti'    => 'MET-04'
            ];
            $cleanMetode = $methodMap[strtolower($metode)] ?? $metode;

            // Insert pembayaran
            $idPay = Helper::generateId('PAY-', 'pembayaran', 'id_pembayaran', 3);
            $sqlPay = "INSERT INTO pembayaran (id_pembayaran, tanggal_pembayaran, jumlah_bayar, uang_kembalian, id_pesanan, id_metode, id_anggota)
                       VALUES (:id, CURRENT_TIMESTAMP, :bayar, :kembali, :pesanan, :metode, :anggota)";
            $stmtPay = $pdo->prepare($sqlPay);
            $stmtPay->execute([
                'id'      => $idPay,
                'bayar'   => $jumlahBayar,
                'kembali' => $kembalian,
                'pesanan' => $idPesanan,
                'metode'  => $cleanMetode,
                'anggota' => $userId
            ]);

            // Update status pembayaran pesanan
            $sqlOrder = "UPDATE pesanan SET status_pembayaran = 'Lunas' WHERE id_pesanan = :id";
            $stmtOrder = $pdo->prepare($sqlOrder);
            $stmtOrder->execute(['id' => $idPesanan]);

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return false;
        }
    }

    /**
     * Mengambil daftar pesanan di lantai produksi (Antrean, Dicuci, Disetrika)
     * Terintegrasi 100% database dengan prioritas Kilat, deadline tercepat, dan waktu masuk.
     */
    public static function getAntreanProduksi(): array {
        $sql = "SELECT ps.*, p.nama_pelanggan, p.no_telepon,
                (SELECT l.nama_layanan FROM detail_pesanan dp JOIN layanan l ON dp.id_layanan = l.id_layanan WHERE dp.id_pesanan = ps.id_pesanan LIMIT 1) AS nama_layanan,
                COALESCE((SELECT SUM(dp.jumlah) FROM detail_pesanan dp WHERE dp.id_pesanan = ps.id_pesanan), 1) AS total_berat,
                COALESCE((SELECT l.satuan FROM detail_pesanan dp JOIN layanan l ON dp.id_layanan = l.id_layanan WHERE dp.id_pesanan = ps.id_pesanan LIMIT 1), 'kg') AS satuan,
                (SELECT GROUP_CONCAT(CONCAT(l.nama_layanan, ' (', dp.jumlah, ' ', l.satuan, ')') SEPARATOR ', ') 
                 FROM detail_pesanan dp 
                 JOIN layanan l ON dp.id_layanan = l.id_layanan 
                 WHERE dp.id_pesanan = ps.id_pesanan) AS rincian_item
                FROM pesanan ps
                JOIN pelanggan p ON ps.id_pelanggan = p.id_pelanggan
                WHERE ps.status_cucian IN ('Antrean', 'Dicuci', 'Disetrika')
                ORDER BY ps.is_kilat DESC, ps.tanggal_keluar ASC, ps.tanggal_masuk ASC";
        return Database::query($sql);
    }

    /**
     * Mengambil daftar pesanan yang siap diambil di rak
     * Terintegrasi 100% database tanpa data dummy
     * @param string|null $rak Filter lokasi rak
     * @param string $tab 'aktif' (hanya Siap Diambil), 'riwayat' (Sudah diserahkan hari ini), atau 'semua'
     */
    public static function getSiapAmbil(?string $rak = null, string $tab = 'aktif'): array {
        if ($tab === 'riwayat') {
            $statusCondition = "ps.status_cucian IN ('Selesai', 'Sudah Diambil') AND (DATE(ps.tanggal_keluar) = CURRENT_DATE OR EXISTS (SELECT 1 FROM riwayat_status rw WHERE rw.id_pesanan = ps.id_pesanan AND rw.status IN ('Selesai', 'Sudah Diambil') AND DATE(rw.waktu_status) = CURRENT_DATE))";
        } elseif ($tab === 'semua') {
            $statusCondition = "ps.status_cucian IN ('Siap Diambil', 'Selesai', 'Sudah Diambil')";
        } else {
            // Default: 'aktif' - hanya yang masih ada di rak & belum diambil pelanggan
            $statusCondition = "ps.status_cucian = 'Siap Diambil'";
        }

        $sql = "SELECT ps.*, p.nama_pelanggan, p.no_telepon, p.alamat,
                (SELECT mp.nama_metode FROM pembayaran pay JOIN metode_pembayaran mp ON pay.id_metode = mp.id_metode WHERE pay.id_pesanan = ps.id_pesanan ORDER BY pay.tanggal_pembayaran DESC LIMIT 1) AS metode_bayar,
                (SELECT GROUP_CONCAT(l.nama_layanan SEPARATOR ', ') FROM detail_pesanan dp JOIN layanan l ON dp.id_layanan = l.id_layanan WHERE dp.id_pesanan = ps.id_pesanan) AS daftar_layanan,
                (SELECT l.nama_layanan FROM detail_pesanan dp JOIN layanan l ON dp.id_layanan = l.id_layanan WHERE dp.id_pesanan = ps.id_pesanan LIMIT 1) AS nama_layanan,
                COALESCE((SELECT SUM(dp.jumlah) FROM detail_pesanan dp WHERE dp.id_pesanan = ps.id_pesanan), 1) AS total_berat,
                COALESCE((SELECT l.satuan FROM detail_pesanan dp JOIN layanan l ON dp.id_layanan = l.id_layanan WHERE dp.id_pesanan = ps.id_pesanan LIMIT 1), 'kg') AS satuan
                FROM pesanan ps
                JOIN pelanggan p ON ps.id_pelanggan = p.id_pelanggan
                WHERE $statusCondition";
        
        $params = [];
        if ($rak && $rak !== 'Semua') {
            $sql .= " AND ps.lokasi_rak = :rak";
            $params['rak'] = $rak;
        }

        $sql .= " ORDER BY ps.tanggal_keluar DESC, ps.tanggal_masuk DESC, ps.id_pesanan DESC";

        return Database::query($sql, $params);
    }

    /**
     * Menghitung total cucian yang saat ini aktif di rak menunggu diambil
     */
    public static function countSiapAmbil(): int {
        try {
            $sql = "SELECT COUNT(*) FROM pesanan WHERE status_cucian = 'Siap Diambil'";
            $pdo = Database::getConnection();
            return (int)$pdo->query($sql)->fetchColumn();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Mengambil notifikasi gerai real-time dari database
     * Menampilkan pesanan prioritas kilat yang aktif dan cucian yang siap diambil
     */
    public static function getStoreNotifications(int $limit = 8): array {
        $sql = "SELECT ps.id_pesanan, ps.status_cucian, ps.status_pembayaran, ps.is_kilat, 
                       ps.lokasi_rak, ps.tanggal_masuk, ps.tanggal_keluar, ps.total_harga,
                       p.nama_pelanggan, p.no_telepon,
                       (SELECT l.nama_layanan FROM detail_pesanan dp JOIN layanan l ON dp.id_layanan = l.id_layanan WHERE dp.id_pesanan = ps.id_pesanan LIMIT 1) AS nama_layanan
                FROM pesanan ps
                JOIN pelanggan p ON ps.id_pelanggan = p.id_pelanggan
                WHERE (ps.is_kilat = 1 AND ps.status_cucian IN ('Antrean', 'Dicuci', 'Disetrika'))
                   OR (ps.status_cucian = 'Siap Diambil')
                ORDER BY ps.is_kilat DESC, 
                         CASE WHEN ps.status_cucian = 'Siap Diambil' THEN 1 ELSE 2 END,
                         ps.tanggal_masuk DESC
                LIMIT " . (int)$limit;
        return Database::query($sql);
    }

    /**
     * Menghitung metrik ringkasan untuk Dashboard (100% Data Riil)
     */
    public static function getMetrics(): array {
        // Omzet Hari Ini
        $sqlOmzet = "SELECT COALESCE(SUM(jumlah_bayar - uang_kembalian), 0) AS omzet_today
                     FROM pembayaran
                     WHERE DATE(tanggal_pembayaran) = CURRENT_DATE";
        $omzet = (float)(Database::fetch($sqlOmzet)['omzet_today'] ?? 0);

        // Omzet Kemarin (untuk komparasi pertumbuhan persentase)
        $sqlOmzetYesterday = "SELECT COALESCE(SUM(jumlah_bayar - uang_kembalian), 0) AS omzet_yesterday
                              FROM pembayaran
                              WHERE DATE(tanggal_pembayaran) = DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)";
        $omzetYesterday = (float)(Database::fetch($sqlOmzetYesterday)['omzet_yesterday'] ?? 0);

        // Hitung persentase pertumbuhan omset
        $growthPercent = 0.0;
        if ($omzetYesterday > 0) {
            $growthPercent = round((($omzet - $omzetYesterday) / $omzetYesterday) * 100, 1);
        } elseif ($omzet > 0) {
            $growthPercent = 100.0;
        }

        // Antrean Produksi Aktif (Antrean, Dicuci, Disetrika)
        $sqlAktif = "SELECT COUNT(*) AS total_aktif FROM pesanan WHERE status_cucian IN ('Antrean', 'Dicuci', 'Disetrika')";
        $aktif = (int)(Database::fetch($sqlAktif)['total_aktif'] ?? 0);

        // Siap Diambil di Rak
        $sqlSiap = "SELECT COUNT(*) AS total_siap FROM pesanan WHERE status_cucian = 'Siap Diambil'";
        $siap = (int)(Database::fetch($sqlSiap)['total_siap'] ?? 0);

        // Selesai atau Siap Diambil Hari ini
        $sqlCompletedToday = "SELECT COUNT(*) AS completed_today FROM pesanan 
                              WHERE DATE(tanggal_keluar) = CURRENT_DATE 
                              AND status_cucian IN ('Siap Diambil', 'Sudah Diambil', 'Selesai')";
        $completedToday = (int)(Database::fetch($sqlCompletedToday)['completed_today'] ?? 0);

        // Total Piutang (Pesanan belum lunas)
        $sqlPiutang = "SELECT COALESCE(SUM(total_harga), 0) AS total_piutang 
                       FROM pesanan 
                       WHERE status_pembayaran = 'Belum Lunas' AND status_cucian != 'Dibatalkan'";
        $piutang = (float)(Database::fetch($sqlPiutang)['total_piutang'] ?? 0);

        return [
            'omzet_today'      => $omzet,
            'omzet_yesterday'  => $omzetYesterday,
            'growth_percent'   => $growthPercent,
            'total_aktif'      => $aktif,
            'total_siap'       => $siap,
            'completed_today'  => $completedToday,
            'total_piutang'    => $piutang
        ];
    }

    /**
     * Data grafik 14 hari terakhir (Omset vs Pengeluaran)
     */
    public static function getChart14Days(): array {
        $days = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $days[$date] = [
                'tanggal'     => $date,
                'label'       => date('d M', strtotime($date)),
                'omset'       => 0,
                'pengeluaran' => 0
            ];
        }

        // Ambil omset 14 hari
        $sqlOmset = "SELECT DATE(tanggal_pembayaran) AS tgl, SUM(jumlah_bayar - uang_kembalian) AS total
                     FROM pembayaran
                     WHERE tanggal_pembayaran >= DATE_SUB(CURRENT_DATE, INTERVAL 14 DAY)
                     GROUP BY DATE(tanggal_pembayaran)";
        $resOmset = Database::query($sqlOmset);
        foreach ($resOmset as $row) {
            $tgl = $row['tgl'];
            if (isset($days[$tgl])) {
                $days[$tgl]['omset'] = (float)$row['total'];
            }
        }

        // Ambil pengeluaran 14 hari
        $sqlExp = "SELECT tanggal_pengeluaran AS tgl, SUM(jumlah) AS total
                   FROM pengeluaran
                   WHERE tanggal_pengeluaran >= DATE_SUB(CURRENT_DATE, INTERVAL 14 DAY)
                   GROUP BY tanggal_pengeluaran";
        $resExp = Database::query($sqlExp);
        foreach ($resExp as $row) {
            $tgl = $row['tgl'];
            if (isset($days[$tgl])) {
                $days[$tgl]['pengeluaran'] = (float)$row['total'];
            }
        }

        return array_values($days);
    }
}
