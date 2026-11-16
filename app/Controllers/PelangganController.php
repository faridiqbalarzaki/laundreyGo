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

        $nama = Helper::sanitize($_POST['nama_pelanggan'] ?? '');
        $telp = Helper::sanitize($_POST['no_telepon'] ?? '');
        $alamat = Helper::sanitize($_POST['alamat'] ?? '');

        if (empty($nama) || empty($telp)) {
            Helper::setFlash('error', 'Nama dan nomor telepon pelanggan wajib diisi.');
            Helper::redirect(Helper::url('pelanggan'));
        }

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
        $nama = Helper::sanitize($_POST['nama_pelanggan'] ?? '');
        $telp = Helper::sanitize($_POST['no_telepon'] ?? '');
        $alamat = Helper::sanitize($_POST['alamat'] ?? '');

        if (empty($id) || empty($nama) || empty($telp)) {
            Helper::setFlash('error', 'Semua data wajib diisi dengan benar.');
            Helper::redirect(Helper::url('pelanggan'));
        }

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
