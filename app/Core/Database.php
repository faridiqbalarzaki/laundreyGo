<?php
namespace App\Core;

use PDO;
use PDOException;

/**
 * Class Database
 * Mengelola koneksi PDO ke database PostgreSQL
 * Menggunakan pola Singleton agar koneksi efisien (1 koneksi per request)
 */
class Database {
    private static ?PDO $instance = null;

    /**
     * Mendapatkan instance PDO aktif
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';

            $dsn = sprintf(
                "%s:host=%s;port=%d;dbname=%s",
                $config['driver'],
                $config['host'],
                $config['port'],
                $config['database']
            );

            try {
                self::$instance = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
            } catch (PDOException $e) {
                // Tampilkan pesan error ramah mahasiswa jika database belum konek
                die("<div style='font-family:sans-serif;max-width:600px;margin:50px auto;padding:25px;border:1px solid #f87171;border-radius:12px;background:#fef2f2;color:#991b1b;'>
                    <h2 style='margin-top:0;'>⚠️ Gagal Terhubung ke Database PostgreSQL</h2>
                    <p>Pesan error: <code>" . htmlspecialchars($e->getMessage()) . "</code></p>
                    <hr style='border:0;border-top:1px solid #fca5a5;margin:15px 0;'>
                    <p><strong>Langkah penyelesaian:</strong></p>
                    <ol style='padding-left:20px;line-height:1.6;'>
                        <li>Pastikan PostgreSQL sudah di-Start di Laragon / Services.</li>
                        <li>Pastikan password di <code>config/database.php</code> sudah sesuai.</li>
                        <li>Jalankan migrasi database via terminal: <br><code>php database/migrate.php</code></li>
                    </ol>
                </div>");
            }
        }

        return self::$instance;
    }

    /**
     * Helper menjalankan query SELECT dan mengembalikan semua baris
     */
    public static function query(string $sql, array $params = []): array {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Helper mengambil 1 baris data
     */
    public static function fetch(string $sql, array $params = []): ?array {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        $res = $stmt->fetch();
        return $res !== false ? $res : null;
    }

    /**
     * Helper eksekusi INSERT / UPDATE / DELETE
     */
    public static function execute(string $sql, array $params = []): bool {
        $stmt = self::getConnection()->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Mendapatkan ID terakhir yang di-insert
     */
    public static function lastInsertId(?string $name = null): string {
        return self::getConnection()->lastInsertId($name);
    }
}
