<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!headers_sent()) {
    ob_start();
}
if (!headers_sent() && ob_get_level() === 0) {
    ob_start();
}

function config(): array {
    static $cfg = null;
    if ($cfg === null) {
        $file = __DIR__ . '/../config/config.php';
        if (!file_exists($file)) {
            die('Missing config/config.php — copy config.sample.php and fill in your values.');
        }
        $cfg = require $file;
    }
    return $cfg;
}

function site_name(): string {
    return config()['site_name'] ?? 'MeowMart';
}

function base_url(string $path = ''): string {
    $base = rtrim(config()['base_url'] ?? '', '/');
    $path = ltrim($path, '/');
    return $path === '' ? ($base ?: '') : $base . '/' . $path;
}

function h(?string $v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void {
    header('Location: ' . base_url($path));
    exit;
}

function set_flash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $msg];
}

function get_flash(): ?array {
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        die('CSRF token mismatch. Please go back and try again.');
    }
}

function old(string $key, string $default = ''): string {
    return h($_SESSION['old'][$key] ?? $default);
}

function store_old(array $data): void {
    $_SESSION['old'] = $data;
}

function clear_old(): void {
    unset($_SESSION['old']);
}

function post(string $key): string {
    return trim((string)($_POST[$key] ?? ''));
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool {
    return current_user() !== null;
}

function is_admin(): bool {
    return (current_user()['role'] ?? '') === 'admin';
}

function require_login(): void {
    if (!is_logged_in()) {
        set_flash('error', 'Please log in to continue.');
        redirect('account/login.php');
    }
}

function require_admin(): void {
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        die('Access denied.');
    }
}

function money(float $amount): string {
    return '$' . number_format($amount, 2);
}

function cart_count(): int {
    return array_sum(array_column($_SESSION['cart'] ?? [], 'qty'));
}

function cart_total(): float {
    $total = 0;
    foreach ($_SESSION['cart'] ?? [] as $row) {
        $total += (float)$row['price'] * (int)$row['qty'];
    }
    return $total;
}

function db_ready(): bool {
    try {
        db();
        return true;
    } catch (Throwable $e) {
        return false;
    }
}
