<?php
$pageTitle = 'Order Confirmed';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/db.php';

$orderId = (int)($_GET['id'] ?? 0);
if ($orderId < 1) { redirect('index.php'); }

$pdo   = db();
$order = null;
$items = [];

try {
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();

    if (!$order) {
        set_flash('error', 'Order not found.');
        redirect('index.php');
    }

    $user = current_user();
    if ($order['user_id'] && (!$user || ((int)$user['id'] !== (int)$order['user_id'] && !is_admin()))) {
        set_flash('error', 'You do not have permission to view this order.');
        redirect('index.php');
    }

    $iStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
    $iStmt->execute([$orderId]);
    $items = $iStmt->fetchAll();
} catch (PDOException $e) {
    if (strpos($e->getMessage(), '1146') !== false) {
        $user = current_user();
?>
<section style="padding:80px 5%;min-height:70vh;text-align:center;">
  <div style="max-width:560px;margin:0 auto;">
    <div style="background:linear-gradient(135deg,#166534,#15803d);border-radius:28px;padding:48px 36px;color:#fff;margin-bottom:32px;">
      <div style="font-size:4rem;margin-bottom:16px;">🎉</div>
      <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:900;margin-bottom:8px;">Order Received!</h1>
      <p style="opacity:.9;">Thank you for your purchase.</p>
    </div>
    <a href="<?= h(base_url('shop/products.php')) ?>" class="btn-primary" style="text-decoration:none;display:inline-block;">Continue Shopping</a>
  </div>
</section>
<?php
        require_once dirname(__DIR__) . '/includes/footer.php';
        exit;
    }
    throw $e;
}

$paymentLabels = ['card'=>'💳 Credit / Debit Card','paynow'=>'📱 PayNow','gpay'=>'🟠 Google Pay','grab'=>'🟢 GrabPay'];
$user = current_user();
$sessionMeta = $_SESSION['order_meta'][$orderId] ?? [];
$calculatedSubtotal = 0.0;
foreach ($items as $item) {
    $calculatedSubtotal += (float)$item['price'] * (int)$item['qty'];
}
$discount         = (float)($order['discount'] ?? $sessionMeta['discount'] ?? 0);
$couponCode       = trim((string)($order['coupon_code'] ?? $sessionMeta['coupon_code'] ?? ''));
$referralCode     = trim((string)($order['referral_code'] ?? $sessionMeta['referral_code'] ?? ''));
$paymentReference = trim((string)($order['payment_reference'] ?? $sessionMeta['payment_reference'] ?? ''));
$subtotal         = (float)($sessionMeta['subtotal'] ?? ($calculatedSubtotal + $discount));
if ($subtotal <= 0) {
    $subtotal = $calculatedSubtotal;
}
unset($_SESSION['order_meta'][$orderId]);
?>

