<?php
$shellUser = \App\Core\Auth::user();
$shellNama = $shellUser['nama_anggota'] ?? ($shellUser['username'] ?? 'User');
$shellWords = array_values(array_filter(explode(' ', trim($shellNama))));
if (count($shellWords) >= 2) {
    $shellInitials = strtoupper(mb_substr($shellWords[0], 0, 1) . mb_substr($shellWords[1], 0, 1));
} else {
    $shellInitials = strtoupper(mb_substr($shellWords[0] ?? 'U', 0, min(2, mb_strlen($shellWords[0] ?? 'U'))));
}
$shellIsAdmin = \App\Core\Auth::isAdmin();
$shellIsKasir = \App\Core\Auth::isKasir();
$shellRoleTitle = $shellIsAdmin ? 'Pemilik Gerai' : ($shellIsKasir ? 'Kasir Meja Depan' : ($shellUser['nama_role'] ?? 'Staf Operasional'));
$shellHomeUrl = \App\Core\Helper::url($shellIsKasir ? 'pos' : 'dashboard');
$shellLogoUrl = \App\Core\Helper::asset('img/logo_icon_drop.png');
?>
<header class="mobile-app-header">
    <a href="<?= $shellHomeUrl ?>" class="mobile-header-left" aria-label="Beranda LaundryGo" style="text-decoration: none !important;">
        <span class="sidebar-logo-squircle">
            <img src="<?= $shellLogoUrl ?>" alt="LaundryGo Icon">
        </span>
        <span class="mobile-header-titles">
            <span class="mobile-header-title-main" style="text-decoration: none !important;">LaundryGo</span>
            <span class="mobile-header-title-sub" style="text-decoration: none !important;">Adi Laundry, Karangploso</span>
        </span>
    </a>
    <div class="mobile-header-right">
        <button type="button" class="mobile-hamburger-btn" id="mobileMenuBtn" aria-controls="mobileDrawerPanel" aria-expanded="false" aria-label="Buka menu navigasi">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
    </div>
</header>

<div class="mobile-drawer-backdrop" id="mobileDrawerBackdrop"></div>

<aside class="mobile-drawer-panel" id="mobileDrawerPanel" aria-label="Navigasi utama">
    <div class="mobile-drawer-head">
        <a href="<?= $shellHomeUrl ?>" class="mobile-drawer-brand" aria-label="Beranda LaundryGo" style="text-decoration: none !important;">
            <span class="sidebar-logo-squircle">
                <img src="<?= $shellLogoUrl ?>" alt="LaundryGo Icon">
            </span>
            <span class="mobile-header-titles">
                <span class="mobile-header-title-main" style="text-decoration: none !important;">LaundryGo</span>
                <span class="mobile-header-title-sub" style="text-decoration: none !important;">Adi Laundry, Karangploso</span>
            </span>
        </a>
        <button type="button" class="mobile-drawer-close-btn" id="mobileDrawerClose" aria-label="Tutup menu">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <nav class="mobile-drawer-nav">
        <?php include __DIR__ . '/sidebar_nav_links.php'; ?>
    </nav>

    <div class="mobile-drawer-footer">
        <div class="sidebar-profile-card">
            <a href="<?= \App\Core\Helper::url('profil') ?>" class="sidebar-profile-main" aria-label="Buka profil dan ubah kata sandi" style="display: flex; align-items: center; gap: 10px; text-decoration: none !important; flex: 1; min-width: 0;">
                <span class="sidebar-profile-avatar" style="width: 32px; height: 32px; border-radius: 50%; background: #0284C7; color: #FFFFFF; font-weight: 800; font-size: 0.76rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; text-decoration: none !important;">
                    <?= $shellInitials ?>
                </span>
                <span class="sidebar-profile-info" style="min-width: 0; text-decoration: none !important;">
                    <span class="sidebar-profile-name" style="display: block; font-size: 0.82rem; font-weight: 700; color: #0F172A; text-decoration: none !important;"><?= htmlspecialchars($shellNama) ?></span>
                    <span class="sidebar-profile-role" style="display: block; font-size: 0.70rem; color: #64748B; text-decoration: none !important;"><?= htmlspecialchars($shellRoleTitle) ?></span>
                </span>
            </a>
            <a href="<?= \App\Core\Helper::url('logout') ?>" class="sidebar-profile-logout" title="Keluar Akun"
                data-confirm="true"
                data-confirm-title="Keluar dari Akun?"
                data-confirm-message="Sesi kerja Anda saat ini akan diakhiri. Pastikan seluruh transaksi kasir telah selesai diproses."
                data-confirm-badge="<?= htmlspecialchars($shellNama) ?> &bull; <?= htmlspecialchars($shellRoleTitle) ?>"
                data-confirm-btn="Ya, Keluar Akun"
                data-confirm-type="warning"
                style="text-decoration: none !important;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </a>
        </div>
    </div>
</aside>
