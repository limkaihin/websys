<?php
require_once __DIR__ . '/includes/header.php';
$pdo = db();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
    set_flash('error', 'Product not found.');
    redirect('products.php');
}
$pageTitle = $p['name'];
$emojiMap  = ['food'=>'🥩','litter'=>'🧴','toys'=>'🧶','apparel'=>'👗','accessories'=>'🎀'];
$icon      = $emojiMap[strtolower($p['category'])] ?? '🐾';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $qty = max(1, (int)post('qty'));
    if (!isset($_SESSION['cart'][$p['id']])) {
        $_SESSION['cart'][$p['id']] = ['name'=>$p['name'], 'price'=>$p['price'], 'qty'=>0];
    }
    $_SESSION['cart'][$p['id']]['qty'] += $qty;
    set_flash('success', h($p['name']) . ' added to cart!');
    redirect('product.php?id=' . $p['id']);
}
?>

<div style="padding:60px 5%;max-width:1100px;margin:0 auto;">
  <!-- Breadcrumb -->
  <p style="font-size:.82rem;color:var(--brown-md);margin-bottom:32px;">
    <a href="<?= h(base_url('index.php')) ?>" style="color:var(--brown-md);text-decoration:none;">Home</a>
    &rsaquo;
    <a href="<?= h(base_url('products.php')) ?>" style="color:var(--brown-md);text-decoration:none;">Shop</a>
    &rsaquo;
    <span style="color:var(--orange);"><?= h($p['name']) ?></span>
  </p>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:start;">
    <!-- Image -->
    <div style="background:var(--warm);border-radius:28px;aspect-ratio:1;display:flex;align-items:center;justify-content:center;font-size:12rem;position:relative;">
      <?= $icon ?>
      <?php if ($p['is_featured']): ?>
        <div class="ribbon" style="position:absolute;top:20px;left:20px;">Featured</div>
      <?php endif; ?>
    </div>

    <!-- Info -->
    <div>
      <div class="product-brand" style="font-size:.8rem;margin-bottom:8px;"><?= h($p['category']) ?></div>
      <h1 style="font-family:'Playfair Display',serif;font-size:clamp(1.6rem,3vw,2.4rem);font-weight:900;line-height:1.15;margin-bottom:12px;color:var(--brown);">
        <?= h($p['name']) ?>
      </h1>
      <div class="product-stars" style="font-size:.9rem;margin-bottom:20px;">⭐⭐⭐⭐⭐ <span class="count">(<?= rand(50,500) ?> reviews)</span></div>
      <div style="font-size:2rem;font-weight:700;color:var(--orange);margin-bottom:24px;"><?= money((float)$p['price']) ?></div>
      <p style="font-size:.95rem;color:var(--brown-md);line-height:1.7;margin-bottom:32px;">
        <?= h($p['description'] ?? 'Premium quality product for your beloved cat.') ?>
      </p>

      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
          <label style="font-size:.78rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--brown-md);">Qty</label>
          <div class="qty-ctrl">
            <button type="button" class="qty-btn" onclick="this.nextElementSibling.value=Math.max(1,+this.nextElementSibling.value-1)">−</button>
            <input type="number" name="qty" value="1" min="1" max="99"
                   style="width:50px;text-align:center;border:1.5px solid var(--warm);border-radius:8px;padding:6px;font-family:'DM Sans',sans-serif;">
            <button type="button" class="qty-btn" onclick="this.previousElementSibling.value=+this.previousElementSibling.value+1">+</button>
          </div>
        </div>
        <button class="btn-primary" type="submit" style="width:100%;justify-content:center;">
          Add to Cart 🛒
        </button>
      </form>

      <div style="margin-top:28px;background:var(--warm);border-radius:20px;padding:22px 24px;">
        <p style="font-size:.82rem;font-weight:700;color:var(--orange);text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;">Why cats love it 🐾</p>
        <p style="font-size:.88rem;color:var(--brown-md);line-height:1.65;">
          Crafted with care by <?= h($p['category']) ?> experts. Made with premium ingredients your cat will adore, backed by thousands of happy paws.
        </p>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
