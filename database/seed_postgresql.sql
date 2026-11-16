-- =======================================================
-- SEED DATA AWAL POSTGRESQL - LAUNDRYGO
-- =======================================================

-- 1. ROLE
INSERT INTO role (id_role, nama_role) VALUES
('ADM', 'Admin'),
('KAS', 'Kasir'),
('PET', 'Petugas Produksi')
ON CONFLICT (id_role) DO NOTHING;

-- 2. ANGGOTA
-- Password admin123: $2y$10$wE9vN.O07J1dG.QcR6Y2vOG1l1zI8T.d8d7A14gE.1J5E1sD1sD1s (atau generate via password_hash)
-- Password 'admin123' -> $2y$10$tZ92241.jXk8Zz11Bsmz/egV5eK6lM1fOQjZgJbKqPcfqR3W8vBwe
-- Password '123456'   -> $2y$10$7sQ0H2t6tL9Xh8Wk/hI7cOI29C1vT.5eFp0Z9sK5gB0X2eG3sJ7te
INSERT INTO anggota (id_anggota, username, password, nama_anggota, status_aktif, id_role) VALUES
('USR-001', 'admin', '$2y$10$NAxHCSiGwoZG848Y741S0eP2Z23IYrNjz7ii8xSh.Eqma1CrisA2K', 'Administrator Utama', 1, 'ADM'),
('USR-002', 'kasir', '$2y$10$HKrwZcDARsw2JJwmh4yMgeRFL.Z0ECrOwIjvsiszZgNFYW.go76Ri', 'Kasir Outlet', 1, 'KAS'),
('USR-003', 'petugas', '$2y$10$HKrwZcDARsw2JJwmh4yMgeRFL.Z0ECrOwIjvsiszZgNFYW.go76Ri', 'Petugas Produksi', 1, 'PET')
ON CONFLICT (id_anggota) DO UPDATE SET
    username = EXCLUDED.username,
    password = EXCLUDED.password,
    nama_anggota = EXCLUDED.nama_anggota,
    id_role = EXCLUDED.id_role;

-- 3. KATEGORI LAYANAN
INSERT INTO kategori_layanan (id_kategori, nama_kategori, deskripsi, status_aktif) VALUES
('KAT-01', 'Kiloan', 'Layanan cuci berbasis timbangan per kilogram', 1),
('KAT-02', 'Satuan', 'Layanan khusus per potong (jas, bed cover, sepatu, dll)', 1),
('KAT-03', 'Express', 'Layanan kilat prioritas tinggi SLA < 12 jam', 1)
ON CONFLICT (id_kategori) DO NOTHING;

-- 4. LAYANAN
INSERT INTO layanan (id_layanan, nama_layanan, harga, estimasi_waktu, satuan, status_aktif, id_kategori) VALUES
('LAY-01', 'Cuci Kering + Setrika', 7000, 48, 'kg', 1, 'KAT-01'),
('LAY-02', 'Cuci Kering Saja', 5000, 24, 'kg', 1, 'KAT-01'),
('LAY-03', 'Setrika Saja', 4000, 24, 'kg', 1, 'KAT-01'),
('LAY-04', 'Bed Cover Besar', 25000, 48, 'pcs', 1, 'KAT-02'),
('LAY-05', 'Cuci Sepatu Premium', 30000, 72, 'pcs', 1, 'KAT-02'),
('LAY-06', 'Kilat Express 6 Jam', 12000, 6, 'kg', 1, 'KAT-03')
ON CONFLICT (id_layanan) DO NOTHING;

-- 5. METODE PEMBAYARAN
INSERT INTO metode_pembayaran (id_metode, nama_metode) VALUES
('MET-01', 'Tunai'),
('MET-02', 'Transfer'),
('MET-03', 'E-Wallet')
ON CONFLICT (id_metode) DO UPDATE SET nama_metode = EXCLUDED.nama_metode;

-- 6. KATEGORI PENGELUARAN
INSERT INTO kategori_pengeluaran (id_kategori_pengeluaran, nama_kategori, deskripsi) VALUES
('KP-01', 'Operasional & Listrik', 'Biaya listrik, air PDAM, dan utilitas ruko'),
('KP-02', 'Bahan Baku & Sabun', 'Detergen konsentrat, parfum laundry, softener, plastik packing'),
('KP-03', 'Gaji & Upah', 'Gaji karyawan dan uang makan'),
('KP-04', 'Pemeliharaan', 'Service mesin cuci, setrika uap, dan dryer')
ON CONFLICT (id_kategori_pengeluaran) DO NOTHING;

-- 7. PELANGGAN SAMPLE
INSERT INTO pelanggan (id_pelanggan, nama_pelanggan, no_telepon, alamat) VALUES
('PEL-001', 'Amanda Putri', '081234567890', 'Jl. Melati No. 12, Karangploso'),
('PEL-002', 'Bpk. Dimas', '081987654321', 'Komp. Permata Jingga Blok B-4'),
('PEL-003', 'Citra Kirana', '085611223344', 'Jl. Mawar No. 8, Batu'),
('PEL-004', 'Rizky Febian', '087800112233', 'Jl. Candi Mendut No. 45'),
('PEL-005', 'Ibu Maya', '081399887766', 'Griya Asri No. 10')
ON CONFLICT (id_pelanggan) DO NOTHING;

