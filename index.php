<?php
/**
 * LaundryGo Aqua - Single Front Controller
 * PHP Native (Lightweight MVC Architecture without Frameworks)
 * Didesain bersih, rapi, dan mudah dipahami untuk mahasiswa semester 3.
 */

declare(strict_types=1);

// HTTP Security Headers (Defense-in-Depth)
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Set timezone Indonesia Barat
date_default_timezone_set('Asia/Jakarta');

// Autoload Kelas (PSR-4 Sederhana)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/app/';

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

use App\Core\Auth;
use App\Core\Helper;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\PosController;
use App\Controllers\PesananController;
use App\Controllers\ProduksiController;
use App\Controllers\PelangganController;
use App\Controllers\PengeluaranController;
use App\Controllers\LaporanController;
use App\Controllers\LayananController;
use App\Controllers\PenggunaController;
use App\Controllers\TrackingController;
use App\Controllers\ProfilController;

// Inisialisasi Session
Auth::init();

// Ambil parameter routing: index.php?page=...&action=...
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Jika belum login dan mengakses halaman selain login / tracking, arahkan ke login
if (!Auth::check() && !in_array($page, ['login', 'tracking'])) {
    $page = 'login';
}

// ROUTING CONTROLLER DISPATCHER
switch ($page) {
    case 'login':
        $controller = new AuthController();
        if ($action === 'doLogin' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->doLogin();
        } else {
            $controller->login();
        }
        break;

    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'dashboard':
        $controller = new DashboardController();
        $controller->index();
        break;

    case 'pos':
        $controller = new PosController();
        if ($action === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } elseif ($action === 'searchPelanggan') {
            $controller->searchPelanggan();
        } else {
            $controller->index();
        }
        break;

    case 'pesanan':
        $controller = new PesananController();
        if ($action === 'selesai') {
            $controller->selesai();
        } elseif ($action === 'detail') {
            $controller->detail();
        } elseif ($action === 'updateStatus' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->updateStatus();
        } elseif (in_array($action, ['ambil', 'serahkan', 'bayarDanSerahkan']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->ambil();
        } else {
            $controller->index();
        }
        break;

    case 'produksi':
        $controller = new ProduksiController();
        if ($action === 'advance' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->advance();
        } elseif ($action === 'revert' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->revert();
        } else {
            $controller->index();
        }
        break;

    case 'pelanggan':
        $controller = new PelangganController();
        if ($action === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->update();
        } elseif ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->delete();
        } elseif ($action === 'history') {
            $controller->history();
        } else {
            $controller->index();
        }
        break;

    case 'pengeluaran':
        $controller = new PengeluaranController();
        if ($action === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->update();
        } elseif ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;

    case 'laporan':
        $controller = new LaporanController();
        $controller->index();
        break;

    case 'layanan':
        $controller = new LayananController();
        if ($action === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->update();
        } elseif ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->delete();
        } elseif ($action === 'toggle' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->toggle();
        } else {
            $controller->index();
        }
        break;

    case 'pengguna':
        $controller = new PenggunaController();
        if ($action === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->update();
        } elseif ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->delete();
        } elseif ($action === 'toggle' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->toggle();
        } else {
            $controller->index();
        }
        break;

    case 'tracking':
        $controller = new TrackingController();
        $controller->index();
        break;

    case 'profil':
        $controller = new ProfilController();
        if ($action === 'update_password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->updatePassword();
        } else {
            $controller->index();
        }
        break;

    default:
        // Halaman 404 Tidak Ditemukan
        http_response_code(404);
        echo "<div style='font-family:sans-serif;text-align:center;padding:80px 20px;'>
                <h1 style='font-size:72px;color:#0284c7;margin:0;'>404</h1>
                <h2>Halaman Tidak Ditemukan</h2>
                <p style='color:#64748b;'>Halaman yang Anda tuju (<code>" . htmlspecialchars($page) . "</code>) tidak tersedia di sistem LaundryGo.</p>
                <a href='index.php' style='display:inline-block;margin-top:15px;padding:10px 20px;background:#0284c7;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;'>Kembali ke Beranda</a>
              </div>";
        break;
}
