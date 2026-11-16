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
            alert('Gagal memuat rincian pesanan: ' + (json.message || 'Error'));
            closeOrderDrawer();
        }
    } catch (e) {
        console.error(e);
        alert('Terjadi kesalahan koneksi.');
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

// 5. THERMAL PRINTER HTML BUILDER
async function loadOrderToThermalPrint(orderId, type = 'receipt') {
    let order = currentDrawerOrder;
    if (!order || order.id_pesanan !== orderId) {
        const res = await fetch(`index.php?page=pesanan&action=detail&id=${encodeURIComponent(orderId)}`);
        const json = await res.json();
        if (json.success && json.data) {
            order = json.data;
        } else {
            alert('Gagal mengambil data untuk cetak.');
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

// 5. GLOBAL TOAST NOTIFICATION API (Aqua Dynamic Island Design)
window.showToast = function(message, type = 'success', title = null, duration = 4500) {
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
            icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>'
        },
        error: {
            title: 'Terjadi Kesalahan',
            icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>'
        },
        warning: {
            title: 'Perhatian',
            icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'
        },
        info: {
            title: 'Informasi',
            icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
        }
    };

    const meta = typeMeta[type] || typeMeta.info;
    const finalTitle = title || meta.title;

    const toast = document.createElement('div');
    toast.className = `flash-toast-item flash-toast-${type}`;
    toast.setAttribute('role', 'alert');
    toast.dataset.autoDismiss = duration;

    toast.innerHTML = `
        <div class="flash-toast-icon">${meta.icon}</div>
        <div class="flash-toast-body">
            <div class="flash-toast-header">
                <span class="flash-toast-title">${escapeHtml(finalTitle)}</span>
                <span class="flash-toast-timestamp">Baru saja</span>
            </div>
            <div class="flash-toast-message">${escapeHtml(message)}</div>
        </div>
        <button type="button" class="flash-toast-close" title="Tutup Notifikasi" aria-label="Tutup Notifikasi">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <div class="flash-toast-progress">
            <div class="flash-toast-progress-bar" style="animation-duration: ${duration}ms;"></div>
        </div>
    `;

    container.appendChild(toast);
    if (window.__attachToastBehavior) {
        window.__attachToastBehavior(toast);
    }
    return toast;
};

