<?php
require 'd:/laragon/www/Laundrey/app/Core/Database.php';
$pdo = App\Core\Database::getConnection();
$rows = $pdo->query('SELECT * FROM pelanggan LIMIT 6')->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
