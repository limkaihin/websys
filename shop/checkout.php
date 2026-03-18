<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../inc/head.inc.php"; ?>
  <title>Checkout – MeowMart</title>
  <style>
    .co-input { width:100%;border:1.5px solid var(--warm);border-radius:12px;padding:11px 15px;font-family:inherit;font-size:.88rem;color:var(--brown);background:#fff;outline:none;transition:border-color .2s; }
    .co-input:focus { border-color:var(--orange); }
    .co-label { display:block;font-size:.8rem;font-weight:600;color:var(--brown-md);margin-bottom:5px;letter-spacing:.04em; }
    .co-row { display:grid;grid-template-columns:1fr 1fr;gap:14px; }
    .co-box { background:#fff;border:1.5px solid var(--warm);border-radius:20px;padding:26px;margin-bottom:18px; }
    .step-num { width:30px;height:30px;background:var(--orange);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:700;flex-shrink:0; }
    .pay-tab { padding:12px 18px;border:1.5px solid var(--warm);border-radius:14px;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:8px;font-weight:600;font-size:.85rem;color:var(--brown-md); }
    .pay-tab.active { border-color:var(--orange);background:var(--warm);color:var(--brown); }
  </style>
</head>
<body>
<?php include "../inc/nav.inc.php"; ?>
<?php
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: /shop/cart.php"); exit;
}
$cart     = $_SESSION['cart'];
$subtotal = array_reduce($cart, fn($s,$i) => $s + $i['price'] * $i['qty'], 0);
$count    = array_reduce($cart, fn($s,$i) => $s + $i['qty'], 0);
$delivery = (!empty($_SESSION['loggedin']) || $subtotal >= 50) ? 0 : 5.90;
$total    = $subtotal + $delivery;
$success  = isset($_GET['success']);
?>

<?php if ($success): ?>
<!-- ORDER SUCCESS -->
<div style="max-width:600px;margin:80px auto;padding:0 5%;text-align:center;">
  <div style="background:#fff;border:1.5px solid var(--warm);border-radius:28px;padding:60px 40px;">
    <div style="font-size:5rem;margin-bottom:18px;">🎉</div>
    <div class="hero-eyebrow" style="margin:0 auto 14px;">Order Confirmed!</div>
    <h1 style="font-family:'Playfair Display',serif;font-size:1.9rem;font-weight:900;margin-bottom:14px;color:var(--brown);">
      Thank you<?= !empty($_SESSION['fname']) ? ', '.htmlspecialchars($_SESSION['fname']) : '' ?>!
    </h1>
    <p style="color:var(--brown-md);line-height:1.7;margin-bottom:28px;">Your order has been placed! We'll send a confirmation email shortly. Your cat is going to love it 🐾</p>
    <div style="background:var(--warm);border-radius:16px;padding:18px;margin-bottom:28px;text-align:left;">
      <div style="display:flex;justify-content:space-between;font-size:.88rem;margin-bottom:8px;">
        <span style="color:var(--brown-md);">Order number</span>
        <span style="font-weight:700;color:var(--brown);">#MM<?= strtoupper(substr(md5(uniqid()),0,8)) ?></span>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:.88rem;">
        <span style="color:var(--brown-md);">Estimated delivery</span>
        <span style="font-weight:700;color:var(--brown);">2–4 business days</span>
      </div>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center;">
      <a href="/shop/products.php" class="btn-primary" style="text-decoration:none;padding:13px 26px;">Continue Shopping</a>
      <a href="/index.php"         class="btn-outline" style="text-decoration:none;padding:13px 26px;display:inline-flex;align-items:center;">Back to Home</a>
    </div>
  </div>
</div>

<?php else: ?>
<!-- PAGE HEADER -->
<div style="background:var(--warm);padding:40px 5% 28px;text-align:center;">
  <div class="hero-eyebrow" style="margin:0 auto 10px;">🔒 Secure Checkout</div>
  <h1 class="section-title" style="font-size:clamp(1.8rem,3vw,2.5rem);">Complete Your <em>Order</em></h1>
</div>

<!-- PROGRESS -->
<div style="background:#fff;border-bottom:1.5px solid var(--warm);padding:14px 5%;">
  <div style="max-width:700px;margin:0 auto;display:flex;align-items:center;justify-content:center;gap:6px;font-size:.8rem;font-weight:600;color:var(--brown-md);">
    <a href="/shop/cart.php" style="color:var(--orange);text-decoration:none;">🛒 Cart</a>
    <span style="color:var(--warm);font-size:1rem;">────</span>
    <span style="color:var(--orange);">📋 Details</span>
    <span style="color:var(--warm);font-size:1rem;">────</span>
    <span>💳 Payment</span>
    <span style="color:var(--warm);font-size:1rem;">────</span>
    <span>✅ Done</span>
  </div>
</div>

<div style="max-width:1200px;margin:0 auto;padding:36px 5% 80px;display:grid;grid-template-columns:1fr 360px;gap:36px;align-items:start;">

  <form action="/shop/process_checkout.php" method="POST">

    <!-- 1. CONTACT -->
    <div class="co-box">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:22px;">
        <div class="step-num">1</div>
        <h2 style="font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:900;color:var(--brown);">Contact Information</h2>
      </div>
      <div class="co-row" style="margin-bottom:14px;">
        <div><label class="co-label">First Name *</label><input class="co-input" type="text" name="fname" required value="<?= htmlspecialchars($_SESSION['fname']??'') ?>" placeholder="Sarah"></div>
        <div><label class="co-label">Last Name *</label><input  class="co-input" type="text" name="lname" required value="<?= htmlspecialchars($_SESSION['lname']??'') ?>" placeholder="Tan"></div>
      </div>
      <div style="margin-bottom:14px;"><label class="co-label">Email *</label><input class="co-input" type="email" name="email" required value="<?= htmlspecialchars($_SESSION['email']??'') ?>" placeholder="you@example.com"></div>
      <div><label class="co-label">Phone</label><input class="co-input" type="tel" name="phone" placeholder="+65 9123 4567"></div>
    </div>

    <!-- 2. SHIPPING -->
    <div class="co-box">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:22px;">
        <div class="step-num">2</div>
        <h2 style="font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:900;color:var(--brown);">Shipping Address</h2>
      </div>
      <div style="margin-bottom:14px;"><label class="co-label">Address Line 1 *</label><input class="co-input" type="text" name="addr1" required placeholder="Block 123, Ang Mo Kio Ave 6"></div>
      <div style="margin-bottom:14px;"><label class="co-label">Unit / Address Line 2</label><input class="co-input" type="text" name="addr2" placeholder="#08-123"></div>
      <div class="co-row" style="margin-bottom:14px;">
        <div><label class="co-label">Postal Code *</label><input class="co-input" type="text" name="postal" required placeholder="560123" maxlength="6"></div>
        <div><label class="co-label">Country</label><input class="co-input" type="text" value="Singapore" readonly style="background:var(--warm);"></div>
      </div>
      <div><label class="co-label">Delivery Notes</label><textarea class="co-input" name="notes" rows="2" placeholder="Leave at door, ring doorbell, etc." style="resize:vertical;"></textarea></div>
    </div>

    <!-- 3. PAYMENT -->
    <div class="co-box">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:22px;">
        <div class="step-num">3</div>
        <h2 style="font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:900;color:var(--brown);">Payment Method</h2>
      </div>
      <div style="display:flex;gap:10px;margin-bottom:22px;flex-wrap:wrap;">
        <div class="pay-tab active" id="tab-card"    onclick="switchPay('card')">💳 Credit / Debit Card</div>
        <div class="pay-tab"       id="tab-paynow"  onclick="switchPay('paynow')">📱 PayNow</div>
        <div class="pay-tab"       id="tab-cod"     onclick="switchPay('cod')">💵 Cash on Delivery</div>
      </div>
      <input type="hidden" name="payment_method" id="pay_method" value="card">

      <!-- Card -->
      <div id="panel-card">
        <div style="margin-bottom:14px;"><label class="co-label">Card Number *</label><input class="co-input" type="text" name="card_number" id="cardNo" placeholder="1234 5678 9012 3456" maxlength="19"></div>
        <div class="co-row" style="margin-bottom:14px;">
          <div><label class="co-label">Expiry *</label><input class="co-input" type="text" name="card_expiry" placeholder="MM / YY" maxlength="7"></div>
          <div><label class="co-label">CVV *</label><input    class="co-input" type="text" name="card_cvv"    placeholder="123"     maxlength="4"></div>
        </div>
        <div><label class="co-label">Name on Card *</label><input class="co-input" type="text" name="card_name" placeholder="SARAH TAN"></div>
        <p style="font-size:.76rem;color:var(--brown-md);margin-top:10px;">We accept Visa, Mastercard &amp; Amex · 🔒 SSL secured</p>
      </div>

      <!-- PayNow -->
      <div id="panel-paynow" style="display:none;text-align:center;padding:20px;">
        <div style="font-size:3.5rem;margin-bottom:12px;">📱</div>
        <p style="font-weight:600;color:var(--brown);margin-bottom:8px;">Scan QR to pay via PayNow</p>
        <div style="background:var(--warm);border-radius:16px;padding:28px;display:inline-block;margin-bottom:12px;">
          <div style="width:110px;height:110px;background:#fff;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:3rem;">🐱</div>
        </div>
        <p style="font-size:.82rem;color:var(--brown-md);">Pay $<?= number_format($total,2) ?> · UEN: 202412345A</p>
      </div>

      <!-- COD -->
      <div id="panel-cod" style="display:none;background:var(--warm);border-radius:14px;padding:18px;">
        <p style="font-weight:600;color:var(--brown);margin-bottom:6px;">💵 Cash on Delivery</p>
        <p style="font-size:.85rem;color:var(--brown-md);line-height:1.6;">Pay in cash when your order arrives. Please have the exact amount ready. Available for all Singapore addresses.</p>
      </div>
    </div>

    <button type="submit" class="btn-join" style="width:100%;padding:17px;font-size:1rem;border:none;cursor:pointer;">
      🔒 Place Order · $<?= number_format($total,2) ?>
    </button>
    <p style="text-align:center;font-size:.76rem;color:var(--brown-md);margin-top:10px;">
      By placing your order you agree to our <a href="#" style="color:var(--orange);">Terms</a> &amp; <a href="#" style="color:var(--orange);">Privacy Policy</a>.
    </p>
  </form>

  <!-- ORDER SUMMARY SIDEBAR -->
  <div style="position:sticky;top:90px;">
    <div class="co-box">
      <h2 style="font-family:'Playfair Display',serif;font-size:1.15rem;font-weight:900;color:var(--brown);margin-bottom:18px;">Order Summary</h2>
      <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:16px;">
        <?php foreach ($cart as $item): ?>
        <div style="display:flex;gap:10px;align-items:center;">
          <div style="width:42px;height:42px;background:var(--warm);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;position:relative;">
            <?= $item['icon'] ?>
            <span style="position:absolute;top:-6px;right:-6px;background:var(--brown);color:#fff;font-size:.6rem;font-weight:700;width:16px;height:16px;border-radius:50%;display:flex;align-items:center;justify-content:center;"><?= $item['qty'] ?></span>
          </div>
          <p style="flex:1;font-size:.8rem;font-weight:600;color:var(--brown);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;min-width:0;"><?= htmlspecialchars($item['name']) ?></p>
          <span style="font-size:.88rem;font-weight:700;color:var(--brown);flex-shrink:0;">$<?= number_format($item['price']*$item['qty'],2) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
      <div style="border-top:1.5px solid var(--warm);padding-top:14px;">
        <div style="display:flex;justify-content:space-between;font-size:.85rem;color:var(--brown-md);margin-bottom:8px;"><span>Subtotal</span><span style="font-weight:600;color:var(--brown);">$<?= number_format($subtotal,2) ?></span></div>
        <div style="display:flex;justify-content:space-between;font-size:.85rem;color:var(--brown-md);margin-bottom:14px;"><span>Delivery</span><span style="font-weight:600;color:<?= $delivery==0?'var(--sage)':'var(--brown)' ?>;"><?= $delivery==0?'✓ Free':'$'.number_format($delivery,2) ?></span></div>
        <div style="display:flex;justify-content:space-between;font-size:1rem;font-weight:700;color:var(--brown);"><span>Total</span><span>$<?= number_format($total,2) ?></span></div>
      </div>
      <a href="/shop/cart.php" style="display:block;text-align:center;margin-top:14px;font-size:.8rem;color:var(--orange);font-weight:600;text-decoration:none;">← Edit Cart</a>
    </div>
    <div class="co-box" style="margin-top:0;">
      <div style="display:flex;flex-direction:column;gap:10px;">
        <span style="font-size:.8rem;color:var(--brown-md);">🔒 SSL encrypted &amp; secure</span>
        <span style="font-size:.8rem;color:var(--brown-md);">↩️ 30-day hassle-free returns</span>
        <span style="font-size:.8rem;color:var(--brown-md);">🚚 Delivery within 2–4 days</span>
        <span style="font-size:.8rem;color:var(--brown-md);">📞 hello@meowmart.sg</span>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
function switchPay(type) {
  ['card','paynow','cod'].forEach(t => {
    document.getElementById('panel-'+t).style.display = t===type?'block':'none';
    document.getElementById('tab-'+t).classList.toggle('active', t===type);
  });
  document.getElementById('pay_method').value = type;
}
const cn = document.getElementById('cardNo');
if (cn) cn.addEventListener('input', e => {
  let v = e.target.value.replace(/\D/g,'').substring(0,16);
  e.target.value = v.replace(/(.{4})/g,'$1 ').trim();
});
</script>

<?php include "../inc/footer.inc.php"; ?>
</body>
</html>
