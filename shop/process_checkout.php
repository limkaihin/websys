<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['cart'])) { header("Location: /shop/cart.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: /shop/checkout.php"); exit; }

$required = ['fname','lname','email','addr1','postal'];
foreach ($required as $f) {
    if (empty(trim($_POST[$f] ?? ''))) {
        header("Location: /shop/checkout.php?error=missing_fields"); exit;
    }
}

// Clear cart after "payment"
$_SESSION['cart'] = [];
header("Location: /shop/checkout.php?success=1");
exit;
