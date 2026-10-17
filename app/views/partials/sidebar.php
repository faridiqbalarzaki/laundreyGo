<?php
/**
 * SIDEBAR dinamis per role (dari $_SESSION). Markup & class identik dengan index.html asli.
 * Variabel: $activeMenu (id menu aktif)
 */
$user   = auth_user();
$role   = $user['role'] ?? '';
$groups = menu_for_role($role);
$counts = nav_counts();
?>
<aside class="sidebar" aria-label="Menu Utama">
  <div class="brand">
    <div class="brand-logo">LG</div>
    <div class="brand-text">
      <div class="brand-title">LaundryGo</div>
      <div class="brand-sub">Adi Laundry, Karangploso</div>
    </div>
  </div>

  <nav id="sidebar-nav" class="sidebar-nav">
    <?php foreach ($groups as $group): ?>
      <div class="nav-group">
        <div class="nav-label"><?= e($group['label']) ?></div>
        <?php foreach ($group['items'] as $item): ?>
          <?php $isActive = ($item['id'] === ($activeMenu ?? '')); ?>
          <a class="nav-item<?= $isActive ? ' active' : '' ?>"
             href="<?= e(url($item['path'])) ?>"
             <?= $isActive ? 'aria-current="page"' : '' ?>>
            <span class="nav-item-left">
              <?= icon($item['icon']) /* SVG statis dari icons.php */ ?>
              <span class="nav-item-text"><?= e($item['label']) ?></span>
            </span>
            <?php if (!empty($item['badge'])): ?>
              <span class="nav-badge" data-badge="<?= e($item['badge']) ?>"><?= (int) ($counts[$item['badge']] ?? 0) ?></span>
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </nav>

  <a id="sidebar-user" class="sidebar-user" href="<?= e(url('/profil')) ?>" title="Profil & password">
    <div class="avatar"><?= e(initials($user['name'] ?? '')) ?></div>
    <div class="sidebar-user-text">
      <div style="font-size:12.5px; font-weight:700;"><?= e($user['name'] ?? '') ?></div>
      <div style="font-size:11px; color:var(--teks-muted);"><?= e($user['subtitle'] ?? '') ?></div>
    </div>
  </a>
</aside>
