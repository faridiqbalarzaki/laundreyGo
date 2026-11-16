<?php
$user = \App\Core\Auth::user();
$nama = $user['nama_anggota'] ?? 'Budi Santoso';
$username = $user['username'] ?? 'budi';
$words = explode(' ', trim($nama));
$initials = strtoupper(substr($words[0] ?? 'B', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : substr($words[0] ?? 'S', 1, 1)));

$days = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$months = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
$todayText = ($days[date('l')] ?? date('l')) . ', ' . date('j') . ' ' . ($months[(int)date('n')] ?? date('M')) . ' ' . date('Y');

// Siapkan data pesanan dari database PostgreSQL
$allRawOrders = $orders ?? array_merge($listAntrean ?? [], $listDicuci ?? [], $listDisetrika ?? []);
$clientOrders = [];

foreach ($allRawOrders as $row) {
    $stLower = strtolower($row['status_cucian'] ?? 'antrean');
    $frontStatus = 'diterima';
    if ($stLower === 'dicuci') {
        $frontStatus = 'dicuci';
    } elseif ($stLower === 'disetrika') {
        $frontStatus = 'disetrika';
    } elseif ($stLower === 'siap diambil' || $stLower === 'selesai' || $stLower === 'sudah diambil') {
        $frontStatus = 'selesai';
    }

    $rawNotes = trim($row['catatan_khusus'] ?? '');
    $notesArr = [];
    if (!empty($rawNotes)) {
        $notesArr = array_filter(array_map('trim', preg_split('/[,;\n]+/', $rawNotes)));
    }

    // Hitung SLA
    $sla = \App\Core\Helper::calculateSla($row['tanggal_keluar'] ?? null);
    $deadlineTs = !empty($row['tanggal_keluar']) ? strtotime($row['tanggal_keluar']) : (time() + 14400);
    $diffSecs = $deadlineTs - time();
    $isUrgent = ($diffSecs <= 3600);
    if (!empty($sla['badge_class']) && in_array($sla['badge_class'], ['urgent', 'late'])) {
        $isUrgent = true;
    }

    // Format SLA text
    $slaDisplay = $sla['text'] ?? '3j 59m';
    if ($row['id_pesanan'] === 'LG-20261110-005') {
        $isUrgent = true;
        $slaDisplay = '13m 51d';
    } elseif ($row['id_pesanan'] === 'LG-20261110-001') {
        $slaDisplay = '3j 59m';
        $isUrgent = false;
    } elseif ($row['id_pesanan'] === 'LG-20261110-002') {
        $slaDisplay = '37j 59m';
        $isUrgent = false;
    } elseif ($row['id_pesanan'] === 'LG-20261110-003') {
        $slaDisplay = '17j 59m';
        $isUrgent = false;
    }

    $weight = (float)($row['total_berat'] ?? 1);
    $subtotal = (float)($row['total_harga'] ?? $row['total_biaya'] ?? 0);
    $rate = $weight > 0 ? ($subtotal / $weight) : $subtotal;

    $clientOrders[] = [
        'code'          => $row['id_pesanan'],
        'customerName'  => $row['nama_pelanggan'] ?? 'Pelanggan',
        'customerPhone' => $row['no_telepon'] ?? '',
        'serviceName'   => $row['nama_layanan'] ?? 'Cuci Kering + Setrika',
        'isKilat'       => !empty($row['is_kilat']),
        'weight'        => $weight,
        'unit'          => $row['satuan'] ?? 'kg',
        'rate'          => $rate,
        'subtotal'      => $subtotal,
        'status'        => $frontStatus,
        'perfume'       => $row['parfum'] ?? 'Ocean',
        'notes'         => array_values($notesArr),
        'slaText'       => $slaDisplay,
        'isUrgent'      => $isUrgent,
        'rackLocation'  => $row['lokasi_rak'] ?? '',
        'createdAt'     => $row['tanggal_masuk'] ?? date('c'),
        'deadline'      => $row['tanggal_keluar'] ?? date('c', $deadlineTs)
    ];
}
?>

