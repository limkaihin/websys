<?php
require_once dirname(__DIR__) . '/includes/functions.php';
$query = [];
if (isset($_GET['tag']) && trim((string)$_GET['tag']) !== '') {
    $query['tag'] = trim((string)$_GET['tag']);
}
$target = 'content/blog.php';
if ($query) $target .= '?' . http_build_query($query);
redirect($target);
