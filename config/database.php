<?php
/**
 * Konfigurasi Database PostgreSQL
 * Terhubung dengan DBeaver & XAMPP (PHP) - Port 5432
 * LaundryGo - Native PHP MVC Ringan
 */

return [
    'driver'   => 'pgsql',
    'host'     => '127.0.0.1',
    'port'     => 5432,
    'database' => 'laundrygo',
    'username' => 'postgres',
    'password' => 'anjay123',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
];