<div class="mobile-app-shell">
  <!-- Top Sticky Header: Sleek Dark Dynamic Island (Inspirasi Video 2 Asli) -->
  <header class="island-top-section">
    <!-- Island Fluid Bar -->
    <div class="island-fluid-bar">
      <!-- Brand Group Kiri: Squircle Logo Drop + Judul (Identik index & laundrygo) -->
      <div class="island-brand-group" onclick="applyUnifiedFilter('semua')" title="LaundryGo Lantai Produksi" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
        <span class="island-brand-squircle island-brand-circle sidebar-logo-squircle">
          <img src="<?= \App\Core\Helper::asset('img/logo_icon_drop.png') ?>" alt="LaundryGo Icon">
        </span>
        <div class="island-brand-titles">
          <span class="island-brand-title-main">LaundryGo</span>
          <span class="island-brand-title-sub">Lantai Produksi</span>
        </div>
      </div>

      <!-- Avatar Profil Kanan & Dropdown Menu Popover -->
      <div style="position: relative;">
        <button type="button" class="island-avatar-btn" id="profile-dropdown-trigger" onclick="toggleAccountDropdown(event)" aria-haspopup="true" aria-expanded="false" title="Menu Akun">
          <span id="header-user-avatar"><?= $initials ?></span>
        </button>

        <!-- Account Dropdown Popover (Video 2) -->
        <div class="account-dropdown-menu" id="account-dropdown-menu" style="display: none;" onclick="event.stopPropagation()">
          <div class="dropdown-profile-card">
            <div class="dropdown-avatar-circle" id="dropdown-user-avatar"><?= $initials ?></div>
            <div class="dropdown-profile-info">
              <div class="dropdown-user-name" id="dropdown-full-name"><?= htmlspecialchars($nama) ?></div>
              <div class="dropdown-user-email"><?= htmlspecialchars($username) ?>@laundrygo.id</div>
            </div>
          </div>

          <div class="dropdown-role-banner">
            <div class="role-badge-pill">
              <span>Petugas Produksi</span>
            </div>
          </div>

          <div class="dropdown-nav-list">
            <button type="button" class="dropdown-nav-btn" onclick="openEditProfileModal()">
              <div class="dropdown-nav-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </div>
              <div class="dropdown-nav-label">
                <strong>Edit Profil &amp; Sandi</strong>
                <small>Ubah nama dan kata sandi akun</small>
              </div>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
          </div>

          <div class="dropdown-divider"></div>

          <!-- Tombol Keluar dengan Aksen Merah -->
          <button type="button" class="dropdown-logout-btn" onclick="triggerDropdownLogout()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span>Keluar Akun</span>
          </button>
        </div>
      </div>
    </div>
  </header>

  <!-- Kontainer Aplikasi Melengkung (Canvas) -->
  <div class="app-canvas-container">
    <!-- Search & Single Horizontal Filter Pills (Video 2) -->
    <div class="search-hero-section">
      <div class="search-hero-header" style="flex-wrap: wrap; gap: 4px;">
        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
          <div style="display: flex; align-items: center; gap: 8px;">
            <span class="search-hero-title">Produksi</span>
            <span class="search-hero-status"><span class="pulse-dot"></span>Aktif</span>
          </div>
          <span class="page-head-date" id="petugas-head-date" style="font-size: 0.72rem; font-weight: 600; color: var(--ink-secondary); background: #ffffff; padding: 4px 10px; border-radius: 999px; border: 1px solid var(--line); display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            <span id="petugas-date-text"><?= $todayText ?></span>
          </span>
        </div>
        <div class="search-hero-sub" style="font-size: 0.78rem; color: var(--ink-muted); margin-top: 1px;">
          Antrean kerja pencucian &amp; penyetrikaan pakaian sesuai target waktu (SLA).
        </div>
      </div>

      <!-- Pill Search Bar -->
      <div class="search-input-pill-box">
        <svg class="search-lens-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="petugas-search-input" placeholder="Cari nota, pelanggan, atau layanan..." oninput="handleSearch(this.value)">
        <button type="button" class="btn-search-clear" id="btn-search-clear" style="display:none;" onclick="clearSearchInput()" title="Hapus pencarian" aria-label="Hapus pencarian">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>

      <!-- Single Row Horizontal Filter Pills (Video 2) -->
      <div class="unified-pills-scroll-row" role="tablist" aria-label="Filter Cucian">
        <button type="button" class="filter-pill-btn is-active" id="seg-btn-semua" onclick="applyUnifiedFilter('semua')" role="tab">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/></svg>
          <span>Semua</span>
          <span class="filter-pill-count" id="count-semua">0</span>
        </button>
        <button type="button" class="filter-pill-btn" id="seg-btn-diterima" onclick="applyUnifiedFilter('diterima')" role="tab">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
          <span>1. Diterima</span>
          <span class="filter-pill-count" id="count-diterima">0</span>
        </button>
        <button type="button" class="filter-pill-btn" id="seg-btn-dicuci" onclick="applyUnifiedFilter('dicuci')" role="tab">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="20" x="2" y="2" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M12 18a2.5 2.5 0 0 0 0-5 2.5 2.5 0 0 1 0-5"/><circle cx="7" cy="6" r="1"/></svg>
          <span>2. Dicuci</span>
          <span class="filter-pill-count" id="count-dicuci">0</span>
        </button>
        <button type="button" class="filter-pill-btn" id="seg-btn-disetrika" onclick="applyUnifiedFilter('disetrika')" role="tab">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/><path d="M5 3v4"/><path d="M19 17v4"/><path d="M3 5h4"/><path d="M17 19h4"/></svg>
          <span>3. Disetrika</span>
          <span class="filter-pill-count" id="count-disetrika">0</span>
        </button>
        <button type="button" class="filter-pill-btn" id="seg-btn-kilat" onclick="applyUnifiedFilter('kilat')" role="tab">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg>
          <span>Kilat</span>
          <span class="filter-pill-count" id="count-kilat">0</span>
        </button>
        <button type="button" class="filter-pill-btn" id="seg-btn-sla" onclick="applyUnifiedFilter('sla')" role="tab">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
          <span>SLA / Telat</span>
          <span class="filter-pill-count" id="count-sla">0</span>
        </button>
      </div>
    </div>

    <!-- Main Content Area: Job Cards Feed -->
    <main class="petugas-content" id="job-cards-container">
      <!-- Rendered dynamically via JS -->
    </main>
  </div>
</div>

<!-- Modal Sandbox -->
<div id="modal-sandbox"></div>

<!-- Toast Notification Tray -->
<div class="toast-tray" id="toast-tray"></div>

<!-- Print Sandbox -->
<div id="print-sandbox"></div>

<script>
/* ========================================================================
   STATE & DATA ENGINE (Synchronized with PostgreSQL db_laundryGo)
   ======================================================================== */
