<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=laundrygo', 'root', '');
echo "--- PESANAN IN MYSQL ---\n";
print_r($pdo->query("SELECT * FROM pesanan LIMIT 2")->fetchAll(PDO::FETCH_ASSOC));
echo "--- PELANGGAN IN MYSQL ---\n";
print_r($pdo->query("SELECT * FROM pelanggan LIMIT 2")->fetchAll(PDO::FETCH_ASSOC));
