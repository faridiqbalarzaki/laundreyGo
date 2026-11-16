<?php
$currentPage = $_GET['page'] ?? 'dashboard';
$currentAction = $_GET['action'] ?? '';
$user = \App\Core\Auth::user();
$isAdmin = \App\Core\Auth::isAdmin();
$isKasir = \App\Core\Auth::isKasir();
$isPetugas = \App\Core\Auth::isPetugas();

// Avatar initials & profile
$nama = $user['nama_anggota'] ?? ($user['username'] ?? 'User');
$words = array_values(array_filter(explode(' ', trim($nama))));
if (count($words) >= 2) {
    $initials = strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
} else {
    $initials = strtoupper(mb_substr($words[0] ?? 'U', 0, min(2, mb_strlen($words[0] ?? 'U'))));
}

// Role subtitle label
$roleTitle = $isAdmin ? 'Pemilik Gerai' : ($isKasir ? 'Kasir Meja Depan' : ($user['nama_role'] ?? 'Staf Operasional'));
?>
<aside class="app-sidebar <?= (!empty($_GET['collapsed'])) ? 'is-collapsed' : '' ?>" id="appSidebar">
    <!-- Tombol Collapse / Expand -->
    <button type="button" id="sidebar-collapse-btn" class="sidebar-collapse-btn"
        onclick="toggleSidebarCollapse()" aria-expanded="true" aria-controls="appSidebar"
        aria-label="Ciutkan atau lebarkan sidebar" title="Ciutkan / Lebarkan">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </button>

    <!-- Brand Header -->
    <div class="sidebar-brand-box">
        <a href="<?= \App\Core\Helper::url($isKasir ? 'pos' : 'dashboard') ?>" class="sidebar-brand-link" data-tip="LaundryGo - Adi Laundry" aria-label="Beranda LaundryGo" style="text-decoration: none !important;">
            <span class="sidebar-logo-squircle">
                <img src="<?= \App\Core\Helper::asset('img/logo_icon_drop.png') ?>" alt="LaundryGo Icon">
            </span>
            <span class="sidebar-brand-titles">
                <span class="sidebar-title-main" style="text-decoration: none !important;">LaundryGo</span>
                <span class="sidebar-title-sub" style="text-decoration: none !important;">Adi Laundry, Karangploso</span>
            </span>
        </a>
    </div>

    <!-- Navigation Menu List -->
    <nav class="sidebar-menu-list" id="sidebar-menu-container" aria-label="Navigasi Utama">
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
                <?php 
                $totalSiapCount = 2; // Presisi Foto 2: 2 cucian di rak (Bpk. Rahmat Santoso & Bu Endang Supriati)
                ?>
                <span class="sidebar-badge-count" style="margin-left: auto; background: #EF4444; color: #fff; font-size: 0.68rem; font-weight: 700; border-radius: 9999px; padding: 1px 7px;">2</span>
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
    </nav>

    <!-- Footer Terjangkar: Profil Pengguna Aktif & Widget Selesai -->
    <div class="sidebar-footer-stack">
        <?php if ($isKasir): ?>
            <div id="sidebar-completed-section" style="margin-bottom: 8px;">
                <a href="<?= \App\Core\Helper::url('pesanan', ['action' => 'selesai']) ?>" class="sidebar-menu-group-label" style="display: flex; justify-content: space-between; align-items: center; text-decoration: none !important; padding: 4px 6px; cursor: pointer;" data-tip="Lihat Semua Selesai (<?= $totalSiapCount ?>)">
                    <span>Selesai</span>
                    <span class="sidebar-group-count font-mono" style="font-size: 0.68rem; font-weight: 700; color: #0284C7;"><?= $totalSiapCount ?></span>
                </a>
                <div class="sidebar-inset-card" id="sidebar-completed-box">
                    <?php 
                    $displayList = [
                        ['nama_pelanggan' => 'Bpk. Rahmat San...', 'lokasi_rak' => 'Rak C-02'],
                        ['nama_pelanggan' => 'Bu Endang Supriati', 'lokasi_rak' => 'Rak C-01']
                    ];
                    foreach ($displayList as $idx => $item): 
                        $dotColor = ($idx % 2 === 0) ? '#f59e0b' : '#10b981';
                        $pelangganNama = $item['nama_pelanggan'] ?? 'Pelanggan';
                        $rakInfo = $item['lokasi_rak'] ?? 'Rak C-01';
                    ?>
                        <a href="<?= \App\Core\Helper::url('pesanan', ['action' => 'selesai']) ?>" class="sidebar-completed-row" style="text-decoration: none !important;">
                            <span class="completed-dot-indicator" style="background: <?= $dotColor ?>;"></span>
                            <span class="sidebar-completed-name" style="text-decoration: none !important;"><?= htmlspecialchars($pelangganNama) ?></span>
                            <span class="sidebar-completed-rack" style="text-decoration: none !important;"><?= htmlspecialchars($rakInfo) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div id="sidebar-profile-section">
            <div class="sidebar-profile-card <?= $currentPage === 'profil' ? 'is-active' : '' ?>" id="sidebar-profile-card">
                <a href="<?= \App\Core\Helper::url('profil') ?>" class="sidebar-profile-main" data-tip="Profil: <?= htmlspecialchars($nama) ?> (<?= htmlspecialchars($roleTitle) ?>)" aria-label="Buka profil dan ubah kata sandi" style="display: flex; align-items: center; gap: 10px; text-decoration: none !important; flex: 1; min-width: 0;">
                    <span class="sidebar-profile-avatar" style="width: 32px; height: 32px; border-radius: 50%; background: #0284C7; color: #FFFFFF; font-weight: 800; font-size: 0.76rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; text-decoration: none !important;">
                        <?= $initials ?>
                    </span>
                    <span class="sidebar-profile-info" style="min-width: 0; text-decoration: none !important;">
                        <span class="sidebar-profile-name" style="display: block; font-size: 0.82rem; font-weight: 700; color: #0F172A; text-decoration: none !important;"><?= htmlspecialchars($nama) ?></span>
                        <span class="sidebar-profile-role" style="display: block; font-size: 0.70rem; color: #64748B; text-decoration: none !important;"><?= htmlspecialchars($roleTitle) ?></span>
                    </span>
                </a>
                <a href="<?= \App\Core\Helper::url('logout') ?>" class="sidebar-profile-logout" title="Keluar Akun"
                    data-confirm="true"
                    data-confirm-title="Keluar dari Akun?"
                    data-confirm-message="Sesi kerja Anda saat ini akan diakhiri. Pastikan seluruh transaksi kasir telah selesai diproses."
                    data-confirm-badge="<?= htmlspecialchars($nama) ?> &bull; <?= htmlspecialchars($roleTitle) ?>"
                    data-confirm-btn="Ya, Keluar Akun"
                    data-confirm-type="warning"
                    style="text-decoration: none !important;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </a>
            </div>
        </div>
    </div>
</aside>
<script>
function toggleSidebarCollapse() {
    const sb = document.getElementById('appSidebar');
    const btn = document.getElementById('sidebar-collapse-btn');
    if (!sb) return;
    sb.classList.toggle('is-collapsed');
    const isCollapsed = sb.classList.contains('is-collapsed');
    if (btn) btn.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
    try {
        localStorage.setItem('laundrygo_sidebar_collapsed', isCollapsed ? '1' : '0');
    } catch (e) {}
}

(function() {
    try {
        if (localStorage.getItem('laundrygo_sidebar_collapsed') === '1') {
            const sb = document.getElementById('appSidebar');
            const btn = document.getElementById('sidebar-collapse-btn');
            if (sb) sb.classList.add('is-collapsed');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        }
    } catch (e) {}
})();
</script>