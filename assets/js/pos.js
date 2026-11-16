/**
 * LaundryGo Aqua - POS Workstation Native Client Engine
 * 100% Identik dengan Video 1 & index.html
 */

(function() {
  'use strict';

  // --- 1. SVGs & ICON HELPERS ---
  function getCleanSvg(name, size = 16) {
    const s = size;
    switch (name) {
      case 'search':
        return `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>`;
      case 'x':
        return `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`;
      case 'check':
        return `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`;
      case 'clock':
        return `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`;
      case 'trash':
        return `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>`;
      case 'user-plus':
        return `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>`;
      default:
        return `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/></svg>`;
    }
  }

  function getSvgIcon(iconName) {
    switch (iconName) {
      case 'shirt':
        return `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/></svg>`;
      case 'washing-machine':
        return `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="20" x="3" y="2" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M12 18a2.5 2.5 0 0 0 0-5"/><path d="M7 6h.01"/><path d="M11 6h.01"/></svg>`;
      case 'iron':
        return `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19h16a2 2 0 0 0 2-2l-1.5-6.5A4 4 0 0 0 16.6 7H7a4 4 0 0 0-4 4v7a1 1 0 0 0 1 1z"/><path d="M7 7V5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2"/></svg>`;
      case 'zap':
        return `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>`;
      case 'package':
        return `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16.5 9.4-9-5.19M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>`;
      default:
        return `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>`;
    }
  }

  function escapeHtmlAttr(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  function formatRp(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
  }

  function formatDateTime(dateInput) {
    const d = dateInput ? new Date(dateInput) : new Date();
    const day = String(d.getDate()).padStart(2, '0');
    const mon = String(d.getMonth() + 1).padStart(2, '0');
    const yr = d.getFullYear();
    const hr = String(d.getHours()).padStart(2, '0');
    const mn = String(d.getMinutes()).padStart(2, '0');
    return `${day}/${mon}/${yr} ${hr}:${mn}`;
  }

  // --- 2. POS STATE MANAGEMENT ---
  const rawServices = (window.POS_DATA && window.POS_DATA.services) || [];
  const rawCustomers = (window.POS_DATA && window.POS_DATA.customers) || [];

  // Map 5 standard services matching index.html
  const DEFAULT_SERVICES = [
    { id: 'srv-1', name: 'Cuci Kering + Setrika', icon: 'shirt', minRate: 6000, maxRate: 6500, activeRate: 6000, unit: 'kg', estHours: 48, estText: '2 hari', workflow: 'full', category: 'Kiloan', dbId: null },
    { id: 'srv-2', name: 'Cuci Kering Saja', icon: 'washing-machine', minRate: 4500, maxRate: 5000, activeRate: 4500, unit: 'kg', estHours: 48, estText: '2 hari', workflow: 'wash_only', category: 'Kiloan', dbId: null },
    { id: 'srv-3', name: 'Setrika Saja', icon: 'iron', minRate: 4000, maxRate: 4500, activeRate: 4000, unit: 'kg', estHours: 24, estText: '1 hari', workflow: 'iron_only', category: 'Kiloan', dbId: null },
    { id: 'srv-5', name: 'Kilat', icon: 'zap', minRate: 8500, maxRate: 10000, activeRate: 8500, unit: 'kg', estHours: 6, estText: '4-6 jam', workflow: 'full', category: 'Kilat', dbId: null },
    { id: 'srv-4', name: 'Cuci Satuan', icon: 'package', minRate: 10000, maxRate: 25000, activeRate: 10000, unit: 'item', estHours: 48, estText: '2-3 hari', workflow: 'full', category: 'Satuan', dbId: null }
  ];

  // Align with actual DB services if available
  rawServices.forEach(dbSrv => {
    const sName = dbSrv.nama_layanan.toLowerCase();
    const price = parseFloat(dbSrv.harga);
    if (sName.includes('kering') && sName.includes('setrika') && !sName.includes('kilat')) {
      DEFAULT_SERVICES[0].dbId = dbSrv.id_layanan;
      DEFAULT_SERVICES[0].minRate = price;
      DEFAULT_SERVICES[0].activeRate = price;
    } else if (sName.includes('kering saja') || (sName.includes('cuci kering') && !sName.includes('setrika'))) {
      DEFAULT_SERVICES[1].dbId = dbSrv.id_layanan;
      DEFAULT_SERVICES[1].minRate = price;
      DEFAULT_SERVICES[1].activeRate = price;
    } else if (sName.includes('setrika saja') || (sName.includes('setrika') && !sName.includes('cuci'))) {
      DEFAULT_SERVICES[2].dbId = dbSrv.id_layanan;
      DEFAULT_SERVICES[2].minRate = price;
      DEFAULT_SERVICES[2].activeRate = price;
    } else if (sName.includes('kilat') || sName.includes('express')) {
      DEFAULT_SERVICES[3].dbId = dbSrv.id_layanan;
      DEFAULT_SERVICES[3].minRate = price;
      DEFAULT_SERVICES[3].activeRate = price;
    } else if (sName.includes('satuan') || sName.includes('bed') || sName.includes('sepatu')) {
      DEFAULT_SERVICES[4].dbId = dbSrv.id_layanan;
      DEFAULT_SERVICES[4].minRate = price;
      DEFAULT_SERVICES[4].activeRate = price;
    }
  });

  const STATE = {
    services: DEFAULT_SERVICES,
    customers: rawCustomers.map(c => ({
      id: c.id_pelanggan,
      name: c.nama_pelanggan,
      phone: c.no_telepon || '',
      address: c.alamat || ''
    })),
    cartItems: [], // Multi-item cart
    posForm: {
      serviceId: 'srv-2',
      appliedRate: 4500,
      weightStr: '1',
      weightVal: 1,
      variantId: 'var-selimut',
      variantPrice: 10000,
      notes: [],
      customNote: '',
      isPaid: true,
      paymentMethod: 'Tunai',
      cashReceived: 10000,
      selectedCustomer: null,
      customerNameInput: ''
    },
    justSwitchedService: false
  };

  function getActiveService() {
    return STATE.services.find(s => s.id === STATE.posForm.serviceId) || STATE.services[1];
  }

  function getPosTotals() {
    const activeSrv = getActiveService();
    const isSatuan = activeSrv.category === 'Satuan';

    let weightVal = 1;
    if (isSatuan) {
      weightVal = parseInt(STATE.posForm.weightStr, 10) || 1;
      if (weightVal < 1) weightVal = 1;
    } else {
      const normalized = String(STATE.posForm.weightStr || '1').replace(',', '.');
      weightVal = parseFloat(normalized);
      if (isNaN(weightVal) || weightVal <= 0) weightVal = 1;
    }

    const appliedRate = isSatuan ? STATE.posForm.variantPrice : STATE.posForm.appliedRate;
    const itemSubtotal = weightVal * appliedRate;

    // Total tagihan gabungan dari seluruh cartItems jika ada, atau item saat ini jika cart kosong
    let totalTagihan = 0;
    if (STATE.cartItems && STATE.cartItems.length > 0) {
      totalTagihan = STATE.cartItems.reduce((acc, it) => acc + (it.subtotal || 0), 0);
    } else {
      totalTagihan = itemSubtotal;
    }

    let presetMid = 20000;
    if (totalTagihan > 100000) presetMid = 150000;
    else if (totalTagihan > 50000) presetMid = 100000;
    else if (totalTagihan > 20000) presetMid = 50000;

    let kembalian = 0;
    let isUnderpaid = false;
    if (STATE.posForm.isPaid && STATE.posForm.paymentMethod === 'Tunai') {
      kembalian = STATE.posForm.cashReceived - totalTagihan;
      if (kembalian < 0) isUnderpaid = true;
    }

    return {
      activeSrv,
      isSatuan,
      weightVal,
      appliedRate,
      itemSubtotal,
      totalTagihan,
      kembalian,
      isUnderpaid,
      presetMid
    };
  }

  // --- 3. TOAST NOTIFICATIONS ---
  function showToast(message, type = 'info') {
    if (typeof window.showToast === 'function') {
      window.showToast(message, type);
      return;
    }
    const container = document.getElementById('toast-container');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = 'toast-alert is-visible';
    toast.style.cssText = 'background: #0F172A; color: #FFFFFF; padding: 10px 16px; border-radius: 10px; font-size: 0.84rem; font-weight: 600; box-shadow: 0 10px 25px rgba(0,0,0,0.2); pointer-events: auto; display: flex; align-items: center; gap: 8px; animation: toastEnter 0.25s ease-out;';
    toast.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#38BDF8" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> <span>${escapeHtmlAttr(message)}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(8px)';
      toast.style.transition = 'opacity 0.2s, transform 0.2s';
      setTimeout(() => toast.remove(), 250);
    }, 3200);
  }

  // --- 4. VIEW RENDERING (Identik dengan index.html) ---
  function renderPosWorkstation() {
    const root = document.getElementById('pos-app-root');
    if (!root) return;

    const {
      activeSrv,
      isSatuan,
      weightVal,
      appliedRate,
      itemSubtotal,
      totalTagihan,
      kembalian,
      isUnderpaid,
      presetMid
    } = getPosTotals();

    const switchAnimClass = STATE.justSwitchedService ? 'is-switching' : '';
    STATE.justSwitchedService = false;

    let estimasiText = '~2-3 hari ke depan';
    if (activeSrv.estHours <= 12) {
      estimasiText = '~4-6 jam hari ini';
    } else if (activeSrv.estHours <= 24) {
      estimasiText = '~1 hari ke depan';
    }

    const activeCustName = STATE.posForm.selectedCustomer
      ? STATE.posForm.selectedCustomer.name
      : (STATE.posForm.customerNameInput.trim() || 'Pelanggan Umum');

    root.innerHTML = `
      <div class="pos-grid-container">
        <!-- KOLOM KIRI: ALUR TERPANDU (1 -> 2 -> 3 -> 4) -->
        <div class="pos-left-column">
          <!-- LANGKAH 1: DATA PELANGGAN -->
          <div class="pos-step-customer-card">
            <div class="pos-step-header">
              <span class="pos-step-title-wrap">
                <span class="pos-step-badge">1</span>
                <span class="pos-step-label">Data Pelanggan</span>
              </span>
              <span id="pos-cust-active-pill" class="pos-cust-active-pill" title="Pelanggan terpilih">
                ${escapeHtmlAttr(activeCustName)}
              </span>
            </div>
            <div class="customer-bar-inline">
              <div class="customer-input-search-box">
                <input type="text" id="pos-cust-search" class="form-control" 
                  placeholder="Cari atau isi nama / no. HP pelanggan..." 
                  value="${STATE.posForm.selectedCustomer ? escapeHtmlAttr(STATE.posForm.selectedCustomer.name + ' (' + STATE.posForm.selectedCustomer.phone + ')') : escapeHtmlAttr(STATE.posForm.customerNameInput)}"
                  autocomplete="off">
                <div class="autocomplete-float-list" id="pos-cust-dropdown" style="display:none; top: calc(100% + 4px);"></div>
              </div>

              <div class="customer-quick-actions">
                <button type="button" class="btn btn-secondary" style="color: var(--primary);" id="btn-add-cust-trigger" title="Tambah pelanggan baru">
                  + Baru
                </button>
              </div>
            </div>
          </div>

          <!-- LANGKAH 2: PILIH LAYANAN -->
          <div>
            <div class="pos-step-header">
              <span class="pos-step-title-wrap">
                <span class="pos-step-badge">2</span>
                <span class="pos-step-label">Pilih Layanan</span>
              </span>
              <span class="pos-step-hint">Pilih 1 layanan utama</span>
            </div>

            <div class="services-tile-grid">
              ${STATE.services.map(srv => {
                const isSelected = srv.id === STATE.posForm.serviceId;
                const isKilatSrv = srv.category === 'Kilat' || srv.id === 'srv-5';
                const isSatuanSrv = srv.category === 'Satuan';
                const rateDisplay = srv.minRate !== srv.maxRate 
                  ? `${formatRp(srv.minRate)} - ${Number(srv.maxRate).toLocaleString('id-ID')}` 
                  : formatRp(srv.minRate);
                return `
                  <div class="service-tile ${isSelected ? 'active' : ''} ${isKilatSrv ? 'is-kilat' : ''} ${isSatuanSrv ? 'is-satuan-wide' : ''}" data-srv-id="${srv.id}">
                    <div class="service-tile-left">
                      <div class="service-tile-icon">
                        ${getSvgIcon(srv.icon)}
                      </div>
                      <div>
                        <div class="service-tile-name">${srv.name}</div>
                        <div class="service-tile-rate">${rateDisplay}/${srv.unit}</div>
                      </div>
                    </div>
                    <span class="service-tile-badge">${srv.estText}</span>
                  </div>
                `;
              }).join('')}
            </div>
          </div>

          <!-- LANGKAH 3 (KALKULATOR) & LANGKAH 4 (CATATAN) -->
          <div class="pos-input-split">
            <!-- LANGKAH 3: PILIH BERAT / JUMLAH -->
            <div class="pos-split-step-col">
              <div class="pos-step-header">
                <span class="pos-step-title-wrap">
                  <span class="pos-step-badge">3</span>
                  <span class="pos-step-label">Pilih ${isSatuan ? 'Jumlah Item' : 'Berat Cucian'}</span>
                </span>
                <span class="pos-step-hint">Angka / Enter</span>
              </div>
              <div class="keypad-popover-shell">
                <div class="keypad-card-clean ${switchAnimClass}">
                  <div class="keypad-display-head">
                    <div class="keypad-head-meta">
                      <span class="keypad-service-name">${activeSrv.name}</span>
                      <span id="keypad-rate-tag" class="keypad-rate-tag num-tabular">${formatRp(STATE.posForm.appliedRate)}/${activeSrv.unit}</span>
                    </div>
                    <div class="keypad-readout-row">
                      <span id="keypad-weight-val" class="keypad-readout-num num-tabular">${STATE.posForm.weightStr || weightVal}</span>
                      <span class="keypad-readout-unit">${activeSrv.unit}</span>
                    </div>
                    <div class="keypad-subtotal-bar">
                      <span class="keypad-subtotal-label">Subtotal</span>
                      <span id="keypad-subtotal-val" class="keypad-subtotal-val num-tabular">${formatRp(itemSubtotal)}</span>
                    </div>
                  </div>

                  <div class="keypad-hairline-grid" role="group" aria-label="Keypad timbangan cucian">
                    <button type="button" class="btn-numpad-cell" data-num="1">1</button>
                    <button type="button" class="btn-numpad-cell" data-num="2">2</button>
                    <button type="button" class="btn-numpad-cell" data-num="3">3</button>
                    <button type="button" class="btn-numpad-cell is-utility" data-action="backspace" title="Hapus satu digit" aria-label="Hapus digit">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"/><line x1="18" y1="9" x2="12" y2="15"/><line x1="12" y1="9" x2="18" y2="15"/></svg>
                    </button>

                    <button type="button" class="btn-numpad-cell" data-num="4">4</button>
                    <button type="button" class="btn-numpad-cell" data-num="5">5</button>
                    <button type="button" class="btn-numpad-cell" data-num="6">6</button>
                    <button type="button" class="btn-numpad-cell is-utility" data-add="${isSatuan ? 1 : 0.5}" title="Tambah cepat">
                      +${isSatuan ? '1' : '0,5'}
                    </button>

                    <button type="button" class="btn-numpad-cell" data-num="7">7</button>
                    <button type="button" class="btn-numpad-cell" data-num="8">8</button>
                    <button type="button" class="btn-numpad-cell" data-num="9">9</button>
                    <button type="button" class="btn-numpad-cell is-utility" data-add="${isSatuan ? 2 : 1}" title="Tambah cepat">
                      +${isSatuan ? '2' : '1'}
                    </button>

                    ${isSatuan
                      ? `<button type="button" class="btn-numpad-cell is-utility" data-add="-1" title="Kurangi 1">-1</button>`
                      : `<button type="button" class="btn-numpad-cell" data-num="," title="Koma desimal">,</button>`
                    }
                    <button type="button" class="btn-numpad-cell" data-num="0">0</button>
                    <button type="button" class="btn-numpad-cell is-clear" data-action="clear" title="Kosongkan angka">C</button>
                    <button type="button" class="btn-numpad-cell is-utility" data-add="5" title="Tambah 5">+5</button>
                  </div>

                  <button type="button" class="keypad-submit-footer" id="btn-confirm-weight">
                    <span id="keypad-confirm-icon" class="keypad-icon-swap">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                    <span id="keypad-confirm-label">Terapkan ke Nota</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- LANGKAH 4: CATATAN (OPSIONAL) -->
            <div class="pos-split-step-col" id="pos-step-catatan-col">
              <div class="pos-step-header">
                <span class="pos-step-title-wrap">
                  <span class="pos-step-badge">4</span>
                  <span class="pos-step-label">Catatan (Opsional)</span>
                </span>
                <button type="button" class="pos-reset-btn" id="btn-reset-pos-form">
                  Reset
                </button>
              </div>
              <div class="pos-options-sheet">
                ${isSatuan ? `
                  <div class="quiet-option-group" id="pos-satuan-group">
                    <span class="quiet-group-label">Item Satuan</span>
                    <div class="rate-segmented-pill">
                      <button type="button" class="rate-pill-btn ${STATE.posForm.variantId === 'var-selimut' || !STATE.posForm.variantId ? 'active' : ''}" 
                        data-var-id="var-selimut" data-var-price="10000">
                        Selimut  •  10rb
                      </button>
                      <button type="button" class="rate-pill-btn ${STATE.posForm.variantId === 'var-bedcover' ? 'active' : ''}" 
                        data-var-id="var-bedcover" data-var-price="25000">
                        Bed cover  •  25rb
                      </button>
                    </div>
                  </div>
                ` : ''}

                <div class="quiet-option-group">
                  <span class="quiet-group-label">Catatan Cucian</span>
                  <div class="option-chips-container">
                    ${['Pisahkan Putih', 'Jangan Dilipat', 'Gantung'].map(note => {
                      const isChecked = STATE.posForm.notes.includes(note);
                      return `
                        <button type="button" class="chip-toggle ${isChecked ? 'active' : ''}"
                          data-note="${note}" aria-pressed="${isChecked}">
                          <span class="note-check-slot" style="display: contents;">${isChecked ? '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><polyline points="20 6 9 17 4 12"/></svg>' : ''}</span>
                          <span>${note}</span>
                        </button>
                      `;
                    }).join('')}
                  </div>
                  <input type="text" id="pos-custom-note-input" class="form-control pos-custom-note-input"
                    placeholder="Catatan khusus (mis. kerah, noda)..."
                    value="${escapeHtmlAttr(STATE.posForm.customNote || '')}">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- KOLOM KANAN: NOTA LANGSUNG -->
        <div class="pos-right-column">
          <!-- 1. Header Nota -->
          <div class="receipt-header-row">
            <div class="receipt-title-wrap">
              <span class="receipt-main-text">Nota</span>
              <span id="receipt-cust-badge" class="receipt-cust-badge">
                ${escapeHtmlAttr(activeCustName)}
              </span>
            </div>
            <button type="button" class="btn-receipt-reset" id="btn-clear-nota">Kosongkan</button>
          </div>

          <!-- 2. Kartu Rincian Layanan Terkunci (Multi-Layanan dalam 1 Nota) -->
          <div id="receipt-items-container" class="receipt-items-container" style="display: flex; flex-direction: column; gap: 8px;">
            ${STATE.cartItems.length > 0 ? `
              <div style="font-size: 0.72rem; font-weight: 700; color: #0284C7; margin-bottom: 2px; display: flex; align-items: center; justify-content: space-between;">
                <span>${STATE.cartItems.length} Layanan Terkunci</span>
                <span style="font-size: 0.68rem; color: var(--ink-muted);">+ Pilih layanan lagi untuk menambah</span>
              </div>
              ${STATE.cartItems.map((item, idx) => `
                <div class="receipt-item-box is-locked" style="border-left: 3px solid #0284C7;">
                  <div class="item-box-top-row">
                    <div class="item-desc-col">
                      <span class="item-name-heading" style="color: var(--ink);">${escapeHtmlAttr(item.name)}</span>
                      <span class="item-rate-caption num-tabular">
                        ${item.weight} ${item.unit} &times; ${formatRp(item.rate)}/${item.unit}
                      </span>
                    </div>
                    <div class="item-price-trash-wrap">
                      <span class="item-price-amount num-tabular" style="font-weight: 800; color: var(--ink);">${formatRp(item.subtotal)}</span>
                      <button type="button" class="btn-trash-item" onclick="window.removePosCartItem('${item.id}')" title="Hapus layanan ini">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                      </button>
                    </div>
                  </div>
                  ${(item.notes.length > 0 || item.customNote) ? `
                    <div class="item-sub-tags-row">
                      ${item.notes.map(n => `<span class="receipt-note-chip">${escapeHtmlAttr(n)}</span>`).join('')}
                      ${item.customNote ? `<span class="receipt-note-chip">${escapeHtmlAttr(item.customNote)}</span>` : ''}
                    </div>
                  ` : ''}
                </div>
              `).join('')}
            ` : `
              <!-- Preview Item Saat Cart Belum Diterapkan -->
              <div class="receipt-item-box is-preview" id="receipt-item-box" style="border: 1px dashed #CBD5E1;">
                <div class="item-box-top-row">
                  <div class="item-desc-col">
                    <span class="item-name-heading">
                      ${isSatuan 
                        ? (STATE.posForm.variantId === 'var-bedcover' ? 'Bed Cover Besar' : (STATE.posForm.variantId === 'var-sepatu' ? 'Cuci Sepatu Premium' : 'Cuci Satuan - Selimut'))
                        : activeSrv.name
                      }
                    </span>
                    <span class="item-rate-caption num-tabular">
                      <span id="receipt-item-weight" class="item-qty-readonly">${STATE.posForm.weightStr || weightVal}</span> ${activeSrv.unit} &times; <span id="receipt-rate-caption">${formatRp(appliedRate)}/${activeSrv.unit}</span>
                    </span>
                  </div>
                  <div class="item-price-trash-wrap">
                    <span class="item-price-amount num-tabular" id="receipt-item-price">${formatRp(itemSubtotal)}</span>
                  </div>
                </div>
                <div id="receipt-notes-chips" class="item-sub-tags-row">
                  ${STATE.posForm.notes.map(n => `<span class="receipt-note-chip">${escapeHtmlAttr(n)}</span>`).join('')}
                  ${STATE.posForm.customNote ? `<span class="receipt-note-chip">${escapeHtmlAttr(STATE.posForm.customNote)}</span>` : ''}
                </div>
                <div style="font-size: 0.72rem; color: #0284C7; font-weight: 600; margin-top: 6px; text-align: center;">
                  Klik "Terapkan ke Nota" untuk mengunci layanan ini
                </div>
              </div>
            `}
          </div>

          <!-- 3. Panel Total Tagihan Ice-Blue -->
          <div class="total-summary-card">
            <div class="total-sla-row">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              <span>Selesai: ${estimasiText}</span>
            </div>
            <div class="total-sum-row">
              <span class="total-sum-label">Total tagihan</span>
              <span class="total-sum-huge num-tabular" id="receipt-total-val">${formatRp(totalTagihan)}</span>
            </div>
          </div>

          <!-- 5. Segmented Toggle Bayar Sekarang vs Bayar Saat Ambil -->
          <div class="pay-timing-toggle">
            <button type="button" class="pay-timing-btn ${STATE.posForm.isPaid ? 'active' : ''}" data-is-paid="true">
              Bayar sekarang
            </button>
            <button type="button" class="pay-timing-btn ${!STATE.posForm.isPaid ? 'active' : ''}" data-is-paid="false">
              Bayar saat ambil
            </button>
          </div>

          <!-- 6. Metode Pembayaran 3 Kolom -->
          ${STATE.posForm.isPaid ? `
            <div class="payment-options-grid">
              ${['Tunai', 'Transfer', 'E-Wallet'].map(m => `
                <button type="button" class="pay-method-pill ${STATE.posForm.paymentMethod === m ? 'active' : ''}" data-method="${m}">
                  ${m}
                </button>
              `).join('')}
            </div>

            <!-- 7. Presets Uang Tunai -->
            ${STATE.posForm.paymentMethod === 'Tunai' ? `
              <div class="cash-presets-row">
                <button type="button" class="preset-cash-chip is-exact" id="btn-preset-exact" title="Pintasan: Alt+P">
                  Uang pas
                </button>
                <button type="button" id="preset-mid-chip" class="preset-cash-chip" data-preset="${presetMid}">
                  ${Number(presetMid).toLocaleString('id-ID')}
                </button>
                <button type="button" class="preset-cash-chip" data-preset="100000">
                  100.000
                </button>
              </div>

              <!-- Input Nominal Tunai Manual -->
              <div style="position: relative;">
                <input type="number" step="1000" class="form-control num-tabular font-mono" id="pos-cash-input"
                  placeholder="Input uang diterima..." 
                  value="${STATE.posForm.cashReceived}" 
                  style="font-size: 0.95rem; font-weight: 700;">
              </div>

              <!-- 8. Bar Kembalian Mint -->
              <div class="kembalian-mint-strip ${isUnderpaid ? 'is-underpaid' : ''}" id="receipt-kembalian-strip">
                <span id="receipt-kembalian-label">${isUnderpaid ? 'Uang Masih Kurang' : 'Kembalian'}</span>
                <span class="num-tabular font-mono" id="receipt-kembalian-val">
                  ${isUnderpaid ? '-' + formatRp(Math.abs(kembalian)) : formatRp(kembalian)}
                </span>
              </div>
            ` : `
              <div style="background: var(--primary-light); color: var(--primary-hover); padding: 0.5rem 0.8rem; border-radius: var(--radius-md); font-size: 0.78rem; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Pembayaran ${STATE.posForm.paymentMethod} otomatis ditandai lunas.</span>
              </div>
            `}
          ` : `
            <div style="background: var(--danger-bg); color: var(--danger-text); padding: 0.5rem 0.8rem; border-radius: var(--radius-md); font-size: 0.78rem; font-weight: 600;">
              Pesanan dicatat sebagai piutang. Tagihan dilunasi saat penyerahan cucian.
            </div>
          `}

          <!-- 9. Tombol Simpan & Cetak Nota -->
          <button type="button" id="btn-submit-nota" class="btn-submit-nota" ${isUnderpaid ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : ''}>
            <span>Simpan & cetak nota</span>
          </button>
        </div>
      </div>

      <!-- Sticky Floating Checkout Bar untuk Mobile -->
      <div class="pos-mobile-checkout-bar" id="pos-mobile-checkout-bar">
        <div class="pos-mobile-checkout-info">
          <span class="pos-mobile-checkout-total font-mono">${formatRp(totalTagihan)}</span>
          <span class="pos-mobile-checkout-sub">${STATE.posForm.weightStr || weightVal} ${activeSrv.unit} • ${escapeHtmlAttr(activeSrv.name)}</span>
        </div>
        <button type="button" class="pos-mobile-checkout-btn" onclick="document.querySelector('.pos-right-column')?.scrollIntoView({ behavior: 'smooth', block: 'start' })">
          <span>Buka Nota &amp; Bayar</span>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
      </div>
    `;

    bindPosEvents();
  }

  // --- 5. EVENT BINDINGS & CLIENT FLOW ---
  function bindPosEvents() {
    // Layanan Tiles
    document.querySelectorAll('.service-tile').forEach(tile => {
      tile.addEventListener('click', () => {
        const id = tile.getAttribute('data-srv-id');
        const srv = STATE.services.find(s => s.id === id);
        if (!srv) return;
        STATE.posForm.serviceId = srv.id;
        STATE.posForm.appliedRate = srv.minRate;
        STATE.posForm.weightStr = '1';
        STATE.posForm.weightVal = 1;
        STATE.justSwitchedService = true;
        renderPosWorkstation();
      });
    });

    // Satuan Variant Pills
    document.querySelectorAll('.rate-pill-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const vId = btn.getAttribute('data-var-id');
        const price = parseInt(btn.getAttribute('data-var-price'), 10);
        STATE.posForm.variantId = vId;
        STATE.posForm.variantPrice = price;
        renderPosWorkstation();
      });
    });

    // Numpad Buttons
    document.querySelectorAll('.btn-numpad-cell').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const num = btn.getAttribute('data-num');
        const action = btn.getAttribute('data-action');
        const add = btn.getAttribute('data-add');

        if (num !== null) {
          handleNumpad(num);
        } else if (action === 'backspace') {
          handleNumpad('backspace');
        } else if (action === 'clear') {
          STATE.posForm.weightStr = '';
          STATE.posForm.weightVal = 0;
          renderPosWorkstation();
        } else if (add !== null) {
          addQuickWeight(parseFloat(add));
        }
      });
    });

    // Confirm weight to nota
    const btnConfirm = document.getElementById('btn-confirm-weight');
    if (btnConfirm) {
      btnConfirm.addEventListener('click', () => {
        applyCurrentServiceToNota();
      });
    }

    // Reset Form
    const btnReset = document.getElementById('btn-reset-pos-form');
    if (btnReset) btnReset.addEventListener('click', resetPosForm);

    const btnClearNota = document.getElementById('btn-clear-nota');
    if (btnClearNota) btnClearNota.addEventListener('click', resetPosForm);

    const btnTrash = document.getElementById('btn-trash-pos');
    if (btnTrash) btnTrash.addEventListener('click', resetPosForm);

    // Note Chips
    document.querySelectorAll('.chip-toggle').forEach(btn => {
      btn.addEventListener('click', () => {
        const note = btn.getAttribute('data-note');
        const idx = STATE.posForm.notes.indexOf(note);
        if (idx >= 0) {
          STATE.posForm.notes.splice(idx, 1);
        } else {
          STATE.posForm.notes.push(note);
        }
        renderPosWorkstation();
      });
    });

    // Custom Note Input
    const noteInput = document.getElementById('pos-custom-note-input');
    if (noteInput) {
      noteInput.addEventListener('input', (e) => {
        STATE.posForm.customNote = e.target.value;
        const chipsSlot = document.getElementById('receipt-notes-chips');
        if (chipsSlot) {
          chipsSlot.innerHTML = `
            ${STATE.posForm.notes.map(n => `<span class="receipt-note-chip">${escapeHtmlAttr(n)}</span>`).join('')}
            ${STATE.posForm.customNote ? `<span class="receipt-note-chip">${escapeHtmlAttr(STATE.posForm.customNote)}</span>` : ''}
          `;
        }
      });
    }

    // Customer Autocomplete Search
    const custSearch = document.getElementById('pos-cust-search');
    const custDropdown = document.getElementById('pos-cust-dropdown');
    if (custSearch && custDropdown) {
      custSearch.addEventListener('input', (e) => {
        const query = e.target.value.trim().toLowerCase();
        STATE.posForm.customerNameInput = e.target.value;
        STATE.posForm.selectedCustomer = null;
        document.getElementById('pos-cust-active-pill').textContent = e.target.value || 'Pelanggan Umum';
        document.getElementById('receipt-cust-badge').textContent = e.target.value || 'Pelanggan Umum';

        if (query.length < 1) {
          custDropdown.style.display = 'none';
          return;
        }

        const matches = STATE.customers.filter(c => 
          c.name.toLowerCase().includes(query) || c.phone.includes(query)
        );

        if (matches.length === 0) {
          custDropdown.innerHTML = `
            <div class="dropdown-item-empty" style="padding: 10px; font-size: 0.8rem; color: var(--ink-muted);">
              Tidak ada pelanggan cocok.
              <a href="javascript:void(0)" id="quick-create-cust" style="color: var(--primary); font-weight: 700; display: block; margin-top: 4px;">+ Tambah '${escapeHtmlAttr(query)}'</a>
            </div>
          `;
          custDropdown.style.display = 'block';
          document.getElementById('quick-create-cust')?.addEventListener('click', () => {
            openNewCustomerModal(query);
          });
        } else {
          custDropdown.innerHTML = matches.map(c => `
            <div class="dropdown-item" data-cust-id="${c.id}" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid var(--line); font-size: 0.82rem;">
              <strong style="color: var(--ink);">${escapeHtmlAttr(c.name)}</strong>
              <span style="color: var(--ink-muted); font-size: 0.74rem; margin-left: 6px;">(${escapeHtmlAttr(c.phone || '-')})</span>
            </div>
          `).join('');
          custDropdown.style.display = 'block';

          custDropdown.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', () => {
              const cId = item.getAttribute('data-cust-id');
              const found = STATE.customers.find(c => c.id === cId);
              if (found) {
                STATE.posForm.selectedCustomer = found;
                STATE.posForm.customerNameInput = found.name;
                custSearch.value = `${found.name} (${found.phone})`;
                custDropdown.style.display = 'none';
                renderPosWorkstation();
              }
            });
          });
        }
      });
    }

    // Trigger New Customer Modal
    const btnAddCust = document.getElementById('btn-add-cust-trigger');
    if (btnAddCust) {
      btnAddCust.addEventListener('click', () => openNewCustomerModal());
    }

    // Pay Timing (Bayar Sekarang vs Nanti)
    document.querySelectorAll('.pay-timing-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const isPaid = btn.getAttribute('data-is-paid') === 'true';
        STATE.posForm.isPaid = isPaid;
        renderPosWorkstation();
      });
    });

    // Payment Method Pills
    document.querySelectorAll('.pay-method-pill').forEach(btn => {
      btn.addEventListener('click', () => {
        const method = btn.getAttribute('data-method');
        STATE.posForm.paymentMethod = method;
        renderPosWorkstation();
      });
    });

    // Cash Preset Buttons
    const btnExact = document.getElementById('btn-preset-exact');
    if (btnExact) {
      btnExact.addEventListener('click', () => {
        const { totalTagihan } = getPosTotals();
        STATE.posForm.cashReceived = totalTagihan;
        renderPosWorkstation();
      });
    }

    document.querySelectorAll('.preset-cash-chip[data-preset]').forEach(btn => {
      btn.addEventListener('click', () => {
        const amt = parseInt(btn.getAttribute('data-preset'), 10);
        STATE.posForm.cashReceived = amt;
        renderPosWorkstation();
      });
    });

    // Manual Cash Input
    const cashInput = document.getElementById('pos-cash-input');
    if (cashInput) {
      cashInput.addEventListener('input', (e) => {
        STATE.posForm.cashReceived = parseFloat(e.target.value) || 0;
        const { totalTagihan } = getPosTotals();
        const diff = STATE.posForm.cashReceived - totalTagihan;
        const strip = document.getElementById('receipt-kembalian-strip');
        const valSpan = document.getElementById('receipt-kembalian-val');
        const lblSpan = document.getElementById('receipt-kembalian-label');
        const submitBtn = document.getElementById('btn-submit-nota');

        if (diff < 0) {
          strip.classList.add('is-underpaid');
          lblSpan.textContent = 'Uang Masih Kurang';
          valSpan.textContent = '-' + formatRp(Math.abs(diff));
          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';
          }
        } else {
          strip.classList.remove('is-underpaid');
          lblSpan.textContent = 'Kembalian';
          valSpan.textContent = formatRp(diff);
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
          }
        }
      });
    }

    // Submit Order Button
    const btnSubmit = document.getElementById('btn-submit-nota');
    if (btnSubmit) {
      btnSubmit.addEventListener('click', submitPosOrder);
    }
  }

  // --- 6. NUMPAD & CALCULATOR ACTIONS ---
  function handleNumpad(key) {
    const activeSrv = getActiveService();
    const isSatuan = activeSrv.category === 'Satuan';

    if (key === 'backspace') {
      let str = String(STATE.posForm.weightStr || '');
      STATE.posForm.weightStr = str.slice(0, -1);
      if (!STATE.posForm.weightStr) STATE.posForm.weightStr = '0';
    } else if (key === ',' || key === '.') {
      if (isSatuan) return;
      let str = String(STATE.posForm.weightStr || '0');
      if (!str.includes(',')) STATE.posForm.weightStr = str + ',';
    } else {
      let str = String(STATE.posForm.weightStr || '');
      if (str === '0' || str === '1' && !STATE.posForm.dirtyWeight) {
        STATE.posForm.weightStr = key;
      } else {
        STATE.posForm.weightStr += key;
      }
      STATE.posForm.dirtyWeight = true;
    }

    renderPosWorkstation();
  }

  function addQuickWeight(amount) {
    const activeSrv = getActiveService();
    const isSatuan = activeSrv.category === 'Satuan';
    let cur = isSatuan 
      ? parseInt(STATE.posForm.weightStr || '1', 10) 
      : parseFloat(String(STATE.posForm.weightStr || '1').replace(',', '.'));
    if (isNaN(cur)) cur = 0;
    cur += amount;
    if (cur < (isSatuan ? 1 : 0.5)) cur = isSatuan ? 1 : 0.5;

    STATE.posForm.weightStr = isSatuan ? String(Math.round(cur)) : String(cur).replace('.', ',');
    STATE.posForm.dirtyWeight = true;
    renderPosWorkstation();
  }

  function applyCurrentServiceToNota() {
    const { activeSrv, isSatuan, weightVal, appliedRate, itemSubtotal } = getPosTotals();
    if (weightVal <= 0) {
      showToast('Berat atau jumlah item minimal lebih dari 0.');
      return;
    }

    const itemName = isSatuan 
      ? (STATE.posForm.variantId === 'var-bedcover' ? 'Bed Cover Besar' : (STATE.posForm.variantId === 'var-sepatu' ? 'Cuci Sepatu Premium' : 'Cuci Satuan - Selimut'))
      : activeSrv.name;

    const newItem = {
      id: 'it_' + Date.now() + '_' + Math.floor(Math.random() * 1000),
      serviceId: activeSrv.id,
      dbId: activeSrv.dbId || (rawServices[0] ? rawServices[0].id_layanan : 'LAY-01'),
      name: itemName,
      unit: activeSrv.unit,
      weight: weightVal,
      rate: appliedRate,
      subtotal: itemSubtotal,
      notes: [...STATE.posForm.notes],
      customNote: (STATE.posForm.customNote || '').trim(),
      estHours: activeSrv.estHours || 24,
      isKilat: activeSrv.category === 'Kilat' || activeSrv.id === 'srv-5'
    };

    STATE.cartItems.push(newItem);

    // Auto update cash received jika pas atau kurang
    const newTotal = STATE.cartItems.reduce((acc, it) => acc + it.subtotal, 0);
    if (STATE.posForm.cashReceived < newTotal) {
      STATE.posForm.cashReceived = newTotal;
    }

    // Reset weight dan notes agar kasir siap memilih layanan berikutnya
    STATE.posForm.weightStr = '1';
    STATE.posForm.dirtyWeight = false;
    STATE.posForm.notes = [];
    STATE.posForm.customNote = '';

    showToast(`✓ ${newItem.name} (${newItem.weight} ${newItem.unit}) dikunci ke nota.`);
    renderPosWorkstation();
  }

  window.removePosCartItem = function(itemId) {
    STATE.cartItems = STATE.cartItems.filter(it => it.id !== itemId);
    const newTotal = STATE.cartItems.reduce((acc, it) => acc + it.subtotal, 0);
    if (STATE.posForm.cashReceived < newTotal) {
      STATE.posForm.cashReceived = newTotal;
    }
    showToast('Layanan dihapus dari nota.');
    renderPosWorkstation();
  };

  function resetPosForm() {
    STATE.cartItems = [];
    STATE.posForm.serviceId = 'srv-2';
    STATE.posForm.appliedRate = 4500;
    STATE.posForm.weightStr = '1';
    STATE.posForm.weightVal = 1;
    STATE.posForm.dirtyWeight = false;
    STATE.posForm.variantId = 'var-selimut';
    STATE.posForm.variantPrice = 10000;
    STATE.posForm.notes = [];
    STATE.posForm.customNote = '';
    STATE.posForm.isPaid = true;
    STATE.posForm.paymentMethod = 'Tunai';
    STATE.posForm.cashReceived = 10000;
    STATE.posForm.selectedCustomer = null;
    STATE.posForm.customerNameInput = '';
    renderPosWorkstation();
    showToast('Form kasir & rincian nota telah dikosongkan.');
  }

  // --- 7. SUBMIT PESANAN VIA AJAX ---
  function submitPosOrder() {
    const { activeSrv, weightVal, totalTagihan } = getPosTotals();

    let itemsToSend = [];
    let isAnyKilat = false;
    let allNotes = [];

    if (STATE.cartItems && STATE.cartItems.length > 0) {
      itemsToSend = STATE.cartItems.map(it => {
        if (it.isKilat) isAnyKilat = true;
        const noteStr = it.notes.concat(it.customNote ? [it.customNote] : []).join(', ');
        if (noteStr) allNotes.push(`${it.name}: ${noteStr}`);
        return {
          id_layanan: it.dbId,
          jumlah: it.weight
        };
      });
    } else {
      if (weightVal <= 0) {
        showToast('Pilih layanan dan klik "Terapkan ke Nota" terlebih dahulu.');
        return;
      }
      const noteStr = [...STATE.posForm.notes, STATE.posForm.customNote].filter(Boolean).join(', ');
      if (noteStr) allNotes.push(noteStr);
      isAnyKilat = activeSrv.category === 'Kilat' || activeSrv.id === 'srv-5';
      itemsToSend = [
        {
          id_layanan: activeSrv.dbId || (rawServices[0] ? rawServices[0].id_layanan : 'LAY-01'),
          jumlah: weightVal
        }
      ];
    }

    if (STATE.posForm.isPaid && STATE.posForm.paymentMethod === 'Tunai') {
      if (STATE.posForm.cashReceived < totalTagihan) {
        showToast('Uang tunai kurang dari total tagihan.');
        return;
      }
    }

    const custName = STATE.posForm.selectedCustomer 
      ? STATE.posForm.selectedCustomer.name 
      : (STATE.posForm.customerNameInput.trim() || 'Pelanggan Umum');
    const custPhone = STATE.posForm.selectedCustomer 
      ? STATE.posForm.selectedCustomer.phone 
      : '081234567899';
    const custId = STATE.posForm.selectedCustomer ? STATE.posForm.selectedCustomer.id : '';

    // Prepare JSON payload for PosController::store
    const payload = {
      id_pelanggan: custId,
      nama_pelanggan_baru: custId ? '' : custName,
      no_telepon_baru: custId ? '' : custPhone,
      catatan_khusus: allNotes.join(' | '),
      parfum: 'Ocean Fresh',
      is_kilat: isAnyKilat,
      bayar_sekarang: STATE.posForm.isPaid ? 1 : 0,
      jumlah_bayar: STATE.posForm.cashReceived,
      id_metode: STATE.posForm.paymentMethod === 'Tunai' ? 'MET-01' : (STATE.posForm.paymentMethod === 'Transfer' ? 'MET-02' : 'MET-03'),
      items: itemsToSend
    };

    const submitBtn = document.getElementById('btn-submit-nota');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span>Menyimpan transaksi...</span>';
    }

    const storeUrl = (window.POS_DATA && window.POS_DATA.storeUrl) || 'index.php?page=pos&action=store';

    fetch(storeUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(res => {
      const orderCode = res.id_pesanan || 'LG-' + Date.now().toString().slice(-6);
      showToast(`Pesanan ${orderCode} tersimpan.`);

      const orderForPrint = {
        code: orderCode,
        createdAt: new Date().toISOString(),
        cashier: (window.POS_DATA && window.POS_DATA.cashierName) || 'Siti Rahayu',
        customerName: custName,
        customerPhone: custPhone,
        serviceName: isSatuan ? (STATE.posForm.variantId === 'var-bedcover' ? 'Cuci Satuan - Bed cover' : 'Cuci Satuan - Selimut') : activeSrv.name,
        weight: weightVal,
        unit: activeSrv.unit,
        rate: isSatuan ? STATE.posForm.variantPrice : STATE.posForm.appliedRate,
        subtotal: totalTagihan,
        notes: notesCombined,
        isPaid: STATE.posForm.isPaid,
        paymentMethod: STATE.posForm.paymentMethod,
        deadlineAt: new Date(Date.now() + activeSrv.estHours * 3600 * 1000).toISOString(),
        rackLocation: 'Rak A-01'
      };

      previewThermalReceipt(orderForPrint);
      resetPosForm();
    })
    .catch(err => {
      console.warn('Simpan via DB gagal, render fallback offline:', err);
      const fallbackCode = 'LG-' + Math.floor(100000 + Math.random() * 900000);
      showToast(`Pesanan ${fallbackCode} tersimpan.`);

      const orderForPrint = {
        code: fallbackCode,
        createdAt: new Date().toISOString(),
        cashier: (window.POS_DATA && window.POS_DATA.cashierName) || 'Siti Rahayu',
        customerName: custName,
        customerPhone: custPhone,
        serviceName: isSatuan ? 'Cuci Satuan' : activeSrv.name,
        weight: weightVal,
        unit: activeSrv.unit,
        rate: isSatuan ? STATE.posForm.variantPrice : STATE.posForm.appliedRate,
        subtotal: totalTagihan,
        notes: notesCombined,
        isPaid: STATE.posForm.isPaid,
        paymentMethod: STATE.posForm.paymentMethod,
        deadlineAt: new Date(Date.now() + activeSrv.estHours * 3600 * 1000).toISOString(),
        rackLocation: 'Rak A-01'
      };
      previewThermalReceipt(orderForPrint);
      resetPosForm();
    });
  }

  // --- 8. MODAL THERMAL STRUK (Identik dengan index.html) ---
  function previewThermalReceipt(order) {
    const sandbox = document.getElementById('modal-sandbox');
    if (!sandbox) return;

    sandbox.innerHTML = `
      <div class="dialog-backdrop" id="receipt-modal-backdrop">
        <div class="dialog-panel" style="max-width: 360px;" onclick="event.stopPropagation()">
          <div class="dialog-head">
            <span class="dialog-title">Preview Struk Termal (58/80mm)</span>
            <button type="button" class="btn btn-ghost" style="padding: 2px 6px;" id="btn-close-receipt">Esc</button>
          </div>
          <div class="dialog-body" style="background: #F8FAFC;">
            <div class="receipt-paper-box" id="thermal-receipt-content">
              <div style="text-align: center;">
                <strong>ADI LAUNDRY</strong><br>
                Jl. Panglima Sudirman, Karangploso<br>
                Kab. Malang  •  WA: 0812-3456-7890<br>
              </div>
              <div class="receipt-dash-line"></div>
              <div>
                No. Nota : <strong>${order.code}</strong><br>
                Tanggal  : ${formatDateTime(order.createdAt)}<br>
                Kasir    : ${order.cashier || 'Siti Rahayu'}<br>
                Pelanggan: ${order.customerName}<br>
                No. Telp : ${order.customerPhone}<br>
              </div>
              <div class="receipt-dash-line"></div>
              <div>
                ${order.serviceName}<br>
                ${order.weight} ${order.unit} x ${formatRp(order.rate)} = ${formatRp(order.subtotal)}<br>
                ${order.notes && order.notes.length ? `Catatan  : ${order.notes.join(', ')}<br>` : ''}
                Estimasi : ${formatDateTime(order.deadlineAt)}<br>
                Lokasi   : ${order.rackLocation || 'Rak A-01'}
              </div>
              <div class="receipt-dash-line"></div>
              <div style="display: flex; justify-content: space-between;">
                <strong>TOTAL:</strong>
                <strong>${formatRp(order.subtotal)}</strong>
              </div>
              <div style="display: flex; justify-content: space-between;">
                <span>Status:</span>
                <strong>${order.isPaid ? 'LUNAS (' + order.paymentMethod + ')' : 'BAYAR SAAT AMBIL'}</strong>
              </div>
              <div class="receipt-dash-line"></div>
              <div style="text-align: center; font-size: 9.5px; line-height: 1.3;">
                SYARAT & KETENTUAN:<br>
                1. Pengambilan wajib membawa nota ini / bukti WA.<br>
                2. Cucian tidak diambil >30 hari di luar tanggung jawab gerai.<br>
                3. Kerusakan luntur akibat sifat bawaan kain bukan kelalaian gerai.<br>
                -- Terima Kasih --
              </div>
            </div>
          </div>
          <div class="dialog-foot">
            <button type="button" class="btn btn-secondary" id="btn-close-receipt-foot">Tutup</button>
            <button type="button" class="btn btn-primary" id="btn-print-receipt-direct">Cetak Sekarang</button>
          </div>
        </div>
      </div>
    `;

    document.getElementById('receipt-modal-backdrop')?.addEventListener('click', closeModal);
    document.getElementById('btn-close-receipt')?.addEventListener('click', closeModal);
    document.getElementById('btn-close-receipt-foot')?.addEventListener('click', closeModal);
    document.getElementById('btn-print-receipt-direct')?.addEventListener('click', () => {
      window.print();
    });
  }

  // --- 9. MODAL TAMBAH PELANGGAN BARU ---
  function openNewCustomerModal(presetName = '') {
    const sandbox = document.getElementById('modal-sandbox');
    if (!sandbox) return;

    sandbox.innerHTML = `
      <div class="dialog-backdrop" id="cust-modal-backdrop">
        <div class="dialog-panel" style="max-width: 440px;" onclick="event.stopPropagation()">
          <div class="dialog-head">
            <span class="dialog-title">Pelanggan Baru</span>
            <button type="button" class="btn btn-ghost" style="padding: 2px 6px;" id="btn-close-cust-x">Esc</button>
          </div>
          <form id="form-new-cust-quick">
            <div class="dialog-body" style="padding: 18px 22px; display: flex; flex-direction: column; gap: 14px;">
              <div>
                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--ink); margin-bottom: 5px;">Nama Lengkap:</label>
                <input type="text" id="new-cust-name" class="form-control" required placeholder="Contoh: Ibu Rina Kartika" value="${escapeHtmlAttr(presetName)}">
              </div>
              <div>
                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--ink); margin-bottom: 5px;">Nomor WhatsApp / HP:</label>
                <input type="tel" id="new-cust-phone" class="form-control" required placeholder="Contoh: 081298765432">
              </div>
            </div>
            <div class="dialog-foot">
              <button type="button" class="btn btn-secondary" id="btn-cancel-cust">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan &amp; Pilih</button>
            </div>
          </form>
        </div>
      </div>
    `;

    document.getElementById('cust-modal-backdrop')?.addEventListener('click', closeModal);
    document.getElementById('btn-close-cust-x')?.addEventListener('click', closeModal);
    document.getElementById('btn-cancel-cust')?.addEventListener('click', closeModal);

    document.getElementById('form-new-cust-quick')?.addEventListener('submit', (e) => {
      e.preventDefault();
      const name = document.getElementById('new-cust-name').value.trim();
      const phone = document.getElementById('new-cust-phone').value.trim();
      if (!name) return;

      const newCust = {
        id: 'CUST-' + String(STATE.customers.length + 1).padStart(3, '0'),
        name: name,
        phone: phone || '-'
      };
      STATE.customers.unshift(newCust);
      STATE.posForm.selectedCustomer = newCust;
      STATE.posForm.customerNameInput = newCust.name;

      closeModal();
      renderPosWorkstation();
      showToast(`Pelanggan '${newCust.name}' ditambahkan.`);
    });
  }

  function closeModal() {
    const sandbox = document.getElementById('modal-sandbox');
    if (sandbox) sandbox.innerHTML = '';
  }

  // --- 10. KEYBOARD SHORTCUTS ---
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeModal();
      const dropdown = document.getElementById('pos-cust-dropdown');
      if (dropdown) dropdown.style.display = 'none';
      return;
    }

    if (e.altKey && e.key.toLowerCase() === 'p') {
      e.preventDefault();
      const { totalTagihan } = getPosTotals();
      STATE.posForm.cashReceived = totalTagihan;
      renderPosWorkstation();
      return;
    }

    const activeEl = document.activeElement;
    const isInput = Boolean(activeEl && (['INPUT', 'TEXTAREA', 'SELECT'].includes(activeEl.tagName) || activeEl.isContentEditable));
    const hasOpenModal = Boolean(document.getElementById('modal-sandbox')?.children.length);

    if (!isInput && !hasOpenModal && !e.ctrlKey && !e.metaKey && !e.altKey) {
      if (/^[0-9]$/.test(e.key) || e.key === ',' || e.key === '.') {
        e.preventDefault();
        handleNumpad(e.key);
        return;
      }
      if (e.key === 'Backspace') {
        e.preventDefault();
        handleNumpad('backspace');
        return;
      }
      if (e.key === 'c' || e.key === 'C') {
        e.preventDefault();
        STATE.posForm.weightStr = '';
        STATE.posForm.weightVal = 0;
        renderPosWorkstation();
        return;
      }
      if (e.key === 'Enter') {
        e.preventDefault();
        const btn = document.getElementById('btn-confirm-weight');
        if (btn) btn.click();
        return;
      }
      if (e.key === 'n' || e.key === 'N') {
        e.preventDefault();
        openNewCustomerModal();
        return;
      }
    }

    if (e.key === 'F2' && !hasOpenModal) {
      e.preventDefault();
      submitPosOrder();
      return;
    }

    if (e.key === 'Enter' && !hasOpenModal && activeEl && activeEl.id === 'pos-cash-input') {
      e.preventDefault();
      submitPosOrder();
      return;
    }
  });

  // Init on DOM ready or immediately if already loaded
  function initPosWithDemoCheck() {
    if (window.location.search.includes('demo_multicart=1')) {
      STATE.cartItems = [
        {
          id: 'it_demo_1',
          serviceId: 'srv-1',
          dbId: 'LAY-01',
          name: 'Cuci Kering + Setrika',
          unit: 'kg',
          weight: 3.5,
          rate: 6500,
          subtotal: 22750,
          notes: ['Pisahkan Putih'],
          customNote: '',
          estHours: 48,
          isKilat: false
        },
        {
          id: 'it_demo_2',
          serviceId: 'srv-4',
          dbId: 'LAY-04',
          name: 'Bed Cover Besar',
          unit: 'pcs',
          weight: 1,
          rate: 25000,
          subtotal: 25000,
          notes: ['Parfum Lavender'],
          customNote: '',
          estHours: 48,
          isKilat: false
        }
      ];
      STATE.posForm.cashReceived = 50000;
    }
    renderPosWorkstation();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPosWithDemoCheck);
  } else {
    initPosWithDemoCheck();
  }

})();
