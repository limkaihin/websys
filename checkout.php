<?php
$pageTitle = 'Checkout';
require_once __DIR__ . '/includes/header.php';

$cart  = $_SESSION['cart'] ?? [];
$total = cart_total();

if (empty($cart)) {
    set_flash('error', 'Your cart is empty.');
    redirect('cart.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name    = post('name');
    $email   = post('email');
    $address = post('address');
    $payment = post('payment');

    if (!$name)             $errors['name']    = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email required.';
    if (!$address)          $errors['address'] = 'Delivery address is required.';

    if (empty($errors)) {
        // Demo: clear cart and show success
        unset($_SESSION['cart']);
        set_flash('success', 'Order placed! Thank you, ' . $name . '. 🐾');
        redirect('index.php');
    } else {
        store_old(compact('name','email','address','payment'));
    }
}
$user = current_user();
?>

<section style="padding:80px 5%;min-height:70vh;">
  <div style="max-width:760px;margin:0 auto;">
    <div class="section-header" style="text-align:left;margin-bottom:40px;">
      <div class="section-tag">💳 Checkout</div>
      <h1 class="section-title">Almost <em>There!</em></h1>
    </div>

    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">

      <div class="membership-right" style="margin-bottom:24px;">
        <h3 style="margin-bottom:24px;">Delivery Details</h3>

        <div class="form-field">
          <label>Full Name</label>
          <input type="text" name="name" value="<?= old('name', $user['name'] ?? '') ?>" placeholder="Sarah Tan" required/>
          <?php if (!empty($errors['name'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;"><?= h($errors['name']) ?></p><?php endif; ?>
        </div>
        <div class="form-field">
          <label>Email Address</label>
          <input type="email" name="email" value="<?= old('email', $user['email'] ?? '') ?>" placeholder="you@example.com" required/>
          <?php if (!empty($errors['email'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;"><?= h($errors['email']) ?></p><?php endif; ?>
        </div>
        <div class="form-field">
          <label>Delivery Address</label>
          <input type="text" name="address" value="<?= old('address', $user['address'] ?? '') ?>" placeholder="12 Whisker Lane, Singapore 238823" required/>
          <?php if (!empty($errors['address'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;"><?= h($errors['address']) ?></p><?php endif; ?>
        </div>
        <div class="form-field">
          <label>Payment Method</label>
          <select name="payment" style="width:100%;background:rgba(255,255,255,.08);border:1.5px solid rgba(255,255,255,.18);border-radius:12px;padding:12px 16px;color:var(--cream);font-family:'DM Sans',sans-serif;font-size:.92rem;">
            <option value="card"   <?= old('payment')=='card'   ?'selected':'' ?>>💳 Credit / Debit Card</option>
            <option value="paynow" <?= old('payment')=='paynow' ?'selected':'' ?>>📱 PayNow</option>
            <option value="grab"   <?= old('payment')=='grab'   ?'selected':'' ?>>🟢 GrabPay</option>
          </select>
        </div>
      </div>

      <!-- Order Summary -->
      <div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:20px;padding:28px 32px;margin-bottom:20px;color:var(--brown);">
        <p style="font-size:.78rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--orange);margin-bottom:16px;">Order Summary</p>
        <?php foreach ($cart as $row): ?>
          <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:.9rem;">
            <span><?= h($row['name']) ?> × <?= (int)$row['qty'] ?></span>
            <span><?= money((float)$row['price'] * $row['qty']) ?></span>
          </div>
        <?php endforeach; ?>
        <div style="border-top:1px solid var(--warm);margin:16px 0;"></div>
        <div style="display:flex;justify-content:space-between;font-weight:700;font-size:1.05rem;">
          <span>Total</span><span style="color:var(--orange);"><?= money($total) ?></span>
        </div>
      </div>

      <button class="btn-primary" type="submit" style="width:100%;justify-content:center;font-size:1rem;">
        Place Order 🐾
      </button>
      <p style="text-align:center;font-size:.78rem;color:var(--brown-md);margin-top:12px;">Demo only — no real payment is processed.</p>
    </form>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
