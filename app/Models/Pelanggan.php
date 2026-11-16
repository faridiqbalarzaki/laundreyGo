<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Helper;

class Pelanggan {
    public static function all(): array {
        $sql = "SELECT p.*, 
                COUNT(ps.id_pesanan) AS total_order, 
                COALESCE(SUM(ps.total_harga), 0) AS total_belanja,
                MAX(ps.tanggal_masuk) AS order_terakhir
                FROM pelanggan p
                LEFT JOIN pesanan ps ON p.id_pelanggan = ps.id_pelanggan
                GROUP BY p.id_pelanggan, p.nama_pelanggan, p.no_telepon, p.alamat
                ORDER BY p.id_pelanggan ASC";
        return Database::query($sql);
    }

    public static function findById(string $id): ?array {
        $sql = "SELECT * FROM pelanggan WHERE id_pelanggan = :id";
        return Database::fetch($sql, ['id' => $id]);
    }

    public static function search(string $keyword): array {
        $sql = "SELECT * FROM pelanggan 
                WHERE nama_pelanggan ILIKE :kw OR no_telepon ILIKE :kw 
                ORDER BY nama_pelanggan ASC LIMIT 20";
        return Database::query($sql, ['kw' => '%' . $keyword . '%']);
    }

    public static function create(array $data): string {
        $id = Helper::generateId('PEL-', 'pelanggan', 'id_pelanggan', 3);
        $sql = "INSERT INTO pelanggan (id_pelanggan, nama_pelanggan, no_telepon, alamat) 
                VALUES (:id, :nama, :telp, :alamat)";
        Database::execute($sql, [
            'id'     => $id,
            'nama'   => $data['nama_pelanggan'],
            'telp'   => $data['no_telepon'],
            'alamat' => $data['alamat'] ?? ''
        ]);
        return $id;
    }

    public static function update(string $id, array $data): bool {
        $sql = "UPDATE pelanggan 
                SET nama_pelanggan = :nama, no_telepon = :telp, alamat = :alamat 
                WHERE id_pelanggan = :id";
        return Database::execute($sql, [
            'id'     => $id,
            'nama'   => $data['nama_pelanggan'],
            'telp'   => $data['no_telepon'],
            'alamat' => $data['alamat'] ?? ''
        ]);
    }

    public static function delete(string $id): bool {
        $sql = "DELETE FROM pelanggan WHERE id_pelanggan = :id";
        return Database::execute($sql, ['id' => $id]);
    }

    public static function getOrderHistory(string $id): array {
        $sql = "SELECT ps.*, 
                (SELECT COUNT(*) FROM detail_pesanan d WHERE d.id_pesanan = ps.id_pesanan) AS jumlah_item
                FROM pesanan ps
                WHERE ps.id_pelanggan = :id
                ORDER BY ps.tanggal_masuk DESC";
        return Database::query($sql, ['id' => $id]);
    }
}
