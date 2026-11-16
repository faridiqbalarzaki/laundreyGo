<?php
namespace App\Models;

use App\Core\Database;

class Anggota {
    public static function all(): array {
        $sql = "SELECT a.*, r.nama_role 
                FROM anggota a 
                JOIN role r ON a.id_role = r.id_role 
                ORDER BY a.id_anggota ASC";
        return Database::query($sql);
    }

    public static function findById(string $id): ?array {
        $sql = "SELECT a.*, r.nama_role 
                FROM anggota a 
                JOIN role r ON a.id_role = r.id_role 
                WHERE a.id_anggota = :id";
        return Database::fetch($sql, ['id' => $id]);
    }

    public static function findByUsername(string $username): ?array {
        $sql = "SELECT a.*, r.nama_role 
                FROM anggota a 
                JOIN role r ON a.id_role = r.id_role 
                WHERE a.username = :username AND a.status_aktif = 1";
        return Database::fetch($sql, ['username' => $username]);
    }

    public static function authenticate(string $username, string $password): ?array {
        $user = self::findByUsername($username);
        if (!$user) {
            return null;
        }

        // Verifikasi password hash bcrypt atau fallback plain text jika akun demo awal
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            return $user;
        }

        return null;
    }

    public static function create(array $data): bool {
        $sql = "INSERT INTO anggota (id_anggota, username, password, nama_anggota, status_aktif, id_role) 
                VALUES (:id, :username, :password, :nama, :status, :role)";
        return Database::execute($sql, [
            'id'       => $data['id_anggota'],
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'nama'     => $data['nama_anggota'],
            'status'   => $data['status_aktif'] ?? 1,
            'role'     => $data['id_role']
        ]);
    }

    public static function update(string $id, array $data): bool {
        if (!empty($data['password'])) {
            $sql = "UPDATE anggota 
                    SET username = :username, password = :password, nama_anggota = :nama, 
                        status_aktif = :status, id_role = :role 
                    WHERE id_anggota = :id";
            return Database::execute($sql, [
                'id'       => $id,
                'username' => $data['username'],
                'password' => password_hash($data['password'], PASSWORD_BCRYPT),
                'nama'     => $data['nama_anggota'],
                'status'   => $data['status_aktif'],
                'role'     => $data['id_role']
            ]);
        } else {
            $sql = "UPDATE anggota 
                    SET username = :username, nama_anggota = :nama, 
                        status_aktif = :status, id_role = :role 
                    WHERE id_anggota = :id";
            return Database::execute($sql, [
                'id'       => $id,
                'username' => $data['username'],
                'nama'     => $data['nama_anggota'],
                'status'   => $data['status_aktif'],
                'role'     => $data['id_role']
            ]);
        }
    }

    public static function delete(string $id): bool {
        $sql = "DELETE FROM anggota WHERE id_anggota = :id";
        return Database::execute($sql, ['id' => $id]);
    }

        public static function updatePassword(string $id, string $newPassword): bool {
        $sql = "UPDATE anggota SET password = :password WHERE id_anggota = :id";
        return Database::execute($sql, [
            'id'       => $id,
            'password' => password_hash($newPassword, PASSWORD_BCRYPT)
        ]);
    }

    public static function getRoles(): array {
        return Database::query("SELECT * FROM role ORDER BY id_role ASC");
    }
}
