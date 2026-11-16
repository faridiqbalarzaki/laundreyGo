<?php
/**
 * Konfigurasi Database PostgreSQL
 * LaundryGo - Native PHP MVC Ringan
 */

return [
    'driver'   => 'pgsql',
    'host'     => '127.0.0.1',
    'port'     => 5432,
    'database' => 'db_laundryGo',
    'username' => 'faiza',
    'password' => '', // Default Laragon PostgreSQL kosong (tanpa password)
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
];
