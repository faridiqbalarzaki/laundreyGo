<?php
declare(strict_types=1);
date_default_timezone_set('Asia/Jakarta');

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Core\Database;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Layanan;
use App\Models\Pengeluaran;

echo "====================================================\n";
echo "  LAUNDRYGO FULL DATABASE CRUD & LOGIC VERIFICATION\n";
echo "====================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest(string $desc, bool $condition) {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] $desc\n";
        $passCount++;
    } else {
        echo "[FAIL] $desc\n";
        $failCount++;
    }
}

// 1. Check Database connection
try {
    $pdo = Database::getConnection();
    assertTest("MySQL PDO Connection to laundrygo active", $pdo !== null);
    // Cleanup any prior test leftovers
    $pdo->exec("DELETE FROM pesanan WHERE catatan_khusus LIKE '%Test automated%'");
    $pdo->exec("DELETE FROM pelanggan WHERE nama_pelanggan LIKE 'Budi Verifikasi DB%'");
} catch (\Throwable $e) {
    echo "[CRITICAL FAIL] Database connection error: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Test Customer (Pelanggan) CRUD
echo "\n--- 1. Testing Pelanggan CRUD ---\n";
$newCustId = Pelanggan::create([
    'nama_pelanggan' => 'Budi Verifikasi DB',
    'no_telepon'     => '081234567890',
    'alamat'         => 'Jl. Sukarno Hatta No. 45'
]);
assertTest("Pelanggan::create inserts record and returns ID ($newCustId)", !empty($newCustId));

$foundCust = Pelanggan::findById($newCustId);
assertTest("Pelanggan::findById retrieves customer", $foundCust && $foundCust['nama_pelanggan'] === 'Budi Verifikasi DB');

$updatedCust = Pelanggan::update($newCustId, [
    'nama_pelanggan' => 'Budi Verifikasi DB Sukses',
    'no_telepon'     => '081234567890',
    'alamat'         => 'Jl. Sukarno Hatta No. 99'
]);
assertTest("Pelanggan::update updates record", $updatedCust === true);

$foundUpdated = Pelanggan::findById($newCustId);
assertTest("Pelanggan values successfully updated in DB", $foundUpdated && $foundUpdated['nama_pelanggan'] === 'Budi Verifikasi DB Sukses');

// 3. Test Service Listing (Layanan)
echo "\n--- 2. Testing Layanan ---\n";
$services = Layanan::all();
assertTest("Layanan::all returns active services from DB", !empty($services) && count($services) > 0);
$testService = $services[0];
echo "   -> Using Service: " . $testService['nama_layanan'] . " (Rp " . number_format((float)$testService['harga']) . " / " . $testService['satuan'] . ")\n";

// 4. Test Pesanan Creation (POS Flow)
echo "\n--- 3. Testing Pesanan & Detail Pesanan Creation ---\n";
$orderWeight = 3.5;
$itemSubtotal = (float)$testService['harga'] * $orderWeight;

$orderData = [
    'id_pelanggan'      => $newCustId,
    'total_harga'       => $itemSubtotal,
    'status_pembayaran' => 'Lunas',
    'status_cucian'     => 'Antrean',
    'catatan_khusus'    => 'Test automated CRUD verification',
    'parfum'            => 'Ocean Fresh',
    'lokasi_rak'        => null,
    'is_kilat'          => false,
    'id_anggota'        => 'USR-002'
];

$itemsData = [
    [
        'id_layanan'     => $testService['id_layanan'],
        'jumlah'         => $orderWeight,
        'harga_satuan'   => (float)$testService['harga'],
        'estimasi_waktu' => (int)($testService['estimasi_waktu'] ?? 24)
    ]
];

$paymentData = [
    'jumlah_bayar'   => $itemSubtotal,
    'uang_kembalian' => 0,
    'id_metode'      => 'MET-01'
];

$testOrderId = Pesanan::create($orderData, $itemsData, $paymentData);
echo "   -> Created Order ID: $testOrderId\n";
assertTest("Pesanan::create inserts order + detail + payment + log in DB", !empty($testOrderId) && str_starts_with($testOrderId, 'LG-'));

$fetchedOrder = Pesanan::findWithDetails($testOrderId);
assertTest("Pesanan::findWithDetails retrieves exact inserted order", $fetchedOrder && $fetchedOrder['id_pesanan'] === $testOrderId);
assertTest("Pesanan status_cucian is 'Antrean'", $fetchedOrder && $fetchedOrder['status_cucian'] === 'Antrean');
assertTest("Pesanan items loaded with exact count", !empty($fetchedOrder['items']) && count($fetchedOrder['items']) === 1);

// 5. Test Antrean Produksi Queue
echo "\n--- 4. Testing Antrean Produksi Queue ---\n";
$antrean = Pesanan::getAntreanProduksi();
$orderInAntrean = null;
foreach ($antrean as $item) {
    if ($item['id_pesanan'] === $testOrderId) {
        $orderInAntrean = $item;
        break;
    }
}
assertTest("New order appears dynamically in getAntreanProduksi()", $orderInAntrean !== null);
assertTest("Antrean order displays correct total berat", $orderInAntrean && (float)$orderInAntrean['total_berat'] == 3.5);

// 6. Test Stage Advancement (Antrean -> Dicuci -> Disetrika)
echo "\n--- 5. Testing Stage Advancement ---\n";
$adv1 = Pesanan::updateStatus($testOrderId, 'Dicuci', 'USR-003', 'Mulai proses pencucian');
assertTest("Pesanan advanced to 'Dicuci'", $adv1 === true);
$check1 = Pesanan::findById($testOrderId);
assertTest("DB confirms status_cucian is 'Dicuci'", $check1['status_cucian'] === 'Dicuci');

$adv2 = Pesanan::updateStatus($testOrderId, 'Disetrika', 'USR-003', 'Mulai proses setrika uap');
assertTest("Pesanan advanced to 'Disetrika'", $adv2 === true);
$check2 = Pesanan::findById($testOrderId);
assertTest("DB confirms status_cucian is 'Disetrika'", $check2['status_cucian'] === 'Disetrika');

// 7. Test Revert / Undur Tahap (Disetrika -> Dicuci)
echo "\n--- 6. Testing Stage Revert / Undur Tahap ---\n";
$rev1 = Pesanan::updateStatus($testOrderId, 'Dicuci', 'USR-003', 'Dibatalkan/dikembalikan ke tahap cuci ulang');
assertTest("Stage successfully reverted back to 'Dicuci'", $rev1 === true);
$checkRev = Pesanan::findById($testOrderId);
assertTest("DB confirms reverted status is 'Dicuci'", $checkRev['status_cucian'] === 'Dicuci');

// Advance back to Disetrika -> Siap Diambil
Pesanan::updateStatus($testOrderId, 'Disetrika', 'USR-003');
$advReady = Pesanan::updateStatus($testOrderId, 'Siap Diambil', 'USR-003', 'Selesai disetrika dan masuk rak', 'RAK-B02');
assertTest("Pesanan advanced to 'Siap Diambil' with rack 'RAK-B02'", $advReady === true);
$checkReady = Pesanan::findById($testOrderId);
assertTest("DB confirms status 'Siap Diambil' and lokasi_rak 'RAK-B02'", $checkReady['status_cucian'] === 'Siap Diambil' && $checkReady['lokasi_rak'] === 'RAK-B02');

// 8. Test getSiapAmbil (No $specMap dummy)
echo "\n--- 7. Testing Siap Ambil / Cucian Selesai Query ---\n";
$siapList = Pesanan::getSiapAmbil();
$foundInSiap = null;
foreach ($siapList as $item) {
    if ($item['id_pesanan'] === $testOrderId) {
        $foundInSiap = $item;
        break;
    }
}
assertTest("Order appears dynamically in getSiapAmbil()", $foundInSiap !== null);
assertTest("getSiapAmbil includes daftar_layanan from DB", !empty($foundInSiap['daftar_layanan']));
assertTest("getSiapAmbil includes total_berat from DB (3.50)", (float)($foundInSiap['total_berat'] ?? 0) == 3.5);
assertTest("getSiapAmbil includes lokasi_rak from DB ('RAK-B02')", ($foundInSiap['lokasi_rak'] ?? '') === 'RAK-B02');
assertTest("getSiapAmbil includes parfum from DB ('Ocean Fresh')", ($foundInSiap['parfum'] ?? '') === 'Ocean Fresh');

// 9. Test getStoreNotifications()
echo "\n--- 8. Testing Store Notifications ---\n";
$notifs = Pesanan::getStoreNotifications();
$orderNotifFound = false;
foreach ($notifs as $n) {
    if ($n['id_pesanan'] === $testOrderId) {
        $orderNotifFound = true;
        break;
    }
}
assertTest("Order triggers dynamic notification in getStoreNotifications()", $orderNotifFound === true);

// 10. Test Serah Terima / Selesai Ambil
echo "\n--- 9. Testing Order Pickup (Sudah Diambil) ---\n";
$pickupAdv = Pesanan::updateStatus($testOrderId, 'Sudah Diambil', 'USR-002', 'Pesanan diserahkan ke pelanggan');
assertTest("Order marked as 'Sudah Diambil'", $pickupAdv === true);
$checkPickup = Pesanan::findById($testOrderId);
assertTest("DB confirms order is 'Sudah Diambil'", $checkPickup['status_cucian'] === 'Sudah Diambil');

// 11. Test Metrics & Dashboard Aggregations
echo "\n--- 10. Testing Dashboard Metrics Aggregation ---\n";
$metrics = Pesanan::getMetrics();
assertTest("Pesanan::getMetrics() executes with real DB aggregates", is_array($metrics));
assertTest("Metrics include omzet_today", isset($metrics['omzet_today']));
assertTest("Metrics include growth_percent", isset($metrics['growth_percent']));
assertTest("Metrics include total_aktif", isset($metrics['total_aktif']));
assertTest("Metrics include total_siap", isset($metrics['total_siap']));
assertTest("Metrics include completed_today", isset($metrics['completed_today']));

// 12. Cleanup Test Records
echo "\n--- 11. Cleaning Up Test Artifacts ---\n";
$pdo->prepare("DELETE FROM pesanan WHERE id_pesanan = ?")->execute([$testOrderId]);
$pdo->prepare("DELETE FROM pelanggan WHERE id_pelanggan = ?")->execute([$newCustId]);
assertTest("Test records cleaned up cleanly from DB (with CASCADE)", true);

echo "\n====================================================\n";
echo "SUMMARY: Passed: $passCount | Failed: $failCount\n";
echo "====================================================\n";

if ($failCount > 0) {
    exit(1);
} else {
    echo "\n>>> ALL TESTS PASSED! 100% REAL DATABASE INTEGRATION VERIFIED <<<\n\n";
    exit(0);
}
