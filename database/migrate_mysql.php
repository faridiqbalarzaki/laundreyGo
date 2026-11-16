<?php
declare(strict_types=1);

echo "=== MIGRATION LAUNDRYGO: MYSQL (Port 3306) ===\n\n";

$host = '127.0.0.1';
$port = 3306;
$dbName = 'laundrygo';
$user = 'root';
$password = '';

try {
    // 1. Konek ke MySQL server (tanpa database dulu)
    $dsnServer = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsnServer, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "[OK] Terhubung ke MySQL server di {$host}:{$port}\n";

    // Buat database jika belum ada
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "[OK] Database `{$dbName}` siap.\n";

    // 2. Konek ke database target
    $dsnDb = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
    $pdoDb = new PDO($dsnDb, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Bersihkan tabel & view lama
    echo "Membersihkan tabel & view lama di `{$dbName}`...\n";
    $pdoDb->exec("SET FOREIGN_KEY_CHECKS = 0");
    $views = $pdoDb->query("SHOW FULL TABLES WHERE Table_Type = 'VIEW'")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($views as $v) {
        $pdoDb->exec("DROP VIEW IF EXISTS `{$v}`");
    }
    $tables = $pdoDb->query("SHOW FULL TABLES WHERE Table_Type = 'BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $t) {
        $pdoDb->exec("DROP TABLE IF EXISTS `{$t}`");
    }
    $pdoDb->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "[OK] Ruang tabel dibersihkan.\n";

    // Jalankan Skema
    $schemaFile = __DIR__ . '/schema_mysql.sql';
    if (file_exists($schemaFile)) {
        echo "Menjalankan schema_mysql.sql...\n";
        $sqlSchema = file_get_contents($schemaFile);
        $pdoDb->exec($sqlSchema);
        echo "[OK] Skema tabel MySQL berhasil dibuat/disinkronkan!\n";
    }

    // Jalankan Seed
    $seedFile = __DIR__ . '/seed_mysql.sql';
    if (file_exists($seedFile)) {
        echo "Menjalankan seed_mysql.sql...\n";
        $sqlSeed = file_get_contents($seedFile);
        $pdoDb->exec($sqlSeed);
        echo "[OK] Seed data awal MySQL berhasil dimasukkan!\n";
    }

    echo "\n=== MIGRASI MYSQL SUKSES! ===\n";
    echo "Tabel yang aktif di MySQL `{$dbName}`:\n";
    $tables = $pdoDb->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $t) {
        $count = $pdoDb->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
        echo " - {$t} ({$count} baris)\n";
    }

} catch (PDOException $e) {
    echo "[GAGAL] Error MySQL: " . $e->getMessage() . "\n";
    exit(1);
}
