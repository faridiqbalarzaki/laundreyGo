<div class="selesai-header-actions" style="display: flex; align-items: center; gap: 12px;">
    <span class="page-head-date"><?= \App\Core\Helper::tanggalIndonesia(date('Y-m-d')) ?></span>
    <?php $openNotif = isset($_GET['open_notif']) && $_GET['open_notif'] === '1'; ?>
    <div id="notif-bell-wrap" class="notif-bell-wrap" style="position: relative;">
        <button type="button" id="notif-bell-btn" class="notif-bell-btn" onclick="toggleKasirNotifPopover(event)"
            aria-haspopup="true" aria-expanded="<?= $openNotif ? 'true' : 'false' ?>" aria-label="Notifikasi Gerai (3)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span id="notif-bell-badge" class="notif-bell-badge">3</span>
        </button>
        <div id="kasir-notif-popover" class="notif-popover <?= $openNotif ? 'is-open' : '' ?>" style="<?= $openNotif ? 'opacity: 1; visibility: visible; pointer-events: auto; transform: translateY(0) scale(1);' : '' ?> position: absolute; right: 0 !important; left: auto !important; top: calc(100% + 8px); width: 340px; z-index: 150; padding: 14px; background: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.06); border: 1px solid #E2E8F0;" role="region" aria-label="Pusat Notifikasi Gerai" onclick="event.stopPropagation()">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #F1F5F9;">
                <strong style="font-size: 0.88rem; color: #0F172A; font-weight: 700;">Notifikasi Gerai (3)</strong>
                <button type="button" onclick="closeKasirNotifPopover()" style="font-size: 0.72rem; padding: 2px 7px; border: 1px solid #E2E8F0; border-radius: 6px; background: #F8FAFC; color: #64748B; cursor: pointer;">Esc</button>
            </div>
            <!-- Item list -->
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <!-- Item 1 -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; border-radius: 10px; background: #F8FAFC; border: 1px solid #F1F5F9;">
                    <div style="min-width: 0;">
                        <div style="font-size: 0.8rem; font-weight: 700; color: #0F172A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Ibu Ratna Wulandari (LG-20261110-001)</div>
                        <div style="font-size: 0.72rem; color: #64748B; margin-top: 2px;"><span style="color: #D97706; font-weight: 600;">Prioritas Kilat</span> &bull; Status: diterima</div>
                    </div>
                    <a href="<?= \App\Core\Helper::url('pesanan', ['keyword' => 'LG-20261110-001']) ?>" style="margin-left: 8px; font-size: 0.74rem; font-weight: 600; padding: 4px 10px; border-radius: 6px; background: #FFFFFF; border: 1px solid #CBD5E1; color: #0F172A; text-decoration: none; white-space: nowrap;">Lihat</a>
                </div>
                <!-- Item 2 -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; border-radius: 10px; background: #F8FAFC; border: 1px solid #F1F5F9;">
                    <div style="min-width: 0;">
                        <div style="font-size: 0.8rem; font-weight: 700; color: #0F172A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Ibu Ratna Wulandari (LG-20261110-005)</div>
                        <div style="font-size: 0.72rem; color: #64748B; margin-top: 2px;"><span style="color: #D97706; font-weight: 600;">Prioritas Kilat</span> &bull; Status: dicuci</div>
                    </div>
                    <a href="<?= \App\Core\Helper::url('pesanan', ['keyword' => 'LG-20261110-005']) ?>" style="margin-left: 8px; font-size: 0.74rem; font-weight: 600; padding: 4px 10px; border-radius: 6px; background: #FFFFFF; border: 1px solid #CBD5E1; color: #0F172A; text-decoration: none; white-space: nowrap;">Lihat</a>
                </div>
                <!-- Item 3 -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; border-radius: 10px; background: #F8FAFC; border: 1px solid #F1F5F9;">
                    <div style="min-width: 0;">
                        <div style="font-size: 0.8rem; font-weight: 700; color: #0F172A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Bpk. Rahmat Santoso &bull; Rak C-02</div>
                        <div style="font-size: 0.72rem; color: #64748B; margin-top: 2px;">LG-20261110-006 siap diambil (Lunas)</div>
                    </div>
                    <button type="button" onclick="alert('Mengirim kabar WhatsApp ke Bpk. Rahmat Santoso...')" style="margin-left: 8px; font-size: 0.74rem; font-weight: 600; padding: 4px 10px; border-radius: 6px; background: #FFFFFF; border: 1px solid #CBD5E1; color: #0F172A; cursor: pointer; white-space: nowrap;">Kabari WA</button>
                </div>
            </div>
            <!-- Footer -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 12px; padding-top: 8px; border-top: 1px solid #F1F5F9; font-size: 0.72rem;">
                <span style="color: #64748B;">Klik Kabari untuk menandai WA terkirim</span>
                <a href="<?= \App\Core\Helper::url('pesanan', ['action' => 'selesai']) ?>" style="color: #0284C7; font-weight: 700; text-decoration: none;">Lihat Cucian Selesai</a>
            </div>
        </div>
    </div>
</div>
<script>
function toggleKasirNotifPopover(e) {
    if (e) e.stopPropagation();
    const pop = document.getElementById('kasir-notif-popover');
    const btn = document.getElementById('notif-bell-btn');
    if (!pop) return;
    const isShown = pop.classList.contains('is-open');
    if (isShown) {
        pop.classList.remove('is-open');
        pop.style.display = 'none';
        if (btn) btn.setAttribute('aria-expanded', 'false');
    } else {
        pop.classList.add('is-open');
        pop.style.display = 'block';
        pop.style.opacity = '1';
        pop.style.visibility = 'visible';
        pop.style.pointerEvents = 'auto';
        pop.style.transform = 'translateY(0) scale(1)';
        if (btn) btn.setAttribute('aria-expanded', 'true');
    }
}
function closeKasirNotifPopover() {
    const pop = document.getElementById('kasir-notif-popover');
    const btn = document.getElementById('notif-bell-btn');
    if (pop) {
        pop.classList.remove('is-open');
        pop.style.display = 'none';
    }
    if (btn) btn.setAttribute('aria-expanded', 'false');
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
        setTimeout(function() {
            const pop = document.getElementById('kasir-notif-popover');
            if (pop) pop.style.display = 'block';
        }, 120);
    });
}
</script>
