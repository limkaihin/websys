<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$action   = $_POST['action']   ?? '';
$redirect = $_POST['redirect'] ?? '/shop/cart.php';

if ($action === 'add') {
    $id    = (int)   ($_POST['id']    ?? 0);
    $name  = trim(   $_POST['name']   ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $icon  = trim(   $_POST['icon']   ?? '🐾');
    $qty   = max(1, (int)($_POST['qty'] ?? 1));

    if ($id && $name && $price > 0) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$id] = ['id'=>$id,'name'=>$name,'price'=>$price,'icon'=>$icon,'qty'=>$qty];
        }
    }
}

if ($action === 'update') {
    $id  = (int)($_POST['id']  ?? 0);
    $qty = (int)($_POST['qty'] ?? 0);
    if ($id && isset($_SESSION['cart'][$id])) {
        if ($qty <= 0) unset($_SESSION['cart'][$id]);
        else $_SESSION['cart'][$id]['qty'] = $qty;
    }
    $redirect = '/shop/cart.php';
}

if ($action === 'remove') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) unset($_SESSION['cart'][$id]);
    $redirect = '/shop/cart.php';
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    $redirect = '/shop/cart.php';
}

header("Location: $redirect");
exit;
