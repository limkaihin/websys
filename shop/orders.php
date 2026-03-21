<?php
$pageTitle = 'My Orders';
require_once dirname(__DIR__) . '/includes/functions.php';
require_login();
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/db.php';

$pdo  = db();
$user = current_user();

$activeTab = $_GET['status'] ?? 'all';
$allowed   = ['all','confirmed','shipped','delivered','cancelled'];
if (!in_array($activeTab, $allowed, true)) $activeTab = 'all';

$orders        = [];
$tablesMissing = false;

try {
    if (!empty($user['id']) && !empty($user['email'])) {
        $adoptStmt = $pdo->prepare('UPDATE orders SET user_id = ? WHERE user_id IS NULL AND LOWER(email) = LOWER(?)');
        $adoptStmt->execute([(int)$user['id'], (string)$user['email']]);
    }

    $sql    = 'SELECT o.*, COUNT(oi.id) AS item_count FROM orders o
               LEFT JOIN order_items oi ON oi.order_id = o.id
               WHERE o.user_id = ?';
    $params = [(int)$user['id']];

    if ($activeTab !== 'all') {
        $sql    .= ' AND o.status = ?';
        $params[] = $activeTab;
    }
    $sql .= ' GROUP BY o.id ORDER BY o.created_at DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    if (strpos($e->getMessage(), '1146') !== false) {
        $tablesMissing = true;
    } else {
        throw $e;
    }
}

$statusColors = [
    'confirmed' => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'Confirmed'],
    'shipped'   => ['bg'=>'#dbeafe','color'=>'#1d4ed8','label'=>'Shipped'],
    'delivered' => ['bg'=>'#dcfce7','color'=>'#166534','label'=>'Delivered'],
    'cancelled' => ['bg'=>'#fee2e2','color'=>'#991b1b','label'=>'Cancelled'],
];
$tabs = [
    'all'       => 'All Orders',
    'confirmed' => '✅ To Ship',
    'shipped'   => '🚚 Shipping',
    'delivered' => '🏠 Completed',
    'cancelled' => '✕ Cancelled',
];
?>

