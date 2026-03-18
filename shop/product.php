<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../inc/head.inc.php"; ?>
</head>
<body>
<?php include "../inc/nav.inc.php"; ?>
<?php
include "../inc/db.inc.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header("Location: /shop/products.php"); exit; }

$p = getProduct($id);
if (!$p) { header("Location: /shop/products.php"); exit; }

$icons  = ['food'=>'🥩','litter'=>'🧴','toys'=>'🧶','apparel'=>'👗','accessories'=>'🎀'];
$bgCols = ['food'=>'#FDE8D0','litter'=>'#D6E8D8','toys'=>'#D8E0F0','apparel'=>'#F4E8F0','accessories'=>'#FFF4D6'];
$slug   = strtolower($p['category']);
$icon   = $icons[$slug]  ?? '🐾';
$bg     = $bgCols[$slug] ?? '#F2E8D9';

// Related = same category, different id, max 4
$related = array_slice(array_filter(
    filterProducts($p['category']),
    fn($r) => $r['id'] !== $id
), 0, 4);

$added = isset($_GET['added']);
$reviews = (($id * 47 + 83) % 450) + 50;
?>
<title><?= htmlspecialchars($p['name']) ?> – MeowMart</title>

<!-- BREADCRUMB -->
<div style="background:var(--warm);padding:14px 5%;border-bottom:1.5px solid var(--cream);">
  <p style="font-size:.82rem;color:var(--brown-md);max-width:1200px;margin:0 auto;">
    <a href="/index.php"                              style="color:var(--brown-md);text-decoration:none;">Home</a> ›
    <a href="/shop/products.php"                      style="color:var(--brown-md);text-decoration:none;">Shop</a> ›
    <a href="/shop/category.php?slug=<?= $slug ?>"    style="color:var(--brown-md);text-decoration:none;"><?= htmlspecialchars($p['category']) ?></a> ›
    <span style="color:var(--orange);font-weight:600;"><?= htmlspecialchars($p['name']) ?></span>
  </p>
</div>

<!-- SUCCESS BANNER -->
<?php if ($added): ?>
<div style="background:#D6E8D8;border-bottom:1.5px solid var(--sage);padding:14px 5%;display:flex;align-items:center;gap:12px;font-weight:600;color:var(--brown);">
  ✅ Added to cart!
  <a href="/shop/cart.php" style="color:var(--orange);margin-left:auto;font-size:.9rem;">View Cart →</a>
</div>
<?php endif; ?>