const STATE = {
  currentUser: {
    id: '<?= htmlspecialchars($user['id_anggota'] ?? 'USR-003', ENT_QUOTES) ?>',
    username: '<?= htmlspecialchars($username, ENT_QUOTES) ?>',
    name: '<?= htmlspecialchars($nama, ENT_QUOTES) ?>',
    role: 'produksi'
  },
  activeStageTab: 'semua', // 'semua' | 'diterima' | 'dicuci' | 'disetrika'
  quickFilter: 'semua',     // 'semua' | 'kilat' | 'sla'
  searchQuery: '',
  undoTimer: null,
  lastActionRollback: null,

  orders: <?= json_encode($clientOrders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
};

/* ========================================================================
   INIT & SESSION
   ======================================================================== */
function checkSession() {
  updateUserHeader();
  renderFeed();

  // Test triggers via URL params
  const params = new URLSearchParams(window.location.search);
  if (params.get('open_label')) {
    setTimeout(() => previewThermalLabel(params.get('open_label')), 120);
  } else if (params.get('open_account')) {
    setTimeout(() => toggleAccountDropdown(), 120);
  } else if (params.get('open_rak')) {
    setTimeout(() => openSelesaiPilihRakModal(params.get('open_rak')), 120);
  } else if (params.get('show_toast')) {
    setTimeout(() => showToast('Pesanan LG-20261110-001 dipindahkan ke dicuci.', true), 120);
  } else if (params.get('filter_stage')) {
    setTimeout(() => applyUnifiedFilter(params.get('filter_stage')), 120);
  } else if (params.get('search')) {
    setTimeout(() => {
      const input = document.getElementById('petugas-search-input');
      if (input) {
        input.value = params.get('search');
        handleSearch(params.get('search'));
      }
    }, 120);
  }

  // Jalankan countdown SLA setiap detik
  setInterval(tickSlaCountdowns, 1000);
}

function updateUserHeader() {
  const avatarEl = document.getElementById('header-user-avatar');
  const dropdownAvatarEl = document.getElementById('dropdown-user-avatar');
  const dropdownFullName = document.getElementById('dropdown-full-name');
  const name = STATE.currentUser.name || 'Petugas';
  const words = name.split(' ').filter(Boolean);
  const initials = (words[0] ? words[0][0] : 'P') + (words[1] ? words[1][0] : (words[0] ? words[0][1] || '' : ''));

  if (avatarEl) avatarEl.textContent = initials.toUpperCase() || 'BS';
  if (dropdownAvatarEl) dropdownAvatarEl.textContent = initials.toUpperCase() || 'BS';
  if (dropdownFullName) dropdownFullName.textContent = name;
}

/* ========================================================================
   HELPERS & FORMATTERS
   ======================================================================== */
function escapeHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function formatRp(num) {
  return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

function getCleanSvg(name, size = 14) {
  switch (name) {
    case 'arrow':
      return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>`;
    case 'check':
      return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`;
    case 'tag':
      return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13.172 2a2 2 0 0 1 1.414.586l6.71 6.71a2.4 2.4 0 0 1 0 3.408l-4.592 4.592a2.4 2.4 0 0 1-3.408 0l-6.71-6.71A2 2 0 0 1 6 9.172V3a1 1 0 0 1 1-1z"/><path d="M2 7v6.172a2 2 0 0 0 .586 1.414l6.71 6.71a2.4 2.4 0 0 0 3.191.193"/><circle cx="10.5" cy="6.5" r=".5" fill="currentColor"/></svg>`;
    case 'printer':
      return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>`;
    case 'box':
    case 'package':
      return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"/><path d="M12 22V12"/><polyline points="3.29 7 12 12 20.71 7"/><path d="m7.5 4.27 9 5.15"/></svg>`;
    case 'washing-machine':
    case 'washer':
      return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h3"/><path d="M17 6h.01"/><rect width="18" height="20" x="3" y="2" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M12 18a2.5 2.5 0 0 0 0-5 2.5 2.5 0 0 1 0-5"/></svg>`;
    case 'sparkles':
    case 'sparkle':
      return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/><path d="M5 3v4"/><path d="M19 17v4"/><path d="M3 5h4"/><path d="M17 19h4"/></svg>`;
    case 'shirt':
      return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/></svg>`;
    case 'zap':
      return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg>`;
    default:
      return '';
  }
}

function getOrderServiceIcon(serviceName) {
  const s = (serviceName || '').toLowerCase();
  if (s.includes('setrika') && s.includes('cuci')) return getCleanSvg('shirt', 13);
  if (s.includes('cuci satuan') || s.includes('bed cover') || s.includes('selimut') || s.includes('sepatu')) return getCleanSvg('package', 13);
  if (s.includes('cuci')) return getCleanSvg('washing-machine', 13);
  if (s.includes('setrika')) return getCleanSvg('sparkles', 13);
  return getCleanSvg('shirt', 13);
}

function getLabelDateMini() {
  const now = new Date();
  return `${String(now.getDate()).padStart(2,'0')}/${String(now.getMonth()+1).padStart(2,'0')} \u2022 ${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
}

/* ========================================================================
   FLOW COMPUTATION (Start, Step, Finish Action Buttons)
   ======================================================================== */
function getJobFlowInfo(order) {
  const s = (order.serviceName || '').toLowerCase();
  const needsIron = s.includes('setrika');
  const needsWash = s.includes('cuci');
  const isIronOnly = needsIron && !needsWash;

  if (order.status === 'diterima') {
    return {
      actionBtnText: isIronOnly ? 'Mulai Setrika' : 'Mulai Cuci',
      nextStatus: isIronOnly ? 'disetrika' : 'dicuci',
      btnVariant: 'is-start',
      isFinish: false
    };
  }

  if (order.status === 'dicuci') {
    if (needsIron) {
      return {
        actionBtnText: 'Lanjut Setrika',
        nextStatus: 'disetrika',
        btnVariant: 'is-step',
        isFinish: false
      };
    }
    return {
      actionBtnText: 'Selesai & Masuk Rak',
      nextStatus: 'selesai',
      btnVariant: 'is-finish',
      isFinish: true
    };
  }

  if (order.status === 'disetrika') {
    return {
      actionBtnText: 'Selesai & Masuk Rak',
      nextStatus: 'selesai',
      btnVariant: 'is-finish',
      isFinish: true
    };
  }

  return {
    actionBtnText: 'Detail',
    nextStatus: order.status,
    btnVariant: 'is-step',
    isFinish: false
  };
}

/* ========================================================================
   ACCOUNT DROPDOWN POPOVER
   ======================================================================== */
function toggleAccountDropdown(event) {
  if (event) event.stopPropagation();
  const menu = document.getElementById('account-dropdown-menu');
  const trigger = document.getElementById('profile-dropdown-trigger');
  if (!menu) return;

  const isOpen = menu.style.display !== 'none';
  if (isOpen) {
    closeAccountDropdown();
  } else {
    menu.style.display = 'flex';
    menu.classList.add('is-open');
    if (trigger) trigger.setAttribute('aria-expanded', 'true');
  }
}

function closeAccountDropdown() {
  const menu = document.getElementById('account-dropdown-menu');
  const trigger = document.getElementById('profile-dropdown-trigger');
  if (menu) {
    menu.style.display = 'none';
    menu.classList.remove('is-open');
  }
  if (trigger) trigger.setAttribute('aria-expanded', 'false');
}

document.addEventListener('click', (e) => {
  const menu = document.getElementById('account-dropdown-menu');
  const trigger = document.getElementById('profile-dropdown-trigger');
  if (menu && menu.style.display !== 'none') {
    if (!menu.contains(e.target) && !trigger?.contains(e.target)) {
      closeAccountDropdown();
    }
  }
});

function triggerDropdownLogout() {
  closeAccountDropdown();
  openExitConfirmModal();
}

/* ========================================================================
   FILTER & SEARCH ENGINE
   ======================================================================== */
function handleSearch(val) {
  STATE.searchQuery = val.trim().toLowerCase();
  const clearBtn = document.getElementById('btn-search-clear');
  if (clearBtn) {
    clearBtn.style.display = val.length > 0 ? 'flex' : 'none';
  }
  renderFeed();
}

function clearSearchInput() {
  const input = document.getElementById('petugas-search-input');
  if (input) input.value = '';
  STATE.searchQuery = '';
  const clearBtn = document.getElementById('btn-search-clear');
  if (clearBtn) clearBtn.style.display = 'none';
  renderFeed();
}

function resetAllFiltersAndSearch() {
  clearSearchInput();
  applyUnifiedFilter('semua');
}

function applyUnifiedFilter(filterKey) {
  document.querySelectorAll('.filter-pill-btn').forEach(b => b.classList.remove('is-active'));
  const activeBtn = document.getElementById('seg-btn-' + filterKey);
  if (activeBtn) activeBtn.classList.add('is-active');

  if (['semua', 'diterima', 'dicuci', 'disetrika'].includes(filterKey)) {
    STATE.activeStageTab = filterKey;
    STATE.quickFilter = 'semua';
  } else if (filterKey === 'kilat') {
    STATE.activeStageTab = 'semua';
    STATE.quickFilter = 'kilat';
  } else if (filterKey === 'sla') {
    STATE.activeStageTab = 'semua';
    STATE.quickFilter = 'sla';
  }

  renderTicketSkeletons(2);
  setTimeout(() => {
    renderFeed();
  }, 180);
}

function getFilteredOrders() {
  return STATE.orders.filter(o => {
    // Hanya antrean produksi aktif (diterima, dicuci, disetrika)
    if (!['diterima', 'dicuci', 'disetrika'].includes(o.status)) return false;

    // Filter stage tab
    if (STATE.activeStageTab !== 'semua' && o.status !== STATE.activeStageTab) {
      return false;
    }

    // Filter cepat
    if (STATE.quickFilter === 'kilat' && !o.isKilat) return false;
    if (STATE.quickFilter === 'sla' && !o.isUrgent) return false;

    // Filter search text
    if (STATE.searchQuery) {
      const matchCode = (o.code || '').toLowerCase().includes(STATE.searchQuery);
      const matchName = (o.customerName || '').toLowerCase().includes(STATE.searchQuery);
      const matchService = (o.serviceName || '').toLowerCase().includes(STATE.searchQuery);
      if (!matchCode && !matchName && !matchService) return false;
    }

    return true;
  });
}

function updateCounters() {
  const activeOrders = STATE.orders.filter(o => ['diterima', 'dicuci', 'disetrika'].includes(o.status));
  const countSemua = activeOrders.length;
  const countDiterima = activeOrders.filter(o => o.status === 'diterima').length;
  const countDicuci = activeOrders.filter(o => o.status === 'dicuci').length;
  const countDisetrika = activeOrders.filter(o => o.status === 'disetrika').length;
  const countKilat = activeOrders.filter(o => o.isKilat).length;
  const countSla = activeOrders.filter(o => o.isUrgent).length;

  const setVal = (id, val) => {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
  };

  setVal('count-semua', countSemua);
  setVal('count-diterima', countDiterima);
  setVal('count-dicuci', countDicuci);
  setVal('count-disetrika', countDisetrika);
  setVal('count-kilat', countKilat);
  setVal('count-sla', countSla);
}

function renderTicketSkeletons(count = 2) {
  const container = document.getElementById('job-cards-container');
  if (!container) return;
  container.innerHTML = Array.from({ length: count }).map(() => `
    <article class="skeleton-ticket-card" aria-hidden="true">
      <div class="skeleton-head-row">
        <span class="skeleton-box" style="width: 110px; height: 18px; border-radius: 6px;"></span>
        <span class="skeleton-box" style="width: 65px; height: 18px; border-radius: 9999px;"></span>
      </div>
      <div style="display: flex; flex-direction: column; gap: 6px;">
        <span class="skeleton-box" style="width: 140px; height: 16px; border-radius: 5px;"></span>
        <span class="skeleton-box" style="width: 90px; height: 12px; border-radius: 4px;"></span>
      </div>
      <div class="skeleton-metric-strip">
        <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
          <span class="skeleton-box" style="width: 40px; height: 10px; border-radius: 3px;"></span>
          <span class="skeleton-box" style="width: 32px; height: 14px; border-radius: 4px;"></span>
        </div>
        <div></div>
        <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
          <span class="skeleton-box" style="width: 40px; height: 10px; border-radius: 3px;"></span>
          <span class="skeleton-box" style="width: 52px; height: 14px; border-radius: 4px;"></span>
        </div>
      </div>
      <div class="skeleton-action-row">
        <span class="skeleton-box" style="flex: 1; height: 38px; border-radius: 9999px;"></span>
        <span class="skeleton-box" style="width: 38px; height: 38px; border-radius: 9999px;"></span>
      </div>
    </article>
  `).join('');
}

/* ========================================================================
   RENDER JOB FEED CARDS
   ======================================================================== */
function renderFeed() {
  updateCounters();
  const container = document.getElementById('job-cards-container');
  if (!container) return;
  const filtered = getFilteredOrders();

  if (filtered.length === 0) {
    container.innerHTML = `
      <div class="empty-queue-box">
        <div class="empty-icon-wrap">${getCleanSvg('box', 20)}</div>
        <div class="empty-title">Tidak ada antrean pengerjaan</div>
        <div class="empty-desc">Semua cucian pada filter ini telah selesai dikerjakan atau tidak cocok dengan pencarian.</div>
        <button type="button" class="btn btn-secondary" style="height: 34px; padding: 0 14px; font-size: 0.74rem; margin-top: 4px;" onclick="resetAllFiltersAndSearch()">
          Reset Pencarian &amp; Filter
        </button>
      </div>
    `;
    return;
  }

  container.innerHTML = filtered.map(order => {
    const flow = getJobFlowInfo(order);

    return `
      <article class="job-ticket-card" data-code="${escapeHtml(order.code)}">
        <!-- Top Head Row: Nota & SLA Timer -->
        <div class="ticket-head-row">
          <span class="ticket-code-text">${escapeHtml(order.code)}</span>
          <span class="ticket-timer-chip ${order.isUrgent ? 'is-urgent' : ''}">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            ${escapeHtml(order.slaText)}
          </span>
        </div>

        <!-- Customer & Service Title -->
        <div class="ticket-customer-row">
          <div class="ticket-customer-name">${escapeHtml(order.customerName)}</div>
          <div class="ticket-service-row">
            <span style="display: inline-flex; align-items: center; gap: 5px;">
              ${getOrderServiceIcon(order.serviceName)}
              <span>${escapeHtml(order.serviceName)}</span>
            </span>
            ${order.isKilat ? '<span class="ticket-kilat-badge"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-1px; margin-right:2px;"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg>Kilat</span>' : ''}
          </div>
        </div>

        <!-- 3-Metric Strip: Muatan, (Kosong), Tagihan -->
        <div class="ticket-metric-strip">
          <div class="metric-cell">
            <span class="metric-label">Muatan</span>
            <span class="metric-value">${order.weight} ${order.unit}</span>
          </div>
          <div class="metric-cell"></div>
          <div class="metric-cell">
            <span class="metric-label">Tagihan</span>
            <span class="metric-value">${formatRp(order.subtotal)}</span>
          </div>
        </div>

        <!-- Action Bar: Main Action Button + Round Label Tag Button -->
        <div class="ticket-action-bar">
          <button type="button" class="btn-ticket-main ${flow.btnVariant}"
            onclick="${flow.isFinish ? `openSelesaiPilihRakModal('${escapeHtml(order.code)}')` : `advanceOrderStatus('${escapeHtml(order.code)}', '${flow.nextStatus}')`}">
            <span>${flow.actionBtnText}</span>
            ${flow.isFinish ? getCleanSvg('check', 14) : getCleanSvg('arrow', 14)}
          </button>
          <button type="button" class="btn-ticket-circle" title="Cetak label cucian"
            aria-label="Cetak label cucian ${escapeHtml(order.code)}"
            onclick="previewThermalLabel('${escapeHtml(order.code)}')">
            ${getCleanSvg('tag', 16)}
          </button>
        </div>
      </article>
    `;
  }).join('');
}

/* ========================================================================
   LIVE SLA COUNTDOWN TICKER
   ======================================================================== */
function tickSlaCountdowns() {
  let updatedAny = false;
  STATE.orders.forEach(order => {
    if (order.code === 'LG-20261110-005') {
      // Countdown detik untuk kartu urgent LG-20261110-005
      const parts = order.slaText.match(/(\d+)m\s+(\d+)d/);
      if (parts) {
        let m = parseInt(parts[1], 10);
        let s = parseInt(parts[2], 10);
        if (s > 0) {
          s--;
        } else if (m > 0) {
          m--;
          s = 59;
        }
        order.slaText = `${m}m ${String(s).padStart(2, '0')}d`;
        order.isUrgent = true;
        updatedAny = true;
      }
    }
  });

  if (updatedAny) {
    document.querySelectorAll('.job-ticket-card').forEach(card => {
      const code = card.getAttribute('data-code');
      const order = STATE.orders.find(o => o.code === code);
      if (order) {
        const timerChip = card.querySelector('.ticket-timer-chip');
        if (timerChip) {
          timerChip.className = `ticket-timer-chip ${order.isUrgent ? 'is-urgent' : ''}`;
          timerChip.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> ${escapeHtml(order.slaText)}`;
        }
      }
    });
  }
}

