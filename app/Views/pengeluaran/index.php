<?php
$catIcon = function (int $size, ?string $name): string {
    $name = $name ?? '';
    if (stripos($name, 'listrik') !== false) {
        $paths = '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>';
    } elseif (stripos($name, 'bahan') !== false || stripos($name, 'sabun') !== false || stripos($name, 'deterjen') !== false) {
        $paths = '<path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>';
    } elseif (stripos($name, 'gaji') !== false || stripos($name, 'upah') !== false) {
        $paths = '<path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/>';
    } elseif (stripos($name, 'pelihara') !== false || stripos($name, 'pemelihara') !== false || stripos($name, 'perawatan') !== false || stripos($name, 'servis') !== false || stripos($name, 'service') !== false || stripos($name, 'perbaikan') !== false) {
        $paths = '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>';
    } else {
        $paths = '<path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r=".5" fill="currentColor"/>';
    }
    return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $paths . '</svg>';
};

$catTotals = [];
foreach ($kategoriList as $k) {
    $catTotals[$k['id_kategori_pengeluaran']] = ['total' => 0.0, 'count' => 0];
}

$overallTotal = 0;
$overallCount = 0;

foreach ($pengeluaran as $exp) {
    $cid = $exp['id_kategori_pengeluaran'];
    if (!isset($catTotals[$cid])) {
        $catTotals[$cid] = ['total' => 0.0, 'count' => 0];
    }
    $catTotals[$cid]['total'] += (float)$exp['jumlah'];
    $catTotals[$cid]['count']++;
    $overallTotal += (float)$exp['jumlah'];
    $overallCount++;
}

// Active category name for label
$activeKatName = 'Semua';
if ($activeKategori !== 'Semua') {
    foreach ($kategoriList as $k) {
        if ($k['id_kategori_pengeluaran'] === $activeKategori) {
            $activeKatName = $k['nama_kategori'];
            break;
        }
    }
}
?>

