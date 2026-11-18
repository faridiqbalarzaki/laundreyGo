-- =======================================================
-- SKEMA DATABASE MYSQL / MARIADB - LAUNDRYGO
-- Disesuaikan untuk DBeaver & Laragon MySQL (Port 3306)
-- Engine: InnoDB, Charset: utf8mb4_unicode_ci
-- =======================================================

CREATE DATABASE IF NOT EXISTS `laundrygo` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `laundrygo`;

SET FOREIGN_KEY_CHECKS = 0;

-- Bersihkan tabel-tabel lama agar skema presisi dan kompatibel
DROP TABLE IF EXISTS `riwayat_status`;
DROP TABLE IF EXISTS `pembayaran`;
DROP TABLE IF EXISTS `detail_pesanan`;
DROP TABLE IF EXISTS `pengeluaran`;
DROP TABLE IF EXISTS `log_aktivitas`;
DROP TABLE IF EXISTS `pesanan`;
DROP TABLE IF EXISTS `layanan`;
DROP TABLE IF EXISTS `kategori_layanan`;
DROP TABLE IF EXISTS `kategori_pengeluaran`;
DROP TABLE IF EXISTS `metode_pembayaran`;
DROP TABLE IF EXISTS `pelanggan`;
DROP TABLE IF EXISTS `anggota`;
DROP TABLE IF EXISTS `role`;

