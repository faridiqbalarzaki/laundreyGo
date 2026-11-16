<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Lacak Cucian - LaundryGo') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= \App\Core\Helper::asset('css/style.css') ?>">
    <link rel="icon" type="image/png" href="<?= \App\Core\Helper::asset('img/logo_icon_drop.png') ?>">
</head>
<body class="tracking-body">
    <div class="tracking-wrapper">
        <!-- HEADER -->
        <header class="tracking-header">
            <a href="index.php?page=login" class="tracking-brand">
                <img src="<?= \App\Core\Helper::asset('img/logo_icon_drop.png') ?>" alt="LaundryGo">
                <span>LaundryGo <strong>Aqua</strong></span>
            </a>
            <a href="index.php?page=login" class="btn btn-secondary btn-sm">Masuk Pegawai &rarr;</a>
        </header>

        <!-- SEARCH BOX -->
        <div class="tracking-search-card">
            <h1>Lacak Status Cucian Anda</h1>
            <p class="text-muted">Masukkan nomor nota yang tertera pada struk fisik kasir Anda.</p>
            <form action="index.php" method="GET" class="tracking-form">
                <input type="hidden" name="page" value="tracking">
                <div class="input-with-icon full-width">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="nota" value="<?= htmlspecialchars($keyword) ?>" placeholder="Contoh: LG-261114-001" required autofocus>
                </div>
                <button type="submit" class="btn btn-primary">Lacak Sekarang</button>
            </form>
        </div>

        <!-- RESULT SECTION -->
        <?php if ($error): ?>
            <div class="tracking-alert-error card">
                <svg width="32" height="32" fill="none" stroke="#ef4444" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <h3>Nota Tidak Ditemukan</h3>
                    <p class="text-muted"><?= htmlspecialchars($error) ?></p>
                </div>
            </div>
        <?php elseif ($order): ?>
            <div class="card tracking-result-card">
                <div class="result-top-banner">
                    <div>
                        <span class="text-muted" style="font-size:12px;">Nomor Nota:</span>
                        <h2 class="result-invoice font-bold"><?= $order['id_pesanan'] ?></h2>
                    </div>
                    <div>
                        <span class="badge-status <?= str_replace(' ', '', $order['status_cucian']) ?>" style="font-size:14px;padding:6px 14px;">
                            <?= $order['status_cucian'] ?>
                        </span>
                    </div>
                </div>

                <!-- PROGRESS TIMELINE BAR -->
                <div class="tracking-timeline-bar">
                    <?php
                    $steps = ['Antrean', 'Dicuci', 'Disetrika', 'Siap Diambil', 'Selesai'];
                    $currentIdx = array_search($order['status_cucian'], $steps);
                    if ($currentIdx === false) $currentIdx = 0;
                    ?>
                    <?php foreach ($steps as $idx => $st): 
                        $isCompleted = ($idx <= $currentIdx);
                        $isCurrent = ($idx === $currentIdx);
                    ?>
                        <div class="timeline-step <?= $isCompleted ? 'completed' : '' ?> <?= $isCurrent ? 'current' : '' ?>">
                            <div class="step-circle"><?= $isCompleted ? '✓' : ($idx + 1) ?></div>
                            <div class="step-label"><?= $st ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- INFO GRID -->
                <div class="tracking-info-grid">
                    <div class="info-item">
                        <span class="text-muted">Nama Pelanggan:</span>
                        <strong><?= htmlspecialchars($order['nama_pelanggan']) ?></strong>
                    </div>
                    <div class="info-item">
                        <span class="text-muted">Waktu Masuk:</span>
                        <span><?= date('d M Y, H:i', strtotime($order['tanggal_masuk'])) ?> WIB</span>
                    </div>
                    <div class="info-item">
                        <span class="text-muted">Estimasi Selesai:</span>
                        <span class="font-semibold text-primary"><?= date('d M Y, H:i', strtotime($order['tanggal_keluar'])) ?> WIB</span>
                    </div>
                    <div class="info-item">
                        <span class="text-muted">Lokasi Rak Ambil:</span>
                        <strong class="text-success"><?= htmlspecialchars($order['lokasi_rak'] ?: 'Dalam Proses Pengerjaan') ?></strong>
                    </div>
                    <div class="info-item">
                        <span class="text-muted">Pilihan Parfum:</span>
                        <span>🌸 <?= htmlspecialchars($order['parfum'] ?? 'Ocean Fresh') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="text-muted">Status Pembayaran:</span>
                        <span class="<?= $order['status_pembayaran'] === 'Lunas' ? 'badge-lunas' : 'badge-belum-lunas' ?>">
                            <?= $order['status_pembayaran'] ?>
                        </span>
                    </div>
                </div>

                <!-- RINCIAN LAYANAN -->
                <div class="tracking-items-section">
                    <h3>Rincian Cucian:</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Layanan</th>
                                <th>Jumlah</th>
                                <th class="text-right">Tarif Satuan</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $it): ?>
                                <tr>
                                    <td class="font-semibold"><?= htmlspecialchars($it['nama_layanan']) ?></td>
                                    <td><?= (float)$it['jumlah'] ?> <?= $it['satuan'] ?></td>
                                    <td class="text-right"><?= \App\Core\Helper::rupiah($it['harga_satuan']) ?></td>
                                    <td class="text-right font-bold"><?= \App\Core\Helper::rupiah($it['jumlah'] * $it['harga_satuan']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">TOTAL TAGIHAN:</th>
                                <th class="text-right font-bold text-primary" style="font-size:16px;">
                                    <?= \App\Core\Helper::rupiah($order['total_harga']) ?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <?php if (!empty($order['timeline'])): ?>
                    <div class="tracking-log-section">
                        <h3>Riwayat Status Pengerjaan:</h3>
                        <ul class="tracking-history-list">
                            <?php foreach ($order['timeline'] as $tl): ?>
                                <li>
                                    <span class="history-time"><?= date('d/m/Y H:i', strtotime($tl['waktu_status'])) ?></span>
                                    <strong class="history-status"><?= htmlspecialchars($tl['status']) ?></strong>
                                    <span class="history-note text-muted">— <?= htmlspecialchars($tl['keterangan']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
