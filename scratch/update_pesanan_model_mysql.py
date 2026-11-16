p = r'd:\laragon\www\Laundrey\app\Models\Pesanan.php'
with open(p, 'r', encoding='utf-8') as f:
    c = f.read()

# 1. string_agg in all()
c = c.replace(
    "(SELECT string_agg(l.nama_layanan, ', ')",
    "(SELECT GROUP_CONCAT(l.nama_layanan SEPARATOR ', ')"
)

# 2. ILIKE in all()
c = c.replace(
    "AND (ps.id_pesanan ILIKE :kw OR p.nama_pelanggan ILIKE :kw OR p.no_telepon ILIKE :kw)",
    "AND (ps.id_pesanan LIKE :kw OR p.nama_pelanggan LIKE :kw OR p.no_telepon LIKE :kw)"
)

# 3. is_kilat boolean for MySQL in create()
c = c.replace(
    "'kilat'         => !empty($orderData['is_kilat']) ? 'true' : 'false',",
    "'kilat'         => !empty($orderData['is_kilat']) ? 1 : 0,"
)

# 4. string_agg in getAntreanProduksi()
c = c.replace(
    "(SELECT string_agg(l.nama_layanan || ' (' || dp.jumlah || ' ' || l.satuan || ')', ', ')",
    "(SELECT GROUP_CONCAT(CONCAT(l.nama_layanan, ' (', dp.jumlah, ' ', l.satuan, ')') SEPARATOR ', ')"
)

# 5. string_agg in getSiapAmbil()
c = c.replace(
    "(SELECT string_agg(l.nama_layanan, ', ') FROM detail_pesanan dp",
    "(SELECT GROUP_CONCAT(l.nama_layanan SEPARATOR ', ') FROM detail_pesanan dp"
)

# 6. is_kilat & LIMIT in getStoreNotifications()
c = c.replace(
    "WHERE (ps.is_kilat = TRUE AND ps.status_cucian IN ('Antrean', 'Dicuci', 'Disetrika'))",
    "WHERE (ps.is_kilat = 1 AND ps.status_cucian IN ('Antrean', 'Dicuci', 'Disetrika'))"
)
c = c.replace(
    "LIMIT :limit\";\n        return Database::query($sql, ['limit' => $limit]);",
    "LIMIT \" . (int)$limit;\n        return Database::query($sql);"
)

# 7. INTERVAL in getMetrics()
c = c.replace(
    "WHERE DATE(tanggal_pembayaran) = CURRENT_DATE - INTERVAL '1 day'",
    "WHERE DATE(tanggal_pembayaran) = DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)"
)

# 8. INTERVAL in getChart14Days()
c = c.replace(
    "WHERE tanggal_pembayaran >= CURRENT_DATE - INTERVAL '14 days'",
    "WHERE tanggal_pembayaran >= DATE_SUB(CURRENT_DATE, INTERVAL 14 DAY)"
)
c = c.replace(
    "WHERE tanggal_pengeluaran >= CURRENT_DATE - INTERVAL '14 days'",
    "WHERE tanggal_pengeluaran >= DATE_SUB(CURRENT_DATE, INTERVAL 14 DAY)"
)

with open(p, 'w', encoding='utf-8') as f:
    f.write(c)

print("SUCCESS: Pesanan model updated for MySQL compatibility")
