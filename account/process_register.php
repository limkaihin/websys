<?php
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/mail.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('account/register.php');
}

$name = trim((string)($_POST['name'] ?? trim(((string)($_POST['fname'] ?? '')) . ' ' . ((string)($_POST['lname'] ?? '')))));
$email = trim((string)($_POST['email'] ?? ''));
$catName = trim((string)($_POST['cat_name'] ?? ''));
$password = (string)($_POST['password'] ?? $_POST['pwd'] ?? '');
$confirm = (string)($_POST['confirm'] ?? $_POST['pwd_confirm'] ?? '');
$referralCode = sanitize_referral_code((string)($_POST['referral_code'] ?? $_POST['ref'] ?? ''));

if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8 || $password !== $confirm) {
    set_flash('error', 'Please check your registration details and try again.');
    redirect('account/register.php');
}

$pdo = db();
$exists = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$exists->execute([$email]);
if ($exists->fetch()) {
    set_flash('error', 'An account with this email already exists.');
    redirect('account/register.php');
}

$columns = ['name', 'email', 'cat_name', 'password', 'role'];
$values  = [$name, $email, $catName, password_hash($password, PASSWORD_BCRYPT), 'member'];
if (db_has_column('users', 'referred_by')) {
    $columns[] = 'referred_by';
    $values[]  = $referralCode !== '' ? $referralCode : null;
}
$placeholders = implode(',', array_fill(0, count($columns), '?'));
$pdo->prepare('INSERT INTO users (' . implode(',', $columns) . ') VALUES (' . $placeholders . ')')->execute($values);

$html = "<p>Hi <strong>" . h($name) . "</strong>, welcome to the MeowClub family! 🐾</p>"
      . ($referralCode !== '' ? "<p>Your account was created with referral code <strong>" . h($referralCode) . "</strong>.</p>" : '')
      . "<p>Your account is ready. You can now shop, track orders and save favourites.</p>"
      . "<a class='btn' href='" . base_url('shop/products.php') . "'>Start Shopping →</a>";
send_mail($email, $name, 'Welcome to MeowClub! 🐾', $html);
set_flash('success', 'Welcome to MeowClub, ' . $name . '! Please log in.');
redirect('account/login.php');
