<?php
$omzet = (float)($metrics['omzet_today'] ?? 0);
$netProfit = $omzet - (float)($expToday ?? 0);
$activeTotal = (int)($metrics['total_aktif'] ?? 0);
$readyTotal = (int)($metrics['total_siap'] ?? 0);
$cDiterima = (int)($stageCounts['Antrean'] ?? 0);
$cDicuci = (int)($stageCounts['Dicuci'] ?? 0);
$cDisetrika = (int)($stageCounts['Disetrika'] ?? 0);
$readyRackList = !empty($readyRacks) ? implode(', ', $readyRacks) : 'Belum ada di rak';

// Chart maximum value for scaling bars
$maxBarVal = 1;
foreach ($chartData as $cd) {
    if ($cd['omset'] > $maxBarVal) $maxBarVal = $cd['omset'];
    if ($cd['pengeluaran'] > $maxBarVal) $maxBarVal = $cd['pengeluaran'];
}
?>
<div class="page-sheet-container" style="display: flex; flex-direction: column; gap: 14px;">
    <!-- Bar Kontrol Ringkas -->
    <div class="dash-control-bar">
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <div class="period-pills-group" role="tablist" aria-label="Periode dashboard">
                <button type="button" class="period-pill-btn active">Hari ini</button>
                <button type="button" class="period-pill-btn">7 hari</button>
                <button type="button" class="period-pill-btn">Bulan ini</button>
            </div>
            <span style="font-size: 0.74rem; color: var(--ink-muted); font-weight: 500;">Data real-time kasir &amp; produksi</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="<?= \App\Core\Helper::url('pesanan') ?>" class="btn btn-primary" style="height: 32px; font-size: 0.76rem; padding: 0 12px;">Daftar Pesanan</a>
            <a href="<?= \App\Core\Helper::url('laporan') ?>" class="btn btn-secondary has-trailing-icon" style="height: 32px; font-size: 0.76rem;">
                <span>Laporan</span>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </div>

    <!-- Baris 1: 3 Kartu Metrik Inti -->
    <div class="dash-grid-row1">
        <!-- Kartu 1: Pemasukan Lunas + Area Sparkline -->
        <div class="bento-card is-hero-aqua">
            <div class="bento-card-head">
                <span class="bento-card-title">Pemasukan lunas (Hari ini)</span>
                <?php 
                $growth = (float)($metrics['growth_percent'] ?? 0); 
                $growthClass = $growth >= 0 ? 'is-success' : 'is-warn';
                $growthSign = $growth >= 0 ? '&#9650; +' : '&#9660; ';
                ?>
                <span class="semantic-pill <?= $growthClass ?>"><?= $growthSign ?><?= number_format(abs($growth), 1, ',', '.') ?>%</span>
            </div>
            <div class="dash-huge-amount num-tabular"><?= \App\Core\Helper::rupiah($omzet) ?></div>
            <div class="dash-sub-meta num-tabular">
                Laba bersih <strong><?= \App\Core\Helper::rupiah($netProfit) ?></strong> &bull; Efisiensi kas <?= (int)($cashEfficiency ?? 100) ?>%
            </div>
            <div class="area-sparkline-box" style="margin-top: auto; padding-top: 8px;">
                <svg viewBox="0 0 300 70" preserveAspectRatio="none" style="width: 100%; height: 58px; overflow: visible;">
                    <defs>
                        <linearGradient id="aquaAreaGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#0284c7" stop-opacity="0.32"/>
                            <stop offset="100%" stop-color="#0284c7" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <polygon points="<?= htmlspecialchars($sparkPolygon ?? '0,55 300,55 300,70 0,70') ?>" fill="url(#aquaAreaGrad)"/>
                    <polyline points="<?= htmlspecialchars($sparkPolyline ?? '0,55 300,55') ?>" fill="none" stroke="#0284c7" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="<?= htmlspecialchars((string)($lastSparkX ?? 300)) ?>" cy="<?= htmlspecialchars((string)($lastSparkY ?? 55)) ?>" r="4.5" fill="#0284c7" stroke="#ffffff" stroke-width="2"/>
                </svg>
            </div>
        </div>

        <!-- Kartu 2: Efisiensi & Antrean Produksi -->
        <div class="bento-card">
            <div class="bento-card-head">
                <div>
                    <div class="bento-card-title">Antrean &amp; efisiensi</div>
                    <div class="bento-card-sub">Proses cuci &amp; beban biaya</div>
                </div>
                <a href="<?= \App\Core\Helper::url('pesanan') ?>" class="bento-arrow-btn" title="Lihat Pesanan Sedang Dikerjakan" aria-label="Lihat Pesanan">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>

            <div class="donut-pipeline-split" style="margin: auto 0; display: flex; align-items: center; gap: 14px;">
                <!-- Mini Donut SVG -->
                <div style="position: relative; width: 68px; height: 68px; flex-shrink: 0;">
                    <svg width="68" height="68" viewBox="0 0 36 36" style="transform: rotate(-90deg);">
                        <circle cx="18" cy="18" r="14" fill="none" stroke="var(--surface-muted)" stroke-width="4.5"/>
                        <circle cx="18" cy="18" r="14" fill="none" stroke="#0284c7" stroke-width="4.5" stroke-dasharray="<?= (int)($completedRatio ?? 0) ?>, 100" stroke-linecap="round"/>
                    </svg>
                    <span style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 0.72rem; font-weight: 800; color: var(--ink);"><?= (int)($completedRatio ?? 0) ?>%</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 3px; min-width: 0;">
                    <div style="display: flex; align-items: baseline; gap: 6px;">
                        <span class="dash-huge-amount num-tabular" style="font-size: 1.65rem;"><?= $activeTotal ?></span>
                        <span style="font-size: 0.76rem; color: var(--ink-muted); font-weight: 600;">sedang dikerjakan</span>
                    </div>
                    <span style="font-size: 0.71rem; color: var(--ink-subtle);">Biaya operasional <?= \App\Core\Helper::rupiah($expToday) ?></span>
                </div>
            </div>

            <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: auto;">
                <span class="semantic-pill" style="background: var(--surface-subtle); color: var(--ink-secondary);">&bull; <?= $cDiterima ?> Diterima</span>
                <span class="semantic-pill is-info">&bull; <?= $cDicuci ?> Dicuci</span>
                <span class="semantic-pill is-warn">&bull; <?= $cDisetrika ?> Disetrika</span>
            </div>
        </div>

        <!-- Kartu 3: Gerai & Serah Terima -->
        <div class="bento-card">
            <div class="outlet-signature-card" style="background: var(--primary-gradient); border-radius: 14px; padding: 12px 14px; color: #fff; display: flex; flex-direction: column; gap: 6px;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 0.76rem; font-weight: 800;">Adi Laundry &bull; Karangploso</span>
                    <span class="outlet-card-pill" style="background: rgba(255,255,255,0.22); font-size: 0.65rem; padding: 2px 7px; border-radius: 9999px; font-weight: 700;"><?= (int)($cashPercent ?? 100) ?>% Tunai</span>
                </div>
                <div style="display: flex; align-items: baseline; justify-content: space-between; gap: 6px;">
                    <span class="num-tabular" style="font-size: 1.35rem; font-weight: 800; line-height: 1.15;"><?= $totalOrdersCount ?> Pesanan</span>
                    <span style="font-size: 0.7rem; color: rgba(255,255,255,0.92); font-weight: 600;"><?= $readyTotal ?> Siap</span>
                </div>
                <div class="num-tabular" style="font-size: 0.68rem; color: rgba(255,255,255,0.85); font-weight: 600;">
                    Rak: <?= htmlspecialchars($readyRackList) ?>
                </div>
            </div>

            <div class="dash-mini-split" style="margin-top: auto; display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                <a href="<?= \App\Core\Helper::url('pesanan', ['action' => 'selesai']) ?>" class="dash-mini-box" style="text-decoration: none; padding: 8px 10px; background: var(--surface-muted); border-radius: 10px; display: flex; flex-direction: column; gap: 2px;" title="Lihat pesanan siap diambil">
                    <span class="dash-mini-label" style="font-size: 0.68rem; color: var(--ink-muted);">Belum kabari WA</span>
                    <span class="dash-mini-val num-tabular" style="font-size: 0.88rem; font-weight: 800; color: var(--primary);"><?= $readyTotal ?> nota</span>
                </a>
                <a href="<?= \App\Core\Helper::url('pesanan') ?>" class="dash-mini-box" style="text-decoration: none; padding: 8px 10px; background: var(--surface-muted); border-radius: 10px; display: flex; flex-direction: column; gap: 2px;" title="Lihat tagihan belum lunas">
                    <span class="dash-mini-label" style="font-size: 0.68rem; color: var(--ink-muted);">Belum lunas (<?= $unpaidCount ?>)</span>
                    <span class="dash-mini-val num-tabular" style="font-size: 0.88rem; font-weight: 800; color: var(--danger);"><?= \App\Core\Helper::rupiah($unpaidTotal) ?></span>
                </a>
            </div>
        </div>
    </div>

    <!-- Baris 2: Tren 14 Hari Kapsul & Pesanan Terbaru -->
    <div class="dash-grid-row2">
        <!-- Kartu 4: Tren 14 Hari Kapsul -->
        <div class="bento-card">
            <div class="bento-card-head">
                <div>
                    <div class="bento-card-title">Tren pemasukan &amp; pengeluaran (14 hari)</div>
                    <div class="bento-card-sub">Bilah grafik harian omset dan beban operasional</div>
                </div>
                <div class="period-pills-group" role="tablist" aria-label="Filter grafik 14 hari">
                    <button type="button" class="period-pill-btn active" style="height: 24px; padding: 0 9px; font-size: 0.69rem;">Semua</button>
                    <button type="button" class="period-pill-btn" style="height: 24px; padding: 0 9px; font-size: 0.69rem;">Masuk</button>
                    <button type="button" class="period-pill-btn" style="height: 24px; padding: 0 9px; font-size: 0.69rem;">Keluar</button>
                </div>
            </div>

            <div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 6px; height: 160px; padding-top: 16px; border-bottom: 1px solid var(--line);">
                <?php foreach ($chartData as $cd): 
                    $hOmset = $maxBarVal > 0 ? max(6, round(($cd['omset'] / $maxBarVal) * 120)) : 6;
                    $hExp = $maxBarVal > 0 ? max(4, round(($cd['pengeluaran'] / $maxBarVal) * 120)) : 4;
                ?>
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px; flex: 1; height: 100%; justify-content: flex-end;" title="<?= $cd['label'] ?>: Omset <?= \App\Core\Helper::rupiah($cd['omset']) ?>, Biaya <?= \App\Core\Helper::rupiah($cd['pengeluaran']) ?>">
                        <div style="display: flex; gap: 3px; align-items: flex-end; height: 120px;">
                            <div style="width: 7px; height: <?= $hOmset ?>px; background: var(--primary); border-radius: 4px 4px 0 0;" class="chart-bar-omset"></div>
                            <div style="width: 7px; height: <?= $hExp ?>px; background: #cbd5e1; border-radius: 4px 4px 0 0;" class="chart-bar-exp"></div>
                        </div>
                        <span style="font-size: 0.62rem; color: var(--ink-muted); font-weight: 600; white-space: nowrap;"><?= substr($cd['label'], 0, 2) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 14px; margin-top: 10px; font-size: 0.72rem; color: var(--ink-muted);">
                <span style="display: inline-flex; align-items: center; gap: 5px;">
                    <span style="width: 8px; height: 8px; background: var(--primary); border-radius: 2px;"></span> Pemasukan
                </span>
                <span style="display: inline-flex; align-items: center; gap: 5px;">
                    <span style="width: 8px; height: 8px; background: #cbd5e1; border-radius: 2px;"></span> Pengeluaran
                </span>
            </div>
        </div>

        <!-- Kartu 5: Pesanan Terbaru -->
        <div class="bento-card">
            <div class="bento-card-head">
                <div>
                    <div class="bento-card-title">Pesanan terbaru</div>
                    <div class="bento-card-sub">4 transaksi terakhir yang dicatat kasir</div>
                </div>
                <a href="<?= \App\Core\Helper::url('pesanan') ?>" class="bento-arrow-btn" title="Lihat Seluruh Pesanan" aria-label="Lihat Pesanan">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 6px;">
                <?php if (empty($recentOrders)): ?>
                    <div style="text-align: center; padding: 24px; color: var(--ink-muted); font-size: 0.8rem;">
                        Belum ada pesanan yang tercatat hari ini.
                    </div>
                <?php else: ?>
                    <?php foreach ($recentOrders as $order): 
                        $statusClass = 'badge-diterima';
                        if ($order['status_cucian'] === 'Dicuci') $statusClass = 'badge-dicuci';
                        elseif ($order['status_cucian'] === 'Disetrika') $statusClass = 'badge-disetrika';
                        elseif ($order['status_cucian'] === 'Siap Diambil') $statusClass = 'badge-selesai';
                        elseif ($order['status_cucian'] === 'Sudah Diambil') $statusClass = 'badge-diambil';
                    ?>
                        <div class="recent-tx-row" style="display: flex; align-items: center; justify-content: space-between; padding: 9px 12px; background: var(--surface-subtle); border-radius: 12px; border: 1px solid var(--line);">
                            <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                                <div style="width: 34px; height: 34px; border-radius: 10px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.74rem; flex-shrink: 0;">
                                    <?= htmlspecialchars(substr($order['id_pesanan'] ?? $order['no_nota'] ?? 'LG', -3)) ?>
                                </div>
                                <div style="min-width: 0;">
                                    <div style="font-size: 0.82rem; font-weight: 700; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?= htmlspecialchars($order['nama_pelanggan'] ?? 'Pelanggan') ?>
                                    </div>
                                    <div style="font-size: 0.71rem; color: var(--ink-muted);">
                                        <?= htmlspecialchars($order['id_pesanan'] ?? $order['no_nota'] ?? '') ?> &bull; <?= htmlspecialchars($order['daftar_layanan'] ?? $order['nama_layanan'] ?? 'Cuci Komplit') ?>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 3px; flex-shrink: 0;">
                                <span class="badge-status <?= $statusClass ?>" style="font-size: 0.68rem; padding: 2px 8px; border-radius: 9999px;">
                                    <?= htmlspecialchars($order['status_cucian']) ?>
                                </span>
                                <span class="num-tabular" style="font-size: 0.78rem; font-weight: 700; color: var(--ink);">
                                    <?= \App\Core\Helper::rupiah($order['total_harga']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