-- 1. TABEL ROLE
CREATE TABLE IF NOT EXISTS `role` (
    `id_role` VARCHAR(10) PRIMARY KEY,
    `nama_role` VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TABEL ANGGOTA (Karyawan / User Sistem)
CREATE TABLE IF NOT EXISTS `anggota` (
    `id_anggota` VARCHAR(10) PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `nama_anggota` VARCHAR(100) NOT NULL,
    `status_aktif` TINYINT DEFAULT 1,
    `id_role` VARCHAR(10) NOT NULL,
    CONSTRAINT `fk_anggota_role` FOREIGN KEY (`id_role`) REFERENCES `role`(`id_role`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. TABEL LOG AKTIVITAS
CREATE TABLE IF NOT EXISTS `log_aktivitas` (
    `id_log` VARCHAR(20) PRIMARY KEY,
    `aktivitas` VARCHAR(100) NOT NULL,
    `waktu_aktivitas` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `keterangan` VARCHAR(255),
    `id_anggota` VARCHAR(10),
    CONSTRAINT `fk_log_anggota` FOREIGN KEY (`id_anggota`) REFERENCES `anggota`(`id_anggota`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TABEL PELANGGAN
CREATE TABLE IF NOT EXISTS `pelanggan` (
    `id_pelanggan` VARCHAR(10) PRIMARY KEY,
    `nama_pelanggan` VARCHAR(100) NOT NULL,
    `no_telepon` VARCHAR(20) NOT NULL,
    `alamat` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. TABEL KATEGORI LAYANAN
CREATE TABLE IF NOT EXISTS `kategori_layanan` (
    `id_kategori` VARCHAR(10) PRIMARY KEY,
    `nama_kategori` VARCHAR(50) NOT NULL,
    `deskripsi` VARCHAR(255),
    `status_aktif` TINYINT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. TABEL LAYANAN
CREATE TABLE IF NOT EXISTS `layanan` (
    `id_layanan` VARCHAR(10) PRIMARY KEY,
    `nama_layanan` VARCHAR(100) NOT NULL,
    `harga` DECIMAL(12, 2) NOT NULL,
    `estimasi_waktu` INT DEFAULT 24,
    `satuan` VARCHAR(20) DEFAULT 'kg',
    `status_aktif` TINYINT DEFAULT 1,
    `id_kategori` VARCHAR(10) NOT NULL,
    CONSTRAINT `fk_layanan_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_layanan`(`id_kategori`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. TABEL PESANAN
CREATE TABLE IF NOT EXISTS `pesanan` (
    `id_pesanan` VARCHAR(20) PRIMARY KEY,
    `tanggal_masuk` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `tanggal_keluar` DATETIME,
    `status_cucian` VARCHAR(30) DEFAULT 'Antrean',
    `total_harga` DECIMAL(12, 2) NOT NULL,
    `status_pembayaran` VARCHAR(30) DEFAULT 'Belum Lunas',
    `lokasi_rak` VARCHAR(30),
    `catatan_khusus` TEXT,
    `is_kilat` TINYINT(1) DEFAULT 0,
    `id_pelanggan` VARCHAR(10) NOT NULL,
    `id_anggota` VARCHAR(10),
    CONSTRAINT `fk_pesanan_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan`(`id_pelanggan`) ON UPDATE CASCADE,
    CONSTRAINT `fk_pesanan_anggota` FOREIGN KEY (`id_anggota`) REFERENCES `anggota`(`id_anggota`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. TABEL DETAIL PESANAN
CREATE TABLE IF NOT EXISTS `detail_pesanan` (
    `id_detail` VARCHAR(20) PRIMARY KEY,
    `jumlah` DECIMAL(6, 2) NOT NULL,
    `harga_satuan` DECIMAL(12, 2) NOT NULL,
    `id_pesanan` VARCHAR(20) NOT NULL,
    `id_layanan` VARCHAR(10) NOT NULL,
    CONSTRAINT `fk_detail_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan`(`id_pesanan`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk_detail_layanan` FOREIGN KEY (`id_layanan`) REFERENCES `layanan`(`id_layanan`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. TABEL METODE PEMBAYARAN
CREATE TABLE IF NOT EXISTS `metode_pembayaran` (
    `id_metode` VARCHAR(10) PRIMARY KEY,
    `nama_metode` VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. TABEL PEMBAYARAN
CREATE TABLE IF NOT EXISTS `pembayaran` (
    `id_pembayaran` VARCHAR(20) PRIMARY KEY,
    `tanggal_pembayaran` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `jumlah_bayar` DECIMAL(12, 2) NOT NULL,
    `uang_kembalian` DECIMAL(12, 2) DEFAULT 0,
    `id_pesanan` VARCHAR(20) NOT NULL,
    `id_metode` VARCHAR(10) NOT NULL,
    `id_anggota` VARCHAR(10),
    CONSTRAINT `fk_pembayaran_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan`(`id_pesanan`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk_pembayaran_metode` FOREIGN KEY (`id_metode`) REFERENCES `metode_pembayaran`(`id_metode`) ON UPDATE CASCADE,
    CONSTRAINT `fk_pembayaran_anggota` FOREIGN KEY (`id_anggota`) REFERENCES `anggota`(`id_anggota`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. TABEL KATEGORI PENGELUARAN
CREATE TABLE IF NOT EXISTS `kategori_pengeluaran` (
    `id_kategori_pengeluaran` VARCHAR(10) PRIMARY KEY,
    `nama_kategori` VARCHAR(50) NOT NULL,
    `deskripsi` VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. TABEL PENGELUARAN
CREATE TABLE IF NOT EXISTS `pengeluaran` (
    `id_pengeluaran` VARCHAR(20) PRIMARY KEY,
    `tanggal_pengeluaran` DATE DEFAULT (CURRENT_DATE),
    `keterangan` VARCHAR(255) NOT NULL,
    `jumlah` DECIMAL(12, 2) NOT NULL,
    `id_anggota` VARCHAR(10),
    `id_kategori_pengeluaran` VARCHAR(10) NOT NULL,
    CONSTRAINT `fk_pengeluaran_anggota` FOREIGN KEY (`id_anggota`) REFERENCES `anggota`(`id_anggota`) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `fk_pengeluaran_kategori` FOREIGN KEY (`id_kategori_pengeluaran`) REFERENCES `kategori_pengeluaran`(`id_kategori_pengeluaran`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. TABEL RIWAYAT STATUS
CREATE TABLE IF NOT EXISTS `riwayat_status` (
    `id_riwayat` VARCHAR(20) PRIMARY KEY,
    `status` VARCHAR(30) NOT NULL,
    `waktu_status` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `keterangan` VARCHAR(255),
    `id_pesanan` VARCHAR(20) NOT NULL,
    `id_anggota` VARCHAR(10),
    CONSTRAINT `fk_riwayat_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan`(`id_pesanan`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk_riwayat_anggota` FOREIGN KEY (`id_anggota`) REFERENCES `anggota`(`id_anggota`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
