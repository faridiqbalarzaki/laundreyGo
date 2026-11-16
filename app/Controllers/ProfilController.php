<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Helper;
use App\Models\Anggota;
use App\Models\LogAktivitas;

class ProfilController {
    /**
     * Tampilan Halaman Profil & Pengaturan Akun
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

        $oldPassword = (string)($_POST['old_password'] ?? '');
        $newPassword = (string)($_POST['new_password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');

        if (empty($oldPassword)) {
            Helper::setFlash('error', 'Masukkan kata sandi saat ini.');
            Helper::redirect(Helper::url('profil'));
            return;
        }

        if (strlen($newPassword) < 6) {
            Helper::setFlash('error', 'Kata sandi baru minimal 6 karakter.');
            Helper::redirect(Helper::url('profil'));
            return;
        }

        if ($newPassword !== $confirmPassword) {
            Helper::setFlash('error', 'Konfirmasi kata sandi baru tidak cocok.');
            Helper::redirect(Helper::url('profil'));
            return;
        }

        // Ambil data user terkini dari database untuk memverifikasi password lama
        $currentUser = Anggota::findById($user['id_anggota']);
        if (!$currentUser) {
            Helper::setFlash('error', 'Data akun pengguna tidak ditemukan.');
            Helper::redirect(Helper::url('profil'));
            return;
        }

        // Cek verifikasi password lama (mendukung hash bcrypt dan fallback plain text akun awal)
        $isOldValid = password_verify($oldPassword, $currentUser['password']) || ($oldPassword === $currentUser['password']);
        if (!$isOldValid) {
            Helper::setFlash('error', 'Kata sandi saat ini yang Anda masukkan salah.');
            Helper::redirect(Helper::url('profil'));
            return;
        }

        // Simpan hash kata sandi baru ke database PostgreSQL
        $updated = Anggota::updatePassword($user['id_anggota'], $newPassword);
        if ($updated) {
            LogAktivitas::log('Ubah Kata Sandi', "Pengguna {$user['username']} ({$user['id_anggota']}) berhasil memperbarui kata sandi akun", $user['id_anggota']);
            Helper::setFlash('success', 'Kata sandi akun Anda berhasil diperbarui!');
        } else {
            Helper::setFlash('error', 'Gagal memperbarui kata sandi di database.');
        }

        Helper::redirect(Helper::url('profil'));
    }
}
