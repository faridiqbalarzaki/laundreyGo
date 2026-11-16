<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Helper;
use App\Core\View;
use App\Models\Pesanan;
use App\Models\LogAktivitas;

class PesananController {
    public function index(): void {
        Auth::requireLogin();

        $status = $_GET['status'] ?? 'Semua';
        $keyword = $_GET['q'] ?? '';

        $orders = Pesanan::all($status, $keyword);

        View::render('pesanan/index', [
            'title'   => 'Daftar Pesanan - LaundryGo',
            'orders'  => $orders,
            'status'  => $status,
            'keyword' => $keyword
        ]);
    }

    /**
     * Halaman Cucian Selesai & Rak Pengambilan
     */
    public function selesai(): void {
        Auth::requireRole(['Admin', 'Kasir']);

        $rak = $_GET['rak'] ?? 'Semua';
        $orders = Pesanan::getSiapAmbil($rak);

        View::render('pesanan/selesai', [
            'title'     => 'Siap Diambil & Rak - LaundryGo',
            'orders'    => $orders,
            'activeRak' => $rak
        ]);
    }

    /**
     * Detail Pesanan (JSON untuk Drawer Interaktif)
     */
    public function detail(): void {
        Auth::requireLogin();
        $id = $_GET['id'] ?? '';
        $order = Pesanan::findWithDetails($id);

        if (!$order) {
            Helper::jsonResponse(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        // Hitung SLA countdown
        $order['sla_info'] = Helper::calculateSla($order['tanggal_keluar']);
        $order['tanggal_masuk_formatted'] = Helper::formatDate($order['tanggal_masuk'], true);
        $order['tanggal_keluar_formatted'] = Helper::formatDate($order['tanggal_keluar'], true);
        $order['total_harga_formatted'] = Helper::rupiah($order['total_harga']);

        Helper::jsonResponse(['success' => true, 'data' => $order]);
    }

    /**
     * Update Status Pesanan (Diterima, Dicuci, Disetrika, Siap Diambil, Selesai, Dibatalkan)
     */
    public function updateStatus(): void {
        Auth::requireLogin();

        $id = Helper::sanitize($_POST['id_pesanan'] ?? '');
        $status = Helper::sanitize($_POST['status_cucian'] ?? '');
        $catatan = Helper::sanitize($_POST['keterangan'] ?? '');
        $rak = !empty($_POST['lokasi_rak']) ? Helper::sanitize($_POST['lokasi_rak']) : null;

        if (empty($id) || empty($status)) {
            Helper::setFlash('error', 'Data update status tidak lengkap.');
            Helper::redirect(Helper::url('pesanan'));
        }

        $success = Pesanan::updateStatus($id, $status, Auth::id(), $catatan, $rak);

        if ($success) {
            LogAktivitas::log('Update Status Pesanan', "Pesanan {$id} diubah ke {$status}", Auth::id());
            Helper::setFlash('success', "Status pesanan {$id} berhasil diubah ke {$status}.");
        } else {
            Helper::setFlash('error', "Gagal memperbarui status pesanan {$id}.");
        }

        // Kembali ke halaman perujuk aman (whitelist agar tidak terkena open-redirect)
        $allowedRedirects = ['pesanan', 'produksi', 'dashboard', 'pos'];
        $redirectPage = in_array($_POST['redirect_to'] ?? '', $allowedRedirects, true) ? $_POST['redirect_to'] : 'pesanan';
        Helper::redirect(Helper::url($redirectPage));
    }

    /**
     * Pengambilan Cucian & Pelunasan Kasir
     */
    public function ambil(): void {
        Auth::requireRole(['Admin', 'Kasir']);

        $id = Helper::sanitize($_POST['id_pesanan'] ?? '');
        $order = Pesanan::findById($id);

        if (!$order) {
            Helper::setFlash('error', 'Pesanan tidak ditemukan.');
            Helper::redirect(Helper::url('pesanan', ['action' => 'selesai']));
        }

        // Jika belum lunas, proses pembayaran pelunasan
        if ($order['status_pembayaran'] !== 'Lunas') {
            $bayar = (float)($_POST['jumlah_bayar'] ?? $order['total_harga']);
            $metode = $_POST['id_metode'] ?? 'MET-01';
            $kembali = max(0, $bayar - (float)$order['total_harga']);

            $paid = Pesanan::bayarPelunasan($id, $bayar, $kembali, $metode, Auth::id());
            if (!$paid) {
                Helper::setFlash('error', "Gagal memproses pelunasan pesanan {$id}.");
                Helper::redirect(Helper::url('pesanan', ['action' => 'selesai']));
            }
        }

        // Update status menjadi 'Selesai' (Sudah diambil pelanggan)
        Pesanan::updateStatus($id, 'Selesai', Auth::id(), 'Pesanan telah diambil oleh pelanggan');

        LogAktivitas::log('Pengambilan Pesanan', "Pesanan {$id} diambil oleh pelanggan", Auth::id());
        Helper::setFlash('success', "Pesanan {$id} berhasil diserahkan ke pelanggan.");
        Helper::redirect(Helper::url('pesanan', ['action' => 'selesai']));
    }
}
