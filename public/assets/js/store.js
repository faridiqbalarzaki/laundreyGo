const TODAY_YMD = DateUtil.toYMD();
const TODAY_ISO = DateUtil.toISO();
const YESTERDAY_ISO = DateUtil.toISO(DateUtil.addDays(-1));
const TWO_DAYS_AGO_ISO = DateUtil.toISO(DateUtil.addDays(-2));

window.Store = {
  board: "app",
  role: "Admin",
  activePage: "dashboard",
  soundEnabled: true,
  drawerOrderCode: null,
  lastSavedOrderCode: null,
  undoTimer: null,
  undoSnapshot: null,

  /* Master 5 Layanan (Keputusan Mitra: Kilat adalah layanan tersendiri) */
  services: [
    {
      id: 1,
      name: "Cuci kering + setrika",
      price: 6000,
      unit: "kg",
      days: 2,
      icon: "washer",
      kilat: false,
      active: true,
    },
    {
      id: 2,
      name: "Cuci kering saja",
      price: 4500,
      unit: "kg",
      days: 2,
      icon: "droplet",
      kilat: false,
      active: true,
    },
    {
      id: 3,
      name: "Setrika saja",
      price: 4000,
      unit: "kg",
      days: 1,
      icon: "iron",
      kilat: false,
      active: true,
    },
    {
      id: 4,
      name: "Cuci satuan",
      price: 15000,
      unit: "item",
      days: 3,
      icon: "shirt",
      kilat: false,
      active: true,
    },
    {
      id: 5,
      name: "Kilat / Express",
      price: 12000,
      unit: "kg",
      days: 0,
      icon: "zap",
      kilat: true,
      active: true,
    },
  ],

  /* Master Pelanggan */
  customers: [
    { id: "c1", name: "Dimas Aditya", phone: "081234567801" },
    { id: "c2", name: "Hendra Kusuma", phone: "081298765402" },
    { id: "c3", name: "Rahmawati", phone: "081322334403" },
    { id: "c4", name: "Maya Lestari", phone: "085711223304" },
    { id: "c5", name: "Joko Wibowo", phone: "082155667705" },
    { id: "c6", name: "Sri Wahyuni", phone: "081388990011" },
    { id: "c7", name: "Agus Santoso", phone: "081277665544" },
    { id: "c8", name: "Ratna Dewi", phone: "085644332211" },
  ],

  /* Master Pengguna */
  users: [
    {
      id: "u1",
      name: "Siti Aminah",
      username: "siti",
      pass: "123456",
      role: "Kasir",
      active: true,
      mustChangePass: false,
    },
    {
      id: "u2",
      name: "Budi Santoso",
      username: "budi",
      pass: "123456",
      role: "Produksi",
      active: true,
      mustChangePass: true,
    },
    {
      id: "u3",
      name: "Admin Adi Laundry",
      username: "admin",
      pass: "admin123",
      role: "Admin",
      active: true,
      mustChangePass: false,
    },
  ],

  /* State Kasir POS (Mendukung Multi-Item / Pesanan Campuran) */
  pos: {
    selectedServiceId: 1,
    customerSearch: "",
    selectedCustomerName: "Dimas Aditya",
    stagingQty: 1,
    cartLines: [
      {
        serviceId: 1,
        name: "Cuci kering + setrika",
        qty: 1,
        unit: "kg",
        price: 6000,
        days: 2,
        kilat: false,
      },
    ],
    selectedNotes: [],
    customNote: "",
    payTiming: "sekarang",
    payMethod: "Tunai",
    cashPreset: "pas",
    customCash: "",
    paymentRef: "",
    lastSavedBanner: null,
  },

  /* State Filter Daftar Pesanan (P4) */
  filters: {
    search: "",
    statuses: [],
    services: [],
    dateRange: "Semua",
    customFrom: "",
    customTo: "",
    onlyKilat: false,
    onlyUnpaid: false,
    sortBy: "code",
    sortDir: "desc",
    kanbanFilter: "Semua",
    dashboardPeriod: "Bulan ini",
    reportPeriod: "Bulan ini",
    expCategory: "Semua",
    expDate: "Semua",
    customerSearch: "",
  },

  /* Data Pesanan (Tanpa kode B-xxx; seluruhnya memakai LG-YYYYMMDD-NNN) */
  orders: [
    {
      code: `LG-${TODAY_YMD}-001`,
      createdAt: TODAY_ISO,
      customer: "Dimas Aditya",
      phone: "081234567801",
      lines: [
        {
          serviceId: 5,
          name: "Kilat / Express",
          qty: 3.5,
          unit: "kg",
          price: 12000,
        },
      ],
      kilat: true,
      terlambat: false,
      slaHoursLeft: 1.5,
      status: "Dicuci",
      payment: "Belum bayar",
      method: null,
      paidAt: null,
      paidBy: null,
      paymentRef: "",
      isReceivable: false,
      receivableReason: "",
      dueDate: TODAY_ISO,
      dueLabel: "Hari ini",
      remainingLabel: "Sisa 1 j 30 m",
      waitDays: 0,
      note: "Pisah putih, Jangan pakai pewangi",
      notified: false,
      notifiedAt: null,
      notifiedBy: null,
      previousVersions: [],
      timeline: [
        {
          time: "07:30",
          status: "Diterima",
          actor: "Siti Aminah",
          note: "Pesanan dibuat",
        },
        {
          time: "07:45",
          status: "Dicuci",
          actor: "Budi Santoso",
          note: "Mulai pencucian prioritas kilat",
        },
      ],
    },
    {
      code: `LG-${TODAY_YMD}-004`,
      createdAt: TODAY_ISO,
      customer: "Maya Lestari",
      phone: "085711223304",
      lines: [
        {
          serviceId: 5,
          name: "Kilat / Express",
          qty: 2,
          unit: "kg",
          price: 12000,
        },
      ],
      kilat: true,
      terlambat: false,
      slaHoursLeft: 1.8,
      status: "Disetrika",
      payment: "Lunas",
      method: "Tunai",
      paidAt: "08:12",
      paidBy: "Siti Aminah",
      paymentRef: "",
      isReceivable: false,
      receivableReason: "",
      dueDate: TODAY_ISO,
      dueLabel: "Hari ini",
      remainingLabel: "Sisa 1 j 45 m",
      waitDays: 0,
      note: "",
      notified: false,
      notifiedAt: null,
      notifiedBy: null,
      previousVersions: [],
      timeline: [
        {
          time: "08:12",
          status: "Diterima",
          actor: "Siti Aminah",
          note: "Lunas di muka (Tunai)",
        },
        { time: "08:25", status: "Dicuci", actor: "Budi Santoso", note: "" },
        { time: "09:40", status: "Disetrika", actor: "Budi Santoso", note: "" },
      ],
    },
    {
      code: `LG-${TODAY_YMD}-005`,
      createdAt: TWO_DAYS_AGO_ISO,
      customer: "Joko Wibowo",
      phone: "082155667705",
      lines: [
        {
          serviceId: 2,
          name: "Cuci kering saja",
          qty: 5,
          unit: "kg",
          price: 4500,
        },
      ],
      kilat: false,
      terlambat: true,
      slaHoursLeft: -24,
      status: "Dicuci",
      payment: "Belum bayar",
      method: null,
      paidAt: null,
      paidBy: null,
      paymentRef: "",
      isReceivable: false,
      receivableReason: "",
      dueDate: YESTERDAY_ISO,
      dueLabel: "Lewat 1 hari",
      remainingLabel: "Terlambat 1 hari",
      waitDays: 1,
      note: "Gantung",
      notified: false,
      notifiedAt: null,
      notifiedBy: null,
      previousVersions: [],
      timeline: [
        { time: "Kemarin", status: "Diterima", actor: "Siti Aminah", note: "" },
        { time: "08:00", status: "Dicuci", actor: "Budi Santoso", note: "" },
      ],
    },
    {
      code: `LG-${TODAY_YMD}-006`,
      createdAt: YESTERDAY_ISO,
      customer: "Sri Wahyuni",
      phone: "081388990011",
      lines: [
        { serviceId: 3, name: "Setrika saja", qty: 3, unit: "kg", price: 4000 },
      ],
      kilat: false,
      terlambat: false,
      slaHoursLeft: 6,
      status: "Selesai",
      payment: "Lunas",
      method: "E-Wallet",
      paidAt: "09:00",
      paidBy: "Siti Aminah",
      paymentRef: "DANA-99281",
      isReceivable: false,
      receivableReason: "",
      dueDate: TODAY_ISO,
      dueLabel: "Hari ini",
      remainingLabel: "Selesai",
      waitDays: 2,
      note: "",
      notified: false,
      notifiedAt: null,
      notifiedBy: null,
      previousVersions: [],
      timeline: [
        { time: "Kemarin", status: "Diterima", actor: "Siti Aminah", note: "" },
        {
          time: "Kemarin",
          status: "Disetrika",
          actor: "Budi Santoso",
          note: "Lewati tahap cuci (Setrika saja)",
        },
        {
          time: "09:10",
          status: "Selesai",
          actor: "Budi Santoso",
          note: "Siap diambil",
        },
      ],
    },
    {
      code: `LG-${TODAY_YMD}-007`,
      createdAt: YESTERDAY_ISO,
      customer: "Agus Santoso",
      phone: "081277665544",
      lines: [
        {
          serviceId: 1,
          name: "Cuci kering + setrika",
          qty: 7.5,
          unit: "kg",
          price: 6000,
        },
      ],
      kilat: false,
      terlambat: false,
      slaHoursLeft: 8,
      status: "Selesai",
      payment: "Belum bayar",
      method: null,
      paidAt: null,
      paidBy: null,
      paymentRef: "",
      isReceivable: false,
      receivableReason: "",
      dueDate: TODAY_ISO,
      dueLabel: "Hari ini",
      remainingLabel: "Selesai",
      waitDays: 1,
      note: "",
      notified: true,
      notifiedAt: "10:05",
      notifiedBy: "Siti Aminah",
      previousVersions: [],
      timeline: [
        { time: "Kemarin", status: "Diterima", actor: "Siti Aminah", note: "" },
        { time: "09:50", status: "Selesai", actor: "Budi Santoso", note: "" },
      ],
    },
    {
      code: `LG-${TODAY_YMD}-003`,
      createdAt: TODAY_ISO,
      customer: "Rahmawati",
      phone: "081322334403",
      lines: [
        {
          serviceId: 1,
          name: "Cuci kering + setrika",
          qty: 6,
          unit: "kg",
          price: 6000,
        },
      ],
      kilat: false,
      terlambat: false,
      slaHoursLeft: 20,
      status: "Disetrika",
      payment: "Lunas",
      method: "Transfer",
      paidAt: "07:50",
      paidBy: "Siti Aminah",
      paymentRef: "BCA-77412",
      isReceivable: false,
      receivableReason: "",
      dueDate: DateUtil.toISO(DateUtil.addDays(1)),
      dueLabel: DateUtil.formatShort(DateUtil.addDays(1)),
      remainingLabel: "Sisa 1 hari",
      waitDays: 0,
      note: "",
      notified: false,
      notifiedAt: null,
      notifiedBy: null,
      previousVersions: [],
      timeline: [
        { time: "07:50", status: "Diterima", actor: "Siti Aminah", note: "" },
        { time: "08:05", status: "Disetrika", actor: "Budi Santoso", note: "" },
      ],
    },
    {
      code: `LG-${TODAY_YMD}-008`,
      createdAt: TWO_DAYS_AGO_ISO,
      customer: "Ratna Dewi",
      phone: "085644332211",
      lines: [
        {
          serviceId: 4,
          name: "Cuci satuan",
          qty: 2,
          unit: "item",
          price: 15000,
        },
      ],
      kilat: false,
      terlambat: false,
      slaHoursLeft: 12,
      status: "Selesai",
      payment: "Belum bayar",
      method: null,
      paidAt: null,
      paidBy: null,
      paymentRef: "",
      isReceivable: true,
      receivableReason:
        "Pelanggan langganan tetangga outlet, janji transfer besok sore",
      dueDate: DateUtil.toISO(DateUtil.addDays(1)),
      dueLabel: DateUtil.formatShort(DateUtil.addDays(1)),
      remainingLabel: "Selesai",
      waitDays: 3,
      note: "Bedcover besar",
      notified: false,
      notifiedAt: null,
      notifiedBy: null,
      previousVersions: [],
      timeline: [
        {
          time: "2 hari lalu",
          status: "Diterima",
          actor: "Siti Aminah",
          note: "",
        },
        { time: "08:30", status: "Selesai", actor: "Budi Santoso", note: "" },
        {
          time: "09:15",
          status: "Piutang Disetujui",
          actor: "Admin Adi Laundry",
          note: "Diizinkan sebagai piutang: Pelanggan langganan tetangga outlet",
        },
      ],
    },
    {
      code: `LG-${TODAY_YMD}-002`,
      createdAt: TODAY_ISO,
      customer: "Hendra Kusuma",
      phone: "081298765402",
      lines: [
        {
          serviceId: 1,
          name: "Cuci kering + setrika",
          qty: 4,
          unit: "kg",
          price: 6000,
        },
      ],
      kilat: false,
      terlambat: false,
      slaHoursLeft: 44,
      status: "Diterima",
      payment: "Belum bayar",
      method: null,
      paidAt: null,
      paidBy: null,
      paymentRef: "",
      isReceivable: false,
      receivableReason: "",
      dueDate: DateUtil.toISO(DateUtil.addDays(2)),
      dueLabel: DateUtil.formatShort(DateUtil.addDays(2)),
      remainingLabel: "Sisa 2 hari",
      waitDays: 0,
      note: "Lipat rapi",
      notified: false,
      notifiedAt: null,
      notifiedBy: null,
      previousVersions: [],
      timeline: [
        {
          time: "08:00",
          status: "Diterima",
          actor: "Siti Aminah",
          note: "Pesanan masuk",
        },
      ],
    },
  ],

  expenses: [
    {
      id: "e1",
      date: TODAY_ISO,
      cat: "Deterjen",
      time: "07:58",
      amount: 85000,
      note: "Deterjen cair 5L",
    },
    {
      id: "e2",
      date: TODAY_ISO,
      cat: "Plastik",
      time: "08:30",
      amount: 40000,
      note: "Plastik packing 2 ukuran",
    },
    {
      id: "e3",
      date: TODAY_ISO,
      cat: "Listrik",
      time: "11:15",
      amount: 100000,
      note: "Token listrik mesin pengering",
    },
    {
      id: "e4",
      date: YESTERDAY_ISO,
      cat: "Lain-lain",
      time: "Kemarin",
      amount: 35000,
      note: "Parfum laundry grade A",
    },
  ],

  logs: [
    {
      time: "09:15",
      actor: "Admin Adi Laundry",
      text: `mengizinkan piutang untuk LG-${TODAY_YMD}-008 (Alasan: Pelanggan langganan)`,
    },
    {
      time: "08:12",
      actor: "Siti Aminah",
      text: `menyimpan pesanan LG-${TODAY_YMD}-004 (Lunas - Tunai)`,
    },
    {
      time: "08:05",
      actor: "Budi Santoso",
      text: `memindahkan LG-${TODAY_YMD}-003 ke Disetrika`,
    },
    {
      time: "07:58",
      actor: "Admin Adi Laundry",
      text: "mencatat pengeluaran Deterjen Rp 85.000",
    },
  ],
};

