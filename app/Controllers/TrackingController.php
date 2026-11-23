<?php
namespace App\Controllers;

use App\Core\Helper;
use App\Core\View;
use App\Models\Pesanan;

class TrackingController {
    public function index(): void {
        $rawNota = trim($_GET['nota'] ?? '');
        $keyword = strtoupper(strip_tags($rawNota));
        $order = null;
        $error = null;
        $errorType = null;

        if (!empty($keyword)) {
            // Validasi format nomor nota standar LaundryGo (LG-YYMMDD-XXX)
            // Validasi format nomor nota standar LaundryGo (angka, contoh: 001)
if (!Helper::isValidInvoice($keyword)) {
    $error = "Format nomor nota '{$keyword}' tidak sesuai standar. Contoh format yang benar: 001.";
            } else {
                // Pencarian exact hanya berdasarkan ID Invoice resmi (mencegah data leak dan SQL Injection)
                $order = Pesanan::findWithDetails($keyword);

                if (!$order) {
                    $error = "Nomor nota '{$keyword}' tidak ditemukan di sistem. Pastikan nomor nota sesuai dengan yang tertera pada struk fisik kasir Anda.";
                    $errorType = 'not_found';
                } else {
                    $order['sla_info'] = Helper::calculateSla($order['tanggal_keluar']);
                }
            }
        }

        View::render('tracking/index', [
            'title'     => 'Lacak Status Cucian - LaundryGo Aqua',
            'keyword'   => $keyword,
            'order'     => $order,
            'error'     => $error,
            'errorType' => $errorType
        ], 'blank');
    }
}
