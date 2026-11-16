<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Helper;
use App\Core\View;
use App\Models\Pelanggan;
use App\Models\Layanan;
use App\Models\Pesanan;
use App\Models\LogAktivitas;

class PosController {
    public function index(): void {
        Auth::requireRole(['Admin', 'Kasir']);

        $layananList = Layanan::getActive();
        $pelangganList = Pelanggan::all();
        $kategoriList = Layanan::getKategoriList();

        View::render('pos/index', [
            'title'         => 'Kasir POS - LaundryGo Aqua',
            'layananList'   => $layananList,
            'pelangganList' => $pelangganList,
            'kategoriList'  => $kategoriList
        ]);
    }

    /**
     * Proses Simpan Transaksi Pesanan Baru dari POS
     */
    public function store(): void {
        Auth::requireRole(['Admin', 'Kasir']);

        // Tangani JSON payload (jika dikirim via AJAX fetch) atau POST biasa
        $rawInput = file_get_contents('php://input');
        $jsonData = json_decode($rawInput, true);

        $input = $jsonData ?? $_POST;

        $idPelanggan = Helper::sanitize($input['id_pelanggan'] ?? '');
        $catatanKhusus = Helper::sanitize($input['catatan_khusus'] ?? '');
        $parfum = Helper::sanitize($input['parfum'] ?? 'Ocean Fresh');
        $isKilat = !empty($input['is_kilat']);
        $items = $input['items'] ?? [];

        // Jika pelanggan baru diinput langsung (inline)
        if (empty($idPelanggan) && !empty($input['nama_pelanggan_baru'])) {
            $idPelanggan = Pelanggan::create([
                'nama_pelanggan' => Helper::sanitize($input['nama_pelanggan_baru']),
                'no_telepon'     => Helper::sanitize($input['no_telepon_baru'] ?? '-'),
                'alamat'         => Helper::sanitize($input['alamat_baru'] ?? '')
            ]);
        }

        if (empty($idPelanggan)) {
            if ($jsonData) {
                Helper::jsonResponse(['success' => false, 'message' => 'Pilih atau masukkan data pelanggan terlebih dahulu!'], 400);
            }
            Helper::setFlash('error', 'Pilih atau masukkan data pelanggan!');
            Helper::redirect(Helper::url('pos'));
        }

        if (empty($items) || !is_array($items)) {
            if ($jsonData) {
                Helper::jsonResponse(['success' => false, 'message' => 'Keranjang pesanan masih kosong!'], 400);
            }
            Helper::setFlash('error', 'Keranjang pesanan masih kosong!');
            Helper::redirect(Helper::url('pos'));
        }

        // Hitung total harga
        $totalHarga = 0;
        $itemsData = [];
        foreach ($items as $item) {
            $layanan = Layanan::findById($item['id_layanan']);
            if ($layanan) {
                $qty = (float)$item['jumlah'];
                $subtotal = $qty * (float)$layanan['harga'];
                $totalHarga += $subtotal;

                $itemsData[] = [
                    'id_layanan'     => $layanan['id_layanan'],
                    'jumlah'         => $qty,
                    'harga_satuan'   => (float)$layanan['harga'],
                    'estimasi_waktu' => (int)$layanan['estimasi_waktu']
                ];
            }
        }

        // Cek Pembayaran
        $bayarSekarang = !empty($input['bayar_sekarang']);
        $paymentData = null;
        $statusPembayaran = 'Belum Lunas';

        if ($bayarSekarang) {
            $jumlahBayar = (float)($input['jumlah_bayar'] ?? 0);
            $kembalian = max(0, $jumlahBayar - $totalHarga);
            $rawMetode = $input['id_metode'] ?? 'MET-01';
            $methodMap = [
                'tunai'    => 'MET-01',
                'cash'     => 'MET-01',
                'transfer' => 'MET-02',
                'bank'     => 'MET-02',
                'qris'     => 'MET-03',
                'ewallet'  => 'MET-03',
                'nanti'    => 'MET-04'
            ];
            $idMetode = $methodMap[strtolower($rawMetode)] ?? $rawMetode;

            if ($jumlahBayar >= $totalHarga) {
                $statusPembayaran = 'Lunas';
                $paymentData = [
                    'jumlah_bayar'   => $jumlahBayar,
                    'uang_kembalian' => $kembalian,
                    'id_metode'      => $idMetode
                ];
            }
        }

        $orderData = [
            'id_pelanggan'      => $idPelanggan,
            'status_cucian'     => 'Antrean',
            'total_harga'       => $totalHarga,
            'status_pembayaran' => $statusPembayaran,
            'lokasi_rak'        => null,
            'catatan_khusus'    => $catatanKhusus,
            'parfum'            => $parfum,
            'is_kilat'          => $isKilat,
            'id_anggota'        => Auth::id()
        ];

        $idPesanan = Pesanan::create($orderData, $itemsData, $paymentData);

        if ($idPesanan) {
            LogAktivitas::log('Transaksi POS', "Membuat pesanan baru {$idPesanan} untuk pelanggan {$idPelanggan}", Auth::id());

            if ($jsonData) {
                Helper::jsonResponse([
                    'success'    => true,
                    'message'    => 'Pesanan berhasil disimpan!',
                    'id_pesanan' => $idPesanan
                ]);
            }

            Helper::setFlash('success', "Pesanan {$idPesanan} berhasil disimpan!");
            Helper::redirect(Helper::url('pos', ['print' => $idPesanan]));
        } else {
            if ($jsonData) {
                Helper::jsonResponse(['success' => false, 'message' => 'Gagal menyimpan pesanan ke database.'], 500);
            }
            Helper::setFlash('error', 'Gagal menyimpan pesanan. Silakan coba lagi.');
            Helper::redirect(Helper::url('pos'));
        }
    }

    /**
     * Endpoint API pencarian pelanggan via AJAX
     */
    public function searchPelanggan(): void {
        Auth::requireLogin();
        $q = Helper::sanitize($_GET['q'] ?? '');
        $data = Pelanggan::search($q);
        Helper::jsonResponse($data);
    }
}
