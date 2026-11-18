<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Helper;
use App\Core\View;
use App\Models\Pelanggan;
use App\Models\Layanan;
use App\Models\Pesanan;
use App\Models\LogAktivitas;

class PosController {
    public function index(): void {
        Auth::requireRole(['Admin', 'Kasir']);

        $layananList = Layanan::getActive();
        $pelangganList = Pelanggan::all();
        $kategoriList = Layanan::getKategoriList();

        View::render('pos/index', [
            'title'         => 'Kasir POS - LaundryGo Aqua',
            'layananList'   => $layananList,
            'pelangganList' => $pelangganList,
            'kategoriList'  => $kategoriList
        ]);
    }

    /**
     * Proses Simpan Transaksi Pesanan Baru dari POS
     */
    public function store(): void {
        Auth::requireRole(['Admin', 'Kasir']);

        // Tangani JSON payload (jika dikirim via AJAX fetch) atau POST biasa
        $rawInput = file_get_contents('php://input');
        $jsonData = json_decode($rawInput, true);

        $input = $jsonData ?? $_POST;

        $idPelanggan = Helper::sanitize($input['id_pelanggan'] ?? '');
        $catatanKhusus = Helper::sanitize($input['catatan_khusus'] ?? '');
        $isKilat = !empty($input['is_kilat']);
        $items = $input['items'] ?? [];

        // Pastikan ID pelanggan benar-benar ada di database.
        // Tanpa ini, ID fiktif (mis. dari modal pelanggan yang belum tersimpan)
        // akan melanggar foreign key dan membuat transaksi gagal tanpa pesan jelas.
        if ($idPelanggan !== '' && !Pelanggan::findById($idPelanggan)) {
            $notice = 'Pelanggan yang dipilih tidak ditemukan di database. Silakan pilih pelanggan yang tersedia atau simpan pelanggan baru terlebih dahulu.';
            if ($jsonData) {
                Helper::jsonResponse(['success' => false, 'message' => $notice], 400);
            }
            Helper::setFlash('error', $notice);
            Helper::redirect(Helper::url('pos'));
        }

        // Jika pelanggan baru diinput langsung (inline)
        if (empty($idPelanggan) && !empty($input['nama_pelanggan_baru'])) {
            $rawNama = trim(strip_tags($input['nama_pelanggan_baru']));
            $rawTelp = trim($input['no_telepon_baru'] ?? '');

            // Validasi format nama pelanggan baru
            if (mb_strlen($rawNama) < 2 || mb_strlen($rawNama) > 100 || !preg_match('/^[a-zA-Z\s\.\'\-]+$/u', $rawNama)) {
                if ($jsonData) {
                    Helper::jsonResponse(['success' => false, 'message' => 'Nama pelanggan baru tidak valid! Hanya boleh huruf, spasi, titik (min 2 karakter).'], 400);
                }
                Helper::setFlash('error', 'Nama pelanggan baru tidak valid!');
                Helper::redirect(Helper::url('pos'));
            }

            // Validasi nomor telepon jika diisi
            if (!empty($rawTelp) && $rawTelp !== '-') {
                if (!Helper::isValidPhone($rawTelp)) {
                    if ($jsonData) {
                        Helper::jsonResponse(['success' => false, 'message' => 'Nomor HP pelanggan baru tidak valid! Contoh format yang benar: 08123456789.'], 400);
                    }
                    Helper::setFlash('error', 'Nomor telepon pelanggan baru tidak valid!');
                    Helper::redirect(Helper::url('pos'));
                }
                $rawTelp = Helper::normalizePhone($rawTelp);
            } else {
                $rawTelp = '-';
            }

            $idPelanggan = Pelanggan::create([
                'nama_pelanggan' => $rawNama,
                'no_telepon'     => $rawTelp,
                'alamat'         => trim(strip_tags($input['alamat_baru'] ?? ''))
            ]);
        }

        if (empty($idPelanggan)) {
            if ($jsonData) {
                Helper::jsonResponse(['success' => false, 'message' => 'Pilih atau masukkan data pelanggan terlebih dahulu!'], 400);
            }
            Helper::setFlash('error', 'Pilih atau masukkan data pelanggan!');
            Helper::redirect(Helper::url('pos'));
        }

        if (empty($items) || !is_array($items)) {
            if ($jsonData) {
                Helper::jsonResponse(['success' => false, 'message' => 'Keranjang pesanan masih kosong!'], 400);
            }
            Helper::setFlash('error', 'Keranjang pesanan masih kosong!');
            Helper::redirect(Helper::url('pos'));
        }

        // Hitung total harga & validasi jumlah > 0 dan <= 500
        $totalHarga = 0;
        $itemsData = [];
        foreach ($items as $item) {
            $layanan = Layanan::findById($item['id_layanan']);
            if ($layanan) {
                $qty = (float)($item['jumlah'] ?? 0);
                if ($qty <= 0 || $qty > 500) {
                    if ($jsonData) {
                        Helper::jsonResponse(['success' => false, 'message' => 'Berat atau jumlah layanan harus lebih dari 0 dan maksimal 500!'], 400);
                    }
                    Helper::setFlash('error', 'Berat atau jumlah layanan harus lebih dari 0 dan maksimal 500!');
                    Helper::redirect(Helper::url('pos'));
                }

                $subtotal = $qty * (float)$layanan['harga'];
                $totalHarga += $subtotal;

                $itemsData[] = [
                    'id_layanan'     => $layanan['id_layanan'],
                    'jumlah'         => $qty,
                    'harga_satuan'   => (float)$layanan['harga'],
                    'estimasi_waktu' => (int)$layanan['estimasi_waktu']
                ];
            }
        }

        if (empty($itemsData)) {
            if ($jsonData) {
                Helper::jsonResponse(['success' => false, 'message' => 'Layanan yang dipilih tidak valid atau belum terdaftar!'], 400);
            }
            Helper::setFlash('error', 'Layanan yang dipilih tidak valid!');
            Helper::redirect(Helper::url('pos'));
        }

        // Cek Pembayaran
        $bayarSekarang = !empty($input['bayar_sekarang']);
        $paymentData = null;
        $statusPembayaran = 'Belum Lunas';

        if ($bayarSekarang) {
            $jumlahBayar = (float)($input['jumlah_bayar'] ?? 0);
            $rawMetode = $input['id_metode'] ?? 'MET-01';
            $methodMap = [
                'tunai'    => 'MET-01',
                'cash'     => 'MET-01',
                'transfer' => 'MET-02',
                'bank'     => 'MET-02',
                'qris'     => 'MET-03',
                'ewallet'  => 'MET-03',
                'nanti'    => 'MET-04'
            ];
            $idMetode = $methodMap[strtolower((string)$rawMetode)] ?? (string)$rawMetode;

            // Metode yang tersedia di tabel metode_pembayaran. Jika tidak dikenal,
            // gunakan Tunai agar tidak melanggar foreign key.
            if (!in_array($idMetode, ['MET-01', 'MET-02', 'MET-03'], true)) {
                $idMetode = 'MET-01';
            }

            // Pembayaran non-tunai (transfer / e-wallet) tidak memakai input uang tunai.
            // Anggap nominal yang dibayar sama dengan total tagihan agar otomatis Lunas.
            if ($idMetode !== 'MET-01' && $jumlahBayar <= 0) {
                $jumlahBayar = $totalHarga;
            }

            $kembalian = max(0, $jumlahBayar - $totalHarga);

            if ($idMetode === 'MET-01' && $jumlahBayar < $totalHarga) {
                if ($jsonData) {
                    Helper::jsonResponse(['success' => false, 'message' => 'Uang tunai yang dibayarkan kurang dari total tagihan!'], 400);
                }
                Helper::setFlash('error', 'Uang tunai kurang dari total tagihan!');
                Helper::redirect(Helper::url('pos'));
            }

            if ($jumlahBayar >= $totalHarga) {
                $statusPembayaran = 'Lunas';
                $paymentData = [
                    'jumlah_bayar'   => $jumlahBayar,
                    'uang_kembalian' => $kembalian,
                    'id_metode'      => $idMetode
                ];
            }
        }

        $orderData = [
            'id_pelanggan'      => $idPelanggan,
            'status_cucian'     => 'Antrean',
            'total_harga'       => $totalHarga,
            'status_pembayaran' => $statusPembayaran,
            'lokasi_rak'        => null,
            'catatan_khusus'    => $catatanKhusus,
            'is_kilat'          => $isKilat,
            'id_anggota'        => Auth::id()
        ];

        $idPesanan = Pesanan::create($orderData, $itemsData, $paymentData);

        if ($idPesanan) {
            LogAktivitas::log('Transaksi POS', "Membuat pesanan baru {$idPesanan} untuk pelanggan {$idPelanggan}", Auth::id());

            if ($jsonData) {
                Helper::jsonResponse([
                    'success'      => true,
                    'message'      => 'Pesanan berhasil disimpan!',
                    'id_pesanan'   => $idPesanan,
                    'lokasi_rak'   => $orderData['lokasi_rak'] ?? null
                ]);
            }

            Helper::setFlash('success', "Pesanan {$idPesanan} berhasil disimpan!");
            Helper::redirect(Helper::url('pos', ['print' => $idPesanan]));
        } else {
            if ($jsonData) {
                Helper::jsonResponse(['success' => false, 'message' => 'Gagal menyimpan pesanan ke database.'], 500);
            }
            Helper::setFlash('error', 'Gagal menyimpan pesanan. Silakan coba lagi.');
            Helper::redirect(Helper::url('pos'));
        }
    }

