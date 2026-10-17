<?php
declare(strict_types=1);

/* ---------- Output & URL ---------- */

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Base URL relatif ("" bila di root, "/laundrygo/public" bila di subfolder). */
function base_url(): string
{
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    return rtrim($dir, '/');
}

function url(string $path = '/'): string
{
    return base_url() . '/' . ltrim($path, '/');
}

/** URL aset + cache-busting berdasarkan filemtime. */
function asset(string $path): string
{
    $file = BASE_PATH . '/public/assets/' . ltrim($path, '/');
    $v = is_file($file) ? '?v=' . filemtime($file) : '';
    return url('assets/' . ltrim($path, '/')) . $v;
}

function redirect(string $path, int $status = 302): never
{
    header('Location: ' . url($path), true, $status);
    exit;
}

function json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function wants_json(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    return str_starts_with(request_path(), '/api/')
        || str_contains($accept, 'application/json')
        || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');
}

/* ---------- View rendering ---------- */

/** Render view ke string (dipakai untuk $content layout). */
function view_capture(string $name, array $data = []): string
{
    $file = APP_PATH . '/views/' . $name . '.php';
    if (!is_file($file)) {
        $file = APP_PATH . '/views/pages/_placeholder.php';
        $data['missingView'] = $name;
    }
    extract($data, EXTR_SKIP);
    ob_start();
    require $file;
    return (string) ob_get_clean();
}

/** Sisipkan partial (views/partials/*.php) dengan data lokal. */
function partial(string $name, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require APP_PATH . '/views/partials/' . $name . '.php';
}

/** Render layout (views/layouts/{name}_layout.php). */
function render_layout(string $layout, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require APP_PATH . '/views/layouts/' . $layout . '_layout.php';
}

/* ---------- CSRF ---------- */

function csrf_token(): string
{
    return $_SESSION['csrf'] ?? '';
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

/* ---------- Role & Menu ---------- */

function role_home(?string $role): string
{
    return match ($role) {
        'admin'    => '/admin/dashboard',
        'kasir'    => '/kasir/pos',
        'produksi' => '/produksi/antrean',
        default    => '/login',
    };
}

/** Menu sidebar untuk satu role, dikelompokkan & berurutan sesuai config/menu.php. */
function menu_for_role(string $role): array
{
    $cfg    = require APP_PATH . '/config/menu.php';
    $result = [];
    foreach ($cfg['groups'] as $groupKey => $groupLabel) {
        $items = array_values(array_filter(
            $cfg['items'],
            static fn(array $i): bool => $i['group'] === $groupKey && in_array($role, $i['roles'], true)
        ));
        if ($items) {
            $result[] = ['label' => $groupLabel, 'items' => $items];
        }
    }
    return $result;
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $out   = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $out .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    return $out !== '' ? $out : '?';
}

/**
 * Counter untuk badge sidebar & pill topbar.
 * TODO (langkah berikutnya): ganti dengan query OrderModel.
 */
function nav_counts(): array
{
    return ['selesai' => 2, 'active' => 5, 'today' => 8];
}