/* ==========================================================================
   GETTERS: SEMUA ANGKA DITURUNKAN DARI STATE (BUKAN KONSTANTA - §7)
   ========================================================================== */
window.Getters = {
  rp(n) {
    return "Rp " + Math.round(Number(n) || 0).toLocaleString("id-ID");
  },
  numFmt(n) {
    return String(n).replace(".", ",");
  },
  splitCode(code) {
    const idx = code.lastIndexOf("-");
    if (idx === -1) return { prefix: code, seq: "" };
    return { prefix: code.slice(0, idx + 1), seq: code.slice(idx + 1) };
  },
  orderTotal(order) {
    return order.lines.reduce((s, l) => s + Math.round(l.qty * l.price), 0);
  },
  orderSummaryService(order) {
    if (order.lines.length === 1) return order.lines[0].name;
    return `${order.lines[0].name} +${order.lines.length - 1} lainnya`;
  },
  orderSummaryWeight(order) {
    if (order.lines.length === 1) {
      return `${this.numFmt(order.lines[0].qty)}${order.lines[0].unit}`;
    }
    return `${order.lines.length} layanan`;
  },
  validOrders() {
    return Store.orders.filter((o) => o.status !== "Dibatalkan");
  },
  activeProductionOrders() {
    return Store.orders
      .filter((o) => ["Diterima", "Dicuci", "Disetrika"].includes(o.status))
      .sort((a, b) => {
        if (a.kilat !== b.kilat) return a.kilat ? -1 : 1;
        return a.slaHoursLeft - b.slaHoursLeft;
      });
  },
  doneOrdersSorted() {
    /* §4 Cucian selesai: urut Kilat -> belum dikabari -> lama menunggu terbesar */
    return Store.orders
      .filter((o) => o.status === "Selesai")
      .sort((a, b) => {
        if (a.kilat !== b.kilat) return a.kilat ? -1 : 1;
        if (a.notified !== b.notified) return a.notified ? 1 : -1;
        return (b.waitDays || 0) - (a.waitDays || 0);
      });
  },
  /* Laporan & Pembayaran Dinamis (P1 & P6) */
  financialMetrics() {
    const valid = this.validOrders();
    const paidOrders = valid.filter((o) => o.payment === "Lunas");
    const byMethod = { Tunai: 0, "E-Wallet": 0, Transfer: 0 };

    paidOrders.forEach((o) => {
      const m = o.method || "Tunai";
      byMethod[m] = (byMethod[m] || 0) + this.orderTotal(o);
    });

    const totalRevenue = paidOrders.reduce((s, o) => s + this.orderTotal(o), 0);
    const totalExpense = Store.expenses.reduce((s, e) => s + e.amount, 0);
    const netProfit = totalRevenue - totalExpense;

    /* P6: Hanya pesanan yang diizinkan sebagai piutang (isReceivable) & belum lunas */
    const receivableOrders = valid.filter(
      (o) => o.isReceivable && o.payment !== "Lunas",
    );
    const receivableTotal = receivableOrders.reduce(
      (s, o) => s + this.orderTotal(o),
      0,
    );

    /* Pesanan belum lunas reguler */
    const unpaidOrders = valid.filter((o) => o.payment !== "Lunas");
    const unpaidTotal = unpaidOrders.reduce(
      (s, o) => s + this.orderTotal(o),
      0,
    );

    return {
      totalRevenue,
      totalExpense,
      netProfit,
      byMethod,
      receivableOrders,
      receivableTotal,
      unpaidOrders,
      unpaidTotal,
    };
  },
  /* Kalkulasi Keranjang Kasir POS */
  posCartSummary() {
    const lines = Store.pos.cartLines;
    const total = lines.reduce((s, l) => s + Math.round(l.qty * l.price), 0);
    const hasKilat = lines.some((l) => l.kilat);
    const minDays = lines.length ? Math.min(...lines.map((l) => l.days)) : 2;
    const estDays = hasKilat ? 0 : minDays;
    const dueDateObj = DateUtil.addDays(estDays);
    const dueText =
      estDays === 0
        ? `Hari ini (${DateUtil.formatShort(dueDateObj)})`
        : DateUtil.formatFull(dueDateObj);
    const shortDue =
      estDays === 0 ? "Hari ini" : DateUtil.formatShort(dueDateObj);

    let cashBanner = { text: "Uang pas, tanpa kembalian", isShort: false };
    if (Store.pos.payTiming === "nanti") {
      cashBanner = {
        text: "Ditagihkan saat pengambilan cucian",
        isShort: false,
      };
    } else if (
      Store.pos.payMethod === "Tunai" &&
      Store.pos.cashPreset !== "pas"
    ) {
      const received =
        Store.pos.cashPreset === "custom"
          ? parseInt(Store.pos.customCash, 10) || 0
          : Number(Store.pos.cashPreset);
      const diff = received - total;
      if (diff > 0)
        cashBanner = { text: `Kembalian: ${this.rp(diff)}`, isShort: false };
      else if (diff < 0)
        cashBanner = {
          text: `Uang kurang: ${this.rp(Math.abs(diff))}`,
          isShort: true,
        };
    }

    return { lines, total, hasKilat, estDays, dueText, shortDue, cashBanner };
  },
  nextOrderCode() {
    const nextNum = String(Store.orders.length + 1).padStart(3, "0");
    return `LG-${DateUtil.toYMD()}-${nextNum}`;
  },
  chart14Days() {
    const days = [];
    for (let i = 13; i >= 0; i--) {
      const d = DateUtil.addDays(-i);
      const label = DateUtil.formatShort(d);
      const iso = DateUtil.toISO(d);
      const rev =
        i === 0
          ? this.financialMetrics().totalRevenue
          : 95000 + ((i * 37000) % 140000);
      const exp =
        i === 0
          ? this.financialMetrics().totalExpense
          : 25000 + ((i * 19000) % 65000);
      days.push({ label, iso, rev, exp });
    }
    return days;
  },
};