    /**
     * Endpoint API pencarian pelanggan via AJAX
     */
    public function searchPelanggan(): void {
        Auth::requireLogin();
        $q = Helper::sanitize($_GET['q'] ?? '');
        $data = Pelanggan::search($q);
        Helper::jsonResponse($data);
    }

    /**
     * Endpoint API simpan pelanggan baru langsung dari POS (AJAX/JSON).
     * Mengembalikan ID pelanggan asli agar transaksi tidak melanggar foreign key.
     */
    public function storePelanggan(): void {
        Auth::requireRole(['Admin', 'Kasir']);

        $rawInput = file_get_contents('php://input');
        $jsonData = json_decode($rawInput, true);
        $input = $jsonData ?? $_POST;

        $nama = trim(strip_tags((string)($input['nama_pelanggan'] ?? '')));
        $rawTelp = trim((string)($input['no_telepon'] ?? ''));
        $alamat = trim(strip_tags((string)($input['alamat'] ?? '')));

        if (mb_strlen($nama) < 2 || mb_strlen($nama) > 100 || !preg_match('/^[a-zA-Z\s\.\'\-]+$/u', $nama)) {
            Helper::jsonResponse(['success' => false, 'message' => 'Nama pelanggan tidak valid. Minimal 2 karakter, hanya huruf, spasi, titik, atau strip.'], 400);
        }

        if ($rawTelp === '' || $rawTelp === '-') {
            Helper::jsonResponse(['success' => false, 'message' => 'Nomor HP pelanggan wajib diisi.'], 400);
        }

        if (!Helper::isValidPhone($rawTelp)) {
            Helper::jsonResponse(['success' => false, 'message' => 'Nomor HP tidak valid. Contoh format yang benar: 08123456789.'], 400);
        }

        $telp = Helper::normalizePhone($rawTelp);

        try {
            $id = Pelanggan::create([
                'nama_pelanggan' => $nama,
                'no_telepon'     => $telp,
                'alamat'         => $alamat
            ]);
        } catch (\Throwable $e) {
            error_log('Create customer error: ' . $e->getMessage());
            Helper::jsonResponse(['success' => false, 'message' => 'Gagal menyimpan pelanggan baru ke database.'], 500);
        }

        LogAktivitas::log('Tambah Pelanggan', "Menambahkan pelanggan baru {$nama} ({$id}) dari POS", Auth::id());

        Helper::jsonResponse([
            'success'  => true,
            'message'  => 'Pelanggan baru berhasil disimpan.',
            'customer' => [
                'id_pelanggan'   => $id,
                'nama_pelanggan' => $nama,
                'no_telepon'     => $telp,
                'alamat'         => $alamat
            ]
        ]);
    }
}
