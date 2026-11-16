<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Helper;
use App\Core\View;
use App\Models\Pengeluaran;
use App\Models\LogAktivitas;

class PengeluaranController {
    public function index(): void {
        Auth::requireRole(['Admin']);

        $kategori = $_GET['kategori'] ?? 'Semua';
        $bulan = $_GET['bulan'] ?? date('Y-m');

        $pengeluaran = Pengeluaran::all($kategori, $bulan);
        $kategoriList = Pengeluaran::getKategoriList();

        $totalPengeluaran = array_reduce($pengeluaran, function($carry, $item) {
            return $carry + (float)$item['jumlah'];
        }, 0);

        View::render('pengeluaran/index', [
            'title'            => 'Pengeluaran Operasional',
            'subtitle'         => 'Catatan belanja deterjen, plastik, token listrik, dan rekap beban usaha harian',
            'pengeluaran'      => $pengeluaran,
            'kategoriList'     => $kategoriList,
            'activeKategori'   => $kategori,
            'activeBulan'      => $bulan,
            'totalPengeluaran' => $totalPengeluaran
        ]);
    }

    public function store(): void {
        Auth::requireRole(['Admin']);

        $tgl = Helper::sanitize($_POST['tanggal_pengeluaran'] ?? date('Y-m-d'));
        $kategori = Helper::sanitize($_POST['id_kategori_pengeluaran'] ?? '');
        $keterangan = Helper::sanitize($_POST['keterangan'] ?? '');
        $jumlah = (float)($_POST['jumlah'] ?? 0);

        if (empty($kategori) || empty($keterangan) || $jumlah <= 0) {
            Helper::setFlash('error', 'Semua kolom pengeluaran wajib diisi dengan benar.');
            Helper::redirect(Helper::url('pengeluaran'));
        }

        $id = Pengeluaran::create([
            'tanggal_pengeluaran'    => $tgl,
            'id_kategori_pengeluaran'=> $kategori,
            'keterangan'             => $keterangan,
            'jumlah'                 => $jumlah,
            'id_anggota'             => Auth::id()
        ]);

        LogAktivitas::log('Catat Pengeluaran', "Pengeluaran {$id} sebesar Rp " . number_format($jumlah, 0, ',', '.') . " untuk {$keterangan}", Auth::id());
        Helper::setFlash('success', 'Pengeluaran baru berhasil dicatat!');
        Helper::redirect(Helper::url('pengeluaran'));
    }

    public function update(): void {
        Auth::requireRole(['Admin']);

        $id = Helper::sanitize($_POST['id_pengeluaran'] ?? '');
        $tgl = Helper::sanitize($_POST['tanggal_pengeluaran'] ?? date('Y-m-d H:i:s'));
        $kategori = Helper::sanitize($_POST['id_kategori_pengeluaran'] ?? '');
        $keterangan = Helper::sanitize($_POST['keterangan'] ?? '');
        $jumlah = (float)($_POST['jumlah'] ?? 0);

        if (empty($id) || empty($kategori) || empty($keterangan) || $jumlah <= 0) {
            Helper::setFlash('error', 'Semua kolom pengeluaran wajib diisi dengan benar.');
            Helper::redirect(Helper::url('pengeluaran'));
        }

        Pengeluaran::update($id, [
            'tanggal_pengeluaran'    => $tgl,
            'id_kategori_pengeluaran'=> $kategori,
            'keterangan'             => $keterangan,
            'jumlah'                 => $jumlah
        ]);

        LogAktivitas::log('Ubah Pengeluaran', "Memperbarui pengeluaran {$id} ({$keterangan})", Auth::id());
        Helper::setFlash('success', 'Catatan pengeluaran berhasil diperbarui!');
        Helper::redirect(Helper::url('pengeluaran'));
    }

    public function delete(): void {
        Auth::requireRole(['Admin']);

        $id = Helper::sanitize($_POST['id_pengeluaran'] ?? '');
        if ($id) {
            Pengeluaran::delete($id);
            LogAktivitas::log('Hapus Pengeluaran', "Menghapus pengeluaran {$id}", Auth::id());
            Helper::setFlash('success', "Catatan pengeluaran {$id} berhasil dihapus.");
        }

        Helper::redirect(Helper::url('pengeluaran'));
    }
}
