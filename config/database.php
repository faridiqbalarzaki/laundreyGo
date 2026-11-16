<?php
/**
 * Konfigurasi Database MySQL (MariaDB)
 * Terhubung langsung dengan DBeaver & Laragon MySQL (Port 3306)
 * LaundryGo - Native PHP MVC Ringan
 */

return [
    'driver'   => 'mysql',
    'host'     => '127.0.0.1',
    'port'     => 3306,
    'database' => 'laundrygo',
    'username' => 'root',
    'password' => '',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
];
