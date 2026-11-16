<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'LaundryGo Aqua') ?></title>
    
    <!-- Google Fonts Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Pure Native Stylesheets (Scoping Sesuai Role & Prototipe HTML) -->
    <?php if ((isset($_GET['page']) && $_GET['page'] === 'produksi') || \App\Core\Auth::isPetugas() || (isset($title) && strpos($title, 'Produksi') !== false)): ?>
        <link rel="stylesheet" href="<?= \App\Core\Helper::asset('css/produksi.css') ?>">
    <?php elseif (\App\Core\Auth::isKasir()): ?>
        <link rel="stylesheet" href="<?= \App\Core\Helper::asset('css/style.css') ?>">
        <link rel="stylesheet" href="<?= \App\Core\Helper::asset('css/pos-app.css') ?>">
    <?php else: ?>
        <link rel="stylesheet" href="<?= \App\Core\Helper::asset('css/laundrygo.css') ?>">
        <link rel="stylesheet" href="<?= \App\Core\Helper::asset('css/style.css') ?>">
        <link rel="stylesheet" href="<?= \App\Core\Helper::asset('css/pos-app.css') ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= \App\Core\Helper::asset('css/pages.css') ?>">
    <link rel="stylesheet" href="<?= \App\Core\Helper::asset('css/toast.css') ?>">
    <link rel="icon" type="image/png" href="<?= \App\Core\Helper::asset('img/logo_icon_drop.png') ?>">
</head>
<?php 
$isPosContext = (\App\Core\Auth::isKasir() || (isset($_GET['page']) && $_GET['page'] === 'pos'));
$bodyClass = 'theme-aqua' . ($isPosContext ? ' is-pos' : '');
?>
<body class="<?= $bodyClass ?>">
    <!-- Modern Floating Toast Notification System (Aqua Design System) -->
    <div id="flashToastContainer" class="flash-toast-container <?= $isPosContext ? 'pos-mode' : '' ?>" aria-live="polite" role="region" aria-label="Notifikasi Sistem">
        <?php
        $flashTypes = [
            'success' => [
                'title' => 'Berhasil',
                'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>'
            ],
            'error' => [
                'title' => 'Terjadi Kesalahan',
                'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
            ],
            'warning' => [
                'title' => 'Perhatian',
                'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'
            ],
            'info' => [
                'title' => 'Informasi',
                'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
            ]
        ];

        foreach ($flashTypes as $type => $meta):
            if (\App\Core\Helper::hasFlash($type)):
                $msg = \App\Core\Helper::getFlash($type);
        ?>
            <div class="flash-toast-item flash-toast-<?= $type ?>" role="alert" data-auto-dismiss="4200" style="--toast-duration: 4200ms;">
                <div class="flash-toast-main">
                    <div class="flash-toast-icon">
                        <?= $meta['icon'] ?>
                    </div>
                    <div class="flash-toast-body">
                        <div class="flash-toast-header">
                            <span class="flash-toast-title"><?= $meta['title'] ?></span>
                        </div>
                        <div class="flash-toast-message"><?= htmlspecialchars($msg) ?></div>
                    </div>
                    <button type="button" class="flash-toast-close" title="Tutup Notifikasi" aria-label="Tutup Notifikasi">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="flash-toast-progress"></div>
            </div>
        <?php
            endif;
        endforeach;
        ?>
    </div>
    <script>
    (function() {
        function attachToastBehavior(toast) {
            if (!toast || toast.dataset.toastBound) return;
            toast.dataset.toastBound = 'true';
            
            var duration = parseInt(toast.dataset.autoDismiss || '4200', 10);
            toast.style.setProperty('--toast-duration', duration + 'ms');
            var closeBtn = toast.querySelector('.flash-toast-close');
            
            var remainingTime = duration;
            var startTime = Date.now();
            var timer = null;
            var isLeaving = false;

            function dismiss() {
                if (isLeaving) return;
                isLeaving = true;
                if (timer) clearTimeout(timer);
                toast.classList.add('is-leaving');
                setTimeout(function() {
                    if (toast && toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 180);
            }

            function pause() {
                if (timer && !isLeaving) {
                    clearTimeout(timer);
                    timer = null;
                    remainingTime -= (Date.now() - startTime);
                    toast.classList.add('is-paused');
                }
            }

            function resume() {
                if (remainingTime > 0 && !isLeaving) {
                    startTime = Date.now();
                    toast.classList.remove('is-paused');
                    timer = setTimeout(dismiss, remainingTime);
                }
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dismiss();
                });
            }

            toast.addEventListener('mouseenter', pause);
            toast.addEventListener('mouseleave', resume);

            startTime = Date.now();
            timer = setTimeout(dismiss, duration);
        }

        document.addEventListener('DOMContentLoaded', function() {
            var items = document.querySelectorAll('.flash-toast-item');
            items.forEach(attachToastBehavior);
        });

        window.__attachToastBehavior = attachToastBehavior;
    })();
    </script>