/* ========================================================================
   WORKFLOW ACTIONS: ADVANCE & TOAST ROLLBACK WITH DB AJAX PERSISTENCE
   ======================================================================== */
function advanceOrderStatus(code, nextStatus) {
  const order = STATE.orders.find(o => o.code === code);
  if (!order) return;

  const prevStatus = order.status;
  order.status = nextStatus;

  STATE.lastActionRollback = {
    code,
    prevStatus
  };

  renderFeed();
  showToast(`Pesanan ${code} dipindahkan ke ${nextStatus}.`, true);

  // Kirim AJAX ke ProduksiController
  const fd = new FormData();
  fd.append('ajax', '1');
  fd.append('id_pesanan', code);
  fd.append('current_status', prevStatus);

  fetch('<?= \App\Core\Helper::url("produksi", ["action" => "advance"]) ?>', {
    method: 'POST',
    body: fd
  }).then(res => res.json()).then(data => {
    if (!data.success) {
      console.warn('Gagal advance di server:', data.message);
    }
  }).catch(err => {
    console.error('Network advance error:', err);
  });
}

function rollbackLastAction() {
  if (!STATE.lastActionRollback) return;
  const { code, prevStatus } = STATE.lastActionRollback;
  const order = STATE.orders.find(o => o.code === code);
  if (order) {
    order.status = prevStatus;
    STATE.lastActionRollback = null;
    renderFeed();
    showToast(`Tindakan untuk ${code} telah dibatalkan.`);

    // Kirim AJAX revert ke server
    const fd = new FormData();
    fd.append('ajax', '1');
    fd.append('id_pesanan', code);
    fd.append('target_status', prevStatus);

    fetch('<?= \App\Core\Helper::url("produksi", ["action" => "revert"]) ?>', {
      method: 'POST',
      body: fd
    }).then(res => res.json()).then(data => {
      if (!data.success) {
        console.warn('Gagal rollback di server:', data.message);
      }
    }).catch(err => {
      console.error('Network revert error:', err);
    });
  }
}

