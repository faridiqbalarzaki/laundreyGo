<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    echo "MYSQL_CONNECTED_SUCCESSFULLY\n";
    $dbs = $pdo->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN);
    echo "Databases in MySQL:\n";
    print_r($dbs);
} catch (Exception $e) {
    echo "MYSQL_CONNECTION_FAILED: " . $e->getMessage() . "\n";
}
