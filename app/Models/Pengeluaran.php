<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Helper;

class Pengeluaran {
    public static function all(?string $kategori = null, ?string $bulan = null): array {
        $sql = "SELECT p.*, kp.nama_kategori, a.nama_anggota
                FROM pengeluaran p
                JOIN kategori_pengeluaran kp ON p.id_kategori_pengeluaran = kp.id_kategori_pengeluaran
                LEFT JOIN anggota a ON p.id_anggota = a.id_anggota
                WHERE 1=1";
        
        $params = [];
        if ($kategori && $kategori !== 'Semua') {
            $sql .= " AND p.id_kategori_pengeluaran = :kat";
            $params['kat'] = $kategori;
        }

        if ($bulan) {
            $sql .= " AND TO_CHAR(p.tanggal_pengeluaran, '%Y-%m') = :bln";
            $params['bln'] = $bulan;
        }

        $sql .= " ORDER BY p.tanggal_pengeluaran ASC, p.id_pengeluaran ASC";

        return Database::query($sql, $params);
    }

    public static function findById(string $id): ?array {
        $sql = "SELECT p.*, kp.nama_kategori 
                FROM pengeluaran p
                JOIN kategori_pengeluaran kp ON p.id_kategori_pengeluaran = kp.id_kategori_pengeluaran
                WHERE p.id_pengeluaran = :id";
        return Database::fetch($sql, ['id' => $id]);
    }

    public static function create(array $data): string {
        $id = Helper::generateId('EXP-', 'pengeluaran', 'id_pengeluaran', 3);
        $sql = "INSERT INTO pengeluaran (id_pengeluaran, tanggal_pengeluaran, keterangan, jumlah, id_anggota, id_kategori_pengeluaran)
                VALUES (:id, :tgl, :ket, :jml, :anggota, :kat)";
        Database::execute($sql, [
            'id'      => $id,
            'tgl'     => $data['tanggal_pengeluaran'] ?? date('Y-m-d'),
            'ket'     => $data['keterangan'],
            'jml'     => $data['jumlah'],
            'anggota' => $data['id_anggota'] ?? null,
            'kat'     => $data['id_kategori_pengeluaran']
        ]);
        return $id;
    }

    public static function update(string $id, array $data): bool {
        $sql = "UPDATE pengeluaran 
                SET tanggal_pengeluaran = :tgl, keterangan = :ket, jumlah = :jml, id_kategori_pengeluaran = :kat
                WHERE id_pengeluaran = :id";
        return Database::execute($sql, [
            'id'  => $id,
            'tgl' => $data['tanggal_pengeluaran'],
            'ket' => $data['keterangan'],
            'jml' => $data['jumlah'],
            'kat' => $data['id_kategori_pengeluaran']
        ]);
    }

    public static function delete(string $id): bool {
        $sql = "DELETE FROM pengeluaran WHERE id_pengeluaran = :id";
        return Database::execute($sql, ['id' => $id]);
    }

    public static function getKategoriList(): array {
        return Database::query("SELECT * FROM kategori_pengeluaran ORDER BY id_kategori_pengeluaran ASC");
    }

    public static function getTotalBulanIni(): float {
        $sql = "SELECT COALESCE(SUM(jumlah), 0) AS total 
                FROM pengeluaran 
                WHERE TO_CHAR(tanggal_pengeluaran, '%Y-%m') = TO_CHAR(CURRENT_DATE, '%Y-%m')";
        $res = Database::fetch($sql);
        return (float)($res['total'] ?? 0);
    }
}