<div class="page-sheet-container" style="display: flex; flex-direction: column; gap: 18px; max-width: 1400px; margin: 0 auto; width: 100%;">
    <!-- 2-Column Split: Kiri (Kategori Filter & Riwayat) & Kanan (Ringkasan Pengeluaran) -->
    <div class="expense-main-split">
        
        <!-- ========================================================================
             KOLOM KIRI: FILTER KATEGORI & RIWAYAT PENGELUARAN
             ======================================================================== -->
        <div style="display: flex; flex-direction: column; gap: 18px;">
            
            <!-- 1. BENTO FILTER KATEGORI -->
            <div class="bento-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 20px 22px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <div style="margin-bottom: 14px;">
                    <div style="font-size: 0.96rem; font-weight: 700; color: #0f172a; letter-spacing: -0.01em;">Filter Berdasarkan Kategori</div>
                    <div style="font-size: 0.77rem; color: #64748b; margin-top: 3px;">Pilih kategori untuk memfokuskan riwayat pengeluaran operasional</div>
                </div>

                <div class="expense-bento-grid">
                    <!-- Kartu: Semua Kategori -->
                    <?php $isAllActive = ($activeKategori === 'Semua'); ?>
                    <a href="<?= \App\Core\Helper::url('pengeluaran', ['kategori' => 'Semua']) ?>"
                       class="expense-cat-card <?= $isAllActive ? 'is-active' : '' ?>"
                       style="text-decoration: none; padding: 13px 15px; border-radius: 14px; border: <?= $isAllActive ? '1.5px solid #0284c7' : '1px solid #e2e8f0' ?>; background: <?= $isAllActive ? '#f0f9ff' : '#ffffff' ?>; display: flex; flex-direction: column; gap: 6px; transition: all 0.15s ease;">
                        <div style="color: #0284c7; display: flex; align-items: center;">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/></svg>
                        </div>
                        <span style="font-size: 0.85rem; font-weight: 700; color: <?= $isAllActive ? '#0284c7' : '#0f172a' ?>;">Semua Kategori</span>
                        <span style="font-size: 0.73rem; color: #64748b;" class="num-tabular"><?= $overallCount ?> item &bull; <?= \App\Core\Helper::rupiah($overallTotal) ?></span>
                    </a>

                    <!-- Kartu: Kategori dari database -->
                    <?php foreach ($kategoriList as $k):
                        $kid = $k['id_kategori_pengeluaran'];
                        $isKatActive = ($activeKategori === $kid);
                        $katTotal = $catTotals[$kid]['total'] ?? 0;
                        $katCount = $catTotals[$kid]['count'] ?? 0;
                    ?>
                    <a href="<?= \App\Core\Helper::url('pengeluaran', ['kategori' => $kid]) ?>"
                       class="expense-cat-card <?= $isKatActive ? 'is-active' : '' ?>"
                       style="text-decoration: none; padding: 13px 15px; border-radius: 14px; border: <?= $isKatActive ? '1.5px solid #0284c7' : '1px solid #e2e8f0' ?>; background: <?= $isKatActive ? '#f0f9ff' : '#ffffff' ?>; display: flex; flex-direction: column; gap: 6px; transition: all 0.15s ease;">
                        <div style="color: <?= $isKatActive ? '#0284c7' : '#475569' ?>; display: flex; align-items: center;">
                            <?= $catIcon(17, $k['nama_kategori']) ?>
                        </div>
                        <span style="font-size: 0.85rem; font-weight: 700; color: <?= $isKatActive ? '#0284c7' : '#0f172a' ?>;"><?= htmlspecialchars($k['nama_kategori']) ?></span>
                        <span style="font-size: 0.73rem; color: #64748b;" class="num-tabular"><?= $katCount ?> item &bull; <?= \App\Core\Helper::rupiah($katTotal) ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 2. RIWAYAT PENGELUARAN -->
            <div class="bento-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 20px 22px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <div style="margin-bottom: 14px;">
                    <div style="font-size: 0.96rem; font-weight: 700; color: #0f172a; letter-spacing: -0.01em;">Riwayat Pengeluaran</div>
                    <div style="font-size: 0.77rem; color: #64748b; margin-top: 3px;">Catatan lengkap nominal, waktu transaksi, dan keterangan belanja outlet</div>
                </div>

                <!-- Input Pencarian -->
                <div style="position: relative; width: 100%; max-width: 420px; margin-bottom: 12px;">
                    <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; display: flex; align-items: center; pointer-events: none;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input type="text" id="expenseSearchInput" class="form-control"
                        placeholder="Cari pengeluaran..."
                        style="width: 100%; height: 38px; padding-left: 38px; border-radius: 12px; border: 1px solid #e2e8f0; font-size: 0.82rem; background: #ffffff;"
                        oninput="handleExpenseFilter(this.value)">
                </div>

                <!-- Item Count Badge -->
                <div style="margin-bottom: 12px;">
                    <span id="expenseBadgeCount" style="display: inline-flex; align-items: center; background: #e0f2fe; color: #0284c7; padding: 4px 12px; border-radius: 9999px; font-size: 0.74rem; font-weight: 700;">
                        <?= count($pengeluaran) ?> item
                    </span>
                </div>

                <!-- Daftar Baris Pengeluaran -->
                <div id="expenseListWrap" style="display: flex; flex-direction: column; gap: 10px;">
                    <?php if (empty($pengeluaran)): ?>
                        <div style="text-align: center; padding: 36px; color: #64748b; font-size: 0.82rem; border: 1px dashed #e2e8f0; border-radius: 14px;">
                            Belum ada riwayat pengeluaran yang tercatat pada kriteria ini.
                        </div>
                    <?php else: ?>
                        <?php foreach ($pengeluaran as $item): 
                            $kName = $item['nama_kategori'] ?? 'Lain-lain';
                            $ts = strtotime($item['tanggal_pengeluaran']);
                            $dateFmt = date('Y-m-d • H:i', $ts);
                            $rawDate = date('Y-m-d\TH:i', $ts);
                        ?>
                            <div class="expense-row-card" data-cat="<?= htmlspecialchars($kName) ?>" style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 14px; padding: 12px 18px; display: flex; align-items: center; justify-content: space-between; gap: 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.02); transition: all 0.15s ease;">
                                <!-- Left: Squircle Icon + Title/Category + Date -->
                                <div style="display: flex; align-items: center; gap: 14px; min-width: 0;">
                                    <div style="width: 42px; height: 42px; border-radius: 12px; border: 1px solid #bae6fd; background: #f0f9ff; color: #0284c7; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <?= $catIcon(18, $kName) ?>
                                    </div>
                                    <div style="min-width: 0;">
                                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                            <span class="exp-title-text" style="font-weight: 700; font-size: 0.88rem; color: #0f172a;">
                                                <?= htmlspecialchars($item['keterangan']) ?>
                                            </span>
                                            <span style="font-size: 0.72rem; font-weight: 600; padding: 2px 8px; border-radius: 6px; background: #f1f5f9; color: #334155;">
                                                <?= htmlspecialchars($kName) ?>
                                            </span>
                                        </div>
                                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 3px;">
                                            <?= $dateFmt ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right: Nominal + Action Buttons (Edit & Hapus) -->
                                <div style="display: flex; align-items: center; gap: 14px; flex-shrink: 0;">
                                    <span class="num-tabular" style="font-size: 0.95rem; font-weight: 800; color: #0f172a;">
                                        <?= \App\Core\Helper::rupiah($item['jumlah']) ?>
                                    </span>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <button type="button" class="btn-expense-edit"
                                            onclick="openEditExpenseModal(<?= htmlspecialchars(json_encode([
                                                'id' => $item['id_pengeluaran'],
                                                'keterangan' => $item['keterangan'],
                                                'jumlah' => (float)$item['jumlah'],
                                                'kategori' => $item['id_kategori_pengeluaran'],
                                                'tanggal' => $rawDate
                                            ]), ENT_QUOTES, 'UTF-8') ?>)"
                                            style="border: 1px solid #e2e8f0; background: #ffffff; color: #334155; font-size: 0.75rem; font-weight: 600; padding: 5px 14px; border-radius: 9999px; cursor: pointer; transition: all 0.15s ease;">
                                            Edit
                                        </button>
                                        <form action="<?= \App\Core\Helper::url('pengeluaran', ['action' => 'delete']) ?>" method="POST" style="margin: 0; display: inline;"
                                            data-confirm="true"
                                            data-confirm-title="Hapus Catatan Pengeluaran?"
                                            data-confirm-message="Catatan belanja pengeluaran ini akan dihapus dari buku operasional gerai."
                                            data-confirm-badge="<?= htmlspecialchars($item['keterangan']) ?> &bull; <?= \App\Core\Helper::rupiah($item['jumlah']) ?>"
                                            data-confirm-btn="Ya, Hapus Catatan"
                                            data-confirm-type="danger">
                                            <input type="hidden" name="id_pengeluaran" value="<?= $item['id_pengeluaran'] ?>">
                                            <button type="submit" class="btn-expense-del"
                                                style="border: 1px solid #fecaca; background: #ffffff; color: #ef4444; font-size: 0.75rem; font-weight: 600; padding: 5px 14px; border-radius: 9999px; cursor: pointer; transition: all 0.15s ease;">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ========================================================================
             KOLOM KANAN: RINGKASAN PENGELUARAN (FOTO 3)
             ======================================================================== -->
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <div class="bento-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 22px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); display: flex; flex-direction: column; gap: 14px;">
                <div style="font-size: 0.98rem; font-weight: 700; color: #0f172a; letter-spacing: -0.01em;">
                    Ringkasan Pengeluaran
                </div>

                <!-- Rincian Tiap Kategori dari database -->
                <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 2px;">
                    <?php foreach ($kategoriList as $k):
                        $kid = $k['id_kategori_pengeluaran'];
                    ?>
                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.82rem;">
                        <span style="color: #334155; display: inline-flex; align-items: center; gap: 6px;">
                            <span style="color: #0284c7; display: inline-flex;"><?= $catIcon(14, $k['nama_kategori']) ?></span>
                            <?= htmlspecialchars($k['nama_kategori']) ?>
                        </span>
                        <span class="num-tabular" style="font-weight: 700; color: #0f172a; font-size: 0.85rem;">
                            <?= \App\Core\Helper::rupiah($catTotals[$kid]['total'] ?? 0) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div style="border-top: 1px solid #f1f5f9; margin: 4px 0;"></div>

                <!-- Total Pengeluaran -->
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="color: #334155; font-size: 0.84rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7V4a1 1 0 0 0-1-1H6a1 1 0 0 0-1 1v3"/><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M12 12v4"/><path d="M10 14h4"/></svg>
                        Total Pengeluaran
                    </span>
                    <span class="num-tabular" style="font-size: 1.15rem; font-weight: 800; color: #0284c7;">
                        <?= \App\Core\Helper::rupiah($overallTotal) ?>
                    </span>
                </div>

                <!-- Filter Aktif -->
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="color: #64748b; font-size: 0.80rem; display: inline-flex; align-items: center; gap: 6px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Filter Aktif
                    </span>
                    <span style="background: #e0f2fe; color: #0284c7; padding: 3px 10px; border-radius: 9999px; font-size: 0.74rem; font-weight: 700;">
                        &bull; <?= htmlspecialchars($activeKatName) ?>
                    </span>
                </div>

                <!-- Tips Callout: Sinkronisasi Laba -->
                <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 14px; padding: 14px; margin-top: 4px;">
                    <div style="display: flex; align-items: center; gap: 7px; color: #0284c7;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        <strong style="font-size: 0.82rem; color: #0f172a;">Tips: Sinkronisasi Laba</strong>
                    </div>
                    <p style="font-size: 0.76rem; color: #475569; margin: 6px 0 0 0; line-height: 1.45;">
                        Setiap pengeluaran yang dicatat otomatis memotong hitungan Laba Bersih pada Laporan Keuangan.
                    </p>
                </div>

                <!-- Tombol Primary: + Tambah Pengeluaran Baru -->
                <button type="button" onclick="openAddExpenseModal()"
                    style="margin-top: 4px; width: 100%; height: 42px; background: #0ea5e9; color: #ffffff; font-weight: 700; font-size: 0.84rem; border: none; border-radius: 9999px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; box-shadow: 0 4px 14px rgba(14, 165, 233, 0.3); transition: all 0.15s ease;">
                    <span style="font-size: 1.1rem; line-height: 1;">+</span> Tambah pengeluaran baru
                </button>

                <!-- Tombol Secondary: Lihat Laporan Keuangan -->
                <a href="<?= \App\Core\Helper::url('laporan') ?>"
                    style="width: 100%; height: 40px; background: #ffffff; color: #0f172a; font-weight: 600; font-size: 0.84rem; border: 1px solid #e2e8f0; border-radius: 9999px; text-decoration: none; display: flex; align-items: center; justify-content: center; transition: all 0.15s ease;">
                    Lihat Laporan Keuangan
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================
     MODAL TAMBAH & EDIT PENGELUARAN
     ======================================================================== -->
