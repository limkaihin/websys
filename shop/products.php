<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../inc/head.inc.php"; ?>
  <title>Shop All Products – MeowMart</title>
</head>
<body>
<?php include "../inc/nav.inc.php"; ?>
<?php
include "../inc/db.inc.php";

$cat    = isset($_GET['cat'])  ? trim($_GET['cat'])  : '';
$search = isset($_GET['q'])    ? trim($_GET['q'])    : '';
$sort   = isset($_GET['sort']) ? trim($_GET['sort']) : 'name';

$products = filterProducts($cat, $search, $sort);

$categories = [
  ['slug'=>'food',        'label'=>'Food',        'icon'=>'🥩'],
  ['slug'=>'litter',      'label'=>'Litter',       'icon'=>'🧴'],
  ['slug'=>'toys',        'label'=>'Toys',         'icon'=>'🧶'],
  ['slug'=>'apparel',     'label'=>'Apparel',      'icon'=>'👗'],
  ['slug'=>'accessories', 'label'=>'Accessories',  'icon'=>'🎀'],
];
$icons  = ['food'=>'🥩','litter'=>'🧴','toys'=>'🧶','apparel'=>'👗','accessories'=>'🎀'];
$bgCols = ['food'=>'#FDE8D0','litter'=>'#D6E8D8','toys'=>'#D8E0F0','apparel'=>'#F4E8F0','accessories'=>'#FFF4D6'];
?>

<!-- BREADCRUMB -->
<div style="background:var(--warm);padding:14px 5%;border-bottom:1.5px solid var(--cream);">
  <p style="font-size:.82rem;color:var(--brown-md);max-width:1200px;margin:0 auto;">
    <a href="/index.php" style="color:var(--brown-md);text-decoration:none;">Home</a> ›
    <span style="color:var(--orange);font-weight:600;">
      <?= $cat ? htmlspecialchars(ucfirst($cat)) : ($search ? 'Search: '.htmlspecialchars($search) : 'All Products') ?>
    </span>
  </p>
</div>

<section class="products" style="min-height:70vh;">
  <div class="products-toolbar">
    <div class="section-header" style="text-align:left;margin-bottom:0;">
      <div class="section-tag">🛒 Browse</div>
      <h1 class="section-title">Shop <em>Everything</em></h1>
    </div>
    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
      <!-- Search form -->
      <form method="GET" action="/shop/products.php" style="display:flex;gap:8px;">
        <?php if ($cat): ?>
          <input type="hidden" name="cat" value="<?= htmlspecialchars($cat) ?>">
        <?php endif; ?>
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
               placeholder="Search products…"
               style="border:1.5px solid var(--warm);border-radius:30px;padding:9px 18px;font-family:inherit;font-size:.85rem;color:var(--brown);background:#fff;outline:none;width:200px;">
        <button type="submit"
                style="background:var(--brown);color:var(--cream);border:none;border-radius:30px;padding:9px 18px;cursor:pointer;font-size:.85rem;font-weight:600;">Search</button>
      </form>
      <!-- Sort -->
      <select onchange="location.href=this.value"
              style="border:1.5px solid var(--warm);border-radius:30px;padding:9px 18px;font-family:inherit;font-size:.85rem;font-weight:600;color:var(--brown);background:#fff;cursor:pointer;outline:none;">
        <?php foreach (['name'=>'Name A–Z','price_asc'=>'Price ↑','price_desc'=>'Price ↓','featured'=>'Featured First'] as $v=>$l): ?>
          <option value="/shop/products.php?<?= $cat ? 'cat='.urlencode($cat).'&' : '' ?><?= $search ? 'q='.urlencode($search).'&' : '' ?>sort=<?= $v ?>"
                  <?= $sort===$v ? 'selected' : '' ?>><?= $l ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <!-- Category pills -->
  <div style="padding:0 5% 24px;display:flex;gap:8px;flex-wrap:wrap;">
    <a class="pill <?= !$cat ? 'active' : '' ?>" href="/shop/products.php" style="text-decoration:none;">All</a>
    <?php foreach ($categories as $c): ?>
      <a class="pill <?= strtolower($cat)===$c['slug'] ? 'active' : '' ?>"
         href="/shop/products.php?cat=<?= $c['slug'] ?>"
         style="text-decoration:none;"><?= $c['icon'].' '.htmlspecialchars($c['label']) ?></a>
    <?php endforeach; ?>
  </div>

  <!-- Results info -->
  <?php if ($search || $cat): ?>
  <div style="padding:0 5% 16px;font-size:.88rem;color:var(--brown-md);">
    <?= count($products) ?> result<?= count($products)!==1 ? 's' : '' ?>
    <?= $search ? ' for "<strong>'.htmlspecialchars($search).'</strong>"' : '' ?>
    <?= $cat ? ' in <strong>'.htmlspecialchars(ucfirst($cat)).'</strong>' : '' ?>
    <?php if ($search || $cat): ?> · <a href="/shop/products.php" style="color:var(--orange);">Clear filters</a><?php endif; ?>
  </div>
  <?php endif; ?>

  <?php if (empty($products)): ?>
    <div style="text-align:center;padding:80px 5%;background:var(--warm);border-radius:24px;margin:0 5%;">
      <div style="font-size:4rem;margin-bottom:16px;">🔍</div>
      <h3 style="font-family:'Playfair Display',serif;font-size:1.6rem;margin-bottom:10px;">No products found</h3>
      <p style="color:var(--brown-md);margin-bottom:24px;">Try a different search term or browse all categories.</p>
      <a href="/shop/products.php" class="btn-primary" style="text-decoration:none;display:inline-block;padding:12px 28px;">Browse All Products</a>
    </div>
  <?php else: ?>
  <div class="products-grid">
    <?php foreach ($products as $p):
      $pSlug = strtolower($p['category']);
      $pIcon = $icons[$pSlug]  ?? '🐾';
      $pBg   = $bgCols[$pSlug] ?? '#F2E8D9';
    ?>
    <div class="product-card">
      <div class="product-img" style="background:<?= $pBg ?>;">
        <span><?= $pIcon ?></span>
        <?php if ($p['is_featured']): ?><div class="ribbon">Featured</div><?php endif; ?>
          
      </div>
      <div class="product-body">
        <div class="product-brand"><?= htmlspecialchars($p['category']) ?></div>
        <h3 class="product-name"><?= htmlspecialchars($p['name']) ?></h3>
        <div class="product-stars">⭐⭐⭐⭐⭐ <span class="count">(<?= (($p['id'] * 47 + 83) % 450) + 50 ?>)</span></div>
        <div class="product-footer">
          <div class="product-price">$<?= number_format($p['price'],2) ?></div>
          <a href="/shop/product.php?id=<?= $p['id'] ?>"
             class="btn-cart" style="text-decoration:none;display:flex;align-items:center;justify-content:center;" title="View Product">🛒</a>
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
