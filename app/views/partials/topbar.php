<?php
/** TOPBAR + SUBBAR. Variabel: $pageTitle, $pageSubtitle */
$user   = auth_user();
$counts = nav_counts();
?>
<header class="topbar">
  <div>
    <h1 class="page-title" id="page-title"><?= e($pageTitle) ?></h1>
    <p class="page-subtitle" id="page-subtitle"><?= e($pageSubtitle) ?></p>
  </div>

  <div class="topbar-controls">
    <div class="search-pill">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8" /><path d="M21 21l-4.35-4.35" />
      </svg>
      <input type="text" id="global-search" placeholder="Cari kode, nama, atau HP (F1 /)" autocomplete="off" oninput="App.handleGlobalSearch(this.value)" />
      <span class="kbd-tag">F1</span>
    </div>

    <div class="status-pills">
      <div class="metric-pill">
        <span class="dot-live"></span>
        <span id="pill-active-orders">Pesanan aktif: <?= (int) $counts['active'] ?></span>
      </div>
      <div class="metric-pill subtle">
        <span id="pill-today-orders">Hari ini: <?= (int) $counts['today'] ?> pesanan</span>
      </div>
    </div>
  </div>
</header>

<div class="subbar">
  <div class="role-switcher" id="role-switcher">
    <span>Masuk sebagai</span>
    <span class="role-btn active" id="role-indicator"><?= e(ucfirst($user['role'] ?? 'Admin')) ?></span>
  </div>

  <div class="utility-btns">
    <button type="button" class="icon-btn" id="btn-sound" onclick="App.toggleSound()" title="Suara Efek Interaksi">
      <span id="sound-icon">
        <span class="snd-on"><?= icon('soundOn') ?></span>
        <span class="snd-off" hidden><?= icon('soundOff') ?></span>
      </span>
    </button>
    <button type="button" class="icon-btn" onclick="App.openShortcutModal()" title="Pintasan Keyboard (?)">
      <span class="mono" style="font-weight: 800; font-size: 13px">?</span>
    </button>
    <button type="button" class="icon-btn" onclick="App.toggleTheme()" title="Mode Gelap Night Shift">
      <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
      </svg>
    </button>
    <form method="post" action="<?= e(url('/logout')) ?>" class="inline-form">
      <?= csrf_field() ?>
      <button type="submit" class="icon-btn" title="Keluar"><?= icon('logout') ?></button>
    </form>
  </div>
</div>
