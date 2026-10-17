<?php /** @var string $roleLabel @var string $homeUrl */ ?>
<div class="card empty-state" style="max-width:520px; margin:40px auto;">
  <h3 style="font-size:20px; color:var(--teks); margin-bottom:8px;">Akses Dibatasi untuk Peran <?= e($roleLabel) ?></h3>
  <p style="font-size:13.5px; margin-bottom:18px;">Halaman ini hanya dapat diakses oleh pengguna dengan hak akses yang sesuai pada Matriks Peran Adi Laundry.</p>
  <a class="btn btn-primary" href="<?= e($homeUrl) ?>">Kembali ke halaman utama</a>
</div>
