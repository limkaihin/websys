<?php
$pageTitle = 'Order Confirmed';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/db.php';

$orderId = (int)($_GET['id'] ?? 0);
if ($orderId < 1) {
    redirect('index.php');
}

$pdo  = db();
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    set_flash('error', 'Order not found.');
    redirect('index.php');
}

// Security: only owner or admin can view
$user = current_user();
if ($order['user_id'] && (!$user || ((int)$user['id'] !== (int)$order['user_id'] && !is_admin()))) {
    set_flash('error', 'You do not have permission to view this order.');
    redirect('index.php');
}

$items = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
$items->execute([$orderId]);
$items = $items->fetchAll();

$paymentLabels = [
    'card'   => '💳 Credit / Debit Card',
    'paynow' => '📱 PayNow',
    'grab'   => '🟢 GrabPay',
];
?>

<section style="padding:60px 5%;min-height:70vh;">
  <div style="max-width:720px;margin:0 auto;">

    <!-- Success Banner -->
    <div style="background:linear-gradient(135deg,#166534,#15803d);border-radius:28px;padding:40px 36px;text-align:center;margin-bottom:36px;color:#fff;">
      <div style="font-size:4rem;margin-bottom:16px;">🎉</div>
      <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:900;margin-bottom:8px;">Order Confirmed!</h1>
      <p style="font-size:1rem;opacity:.9;margin-bottom:4px;">Thank you, <strong><?= h($order['name']) ?></strong>!</p>
      <p style="font-size:.88rem;opacity:.75;">Order #<?= (int)$order['id'] ?> · <?= date('d M Y, g:i A', strtotime($order['created_at'])) ?></p>
    </div>

    <!-- Status Tracker -->
    <div style="background:var(--white);border-radius:24px;padding:32px 36px;margin-bottom:24px;box-shadow:0 2px 16px rgba(61,35,20,.07);">
      <h2 style="font-family:'Playfair Display',serif;font-size:1.2rem;margin-bottom:28px;">📦 Delivery Status</h2>
      <?php
        $steps = [
          'confirmed'  => ['✅','Order Confirmed',  'Your order has been received'],
          'shipped'    => ['🚚','Shipped',          'On its way to you'],
          'delivered'  => ['🏠','Delivered',        'Enjoy your purchase!'],
        ];
        $statusOrder = ['confirmed','shipped','delivered'];
        $currentIdx  = array_search($order['status'], $statusOrder);
        if ($currentIdx === false) $currentIdx = 0;
      ?>
      <div style="display:flex;align-items:flex-start;gap:0;position:relative;">
        <?php foreach ($statusOrder as $i => $s):
          $done   = $i <= $currentIdx;
          $active = $i === $currentIdx;
        ?>
          <div style="flex:1;text-align:center;position:relative;">
            <?php if ($i < count($statusOrder)-1): ?>
              <div style="position:absolute;top:18px;left:50%;width:100%;height:3px;background:<?= $i < $currentIdx ? 'var(--orange)' : 'var(--warm)' ?>;z-index:0;"></div>
            <?php endif; ?>
            <div style="position:relative;z-index:1;display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:50%;background:<?= $done ? 'var(--orange)' : 'var(--warm)' ?>;font-size:1.1rem;margin-bottom:8px;"><?= $steps[$s][0] ?></div>
            <div style="font-size:.78rem;font-weight:<?= $active?'700':'400' ?>;color:<?= $active?'var(--orange)':'var(--brown-md)' ?>;"><?= $steps[$s][1] ?></div>
            <div style="font-size:.7rem;color:var(--brown-md);opacity:.75;"><?= $steps[$s][2] ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Order Items -->
    <div style="background:var(--white);border-radius:24px;padding:32px 36px;margin-bottom:24px;box-shadow:0 2px 16px rgba(61,35,20,.07);">
      <h2 style="font-family:'Playfair Display',serif;font-size:1.2rem;margin-bottom:20px;">🛒 Items Ordered</h2>
      <?php foreach ($items as $item): ?>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid var(--warm);">
          <div>
            <div style="font-size:.95rem;font-weight:600;"><?= h($item['name']) ?></div>
            <div style="font-size:.8rem;color:var(--brown-md);">Qty: <?= (int)$item['qty'] ?> × <?= money((float)$item['price']) ?></div>
          </div>
          <div style="font-weight:700;color:var(--brown);"><?= money((float)$item['price'] * $item['qty']) ?></div>
        </div>
      <?php endforeach; ?>
      <div style="display:flex;justify-content:space-between;margin-top:16px;font-size:.9rem;color:var(--brown-md);">
        <span>Delivery</span><span style="color:var(--sage);">FREE</span>
      </div>
      <div style="display:flex;justify-content:space-between;margin-top:10px;font-weight:700;font-size:1.1rem;">
        <span>Total Paid</span>
        <span style="color:var(--orange);font-family:'Playfair Display',serif;"><?= money((float)$order['total']) ?></span>
      </div>
    </div>

    <!-- Delivery Details -->
    <div style="background:var(--white);border-radius:24px;padding:32px 36px;margin-bottom:24px;box-shadow:0 2px 16px rgba(61,35,20,.07);">
      <h2 style="font-family:'Playfair Display',serif;font-size:1.2rem;margin-bottom:20px;">📋 Delivery Details</h2>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div>
          <div style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--orange);margin-bottom:4px;">Name</div>
          <div style="font-size:.95rem;"><?= h($order['name']) ?></div>
        </div>
        <div>
          <div style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--orange);margin-bottom:4px;">Email</div>
          <div style="font-size:.95rem;"><?= h($order['email']) ?></div>
        </div>
        <div style="grid-column:span 2;">
          <div style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--orange);margin-bottom:4px;">Delivery Address</div>
          <div style="font-size:.95rem;"><?= nl2br(h($order['address'])) ?></div>
        </div>
        <div>
          <div style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--orange);margin-bottom:4px;">Payment</div>
          <div style="font-size:.95rem;"><?= h($paymentLabels[$order['payment']] ?? $order['payment']) ?></div>
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
      <?php if ($user): ?>
        <a href="<?= h(base_url('shop/orders.php')) ?>" class="btn-primary" style="text-decoration:none;display:inline-block;flex:1;text-align:center;">
          View All Orders
        </a>
      <?php endif; ?>
      <a href="<?= h(base_url('shop/products.php')) ?>" class="btn-outline" style="text-decoration:none;display:inline-block;flex:1;text-align:center;">
        Continue Shopping
      </a>
    </div>
  </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
