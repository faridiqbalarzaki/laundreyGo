<?php /** @var ?string $error @var string $old */ ?>
<div class="login-grid">
  <div class="login-hero">
    <div style="display:flex; align-items:center; gap:12px;">
      <div style="width:40px; height:40px; border-radius:12px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; font-weight:800;">LG</div>
      <span style="font-weight:700; font-size:17px;">LaundryGo</span>
    </div>
    <div>
      <h2 style="font-size:44px; font-weight:800; line-height:1.12; margin-bottom:14px;">Cucian rapi, catatan rapi.</h2>
      <p style="font-size:15px; opacity:0.9;">Satu tempat untuk pesanan, antrean cuci, pembayaran, dan laporan omset Adi Laundry.</p>
    </div>
    <div class="chip-row">
      <span class="badge" style="background:rgba(255,255,255,0.16); color:#fff;">Nota &amp; label satu kode LG-…</span>
      <span class="badge" style="background:rgba(255,255,255,0.16); color:#fff;">Rekap omset nyata</span>
    </div>
  </div>
  <div>
    <form class="login-card" method="post" action="<?= e(url('/login')) ?>" autocomplete="on">
      <?= csrf_field() ?>
      <span class="badge badge-dicuci" style="margin-bottom:12px;"><span class="badge-dot"></span>Sistem kasir v3.0</span>
      <h3 style="font-size:24px; font-weight:800; margin-bottom:4px;">Masuk</h3>
      <?php if (APP_DEBUG): ?>
        <p style="font-size:13px; color:var(--teks-muted); margin-bottom:18px;">Coba: <code>admin</code> (<code>admin123</code>), <code>siti</code> (<code>123456</code>), atau <code>budi</code> (<code>123456</code> - wajib ganti password).</p>
      <?php else: ?>
        <p style="font-size:13px; color:var(--teks-muted); margin-bottom:18px;">Masuk dengan akun Adi Laundry Anda.</p>
      <?php endif; ?>

      <?php if ($error): ?>
        <div id="login-error" role="alert" style="background:var(--bg-terlambat); color:var(--txt-terlambat); padding:10px 12px; border-radius:10px; font-size:12.5px; font-weight:700; margin-bottom:12px;"><?= e($error) ?></div>
      <?php endif; ?>

      <div style="margin-bottom:12px;">
        <label for="login-username" style="font-size:12px; font-weight:700; color:var(--teks-muted); display:block; margin-bottom:5px;">Username</label>
        <input type="text" id="login-username" name="username" class="input-field" value="<?= e($old) ?>" autocomplete="username" required autofocus />
      </div>
      <div style="margin-bottom:18px;">
        <label for="login-password" style="font-size:12px; font-weight:700; color:var(--teks-muted); display:block; margin-bottom:5px;">Password</label>
        <input type="password" id="login-password" name="password" class="input-field" placeholder="Masukkan password..." autocomplete="current-password" required />
      </div>
      <button type="submit" class="btn btn-primary w-full" id="btn-login-submit">Masuk</button>
    </form>
  </div>
</div>
