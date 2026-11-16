<section id="login-screen" class="login-backdrop" aria-label="Halaman Masuk LaundryGo">
    <div class="login-split-card">
      <!-- KIRI: Panggung Animasi Laundry (2-Scene Morphing) -->
      <div class="login-stage" aria-hidden="true">
        <div class="login-stage-grid-bg"></div>

        <!-- SCENE 1: Masuk Akun Staf (Laptop Kasir + Dua Kartu Minimal LaundryGo) -->
        <div id="login-scene-signin" class="login-scene is-active">
          <div class="stage-composition">
            <!-- Kartu Melayang Kiri-Atas: Nota Lunas -->
            <div class="float-widget float-card-lunas">
              <div class="float-card-lunas-head">
                <span class="float-card-lunas-icon">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/></svg>
                </span>
                <span class="float-card-lunas-title">Nota Lunas</span>
              </div>
              <div class="float-card-lunas-sub num-tabular">LG-261114-001<br>Tunai  •  Rp 28.000</div>
              <span class="float-card-lunas-pill">Siap Diambil</span>
            </div>

            <!-- Mockup Laptop Kasir LaundryGo -->
            <div class="laptop-mockup">
              <div class="laptop-lid">
                <div class="laptop-camera"></div>
                <div class="laptop-screen">
                  <div class="laptop-screen-top">
                    <span class="mini-brand">
                      <span class="mini-brand-dot"><img src="<?= \App\Core\Helper::asset('img/logo_icon_drop.png') ?>" alt="LG"></span>
                      <span>LaundryGo Kasir</span>
                    </span>
                    <span class="mini-screen-pill">Adi Laundry</span>
                  </div>
                  <div class="mini-screen-grid">
                    <div class="mini-nav-col">
                      <div class="mini-nav-item active">Dashboard</div>
                      <div class="mini-nav-item">Pesanan</div>
                      <div class="mini-nav-item">Pelanggan</div>
                      <div class="mini-nav-item">Laporan</div>
                    </div>
                    <div class="mini-rows-col">
                      <div class="mini-order-row">
                        <span class="num-tabular">LG-261114-001  •  Amanda</span>
                        <span class="mini-status-chip">Selesai</span>
                      </div>
                      <div class="mini-order-row">
                        <span class="num-tabular">LG-261115-001  •  Citra</span>
                        <span class="mini-status-chip">Lunas</span>
                      </div>
                      <div class="mini-order-row">
                        <span class="num-tabular">LG-261115-002  •  Rizky</span>
                        <span class="mini-status-chip is-wash">Rak A-01</span>
                      </div>
                      <div class="mini-order-row">
                        <span class="num-tabular">Omset: Rp 1.840.000</span>
                        <span class="mini-status-chip">+14,2%</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="laptop-base"><div class="laptop-notch"></div></div>
            </div>

            <!-- Kartu Melayang Bawah-Kanan: Info Rak & Customer -->
            <div class="float-widget float-card-rak">
              <div style="display: flex; align-items: center; gap: 8px;">
                <span style="width: 26px; height: 26px; border-radius: 8px; background: var(--primary-light); color: var(--primary-hover); display: inline-flex; align-items: center; justify-content: center;">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </span>
                <div style="text-align: left;">
                  <div style="font-size: 0.72rem; font-weight: 700; color: var(--ink);">Rak A-01  •  Siap</div>
                  <div style="font-size: 0.64rem; color: var(--ink-muted);">Amanda Putri  •  Hari ini</div>
                </div>
              </div>
              <div style="padding-left: 10px; border-left: 1px solid var(--line); text-align: right;">
                <div style="font-size: 0.62rem; color: var(--ink-muted);">Status</div>
                <div style="font-size: 0.7rem; font-weight: 700; color: var(--success, #16a34a);">Lunas</div>
              </div>
            </div>
          </div>

          <div style="text-align: center; margin-top: 0.35rem;">
            <h2 class="monitor-caption-title">Cucian rapi, catatan rapi.</h2>
            <p class="monitor-caption-sub" style="margin: 0.45rem auto 0;">
              Satu tempat untuk pesanan, antrean cuci, pembayaran, dan laporan omset Adi Laundry.
            </p>
          </div>
        </div>

        <!-- SCENE 2: Lacak Status Nota Publik -->
        <div id="login-scene-demo" class="login-scene is-hidden">
          <div class="monitor-stage-wrap">
            <div class="monitor-composition">
              <!-- Monitor Pelacakan Status -->
              <div>
                <div class="monitor-frame">
                  <div class="laptop-screen" style="min-height: 186px; border-radius: 6px;">
                    <div class="laptop-screen-top">
                      <span class="mini-brand">
                        <span class="mini-brand-dot"><img src="<?= \App\Core\Helper::asset('img/logo_icon_drop.png') ?>" alt="LG"></span>
                        <span>Lacak Nota  •  Adi Laundry</span>
                      </span>
                      <span class="mini-screen-pill">Real-Time</span>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px;">
                      <div style="background: var(--primary-subtle); border: 1px solid var(--primary-border); border-radius: 8px; padding: 7px; text-align: left;">
                        <div style="font-size: 7px; color: var(--ink-muted); font-weight: 600;">Siap diambil</div>
                        <div style="font-size: 11px; font-weight: 800; color: var(--ink);" class="num-tabular">11 Pesanan</div>
                        <div style="font-size: 6.5px; color: var(--success, #16a34a); font-weight: 700;">Tersimpan rapi di rak</div>
                      </div>
                      <div style="background: #fff; border: 1px solid var(--line); border-radius: 8px; padding: 7px; text-align: left;">
                        <div style="font-size: 7px; color: var(--ink-muted); font-weight: 600;">Sedang diproses</div>
                        <div style="font-size: 11px; font-weight: 800; color: var(--ink);" class="num-tabular">4 Antrean</div>
                        <div style="font-size: 6.5px; color: var(--primary-hover); font-weight: 700;">Diterima &amp; dicuci</div>
                      </div>
                    </div>
                    <div class="mini-rows-col" style="margin-top:6px;">
                      <div class="mini-order-row"><span class="num-tabular">LG-261114-001  •  Amanda</span><span class="mini-status-chip">Siap Diambil</span></div>
                      <div class="mini-order-row"><span class="num-tabular">LG-261114-002  •  Dimas</span><span class="mini-status-chip is-wash">Dicuci</span></div>
                      <div class="mini-order-row"><span class="num-tabular">LG-261115-001  •  Citra</span><span class="mini-status-chip is-wash">Disetrika</span></div>
                    </div>
                  </div>
                </div>
                <div class="monitor-stand"></div>
                <div class="monitor-foot"></div>
              </div>
            </div>

            <div>
              <h2 class="monitor-caption-title">Lacak Status Nota</h2>
              <p class="monitor-caption-sub" style="margin: 0.45rem auto 0;">
                Pantau progres cucian dari diterima hingga siap diambil di rak Adi Laundry.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- KANAN: Panel Form Masuk & Cek Nota -->
      <div class="login-form-pane">
        <div class="login-pane-stack">
          <div class="login-brand-header">
            <img src="<?= \App\Core\Helper::asset('img/logo_horizontal_laundrygo.png') ?>" alt="LaundryGo" class="login-brand-logo-img">
            <div class="login-outlet-pill">
              <span class="login-outlet-dot"></span>
              <span>Adi Laundry, Karangploso</span>
            </div>
          </div>

          <!-- Mode A: Form Masuk -->
          <div id="auth-pane-signin" class="login-auth-pane">
            <div class="login-title-row">
              <h1 class="login-heading">Masuk</h1>
            </div>
            <p class="login-subheading">
              Masuk ke ruang kerja staf atau admin gerai Adi Laundry.
            </p>

            <form action="<?= \App\Core\Helper::url('login', ['action' => 'doLogin']) ?>" method="POST" id="login-form" class="login-inner-box">
              <div class="login-field-group">
                <label for="login-username-input" class="login-field-label">Username</label>
                <input type="text" id="login-username-input" name="username" class="form-control" autocomplete="username"
                  value="" placeholder="Masukkan username" required autofocus>
              </div>

              <div class="login-field-group">
                <label for="login-password-input" class="login-field-label">Password</label>
                <div class="login-pwd-wrap">
                  <input type="password" id="login-password-input" name="password" class="form-control" autocomplete="current-password"
                    value="" placeholder="Masukkan password" required>
                  <button type="button" id="login-pwd-eye-btn" class="login-pwd-toggle"
                    onclick="toggleLoginPasswordVisibility()" aria-label="Tampilkan atau sembunyikan password">
                    <span id="eye-icon-show" class="icon-swap-layer is-shown">
                      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="10" r="3"/></svg>
                    </span>
                    <span id="eye-icon-hide" class="icon-swap-layer is-hidden" style="display:none;">
                      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </span>
                  </button>
                </div>
              </div>

              <button type="submit" class="btn btn-primary" style="width: 100%; height: 42px; font-size: 0.88rem; margin-top: 2px;">
                Masuk
              </button>
            </form>

            <button type="button" id="btn-open-track-nota" class="login-track-callout" onclick="switchLoginScene('demo')">
              <span class="login-track-callout-left">
                <span class="login-track-callout-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <span>
                  <div class="login-track-callout-title">Cek Nota &amp; Transaksi</div>
                  <div class="login-track-callout-sub">Lacak status &amp; rak cucian tanpa login</div>
                </span>
              </span>
              <span class="login-track-callout-action">
                <span>Cek</span>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
              </span>
            </button>
          </div>

          <!-- Mode B: Cek Nota Publik -->
          <div id="auth-pane-demo" class="login-auth-pane" hidden>
            <div class="login-title-row">
              <h1 class="login-heading">Cek Nota &amp; Transaksi</h1>
            </div>
            <p class="login-subheading">
              Masukkan nomor nota untuk melihat status pengerjaan dan lokasi rak cucian Anda.
            </p>

            <div class="login-inner-box">
              <div class="login-field-group">
                <label for="quick-track-code" class="login-field-label">Nomor Nota / Invoice</label>
                <div class="track-search-row">
                  <input type="text" id="quick-track-code" class="form-control num-tabular"
                    value="" placeholder="Contoh: LG-261114-001" autocomplete="off" spellcheck="false"
                    oninput="handleTrackInputMask(this)"
                    onkeydown="if(event.key==='Enter'){event.preventDefault();submitTrackSearch();}">
                  <button type="button" class="btn btn-primary" style="flex-shrink: 0; padding: 0 16px;" onclick="submitTrackSearch()">Cek Nota</button>
                </div>
                <div id="track-input-hint" style="font-size: 0.72rem; margin-top: 6px; color: var(--ink-muted); transition: color 0.2s ease;">
                  Format nota resmi: <strong style="color: var(--primary);">LG-YYMMDD-XXX</strong> (lihat pada struk fisik)
                </div>
              </div>
            </div>

            <button type="button" id="btn-back-to-signin" class="login-back-btn" onclick="switchLoginScene('signin')">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
              <span>Kembali ke Masuk Pegawai</span>
            </button>
          </div>
        </div>
      </div>
    </div>
