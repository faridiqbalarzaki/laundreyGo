/**
 * LaundryGo Aqua - Daftar Pesanan Native Engine (Kasir Role)
 * 100% Identik dengan index.html & Video Prototipe
 */

(function() {
  'use strict';

  // --- 1. SVGs & ICON HELPERS ---
  function getCleanSvg(name, size = 14) {
    const s = size;
    switch (name) {
      case 'check':
        return `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`;
      case 'x':
        return `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`;
      case 'tag':
        return `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>`;
      case 'search':
        return `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>`;
      case 'download':
        return `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>`;
      case 'printer':
        return `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>`;
      default:
        return `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/></svg>`;
    }
  }

  function getFilterPillIconSvg(key) {
    switch (key) {
      case 'status':
        return `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>`;
      case 'tags':
        return `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>`;
      case 'date':
        return `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`;
      case 'payment':
        return `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>`;
      default:
        return '';
    }
  }

  function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  function formatRp(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
  }

  function formatDateParts(dateStr) {
    if (!dateStr) return { date: '-', time: '-' };
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return { date: String(dateStr).split(' ')[0] || '-', time: String(dateStr).split(' ')[1] || '-' };
    const day = String(d.getDate()).padStart(2, '0');
    const mon = String(d.getMonth() + 1).padStart(2, '0');
    const yr = d.getFullYear();
    const hr = String(d.getHours()).padStart(2, '0');
    const mn = String(d.getMinutes()).padStart(2, '0');
    return {
      date: `${day}/${mon}/${yr}`,
      time: `${hr}:${mn}`
    };
  }

  // --- 2. DATA CONSTANTS & STATE ---
  const ORDER_STATUS_OPTIONS = [
    { id: 'antrean', label: 'Antrean', color: '#0EA5E9' },
    { id: 'dicuci', label: 'Dicuci', color: '#0284C7' },
    { id: 'disetrika', label: 'Disetrika', color: '#F59E0B' },
    { id: 'siap diambil', label: 'Siap Diambil', color: '#10B981' },
    { id: 'selesai', label: 'Selesai', color: '#16A34A' }
  ];

  const rawOrders = (window.ORDERS_DATA && window.ORDERS_DATA.orders) || [];
  const rawServices = (window.ORDERS_DATA && window.ORDERS_DATA.services) || [];

  const STATE = {
    orders: rawOrders.map(o => ({
      code: o.id_pesanan || o.code,
      createdAt: o.tanggal_masuk || o.waktu_masuk || o.createdAt,
      customerName: o.nama_pelanggan || o.customerName || 'Pelanggan Umum',
      customerPhone: o.no_telepon || o.customerPhone || '-',
      serviceName: o.daftar_layanan || o.nama_layanan || o.serviceName || 'Cuci Kering + Setrika',
      status: (o.status_cucian || o.status || 'Antrean').toLowerCase(),
      isPaid: String(o.status_pembayaran || o.isPaid).toLowerCase() === 'lunas' || o.isPaid === true,
      paymentMethod: o.metode_pembayaran || o.paymentMethod || 'Tunai',
      subtotal: parseFloat(o.total_harga || o.subtotal || 0),
      isKilat: Boolean(o.is_kilat) || (o.daftar_layanan || '').toLowerCase().includes('kilat') || (o.serviceName || '').toLowerCase().includes('kilat'),
      rackLocation: o.lokasi_rak || o.rackLocation || '',
      weight: parseFloat(o.total_berat || o.weight || 1),
      unit: o.satuan || o.unit || 'kg',
      notes: o.catatan ? [o.catatan] : (o.notes || [])
    })),
    services: rawServices,
    orderFilters: {
      statuses: [],
      statusMode: 'include',
      tags: [],
      tagMode: 'include',
      datePreset: 'semua',
      dateFrom: '',
      dateTo: '',
      payStatus: 'semua',
      payMethod: 'semua',
      amountOp: 'lt',
      amountValue: ''
    },
    activeOrderFilterPopover: null,
    orderSearchQuery: '',
    ordersPage: 1,
    ordersPageSize: 10,
    selectedOrders: []
  };

  function getOrderTagOptions() {
    return [
      { id: 'kering-setrika', label: 'Cuci Kering + Setrika', color: '#0EA5E9' },
      { id: 'kering-saja', label: 'Cuci Kering Saja', color: '#0284C7' },
      { id: 'setrika-saja', label: 'Setrika Saja', color: '#06B6D4' },
      { id: 'kilat', label: 'Kilat Express', color: '#F59E0B' },
      { id: 'satuan', label: 'Cuci Satuan', color: '#8B5CF6' },
      { id: 'lipat', label: 'Jangan Dilipat', color: '#EC4899' },
      { id: 'gantung', label: 'Gantung', color: '#14B8A6' }
    ];
  }

  // --- 3. FILTERING LOGIC (Identik index.html) ---
  function matchesOrderTag(order, tagId) {
    const sName = (order.serviceName || '').toLowerCase();
    const notesStr = (order.notes || []).join(' ').toLowerCase();

    switch (tagId) {
      case 'kering-setrika': return sName.includes('kering') && sName.includes('setrika');
      case 'kering-saja': return sName.includes('kering saja') || (sName.includes('kering') && !sName.includes('setrika'));
      case 'setrika-saja': return sName.includes('setrika saja') || (sName.includes('setrika') && !sName.includes('cuci'));
      case 'kilat': return order.isKilat || sName.includes('kilat') || sName.includes('express');
      case 'satuan': return sName.includes('satuan') || order.unit === 'item' || order.unit === 'pcs';
      case 'lipat': return notesStr.includes('lipat');
      case 'gantung': return notesStr.includes('gantung');
      default: return false;
    }
  }

  function getFilteredOrders() {
    const f = STATE.orderFilters;
    const searchQ = (STATE.orderSearchQuery || '').trim().toLowerCase();
    const amountVal = f.amountValue !== '' ? parseInt(f.amountValue, 10) : NaN;
    const hasAmountFilter = !isNaN(amountVal) && amountVal >= 0;

    return STATE.orders.filter(o => {
      // Status filter
      if (f.statuses.length > 0) {
        const hit = f.statuses.includes(o.status) || f.statuses.includes(o.status.replace(/_/g, ' ')) || f.statuses.includes(o.status.replace(/ /g, '_'));
        if ((f.statusMode === 'exclude' && hit) || (f.statusMode !== 'exclude' && !hit)) {
          return false;
        }
      }

      // Tag filter
      if (f.tags.length > 0) {
        const tagHit = f.tags.some(t => matchesOrderTag(o, t));
        if ((f.tagMode === 'exclude' && tagHit) || (f.tagMode !== 'exclude' && !tagHit)) {
          return false;
        }
      }

      // Date filter
      if (f.datePreset !== 'semua') {
        const d = new Date(o.createdAt);
        const now = new Date();
        const startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        if (f.datePreset === 'today') {
          if (d < startOfToday) return false;
        } else if (f.datePreset === 'yesterday') {
          const startOfYesterday = new Date(startOfToday.getTime() - 86400000);
          if (d < startOfYesterday || d >= startOfToday) return false;
        } else if (f.datePreset === '7d') {
          const startOf7d = new Date(startOfToday.getTime() - 7 * 86400000);
          if (d < startOf7d) return false;
        }
      }
      if (f.dateFrom) {
        const d = new Date(o.createdAt);
        const fromD = new Date(f.dateFrom);
        if (d < fromD) return false;
      }
      if (f.dateTo) {
        const d = new Date(o.createdAt);
        const toD = new Date(f.dateTo + 'T23:59:59');
        if (d > toD) return false;
      }

      // Payment filter
      if (f.payStatus === 'paid' && !o.isPaid) return false;
      if (f.payStatus === 'unpaid' && o.isPaid) return false;
      if (f.payMethod !== 'semua' && (o.paymentMethod || '').toLowerCase() !== f.payMethod.toLowerCase()) {
        return false;
      }
      if (hasAmountFilter) {
        if (f.amountOp === 'lt' && !(o.subtotal < amountVal)) return false;
        if (f.amountOp === 'gte' && !(o.subtotal >= amountVal)) return false;
      }

      // Search query
      if (searchQ) {
        const codeHit = (o.code || '').toLowerCase().includes(searchQ);
        const nameHit = (o.customerName || '').toLowerCase().includes(searchQ);
        const phoneHit = (o.customerPhone || '').includes(searchQ);
        const srvHit = (o.serviceName || '').toLowerCase().includes(searchQ);
        if (!codeHit && !nameHit && !phoneHit && !srvHit) {
          return false;
        }
      }

      return true;
    });
  }

  function getOrderGroupSummary(key) {
    const f = STATE.orderFilters;
    if (key === 'status') {
      if (f.statuses.length === 0) return { active: false, label: 'Status' };
      const prefix = f.statusMode === 'exclude' ? 'Kecuali: ' : '';
      if (f.statuses.length === 1) {
        const opt = ORDER_STATUS_OPTIONS.find(s => s.id === f.statuses[0]);
        return { active: true, label: `${prefix}${opt ? opt.label : f.statuses[0]}` };
      }
      return { active: true, label: `${prefix}Status (${f.statuses.length})` };
    }

    if (key === 'tags') {
      if (f.tags.length === 0) return { active: false, label: 'Layanan & Tag' };
      const prefix = f.tagMode === 'exclude' ? 'Kecuali: ' : '';
      const tagOpts = getOrderTagOptions();
      if (f.tags.length === 1) {
        const opt = tagOpts.find(t => t.id === f.tags[0]);
        return { active: true, label: `${prefix}${opt ? opt.label : f.tags[0]}` };
      }
      return { active: true, label: `${prefix}Layanan (${f.tags.length})` };
    }

    if (key === 'date') {
      if (f.datePreset === 'semua' && !f.dateFrom && !f.dateTo) return { active: false, label: 'Tanggal' };
      if (f.datePreset === 'today') return { active: true, label: 'Hari Ini' };
      if (f.datePreset === 'yesterday') return { active: true, label: 'Kemarin' };
      if (f.datePreset === '7d') return { active: true, label: '7 Hari Terakhir' };
      return { active: true, label: `${f.dateFrom || '...'} - ${f.dateTo || '...'}` };
    }

    if (key === 'payment') {
      const parts = [];
      if (f.payStatus === 'paid') parts.push('Lunas');
      if (f.payStatus === 'unpaid') parts.push('Belum Lunas');
      if (f.payMethod !== 'semua') parts.push(f.payMethod);
      const amt = f.amountValue !== '' ? parseInt(f.amountValue, 10) : NaN;
      if (!isNaN(amt) && amt >= 0) {
        parts.push(`${f.amountOp === 'lt' ? '<' : '>='} ${formatRp(amt)}`);
      }
      if (parts.length === 0) return { active: false, label: 'Bayar & Tagihan' };
      return { active: true, label: parts.join(', ') };
    }

    return { active: false, label: key };
  }

  function hasAnyOrderFilterApplied() {
    const f = STATE.orderFilters;
    return f.statuses.length > 0 || f.tags.length > 0 || f.datePreset !== 'semua' || f.dateFrom || f.dateTo || f.payStatus !== 'semua' || f.payMethod !== 'semua' || f.amountValue !== '' || Boolean((STATE.orderSearchQuery || '').trim());
  }

  // --- 4. VIEW RENDERING (Identik index.html) ---
  function renderOrdersFilterStripHtml() {
    const keys = ['status', 'tags', 'date', 'payment'];
    return keys.map(key => {
      const summary = getOrderGroupSummary(key);
      const isPopoverOpen = STATE.activeOrderFilterPopover === key;
      const alignRightClass = key === 'payment' ? 'align-right' : '';
      return `
        <div class="filter-pill-anchor ${alignRightClass}">
          <button type="button"
            data-filter-key="${key}"
            class="filter-pill-trigger ${summary.active ? 'is-active' : ''} ${isPopoverOpen ? 'is-open' : ''}"
            aria-expanded="${isPopoverOpen}"
            aria-haspopup="dialog"
            onclick="window.ORDERS_APP.togglePopover('${key}', event)">
            ${getFilterPillIconSvg(key)}
            <span data-pill-label>${summary.label}</span>
            <span class="filter-pill-chevron">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
          </button>
          ${isPopoverOpen ? renderOrderFilterPopoverHtml(key) : ''}
        </div>
      `;
    }).join('');
  }

  function renderFilterModeTrackHtml(scope, activeMode) {
    return `
      <div class="popover-mode-track">
        <button type="button" class="popover-mode-btn ${activeMode === 'include' ? 'active' : ''}"
          onclick="window.ORDERS_APP.setFilterMode('${scope}', 'include')">Sertakan</button>
        <button type="button" class="popover-mode-btn ${activeMode === 'exclude' ? 'active' : ''}"
          onclick="window.ORDERS_APP.setFilterMode('${scope}', 'exclude')">Kecualikan</button>
      </div>
    `;
  }

  function renderOrderFilterPopoverHtml(key) {
    const f = STATE.orderFilters;

    if (key === 'status') {
      return `
        <div class="filter-popover-panel" role="dialog" aria-label="Filter Status Alur" onclick="event.stopPropagation()">
          <div class="popover-head">
            <span class="popover-title">Status Alur</span>
            <button type="button" class="popover-clear-btn" onclick="window.ORDERS_APP.clearFilterGroup('status')">Bersihkan</button>
          </div>
          ${renderFilterModeTrackHtml('status', f.statusMode)}
          <div class="popover-options-list">
            ${ORDER_STATUS_OPTIONS.map(st => {
              const isChecked = f.statuses.includes(st.id);
              return `
                <button type="button" class="popover-option-row ${isChecked ? 'active' : ''}"
                  onclick="window.ORDERS_APP.toggleFilterItem('status', '${st.id}')">
                  <span class="popover-tag-badge">
                    <span class="popover-dot" style="background: ${st.color};"></span>
                    <span>${st.label}</span>
                  </span>
                  ${isChecked ? getCleanSvg('check', 14) : ''}
                </button>
              `;
            }).join('')}
          </div>
        </div>
      `;
    }

    if (key === 'tags') {
      return `
        <div class="filter-popover-panel" role="dialog" aria-label="Filter Layanan dan Prioritas" onclick="event.stopPropagation()">
          <div class="popover-head">
            <span class="popover-title">Layanan & Prioritas</span>
            <button type="button" class="popover-clear-btn" onclick="window.ORDERS_APP.clearFilterGroup('tags')">Bersihkan</button>
          </div>
          ${renderFilterModeTrackHtml('tags', f.tagMode)}
          <div class="popover-options-list">
            ${getOrderTagOptions().map(opt => {
              const isChecked = f.tags.includes(opt.id);
              return `
                <button type="button" class="popover-option-row ${isChecked ? 'active' : ''}"
                  onclick="window.ORDERS_APP.toggleFilterItem('tags', '${opt.id}')">
                  <span class="popover-tag-badge">
                    <span class="popover-dot" style="background: ${opt.color};"></span>
                    <span>${opt.label}</span>
                  </span>
                  ${isChecked ? getCleanSvg('check', 14) : ''}
                </button>
              `;
            }).join('')}
          </div>
        </div>
      `;
    }

    if (key === 'date') {
      return `
        <div class="filter-popover-panel" role="dialog" aria-label="Filter Tanggal Masuk" onclick="event.stopPropagation()">
          <div class="popover-head">
            <span class="popover-title">Tanggal Masuk</span>
            <button type="button" class="popover-clear-btn" onclick="window.ORDERS_APP.clearFilterGroup('date')">Bersihkan</button>
          </div>
          <span class="popover-section-label" style="margin-top: 0;">Pilihan Cepat</span>
          <div class="popover-chips-grid">
            ${[
              { id: 'semua', label: 'Semua' },
              { id: 'today', label: 'Hari Ini' },
              { id: 'yesterday', label: 'Kemarin' },
              { id: '7d', label: '7 Hari Terakhir' }
            ].map(p => `
              <button type="button" class="popover-chip-btn ${f.datePreset === p.id ? 'active' : ''}"
                onclick="window.ORDERS_APP.setDatePreset('${p.id}')">${p.label}</button>
            `).join('')}
          </div>
          <span class="popover-section-label">Rentang Tanggal</span>
          <div class="popover-date-grid">
            <label class="popover-field-label">
              <span>Dari</span>
              <input type="date" class="popover-mini-input" value="${escapeHtml(f.dateFrom || '')}"
                onchange="window.ORDERS_APP.setCustomDate('dateFrom', this.value)">
            </label>
            <label class="popover-field-label">
              <span>Sampai</span>
              <input type="date" class="popover-mini-input" value="${escapeHtml(f.dateTo || '')}"
                onchange="window.ORDERS_APP.setCustomDate('dateTo', this.value)">
            </label>
          </div>
        </div>
      `;
    }

    if (key === 'payment') {
      const amtNum = f.amountValue !== '' ? parseInt(f.amountValue, 10) : NaN;
      const isLt25 = f.amountOp === 'lt' && amtNum === 25000;
      const isGte25 = f.amountOp === 'gte' && amtNum === 25000;
      return `
        <div class="filter-popover-panel align-right" role="dialog" aria-label="Filter Bayar dan Tagihan" onclick="event.stopPropagation()">
          <div class="popover-head">
            <span class="popover-title">Bayar & Tagihan</span>
            <button type="button" class="popover-clear-btn" onclick="window.ORDERS_APP.clearFilterGroup('payment')">Bersihkan</button>
          </div>
          <span class="popover-section-label" style="margin-top: 0;">Status Pembayaran</span>
          <div class="popover-chips-grid">
            ${[
              { id: 'semua', label: 'Semua' },
              { id: 'paid', label: 'Lunas' },
              { id: 'unpaid', label: 'Belum Lunas' }
            ].map(st => `
              <button type="button" class="popover-chip-btn ${f.payStatus === st.id ? 'active' : ''}"
                onclick="window.ORDERS_APP.setPayStatus('${st.id}')">${st.label}</button>
            `).join('')}
          </div>
          <span class="popover-section-label">Metode Pembayaran</span>
          <div class="popover-chips-grid">
            ${['semua', 'Tunai', 'Transfer', 'E-Wallet'].map(m => `
              <button type="button" class="popover-chip-btn ${f.payMethod === m ? 'active' : ''}"
                onclick="window.ORDERS_APP.setPayMethod('${m}')">${m === 'semua' ? 'Semua' : m}</button>
            `).join('')}
          </div>
          <span class="popover-section-label">Batas Nominal Tagihan</span>
          <div class="popover-chips-grid">
            <button type="button" class="popover-chip-btn ${isLt25 ? 'active' : ''}"
              onclick="window.ORDERS_APP.setAmountPreset('lt', '25000')">&lt; Rp 25.000</button>
            <button type="button" class="popover-chip-btn ${isGte25 ? 'active' : ''}"
              onclick="window.ORDERS_APP.setAmountPreset('gte', '25000')">&ge; Rp 25.000</button>
          </div>
          <div class="popover-amount-row">
            <select class="popover-mini-input" style="width: auto;" aria-label="Operator nominal"
              onchange="window.ORDERS_APP.setAmountOp(this.value)">
              <option value="lt" ${f.amountOp === 'lt' ? 'selected' : ''}>&lt; Di bawah</option>
              <option value="gte" ${f.amountOp === 'gte' ? 'selected' : ''}>&ge; Minimal</option>
            </select>
            <input type="number" min="0" step="1000" class="popover-mini-input num-tabular font-mono"
              placeholder="Nominal Rp..." value="${escapeHtml(f.amountValue || '')}"
              oninput="window.ORDERS_APP.setAmountInput(this.value)">
          </div>
        </div>
      `;
    }

    return '';
  }

  function renderOrdersTableRowsHtml(pageItems) {
    if (pageItems.length === 0) {
      return `
        <tr>
          <td colspan="9">
            <div style="padding: 2.75rem 1.5rem; text-align: center; color: var(--ink-muted); display: flex; flex-direction: column; align-items: center; gap: 0.65rem;">
              <span>Tidak ditemukan pesanan yang sesuai dengan kriteria filter.</span>
              <button type="button" class="btn btn-secondary" style="font-size: 0.78rem; padding: 0.35rem 0.85rem;" onclick="window.ORDERS_APP.resetFilters()">
                Bersihkan semua filter
              </button>
            </div>
          </td>
        </tr>
      `;
    }

    return pageItems.map(order => {
      const { date, time } = formatDateParts(order.createdAt);
      const isSelected = STATE.selectedOrders.includes(order.code);
      const custName = order.customerName || order.customer || 'Pelanggan Umum';
      const custPhone = order.customerPhone || order.phone || '-';
      const srvName = order.serviceName || order.service || 'Cuci Kering + Setrika';
      const subtotalVal = order.subtotal ?? order.total ?? 0;
      const payMethodVal = order.paymentMethod || order.payMethod || 'Tunai';
      const weightVal = order.weight || 1;
      const unitVal = order.unit || 'kg';

      const rawSt = (order.status || 'antrean').toLowerCase();
      let dispSt = 'Antrean';
      if (rawSt.includes('cuci')) dispSt = 'Dicuci';
      else if (rawSt.includes('setrika')) dispSt = 'Disetrika';
      else if (rawSt.includes('siap')) dispSt = 'Siap Diambil';
      else if (rawSt.includes('selesai') || rawSt.includes('ambil')) dispSt = 'Selesai';

      return `
        <tr class="${isSelected ? 'is-selected' : ''}">
          <td class="col-check">
            <input type="checkbox" class="row-check" aria-label="Pilih ${escapeHtml(order.code)}"
              ${isSelected ? 'checked' : ''}
              onchange="window.ORDERS_APP.toggleRowSelect('${escapeHtml(order.code)}', this.checked)">
          </td>
          <td>
            <div style="display: inline-flex; flex-wrap: wrap; align-items: center; gap: 3px 5px;">
              <span class="table-order-code num-tabular">${escapeHtml(order.code)}</span>
              ${order.isKilat ? `<span class="table-kilat-chip">Kilat</span>` : ''}
            </div>
          </td>
          <td>
            <div class="table-date-stack">
              <span class="table-date-main">${date}</span>
              <span class="table-date-sub">${time}</span>
            </div>
          </td>
          <td>
            <strong style="display: block; line-height: 1.28; color: var(--ink); font-size: 0.82rem;">${escapeHtml(custName)}</strong>
            <div class="orders-spec-sub" style="font-variant-numeric: tabular-nums;">${escapeHtml(custPhone)}</div>
          </td>
          <td>
            <strong style="display: block; line-height: 1.28; color: var(--ink); font-size: 0.82rem;">${escapeHtml(srvName)}</strong>
          </td>
          <td>
            <span class="orders-status-badge" data-status="${escapeHtml(order.status)}">${dispSt}</span>
          </td>
          <td>
            <span class="orders-pay-badge" data-paid="${order.isPaid ? 'true' : 'false'}">
              ${order.isPaid ? 'Lunas' : 'Belum Lunas'}
            </span>
          </td>
          <td class="num-tabular font-mono" style="font-size: 0.84rem; font-weight: 700; text-align: right; white-space: nowrap; color: var(--ink);">
            ${formatRp(subtotalVal)}
          </td>
          <td style="text-align: center;">
            <div style="display: inline-flex; gap: 4px; justify-content: center;">
              <button type="button" class="btn btn-secondary orders-action-btn" title="Kelola Alur & Rincian" style="color: var(--primary); font-weight: 700;"
                onclick="if(typeof openOrderDrawer==='function')openOrderDrawer('${escapeHtml(order.code)}')">
                Alur
              </button>
              <button type="button" class="btn btn-secondary orders-action-btn"
                onclick="window.ORDERS_APP.previewReceipt('${escapeHtml(order.code)}')">
                Nota
              </button>
              <button type="button" class="btn btn-secondary orders-action-btn"
                onclick="window.ORDERS_APP.previewSticker('${escapeHtml(order.code)}')">
                ${getCleanSvg('tag', 11)} Label
              </button>
            </div>
          </td>
        </tr>
      `;
    }).join('');
  }

  // --- 5. SLIDING LENS PAGINATION DOCK (Identik index.html) ---
  function getPaginationWindow(totalPages, activePage) {
    if (totalPages <= 7) {
      return Array.from({ length: totalPages }, (_, i) => i + 1);
    }
    let start = Math.max(2, activePage - 1);
    let end = Math.min(totalPages - 1, activePage + 1);
    if (activePage <= 3) { start = 2; end = 4; }
    if (activePage >= totalPages - 2) { start = totalPages - 3; end = totalPages - 1; }
    const items = [1];
    if (start > 2) items.push('gap');
    for (let i = start; i <= end; i++) items.push(i);
    if (end < totalPages - 1) items.push('gap');
    items.push(totalPages);
    return items;
  }

  function renderScopedPaginationDotsHtml(totalPages, safePage, windowItems) {
    return windowItems.map(item => {
      if (item === 'gap') {
        return `<span class="pagination-gap" aria-hidden="true">&hellip;</span>`;
      }
      const isActive = item === safePage;
      return `<button type="button" data-page="${item}"
        class="pagination-dot ${isActive ? 'is-active' : ''}"
        aria-label="Halaman ${item}" ${isActive ? 'aria-current="page"' : ''}
        onclick="window.ORDERS_APP.goToPage(${item})">${item}</button>`;
    }).join('');
  }

  function renderScopedPaginationInnerHtml(filteredLength) {
    const pageSize = STATE.ordersPageSize || 10;
    const totalPages = Math.max(1, Math.ceil(filteredLength / pageSize));
    const safePage = Math.min(Math.max(1, STATE.ordersPage || 1), totalPages);
    STATE.ordersPage = safePage;
    const atFirst = safePage <= 1 ? 'disabled' : '';
    const atLast = safePage >= totalPages ? 'disabled' : '';
    const windowItems = getPaginationWindow(totalPages, safePage);
    const windowSig = windowItems.join(',');

    return `
      <nav id="orders-pagination-nav" class="wg-capsule" aria-label="Halaman">
        <button type="button" id="pagination-prev-btn" class="wg-nav is-prev" ${atFirst}
          onclick="window.ORDERS_APP.goToPage(${safePage - 1})">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
          <span>Sebelumnya</span>
        </button>
        <div id="orders-pagination-track" class="wg-pages-track" data-window-sig="${windowSig}">
          <span id="pagination-glass-lens" class="wg-active-pill" aria-hidden="true"></span>
          ${renderScopedPaginationDotsHtml(totalPages, safePage, windowItems)}
        </div>
        <button type="button" id="pagination-next-btn" class="wg-nav is-next" ${atLast}
          onclick="window.ORDERS_APP.goToPage(${safePage + 1})">
          <span>Berikutnya</span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
        </button>
      </nav>
    `;
  }

  function syncScopedPaginationPill(instant = false) {
    const trackEl = document.getElementById('orders-pagination-track');
    const lensEl = document.getElementById('pagination-glass-lens');
    if (!trackEl || !lensEl) return;

    const activeDot = trackEl.querySelector('.pagination-dot.is-active');
    if (!activeDot || activeDot.offsetWidth === 0) {
      lensEl.style.opacity = '0';
      return;
    }

    const targetX = activeDot.offsetLeft;
    const targetW = activeDot.offsetWidth;
    lensEl.style.width = `${targetW}px`;

    if (instant) {
      lensEl.style.transition = 'none';
      lensEl.style.transform = `translate3d(${targetX}px, 0, 0)`;
      lensEl.style.opacity = '1';
      void lensEl.offsetWidth;
      lensEl.style.transition = '';
    } else {
      lensEl.style.opacity = '1';
      lensEl.style.transform = `translate3d(${targetX}px, 0, 0)`;
    }
  }

  // --- 6. MODAL SANDBOX HELPERS (Struk Termal & Label Stiker) ---
  function openModal(contentHtml) {
    const sandbox = document.getElementById('modal-sandbox');
    if (!sandbox) return;
    sandbox.innerHTML = contentHtml;
  }

  function closeModal() {
    const sandbox = document.getElementById('modal-sandbox');
    if (sandbox) sandbox.innerHTML = '';
  }

  function previewThermalReceipt(code) {
    const order = STATE.orders.find(o => o.code === code) || STATE.orders[0];
    if (!order) return;

    const { date, time } = formatDateParts(order.createdAt);
    const modalHtml = `
      <div class="dialog-backdrop" onclick="window.ORDERS_APP.closeModal()">
        <div class="dialog-panel" style="max-width: 360px;" onclick="event.stopPropagation()">
          <div class="dialog-head">
            <span class="dialog-title">Preview Struk Termal</span>
            <button type="button" class="btn btn-ghost" style="padding: 2px 6px;" onclick="window.ORDERS_APP.closeModal()">Esc</button>
          </div>
          <div class="dialog-body" style="background: #F1F5F9; border-top: 1px solid var(--line); border-bottom: 1px solid var(--line);">
            <div class="modern-thermal-receipt" id="thermal-receipt-content" style="background: #fff; padding: 18px 14px; font-family: 'JetBrains Mono', monospace; font-size: 11px; line-height: 1.45; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border-radius: 4px;">
              <div style="text-align: center; border-bottom: 1px dashed #CBD5E1; padding-bottom: 10px; margin-bottom: 10px;">
                <div style="font-weight: 800; font-size: 13px; color: #0F172A;">LAUNDRYGO \u2022 ADI LAUNDRY</div>
                <div style="font-size: 9.5px; color: #64748B;">Jl. Raya Sengkaling No. 45, Malang</div>
                <div style="font-size: 9.5px; color: #64748B;">WA: 0812-3456-7890</div>
              </div>
              <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                <span>NOTA:</span> <strong>${escapeHtml(order.code)}</strong>
              </div>
              <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                <span>TGL:</span> <span>${date} ${time}</span>
              </div>
              <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span>PLG:</span> <span>${escapeHtml(order.customerName)}</span>
              </div>
              <div style="border-top: 1px dashed #CBD5E1; border-bottom: 1px dashed #CBD5E1; padding: 8px 0; margin-bottom: 8px;">
                <div style="display: flex; justify-content: space-between; font-weight: 700;">
                  <span>${escapeHtml(order.serviceName)}</span>
                  <span>${formatRp(order.subtotal)}</span>
                </div>
                <div style="color: #64748B; font-size: 10px;">${order.weight} ${escapeHtml(order.unit)}</div>
              </div>
              <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 800; margin-bottom: 4px;">
                <span>TOTAL:</span>
                <span>${formatRp(order.subtotal)}</span>
              </div>
              <div style="display: flex; justify-content: space-between; color: ${order.isPaid ? '#16A34A' : '#EF4444'}; font-weight: 700; margin-bottom: 12px;">
                <span>STATUS:</span>
                <span>${order.isPaid ? 'LUNAS (' + escapeHtml(order.paymentMethod) + ')' : 'BELUM LUNAS'}</span>
              </div>
              <div style="text-align: center; border-top: 1px dashed #CBD5E1; padding-top: 10px; font-size: 9.5px; color: #64748B;">
                <div>Terima Kasih Atas Kepercayaan Anda!</div>
                <div>Simpan nota ini untuk pengambilan.</div>
              </div>
            </div>
          </div>
          <div class="dialog-foot" style="display: flex; justify-content: space-between; align-items: center;">
            <button type="button" class="btn btn-secondary" onclick="window.ORDERS_APP.closeModal()">Tutup</button>
            <button type="button" class="btn btn-primary" onclick="window.print()">
              ${getCleanSvg('printer', 14)} <span>Cetak Nota</span>
            </button>
          </div>
        </div>
      </div>
    `;
    openModal(modalHtml);
  }

  function previewThermalSticker(code) {
    const order = STATE.orders.find(o => o.code === code) || STATE.orders[0];
    if (!order) return;

    const modalHtml = `
      <div class="dialog-backdrop" onclick="window.ORDERS_APP.closeModal()">
        <div class="dialog-panel" style="max-width: 380px;" onclick="event.stopPropagation()">
          <div class="dialog-head">
            <div>
              <span class="dialog-title">Label Cucian</span>
              <div style="font-size: 0.7rem; color: var(--ink-muted); margin-top: 1px;">Format Cetak Termal 58 \u00d7 40 mm</div>
            </div>
            <button type="button" class="btn btn-ghost" style="padding: 2px 6px;" onclick="window.ORDERS_APP.closeModal()">Esc</button>
          </div>
          <div class="dialog-body" style="background: linear-gradient(180deg, #EFF9FF 0%, #E6F4FA 100%); border-top: 1px solid rgba(2,132,199,0.14); border-bottom: 1px solid rgba(2,132,199,0.14);">
            <div class="modern-thermal-label" id="thermal-sticker-content" style="background: #fff; border: 1.5px solid #0284C7; border-radius: 8px; padding: 12px 14px; font-family: 'Plus Jakarta Sans', sans-serif;">
              <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E2E8F0; padding-bottom: 6px; margin-bottom: 8px;">
                <span style="font-size: 0.68rem; font-weight: 800; color: #0284C7;">LAUNDRYGO \u2022 ADI LAUNDRY</span>
                <span style="font-size: 0.65rem; color: #64748B;">${formatDateParts(order.createdAt).date}</span>
              </div>
              <div style="font-size: 1.3rem; font-weight: 800; font-family: 'JetBrains Mono', monospace; color: #0F172A; margin-bottom: 6px;">
                ${escapeHtml(order.code)}
              </div>
              <div style="margin-bottom: 6px;">
                <span class="label-priority-pill ${order.isKilat ? 'is-kilat' : 'is-reguler'}">
                  ${order.isKilat ? '\u26a1 KILAT EXPRESS' : '\u23f1 REGULER'}
                </span>
              </div>
              <div style="font-size: 0.88rem; font-weight: 800; color: #0F172A;">${escapeHtml(order.customerName)}</div>
              <div style="font-size: 0.75rem; color: #475569; margin-bottom: 4px;">${escapeHtml(order.serviceName)} \u2022 ${order.weight} ${escapeHtml(order.unit)}</div>

              <div style="display: flex; justify-content: space-between; margin-top: 8px; padding-top: 6px; border-top: 1px dashed #CBD5E1; font-size: 0.7rem; font-weight: 700; color: #64748B;">
                <span>STATUS: ${escapeHtml(order.status.toUpperCase())}</span>
                <span>RAK: ${escapeHtml(order.rackLocation || 'A1')}</span>
              </div>
            </div>
          </div>
          <div class="dialog-foot" style="display: flex; justify-content: space-between; align-items: center;">
            <button type="button" class="btn btn-secondary" onclick="window.ORDERS_APP.closeModal()">Tutup</button>
            <button type="button" class="btn btn-primary" onclick="window.print()">
              ${getCleanSvg('printer', 14)} <span>Cetak Label</span>
            </button>
          </div>
        </div>
      </div>
    `;
    openModal(modalHtml);
  }

  // --- 7. EXPORT CSV ---
  function exportOrdersCsv() {
    const filtered = getFilteredOrders();
    const rows = [
      ['Nomor Nota', 'Tanggal', 'Pelanggan', 'No HP', 'Layanan', 'Status', 'Pembayaran', 'Total']
    ];
    filtered.forEach(o => {
      rows.push([
        o.code,
        o.createdAt,
        o.customerName,
        o.customerPhone,
        o.serviceName,
        o.status,
        o.isPaid ? 'Lunas' : 'Belum Lunas',
        o.subtotal
      ]);
    });
    const csvContent = 'data:text/csv;charset=utf-8,' + rows.map(e => e.map(i => `"${String(i).replace(/"/g, '""')}"`).join(',')).join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `Daftar_Pesanan_${new Date().toISOString().slice(0, 10)}.csv`);
    document.body.appendChild(link);
    link.click();
    link.remove();
  }

  // --- 8. REACTIVE RE-RENDER ENGINE ---
  function updateOrdersTableInPlace() {
    const filtered = getFilteredOrders();
    const pageSize = STATE.ordersPageSize || 10;
    const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));
    const safePage = Math.min(Math.max(1, STATE.ordersPage || 1), totalPages);
    STATE.ordersPage = safePage;

    const start = (safePage - 1) * pageSize;
    const pageItems = filtered.slice(start, start + pageSize);

    // Update tbody
    const tbody = document.getElementById('orders-table-tbody');
    if (tbody) {
      tbody.innerHTML = renderOrdersTableRowsHtml(pageItems);
    }

    // Update filter strip
    const filterStrip = document.getElementById('orders-filter-strip');
    if (filterStrip) {
      filterStrip.innerHTML = renderOrdersFilterStripHtml();
    }

    // Update reset button
    const resetSlot = document.getElementById('orders-reset-slot');
    if (resetSlot) {
      resetSlot.innerHTML = hasAnyOrderFilterApplied() ? `
        <button type="button" class="orders-reset-link-btn" onclick="window.ORDERS_APP.resetFilters()">
          Reset
        </button>
      ` : '';
    }

    // Update Pagination Dock
    const dock = document.getElementById('orders-pagination-dock');
    if (dock) {
      dock.innerHTML = renderScopedPaginationInnerHtml(filtered.length);
      syncScopedPaginationPill(false);
    }

    // Update Meta count
    const metaCount = document.getElementById('orders-total-count-label');
    if (metaCount) {
      metaCount.textContent = `Total ${filtered.length} pesanan`;
    }
  }

  // Public Interface for Inline Event Handlers
  window.ORDERS_APP = {
    togglePopover: function(key, e) {
      if (e) {
        e.preventDefault();
        e.stopPropagation();
      }
      STATE.activeOrderFilterPopover = (STATE.activeOrderFilterPopover === key) ? null : key;
      updateOrdersTableInPlace();
    },
    setFilterMode: function(scope, mode, e) {
      if (e) e.stopPropagation();
      if (scope === 'status') STATE.orderFilters.statusMode = mode;
      if (scope === 'tags') STATE.orderFilters.tagMode = mode;
      STATE.ordersPage = 1;
      updateOrdersTableInPlace();
    },
    toggleFilterItem: function(scope, id, e) {
      if (e) e.stopPropagation();
      if (scope === 'status') {
        const arr = STATE.orderFilters.statuses;
        const idx = arr.indexOf(id);
        if (idx >= 0) arr.splice(idx, 1);
        else arr.push(id);
      } else if (scope === 'tags') {
        const arr = STATE.orderFilters.tags;
        const idx = arr.indexOf(id);
        if (idx >= 0) arr.splice(idx, 1);
        else arr.push(id);
      }
      STATE.ordersPage = 1;
      updateOrdersTableInPlace();
    },
    clearFilterGroup: function(key) {
      if (key === 'status') STATE.orderFilters.statuses = [];
      if (key === 'tags') STATE.orderFilters.tags = [];
      if (key === 'date') {
        STATE.orderFilters.datePreset = 'semua';
        STATE.orderFilters.dateFrom = '';
        STATE.orderFilters.dateTo = '';
      }
      if (key === 'payment') {
        STATE.orderFilters.payStatus = 'semua';
        STATE.orderFilters.payMethod = 'semua';
        STATE.orderFilters.amountValue = '';
      }
      STATE.ordersPage = 1;
      updateOrdersTableInPlace();
    },
    setDatePreset: function(preset) {
      STATE.orderFilters.datePreset = preset;
      STATE.orderFilters.dateFrom = '';
      STATE.orderFilters.dateTo = '';
      STATE.ordersPage = 1;
      updateOrdersTableInPlace();
    },
    setCustomDate: function(field, val) {
      STATE.orderFilters.datePreset = 'custom';
      STATE.orderFilters[field] = val;
      STATE.ordersPage = 1;
      updateOrdersTableInPlace();
    },
    setPayStatus: function(val) {
      STATE.orderFilters.payStatus = val;
      STATE.ordersPage = 1;
      updateOrdersTableInPlace();
    },
    setPayMethod: function(val) {
      STATE.orderFilters.payMethod = val;
      STATE.ordersPage = 1;
      updateOrdersTableInPlace();
    },
    setAmountPreset: function(op, val) {
      STATE.orderFilters.amountOp = op;
      STATE.orderFilters.amountValue = val;
      STATE.ordersPage = 1;
      updateOrdersTableInPlace();
    },
    setAmountOp: function(op) {
      STATE.orderFilters.amountOp = op;
      STATE.ordersPage = 1;
      updateOrdersTableInPlace();
    },
    setAmountInput: function(val) {
      STATE.orderFilters.amountValue = val;
      STATE.ordersPage = 1;
      updateOrdersTableInPlace();
    },
    handleSearch: function(val) {
      STATE.orderSearchQuery = val;
      STATE.ordersPage = 1;
      updateOrdersTableInPlace();
    },
    resetFilters: function() {
      STATE.orderFilters = {
        statuses: [],
        statusMode: 'include',
        tags: [],
        tagMode: 'include',
        datePreset: 'semua',
        dateFrom: '',
        dateTo: '',
        payStatus: 'semua',
        payMethod: 'semua',
        amountOp: 'lt',
        amountValue: ''
      };
      STATE.orderSearchQuery = '';
      const inp = document.getElementById('order-list-search-input');
      if (inp) inp.value = '';
      STATE.ordersPage = 1;
      STATE.activeOrderFilterPopover = null;
      updateOrdersTableInPlace();
    },
    goToPage: function(pageNum) {
      STATE.ordersPage = pageNum;
      updateOrdersTableInPlace();
    },
    toggleRowSelect: function(code, isChecked) {
      const idx = STATE.selectedOrders.indexOf(code);
      if (isChecked && idx < 0) STATE.selectedOrders.push(code);
      if (!isChecked && idx >= 0) STATE.selectedOrders.splice(idx, 1);
    },
    toggleSelectAll: function(isChecked) {
      const filtered = getFilteredOrders();
      if (isChecked) {
        STATE.selectedOrders = filtered.map(o => o.code);
      } else {
        STATE.selectedOrders = [];
      }
      updateOrdersTableInPlace();
    },
    previewReceipt: previewThermalReceipt,
    previewSticker: previewThermalSticker,
    closeModal: closeModal,
    exportCsv: exportOrdersCsv
  };

  // Close popover when clicking outside
  document.addEventListener('click', function(e) {
    if (STATE.activeOrderFilterPopover && !e.target.closest('.orders-filter-bar-wrap')) {
      STATE.activeOrderFilterPopover = null;
      updateOrdersTableInPlace();
    }
  });

  // Init
  function initOrdersWithQueryCheck() {
    if (window.location.search.includes('open_filter=')) {
      const match = window.location.search.match(/open_filter=([a-z]+)/);
      if (match && match[1]) STATE.activeOrderFilterPopover = match[1];
    }
    updateOrdersTableInPlace();
    syncScopedPaginationPill(true);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initOrdersWithQueryCheck);
  } else {
    initOrdersWithQueryCheck();
  }

})();
