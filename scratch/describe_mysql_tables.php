<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=laundrygo', 'root', '');
$tables = ['pesanan', 'pelanggan', 'layanan', 'pembayaran', 'detail_pesanan', 'pengguna'];
foreach ($tables as $t) {
    echo "=== TABLE $t ===\n";
    $cols = $pdo->query("DESCRIBE `$t`")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo "  {$c['Field']} ({$c['Type']})\n";
    }
}
