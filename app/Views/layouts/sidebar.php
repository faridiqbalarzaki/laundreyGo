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
        <?php include __DIR__ . '/../partials/sidebar_nav_links.php'; ?>
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
                    $activeInRak = \App\Models\Pesanan::getSiapAmbil(null, 'aktif');
                    $displayList = array_slice($activeInRak, 0, 3);
                    if (empty($displayList)):
                    ?>
                        <div style="font-size: 0.72rem; color: #94A3B8; padding: 6px 8px; text-align: center;">Tidak ada cucian di rak</div>
                    <?php else: ?>
                        <?php foreach ($displayList as $idx => $item): 
                            $dotColor = ($idx % 2 === 0) ? '#f59e0b' : '#10b981';
                            $pelangganNama = $item['nama_pelanggan'] ?? 'Pelanggan';
                            $rakInfo = $item['lokasi_rak'] ?: 'Rak';
                        ?>
                            <a href="<?= \App\Core\Helper::url('pesanan', ['action' => 'selesai']) ?>" class="sidebar-completed-row" style="text-decoration: none !important;">
                                <span class="completed-dot-indicator" style="background: <?= $dotColor ?>;"></span>
                                <span class="sidebar-completed-name" style="text-decoration: none !important;"><?= htmlspecialchars($pelangganNama) ?></span>
                                <span class="sidebar-completed-rack" style="text-decoration: none !important;"><?= htmlspecialchars($rakInfo) ?></span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
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