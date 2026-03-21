<?php
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/db.php';

require_login();

$cart     = cart_items();
$subtotal = cart_total();

if (empty($cart)) {
    set_flash('error', 'Your cart is empty.');
    redirect('shop/cart.php');
}

$errors = [];
$user   = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name          = post('name');
    $email         = post('email');
    $address       = post('address');
    $payment       = post('payment');
    $couponCode    = strtoupper(trim((string)($_POST['coupon_code'] ?? '')));
    $referralCode  = sanitize_referral_code((string)($_POST['referral_code'] ?? ''));

    $cardName      = trim((string)($_POST['card_name'] ?? ''));
    $cardNumberRaw = preg_replace('/\D+/', '', (string)($_POST['card_number'] ?? ''));
    $cardExpiry    = trim((string)($_POST['card_expiry'] ?? ''));
    $cardCvv       = preg_replace('/\D+/', '', (string)($_POST['card_cvv'] ?? ''));

    $paynowName    = trim((string)($_POST['paynow_name'] ?? ''));
    $paynowPhone   = preg_replace('/\D+/', '', (string)($_POST['paynow_phone'] ?? ''));

    $gpayEmail     = trim((string)($_POST['gpay_email'] ?? ''));

    if (strlen($name) < 2) $errors['name'] = 'Full name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'A valid email address is required.';
    if (strlen($address) < 5) $errors['address'] = 'Delivery address is required.';

    $allowedPayments = ['card', 'paynow', 'gpay'];
    if (!in_array($payment, $allowedPayments, true)) $payment = 'card';

    $couponInfo = coupon_discount_details($subtotal, $couponCode);
    if ($couponCode !== '' && $couponInfo['error'] !== '') {
        $errors['coupon_code'] = $couponInfo['error'];
    }

    if ($referralCode !== '' && strlen($referralCode) < 4) {
        $errors['referral_code'] = 'Please enter a valid referral code.';
    }

    $paymentReference = '';
    if ($payment === 'card') {
        if (strlen($cardName) < 2) $errors['card_name'] = 'Name on card is required.';
        if (!preg_match('/^\d{16}$/', $cardNumberRaw)) $errors['card_number'] = 'Card number must be exactly 16 digits.';
        if (!preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $cardExpiry)) $errors['card_expiry'] = 'Use MM/YY format.';
        if (!preg_match('/^\d{3}$/', $cardCvv)) $errors['card_cvv'] = 'CVV must be exactly 3 digits.';
        if (!isset($errors['card_number'])) {
            $paymentReference = 'Card ending ' . substr($cardNumberRaw, -4);
        }
    } elseif ($payment === 'paynow') {
        if (strlen($paynowName) < 2) $errors['paynow_name'] = 'PayNow account name is required.';
        if (strlen($paynowPhone) < 8) $errors['paynow_phone'] = 'Please enter the mobile number used for PayNow.';
        if (!isset($errors['paynow_phone'])) {
            $paymentReference = 'PayNow ' . $paynowPhone;
        }
    } else {
        if (!filter_var($gpayEmail, FILTER_VALIDATE_EMAIL)) $errors['gpay_email'] = 'Please enter the Google account email for Google Pay.';
        if (!isset($errors['gpay_email'])) {
            $paymentReference = 'Google Pay ' . $gpayEmail;
        }
    }

    if (empty($errors)) {
        $discount   = (float)$couponInfo['discount'];
        $finalTotal = max(0, $subtotal - $discount);
        $pdo        = db();

        try {
            $columns = ['user_id', 'name', 'email', 'address', 'payment', 'total', 'status'];
            $values  = [$user['id'] ?? null, $name, $email, $address, $payment, $finalTotal, 'confirmed'];

            foreach ([
                'coupon_code'       => ($couponInfo['applied_code'] !== '' ? $couponInfo['applied_code'] : null),
                'discount'          => ($discount > 0 ? $discount : 0),
                'referral_code'     => ($referralCode !== '' ? $referralCode : null),
                'payment_reference' => ($paymentReference !== '' ? $paymentReference : null),
            ] as $column => $value) {
                if (db_has_column('orders', $column)) {
                    $columns[] = $column;
                    $values[]  = $value;
                }
            }

            $placeholders = implode(', ', array_fill(0, count($columns), '?'));
            $stmt = $pdo->prepare('INSERT INTO orders (' . implode(', ', $columns) . ') VALUES (' . $placeholders . ')');
            $stmt->execute($values);
            $orderId = (int)$pdo->lastInsertId();

            $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, name, price, qty) VALUES (?, ?, ?, ?, ?)');
            foreach ($cart as $pid => $row) {
                $itemStmt->execute([$orderId, (int)$pid, $row['name'], (float)$row['price'], (int)$row['qty']]);
            }

            $_SESSION['order_meta'][$orderId] = [
                'subtotal'          => $subtotal,
                'discount'          => $discount,
                'coupon_code'       => $couponInfo['applied_code'],
                'referral_code'     => $referralCode,
                'payment_reference' => $paymentReference,
            ];

            cart_clear();
            clear_old();
            set_flash('success', 'Order #' . $orderId . ' confirmed! Thank you, ' . h($name) . '. 🐾');
            redirect('shop/order_confirmation.php?id=' . $orderId);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), '1146') !== false) {
                cart_clear();
                clear_old();
                set_flash('success', 'Thank you, ' . h($name) . '! Your order has been received. 🐾');
                redirect('index.php');
            }
            throw $e;
        }
    } else {
        store_old([
            'name'          => $name,
            'email'         => $email,
            'address'       => $address,
            'payment'       => $payment,
            'coupon_code'   => $couponCode,
            'referral_code' => $referralCode,
            'paynow_name'   => $paynowName,
            'paynow_phone'  => $paynowPhone,
            'gpay_email'    => $gpayEmail,
        ]);
    }
}

