<?php
$searchQuery = $_GET['q'] ?? '';
$activeRak = $_GET['rak'] ?? 'Semua';
$activeTab = $currentTab ?? ($_GET['tab'] ?? 'aktif');
$ordersList = $orders ?? [];

?>



<div class="page-sheet-container is-orders-page" style="display: flex; flex-direction: column; gap: 10px; padding: 0; height: auto;">
    <!-- Tier 1: Header Bar Cucian Selesai (Foto 4 / index.html) -->
    <div class="selesai-header-bar" style="display: flex; justify-content: space-between; align-items: center;">
        <div class="selesai-title-group" style="display: flex; align-items: center; gap: 10px;">
            <h2 class="selesai-page-title" style="margin: 0; font-size: 1.35rem; font-weight: 800; color: #0F172A;">Cucian Selesai</h2>
            <span class="selesai-sub-pill" style="font-size: 0.74rem; font-weight: 700; background: #E0F2FE; color: #0284C7; padding: 3px 10px; border-radius: 9999px;">
                <?= $activeTab === 'riwayat' ? 'Riwayat Penyerahan Hari Ini' : 'Siap Diambil di Rak' ?>
            </span>
        </div>
        <?php include __DIR__ . '/../layouts/kasir_header_actions.php'; ?>
    </div>

    <!-- Tier 2: Toolbar Inline (Pencarian di kiri, Tab Switcher di tengah, Tombol Ekspor di kanan) -->
    <div class="orders-inline-toolbar orders-filter-bar-wrap" style="display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
        <div class="orders-search-box" style="position: relative; flex: 1 1 240px; max-width: 320px;">
            <span class="orders-search-icon" style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #94A3B8; display: inline-flex;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" id="search-selesai-input" class="form-control"
                placeholder="Cari nota, pelanggan, atau rak..."
                value="<?= htmlspecialchars($searchQuery) ?>"
                oninput="filterSelesaiRows(this.value)"
                style="padding-left: 34px; height: 36px; border-radius: 12px; font-size: 0.82rem; border: 1px solid #E2E8F0;">
        </div>

        <!-- Segmented Tab: Menunggu di Rak (Aktif) vs Diserahkan Hari Ini -->
        <div class="selesai-tab-dock" style="display: inline-flex; background: #F1F5F9; padding: 3px; border-radius: 9999px; gap: 4px;">
            <a href="<?= \App\Core\Helper::url('pesanan', ['action' => 'selesai', 'tab' => 'aktif', 'rak' => $activeRak]) ?>"
                class="selesai-tab-btn <?= $activeTab === 'aktif' ? 'is-active' : '' ?>"
                style="padding: 5px 14px; border-radius: 9999px; font-size: 0.74rem; font-weight: 700; text-decoration: none; transition: all 150ms ease; <?= $activeTab === 'aktif' ? 'background: #0284C7; color: #FFFFFF; box-shadow: 0 1px 3px rgba(2, 132, 199, 0.25);' : 'color: #64748B;' ?>">
                Menunggu di Rak
            </a>
            <a href="<?= \App\Core\Helper::url('pesanan', ['action' => 'selesai', 'tab' => 'riwayat', 'rak' => $activeRak]) ?>"
                class="selesai-tab-btn <?= $activeTab === 'riwayat' ? 'is-active' : '' ?>"
                style="padding: 5px 14px; border-radius: 9999px; font-size: 0.74rem; font-weight: 700; text-decoration: none; transition: all 150ms ease; <?= $activeTab === 'riwayat' ? 'background: #0284C7; color: #FFFFFF; box-shadow: 0 1px 3px rgba(2, 132, 199, 0.25);' : 'color: #64748B;' ?>">
                Diserahkan Hari Ini
            </a>
        </div>

        <button type="button" class="btn btn-secondary" onclick="window.print()" style="height: 32px; font-size: 0.76rem; font-weight: 600; padding: 0 14px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 6px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #0F172A;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            <span>Ekspor</span>
        </button>
    </div>

    <!-- Tier 3: Data Table Card Cucian Selesai (Bebas Horizontal Scroll) -->
    <div class="modern-table-card orders-table-card">
        <div class="orders-table-scroll">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th class="col-check"><input type="checkbox" id="selesai-select-all" class="row-check"></th>
                        <th>Nomor Nota</th>
                        <th>Pelanggan</th>
                        <th>Layanan &amp; Spesifikasi</th>
                        <th style="text-align: center;">Lokasi Rak</th>
                        <th style="text-align: center;">Status Bayar</th>
                        <th style="text-align: right;">Total Tagihan</th>
                        <th style="text-align: center;">Notifikasi WA</th>
                        <th style="text-align: center;">Aksi Penyerahan</th>
                    </tr>
                </thead>
                <tbody id="selesaiTableBody">
                    <?php if (empty($ordersList)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px 16px; color: #94A3B8;">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 8px; display: block; opacity: 0.4;"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                <?= $activeTab === 'riwayat' ? 'Belum ada cucian yang diserahkan hari ini.' : 'Semua cucian di rak telah diserahkan ke pelanggan.' ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ordersList as $order): 
                            $isLunas = (strtolower($order['status_pembayaran'] ?? '') === 'lunas');
                            $orderId = $order['id_pesanan'] ?? $order['no_nota'] ?? 'LG-000';
                            $waClean = preg_replace('/[^0-9]/', '', $order['no_telepon'] ?? '');
                            if (str_starts_with($waClean, '0')) $waClean = '62' . substr($waClean, 1);
                            $rakLocation = $order['lokasi_rak'] ?: 'Rak C-01';
                            $statusDiserahkan = ($order['status_cucian'] === 'Sudah Diambil' || $order['status_cucian'] === 'Selesai');
                            
                            // Ambil spesifikasi layanan murni dari database
                            $serviceTitle = !empty($order['daftar_layanan']) ? $order['daftar_layanan'] : (!empty($order['nama_layanan']) ? $order['nama_layanan'] : 'Cuci Kering + Setrika');
                            $beratVal = !empty($order['total_berat']) ? (float)$order['total_berat'] : 1;
                            $satuanVal = !empty($order['satuan']) ? $order['satuan'] : 'kg';
                            $serviceSub = "{$beratVal} {$satuanVal}";
                            $payMethod = !empty($order['metode_bayar']) ? $order['metode_bayar'] : ($isLunas ? 'Tunai' : '');
                            $rakLocation = !empty($order['lokasi_rak']) ? $order['lokasi_rak'] : 'Belum di rak';
                            $waMessage = urlencode("Halo {$order['nama_pelanggan']}, cucian Anda dengan nomor nota {$orderId} sudah selesai dan siap diambil di LaundryGo ({$rakLocation}). Total tagihan: " . ($isLunas ? 'Lunas' : \App\Core\Helper::rupiah($order['total_harga'] ?? 0)) . ". Terima kasih!");
                        ?>
                            <tr class="selesai-row-item <?= $statusDiserahkan ? 'is-delivered-row' : '' ?>">
                                <td class="col-check"><input type="checkbox" class="row-check"></td>
                                <td>
                                    <span class="table-order-code num-tabular nota-val"><?= htmlspecialchars($orderId) ?></span>
                                </td>
                                <td>
                                    <strong class="cust-val" style="display: block; line-height: 1.25; color: var(--ink); white-space: nowrap;"><?= htmlspecialchars($order['nama_pelanggan'] ?? 'Pelanggan Umum') ?></strong>
                                    <div class="orders-spec-sub" style="font-variant-numeric: tabular-nums; white-space: nowrap;"><?= htmlspecialchars($order['no_telepon'] ?? '-') ?></div>
                                </td>
                                <td>
                                    <strong style="display: block; line-height: 1.25; color: var(--ink); white-space: nowrap;"><?= htmlspecialchars($serviceTitle) ?></strong>
                                    <div class="orders-spec-sub" style="white-space: nowrap;"><?= htmlspecialchars($serviceSub) ?></div>
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <span class="ticket-tag-rack">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        <span class="rak-val"><?= htmlspecialchars($rakLocation) ?></span>
                                    </span>
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <span class="orders-pay-badge" data-paid="<?= $isLunas ? 'true' : 'false' ?>">
                                        <?= $isLunas ? 'Lunas' : 'Belum Lunas' ?>
                                    </span>
                                    <?php if ($isLunas && !empty($payMethod)): ?>
                                        <div class="orders-spec-sub" style="justify-content: center;"><?= htmlspecialchars($payMethod) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="num-tabular font-mono" style="font-size: 0.84rem; font-weight: 700; text-align: right; white-space: nowrap; color: var(--ink);">
                                    <?= \App\Core\Helper::rupiah($order['total_harga'] ?? 0) ?>
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <?php if (!empty($waClean) && $waClean !== '-'): ?>
                                        <a href="https://wa.me/<?= $waClean ?>?text=<?= $waMessage ?>" target="_blank" class="btn btn-secondary orders-action-btn" style="text-decoration: none;" title="Hubungi Pelanggan via WhatsApp">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                            <span>Kabari WA</span>
                                        </a>
                                    <?php else: ?>
                                        <span style="font-size: 0.72rem; color: #94A3B8;">Tanpa WA</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <div style="display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                                        <?php if ($statusDiserahkan): ?>
                                            <span class="orders-delivered-chip">Sudah diserahkan</span>
                                        <?php else: ?>
                                            <?php if ($isLunas): ?>
                                                <form action="<?= \App\Core\Helper::url('pesanan', ['action' => 'serahkan']) ?>" method="POST" style="display: inline;"
                                                    data-confirm="true"
                                                    data-confirm-title="Serahkan Cucian ke Pelanggan?"
                                                    data-confirm-message="Pastikan fisik cucian telah diperiksa dengan teliti dan diserahkan ke pelanggan."
                                                    data-confirm-badge="<?= htmlspecialchars($orderId) ?> &bull; <?= htmlspecialchars($order['nama_pelanggan'] ?? 'Pelanggan Umum') ?> &bull; <?= htmlspecialchars($rakLocation) ?>"
                                                    data-confirm-btn="Ya, Serahkan Cucian"
                                                    data-confirm-type="primary">
                                                    <input type="hidden" name="id_pesanan" value="<?= $order['id_pesanan'] ?>">
                                                    <button type="submit" class="btn btn-primary orders-action-btn">
                                                        Serahkan
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form action="<?= \App\Core\Helper::url('pesanan', ['action' => 'bayarDanSerahkan']) ?>" method="POST" style="display: inline;"
                                                    data-confirm="true"
                                                    data-confirm-payment="true"
                                                    data-confirm-total="<?= (float)($order['total_harga'] ?? 0) ?>"
                                                    data-confirm-title="Terima Pembayaran &amp; Serahkan Cucian?"
                                                    data-confirm-message="Tagihan sebesar <?= \App\Core\Helper::rupiah($order['total_harga'] ?? 0) ?> akan diproses lunas dan pesanan diserahkan ke pelanggan."
                                                    data-confirm-badge="<?= htmlspecialchars($orderId) ?> &bull; <?= htmlspecialchars($order['nama_pelanggan'] ?? 'Pelanggan Umum') ?> &bull; <?= \App\Core\Helper::rupiah($order['total_harga'] ?? 0) ?>"
                                                    data-confirm-btn="Ya, Lunasi &amp; Serahkan"
                                                    data-confirm-type="primary">
                                                    <input type="hidden" name="id_pesanan" value="<?= $order['id_pesanan'] ?>">
                                                    <input type="hidden" name="id_metode" value="MET-01">
                                                    <input type="hidden" name="jumlah_bayar" value="<?= (float)($order['total_harga'] ?? 0) ?>">
                                                    <button type="submit" class="btn btn-secondary orders-action-btn btn-outline-pay">
                                                        Bayar &amp; Ambil
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <!-- Tombol Undur ke Lantai Produksi (Setrika Ulang) -->
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar (Persis Foto 2: < Sebelumnya 1 2 3 4 5 6 7 Berikutnya >) -->
        <div style="display: flex; justify-content: flex-end; align-items: center; padding: 8px 16px; border-top: 1px solid var(--line); background: #FFFFFF;">
            <div id="selesai-pagination-dock" class="orders-pagination-dock" style="margin-left: auto;">
                <nav id="pagi-nav-selesai" class="wg-capsule" aria-label="Halaman Cucian Selesai">
                    <button type="button" id="pagi-prev-selesai" class="wg-nav is-prev" onclick="goToSelesaiPage(currentSelesaiPage - 1)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="width: 12px; height: 12px;"><path d="M15 5l-7 7 7 7"/></svg>
                        <span>Sebelumnya</span>
                    </button>
                    <div id="pagi-track-selesai" class="wg-pages-track">
                        <!-- Dynamically populated by JS: 1 2 3 4 5 6 7 -->
                    </div>
                    <button type="button" id="pagi-next-selesai" class="wg-nav is-next" onclick="goToSelesaiPage(currentSelesaiPage + 1)">
                        <span>Berikutnya</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="width: 12px; height: 12px;"><path d="M9 5l7 7-7 7"/></svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</div>

<script>
const SELESAI_PAGE_SIZE = 8;
let currentSelesaiPage = 1;
let activeRows = [];

function initSelesaiPagination() {
    activeRows = Array.from(document.querySelectorAll('.selesai-row-item'));
    const urlParams = new URLSearchParams(window.location.search);
    const initialPage = parseInt(urlParams.get('pagi') || '1', 10);
    renderSelesaiPage(initialPage);
    window.addEventListener('resize', () => syncSelesaiPaginationPill(true));
}

function getSelesaiPaginationWindow(totalPages, activePage) {
    if (totalPages <= 7) {
        return Array.from({ length: totalPages }, (_, i) => i + 1);
    }
    let start = Math.max(2, activePage - 1);
    let end = Math.min(totalPages - 1, activePage + 1);
    if (activePage <= 3) { start = 2; end = 4; }
    if (activePage >= totalPages - 2) { start = totalPages - 3; end = totalPages - 1; }
    const items = [1];
    if (start > 2) items.push('gap');
    for (let i = start; i <= end; i++) items.push(i);
    if (end < totalPages - 1) items.push('gap');
    items.push(totalPages);
    return items;
}

function syncSelesaiPaginationPill(instant = false) {
    const trackEl = document.getElementById('pagi-track-selesai');
    const lensEl = document.getElementById('pagi-lens-selesai');
    if (!trackEl || !lensEl) return;

    const activeDot = trackEl.querySelector('.pagination-dot.is-active');
    if (!activeDot || activeDot.offsetWidth === 0) {
        lensEl.style.opacity = '0';
        return;
    }

    const targetX = activeDot.offsetLeft;
    const targetW = activeDot.offsetWidth;
    lensEl.style.width = `${targetW}px`;

    if (instant) {
        lensEl.style.transition = 'none';
        lensEl.style.transform = `translate3d(${targetX}px, 0, 0)`;
        lensEl.style.opacity = '1';
        void lensEl.offsetWidth;
        lensEl.style.transition = '';
    } else {
        lensEl.style.transform = `translate3d(${targetX}px, 0, 0)`;
        lensEl.style.opacity = '1';
    }
}

function renderSelesaiPage(page) {
    const totalItems = activeRows.length;
    const totalPages = Math.max(1, Math.ceil(totalItems / SELESAI_PAGE_SIZE));
    currentSelesaiPage = Math.min(Math.max(1, page), totalPages);

    const start = (currentSelesaiPage - 1) * SELESAI_PAGE_SIZE;
    const end = start + SELESAI_PAGE_SIZE;

    // Show/hide rows
    activeRows.forEach((row, idx) => {
        row.style.display = (idx >= start && idx < end) ? '' : 'none';
    });

    // Update Track Dots with Sliding Bubble Lens
    const trackEl = document.getElementById('pagi-track-selesai');
    if (trackEl) {
        const windowItems = getSelesaiPaginationWindow(totalPages, currentSelesaiPage);
        let dotsHtml = '<span id="pagi-lens-selesai" class="wg-active-pill" aria-hidden="true"></span>';
        
        windowItems.forEach(item => {
            if (item === 'gap') {
                dotsHtml += `<span class="pagination-gap" aria-hidden="true">&hellip;</span>`;
            } else {
                const isActive = (item === currentSelesaiPage);
                dotsHtml += `<button type="button" class="pagination-dot ${isActive ? 'is-active' : ''}" data-page="${item}" aria-label="Halaman ${item}" ${isActive ? 'aria-current="page"' : ''} onclick="goToSelesaiPage(${item})">${item}</button>`;
            }
        });
        trackEl.innerHTML = dotsHtml;
        
        // Trigger instant or smooth sync after DOM paint
        requestAnimationFrame(() => {
            syncSelesaiPaginationPill(false);
        });
    }

    // Prev / Next button states
    const prevBtn = document.getElementById('pagi-prev-selesai');
    const nextBtn = document.getElementById('pagi-next-selesai');
    if (prevBtn) {
        prevBtn.disabled = (currentSelesaiPage <= 1);
    }
    if (nextBtn) {
        nextBtn.disabled = (currentSelesaiPage >= totalPages);
    }
}

function goToSelesaiPage(page) {
    renderSelesaiPage(page);
    const scrollContainer = document.querySelector('.orders-table-scroll');
    if (scrollContainer) scrollContainer.scrollTop = 0;
}

function filterSelesaiRows(q) {
    q = q.toLowerCase().trim();
    const allTableRows = Array.from(document.querySelectorAll('.selesai-row-item'));
    activeRows = allTableRows.filter(row => {
        const nota = (row.querySelector('.nota-val')?.textContent || '').toLowerCase();
        const cust = (row.querySelector('.cust-val')?.textContent || '').toLowerCase();
        const rak = (row.querySelector('.rak-val')?.textContent || '').toLowerCase();
        const match = nota.includes(q) || cust.includes(q) || rak.includes(q);
        if (!match) row.style.display = 'none';
        return match;
    });
    renderSelesaiPage(1);
}

document.addEventListener('DOMContentLoaded', initSelesaiPagination);
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    initSelesaiPagination();
}
</script>
