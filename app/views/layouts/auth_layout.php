<?php
/** LAYOUT PUBLIK (login & error tamu). Variabel: $content */
?>
<!doctype html>
<html lang="id">
<head>
  <?php partial('head', ['title' => 'Masuk — LaundryGo']); ?>
</head>
<body data-base="<?= e(base_url()) ?>">
  <script>try{if(localStorage.getItem('lg.theme')==='dark')document.body.classList.add('dark')}catch(e){}</script>
  <div class="login-screen no-print"><?= $content ?></div>
  <script src="<?= e(asset('js/ui.js')) ?>"></script>
</body>
</html>
