# 🧺 LaundryGo Aqua — Web Aplikasi Kasir & Operasional Laundry

> **100% PHP Native • Pure CSS (Tanpa Framework) • Database PostgreSQL (PDO pgsql)**  
> Didesain dengan arsitektur **MVC Ringan & Bersih (Clean Code, Anti-Spaghetti)** yang sangat mudah dibaca, dipahami, dan dipresentasikan untuk mahasiswa semester 3.

---

## 📋 Daftar Isi
1. [Tentang Proyek](#tentang-proyek)
2. [Fitur Unggulan Sistem](#fitur-unggulan-sistem)
3. [Akun Login Bawaan (Demo)](#akun-login-bawaan-demo)
4. [Persyaratan & Cara Menjalankan](#persyaratan--cara-menjalankan)
5. [Skema Database (ERD PostgreSQL)](#skema-database-erd-postgresql)
6. [Struktur Folder & Penjelasan Kode](#struktur-folder--penjelasan-kode)
7. [Pola Routing URL (Semester 3 Friendly)](#pola-routing-url-semester-3-friendly)

---

## 🎯 Tentang Proyek
Aplikasi ini dikembangkan untuk mendigitalkan seluruh siklus bisnis pada **Adi Laundry (Karangploso, Malang)**, mulai dari kasir penerimaan pakaian, alur kerja operator mesin & setrika uap di lantai produksi, penataan di rak penyimpanan, hingga penyerahan cucian ke pelanggan dan laporan keuangan berkala.

Tampilan antarmuka (UI/UX) mengadopsi tema modern **Aqua Fresh** dengan sistem komponen native murni tanpa library CSS berat seperti Bootstrap maupun Tailwind.

---

## ⚡ Fitur Unggulan Sistem

### 1. 🛒 Kasir Point of Sale (POS) — `?page=pos`
- **Pencarian Cepat Pelanggan** secara realtime (auto-complete) + tombol shortcut **`N`** untuk tambah pelanggan baru secara instan.
- **Katalog Layanan Laundry Multi-Kategori** (Kiloan, Satuan/Bed Cover/Sepatu, Express Kilat).
- **Keranjang Dinamis Multi-Item**:
  - Penambahan & pengurangan berat (kg) / jumlah (pcs) dengan tombol step `+0.5` / `-0.5` atau ketik manual.
  - Perhitungan SLA (estimasi tanggal & jam selesai) secara otomatis.
  - Opsi prioritas **⚡ Kilat (Express 6 Jam)** dengan badge khusus.
  - Pilihan aroma parfum: *Ocean Fresh, Floral Romantic, Sweet Berry, Lavender Care, atau Tanpa Parfum*.
  - Input catatan khusus cucian dengan **Quick Chips** (*+ Pisah Putih, + No Parfum, + Baju Luntur, + Gantungan*).
- **Pembayaran Fleksibel**:
  - Pilihan **Bayar Sekarang (Lunas)** vs **Bayar Saat Ambil (Belum Lunas)**.
  - Metode bayar: Tunai, Transfer, E-Wallet.
  - Preset uang tunai cepat (*Uang Pas, 20.000, 50.000, 100.000*) & kalkulator kembalian otomatis.
- **Pintasan Keyboard (Shortcuts)**:
  - `F2` / `Enter`: Simpan & Buka dialog cetak nota.
  - `F3`: Cetak Label Stiker Pakaian (58x40mm).
  - `N`: Tambah pelanggan baru.
  - `/`: Fokus ke kotak pencarian pelanggan.
  - `Esc`: Tutup modal / drawer aktif.

### 2. 📊 Bento-Grid Dashboard — `?page=dashboard`
- **4 Kartu Metrik Utama**: Omzet Kas Masuk Hari Ini, Antrean Produksi Aktif, Siap Diambil di Rak, dan Total Piutang Belum Lunas.
- **Grafik Interaktif 14 Hari (SVG Native)**: Membandingkan tren omzet penjualan vs biaya pengeluaran operasional secara visual tanpa library chart eksternal.
- **Kartu Antrean Prioritas / Kilat**: Menampilkan countdown SLA tersisa sebelum batas waktu terlewati (*overdue*).
- **Daftar Pesanan Terkini**: Tabel ringkas dengan akses satu klik ke drawer rincian.

### 3. 🏭 Lantai Produksi (Operator Floor) — `?page=produksi`
- Mengadopsi tampilan antarmuka ramah mobile/tablet operator:
  - **Tahap 1: Menunggu Dicuci (Antrean)** &rarr; Tombol aksi *Mulai Cuci (Mesin)*.
  - **Tahap 2: Sedang Dicuci** &rarr; Tombol aksi *Selesai Cuci &rarr; Setrika Uap*.
  - **Tahap 3: Sedang Disetrika & Packing** &rarr; Modal pemilihan nomor rak penyimpanan (*Rak A1, A2, B1, B2, C1, Gantungan*).
- Setiap perpindahan status otomatis tercatat di tabel `riwayat_status` lengkap dengan timestamp dan nama petugas.

### 4. 🗄️ Cucian Selesai & Rak Pengambilan — `?page=pesanan&action=selesai`
- Filter cepat berdasarkan nomor rak (*Semua Rak, Rak A1, A2, B1, B2, C1, dll.*).
- Penyerahan cucian ke pelanggan: jika pesanan berstatus *Belum Lunas*, sistem otomatis menampilkan form pelunasan uang kasir & hitung kembalian sebelum status berubah menjadi *Selesai*.
- Tombol kirim pesan WhatsApp otomatis ke nomor pelanggan memberitahukan bahwa cucian siap diambil.

### 5. 📑 Rincian Pesanan (Interactive Side Drawer) — `?page=pesanan&action=detail`
- Slide drawer dari sisi kanan layar tanpa reload halaman (menggunakan Fetch API native).
- Menampilkan rincian layanan, timeline status pengerjaan, informasi pembayaran, dan tombol cetak ulang nota.

### 6. 💰 Buku Pengeluaran Operasional — `?page=pengeluaran`
- Pencatatan biaya detergen cair, parfum, listrik/air ruko, gaji karyawan, dan servis mesin.
- Filter per bulan dan per kategori pengeluaran.
- Rekapitulasi total belanja operasional.

### 7. 📈 Laporan Keuangan & Penjualan — `?page=laporan`
- Filter kustom rentang tanggal (*Start Date* s/d *End Date*).
- Total Omzet, Total Pengeluaran, dan **Laba Bersih (Net Profit)**.
- Rincian penerimaan kas per metode pembayaran (Tunai vs Transfer vs E-Wallet).
- Rincian volume dan pendapatan per jenis layanan laundry.
- Tombol cetak / export ke format PDF bersih (*print stylesheet*).

### 8. 🔍 Pelacakan Publik Tanpa Login — `?page=tracking`
- Pelanggan dapat membuka link pelacakan langsung: `index.php?page=tracking&nota=LG-261114-001`.
- Menampilkan visual progress bar status (*Antrean &rarr; Dicuci &rarr; Disetrika &rarr; Siap Diambil &rarr; Selesai*), nomor rak, dan estimasi waktu selesai.

---

## 🔑 Akun Login Bawaan (Demo)
Sistem memiliki 3 role akses yang telah dikonfigurasi:

| Role | Username | Password | Hak Akses Halaman |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `admin123` | Akses Penuh: Dashboard, POS Kasir, Pesanan, Produksi, Selesai, Pelanggan, Pengeluaran, Laporan, Tarif Layanan, Kelola Karyawan |
| **Kasir** | `kasir` | `123456` | POS Kasir, Dashboard, Pesanan, Selesai / Rak, Pelanggan, Lantai Produksi |
| **Petugas Produksi** | `petugas` | `123456` | Lantai Produksi, Daftar Pesanan |

---

## 🚀 Persyaratan & Cara Menjalankan

### Persyaratan Sistem:
1. **PHP >= 8.1** dengan ekstensi `pdo_pgsql` aktif (sudah diaktifkan di Laragon).
2. **PostgreSQL Database** (PostgreSQL 14, 15, 16, atau 17 di Laragon).
3. Web Server: **Apache (Laragon)** atau **PHP Built-in Server**.

### Langkah Menjalankan:
1. **Pastikan PostgreSQL Berjalan di Laragon**:
   - Buka aplikasi Laragon, klik tombol **Start All** (memastikan Apache, MySQL, dan PostgreSQL aktif).
2. **Migrasi Database Otomatis**:
   - Buka terminal / command prompt di folder proyek:
     ```bash
     cd D:\laragon\www\Laundrey
     php database/migrate.php
     ```
   - Skrip ini akan otomatis membuat database `laundrygo_db`, membuat semua tabel PostgreSQL sesuai ERD, dan mengisi data awal (*seed*).
3. **Buka di Browser**:
   - Akses URL: `http://localhost/Laundrey`
   - Atau jika menggunakan PHP built-in server:
     ```bash
     php -S localhost:8000
     ```
     Lalu buka `http://localhost:8000`.

---

## 🗄️ Skema Database (ERD PostgreSQL)
Database dirancang sesuai dengan diagram ERD yang diberikan dan diperluas untuk mencakup kebutuhan operasional laundry modern:

```
[ROLE] ──< [ANGGOTA] ──< [LOG_AKTIVITAS]
               │
               ├──< [PESANAN] >── [PELANGGAN]
               │        │
               │        ├──< [DETAIL_PESANAN] >── [LAYANAN] >── [KATEGORI_LAYANAN]
               │        ├──< [PEMBAYARAN] >── [METODE_PEMBAYARAN]
               │        └──< [RIWAYAT_STATUS]
               │
               └──< [PENGELUARAN] >── [KATEGORI_PENGELUARAN]
```

### Relasi Tabel:
1. `role`: `id_role` (PK), `nama_role`
2. `anggota`: `id_anggota` (PK), `username`, `password`, `nama_anggota`, `status_aktif`, `id_role` (FK)
3. `log_aktivitas`: `id_log` (PK), `aktivitas`, `waktu_aktivitas`, `keterangan`, `id_anggota` (FK)
4. `pelanggan`: `id_pelanggan` (PK), `nama_pelanggan`, `no_telepon`, `alamat`
5. `kategori_layanan`: `id_kategori` (PK), `nama_kategori`, `deskripsi`, `status_aktif`
6. `layanan`: `id_layanan` (PK), `nama_layanan`, `harga`, `estimasi_waktu`, `satuan`, `status_aktif`, `id_kategori` (FK)
7. `pesanan`: `id_pesanan` (PK), `tanggal_masuk`, `tanggal_keluar`, `status_cucian`, `total_harga`, `status_pembayaran`, `lokasi_rak`, `catatan_khusus`, `parfum`, `is_kilat`, `id_pelanggan` (FK), `id_anggota` (FK)
8. `detail_pesanan`: `id_detail` (PK), `jumlah`, `harga_satuan`, `id_pesanan` (FK), `id_layanan` (FK)
9. `metode_pembayaran`: `id_metode` (PK), `nama_metode`
10. `pembayaran`: `id_pembayaran` (PK), `tanggal_pembayaran`, `jumlah_bayar`, `uang_kembalian`, `id_pesanan` (FK), `id_metode` (FK), `id_anggota` (FK)
11. `kategori_pengeluaran`: `id_kategori_pengeluaran` (PK), `nama_kategori`, `deskripsi`
12. `pengeluaran`: `id_pengeluaran` (PK), `tanggal_pengeluaran`, `keterangan`, `jumlah`, `id_anggota` (FK), `id_kategori_pengeluaran` (FK)
13. `riwayat_status`: `id_riwayat` (PK), `status`, `waktu_status`, `keterangan`, `id_pesanan` (FK), `id_anggota` (FK)

---

## 📁 Struktur Folder & Penjelasan Kode

```
Laundrey/
├── config/                     # Konfigurasi aplikasi & database
│   ├── app.php                 # Nama outlet, alamat, timezone
│   └── database.php            # Parameter koneksi PostgreSQL PDO
│
├── app/
│   ├── Core/                   # Pondasi inti aplikasi (Singleton & Utilities)
│   │   ├── Database.php        # Wrapper PDO pgsql terpusat & prepared statements
│   │   ├── Auth.php            # Session auth, role RBAC (isAdmin, isKasir, isPetugas)
│   │   ├── Helper.php          # URL builder, Rupiah, tanggal Indo, SLA countdown
│   │   └── View.php            # Template engine render view & layout
│   │
│   ├── Models/                 # Lapisan data & query PostgreSQL
│   │   ├── Anggota.php         # Model user & login
│   │   ├── Pelanggan.php       # Model pelanggan & riwayat belanja
│   │   ├── Layanan.php         # Model tarif & katalog layanan
│   │   ├── Pesanan.php         # Model transaksi, status, metrik, & grafik
│   │   ├── Pengeluaran.php     # Model buku pengeluaran kas
│   │   └── LogAktivitas.php    # Audit trail pencatatan aktivitas user
│   │
│   ├── Controllers/            # Pengendali logika aplikasi
│   │   ├── AuthController.php        # Login & logout
│   │   ├── DashboardController.php   # Kartu metrik & grafik 14 hari
│   │   ├── PosController.php         # Kasir POS & simpan pesanan
│   │   ├── PesananController.php     # Daftar pesanan, drawer detail, & rak
│   │   ├── ProduksiController.php    # Papan pengerjaan operator lantai cuci
│   │   ├── PelangganController.php   # Manajemen pelanggan
│   │   ├── PengeluaranController.php # Manajemen pengeluaran
│   │   ├── LaporanController.php     # Laporan laba rugi & omzet
│   │   ├── LayananController.php     # Manajemen tarif
│   │   ├── PenggunaController.php    # Manajemen akun staf
│   │   └── TrackingController.php    # Pelacakan nota publik
│   │
│   └── Views/                  # Tampilan HTML / PHP murni
│       ├── layouts/            # header, footer, sidebar, topbar
│       ├── auth/login.php      # Halaman login staf & pelacakan nota
│       ├── dashboard/index.php # Bento dashboard & SVG chart
│       ├── pos/index.php       # Layar kasir POS lengkap
│       ├── pesanan/index.php   # Tabel pesanan & filter status
│       ├── pesanan/selesai.php # Rak cucian & modal serah terima
│       ├── produksi/index.php  # Papan kanban 3 tahapan cuci
│       ├── pelanggan/index.php # Master data pelanggan
│       ├── pengeluaran/index.php # Buku kas pengeluaran
│       ├── laporan/index.php   # Laporan keuangan & penjualan
│       ├── layanan/index.php   # Master data tarif
│       ├── pengguna/index.php  # Master data staf & role
│       ├── tracking/index.php  # Halaman lacak nota publik
│       └── partials/           # Modals, side drawer, dan struk print
│
├── assets/                     # Aset statis client-side
│   ├── css/                    # Pure native CSS (tokens, layout, components, style)
│   ├── js/                     # Native JavaScript (app.js, pos.js)
│   └── img/                    # Logo LaundryGo Aqua
│
├── database/                   # Skrip database PostgreSQL
│   ├── schema_postgresql.sql   # DDL tabel & relasi foreign key
│   ├── seed_postgresql.sql     # Data awal (roles, users, layanan, pelanggan, pesanan)
│   └── migrate.php             # Skrip runner migrasi 1-klik
│
├── index.php                   # Single Front Controller (Entry point utama)
└── README.md                   # Dokumentasi lengkap proyek
```

---

## 🌐 Pola Routing URL (Semester 3 Friendly)

Sistem menggunakan pola **Query String Routing** (`index.php?page=...&action=...`) yang sangat praktis, stabil, tidak memerlukan konfigurasi server yang rumit (`.htaccess` / Apache mod_rewrite), dan sangat mudah dijelaskan saat sidang/tugas akhir:

| URL Endpoint | Halaman / Fungsi | Controller Terkait |
| :--- | :--- | :--- |
| `index.php?page=login` | Halaman form login | `AuthController->login()` |
| `index.php?page=login&action=doLogin` | Proses POST login | `AuthController->doLogin()` |
| `index.php?page=logout` | Keluar sistem | `AuthController->logout()` |
| `index.php?page=dashboard` | Dashboard utama | `DashboardController->index()` |
| `index.php?page=pos` | Layar Kasir POS | `PosController->index()` |
| `index.php?page=pos&action=store` | Simpan pesanan POS | `PosController->store()` |
| `index.php?page=pesanan` | Daftar seluruh pesanan | `PesananController->index()` |
| `index.php?page=pesanan&action=detail&id=LG-...` | API detail pesanan (Drawer) | `PesananController->detail()` |
| `index.php?page=pesanan&action=selesai` | Rak cucian siap diambil | `PesananController->selesai()` |
| `index.php?page=pesanan&action=ambil` | Serah terima & pelunasan | `PesananController->ambil()` |
| `index.php?page=produksi` | Lantai produksi operator | `ProduksiController->index()` |
| `index.php?page=produksi&action=advance` | Majukan status cucian | `ProduksiController->advance()` |
| `index.php?page=pelanggan` | Master pelanggan | `PelangganController->index()` |
| `index.php?page=pengeluaran` | Buku pengeluaran | `PengeluaranController->index()` |
| `index.php?page=laporan` | Laporan keuangan | `LaporanController->index()` |
| `index.php?page=layanan` | Master tarif & layanan | `LayananController->index()` |
| `index.php?page=pengguna` | Kelola user & role | `PenggunaController->index()` |
| `index.php?page=tracking&nota=LG-...` | Lacak nota publik | `TrackingController->index()` |

---

<p align="center">
  Dibuat dengan ❤️ untuk sistem kasir modern LaundryGo — Bersih, Andal, & Mudah Dikembangkan.
</p>
