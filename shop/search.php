<?php
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$pageTitle = 'Search Products';
$query = trim((string)($_GET['q'] ?? ''));
$results = [];
if ($query !== '') {
    $stmt = db()->prepare('SELECT * FROM products WHERE name LIKE ? OR description LIKE ? OR category LIKE ? ORDER BY is_featured DESC, name ASC');
    $like = '%' . $query . '%';
    $stmt->execute([$like, $like, $like]);
    $results = $stmt->fetchAll();
}
require_once dirname(__DIR__) . '/includes/header.php';
?>

<section style="padding:70px 5% 80px;min-height:70vh;">
  <div style="max-width:1100px;margin:0 auto;">
    <div class="section-header" style="text-align:left;margin-bottom:24px;">
      <div class="section-tag"><i class="fa-solid fa-magnifying-glass"></i> Search</div>
      <h1 class="section-title">Find the right <em>MeowMart</em> item</h1>
    </div>

    <form method="GET" class="search-page-form">
      <div class="search-input-wrap"><input type="search" name="q" value="<?= h($query) ?>" placeholder="Search food, toys, litter, beds..." class="search-page-input" /></div>
      <button class="btn-primary" type="submit" style="border:none;">Search</button>
      <?php if ($query !== ''): ?>
        <a class="btn-outline" href="<?= h(base_url('shop/search.php')) ?>" style="text-decoration:none;display:inline-flex;align-items:center;">Clear</a>
      <?php endif; ?>
    </form>

    <?php if ($query === ''): ?>
      <div style="background:var(--white);border-radius:24px;padding:28px 30px;box-shadow:0 2px 12px rgba(61,35,20,.06);">
        <p style="margin:0 0 16px;color:var(--brown-md);">Try one of these quick searches:</p>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
          <?php foreach (['salmon', 'tofu litter', 'laser toy', 'cat bed', 'hoodie'] as $term): ?>
            <a class="pill" href="<?= h(base_url('shop/search.php?q=' . urlencode($term))) ?>" style="text-decoration:none;"><?= h($term) ?></a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php else: ?>
      <p style="color:var(--brown-md);margin-bottom:20px;">Showing <?= count($results) ?> result(s) for <strong><?= h($query) ?></strong>.</p>
      <?php if (empty($results)): ?>
        <div style="background:var(--white);border-radius:24px;padding:28px 30px;box-shadow:0 2px 12px rgba(61,35,20,.06);">
          <p style="margin:0;color:var(--brown-md);">No direct matches found. Try a broader term or browse all products instead.</p>
          <a href="<?= h(base_url('shop/products.php')) ?>" class="btn-primary" style="text-decoration:none;display:inline-block;margin-top:16px;">Browse all products</a>
        </div>
      <?php else: ?>
        <div class="products-grid"> 
          <?php foreach ($results as $p): $icon = product_icon_for($p); ?>
            <article class="product-card">
              <a href="<?= h(base_url('shop/product.php?id=' . $p['id'])) ?>" style="text-decoration:none;color:inherit;display:block;">
                <div class="product-img"><span><?= $icon ?></span><?php if ($p['is_featured']): ?><div class="ribbon">Featured</div><?php endif; ?></div>
                <div class="product-body">
                  <div class="product-brand"><?= h($p['category']) ?></div>
                  <h3 class="product-name"><?= h($p['name']) ?></h3>
                  <p style="font-size:.86rem;color:var(--brown-md);line-height:1.6;min-height:3.2em;"><?= h(mb_strimwidth($p['description'], 0, 110, '…')) ?></p>
                  <div class="product-footer">
                    <div class="product-price"><?= money((float)$p['price']) ?></div>
                    <span class="btn-cart">View</span>
                  </div>
                </div>
              </a>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