-- 8. PESANAN SAMPLE
INSERT INTO pesanan (id_pesanan, tanggal_masuk, tanggal_keluar, status_cucian, total_harga, status_pembayaran, lokasi_rak, catatan_khusus, parfum, is_kilat, id_pelanggan, id_anggota) VALUES
('LG-261114-001', CURRENT_TIMESTAMP - INTERVAL '2 days', CURRENT_TIMESTAMP - INTERVAL '1 day', 'Siap Diambil', 28000, 'Lunas', 'A1', 'Pisahkan baju putih', 'Ocean Fresh', FALSE, 'PEL-001', 'USR-002'),
('LG-261114-002', CURRENT_TIMESTAMP - INTERVAL '1 day', CURRENT_TIMESTAMP + INTERVAL '1 day', 'Dicuci', 35000, 'Belum Lunas', NULL, 'Gunakan pewangi extra', 'Floral Romantic', FALSE, 'PEL-002', 'USR-002'),
('LG-261115-001', CURRENT_TIMESTAMP - INTERVAL '5 hours', CURRENT_TIMESTAMP + INTERVAL '1 hour', 'Disetrika', 36000, 'Lunas', NULL, 'Baju kerja disetrika rapi dan licin', 'Sweet Berry', TRUE, 'PEL-003', 'USR-002'),
('LG-261115-002', CURRENT_TIMESTAMP - INTERVAL '1 hour', CURRENT_TIMESTAMP + INTERVAL '23 hours', 'Antrean', 21000, 'Belum Lunas', NULL, 'Celana levis jangan disikat kasar', 'Ocean Fresh', FALSE, 'PEL-004', 'USR-002')
ON CONFLICT (id_pesanan) DO NOTHING;

-- 9. DETAIL PESANAN SAMPLE
INSERT INTO detail_pesanan (id_detail, jumlah, harga_satuan, id_pesanan, id_layanan) VALUES
('DET-001', 4.0, 7000, 'LG-261114-001', 'LAY-01'),
('DET-002', 5.0, 7000, 'LG-261114-002', 'LAY-01'),
('DET-003', 3.0, 12000, 'LG-261115-001', 'LAY-06'),
('DET-004', 3.0, 7000, 'LG-261115-002', 'LAY-01')
ON CONFLICT (id_detail) DO NOTHING;

-- 10. PEMBAYARAN SAMPLE
INSERT INTO pembayaran (id_pembayaran, tanggal_pembayaran, jumlah_bayar, uang_kembalian, id_pesanan, id_metode, id_anggota) VALUES
('PAY-001', CURRENT_TIMESTAMP - INTERVAL '2 days', 50000, 22000, 'LG-261114-001', 'MET-01', 'USR-002'),
('PAY-002', CURRENT_TIMESTAMP - INTERVAL '5 hours', 36000, 0, 'LG-261115-001', 'MET-02', 'USR-002')
ON CONFLICT (id_pembayaran) DO NOTHING;

-- 11. RIWAYAT STATUS SAMPLE
INSERT INTO riwayat_status (id_riwayat, status, waktu_status, keterangan, id_pesanan, id_anggota) VALUES
('RW-001', 'Antrean', CURRENT_TIMESTAMP - INTERVAL '2 days', 'Pesanan diterima di kasir', 'LG-261114-001', 'USR-002'),
('RW-002', 'Dicuci', CURRENT_TIMESTAMP - INTERVAL '36 hours', 'Masuk mesin cuci 02', 'LG-261114-001', 'USR-003'),
('RW-003', 'Disetrika', CURRENT_TIMESTAMP - INTERVAL '28 hours', 'Sedang disetrika uap', 'LG-261114-001', 'USR-003'),
('RW-004', 'Siap Diambil', CURRENT_TIMESTAMP - INTERVAL '24 hours', 'Selesai di packing & ditaruh di Rak A1', 'LG-261114-001', 'USR-003'),
('RW-005', 'Antrean', CURRENT_TIMESTAMP - INTERVAL '1 day', 'Pesanan diterima di kasir', 'LG-261114-002', 'USR-002'),
('RW-006', 'Dicuci', CURRENT_TIMESTAMP - INTERVAL '6 hours', 'Masuk mesin cuci 01', 'LG-261114-002', 'USR-003'),
('RW-007', 'Antrean', CURRENT_TIMESTAMP - INTERVAL '5 hours', 'Pesanan Express diterima', 'LG-261115-001', 'USR-002'),
('RW-008', 'Dicuci', CURRENT_TIMESTAMP - INTERVAL '4 hours', 'Cuci cepat express', 'LG-261115-001', 'USR-003'),
('RW-009', 'Disetrika', CURRENT_TIMESTAMP - INTERVAL '1 hour', 'Setrika uap prioritas kilat', 'LG-261115-001', 'USR-003'),
('RW-010', 'Antrean', CURRENT_TIMESTAMP - INTERVAL '1 hour', 'Pesanan reguler baru masuk', 'LG-261115-002', 'USR-002')
ON CONFLICT (id_riwayat) DO NOTHING;

-- 12. PENGELUARAN SAMPLE
INSERT INTO pengeluaran (id_pengeluaran, tanggal_pengeluaran, keterangan, jumlah, id_anggota, id_kategori_pengeluaran) VALUES
('EXP-001', CURRENT_DATE - 3, 'Beli Detergen Cair Matic 20 Liter', 180000, 'USR-001', 'KP-02'),
('EXP-002', CURRENT_DATE - 2, 'Beli Parfum Laundry Ocean Fresh 5L', 95000, 'USR-001', 'KP-02'),
('EXP-003', CURRENT_DATE - 1, 'Token Listrik Ruko 200rb', 205000, 'USR-001', 'KP-01'),
('EXP-004', CURRENT_DATE, 'Plastik Packing Laundry 3 pack', 45000, 'USR-002', 'KP-02')
ON CONFLICT (id_pengeluaran) DO NOTHING;
