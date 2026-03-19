<?php
require_once __DIR__ . '/functions.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $c   = config();
        $dsn = "mysql:host={$c['db_host']};dbname={$c['db_name']};charset=utf8mb4";
        $pdo = new PDO($dsn, $c['db_user'], $c['db_pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

function db_table_columns(string $table): array
{
    static $cache = [];
    if (isset($cache[$table])) {
        return $cache[$table];
    }

    $pdo = db();
    try {
        $stmt = $pdo->query('SHOW COLUMNS FROM `' . str_replace('`', '', $table) . '`');
        $rows = $stmt->fetchAll();
        $cache[$table] = array_map(static fn(array $row): string => (string)$row['Field'], $rows);
    } catch (Throwable $e) {
        $cache[$table] = [];
    }

    return $cache[$table];
}

function db_has_column(string $table, string $column): bool
{
    return in_array($column, db_table_columns($table), true);
}
