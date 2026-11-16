-- =======================================================
-- SKEMA DATABASE POSTGRESQL - LAUNDRYGO
-- Disesuaikan dengan ERD Analisis Sistem Laundry
-- =======================================================

-- 1. TABEL ROLE
CREATE TABLE IF NOT EXISTS role (
    id_role VARCHAR(10) PRIMARY KEY,
    nama_role VARCHAR(50) NOT NULL
);

-- 2. TABEL ANGGOTA (Karyawan / User Sistem)
CREATE TABLE IF NOT EXISTS anggota (
    id_anggota VARCHAR(10) PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_anggota VARCHAR(100) NOT NULL,
    status_aktif SMALLINT DEFAULT 1,
    id_role VARCHAR(10) NOT NULL REFERENCES role(id_role) ON UPDATE CASCADE
);

-- 3. TABEL LOG AKTIVITAS
CREATE TABLE IF NOT EXISTS log_aktivitas (
    id_log VARCHAR(20) PRIMARY KEY,
    aktivitas VARCHAR(100) NOT NULL,
    waktu_aktivitas TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    keterangan VARCHAR(255),
    id_anggota VARCHAR(10) REFERENCES anggota(id_anggota) ON UPDATE CASCADE ON DELETE SET NULL
);

-- 4. TABEL PELANGGAN
CREATE TABLE IF NOT EXISTS pelanggan (
    id_pelanggan VARCHAR(10) PRIMARY KEY,
    nama_pelanggan VARCHAR(100) NOT NULL,
    no_telepon VARCHAR(20) NOT NULL,
    alamat TEXT
);

-- 5. TABEL KATEGORI LAYANAN
CREATE TABLE IF NOT EXISTS kategori_layanan (
    id_kategori VARCHAR(10) PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL,
    deskripsi VARCHAR(255),
    status_aktif SMALLINT DEFAULT 1
);

-- 6. TABEL LAYANAN
CREATE TABLE IF NOT EXISTS layanan (
    id_layanan VARCHAR(10) PRIMARY KEY,
    nama_layanan VARCHAR(100) NOT NULL,
    harga NUMERIC(12, 2) NOT NULL,
    estimasi_waktu SMALLINT DEFAULT 24, -- dalam jam
    satuan VARCHAR(20) DEFAULT 'kg', -- 'kg', 'pcs', 'm2'
    status_aktif SMALLINT DEFAULT 1,
    id_kategori VARCHAR(10) NOT NULL REFERENCES kategori_layanan(id_kategori) ON UPDATE CASCADE
);

-- 7. TABEL PESANAN
CREATE TABLE IF NOT EXISTS pesanan (
    id_pesanan VARCHAR(20) PRIMARY KEY,
    tanggal_masuk TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tanggal_keluar TIMESTAMP,
    status_cucian VARCHAR(30) DEFAULT 'Antrean', -- 'Antrean', 'Dicuci', 'Disetrika', 'Siap Diambil', 'Selesai', 'Dibatalkan'
    total_harga NUMERIC(12, 2) DEFAULT 0,
    status_pembayaran VARCHAR(20) DEFAULT 'Belum Lunas', -- 'Lunas', 'Belum Lunas'
    lokasi_rak VARCHAR(20),
    catatan_khusus TEXT,
    parfum VARCHAR(50) DEFAULT 'Ocean Fresh',
    is_kilat BOOLEAN DEFAULT FALSE,
    id_pelanggan VARCHAR(10) NOT NULL REFERENCES pelanggan(id_pelanggan) ON UPDATE CASCADE,
    id_anggota VARCHAR(10) REFERENCES anggota(id_anggota) ON UPDATE CASCADE ON DELETE SET NULL
);

-- 8. TABEL DETAIL PESANAN
CREATE TABLE IF NOT EXISTS detail_pesanan (
    id_detail VARCHAR(20) PRIMARY KEY,
    jumlah NUMERIC(10, 2) NOT NULL,
    harga_satuan NUMERIC(12, 2) NOT NULL,
    id_pesanan VARCHAR(20) NOT NULL REFERENCES pesanan(id_pesanan) ON UPDATE CASCADE ON DELETE CASCADE,
    id_layanan VARCHAR(10) NOT NULL REFERENCES layanan(id_layanan) ON UPDATE CASCADE
);

-- 9. TABEL METODE PEMBAYARAN
CREATE TABLE IF NOT EXISTS metode_pembayaran (
    id_metode VARCHAR(10) PRIMARY KEY,
    nama_metode VARCHAR(30) NOT NULL
);

-- 10. TABEL PEMBAYARAN
CREATE TABLE IF NOT EXISTS pembayaran (
    id_pembayaran VARCHAR(20) PRIMARY KEY,
    tanggal_pembayaran TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    jumlah_bayar NUMERIC(12, 2) NOT NULL,
    uang_kembalian NUMERIC(12, 2) DEFAULT 0,
    id_pesanan VARCHAR(20) NOT NULL REFERENCES pesanan(id_pesanan) ON UPDATE CASCADE ON DELETE CASCADE,
    id_metode VARCHAR(10) NOT NULL REFERENCES metode_pembayaran(id_metode) ON UPDATE CASCADE,
    id_anggota VARCHAR(10) REFERENCES anggota(id_anggota) ON UPDATE CASCADE ON DELETE SET NULL
);

-- 11. TABEL KATEGORI PENGELUARAN
CREATE TABLE IF NOT EXISTS kategori_pengeluaran (
    id_kategori_pengeluaran VARCHAR(10) PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL,
    deskripsi VARCHAR(255)
);

-- 12. TABEL PENGELUARAN
CREATE TABLE IF NOT EXISTS pengeluaran (
    id_pengeluaran VARCHAR(20) PRIMARY KEY,
    tanggal_pengeluaran DATE DEFAULT CURRENT_DATE,
    keterangan VARCHAR(255) NOT NULL,
    jumlah NUMERIC(12, 2) NOT NULL,
    id_anggota VARCHAR(10) REFERENCES anggota(id_anggota) ON UPDATE CASCADE ON DELETE SET NULL,
    id_kategori_pengeluaran VARCHAR(10) NOT NULL REFERENCES kategori_pengeluaran(id_kategori_pengeluaran) ON UPDATE CASCADE
);

-- 13. TABEL RIWAYAT STATUS
CREATE TABLE IF NOT EXISTS riwayat_status (
    id_riwayat VARCHAR(20) PRIMARY KEY,
    status VARCHAR(30) NOT NULL,
    waktu_status TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    keterangan VARCHAR(255),
    id_pesanan VARCHAR(20) NOT NULL REFERENCES pesanan(id_pesanan) ON UPDATE CASCADE ON DELETE CASCADE,
    id_anggota VARCHAR(10) REFERENCES anggota(id_anggota) ON UPDATE CASCADE ON DELETE SET NULL
);
