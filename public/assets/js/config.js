/* Helper Tanggal Dinamis berdasarkan new Date() (§7) */
window.DateUtil = {
  now() {
    return new Date();
  },
  toYMD(dateObj = new Date()) {
    const y = dateObj.getFullYear();
    const m = String(dateObj.getMonth() + 1).padStart(2, "0");
    const d = String(dateObj.getDate()).padStart(2, "0");
    return `${y}${m}${d}`;
  },
  toISO(dateObj = new Date()) {
    const y = dateObj.getFullYear();
    const m = String(dateObj.getMonth() + 1).padStart(2, "0");
    const d = String(dateObj.getDate()).padStart(2, "0");
    return `${y}-${m}-${d}`;
  },
  addDays(days, base = new Date()) {
    const d = new Date(base);
    d.setDate(d.getDate() + days);
    return d;
  },
  formatShort(dateObj) {
    const months = [
      "Jan",
      "Feb",
      "Mar",
      "Apr",
      "Mei",
      "Jun",
      "Jul",
      "Agu",
      "Sep",
      "Okt",
      "Nov",
      "Des",
    ];
    return `${dateObj.getDate()} ${months[dateObj.getMonth()]}`;
  },
  formatFull(dateObj) {
    const months = [
      "Jan",
      "Feb",
      "Mar",
      "Apr",
      "Mei",
      "Jun",
      "Jul",
      "Agu",
      "Sep",
      "Okt",
      "Nov",
      "Des",
    ];
    return `${dateObj.getDate()} ${months[dateObj.getMonth()]} ${dateObj.getFullYear()}`;
  },
  timeNow() {
    const d = new Date();
    return `${String(d.getHours()).padStart(2, "0")}:${String(d.getMinutes()).padStart(2, "0")}`;
  },
};

window.ICONS = {
  kasir: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 3h10a2 2 0 012 2v16l-3-2-3 2-3-2-3 2V5a2 2 0 012-2z"/><path d="M9 9h6M9 13h4"/></svg>`,
  dashboard: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>`,
  pesanan: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>`,
  antrean: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>`,
  selesai: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>`,
  pelanggan: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/></svg>`,
  pengeluaran: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 3v12m0 0l-4-4m4 4l4-4M4 19h16"/></svg>`,
  laporan: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 20V10m6 10V4m6 16v-6"/></svg>`,
  layanan: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><circle cx="7" cy="7" r="1.5"/></svg>`,
  pengguna: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>`,
  profil: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>`,
  washer: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="4" y="2" width="16" height="20" rx="2"/><circle cx="12" cy="13" r="4"/><path d="M8 6h.01M12 6h.01"/></svg>`,
  droplet: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2.69l5.66 5.66a8 8 0 11-11.31 0z"/></svg>`,
  iron: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 17h18l-2-7H9a6 6 0 00-6 7zM14 10V6a2 2 0 00-2-2H9"/></svg>`,
  shirt: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.38 3.46L16 2a4 4 0 01-8 0L3.62 3.46a2 2 0 00-1.34 2.23l.58 3.47a1 1 0 00.99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 002-2V10h2.15a1 1 0 00.99-.84l.58-3.47a2 2 0 00-1.34-2.23z"/></svg>`,
  zap: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>`,
  printer: `<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>`,
  check: `<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>`,
  soundOn: `<svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 010 7.07M19.07 4.93a10 10 0 010 14.14"/></svg>`,
  soundOff: `<svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/></svg>`,
};

/* Matriks Hak Akses §3 */
window.CONFIG = {
  roles: {
    Admin: {
      initials: "AL",
      name: "Admin Adi Laundry",
      subtitle: "Admin dan pemilik",
      defaultPage: "dashboard",
      allowedMenus: [
        "dashboard",
        "kasir",
        "pesanan",
        "antrean",
        "selesai",
        "pelanggan",
        "pengeluaran",
        "laporan",
        "layanan",
        "pengguna",
        "profil",
        "ds",
      ],
    },
    Kasir: {
      initials: "SA",
      name: "Siti Aminah",
      subtitle: "Kasir meja depan",
      defaultPage: "kasir",
      allowedMenus: ["kasir", "selesai", "pelanggan", "profil"],
    },
    Produksi: {
      initials: "BS",
      name: "Budi Santoso",
      subtitle: "Pegawai produksi",
      defaultPage: "antrean",
      allowedMenus: ["antrean", "pesanan", "selesai", "profil"],
    },
  },
  menuGroups: [
    {
      label: "Operasional",
      items: [
        { id: "kasir", label: "Kasir", icon: "kasir" },
        { id: "dashboard", label: "Dashboard", icon: "dashboard" },
        { id: "pesanan", label: "Daftar pesanan", icon: "pesanan" },
        { id: "antrean", label: "Antrean produksi", icon: "antrean" },
        {
          id: "selesai",
          label: "Cucian selesai",
          icon: "selesai",
          badge: true,
        },
        { id: "pelanggan", label: "Data pelanggan", icon: "pelanggan" },
      ],
    },
    {
      label: "Keuangan",
      items: [
        { id: "pengeluaran", label: "Pengeluaran", icon: "pengeluaran" },
        { id: "laporan", label: "Laporan keuangan", icon: "laporan" },
      ],
    },
    {
      label: "Pengaturan",
      items: [
        { id: "layanan", label: "Layanan dan harga", icon: "layanan" },
        { id: "pengguna", label: "Pengguna dan log", icon: "pengguna" },
        { id: "profil", label: "Profil & password", icon: "profil" },
        { id: "ds", label: "Sistem desain", icon: "desain" },
      ],
    },
  ],
  pages: {
    kasir: {
      title: "Kasir",
      subtitle: "Catat pesanan baru, hitung total, cetak nota dan label",
    },
    dashboard: {
      title: "Dashboard",
      subtitle: "Ringkasan omset, antrean produksi, dan cucian selesai",
    },
    pesanan: {
      title: "Daftar pesanan",
      subtitle: "Cari, filter multi-kriteria, dan buka detail pesanan",
    },
    antrean: {
      title: "Antrean produksi",
      subtitle: "Urut berdasarkan hari selesai, kilat di atas",
    },
    selesai: {
      title: "Cucian selesai",
      subtitle: "Kabari pelanggan manual, terima pembayaran, serahkan cucian",
    },
    pelanggan: {
      title: "Data pelanggan",
      subtitle:
        "Cari, tambah, ubah kontak, dan lihat riwayat pesanan pelanggan",
    },
    pengeluaran: {
      title: "Pengeluaran",
      subtitle: "Gantikan buku catatan terpisah dengan rekap per kategori",
    },
    laporan: {
      title: "Laporan keuangan",
      subtitle: "Omset nyata per metode bayar, pengeluaran, laba, dan piutang",
    },
    layanan: {
      title: "Layanan dan harga",
      subtitle: "Lima layanan utama Adi Laundry",
    },
    pengguna: {
      title: "Pengguna dan log",
      subtitle: "Atur peran pegawai dan lihat rekam jejak aktivitas",
    },
    profil: {
      title: "Profil & password",
      subtitle: "Kelola akun aktif dan perbarui password keamanan",
    },
    ds: {
      title: "Sistem Desain Fresh Aqua",
      subtitle: "Dokumentasi token, tipografi, komponen tombol, dan badge status",
    },
  },
  quickNotes: [
    "Pisah putih",
    "Jangan pakai pewangi",
    "Lipat rapi",
    "Gantung",
    "Noda di kerah",
  ],
};
