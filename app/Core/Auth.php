<?php
namespace App\Core;

/**
 * Class Auth
 * Mengelola autentikasi berbasis session, peran user (Role RBAC), dan proteksi route
 */
class Auth {
    /**
     * Memastikan session PHP aktif
     */
    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Local development session bypass for visual verification
        if (!empty($_GET['dev_role']) && in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'])) {
            $r = strtolower($_GET['dev_role']);
            if ($r === 'admin') {
                $_SESSION['user'] = ['id_anggota' => 'USR-001', 'username' => 'admin', 'nama_anggota' => 'Budi Santoso', 'id_role' => 'ADM', 'nama_role' => 'Admin'];
            } elseif ($r === 'kasir') {
                $_SESSION['user'] = ['id_anggota' => 'USR-002', 'username' => 'siti', 'nama_anggota' => 'Siti Rahayu', 'id_role' => 'KAS', 'nama_role' => 'Kasir'];
            } elseif ($r === 'petugas') {
                $_SESSION['user'] = ['id_anggota' => 'USR-003', 'username' => 'petugas', 'nama_anggota' => 'Budi Santoso', 'id_role' => 'PET', 'nama_role' => 'Petugas Produksi'];
            }

            if (!empty($_GET['test_flash'])) {
                $t = $_GET['test_flash'];
                $_SESSION['flash'][$t] = $_GET['test_msg'] ?? 'Selamat datang kembali, Kasir Outlet!';
            }
        }
    }

    /**
     * Memeriksa apakah user sudah login
     */
    public static function check(): bool {
        self::init();
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id_anggota']);
    }

    /**
     * Mengambil data user yang sedang login
     */
    public static function user(): ?array {
        self::init();
        return $_SESSION['user'] ?? null;
    }

    /**
     * Mengambil ID anggota aktif
     */
    public static function id(): ?string {
        return self::user()['id_anggota'] ?? null;
    }

    /**
     * Mengambil nama role user aktif
     */
    public static function role(): ?string {
        return self::user()['nama_role'] ?? null;
    }

    /**
     * Mengambil ID role user aktif
     */
    public static function roleId(): ?string {
        return self::user()['id_role'] ?? null;
    }

    /**
     * Set data session login user
     */
    public static function login(array $userData): void {
        self::init();
        $_SESSION['user'] = [
            'id_anggota'   => $userData['id_anggota'],
            'username'     => $userData['username'],
            'nama_anggota' => $userData['nama_anggota'],
            'id_role'      => $userData['id_role'],
            'nama_role'    => $userData['nama_role'] ?? 'User'
        ];
    }

    /**
     * Logout dan hancurkan session
     */
    public static function logout(): void {
        self::init();
        unset($_SESSION['user']);
        session_destroy();
    }

    /**
     * Cek apakah role user cocok dengan salah satu role yang diizinkan
     * @param string|array $roles Contoh: 'Admin' atau ['Admin', 'Kasir']
     */
    public static function hasRole($roles): bool {
        if (!self::check()) {
            return false;
        }

        $currentRole = self::role();
        if (is_array($roles)) {
            return in_array($currentRole, $roles, true);
        }

        return strcasecmp((string)$currentRole, (string)$roles) === 0;
    }

    /**
     * Helper cek khusus role
     */
    public static function isAdmin(): bool {
        return self::hasRole('Admin');
    }

    public static function isKasir(): bool {
        return self::hasRole('Kasir');
    }

    public static function isPetugas(): bool {
        return self::hasRole('Petugas Produksi') || self::hasRole('Petugas');
    }

    /**
     * Proteksi halaman: jika belum login, lempar ke halaman login
     */
    public static function requireLogin(): void {
        if (!self::check()) {
            Helper::setFlash('error', 'Silakan login terlebih dahulu untuk mengakses sistem.');
            Helper::redirect('index.php?page=login');
        }

        // Validasi keamanan: jika akun dinonaktifkan oleh Admin, putus sesi login seketika
        $uid = self::id();
        if ($uid && empty($_GET['dev_role'])) {
            $userDb = Database::fetch("SELECT status_aktif FROM anggota WHERE id_anggota = :id", ['id' => $uid]);
            if ($userDb && (int)$userDb['status_aktif'] === 0) {
                self::logout();
                Helper::setFlash('error', 'Akun Anda telah dinonaktifkan oleh administrator. Silakan hubungi admin gerai.');
                Helper::redirect('index.php?page=login');
            }
        }
    }

    /**
     * Proteksi halaman berdasarkan role
     */
    public static function requireRole($allowedRoles): void {
        self::requireLogin();
        if (!self::hasRole($allowedRoles)) {
            Helper::setFlash('error', 'Akses ditolak: Anda tidak memiliki izin untuk halaman tersebut.');
            // Arahkan ke halaman utama sesuai role
            if (self::isPetugas()) {
                Helper::redirect('index.php?page=produksi');
            } elseif (self::isKasir()) {
                Helper::redirect('index.php?page=pos');
            } else {
                Helper::redirect('index.php?page=dashboard');
            }
        }
    }
}
