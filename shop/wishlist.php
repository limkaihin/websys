<?php
require_once dirname(__DIR__) . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $pid = (int)post('product_id');
    $added = wishlist_toggle($pid);
    set_flash('success', $added ? 'Added to wishlist.' : 'Removed from wishlist.');
    redirect('shop/wishlist.php');
}

$pageTitle = 'Your Wishlist';

$ids = wishlist_items();
$products = [];
if (!empty($ids)) {
    $pdo = db();
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders) ORDER BY name");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();
}
$emojiMap = ['food' => '🥩', 'litter' => '🧴', 'toys' => '🧶', 'apparel' => '👗', 'accessories' => '🎀'];
// ── Output starts here ─────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/includes/header.php';

?>

<section class="products" style="min-height:70vh;">
  <div class="products-toolbar">
    <div class="section-header" style="text-align:left;margin-bottom:0;">
      <div class="section-tag">🤍 Saved</div>
      <h1 class="section-title">Your <em>Wishlist</em></h1>
    </div>
  </div>

  <?php if (empty($products)): ?>
    <div style="text-align:center;padding:80px 20px;">
      <div style="font-size:4rem;margin-bottom:16px;">🤍</div>
      <h2 style="font-family:'Playfair Display',serif;margin-bottom:12px;">No saved items yet</h2>
      <p style="color:var(--brown-md);margin-bottom:28px;">Tap the heart on any product to save it for later.</p>
      <a href="<?= h(base_url('shop/products.php')) ?>" class="btn-primary" style="text-decoration:none;display:inline-flex;">Browse products</a>
    </div>
  <?php else: ?>
    <div class="products-grid">
      <?php foreach ($products as $p): $icon = $emojiMap[strtolower($p['category'])] ?? '🐾'; ?>
        <div class="product-card">
          <div class="product-img">
            <span><?= $icon ?></span>
            <form method="POST" style="position:absolute;top:12px;right:12px;z-index:2;">
              <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
              <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
              <button class="wishlist active" type="submit" aria-label="Remove from wishlist">❤️</button>
            </form>
          </div>
          <div class="product-body">
            <div class="product-brand"><?= h($p['category']) ?></div>
            <h3 class="product-name"><?= h($p['name']) ?></h3>
            <div class="product-footer">
              <div class="product-price"><?= money((float)$p['price']) ?></div>
              <a href="<?= h(base_url('shop/product.php?id=' . $p['id'])) ?>" class="btn-cart" style="text-decoration:none;display:flex;align-items:center;justify-content:center;">View</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
