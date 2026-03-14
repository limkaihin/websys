<?php
require_once __DIR__ . '/functions.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $c   = config();
        $dsn = "mysql:host=127.0.0.1;dbname=meowmart;charset=utf8mb4";
        $pdo = new PDO($dsn, 'root', '8569', [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}
