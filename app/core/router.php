<?php
declare(strict_types=1);

function request_path(): string
{
    $uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $uri  = rawurldecode($uri);
    $base = base_url();
    if ($base !== '' && str_starts_with($uri, $base)) {
        $uri = substr($uri, strlen($base)) ?: '/';
    }
    if ($uri === '/index.php') {
        $uri = '/';
    }
    return $uri === '/' ? '/' : rtrim($uri, '/');
}

function route_dispatch(): void
{
    $routes = require APP_PATH . '/config/routes.php';
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    $method = $method === 'HEAD' ? 'GET' : $method;
    $path   = request_path();

    if ($path === '/') {
        redirect(auth_check() ? role_home(auth_role()) : '/login');
    }

    $route = $routes["$method $path"] ?? null;

    if ($route === null) {
        // Path ada tapi method salah => 405, selain itu 404.
        foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $m) {
            if (isset($routes["$m $path"])) {
                http_response_code(405);
                header('Allow: ' . $m);
                exit('Method tidak diizinkan.');
            }
        }
        render_not_found();
    }

    $route['path'] = $path;
    run_middleware($route);               // 1) Guard (auth -> role -> csrf)

    if (isset($route['redirect'])) {      // 2a) Redirect akar ruang kerja
        redirect($route['redirect']);
    }
    if (isset($route['handler'])) {       // 2b) Handler (form/API)
        if (isset($route['file'])) {
            require_once APP_PATH . '/' . $route['file'];
        }
        ($route['handler'])();
        return;
    }

    render_page($route);                  // 2c) Halaman penuh dalam app_layout
}

function render_page(array $route): void
{
    $content = view_capture($route['view'], ['route' => $route]);
    render_layout('app', [
        'pageTitle'    => $route['title'],
        'pageSubtitle' => $route['subtitle'] ?? '',
        'activeMenu'   => $route['menu'] ?? '',
        'pageScripts'  => $route['scripts'] ?? [],   // mis. ['pos.js']
        'content'      => $content,
    ]);
}

function render_not_found(): never
{
    http_response_code(404);
    if (wants_json()) {
        json_response(['ok' => false, 'error' => 'Tidak ditemukan.'], 404);
    }
    if (auth_check()) {
        render_layout('app', [
            'pageTitle'    => 'Halaman tidak ditemukan',
            'pageSubtitle' => '',
            'activeMenu'   => '',
            'content'      => view_capture('errors/404', ['homeUrl' => url(role_home(auth_role()))]),
        ]);
    } else {
        render_layout('auth', [
            'content' => view_capture('errors/404', ['homeUrl' => url('/login')]),
        ]);
    }
    exit;
}
