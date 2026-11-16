<?php
$user = $user ?? \App\Core\Auth::user();
$nama = $user['nama_anggota'] ?? ($user['username'] ?? 'Pengguna');
$username = $user['username'] ?? 'user';
$isKasir = \App\Core\Auth::isKasir();
$roleTitle = \App\Core\Auth::isAdmin() ? 'Pemilik Gerai' : ($isKasir ? 'Kasir Meja Depan' : ($user['nama_role'] ?? 'Staf Operasional'));

$words = array_values(array_filter(explode(' ', trim($nama))));
if (count($words) >= 2) {
    $initials = strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
} else {
    $initials = strtoupper(mb_substr($words[0] ?? 'U', 0, min(2, mb_strlen($words[0] ?? 'U'))));
}
?>

<div class="page-sheet-container">
    <div class="profile-stack-shell">
        <div class="profile-header-block" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
            <div>
                <h2 class="profile-header-title">Profil &amp; Pengaturan Akun</h2>
                <span class="profile-header-sub">Informasi login pegawai dan keamanan kata sandi</span>
            </div>
            <?php include __DIR__ . '/../layouts/kasir_header_actions.php'; ?>
        </div>

        

        

        <!-- Kartu 1: Identitas Pegawai -->
        <div class="profile-panel-card">
            <div class="profile-identity-row">
                <span class="profile-avatar-xl"><?= htmlspecialchars($initials) ?></span>
                <div class="profile-identity-meta">
                    <span class="profile-identity-name"><?= htmlspecialchars($nama) ?></span>
                    <span class="profile-identity-sub" style="font-size: 0.82rem; color: var(--ink-muted);">
                        Username: <strong style="color: var(--ink-secondary);"><?= htmlspecialchars($username) ?></strong> &bull; Peran: <?= htmlspecialchars($roleTitle) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Kartu 2: Ubah Kata Sandi -->
        <div class="profile-panel-card">
            <span class="profile-panel-title" style="font-size: 1.05rem; font-weight: 700; color: var(--ink);">Ubah Kata Sandi</span>

            <form action="<?= \App\Core\Helper::url('profil', ['action' => 'update_password']) ?>" method="POST" id="profile-password-form">
                <div class="profile-form-group" style="margin-bottom: 14px;">
                    <label class="profile-form-label" for="profil-old-pwd" style="display: block; font-size: 0.82rem; font-weight: 600; color: var(--ink-secondary); margin-bottom: 6px;">Kata Sandi Lama:</label>
                    <input type="password" name="old_password" id="profil-old-pwd" class="form-control" placeholder="Masukkan kata sandi saat ini" autocomplete="current-password" required style="width: 100%; height: 42px; border-radius: 12px; padding: 0 14px; font-size: 0.84rem; border: 1px solid var(--line);">
                </div>

                <div class="profile-form-group" style="margin-bottom: 14px;">
                    <label class="profile-form-label" for="profil-new-pwd" style="display: block; font-size: 0.82rem; font-weight: 600; color: var(--ink-secondary); margin-bottom: 6px;">Kata Sandi Baru:</label>
                    <input type="password" name="new_password" id="profil-new-pwd" class="form-control" placeholder="Minimal 6 karakter" autocomplete="new-password" required minlength="6" style="width: 100%; height: 42px; border-radius: 12px; padding: 0 14px; font-size: 0.84rem; border: 1px solid var(--line);">
                </div>

                <div class="profile-form-group" style="margin-bottom: 18px;">
                    <label class="profile-form-label" for="profil-confirm-pwd" style="display: block; font-size: 0.82rem; font-weight: 600; color: var(--ink-secondary); margin-bottom: 6px;">Ulangi Kata Sandi Baru:</label>
                    <input type="password" name="confirm_password" id="profil-confirm-pwd" class="form-control" placeholder="Ketik ulang kata sandi baru" autocomplete="new-password" required minlength="6" style="width: 100%; height: 42px; border-radius: 12px; padding: 0 14px; font-size: 0.84rem; border: 1px solid var(--line);">
                </div>

                <div class="profile-form-footer" style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="height: 40px; padding: 0 22px; font-size: 0.84rem; font-weight: 700; border-radius: 12px; background: var(--primary); color: #fff; border: none; cursor: pointer; box-shadow: 0 4px 12px -2px rgba(2, 132, 199, 0.35);">
                        Simpan Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
