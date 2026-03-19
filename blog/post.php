<?php
require_once dirname(__DIR__) . '/includes/functions.php';
$id = (int)($_GET['id'] ?? 0);
$target = 'content/blog_post.php';
if ($id > 0) $target .= '?id=' . $id;
redirect($target);
