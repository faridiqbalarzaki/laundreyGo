<?php 
if (!function_exists('getLayananLucideSvg')) {
    function getLayananLucideSvg($name, $size = 16) {
        $n = strtolower($name);
        if (str_contains($n, 'satuan')) {
            return '<svg width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>';
        }
        if (str_contains($n, 'sepatu')) {
            return '<svg width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 16v-2.38C4 11.5 2.97 10.5 3 8c.03-2.72 1.49-6 4.5-6C9.37 2 10 3.8 10 5.5c0 3.11-2 5.66-2 8.68V16a2 2 0 1 1-4 0Z"/><path d="M20 20v-2.38c0-2.12 1.03-3.12 1-5.62-.03-2.72-1.49-6-4.5-6C14.63 6 14 7.8 14 9.5c0 3.11 2 5.66 2 8.68V20a2 2 0 1 0 4 0Z"/><path d="M16 17h4"/><path d="M4 13h4"/></svg>';
        }
        if (str_contains($n, 'bed cover') || str_contains($n, 'selimut') || str_contains($n, 'sprei')) {
            return '<svg width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 20v-8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8"/><path d="M4 10V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4"/><path d="M12 4v6"/><path d="M2 18h20"/></svg>';
        }
        if (str_contains($n, 'kilat') || str_contains($n, 'express')) {
            return '<svg width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>';
        }
        if (str_contains($n, 'setrika') && !str_contains($n, 'cuci')) {
            return '<svg width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>';
        }
        if (str_contains($n, 'kering saja') || (str_contains($n, 'cuci') && !str_contains($n, 'setrika'))) {
            return '<svg width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h3"/><path d="M17 6h.01"/><rect width="18" height="20" x="3" y="2" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M12 18a2.5 2.5 0 0 0 0-5 2.5 2.5 0 0 1 0-5"/></svg>';
        }
        return '<svg width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/></svg>';
    }
}
?>
<?php if (\App\Core\Auth::isKasir()): ?>
    <!-- TAMPILAN KATALOG RESMI TARIF LAYANAN KHUSUS KASIR (Identik dengan index.html) -->
    <div class="page-sheet-container is-orders-page" style="display: flex; flex-direction: column; gap: 14px;">
        <div class="selesai-header-bar">
            <div class="selesai-title-group">
                <h2 class="selesai-page-title">Tarif Layanan</h2>
                <span class="selesai-sub-pill">Katalog Resmi</span>
            </div>
            <?php include __DIR__ . '/../layouts/kasir_header_actions.php'; ?>
        </div>
        <div class="modern-table-card">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Layanan</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Estimasi</th>
                        <th>Rentang Tarif</th>
                        <th>Alur Pengerjaan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($layanan as $srv): 
                        $kat = $srv['nama_kategori'] ?? 'Kiloan';
                        $catBadgeClass = 'is-info';
                        $iconBg = 'var(--primary-subtle)';
                        $iconColor = 'var(--primary)';

                        if (stripos($kat, 'Kilat') !== false || stripos($srv['nama_layanan'], 'Kilat') !== false) {
                            $catBadgeClass = 'is-warn';
                            $iconBg = 'rgba(245, 158, 11, 0.12)';
                            $iconColor = '#D97706';
                        } elseif (stripos($kat, 'Satuan') !== false) {
                            $catBadgeClass = 'is-success';
                            $iconBg = 'rgba(16, 185, 129, 0.12)';
                            $iconColor = '#059669';
                        }

                        $satuan = $srv['satuan'] ?? 'kg';
                        $estHours = (int)($srv['estimasi_waktu'] ?? 24);
                        $estText = $estHours <= 6 ? "{$estHours} jam" : (ceil($estHours / 24) . " hari");
                        $workflowText = (stripos($srv['nama_layanan'], 'Setrika') !== false && stripos($srv['nama_layanan'], 'Cuci') === false)
                            ? 'Setrika Saja'
                            : ((stripos($srv['nama_layanan'], 'Cuci Kering Saja') !== false) ? 'Cuci Saja' : 'Cuci &rarr; Setrika');
                    ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="width: 32px; height: 32px; border-radius: 9px; background: <?= $iconBg ?>; color: <?= $iconColor ?>; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
                                        <?= getLayananLucideSvg($srv['nama_layanan'], 18) ?>
                                    </span>
                                    <strong style="color: var(--ink); font-size: 0.86rem;"><?= htmlspecialchars($srv['nama_layanan']) ?></strong>
                                </div>
                            </td>
                            <td>
                                <span class="semantic-pill <?= $catBadgeClass ?>" style="font-weight: 700; font-size: 0.72rem;">
                                    <?= htmlspecialchars($kat) ?>
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 0.77rem; font-weight: 600; color: var(--ink-secondary); background: var(--surface-subtle); padding: 2px 7px; border-radius: var(--radius-pill);">
                                    per <?= htmlspecialchars($satuan) ?>
                                </span>
                            </td>
                            <td>
                                <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.76rem; font-weight: 600; color: #0369A1;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <span><?= htmlspecialchars($estText) ?></span>
                                </span>
                            </td>
                            <td>
                                <span class="num-tabular" style="font-weight: 800; font-size: 0.84rem; color: var(--ink);">
                                    <?= \App\Core\Helper::rupiah($srv['harga']) ?>
                                </span>
                            </td>
                            <td>
                                <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.73rem; font-weight: 600; color: var(--ink-muted); background: var(--surface-subtle); padding: 3px 8px; border-radius: 6px;">
                                    <?= $workflowText ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <!-- TAMPILAN ADMIN/OWNER (Grid Kartu + Edit Harga + Toggle) -->
    <div class="page-sheet-container" style="display: flex; flex-direction: column; gap: 14px;">
        <!-- Toolbar Header Admin -->
        <div class="orders-inline-toolbar" style="display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <span class="dash-card-title" style="font-size: 1rem; font-weight: 800; color: var(--ink);">Katalog Layanan &amp; Tarif Gerai</span>
                <span class="semantic-pill is-info num-tabular" style="font-size: 0.72rem; padding: 2px 8px; border-radius: 9999px;">
                    <?= count($layanan) ?> layanan terdaftar
                </span>
            </div>
            <div class="toolbar-end">
                <button type="button" class="btn btn-primary" onclick="openAddLayananModal()" style="height: 36px; font-size: 0.8rem; padding: 0 16px; border-radius: 10px; font-weight: 700; background: #0284c7; color: #fff; border: none; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; box-shadow: 0 2px 6px -1px rgba(2, 132, 199, 0.35);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span>Tambah Layanan Baru</span>
                </button>
            </div>
        </div>

        <div class="service-admin-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px;">
            <?php foreach ($layanan as $srv): 
                $isActive = ($srv['status_aktif'] == 1);
                $satuan = $srv['satuan'] ?? 'kg';
            ?>
                <div class="service-admin-card" style="background: var(--surface); border: 1px solid var(--line); border-radius: 16px; padding: 18px 20px; box-shadow: var(--shadow-border); display: flex; flex-direction: column; gap: 8px; <?= $isActive ? '' : 'opacity: 0.65;' ?>">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 0.74rem; color: var(--ink-muted); font-weight: 600;">Per <?= htmlspecialchars($satuan) ?></span>
                        <span class="semantic-pill <?= $isActive ? 'is-success' : 'is-danger' ?>" style="font-size: 0.7rem; padding: 2px 8px; border-radius: 9999px;">
                            &bull; <?= $isActive ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 10px; margin: 4px 0 2px;">
                        <span style="width: 32px; height: 32px; border-radius: 10px; background: rgba(14, 165, 233, 0.12); color: #0284c7; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <?= getLayananLucideSvg($srv['nama_layanan'], 18) ?>
                        </span>
                        <div style="font-size: 1rem; font-weight: 800; color: var(--ink);">
                            <?= htmlspecialchars($srv['nama_layanan']) ?>
                        </div>
                    </div>

                    <div class="service-admin-price num-tabular" style="font-size: 1.55rem; font-weight: 800; color: var(--ink); letter-spacing: -0.025em;">
                        <?= \App\Core\Helper::rupiah($srv['harga']) ?><span style="font-size: 0.85rem; font-weight: 600; color: var(--ink-muted);">/<?= htmlspecialchars($satuan) ?></span>
                    </div>

                    <div style="font-size: 0.78rem; color: var(--ink-muted);">
                        Estimasi selesai: <?= htmlspecialchars($srv['estimasi_waktu']) ?> Jam
                    </div>

                    <div style="display: flex; gap: 8px; margin-top: auto; padding-top: 10px;">
                        <button type="button" class="btn btn-secondary orders-action-btn" style="flex: 1; height: 32px; font-size: 0.76rem;"
                                onclick="editLayanan(<?= htmlspecialchars(json_encode($srv, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>)">
                            Ubah Harga
                        </button>
                        <form action="<?= \App\Core\Helper::url('layanan', ['action' => 'toggle']) ?>" method="POST" style="flex: 1;">
                            <input type="hidden" name="id_layanan" value="<?= $srv['id_layanan'] ?>">
                            <button type="submit" class="btn btn-secondary orders-action-btn" style="width: 100%; height: 32px; font-size: 0.76rem;">
                                <?= $isActive ? 'Nonaktifkan' : 'Aktifkan' ?>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Kartu 6: Metode Pembayaran -->
            <div class="service-admin-card" style="background: var(--surface); border: 1px solid var(--line); border-radius: 16px; padding: 18px 20px; box-shadow: var(--shadow-border); display: flex; flex-direction: column; gap: 8px;">
                <div style="font-size: 1rem; font-weight: 800; color: var(--ink); margin-bottom: 4px;">Metode Pembayaran</div>
                <div style="display: flex; flex-direction: column;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--line);">
                        <span style="font-size: 0.82rem; color: var(--ink); font-weight: 600;">Tunai (Cash)</span>
                        <span class="semantic-pill is-success" style="font-size: 0.68rem; padding: 2px 7px; border-radius: 9999px;">Aktif</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--line);">
                        <span style="font-size: 0.82rem; color: var(--ink); font-weight: 600;">QRIS / E-Wallet</span>
                        <span class="semantic-pill is-success" style="font-size: 0.68rem; padding: 2px 7px; border-radius: 9999px;">Aktif</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--line);">
                        <span style="font-size: 0.82rem; color: var(--ink); font-weight: 600;">Transfer Bank (BCA/BRI/Mandiri)</span>
                        <span class="semantic-pill is-success" style="font-size: 0.68rem; padding: 2px 7px; border-radius: 9999px;">Aktif</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0;">
                        <span style="font-size: 0.82rem; color: var(--ink); font-weight: 600;">Bayar Nanti (Saat Pengambilan)</span>
                        <span class="semantic-pill is-success" style="font-size: 0.68rem; padding: 2px 7px; border-radius: 9999px;">Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH & EDIT LAYANAN -->
    <div id="layananModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px); z-index: 999; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #ffffff; width: 100%; max-width: 480px; border-radius: 18px; box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.2); overflow: hidden; border: 1px solid #e2e8f0; animation: modalPop 0.18s cubic-bezier(0.16, 1, 0.3, 1);">
            <div style="padding: 16px 22px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 28px; height: 28px; border-radius: 8px; background: rgba(2, 132, 199, 0.1); color: #0284c7; display: flex; align-items: center; justify-content: center;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/></svg>
                    </span>
                    <strong id="modalLayananTitle" style="font-size: 0.98rem; font-weight: 700; color: #0f172a;">Tambah Layanan Baru</strong>
                </div>
                <button type="button" onclick="closeLayananModal()" style="border: none; background: #f1f5f9; color: #64748b; width: 28px; height: 28px; border-radius: 8px; font-size: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">&times;</button>
            </div>
            <form id="layananModalForm" action="<?= \App\Core\Helper::url('layanan', ['action' => 'store']) ?>" method="POST" style="padding: 20px 24px; display: flex; flex-direction: column; gap: 14px;">
                <input type="hidden" name="id_layanan" id="inputLayananId" value="">
                
                <div>
                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Nama Layanan <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="nama_layanan" id="inputNamaLayanan" required placeholder="Contoh: Cuci Kering + Setrika" class="form-control" style="width: 100%; height: 38px; border: 1px solid #cbd5e1; border-radius: 9px; padding: 0 12px; font-size: 0.85rem;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Kategori <span style="color: #ef4444;">*</span></label>
                        <select name="id_kategori" id="selectKategoriLayanan" required class="form-control" style="width: 100%; height: 38px; border: 1px solid #cbd5e1; border-radius: 9px; padding: 0 10px; font-size: 0.85rem;">
                            <?php foreach ($kategoriList as $k): ?>
                                <option value="<?= $k['id_kategori'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Satuan Hitung <span style="color: #ef4444;">*</span></label>
                        <select name="satuan" id="selectSatuanLayanan" required class="form-control" style="width: 100%; height: 38px; border: 1px solid #cbd5e1; border-radius: 9px; padding: 0 10px; font-size: 0.85rem;">
                            <option value="kg">kg (Kilogram)</option>
                            <option value="pcs">pcs (Satuan/Item)</option>
                            <option value="pasang">pasang (Sepatu/Sandal)</option>
                            <option value="m2">m² (Karpet/Gorden)</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Tarif / Harga (Rp) <span style="color: #ef4444;">*</span></label>
                        <input type="number" name="harga" id="inputHargaLayanan" required min="100" step="500" placeholder="7000" class="form-control" style="width: 100%; height: 38px; border: 1px solid #cbd5e1; border-radius: 9px; padding: 0 12px; font-size: 0.85rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Estimasi Selesai (Jam) <span style="color: #ef4444;">*</span></label>
                        <input type="number" name="estimasi_waktu" id="inputEstimasiLayanan" required min="1" placeholder="24" class="form-control" style="width: 100%; height: 38px; border: 1px solid #cbd5e1; border-radius: 9px; padding: 0 12px; font-size: 0.85rem;">
                    </div>
                </div>

                <div id="statusAktifWrap" style="display: flex; align-items: center; gap: 8px; padding-top: 4px;">
                    <input type="checkbox" name="status_aktif" id="checkStatusAktif" value="1" checked style="width: 16px; height: 16px; accent-color: #0284c7;">
                    <label for="checkStatusAktif" style="font-size: 0.8rem; font-weight: 600; color: #334155; cursor: pointer;">Layanan Aktif (Bisa dipilih di Kasir POS)</label>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="closeLayananModal()" style="flex: 1; height: 38px; border: 1px solid #e2e8f0; background: #ffffff; color: #475569; font-size: 0.82rem; font-weight: 600; border-radius: 10px; cursor: pointer;">Batal</button>
                    <button type="submit" id="btnSubmitLayanan" style="flex: 1; height: 38px; border: none; background: #0284c7; color: #ffffff; font-size: 0.82rem; font-weight: 700; border-radius: 10px; cursor: pointer; box-shadow: 0 2px 4px rgba(2, 132, 199, 0.25);">Simpan Tarif</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openAddLayananModal() {
        const modal = document.getElementById('layananModal');
        const form = document.getElementById('layananModalForm');
        const title = document.getElementById('modalLayananTitle');
        const btn = document.getElementById('btnSubmitLayanan');
        if (!modal || !form) return;

        title.textContent = 'Tambah Layanan Baru';
        btn.textContent = 'Simpan Layanan';
        form.action = 'index.php?page=layanan&action=store';

        document.getElementById('inputLayananId').value = '';
        document.getElementById('inputNamaLayanan').value = '';
        document.getElementById('inputHargaLayanan').value = '';
        document.getElementById('inputEstimasiLayanan').value = '24';
        document.getElementById('selectSatuanLayanan').value = 'kg';
        document.getElementById('checkStatusAktif').checked = true;

        modal.style.display = 'flex';
    }

    function editLayanan(data) {
        if (!data) return;
        const modal = document.getElementById('layananModal');
        const form = document.getElementById('layananModalForm');
        const title = document.getElementById('modalLayananTitle');
        const btn = document.getElementById('btnSubmitLayanan');
        if (!modal || !form) return;

        title.textContent = 'Ubah Tarif & Spesifikasi';
        btn.textContent = 'Perbarui Tarif';
        form.action = 'index.php?page=layanan&action=update';

        document.getElementById('inputLayananId').value = data.id_layanan || '';
        document.getElementById('inputNamaLayanan').value = data.nama_layanan || '';
        document.getElementById('inputHargaLayanan').value = data.harga || '';
        document.getElementById('inputEstimasiLayanan').value = data.estimasi_waktu || '24';
        document.getElementById('selectSatuanLayanan').value = data.satuan || 'kg';
        if (data.id_kategori) {
            document.getElementById('selectKategoriLayanan').value = data.id_kategori;
        }
        document.getElementById('checkStatusAktif').checked = (data.status_aktif == 1 || data.status_aktif === true);

        modal.style.display = 'flex';
    }

    function closeLayananModal() {
        const modal = document.getElementById('layananModal');
        if (modal) modal.style.display = 'none';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLayananModal();
    });
    </script>
<?php endif; ?>
