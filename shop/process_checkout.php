<?php
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/db.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('shop/checkout.php');
}
if (empty(cart_items())) {
    set_flash('error', 'Your cart is empty.');
    redirect('shop/cart.php');
}

$cart         = cart_items();
$subtotal     = cart_total();
$nameParts    = array_filter([trim((string)($_POST['name'] ?? '')), trim((string)($_POST['fname'] ?? '')), trim((string)($_POST['lname'] ?? ''))]);
$name         = trim(implode(' ', $nameParts));
$email        = trim((string)($_POST['email'] ?? ''));
$addressPieces = array_filter([trim((string)($_POST['address'] ?? '')), trim((string)($_POST['addr1'] ?? '')), trim((string)($_POST['addr2'] ?? '')), trim((string)($_POST['postal'] ?? ''))]);
$address      = trim(implode(', ', $addressPieces));
$payment      = trim((string)($_POST['payment'] ?? 'card'));
$couponCode   = strtoupper(trim((string)($_POST['coupon_code'] ?? '')));
$referralCode = sanitize_referral_code((string)($_POST['referral_code'] ?? ''));

$allowedPayments = ['card', 'paynow', 'gpay'];
if (!in_array($payment, $allowedPayments, true)) $payment = 'card';

if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($address) < 5) {
    set_flash('error', 'Please complete your delivery details.');
    redirect('shop/checkout.php');
}

$couponInfo = coupon_discount_details($subtotal, $couponCode);
if ($couponCode !== '' && $couponInfo['error'] !== '') {
    set_flash('error', $couponInfo['error']);
    redirect('shop/checkout.php');
}

$paymentReference = '';
if ($payment === 'card') {
    $cardNumber = preg_replace('/\D+/', '', (string)($_POST['card_number'] ?? ''));
    $cardExpiry = trim((string)($_POST['card_expiry'] ?? ''));
    $cardCvv = preg_replace('/\D+/', '', (string)($_POST['card_cvv'] ?? ''));
    if (!preg_match('/^\d{16}$/', $cardNumber)) {
        set_flash('error', 'Card number must be exactly 16 digits.');
        redirect('shop/checkout.php');
    }
    if (!preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $cardExpiry)) {
        set_flash('error', 'Use MM/YY format for the card expiry date.');
        redirect('shop/checkout.php');
    }
    if (!preg_match('/^\d{3}$/', $cardCvv)) {
        set_flash('error', 'CVV must be exactly 3 digits.');
        redirect('shop/checkout.php');
    }
    $paymentReference = 'Card ending ' . substr($cardNumber, -4);
} elseif ($payment === 'paynow') {
    $paynowPhone = preg_replace('/\D+/', '', (string)($_POST['paynow_phone'] ?? ''));
    $paymentReference = $paynowPhone ? 'PayNow ' . $paynowPhone : 'PayNow payment';
} else {
    $gpayEmail = trim((string)($_POST['gpay_email'] ?? ''));
    $paymentReference = $gpayEmail ? 'Google Pay ' . $gpayEmail : 'Google Pay payment';
}

$total = max(0, $subtotal - (float)$couponInfo['discount']);
$user  = current_user();
$pdo   = db();

$columns = ['user_id', 'name', 'email', 'address', 'payment', 'total', 'status'];
$values  = [$user['id'] ?? null, $name, $email, $address, $payment, $total, 'confirmed'];
foreach ([
    'coupon_code'       => ($couponInfo['applied_code'] !== '' ? $couponInfo['applied_code'] : null),
    'discount'          => (float)$couponInfo['discount'],
    'referral_code'     => ($referralCode !== '' ? $referralCode : null),
    'payment_reference' => $paymentReference,
] as $column => $value) {
    if (db_has_column('orders', $column)) {
        $columns[] = $column;
        $values[]  = $value;
    }
}

$pdo->prepare('INSERT INTO orders (' . implode(', ', $columns) . ') VALUES (' . implode(', ', array_fill(0, count($columns), '?')) . ')')->execute($values);
$orderId = (int)$pdo->lastInsertId();
$itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, name, price, qty) VALUES (?, ?, ?, ?, ?)');
foreach ($cart as $productId => $row) {
    $itemStmt->execute([$orderId, (int)$productId, $row['name'], (float)$row['price'], (int)$row['qty']]);
}

$_SESSION['order_meta'][$orderId] = [
    'subtotal'          => $subtotal,
    'discount'          => (float)$couponInfo['discount'],
    'coupon_code'       => $couponInfo['applied_code'],
    'referral_code'     => $referralCode,
    'payment_reference' => $paymentReference,
];

cart_clear();
set_flash('success', 'Order #' . $orderId . ' confirmed!');
redirect('shop/order_confirmation.php?id=' . $orderId);