</section>

<script>
let loginPwdVisible = false;

function switchLoginScene(scene) {
    const s1 = document.getElementById('login-scene-signin');
    const s2 = document.getElementById('login-scene-demo');
    const p1 = document.getElementById('auth-pane-signin');
    const p2 = document.getElementById('auth-pane-demo');

    if (scene === 'demo') {
        s1.classList.remove('is-active');
        s1.classList.add('is-hidden');
        s2.classList.remove('is-hidden');
        s2.classList.add('is-active');
        p1.hidden = true;
        p2.hidden = false;
        document.getElementById('quick-track-code').focus();
    } else {
        s2.classList.remove('is-active');
        s2.classList.add('is-hidden');
        s1.classList.remove('is-hidden');
        s1.classList.add('is-active');
        p2.hidden = true;
        p1.hidden = false;
        document.getElementById('login-username-input').focus();
    }
}

function toggleLoginPasswordVisibility() {
    loginPwdVisible = !loginPwdVisible;
    const pwdInput = document.getElementById('login-password-input');
    const showIcon = document.getElementById('eye-icon-show');
    const hideIcon = document.getElementById('eye-icon-hide');

    if (loginPwdVisible) {
        pwdInput.type = 'text';
        showIcon.style.display = 'none';
        hideIcon.style.display = 'inline-block';
    } else {
        pwdInput.type = 'password';
        showIcon.style.display = 'inline-block';
        hideIcon.style.display = 'none';
    }
}