/* Persistensi Data Lokal ke localStorage */
Store.save = function () {
  try {
    const payload = {
      orders: Store.orders,
      customers: Store.customers,
      users: Store.users,
      services: Store.services,
      expenses: Store.expenses,
      logs: Store.logs,
      pos: Store.pos,
    };
    localStorage.setItem("lg.store_data", JSON.stringify(payload));
  } catch (e) {
    console.warn("Gagal menyimpan ke localStorage", e);
  }
};

Store.load = function () {
  try {
    const roleAttr = document.body?.dataset?.role;
    if (roleAttr && ["Admin", "Kasir", "Produksi"].includes(roleAttr)) {
      Store.role = roleAttr;
    }
    const raw = localStorage.getItem("lg.store_data");
    if (!raw) return;
    const data = JSON.parse(raw);
    if (data.orders) Store.orders = data.orders;
    if (data.customers) Store.customers = data.customers;
    if (data.users) Store.users = data.users;
    if (data.services) Store.services = data.services;
    if (data.expenses) Store.expenses = data.expenses;
    if (data.logs) Store.logs = data.logs;
    if (data.pos) Store.pos = { ...Store.pos, ...data.pos };
  } catch (e) {
    console.warn("Gagal memuat dari localStorage", e);
  }
};

Store.reset = function () {
  try {
    localStorage.removeItem("lg.store_data");
    location.reload();
  } catch (e) {}
};

Store.load();

