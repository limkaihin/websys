<?php
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/db.php';

$cart  = $_SESSION['cart'] ?? [];
$total = cart_total();

if (empty($cart)) {
    set_flash('error', 'Your cart is empty.');
    redirect('shop/cart.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name    = post('name');
    $email   = post('email');
    $address = post('address');
    $payment = post('payment');

    if (strlen($name) < 2)  $errors['name']    = 'Full name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'A valid email address is required.';
    if (strlen($address) < 5) $errors['address'] = 'Delivery address is required.';

    $allowed_payments = ['card','paynow','grab'];
    if (!in_array($payment, $allowed_payments, true)) $payment = 'card';

    if (empty($errors)) {
        $pdo  = db();
        $user = current_user();

        try {
            // Insert order
            $stmt = $pdo->prepare(
                'INSERT INTO orders (user_id, name, email, address, payment, total, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $user['id'] ?? null,
                $name, $email, $address, $payment, $total, 'confirmed'
            ]);
            $orderId = (int)$pdo->lastInsertId();

            // Insert order items
            $itemStmt = $pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, name, price, qty) VALUES (?, ?, ?, ?, ?)'
            );
            foreach ($cart as $pid => $row) {
                $itemStmt->execute([$orderId, (int)$pid, $row['name'], (float)$row['price'], (int)$row['qty']]);
            }

            unset($_SESSION['cart']);
            set_flash('success', 'Order #' . $orderId . ' confirmed! Thank you, ' . h($name) . '. 🐾');
            redirect('shop/order_confirmation.php?id=' . $orderId);

        } catch (PDOException $e) {
            // Orders table not yet created — fall back to cart-clear + flash
            if (strpos($e->getMessage(), '1146') !== false) {
                unset($_SESSION['cart']);
                set_flash('success', 'Thank you, ' . h($name) . '! Your order has been received. 🐾 (Tip: run sql/migrate.sql to enable full order tracking.)');
                redirect('index.php');
            }
            throw $e;
        }
    } else {
        store_old(compact('name','email','address','payment'));
    }
}
$user = current_user();

// ── Output starts here ────────────────────────────────────────────────────────
$pageTitle = 'Checkout';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<section style="padding:80px 5%;min-height:70vh;">
  <div style="max-width:760px;margin:0 auto;position:relative;z-index:1;">
    <div class="section-header" style="text-align:left;margin-bottom:40px;">
      <div class="section-tag">💳 Checkout</div>
      <h1 class="section-title">Almost <em>There!</em></h1>
    </div>

    <form method="POST" novalidate>
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">

      <div class="membership-right" style="margin-bottom:24px;">
        <h3 style="margin-bottom:24px;">Delivery Details</h3>
        <div class="form-field">
          <label for="co-name">Full Name</label>
          <input id="co-name" type="text" name="name" value="<?= old('name', $user['name'] ?? '') ?>" placeholder="Sarah Tan" required autocomplete="name"/>
          <?php if (!empty($errors['name'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;" role="alert"><?= h($errors['name']) ?></p><?php endif; ?>
        </div>
        <div class="form-field">
          <label for="co-email">Email Address</label>
          <input id="co-email" type="email" name="email" value="<?= old('email', $user['email'] ?? '') ?>" placeholder="you@example.com" required autocomplete="email"/>
          <?php if (!empty($errors['email'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;" role="alert"><?= h($errors['email']) ?></p><?php endif; ?>
        </div>
        <div class="form-field">
          <label for="co-address">Delivery Address</label>
          <textarea id="co-address" name="address" rows="3" placeholder="12 Whisker Lane, Singapore 238823" required autocomplete="street-address"><?= old('address', $user['address'] ?? '') ?></textarea>
          <?php if (!empty($errors['address'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;" role="alert"><?= h($errors['address']) ?></p><?php endif; ?>
        </div>
        <div class="form-field">
          <label for="co-payment">Payment Method</label>
          <select id="co-payment" name="payment">
            <option value="card"   <?= old('payment','card')=='card'   ?'selected':'' ?>>💳 Credit / Debit Card</option>
            <option value="paynow" <?= old('payment')=='paynow'        ?'selected':'' ?>>📱 PayNow</option>
            <option value="grab"   <?= old('payment')=='grab'          ?'selected':'' ?>>🟢 GrabPay</option>
          </select>
        </div>
      </div>

      <!-- Order summary -->
      <div style="background:var(--white);border:1.5px solid var(--warm);border-radius:20px;padding:28px 32px;margin-bottom:20px;">
        <p style="font-size:.78rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--orange);margin-bottom:16px;">Order Summary</p>
        <?php foreach ($cart as $row): ?>
          <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:.9rem;">
            <span><?= h($row['name']) ?> × <?= (int)$row['qty'] ?></span>
            <span><?= money((float)$row['price'] * $row['qty']) ?></span>
          </div>
        <?php endforeach; ?>
        <div style="border-top:1px solid var(--warm);margin:16px 0;"></div>
        <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:.9rem;">
          <span>Delivery</span><span style="color:var(--sage);">FREE</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-weight:700;font-size:1.05rem;">
          <span>Total</span><span style="color:var(--orange);"><?= money($total) ?></span>
        </div>
      </div>

      <button class="btn-primary" type="submit" style="width:100%;display:block;text-align:center;font-size:1rem;">
        Place Order 🐾
      </button>
      <p style="text-align:center;font-size:.78rem;color:var(--brown-md);margin-top:12px;">Demo only — no real payment is processed.</p>
    </form>
  </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