<div id="expenseModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px); z-index: 999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #ffffff; border-radius: 20px; width: 100%; max-width: 480px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); overflow: hidden; animation: popIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);">
        <div style="padding: 18px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;">
            <strong id="modalExpenseTitle" style="font-size: 0.98rem; font-weight: 700; color: #0f172a;">Catat Pengeluaran Baru</strong>
            <button type="button" onclick="closeExpenseModal()" style="border: none; background: #f1f5f9; color: #64748b; width: 28px; height: 28px; border-radius: 8px; font-size: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">&times;</button>
        </div>
        <form id="expenseModalForm" action="<?= \App\Core\Helper::url('pengeluaran', ['action' => 'store']) ?>" method="POST" style="padding: 20px 24px; display: flex; flex-direction: column; gap: 14px;">
            <input type="hidden" name="id_pengeluaran" id="m_id_pengeluaran" value="">

            <div>
                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Tanggal & Waktu</label>
                <input type="datetime-local" name="tanggal_pengeluaran" id="m_tanggal" class="form-control" style="width: 100%; height: 38px; border-radius: 10px; border: 1px solid #cbd5e1; padding: 0 12px; font-size: 0.82rem;" required>
            </div>

            <div>
                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Kategori Pengeluaran</label>
                <select name="id_kategori_pengeluaran" id="m_kategori" class="form-control" style="width: 100%; height: 38px; border-radius: 10px; border: 1px solid #cbd5e1; padding: 0 12px; font-size: 0.82rem;" required>
                    <?php foreach ($kategoriList as $k): ?>
                        <option value="<?= $k['id_kategori_pengeluaran'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Keterangan Belanja</label>
                <input type="text" name="keterangan" id="m_keterangan" placeholder="Contoh: Deterjen cair 5L, Token listrik" class="form-control" style="width: 100%; height: 38px; border-radius: 10px; border: 1px solid #cbd5e1; padding: 0 12px; font-size: 0.82rem;" required>
            </div>

            <div>
                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Nominal (Rp)</label>
                <input type="number" name="jumlah" id="m_jumlah" placeholder="0" min="1000" step="500" class="form-control" style="width: 100%; height: 38px; border-radius: 10px; border: 1px solid #cbd5e1; padding: 0 12px; font-size: 0.88rem; font-weight: 700;" required>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <button type="button" onclick="closeExpenseModal()" style="flex: 1; height: 38px; border: 1px solid #e2e8f0; background: #ffffff; color: #475569; font-size: 0.82rem; font-weight: 600; border-radius: 10px; cursor: pointer;">Batal</button>
                <button type="submit" id="btnSubmitExpense" style="flex: 1; height: 38px; border: none; background: #0284c7; color: #ffffff; font-size: 0.82rem; font-weight: 700; border-radius: 10px; cursor: pointer;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function handleExpenseFilter(q) {
    q = (q || '').toLowerCase();
    const rows = document.querySelectorAll('.expense-row-card');
    let visibleCount = 0;
    rows.forEach(row => {
        const text = (row.querySelector('.exp-title-text')?.textContent || '').toLowerCase();
        const cat = (row.getAttribute('data-cat') || '').toLowerCase();
        if (text.includes(q) || cat.includes(q)) {
            row.style.display = 'flex';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    const badge = document.getElementById('expenseBadgeCount');
    if (badge) badge.textContent = visibleCount + ' item';
}

function openAddExpenseModal() {
    const modal = document.getElementById('expenseModal');
    const form = document.getElementById('expenseModalForm');
    const title = document.getElementById('modalExpenseTitle');
    if (!modal || !form) return;
    
    title.textContent = 'Tambah Pengeluaran Baru';
    form.action = '<?= \App\Core\Helper::url('pengeluaran', ['action' => 'store']) ?>';
    document.getElementById('m_id_pengeluaran').value = '';
    document.getElementById('m_keterangan').value = '';
    document.getElementById('m_jumlah').value = '';
    
    // Set current date-time in local ISO format
    const now = new Date();
    const offset = now.getTimezoneOffset() * 60000;
    const localISOTime = (new Date(now - offset)).toISOString().slice(0, 16);
    document.getElementById('m_tanggal').value = localISOTime;

    modal.style.display = 'flex';
}

function openEditExpenseModal(data) {
    const modal = document.getElementById('expenseModal');
    const form = document.getElementById('expenseModalForm');
    const title = document.getElementById('modalExpenseTitle');
    if (!modal || !form || !data) return;

    title.textContent = 'Edit Pengeluaran';
    form.action = '<?= \App\Core\Helper::url('pengeluaran', ['action' => 'update']) ?>';
    document.getElementById('m_id_pengeluaran').value = data.id || '';
    document.getElementById('m_keterangan').value = data.keterangan || '';
    document.getElementById('m_jumlah').value = data.jumlah || '';
    document.getElementById('m_kategori').value = data.kategori || '';
    document.getElementById('m_tanggal').value = data.tanggal || '';

    modal.style.display = 'flex';
}

function closeExpenseModal() {
    const modal = document.getElementById('expenseModal');
    if (modal) modal.style.display = 'none';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeExpenseModal();
});
</script>
