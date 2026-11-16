<?php
$customerList = $pelanggan ?? [];
$totalCustomers = count($customerList);
?>

<div class="page-sheet-container is-orders-page" style="display: flex; flex-direction: column; gap: 14px;">
    <!-- Tier 1: Header Bar (Foto 2 / index.html) -->
    <div class="selesai-header-bar" style="display: flex; justify-content: space-between; align-items: center;">
        <div class="selesai-title-group" style="display: flex; align-items: center; gap: 10px;">
            <h2 class="selesai-page-title" style="margin: 0; font-size: 1.35rem; font-weight: 800; color: #0F172A;">Data Pelanggan</h2>
            <span class="selesai-sub-pill" style="font-size: 0.74rem; font-weight: 700; background: #E0F2FE; color: #0284C7; padding: 3px 10px; border-radius: 9999px;">
                <span id="cust-count-badge"><?= $totalCustomers ?></span> dari <?= $totalCustomers ?> Kontak
            </span>
        </div>
        <?php include __DIR__ . '/../layouts/kasir_header_actions.php'; ?>
    </div>

    <!-- Tier 2: Toolbar Inline (Pencarian di kiri, Tombol Tambah di kanan) -->
    <div class="orders-inline-toolbar orders-filter-bar-wrap" style="display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
        <div class="orders-search-box" style="position: relative; flex: 1 1 260px; max-width: 340px;">
            <span class="orders-search-icon" style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #94A3B8; display: inline-flex;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" id="customer-list-search-input" class="form-control"
                placeholder="Cari nama atau no. WhatsApp..."
                oninput="filterCustomerTable(this.value)"
                style="padding-left: 34px; height: 36px; border-radius: 12px; font-size: 0.82rem; border: 1px solid #E2E8F0;">
        </div>
        <div style="flex: 1;"></div>
        <button type="button" class="btn btn-primary" onclick="openCustomerModal()" style="height: 36px; border-radius: 9999px; padding: 0 16px; font-size: 0.80rem; font-weight: 700; background: #0284C7; color: #FFFFFF; border: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px -1px rgba(2, 132, 199, 0.35);">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            <span>Tambah Pelanggan</span>
        </button>
    </div>

    <!-- Tier 3: Tabel Pelanggan Ramping & Terstruktur (Foto 2) -->
    <div class="modern-table-card" style="background: #FFFFFF; border: 1px solid rgba(226, 232, 240, 0.9); border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);">
        <div class="orders-table-scroll" style="overflow-x: auto;">
            <table class="modern-table" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.82rem;">
                <thead>
                    <tr style="background: #FFFFFF; border-bottom: 1px solid #E2E8F0; color: #64748B; font-size: 0.70rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">
                        <th style="width: 110px; padding: 10px 14px; white-space: nowrap;">ID</th>
                        <th style="padding: 10px 14px; white-space: nowrap;">PELANGGAN</th>
                        <th style="padding: 10px 14px; white-space: nowrap;">WHATSAPP / NO. HP</th>
                        <th style="text-align: center; width: 130px; padding: 10px 14px; white-space: nowrap;">TOTAL ORDER</th>
                        <th style="text-align: right; width: 210px; padding: 10px 16px; white-space: nowrap;">AKSI</th>
                    </tr>
                </thead>
                <tbody id="customerTableBody">
                    <?php if (empty($customerList)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px 16px; color: #94A3B8;">
                                Belum ada data pelanggan yang tersimpan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customerList as $c): 
                            $cName = $c['nama_pelanggan'];
                            $cWords = explode(' ', trim($cName));
                            $cInitials = strtoupper(substr($cWords[0] ?? 'C', 0, 1) . (isset($cWords[1]) ? substr($cWords[1], 0, 1) : substr($cWords[0] ?? 'P', 1, 1)));
                            $waClean = preg_replace('/[^0-9]/', '', $c['no_telepon']);
                            if (str_starts_with($waClean, '0')) $waClean = '62' . substr($waClean, 1);
                            $orderCount = (int)($c['total_order'] ?? 0);
                            $isLoyal = $orderCount >= 5;
                        ?>
                            <tr class="customer-row-item" style="border-bottom: 1px solid #F1F5F9; transition: background 150ms ease;">
                                <td style="padding: 10px 14px; white-space: nowrap;">
                                    <span class="ticket-code-tag font-mono" style="font-size: 0.82rem; font-weight: 700; color: #0F172A; letter-spacing: 0.02em;"><?= htmlspecialchars($c['id_pelanggan']) ?></span>
                                </td>
                                <td style="padding: 10px 14px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div class="cust-avatar-circle" style="width: 30px; height: 30px; border-radius: 50%; background: #E0F2FE; color: #0284C7; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.74rem; flex-shrink: 0;">
                                            <?= $cInitials ?>
                                        </div>
                                        <div style="line-height: 1.25;">
                                            <strong class="cust-name-val" style="color: #0F172A; font-size: 0.82rem; font-weight: 700;"><?= htmlspecialchars($cName) ?></strong>
                                            <div style="margin-top: 1px;">
                                                <?php if ($orderCount >= 5): ?>
                                                    <span style="color: #0284C7; font-size: 0.70rem; font-weight: 700;">Pelanggan Loyal</span>
                                                <?php elseif ($orderCount >= 1): ?>
                                                    <span style="color: #16A34A; font-size: 0.70rem; font-weight: 600;">Pelanggan Aktif</span>
                                                <?php else: ?>
                                                    <span style="color: #64748B; font-size: 0.70rem; font-weight: 500;">Pelanggan Baru</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 10px 14px; white-space: nowrap;">
                                    <?php if (!empty($waClean)): ?>
                                        <a href="https://wa.me/<?= $waClean ?>" target="_blank" rel="noopener noreferrer" class="cust-wa-btn font-mono" style="display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; border-radius: 9999px; background: #F0FDF4; border: 1px solid #DCFCE7; color: #15803D; text-decoration: none; font-size: 0.78rem; font-weight: 600; white-space: nowrap;" title="Buka obrolan WhatsApp">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                            <span class="cust-phone-val"><?= htmlspecialchars($c['no_telepon']) ?></span>
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #94A3B8; font-size: 0.74rem;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 10px 14px; text-align: center; white-space: nowrap;">
                                    <span class="num-tabular" style="font-weight: 600; font-size: 0.80rem; color: #0F172A;">
                                        <?= $orderCount ?> order
                                    </span>
                                </td>
                                <td style="padding: 10px 16px; text-align: right; white-space: nowrap;">
                                    <div style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                        <a href="<?= \App\Core\Helper::url('pos', ['cust' => $c['id_pelanggan']]) ?>" class="btn btn-secondary" style="font-size: 0.74rem; font-weight: 600; padding: 3px 12px; border-radius: 9999px; text-decoration: none; color: #0F172A; border: 1px solid #E2E8F0; background: #FFFFFF; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;">
                                            + Buat Order
                                        </a>
                                        <button type="button" class="btn btn-secondary" style="font-size: 0.74rem; font-weight: 600; padding: 3px 10px; border-radius: 9999px; color: #0F172A; border: 1px solid #E2E8F0; background: #FFFFFF; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;"
                                            onclick="editCustomerModal('<?= $c['id_pelanggan'] ?>', '<?= htmlspecialchars($cName, ENT_QUOTES) ?>', '<?= htmlspecialchars($c['no_telepon'], ENT_QUOTES) ?>')">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            <span>Edit</span>
                                        </button>
                                        <?php if (\App\Core\Auth::isAdmin()): ?>
                                            <form action="<?= \App\Core\Helper::url('pelanggan', ['action' => 'delete']) ?>" method="POST" style="display:inline;"
                                                data-confirm="true"
                                                data-confirm-title="Hapus Data Pelanggan?"
                                                data-confirm-message="Pelanggan ini akan dihapus dari database. Data dengan riwayat transaksi aktif tidak dapat dihapus."
                                                data-confirm-badge="<?= htmlspecialchars($cName) ?> &bull; <?= htmlspecialchars($c['no_telepon']) ?>"
                                                data-confirm-btn="Ya, Hapus Pelanggan"
                                                data-confirm-type="danger">
                                                <input type="hidden" name="id_pelanggan" value="<?= $c['id_pelanggan'] ?>">
                                                <button type="submit" class="btn btn-secondary" style="font-size: 0.74rem; font-weight: 600; padding: 3px 8px; border-radius: 9999px; color: #ef4444; border: 1px solid #fee2e2; background: #fef2f2; cursor: pointer;">
                                                    Hapus
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination Dock (Foto 2) -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 18px; border-top: 1px solid #E2E8F0; flex-wrap: wrap; gap: 10px; background: #FFFFFF;">
            <span id="cust-total-count" style="font-size: 0.78rem; color: #64748B; font-weight: 500;">Menampilkan 1–<?= $totalCustomers ?> dari <?= $totalCustomers ?> kontak</span>
            <div id="customer-pagination-dock" class="orders-pagination-dock" style="margin-left: auto;">
                <nav id="pagi-nav-cust" class="wg-capsule" aria-label="Halaman Pelanggan">
                    <button type="button" id="pagi-prev-cust" class="wg-nav is-prev" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
                        <span>Sebelumnya</span>
                    </button>
                    <div id="pagi-track-cust" class="wg-pages-track">
                        <span id="pagi-lens-cust" class="wg-active-pill" aria-hidden="true"></span>
                        <button type="button" class="pagination-dot is-active" aria-current="page" style="position: relative; z-index: 2;">1</button>
                    </div>
                    <button type="button" id="pagi-next-cust" class="wg-nav is-next" disabled>
                        <span>Berikutnya</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pelanggan Baru & Edit -->
