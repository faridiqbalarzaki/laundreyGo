<?php
$activeCount = 0;
foreach ($users as $u) {
    if ($u['status_aktif'] == 1) $activeCount++;
}
?>
<div class="page-sheet-container is-orders-page" style="display: flex; flex-direction: column; gap: 14px;">
    <!-- Inline Toolbar Terkunci -->
    <div class="orders-inline-toolbar" style="display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <span class="dash-card-title" style="font-size: 1rem; font-weight: 800; color: var(--ink);">Daftar Pengguna Aktif</span>
            <span class="semantic-pill is-success num-tabular" style="font-size: 0.72rem; padding: 2px 8px; border-radius: 9999px;">
                &bull; <?= $activeCount ?> aktif dari <?= count($users) ?> akun
            </span>
        </div>
        <div class="toolbar-end">
            <button type="button" class="btn btn-primary" onclick="openAddUserModal()" style="height: 36px; font-size: 0.8rem; padding: 0 14px; border-radius: 10px;">
                + Tambah Pengguna
            </button>
        </div>
    </div>

    <!-- Data Table Card Modern -->
    <div class="modern-table-card orders-table-card" style="background: var(--surface); border: 1px solid var(--line); border-radius: 16px; overflow: hidden; box-shadow: var(--shadow-border);">
        <div class="orders-table-scroll" style="overflow-x: auto;">
            <table class="modern-table" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.82rem;">
                <thead>
                    <tr style="background: var(--surface-muted); border-bottom: 1px solid var(--line); color: var(--ink-muted); font-size: 0.74rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;">
                        <th style="padding: 12px 16px;">Nama Pegawai</th>
                        <th style="padding: 12px 16px;">Username</th>
                        <th style="padding: 12px 16px;">Peran / Hak Akses</th>
                        <th style="padding: 12px 16px;">Status</th>
                        <th style="padding: 12px 16px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): 
                        $uName = $u['nama_anggota'];
                        $uWords = explode(' ', trim($uName));
                        $uInitials = strtoupper(substr($uWords[0] ?? 'U', 0, 1) . (isset($uWords[1]) ? substr($uWords[1], 0, 1) : substr($uWords[0] ?? 'S', 1, 1)));
                        $isActive = ($u['status_aktif'] == 1);
                    ?>
                        <tr style="border-bottom: 1px solid var(--line); transition: background 120ms;" onmouseover="this.style.background='var(--surface-subtle)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 12px 16px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem; flex-shrink: 0;">
                                        <?= $uInitials ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; color: var(--ink);"><?= htmlspecialchars($u['nama_anggota']) ?></div>
                                        <div style="font-size: 0.7rem; color: var(--ink-muted);">ID: <?= htmlspecialchars($u['id_anggota']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 12px 16px; font-weight: 600; color: var(--ink-secondary);">
                                <?= htmlspecialchars($u['username']) ?>
                            </td>
                            <td style="padding: 12px 16px;">
                                <span class="badge-status badge-diterima" style="font-size: 0.72rem; padding: 2px 8px; border-radius: 9999px;">
                                    <?= htmlspecialchars($u['nama_role']) ?>
                                </span>
                            </td>
                            <td style="padding: 12px 16px;">
                                <span class="badge-pay <?= $isActive ? 'lunas' : 'belum' ?>" style="font-size: 0.72rem; padding: 2px 8px; border-radius: 9999px;">
                                    <?= $isActive ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td style="padding: 12px 16px; text-align: right;">
                                <div style="display: inline-flex; align-items: center; gap: 6px;">
                                    <button type="button" class="btn btn-secondary" style="height: 28px; padding: 0 10px; font-size: 0.74rem;" 
                                            onclick="editUser(<?= htmlspecialchars(json_encode($u, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>)">
                                        Edit
                                    </button>
                                    <?php if ($u['id_anggota'] !== \App\Core\Auth::id()): ?>
                                        <form action="<?= \App\Core\Helper::url('pengguna', ['action' => 'toggle']) ?>" method="POST" style="display:inline;">
                                            <input type="hidden" name="id_anggota" value="<?= $u['id_anggota'] ?>">
                                            <button type="submit" class="btn btn-secondary" style="height: 28px; padding: 0 8px; font-size: 0.74rem;">
                                                <?= $isActive ? 'Nonaktifkan' : 'Aktifkan' ?>
                                            </button>
                                        </form>
                                        <form action="<?= \App\Core\Helper::url('pengguna', ['action' => 'delete']) ?>" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini? Akun dengan histori transaksi sebaiknya dinonaktifkan.')">
                                            <input type="hidden" name="id_anggota" value="<?= $u['id_anggota'] ?>">
                                            <button type="submit" class="btn btn-secondary" style="height: 28px; padding: 0 8px; font-size: 0.74rem; color: var(--danger);">
                                                Hapus
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL TAMBAH & EDIT PENGGUNA -->
    <div id="userModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px); z-index: 999; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #ffffff; width: 100%; max-width: 460px; border-radius: 18px; box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.2); overflow: hidden; border: 1px solid #e2e8f0; animation: modalPop 0.18s cubic-bezier(0.16, 1, 0.3, 1);">
            <div style="padding: 16px 22px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 28px; height: 28px; border-radius: 8px; background: rgba(2, 132, 199, 0.1); color: #0284c7; display: flex; align-items: center; justify-content: center;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                    </span>
                    <strong id="modalUserTitle" style="font-size: 0.98rem; font-weight: 700; color: #0f172a;">Tambah Pengguna Baru</strong>
                </div>
                <button type="button" onclick="closeUserModal()" style="border: none; background: #f1f5f9; color: #64748b; width: 28px; height: 28px; border-radius: 8px; font-size: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">&times;</button>
            </div>
            <form id="userModalForm" action="<?= \App\Core\Helper::url('pengguna', ['action' => 'store']) ?>" method="POST" style="padding: 20px 24px; display: flex; flex-direction: column; gap: 14px;">
                <input type="hidden" name="id_anggota" id="inputUserId" value="">
                
                <div>
                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Nama Lengkap Pegawai <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="nama_anggota" id="inputNamaUser" required placeholder="Contoh: Siti Rahmawati" class="form-control" style="width: 100%; height: 38px; border: 1px solid #cbd5e1; border-radius: 9px; padding: 0 12px; font-size: 0.85rem;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Username Login <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="username" id="inputUsername" required placeholder="kasir1" class="form-control" style="width: 100%; height: 38px; border: 1px solid #cbd5e1; border-radius: 9px; padding: 0 12px; font-size: 0.85rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Peran / Hak Akses <span style="color: #ef4444;">*</span></label>
                        <select name="id_role" id="selectRole" required class="form-control" style="width: 100%; height: 38px; border: 1px solid #cbd5e1; border-radius: 9px; padding: 0 10px; font-size: 0.85rem;">
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['id_role'] ?>"><?= htmlspecialchars($r['nama_role']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 5px;">
                        Password Login <span id="pwdRequiredStar" style="color: #ef4444;">*</span>
                    </label>
                    <input type="password" name="password" id="inputPassword" placeholder="Masukkan password" class="form-control" style="width: 100%; height: 38px; border: 1px solid #cbd5e1; border-radius: 9px; padding: 0 12px; font-size: 0.85rem;">
                    <span id="pwdHint" style="display: none; font-size: 0.72rem; color: #64748b; margin-top: 4px;">* Biarkan kosong jika tidak ingin mengganti password akun.</span>
                </div>

                <div id="userStatusWrap" style="display: flex; align-items: center; gap: 8px; padding-top: 4px;">
                    <input type="checkbox" name="status_aktif" id="checkUserAktif" value="1" checked style="width: 16px; height: 16px; accent-color: #0284c7;">
                    <label for="checkUserAktif" style="font-size: 0.8rem; font-weight: 600; color: #334155; cursor: pointer;">Akun Aktif (Dapat Login ke Sistem)</label>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="closeUserModal()" style="flex: 1; height: 38px; border: 1px solid #e2e8f0; background: #ffffff; color: #475569; font-size: 0.82rem; font-weight: 600; border-radius: 10px; cursor: pointer;">Batal</button>
                    <button type="submit" id="btnSubmitUser" style="flex: 1; height: 38px; border: none; background: #0284c7; color: #ffffff; font-size: 0.82rem; font-weight: 700; border-radius: 10px; cursor: pointer; box-shadow: 0 2px 4px rgba(2, 132, 199, 0.25);">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openAddUserModal() {
        const modal = document.getElementById('userModal');
        const form = document.getElementById('userModalForm');
        const title = document.getElementById('modalUserTitle');
        const btn = document.getElementById('btnSubmitUser');
        const pwdInput = document.getElementById('inputPassword');
        const pwdStar = document.getElementById('pwdRequiredStar');
        const pwdHint = document.getElementById('pwdHint');
        if (!modal || !form) return;

        title.textContent = 'Tambah Pengguna Baru';
        btn.textContent = 'Simpan Pengguna';
        form.action = 'index.php?page=pengguna&action=store';

        document.getElementById('inputUserId').value = '';
        document.getElementById('inputNamaUser').value = '';
        document.getElementById('inputUsername').value = '';
        pwdInput.value = '';
        pwdInput.required = true;
        pwdStar.style.display = 'inline';
        pwdHint.style.display = 'none';
        document.getElementById('checkUserAktif').checked = true;

        modal.style.display = 'flex';
    }

    function editUser(u) {
        if (!u) return;
        const modal = document.getElementById('userModal');
        const form = document.getElementById('userModalForm');
        const title = document.getElementById('modalUserTitle');
        const btn = document.getElementById('btnSubmitUser');
        const pwdInput = document.getElementById('inputPassword');
        const pwdStar = document.getElementById('pwdRequiredStar');
        const pwdHint = document.getElementById('pwdHint');
        if (!modal || !form) return;

        title.textContent = 'Edit Data Pengguna';
        btn.textContent = 'Perbarui Akun';
        form.action = 'index.php?page=pengguna&action=update';

        document.getElementById('inputUserId').value = u.id_anggota || '';
        document.getElementById('inputNamaUser').value = u.nama_anggota || '';
        document.getElementById('inputUsername').value = u.username || '';
        pwdInput.value = '';
        pwdInput.required = false;
        pwdStar.style.display = 'none';
        pwdHint.style.display = 'block';

        if (u.id_role) {
            document.getElementById('selectRole').value = u.id_role;
        }
        document.getElementById('checkUserAktif').checked = (u.status_aktif == 1 || u.status_aktif === true);

        modal.style.display = 'flex';
    }

    function closeUserModal() {
        const modal = document.getElementById('userModal');
        if (modal) modal.style.display = 'none';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeUserModal();
    });
    </script>
</div>
