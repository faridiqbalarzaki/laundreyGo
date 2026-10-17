<?php
/**
 * LAYOUT UTAMA (area terautentikasi).
 * Variabel: $pageTitle, $pageSubtitle, $activeMenu, $content (HTML view), $pageScripts (opsional)
 */
$user  = auth_user();
$title = ($pageTitle ?? 'LaundryGo') . ' — LaundryGo';
?>
<!doctype html>
<html lang="id">
<head>
  <?php partial('head', ['title' => $title]); ?>
</head>
<body data-role="<?= e(ucfirst($user['role'] ?? 'admin')) ?>" data-base="<?= e(base_url()) ?>">
  <script>try{if(localStorage.getItem('lg.theme')==='dark')document.body.classList.add('dark')}catch(e){}</script>

  <div id="board-app" class="app-shell no-print">
    <?php partial('sidebar', ['activeMenu' => $activeMenu ?? '']); ?>

    <main class="workspace">
      <?php partial('topbar', [
          'pageTitle'    => $pageTitle ?? '',
          'pageSubtitle' => $pageSubtitle ?? '',
      ]); ?>

      <!-- Kontainer halaman: isi view dari route -->
      <section id="app-view" class="view-container"><?= $content ?></section>
    </main>
  </div>

  <?php partial('order_detail_drawer'); ?>
  <?php partial('action_modal'); ?>

  <!-- Toast & undo bar (ARIA-live) -->
  <div id="toast-container" class="toast-container no-print" aria-live="polite"></div>

  <!-- Area cetak khusus (@media print) -->
  <div id="print-mount" class="print-only"></div>

  <script src="<?= e(asset('js/config.js')) ?>"></script>
  <script src="<?= e(asset('js/store.js')) ?>"></script>
  <script src="<?= e(asset('js/views.js')) ?>"></script>
  <script src="<?= e(asset('js/app.js')) ?>"></script>
  <script src="<?= e(asset('js/ui.js')) ?>"></script>
  <?php foreach (($pageScripts ?? []) as $script): ?>
  <script src="<?= e(asset('js/' . $script)) ?>"></script>
  <?php endforeach; ?>
</body>
</html>
