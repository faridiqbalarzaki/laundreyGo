<?php
$currentPage = $_GET['page'] ?? 'dashboard';
$currentAction = $_GET['action'] ?? '';
$isAdmin = \App\Core\Auth::isAdmin();
$isKasir = \App\Core\Auth::isKasir();
$totalSiapCount = \App\Models\Pesanan::countSiapAmbil();
?>
<?php if ($isAdmin): ?>
    <!-- ADMIN: OPERASIONAL -->
    <div class="sidebar-menu-group-label">
        <span>OPERASIONAL</span>
    </div>

    <a href="<?= \App\Core\Helper::url('dashboard') ?>" class="sidebar-nav-btn <?= $currentPage === 'dashboard' ? 'active' : '' ?>" data-tip="Dashboard" aria-label="Dashboard" style="text-decoration: none !important;">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
        <span class="sidebar-nav-label" style="text-decoration: none !important;">Dashboard</span>
    </a>

    <a href="<?= \App\Core\Helper::url('pesanan') ?>" class="sidebar-nav-btn <?= ($currentPage === 'pesanan' && $currentAction !== 'selesai') ? 'active' : '' ?>" data-tip="Daftar Pesanan" aria-label="Daftar Pesanan" style="text-decoration: none !important;">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        <span class="sidebar-nav-label" style="text-decoration: none !important;">Daftar Pesanan</span>
    </a>

    <a href="<?= \App\Core\Helper::url('pesanan', ['action' => 'selesai']) ?>" class="sidebar-nav-btn <?= ($currentPage === 'pesanan' && $currentAction === 'selesai') ? 'active' : '' ?>" data-tip="Cucian Selesai" aria-label="Cucian Selesai" style="text-decoration: none !important;">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m16 9-5.5 5.5L8 12"/></svg>
        <span class="sidebar-nav-label" style="text-decoration: none !important;">Cucian Selesai</span>
        <?php if ($totalSiapCount > 0): ?>
            <span class="sidebar-badge-count" style="margin-left: auto; background: #EF4444; color: #fff; font-size: 0.68rem; font-weight: 700; border-radius: 9999px; padding: 1px 7px;"><?= $totalSiapCount ?></span>
        <?php endif; ?>
    </a>

    <a href="<?= \App\Core\Helper::url('pelanggan') ?>" class="sidebar-nav-btn <?= $currentPage === 'pelanggan' ? 'active' : '' ?>" data-tip="Data Pelanggan" aria-label="Data Pelanggan" style="text-decoration: none !important;">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span class="sidebar-nav-label" style="text-decoration: none !important;">Data Pelanggan</span>
    </a>

    <!-- ADMIN: KEUANGAN -->
    <div class="sidebar-menu-group-label" style="margin-top: 6px;">
        <span>KEUANGAN</span>
    </div>

    <a href="<?= \App\Core\Helper::url('pengeluaran') ?>" class="sidebar-nav-btn <?= $currentPage === 'pengeluaran' ? 'active' : '' ?>" data-tip="Pengeluaran" aria-label="Pengeluaran" style="text-decoration: none !important;">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 15h0M2 10h20"/></svg>
        <span class="sidebar-nav-label" style="text-decoration: none !important;">Pengeluaran</span>
    </a>

    <a href="<?= \App\Core\Helper::url('laporan') ?>" class="sidebar-nav-btn <?= $currentPage === 'laporan' ? 'active' : '' ?>" data-tip="Laporan Keuangan" aria-label="Laporan Keuangan" style="text-decoration: none !important;">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        <span class="sidebar-nav-label" style="text-decoration: none !important;">Laporan Keuangan</span>
    </a>

    <!-- ADMIN: PENGATURAN -->
    <div class="sidebar-menu-group-label" style="margin-top: 6px;">
        <span>PENGATURAN</span>
    </div>

    <a href="<?= \App\Core\Helper::url('layanan') ?>" class="sidebar-nav-btn <?= $currentPage === 'layanan' ? 'active' : '' ?>" data-tip="Layanan & Harga" aria-label="Layanan & Harga" style="text-decoration: none !important;">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r=".5" fill="currentColor"/></svg>
        <span class="sidebar-nav-label" style="text-decoration: none !important;">Layanan &amp; Harga</span>
    </a>

    <a href="<?= \App\Core\Helper::url('pengguna') ?>" class="sidebar-nav-btn <?= $currentPage === 'pengguna' ? 'active' : '' ?>" data-tip="Pengguna Aktif" aria-label="Pengguna Aktif" style="text-decoration: none !important;">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
        <span class="sidebar-nav-label" style="text-decoration: none !important;">Pengguna Aktif</span>
    </a>

<?php elseif ($isKasir): ?>
    <!-- KASIR: OPERASIONAL -->
    <div class="sidebar-menu-group-label">
        <span>Operasional</span>
    </div>

    <a href="<?= \App\Core\Helper::url('pos') ?>" class="sidebar-nav-btn <?= $currentPage === 'pos' ? 'active' : '' ?>" data-tip="Kasir (POS)" aria-label="Kasir (POS)" style="text-decoration: none !important;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
        <span class="sidebar-nav-label" style="text-decoration: none !important;">Kasir (POS)</span>
    </a>

    <a href="<?= \App\Core\Helper::url('pesanan') ?>" class="sidebar-nav-btn <?= ($currentPage === 'pesanan' && $currentAction !== 'selesai') ? 'active' : '' ?>" data-tip="Daftar Pesanan" aria-label="Daftar Pesanan" style="text-decoration: none !important;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/></svg>
        <span class="sidebar-nav-label" style="text-decoration: none !important;">Daftar Pesanan</span>
    </a>

    <a href="<?= \App\Core\Helper::url('pesanan', ['action' => 'selesai']) ?>" class="sidebar-nav-btn <?= ($currentPage === 'pesanan' && $currentAction === 'selesai') ? 'active' : '' ?>" data-tip="Cucian Selesai" aria-label="Cucian Selesai" style="text-decoration: none !important;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m16 9-5.5 5.5L8 12"/></svg>
        <span class="sidebar-nav-label" style="text-decoration: none !important;">Cucian Selesai</span>
        <?php if ($totalSiapCount > 0): ?>
            <span class="sidebar-badge-count" style="margin-left: auto; background: #EF4444; color: #fff; font-size: 0.68rem; font-weight: 700; border-radius: 9999px; padding: 1px 7px;"><?= $totalSiapCount ?></span>
        <?php endif; ?>
    </a>

    <a href="<?= \App\Core\Helper::url('pelanggan') ?>" class="sidebar-nav-btn <?= $currentPage === 'pelanggan' ? 'active' : '' ?>" data-tip="Data Pelanggan" aria-label="Data Pelanggan" style="text-decoration: none !important;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span class="sidebar-nav-label" style="text-decoration: none !important;">Data Pelanggan</span>
    </a>

    <!-- KASIR: LAINNYA -->
    <div class="sidebar-menu-group-label" style="margin-top: 6px;">
        <span>Lainnya</span>
    </div>

    <a href="<?= \App\Core\Helper::url('layanan') ?>" class="sidebar-nav-btn <?= $currentPage === 'layanan' ? 'active' : '' ?>" data-tip="Tarif Layanan" aria-label="Tarif Layanan" style="text-decoration: none !important;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r=".5" fill="currentColor"/></svg>
        <span class="sidebar-nav-label" style="text-decoration: none !important;">Tarif Layanan</span>
    </a>
<?php endif; ?>
