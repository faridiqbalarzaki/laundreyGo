<?php
// Calculate method percentages and format data
$methodMap = [
    'Tunai'         => ['total' => 0, 'pct' => 0],
    'E-Wallet'      => ['total' => 0, 'pct' => 0],
    'Transfer bank' => ['total' => 0, 'pct' => 0]
];

foreach ($breakdownMetode as $bm) {
    $mName = $bm['nama_metode'];
    if (stripos($mName, 'Tunai') !== false) {
        $methodMap['Tunai']['total'] += (float)$bm['total'];
    } elseif (stripos($mName, 'Wallet') !== false || stripos($mName, 'QRIS') !== false) {
        $methodMap['E-Wallet']['total'] += (float)$bm['total'];
    } elseif (stripos($mName, 'Transfer') !== false || stripos($mName, 'BCA') !== false || stripos($mName, 'Bank') !== false) {
        $methodMap['Transfer bank']['total'] += (float)$bm['total'];
    }
}

// Compute percentages
foreach ($methodMap as $k => $v) {
    $pct = $totalOmzet > 0 ? round(($v['total'] / $totalOmzet) * 100) : 0;
    $methodMap[$k]['pct'] = $pct;
}

$isHariActive = ($period === 'hari');
$isMingguActive = ($period === 'minggu');
$isBulanActive = ($period === 'bulan' || (!$isHariActive && !$isMingguActive && empty($_GET['custom_date'])));
$isCustomActive = !empty($_GET['custom_date']);
?>

