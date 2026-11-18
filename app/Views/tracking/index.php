<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Nota &amp; Transaksi - LaundryGo Aqua</title>

    <!-- Google Fonts Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- LaundryGo Design System (token & komponen panel Cek Nota) -->
    <link rel="stylesheet" href="<?= \App\Core\Helper::asset('css/laundrygo.css') ?>">
    <link rel="stylesheet" href="<?= \App\Core\Helper::asset('css/tracking.css') ?>">
    <link rel="stylesheet" href="<?= \App\Core\Helper::asset('css/toast.css') ?>">
    <link rel="icon" type="image/png" href="<?= \App\Core\Helper::asset('img/logo_icon_drop.png') ?>">
</head>
<body class="tracking-body">
    <main class="tracking-page">
        <section class="tracking-card" aria-label="Cek Nota dan Transaksi">
            <header class="login-brand-header">
                <img src="<?= \App\Core\Helper::asset('img/logo_horizontal_laundrygo.png') ?>" alt="LaundryGo" class="login-brand-logo-img">
                <div class="login-outlet-pill">
                    <span class="login-outlet-dot"></span>
                    <span>Adi Laundry, Karangploso</span>
                </div>
            </header>

            <h1 class="login-heading">Cek Nota &amp; Transaksi</h1>
            <p class="login-subheading">Masukkan kode nota untuk melihat status dan lokasi rak cucian.</p>

            <form action="index.php" method="GET" class="login-inner-box tracking-form" onsubmit="return validateTrackingSubmit(this);">
                <input type="hidden" name="page" value="tracking">

                <div class="login-field-group">
                    <label for="nota-input" class="login-field-label">Kode Nota</label>
                    <div class="track-search-row">
                        <input type="text" id="nota-input" name="nota" class="form-control num-tabular tracking-input"
                               value="<?= htmlspecialchars($keyword ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               placeholder="Contoh: LG-261114-001"
                               autocomplete="off" spellcheck="false"
                               oninput="handleTrackInput(this)"
                               required autofocus>
                        <button type="submit" class="btn btn-primary tracking-cek-btn">Cek</button>
                    </div>
                </div>

                <div id="nota-hint" class="tracking-hint" aria-live="polite"></div>

                <?php if (!empty($error)): ?>
                    <div class="login-error-banner tracking-error" role="alert">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>
                            <strong><?= ($errorType === 'invalid_format') ? 'Format Nomor Nota Tidak Valid' : 'Nomor Nota Tidak Ditemukan' ?></strong><br>
                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($order)):
                    $stepDefs = [
                        ['label' => 'Diterima', 'statuses' => ['Antrean']],
                        ['label' => 'Dicuci',   'statuses' => ['Dicuci', 'Disetrika']],
                        ['label' => 'Selesai',  'statuses' => ['Siap Diambil']],
                        ['label' => 'Diambil',  'statuses' => ['Selesai']],
                    ];
                    $currentStep = 0;
                    foreach ($stepDefs as $idx => $def) {
                        if (in_array($order['status_cucian'], $def['statuses'], true)) {
                            $currentStep = $idx;
                            break;
                        }
                    }

                    $statusPillClass = [
                        'Antrean'      => 'is-antrean',
                        'Dicuci'       => 'is-dicuci',
                        'Disetrika'    => 'is-disetrika',
                        'Siap Diambil' => 'is-siap',
                        'Selesai'      => 'is-selesai',
                    ][$order['status_cucian']] ?? 'is-disetrika';

                    $items = $order['items'] ?? [];
                    $names = array_values(array_unique(array_column($items, 'nama_layanan')));
                    $layananLabel = $names ? implode(', ', $names) : 'Layanan Laundry';
                    $totalQty = array_sum(array_map(fn($it) => (float)$it['jumlah'], $items));
                    $satuan = $items[0]['satuan'] ?? 'kg';
                    $qtyLabel = rtrim(rtrim(number_format($totalQty, 1, ',', '.'), '0'), ',');
                    $layananBerat = $layananLabel . ' • ' . $qtyLabel . ' ' . $satuan;

                    $pembayaranLabel = htmlspecialchars($order['status_pembayaran'], ENT_QUOTES, 'UTF-8')
                        . ' • ' . \App\Core\Helper::rupiah($order['total_harga']);
                ?>
                    <div class="tracking-result">
                        <div class="tracking-result-head">
                            <h2 class="tracking-result-code num-tabular"><?= htmlspecialchars($order['id_pesanan'], ENT_QUOTES, 'UTF-8') ?></h2>
                            <span class="tracking-status <?= $statusPillClass ?>"><?= htmlspecialchars($order['status_cucian'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>

                        <ol class="tracking-steps" aria-label="Progres pengerjaan cucian">
                            <?php foreach ($stepDefs as $idx => $def):
                                $state = $idx < $currentStep ? 'is-done' : ($idx === $currentStep ? 'is-current' : 'is-pending');
                            ?>
                                <li class="tracking-step <?= $state ?>" <?= $idx === $currentStep ? 'aria-current="step"' : '' ?>>
                                    <span class="tracking-step-dot" aria-hidden="true"></span>
                                    <span class="tracking-step-label"><?= htmlspecialchars($def['label'], ENT_QUOTES, 'UTF-8') ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ol>

                        <dl class="tracking-info-grid">
                            <div>
                                <dt>Pelanggan</dt>
                                <dd><?= htmlspecialchars($order['nama_pelanggan'], ENT_QUOTES, 'UTF-8') ?></dd>
                            </div>
                            <div>
                                <dt>Layanan &amp; Berat</dt>
                                <dd><?= htmlspecialchars($layananBerat, ENT_QUOTES, 'UTF-8') ?></dd>
                            </div>
                            <div>
                                <dt>Lokasi Rak</dt>
                                <dd><?= htmlspecialchars($order['lokasi_rak'] ?: 'Dalam Proses Pengerjaan', ENT_QUOTES, 'UTF-8') ?></dd>
                            </div>
                            <div>
                                <dt>Pembayaran</dt>
                                <dd class="num-tabular"><?= $pembayaranLabel ?></dd>
                            </div>
                            <div>
                                <dt>Waktu Diterima Kasir</dt>
                                <dd class="num-tabular"><?= date('d M Y, H:i', strtotime($order['tanggal_masuk'])) ?> WIB</dd>
                            </div>
                            <div>
                                <dt>Estimasi Waktu Selesai (SLA)</dt>
                                <dd class="num-tabular"><?= date('d M Y, H:i', strtotime($order['tanggal_keluar'])) ?> WIB</dd>
                            </div>
                        </dl>
                    </div>
                <?php endif; ?>
            </form>

            <a class="login-back-btn tracking-back" href="index.php?page=login">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                <span>Kembali ke Masuk</span>
            </a>
        </section>
    </main>

    <script>
    function handleTrackInput(input) {
        // Otomatis huruf besar dan batasi karakter hanya A-Z, 0-9, dan minus (-)
        input.value = input.value.toUpperCase().replace(/[^A-Z0-9\-]/g, '');
        input.classList.remove('is-invalid');

        const hint = document.getElementById('nota-hint');
        if (hint) hint.innerHTML = '';
    }

    function validateTrackingSubmit(form) {
        const input = form.querySelector('#nota-input');
        const val = input.value.trim().toUpperCase();
        const hint = document.getElementById('nota-hint');

        if (!val) {
            input.focus();
            if (hint) hint.innerHTML = '<span style="color:#ef4444;font-weight:600;">⚠️ Masukkan nomor nota terlebih dahulu!</span>';
            input.classList.add('is-invalid');
            return false;
        }

        const notaRegex = /^LG-[0-9]{6,8}-[0-9]{3,4}$/;
        if (!notaRegex.test(val)) {
            if (hint) hint.innerHTML = `<span style="color:#ef4444;font-weight:600;">⚠️ Format nota "${val}" tidak valid. Contoh yang benar: <strong>LG-261114-001</strong></span>`;
            input.classList.add('is-invalid');
            input.focus();
            return false;
        }

        return true;
    }
    </script>
</body>
</html>
