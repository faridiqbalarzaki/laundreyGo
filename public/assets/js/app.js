window.App = {
  init() {
    const roleAttr = document.body?.dataset?.role;
    if (roleAttr && CONFIG.roles[roleAttr]) {
      Store.role = roleAttr;
    }
    const mount = document.getElementById("page-mount");
    if (mount?.dataset?.page) {
      Store.activePage = mount.dataset.page;
    }
    this.refresh();
    this.bindShortcuts();
    window.addEventListener("popstate", (e) => {
      if (e.state && e.state.page) {
        Store.activePage = e.state.page;
        this.refresh();
      }
    });
  },

  /* Efek Suara Pop Halus via Web Audio API (§5) */
  playPop() {
    if (!Store.soundEnabled) return;
    try {
      const ctx = new (window.AudioContext || window.webkitAudioContext)();
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = "sine";
      osc.frequency.setValueAtTime(520, ctx.currentTime);
      osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.065);
      gain.gain.setValueAtTime(0.08, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.07);
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start();
      osc.stop(ctx.currentTime + 0.075);
    } catch (e) {}
  },

  switchBoard(board) {
    Store.board = board;
    document.querySelectorAll(".board-switcher button").forEach((b) => {
      b.classList.toggle("active", b.dataset.board === board);
    });
    document.getElementById("board-app").style.display =
      board === "app" ? "flex" : "none";
    document.getElementById("board-login").style.display =
      board === "login" ? "flex" : "none";
    document.getElementById("board-ds").style.display =
      board === "ds" ? "block" : "none";
    if (board === "login")
      document.getElementById("board-login").innerHTML = Views.login();
    if (board === "ds")
      document.getElementById("board-ds").innerHTML = Views.ds();
  },

  renderShell() {
    const roleCfg = CONFIG.roles[Store.role] || CONFIG.roles.Admin;
    const doneCount = Getters.doneOrdersSorted().length;
    const activeCount = Getters.activeProductionOrders().length;
    const todayCount = Getters.validOrders().length;

    const navEl = document.getElementById("sidebar-nav");
    if (navEl) {
      navEl.innerHTML = CONFIG.menuGroups
        .map((g) => {
          const items = g.items.filter((i) =>
            roleCfg.allowedMenus.includes(i.id),
          );
          if (!items.length) return "";
          return `
          <div class="nav-group">
            <div class="nav-label">${g.label}</div>
            ${items
              .map(
                (i) => `
              <button class="nav-item ${Store.activePage === i.id ? "active" : ""}" onclick="App.navigate('${i.id}')">
                <span class="nav-item-left">${ICONS[i.icon]} <span class="nav-item-text">${i.label}</span></span>
                ${i.badge ? `<span class="nav-badge" data-badge="selesai">${doneCount}</span>` : ""}
              </button>
            `,
              )
              .join("")}
          </div>
        `;
        })
        .join("");
    }

    const userEl = document.getElementById("sidebar-user");
    if (userEl) {
      userEl.innerHTML = `
        <div class="avatar" onclick="App.navigate('profil')">${roleCfg.initials}</div>
        <div class="sidebar-user-text" onclick="App.navigate('profil')">
          <div style="font-size:12.5px; font-weight:700;">${roleCfg.name}</div>
          <div style="font-size:11px; color:var(--teks-muted);">${roleCfg.subtitle}</div>
        </div>
      `;
    }

    const meta = CONFIG.pages[Store.activePage] || {
      title: "Akses Dibatasi",
      subtitle: "",
    };
    const titleEl = document.getElementById("page-title");
    if (titleEl) titleEl.textContent = meta.title;
    const subEl = document.getElementById("page-subtitle");
    if (subEl) subEl.textContent = meta.subtitle;

    const actPill = document.getElementById("pill-active-orders");
    if (actPill) actPill.textContent = `Pesanan aktif: ${activeCount}`;
    const tdPill = document.getElementById("pill-today-orders");
    if (tdPill) tdPill.textContent = `Hari ini: ${todayCount} pesanan`;

    const sndEl = document.getElementById("sound-icon");
    if (sndEl) {
      sndEl.innerHTML = Store.soundEnabled ? ICONS.soundOn : ICONS.soundOff;
    }

    const roleSw = document.getElementById("role-switcher");
    if (roleSw) {
      const roleName = Store.role || "Kasir";
      roleSw.innerHTML = `
        <span>Masuk sebagai</span>
        <span class="role-btn active" style="cursor:default; pointer-events:none;">${roleName}</span>
      `;
    }
  },

  renderView() {
    const roleCfg = CONFIG.roles[Store.role] || CONFIG.roles.Admin;
    const allowed = roleCfg.allowedMenus;
    if (!allowed.includes(Store.activePage)) {
      document.getElementById("app-view").innerHTML = Views.restricted();
      return;
    }
    const fn = Views[Store.activePage];
    if (fn) document.getElementById("app-view").innerHTML = fn.call(Views);
    if (Store.drawerOrderCode) {
      const ord = Store.orders.find((o) => o.code === Store.drawerOrderCode);
      if (ord)
        document.getElementById("order-drawer").innerHTML = Views.drawer(ord);
    }
  },

  refresh() {
    this.renderShell();
    this.renderView();
  },

  navigate(pageId) {
    Store.activePage = pageId;
    this.refresh();
    const pathMap = {
      kasir: "/kasir/pos",
      dashboard: "/admin/dashboard",
      pesanan: "/admin/pesanan",
      antrean: "/produksi/antrean",
      selesai: "/kasir/selesai",
      pelanggan: "/kasir/pelanggan",
      pengeluaran: "/admin/pengeluaran",
      laporan: "/admin/laporan",
      layanan: "/admin/layanan",
      pengguna: "/admin/pengguna",
      profil: "/profil",
      ds: "/desain",
    };
    const base = document.body?.dataset?.base || "";
    const targetPath = pathMap[pageId] ? (base + pathMap[pageId]) : null;
    if (targetPath && window.location.pathname !== targetPath) {
      try {
        history.pushState({ page: pageId }, "", targetPath);
      } catch (e) {}
    }
  },

  setRole(role) {
    // Role dikunci sesuai sesi login aktif
    return;
  },

  toggleTheme() {
    document.body.classList.toggle("dark");
  },

  toggleSound() {
    Store.soundEnabled = !Store.soundEnabled;
    if (Store.soundEnabled) this.playPop();
    this.renderShell();
  },

  log(text) {
    const actor = (CONFIG.roles[Store.role] || CONFIG.roles.Admin).name;
    Store.logs.unshift({
      time: DateUtil.timeNow(),
      actor,
      text,
    });
    if (typeof Store.save === "function") {
      Store.save();
    }
  },

  showToast(message, undoCallback = null) {
    const container = document.getElementById("toast-container");
    const id = "t-" + Date.now();
    container.innerHTML = `
      <div class="toast-item" id="${id}">
        <span>${message}</span>
        ${undoCallback ? `<button class="toast-undo-btn" onclick="App.triggerUndo()">Urungkan (5 dtk)</button>` : ""}
      </div>
    `;
    clearTimeout(Store.undoTimer);
    Store.undoTimer = setTimeout(() => {
      container.innerHTML = "";
      Store.undoSnapshot = null;
    }, 5000);
  },

  triggerUndo() {
    if (Store.undoSnapshot) {
      Store.orders = JSON.parse(Store.undoSnapshot);
      Store.undoSnapshot = null;
      document.getElementById("toast-container").innerHTML = "";
      this.refresh();
    }
  },

  /* Drawer & Modal Helpers */
  openDrawer(code) {
    Store.drawerOrderCode = code;
    const ord = Store.orders.find((o) => o.code === code);
    if (!ord) return;
    document.getElementById("order-drawer").innerHTML = Views.drawer(ord);
    document.getElementById("order-drawer").classList.add("open");
    document.getElementById("drawer-backdrop").classList.add("open");
  },

  closeDrawer() {
    Store.drawerOrderCode = null;
    document.getElementById("order-drawer").classList.remove("open");
    document.getElementById("drawer-backdrop").classList.remove("open");
  },

  openModal(html) {
    const bd = document.getElementById("modal-backdrop");
    document.getElementById("modal-content").innerHTML = html;
    bd.style.display = "flex";
    const firstInput = bd.querySelector("input, select, textarea, button");
    if (firstInput) firstInput.focus();
  },

  closeModal() {
    document.getElementById("modal-backdrop").style.display = "none";
  },

  /* Login Validation & First-Time Password Change (§4) */
  handleLogin() {
    const uInput = (document.getElementById("login-username").value || "")
      .trim()
      .toLowerCase();
    const pInput = document.getElementById("login-password").value || "";
    const errEl = document.getElementById("login-error");
    const btn = document.getElementById("btn-login-submit");

    if (!uInput || !pInput) {
      errEl.textContent = "Username dan password wajib diisi.";
      errEl.style.display = "block";
      return;
    }
    const user = Store.users.find((u) => u.username === uInput);
    if (!user) {
      errEl.textContent = "Akun tidak ditemukan.";
      errEl.style.display = "block";
      return;
    }
    if (!user.active) {
      errEl.textContent = "Akun tidak aktif. Hubungi Admin Adi Laundry.";
      errEl.style.display = "block";
      return;
    }
    if (user.pass !== pInput) {
      errEl.textContent = "Password salah. Silakan coba lagi.";
      errEl.style.display = "block";
      return;
    }

    btn.textContent = "Memuat sesi...";
    btn.disabled = true;
    setTimeout(() => {
      Store.role = user.role;
      Store.activePage = CONFIG.roles[user.role].defaultPage;
      this.switchBoard("app");
      this.refresh();
      if (user.mustChangePass) {
        this.openModal(`
          <h3 style="margin-bottom:8px;">Wajib Ganti Password Pertama Kali</h3>
          <p style="font-size:13px; color:var(--teks-muted); margin-bottom:14px;">Demi keamanan akun ${user.name}, silakan buat password baru sebelum mulai bekerja.</p>
          <input type="password" id="first-pass-input" class="input-field" style="margin-bottom:14px;" placeholder="Password baru..." />
          <button class="btn btn-primary w-full" onclick="App.completeFirstPass('${user.id}')">Simpan & Lanjutkan</button>
        `);
      }
    }, 320);
  },

  completeFirstPass(uid) {
    const val = document.getElementById("first-pass-input").value;
    if (!val || val.length < 4) return alert("Password minimal 4 karakter.");
    const u = Store.users.find((x) => x.id === uid);
    if (u) {
      u.pass = val;
      u.mustChangePass = false;
    }
    this.closeModal();
    this.showToast("Password berhasil diperbarui.");
  },

  /* Actions Kasir POS */
  selectPosService(id) {
    const svc = Store.services.find((s) => s.id === id);
    if (!svc || !svc.active)
      return this.showToast("Layanan ini sedang nonaktif.");
    Store.pos.selectedServiceId = id;
    if (Store.pos.cartLines.length === 1) {
      Store.pos.cartLines[0] = {
        serviceId: svc.id,
        name: svc.name,
        qty: Store.pos.stagingQty,
        unit: svc.unit,
        price: svc.price,
        days: svc.days,
        kilat: svc.kilat,
      };
    }
    this.renderView();
  },
  filterPosCustomer(val) {
    Store.pos.customerSearch = val;
    this.renderView();
  },
  selectPosCustomer(name) {
    Store.pos.selectedCustomerName = name;
    this.renderView();
  },
  setPosDirectQty(val) {
    const v = parseFloat(val);
    if (!isNaN(v) && v > 0) {
      Store.pos.stagingQty = v;
      if (Store.pos.cartLines.length > 0) {
        Store.pos.cartLines[Store.pos.cartLines.length - 1].qty = v;
      }
      this.updateReceiptPanel();
    }
  },

  updateReceiptPanel() {
    const el = document.querySelector(".receipt-card");
    if (el && typeof Views.receiptCard === "function") {
      el.outerHTML = Views.receiptCard();
    }
  },

  adjustPosQty(delta) {
    Store.pos.stagingQty = Math.max(
      0.1,
      Number((Store.pos.stagingQty + delta).toFixed(1)),
    );
    if (Store.pos.cartLines.length > 0) {
      Store.pos.cartLines[Store.pos.cartLines.length - 1].qty =
        Store.pos.stagingQty;
    }
    const input = document.getElementById("pos-staging-qty-input");
    if (input) input.value = Store.pos.stagingQty;
    this.updateReceiptPanel();
  },

  resetPosQty() {
    const svc =
      Store.services.find((s) => s.id === Store.pos.selectedServiceId) ||
      Store.services[0];
    Store.pos.stagingQty = 1;
    Store.pos.cartLines = [
      {
        serviceId: svc.id,
        name: svc.name,
        qty: 1,
        unit: svc.unit,
        price: svc.price,
        days: svc.days,
        kilat: svc.kilat,
      },
    ];
    const input = document.getElementById("pos-staging-qty-input");
    if (input) input.value = 1;
    this.updateReceiptPanel();
  },

  openNumpadModal() {
    this.openModal(`
      <h3 style="margin-bottom:10px;">Input Berat / Jumlah</h3>
      <input type="number" step="0.1" id="numpad-val" class="input-field mono" style="font-size:22px; margin-bottom:14px;" value="${Store.pos.stagingQty}" />
      <div style="display:flex; gap:8px;">
        <button class="btn btn-outline w-full" onclick="App.closeModal()">Batal</button>
        <button class="btn btn-primary w-full" onclick="App.applyNumpad()">Terapkan</button>
      </div>
    `);
  },

  applyNumpad() {
    const v = parseFloat(document.getElementById("numpad-val").value);
    if (!isNaN(v) && v > 0) {
      Store.pos.stagingQty = v;
      if (Store.pos.cartLines.length > 0)
        Store.pos.cartLines[Store.pos.cartLines.length - 1].qty = v;
    }
    this.closeModal();
    const input = document.getElementById("pos-staging-qty-input");
    if (input) input.value = Store.pos.stagingQty;
    this.updateReceiptPanel();
  },

  addServiceToCart() {
    const svc = Store.services.find(
      (s) => s.id === Store.pos.selectedServiceId,
    );
    Store.pos.cartLines.push({
      serviceId: svc.id,
      name: svc.name,
      qty: Store.pos.stagingQty,
      unit: svc.unit,
      price: svc.price,
      days: svc.days,
      kilat: svc.kilat,
    });
    this.playPop();
    this.updateReceiptPanel();
  },

  changeCartLineQty(idx, delta) {
    const line = Store.pos.cartLines[idx];
    if (!line) return;
    const next = Number((line.qty + delta).toFixed(1));
    if (next <= 0) {
      if (Store.pos.cartLines.length > 1) Store.pos.cartLines.splice(idx, 1);
    } else {
      line.qty = next;
    }
    if (idx === Store.pos.cartLines.length - 1) {
      Store.pos.stagingQty = line.qty;
      const input = document.getElementById("pos-staging-qty-input");
      if (input) input.value = Store.pos.stagingQty;
    }
    this.updateReceiptPanel();
  },

  togglePosNote(note) {
    const i = Store.pos.selectedNotes.indexOf(note);
    if (i >= 0) Store.pos.selectedNotes.splice(i, 1);
    else Store.pos.selectedNotes.push(note);
    this.renderView();
  },

  setPosPayTiming(t) {
    Store.pos.payTiming = t;
    this.updateReceiptPanel();
  },

  setPosPayMethod(m) {
    Store.pos.payMethod = m;
    this.updateReceiptPanel();
  },

  setPosCashPreset(p) {
    Store.pos.cashPreset = p;
    Store.pos.customCash = "";
    this.updateReceiptPanel();
  },

  setPosCustomCash(v) {
    Store.pos.customCash = v;
    Store.pos.cashPreset = v ? "custom" : "pas";
    const cart = Getters.posCartSummary();
    const banner = document.querySelector(".change-banner");
    if (banner) {
      banner.className = `change-banner ${cart.cashBanner.isShort ? "kurang" : ""}`;
      banner.style.background = cart.cashBanner.isShort ? "var(--bg-terlambat)" : "var(--bg-lunas)";
      banner.style.color = cart.cashBanner.isShort ? "var(--txt-terlambat)" : "var(--txt-lunas)";
      banner.textContent = cart.cashBanner.text;
    }
  },
  dismissPosBanner() {
    Store.pos.lastSavedBanner = null;
    this.renderView();
    const el = document.getElementById("pos-customer-search");
    if (el) el.focus();
  },

  submitPosOrder() {
    const cart = Getters.posCartSummary();
    if (Store.pos.payTiming === "sekarang" && cart.cashBanner.isShort) {
      return this.showToast("Nominal uang tunai yang diterima masih kurang.");
    }
    const code = Getters.nextOrderCode();
    const cust =
      Store.customers.find((c) => c.name === Store.pos.selectedCustomerName) ||
      Store.customers[0];
    const notes = [...Store.pos.selectedNotes, Store.pos.customNote]
      .filter(Boolean)
      .join(", ");
    const isPaid = Store.pos.payTiming === "sekarang";
    const actor = CONFIG.roles[Store.role].name;

    const newOrd = {
      code,
      createdAt: DateUtil.toISO(),
      customer: cust.name,
      phone: cust.phone,
      lines: JSON.parse(JSON.stringify(cart.lines)),
      kilat: cart.hasKilat,
      terlambat: false,
      slaHoursLeft: cart.hasKilat ? 4 : cart.estDays * 24,
      status: "Diterima",
      payment: isPaid ? "Lunas" : "Belum bayar",
      method: isPaid ? Store.pos.payMethod : null,
      paidAt: isPaid ? DateUtil.timeNow() : null,
      paidBy: isPaid ? actor : null,
      paymentRef: isPaid ? Store.pos.paymentRef : "",
      isReceivable: false,
      receivableReason: "",
      dueDate: DateUtil.toISO(DateUtil.addDays(cart.estDays)),
      dueLabel: cart.shortDue,
      remainingLabel: cart.hasKilat
        ? "Sisa 4 j 00 m"
        : `Sisa ${cart.estDays} hari`,
      waitDays: 0,
      note: notes,
      notified: false,
      previousVersions: [],
      timeline: [
        {
          time: DateUtil.timeNow(),
          status: "Diterima",
          actor,
          note: isPaid
            ? `Lunas di muka (${Store.pos.payMethod})`
            : "Bayar saat ambil",
        },
      ],
    };

    Store.orders.unshift(newOrd);
    Store.lastSavedOrderCode = code;
    Store.pos.lastSavedBanner = code;
    this.log(
      `menyimpan pesanan baru ${code} (${isPaid ? "Lunas - " + Store.pos.payMethod : "Belum bayar"})`,
    );
    this.playPop();

    // Reset keranjang POS
    const svc = Store.services[0];
    Store.pos.selectedServiceId = svc.id;
    Store.pos.stagingQty = 1;
    Store.pos.cartLines = [
      {
        serviceId: svc.id,
        name: svc.name,
        qty: 1,
        unit: svc.unit,
        price: svc.price,
        days: svc.days,
        kilat: svc.kilat,
      },
    ];
    Store.pos.selectedNotes = [];
    Store.pos.customNote = "";
    Store.pos.cashPreset = "pas";
    Store.pos.customCash = "";
    Store.pos.paymentRef = "";

    this.refresh();
    setTimeout(() => {
      const searchEl = document.getElementById("pos-customer-search");
      if (searchEl) searchEl.focus();
    }, 60);
  },

  /* P1: Panel Dialog Pembayaran Saat Ambil / Pelunasan Piutang */
  openPaymentModal(code) {
    const o = Store.orders.find((x) => x.code === code);
    if (!o) return;
    const total = Getters.orderTotal(o);
    this.openModal(`
      <h3 style="margin-bottom:4px;">Terima Pembayaran</h3>
      <p class="mono" style="font-size:13px; color:var(--teks-muted); margin-bottom:14px;">${o.code} • ${o.customer} • <strong>${Getters.rp(total)}</strong></p>

      <div style="font-size:12px; font-weight:700; margin-bottom:6px;">Pilih metode pembayaran</div>
      <div class="pay-method-grid" id="modal-pay-methods">
        <button class="chip active" onclick="App.selectModalPayMethod('Tunai', ${total}, this)">Tunai</button>
        <button class="chip" onclick="App.selectModalPayMethod('E-Wallet', ${total}, this)">E-Wallet</button>
        <button class="chip" onclick="App.selectModalPayMethod('Transfer', ${total}, this)">Transfer</button>
      </div>

      <div id="modal-pay-dynamic">
        <div class="chip-row" style="margin-bottom:8px;">
          <button class="chip" onclick="document.getElementById('modal-cash-in').value=${total}; App.calcModalChange(${total})">Uang pas</button>
          <button class="chip" onclick="document.getElementById('modal-cash-in').value=50000; App.calcModalChange(${total})">Rp 50.000</button>
          <button class="chip" onclick="document.getElementById('modal-cash-in').value=100000; App.calcModalChange(${total})">Rp 100.000</button>
        </div>
        <input type="number" id="modal-cash-in" class="input-field mono" value="${total}" oninput="App.calcModalChange(${total})" />
        <div id="modal-change-info" class="change-banner" style="margin-top:10px;">Uang pas, tanpa kembalian</div>
      </div>

      <input type="hidden" id="modal-selected-method" value="Tunai" />
      <div style="display:flex; gap:8px; margin-top:16px;">
        <button class="btn btn-outline w-full" onclick="App.closeModal()">Batal</button>
        <button class="btn btn-primary w-full" onclick="App.confirmPayment('${o.code}')">Simpan pelunasan</button>
      </div>
    `);
  },

  selectModalPayMethod(method, total, btn) {
    document.getElementById("modal-selected-method").value = method;
    btn.parentElement
      .querySelectorAll(".chip")
      .forEach((c) => c.classList.remove("active"));
    btn.classList.add("active");
    const dyn = document.getElementById("modal-pay-dynamic");
    if (method === "Tunai") {
      dyn.innerHTML = `
        <div class="chip-row" style="margin-bottom:8px;">
          <button class="chip" onclick="document.getElementById('modal-cash-in').value=${total}; App.calcModalChange(${total})">Uang pas</button>
          <button class="chip" onclick="document.getElementById('modal-cash-in').value=50000; App.calcModalChange(${total})">Rp 50.000</button>
          <button class="chip" onclick="document.getElementById('modal-cash-in').value=100000; App.calcModalChange(${total})">Rp 100.000</button>
        </div>
        <input type="number" id="modal-cash-in" class="input-field mono" value="${total}" oninput="App.calcModalChange(${total})" />
        <div id="modal-change-info" class="change-banner" style="margin-top:10px;">Uang pas, tanpa kembalian</div>
      `;
    } else {
      dyn.innerHTML = `<input type="text" id="modal-pay-ref" class="input-field mono" placeholder="Nomor referensi / bukti ${method} (opsional)" />`;
    }
  },

  calcModalChange(total) {
    const val =
      parseInt(document.getElementById("modal-cash-in").value, 10) || 0;
    const diff = val - total;
    const el = document.getElementById("modal-change-info");
    if (diff === 0) {
      el.textContent = "Uang pas, tanpa kembalian";
      el.classList.remove("kurang");
    } else if (diff > 0) {
      el.textContent = `Kembalian: ${Getters.rp(diff)}`;
      el.classList.remove("kurang");
    } else {
      el.textContent = `Uang kurang: ${Getters.rp(Math.abs(diff))}`;
      el.classList.add("kurang");
    }
  },

  confirmPayment(code) {
    const o = Store.orders.find((x) => x.code === code);
    if (!o) return;
    const method = document.getElementById("modal-selected-method").value;
    const refEl = document.getElementById("modal-pay-ref");
    const actor = CONFIG.roles[Store.role].name;

    o.payment = "Lunas";
    o.method = method;
    o.paidAt = DateUtil.timeNow();
    o.paidBy = actor;
    o.paymentRef = refEl ? refEl.value.trim() : "";
    o.timeline.push({
      time: o.paidAt,
      status: `Lunas (${method})`,
      actor,
      note: o.paymentRef ? `Ref: ${o.paymentRef}` : "Pembayaran diterima",
    });

    this.log(
      `menerima pembayaran ${code} sebesar ${Getters.rp(Getters.orderTotal(o))} via ${method}`,
    );
    this.playPop();
    this.closeModal();
    this.refresh();
    this.showToast(`Pembayaran ${code} (${method}) berhasil disimpan.`);
  },

  /* Maju Status Produksi dengan Urungkan 5 Detik (§4 & §5) */
  advanceStatusWithUndo(code) {
    Store.undoSnapshot = JSON.stringify(Store.orders);
    const o = Store.orders.find((x) => x.code === code);
    if (!o) return;
    const prev = o.status;
    const isSingleSetrika =
      o.lines.length === 1 && o.lines[0].name === "Setrika saja" && !o.kilat;
    const isSingleCuciKering =
      o.lines.length === 1 &&
      o.lines[0].name === "Cuci kering saja" &&
      !o.kilat;

    if (o.status === "Diterima")
      o.status = isSingleSetrika ? "Disetrika" : "Dicuci";
    else if (o.status === "Dicuci")
      o.status = isSingleCuciKering ? "Selesai" : "Disetrika";
    else if (o.status === "Disetrika") o.status = "Selesai";

    const actor = CONFIG.roles[Store.role].name;
    o.timeline.push({
      time: DateUtil.timeNow(),
      status: o.status,
      actor,
      note: Store.role === "Kasir" ? "Diubah via akses Cadangan Kasir" : "",
    });
    this.log(`memindahkan ${code} dari ${prev} ke ${o.status}`);
    this.playPop();
    this.refresh();
    this.showToast(`${code} dipindahkan ke ${o.status}`, true);
  },

  /* P6: Mundur Status 1 Tahap (Admin Saja, Wajib Alasan) */
  openRevertStatusModal(code) {
    this.openModal(`
      <h3 style="margin-bottom:6px;">Mundurkan Status Pesanan</h3>
      <p style="font-size:13px; color:var(--teks-muted); margin-bottom:12px;">Hanya Admin yang dapat memundurkan tahapan pesanan ${code}. Alasan wajib diisi untuk audit log.</p>
      <input type="text" id="revert-reason" class="input-field" style="margin-bottom:14px;" placeholder="Alasan wajib, mis. Salah tekan keranjang..." />
      <div style="display:flex; gap:8px;">
        <button class="btn btn-outline w-full" onclick="App.closeModal()">Batal</button>
        <button class="btn btn-primary w-full" onclick="App.confirmRevertStatus('${code}')">Mundurkan Status</button>
      </div>
    `);
  },

  confirmRevertStatus(code) {
    const reason = (
      document.getElementById("revert-reason").value || ""
    ).trim();
    if (!reason) return alert("Alasan wajib diisi!");
    const o = Store.orders.find((x) => x.code === code);
    if (!o) return;
    const mapBack = {
      Selesai: "Disetrika",
      Disetrika: "Dicuci",
      Dicuci: "Diterima",
    };
    const next = mapBack[o.status] || "Diterima";
    o.status = next;
    o.timeline.push({
      time: DateUtil.timeNow(),
      status: `Mundur ke ${next}`,
      actor: CONFIG.roles[Store.role].name,
      note: `Alasan: ${reason}`,
    });
    this.log(`memundurkan status ${code} ke ${next} (Alasan: ${reason})`);
    this.closeModal();
    this.refresh();
    this.showToast(`Status ${code} dimundurkan ke ${next}.`);
  },

  /* P6: Izinkan Serah Terima sebagai Piutang (Admin Saja, Wajib Alasan) */
  openReceivableModal(code) {
    this.openModal(`
      <h3 style="margin-bottom:6px;">Izinkan Ambil Sebagai Piutang</h3>
      <p style="font-size:13px; color:var(--teks-muted); margin-bottom:12px;">Pesanan ${code} belum lunas. Konfirmasi izin Admin agar cucian boleh diserahkan dan dicatat ke daftar Piutang Laporan Keuangan.</p>
      <input type="text" id="receivable-reason" class="input-field" style="margin-bottom:14px;" placeholder="Alasan wajib, mis. Pelanggan langganan bayar akhir pekan..." />
      <div style="display:flex; gap:8px;">
        <button class="btn btn-outline w-full" onclick="App.closeModal()">Batal</button>
        <button class="btn btn-primary w-full" onclick="App.confirmReceivable('${code}')">Setujui Piutang</button>
      </div>
    `);
  },

  confirmReceivable(code) {
    const reason = (
      document.getElementById("receivable-reason").value || ""
    ).trim();
    if (!reason) return alert("Alasan persetujuan piutang wajib diisi!");
    const o = Store.orders.find((x) => x.code === code);
    if (!o) return;
    o.isReceivable = true;
    o.receivableReason = reason;
    o.timeline.push({
      time: DateUtil.timeNow(),
      status: "Piutang Disetujui",
      actor: CONFIG.roles[Store.role].name,
      note: reason,
    });
    this.log(`menyetujui piutang ${code} (Alasan: ${reason})`);
    this.closeModal();
    this.refresh();
    this.showToast(`${code} diizinkan sebagai piutang.`);
  },

  /* P5: Edit & Batalkan Pesanan (Simpan Versi Sebelumnya) */
  openEditOrderModal(code) {
    const o = Store.orders.find((x) => x.code === code);
    if (!o) return;
    const needReason = o.status !== "Diterima";
    const firstLine = o.lines[0];
    this.openModal(`
      <h3 style="margin-bottom:6px;">Edit Pesanan ${o.code}</h3>
      <div style="margin-bottom:10px;">
        <label style="font-size:12px; font-weight:700;">Layanan Utama</label>
        <select id="edit-svc-id" class="input-field">
          ${Store.services.map((s) => `<option value="${s.id}" ${s.id === firstLine.serviceId ? "selected" : ""}>${s.name} (${Getters.rp(s.price)}/${s.unit})</option>`).join("")}
        </select>
      </div>
      <div style="margin-bottom:10px;">
        <label style="font-size:12px; font-weight:700;">Berat / Jumlah</label>
        <input type="number" step="0.5" id="edit-qty" class="input-field mono" value="${firstLine.qty}" />
      </div>
      <div style="margin-bottom:10px;">
        <label style="font-size:12px; font-weight:700;">Catatan Khusus</label>
        <input type="text" id="edit-note" class="input-field" value="${o.note}" />
      </div>
      ${
        needReason
          ? `
        <div style="margin-bottom:12px;">
          <label style="font-size:12px; font-weight:700; color:var(--coral-accent);">Alasan Perubahan (Wajib karena sudah diproses)</label>
          <input type="text" id="edit-reason" class="input-field" placeholder="Mis. Koreksi timbangan ulang..." />
        </div>
      `
          : ""
      }
      <div style="display:flex; gap:8px;">
        <button class="btn btn-outline w-full" onclick="App.closeModal()">Batal</button>
        <button class="btn btn-primary w-full" onclick="App.confirmEditOrder('${o.code}', ${needReason})">Simpan Perubahan</button>
      </div>
    `);
  },

  confirmEditOrder(code, needReason) {
    const o = Store.orders.find((x) => x.code === code);
    if (!o) return;
    const reasonEl = document.getElementById("edit-reason");
    const reason = reasonEl ? reasonEl.value.trim() : "Edit sebelum diproses";
    if (needReason && !reason) return alert("Alasan perubahan wajib diisi!");

    // Simpan snapshot versi sebelumnya (P5)
    o.previousVersions.push({
      savedAt: DateUtil.timeNow(),
      summary: `${Getters.orderSummaryService(o)} (${Getters.orderSummaryWeight(o)}) — ${Getters.rp(Getters.orderTotal(o))}`,
      note: o.note,
    });

    const svcId = Number(document.getElementById("edit-svc-id").value);
    const svc = Store.services.find((s) => s.id === svcId);
    const qty = parseFloat(document.getElementById("edit-qty").value) || 1;
    const note = document.getElementById("edit-note").value.trim();

    o.lines[0] = {
      serviceId: svc.id,
      name: svc.name,
      qty,
      unit: svc.unit,
      price: svc.price,
    };
    o.kilat = o.lines.some((l) => l.serviceId === 5);
    o.note = note;
    o.timeline.push({
      time: DateUtil.timeNow(),
      status: "Pesanan Diedit",
      actor: CONFIG.roles[Store.role].name,
      note: reason,
    });

    this.log(`mengedit pesanan ${code} (${reason})`);
    this.closeModal();
    this.refresh();
    this.showToast(`Perubahan pada ${code} disimpan.`);
  },

  openVersionHistoryModal(code) {
    const o = Store.orders.find((x) => x.code === code);
    if (!o) return;
    this.openModal(`
      <h3 style="margin-bottom:10px;">Riwayat Versi Nota ${o.code}</h3>
      ${o.previousVersions
        .map(
          (v, i) => `
        <div class="spec-row">
          <div>
            <strong>Versi #${i + 1} (${v.savedAt})</strong>
            <div style="font-size:12.5px;">${v.summary}</div>
            <div style="font-size:11.5px; color:var(--teks-muted);">Catatan: ${v.note || "-"}</div>
          </div>
        </div>
      `,
        )
        .join("")}
      <button class="btn btn-primary w-full" style="margin-top:14px;" onclick="App.closeModal()">Tutup</button>
    `);
  },

  openCancelOrderModal(code) {
    this.openModal(`
      <h3 style="margin-bottom:6px;">Batalkan Pesanan ${code}</h3>
      <p style="font-size:13px; color:var(--teks-muted); margin-bottom:12px;">Pesanan yang dibatalkan akan dikeluarkan dari antrean produksi dan perhitungan omset laporan.</p>
      <input type="text" id="cancel-reason" class="input-field" style="margin-bottom:14px;" placeholder="Alasan pembatalan wajib diisi..." />
      <div style="display:flex; gap:8px;">
        <button class="btn btn-outline w-full" onclick="App.closeModal()">Kembali</button>
        <button class="btn btn-danger w-full" onclick="App.confirmCancelOrder('${code}')">Batalkan Pesanan</button>
      </div>
    `);
  },

  confirmCancelOrder(code) {
    const reason = (
      document.getElementById("cancel-reason").value || ""
    ).trim();
    if (!reason) return alert("Alasan pembatalan wajib diisi!");
    const o = Store.orders.find((x) => x.code === code);
    if (!o) return;
    o.status = "Dibatalkan";
    o.timeline.push({
      time: DateUtil.timeNow(),
      status: "Dibatalkan",
      actor: CONFIG.roles[Store.role].name,
      note: `Alasan: ${reason}`,
    });
    this.log(`membatalkan pesanan ${code} (Alasan: ${reason})`);
    this.closeModal();
    this.closeDrawer();
    this.refresh();
    this.showToast(`Pesanan ${code} telah dibatalkan.`);
  },

  /* Komunikasi Manual WA / Tel & Serah Terima (§4) */
  contactCustomerManual(code, type) {
    const o = Store.orders.find((x) => x.code === code);
    if (!o) return;
    const total = Getters.rp(Getters.orderTotal(o));
    const cleanPhone = o.phone.replace(/\D/g, "").replace(/^0/, "62");
    const msg = encodeURIComponent(
      `Halo Kak ${o.customer}, cucian Anda di Adi Laundry dengan kode *${o.code}* sudah SELESAI dan siap diambil. Total tagihan: *${total}* (${o.payment}). Terima kasih!`,
    );

    if (type === "wa")
      window.open(`https://wa.me/${cleanPhone}?text=${msg}`, "_blank");
    else window.open(`tel:${o.phone}`, "_self");

    if (!o.notified) {
      this.openModal(`
        <h3 style="margin-bottom:8px;">Tandai Sudah Dikabari?</h3>
        <p style="font-size:13px; color:var(--teks-muted); margin-bottom:16px;">Apakah Anda sudah menghubungi ${o.customer} untuk pesanan ${o.code}?</p>
        <div style="display:flex; gap:8px;">
          <button class="btn btn-outline w-full" onclick="App.closeModal()">Belum</button>
          <button class="btn btn-primary w-full" onclick="App.markNotified('${o.code}'); App.closeModal();">Ya, Tandai Sudah Dikabari</button>
        </div>
      `);
    }
  },

  markNotified(code) {
    const o = Store.orders.find((x) => x.code === code);
    if (!o) return;
    o.notified = true;
    o.notifiedAt = DateUtil.timeNow();
    o.notifiedBy = CONFIG.roles[Store.role].name;
    o.timeline.push({
      time: o.notifiedAt,
      status: "Pelanggan Dikabari",
      actor: o.notifiedBy,
      note: "Dihubungi manual oleh kasir",
    });
    this.log(`menandai pelanggan ${o.customer} (${code}) sudah dikabari`);
    this.refresh();
  },

  handoverOrder(code) {
    const o = Store.orders.find((x) => x.code === code);
    if (!o) return;
    o.status = "Diambil";
    o.timeline.push({
      time: DateUtil.timeNow(),
      status: "Diambil",
      actor: CONFIG.roles[Store.role].name,
      note: "Cucian diserahkan ke pelanggan",
    });
    this.log(`menyerahkan cucian ${code} kepada ${o.customer}`);
    this.playPop();
    this.refresh();
    this.showToast(`Cucian ${code} telah diserahkan (Status: Diambil).`);
  },

  /* P2 & §6: Cetak Nota & Label */
  printOrder(code, mode = "nota") {
    const o = Store.orders.find((x) => x.code === code);
    if (!o) return;
    const html =
      mode === "label"
        ? Views.printLabelTemplate(o)
        : Views.printReceiptTemplate(o);
    document.getElementById("print-mount").innerHTML = html;
    this.log(`mencetak ulang ${mode} untuk ${code}`);

    this.openModal(`
      <h3 style="margin-bottom:10px;">Pratinjau Cetak ${mode === "label" ? "Label Cucian (58×40mm)" : "Nota Struk (58/80mm)"}</h3>
      <div style="background:#f8fafc; padding:14px; border-radius:12px; margin-bottom:14px;">${html}</div>
      <div style="display:flex; gap:8px;">
        <button class="btn btn-outline w-full" onclick="App.closeModal()">Tutup</button>
        <button class="btn btn-primary w-full" onclick="window.print()">${ICONS.printer} Cetak Sekarang</button>
      </div>
    `);
  },

  /* Filter & Sorting Handlers */
  handleGlobalSearch(val) {
    Store.filters.search = val;
    if (Store.activePage !== "pesanan") Store.activePage = "pesanan";
    this.refresh();
  },
  toggleFilterArray(key, val) {
    const arr = Store.filters[key];
    const i = arr.indexOf(val);
    if (i >= 0) arr.splice(i, 1);
    else arr.push(val);
    this.renderView();
  },
  toggleBooleanFilter(key) {
    Store.filters[key] = !Store.filters[key];
    this.renderView();
  },
  resetFilters() {
    Store.filters.search = "";
    Store.filters.statuses = [];
    Store.filters.services = [];
    Store.filters.onlyKilat = false;
    Store.filters.onlyUnpaid = false;
    document.getElementById("global-search").value = "";
    this.renderView();
  },
  setSort(col) {
    if (Store.filters.sortBy === col)
      Store.filters.sortDir = Store.filters.sortDir === "asc" ? "desc" : "asc";
    else {
      Store.filters.sortBy = col;
      Store.filters.sortDir = "asc";
    }
    this.renderView();
  },
  setKanbanFilter(m) {
    Store.filters.kanbanFilter = m;
    this.renderView();
  },
  setDashboardPeriod(p) {
    Store.filters.dashboardPeriod = p;
    this.renderView();
  },
  setReportPeriod(p) {
    Store.filters.reportPeriod = p;
    this.renderView();
  },
  setExpFilter(c) {
    Store.filters.expCategory = c;
    this.renderView();
  },
  filterCustomerPage(v) {
    Store.filters.customerSearch = v;
    this.renderView();
  },

  /* CRUD Pelanggan, Layanan (Aturan Kilat), Pengeluaran, & Pengguna */
  openCustomerModal(id = null) {
    const c = Store.customers.find((x) => x.id === id) || {
      name: "",
      phone: "0812",
    };
    this.openModal(`
      <h3 style="margin-bottom:10px;">${id ? "Ubah Data Pelanggan" : "Tambah Pelanggan Baru"}</h3>
      <input type="text" id="cust-modal-name" class="input-field" style="margin-bottom:10px;" placeholder="Nama lengkap pelanggan" value="${c.name}" />
      <input type="text" id="cust-modal-phone" class="input-field mono" style="margin-bottom:14px;" placeholder="Nomor WhatsApp" value="${c.phone}" />
      <div style="display:flex; gap:8px;">
        <button class="btn btn-outline w-full" onclick="App.closeModal()">Batal</button>
        <button class="btn btn-primary w-full" onclick="App.saveCustomer('${id || ""}')">Simpan</button>
      </div>
    `);
  },
  saveCustomer(id) {
    const name = document.getElementById("cust-modal-name").value.trim();
    const phone = document.getElementById("cust-modal-phone").value.trim();
    if (!name) return alert("Nama pelanggan wajib diisi.");
    if (id) {
      const c = Store.customers.find((x) => x.id === id);
      if (c) {
        c.name = name;
        c.phone = phone;
      }
      this.log(`mengubah kontak pelanggan ${name}`);
      this.showToast("Data pelanggan berhasil diperbarui.");
    } else {
      Store.customers.unshift({ id: "c" + Date.now(), name, phone });
      Store.pos.selectedCustomerName = name;
      this.log(`menambahkan pelanggan baru ${name}`);
      this.showToast("Pelanggan baru berhasil ditambahkan.");
    }
    if (typeof Store.save === "function") Store.save();
    this.closeModal();
    this.refresh();
  },
  openCustomerHistoryModal(name) {
    const list = Store.orders.filter((o) => o.customer === name);
    this.openModal(`
      <h3 style="margin-bottom:10px;">Riwayat Pesanan: ${name}</h3>
      ${
        list.length === 0
          ? `<p>Belum ada pesanan.</p>`
          : list
              .map(
                (o) => `
        <div class="spec-row">
          <div><strong class="mono">${o.code}</strong> •${Getters.orderSummaryService(o)}</div>
          <strong>${Getters.rp(Getters.orderTotal(o))}</strong>
        </div>
      `,
              )
              .join("")
      }
      <button class="btn btn-primary w-full" style="margin-top:14px;" onclick="App.closeModal()">Tutup</button>
    `);
  },

  /* Aturan Kilat: Tidak bisa dinonaktifkan jika masih ada pesanan Kilat aktif */
  toggleServiceActive(id) {
    const svc = Store.services.find((s) => s.id === id);
    if (!svc) return;
    if (svc.kilat && svc.active) {
      const hasActiveKilat = Store.orders.some(
        (o) =>
          o.kilat && ["Diterima", "Dicuci", "Disetrika"].includes(o.status),
      );
      if (hasActiveKilat) {
        return this.showToast(
          "Tidak dapat menonaktifkan Kilat / Express karena masih ada pesanan Kilat yang sedang aktif diproses.",
        );
      }
    }
    svc.active = !svc.active;
    this.log(
      `${svc.active ? "mengaktifkan" : "menonaktifkan"} layanan ${svc.name}`,
    );
    this.refresh();
  },

  openEditServiceModal(id) {
    const s = Store.services.find((x) => x.id === id);
    this.openModal(`
      <h3 style="margin-bottom:10px;">Ubah Layanan: ${s.name}</h3>
      <label style="font-size:12px; font-weight:700;">Harga per ${s.unit}</label>
      <input type="number" id="svc-price-in" class="input-field mono" style="margin-bottom:10px;" value="${s.price}" />
      ${
        !s.kilat
          ? `
        <label style="font-size:12px; font-weight:700;">Estimasi selesai (hari)</label>
        <input type="number" id="svc-days-in" class="input-field mono" style="margin-bottom:14px;" value="${s.days}" />
      `
          : ""
      }
      <div style="display:flex; gap:8px;">
        <button class="btn btn-outline w-full" onclick="App.closeModal()">Batal</button>
        <button class="btn btn-primary w-full" onclick="App.saveServiceEdit(${s.id})">Simpan</button>
      </div>
    `);
  },
  saveServiceEdit(id) {
    const s = Store.services.find((x) => x.id === id);
    s.price = Number(document.getElementById("svc-price-in").value) || s.price;
    const dEl = document.getElementById("svc-days-in");
    if (dEl) s.days = Number(dEl.value) || s.days;
    this.log(
      `mengubah tarif layanan ${s.name} menjadi ${Getters.rp(s.price)}/${s.unit}`,
    );
    this.closeModal();
    this.refresh();
  },

  openExpenseModal(id = null) {
    const e = Store.expenses.find((x) => x.id === id) || {
      cat: "Deterjen",
      amount: 50000,
      note: "",
    };
    this.openModal(`
      <h3 style="margin-bottom:10px;">${id ? "Edit Pengeluaran" : "Catat Pengeluaran"}</h3>
      <select id="exp-modal-cat" class="input-field" style="margin-bottom:10px;">
        ${["Deterjen", "Plastik", "Listrik", "Lain-lain"].map((c) => `<option value="${c}" ${e.cat === c ? "selected" : ""}>${c}</option>`).join("")}
      </select>
      <input type="number" id="exp-modal-amt" class="input-field mono" style="margin-bottom:10px;" value="${e.amount}" />
      <input type="text" id="exp-modal-note" class="input-field" style="margin-bottom:14px;" placeholder="Keterangan..." value="${e.note}" />
      <div style="display:flex; gap:8px;">
        <button class="btn btn-outline w-full" onclick="App.closeModal()">Batal</button>
        <button class="btn btn-primary w-full" onclick="App.saveExpenseModal('${id || ""}')">Simpan</button>
      </div>
    `);
  },
  saveExpenseModal(id) {
    const cat = document.getElementById("exp-modal-cat").value;
    const amount = Number(document.getElementById("exp-modal-amt").value) || 0;
    const note = document.getElementById("exp-modal-note").value.trim();
    if (amount <= 0) return;
    if (id) {
      const e = Store.expenses.find((x) => x.id === id);
      if (e) {
        e.cat = cat;
        e.amount = amount;
        e.note = note;
      }
      this.log(
        `mengubah catatan pengeluaran ${cat} menjadi ${Getters.rp(amount)}`,
      );
    } else {
      Store.expenses.unshift({
        id: "e" + Date.now(),
        date: DateUtil.toISO(),
        cat,
        time: DateUtil.timeNow(),
        amount,
        note,
      });
      this.log(`mencatat pengeluaran ${cat} ${Getters.rp(amount)}`);
    }
    this.closeModal();
    this.refresh();
  },
  deleteExpense(id) {
    const e = Store.expenses.find((x) => x.id === id);
    if (!e) return;
    Store.expenses = Store.expenses.filter((x) => x.id !== id);
    this.log(`menghapus pengeluaran ${e.cat} ${Getters.rp(e.amount)}`);
    this.refresh();
  },

  openExportModal() {
    this.openModal(`
      <h3 style="margin-bottom:8px;">Ekspor Laporan Keuangan</h3>
      <p style="font-size:13px; color:var(--teks-muted); margin-bottom:14px;">Pilih format dokumen laporan periode ${Store.filters.reportPeriod}:</p>
      <div style="display:flex; gap:8px;">
        <button class="btn btn-secondary w-full" onclick="App.closeModal(); App.showToast('Laporan PDF berhasil diunduh.');">📄 Unduh PDF</button>
        <button class="btn btn-primary w-full" onclick="App.closeModal(); App.showToast('Laporan Excel (.xlsx) berhasil diunduh.');">📊 Unduh Excel</button>
      </div>
    `);
  },

  openMonthEndReportModal() {
    const fin = Getters.financialMetrics();
    this.openModal(`
      <h3 style="margin-bottom:8px;">Rekap Laporan Akhir Bulan Adi Laundry</h3>
      <div class="spec-row"><span>Total Pemasukan Lunas</span><strong class="mono">${Getters.rp(fin.totalRevenue)}</strong></div>
      <div class="spec-row"><span>• Tunai</span><span class="mono">${Getters.rp(fin.byMethod["Tunai"])}</span></div>
      <div class="spec-row"><span>• E-Wallet</span><span class="mono">${Getters.rp(fin.byMethod["E-Wallet"])}</span></div>
      <div class="spec-row"><span>• Transfer Bank</span><span class="mono">${Getters.rp(fin.byMethod["Transfer"])}</span></div>
      <div class="spec-row"><span>Total Pengeluaran</span><strong class="mono">${Getters.rp(fin.totalExpense)}</strong></div>
      <div class="spec-row"><span>Laba Bersih</span><strong class="mono" style="color:var(--utama);">${Getters.rp(fin.netProfit)}</strong></div>
      <div style="display:flex; gap:8px; margin-top:16px;">
        <button class="btn btn-outline w-full" onclick="App.closeModal()">Tutup</button>
        <button class="btn btn-primary w-full" onclick="window.print()">${ICONS.printer} Cetak Laporan</button>
      </div>
    `);
  },

  openUserModal(id = null) {
    const u = Store.users.find((x) => x.id === id) || {
      name: "",
      username: "",
      role: "Kasir",
    };
    this.openModal(`
      <h3 style="margin-bottom:10px;">${id ? "Ubah Pengguna" : "Tambah Pegawai Baru"}</h3>
      <input type="text" id="usr-name" class="input-field" style="margin-bottom:10px;" placeholder="Nama lengkap" value="${u.name}" />
      <input type="text" id="usr-uname" class="input-field mono" style="margin-bottom:10px;" placeholder="Username login" value="${u.username}" />
      <select id="usr-role" class="input-field" style="margin-bottom:14px;">
        ${["Kasir", "Produksi", "Admin"].map((r) => `<option value="${r}" ${u.role === r ? "selected" : ""}>${r}</option>`).join("")}
      </select>
      <div style="display:flex; gap:8px;">
        <button class="btn btn-outline w-full" onclick="App.closeModal()">Batal</button>
        <button class="btn btn-primary w-full" onclick="App.saveUser('${id || ""}')">Simpan</button>
      </div>
    `);
  },
  saveUser(id) {
    const name = document.getElementById("usr-name").value.trim();
    const username = document
      .getElementById("usr-uname")
      .value.trim()
      .toLowerCase();
    const role = document.getElementById("usr-role").value;
    if (!name || !username) return;
    if (id) {
      const u = Store.users.find((x) => x.id === id);
      if (u) {
        u.name = name;
        u.username = username;
        u.role = role;
      }
    } else {
      Store.users.push({
        id: "u" + Date.now(),
        name,
        username,
        pass: "123456",
        role,
        active: true,
        mustChangePass: true,
      });
    }
    this.log(`memperbarui data pengguna ${name} (${role})`);
    this.closeModal();
    this.refresh();
  },
  resetUserPassword(id) {
    const u = Store.users.find((x) => x.id === id);
    if (!u) return;
    u.pass = "123456";
    u.mustChangePass = true;
    this.log(`mereset password akun ${u.username}`);
    this.showToast(
      `Password ${u.name} direset ke 123456 (Wajib ganti saat login).`,
    );
  },
  toggleUserActive(id) {
    const u = Store.users.find((x) => x.id === id);
    if (!u) return;
    u.active = !u.active;
    this.log(
      `${u.active ? "mengaktifkan" : "menonaktifkan"} akun ${u.name} (riwayat transaksi tetap tersimpan)`,
    );
    this.refresh();
  },
  saveProfilePassword() {
    const val = document.getElementById("prof-pass-new").value;
    if (!val || val.length < 6)
      return this.showToast("Password minimal 6 karakter.");
    this.showToast("Password akun Anda berhasil diperbarui.");
  },

  /* Bantuan Shortcut Keyboard (?) & Keydown Listener (§5) */
  openShortcutModal() {
    this.openModal(`
      <h3 style="margin-bottom:10px;">Pintasan Keyboard LaundryGo</h3>
      <div class="spec-row"><span>Fokus pencarian cepat</span><span class="kbd-tag">F1 atau /</span></div>
      <div class="spec-row"><span>Pilih layanan 1–5 di Kasir</span><span class="kbd-tag">1 – 5</span></div>
      <div class="spec-row"><span>Tambah pelanggan baru</span><span class="kbd-tag">N</span></div>
      <div class="spec-row"><span>Simpan & cetak nota Kasir</span><span class="kbd-tag">Enter</span></div>
      <div class="spec-row"><span>Cetak ulang nota terakhir</span><span class="kbd-tag">P</span></div>
      <div class="spec-row"><span>Tutup drawer / dialog</span><span class="kbd-tag">Esc</span></div>
      <button class="btn btn-primary w-full" style="margin-top:14px;" onclick="App.closeModal()">Mengerti</button>
    `);
  },

  bindShortcuts() {
    window.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        this.closeModal();
        this.closeDrawer();
        return;
      }
      if (e.key === "F1") {
        e.preventDefault();
        document.getElementById("global-search").focus();
        return;
      }
      const tag = document.activeElement?.tagName;
      if (["INPUT", "TEXTAREA", "SELECT"].includes(tag)) return;

      if (e.key === "/") {
        e.preventDefault();
        document.getElementById("global-search").focus();
      } else if (e.key === "?") {
        e.preventDefault();
        this.openShortcutModal();
      } else if (e.key.toLowerCase() === "p") {
        e.preventDefault();
        const code = Store.lastSavedOrderCode || Store.orders[0]?.code;
        if (code) this.printOrder(code, "nota");
      } else if (Store.activePage === "kasir") {
        if (["1", "2", "3", "4", "5"].includes(e.key))
          this.selectPosService(Number(e.key));
        else if (e.key.toLowerCase() === "n") {
          e.preventDefault();
          this.openCustomerModal();
        } else if (e.key === "Enter") {
          e.preventDefault();
          this.submitPosOrder();
        }
      }
    });
  },
};

document.addEventListener("DOMContentLoaded", () => App.init());
