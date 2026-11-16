/**
 * LaundryGo Master Application Script
 * Pure Vanilla JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initClock();
    initGlobalShortcuts();
});

// 1. SIDEBAR TOGGLE
function initSidebar() {
    const btn = document.getElementById('btnSidebarToggle');
    const sidebar = document.getElementById('appSidebar');

    if (btn && sidebar) {
        btn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        // Tutup jika klik di luar pada layar mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 992 && 
                sidebar.classList.contains('open') && 
                !sidebar.contains(e.target) && 
                !btn.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    }
}

// 2. REALTIME CLOCK
function initClock() {
    const clock = document.getElementById('digitalClock');
    if (!clock) return;

    function update() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        clock.innerText = `${h}:${m} WIB`;
    }

    setInterval(update, 1000);
}

// 3. GLOBAL KEYBOARD SHORTCUTS
function initGlobalShortcuts() {
    const btnHelp = document.getElementById('btnKeyboardShortcuts');
    if (btnHelp) {
        btnHelp.addEventListener('click', () => {
            if (typeof openShortcutModal === 'function') openShortcutModal();
        });
    }

    document.addEventListener('keydown', (e) => {
        // Esc: Tutup drawer atau modal
        if (e.key === 'Escape') {
            closeOrderDrawer();
            if (typeof closeAddCustomerModal === 'function') closeAddCustomerModal();
            if (typeof closeShortcutModal === 'function') closeShortcutModal();
            if (typeof closePrintModal === 'function') closePrintModal();
            if (typeof closePickupModal === 'function') closePickupModal();
        }

        // Global search focus '/'
        if (e.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
            const searchInput = document.getElementById('globalSearchInput');
            if (searchInput) {
                e.preventDefault();
                searchInput.focus();
            }
        }
    });

    const globalSearch = document.getElementById('globalSearchInput');
    if (globalSearch) {
        globalSearch.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const val = globalSearch.value.trim();
                if (val) {
                    window.location.href = `index.php?page=pesanan&q=${encodeURIComponent(val)}`;
                }
            }
        });
    }
}

// 4. INTERACTIVE ORDER DRAWER
let currentDrawerOrder = null;

async function openOrderDrawer(orderId) {
    const backdrop = document.getElementById('orderDrawerBackdrop');
    const drawer = document.getElementById('orderDrawer');
    const loading = document.getElementById('drawerLoading');
    const content = document.getElementById('drawerContent');

    if (!drawer) return;

    backdrop.style.display = 'block';
    drawer.classList.add('open');
    loading.style.display = 'flex';
    content.style.display = 'none';

    document.getElementById('drawerOrderId').innerText = orderId;

    try {
        const res = await fetch(`index.php?page=pesanan&action=detail&id=${encodeURIComponent(orderId)}`);
        const json = await res.json();

        if (json.success && json.data) {
            renderDrawerData(json.data);
            currentDrawerOrder = json.data;
        } else {
            showToast('Gagal memuat rincian pesanan: ' + (json.message || 'Data tidak ditemukan'), 'error');
            closeOrderDrawer();
        }
    } catch (e) {
        console.error(e);
        showToast('Terjadi kesalahan koneksi saat memuat pesanan.', 'error');
        closeOrderDrawer();
    } finally {
        loading.style.display = 'none';
        content.style.display = 'block';
    }
}

function closeOrderDrawer() {
    const backdrop = document.getElementById('orderDrawerBackdrop');
    const drawer = document.getElementById('orderDrawer');
    if (drawer) drawer.classList.remove('open');
    if (backdrop) backdrop.style.display = 'none';
    currentDrawerOrder = null;
}

function renderDrawerData(order) {
    // Badges
    const statusBadge = document.getElementById('drawerStatusBadge');
    statusBadge.className = 'badge-status ' + (order.status_cucian || '').replace(/\s+/g, '');
    statusBadge.innerText = order.status_cucian;

    const payBadge = document.getElementById('drawerPaymentBadge');
    payBadge.className = order.status_pembayaran === 'Lunas' ? 'badge-lunas' : 'badge-belum-lunas';
    payBadge.innerText = order.status_pembayaran;

    const rakBadge = document.getElementById('drawerRakBadge');
    if (order.lokasi_rak) {
        rakBadge.style.display = 'inline-flex';
        rakBadge.innerText = 'Rak: ' + order.lokasi_rak;
    } else {
        rakBadge.style.display = 'none';
    }

    // Workflow Actions (Maju / Undur Tahapan Alur Cucian)
    const actionSection = document.getElementById('drawerActionSection');
    const actionBtns = document.getElementById('drawerStatusActionButtons');
    const statusDesc = document.getElementById('drawerStatusDesc');

    if (actionSection && actionBtns) {
        actionSection.style.display = 'block';
        const st = (order.status_cucian || '').toLowerCase();
        let btnsHtml = '';

        if (statusDesc) {
            statusDesc.innerText = order.status_cucian;
        }

        if (st === 'antrean') {
            btnsHtml = `
                <button type="button" class="btn btn-primary btn-sm" onclick="confirmDrawerStatusTransition('${escapeHtml(order.id_pesanan)}', 'Dicuci', 'Mulai Cuci')">
                    <span>Mulai Cuci &rarr;</span>
                </button>
            `;
        } else if (st === 'dicuci') {
            btnsHtml = `
                <button type="button" class="btn btn-secondary btn-sm" style="color: #64748B; border-color: #CBD5E1; display: inline-flex; align-items: center; gap: 4px;" onclick="confirmDrawerStatusTransition('${escapeHtml(order.id_pesanan)}', 'Antrean', 'Kembalikan ke Antrean')">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5v0a5.5 5.5 0 0 1-5.5 5.5H11"/></svg>
                    <span>&larr; Ke Antrean</span>
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="confirmDrawerStatusTransition('${escapeHtml(order.id_pesanan)}', 'Disetrika', 'Selesai Cuci & Setrika')">
                    <span>Lanjut ke Disetrika &rarr;</span>
                </button>
            `;
        } else if (st === 'disetrika') {
            btnsHtml = `
                <button type="button" class="btn btn-secondary btn-sm" style="color: #64748B; border-color: #CBD5E1; display: inline-flex; align-items: center; gap: 4px;" onclick="confirmDrawerStatusTransition('${escapeHtml(order.id_pesanan)}', 'Dicuci', 'Pencucian Ulang')">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5v0a5.5 5.5 0 0 1-5.5 5.5H11"/></svg>
                    <span>&larr; Ke Cuci</span>
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="confirmDrawerStatusTransition('${escapeHtml(order.id_pesanan)}', 'Siap Diambil', 'Selesai & Masuk Rak')">
                    <span>Siap Diambil &rarr;</span>
                </button>
            `;
        } else if (st === 'siap diambil' || st === 'siap_diambil') {
            btnsHtml = `
                <button type="button" class="btn btn-secondary btn-sm" style="color: #64748B; border-color: #CBD5E1; display: inline-flex; align-items: center; gap: 4px;" onclick="confirmDrawerStatusTransition('${escapeHtml(order.id_pesanan)}', 'Disetrika', 'Perlu Setrika Ulang')">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5v0a5.5 5.5 0 0 1-5.5 5.5H11"/></svg>
                    <span>&larr; Ke Setrika</span>
                </button>
                <button type="button" class="btn btn-success btn-sm" onclick="confirmDrawerStatusTransition('${escapeHtml(order.id_pesanan)}', 'Selesai', 'Serahkan ke Pelanggan')">
                    <span>Serahkan ke Pelanggan &check;</span>
                </button>
            `;
        } else if (st === 'selesai' || st === 'sudah diambil') {
            btnsHtml = `
                <span style="font-size: 0.76rem; color: #16A34A; font-weight: 700; margin-right: 4px;">&check; Cucian telah selesai & diserahkan</span>
                <button type="button" class="btn btn-secondary btn-sm" style="color: #B45309; border-color: #FDE68A; background: #FEFCE8; display: inline-flex; align-items: center; gap: 4px;" onclick="confirmDrawerStatusTransition('${escapeHtml(order.id_pesanan)}', 'Siap Diambil', 'Batalkan Penyerahan')">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5v0a5.5 5.5 0 0 1-5.5 5.5H11"/></svg>
                    <span>Batal Serahkan</span>
                </button>
            `;
        }

        actionBtns.innerHTML = btnsHtml;
    }

    // Customer
    document.getElementById('drawerCustName').innerText = order.nama_pelanggan || '-';
    document.getElementById('drawerCustPhone').innerText = order.no_telepon || '-';
    document.getElementById('drawerCustAddress').innerText = order.alamat || '-';

    // Items
    const itemsList = document.getElementById('drawerItemsList');
    itemsList.innerHTML = (order.items || []).map(it => `
        <div class="drawer-item-row">
            <div>
                <strong>${escapeHtml(it.nama_layanan)}</strong>
                <div class="text-muted" style="font-size:12px;">${parseFloat(it.jumlah)} ${it.satuan} &times; ${formatRupiah(it.harga_satuan)}</div>
            </div>
            <div class="font-bold">${formatRupiah(it.jumlah * it.harga_satuan)}</div>
        </div>
    `).join('');

    document.getElementById('drawerTotalHarga').innerText = order.total_harga_formatted || formatRupiah(order.total_harga);

    // Notes
    document.getElementById('drawerParfum').innerText = '🌸 ' + (order.parfum || 'Ocean Fresh');
    document.getElementById('drawerKilat').innerText = order.is_kilat ? '⚡ Layanan Kilat' : 'Reguler';
    document.getElementById('drawerCatatan').innerText = order.catatan_khusus || 'Tidak ada catatan khusus';

    // Timeline
    const timelineEl = document.getElementById('drawerTimeline');
    if (order.timeline && order.timeline.length > 0) {
        timelineEl.innerHTML = order.timeline.map(tl => `
            <div class="timeline-entry">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="timeline-time text-muted">${formatDateTime(tl.waktu_status)}</div>
                    <div class="timeline-title font-semibold">${escapeHtml(tl.status)}</div>
                    <div class="timeline-desc text-muted">${escapeHtml(tl.keterangan || '')}</div>
                </div>
            </div>
        `).join('');
    } else {
        timelineEl.innerHTML = '<p class="text-muted" style="font-size:12px;">Belum ada catatan status.</p>';
    }

    // WhatsApp Link
    const waLink = document.getElementById('drawerWaLink');
    const cleanPhone = (order.no_telepon || '').replace(/[^0-9]/g, '');
    const waText = encodeURIComponent(`Halo Kak ${order.nama_pelanggan}, status cucian Anda (${order.id_pesanan}) saat ini: ${order.status_cucian}. Total: ${order.total_harga_formatted} (${order.status_pembayaran}). Terima kasih telah mempercayai Adi Laundry!`);
    waLink.href = `https://wa.me/${cleanPhone}?text=${waText}`;
}

function printReceiptFromDrawer() {
    if (currentDrawerOrder) {
        openPrintModal(currentDrawerOrder.id_pesanan);
    }
}

function confirmDrawerStatusTransition(orderId, targetStatus, actionLabel) {
    if (!currentDrawerOrder || currentDrawerOrder.id_pesanan !== orderId) return;

    const isRevert = ['Antrean', 'Dicuci', 'Disetrika'].includes(targetStatus) && currentDrawerOrder.status_cucian !== 'Antrean';
    const titleText = isRevert ? `Undur ke Tahap ${targetStatus}?` : `Ubah ke Tahap ${targetStatus}?`;
    const msgText = isRevert 
        ? `Status pengerjaan pesanan ini akan diundurkan kembali ke tahap <strong>${targetStatus}</strong>. Catatan pengunduran akan tersimpan rapi di riwayat timeline.` 
        : `Status pengerjaan pesanan ini akan dimajukan ke tahap <strong>${targetStatus}</strong>.`;

    if (window.AquaModal && typeof window.AquaModal.confirm === 'function') {
        window.AquaModal.confirm({
            title: titleText,
            badge: `${orderId} • ${currentDrawerOrder.nama_pelanggan || 'Pelanggan'} (${currentDrawerOrder.status_cucian})`,
            message: msgText,
            confirmText: `Ya, ${actionLabel}`,
            cancelText: 'Batal',
            type: isRevert ? 'warning' : 'primary',
            onConfirm: () => {
                executeDrawerStatusTransition(orderId, targetStatus, actionLabel);
            }
        });
    } else {
        executeDrawerStatusTransition(orderId, targetStatus, actionLabel);
    }
}

async function executeDrawerStatusTransition(orderId, targetStatus, actionLabel) {
    try {
        const fd = new FormData();
        fd.append('ajax', '1');
        fd.append('id_pesanan', orderId);
        fd.append('status_cucian', targetStatus);
        fd.append('keterangan', `${actionLabel} via drawer rincian oleh petugas`);
        if (['Antrean', 'Dicuci', 'Disetrika'].includes(targetStatus)) {
            fd.append('lokasi_rak', 'clear');
        }

        const res = await fetch('index.php?page=pesanan&action=updateStatus', {
            method: 'POST',
            body: fd
        });
        const json = await res.json().catch(() => ({}));

        if (json.success) {
            if (typeof showToast === 'function') {
                showToast(`Pesanan ${orderId} berhasil diubah ke ${targetStatus}!`, 'success', 'Status Diperbarui');
            }
            openOrderDrawer(orderId);
            // Refresh tabel jika ada ORDERS_APP
            if (window.ORDERS_APP && typeof window.ORDERS_APP.reloadTable === 'function') {
                window.ORDERS_APP.reloadTable();
            }
        } else {
            if (typeof showToast === 'function') {
                showToast(json.message || 'Gagal memperbarui status', 'error', 'Kesalahan');
            } else {
                alert(json.message || 'Gagal memperbarui status');
            }
        }
    } catch (err) {
        console.error('Error status transition:', err);
    }
}

// 5. THERMAL PRINTER HTML BUILDER
async function loadOrderToThermalPrint(orderId, type = 'receipt') {
    let order = currentDrawerOrder;
    if (!order || order.id_pesanan !== orderId) {
        const res = await fetch(`index.php?page=pesanan&action=detail&id=${encodeURIComponent(orderId)}`);
        const json = await res.json();
        if (json.success && json.data) {
            order = json.data;
        } else {
            showToast('Gagal mengambil data untuk cetak nota.', 'error');
            return;
        }
    }

    const printContent = document.getElementById('thermalPrintContent');
    const printArea = document.getElementById('thermal-print-area');

    if (type === 'receipt') {
        printContent.innerHTML = `
            <div style="text-align:center;font-family:'Courier New',monospace;font-size:11px;line-height:1.2;">
                <strong style="font-size:14px;">ADI LAUNDRY</strong><br>
                <span>Karangploso, Malang</span><br>
                <span>Telp: 0812-3456-7890</span><br>
                --------------------------------<br>
                <div style="text-align:left;font-size:10px;">
                    No. Nota : <strong>${order.id_pesanan}</strong><br>
                    Tgl Masuk: ${order.tanggal_masuk_formatted || order.tanggal_masuk}<br>
                    Selesai  : ${order.tanggal_keluar_formatted || order.tanggal_keluar}<br>
                    Pelanggan: <strong>${escapeHtml(order.nama_pelanggan)}</strong> (${order.no_telepon})<br>
                    Parfum   : ${escapeHtml(order.parfum || 'Ocean Fresh')}<br>
                    ${order.is_kilat ? '*** PRIORITAS KILAT ***<br>' : ''}
                </div>
                --------------------------------<br>
                <div style="text-align:left;font-size:10px;">
                    ${(order.items || []).map(it => `
                        <div>${escapeHtml(it.nama_layanan)}</div>
                        <div style="display:flex;justify-content:space-between;">
                            <span>${parseFloat(it.jumlah)} ${it.satuan} x ${formatRupiah(it.harga_satuan)}</span>
                            <strong>${formatRupiah(it.jumlah * it.harga_satuan)}</strong>
                        </div>
                    `).join('')}
                </div>
                --------------------------------<br>
                <div style="display:flex;justify-content:space-between;font-weight:bold;font-size:12px;">
                    <span>TOTAL:</span>
                    <span>${order.total_harga_formatted || formatRupiah(order.total_harga)}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:10px;margin-top:2px;">
                    <span>Status Pembayaran:</span>
                    <strong>${order.status_pembayaran}</strong>
                </div>
                ${order.catatan_khusus ? `<div style="text-align:left;font-size:9px;margin-top:4px;">Catatan: ${escapeHtml(order.catatan_khusus)}</div>` : ''}
                --------------------------------<br>
                <div style="font-size:9px;text-align:center;margin-top:4px;">
                    Cek status cucian online di:<br>
                    <strong>localhost/Laundrey/index.php?page=tracking</strong><br>
                    Ketik nomor nota: ${order.id_pesanan}<br><br>
                    Terima Kasih Atas Kepercayaan Anda!
                </div>
            </div>
        `;
    } else {
        // Label Stiker Pakaian 58x40mm
        printContent.innerHTML = `
            <div style="text-align:center;font-family:'Courier New',monospace;font-size:11px;line-height:1.2;border:1px dashed #000;padding:5px;">
                <strong style="font-size:13px;">ADI LAUNDRY</strong><br>
                <div style="font-size:16px;font-weight:bold;margin:4px 0;">${order.id_pesanan}</div>
                <div style="font-size:12px;font-weight:bold;">${escapeHtml(order.nama_pelanggan)}</div>
                <div style="font-size:10px;margin-top:2px;">
                    ${order.is_kilat ? '⚡ [KILAT EXPRESS] • ' : ''}Rak: <strong>${order.lokasi_rak || 'A1'}</strong>
                </div>
                <div style="font-size:9px;margin-top:2px;">
                    Parfum: ${escapeHtml(order.parfum || 'Ocean Fresh')}
                </div>
                <div style="font-size:9px;color:#555;margin-top:2px;">
                    SLA: ${order.tanggal_keluar_formatted || order.tanggal_keluar}
                </div>
            </div>
        `;
    }

    printArea.style.display = 'block';
}

// Helpers
function formatRupiah(num) {
    return 'Rp ' + Math.round(num).toLocaleString('id-ID');
}

function escapeHtml(text) {
    if (!text) return '';
    return text.toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function formatDateTime(str) {
    if (!str) return '-';
    const d = new Date(str);
    return `${d.getDate()}/${d.getMonth()+1}/${d.getFullYear()} ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
}

// 5. GLOBAL TOAST NOTIFICATION API (createui Showcase Style)
window.showToast = function(message, type = 'success', title = null, duration = 4200) {
    let container = document.getElementById('flashToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'flashToastContainer';
        container.className = 'flash-toast-container';
        container.setAttribute('aria-live', 'polite');
        container.setAttribute('role', 'region');
        container.setAttribute('aria-label', 'Notifikasi Sistem');
        document.body.appendChild(container);
    }

    const typeMeta = {
        success: {
            title: 'Berhasil',
            icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>'
        },
        error: {
            title: 'Terjadi Kesalahan',
            icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
        },
        warning: {
            title: 'Perhatian',
            icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'
        },
        info: {
            title: 'Informasi',
            icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
        }
    };

    const meta = typeMeta[type] || typeMeta.info;
    const finalTitle = title || meta.title;

    const toast = document.createElement('div');
    toast.className = `flash-toast-item flash-toast-${type}`;
    toast.setAttribute('role', 'alert');
    toast.dataset.autoDismiss = duration;
    toast.style.setProperty('--toast-duration', duration + 'ms');

    toast.innerHTML = `
        <div class="flash-toast-main">
            <div class="flash-toast-icon">${meta.icon}</div>
            <div class="flash-toast-body">
                <div class="flash-toast-header">
                    <span class="flash-toast-title">${escapeHtml(finalTitle)}</span>
                </div>
                <div class="flash-toast-message">${escapeHtml(message)}</div>
            </div>
            <button type="button" class="flash-toast-close" title="Tutup Notifikasi" aria-label="Tutup Notifikasi">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="flash-toast-progress"></div>
    `;

    container.appendChild(toast);
    if (window.__attachToastBehavior) {
        window.__attachToastBehavior(toast);
    }
    return toast;
};

// 6. AQUA CONFIRMATION MODAL SYSTEM (Foto 1 Style: Samuel Adekoya)
window.AquaModal = (function() {
    let activeModal = null;

    function close() {
        if (!activeModal) return;
        const modal = activeModal;
        activeModal = null;
        const card = modal.querySelector('.aqua-modal-card');
        if (card) card.classList.add('is-leaving');
        modal.classList.add('is-leaving');
        setTimeout(() => {
            if (modal.parentNode) modal.parentNode.removeChild(modal);
        }, 180);
    }

    function confirm(options) {
        close();

        const title = options.title || 'Konfirmasi Tindakan';
        const message = options.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
        const badge = options.badge || '';
        const confirmText = options.confirmText || 'Ya, Lanjutkan';
        const cancelText = options.cancelText || 'Batal';
        const type = options.type || 'primary';
        const onConfirm = typeof options.onConfirm === 'function' ? options.onConfirm : () => {};
        const onCancel = typeof options.onCancel === 'function' ? options.onCancel : () => {};

        let iconSvg = '';
        if (options.icon) {
            iconSvg = options.icon;
        } else if (type === 'danger') {
            iconSvg = `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>`;
        } else if (type === 'success') {
            iconSvg = `<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`;
        } else if (type === 'warning') {
            if (/keluar|logout/i.test(title)) {
                iconSvg = `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>`;
            } else {
                iconSvg = `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`;
            }
        } else {
            iconSvg = `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>`;
        }

        const backdrop = document.createElement('div');
        backdrop.className = `aqua-modal-backdrop aqua-modal-${type}`;
        backdrop.setAttribute('role', 'dialog');
        backdrop.setAttribute('aria-modal', 'true');
        backdrop.setAttribute('aria-labelledby', 'aquaModalTitle');

        backdrop.innerHTML = `
            <div class="aqua-modal-card" onclick="event.stopPropagation()">
                <div class="aqua-modal-icon-wrap">
                    ${iconSvg}
                </div>
                <h3 id="aquaModalTitle" class="aqua-modal-title">${escapeHtml(title)}</h3>
                ${badge ? `<div class="aqua-modal-badge">${badge}</div>` : ''}
                <div class="aqua-modal-desc">${message}</div>
                <div class="aqua-modal-actions">
                    <button type="button" class="aqua-modal-btn aqua-modal-btn-cancel">${escapeHtml(cancelText)}</button>
                    <button type="button" class="aqua-modal-btn aqua-modal-btn-confirm btn-${type}">${escapeHtml(confirmText)}</button>
                </div>
            </div>
        `;

        backdrop.addEventListener('click', () => {
            close();
            onCancel();
        });

        const cancelBtn = backdrop.querySelector('.aqua-modal-btn-cancel');
        const confirmBtn = backdrop.querySelector('.aqua-modal-btn-confirm');

        cancelBtn.addEventListener('click', () => {
            close();
            onCancel();
        });

        confirmBtn.addEventListener('click', () => {
            confirmBtn.style.opacity = '0.85';
            confirmBtn.disabled = true;
            onConfirm();
            close();
        });

        document.body.appendChild(backdrop);
        activeModal = backdrop;
        confirmBtn.focus();
    }

    document.addEventListener('keydown', function(e) {
        if (!activeModal) return;
        if (e.key === 'Escape') {
            e.preventDefault();
            close();
        }
    });

    return { confirm, close };
})();
window.AquaModal = AquaModal;

// Automatic Declarative Interceptor for forms with data-confirm
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (!form || !form.dataset || !form.dataset.confirm) return;
    if (form._aquaConfirmed) return;

    e.preventDefault();
    e.stopPropagation();

    const title = form.dataset.confirmTitle || 'Konfirmasi Persetujuan';
    const message = form.dataset.confirmMessage || 'Apakah Anda yakin ingin memproses tindakan ini?';
    const badge = form.dataset.confirmBadge || '';
    const confirmText = form.dataset.confirmBtn || 'Ya, Lanjutkan';
    const cancelText = form.dataset.cancelBtn || 'Batal';
    const type = form.dataset.confirmType || 'primary';

    AquaModal.confirm({
        title: title,
        message: message,
        badge: badge,
        confirmText: confirmText,
        cancelText: cancelText,
        type: type,
        onConfirm: function() {
            form._aquaConfirmed = true;
            form.submit();
        }
    });
}, true);

// Automatic Declarative Interceptor for links/buttons with data-confirm
document.addEventListener('click', function(e) {
    const trigger = e.target.closest('[data-confirm]');
    if (!trigger || trigger.tagName === 'FORM') return;

    e.preventDefault();
    e.stopPropagation();

    const title = trigger.dataset.confirmTitle || 'Konfirmasi Tindakan';
    const message = trigger.dataset.confirmMessage || 'Apakah Anda yakin ingin melanjutkan?';
    const badge = trigger.dataset.confirmBadge || '';
    const confirmText = trigger.dataset.confirmBtn || 'Ya, Lanjutkan';
    const cancelText = trigger.dataset.cancelBtn || 'Batal';
    const type = trigger.dataset.confirmType || 'primary';

    AquaModal.confirm({
        title: title,
        message: message,
        badge: badge,
        confirmText: confirmText,
        cancelText: cancelText,
        type: type,
        onConfirm: function() {
            if (trigger.tagName === 'A' && trigger.href) {
                window.location.href = trigger.href;
            } else if (trigger.onclick) {
                trigger.onclick();
            }
        }
    });
}, true);

