<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=laundrygo', 'root', '');
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
echo "Tables in MySQL laundrygo:\n";
print_r($tables);
foreach ($tables as $t) {
    $count = $pdo->query("SELECT count(*) FROM `$t`")->fetchColumn();
    echo "$t: $count rows\n";
}