<div id="customer-modal-sandbox"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const track = document.getElementById('pagi-track-cust');
    const lens = document.getElementById('pagi-lens-cust');
    const active = track ? track.querySelector('.pagination-dot.is-active') : null;
    if (track && lens && active) {
        lens.style.width = active.offsetWidth + 'px';
        lens.style.transform = `translate3d(${active.offsetLeft}px, 0, 0)`;
        lens.style.opacity = '1';
    }
});

function filterCustomerTable(query) {
    const q = query.toLowerCase().trim();
    let visibleCount = 0;
    document.querySelectorAll('#customerTableBody tr.customer-row-item').forEach(tr => {
        const name = (tr.querySelector('.cust-name-val')?.textContent || '').toLowerCase();
        const phone = (tr.querySelector('.cust-phone-val')?.textContent || '').toLowerCase();
        if (name.includes(q) || phone.includes(q)) {
            tr.style.display = '';
            visibleCount++;
        } else {
            tr.style.display = 'none';
        }
    });
    const badge = document.getElementById('cust-count-badge');
    if (badge) badge.textContent = visibleCount;
}

function openCustomerModal(id = '', name = '', phone = '') {
    const sandbox = document.getElementById('customer-modal-sandbox');
    const isEdit = Boolean(id);
    const actionUrl = isEdit ? 'index.php?page=pelanggan&action=update' : 'index.php?page=pelanggan&action=store';

    sandbox.innerHTML = `
        <div class="dialog-backdrop" onclick="this.remove()">
            <div class="dialog-panel" style="max-width: 440px;" onclick="event.stopPropagation()">
                <div class="dialog-head">
                    <span class="dialog-title">${isEdit ? 'Edit Pelanggan' : 'Pelanggan Baru'}</span>
                    <button type="button" class="btn btn-ghost" style="padding: 2px 6px;" onclick="this.closest('.dialog-backdrop').remove()">Esc</button>
                </div>
                <form action="${actionUrl}" method="POST">
                    ${isEdit ? `<input type="hidden" name="id_pelanggan" value="${id}">` : ''}
                    <div class="dialog-body" style="padding: 18px 22px; display: flex; flex-direction: column; gap: 14px;">
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #0F172A; margin-bottom: 5px;">Nama Lengkap:</label>
                            <input type="text" name="nama_pelanggan" class="form-control" required placeholder="Contoh: Ibu Rina Kartika" value="${name}" minlength="2" maxlength="100" pattern="^[a-zA-Z\\s\\.\\'\\-]+$" title="Nama hanya boleh berisi huruf, spasi, titik, atau tanda hubung (min 2 karakter)">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #0F172A; margin-bottom: 5px;">Nomor WhatsApp / HP:</label>
                            <input type="tel" name="no_telepon" class="form-control" required placeholder="Contoh: 081298765432" value="${phone}" oninput="this.value=this.value.replace(/[^0-9+]/g,'')" pattern="^(\\+62|62|08)[0-9]{8,13}$" title="Nomor HP Indonesia yang valid: diawali 08, 62, atau +62 (10-15 digit)">
                            <div style="font-size: 0.7rem; color: #64748B; margin-top: 4px;">Contoh: 08123456789 atau +628123456789</div>
                        </div>
                    </div>
                    <div class="dialog-foot">
                        <button type="button" class="btn btn-secondary" onclick="this.closest('.dialog-backdrop').remove()">Batal</button>
                        <button type="submit" class="btn btn-primary" style="background: #0284C7;">Simpan &amp; Pilih</button>
                    </div>
                </form>
            </div>
        </div>
    `;
}

function editCustomerModal(id, name, phone) {
    openCustomerModal(id, name, phone);
}
</script>
