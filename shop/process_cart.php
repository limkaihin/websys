<?php
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('shop/cart.php');
}

$action = strtolower(trim((string)($_POST['action'] ?? 'add')));
$pid = (int)($_POST['product_id'] ?? $_POST['id'] ?? 0);
$qty = max(1, (int)($_POST['qty'] ?? 1));
$redirectTarget = trim((string)($_POST['redirect'] ?? $_POST['return_to'] ?? 'shop/cart.php'));

if ($action === 'clear') {
    cart_clear();
    set_flash('success', 'Cart cleared.');
    redirect('shop/cart.php');
}

if ($pid > 0) {
    if ($action === 'remove') {
        cart_remove_product($pid);
        set_flash('success', 'Item removed from cart.');
        redirect('shop/cart.php');
    }

    if ($action === 'update') {
        cart_set_quantity($pid, $qty);
        set_flash('success', 'Cart updated.');
        redirect('shop/cart.php');
    }

    $stmt = db()->prepare('SELECT id, name, price FROM products WHERE id = ?');
    $stmt->execute([$pid]);
    $product = $stmt->fetch();
    if ($product) {
        cart_add_product($product, $qty);
        set_flash('success', $product['name'] . ' added to cart! 🛒');
        redirect($redirectTarget);
    }
}

set_flash('error', 'Unable to process cart action.');
redirect('shop/cart.php');
