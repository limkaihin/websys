<?php
return [
    // ─── Database ───────────────────────────────────
    'db_host'   => '127.0.0.1',
    'db_name'   => 'meowmart',
    'db_user'   => 'root',
    'db_pass'   => '',

    // ─── Site ───────────────────────────────────────
    'site_name' => 'MeowMart',
    'base_url'  => '',          // e.g. '/meowmart' if site is in a subfolder

    // ─── Email (PHPMailer) ──────────────────────────
    // Set 'mail_enabled' to true and fill in your SMTP credentials.
    // Using SMTP avoids the shell-injection risk of PHP's mail() function.
    'mail_enabled'   => false,
    'mail_host'      => 'smtp.gmail.com',   // your SMTP server
    'mail_port'      => 587,                // 587 = STARTTLS, 465 = SSL
    'mail_username'  => 'your@gmail.com',
    'mail_password'  => 'your_app_password',
    'mail_from'      => 'no-reply@meowmart.com.sg',
    'mail_from_name' => 'MeowMart',
    'mail_encryption'=> 'tls',              // 'tls' or 'ssl'

    // ─── Session (Zebra_Session) ────────────────────
    // Set 'session_db' to true to store sessions in MySQL instead of files.
    // Recommended for multi-server deployments or shared hosting.
    'session_db'           => false,
    'session_lifetime'     => 3600,         // seconds (1 hour)
    'session_security_code'=> 'change_this_to_a_long_random_string_xyz_123',
    'session_table'        => 'session_data',
];
