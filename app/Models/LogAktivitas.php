<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Helper;
use App\Core\Auth;

class LogAktivitas {
    public static function log(string $aktivitas, ?string $keterangan = null, ?string $idAnggota = null): void {
        $id = Helper::generateId('LOG-', 'log_aktivitas', 'id_log', 4);
        $user = $idAnggota ?? Auth::id();

        $sql = "INSERT INTO log_aktivitas (id_log, aktivitas, waktu_aktivitas, keterangan, id_anggota)
                VALUES (:id, :akt, CURRENT_TIMESTAMP, :ket, :user)";
        Database::execute($sql, [
            'id'   => $id,
            'akt'  => $aktivitas,
            'ket'  => $keterangan,
            'user' => $user
        ]);
    }

    public static function getLatest(int $limit = 20): array {
        $sql = "SELECT l.*, a.nama_anggota, a.username
                FROM log_aktivitas l
                LEFT JOIN anggota a ON l.id_anggota = a.id_anggota
                ORDER BY l.waktu_aktivitas DESC
                LIMIT :lim";
        return Database::query($sql, ['lim' => $limit]);
    }
}
