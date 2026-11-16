<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Helper;
use App\Core\View;

class LaporanController {
    public function index(): void {
        Auth::requireRole(['Admin']);

        $period = $_GET['period'] ?? 'bulan';

        if ($period === 'hari') {
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
        } elseif ($period === 'minggu') {
            $startDate = date('Y-m-d', strtotime('-7 days'));
            $endDate = date('Y-m-d');
        } else {
            // Default: bulan ini
            $startDate = $_GET['start'] ?? date('Y-m-01');
            $endDate = $_GET['end'] ?? date('Y-m-d');
        }

        // 1. Total Pendapatan / Omzet
        $sqlOmzet = "SELECT COALESCE(SUM(jumlah_bayar - uang_kembalian), 0) AS total_omzet,
                            COUNT(id_pembayaran) AS total_transaksi
                     FROM pembayaran
                     WHERE DATE(tanggal_pembayaran) BETWEEN :start AND :end";
        $omzetData = Database::fetch($sqlOmzet, ['start' => $startDate, 'end' => $endDate]);
        $totalOmzet = (float)($omzetData['total_omzet'] ?? 0);
        $totalTransaksi = (int)($omzetData['total_transaksi'] ?? 0);

        // 2. Total Pengeluaran
        $sqlExp = "SELECT COALESCE(SUM(jumlah), 0) AS total_exp,
                          COUNT(id_pengeluaran) AS total_catatan
                   FROM pengeluaran
                   WHERE DATE(tanggal_pengeluaran) BETWEEN :start AND :end";
        $expData = Database::fetch($sqlExp, ['start' => $startDate, 'end' => $endDate]);
        $totalPengeluaran = (float)($expData['total_exp'] ?? 0);
        $totalCatatanExp = (int)($expData['total_catatan'] ?? 0);

        // 3. Laba Bersih & Margin
        $labaBersih = $totalOmzet - $totalPengeluaran;
        $margin = $totalOmzet > 0 ? max(0, min(100, round(($labaBersih / $totalOmzet) * 100))) : 0;

        // 4. Breakdown per Metode Pembayaran (Ensure Tunai, E-Wallet, Transfer bank)
        $sqlMetode = "SELECT mp.nama_metode, 
                             COALESCE(SUM(pb.jumlah_bayar - pb.uang_kembalian), 0) AS total,
                             COUNT(pb.id_pembayaran) AS jumlah_trx
                      FROM metode_pembayaran mp
                      LEFT JOIN pembayaran pb ON mp.id_metode = pb.id_metode 
                           AND DATE(pb.tanggal_pembayaran) BETWEEN :start AND :end
                      GROUP BY mp.id_metode, mp.nama_metode
                      ORDER BY total DESC";
        $breakdownMetode = Database::query($sqlMetode, ['start' => $startDate, 'end' => $endDate]);

        // 5. Piutang (Pesanan belum lunas)
        $sqlPiutang = "SELECT COALESCE(SUM(total_harga), 0) AS total_piutang,
                              COUNT(id_pesanan) AS jumlah_piutang
                       FROM pesanan
                       WHERE LOWER(status_pembayaran) != 'lunas'";
        $piutangData = Database::fetch($sqlPiutang);
        $totalPiutang = (float)($piutangData['total_piutang'] ?? 0);
        $jumlahPiutang = (int)($piutangData['jumlah_piutang'] ?? 0);

        View::render('laporan/index', [
            'title'            => 'Laporan Keuangan',
            'subtitle'         => 'Omset transaksi nyata, total pengeluaran belanja, laba bersih, dan pantauan piutang',
            'startDate'        => $startDate,
            'endDate'          => $endDate,
            'period'           => $period,
            'totalOmzet'       => $totalOmzet,
            'totalTransaksi'   => $totalTransaksi,
            'totalPengeluaran' => $totalPengeluaran,
            'totalCatatanExp'  => $totalCatatanExp,
            'labaBersih'       => $labaBersih,
            'margin'           => $margin,
            'breakdownMetode'  => $breakdownMetode,
            'totalPiutang'     => $totalPiutang,
            'jumlahPiutang'    => $jumlahPiutang
        ]);
    }
}
