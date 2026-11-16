<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Helper;

class Layanan {
    public static function all(): array {
        $sql = "SELECT l.*, k.nama_kategori 
                FROM layanan l
                JOIN kategori_layanan k ON l.id_kategori = k.id_kategori
                ORDER BY l.id_layanan ASC";
        return Database::query($sql);
    }

    public static function getActive(): array {
        $sql = "SELECT l.*, k.nama_kategori 
                FROM layanan l
                JOIN kategori_layanan k ON l.id_kategori = k.id_kategori
                WHERE l.status_aktif = 1
                ORDER BY l.id_layanan ASC";
        return Database::query($sql);
    }

    public static function findById(string $id): ?array {
        $sql = "SELECT l.*, k.nama_kategori 
                FROM layanan l
                JOIN kategori_layanan k ON l.id_kategori = k.id_kategori
                WHERE l.id_layanan = :id";
        return Database::fetch($sql, ['id' => $id]);
    }

    public static function create(array $data): string {
        $id = Helper::generateId('LAY-', 'layanan', 'id_layanan', 2);
        $sql = "INSERT INTO layanan (id_layanan, nama_layanan, harga, estimasi_waktu, satuan, status_aktif, id_kategori)
                VALUES (:id, :nama, :harga, :estimasi, :satuan, :status, :kategori)";
        Database::execute($sql, [
            'id'       => $id,
            'nama'     => $data['nama_layanan'],
            'harga'    => $data['harga'],
            'estimasi' => $data['estimasi_waktu'] ?? 24,
            'satuan'   => $data['satuan'] ?? 'kg',
            'status'   => $data['status_aktif'] ?? 1,
            'kategori' => $data['id_kategori']
        ]);
        return $id;
    }

    public static function update(string $id, array $data): bool {
        $sql = "UPDATE layanan 
                SET nama_layanan = :nama, harga = :harga, estimasi_waktu = :estimasi, 
                    satuan = :satuan, status_aktif = :status, id_kategori = :kategori
                WHERE id_layanan = :id";
        return Database::execute($sql, [
            'id'       => $id,
            'nama'     => $data['nama_layanan'],
            'harga'    => $data['harga'],
            'estimasi' => $data['estimasi_waktu'],
            'satuan'   => $data['satuan'],
            'status'   => $data['status_aktif'],
            'kategori' => $data['id_kategori']
        ]);
    }

    public static function delete(string $id): bool {
        $sql = "DELETE FROM layanan WHERE id_layanan = :id";
        return Database::execute($sql, ['id' => $id]);
    }

    public static function getKategoriList(): array {
        return Database::query("SELECT * FROM kategori_layanan WHERE status_aktif = 1 ORDER BY id_kategori ASC");
    }
}
