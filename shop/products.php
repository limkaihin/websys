<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../inc/head.inc.php"; ?>
  <title>Shop – MeowMart</title>
</head>
<body>

<?php include "../inc/nav.inc.php"; ?>

<?php
$config = parse_ini_file('/var/www/private/db-config.ini');
$conn   = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);

$cat    = isset($_GET['cat']) ? trim($_GET['cat']) : '';
$search = isset($_GET['q'])   ? trim($_GET['q'])   : '';

$where  = "1=1";
$params = [];
$types  = "";

if ($cat) {
    $where   .= " AND LOWER(category) = ?";
    $params[] = strtolower($cat);
    $types   .= "s";
}
if ($search) {
    $where   .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types   .= "ss";
}

$stmt = $conn->prepare("SELECT * FROM products WHERE $where ORDER BY name");
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

$categories = [
    ['slug'=>'food',        'label'=>'Food',        'icon'=>'🥩'],
    ['slug'=>'litter',      'label'=>'Litter',       'icon'=>'🧴'],
    ['slug'=>'toys',        'label'=>'Toys',         'icon'=>'🧶'],
    ['slug'=>'apparel',     'label'=>'Apparel',      'icon'=>'👗'],
    ['slug'=>'accessories', 'label'=>'Accessories',  'icon'=>'🎀'],
];
$icons = ['food'=>'🥩','litter'=>'🧴','toys'=>'🧶','apparel'=>'👗','accessories'=>'🎀'];
?>

<section class="products" style="min-height:70vh;">
  <div class="products-toolbar">
    <div class="section-header" style="text-align:left;margin-bottom:0;">
      <div class="section-tag">🛒 Browse</div>
      <h1 class="section-title">Shop <em>Everything</em></h1>
    </div>
    <div class="filter-pills">
      <a class="pill <?= !$cat ? 'active':'' ?>"
         href="/shop/products.php" style="text-decoration:none;">All</a>
      <?php foreach ($categories as $c): ?>
        <a class="pill <?= $cat===$c['slug']?'active':'' ?>"
           href="/shop/products.php?cat=<?= $c['slug'] ?>"
           style="text-decoration:none;">
          <?= $c['icon'].' '.htmlspecialchars($c['label']) ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if (empty($products)): ?>
    <p style="text-align:center;padding:60px;color:var(--brown-md);">
      No products found.
      <a href="/shop/products.php" style="color:var(--orange);">Browse all →</a>
    </p>
  <?php else: ?>
  <div class="products-grid">
    <?php foreach ($products as $p):
      $icon = $icons[strtolower($p['category'])] ?? '🐾';
    ?>
      <div class="product-card">
        <div class="product-img">
          <span><?= $icon ?></span>
          <?php if ($p['is_featured']): ?>
            <div class="ribbon">Featured</div>
          <?php endif; ?>
          <button class="wishlist">🤍</button>
        </div>
        <div class="product-body">
          <div class="product-brand"><?= htmlspecialchars($p['category']) ?></div>
          <h3 class="product-name"><?= htmlspecialchars($p['name']) ?></h3>
          <div class="product-stars">⭐⭐⭐⭐⭐ <span class="count">(<?= rand(50,500) ?>)</span></div>
          <div class="product-footer">
            <div class="product-price">$<?= number_format((float)$p['price'],2) ?></div>
            <a href="/shop/product.php?id=<?= $p['id'] ?>"
               class="btn-cart"
               style="text-decoration:none;display:flex;align-items:center;justify-content:center;">🛒</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<?php include "../inc/footer.inc.php"; ?>
</body>
</html>
