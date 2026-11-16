<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Helper;
use App\Core\View;
use App\Core\Database;
use App\Models\Pesanan;
use App\Models\Pengeluaran;

class DashboardController {
    public function index(): void {
        Auth::requireLogin();

        // Petugas diarahkan ke lantai produksi
        if (Auth::isPetugas()) {
            Helper::redirect(Helper::url('produksi'));
        }

        $metrics = Pesanan::getMetrics();
        $chartData = Pesanan::getChart14Days();
        $recentOrders = Pesanan::all(null, null);
        $recentOrders = array_slice($recentOrders, 0, 4);

        // Stage counts
        $sqlStages = "SELECT status_cucian, COUNT(*) AS total FROM pesanan WHERE status_cucian IN ('Antrean', 'Dicuci', 'Disetrika') GROUP BY status_cucian";
        $resStages = Database::query($sqlStages);
        $stageCounts = ['Antrean' => 0, 'Dicuci' => 0, 'Disetrika' => 0];
        foreach ($resStages as $st) {
            $stageCounts[$st['status_cucian']] = (int)$st['total'];
        }

        // Ready racks
        $sqlRacks = "SELECT DISTINCT lokasi_rak FROM pesanan WHERE status_cucian = 'Siap Diambil' AND lokasi_rak IS NOT NULL AND lokasi_rak != '' ORDER BY lokasi_rak ASC LIMIT 4";
        $racks = Database::query($sqlRacks);
        $readyRacks = array_column($racks, 'lokasi_rak');

        // Total orders count
        $sqlTotalOrders = "SELECT COUNT(*) as total FROM pesanan";
        $totalOrdersCount = (int)(Database::fetch($sqlTotalOrders)['total'] ?? 0);

        // Expense today
        $sqlExpToday = "SELECT COALESCE(SUM(jumlah), 0) AS total FROM pengeluaran WHERE tanggal_pengeluaran = CURRENT_DATE";
        $expToday = (float)(Database::fetch($sqlExpToday)['total'] ?? 0);

        // Unpaid counts
        $sqlUnpaid = "SELECT COUNT(*) as total_count, COALESCE(SUM(total_harga), 0) as total_nominal FROM pesanan WHERE status_pembayaran = 'Belum Lunas' AND status_cucian != 'Dibatalkan'";
        $unpaidInfo = Database::fetch($sqlUnpaid);
        $unpaidCount = (int)($unpaidInfo['total_count'] ?? 0);
        $unpaidTotal = (float)($unpaidInfo['total_nominal'] ?? 0);

        // Dynamic Calculations
        $omzet = (float)($metrics['omzet_today'] ?? 0);
        $netProfit = $omzet - $expToday;
        $cashEfficiency = $omzet > 0 ? max(0, min(100, round(($netProfit / $omzet) * 100))) : ($expToday > 0 ? 0 : 100);

        $activeTotal = (int)($metrics['total_aktif'] ?? 0);
        $readyTotal = (int)($metrics['total_siap'] ?? 0);
        $completedRatio = ($activeTotal + $readyTotal) > 0 ? round(($readyTotal / ($activeTotal + $readyTotal)) * 100) : 100;

        // Payment method distribution today (Tunai vs Digital)
        $sqlPaymentDist = "SELECT mp.nama_metode, COALESCE(SUM(pb.jumlah_bayar - pb.uang_kembalian), 0) AS total
                           FROM metode_pembayaran mp
                           LEFT JOIN pembayaran pb ON mp.id_metode = pb.id_metode AND DATE(pb.tanggal_pembayaran) = CURRENT_DATE
                           GROUP BY mp.id_metode, mp.nama_metode";
        $paymentDist = Database::query($sqlPaymentDist);
        $cashTotal = 0;
        $allPayTotal = 0;
        foreach ($paymentDist as $pd) {
            $amt = (float)$pd['total'];
            $allPayTotal += $amt;
            if (stripos($pd['nama_metode'], 'Tunai') !== false) {
                $cashTotal += $amt;
            }
        }
        $cashPercent = $allPayTotal > 0 ? round(($cashTotal / $allPayTotal) * 100) : 100;

        // Generate dynamic SVG sparkline coordinates (viewBox 0 0 300 70)
        $maxSparkVal = 1;
        foreach ($chartData as $cd) {
            if ($cd['omset'] > $maxSparkVal) $maxSparkVal = $cd['omset'];
        }
        $polyPoints = [];
        $stepX = 300 / max(1, count($chartData) - 1);
        foreach (array_values($chartData) as $idx => $cd) {
            $x = round($idx * $stepX, 1);
            $ratio = $maxSparkVal > 0 ? ($cd['omset'] / $maxSparkVal) : 0;
            $y = round(58 - ($ratio * 46), 1);
            $polyPoints[] = "{$x},{$y}";
        }
        $sparkPolyline = implode(' ', $polyPoints);
        $sparkPolygon = "0,70 " . $sparkPolyline . " 300,70";
        $lastPointParts = explode(',', end($polyPoints));
        $lastSparkX = $lastPointParts[0] ?? '300';
        $lastSparkY = $lastPointParts[1] ?? '12';

        View::render('dashboard/index', [
            'title'             => 'Dashboard',
            'subtitle'          => 'Ringkasan omset, antrean produksi, dan cucian selesai',
            'metrics'           => $metrics,
            'chartData'         => $chartData,
            'recentOrders'      => $recentOrders,
            'stageCounts'       => $stageCounts,
            'readyRacks'        => $readyRacks,
            'totalOrdersCount'  => $totalOrdersCount,
            'expToday'          => $expToday,
            'unpaidCount'       => $unpaidCount,
            'unpaidTotal'       => $unpaidTotal,
            'netProfit'         => $netProfit,
            'cashEfficiency'    => $cashEfficiency,
            'completedRatio'    => $completedRatio,
            'cashPercent'       => $cashPercent,
            'sparkPolyline'     => $sparkPolyline,
            'sparkPolygon'      => $sparkPolygon,
            'lastSparkX'        => $lastSparkX,
            'lastSparkY'        => $lastSparkY
        ]);
    }
}