$couponInfo = coupon_discount_details($subtotal, old('coupon_code'));
$discount   = (float)$couponInfo['discount'];
$total      = max(0, $subtotal - $discount);

$pageTitle = 'Checkout';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<section style="padding:80px 5%;min-height:70vh;">
  <div style="max-width:920px;margin:0 auto;position:relative;z-index:1;">
    <div class="section-header" style="text-align:left;margin-bottom:40px;">
      <div class="section-tag">💳 Checkout</div>
      <h1 class="section-title">Almost <em>There!</em></h1>
    </div>

    <form method="POST" novalidate id="checkoutForm">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">

      <div style="display:grid;grid-template-columns:1.3fr .9fr;gap:24px;align-items:start;">
        <div style="display:grid;gap:24px;">
          <div class="membership-right">
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
          </div>

          <div style="background:var(--white);border:1.5px solid var(--warm);border-radius:22px;padding:28px 26px;display:grid;gap:18px;">
            <div>
              <p style="font-size:.78rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--orange);margin-bottom:12px;">Voucher & Referral</p>
              <p style="color:var(--brown-md);margin:0;line-height:1.7;">Use <strong>MEOW10</strong> for 10% off orders above $60, and enter a referral code if you joined through a friend.</p>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
              <div class="form-field" style="margin:0;">
                <label for="co-coupon">Voucher Code</label>
                <div style="display:flex;gap:10px;align-items:center;">
                  <input id="co-coupon" type="text" name="coupon_code" value="<?= old('coupon_code') ?>" placeholder="MEOW10" style="text-transform:uppercase;" autocomplete="off"/>
                  <button type="button" id="applyCouponBtn" class="btn-outline" style="white-space:nowrap;">Apply</button>
                </div>
                <p id="couponStatus" style="font-size:.78rem;margin-top:6px;color:<?= !empty($errors['coupon_code']) ? '#f87171' : 'var(--sage)' ?>;"><?= h($errors['coupon_code'] ?? $couponInfo['success']) ?></p>
              </div>
              <div class="form-field" style="margin:0;">
                <label for="co-referral">Referral Code <span style="font-weight:400;color:var(--brown-md);">(optional)</span></label>
                <input id="co-referral" type="text" name="referral_code" value="<?= old('referral_code') ?>" placeholder="Friend's referral code" style="text-transform:uppercase;" autocomplete="off"/>
                <?php if (!empty($errors['referral_code'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:6px;" role="alert"><?= h($errors['referral_code']) ?></p><?php else: ?><p style="font-size:.78rem;color:var(--brown-md);margin-top:6px;">Optional. This is saved with the demo order.</p><?php endif; ?>
              </div>
            </div>
          </div>

          <div style="background:var(--white);border:1.5px solid var(--warm);border-radius:22px;padding:28px 26px;display:grid;gap:20px;">
            <div class="form-field" style="margin:0;">
              <label for="co-payment">Payment Method</label>
              <select id="co-payment" name="payment">
                <option value="card"   <?= old('payment','card') === 'card' ? 'selected' : '' ?>>💳 Credit / Debit Card</option>
                <option value="paynow" <?= old('payment') === 'paynow' ? 'selected' : '' ?>>📱 PayNow</option>
                <option value="gpay"   <?= old('payment') === 'gpay' ? 'selected' : '' ?>>🟠 Google Pay</option>
              </select>
            </div>

            <div id="payment-card" data-payment-section="card" style="display:grid;gap:14px;">
              <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;">
                <div class="form-field" style="margin:0;">
                  <label for="card-name">Name on Card</label>
                  <input id="card-name" type="text" name="card_name" value="" placeholder="Sarah Tan" autocomplete="cc-name"/>
                  <?php if (!empty($errors['card_name'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:6px;"><?= h($errors['card_name']) ?></p><?php endif; ?>
                </div>
                <div class="form-field" style="margin:0;">
                  <label for="card-number">Card Number</label>
                  <input id="card-number" type="text" name="card_number" value="" inputmode="numeric" maxlength="19" placeholder="4242 4242 4242 4242" autocomplete="cc-number"/>
                  <?php if (!empty($errors['card_number'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:6px;"><?= h($errors['card_number']) ?></p><?php endif; ?>
                </div>
              </div>
              <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;">
                <div class="form-field" style="margin:0;">
                  <label for="card-expiry">Expiry</label>
                  <input id="card-expiry" type="text" name="card_expiry" value="" inputmode="numeric" maxlength="5" placeholder="MM/YY" autocomplete="cc-exp"/>
                  <?php if (!empty($errors['card_expiry'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:6px;"><?= h($errors['card_expiry']) ?></p><?php endif; ?>
                </div>
                <div class="form-field" style="margin:0;">
                  <label for="card-cvv">CVV</label>
                  <input id="card-cvv" type="password" name="card_cvv" value="" inputmode="numeric" maxlength="3" placeholder="123" autocomplete="cc-csc"/>
                  <?php if (!empty($errors['card_cvv'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:6px;"><?= h($errors['card_cvv']) ?></p><?php endif; ?>
                </div>
              </div>
            </div>

            <div id="payment-paynow" data-payment-section="paynow" style="display:none;gap:16px;">
              <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;align-items:center;">
                <div style="background:var(--warm);border-radius:22px;padding:18px;text-align:center;">
                  <img src="<?= h(base_url('assets/img/paynow-demo-qr.svg')) ?>" alt="Demo PayNow QR code" style="width:min(100%,220px);height:auto;border-radius:18px;background:#fff;padding:10px;display:block;margin:0 auto 10px;">
                  <p style="margin:0;font-size:.84rem;color:var(--brown-md);">Scan this demo QR to simulate a PayNow payment.</p>
                </div>
                <div style="display:grid;gap:14px;">
                  <div class="form-field" style="margin:0;">
                    <label for="paynow-name">PayNow Account Name</label>
                    <input id="paynow-name" type="text" name="paynow_name" value="<?= old('paynow_name') ?>" placeholder="Sarah Tan" autocomplete="name"/>
                    <?php if (!empty($errors['paynow_name'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:6px;"><?= h($errors['paynow_name']) ?></p><?php endif; ?>
                  </div>
                  <div class="form-field" style="margin:0;">
                    <label for="paynow-phone">Mobile Number</label>
                    <input id="paynow-phone" type="text" name="paynow_phone" value="<?= old('paynow_phone') ?>" inputmode="numeric" placeholder="91234567" autocomplete="tel"/>
                    <?php if (!empty($errors['paynow_phone'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:6px;"><?= h($errors['paynow_phone']) ?></p><?php endif; ?>
                  </div>
                  <div style="background:var(--cream);border-radius:18px;padding:14px 16px;font-size:.88rem;color:var(--brown-md);line-height:1.7;">Demo payee: <strong>UEN T26MEOWMART</strong><br>Reference: <strong>MEOWMART-DEMO</strong></div>
                </div>
              </div>
            </div>

            <div id="payment-gpay" data-payment-section="gpay" style="display:none;gap:16px;">
              <div style="background:linear-gradient(135deg,#fff7ed,#fef3c7);border-radius:22px;padding:18px 18px 10px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:14px;">
                  <div>
                    <p style="font-size:.78rem;letter-spacing:.08em;text-transform:uppercase;color:var(--brown-md);margin:0 0 6px;">Google Pay Demo</p>
                    <h4 style="margin:0;font-size:1.05rem;color:var(--brown);">Fast checkout on your saved Google account</h4>
                  </div>
                  <div style="background:#000;color:#fff;border-radius:999px;padding:10px 16px;font-weight:700;">G Pay</div>
                </div>
                <div class="form-field" style="margin:0;">
                  <label for="gpay-email">Google Account Email</label>
                  <input id="gpay-email" type="email" name="gpay_email" value="<?= old('gpay_email', $user['email'] ?? '') ?>" placeholder="you@gmail.com" autocomplete="email"/>
                  <?php if (!empty($errors['gpay_email'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:6px;"><?= h($errors['gpay_email']) ?></p><?php endif; ?>
                </div>
                <div style="margin-top:14px;background:#fff;border-radius:18px;padding:14px 16px;font-size:.88rem;color:var(--brown-md);line-height:1.7;">This is a demo payment flow for presentation purposes. No real payment is processed.</div>
              </div>
            </div>
          </div>
        </div>

        <div style="position:sticky;top:110px;display:grid;gap:18px;">
          <div style="background:var(--white);border:1.5px solid var(--warm);border-radius:20px;padding:28px 24px;">
            <p style="font-size:.78rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--orange);margin-bottom:16px;">Order Summary</p>
            <?php foreach ($cart as $row): ?>
              <div style="display:flex;justify-content:space-between;gap:12px;margin-bottom:10px;font-size:.9rem;align-items:flex-start;">
                <span style="flex:1;"><?= h($row['name']) ?> × <?= (int)$row['qty'] ?></span>
                <span><?= money((float)$row['price'] * $row['qty']) ?></span>
              </div>
            <?php endforeach; ?>
            <div style="border-top:1px solid var(--warm);margin:16px 0;"></div>
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:.92rem;">
              <span>Subtotal</span><span id="summarySubtotal" data-subtotal="<?= h(number_format($subtotal, 2, '.', '')) ?>"><?= money($subtotal) ?></span>
            </div>
            <div id="discountRow" style="display:<?= $discount > 0 ? 'flex' : 'none' ?>;justify-content:space-between;margin-bottom:8px;font-size:.92rem;color:var(--sage);">
              <span>Voucher Discount</span><span id="summaryDiscount">-<?= money($discount) ?></span>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:.92rem;">
              <span>Delivery</span><span style="color:var(--sage);">FREE</span>
            </div>
            <div style="border-top:1px solid var(--warm);margin:16px 0;"></div>
            <div style="display:flex;justify-content:space-between;font-weight:700;font-size:1.08rem;">
              <span>Total</span><span id="summaryTotal" style="color:var(--orange);"><?= money($total) ?></span>
            </div>
          </div>

          <button class="btn-primary" type="submit" style="width:100%;display:block;text-align:center;font-size:1rem;">
            Place Order 🐾
          </button>
          <p style="text-align:center;font-size:.78rem;color:var(--brown-md);margin:0;">Demo checkout only — no real payment is processed.</p>
        </div>
      </div>
    </form>
  </div>
</section>

<style>
@media (max-width: 860px) {
  #checkoutForm > div { grid-template-columns: 1fr !important; }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var paymentSelect = document.getElementById('co-payment');
  var sections = document.querySelectorAll('[data-payment-section]');
  var couponInput = document.getElementById('co-coupon');
  var couponStatus = document.getElementById('couponStatus');
  var applyCouponBtn = document.getElementById('applyCouponBtn');
  var subtotalEl = document.getElementById('summarySubtotal');
  var discountRow = document.getElementById('discountRow');
  var discountEl = document.getElementById('summaryDiscount');
  var totalEl = document.getElementById('summaryTotal');
  var subtotal = parseFloat(subtotalEl.dataset.subtotal || '0');
  var cardNumberInput = document.getElementById('card-number');
  var cardExpiryInput = document.getElementById('card-expiry');
  var cardCvvInput = document.getElementById('card-cvv');
  var paynowPhoneInput = document.getElementById('paynow-phone');

  function digitsOnly(value) {
    return String(value || '').replace(/\D+/g, '');
  }

  function money(v) {
    return '$' + Number(v).toFixed(2);
  }

  function togglePaymentSection() {
    var selected = paymentSelect ? paymentSelect.value : 'card';
    sections.forEach(function (section) {
      section.style.display = section.dataset.paymentSection === selected ? 'grid' : 'none';
    });
  }

  function updateCouponPreview() {
    var code = (couponInput.value || '').trim().toUpperCase();
    couponInput.value = code;
    var discount = 0;
    var message = '';
    var ok = false;

    if (!code) {
      message = '';
    } else if (code !== 'MEOW10') {
      message = 'That voucher code is not recognised.';
    } else if (subtotal < 60) {
      message = 'MEOW10 applies to orders of $60 or more.';
    } else {
      discount = +(subtotal * 0.10).toFixed(2);
      message = 'MEOW10 applied. You saved ' + money(discount) + '.';
      ok = true;
    }

    couponStatus.textContent = message;
    couponStatus.style.color = ok ? 'var(--sage)' : (message ? '#f87171' : 'var(--brown-md)');
    discountRow.style.display = discount > 0 ? 'flex' : 'none';
    discountEl.textContent = '-' + money(discount);
    totalEl.textContent = money(subtotal - discount);
  }

  if (cardNumberInput) {
    cardNumberInput.addEventListener('input', function () {
      var digits = digitsOnly(cardNumberInput.value).slice(0, 16);
      cardNumberInput.value = digits.replace(/(.{4})/g, '$1 ').trim();
    });
  }

  if (cardExpiryInput) {
    cardExpiryInput.addEventListener('input', function () {
      var digits = digitsOnly(cardExpiryInput.value).slice(0, 4);
      if (digits.length >= 3) {
        cardExpiryInput.value = digits.slice(0, 2) + '/' + digits.slice(2);
      } else {
        cardExpiryInput.value = digits;
      }
    });
  }

  if (cardCvvInput) {
    cardCvvInput.addEventListener('input', function () {
      cardCvvInput.value = digitsOnly(cardCvvInput.value).slice(0, 3);
    });
  }

  if (paynowPhoneInput) {
    paynowPhoneInput.addEventListener('input', function () {
      paynowPhoneInput.value = digitsOnly(paynowPhoneInput.value).slice(0, 8);
    });
  }

  if (paymentSelect) {
    paymentSelect.addEventListener('change', togglePaymentSection);
    togglePaymentSection();
  }
  if (applyCouponBtn) applyCouponBtn.addEventListener('click', updateCouponPreview);
  if (couponInput) couponInput.addEventListener('input', updateCouponPreview);
  updateCouponPreview();
});
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
