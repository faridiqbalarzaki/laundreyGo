<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Helper;
use App\Models\Pengguna;

class ProfilController {
    /**
     * Tampilan Halaman Profil & Pengaturan Akun (Video 2)
     */
    public function index(): void {
        Auth::requireLogin();
        $user = Auth::user();

        View::render('profil/index', [
            'title' => 'Profil & Pengaturan Akun - LaundryGo',
            'user'  => $user
        ]);
    }

    /**
     * Simpan Perubahan Kata Sandi
     */
    public function updatePassword(): void {
        Auth::requireLogin();
        $user = Auth::user();

        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($oldPassword)) {
            Helper::flash('error', 'Masukkan kata sandi saat ini.');
            Helper::redirect('profil');
            return;
        }

        if (strlen($newPassword) < 6) {
            Helper::flash('error', 'Kata sandi baru minimal 6 karakter.');
            Helper::redirect('profil');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            Helper::flash('error', 'Konfirmasi kata sandi baru tidak cocok.');
            Helper::redirect('profil');
            return;
        }

        // Jika berhasil
        Helper::flash('success', 'Kata sandi berhasil diperbarui.');
        Helper::redirect('profil');
    }
}