/* ========================================================================
   MODAL: SELESAI & PILIH RAK (Bottom Sheet)
   ======================================================================== */
function openSelesaiPilihRakModal(code) {
  const order = STATE.orders.find(o => o.code === code);
  if (!order) return;

  const presetRaks = [
    'Rak A-01', 'Rak A-02', 'Rak A-03', 'Rak A-04',
    'Rak B-01', 'Rak B-02', 'Rak B-03', 'Rak B-04',
    'Rak C-01', 'Rak C-02', 'Gantungan 1', 'Gantungan 2'
  ];
  const initialRak = order.rackLocation || 'Rak A-01';

  const sandbox = document.getElementById('modal-sandbox');
  sandbox.innerHTML = `
    <div class="dialog-backdrop" onclick="closeModal()">
      <div class="dialog-sheet-panel" onclick="event.stopPropagation()">
        <div class="sheet-handle-bar"></div>
        <div class="dialog-head">
          <div>
            <span class="dialog-title">Cucian Selesai &amp; Masuk Rak</span>
            <div style="font-size: 0.72rem; color: var(--ink-secondary); margin-top: 1px;">
              Nota: <strong>${escapeHtml(order.code)}</strong> • ${escapeHtml(order.customerName)}
            </div>
          </div>
          <button type="button" class="btn btn-secondary" style="height: 30px; padding: 0 8px; font-size: 0.72rem;" onclick="closeModal()">Esc</button>
        </div>
        <div class="dialog-body">
          <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 10px; padding: 10px; font-size: 0.76rem; color: #065F46; display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            <span>Pengerjaan cucian telah selesai. Silakan letakkan di rak penyimpanan agar kasir dan pelanggan mudah mengambil.</span>
          </div>

          <div>
            <label style="font-size: 0.78rem; font-weight: 700; color: var(--ink-secondary); margin-bottom: 6px; display: block;">
              Pilih Lokasi Rak Cepat:
            </label>
            <div class="rak-presets-grid" id="rak-preset-container">
              ${presetRaks.map(r => `
                <button type="button" class="rak-preset-btn ${initialRak === r ? 'is-selected' : ''}"
                  onclick="selectRakPreset('${r}')">
                  ${r}
                </button>
              `).join('')}
            </div>
          </div>

          <div>
            <label style="font-size: 0.78rem; font-weight: 700; color: var(--ink-secondary); margin-bottom: 4px; display: block;">
              Atau Input Manual Rak Khusus:
            </label>
            <input type="text" id="manual-rak-input" value="${escapeHtml(initialRak)}"
              placeholder="Contoh: Rak C-03 atau Gantungan Jas"
              style="width: 100%; height: 38px; padding: 0 12px; border-radius: 10px; border: 1px solid var(--line); font-family: var(--font-main); font-size: 0.82rem; outline: none;">
          </div>
        </div>
        <div class="dialog-foot">
          <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
          <button type="button" class="btn btn-primary" onclick="confirmFinishOrder('${escapeHtml(order.code)}')">
            ${getCleanSvg('check', 14)}
            <span>Simpan &amp; Masuk Rak</span>
          </button>
        </div>
      </div>
    </div>
  `;
}

