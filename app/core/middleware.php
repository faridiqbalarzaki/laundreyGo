<?php
declare(strict_types=1);

/**
 * MIDDLEWARE / GUARD — fungsi PHP biasa bernama guard_{nama}(array $route, array $args).
 * Bila gagal, guard MENGHENTIKAN request (redirect / 403 / 419). Bila lolos, return normal.
 * Dipakai di routes.php:  'mw' => ['auth', 'role:admin,kasir', 'csrf']
 */

function run_middleware(array $route): void
{
    foreach ($route['mw'] ?? [] as $spec) {
        [$name, $argStr] = array_pad(explode(':', $spec, 2), 2, '');
        $fn   = 'guard_' . $name;
        $args = $argStr === '' ? [] : explode(',', $argStr);
        if (!function_exists($fn)) {
            http_response_code(500);
            exit('Middleware tidak dikenal: ' . e($name));
        }
        $fn($route, $args);
    }
}

/** Hanya tamu (belum login). Yang sudah login diarahkan ke ruang kerja role-nya. */
function guard_guest(array $route, array $args = []): void
{
    if (auth_check()) {
        redirect(role_home(auth_role()));
    }
}

/** Wajib login. */
function guard_auth(array $route, array $args = []): void
{
    if (auth_check()) {
        return;
    }
    if (wants_json()) {
        json_response(['ok' => false, 'error' => 'Sesi berakhir. Silakan masuk kembali.'], 401);
    }
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
        $_SESSION['intended'] = request_path();
    }
    redirect('/login');
}

/** Wajib salah satu role. Gagal => halaman "Akses Dibatasi" (HTTP 403). */
function guard_role(array $route, array $args = []): void
{
    $role = auth_role();
    if ($role !== null && in_array($role, $args, true)) {
        return;
    }
    if (wants_json()) {
        json_response(['ok' => false, 'error' => 'Akses dibatasi untuk peran Anda.'], 403);
    }
    render_restricted();
}

/** Validasi token CSRF untuk method yang mengubah state. */
function guard_csrf(array $route, array $args = []): void
{
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
        return;
    }
    $sent = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (is_string($sent) && $sent !== '' && hash_equals(csrf_token(), $sent)) {
        return;
    }
    if (wants_json()) {
        json_response(['ok' => false, 'error' => 'Token keamanan tidak valid. Muat ulang halaman.'], 419);
    }
    http_response_code(419);
    exit('Token keamanan tidak valid. Muat ulang halaman lalu coba lagi.');
}

/** Halaman Restricted Access, dirender di dalam app layout milik role yang sedang login. */
function render_restricted(): never
{
    http_response_code(403);
    render_layout('app', [
        'pageTitle'    => 'Akses Dibatasi',
        'pageSubtitle' => '',
        'activeMenu'   => '',
        'content'      => view_capture('errors/restricted', [
            'roleLabel' => ucfirst((string) auth_role()),
            'homeUrl'   => url(role_home(auth_role())),
        ]),
    ]);
    exit;
}
