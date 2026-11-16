<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Helper;
use App\Core\View;
use App\Models\Pesanan;
use App\Models\LogAktivitas;

class ProduksiController {
    public function index(): void {
        Auth::requireLogin();

        $antrean = Pesanan::getAntreanProduksi();

        // Bagi pesanan berdasarkan tahapan produksi
        $listAntrean = [];
        $listDicuci = [];
        $listDisetrika = [];

        foreach ($antrean as $item) {
            $item['sla'] = Helper::calculateSla($item['tanggal_keluar']);
            if ($item['status_cucian'] === 'Antrean') {
                $listAntrean[] = $item;
            } elseif ($item['status_cucian'] === 'Dicuci') {
                $listDicuci[] = $item;
            } elseif ($item['status_cucian'] === 'Disetrika') {
                $listDisetrika[] = $item;
            }
        }

        View::render('produksi/index', [
            'title'         => 'Lantai Produksi - LaundryGo',
            'orders'        => $antrean,
            'listAntrean'   => $listAntrean,
            'listDicuci'    => $listDicuci,
            'listDisetrika' => $listDisetrika,
            'totalProduksi' => count($antrean)
        ], 'petugas');
    }

    /**
     * Memajukan status pengerjaan (Antrean -> Dicuci -> Disetrika -> Siap Diambil)
     */
    public function advance(): void {
        Auth::requireLogin();

        $id = Helper::sanitize($_POST['id_pesanan'] ?? '');
        $currentStatus = Helper::sanitize($_POST['current_status'] ?? '');
        $catatan = Helper::sanitize($_POST['catatan'] ?? '');
        $rak = !empty($_POST['lokasi_rak']) ? Helper::sanitize($_POST['lokasi_rak']) : null;
        $isAjax = !empty($_POST['ajax']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        // Normalisasi status case-insensitive jika perlu
        $statusNorm = ucfirst(strtolower($currentStatus));
        if ($statusNorm === 'Diterima') $statusNorm = 'Antrean';

        $nextStatus = '';
        if ($statusNorm === 'Antrean') {
            $nextStatus = 'Dicuci';
            if (empty($catatan)) $catatan = 'Cucian dimasukkan ke mesin cuci';
        } elseif ($statusNorm === 'Dicuci') {
            $nextStatus = 'Disetrika';
            if (empty($catatan)) $catatan = 'Cucian masuk ke tahap setrika uap';
        } elseif ($statusNorm === 'Disetrika') {
            $nextStatus = 'Siap Diambil';
            if (empty($catatan)) $catatan = 'Cucian selesai disetrika dan dipacking rapi';
        }

        if ($nextStatus) {
            Pesanan::updateStatus($id, $nextStatus, Auth::id(), $catatan, $rak);
            LogAktivitas::log('Lantai Produksi', "Pesanan {$id} dimajukan dari {$statusNorm} ke {$nextStatus}", Auth::id());
            
            if ($isAjax) {
                Helper::jsonResponse([
                    'success'    => true,
                    'message'    => "Pesanan {$id} dipindahkan ke {$nextStatus}.",
                    'code'       => $id,
                    'prevStatus' => strtolower($statusNorm === 'Antrean' ? 'diterima' : $statusNorm),
                    'nextStatus' => strtolower($nextStatus === 'Siap Diambil' ? 'selesai' : $nextStatus)
                ]);
                return;
            }

            Helper::setFlash('success', "Pesanan {$id} berhasil dipindahkan ke tahap {$nextStatus}!");
        } else {
            if ($isAjax) {
                Helper::jsonResponse(['success' => false, 'message' => 'Tahapan status tidak valid.'], 400);
                return;
            }
            Helper::setFlash('error', "Tahapan status tidak valid.");
        }

        Helper::redirect(Helper::url('produksi'));
    }

    /**
     * Membatalkan pemindahan status (Rollback via tombol Batalkan di Toast)
     */
    public function revert(): void {
        Auth::requireLogin();

        $id = Helper::sanitize($_POST['id_pesanan'] ?? '');
        $targetStatus = Helper::sanitize($_POST['target_status'] ?? '');
        $isAjax = !empty($_POST['ajax']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        $statusMap = [
            'diterima' => 'Antrean',
            'antrean' => 'Antrean',
            'dicuci' => 'Dicuci',
            'disetrika' => 'Disetrika',
            'siap_diambil' => 'Siap Diambil',
            'siap diambil' => 'Siap Diambil'
        ];

        $dbStatus = $statusMap[strtolower($targetStatus)] ?? 'Antrean';
        $clearRak = in_array($dbStatus, ['Antrean', 'Dicuci', 'Disetrika'], true) ? 'clear' : null;
        $catatan = !empty($_POST['catatan']) ? Helper::sanitize($_POST['catatan']) : "Pengunduran status pengerjaan ke {$dbStatus} oleh petugas";

        if ($id && $dbStatus) {
            Pesanan::updateStatus($id, $dbStatus, Auth::id(), $catatan, $clearRak);
            LogAktivitas::log('Lantai Produksi', "Pesanan {$id} diundurkan ke status {$dbStatus}", Auth::id());

            if ($isAjax) {
                Helper::jsonResponse([
                    'success'      => true,
                    'message'      => "Aksi dibatalkan. Pesanan {$id} dikembalikan ke {$dbStatus}.",
                    'code'         => $id,
                    'currentStatus'=> strtolower($dbStatus === 'Antrean' ? 'diterima' : $dbStatus)
                ]);
                return;
            }
            Helper::setFlash('success', "Pesanan {$id} berhasil dikembalikan ke {$dbStatus}.");
        } else {
            if ($isAjax) {
                Helper::jsonResponse(['success' => false, 'message' => 'Parameter tidak valid.'], 400);
                return;
            }
            Helper::setFlash('error', "Gagal membatalkan status.");
        }

        Helper::redirect(Helper::url('produksi'));
    }
}
