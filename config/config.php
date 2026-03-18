<?php
return [
    // ─── Database ───────────────────────────────────
    'db_host'   => '127.0.0.1',
    'db_name'   => 'meowmart',
    'db_user'   => 'root',
    'db_pass'   => '',

    // ─── Site ───────────────────────────────────────
    'site_name' => 'MeowMart',
    'base_url'  => '',

    // ─── Email (PHPMailer) ──────────────────────────
    'mail_enabled'   => false,
    'mail_host'      => 'smtp.gmail.com',
    'mail_port'      => 587,
    'mail_username'  => 'your@gmail.com',
    'mail_password'  => 'your_app_password',
    'mail_from'      => 'no-reply@meowmart.com.sg',
    'mail_from_name' => 'MeowMart',
    'mail_encryption'=> 'tls',

    // ─── Session (Zebra_Session) ────────────────────
    'session_db'           => false,
    'session_lifetime'     => 3600,
    'session_security_code'=> 'meowmart_session_secret_xyz_2025',
    'session_table'        => 'session_data',
];
