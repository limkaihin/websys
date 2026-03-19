<?php
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/db.php';

_init_session(db());
ensure_user_collection_state_loaded();
persist_user_collection_state();

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

session_destroy();
session_start();
set_flash('success', 'You have been logged out. See you soon! 🐾');
redirect('index.php');
