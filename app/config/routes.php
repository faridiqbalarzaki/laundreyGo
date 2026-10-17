<?php
declare(strict_types=1);

/**
 * TABEL ROUTE — key "METHOD /path".
 *   mw       : daftar guard berurutan (lihat core/middleware.php)
 *   view     : file di app/views/ (tanpa .php) -> dirender dalam app_layout
 *   handler  : nama fungsi PHP (form/API); 'file' = file yang di-require sebelumnya
 *   menu     : id menu sidebar yang aktif
 */

/** Helper definisi halaman ber-role. */
$page = static fn (string $view, string $menu, string $title, string $subtitle, string ...$roles): array => [
    'mw'       => ['auth', 'role:' . implode(',', $roles), 'csrf'],
    'view'     => $view,
    'menu'     => $menu,
    'title'    => $title,
    'subtitle' => $subtitle,
];

/** Redirect akar ruang kerja: /admin => /admin/dashboard, dst. */
$home = static fn (string $role, string $to): array => [
    'mw'       => ['auth', 'role:' . $role],
    'redirect' => $to,
];

return [
    // ---------- Publik / Auth ----------
    'GET /login'   => ['mw' => ['guest'],         'handler' => 'auth_show_login'],
    'POST /login'  => ['mw' => ['guest', 'csrf'], 'handler' => 'auth_login'],
    'POST /logout' => ['mw' => ['auth', 'csrf'],  'handler' => 'auth_logout'],

    // ---------- Semua role ----------
    'GET /profil' => $page('pages/shared/profil', 'profil', 'Profil & password',
        'Kelola akun aktif dan perbarui password keamanan', 'admin', 'kasir', 'produksi'),
    'GET /desain' => $page('pages/shared/desain', 'shared.desain', 'Sistem Desain Fresh Aqua',
        'Dokumentasi token, tipografi, komponen tombol, dan badge status LaundryGo', 'admin', 'kasir', 'produksi'),

    // ---------- Alias Cepat ----------
    'GET /pos'       => ['mw' => ['auth'], 'redirect' => '/kasir/pos'],
    'GET /dashboard' => ['mw' => ['auth'], 'redirect' => '/admin/dashboard'],
    'GET /pesanan'   => ['mw' => ['auth'], 'redirect' => '/admin/pesanan'],
    'GET /antrean'   => ['mw' => ['auth'], 'redirect' => '/produksi/antrean'],
    'GET /selesai'   => ['mw' => ['auth'], 'redirect' => '/kasir/selesai'],
    'GET /pelanggan' => ['mw' => ['auth'], 'redirect' => '/kasir/pelanggan'],

    // ---------- /admin ----------
    'GET /admin'             => $home('admin', '/admin/dashboard'),
    'GET /admin/dashboard'   => $page('pages/admin/dashboard',   'admin.dashboard',   'Dashboard',         'Ringkasan omset, antrean produksi, dan cucian selesai', 'admin'),
    'GET /admin/pesanan'     => $page('pages/admin/pesanan',     'admin.pesanan',     'Daftar pesanan',    'Cari, filter multi-kriteria, dan buka detail pesanan', 'admin', 'produksi'),
    'GET /admin/pengeluaran' => $page('pages/admin/pengeluaran', 'admin.pengeluaran', 'Pengeluaran',       'Gantikan buku catatan terpisah dengan rekap per kategori', 'admin'),
    'GET /admin/laporan'     => $page('pages/admin/laporan',     'admin.laporan',     'Laporan keuangan',  'Omset nyata per metode bayar, pengeluaran, laba, dan piutang', 'admin'),
    'GET /admin/layanan'     => $page('pages/admin/layanan',     'admin.layanan',     'Layanan dan harga', 'Lima layanan utama Adi Laundry', 'admin'),
    'GET /admin/pengguna'    => $page('pages/admin/pengguna',    'admin.pengguna',    'Pengguna dan log',  'Atur peran pegawai dan lihat rekam jejak aktivitas', 'admin'),

    // ---------- /kasir ----------
    'GET /kasir'           => $home('kasir', '/kasir/pos'),
    'GET /kasir/pos'       => $page('pages/kasir/pos',       'kasir.pos',       'Kasir',          'Catat pesanan baru, hitung total, cetak nota dan label', 'kasir', 'admin'),
    'GET /kasir/pesanan'   => $page('pages/kasir/pesanan',   'admin.pesanan',   'Daftar pesanan', 'Cari, filter multi-kriteria, dan buka detail pesanan', 'admin', 'produksi'),
    'GET /kasir/selesai'   => $page('pages/kasir/selesai',   'kasir.selesai',   'Cucian selesai', 'Kabari pelanggan manual, terima pembayaran, serahkan cucian', 'kasir', 'admin', 'produksi'),
    'GET /kasir/pelanggan' => $page('pages/kasir/pelanggan', 'kasir.pelanggan', 'Data pelanggan', 'Cari, tambah, ubah kontak, dan lihat riwayat pesanan pelanggan', 'kasir', 'admin'),

    // ---------- /produksi ----------
    'GET /produksi'         => $home('produksi', '/produksi/antrean'),
    'GET /produksi/antrean' => $page('pages/produksi/antrean', 'produksi.antrean', 'Antrean produksi', 'Urut berdasarkan hari selesai, kilat di atas', 'produksi', 'admin'),
    'GET /produksi/pesanan' => $page('pages/produksi/pesanan', 'admin.pesanan',    'Daftar pesanan',   'Daftar ringkas pesanan yang sedang dikerjakan', 'produksi', 'admin'),

    // ---------- API (langkah berikutnya) ----------
    // 'POST /api/pos/add-item' => ['mw' => ['auth', 'role:kasir', 'csrf'], 'file' => 'api/pos_handler.php', 'handler' => 'pos_add_item'],
];
