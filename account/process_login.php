<?php
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('account/login.php');
}

$email = trim((string)($_POST['email'] ?? ''));
$password = (string)($_POST['password'] ?? $_POST['pwd'] ?? '');
if ($email === '' || $password === '') {
    set_flash('error', 'Please enter your email and password.');
    redirect('account/login.php');
}

$stmt = db()->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();
if (!$user || !password_verify($password, $user['password'])) {
    set_flash('error', 'Incorrect email or password.');
    redirect('account/login.php');
}

session_regenerate_id(true);
$_SESSION['user'] = [
    'id'       => $user['id'],
    'name'     => $user['name'],
    'email'    => $user['email'],
    'cat_name' => $user['cat_name'],
    'role'     => $user['role'],
];
set_flash('success', 'Welcome back, ' . $user['name'] . '! 🐾');
redirect('index.php');
