<!-- MODAL TAMBAH PELANGGAN CEPAT (GLOBAL) -->
<div class="modal-backdrop" id="addCustomerGlobalModal" style="display:none;">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Pelanggan Baru</h3>
            <button type="button" class="btn-close-modal" onclick="closeAddCustomerModal()">&times;</button>
        </div>
        <form action="<?= \App\Core\Helper::url('pelanggan', ['action' => 'store']) ?>" method="POST" id="addCustomerForm">
            <div class="modal-body">
                <div class="form-group">
                    <label for="newCustNama">Nama Lengkap Pelanggan:</label>
                    <input type="text" name="nama_pelanggan" id="newCustNama" class="form-input" required placeholder="Contoh: Amanda Putri">
                </div>
                <div class="form-group">
                    <label for="newCustTelp">Nomor WhatsApp / HP:</label>
                    <input type="tel" name="no_telepon" id="newCustTelp" class="form-input" required placeholder="Contoh: 081234567890">
                </div>
                <div class="form-group">
                    <label for="newCustAlamat">Alamat Tempat Tinggal:</label>
                    <textarea name="alamat" id="newCustAlamat" class="form-textarea" rows="2" placeholder="Contoh: Jl. Melati No. 12, Karangploso"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAddCustomerModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Pelanggan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL BANTUAN SHORTCUT KEYBOARD -->
<div class="modal-backdrop" id="keyboardShortcutModal" style="display:none;">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Pintasan Keyboard (Shortcuts)</h3>
            <button type="button" class="btn-close-modal" onclick="closeShortcutModal()">&times;</button>
        </div>
        <div class="modal-body">
            <table class="shortcut-table">
                <tr><td><kbd>F2</kbd></td><td>Buka Kasir POS / Simpan & Cetak Pesanan</td></tr>
                <tr><td><kbd>F3</kbd></td><td>Cetak Label Stiker Thermal (58x40mm)</td></tr>
                <tr><td><kbd>N</kbd></td><td>Buka Modal Tambah Pelanggan Baru</td></tr>
                <tr><td><kbd>/</kbd></td><td>Fokus ke Pencarian Global / Pelanggan</td></tr>
                <tr><td><kbd>Esc</kbd></td><td>Tutup Modal / Drawer yang Aktif</td></tr>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeShortcutModal()">Tutup</button>
        </div>
    </div>
</div>

<!-- MODAL PILIH CETAK (STRUK KASIR VS LABEL STIKER) -->
<div class="modal-backdrop" id="printSelectModal" style="display:none;">
    <div class="modal-box" style="max-width:440px;">
        <div class="modal-header">
            <h3 class="modal-title">Cetak Dokumen Pesanan</h3>
            <button type="button" class="btn-close-modal" onclick="closePrintModal()">&times;</button>
        </div>
        <div class="modal-body text-center">
            <p class="text-muted" style="margin-bottom:20px;">Pilih format dokumen yang ingin dicetak ke printer:</p>
            <div class="print-options-grid">
                <button type="button" class="btn-print-choice" onclick="executePrintReceipt()">
                    <div class="choice-icon">🧾</div>
                    <div class="choice-title">Struk Nota Kasir</div>
                    <div class="choice-sub text-muted">Format Thermal 58mm / 80mm</div>
                </button>
                <button type="button" class="btn-print-choice" onclick="executePrintLabel()">
                    <div class="choice-icon">🏷️</div>
                    <div class="choice-title">Label Stiker Pakaian</div>
                    <div class="choice-sub text-muted">Format Label 58x40mm</div>
                </button>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-block" onclick="closePrintModal()">Selesai & Tutup</button>
        </div>
    </div>
</div>

<script>
let activePrintOrderId = null;

function openAddCustomerModal(initialName = '') {
    const modal = document.getElementById('addCustomerGlobalModal');
    if (modal) {
        modal.style.display = 'flex';
        const nameInput = document.getElementById('newCustNama');
        if (nameInput) {
            nameInput.value = initialName;
            nameInput.focus();
        }
    }
}

function closeAddCustomerModal() {
    const modal = document.getElementById('addCustomerGlobalModal');
    if (modal) modal.style.display = 'none';
}

function openShortcutModal() {
    document.getElementById('keyboardShortcutModal').style.display = 'flex';
}
function closeShortcutModal() {
    document.getElementById('keyboardShortcutModal').style.display = 'none';
}

function openPrintModal(orderId) {
    activePrintOrderId = orderId;
    document.getElementById('printSelectModal').style.display = 'flex';
}
function closePrintModal() {
    document.getElementById('printSelectModal').style.display = 'none';
}

async function executePrintReceipt() {
    if (!activePrintOrderId) return;
    await loadOrderToThermalPrint(activePrintOrderId, 'receipt');
    window.print();
}

async function executePrintLabel() {
    if (!activePrintOrderId) return;
    await loadOrderToThermalPrint(activePrintOrderId, 'label');
    window.print();
}
</script>