<section style="padding:60px 5%;min-height:70vh;">
  <div style="max-width:860px;margin:0 auto;">

    <div class="section-header" style="text-align:left;margin-bottom:32px;">
      <div class="section-tag">📦 My Account</div>
      <h1 class="section-title">My <em>Orders</em></h1>
    </div>

    <?php if ($tablesMissing): ?>
      <!-- Database not yet migrated — show setup notice instead of crashing -->
      <div style="background:var(--white);border-radius:20px;padding:48px 36px;text-align:center;border:1.5px solid var(--warm);">
        <div style="font-size:4rem;margin-bottom:20px;">🔧</div>
        <h2 style="font-family:'Playfair Display',serif;margin-bottom:12px;">Database Setup Required</h2>
        <p style="color:var(--brown-md);margin-bottom:8px;">The orders tables haven't been created yet.</p>
        <p style="color:var(--brown-md);font-size:.88rem;margin-bottom:28px;">
          Please run this command on your server:<br>
          <code style="background:var(--warm);padding:6px 14px;border-radius:8px;font-size:.85rem;display:inline-block;margin-top:8px;">
            mysql -u root -p meowmart &lt; sql/migrate.sql
          </code>
        </p>
        <a href="<?= h(base_url('shop/products.php')) ?>" class="btn-primary" style="text-decoration:none;display:inline-block;">Continue Shopping</a>
      </div>
    <?php else: ?>
      <!-- Tab bar -->
      <div style="display:flex;border-bottom:2px solid var(--warm);margin-bottom:32px;overflow-x:auto;">
        <?php foreach ($tabs as $key => $label): ?>
          <a href="?status=<?= h($key) ?>"
             style="padding:12px 20px;font-size:.85rem;font-weight:<?= $activeTab===$key?'700':'500' ?>;
                    color:<?= $activeTab===$key?'var(--orange)':'var(--brown-md)' ?>;
                    border-bottom:2px solid <?= $activeTab===$key?'var(--orange)':'transparent' ?>;
                    text-decoration:none;white-space:nowrap;transition:color .2s;margin-bottom:-2px;">
            <?= h($label) ?>
          </a>
        <?php endforeach; ?>
      </div>

      <?php if (empty($orders)): ?>
        <div style="text-align:center;padding:80px 20px;">
          <div style="font-size:5rem;margin-bottom:20px;">📭</div>
          <h2 style="font-family:'Playfair Display',serif;margin-bottom:12px;">No orders yet</h2>
          <p style="color:var(--brown-md);margin-bottom:28px;">Looks like your cat hasn't placed any orders!</p>
          <a href="<?= h(base_url('shop/products.php')) ?>" class="btn-primary" style="text-decoration:none;display:inline-block;">Start Shopping →</a>
        </div>
      <?php else: ?>
        <?php foreach ($orders as $order):
          $sc = $statusColors[$order['status']] ?? ['bg'=>'#f3f4f6','color'=>'#374151','label'=>ucfirst($order['status'])];
        ?>
          <div style="background:var(--white);border-radius:20px;margin-bottom:20px;overflow:hidden;box-shadow:0 2px 16px rgba(61,35,20,.06);border:1.5px solid var(--warm);">
            <!-- Header -->
            <div style="padding:16px 24px;background:var(--warm);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
              <div style="display:flex;align-items:center;flex-wrap:wrap;gap:20px;">
                <div>
                  <div style="font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--brown-md);">Order</div>
                  <div style="font-size:.95rem;font-weight:700;color:var(--brown);">#<?= (int)$order['id'] ?></div>
                </div>
                <div>
                  <div style="font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--brown-md);">Date</div>
                  <div style="font-size:.88rem;color:var(--brown);"><?= date('d M Y', strtotime($order['created_at'])) ?></div>
                </div>
                <div>
                  <div style="font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--brown-md);">Items</div>
                  <div style="font-size:.88rem;color:var(--brown);"><?= (int)$order['item_count'] ?> item<?= $order['item_count']!=1?'s':'' ?></div>
                </div>
              </div>
              <span style="background:<?= $sc['bg'] ?>;color:<?= $sc['color'] ?>;border-radius:20px;padding:4px 14px;font-size:.78rem;font-weight:700;">
                <?= h($sc['label']) ?>
              </span>
            </div>

            <!-- Items preview -->
            <?php
              try {
                  $itemsStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ? LIMIT 3');
                  $itemsStmt->execute([$order['id']]);
                  $previewItems = $itemsStmt->fetchAll();
              } catch (PDOException $e) { $previewItems = []; }
            ?>
            <div style="padding:20px 24px;">
              <?php foreach ($previewItems as $item): ?>
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:12px;">
                  <div style="width:48px;height:48px;border-radius:12px;background:var(--warm);display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;"><?= product_icon_from_text((string)($item['name'] ?? '')) ?></div>
                  <div style="flex:1;">
                    <div style="font-size:.9rem;font-weight:600;color:var(--brown);"><?= h($item['name']) ?></div>
                    <div style="font-size:.78rem;color:var(--brown-md);">Qty: <?= (int)$item['qty'] ?></div>
                  </div>
                  <div style="font-size:.9rem;font-weight:700;"><?= money((float)$item['price'] * $item['qty']) ?></div>
                </div>
              <?php endforeach; ?>
              <?php if ((int)$order['item_count'] > 3): ?>
                <p style="font-size:.8rem;color:var(--brown-md);margin-top:4px;">+<?= (int)$order['item_count'] - 3 ?> more item(s)</p>
              <?php endif; ?>
            </div>

            <!-- Footer -->
            <div style="padding:16px 24px;border-top:1.5px solid var(--warm);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
              <div>
                <span style="font-size:.8rem;color:var(--brown-md);">Order Total: </span>
                <span style="font-family:'Playfair Display',serif;font-size:1.15rem;font-weight:900;color:var(--orange);"><?= money((float)$order['total']) ?></span>
              </div>
              <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="<?= h(base_url('shop/order_confirmation.php?id=' . $order['id'])) ?>"
                   style="background:var(--orange);color:#fff;border-radius:20px;padding:8px 20px;font-size:.82rem;font-weight:600;text-decoration:none;">
                  View Details →
                </a>
                <?php if ($order['status'] === 'delivered'): ?>
                  <a href="<?= h(base_url('shop/products.php')) ?>"
                     style="background:var(--warm);color:var(--brown);border-radius:20px;padding:8px 20px;font-size:.82rem;font-weight:600;text-decoration:none;">
                    Buy Again
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
