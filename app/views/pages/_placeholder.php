<?php /** Fallback sementara untuk halaman yang belum dimigrasikan. @var string $missingView */ ?>
<div class="card empty-state" style="max-width:520px; margin:40px auto;">
  <h3 style="font-size:18px; color:var(--teks); margin-bottom:8px;">Halaman belum dimigrasikan</h3>
  <p style="font-size:13.5px;">View <code>app/views/<?= e($missingView ?? '') ?>.php</code> akan dibuat pada langkah berikutnya.</p>
</div>
