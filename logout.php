<?php
require_once __DIR__ . '/includes/functions.php';
session_destroy();
session_start();
set_flash('success', 'You have been logged out. See you soon! 🐾');
redirect('index.php');
