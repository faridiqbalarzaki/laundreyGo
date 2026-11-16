<div class="selesai-header-actions" style="display: flex; align-items: center; gap: 12px;">
    <span class="page-head-date"><?= \App\Core\Helper::tanggalIndonesia(date('Y-m-d')) ?></span>
    <?php 
    $openNotif = isset($_GET['open_notif']) && $_GET['open_notif'] === '1'; 
    $storeNotifications = \App\Models\Pesanan::getStoreNotifications(8);
    $notifCount = count($storeNotifications);
    ?>
    <div id="notif-bell-wrap" class="notif-bell-wrap" style="position: relative;">
        <button type="button" id="notif-bell-btn" class="notif-bell-btn" onclick="toggleKasirNotifPopover(event)"
            aria-haspopup="true" aria-expanded="<?= $openNotif ? 'true' : 'false' ?>" aria-label="Notifikasi Gerai (<?= $notifCount ?>)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <?php if ($notifCount > 0): ?>
                <span id="notif-bell-badge" class="notif-bell-badge"><?= $notifCount ?></span>
            <?php endif; ?>
        </button>
        <div id="kasir-notif-popover" class="notif-popover notif-popover-modern <?= $openNotif ? 'is-open' : '' ?>" role="region" aria-label="Pusat Notifikasi Gerai" onclick="event.stopPropagation()">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #F1F5F9;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <strong style="font-size: 0.88rem; color: #0F172A; font-weight: 700;">Notifikasi Gerai</strong>
                    <span id="notif-popover-count" style="font-size: 0.7rem; font-weight: 700; background: #F0F9FF; color: #0284C7; border: 1px solid #BAE6FD; padding: 1px 7px; border-radius: 999px;"><?= $notifCount ?> baru</span>
                </div>
                <button type="button" onclick="tandaiSemuaNotifDibaca()" style="font-size: 0.72rem; font-weight: 600; padding: 3px 8px; border: 1px solid #E2E8F0; border-radius: 6px; background: #F8FAFC; color: #64748B; cursor: pointer; transition: all 0.15s ease;">Tandai Dibaca</button>
            </div>
            <!-- Item list -->
            <div id="notif-popover-items" style="display: flex; flex-direction: column; gap: 8px;">
                <?php if (empty($storeNotifications)): ?>
                    <div style="text-align: center; padding: 22px 12px; color: #94A3B8; font-size: 0.78rem;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="margin: 0 auto 6px; display: block; opacity: 0.6;"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                        Semua pesanan tertangani.<br>Belum ada notifikasi baru.
                    </div>
                <?php else: ?>
                    <?php foreach ($storeNotifications as $notif): 
                        $nId = $notif['id_pesanan'];
                        $nCust = $notif['nama_pelanggan'];
                        $nTelp = preg_replace('/[^0-9]/', '', $notif['no_telepon']);
                        if (str_starts_with($nTelp, '0')) $nTelp = '62' . substr($nTelp, 1);
                        $isKilat = !empty($notif['is_kilat']);
                        $isSiap = ($notif['status_cucian'] === 'Siap Diambil');
                        $isLunas = (strtolower($notif['status_pembayaran']) === 'lunas');
                        $rak = $notif['lokasi_rak'] ?: '-';
                    ?>
                        <div class="notif-item-card" style="display: flex; justify-content: space-between; align-items: center; padding: 9px 11px; border-radius: 11px; background: #F8FAFC; border: 1px solid #F1F5F9;">
                            <div style="min-width: 0; flex: 1; padding-right: 8px;">
                                <div style="font-size: 0.8rem; font-weight: 700; color: #0F172A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?= htmlspecialchars($nCust) ?> <?= $isSiap && $rak !== '-' ? '&bull; Rak ' . htmlspecialchars($rak) : '' ?>
                                </div>
                                <div style="font-size: 0.72rem; color: #64748B; margin-top: 2px;">
                                    <?php if ($isKilat && !$isSiap): ?>
                                        <span style="color: #D97706; font-weight: 600;">⚡ Prioritas Kilat</span> &bull; Status: <?= strtolower($notif['status_cucian']) ?>
                                    <?php elseif ($isSiap): ?>
                                        <span style="color: #059669; font-weight: 600;">✓ Siap diambil</span> (<?= $isLunas ? 'Lunas' : 'Belum Lunas' ?>) &bull; <?= htmlspecialchars($nId) ?>
                                    <?php else: ?>
                                        <?= htmlspecialchars($nId) ?> &bull; Status: <?= strtolower($notif['status_cucian']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div style="flex-shrink: 0;">
                                <?php if ($isSiap && !empty($nTelp)): ?>
                                    <a href="https://wa.me/<?= $nTelp ?>?text=<?= urlencode("Halo {$nCust}, cucian Anda dengan nomor nota {$nId} sudah selesai dan siap diambil di LaundryGo (Rak {$rak}). Terima kasih!") ?>" target="_blank" class="btn-kabari-wa" onclick="kirimKabarWa('<?= htmlspecialchars($nCust) ?>', '<?= htmlspecialchars($nId) ?>', this)" style="font-size: 0.74rem; font-weight: 600; padding: 4px 10px; border-radius: 6px; background: #FFFFFF; border: 1px solid #CBD5E1; color: #0F172A; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;">Kabari WA</a>
                                <?php else: ?>
                                    <a href="<?= \App\Core\Helper::url('pesanan', ['keyword' => $nId]) ?>" style="font-size: 0.74rem; font-weight: 600; padding: 4px 10px; border-radius: 6px; background: #FFFFFF; border: 1px solid #CBD5E1; color: #0F172A; text-decoration: none; white-space: nowrap; transition: all 0.15s ease;">Lihat</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <!-- Footer -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 12px; padding-top: 8px; border-top: 1px solid #F1F5F9; font-size: 0.72rem;">
                <span style="color: #64748B;">Klik Kabari untuk konfirmasi WA</span>
                <a href="<?= \App\Core\Helper::url('pesanan', ['action' => 'selesai']) ?>" style="color: #0284C7; font-weight: 700; text-decoration: none;">Lihat Cucian Selesai &rarr;</a>
            </div>
        </div>
    </div>
</div>
<script>
function toggleKasirNotifPopover(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    const pop = document.getElementById('kasir-notif-popover');
    const btn = document.getElementById('notif-bell-btn');
    if (!pop) return;
    const isShown = pop.classList.contains('is-open');
    if (isShown) {
        pop.classList.remove('is-open');
        if (btn) btn.setAttribute('aria-expanded', 'false');
    } else {
        pop.classList.add('is-open');
        if (btn) btn.setAttribute('aria-expanded', 'true');
    }
}
function closeKasirNotifPopover() {
    const pop = document.getElementById('kasir-notif-popover');
    const btn = document.getElementById('notif-bell-btn');
    if (pop) {
        pop.classList.remove('is-open');
    }
    if (btn) btn.setAttribute('aria-expanded', 'false');
}
function kirimKabarWa(nama, noNota, btn) {
    if (btn.classList.contains('is-sent')) return;
    btn.classList.add('is-sent');
    btn.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:middle;margin-right:3px;"><polyline points="20 6 9 17 4 12"/></svg>Terkirim`;
    
    // Decrement badge
    const badge = document.getElementById('notif-bell-badge');
    const countBadge = document.getElementById('notif-popover-count');
    if (badge) {
        let current = parseInt(badge.innerText || '3', 10);
        if (current > 1) {
            badge.innerText = current - 1;
            badge.classList.add('badge-pulse');
            setTimeout(() => badge.classList.remove('badge-pulse'), 250);
            if (countBadge) countBadge.innerText = `${current - 1} baru`;
        } else {
            badge.style.display = 'none';
            if (countBadge) countBadge.innerText = '0 baru';
        }
    }
    
    if (typeof window.showToast === 'function') {
        window.showToast(`Pesan WhatsApp telah diteruskan ke ${nama} (${noNota}) untuk pengambilan cucian.`, 'success', 'WhatsApp Terkirim');
    }
}
function tandaiSemuaNotifDibaca() {
    const badge = document.getElementById('notif-bell-badge');
    const countBadge = document.getElementById('notif-popover-count');
    if (badge) {
        badge.style.opacity = '0';
        badge.style.transform = 'scale(0.5)';
        setTimeout(() => badge.style.display = 'none', 200);
    }
    if (countBadge) {
        countBadge.innerText = '0 baru';
        countBadge.style.background = '#F1F5F9';
        countBadge.style.color = '#64748B';
        countBadge.style.borderColor = '#E2E8F0';
    }
    if (typeof window.showToast === 'function') {
        window.showToast('Semua notifikasi gerai telah ditandai dibaca.', 'info', 'Notifikasi Dibaca');
    }
}
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('notif-bell-wrap');
    if (wrap && !wrap.contains(e.target)) {
        closeKasirNotifPopover();
    }
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeKasirNotifPopover();
});
if (window.location.search.includes('open_notif=1')) {
    document.addEventListener('DOMContentLoaded', function() {
        toggleKasirNotifPopover();
    });
}
</script>
