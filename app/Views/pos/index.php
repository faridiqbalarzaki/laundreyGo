<?php
/**
 * Kasir POS Workstation View
 * 100% Identik dengan Video 1 & index.html
 */
$currentUser = \App\Core\Auth::user();
$cashierName = $currentUser['nama_anggota'] ?? 'Siti Rahayu';
?>

<div id="pos-app-root">
    <!-- Rendered via JavaScript Engine in pos.js -->
</div>

<!-- Modal Sandbox Container for Dialogs (Thermal Receipt & Pelanggan Baru) -->
<div id="modal-sandbox"></div>

<!-- Toast Container for Notifications -->
<div id="toast-container" class="toast-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; pointer-events: none;"></div>

<!-- Injected Backend Data -->
<script>
window.POS_DATA = {
    services: <?= json_encode($layananList, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
    customers: <?= json_encode($pelangganList, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
    cashierName: <?= json_encode($cashierName) ?>,
    storeUrl: <?= json_encode(\App\Core\Helper::url('pos', ['action' => 'store'])) ?>,
    customerStoreUrl: <?= json_encode(\App\Core\Helper::url('pelanggan', ['action' => 'store'])) ?>
};
</script>
<script src="<?= \App\Core\Helper::asset('js/pos.js') ?>"></script>