<section style="padding:60px 5%;min-height:70vh;">
  <div style="max-width:720px;margin:0 auto;">
    <div style="background:linear-gradient(135deg,#166534,#15803d);border-radius:28px;padding:40px 36px;text-align:center;margin-bottom:36px;color:#fff;">
      <div style="font-size:4rem;margin-bottom:16px;">🎉</div>
      <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:900;margin-bottom:8px;">Order Confirmed!</h1>
      <p style="font-size:1rem;opacity:.9;margin-bottom:4px;">Thank you, <strong><?= h($order['name']) ?></strong>!</p>
      <p style="font-size:.88rem;opacity:.75;">Order #<?= (int)$order['id'] ?> · <?= date('d M Y, g:i A', strtotime($order['created_at'])) ?></p>
    </div>

    <div class="order-status-card">
      <h2 style="font-family:'Playfair Display',serif;font-size:1.2rem;margin-bottom:28px;">📦 Delivery Status</h2>
      <?php
        $steps      = ['confirmed'=>['✅','Order Confirmed','Your order has been received'], 'shipped'=>['🚚','Shipped','On its way to you'], 'delivered'=>['🏠','Delivered','Enjoy your purchase!']];
        $statusOrder = ['confirmed','shipped','delivered'];
        $currentIdx  = array_search($order['status'], $statusOrder);
        if ($currentIdx === false) $currentIdx = 0;
      ?>
      <div class="order-status-track">
        <?php foreach ($statusOrder as $i => $s): $done = $i <= $currentIdx; $active = $i === $currentIdx; ?>
          <div class="order-status-step">
            <?php if ($i < count($statusOrder)-1): ?>
              <div class="order-status-line" style="background:<?= $i < $currentIdx ? 'var(--orange)' : 'var(--warm)' ?>;"></div>
            <?php endif; ?>
            <div style="position:relative;z-index:1;display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:50%;background:<?= $done ? 'var(--orange)' : 'var(--warm)' ?>;font-size:1.1rem;margin-bottom:8px;"><?= $steps[$s][0] ?></div>
            <div>
              <div style="font-size:.78rem;font-weight:<?= $active?'700':'400' ?>;color:<?= $active?'var(--orange)':'var(--brown-md)' ?>;"><?= $steps[$s][1] ?></div>
              <div style="font-size:.7rem;color:var(--brown-md);opacity:.75;"><?= $steps[$s][2] ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="order-items-card">
      <h2 style="font-family:'Playfair Display',serif;font-size:1.2rem;margin-bottom:20px;">🛒 Items Ordered</h2>
      <?php foreach ($items as $item): ?>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid var(--warm);gap:12px;">
          <div>
            <div style="font-size:.95rem;font-weight:600;"><?= h($item['name']) ?></div>
            <div style="font-size:.8rem;color:var(--brown-md);">Qty: <?= (int)$item['qty'] ?> × <?= money((float)$item['price']) ?></div>
          </div>
          <div style="font-weight:700;color:var(--brown);"><?= money((float)$item['price'] * $item['qty']) ?></div>
        </div>
      <?php endforeach; ?>
      <div style="display:flex;justify-content:space-between;margin-top:16px;font-size:.9rem;color:var(--brown-md);">
        <span>Subtotal</span><span><?= money($subtotal) ?></span>
      </div>
      <?php if ($discount > 0): ?>
        <div style="display:flex;justify-content:space-between;margin-top:10px;font-size:.9rem;color:var(--sage);">
          <span>Voucher Discount<?= $couponCode !== '' ? ' (' . h($couponCode) . ')' : '' ?></span><span>-<?= money($discount) ?></span>
        </div>
      <?php endif; ?>
      <div style="display:flex;justify-content:space-between;margin-top:10px;font-size:.9rem;color:var(--brown-md);">
        <span>Delivery</span><span style="color:var(--sage);">FREE</span>
      </div>
      <div style="display:flex;justify-content:space-between;margin-top:10px;font-weight:700;font-size:1.1rem;">
        <span>Total Paid</span>
        <span style="color:var(--orange);font-family:'Playfair Display',serif;"><?= money((float)$order['total']) ?></span>
      </div>
    </div>

    <div class="order-delivery-card">
      <h2 style="font-family:'Playfair Display',serif;font-size:1.2rem;margin-bottom:20px;">📋 Delivery Details</h2>
      <div class="order-delivery-grid">
        <div>
          <div style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--orange);margin-bottom:4px;">Name</div>
          <div style="font-size:.95rem;"><?= h($order['name']) ?></div>
        </div>
        <div>
          <div style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--orange);margin-bottom:4px;">Email</div>
          <div style="font-size:.95rem;"><?= h($order['email']) ?></div>
        </div>
        <div class="order-delivery-address">
          <div style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--orange);margin-bottom:4px;">Delivery Address</div>
          <div style="font-size:.95rem;"><?= nl2br(h($order['address'])) ?></div>
        </div>
        <div>
          <div style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--orange);margin-bottom:4px;">Payment</div>
          <div style="font-size:.95rem;"><?= h($paymentLabels[$order['payment']] ?? $order['payment']) ?></div>
          <?php if ($paymentReference !== ''): ?><div style="font-size:.82rem;color:var(--brown-md);margin-top:6px;"><?= h($paymentReference) ?></div><?php endif; ?>
        </div>
        <?php if ($referralCode !== ''): ?>
          <div>
            <div style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--orange);margin-bottom:4px;">Referral Code</div>
            <div style="font-size:.95rem;"><?= h($referralCode) ?></div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="order-actions">
      <?php if ($user): ?>
        <a href="<?= h(base_url('shop/orders.php')) ?>" class="btn-primary" style="text-decoration:none;display:inline-block;flex:1;text-align:center;">View All Orders</a>
      <?php endif; ?>
      <a href="<?= h(base_url('shop/products.php')) ?>" class="btn-outline" style="text-decoration:none;display:inline-block;flex:1;text-align:center;">Continue Shopping</a>
    </div>
  </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
