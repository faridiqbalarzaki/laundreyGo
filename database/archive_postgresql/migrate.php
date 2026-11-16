<?php
/**
 * Script Migrasi & Seed Database PostgreSQL
 * Jalankan via CLI: php database/migrate.php
 * Atau via Browser: http://localhost/Laundrey/database/migrate.php
 */

$config = require __DIR__ . '/../config/database.php';

$host     = $config['host'];
$port     = $config['port'];
$dbName   = $config['database'];
$user     = $config['username'];
$password = $config['password'];

$isCli = (php_sapi_name() === 'cli');

function out($msg, $isCli) {
    if ($isCli) {
        echo $msg . "\n";
    } else {
        echo nl2br(htmlspecialchars($msg)) . "<br>";
    }
}

out("=== MEMULAI MIGRASI DATABASE LAUNDRYGO (POSTGRESQL) ===", $isCli);

// 1. Coba koneksi ke database postgres default untuk cek/buat database target
try {
    $dsnDefault = "pgsql:host={$host};port={$port};dbname=postgres";
    $pdoInit = new PDO($dsnDefault, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Cek apakah database sudah ada
    $stmt = $pdoInit->prepare("SELECT 1 FROM pg_database WHERE datname = :dbname");
    $stmt->execute(['dbname' => $dbName]);
    $exists = $stmt->fetchColumn();

    if (!$exists) {
        out("Database '{$dbName}' belum ada. Membuat database...", $isCli);
        $pdoInit->exec("CREATE DATABASE \"{$dbName}\"");
        out("[OK] Database '{$dbName}' berhasil dibuat!", $isCli);
    } else {
        out("[INFO] Database '{$dbName}' sudah tersedia.", $isCli);
    }
} catch (PDOException $e) {
    out("[WARNING] Gagal konek ke db postgres default: " . $e->getMessage(), $isCli);
    out("Mencoba langsung menyambung ke database target '{$dbName}'...", $isCli);
}

// 2. Konek ke database target dan jalankan schema & seed
try {
    $dsnTarget = "pgsql:host={$host};port={$port};dbname={$dbName}";
    $pdo = new PDO($dsnTarget, $user, $password, $config['options']);
    out("[OK] Berhasil terhubung ke database '{$dbName}'.", $isCli);

    // Jalankan Skema
    $schemaFile = __DIR__ . '/schema_postgresql.sql';
    if (file_exists($schemaFile)) {
        out("Menjalankan schema_postgresql.sql...", $isCli);
        $sqlSchema = file_get_contents($schemaFile);
        $pdo->exec($sqlSchema);
        out("[OK] Skema tabel PostgreSQL berhasil dieksekusi!", $isCli);
    } else {
        out("[ERROR] File schema_postgresql.sql tidak ditemukan!", $isCli);
    }

    // Jalankan Seed
    $seedFile = __DIR__ . '/seed_postgresql.sql';
    if (file_exists($seedFile)) {
        out("Menjalankan seed_postgresql.sql...", $isCli);
        $sqlSeed = file_get_contents($seedFile);
        $pdo->exec($sqlSeed);
        out("[OK] Seed data awal berhasil dimasukkan!", $isCli);
    }

    out("\n=== MIGRASI SELESAI DENGAN SUKSES! ===", $isCli);
    out("Akun login bawaan:", $isCli);
    out("- Admin   : admin   / admin123", $isCli);
    out("- Kasir   : kasir   / 123456", $isCli);
    out("- Petugas : petugas / 123456", $isCli);

} catch (PDOException $e) {
    out("\n[GAGAL] Terjadi kesalahan database: " . $e->getMessage(), $isCli);
    out("Pastikan service PostgreSQL berjalan di port {$port} dan password di config/database.php sudah benar.", $isCli);
}
