<?php
declare(strict_types=1);

/**
 * Sumber tunggal menu sidebar. 'id' harus sama dengan 'menu' di routes.php
 * agar item aktif ter-highlight. Urutan array = urutan tampil.
 */
return [
    'groups' => [
        'operasional' => 'Operasional',
        'keuangan'    => 'Keuangan',
        'pengaturan'  => 'Pengaturan',
    ],
    'items' => [
        // ---- Operasional ----
        ['id' => 'kasir.pos',         'label' => 'Kasir',             'icon' => 'kasir',       'group' => 'operasional', 'path' => '/kasir/pos',          'roles' => ['admin', 'kasir']],
        ['id' => 'admin.dashboard',   'label' => 'Dashboard',         'icon' => 'dashboard',   'group' => 'operasional', 'path' => '/admin/dashboard',   'roles' => ['admin']],
        ['id' => 'admin.pesanan',     'label' => 'Daftar pesanan',    'icon' => 'pesanan',     'group' => 'operasional', 'path' => '/admin/pesanan',     'roles' => ['admin', 'produksi']],
        ['id' => 'produksi.antrean',  'label' => 'Antrean produksi',  'icon' => 'antrean',     'group' => 'operasional', 'path' => '/produksi/antrean',  'roles' => ['admin', 'produksi']],
        ['id' => 'kasir.selesai',     'label' => 'Cucian selesai',    'icon' => 'selesai',     'group' => 'operasional', 'path' => '/kasir/selesai',     'roles' => ['admin', 'kasir', 'produksi'], 'badge' => 'selesai'],
        ['id' => 'kasir.pelanggan',   'label' => 'Data pelanggan',    'icon' => 'pelanggan',   'group' => 'operasional', 'path' => '/kasir/pelanggan',   'roles' => ['admin', 'kasir']],

        // ---- Keuangan ----
        ['id' => 'admin.pengeluaran', 'label' => 'Pengeluaran',       'icon' => 'pengeluaran', 'group' => 'keuangan',    'path' => '/admin/pengeluaran', 'roles' => ['admin']],
        ['id' => 'admin.laporan',     'label' => 'Laporan keuangan',  'icon' => 'laporan',     'group' => 'keuangan',    'path' => '/admin/laporan',     'roles' => ['admin']],

        // ---- Pengaturan ----
        ['id' => 'admin.layanan',     'label' => 'Layanan dan harga', 'icon' => 'layanan',     'group' => 'pengaturan',  'path' => '/admin/layanan',     'roles' => ['admin']],
        ['id' => 'admin.pengguna',    'label' => 'Pengguna dan log',  'icon' => 'pengguna',    'group' => 'pengaturan',  'path' => '/admin/pengguna',    'roles' => ['admin']],
        ['id' => 'profil',            'label' => 'Profil & password', 'icon' => 'profil',     'group' => 'pengaturan',  'path' => '/profil',            'roles' => ['admin', 'kasir', 'produksi']],
        ['id' => 'shared.desain',     'label' => 'Sistem desain',     'icon' => 'desain',     'group' => 'pengaturan',  'path' => '/desain',            'roles' => ['admin']],
    ],
];
