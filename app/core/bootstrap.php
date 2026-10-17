<?php
declare(strict_types=1);

define('APP_ENV', getenv('APP_ENV') ?: 'development');   // set 'production' di server
define('APP_DEBUG', APP_ENV !== 'production');
define('SESSION_IDLE_TTL', 60 * 60 * 8);                   // 8 jam tanpa aktivitas

date_default_timezone_set('Asia/Jakarta');
ini_set('display_errors', APP_DEBUG ? '1' : '0');
error_reporting(E_ALL);

require_once APP_PATH . '/core/helpers.php';
require_once APP_PATH . '/core/icons.php';
require_once APP_PATH . '/core/session.php';
require_once APP_PATH . '/core/middleware.php';
require_once APP_PATH . '/core/router.php';
require_once APP_PATH . '/controllers/auth_controller.php';
