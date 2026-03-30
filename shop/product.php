<?php
require_once dirname(__DIR__) . '/includes/db.php';

$id  = (int)($_GET['id'] ?? 0);
$pdo = db();
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
    set_flash('error', 'Product not found.');
    redirect('shop/products.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = post('action');

    if ($action === 'wishlist') {
        $added = wishlist_toggle((int)$p['id']);
        set_flash('success', $added ? 'Saved to wishlist! 🤍' : 'Removed from wishlist.');
        redirect('shop/product.php?id=' . $p['id']);
    }

    $qty = max(1, (int)post('qty'));
    cart_add_product($p, $qty);
    set_flash('success', normalize_display_text((string)$p['name']) . ' added to cart! 🛒');
    redirect('shop/product.php?id=' . $p['id']);
}

$pageTitle = $p['name'];
require_once dirname(__DIR__) . '/includes/header.php';

$icon     = product_icon_for($p);

// Stable fake rating
$seed    = ($p['id'] * 6271 + 1009) % 1000;
$count   = 50 + ($seed % 450);
$stars   = 4 + (($p['id'] * 31) % 2);
$starStr = str_repeat('⭐', $stars);

// Related products (same category, different ID)
$relStmt = $pdo->prepare('SELECT * FROM products WHERE LOWER(category)=LOWER(?) AND id != ? ORDER BY is_featured DESC, RAND() LIMIT 4');
$relStmt->execute([$p['category'], $p['id']]);
$related = $relStmt->fetchAll();
?>

<div class="detail-page-shell">

  <!-- Breadcrumb -->
  <nav class="breadcrumb-wrap" aria-label="Breadcrumb">
    <div class="breadcrumb-list">
      <a href="<?= h(base_url('index.php')) ?>">Home</a>
      <span class="breadcrumb-sep" aria-hidden="true">›</span>
      <a href="<?= h(base_url('shop/products.php')) ?>">Shop</a>
      <span class="breadcrumb-sep" aria-hidden="true">›</span>
      <a href="<?= h(base_url('shop/products.php?cat=' . urlencode(strtolower($p['category'])))) ?>"><?= h($p['category']) ?></a>
      <span class="breadcrumb-sep" aria-hidden="true">›</span>
      <span class="breadcrumb-current" aria-current="page"><?= h($p['name']) ?></span>
    </div>
  </nav>

  <!-- Main product grid -->
  <div class="detail-layout">

    <!-- Image panel -->
    <div class="detail-media">
      <span aria-hidden="true"><?= $icon ?></span>
      <?php if ($p['is_featured']): ?>
        <div class="ribbon" style="position:absolute;top:20px;left:20px;" aria-label="Featured product">Featured</div>
      <?php endif; ?>
    </div>

    <!-- Details panel -->
    <div class="detail-summary">
      <div class="product-brand" style="font-size:.8rem;margin-bottom:8px;"><?= h($p['category']) ?></div>
      <h1>
        <?= h($p['name']) ?>
      </h1>

      <!-- Rating -->
      <div class="detail-rating-row">
        <div class="product-stars" style="font-size:.95rem;" aria-label="<?= $stars ?> out of 5 stars">
          <?= $starStr ?>
        </div>
        <span style="font-size:.85rem;color:var(--brown-md);">(<?= $count ?> reviews)</span>
        <?php if (wishlist_has((int)$p['id'])): ?>
          <span data-wishlist-indicator style="font-size:.82rem;color:var(--orange);">❤️ In your wishlist</span>
        <?php endif; ?>
      </div>

      <!-- Price + wishlist -->
      <div class="detail-price-row">
        <div style="font-size:2rem;font-weight:700;color:var(--orange);" aria-label="Price: <?= money((float)$p['price']) ?>"><?= money((float)$p['price']) ?></div>
        <form method="POST" style="margin:0;" data-ajax-wishlist="detail" data-product-id="<?= (int)$p['id'] ?>">
          <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
          <input type="hidden" name="action"     value="wishlist">
          <button class="wishlist <?= wishlist_has((int)$p['id']) ? 'active' : '' ?>" type="submit"
                  aria-label="<?= wishlist_has((int)$p['id']) ? 'Remove from wishlist' : 'Add to wishlist' ?>"
                  style="position:static;font-size:1.5rem;width:46px;height:46px;">
            <i class="fa-<?= wishlist_has((int)$p['id']) ? 'solid' : 'regular' ?> fa-heart"></i>
          </button>
        </form>
      </div>

      <!-- Description -->
      <p class="detail-description">
        <?= nl2br(h($p['description'] ?? 'Premium quality product for your beloved cat.')) ?>
      </p>

      <!-- Add to cart -->
      <form method="POST" data-ajax-cart="detail" data-product-id="<?= (int)$p['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <input type="hidden" name="action"     value="cart">
        <div class="detail-qty-row">
          <label for="prod-qty" style="font-size:.78rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--brown-md);">Qty</label>
          <div class="qty-ctrl">
            <button type="button" class="qty-btn" onclick="var i=document.getElementById('prod-qty');i.value=Math.max(1,+i.value-1);" aria-label="Decrease quantity">−</button>
            <input id="prod-qty" type="number" name="qty" value="1" min="1" max="99"
                   style="width:52px;text-align:center;border:1.5px solid var(--warm);border-radius:8px;padding:6px;font-family:'DM Sans',sans-serif;"
                   aria-label="Quantity"/>
            <button type="button" class="qty-btn" onclick="var i=document.getElementById('prod-qty');i.value=+i.value+1;" aria-label="Increase quantity">+</button>
          </div>
        </div>
        <button class="btn-primary" type="submit" style="width:100%;display:block;text-align:center;font-size:1rem;">
          Add to Cart 🛒
        </button>
      </form>

      <!-- Perks strip -->
      <div class="detail-perks">
        <?php foreach (['🚚 Free delivery for members','🔄 30-day returns','✅ Quality guaranteed'] as $perk): ?>
          <span class="detail-perk"><?= $perk ?></span>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Related products -->
  <?php if (!empty($related)): ?>
  <div style="margin-top:20px;">
    <div class="section-header" style="text-align:left;margin-bottom:32px;">
      <div class="section-tag">🐾 More Like This</div>
      <h2 class="section-title">You Might Also <em>Love</em></h2>
    </div>
    <div class="products-grid">
      <?php foreach ($related as $r):
        $rIcon = product_icon_for($r);
        [$rStars, $rCount] = [4 + (($r['id'] * 31) % 2), 50 + (($r['id'] * 6271 + 1009) % 1000 % 450)];
      ?>
        <article class="product-card" aria-label="<?= h($r['name']) ?>">
          <a href="<?= h(base_url('shop/product.php?id=' . $r['id'])) ?>" style="text-decoration:none;color:inherit;display:block;">
            <div class="product-img">
              <span aria-hidden="true"><?= $rIcon ?></span>
              <?php if ($r['is_featured']): ?><div class="ribbon">Featured</div><?php endif; ?>
            </div>
            <div class="product-body">
              <div class="product-brand"><?= h($r['category']) ?></div>
              <h3 class="product-name"><?= h($r['name']) ?></h3>
              <div class="product-stars" aria-label="<?= $rStars ?> stars">
                <?= str_repeat('⭐', $rStars) ?> <span class="count">(<?= $rCount ?>)</span>
              </div>
              <div class="product-footer">
                <div class="product-price"><?= money((float)$r['price']) ?></div>
                <span class="btn-cart" role="img" aria-hidden="true">🛒</span>
              </div>
            </div>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>


<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
