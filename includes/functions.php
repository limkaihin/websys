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

function normalize_display_text(?string $text): string
{
    $text = (string)$text;
    if ($text === '') {
        return '';
    }

    $replacements = [
        'PÃ¢tÃ©' => 'Pate',
        'Pâté' => 'Pate',
        'P-ót|®' => 'Pate',
        'ÔÇô' => '-',
        'â€“' => '-',
        '–' => '-',
        '—' => '-',
        'ÔÇÖ' => "'",
        'â€™' => "'",
        '’' => "'",
        'ÔÇ£' => '"',
        'ÔÇØ' => '"',
        'â€œ' => '"',
        'â€' => '"',
        '“' => '"',
        '”' => '"',
        'Â®' => '®',
        '®' => '®',
        'Ã©' => 'e',
        'Ã¨' => 'e',
        'Ãª' => 'e',
        'Ã¢' => 'a',
        'Ã' => '',
        'Â' => '',
    ];

    return strtr($text, $replacements);
}

function h(?string $v): string
{
    return htmlspecialchars(normalize_display_text((string)$v), ENT_QUOTES, 'UTF-8');
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

function user_collection_storage_available(): bool
{
    static $available = null;

    if ($available !== null) {
        return $available;
    }

    if (!function_exists('db_has_column')) {
        return $available = false;
    }

    try {
        $available = db_has_column('users', 'wishlist_json') && db_has_column('users', 'cart_json');
    } catch (Throwable $e) {
        $available = false;
    }

    return $available;
}

function normalize_wishlist_data($items): array
{
    if (!is_array($items)) {
        return [];
    }

    $clean = [];
    foreach ($items as $item) {
        $id = (int)$item;
        if ($id > 0) {
            $clean[] = $id;
        }
    }

    return array_values(array_unique($clean));
}

function normalize_cart_data($cart): array
{
    if (!is_array($cart)) {
        return [];
    }

    $clean = [];
    foreach ($cart as $productId => $row) {
        if (!is_array($row)) {
            continue;
        }

        $id = (int)$productId;
        if ($id <= 0) {
            $id = (int)($row['id'] ?? 0);
        }
        if ($id <= 0) {
            continue;
        }

        $qty = (int)($row['qty'] ?? 0);
        if ($qty < 1) {
            continue;
        }

        $clean[$id] = [
            'name'  => trim((string)($row['name'] ?? '')),
            'price' => round((float)($row['price'] ?? 0), 2),
            'qty'   => $qty,
        ];
    }

    ksort($clean);
    return $clean;
}

function merge_cart_data(array $storedCart, array $sessionCart): array
{
    $merged = normalize_cart_data($storedCart);

    foreach (normalize_cart_data($sessionCart) as $productId => $row) {
        if (isset($merged[$productId])) {
            $merged[$productId]['qty'] += (int)$row['qty'];
            if ($merged[$productId]['name'] === '' && $row['name'] !== '') {
                $merged[$productId]['name'] = $row['name'];
            }
            if ((float)$merged[$productId]['price'] <= 0 && (float)$row['price'] > 0) {
                $merged[$productId]['price'] = round((float)$row['price'], 2);
            }
        } else {
            $merged[$productId] = $row;
        }
    }

    ksort($merged);
    return $merged;
}

function ensure_user_collection_state_loaded(): void
{
    if (!is_logged_in()) {
        return;
    }

    if (!empty($_SESSION['_user_collection_state_loaded'])) {
        return;
    }

    $_SESSION['_user_collection_state_loaded'] = true;

    if (!user_collection_storage_available() || !function_exists('db')) {
        return;
    }

    $userId = (int)(current_user()['id'] ?? 0);
    if ($userId <= 0) {
        return;
    }

    try {
        $stmt = db()->prepare('SELECT wishlist_json, cart_json FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        if (!$row) {
            return;
        }

        $storedWishlist = normalize_wishlist_data(json_decode((string)($row['wishlist_json'] ?? '[]'), true) ?: []);
        $storedCart     = normalize_cart_data(json_decode((string)($row['cart_json'] ?? '{}'), true) ?: []);
        $sessionWishlist = normalize_wishlist_data($_SESSION['wishlist'] ?? []);
        $sessionCart     = normalize_cart_data($_SESSION['cart'] ?? []);

        $_SESSION['wishlist'] = array_values(array_unique(array_merge($storedWishlist, $sessionWishlist)));
        $_SESSION['cart']     = merge_cart_data($storedCart, $sessionCart);
    } catch (Throwable $e) {
        // Keep the in-session values if the database is unavailable.
    }
}

function persist_user_collection_state(): void
{
    if (!is_logged_in() || !user_collection_storage_available() || !function_exists('db')) {
        return;
    }

    $userId = (int)(current_user()['id'] ?? 0);
    if ($userId <= 0) {
        return;
    }

    try {
        $wishlistJson = json_encode(normalize_wishlist_data($_SESSION['wishlist'] ?? []), JSON_UNESCAPED_UNICODE);
        $cartJson     = json_encode(normalize_cart_data($_SESSION['cart'] ?? []), JSON_UNESCAPED_UNICODE);

        db()->prepare('UPDATE users SET wishlist_json = ?, cart_json = ? WHERE id = ?')->execute([
            $wishlistJson ?: '[]',
            $cartJson ?: '{}',
            $userId,
        ]);
    } catch (Throwable $e) {
        // Ignore persistence failures in demo mode.
    }
}

function cart_items(): array
{
    ensure_user_collection_state_loaded();

    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $_SESSION['cart'] = normalize_cart_data($_SESSION['cart']);
    return $_SESSION['cart'];
}

function cart_add_product(array $product, int $qty = 1): void
{
    ensure_user_collection_state_loaded();

    $productId = (int)($product['id'] ?? 0);
    if ($productId <= 0) {
        return;
    }

    $qty  = max(1, $qty);
    $cart = cart_items();

    if (!isset($cart[$productId])) {
        $cart[$productId] = [
            'name'  => (string)($product['name'] ?? ''),
            'price' => round((float)($product['price'] ?? 0), 2),
            'qty'   => 0,
        ];
    }

    $cart[$productId]['qty'] += $qty;
    $_SESSION['cart'] = normalize_cart_data($cart);
    persist_user_collection_state();
}

function cart_set_quantity(int $productId, int $qty): void
{
    ensure_user_collection_state_loaded();

    $productId = (int)$productId;
    if ($productId <= 0) {
        return;
    }

    $cart = cart_items();
    if (!isset($cart[$productId])) {
        return;
    }

    if ($qty < 1) {
        unset($cart[$productId]);
    } else {
        $cart[$productId]['qty'] = $qty;
    }

    $_SESSION['cart'] = normalize_cart_data($cart);
    persist_user_collection_state();
}

function cart_remove_product(int $productId): void
{
    ensure_user_collection_state_loaded();

    $cart = cart_items();
    unset($cart[(int)$productId]);
    $_SESSION['cart'] = normalize_cart_data($cart);
    persist_user_collection_state();
}

function cart_clear(): void
{
    ensure_user_collection_state_loaded();
    $_SESSION['cart'] = [];
    persist_user_collection_state();
}

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


function referral_code_for_user(?array $user): string
{
    if (!$user) {
        return 'MEOWFRIEND';
    }

    $name = strtoupper(preg_replace('/[^A-Z0-9]/', '', (string)($user['name'] ?? 'MEOW')));
    $name = substr($name ?: 'MEOW', 0, 4);
    $id   = str_pad((string)((int)($user['id'] ?? 0)), 4, '0', STR_PAD_LEFT);
    $hash = strtoupper(substr(sha1((string)($user['email'] ?? $name)), 0, 4));

    return $name . $id . $hash;
}

function referral_link_for_user(?array $user): string
{
    return base_url('account/register.php?ref=' . rawurlencode(referral_code_for_user($user)));
}


function product_icon_catalog(): array
{
    return [
        1  => 'p1-salmon-pate.svg',
        2  => 'p2-litter-bag.svg',
        3  => 'p3-feather-wand.svg',
        4  => 'p4-collar-bowtie.svg',
        5  => 'p5-chicken-treats.svg',
        6  => 'p6-litter-box.svg',
        7  => 'p7-laser-toy.svg',
        8  => 'p8-hoodie.svg',
        9  => 'p9-mousse-multipack.svg',
        10 => 'p10-tofu-litter.svg',
        11 => 'p11-cat-tree.svg',
        12 => 'p12-feeder-bowls.svg',
        13 => 'p13-donut-bed.svg',
        14 => 'p14-crinkle-balls.svg',
        15 => 'p15-raincoat.svg',
    ];
}

function product_icon_filename_from_text(string $text, string $category = ''): string
{
    $haystack = strtolower(trim($text . ' ' . $category));

    $keywordFiles = [
        'salmon'        => 'p1-salmon-pate.svg',
        'pâté'          => 'p1-salmon-pate.svg',
        'pate'          => 'p1-salmon-pate.svg',
        'lavender'      => 'p2-litter-bag.svg',
        'clumping'      => 'p2-litter-bag.svg',
        'feather'       => 'p3-feather-wand.svg',
        'wand'          => 'p3-feather-wand.svg',
        'bow tie'       => 'p4-collar-bowtie.svg',
        'collar'        => 'p4-collar-bowtie.svg',
        'chicken'       => 'p5-chicken-treats.svg',
        'treat'         => 'p5-chicken-treats.svg',
        'self-cleaning' => 'p6-litter-box.svg',
        'litter box'    => 'p6-litter-box.svg',
        'laser'         => 'p7-laser-toy.svg',
        'chase'         => 'p7-laser-toy.svg',
        'hoodie'        => 'p8-hoodie.svg',
        'mousse'        => 'p9-mousse-multipack.svg',
        'tuna'          => 'p9-mousse-multipack.svg',
        'prawn'         => 'p9-mousse-multipack.svg',
        'shrimp'        => 'p9-mousse-multipack.svg',
        'tofu'          => 'p10-tofu-litter.svg',
        'tree'          => 'p11-cat-tree.svg',
        'scratching'    => 'p11-cat-tree.svg',
        'post'          => 'p11-cat-tree.svg',
        'feeder'        => 'p12-feeder-bowls.svg',
        'bowl'          => 'p12-feeder-bowls.svg',
        'bed'           => 'p13-donut-bed.svg',
        'donut'         => 'p13-donut-bed.svg',
        'crinkle'       => 'p14-crinkle-balls.svg',
        'ball'          => 'p14-crinkle-balls.svg',
        'raincoat'      => 'p15-raincoat.svg',
        'waterproof'    => 'p15-raincoat.svg',
    ];

    foreach ($keywordFiles as $keyword => $file) {
        if ($keyword !== '' && strpos($haystack, strtolower($keyword)) !== false) {
            return $file;
        }
    }

    $fallbacks = [
        'food'        => ['p1-salmon-pate.svg', 'p5-chicken-treats.svg', 'p9-mousse-multipack.svg'],
        'litter'      => ['p2-litter-bag.svg', 'p6-litter-box.svg', 'p10-tofu-litter.svg'],
        'toys'        => ['p3-feather-wand.svg', 'p7-laser-toy.svg', 'p14-crinkle-balls.svg'],
        'apparel'     => ['p4-collar-bowtie.svg', 'p8-hoodie.svg', 'p15-raincoat.svg'],
        'accessories' => ['p11-cat-tree.svg', 'p12-feeder-bowls.svg', 'p13-donut-bed.svg'],
    ];

    $cat = strtolower(trim($category));
    if (isset($fallbacks[$cat])) {
        $files = $fallbacks[$cat];
        $index = abs(crc32($haystack)) % count($files);
        return $files[$index];
    }

    return 'p14-crinkle-balls.svg';
}

function product_icon_filename_for(?array $product): string
{
    if (!$product) {
        return 'p14-crinkle-balls.svg';
    }

    $catalog = product_icon_catalog();
    $id = (int)($product['id'] ?? 0);
    if ($id > 0 && isset($catalog[$id])) {
        return $catalog[$id];
    }

    return product_icon_filename_from_text(
        ($product['name'] ?? '') . ' ' . ($product['description'] ?? ''),
        $product['category'] ?? ''
    );
}

function product_icon_markup(string $filename): string
{
    $src = base_url('assets/img/products/' . rawurlencode($filename));
    return '<img src="' . h($src) . '" alt="" aria-hidden="true" class="product-icon-asset" loading="lazy" decoding="async" style="width:72%;height:72%;object-fit:contain;display:block;">';
}

function product_icon_for(?array $product): string
{
    return product_icon_markup(product_icon_filename_for($product));
}

function product_icon_from_text(string $text, string $category = ''): string
{
    return product_icon_markup(product_icon_filename_from_text($text, $category));
}

function wishlist_items(): array
{
    ensure_user_collection_state_loaded();

    if (!isset($_SESSION['wishlist']) || !is_array($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = [];
    }

    $_SESSION['wishlist'] = normalize_wishlist_data($_SESSION['wishlist']);
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
        persist_user_collection_state();
        return false;
    }
    $items[] = $productId;
    $_SESSION['wishlist'] = normalize_wishlist_data($items);
    persist_user_collection_state();
    return true;
}

function wishlist_count(): int { return count(wishlist_items()); }

function cart_count(): int
{
    return array_sum(array_column(cart_items(), 'qty'));
}

function cart_total(): float
{
    $total = 0;
    foreach (cart_items() as $row) {
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


function sanitize_referral_code(string $code): string
{
    $code = strtoupper(trim($code));
    $code = preg_replace('/[^A-Z0-9\-]/', '', $code);
    return substr($code, 0, 32);
}

function coupon_discount_details(float $subtotal, string $couponCode): array
{
    $couponCode = strtoupper(trim($couponCode));

    if ($couponCode === '') {
        return [
            'entered_code' => '',
            'applied_code' => '',
            'discount' => 0.0,
            'error' => '',
            'success' => '',
        ];
    }

    if ($couponCode !== 'MEOW10') {
        return [
            'entered_code' => $couponCode,
            'applied_code' => '',
            'discount' => 0.0,
            'error' => 'That voucher code is not recognised.',
            'success' => '',
        ];
    }

    if ($subtotal < 60) {
        return [
            'entered_code' => $couponCode,
            'applied_code' => '',
            'discount' => 0.0,
            'error' => 'MEOW10 applies to orders of $60 or more.',
            'success' => '',
        ];
    }

    $discount = round($subtotal * 0.10, 2);
    return [
        'entered_code' => $couponCode,
        'applied_code' => 'MEOW10',
        'discount' => $discount,
        'error' => '',
        'success' => 'MEOW10 applied. You saved ' . money($discount) . '.',
    ];
}
