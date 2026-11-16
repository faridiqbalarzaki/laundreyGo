-- =======================================================
-- SEED DATA AWAL MYSQL / MARIADB - LAUNDRYGO
-- =======================================================

USE `laundrygo`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. ROLE
INSERT INTO `role` (`id_role`, `nama_role`) VALUES
('ADM', 'Admin'),
('KAS', 'Kasir'),
('PET', 'Petugas Produksi')
ON DUPLICATE KEY UPDATE `nama_role` = VALUES(`nama_role`);

-- 2. ANGGOTA
-- Password 'admin123': $2y$10$NAxHCSiGwoZG848Y741S0eP2Z23IYrNjz7ii8xSh.Eqma1CrisA2K
-- Password '123456':   $2y$10$HKrwZcDARsw2JJwmh4yMgeRFL.Z0ECrOwIjvsiszZgNFYW.go76Ri
INSERT INTO `anggota` (`id_anggota`, `username`, `password`, `nama_anggota`, `status_aktif`, `id_role`) VALUES
('USR-001', 'admin', '$2y$10$NAxHCSiGwoZG848Y741S0eP2Z23IYrNjz7ii8xSh.Eqma1CrisA2K', 'Administrator Utama', 1, 'ADM'),
('USR-002', 'kasir', '$2y$10$HKrwZcDARsw2JJwmh4yMgeRFL.Z0ECrOwIjvsiszZgNFYW.go76Ri', 'Kasir Outlet', 1, 'KAS'),
('USR-003', 'petugas', '$2y$10$HKrwZcDARsw2JJwmh4yMgeRFL.Z0ECrOwIjvsiszZgNFYW.go76Ri', 'Petugas Produksi', 1, 'PET')
ON DUPLICATE KEY UPDATE 
    `username` = VALUES(`username`),
    `password` = VALUES(`password`),
    `nama_anggota` = VALUES(`nama_anggota`),
    `id_role` = VALUES(`id_role`);

-- 3. KATEGORI LAYANAN
INSERT INTO `kategori_layanan` (`id_kategori`, `nama_kategori`, `deskripsi`, `status_aktif`) VALUES
('KAT-01', 'Kiloan', 'Layanan cuci berbasis timbangan per kilogram', 1),
('KAT-02', 'Satuan', 'Layanan khusus per potong (jas, bed cover, sepatu, dll)', 1),
('KAT-03', 'Express', 'Layanan kilat prioritas tinggi SLA < 12 jam', 1)
ON DUPLICATE KEY UPDATE `nama_kategori` = VALUES(`nama_kategori`);

-- 4. LAYANAN
INSERT INTO `layanan` (`id_layanan`, `nama_layanan`, `harga`, `estimasi_waktu`, `satuan`, `status_aktif`, `id_kategori`) VALUES
('LAY-01', 'Cuci Kering + Setrika', 7000, 48, 'kg', 1, 'KAT-01'),
('LAY-02', 'Cuci Kering Saja', 5000, 24, 'kg', 1, 'KAT-01'),
('LAY-03', 'Setrika Saja', 4000, 24, 'kg', 1, 'KAT-01'),
('LAY-04', 'Bed Cover Besar', 25000, 48, 'pcs', 1, 'KAT-02'),
('LAY-05', 'Cuci Sepatu Premium', 30000, 72, 'pcs', 1, 'KAT-02'),
('LAY-06', 'Kilat Express 6 Jam', 12000, 6, 'kg', 1, 'KAT-03')
ON DUPLICATE KEY UPDATE `harga` = VALUES(`harga`), `nama_layanan` = VALUES(`nama_layanan`);

-- 5. METODE PEMBAYARAN
INSERT INTO `metode_pembayaran` (`id_metode`, `nama_metode`) VALUES
('MET-01', 'Tunai'),
('MET-02', 'Transfer'),
('MET-03', 'E-Wallet')
ON DUPLICATE KEY UPDATE `nama_metode` = VALUES(`nama_metode`);

-- 6. KATEGORI PENGELUARAN
INSERT INTO `kategori_pengeluaran` (`id_kategori_pengeluaran`, `nama_kategori`, `deskripsi`) VALUES
('KP-01', 'Operasional & Listrik', 'Biaya listrik, air PDAM, dan utilitas ruko'),
('KP-02', 'Bahan Baku & Sabun', 'Detergen konsentrat, parfum laundry, softener, plastik packing'),
('KP-03', 'Gaji & Upah', 'Gaji karyawan dan uang makan'),
('KP-04', 'Pemeliharaan', 'Service mesin cuci, setrika uap, dan dryer')
ON DUPLICATE KEY UPDATE `nama_kategori` = VALUES(`nama_kategori`);

-- 7. PELANGGAN SAMPLE
INSERT INTO `pelanggan` (`id_pelanggan`, `nama_pelanggan`, `no_telepon`, `alamat`) VALUES
('PEL-001', 'Amanda Putri', '081234567890', 'Jl. Melati No. 12, Karangploso'),
('PEL-002', 'Bpk. Dimas', '081987654321', 'Komp. Permata Jingga Blok B-4'),
('PEL-003', 'Citra Kirana', '085611223344', 'Jl. Mawar No. 8, Batu'),
('PEL-004', 'Rizky Febian', '087800112233', 'Jl. Candi Mendut No. 45'),
('PEL-005', 'Ibu Maya', '081399887766', 'Griya Asri No. 10')
ON DUPLICATE KEY UPDATE `nama_pelanggan` = VALUES(`nama_pelanggan`), `no_telepon` = VALUES(`no_telepon`);

