<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Helper;
use App\Core\View;
use App\Models\Anggota;
use App\Models\LogAktivitas;

class AuthController {
    /**
     * Tampilkan halaman login
     */
    public function login(): void {
        if (Auth::check()) {
            if (Auth::isPetugas()) {
                Helper::redirect(Helper::url('produksi'));
            } elseif (Auth::isKasir()) {
                Helper::redirect(Helper::url('pos'));
            } else {
                Helper::redirect(Helper::url('dashboard'));
            }
        }

        View::render('auth/login', [
            'title' => 'Login - LaundryGo Aqua'
        ], 'auth');
    }

    /**
     * Proses autentikasi login
     */
    public function doLogin(): void {
        $username = Helper::sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            Helper::setFlash('error', 'Username dan password wajib diisi.');
            Helper::redirect(Helper::url('login'));
        }

        $user = Anggota::authenticate($username, $password);

        if ($user) {
            Auth::login($user);
            LogAktivitas::log('Login', 'User login ke sistem dari IP ' . ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'), $user['id_anggota']);
            
            Helper::setFlash('success', 'Selamat datang kembali, ' . $user['nama_anggota'] . '!');

            if (Auth::isPetugas()) {
                Helper::redirect(Helper::url('produksi'));
            } elseif (Auth::isKasir()) {
                Helper::redirect(Helper::url('pos'));
            } else {
                Helper::redirect(Helper::url('dashboard'));
            }
        } else {
            Helper::setFlash('error', 'Username atau password salah. Silakan coba lagi.');
            Helper::redirect(Helper::url('login'));
        }
    }

    /**
     * Logout
     */
    public function logout(): void {
        if (Auth::check()) {
            LogAktivitas::log('Logout', 'User keluar dari sistem', Auth::id());
        }
        Auth::logout();
        Helper::setFlash('success', 'Anda telah berhasil logout.');
        Helper::redirect(Helper::url('login'));
    }
}
