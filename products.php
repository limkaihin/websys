<?php
$pageTitle = 'Shop All Products';
require_once __DIR__ . '/includes/header.php';
$pdo = db();

$cat    = isset($_GET['cat']) ? trim($_GET['cat']) : '';
$search = isset($_GET['q'])   ? trim($_GET['q'])   : '';

$params = [];
$where  = ['1=1'];
if ($cat)    { $where[] = 'LOWER(category) = ?'; $params[] = strtolower($cat); }
if ($search) { $where[] = '(name LIKE ? OR description LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }

$sql      = 'SELECT * FROM products WHERE ' . implode(' AND ', $where) . ' ORDER BY name';
$stmt     = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = [
    ['slug'=>'food',        'label'=>'Food',         'icon'=>'🥩'],
    ['slug'=>'litter',      'label'=>'Litter',        'icon'=>'🧴'],
    ['slug'=>'toys',        'label'=>'Toys',          'icon'=>'🧶'],
    ['slug'=>'apparel',     'label'=>'Apparel',       'icon'=>'👗'],
    ['slug'=>'accessories', 'label'=>'Accessories',   'icon'=>'🎀'],
];
$emojiMap = ['food'=>'🥩','litter'=>'🧴','toys'=>'🧶','apparel'=>'👗','accessories'=>'🎀'];
?>

<section class="products" style="min-height:70vh;">
  <div class="products-toolbar">
    <div class="section-header" style="text-align:left;margin-bottom:0;">
      <div class="section-tag">🛒 Browse</div>
      <h1 class="section-title">Shop <em>Everything</em></h1>
    </div>
    <div class="filter-pills">
      <a class="pill <?= !$cat ? 'active' : '' ?>" href="<?= h(base_url('products.php')) ?>">All</a>
      <?php foreach ($categories as $c): ?>
        <a class="pill <?= $cat === $c['slug'] ? 'active' : '' ?>"
           href="<?= h(base_url('products.php?cat=' . $c['slug'])) ?>"
           style="text-decoration:none;">
          <?= $c['icon'] . ' ' . h($c['label']) ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if (empty($products)): ?>
    <p style="text-align:center;padding:60px;color:var(--brown-md);font-size:1.1rem;">
      No products found. <a href="<?= h(base_url('products.php')) ?>" style="color:var(--orange);">Browse all →</a>
    </p>
  <?php else: ?>
  <div class="products-grid">
    <?php foreach ($products as $p):
      $icon = $emojiMap[strtolower($p['category'])] ?? '🐾';
    ?>
      <div class="product-card">
        <div class="product-img">
          <span><?= $icon ?></span>
          <?php if ($p['is_featured']): ?><div class="ribbon">Featured</div><?php endif; ?>
          <button class="wishlist">🤍</button>
        </div>
        <div class="product-body">
          <div class="product-brand"><?= h($p['category']) ?></div>
          <h3 class="product-name"><?= h($p['name']) ?></h3>
          <div class="product-stars">⭐⭐⭐⭐⭐ <span class="count">(<?= rand(50,500) ?>)</span></div>
          <div class="product-footer">
            <div class="product-price"><?= money((float)$p['price']) ?></div>
            <a href="<?= h(base_url('product.php?id=' . $p['id'])) ?>"
               class="btn-cart" style="text-decoration:none;display:flex;align-items:center;justify-content:center;">🛒</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