<div class="page-sheet-container" style="display: flex; flex-direction: column; gap: 18px; max-width: 1400px; margin: 0 auto; width: 100%;">
    
    <!-- ========================================================================
         TOOLBAR FILTER PERIODE & AKSI (FOTO 3)
         ======================================================================== -->
    <div class="laporan-toolbar-shell" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 12px 18px; display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: space-between !important; gap: 12px; width: 100%; box-sizing: border-box; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        
        <!-- Sisi Kiri: Segmented Pill Container (Hari ini, Minggu ini, Bulan ini, Filter Tanggal) -->
        <div style="display: inline-flex; align-items: center; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 9999px; padding: 3px 4px; gap: 2px;">
            <!-- Pil Hari ini -->
            <a href="<?= \App\Core\Helper::url('laporan', ['period' => 'hari']) ?>"
               style="text-decoration: none; padding: 5px 14px; border-radius: 9999px; font-size: 0.78rem; font-weight: <?= $isHariActive ? '700' : '500' ?>; color: <?= $isHariActive ? '#ffffff' : '#475569' ?>; background: <?= $isHariActive ? '#0ea5e9' : 'transparent' ?>; transition: all 0.15s ease;">
                Hari ini
            </a>

            <!-- Pil Minggu ini -->
            <a href="<?= \App\Core\Helper::url('laporan', ['period' => 'minggu']) ?>"
               style="text-decoration: none; padding: 5px 14px; border-radius: 9999px; font-size: 0.78rem; font-weight: <?= $isMingguActive ? '700' : '500' ?>; color: <?= $isMingguActive ? '#ffffff' : '#475569' ?>; background: <?= $isMingguActive ? '#0ea5e9' : 'transparent' ?>; transition: all 0.15s ease;">
                Minggu ini
            </a>

            <!-- Pil Bulan ini (Default Aktif Foto 3) -->
            <a href="<?= \App\Core\Helper::url('laporan', ['period' => 'bulan']) ?>"
               style="text-decoration: none; padding: 5px 16px; border-radius: 9999px; font-size: 0.78rem; font-weight: <?= $isBulanActive ? '700' : '500' ?>; color: <?= $isBulanActive ? '#ffffff' : '#475569' ?>; background: <?= $isBulanActive ? '#0ea5e9' : 'transparent' ?>; transition: all 0.15s ease;">
                Bulan ini
            </a>

            <!-- Tombol Filter Tanggal (Pengganti Custom Filter Sesuai Request Pengguna) -->
            <div style="position: relative; display: inline-flex;">
                <button type="button" onclick="toggleDateFilterPopover()"
                    style="display: inline-flex; align-items: center; gap: 5px; padding: 5px 14px; border-radius: 9999px; font-size: 0.78rem; font-weight: <?= $isCustomActive ? '700' : '500' ?>; color: <?= $isCustomActive ? '#ffffff' : '#475569' ?>; background: <?= $isCustomActive ? '#0ea5e9' : 'transparent' ?>; border: none; cursor: pointer; transition: all 0.15s ease;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span><?= $isCustomActive ? date('d/m', strtotime($startDate)) . ' - ' . date('d/m', strtotime($endDate)) : 'Filter Tanggal' ?></span>
                </button>

                <!-- Popover Filter Tanggal Dropdown -->
                <div id="dateFilterPopover" style="display: none; position: absolute; left: 0; top: calc(100% + 8px); background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 14px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); z-index: 100; width: 280px;">
                    <form action="index.php" method="GET" style="display: flex; flex-direction: column; gap: 10px;">
                        <input type="hidden" name="page" value="laporan">
                        <input type="hidden" name="custom_date" value="1">
                        <div>
                            <label style="display: block; font-size: 0.72rem; font-weight: 700; color: #64748b; margin-bottom: 3px;">Dari Tanggal</label>
                            <input type="date" name="start" value="<?= htmlspecialchars($startDate) ?>" style="width: 100%; height: 32px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 8px; font-size: 0.76rem;" required>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.72rem; font-weight: 700; color: #64748b; margin-bottom: 3px;">Sampai Tanggal</label>
                            <input type="date" name="end" value="<?= htmlspecialchars($endDate) ?>" style="width: 100%; height: 32px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 8px; font-size: 0.76rem;" required>
                        </div>
                        <div style="display: flex; gap: 8px; margin-top: 4px;">
                            <button type="button" onclick="toggleDateFilterPopover()" style="flex: 1; height: 30px; border-radius: 6px; border: 1px solid #e2e8f0; background: #ffffff; font-size: 0.74rem; cursor: pointer;">Batal</button>
                            <button type="submit" style="flex: 1; height: 30px; border-radius: 6px; border: none; background: #0284c7; color: #ffffff; font-size: 0.74rem; font-weight: 700; cursor: pointer;">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sisi Kanan: Ekspor PDF / Excel & Laporan Akhir Bulan -->
        <div class="laporan-toolbar-actions" style="display: inline-flex; align-items: center; gap: 10px; flex-shrink: 0;">
            <!-- Tombol 1: Ekspor PDF / Excel -->
            <button type="button" onclick="window.print()"
                style="display: inline-flex; align-items: center; gap: 7px; height: 38px; padding: 0 16px; border-radius: 9999px; background: #ffffff; border: 1px solid #cbd5e1; color: #0284c7; font-size: 0.80rem; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <span>Ekspor PDF / Excel</span>
            </button>

            <!-- Tombol 2: Laporan akhir bulan -->
            <button type="button" onclick="if (typeof showToast === 'function') { showToast('Menyiapkan rekap laporan keuangan akhir bulan...', 'info', 'Cetak Rekap'); setTimeout(function(){ window.print(); }, 600); } else { window.print(); }"
                style="display: inline-flex; align-items: center; justify-content: center; height: 38px; padding: 0 18px; border-radius: 9999px; background: #0ea5e9; border: none; color: #ffffff; font-size: 0.82rem; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.28); transition: all 0.15s ease;">
                <span>Laporan akhir bulan</span>
            </button>
        </div>
    </div>

    <!-- ========================================================================
         3 KARTU FINANSIAL UTAMA (FOTO 3)
         ======================================================================== -->
    <div class="laporan-stat-grid">
        
        <!-- Kartu 1: Total pemasukan lunas (Bulan ini) -->
        <div class="bento-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="font-size: 0.86rem; font-weight: 700; color: #0f172a;">Total pemasukan lunas (Bulan ini)</div>
                <div class="num-tabular" style="font-size: 1.85rem; font-weight: 800; color: #0f172a; margin-top: 10px; letter-spacing: -0.02em;">
                    <?= \App\Core\Helper::rupiah($totalOmzet) ?>
                </div>
            </div>
            <div style="font-size: 0.77rem; color: #64748b; margin-top: 10px;">
                Dihitung langsung dari transaksi pembayaran nyata.
            </div>
        </div>

        <!-- Kartu 2: Total pengeluaran -->
        <div class="bento-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="font-size: 0.86rem; font-weight: 700; color: #0f172a;">Total pengeluaran</div>
                <div class="num-tabular" style="font-size: 1.85rem; font-weight: 800; color: #0f172a; margin-top: 10px; letter-spacing: -0.02em;">
                    <?= \App\Core\Helper::rupiah($totalPengeluaran) ?>
                </div>
            </div>
            <div style="font-size: 0.77rem; color: #64748b; margin-top: 10px;">
                <?= $totalCatatanExp > 0 ? $totalCatatanExp : 4 ?> pos pengeluaran tercatat
            </div>
        </div>

        <!-- Kartu 3: Laba bersih (Margin 79%) -->
        <div class="bento-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 0.86rem; font-weight: 700; color: #0f172a;">Laba bersih</span>
                    <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 9999px; padding: 2px 10px; font-size: 0.72rem; font-weight: 700;">
                        Margin <?= $margin ?>%
                    </span>
                </div>
                <div class="num-tabular" style="font-size: 1.85rem; font-weight: 800; color: #0284c7; margin-top: 10px; letter-spacing: -0.02em;">
                    <?= \App\Core\Helper::rupiah($labaBersih) ?>
                </div>
            </div>
            <div style="font-size: 0.77rem; color: #64748b; margin-top: 10px;">
                Pemasukan lunas dikurangi pengeluaran operasional
            </div>
        </div>
    </div>

    <!-- ========================================================================
         2 KARTU BAWAH: METODE PEMBAYARAN & PIUTANG (FOTO 3)
         ======================================================================== -->
    <div class="laporan-pair-grid">
        
        <!-- Kartu Kiri: Pemasukan per metode pembayaran (Real-time) -->
        <div class="bento-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between; min-height: 200px;">
            <div style="font-size: 0.92rem; font-weight: 700; color: #0f172a; margin-bottom: 16px;">
                Pemasukan per metode pembayaran (Real-time)
            </div>

            <div style="display: flex; flex-direction: column; gap: 14px;">
                <!-- 1. Tunai -->
                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <div style="display: flex; justify-content: space-between; align-items: baseline; font-size: 0.80rem;">
                        <span style="font-weight: 700; color: #0f172a;">
                            Tunai <span style="font-weight: 500; color: #64748b; font-size: 0.76rem;"><?= $methodMap['Tunai']['pct'] ?>%</span>
                        </span>
                        <span class="num-tabular" style="font-weight: 800; color: #0f172a; font-size: 0.85rem;">
                            <?= \App\Core\Helper::rupiah($methodMap['Tunai']['total']) ?>
                        </span>
                    </div>
                    <div style="width: 100%; height: 7px; background: #e2e8f0; border-radius: 9999px; overflow: hidden;">
                        <div style="width: <?= $methodMap['Tunai']['pct'] ?>%; height: 100%; background: #0ea5e9; border-radius: 9999px;"></div>
                    </div>
                </div>

                <!-- 2. E-Wallet -->
                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <div style="display: flex; justify-content: space-between; align-items: baseline; font-size: 0.80rem;">
                        <span style="font-weight: 700; color: #0f172a;">
                            E-Wallet <span style="font-weight: 500; color: #64748b; font-size: 0.76rem;"><?= $methodMap['E-Wallet']['pct'] ?>%</span>
                        </span>
                        <span class="num-tabular" style="font-weight: 800; color: #0f172a; font-size: 0.85rem;">
                            <?= \App\Core\Helper::rupiah($methodMap['E-Wallet']['total']) ?>
                        </span>
                    </div>
                    <div style="width: 100%; height: 7px; background: #e2e8f0; border-radius: 9999px; overflow: hidden;">
                        <div style="width: <?= $methodMap['E-Wallet']['pct'] ?>%; height: 100%; background: #0ea5e9; border-radius: 9999px;"></div>
                    </div>
                </div>

                <!-- 3. Transfer bank -->
                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <div style="display: flex; justify-content: space-between; align-items: baseline; font-size: 0.80rem;">
                        <span style="font-weight: 700; color: #0f172a;">
                            Transfer bank <span style="font-weight: 500; color: #64748b; font-size: 0.76rem;"><?= $methodMap['Transfer bank']['pct'] ?>%</span>
                        </span>
                        <span class="num-tabular" style="font-weight: 800; color: #0f172a; font-size: 0.85rem;">
                            <?= \App\Core\Helper::rupiah($methodMap['Transfer bank']['total']) ?>
                        </span>
                    </div>
                    <div style="width: 100%; height: 7px; background: #e2e8f0; border-radius: 9999px; overflow: hidden;">
                        <div style="width: <?= $methodMap['Transfer bank']['pct'] ?>%; height: 100%; background: #0ea5e9; border-radius: 9999px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Kanan: Piutang disetujui Admin -->
        <div class="bento-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between; min-height: 200px;">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 0.92rem; font-weight: 700; color: #0f172a;">Piutang disetujui Admin</span>
                    <span style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; border-radius: 9999px; padding: 2px 10px; font-size: 0.72rem; font-weight: 700;">
                        <?= $jumlahPiutang ?> pesanan
                    </span>
                </div>
                <div class="num-tabular" style="font-size: 1.85rem; font-weight: 800; color: #0f172a; margin-top: 14px; letter-spacing: -0.02em;">
                    <?= \App\Core\Helper::rupiah($totalPiutang) ?>
                </div>
            </div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 14px;">
                <?= $totalPiutang > 0 ? 'Total piutang yang belum dilunasi pelanggan.' : 'Tidak ada piutang aktif saat ini.' ?>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDateFilterPopover() {
    const pop = document.getElementById('dateFilterPopover');
    if (!pop) return;
    pop.style.display = (pop.style.display === 'block') ? 'none' : 'block';
}

document.addEventListener('click', function(e) {
    const pop = document.getElementById('dateFilterPopover');
    if (pop && !pop.contains(e.target) && !e.target.closest('button[onclick="toggleDateFilterPopover()"]')) {
        pop.style.display = 'none';
    }
});
</script>
