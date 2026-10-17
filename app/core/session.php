<?php
declare(strict_types=1);

function session_boot(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_name('LGSESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();

    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    // Idle timeout
    if (isset($_SESSION['user'])) {
        $last = $_SESSION['last_activity'] ?? time();
        if (time() - $last > SESSION_IDLE_TTL) {
            auth_logout_user();
            session_boot();
            return;
        }
        $_SESSION['last_activity'] = time();
    }
}

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function auth_check(): bool
{
    return isset($_SESSION['user']['role']);
}

function auth_role(): ?string
{
    return $_SESSION['user']['role'] ?? null;
}

/** Dipanggil setelah kredensial valid. Mengganti ID sesi (anti session fixation). */
function auth_login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id'               => $user['id'],
        'name'             => $user['name'],
        'username'         => $user['username'],
        'role'             => $user['role'],        // admin | kasir | produksi
        'subtitle'         => $user['subtitle'] ?? '',
        'must_change_pass' => (bool) ($user['must_change_pass'] ?? false),
    ];
    $_SESSION['cart']          = [];                // keranjang POS sementara
    $_SESSION['last_activity'] = time();
}

function auth_logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'  => time() - 42000,
            'path'     => $p['path'],
            'domain'   => $p['domain'],
            'secure'   => $p['secure'],
            'httponly' => $p['httponly'],
            'samesite' => $p['samesite'] ?: 'Lax',
        ]);
    }
    session_destroy();
}
