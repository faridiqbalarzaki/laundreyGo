<?php /** @var string $title */ ?>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="<?= e(csrf_token()) ?>" />
<title><?= e($title) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?= e(asset('css/tokens.css')) ?>" />
<link rel="stylesheet" href="<?= e(asset('css/layout.css')) ?>" />
<link rel="stylesheet" href="<?= e(asset('css/components.css')) ?>" />
<link rel="stylesheet" href="<?= e(asset('css/compat.css')) ?>" />
