<?php
declare(strict_types=1);

function auth_show_login(): void
{
    $error = $_SESSION['flash_error'] ?? null;
    $old   = $_SESSION['flash_old'] ?? '';
    unset($_SESSION['flash_error'], $_SESSION['flash_old']);

    render_layout('auth', [
        'content' => view_capture('auth/login', ['error' => $error, 'old' => $old]),
    ]);
}

function auth_login(): void
{
    $username = strtolower(trim((string) ($_POST['username'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    $fail = static function (string $msg) use ($username): never {
        $_SESSION['flash_error'] = $msg;
        $_SESSION['flash_old']   = $username;
        redirect('/login');
    };

    if ($username === '' || $password === '') {
        $fail('Username dan password wajib diisi.');
    }

    $users = require APP_PATH . '/config/users.php';   // TODO: UserModel::findByUsername()
    $user  = $users[$username] ?? null;

    if ($user === null || !password_verify($password, $user['hash'])) {
        $fail('Username atau password salah. Silakan coba lagi.');
    }
    if (!$user['active']) {
        $fail('Akun tidak aktif. Hubungi Admin Adi Laundry.');
    }

    $intended = (string) ($_SESSION['intended'] ?? '');
    auth_login_user($user);

    if ($user['must_change_pass']) {
        redirect('/profil');                            // wajib ganti password (aturan asli)
    }
    $role = $user['role'];
    if ($intended !== '' && (str_starts_with($intended, '/' . $role . '/') || $intended === '/profil')) {
        redirect($intended);
    }
    redirect(role_home($role));
}

function auth_logout(): void
{
    auth_logout_user();
    redirect('/login');
}
