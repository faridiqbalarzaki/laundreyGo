<?php
declare(strict_types=1);

/**
 * FRONT CONTROLLER — satu-satunya pintu masuk HTTP.
 * Dev server:  php -S localhost:8000 -t public
 */
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

require APP_PATH . '/core/bootstrap.php';

session_boot();
route_dispatch();
