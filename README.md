# LaundryGo — PHP Native (MVC Ringan) — UI/UX Fresh Aqua

Aplikasi Sistem Kasir & Operasional LaundryGo (Adi Laundry, Karangploso) dengan struktur PHP Native MVC dan integrasi tampilan UI/UX 100% identik dengan LaundryGo Aqua.

## Cara Menjalankan (PHP >= 8.1)
```bash
cd laundrygo-php
php -S localhost:8000 -t public
```
Buka browser: `http://localhost:8000`

### Akun Demo:
- **Admin**: `admin` / `admin123` (Hak akses penuh: Kasir, Dashboard, Pesanan, Antrean, Selesai, Pelanggan, Pengeluaran, Laporan, Layanan, Pengguna, Profil, Desain)
- **Kasir**: `siti` / `123456` (Kasir, Dashboard, Pesanan, Antrean, Selesai, Pelanggan, Layanan, Profil)
- **Produksi**: `budi` / `123456` (Antrean produksi, Pesanan, Selesai, Profil)

---

## Struktur Proyek
```
laundrygo-php/
├─ public/
│  ├─ index.php               Front Controller
│  ├─ .htaccess               Apache rewrite
│  └─ assets/
│     ├─ css/                 tokens.css, layout.css, components.css, compat.css
│     └─ js/                  config.js, store.js, views.js, app.js, ui.js
├─ app/
│  ├─ core/                   bootstrap, router, middleware, session, helpers, icons
│  ├─ config/                 routes.php, menu.php, users.php
│  ├─ controllers/            auth_controller.php
│  └─ views/
│     ├─ layouts/             app_layout.php, auth_layout.php
│     ├─ partials/            head, sidebar, topbar, order_detail_drawer, action_modal
│     ├─ auth/                login.php
│     ├─ errors/              404.php, restricted.php
│     └─ pages/
│        ├─ kasir/            pos.php, pesanan.php, selesai.php, pelanggan.php
│        ├─ admin/            dashboard.php, pesanan.php, pengeluaran.php, laporan.php, layanan.php, pengguna.php
│        ├─ produksi/         antrean.php, pesanan.php
│        └─ shared/           profil.php, desain.php
```

---

## Fitur & Tampilan yang Diimplementasikan (Identik Aqua):
1. **Kasir POS (`/kasir/pos`)**:
   - Pemilihan 5 layanan utama (Cuci Kering + Setrika, Cuci Kering Saja, Setrika Saja, Cuci Satuan, Kilat/Express)
   - Pencarian & pemilihan pelanggan + Modal Tambah Pelanggan Baru (`N`)
   - Pengaturan berat/jumlah dengan tombol cepat (-0.5 s/d +5) dan Modal Numpad interaktif
   - Input catatan khusus dengan chip cepat (Pisah putih, Jangan pakai pewangi, dll)
   - Nota digital multi-item dengan kalkulasi SLA otomatis & badge prioritas kilat
   - Pilihan bayar sekarang (Tunai dengan preset uang pas/50rb/100rb & hitung kembalian, QRIS/E-Wallet, Transfer) vs bayar saat ambil
   - Simpan pesanan & cetak nota (`Enter` / `F2`) / cetak label (`F3`)
   - Banner sukses pesanan tersimpan dengan tombol cetak ulang dan pesanan baru
2. **Dashboard (`/admin/dashboard`)**:
   - Kartu metrik omset hari ini, antrean produksi, cucian selesai, dan piutang
   - Grafik 14 hari interaktif SVG perbandingan omset vs pengeluaran
   - Antrean prioritas dan daftar pesanan terkini
3. **Daftar Pesanan (`/admin/pesanan`, `/kasir/pesanan`, `/produksi/pesanan`)**:
   - Multi-kriteria filter: pencarian global (`F1`/`/`), status pengerjaan, layanan, rentang tanggal, filter kilat, filter belum lunas
   - Tabel sortable dengan badge status, indikator keterlambatan, dan kilat
   - Klik baris membuka Drawer Detail Pesanan
4. **Antrean Produksi (`/produksi/antrean`)**:
   - Kanban board (Diterima -> Dicuci -> Disetrika)
   - Prioritas kilat di atas dengan countdown SLA pengerjaan
   - Tombol progress pengerjaan dengan toast Undo 5 detik
5. **Cucian Selesai (`/kasir/selesai`)**:
   - Daftar cucian siap diambil
   - Hubungi WA via tautan otomatis wa.me dengan draft pesan siap kirim
   - Tandai sudah dikabari, terima pembayaran pelunasan, serahkan cucian
6. **Data Pelanggan (`/kasir/pelanggan`)**:
   - Tabel pelanggan lengkap dengan riwayat total transaksi
   - Modal Tambah & Ubah Pelanggan
   - Modal Riwayat Pesanan per Pelanggan
7. **Pengeluaran (`/admin/pengeluaran`)**:
   - Rekap pengeluaran per kategori (Deterjen, Plastik, Listrik, Parfum, Gaji, dll)
   - Modal Catat Pengeluaran Baru & Hapus Pengeluaran
8. **Laporan Keuangan (`/admin/laporan`)**:
   - Rekap Omset, Pengeluaran, Laba Bersih, Piutang
   - Breakdown omset per metode pembayaran (Tunai, E-Wallet, Transfer)
   - Modal Ekspor Data (CSV/Excel) & Cetak Laporan Tutup Bulan
9. **Layanan & Harga (`/admin/layanan`)**:
   - Kartu 5 layanan utama, toggle aktif/nonaktif, Modal Ubah Harga & Durasi
10. **Pengguna & Log (`/admin/pengguna`)**:
    - Kelola akun kasir/produksi/admin, reset password, audit log aktivitas
11. **Profil & Password (`/profil`)**:
    - Info akun aktif & form ganti password
12. **Sistem Desain Fresh Aqua (`/desain` / Board Switcher)**:
    - Dokumentasi token warna, badge status, tipografi, komponen tombol, dan form input
13. **Drawer Detail Pesanan & Universal Modals**:
    - Drawer samping 420px (Timeline lengkap, ubah pesanan, riwayat versi, persetujuan piutang, batalkan pesanan, dll.)
    - Cetak thermal Struk Nota 58/80mm & Label 58x40mm
    - Mode Gelap Night Shift & Efek Suara Interaksi Pop
    - Penyimpanan otomatis state ke `localStorage` sehingga perubahan data tetap tersimpan saat navigasi halaman.

