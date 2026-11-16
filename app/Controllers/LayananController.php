<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Helper;
use App\Core\View;
use App\Models\Layanan;
use App\Models\LogAktivitas;

class LayananController {
    public function index(): void {
        Auth::requireRole(['Admin', 'Owner', 'Kasir']);

        // Admin melihat semua layanan agar dapat mengaktifkan kembali yang nonaktif
        $layanan = Auth::isAdmin() ? Layanan::all() : Layanan::getActive();
        $kategoriList = Layanan::getKategoriList();

        View::render('layanan/index', [
            'title'        => 'Tarif & Layanan – LaundryGo',
            'subtitle'     => 'Atur daftar layanan, harga per kg/satuan, dan metode pembayaran',
            'layanan'      => $layanan,
            'kategoriList' => $kategoriList,
            'isKasir'      => Auth::isKasir()
        ]);
    }

    public function store(): void {
        Auth::requireRole(['Admin']);

        $nama = Helper::sanitize($_POST['nama_layanan'] ?? '');
        $harga = (float)($_POST['harga'] ?? 0);
        $estimasi = (int)($_POST['estimasi_waktu'] ?? 24);
        $satuan = Helper::sanitize($_POST['satuan'] ?? 'kg');
        $kategori = Helper::sanitize($_POST['id_kategori'] ?? '');

        if (empty($nama) || $harga <= 0 || empty($kategori)) {
            Helper::setFlash('error', 'Semua kolom layanan wajib diisi.');
            Helper::redirect(Helper::url('layanan'));
        }

        $id = Layanan::create([
            'nama_layanan'   => $nama,
            'harga'          => $harga,
            'estimasi_waktu' => $estimasi,
            'satuan'         => $satuan,
            'status_aktif'   => 1,
            'id_kategori'    => $kategori
        ]);

        LogAktivitas::log('Tambah Layanan', "Menambahkan layanan baru {$nama} ({$id})", Auth::id());
        Helper::setFlash('success', "Layanan {$nama} berhasil ditambahkan!");
        Helper::redirect(Helper::url('layanan'));
    }

    public function update(): void {
        Auth::requireRole(['Admin']);

        $id = Helper::sanitize($_POST['id_layanan'] ?? '');
        $nama = Helper::sanitize($_POST['nama_layanan'] ?? '');
        $harga = (float)($_POST['harga'] ?? 0);
        $estimasi = (int)($_POST['estimasi_waktu'] ?? 24);
        $satuan = Helper::sanitize($_POST['satuan'] ?? 'kg');
        $kategori = Helper::sanitize($_POST['id_kategori'] ?? '');
        $status = isset($_POST['status_aktif']) ? 1 : 0;

        if (empty($id) || empty($nama) || $harga <= 0) {
            Helper::setFlash('error', 'Data layanan tidak valid.');
            Helper::redirect(Helper::url('layanan'));
        }

        $existing = Layanan::findById($id);
        if (!$existing) {
            Helper::setFlash('error', 'Layanan tidak ditemukan.');
            Helper::redirect(Helper::url('layanan'));
        }
        if (empty($kategori)) {
            $kategori = $existing['id_kategori'];
        }

        Layanan::update($id, [
            'nama_layanan'   => $nama,
            'harga'          => $harga,
            'estimasi_waktu' => $estimasi,
            'satuan'         => $satuan,
            'status_aktif'   => $status,
            'id_kategori'    => $kategori
        ]);

        LogAktivitas::log('Edit Layanan', "Mengubah data layanan {$id}", Auth::id());
        Helper::setFlash('success', "Tarif & layanan {$nama} berhasil diperbarui!");
        Helper::redirect(Helper::url('layanan'));
    }

    public function delete(): void {
        Auth::requireRole(['Admin']);

        $id = Helper::sanitize($_POST['id_layanan'] ?? '');
        if ($id) {
            try {
                Layanan::delete($id);
                LogAktivitas::log('Hapus Layanan', "Menghapus layanan {$id}", Auth::id());
                Helper::setFlash('success', "Layanan {$id} berhasil dihapus.");
            } catch (\PDOException $e) {
                // Relasi foreign key ke detail_pesanan
                Helper::setFlash('error', "Layanan tidak dapat dihapus permanen karena terdapat dalam riwayat pesanan. Anda dapat menonaktifkan status layanan.");
            }
        }

        Helper::redirect(Helper::url('layanan'));
    }

    public function toggle(): void {
        Auth::requireRole(['Admin']);

        $id = Helper::sanitize($_POST['id_layanan'] ?? '');
        $item = Layanan::findById($id);

        if ($item) {
            $newStatus = ($item['status_aktif'] == 1) ? 0 : 1;
            $sql = "UPDATE layanan SET status_aktif = :st WHERE id_layanan = :id";
            \App\Core\Database::execute($sql, ['st' => $newStatus, 'id' => $id]);
            $label = $newStatus == 1 ? 'diaktifkan kembali' : 'dinonaktifkan';
            LogAktivitas::log('Toggle Layanan', "Layanan {$item['nama_layanan']} ({$id}) {$label}", Auth::id());
            Helper::setFlash('success', "Layanan {$item['nama_layanan']} berhasil {$label}!");
        } else {
            Helper::setFlash('error', "Layanan tidak ditemukan.");
        }

        Helper::redirect(Helper::url('layanan'));
    }
}
