<?php
namespace App\Controllers;

use App\Core\Helper;
use App\Core\View;
use App\Models\Pesanan;

class TrackingController {
    public function index(): void {
        $keyword = Helper::sanitize($_GET['nota'] ?? '');
        $order = null;
        $error = null;

        if (!empty($keyword)) {
            // Cek langsung berdasarkan invoice
            $order = Pesanan::findWithDetails($keyword);

            if (!$order) {
                // Jika tidak ketemu, coba cari like
                $candidates = Pesanan::all(null, $keyword);
                if (!empty($candidates)) {
                    $order = Pesanan::findWithDetails($candidates[0]['id_pesanan']);
                }
            }

            if (!$order) {
                $error = "Nomor nota '{$keyword}' tidak ditemukan. Pastikan nomor nota yang Anda masukkan sesuai.";
            } else {
                $order['sla_info'] = Helper::calculateSla($order['tanggal_keluar']);
            }
        }

        View::render('tracking/index', [
            'title'   => 'Lacak Status Cucian - LaundryGo',
            'keyword' => $keyword,
            'order'   => $order,
            'error'   => $error
        ], 'blank');
    }
}