-- 8. PESANAN SAMPLE
INSERT INTO `pesanan` (`id_pesanan`, `tanggal_masuk`, `tanggal_keluar`, `status_cucian`, `total_harga`, `status_pembayaran`, `lokasi_rak`, `catatan_khusus`, `parfum`, `is_kilat`, `id_pelanggan`, `id_anggota`) VALUES
('LG-261114-001', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 'Siap Diambil', 28000, 'Lunas', 'A1', 'Pisahkan baju putih', 'Ocean Fresh', 0, 'PEL-001', 'USR-002'),
('LG-261114-002', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 1 DAY), 'Dicuci', 35000, 'Belum Lunas', NULL, 'Gunakan pewangi extra', 'Floral Romantic', 0, 'PEL-002', 'USR-002'),
('LG-261115-001', DATE_SUB(NOW(), INTERVAL 5 HOUR), DATE_ADD(NOW(), INTERVAL 1 HOUR), 'Disetrika', 36000, 'Lunas', NULL, 'Baju kerja disetrika rapi dan licin', 'Sweet Berry', 1, 'PEL-003', 'USR-002'),
('LG-261115-002', DATE_SUB(NOW(), INTERVAL 1 HOUR), DATE_ADD(NOW(), INTERVAL 23 HOUR), 'Antrean', 21000, 'Belum Lunas', NULL, 'Celana levis jangan disikat kasar', 'Ocean Fresh', 0, 'PEL-004', 'USR-002')
ON DUPLICATE KEY UPDATE `status_cucian` = VALUES(`status_cucian`);

-- 9. DETAIL PESANAN SAMPLE
INSERT INTO `detail_pesanan` (`id_detail`, `jumlah`, `harga_satuan`, `id_pesanan`, `id_layanan`) VALUES
('DET-001', 4.0, 7000, 'LG-261114-001', 'LAY-01'),
('DET-002', 5.0, 7000, 'LG-261114-002', 'LAY-01'),
('DET-003', 3.0, 12000, 'LG-261115-001', 'LAY-06'),
('DET-004', 3.0, 7000, 'LG-261115-002', 'LAY-01')
ON DUPLICATE KEY UPDATE `harga_satuan` = VALUES(`harga_satuan`);

-- 10. PEMBAYARAN SAMPLE
INSERT INTO `pembayaran` (`id_pembayaran`, `tanggal_pembayaran`, `jumlah_bayar`, `uang_kembalian`, `id_pesanan`, `id_metode`, `id_anggota`) VALUES
('PAY-001', DATE_SUB(NOW(), INTERVAL 2 DAY), 50000, 22000, 'LG-261114-001', 'MET-01', 'USR-002'),
('PAY-002', DATE_SUB(NOW(), INTERVAL 5 HOUR), 36000, 0, 'LG-261115-001', 'MET-02', 'USR-002')
ON DUPLICATE KEY UPDATE `jumlah_bayar` = VALUES(`jumlah_bayar`);

-- 11. RIWAYAT STATUS SAMPLE
INSERT INTO `riwayat_status` (`id_riwayat`, `status`, `waktu_status`, `keterangan`, `id_pesanan`, `id_anggota`) VALUES
('RW-001', 'Antrean', DATE_SUB(NOW(), INTERVAL 2 DAY), 'Pesanan diterima di kasir', 'LG-261114-001', 'USR-002'),
('RW-002', 'Dicuci', DATE_SUB(NOW(), INTERVAL 36 HOUR), 'Masuk mesin cuci 02', 'LG-261114-001', 'USR-003'),
('RW-003', 'Disetrika', DATE_SUB(NOW(), INTERVAL 28 HOUR), 'Sedang disetrika uap', 'LG-261114-001', 'USR-003'),
('RW-004', 'Siap Diambil', DATE_SUB(NOW(), INTERVAL 24 HOUR), 'Selesai di packing & ditaruh di Rak A1', 'LG-261114-001', 'USR-003'),
('RW-005', 'Antrean', DATE_SUB(NOW(), INTERVAL 1 DAY), 'Pesanan diterima di kasir', 'LG-261114-002', 'USR-002'),
('RW-006', 'Dicuci', DATE_SUB(NOW(), INTERVAL 6 HOUR), 'Masuk mesin cuci 01', 'LG-261114-002', 'USR-003'),
('RW-007', 'Antrean', DATE_SUB(NOW(), INTERVAL 5 HOUR), 'Pesanan Express diterima', 'LG-261115-001', 'USR-002'),
('RW-008', 'Dicuci', DATE_SUB(NOW(), INTERVAL 4 HOUR), 'Cuci cepat express', 'LG-261115-001', 'USR-003'),
('RW-009', 'Disetrika', DATE_SUB(NOW(), INTERVAL 1 HOUR), 'Setrika uap prioritas kilat', 'LG-261115-001', 'USR-003'),
('RW-010', 'Antrean', DATE_SUB(NOW(), INTERVAL 1 HOUR), 'Pesanan reguler baru masuk', 'LG-261115-002', 'USR-002')
ON DUPLICATE KEY UPDATE `status` = VALUES(`status`);

SET FOREIGN_KEY_CHECKS = 1;
