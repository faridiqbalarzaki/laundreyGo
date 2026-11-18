<?php
$currentStatus = $status ?? 'Semua';
$searchQuery = $keyword ?? '';
$isKasir = \App\Core\Auth::isKasir();
$ordersList = $orders ?? $pesanan ?? [];

// Map database orders to client JS orders format
$clientOrders = [];
foreach ($ordersList as $row) {
    $code = $row['id_pesanan'] ?? $row['no_nota'] ?? 'LG-000';
    $isPaid = strtolower($row['status_pembayaran'] ?? '') === 'lunas';
    $isKilat = !empty($row['is_kilat']) || stripos($row['daftar_layanan'] ?? '', 'Kilat') !== false;
    $rawStatus = strtolower($row['status_cucian'] ?? 'antrean');
    
    $statusKey = 'antrean';
    if (str_contains($rawStatus, 'cuci')) $statusKey = 'dicuci';
    elseif (str_contains($rawStatus, 'setrika')) $statusKey = 'disetrika';
    elseif (str_contains($rawStatus, 'siap')) $statusKey = 'siap diambil';
    elseif (str_contains($rawStatus, 'selesai') || str_contains($rawStatus, 'ambil')) $statusKey = 'selesai';

    $serv = $row['daftar_layanan'] ?? $row['nama_layanan'] ?? 'Cuci Kering + Setrika';
    $cat = (stripos($serv, 'Satuan') !== false) ? 'Satuan' : ((stripos($serv, 'Setrika') !== false && stripos($serv, 'Cuci') === false) ? 'Setrika' : 'Kiloan');

    $tags = [$cat];
    if ($isKilat) $tags[] = 'Kilat';

    $clientOrders[] = [
        'code' => $code,
        'createdAt' => !empty($row['waktu_masuk']) ? $row['waktu_masuk'] : (!empty($row['tanggal_masuk']) ? $row['tanggal_masuk'] : date('Y-m-d H:i:s')),
        'customer' => $row['nama_pelanggan'] ?? 'Pelanggan Umum',
        'customerName' => $row['nama_pelanggan'] ?? 'Pelanggan Umum',
        'phone' => $row['no_telepon'] ?? '-',
        'customerPhone' => $row['no_telepon'] ?? '-',
        'service' => $serv,
        'serviceName' => $serv,
        'perfume' => '',
        'tags' => $tags,
        'status' => $statusKey,
        'isPaid' => $isPaid,
        'payMethod' => strtolower($row['metode_pembayaran'] ?? 'tunai'),
        'paymentMethod' => $row['metode_pembayaran'] ?? 'Tunai',
        'total' => (float)($row['total_harga'] ?? 0),
        'subtotal' => (float)($row['total_harga'] ?? 0),
        'weight' => (float)($row['berat'] ?? 1),
        'unit' => $row['satuan'] ?? 'kg',
        'isKilat' => $isKilat,
        'rak' => $row['lokasi_rak'] ?? 'A1'
    ];
}
?>

<div class="page-sheet-container is-orders-page">
    <?php if ($isKasir): ?>
        <!-- Header Bar Kasir (Khusus Kasir karena tidak menggunakan topbar global) -->
        <div class="selesai-header-bar">
            <div class="selesai-title-group">
                <h2 class="selesai-page-title">Daftar Pesanan</h2>
                <span class="selesai-sub-pill">Semua Nota</span>
            </div>
            <?php include __DIR__ . '/../layouts/kasir_header_actions.php'; ?>
        </div>
    <?php endif; ?>

    <!-- Toolbar Inline: Pencarian + Filter Berjejer (4 Popover) + Reset + Ekspor -->
    <div class="orders-inline-toolbar orders-filter-bar-wrap">
        <div class="orders-search-box">
            <span class="orders-search-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" id="order-list-search-input" class="form-control"
                placeholder="Cari nota, pelanggan, atau rak..."
                value="<?= htmlspecialchars($searchQuery) ?>"
                oninput="window.ORDERS_APP && window.ORDERS_APP.handleSearch(this.value)">
        </div>
        <div id="orders-filter-strip" class="orders-filter-strip">
            <!-- Diisi otomatis oleh orders.js: ⊙ Status, 🏷 Layanan & Tag, 📅 Tanggal, 💳 Bayar & Tagihan -->
        </div>
        <span id="orders-reset-slot" class="orders-reset-slot"></span>
        <div class="toolbar-end">
            <button type="button" class="btn btn-secondary orders-export-btn" onclick="window.ORDERS_APP && window.ORDERS_APP.exportCsv()" style="height: 32px; font-size: 0.76rem; padding: 0 14px; border-radius: var(--radius-pill); display: inline-flex; align-items: center; gap: 6px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span>Ekspor</span>
            </button>
        </div>
    </div>

    <!-- Tabel Data Pesanan 100vh Penuh (Sticky Header + Footer Pagination di dalam card) -->
    <div class="modern-table-card orders-table-card">
        <div class="orders-table-scroll">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th class="col-check"><input type="checkbox" id="orders-select-all" class="row-check" aria-label="Pilih semua di halaman ini" onchange="window.ORDERS_APP && window.ORDERS_APP.toggleSelectAll(this.checked)"></th>
                        <th>Nomor Nota</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Layanan &amp; Spesifikasi</th>
                        <th>Status Alur</th>
                        <th>Status Bayar</th>
                        <th style="text-align: right;">Total Tagihan</th>
                        <th style="text-align: center;">Aksi Cetak</th>
                    </tr>
                </thead>
                <tbody id="orders-table-tbody" class="orders-table-body">
                    <!-- Populated by orders.js -->
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination Dock (Kapsul Geser dengan Lensa Kaca Meluncur) -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; border-top: 1px solid var(--line); flex-wrap: wrap; gap: 10px;">
            <span id="orders-total-count-label" style="font-size: 0.78rem; color: var(--ink-muted); font-weight: 600;">Total <?= count($clientOrders) ?> pesanan</span>
            <div id="orders-pagination-dock" class="orders-pagination-dock" style="margin-left: auto;">
                <!-- Diisi otomatis oleh orders.js dengan komponen .wg-capsule -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Sandbox untuk Nota & Label Stiker Termal -->
<div id="modal-sandbox"></div>

<script>
    window.ORDERS_DATA = {
        orders: <?= json_encode($clientOrders, JSON_UNESCAPED_UNICODE) ?>
    };
</script>
<script src="<?= \App\Core\Helper::asset('js/orders.js') ?>"></script>
