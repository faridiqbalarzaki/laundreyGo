<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Lacak Status Cucian - LaundryGo Aqua', ENT_QUOTES, 'UTF-8') ?></title>
    
    <!-- Google Fonts Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- LaundryGo Tracking & Toast Stylesheet -->
    <link rel="stylesheet" href="<?= \App\Core\Helper::asset('css/tracking.css') ?>">
    <link rel="stylesheet" href="<?= \App\Core\Helper::asset('css/toast.css') ?>">
    <link rel="icon" type="image/png" href="<?= \App\Core\Helper::asset('img/logo_icon_drop.png') ?>">
</head>
<body class="tracking-body">
    <div class="tracking-wrapper">
        <!-- HEADER -->
        <header class="tracking-header">
            <a href="index.php?page=login" class="tracking-brand">
                <img src="<?= \App\Core\Helper::asset('img/logo_icon_drop.png') ?>" alt="LaundryGo Aqua" class="tracking-brand-logo">
                <div class="tracking-brand-name">
                    LaundryGo <span>Aqua</span>
                    <span class="tracking-outlet-chip">Adi Laundry</span>
                </div>
            </a>
            <a href="index.php?page=login" class="tracking-back-btn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                <span>Masuk Pegawai</span>
            </a>
        </header>

        <!-- SEARCH CARD -->
        <div class="tracking-search-card">
            <h1 class="tracking-search-title">Lacak Status Cucian Anda</h1>
            <p class="tracking-search-desc">Masukkan nomor nota resmi yang tertera pada struk fisik kasir LaundryGo Anda.</p>
            
            <form action="index.php" method="GET" class="tracking-form" onsubmit="return validateTrackingSubmit(this);">
                <input type="hidden" name="page" value="tracking">
                <div class="tracking-input-group">
                    <div class="tracking-input-wrapper">
                        <span class="tracking-input-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" id="nota-input" name="nota" class="tracking-input"
                               value="<?= htmlspecialchars($keyword ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                               placeholder="Contoh: LG-261114-001" 
                               autocomplete="off" spellcheck="false"
                               oninput="handleTrackInput(this)"
                               required autofocus>
                    </div>
                    <button type="submit" class="tracking-submit-btn">
                        <span>Lacak Sekarang</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </div>
                <div id="nota-hint" class="tracking-format-hint">
                    Format nota resmi: <strong>LG-YYMMDD-XXX</strong> (contoh: <strong>LG-261114-001</strong>).
                </div>
            </form>
        </div>

        <!-- ERROR NOTIFICATION -->
        <?php if (!empty($error)): ?>
            <div class="tracking-alert-error" role="alert">
                <div class="tracking-alert-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div class="tracking-alert-content">
                    <h3><?= ($errorType === 'invalid_format') ? 'Format Nomor Nota Tidak Valid' : 'Nomor Nota Tidak Ditemukan' ?></h3>
                    <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- RESULT SECTION -->
        <?php if (!empty($order)): ?>
            <div class="tracking-result-card">
                <div class="result-top-banner">
                    <div>
                        <div class="result-invoice-lbl">Nomor Nota Struk</div>
                        <h2 class="result-invoice-code"><?= htmlspecialchars($order['id_pesanan'], ENT_QUOTES, 'UTF-8') ?></h2>
                    </div>
                    <div>
                        <?php 
                        $statusClass = str_replace(' ', '', $order['status_cucian']); 
                        ?>
                        <span class="badge-status <?= htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($order['status_cucian'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                </div>

                <!-- PROGRESS TIMELINE STEPPER -->
                <div class="tracking-timeline-bar" aria-label="Progress pengerjaan cucian">
                    <?php
                    $steps = ['Antrean', 'Dicuci', 'Disetrika', 'Siap Diambil', 'Selesai'];
                    $currentIdx = array_search($order['status_cucian'], $steps);
                    if ($currentIdx === false) $currentIdx = 0;
                    ?>
                    <?php foreach ($steps as $idx => $st): 
                        $isCompleted = ($idx < $currentIdx);
                        $isCurrent = ($idx === $currentIdx);
                    ?>
                        <div class="timeline-step <?= $isCompleted ? 'completed' : '' ?> <?= $isCurrent ? 'current' : '' ?>">
                            <div class="step-circle"><?= $isCompleted ? '✓' : ($idx + 1) ?></div>
                            <div class="step-label"><?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- INFO GRID -->
                <div class="tracking-info-grid">
                    <div class="info-item">
                        <span class="info-item-lbl">Nama Pelanggan</span>
                        <strong class="info-item-val"><?= htmlspecialchars($order['nama_pelanggan'], ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>
                    <div class="info-item">
                        <span class="info-item-lbl">Waktu Diterima Kasir</span>
                        <span class="info-item-val num-tabular"><?= date('d M Y, H:i', strtotime($order['tanggal_masuk'])) ?> WIB</span>
                    </div>
                    <div class="info-item">
                        <span class="info-item-lbl">Estimasi Waktu Selesai (SLA)</span>
                        <span class="info-item-val highlight num-tabular"><?= date('d M Y, H:i', strtotime($order['tanggal_keluar'])) ?> WIB</span>
                    </div>
                    <div class="info-item">
                        <span class="info-item-lbl">Lokasi Rak Pengambilan</span>
                        <strong class="info-item-val success">
                            <?= htmlspecialchars($order['lokasi_rak'] ?: 'Dalam Proses Pengerjaan', ENT_QUOTES, 'UTF-8') ?>
                        </strong>
                    </div>
                    <div class="info-item">
                        <span class="info-item-lbl">Pilihan Parfum Laundry</span>
                        <span class="info-item-val">🌸 <?= htmlspecialchars($order['parfum'] ?? 'Ocean Fresh', ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-item-lbl">Status Pembayaran</span>
                        <span class="<?= $order['status_pembayaran'] === 'Lunas' ? 'badge-paid' : 'badge-unpaid' ?>">
                            <?= htmlspecialchars($order['status_pembayaran'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                </div>

                <!-- RINCIAN LAYANAN & TOTAL -->
                <div class="tracking-items-section">
                    <h3 class="section-heading">Rincian Layanan Cucian</h3>
                    <div class="tracking-table-wrap">
                        <table class="tracking-table">
                            <thead>
                                <tr>
                                    <th>Layanan</th>
                                    <th>Kuantitas / Bobot</th>
                                    <th class="text-right">Tarif Satuan</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order['items'] as $it): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($it['nama_layanan'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                        <td class="num-tabular"><?= (float)$it['jumlah'] ?> <?= htmlspecialchars($it['satuan'] ?? 'Kg', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-right num-tabular"><?= \App\Core\Helper::rupiah($it['harga_satuan']) ?></td>
                                        <td class="text-right font-bold num-tabular"><?= \App\Core\Helper::rupiah($it['jumlah'] * $it['harga_satuan']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right">TOTAL TAGIHAN:</td>
                                    <td class="text-right font-bold num-tabular" style="color: var(--aqua-primary); font-size: 1.05rem;">
                                        <?= \App\Core\Helper::rupiah($order['total_harga']) ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- RIWAYAT PENGERJAAN -->
                <?php if (!empty($order['timeline'])): ?>
                    <div class="tracking-log-section">
                        <h3 class="section-heading">Riwayat Pengerjaan</h3>
                        <ul class="tracking-history-list">
                            <?php foreach ($order['timeline'] as $tl): ?>
                                <li class="tracking-history-item">
                                    <span class="history-time"><?= date('d/m/Y H:i', strtotime($tl['waktu_status'])) ?></span>
                                    <strong class="history-status"><?= htmlspecialchars($tl['status'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <span class="history-note">— <?= htmlspecialchars($tl['keterangan'], ENT_QUOTES, 'UTF-8') ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
    function handleTrackInput(input) {
        // Otomatis huruf besar dan batasi karakter hanya A-Z, 0-9, dan minus (-)
        input.value = input.value.toUpperCase().replace(/[^A-Z0-9\-]/g, '');
        input.classList.remove('is-invalid');
        
        const hint = document.getElementById('nota-hint');
        if (input.value.length > 0 && !input.value.startsWith('LG-') && !input.value.startsWith('L')) {
            if (hint) hint.innerHTML = '<span style="color: #f59e0b; font-weight: 600;">💡 Tip: Nomor nota kasir selalu diawali dengan <strong>LG-</strong></span>';
        } else if (hint) {
            hint.innerHTML = 'Format nota resmi: <strong>LG-YYMMDD-XXX</strong> (contoh: <strong>LG-261114-001</strong>).';
        }
    }

    function validateTrackingSubmit(form) {
        const input = form.querySelector('#nota-input');
        const val = input.value.trim().toUpperCase();
        const hint = document.getElementById('nota-hint');

        if (!val) {
            input.focus();
            if (hint) hint.innerHTML = '<span style="color: #ef4444; font-weight: 600;">⚠️ Masukkan nomor nota terlebih dahulu!</span>';
            input.classList.add('is-invalid');
            return false;
        }

        const notaRegex = /^LG-[0-9]{6,8}-[0-9]{3,4}$/;
        if (!notaRegex.test(val)) {
            if (hint) hint.innerHTML = `<span style="color: #ef4444; font-weight: 600;">⚠️ Format nota "${val}" tidak valid. Contoh yang benar: <strong>LG-261114-001</strong></span>`;
            input.classList.add('is-invalid');
            input.focus();
            return false;
        }

        return true;
    }
    </script>
</body>
</html>
