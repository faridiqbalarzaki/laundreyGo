<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Helper;
use App\Core\View;
use App\Models\Pelanggan;
use App\Models\LogAktivitas;

class PelangganController {
    public function index(): void {
        Auth::requireRole(['Admin', 'Kasir']);

        $pelanggan = Pelanggan::all();

        View::render('pelanggan/index', [
            'title'     => 'Data Pelanggan - LaundryGo',
            'pelanggan' => $pelanggan
        ]);
    }

    public function store(): void {
        Auth::requireRole(['Admin', 'Kasir']);

        $nama = trim(strip_tags($_POST['nama_pelanggan'] ?? ''));
        $rawTelp = trim($_POST['no_telepon'] ?? '');
        $alamat = trim(strip_tags($_POST['alamat'] ?? ''));

        if (empty($nama) || empty($rawTelp)) {
            Helper::setFlash('error', 'Nama dan nomor telepon pelanggan wajib diisi.');
            Helper::redirect(Helper::url('pelanggan'));
        }

        // Validasi format nama: minimal 2 karakter, hanya huruf, spasi, titik, apostrof, strip
        if (mb_strlen($nama) < 2 || mb_strlen($nama) > 100 || !preg_match('/^[a-zA-Z\s\.\'\-]+$/u', $nama)) {
            Helper::setFlash('error', 'Nama pelanggan tidak valid. Hanya boleh berupa huruf, spasi, titik, atau tanda hubung (min 2 karakter).');
            Helper::redirect(Helper::url('pelanggan'));
        }

        // Validasi nomor telepon Indonesia (+62 / 62 / 08, 10-15 digit)
        if (!Helper::isValidPhone($rawTelp)) {
            Helper::setFlash('error', 'Nomor telepon tidak valid. Masukkan nomor HP Indonesia yang benar (contoh: 08123456789 atau +628123456789).');
            Helper::redirect(Helper::url('pelanggan'));
        }

        $telp = Helper::normalizePhone($rawTelp);

        $id = Pelanggan::create([
            'nama_pelanggan' => $nama,
            'no_telepon'     => $telp,
            'alamat'         => $alamat
        ]);

        LogAktivitas::log('Tambah Pelanggan', "Menambahkan pelanggan baru {$nama} ({$id})", Auth::id());
        Helper::setFlash('success', "Pelanggan {$nama} berhasil ditambahkan!");
        Helper::redirect(Helper::url('pelanggan'));
    }

    public function update(): void {
        Auth::requireRole(['Admin', 'Kasir']);

        $id = Helper::sanitize($_POST['id_pelanggan'] ?? '');
        $nama = trim(strip_tags($_POST['nama_pelanggan'] ?? ''));
        $rawTelp = trim($_POST['no_telepon'] ?? '');
        $alamat = trim(strip_tags($_POST['alamat'] ?? ''));

        if (empty($id) || empty($nama) || empty($rawTelp)) {
            Helper::setFlash('error', 'Semua data wajib diisi dengan benar.');
            Helper::redirect(Helper::url('pelanggan'));
        }

        // Validasi format nama
        if (mb_strlen($nama) < 2 || mb_strlen($nama) > 100 || !preg_match('/^[a-zA-Z\s\.\'\-]+$/u', $nama)) {
            Helper::setFlash('error', 'Nama pelanggan tidak valid. Hanya boleh berupa huruf, spasi, titik, atau tanda hubung (min 2 karakter).');
            Helper::redirect(Helper::url('pelanggan'));
        }

        // Validasi nomor telepon
        if (!Helper::isValidPhone($rawTelp)) {
            Helper::setFlash('error', 'Nomor telepon tidak valid. Masukkan nomor HP Indonesia yang benar (contoh: 08123456789).');
            Helper::redirect(Helper::url('pelanggan'));
        }

        $telp = Helper::normalizePhone($rawTelp);

        Pelanggan::update($id, [
            'nama_pelanggan' => $nama,
            'no_telepon'     => $telp,
            'alamat'         => $alamat
        ]);

        LogAktivitas::log('Edit Pelanggan', "Mengubah data pelanggan {$id}", Auth::id());
        Helper::setFlash('success', "Data pelanggan {$nama} berhasil diperbarui!");
        Helper::redirect(Helper::url('pelanggan'));
    }

    public function delete(): void {
        Auth::requireRole(['Admin']);

        $id = Helper::sanitize($_POST['id_pelanggan'] ?? '');
        if ($id) {
            try {
                Pelanggan::delete($id);
                LogAktivitas::log('Hapus Pelanggan', "Menghapus pelanggan {$id}", Auth::id());
                Helper::setFlash('success', "Pelanggan {$id} berhasil dihapus.");
            } catch (\PDOException $e) {
                Helper::setFlash('error', "Pelanggan tidak dapat dihapus karena memiliki riwayat pesanan aktif atau selesai.");
            }
        }

        Helper::redirect(Helper::url('pelanggan'));
    }

    public function history(): void {
        Auth::requireRole(['Admin', 'Kasir']);
        $id = $_GET['id'] ?? '';
        $customer = Pelanggan::findById($id);
        if (!$customer) {
            Helper::jsonResponse(['success' => false, 'message' => 'Pelanggan tidak ditemukan'], 404);
        }

        $orders = Pelanggan::getOrderHistory($id);
        Helper::jsonResponse(['success' => true, 'customer' => $customer, 'orders' => $orders]);
    }
}
