<?php
require_once dirname(__DIR__) . '/includes/functions.php';
$slug = strtolower(trim((string)($_GET['slug'] ?? '')));
$params = [];
if ($slug !== '') $params['cat'] = $slug;
if (!empty($_GET['sort'])) $params['sort'] = trim((string)$_GET['sort']);
if (!empty($_GET['q'])) $params['q'] = trim((string)$_GET['q']);
$target = 'shop/products.php';
if ($params) $target .= '?' . http_build_query($params);
redirect($target);