<!-- PRODUCT DETAIL -->
<section style="padding:50px 5%;max-width:1200px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:start;">

  <!-- Left: image panel -->
  <div>
    <div style="background:<?= $bg ?>;border-radius:28px;aspect-ratio:1;display:flex;align-items:center;justify-content:center;font-size:8rem;position:relative;overflow:hidden;">
      <span><?= $icon ?></span>
      <?php if ($p['is_featured']): ?>
        <div style="position:absolute;top:20px;left:20px;background:var(--orange);color:#fff;padding:6px 18px;border-radius:30px;font-size:.78rem;font-weight:700;letter-spacing:.04em;">Featured</div>
      <?php endif; ?>
    </div>
    <!-- Thumbnail strip -->
    <div style="display:flex;gap:12px;margin-top:16px;">
      <?php for($i=0;$i<3;$i++): ?>
      <div style="flex:1;background:<?= $bg ?>;border-radius:14px;aspect-ratio:1;display:flex;align-items:center;justify-content:center;font-size:2rem;
                  opacity:<?= $i===0?'1':'.45' ?>;border:2px solid <?= $i===0?'var(--orange)':'transparent' ?>;cursor:pointer;">
        <?= $icon ?>
      </div>
      <?php endfor; ?>
    </div>
  </div>

  <!-- Right: info -->
  <div>
    <div class="hero-eyebrow" style="margin-bottom:14px;">
      <a href="/shop/category.php?slug=<?= $slug ?>" style="color:inherit;text-decoration:none;"><?= htmlspecialchars($p['category']) ?></a>
    </div>
    <h1 style="font-family:'Playfair Display',serif;font-size:clamp(1.5rem,3vw,2.2rem);font-weight:900;line-height:1.15;margin-bottom:12px;color:var(--brown);">
      <?= htmlspecialchars($p['name']) ?>
    </h1>
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
      <span style="color:#E8651A;font-size:1.05rem;">⭐⭐⭐⭐⭐</span>
      <span style="font-size:.85rem;color:var(--brown-md);">(<?= $reviews ?> reviews)</span>
    </div>

    <div style="font-size:2.4rem;font-weight:800;color:var(--brown);margin-bottom:6px;letter-spacing:-.02em;">
      $<?= number_format($p['price'],2) ?>
    </div>
    <p style="font-size:.8rem;color:var(--sage);font-weight:600;margin-bottom:22px;">✓ Free delivery for MeowClub members · In stock</p>

    <p style="color:var(--brown-md);line-height:1.75;font-size:.95rem;margin-bottom:32px;">
      <?= htmlspecialchars($p['description']) ?>
    </p>

    <!-- Add to cart form -->
    <form action="/shop/process_cart.php" method="POST">
      <input type="hidden" name="action"   value="add">
      <input type="hidden" name="id"       value="<?= $p['id'] ?>">
      <input type="hidden" name="name"     value="<?= htmlspecialchars($p['name']) ?>">
      <input type="hidden" name="price"    value="<?= $p['price'] ?>">
      <input type="hidden" name="icon"     value="<?= $icon ?>">
      <input type="hidden" name="redirect" value="/shop/product.php?id=<?= $p['id'] ?>&added=1">

      <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;">
        <label style="font-weight:600;font-size:.88rem;color:var(--brown);">Qty</label>
        <div style="display:flex;align-items:center;border:1.5px solid var(--warm);border-radius:30px;overflow:hidden;background:#fff;">
          <button type="button"
                  onclick="var i=document.getElementById('qty');i.value=Math.max(1,+i.value-1)"
                  style="background:var(--warm);border:none;padding:10px 16px;cursor:pointer;font-size:1.1rem;color:var(--brown);font-weight:600;">−</button>
          <input type="number" name="qty" id="qty" value="1" min="1" max="99"
                 style="width:46px;text-align:center;border:none;background:#fff;font-size:.95rem;font-weight:700;color:var(--brown);outline:none;padding:10px 0;">
          <button type="button"
                  onclick="var i=document.getElementById('qty');i.value=Math.min(99,+i.value+1)"
                  style="background:var(--warm);border:none;padding:10px 16px;cursor:pointer;font-size:1.1rem;color:var(--brown);font-weight:600;">+</button>
        </div>
      </div>

      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <button type="submit" class="btn-primary"
                style="flex:1;min-width:160px;padding:15px 24px;font-size:.95rem;">🛒 Add to Cart</button>
        <a href="/shop/cart.php" class="btn-outline"
           style="flex:1;min-width:120px;text-decoration:none;display:flex;align-items:center;justify-content:center;padding:13px 24px;">View Cart →</a>
      </div>
    </form>

    <!-- Trust badges -->
    <div style="display:flex;gap:20px;flex-wrap:wrap;margin-top:28px;padding-top:22px;border-top:1.5px solid var(--warm);">
      <span style="font-size:.82rem;color:var(--brown-md);">🚚 Free delivery for members</span>
      <span style="font-size:.82rem;color:var(--brown-md);">↩️ 30-day returns</span>
      <span style="font-size:.82rem;color:var(--brown-md);">🔒 Secure checkout</span>
    </div>
  </div>
</section>

<!-- RELATED PRODUCTS -->
<?php if (!empty($related)): ?>
<section style="padding:0 5% 80px;max-width:1200px;margin:0 auto;">
  <div class="section-header" style="text-align:left;margin-bottom:28px;">
    <div class="section-tag">🐾 More Like This</div>
    <h2 class="section-title">You Might Also <em>Love</em></h2>
  </div>
  <div class="products-grid">
    <?php foreach ($related as $r):
      $rSlug = strtolower($r['category']);
      $rIcon = $icons[$rSlug]  ?? '🐾';
      $rBg   = $bgCols[$rSlug] ?? '#F2E8D9';
    ?>
    <div class="product-card">
      <div class="product-body">
        <div class="product-brand"><?= htmlspecialchars($r['category']) ?></div>
        <h3 class="product-name"><?= htmlspecialchars($r['name']) ?></h3>
        <div class="product-stars">⭐⭐⭐⭐⭐ <span class="count">(<?= (($r['id']*47+83)%450)+50 ?>)</span></div>
        <div class="product-footer">
          <div class="product-price">$<?= number_format($r['price'],2) ?></div>
          <a href="/shop/product.php?id=<?= $r['id'] ?>"
             class="btn-cart" style="text-decoration:none;display:flex;align-items:center;justify-content:center;">🛒</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php include "../inc/footer.inc.php"; ?>
</body>
</html>
