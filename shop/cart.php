<?php
require_once dirname(__DIR__) . '/includes/functions.php';

// ── POST processing BEFORE any output ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = post('action');
    $pid    = (int)post('product_id');

    if ($action === 'remove') {
        unset($_SESSION['cart'][$pid]);
    } elseif ($action === 'update') {
        $qty = max(1, (int)post('qty'));
        if (isset($_SESSION['cart'][$pid])) {
            $_SESSION['cart'][$pid]['qty'] = $qty;
        }
    }
    redirect('shop/cart.php');
}

// ── Output starts here ────────────────────────────────────────────────────────
$pageTitle = 'Your Cart';
require_once dirname(__DIR__) . '/includes/header.php';

$cart  = $_SESSION['cart'] ?? [];
$total = cart_total();
?>

<section style="padding:80px 5%;min-height:70vh;">
  <div style="max-width:900px;margin:0 auto;">
    <div class="section-header" style="text-align:left;margin-bottom:40px;">
      <div class="section-tag">🛒 Your Cart</div>
      <h1 class="section-title">Shopping <em>Basket</em></h1>
    </div>

    <?php if (empty($cart)): ?>
      <div style="text-align:center;padding:80px 20px;">
        <div style="font-size:6rem;margin-bottom:24px;">🛒</div>
        <h2 style="font-family:'Playfair Display',serif;margin-bottom:12px;">Your cart is empty</h2>
        <p style="color:var(--brown-md);margin-bottom:32px;">Looks like your cat hasn't picked anything yet!</p>
        <a href="<?= h(base_url('shop/products.php')) ?>" class="btn-primary" style="text-decoration:none;display:inline-block;">Browse Products →</a>
      </div>
    <?php else: ?>
      <?php foreach ($cart as $pid => $row):
        $emojiMap = ['food'=>'🥩','litter'=>'🧴','toys'=>'🧶','apparel'=>'👗','accessories'=>'🎀'];
      ?>
      <div class="cart-item" style="background:var(--white);border-radius:20px;padding:20px 24px;margin-bottom:16px;display:flex;gap:20px;align-items:center;box-shadow:0 2px 12px rgba(61,35,20,.06);">
        <div class="thumb" style="width:80px;height:80px;border-radius:16px;background:var(--warm);display:flex;align-items:center;justify-content:center;font-size:2.5rem;flex-shrink:0;">🐾</div>
        <div class="details" style="flex:1;">
          <h4 style="font-family:'Playfair Display',serif;font-size:1rem;margin-bottom:4px;"><?= h($row['name']) ?></h4>
          <div style="font-size:.82rem;color:var(--brown-md);margin-bottom:12px;"><?= money((float)$row['price']) ?> each</div>
          <form method="POST" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            <input type="hidden" name="csrf_token"  value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="action"      value="update">
            <input type="hidden" name="product_id"  value="<?= (int)$pid ?>">
            <div class="qty-ctrl">
              <button type="submit" name="qty" value="<?= max(1, $row['qty']-1) ?>" class="qty-btn">−</button>
              <span style="min-width:24px;text-align:center;"><?= (int)$row['qty'] ?></span>
              <button type="submit" name="qty" value="<?= $row['qty']+1 ?>"         class="qty-btn">+</button>
            </div>
            <strong style="font-size:1rem;"><?= money((float)$row['price'] * $row['qty']) ?></strong>
          </form>
        </div>
        <form method="POST">
          <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
          <input type="hidden" name="action"     value="remove">
          <input type="hidden" name="product_id" value="<?= (int)$pid ?>">
          <button type="submit" style="background:none;border:none;cursor:pointer;font-size:1.3rem;color:var(--brown-md);transition:color .2s;" onmouseover="this.style.color='var(--orange)'" onmouseout="this.style.color='var(--brown-md)'">✕</button>
        </form>
      </div>
      <?php endforeach; ?>

      <!-- Order summary -->
      <div style="background:var(--brown);border-radius:24px;padding:36px 40px;margin-top:32px;color:var(--cream);">
        <div style="display:flex;justify-content:space-between;margin-bottom:12px;font-size:.95rem;">
          <span>Subtotal</span><span><?= money($total) ?></span>
        </div>
        <div style="display:flex;justify-content:space-between;margin-bottom:12px;font-size:.95rem;">
          <span>Delivery</span><span style="color:var(--orange-lt);">Free (MeowClub)</span>
        </div>
        <div style="border-top:1px solid rgba(255,255,255,.15);margin:20px 0;"></div>
        <div style="display:flex;justify-content:space-between;margin-bottom:28px;">
          <span style="font-size:1.1rem;font-weight:600;">Total</span>
          <span style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:900;color:var(--orange-lt);"><?= money($total) ?></span>
        </div>
        <a href="<?= h(base_url('shop/checkout.php')) ?>" class="btn-join" style="text-decoration:none;display:block;text-align:center;">Proceed to Checkout →</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