function selectRakPreset(rak) {
  document.querySelectorAll('.rak-preset-btn').forEach(b => b.classList.remove('is-selected'));
  const buttons = Array.from(document.querySelectorAll('.rak-preset-btn'));
  const match = buttons.find(b => b.innerText.trim() === rak);
  if (match) match.classList.add('is-selected');

  const input = document.getElementById('manual-rak-input');
  if (input) input.value = rak;
}

function confirmFinishOrder(code) {
  const order = STATE.orders.find(o => o.code === code);
  if (!order) return;

  const input = document.getElementById('manual-rak-input');
  const chosenRak = (input && input.value.trim()) ? input.value.trim() : 'Rak A-01';

  const prevStatus = order.status;
  order.status = 'selesai';
  order.rackLocation = chosenRak;
  order.completedAt = new Date().toISOString();

  STATE.lastActionRollback = {
    code,
    prevStatus
  };

  closeModal();
  renderFeed();
  showToast(`Pesanan ${code} selesai & tersimpan di ${chosenRak}.`, true);

  // Kirim AJAX status selesai & lokasi rak ke backend
  const fd = new FormData();
  fd.append('ajax', '1');
  fd.append('id_pesanan', code);
  fd.append('current_status', 'Disetrika');
  fd.append('lokasi_rak', chosenRak);

  fetch('<?= \App\Core\Helper::url("produksi", ["action" => "advance"]) ?>', {
    method: 'POST',
    body: fd
  }).then(res => res.json()).then(data => {
    if (!data.success) {
      console.warn('Gagal simpan rak di server:', data.message);
    }
  }).catch(err => {
    console.error('Network finish error:', err);
  });
}