function handleTrackInputMask(input) {
    // Sanitasi real-time: otomatis huruf besar dan hanya izinkan karakter A-Z, 0-9, dan dash (-)
    let val = input.value.toUpperCase().replace(/[^A-Z0-9\-]/g, '');
    input.value = val;

    const hint = document.getElementById('track-input-hint');
    if (val.length > 0 && !val.startsWith('LG-') && !val.startsWith('L')) {
        if (hint) {
            hint.innerHTML = `<span style="color: #f59e0b; font-weight: 600;">💡 Tip: Nomor nota kasir selalu diawali dengan <strong>LG-</strong></span>`;
        }
    } else if (hint) {
        hint.innerHTML = `Format nota resmi: <strong style="color: var(--primary);">LG-YYMMDD-XXX</strong> (lihat pada struk fisik)`;
    }
}

function submitTrackSearch() {
    const inputEl = document.getElementById('quick-track-code');
    const rawCode = inputEl.value.trim();
    const hint = document.getElementById('track-input-hint');

    if (!rawCode) {
        if (typeof showToast === 'function') {
            showToast('Ketik nomor nota fisik Anda terlebih dahulu!', 'warning', 'Nomor Nota Kosong');
        }
        if (hint) {
            hint.innerHTML = `<span style="color: #ef4444; font-weight: 600;">⚠️ Kolom nomor nota wajib diisi. Contoh: <strong>LG-261114-001</strong></span>`;
        }
        inputEl.focus();
        return;
    }

    const code = rawCode.toUpperCase();
    const notaRegex = /^LG-[0-9]{6,8}-[0-9]{3,4}$/;

    if (!notaRegex.test(code)) {
        if (typeof showToast === 'function') {
            showToast(`Format nota "${code}" tidak valid! Contoh yang benar: LG-261114-001`, 'error', 'Format Tidak Sesuai');
        }
        if (hint) {
            hint.innerHTML = `<span style="color: #ef4444; font-weight: 600;">⚠️ Format nota tidak sesuai standar. Contoh: <strong>LG-261114-001</strong></span>`;
        }
        inputEl.classList.add('is-invalid');
        inputEl.focus();
        return;
    }

    window.location.href = `index.php?page=tracking&nota=${encodeURIComponent(code)}`;
}
</script>
