<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Helper;
use App\Core\View;
use App\Models\Anggota;
use App\Models\LogAktivitas;

class PenggunaController {
    public function index(): void {
        Auth::requireRole(['Admin']);

        $users = Anggota::all();
        $roles = Anggota::getRoles();

        View::render('pengguna/index', [
            'title' => 'Manajemen Pengguna - LaundryGo',
            'users' => $users,
            'roles' => $roles
        ]);
    }

    public function store(): void {
        Auth::requireRole(['Admin']);

        $username = Helper::sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $nama = Helper::sanitize($_POST['nama_anggota'] ?? '');
        $role = Helper::sanitize($_POST['id_role'] ?? 'KAS');

        if (empty($username) || empty($password) || empty($nama)) {
            Helper::setFlash('error', 'Semua data pengguna wajib diisi.');
            Helper::redirect(Helper::url('pengguna'));
        }

        // Cek username duplikat
        if (Anggota::findByUsername($username)) {
            Helper::setFlash('error', "Username '{$username}' sudah digunakan.");
            Helper::redirect(Helper::url('pengguna'));
        }

        $id = Helper::generateId('USR-', 'anggota', 'id_anggota', 3);
        Anggota::create([
            'id_anggota'   => $id,
            'username'     => $username,
            'password'     => $password,
            'nama_anggota' => $nama,
            'status_aktif' => 1,
            'id_role'      => $role
        ]);

        LogAktivitas::log('Tambah Pengguna', "Menambahkan user {$username} ({$id})", Auth::id());
        Helper::setFlash('success', "Pengguna {$nama} berhasil ditambahkan!");
        Helper::redirect(Helper::url('pengguna'));
    }

    public function update(): void {
        Auth::requireRole(['Admin']);

        $id = Helper::sanitize($_POST['id_anggota'] ?? '');
        $username = Helper::sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $nama = Helper::sanitize($_POST['nama_anggota'] ?? '');
        $role = Helper::sanitize($_POST['id_role'] ?? 'KAS');
        $status = isset($_POST['status_aktif']) ? 1 : 0;

        if (empty($id) || empty($username) || empty($nama)) {
            Helper::setFlash('error', 'Data pengguna tidak valid.');
            Helper::redirect(Helper::url('pengguna'));
        }

        // Cek duplikasi username pada pengguna lain
        $existingUser = Anggota::findByUsername($username);
        if ($existingUser && $existingUser['id_anggota'] !== $id) {
            Helper::setFlash('error', "Username '{$username}' sudah digunakan oleh akun lain.");
            Helper::redirect(Helper::url('pengguna'));
        }

        // Cegah admin mengunci atau menurunkan hak akses akun sendiri secara tak sengaja
        if ($id === Auth::id()) {
            $status = 1;
            $role = 'ADM';
        }

        Anggota::update($id, [
            'username'     => $username,
            'password'     => $password,
            'nama_anggota' => $nama,
            'status_aktif' => $status,
            'id_role'      => $role
        ]);

        LogAktivitas::log('Edit Pengguna', "Mengubah data user {$id}", Auth::id());
        Helper::setFlash('success', "Data pengguna {$nama} berhasil diperbarui!");
        Helper::redirect(Helper::url('pengguna'));
    }

    public function delete(): void {
        Auth::requireRole(['Admin']);

        $id = Helper::sanitize($_POST['id_anggota'] ?? '');
        if ($id === Auth::id()) {
            Helper::setFlash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            Helper::redirect(Helper::url('pengguna'));
        }

        if ($id) {
            try {
                Anggota::delete($id);
                LogAktivitas::log('Hapus Pengguna', "Menghapus user {$id}", Auth::id());
                Helper::setFlash('success', "Pengguna {$id} berhasil dihapus.");
            } catch (\PDOException $e) {
                Helper::setFlash('error', "Akun pengguna tidak dapat dihapus karena memiliki riwayat transaksi/aktivitas operasional. Anda dapat menonaktifkan status akun.");
            }
        }

        Helper::redirect(Helper::url('pengguna'));
    }

    public function toggle(): void {
        Auth::requireRole(['Admin']);

        $id = Helper::sanitize($_POST['id_anggota'] ?? '');
        if ($id === Auth::id()) {
            Helper::setFlash('error', 'Anda tidak dapat menonaktifkan akun yang sedang digunakan.');
            Helper::redirect(Helper::url('pengguna'));
        }

        $user = Anggota::findById($id);
        if ($user) {
            $newStatus = ($user['status_aktif'] == 1) ? 0 : 1;
            $sql = "UPDATE anggota SET status_aktif = :st WHERE id_anggota = :id";
            \App\Core\Database::execute($sql, ['st' => $newStatus, 'id' => $id]);
            $label = $newStatus == 1 ? 'diaktifkan kembali' : 'dinonaktifkan';
            LogAktivitas::log('Toggle Pengguna', "Akun {$user['nama_anggota']} ({$id}) {$label}", Auth::id());
            Helper::setFlash('success', "Status akun {$user['nama_anggota']} berhasil {$label}!");
        } else {
            Helper::setFlash('error', "Pengguna tidak ditemukan.");
        }

        Helper::redirect(Helper::url('pengguna'));
    }
}
