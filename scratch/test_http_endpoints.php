<?php
$baseUrl = 'http://localhost/Laundrey/index.php';

// First, login as admin to get session cookie
$cookieFile = __DIR__ . '/test_cookie.txt';
if (file_exists($cookieFile)) unlink($cookieFile);

$ch = curl_init($baseUrl . '?page=login&action=doLogin');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'username' => 'admin',
    'password' => 'admin123'
]));
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$loginResp = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Login status: HTTP $httpCode\n\n";

$pages = [
    'dashboard'             => 'Dashboard',
    'pesanan'               => 'Daftar Pesanan',
    'pesanan&action=selesai'=> 'Cucian Selesai & Rak',
    'produksi'              => 'Lantai Produksi',
    'pelanggan'             => 'Data Pelanggan',
    'pos'                   => 'Kasir POS',
    'laporan'               => 'Laporan Keuangan',
    'profil'                => 'Profil Akun'
];

$allOk = true;
foreach ($pages as $param => $label) {
    $ch = curl_init($baseUrl . '?page=' . $param);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $html = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $hasFatalError = str_contains($html, 'Fatal error') || str_contains($html, 'Parse error') || str_contains($html, 'Warning:');
    $isLoginPage = str_contains($html, 'Masuk ke LaundryGo');
    $status = ($code === 200 && !$hasFatalError && !$isLoginPage) ? '[OK]' : '[ERROR]';
    if ($status === '[ERROR]') $allOk = false;
    echo sprintf("%-8s %-25s -> HTTP %d (Length: %d bytes)%s\n", $status, $label, $code, strlen($html), $isLoginPage ? ' (Redirected to Login!)' : '');
}

echo "\nResult: " . ($allOk ? "ALL ENDPOINTS RENDERING CLEANLY (HTTP 200 OK)" : "SOME ENDPOINTS FAILED") . "\n";
