<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../inc/head.inc.php"; ?>
  <title>Your Cart – MeowMart</title>
</head>
<body>
<?php include "../inc/nav.inc.php"; ?>
<?php
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
$cart      = $_SESSION['cart'];
$subtotal  = array_reduce($cart, fn($s,$i) => $s + $i['price'] * $i['qty'], 0);
$count     = array_reduce($cart, fn($s,$i) => $s + $i['qty'], 0);
$delivery  = (!empty($_SESSION['loggedin']) || $subtotal >= 50) ? 0 : 5.90;
$total     = $subtotal + $delivery;
?>

<!-- HEADER -->
<div style="background:var(--warm);padding:60px 5% 40px;text-align:center;">
  <div class="hero-eyebrow" style="margin:0 auto 14px;">🛒 Cart</div>
  <h1 class="section-title" style="font-size:clamp(2rem,4vw,3rem);margin-bottom:8px;">Shopping <em>Cart</em></h1>
  <p style="color:var(--brown-md);font-size:.9rem;"><?= $count ?> item<?= $count!==1?'s':'' ?> in your cart</p>
</div>

<div style="max-width:1200px;margin:0 auto;padding:50px 5% 80px;display:grid;grid-template-columns:1fr 360px;gap:40px;align-items:start;">

  <!-- ITEMS -->
  <div>
    <?php if (empty($cart)): ?>
    <div style="text-align:center;padding:80px 40px;background:var(--warm);border-radius:24px;">
      <div style="font-size:5rem;margin-bottom:20px;">🛒</div>
      <h2 style="font-family:'Playfair Display',serif;font-size:1.8rem;margin-bottom:12px;">Your cart is empty</h2>
      <p style="color:var(--brown-md);margin-bottom:28px;">Looks like you haven't added anything yet!</p>
      <a href="/shop/products.php" class="btn-primary" style="text-decoration:none;display:inline-block;padding:14px 32px;">Browse Products →</a>
    </div>
    <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:16px;">
      <?php foreach ($cart as $item): ?>
      <div style="background:#fff;border:1.5px solid var(--warm);border-radius:20px;padding:22px;display:flex;gap:18px;align-items:center;">
        <div style="width:76px;height:76px;border-radius:14px;background:var(--warm);display:flex;align-items:center;justify-content:center;font-size:2.4rem;flex-shrink:0;"><?= $item['icon'] ?></div>
        <div style="flex:1;min-width:0;">
          <h3 style="font-size:.92rem;font-weight:600;color:var(--brown);margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($item['name']) ?></h3>
          <p style="font-size:.8rem;color:var(--brown-md);margin-bottom:10px;">MeowMart · $<?= number_format($item['price'],2) ?> each</p>
          <!-- Qty -->
          <form action="/shop/process_cart.php" method="POST" style="display:inline-flex;align-items:center;gap:6px;">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id"     value="<?= $item['id'] ?>">
            <div style="display:flex;align-items:center;border:1.5px solid var(--warm);border-radius:30px;overflow:hidden;">
              <button type="submit" name="qty" value="<?= $item['qty']-1 ?>" style="background:var(--warm);border:none;padding:7px 13px;cursor:pointer;font-size:.95rem;color:var(--brown);">−</button>
              <span style="padding:7px 13px;font-size:.88rem;font-weight:600;color:var(--brown);"><?= $item['qty'] ?></span>
              <button type="submit" name="qty" value="<?= $item['qty']+1 ?>" style="background:var(--warm);border:none;padding:7px 13px;cursor:pointer;font-size:.95rem;color:var(--brown);">+</button>
            </div>
          </form>
        </div>
        <div style="text-align:right;flex-shrink:0;">
          <div style="font-size:1.05rem;font-weight:700;color:var(--brown);margin-bottom:10px;">$<?= number_format($item['price']*$item['qty'],2) ?></div>
          <form action="/shop/process_cart.php" method="POST" style="display:inline;">
            <input type="hidden" name="action" value="remove">
            <input type="hidden" name="id"     value="<?= $item['id'] ?>">
            <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--brown-md);font-size:.8rem;text-decoration:underline;">Remove</button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;flex-wrap:wrap;gap:10px;">
      <a href="/shop/products.php" style="color:var(--orange);font-size:.88rem;font-weight:600;text-decoration:none;">← Continue Shopping</a>
      <form action="/shop/process_cart.php" method="POST">
        <input type="hidden" name="action" value="clear">
        <button type="submit" style="background:none;border:1.5px solid var(--warm);border-radius:30px;padding:8px 18px;cursor:pointer;color:var(--brown-md);font-size:.82rem;">🗑 Clear Cart</button>
      </form>
    </div>
    <?php endif; ?>
  </div>

  <!-- ORDER SUMMARY -->
  <?php if (!empty($cart)): ?>
  <div style="background:#fff;border:1.5px solid var(--warm);border-radius:24px;padding:28px;position:sticky;top:90px;">
    <h2 style="font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:900;color:var(--brown);margin-bottom:22px;">Order Summary</h2>
    <div style="display:flex;justify-content:space-between;font-size:.88rem;color:var(--brown-md);margin-bottom:10px;">
      <span>Subtotal (<?= $count ?> items)</span>
      <span style="font-weight:600;color:var(--brown);">$<?= number_format($subtotal,2) ?></span>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:.88rem;color:var(--brown-md);margin-bottom:14px;">
      <span>Delivery</span>
      <span style="font-weight:600;color:<?= $delivery==0?'var(--sage)':'var(--brown)' ?>;">
        <?= $delivery==0 ? '✓ Free' : '$'.number_format($delivery,2) ?>
      </span>
    </div>
    <?php if ($delivery > 0): ?>
    <div style="background:var(--warm);border-radius:12px;padding:10px 14px;margin-bottom:14px;font-size:.8rem;color:var(--brown-md);">
      🐾 Free delivery on orders over $50 or for <a href="/account/register.php" style="color:var(--orange);font-weight:600;">MeowClub members</a>.
    </div>
    <?php endif; ?>
    <!-- Promo -->
    <div style="display:flex;gap:8px;margin-bottom:18px;">
      <input type="text" placeholder="Promo code" style="flex:1;border:1.5px solid var(--warm);border-radius:30px;padding:9px 14px;font-size:.82rem;font-family:inherit;color:var(--brown);background:#fff;outline:none;">
      <button style="background:var(--brown);color:var(--cream);border:none;border-radius:30px;padding:9px 16px;font-size:.8rem;font-weight:600;cursor:pointer;">Apply</button>
    </div>
    <div style="border-top:1.5px solid var(--warm);padding-top:14px;margin-bottom:18px;display:flex;justify-content:space-between;">
      <span style="font-size:1rem;font-weight:700;color:var(--brown);">Total</span>
      <span style="font-size:1.15rem;font-weight:700;color:var(--brown);">$<?= number_format($total,2) ?></span>
    </div>
    <a href="/shop/checkout.php" class="btn-join" style="display:block;text-align:center;text-decoration:none;padding:15px;font-size:.95rem;">
      Proceed to Checkout →
    </a>
    <p style="text-align:center;font-size:.76rem;color:var(--brown-md);margin-top:12px;">🔒 Secure checkout · SSL encrypted</p>
  </div>
  <?php endif; ?>
</div>

<?php include "../inc/footer.inc.php"; ?>
</body>
</html>
