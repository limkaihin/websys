<?php
/**
 * MeowMart — Core Functions
 * Integrates:
 *   - Zebra_Session (Open Source Project #5): database-backed sessions
 */

// ── Session bootstrap ───────────────────────────────────────────────────────
// We do NOT call session_start() here directly anymore.
// Instead, _init_session() is called by header.php after the DB is ready.

function _init_session(\PDO $pdo): void
{
    $cfg = config();

    // If DB sessions requested and session is running via files, close it so
    // Zebra_Session can re-open with its own handler.
    if (!empty($cfg['session_db']) && session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    // Already running with Zebra (or DB sessions not requested)
    if (session_status() === PHP_SESSION_ACTIVE) return;

    if (!empty($cfg['session_db'])) {
        // ── Zebra_Session: store sessions in MySQL (Open Source Project #5) ──
        $zebraFile = __DIR__ . '/../vendor/stefangabos/Zebra_Session.php';
        if (file_exists($zebraFile)) {
            require_once $zebraFile;
            // Zebra_Session calls session_start() internally
            new Zebra_Session(
                $pdo,
                $cfg['session_security_code'] ?? 'meowmart_fallback_key',
                $cfg['session_lifetime']      ?? 3600,
                true,   // lock_to_user_agent
                false,  // lock_to_ip
                1,      // gc_probability
                100,    // gc_divisor
                $cfg['session_table'] ?? 'session_data',
                60      // lock_timeout
            );
            return;
        }
        error_log('Zebra_Session not found — falling back to file-based sessions.');
    }

    // Default file-based sessions
    session_start();
}

// ── Config & helpers ─────────────────────────────────────────────────────────

function config(): array
{
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

function site_name(): string  { return config()['site_name'] ?? 'MeowMart'; }

function base_url(string $path = ''): string
{
    $base = rtrim(config()['base_url'] ?? '', '/');
    $path = ltrim($path, '/');
    return $path === '' ? ($base ?: '') : $base . '/' . $path;
}

function h(?string $v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function set_flash(string $type, string $msg): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $msg];
}

function get_flash(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        die('CSRF token mismatch. Please go back and try again.');
    }
}

function old(string $key, string $default = ''): string
{
    return h($_SESSION['old'][$key] ?? $default);
}

function store_old(array $data): void  { $_SESSION['old'] = $data; }
function clear_old(): void             { unset($_SESSION['old']); }
function post(string $key): string     { return trim((string)($_POST[$key] ?? '')); }
function current_user(): ?array        { return $_SESSION['user'] ?? null; }
function is_logged_in(): bool          { return current_user() !== null; }
function is_admin(): bool              { return (current_user()['role'] ?? '') === 'admin'; }

function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('error', 'Please log in to continue.');
        redirect('account/login.php');
    }
}

function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        die('Access denied.');
    }
}

function money(float $amount): string
{
    return '$' . number_format($amount, 2);
}

function wishlist_items(): array
{
    if (!isset($_SESSION['wishlist']) || !is_array($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = [];
    }
    return $_SESSION['wishlist'];
}

function wishlist_has(int $productId): bool
{
    return in_array($productId, wishlist_items(), true);
}

function wishlist_toggle(int $productId): bool
{
    $items = wishlist_items();
    $key   = array_search($productId, $items, true);
    if ($key !== false) {
        unset($items[$key]);
        $_SESSION['wishlist'] = array_values($items);
        return false;
    }
    $items[] = $productId;
    $_SESSION['wishlist'] = array_values(array_unique(array_map('intval', $items)));
    return true;
}

function wishlist_count(): int { return count(wishlist_items()); }

function cart_count(): int
{
    return array_sum(array_column($_SESSION['cart'] ?? [], 'qty'));
}

function cart_total(): float
{
    $total = 0;
    foreach ($_SESSION['cart'] ?? [] as $row) {
        $total += (float)$row['price'] * (int)$row['qty'];
    }
    return $total;
}

function db_ready(): bool
{
    try { db(); return true; }
    catch (Throwable $e) { return false; }
}

// ── Auto-start session for pages that include functions.php without header.php ─
// (e.g. POST handlers in shop/products.php, shop/cart.php)
// header.php calls _init_session(db()) which handles Zebra_Session properly.
// This fallback just covers raw POST-only includes.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
