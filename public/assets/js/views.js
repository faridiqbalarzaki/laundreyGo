window.Views = {
  /* Helper: Render Kode LG-YYYYMMDD-NNN Bertingkat Tanpa Kode Lain */
  renderLgCodeHero(code) {
    const { prefix, seq } = Getters.splitCode(code);
    return `<span class="lg-code-hero"><span class="lg-code-prefix">${prefix}</span><span class="lg-code-seq">${seq}</span></span>`;
  },

  renderStatusBadge(status) {
    const map = {
      Diterima: "badge-diterima",
      Dicuci: "badge-dicuci",
      Disetrika: "badge-disetrika",
      Selesai: "badge-selesai",
      Diambil: "badge-diambil",
      Dibatalkan: "badge-batal",
    };
    return `<span class="badge ${map[status] || "badge-diterima"}"><span class="badge-dot"></span>${status}</span>`;
  },

  /* ==========================================================================
     1. DASHBOARD (VERSI LENGKAP ADMIN & VERSI RINGKAS KASIR - §3 & §4)
     ========================================================================== */
  dashboard() {
    const fin = Getters.financialMetrics();
    const activeOrders = Getters.activeProductionOrders();
    const doneOrders = Getters.doneOrdersSorted();
    const unnotified = doneOrders.filter((o) => !o.notified);
    const attentionOrders = Store.orders.filter(
      (o) =>
        (o.kilat || o.terlambat) &&
        ["Diterima", "Dicuci", "Disetrika"].includes(o.status),
    );

    /* §3: Jika Role = Kasir, tampilkan Dashboard Ringkas */
    if (Store.role === "Kasir") {
      return `
        <div class="bento-grid animate-stagger">
          <div class="card card-gradient col-4">
            <div style="font-size:13px; font-weight:700; opacity:0.9;">Pesanan hari ini</div>
            <div class="mono" style="font-size:36px; font-weight:800; margin:10px 0;">${Getters.validOrders().length}</div>
            <div style="font-size:12.5px; opacity:0.9; margin-bottom:14px;">${activeOrders.length} sedang diproses di produksi</div>
            <button class="btn btn-sm" style="background:rgba(255,255,255,0.2); color:#fff;" onclick="App.navigate('kasir')">+ Input pesanan baru</button>
          </div>
          <div class="card col-4">
            <div class="card-title">Selesai belum dikabari</div>
            <div class="mono" style="font-size:34px; font-weight:800; color:var(--utama); margin:10px 0;">${unnotified.length}</div>
            <div style="font-size:12.5px; color:var(--teks-muted); margin-bottom:14px;">Dari total ${doneOrders.length} cucian selesai</div>
            <button class="btn btn-secondary btn-sm" onclick="App.navigate('selesai')">Hubungi pelanggan</button>
          </div>
          <div class="card col-4">
            <div class="card-title">Belum lunas</div>
            <div class="mono" style="font-size:34px; font-weight:800; color:var(--coral-accent); margin:10px 0;">${fin.unpaidOrders.length}</div>
            <div style="font-size:12.5px; color:var(--teks-muted); margin-bottom:14px;">Total tagihan ${Getters.rp(fin.unpaidTotal)}</div>
            <button class="btn btn-outline btn-sm" onclick="App.navigate('pesanan')">Lihat daftar pesanan</button>
          </div>
        </div>
      `;
    }

    /* Dashboard Lengkap Admin */
    const chartData = Getters.chart14Days();
    const maxVal = Math.max(
      ...chartData.map((d) => Math.max(d.rev, d.exp)),
      100000,
    );

    return `
      <div class="bento-grid animate-stagger">
        <!-- Filter Periode Dashboard -->
        <div class="card col-12" style="padding:12px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
          <div style="display:flex; align-items:center; gap:8px;">
            <span style="font-size:12.5px; font-weight:700; color:var(--teks-muted);">Periode pantauan:</span>
            ${["Hari ini", "7 hari", "Bulan ini"]
              .map(
                (p) => `
              <button class="chip ${Store.filters.dashboardPeriod === p ? "active" : ""}" onclick="App.setDashboardPeriod('${p}')">${p}</button>
            `,
              )
              .join("")}
          </div>
          <span style="font-size:12px; color:var(--teks-muted);">Data real-time dari transaksi kasir & produksi</span>
        </div>

        <!-- KPI 1: Hero Omset dengan Indikator Tren ▲ Mint -->
        <div class="card card-gradient col-4">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:13.5px; font-weight:700; opacity:0.92;">Pemasukan lunas (${Store.filters.dashboardPeriod})</span>
            <span class="trend-pill trend-up">▲ +14,2% vs periode lalu</span>
          </div>
          <div class="mono" style="font-size:34px; font-weight:800; margin:14px 0 8px;">${Getters.rp(fin.totalRevenue)}</div>
          <div style="font-size:12.5px; opacity:0.92; margin-bottom:16px;">
            Laba bersih <strong class="mono">${Getters.rp(fin.netProfit)}</strong> • Pengeluaran <strong class="mono">${Getters.rp(fin.totalExpense)}</strong>
          </div>
          <button class="btn btn-sm" style="background:rgba(255,255,255,0.2); color:#fff;" onclick="App.navigate('laporan')">Buka rincian laporan →</button>
        </div>

        <!-- KPI 2: Beban Antrean & Sebaran Status -->
        <div class="card col-4">
          <div class="card-header" style="margin-bottom:8px;">
            <span class="card-title">Pesanan & sebaran status</span>
            <span class="trend-pill trend-up">▲ +2 pesanan</span>
          </div>
          <div class="mono" style="font-size:32px; font-weight:800; margin-bottom:10px;">
            ${activeOrders.length} <span style="font-size:13.5px; font-weight:600; color:var(--teks-muted); font-family:'Plus Jakarta Sans',sans-serif;">sedang dikerjakan</span>
          </div>
          <div class="chip-row" style="margin-bottom:14px;">
            <span class="badge badge-diterima"><span class="badge-dot"></span>${Store.orders.filter((o) => o.status === "Diterima").length} Diterima</span>
            <span class="badge badge-dicuci"><span class="badge-dot"></span>${Store.orders.filter((o) => o.status === "Dicuci").length} Dicuci</span>
            <span class="badge badge-disetrika"><span class="badge-dot"></span>${Store.orders.filter((o) => o.status === "Disetrika").length} Disetrika</span>
          </div>
          <button class="btn btn-outline btn-sm w-full" onclick="App.navigate('antrean')">Kelola antrean produksi</button>
        </div>

        <!-- KPI 3: Cucian Selesai & Belum Lunas -->
        <div class="card col-4">
          <div class="card-header" style="margin-bottom:8px;">
            <span class="card-title">Cucian selesai & belum lunas</span>
            <span class="trend-pill trend-down">▼ ${unnotified.length} belum dikabari</span>
          </div>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin:10px 0 14px;">
            <div style="background:var(--bg-latar); padding:10px 12px; border-radius:12px;">
              <div style="font-size:11.5px; color:var(--teks-muted);">Menunggu diambil</div>
              <div class="mono" style="font-size:22px; font-weight:800; color:var(--utama);">${doneOrders.length}</div>
            </div>
            <div style="background:var(--bg-latar); padding:10px 12px; border-radius:12px;">
              <div style="font-size:11.5px; color:var(--teks-muted);">Belum lunas (${fin.unpaidOrders.length})</div>
              <div class="mono" style="font-size:15.5px; font-weight:800; color:var(--coral-accent); margin-top:4px;">${Getters.rp(fin.unpaidTotal)}</div>
            </div>
          </div>
          <button class="btn btn-secondary btn-sm w-full" onclick="App.navigate('selesai')">Buka cucian selesai</button>
        </div>

        <!-- BARIS 2: PERLU PERHATIAN (KILAT / TERLAMBAT) & GRAFIK 14 HARI DENGAN TOOLTIP -->
        <div class="card col-6">
          <div class="card-header">
            <span class="card-title">Perlu perhatian (Kilat & Terlambat)</span>
            <span class="badge badge-kilat">${attentionOrders.length} prioritas</span>
          </div>
          ${
            attentionOrders.length === 0
              ? `
            <div class="empty-state">Semua pesanan prioritas sudah selesai tepat waktu.</div>
          `
              : attentionOrders
                  .map(
                    (o) => `
            <div class="spec-row card-interactive" style="padding:12px 10px; border-radius:12px;" onclick="App.openDrawer('${o.code}')">
              <div>
                <div style="display:flex; align-items:center; gap:8px;">
                  <strong class="mono">${o.code}</strong>
                  ${o.kilat ? `<span class="badge badge-kilat"><span class="badge-dot"></span>KILAT</span>` : ""}
                  ${o.terlambat ? `<span class="badge badge-terlambat"><span class="badge-dot"></span>Terlambat</span>` : ""}
                </div>
                <div style="font-size:13px; margin-top:3px;"><strong>${o.customer}</strong> • ${Getters.orderSummaryService(o)}</div>
              </div>
              <div style="text-align:right;">
                ${this.renderStatusBadge(o.status)}
                <div style="font-size:11.5px; font-weight:700; color:var(--coral-accent); margin-top:4px;">${o.remainingLabel}</div>
              </div>
            </div>
          `,
                  )
                  .join("")
          }
        </div>

        <div class="card col-6">
          <div class="card-header">
            <span class="card-title">Tren pemasukan & pengeluaran (14 hari)</span>
            <span style="font-size:11.5px; color:var(--teks-muted);">Arahkan kursor ke grafik untuk rincian</span>
          </div>
          <div class="chart-14-grid">
            ${chartData
              .map((d) => {
                const inH = Math.max(8, Math.round((d.rev / maxVal) * 100));
                const outH = Math.max(6, Math.round((d.exp / maxVal) * 100));
                return `
                <div class="chart-col">
                  <div class="chart-tooltip">
                    <strong>${d.label}</strong><br/>
                    Masuk: ${Getters.rp(d.rev)}<br/>
                    Keluar: ${Getters.rp(d.exp)}
                  </div>
                  <div class="chart-bars">
                    <div class="bar-in" style="height:${inH}%;"></div>
                    <div class="bar-out" style="height:${outH}%;"></div>
                  </div>
                  <span class="mono" style="font-size:9.5px; color:var(--teks-muted); margin-top:4px;">${d.label.split(" ")[0]}</span>
                </div>
              `;
              })
              .join("")}
          </div>
        </div>
      </div>
    `;
  },

  /* ==========================================================================
     2. HALAMAN KASIR POS (§4: Solid Teal + Checkmark, Multi-item, Banner Sukses)
     ========================================================================== */
  kasir() {
    const activeSvc =
      Store.services.find((s) => s.id === Store.pos.selectedServiceId) ||
      Store.services[0];
    const cart = Getters.posCartSummary();
    const filteredCust = Store.customers.filter(
      (c) =>
        c.name.toLowerCase().includes(Store.pos.customerSearch.toLowerCase()) ||
        c.phone.includes(Store.pos.customerSearch),
    );
    const activeCust =
      Store.customers.find((c) => c.name === Store.pos.selectedCustomerName) ||
      Store.customers[0];

    const renderServiceTile = (s) => {
      const isSelected = s.id === activeSvc.id;
      return `
        <button class="service-tile ${isSelected ? "active" : ""}" onclick="App.selectPosService(${s.id})">
          <div class="service-tile-top">
            <div class="service-icon-box">${ICONS[s.icon]}</div>
            <span class="service-num-badge">${isSelected ? ICONS.check : s.id}</span>
          </div>
          <div>
            <div class="service-name">${s.name}</div>
            <div class="service-meta">
              <span class="service-price">${Getters.rp(s.price)}/${s.unit}</span>
              ${
                s.kilat
                  ? `<span class="badge badge-kilat">Selesai hari ini</span>`
                  : `<span class="service-est">${s.days} hari</span>`
              }
            </div>
          </div>
        </button>
      `;
    };

    return `
      ${
        Store.pos.lastSavedBanner
          ? `
        <div class="success-banner">
          <div>
            <strong>Pesanan ${Store.pos.lastSavedBanner} berhasil disimpan!</strong>
            <span style="font-size:12.5px; margin-left:6px;">Tempelkan label kode pada keranjang/pakaian.</span>
          </div>
          <div class="chip-row">
            <button class="btn btn-outline btn-sm" onclick="App.printOrder('${Store.pos.lastSavedBanner}', 'nota')">${ICONS.printer} Cetak nota</button>
            <button class="btn btn-outline btn-sm" onclick="App.printOrder('${Store.pos.lastSavedBanner}', 'label')">${ICONS.printer} Cetak label</button>
            <button class="btn btn-primary btn-sm" onclick="App.dismissPosBanner()">+ Pesanan baru</button>
          </div>
        </div>
      `
          : ""
      }

      <div class="bento-grid">
        <div class="col-8" style="display:flex; flex-direction:column; gap:var(--gap-bento);">
          <!-- Pilih Layanan -->
          <div class="card">
            <div class="card-header">
              <span class="card-title">${ICONS.layanan} Pilih layanan (Tekan 1–5)</span>
              <span style="font-size:12px; color:var(--teks-muted);">Bisa gabung beberapa layanan dalam satu nota</span>
            </div>
            <div class="service-grid-top">${Store.services.slice(0, 3).map(renderServiceTile).join("")}</div>
            <div class="service-grid-bottom">${Store.services.slice(3, 5).map(renderServiceTile).join("")}</div>
          </div>

          <!-- Pelanggan & Berat/Jumlah -->
          <div class="bento-grid">
            <div class="card col-6">
              <div class="card-header">
                <span class="card-title">${ICONS.pelanggan} Pelanggan</span>
                <button class="btn btn-secondary btn-sm" onclick="App.openCustomerModal()">+ Pelanggan baru <span class="kbd-tag">N</span></button>
              </div>
              <input type="text" id="pos-customer-search" class="input-field" style="margin-bottom:10px;" placeholder="Cari nama atau nomor HP" value="${Store.pos.customerSearch}" oninput="App.filterPosCustomer(this.value)" />
              <div class="chip-row">
                ${filteredCust
                  .slice(0, 6)
                  .map(
                    (c) => `
                  <button class="chip ${activeCust.name === c.name ? "active" : ""}" onclick="App.selectPosCustomer('${c.name}')">${c.name}</button>
                `,
                  )
                  .join("")}
              </div>
              <div style="background:var(--bg-tint); border-radius:12px; padding:11px 14px; margin-top:12px; display:flex; justify-content:space-between; font-size:12.5px;">
                <span style="color:var(--teks-muted);">Nomor WhatsApp</span>
                <strong class="mono">${activeCust.phone}</strong>
              </div>
            </div>

            <div class="card col-6">
              <div class="card-header">
                <span class="card-title">Berat atau jumlah (${activeSvc.name})</span>
              </div>
              <div style="background:var(--bg-tint); border-radius:14px; padding:10px 16px; display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; border:1px solid var(--border-soft);">
                <div style="display:flex; align-items:baseline; gap:8px;">
                  <input
                    type="number"
                    step="0.1"
                    min="0.1"
                    id="pos-staging-qty-input"
                    class="mono"
                    value="${Store.pos.stagingQty}"
                    oninput="App.setPosDirectQty(this.value)"
                    onfocus="this.select()"
                    style="font-size:28px; font-weight:800; background:transparent; border:none; outline:none; color:var(--teks-utama); width:130px;"
                    title="Ketik langsung kuantitas / berat"
                    placeholder="1"
                  />
                  <span style="font-size:11px; color:var(--teks-muted);">(Ketik langsung)</span>
                </div>
                <span style="font-size:14px; color:var(--teks-muted); font-weight:700;">${activeSvc.unit}</span>
              </div>
              <div class="chip-row" style="margin-bottom:8px;">
                <button class="chip mono" onclick="App.adjustPosQty(-0.5)">-0,5</button>
                <button class="chip mono" onclick="App.adjustPosQty(0.5)">+0,5</button>
                <button class="chip mono" onclick="App.adjustPosQty(1)">+1</button>
                <button class="chip mono" onclick="App.adjustPosQty(2)">+2</button>
                <button class="chip mono" onclick="App.adjustPosQty(5)">+5</button>
              </div>
              <div class="chip-row" style="margin-bottom:12px;">
                <button class="chip" onclick="App.resetPosQty()">Reset</button>
                <button class="chip" onclick="App.openNumpadModal()">Numpad</button>
              </div>
              <button class="btn btn-secondary w-full" onclick="App.addServiceToCart()">+ Tambah baris layanan ke nota</button>
            </div>
          </div>

          <!-- Catatan Khusus -->
          <div class="card">
            <div class="card-header"><span class="card-title">Catatan khusus</span></div>
            <div class="chip-row" style="margin-bottom:10px;">
              ${CONFIG.quickNotes
                .map(
                  (n) => `
                <button class="chip ${Store.pos.selectedNotes.includes(n) ? "active" : ""}" onclick="App.togglePosNote('${n}')">${n}</button>
              `,
                )
                .join("")}
            </div>
            <input type="text" class="input-field" placeholder="Catatan tambahan lainnya..." value="${Store.pos.customNote}" oninput="Store.pos.customNote = this.value" />
          </div>
        </div>

        ${this.receiptCard()}
      </div>
    `;
  },

  receiptCard() {
    const cart = Getters.posCartSummary();
    return `
        <!-- Panel Nota Digital Kanan (Sticky) -->
        <div class="card col-4 receipt-card">
          <div class="card-header" style="margin-bottom:8px;">
            <span class="card-title">Nota digital <span class="nav-badge">${cart.lines.length}</span></span>
            <span class="badge badge-dicuci mono">${Getters.nextOrderCode()}</span>
          </div>

          <div style="display:flex; gap:6px; flex-wrap:wrap;">
            <span class="badge badge-kilat">Perkiraan selesai: ${cart.dueText}</span>
            ${cart.hasKilat ? `<span class="badge badge-kilat"><span class="badge-dot"></span>PRIORITAS KILAT</span>` : ""}
          </div>

          <div class="receipt-items">
            ${cart.lines
              .map(
                (line, idx) => `
              <div class="receipt-line">
                <div style="flex:1;">
                  <div style="font-weight:700;">${line.name} ${line.kilat ? `<span class="badge badge-kilat" style="font-size:10px; padding:2px 6px;">KILAT</span>` : ""}</div>
                  <div class="mono" style="font-size:11.5px; color:var(--teks-muted);">@ ${Getters.rp(line.price)}/${line.unit}</div>
                </div>
                <div class="qty-stepper">
                  <button onclick="App.changeCartLineQty(${idx}, -0.5)">−</button>
                  <span class="mono" style="font-size:12px; padding:0 6px; font-weight:700;">${Getters.numFmt(line.qty)} ${line.unit}</span>
                  <button onclick="App.changeCartLineQty(${idx}, 0.5)">+</button>
                </div>
                <strong class="mono">${Getters.rp(line.qty * line.price)}</strong>
              </div>
            `,
              )
              .join("")}
          </div>

          <div class="receipt-footer-sticky">
            <div style="display:flex; justify-content:space-between; align-items:baseline;">
              <span style="font-size:13px; color:var(--teks-muted);">Total tagihan</span>
              <strong class="mono" style="font-size:26px; font-weight:800;">${Getters.rp(cart.total)}</strong>
            </div>

            <div style="background:var(--bg-tint); padding:4px; border-radius:var(--radius-pill); display:grid; grid-template-columns:1fr 1fr; margin:12px 0;">
              <button class="role-btn ${Store.pos.payTiming === "sekarang" ? "active" : ""}" onclick="App.setPosPayTiming('sekarang')">Bayar sekarang</button>
              <button class="role-btn ${Store.pos.payTiming === "nanti" ? "active" : ""}" onclick="App.setPosPayTiming('nanti')">Bayar saat ambil</button>
            </div>

            ${
              Store.pos.payTiming === "sekarang"
                ? `
              <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-bottom:10px;">
                ${["Tunai", "E-Wallet", "Transfer"]
                  .map(
                    (m) => `
                  <button class="chip ${Store.pos.payMethod === m ? "active" : ""}" onclick="App.setPosPayMethod('${m}')">${m}</button>
                `,
                  )
                  .join("")}
              </div>

              ${
                Store.pos.payMethod === "Tunai"
                  ? `
                <div class="chip-row" style="margin-bottom:8px;">
                  <button class="chip ${Store.pos.cashPreset === "pas" ? "active" : ""}" onclick="App.setPosCashPreset('pas')">Uang pas</button>
                  <button class="chip ${Store.pos.cashPreset === 50000 ? "active" : ""}" onclick="App.setPosCashPreset(50000)">Rp 50.000</button>
                  <button class="chip ${Store.pos.cashPreset === 100000 ? "active" : ""}" onclick="App.setPosCashPreset(100000)">Rp 100.000</button>
                </div>
                <input type="number" class="input-field mono" placeholder="Nominal uang diterima..." value="${Store.pos.customCash}" oninput="App.setPosCustomCash(this.value)" />
              `
                  : `
                <input type="text" class="input-field mono" placeholder="Referensi / bukti ${Store.pos.payMethod} (opsional)" value="${Store.pos.paymentRef}" oninput="Store.pos.paymentRef = this.value" />
              `
              }
            `
                : ""
            }

            <div class="change-banner ${cart.cashBanner.isShort ? "kurang" : ""}" style="background:${cart.cashBanner.isShort ? "var(--bg-terlambat)" : "var(--bg-lunas)"}; color:${cart.cashBanner.isShort ? "var(--txt-terlambat)" : "var(--txt-lunas)"}; padding:10px 14px; border-radius:12px; font-size:12.5px; font-weight:700; margin:10px 0;">
              ${cart.cashBanner.text}
            </div>

            <button class="btn btn-primary w-full" onclick="App.submitPosOrder()">
              ${ICONS.printer} Simpan dan cetak nota <span class="kbd-tag" style="background:rgba(255,255,255,0.2); color:#fff;">Enter</span>
            </button>
          </div>
        </div>
      `;
  },

  /* ==========================================================================
     3. DAFTAR PESANAN (P4: Multi-Filter, Sticky Sortable, Role Produksi Tanpa Harga)
     ========================================================================== */
  pesanan() {
    const isProduksi = Store.role === "Produksi";
    const f = Store.filters;
    const allStatuses = [
      "Diterima",
      "Dicuci",
      "Disetrika",
      "Selesai",
      "Diambil",
      "Dibatalkan",
    ];

    let list = Store.orders.filter((o) => {
      /* §3: Produksi hanya melihat pesanan aktif */
      if (isProduksi && !["Diterima", "Dicuci", "Disetrika"].includes(o.status))
        return false;
      const q = (f.search || "").toLowerCase();
      if (
        q &&
        !(
          o.code.toLowerCase().includes(q) ||
          o.customer.toLowerCase().includes(q) ||
          o.phone.includes(q)
        )
      )
        return false;
      if (f.statuses.length && !f.statuses.includes(o.status)) return false;
      if (
        f.services.length &&
        !o.lines.some((l) => f.services.includes(l.name))
      )
        return false;
      if (f.onlyKilat && !o.kilat) return false;
      if (f.onlyUnpaid && o.payment === "Lunas") return false;
      if (f.dateRange === "Hari ini" && o.createdAt !== DateUtil.toISO())
        return false;
      return true;
    });

    list.sort((a, b) => {
      if (a.kilat !== b.kilat) return a.kilat ? -1 : 1;
      const dir = f.sortDir === "asc" ? 1 : -1;
      if (f.sortBy === "total")
        return (Getters.orderTotal(a) - Getters.orderTotal(b)) * dir;
      if (f.sortBy === "due") return a.dueDate.localeCompare(b.dueDate) * dir;
      return a.code.localeCompare(b.code) * dir;
    });

    return `
      <div class="bento-grid">
        <!-- Panel Filter Multi-Kriteria (P4) -->
        <div class="card col-12">
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:10px;">
            <div class="chip-row">
              <span style="font-size:12px; font-weight:700; color:var(--teks-muted); align-self:center;">Status:</span>
              ${allStatuses
                .map(
                  (st) => `
                <button class="chip ${f.statuses.includes(st) ? "active" : ""}" onclick="App.toggleFilterArray('statuses', '${st}')">${st}</button>
              `,
                )
                .join("")}
            </div>
            <div class="chip-row">
              <button class="chip ${f.onlyKilat ? "active" : ""}" onclick="App.toggleBooleanFilter('onlyKilat')">⚡ Kilat saja</button>
              ${!isProduksi ? `<button class="chip ${f.onlyUnpaid ? "active" : ""}" onclick="App.toggleBooleanFilter('onlyUnpaid')">Belum lunas</button>` : ""}
              <button class="btn btn-outline btn-sm" onclick="App.resetFilters()">Reset filter</button>
            </div>
          </div>

          <div class="chip-row">
            <span style="font-size:12px; font-weight:700; color:var(--teks-muted); align-self:center;">Layanan:</span>
            ${Store.services
              .map(
                (s) => `
              <button class="chip ${f.services.includes(s.name) ? "active" : ""}" onclick="App.toggleFilterArray('services', '${s.name}')">${s.name}</button>
            `,
              )
              .join("")}
          </div>
        </div>

        <!-- Tabel Daftar Pesanan -->
        <div class="card col-12">
          <div class="card-header">
            <span class="card-title">Menampilkan ${list.length} pesanan ${isProduksi ? "(Mode Produksi: Hanya Pesanan Aktif)" : ""}</span>
            <span style="font-size:12px; color:var(--teks-muted);">Klik baris untuk membuka Detail & Timeline</span>
          </div>

          ${
            list.length === 0
              ? `
            <div class="empty-state">
              <p style="font-weight:700; margin-bottom:8px;">Tidak ada pesanan yang cocok dengan filter ini.</p>
              <button class="btn btn-secondary btn-sm" onclick="App.resetFilters()">Reset semua filter</button>
            </div>
          `
              : `
            <div class="table-wrap">
              <table class="data-table">
                <thead>
                  <tr>
                    <th class="th-sortable" onclick="App.setSort('code')">Kode ↕</th>
                    <th>Pelanggan</th>
                    <th>Layanan</th>
                    <th>Berat/Item</th>
                    ${!isProduksi ? `<th class="th-sortable" onclick="App.setSort('total')">Total ↕</th>` : ""}
                    <th>Status</th>
                    ${!isProduksi ? `<th>Pembayaran</th>` : ""}
                    <th class="th-sortable" onclick="App.setSort('due')">Selesai ↕</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  ${list
                    .map(
                      (o) => `
                    <tr class="${o.terlambat ? "row-terlambat" : ""}" onclick="App.openDrawer('${o.code}')">
                      <td class="mono" style="font-weight:800;">${o.code}</td>
                      <td><strong>${o.customer}</strong></td>
                      <td>
                        ${Getters.orderSummaryService(o)}
                        ${o.kilat ? `<span class="badge badge-kilat" style="margin-left:5px;">KILAT</span>` : ""}
                      </td>
                      <td class="mono">${Getters.orderSummaryWeight(o)}</td>
                      ${!isProduksi ? `<td class="mono"><strong>${Getters.rp(Getters.orderTotal(o))}</strong></td>` : ""}
                      <td>${this.renderStatusBadge(o.status)}</td>
                      ${
                        !isProduksi
                          ? `
                        <td>
                          <span class="badge ${o.payment === "Lunas" ? "badge-lunas" : o.isReceivable ? "badge-piutang" : "badge-belum"}">
                            <span class="badge-dot"></span>${o.payment === "Lunas" ? `Lunas (${o.method || "Tunai"})` : o.isReceivable ? "Piutang" : "Belum bayar"}
                          </span>
                        </td>
                      `
                          : ""
                      }
                      <td><strong>${o.dueLabel}</strong></td>
                      <td onclick="event.stopPropagation()">
                        <div class="row-actions">
                          <button class="btn btn-outline btn-sm" onclick="App.openDrawer('${o.code}')">Detail</button>
                          <button class="btn btn-outline btn-sm" onclick="App.printOrder('${o.code}', 'nota')" title="Cetak Nota">${ICONS.printer}</button>
                          <button class="btn btn-outline btn-sm" onclick="App.printOrder('${o.code}', 'label')" title="Cetak Label">🏷️</button>
                        </div>
                      </td>
                    </tr>
                  `,
                    )
                    .join("")}
                </tbody>
              </table>
            </div>
          `
          }
        </div>
      </div>
    `;
  },

  /* ==========================================================================
     4. ANTREAN PRODUKSI (§4: Kode LG-Bertingkat 30px, Tanpa B-xxx, Undo 5 dtk)
     ========================================================================== */
  antrean() {
    const isKasir = Store.role === "Kasir";
    const kf = Store.filters.kanbanFilter;
    const cols = [
      { status: "Diterima", badge: "badge-diterima" },
      { status: "Dicuci", badge: "badge-dicuci" },
      { status: "Disetrika", badge: "badge-disetrika" },
    ];

    return `
      <div class="bento-grid">
        <div class="card col-12" style="padding:12px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
          <div class="chip-row">
            <span style="font-size:12.5px; font-weight:700; color:var(--teks-muted); align-self:center;">Filter antrean:</span>
            ${["Semua", "Kilat", "Terlambat"]
              .map(
                (m) => `
              <button class="chip ${kf === m ? "active" : ""}" onclick="App.setKanbanFilter('${m}')">${m}</button>
            `,
              )
              .join("")}
          </div>
          ${isKasir ? `<span class="badge badge-cadangan">Mode Kasir: Tombol ubah status bertindak sebagai Cadangan</span>` : ""}
        </div>

        ${cols
          .map((col) => {
            const list = Getters.activeProductionOrders().filter((o) => {
              if (o.status !== col.status) return false;
              if (kf === "Kilat" && !o.kilat) return false;
              if (kf === "Terlambat" && !o.terlambat) return false;
              return true;
            });

            return `
            <div class="card col-4">
              <div class="card-header">
                <span class="card-title">${col.status}</span>
                <span class="badge ${col.badge}"><span class="badge-dot"></span>${list.length} pesanan</span>
              </div>

              ${
                list.length === 0
                  ? `<div class="empty-state" style="padding:24px 0;">Kosong</div>`
                  : list
                      .map((o) => {
                        let nextBtn = "Mulai cuci";
                        if (col.status === "Dicuci") {
                          const isCuciKeringSaja =
                            o.lines.length === 1 &&
                            o.lines[0].name === "Cuci kering saja" &&
                            !o.kilat;
                          nextBtn = isCuciKeringSaja
                            ? "Tandai selesai"
                            : "Lanjut setrika";
                        } else if (col.status === "Disetrika") {
                          nextBtn = "Tandai selesai";
                        }
                        const urgent = o.terlambat || o.slaHoursLeft <= 2;

                        return `
                  <div class="kanban-ticket ${o.kilat ? "kilat" : ""} ${o.terlambat ? "terlambat" : ""}" onclick="App.openDrawer('${o.code}')">
                    <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:8px; gap:6px; flex-wrap:wrap;">
                      ${this.renderLgCodeHero(o.code)}
                      <div style="display:flex; gap:4px;">
                        ${o.kilat ? `<span class="badge badge-kilat"><span class="badge-dot"></span>KILAT</span>` : ""}
                        ${o.terlambat ? `<span class="badge badge-terlambat"><span class="badge-dot"></span>Terlambat</span>` : ""}
                      </div>
                    </div>

                    <div style="font-weight:800; font-size:15px;">${o.customer}</div>
                    <div style="font-size:12.5px; color:var(--teks-muted); margin:2px 0;">${Getters.orderSummaryService(o)} • ${Getters.orderSummaryWeight(o)}</div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:6px;">
                      <span style="font-size:12px; color:var(--teks-muted);">Selesai: <strong style="color:var(--teks);">${o.dueLabel}</strong></span>
                      <span class="sla-timer ${urgent ? "urgent" : ""}">⏱ ${o.remainingLabel}</span>
                    </div>

                    ${o.note ? `<div class="warning-note-pill">⚠️ ${o.note}</div>` : '<div style="height:10px;"></div>'}

                    <div onclick="event.stopPropagation()">
                      <button class="btn btn-primary w-full btn-sm" style="min-height:42px;" onclick="App.advanceStatusWithUndo('${o.code}')">
                        ${nextBtn}
                        ${isKasir ? `<span class="badge badge-cadangan" style="margin-left:6px; font-size:10px;">Cadangan</span>` : ""}
                      </button>
                    </div>
                  </div>
                `;
                      })
                      .join("")
              }
            </div>
          `;
          })
          .join("")}
      </div>
    `;
  },

  /* ==========================================================================
     5. CUCIAN SELESAI (P1, P2, P6, & Link Manual WA/Tel)
     ========================================================================== */
  selesai() {
    const isProduksi = Store.role === "Produksi";
    const doneOrders = Getters.doneOrdersSorted();
    const belumDikabari = doneOrders.filter((o) => !o.notified).length;
    const belumLunas = doneOrders.filter((o) => o.payment !== "Lunas").length;

    return `
      <div class="bento-grid">
        <div class="card col-4"><div style="font-size:13px; font-weight:700;">Belum dikabari</div><div class="mono" style="font-size:30px; font-weight:800; margin-top:6px;">${belumDikabari}</div></div>
        <div class="card col-4"><div style="font-size:13px; font-weight:700;">Menunggu diambil</div><div class="mono" style="font-size:30px; font-weight:800; margin-top:6px;">${doneOrders.length}</div></div>
        <div class="card col-4"><div style="font-size:13px; font-weight:700;">Belum lunas</div><div class="mono" style="font-size:30px; font-weight:800; margin-top:6px;">${belumLunas}</div></div>

        ${doneOrders
          .map((o) => {
            const total = Getters.orderTotal(o);
            const canHandover = o.payment === "Lunas" || o.isReceivable;
            return `
            <div class="card col-4" style="display:flex; flex-direction:column; justify-content:space-between; ${o.kilat ? "border-color:#F59E0B;" : ""}">
              <div>
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:6px; margin-bottom:10px;">
                  <strong class="mono" style="font-size:13.5px; cursor:pointer;" onclick="App.openDrawer('${o.code}')">${o.code}</strong>
                  <div style="display:flex; gap:5px; flex-wrap:wrap;">
                    ${o.kilat ? `<span class="badge badge-kilat">KILAT</span>` : ""}
                    <span class="badge ${o.notified ? "badge-lunas" : "badge-kilat"}"><span class="badge-dot"></span>${o.notified ? "Sudah dikabari" : "Belum dikabari"}</span>
                    ${!isProduksi ? `<span class="badge ${o.payment === "Lunas" ? "badge-lunas" : o.isReceivable ? "badge-piutang" : "badge-belum"}"><span class="badge-dot"></span>${o.payment === "Lunas" ? "Lunas" : o.isReceivable ? "Piutang" : "Belum bayar"}</span>` : ""}
                  </div>
                </div>

                <div style="font-size:16.5px; font-weight:800;">${o.customer}</div>
                <div style="font-size:12.5px; color:var(--teks-muted); margin-top:2px;">${Getters.orderSummaryService(o)} (${Getters.orderSummaryWeight(o)})</div>
                <div style="font-size:12px; font-weight:700; color:var(--utama); margin-top:4px;">⏳ Menunggu ${o.waitDays || 0} hari</div>
                ${!isProduksi ? `<div style="font-size:13.5px; margin:8px 0 12px;">Total <strong class="mono">${Getters.rp(total)}</strong></div>` : '<div style="height:12px;"></div>'}

                ${
                  !isProduksi
                    ? `
                  <div class="chip-row" style="margin-bottom:12px;">
                    <button class="btn btn-secondary btn-sm" onclick="App.contactCustomerManual('${o.code}', 'wa')">Chat WA</button>
                    <button class="btn btn-outline btn-sm" onclick="App.contactCustomerManual('${o.code}', 'tel')">Telepon</button>
                    ${!o.notified ? `<button class="btn btn-outline btn-sm" onclick="App.markNotified('${o.code}')">Tandai sudah dikabari</button>` : ""}
                    ${o.payment !== "Lunas" ? `<button class="btn btn-outline btn-sm" onclick="App.openPaymentModal('${o.code}')">Terima pembayaran</button>` : ""}
                    <button class="btn btn-outline btn-sm" onclick="App.printOrder('${o.code}', 'nota')" title="Cetak Nota">${ICONS.printer} Nota</button>
                    <button class="btn btn-outline btn-sm" onclick="App.printOrder('${o.code}', 'label')" title="Cetak Label">🏷️ Label</button>
                  </div>
                `
                    : ""
                }
              </div>

              ${
                !isProduksi
                  ? canHandover
                    ? `<button class="btn btn-primary w-full" onclick="App.handoverOrder('${o.code}')">Serahkan dan tandai diambil</button>`
                    : `<div style="display:flex; flex-direction:column; gap:6px;">
                      <button class="btn btn-disabled w-full" disabled>Selesaikan pembayaran dulu</button>
                      ${Store.role === "Admin" ? `<button class="btn btn-outline btn-sm w-full" onclick="App.openReceivableModal('${o.code}')">Izinkan ambil sebagai piutang (Admin)</button>` : ""}
                    </div>`
                  : `<button class="btn btn-outline w-full btn-sm" onclick="App.openDrawer('${o.code}')">Lihat detail ringkas</button>`
              }
            </div>
          `;
          })
          .join("")}
      </div>
    `;
  },

  /* ==========================================================================
     6. HALAMAN BARU: DATA PELANGGAN (§3: Cari, Tambah, Ubah, Riwayat Pesanan)
     ========================================================================== */
  pelanggan() {
    const q = (Store.filters.customerSearch || "").toLowerCase();
    const list = Store.customers.filter(
      (c) => c.name.toLowerCase().includes(q) || c.phone.includes(q),
    );

    return `
      <div class="bento-grid">
        <div class="card col-12" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
          <input type="text" class="input-field" style="max-width:340px;" placeholder="Cari nama atau nomor WA pelanggan..." value="${Store.filters.customerSearch}" oninput="App.filterCustomerPage(this.value)" />
          <button class="btn btn-primary" onclick="App.openCustomerModal()">+ Tambah pelanggan</button>
        </div>

        <div class="card col-12">
          <table class="data-table">
            <thead>
              <tr><th>Nama Pelanggan</th><th>Nomor WhatsApp</th><th>Jumlah Pesanan</th><th>Total Transaksi</th><th>Aksi</th></tr>
            </thead>
            <tbody>
              ${list
                .map((c) => {
                  const custOrders = Store.orders.filter(
                    (o) => o.customer === c.name && o.status !== "Dibatalkan",
                  );
                  const totalSpend = custOrders.reduce(
                    (s, o) => s + Getters.orderTotal(o),
                    0,
                  );
                  return `
                  <tr>
                    <td><strong>${c.name}</strong></td>
                    <td class="mono">${c.phone}</td>
                    <td class="mono">${custOrders.length} pesanan</td>
                    <td class="mono"><strong>${Getters.rp(totalSpend)}</strong></td>
                    <td>
                      <button class="btn btn-outline btn-sm" onclick="App.openCustomerHistoryModal('${c.name}')">Riwayat pesanan</button>
                      <button class="btn btn-outline btn-sm" onclick="App.openCustomerModal('${c.id}')">Ubah kontak</button>
                    </td>
                  </tr>
                `;
                })
                .join("")}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  /* ==========================================================================
     7. PENGELUARAN & LAPORAN KEUANGAN DINAMIS (P1 & P6)
     ========================================================================== */
  pengeluaran() {
    const cats = ["Semua", "Deterjen", "Plastik", "Listrik", "Lain-lain"];
    const filtered = Store.expenses.filter(
      (e) =>
        Store.filters.expCategory === "Semua" ||
        e.cat === Store.filters.expCategory,
    );
    const byCat = { Deterjen: 0, Plastik: 0, Listrik: 0, "Lain-lain": 0 };
    Store.expenses.forEach((e) => {
      byCat[e.cat] = (byCat[e.cat] || 0) + e.amount;
    });

    return `
      <div class="bento-grid">
        <div class="card col-5">
          <div class="card-header"><span class="card-title">Catat pengeluaran operasional</span></div>
          <button class="btn btn-primary w-full" onclick="App.openExpenseModal()">+ Tambah pengeluaran baru</button>
          <div style="margin-top:18px;">
            <div class="card-title" style="margin-bottom:10px;">Total per kategori</div>
            ${Object.entries(byCat)
              .map(
                ([k, v]) => `
              <div class="spec-row"><span>${k}</span><strong class="mono">${Getters.rp(v)}</strong></div>
            `,
              )
              .join("")}
          </div>
        </div>

        <div class="card col-7">
          <div class="card-header">
            <span class="card-title">Riwayat pengeluaran</span>
            <div class="chip-row">
              ${cats.map((c) => `<button class="chip ${Store.filters.expCategory === c ? "active" : ""}" onclick="App.setExpFilter('${c}')">${c}</button>`).join("")}
            </div>
          </div>
          ${filtered
            .map(
              (e) => `
            <div class="spec-row">
              <div>
                <strong>${e.cat}</strong> <span style="font-size:12px; color:var(--teks-muted);">(${e.date} • ${e.time})</span>
                <div style="font-size:12.5px; color:var(--teks-muted);">${e.note || "-"}</div>
              </div>
              <div style="display:flex; align-items:center; gap:8px;">
                <strong class="mono">${Getters.rp(e.amount)}</strong>
                <button class="btn btn-outline btn-sm" onclick="App.openExpenseModal('${e.id}')">Edit</button>
                <button class="btn btn-danger btn-sm" onclick="App.deleteExpense('${e.id}')">Hapus</button>
              </div>
            </div>
          `,
            )
            .join("")}
        </div>
      </div>
    `;
  },

  laporan() {
    const fin = Getters.financialMetrics();
    const rev = Math.max(fin.totalRevenue, 1);
    const pctTunai = Math.round((fin.byMethod["Tunai"] / rev) * 100);
    const pctEwallet = Math.round((fin.byMethod["E-Wallet"] / rev) * 100);
    const pctTransfer = Math.round((fin.byMethod["Transfer"] / rev) * 100);

    return `
      <div class="bento-grid">
        <div class="card col-12" style="padding:14px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
          <div class="chip-row">
            ${["Hari ini", "Minggu ini", "Bulan ini", "Custom"]
              .map(
                (p) => `
              <button class="chip ${Store.filters.reportPeriod === p ? "active" : ""}" onclick="App.setReportPeriod('${p}')">${p}</button>
            `,
              )
              .join("")}
          </div>
          <div style="display:flex; gap:10px;">
            <button class="btn btn-secondary" onclick="App.openExportModal()">Ekspor PDF atau Excel</button>
            <button class="btn btn-primary" onclick="App.openMonthEndReportModal()">Laporan akhir bulan</button>
          </div>
        </div>

        <div class="card card-gradient col-5">
          <div style="font-size:13.5px; font-weight:700; opacity:0.9;">Total pemasukan lunas (${Store.filters.reportPeriod})</div>
          <div class="mono" style="font-size:36px; font-weight:800; margin:16px 0;">${Getters.rp(fin.totalRevenue)}</div>
          <div style="font-size:13px; opacity:0.9;">Dihitung langsung dari transaksi pembayaran nyata.</div>
        </div>

        <div class="card col-3">
          <div class="card-title">Total pengeluaran</div>
          <div class="mono" style="font-size:26px; font-weight:800; margin-top:12px;">${Getters.rp(fin.totalExpense)}</div>
        </div>

        <div class="card col-4">
          <div class="card-title">Laba bersih</div>
          <div class="mono" style="font-size:26px; font-weight:800; color:var(--utama); margin-top:12px;">${Getters.rp(fin.netProfit)}</div>
          <div style="font-size:12px; color:var(--teks-muted); margin-top:6px;">Pemasukan lunas dikurangi pengeluaran</div>
        </div>

        <!-- P1: Pemasukan Per Metode Dinamis -->
        <div class="card col-7">
          <div class="card-header"><span class="card-title">Pemasukan per metode pembayaran (Real-time)</span></div>
          ${[
            {
              label: "Tunai",
              val: fin.byMethod["Tunai"],
              pct: pctTunai,
              color: "var(--utama)",
            },
            {
              label: "E-Wallet",
              val: fin.byMethod["E-Wallet"],
              pct: pctEwallet,
              color: "var(--aksen)",
            },
            {
              label: "Transfer bank",
              val: fin.byMethod["Transfer"],
              pct: pctTransfer,
              color: "#818CF8",
            },
          ]
            .map(
              (m) => `
            <div style="margin-bottom:14px;">
              <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:6px;">
                <span><strong>${m.label}</strong> <span style="color:var(--teks-muted);">${m.pct}%</span></span>
                <strong class="mono">${Getters.rp(m.val)}</strong>
              </div>
              <div style="height:8px; background:var(--bg-tint); border-radius:99px; overflow:hidden;">
                <div style="width:${m.pct}%; height:100%; background:${m.color};"></div>
              </div>
            </div>
          `,
            )
            .join("")}
        </div>

        <!-- P6: Tile Piutang (Hanya yang Diizinkan Admin sebagai Piutang) -->
        <div class="card col-5">
          <div class="card-header">
            <span class="card-title">Piutang disetujui Admin</span>
            <span class="badge badge-piutang">${fin.receivableOrders.length} pesanan</span>
          </div>
          <div class="mono" style="font-size:26px; font-weight:800; margin-bottom:10px;">${Getters.rp(fin.receivableTotal)}</div>
          ${
            fin.receivableOrders.length === 0
              ? `<div style="font-size:12.5px; color:var(--teks-muted);">Tidak ada piutang aktif.</div>`
              : fin.receivableOrders
                  .map(
                    (o) => `
            <div class="spec-row">
              <div>
                <strong class="mono">${o.code}</strong> • ${o.customer}
                <div style="font-size:11.5px; color:var(--teks-muted);">${Getters.rp(Getters.orderTotal(o))}</div>
              </div>
              <button class="btn btn-primary btn-sm" onclick="App.openPaymentModal('${o.code}')">Catat pelunasan</button>
            </div>
          `,
                  )
                  .join("")
          }
        </div>
      </div>
    `;
  },

  /* ==========================================================================
     8. LAYANAN & HARGA, PENGGUNA & LOG, PROFIL, & AKSES DIBATASI
     ========================================================================== */
  layanan() {
    const isAdmin = Store.role === "Admin";
    return `
      <div class="bento-grid">
        <div class="card col-12" style="display:flex; justify-content:space-between; align-items:center;">
          <span style="font-size:13.5px; color:var(--teks-muted);">
            ${isAdmin ? "Admin dapat mengubah harga dan status aktif layanan." : "Mode Kasir (Hanya Lihat): Hubungi Admin untuk mengubah tarif layanan."}
          </span>
        </div>

        ${Store.services
          .map(
            (s) => `
          <div class="card col-4">
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
              <span style="font-size:12.5px; font-weight:700;">Per ${s.unit}</span>
              <span class="badge ${s.active ? "badge-aktif" : "badge-batal"}"><span class="badge-dot"></span>${s.active ? "Aktif" : "Nonaktif"}</span>
            </div>
            <div style="font-size:16px; font-weight:700; margin-bottom:6px;">${s.name}</div>
            <div class="mono" style="font-size:26px; font-weight:800; margin-bottom:6px;">${Getters.rp(s.price)}/${s.unit}</div>
            <div style="font-size:12.5px; color:var(--teks-muted); margin-bottom:14px;">Estimasi selesai: ${s.kilat ? "Hari ini" : s.days + " hari"}</div>
            ${
              isAdmin
                ? `
              <div class="chip-row">
                <button class="btn btn-outline btn-sm" onclick="App.openEditServiceModal(${s.id})">Ubah harga</button>
                <button class="btn btn-outline btn-sm" onclick="App.toggleServiceActive(${s.id})">${s.active ? "Nonaktifkan" : "Aktifkan"}</button>
              </div>
            `
                : ""
            }
          </div>
        `,
          )
          .join("")}

        <div class="card col-4">
          <div class="card-header"><span class="card-title">Metode pembayaran</span></div>
          <div class="spec-row"><span>Tunai</span><span class="badge badge-aktif"><span class="badge-dot"></span>Aktif</span></div>
          <div class="spec-row"><span>E-Wallet</span><span class="badge badge-aktif"><span class="badge-dot"></span>Aktif</span></div>
          <div class="spec-row"><span>Transfer bank</span><span class="badge badge-aktif"><span class="badge-dot"></span>Aktif</span></div>
          <div class="spec-row"><span>QRIS</span><span class="badge badge-diterima"><span class="badge-dot"></span>Belum tersedia</span></div>
        </div>
      </div>
    `;
  },

  pengguna() {
    return `
      <div class="bento-grid">
        <div class="card col-7">
          <div class="card-header">
            <span class="card-title">Daftar pengguna sistem</span>
            <button class="btn btn-primary btn-sm" onclick="App.openUserModal()">+ Tambah pengguna</button>
          </div>
          <table class="data-table">
            <thead><tr><th>Nama</th><th>Username</th><th>Peran</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
              ${Store.users
                .map(
                  (u) => `
                <tr>
                  <td><strong>${u.name}</strong></td>
                  <td class="mono">${u.username}</td>
                  <td>${u.role}</td>
                  <td><span class="badge ${u.active ? "badge-aktif" : "badge-batal"}"><span class="badge-dot"></span>${u.active ? "Aktif" : "Nonaktif"}</span></td>
                  <td>
                    <div class="chip-row">
                      <button class="btn btn-outline btn-sm" onclick="App.openUserModal('${u.id}')">Ubah</button>
                      <button class="btn btn-outline btn-sm" onclick="App.resetUserPassword('${u.id}')">Reset password</button>
                      ${u.role !== "Admin" ? `<button class="btn btn-danger btn-sm" onclick="App.toggleUserActive('${u.id}')">${u.active ? "Nonaktifkan" : "Aktifkan"}</button>` : ""}
                    </div>
                  </td>
                </tr>
              `,
                )
                .join("")}
            </tbody>
          </table>
        </div>

        <div class="card col-5">
          <div class="card-header"><span class="card-title">Log aktivitas sistem</span></div>
          <div style="max-height:460px; overflow-y:auto;">
            ${Store.logs
              .map(
                (l) => `
              <div class="spec-row" style="justify-content:flex-start; gap:10px;">
                <span class="mono" style="font-size:11.5px; color:var(--teks-muted); flex-shrink:0;">${l.time}</span>
                <span style="font-size:12.5px;"><strong>${l.actor}</strong> ${l.text}</span>
              </div>
            `,
              )
              .join("")}
          </div>
        </div>
      </div>
    `;
  },

  profil() {
    const cfg = CONFIG.roles[Store.role];
    return `
      <div class="bento-grid">
        <div class="card col-6">
          <div class="card-header"><span class="card-title">Profil pengguna aktif</span></div>
          <div class="spec-row"><span>Nama lengkap</span><strong>${cfg.name}</strong></div>
          <div class="spec-row"><span>Peran akses</span><span class="badge badge-dicuci">${Store.role}</span></div>
          <div class="spec-row"><span>Outlet</span><strong>Adi Laundry, Karangploso</strong></div>
        </div>
        <div class="card col-6">
          <div class="card-header"><span class="card-title">Ganti password keamanan</span></div>
          <input type="password" id="prof-pass-new" class="input-field" style="margin-bottom:10px;" placeholder="Password baru (minimal 6 karakter)" />
          <button class="btn btn-primary" onclick="App.saveProfilePassword()">Perbarui password</button>
        </div>
      </div>
    `;
  },

  restricted() {
    return `
      <div class="card empty-state" style="max-width:520px; margin:40px auto;">
        <h3 style="font-size:20px; color:var(--teks); margin-bottom:8px;">Akses Dibatasi untuk Peran ${Store.role}</h3>
        <p style="font-size:13.5px; margin-bottom:18px;">Halaman ini hanya dapat diakses oleh pengguna dengan hak akses yang sesuai pada Matriks Peran Adi Laundry.</p>
        <button class="btn btn-primary" onclick="App.navigate('${CONFIG.roles[Store.role].defaultPage}')">Kembali ke halaman utama</button>
      </div>
    `;
  },

  /* ==========================================================================
     9. DRAWER KANAN: DETAIL PESANAN & TIMELINE (P3, P5, P6)
     ========================================================================== */
  drawer(order) {
    if (!order) return "";
    const isProduksi = Store.role === "Produksi";
    const isAdmin = Store.role === "Admin";
    const canEdit =
      (Store.role === "Kasir" && order.status === "Diterima") || isAdmin;
    const total = Getters.orderTotal(order);

    return `
      <div style="padding:20px 22px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
        <div>
          <div style="font-size:11.5px; font-weight:700; color:var(--teks-muted);">DETAIL PESANAN</div>
          <div class="mono" style="font-size:20px; font-weight:800;">${order.code}</div>
        </div>
        <button class="icon-btn" onclick="App.closeDrawer()">✕</button>
      </div>

      <div style="padding:20px 22px; overflow-y:auto; flex:1;">
        <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:14px;">
          ${this.renderStatusBadge(order.status)}
          ${order.kilat ? `<span class="badge badge-kilat">⚡ PRIORITAS KILAT</span>` : ""}
          ${order.terlambat ? `<span class="badge badge-terlambat">Terlambat</span>` : ""}
          ${order.isReceivable ? `<span class="badge badge-piutang">Piutang Disetujui</span>` : ""}
        </div>

        <div style="background:var(--bg-latar); padding:14px; border-radius:14px; margin-bottom:14px;">
          <div style="font-weight:800; font-size:16px;">${order.customer}</div>
          <div class="mono" style="font-size:12.5px; color:var(--teks-muted); margin-top:2px;">WA: ${order.phone}</div>
          <div style="font-size:12.5px; margin-top:6px;">Estimasi selesai: <strong>${order.dueLabel}</strong></div>
        </div>

        <div class="card-title" style="margin-bottom:8px;">Rincian layanan</div>
        ${order.lines
          .map(
            (l) => `
          <div class="spec-row">
            <span>${l.name} <strong class="mono">(${Getters.numFmt(l.qty)} ${l.unit})</strong></span>
            ${!isProduksi ? `<strong class="mono">${Getters.rp(l.qty * l.price)}</strong>` : ""}
          </div>
        `,
          )
          .join("")}

        ${order.note ? `<div class="warning-note-pill" style="width:100%;">Catatan: ${order.note}</div>` : ""}

        ${
          order.previousVersions && order.previousVersions.length > 0
            ? `
          <div style="margin:10px 0;">
            <button class="btn btn-outline btn-sm w-full" onclick="App.openVersionHistoryModal('${order.code}')">
              Lihat versi nota sebelumnya (${order.previousVersions.length})
            </button>
          </div>
        `
            : ""
        }

        ${
          !isProduksi
            ? `
          <div style="background:var(--bg-tint); padding:14px; border-radius:14px; margin:14px 0;">
            <div style="display:flex; justify-content:space-between; align-items:baseline;">
              <span>Total tagihan</span>
              <strong class="mono" style="font-size:20px;">${Getters.rp(total)}</strong>
            </div>
            <div style="font-size:12px; color:var(--teks-muted); margin-top:4px;">
              Status: <strong>${order.payment}</strong>
              ${order.method ? ` • Metode: <strong>${order.method}</strong> (${order.paidAt} oleh ${order.paidBy})` : ""}
              ${order.paymentRef ? ` • Ref: <span class="mono">${order.paymentRef}</span>` : ""}
            </div>
          </div>
        `
            : ""
        }

        <div class="card-title" style="margin:16px 0 8px;">Timeline riwayat status</div>
        <div class="timeline-list">
          ${order.timeline
            .map(
              (t) => `
            <div class="timeline-item">
              <div class="timeline-dot"></div>
              <div><strong>${t.status}</strong> <span class="mono" style="font-size:11px; color:var(--teks-muted);">${t.time}</span></div>
              <div style="color:var(--teks-muted); font-size:12px;">Oleh: ${t.actor} ${t.note ? `— "${t.note}"` : ""}</div>
            </div>
          `,
            )
            .join("")}
        </div>
      </div>

      <div style="padding:16px 22px; border-top:1px solid var(--border); display:flex; flex-direction:column; gap:8px;">
        <div class="chip-row">
          ${
            !isProduksi &&
            order.payment !== "Lunas" &&
            order.status !== "Dibatalkan"
              ? `
            <button class="btn btn-primary btn-sm" onclick="App.openPaymentModal('${order.code}')">Terima pembayaran</button>
          `
              : ""
          }
          <button class="btn btn-outline btn-sm" onclick="App.printOrder('${order.code}', 'nota')">${ICONS.printer} Cetak nota</button>
          <button class="btn btn-outline btn-sm" onclick="App.printOrder('${order.code}', 'label')">🏷️ Cetak label</button>
        </div>

        <div class="chip-row">
          ${canEdit && order.status !== "Dibatalkan" ? `<button class="btn btn-outline btn-sm" onclick="App.openEditOrderModal('${order.code}')">Edit pesanan</button>` : ""}
          ${isAdmin && ["Dicuci", "Disetrika", "Selesai"].includes(order.status) ? `<button class="btn btn-outline btn-sm" onclick="App.openRevertStatusModal('${order.code}')">↩ Mundur 1 tahap</button>` : ""}
          ${isAdmin && order.status !== "Dibatalkan" ? `<button class="btn btn-danger btn-sm" onclick="App.openCancelOrderModal('${order.code}')">Batalkan pesanan</button>` : ""}
        </div>
      </div>
    `;
  },

  /* ==========================================================================
     10. BOARD 2 (LAYAR MASUK) & BOARD 3 (SISTEM DESAIN DIPERBARUI)
     ========================================================================== */
  login() {
    return `
      <div class="login-grid">
        <div class="login-hero">
          <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:12px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; font-weight:800;">LG</div>
            <span style="font-weight:700; font-size:17px;">LaundryGo</span>
          </div>
          <div>
            <h2 style="font-size:44px; font-weight:800; line-height:1.12; margin-bottom:14px;">Cucian rapi, catatan rapi.</h2>
            <p style="font-size:15px; opacity:0.9;">Satu tempat untuk pesanan, antrean cuci, pembayaran, dan laporan omset Adi Laundry.</p>
          </div>
          <div class="chip-row">
            <span class="badge" style="background:rgba(255,255,255,0.16); color:#fff;">Nota & label satu kode LG-…</span>
            <span class="badge" style="background:rgba(255,255,255,0.16); color:#fff;">Rekap omset nyata</span>
          </div>
        </div>
        <div>
          <div class="login-card">
            <span class="badge badge-dicuci" style="margin-bottom:12px;"><span class="badge-dot"></span>Sistem kasir v3.0</span>
            <h3 style="font-size:24px; font-weight:800; margin-bottom:4px;">Masuk</h3>
            <p style="font-size:13px; color:var(--teks-muted); margin-bottom:18px;">Coba: <code>admin</code> (pass: <code>admin123</code>), <code>siti</code> (<code>123456</code>), atau <code>budi</code> (<code>123456</code> - wajib ganti password).</p>
            <div id="login-error" style="display:none; background:var(--bg-terlambat); color:var(--txt-terlambat); padding:10px 12px; border-radius:10px; font-size:12.5px; font-weight:700; margin-bottom:12px;"></div>
            <div style="margin-bottom:12px;">
              <label style="font-size:12px; font-weight:700; color:var(--teks-muted); display:block; margin-bottom:5px;">Username</label>
              <input type="text" id="login-username" class="input-field" value="siti" />
            </div>
            <div style="margin-bottom:18px;">
              <label style="font-size:12px; font-weight:700; color:var(--teks-muted); display:block; margin-bottom:5px;">Password</label>
              <input type="password" id="login-password" class="input-field" value="" placeholder="Masukkan password..." />
            </div>
            <button class="btn btn-primary w-full" id="btn-login-submit" onclick="App.handleLogin()">Masuk</button>
          </div>
        </div>
      </div>
    `;
  },

  ds() {
    const sampleOrder = Store.orders[0];
    return `
      <h1 style="font-size:32px; font-weight:800; margin-bottom:8px;">Sistem desain Fresh Aqua (v3 Revisi Final)</h1>
      <p style="font-size:14.5px; color:var(--teks-muted); margin-bottom:26px;">Modern minimalist dengan susunan bento grid, mendukung Dark Mode "Night Shift", komponen lencana lengkap, dan template cetak termal.</p>

      <div class="bento-grid">
        <div class="card col-12">
          <div class="card-header"><span class="card-title">Lencana Status & Peran Baru</span></div>
          <div class="chip-row">
            <span class="badge badge-diterima"><span class="badge-dot"></span>Diterima</span>
            <span class="badge badge-dicuci"><span class="badge-dot"></span>Dicuci</span>
            <span class="badge badge-disetrika"><span class="badge-dot"></span>Disetrika</span>
            <span class="badge badge-selesai"><span class="badge-dot"></span>Selesai / Lunas</span>
            <span class="badge badge-kilat"><span class="badge-dot"></span>KILAT</span>
            <span class="badge badge-terlambat"><span class="badge-dot"></span>Terlambat</span>
            <span class="badge badge-batal"><span class="badge-dot"></span>Dibatalkan</span>
            <span class="badge badge-cadangan">Cadangan (Kasir)</span>
            <span class="badge badge-piutang">Piutang Disetujui</span>
          </div>
        </div>

        <div class="card col-6">
          <div class="card-header"><span class="card-title">Pratinjau Cetak Label Cucian (58×40mm)</span></div>
          ${this.printLabelTemplate(sampleOrder)}
        </div>

        <div class="card col-6">
          <div class="card-header"><span class="card-title">Pratinjau Cetak Nota Struk (58/80mm)</span></div>
          ${this.printReceiptTemplate(sampleOrder)}
        </div>
      </div>
    `;
  },

  /* Template Cetak Label 58x40mm (§6: Satu Kode LG-YYYYMMDD-NNN, Kotak Hitam KILAT) */
  printLabelTemplate(o) {
    const { prefix, seq } = Getters.splitCode(o.code);
    return `
      <div class="print-label-box">
        <div class="print-label-code">
          <span class="prefix">${prefix}</span>
          <span class="seq">${seq}</span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
          <strong style="font-size:11pt;">${o.customer}</strong>
          ${o.kilat ? `<span class="print-kilat-box">KILAT</span>` : ""}
        </div>
        <div style="font-size:8.5pt;">${Getters.orderSummaryService(o)} • <strong>${Getters.orderSummaryWeight(o)}</strong></div>
        <div style="font-size:8.5pt;">Selesai: <strong>${o.dueLabel}</strong></div>
        ${o.note ? `<div style="font-size:8pt; border-top:1px dotted #000; margin-top:3px; padding-top:2px;">Cat: ${o.note}</div>` : ""}
      </div>
    `;
  },

  /* Template Cetak Nota Struk 58/80mm (§6) */
  printReceiptTemplate(o) {
    const total = Getters.orderTotal(o);
    return `
      <div class="print-receipt-box">
        <div style="text-align:center; border-bottom:1px dashed #000; padding-bottom:6px; margin-bottom:6px;">
          <strong style="font-size:11pt;">ADI LAUNDRY</strong><br/>
          Karangploso, Kab. Malang<br/>
          Kode: <strong>${o.code}</strong>
        </div>
        <div>Pelanggan : ${o.customer} (${o.phone})</div>
        <div>Est. Selesai: ${o.dueLabel} ${o.kilat ? "[KILAT]" : ""}</div>
        <div style="border-top:1px dashed #000; border-bottom:1px dashed #000; margin:6px 0; padding:4px 0;">
          ${o.lines
            .map(
              (l) => `
            <div style="display:flex; justify-content:space-between;">
              <span>${l.name} (${Getters.numFmt(l.qty)} ${l.unit})</span>
              <span>${Getters.rp(l.qty * l.price)}</span>
            </div>
          `,
            )
            .join("")}
        </div>
        <div style="display:flex; justify-content:space-between; font-weight:800; font-size:10pt;">
          <span>TOTAL</span><span>${Getters.rp(total)}</span>
        </div>
        <div>Status Bayar: <strong>${o.payment.toUpperCase()}</strong> ${o.method ? `(${o.method})` : ""}</div>
        ${o.note ? `<div>Catatan: ${o.note}</div>` : ""}
        <div style="text-align:center; margin-top:8px; font-size:8pt;">* Simpan nota ini untuk pengambilan *</div>
      </div>
    `;
  },
};