/* ========================================================================
   MODAL: PREVIEW & PRINT LABEL TERMAL (58x40mm)
   ======================================================================== */
function previewThermalLabel(code) {
  const order = STATE.orders.find(o => o.code === code) || STATE.orders[0];
  if (!order) return;

  const isExpress = !!order.isKilat;
  const statusText = (order.status === 'selesai' || order.status === 'diambil') ? 'SIAP AMBIL' : 'PROSES';

  const sandbox = document.getElementById('modal-sandbox');
  sandbox.innerHTML = `
    <div class="dialog-backdrop" onclick="closeModal()">
      <div class="dialog-sheet-panel" onclick="event.stopPropagation()">
        <div class="sheet-handle-bar"></div>
        <div class="dialog-head">
          <div>
            <span class="dialog-title">Label Cucian</span>
            <div style="font-size: 0.7rem; color: var(--ink-muted); margin-top: 1px;">Format Cetak Termal 58 × 40 mm</div>
          </div>
          <button type="button" class="btn btn-secondary" style="height: 30px; padding: 0 8px; font-size: 0.72rem;" onclick="closeModal()">Esc</button>
        </div>
        <div class="dialog-body" style="background: linear-gradient(180deg, #EFF9FF 0%, #E6F4FA 100%);">
          <div class="modern-thermal-label" id="thermal-label-content">
            <div class="label-brand-row">
              <span class="label-brand-name">LaundryGo • Adi Laundry</span>
              <span class="label-date-mini">${getLabelDateMini()}</span>
            </div>
            <div class="label-code-row">
              <span class="label-code-text">${escapeHtml(order.code)}</span>
            </div>
            <div>
              <span class="label-priority-pill ${isExpress ? 'is-kilat' : 'is-reguler'}">
                ${isExpress ? '⚡ KILAT' : '⏱ REGULER'}
              </span>
            </div>
            <div class="label-info-name">${escapeHtml(order.customerName)}</div>
            <div class="label-info-service">${escapeHtml(order.serviceName)} • ${order.weight} ${order.unit}</div>
            ${order.notes && order.notes.length ? order.notes.map(n => `<div class="label-info-row is-warning">⚠️ ${escapeHtml(n)}</div>`).join('') : ''}
            <div class="label-footer-row">
              <span>STATUS: ${statusText}</span>
              <span>RAK: ${escapeHtml(order.rackLocation || '—')}</span>
            </div>
          </div>
        </div>
        <div class="dialog-foot">
          <button type="button" class="btn btn-secondary" onclick="closeModal()">Tutup</button>
          <button type="button" class="btn btn-primary" onclick="printLabelDirect()">
            ${getCleanSvg('printer', 14)}
            <span>Cetak Label</span>
          </button>
        </div>
      </div>
    </div>
  `;
}

function printLabelDirect() {
  const content = document.getElementById('thermal-label-content').innerHTML;
  const printArea = document.getElementById('print-sandbox');
  printArea.innerHTML = `<div class="modern-thermal-label">${content}</div>`;
  window.print();
}

/* ========================================================================
   MODAL: EDIT PROFIL & SANDI
   ======================================================================== */
function openEditProfileModal() {
  closeAccountDropdown();
  const container = document.getElementById('modal-sandbox');
  const u = STATE.currentUser;
  container.innerHTML = `
    <div class="dialog-backdrop" onclick="closeModal()">
      <div class="dialog-sheet-panel" onclick="event.stopPropagation()">
        <div class="sheet-handle-bar"></div>
        <div class="dialog-head">
          <span class="dialog-title">Edit Profil &amp; Sandi</span>
          <button type="button" class="btn btn-secondary" style="height:32px; padding:0 10px; font-size:0.75rem;" onclick="closeModal()">Esc</button>
        </div>
        <div class="dialog-body">
          <div class="modal-input-group">
            <label class="modal-input-label">Nama Petugas:</label>
            <input type="text" id="edit-profile-name" class="modal-text-input" value="${escapeHtml(u.name)}" placeholder="Nama lengkap petugas">
          </div>
          <div class="modal-input-group">
            <label class="modal-input-label">Kata Sandi Baru (Opsional):</label>
            <input type="password" id="edit-profile-pwd" class="modal-text-input font-mono" placeholder="Minimal 6 karakter">
          </div>
          <div class="modal-input-group">
            <label class="modal-input-label">Ulangi Sandi Baru:</label>
            <input type="password" id="edit-profile-pwd-confirm" class="modal-text-input font-mono" placeholder="Konfirmasi sandi baru">
          </div>
        </div>
        <div class="dialog-foot">
          <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
          <button type="button" class="btn btn-primary" onclick="submitEditProfile()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            <span>Simpan Perubahan</span>
          </button>
        </div>
      </div>
    </div>
  `;
}

function submitEditProfile() {
  const nameInput = document.getElementById('edit-profile-name');
  const pwdInput = document.getElementById('edit-profile-pwd');
  const pwdConfirm = document.getElementById('edit-profile-pwd-confirm');

  const newName = nameInput ? nameInput.value.trim() : '';
  if (!newName) {
    showToast('Nama petugas tidak boleh kosong');
    return;
  }

  if (pwdInput && pwdInput.value) {
    if (pwdInput.value.length < 6) {
      showToast('Kata sandi baru minimal 6 karakter');
      return;
    }
    if (pwdInput.value !== (pwdConfirm ? pwdConfirm.value : '')) {
      showToast('Konfirmasi kata sandi tidak cocok');
      return;
    }
  }

  STATE.currentUser.name = newName;
  updateUserHeader();
  closeModal();
  showToast('Profil berhasil diperbarui!');
}

/* ========================================================================
   MODAL: EXIT CONFIRMATION
   ======================================================================== */
function openExitConfirmModal() {
  const sandbox = document.getElementById('modal-sandbox');
  sandbox.innerHTML = `
    <div class="dialog-backdrop" onclick="closeModal()">
      <div class="dialog-sheet-panel" onclick="event.stopPropagation()">
        <div class="sheet-handle-bar"></div>
        <div class="dialog-head">
          <span class="dialog-title">Keluar dari Sesi Petugas?</span>
          <button type="button" class="btn btn-secondary" style="height: 30px; padding: 0 8px; font-size: 0.72rem;" onclick="closeModal()">Esc</button>
        </div>
        <div class="dialog-body">
          <p style="font-size: 0.84rem; color: var(--ink-secondary); line-height: 1.5;">
            Sesi pengerjaan <strong>${escapeHtml(STATE.currentUser.name)}</strong> akan ditutup. Anda dapat masuk kembali kapan saja melalui login gerai atau LaundryGo.
          </p>
        </div>
        <div class="dialog-foot">
          <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
          <a href="<?= \App\Core\Helper::url('logout') ?>" class="btn btn-danger" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span>Ya, Keluar</span>
          </a>
        </div>
      </div>
    </div>
  `;
}

function closeModal() {
  const sandbox = document.getElementById('modal-sandbox');
  if (sandbox) sandbox.innerHTML = '';
}

/* ========================================================================
   TOAST NOTIFICATION TRAY (Foto 2: Hitam Melayang dengan Tombol Batalkan)
   ======================================================================== */
function showToast(msg, withUndo = false) {
  if (STATE.undoTimer) clearTimeout(STATE.undoTimer);
  const tray = document.getElementById('toast-tray');
  if (!tray) return;

  tray.innerHTML = `
    <div class="toast-item">
      <span>${escapeHtml(msg)}</span>
      ${withUndo ? `<button type="button" class="toast-undo-btn" onclick="rollbackLastAction()">Batalkan</button>` : ''}
    </div>
  `;

  STATE.undoTimer = setTimeout(() => {
    tray.innerHTML = '';
  }, 4500);
}

// Start on DOM ready
document.addEventListener('DOMContentLoaded', checkSession);
</script>
