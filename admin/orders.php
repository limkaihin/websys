<?php
$pageTitle = 'Manage Orders';
require_once dirname(__DIR__) . '/includes/functions.php';
require_admin();
require_once dirname(__DIR__) . '/includes/db.php';

$pdo = db();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $orderId   = (int)post('order_id');
    $newStatus = post('status');
    $allowed   = ['confirmed','shipped','delivered','cancelled'];
    if ($orderId > 0 && in_array($newStatus, $allowed, true)) {
        $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$newStatus, $orderId]);
        set_flash('success', 'Order #' . $orderId . ' updated to ' . $newStatus . '.');
    }
    redirect('admin/orders.php');
}

// Filters
$filterStatus = $_GET['status'] ?? 'all';
$search       = trim($_GET['q'] ?? '');
$allowed      = ['all','confirmed','shipped','delivered','cancelled'];
if (!in_array($filterStatus, $allowed, true)) $filterStatus = 'all';

$sql    = 'SELECT o.*, COUNT(oi.id) AS item_count FROM orders o
           LEFT JOIN order_items oi ON oi.order_id = o.id WHERE 1=1';
$params = [];
if ($filterStatus !== 'all') {
    $sql .= ' AND o.status = ?';
    $params[] = $filterStatus;
}
if ($search !== '') {
    $sql .= ' AND (o.name LIKE ? OR o.email LIKE ? OR o.id = ?)';
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = (int)$search;
}
$sql .= ' GROUP BY o.id ORDER BY o.created_at DESC';
$orders = [];
$tablesMissing = false;
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    if (strpos($e->getMessage(), '1146') !== false) { $tablesMissing = true; }
    else throw $e;
}

// Stats
$stats = [];
try {
    $stats = $pdo->query('SELECT status, COUNT(*) as cnt, SUM(total) as rev FROM orders GROUP BY status')->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { if (strpos($e->getMessage(), '1146') === false) throw $e; }
$statMap = [];
foreach ($stats as $s) $statMap[$s['status']] = $s;
$totalRev = array_sum(array_column($stats, 'rev'));
$totalOrders = array_sum(array_column($stats, 'cnt'));

$statusColors = [
    'confirmed'  => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'Confirmed'],
    'shipped'    => ['bg'=>'#dbeafe','color'=>'#1d4ed8','label'=>'Shipped'],
    'delivered'  => ['bg'=>'#dcfce7','color'=>'#166534','label'=>'Delivered'],
    'cancelled'  => ['bg'=>'#fee2e2','color'=>'#991b1b','label'=>'Cancelled'],
];
// ── Output starts here ─────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/includes/header.php';

?>

<?php include __DIR__ . '/sidebar.php'; ?>

<div style="margin-left:240px;padding:40px 48px;min-height:100vh;background:var(--cream);">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;flex-wrap:wrap;gap:12px;">
    <div>
      <div class="section-tag">📦 Admin</div>
      <h1 class="section-title" style="font-size:2rem;">Order <em>Management</em></h1>
    </div>
  </div>

  <!-- Summary stats -->
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:36px;">
    <?php
      $cards = [
        ['label'=>'Total Orders','value'=>$totalOrders,'icon'=>'📦','color'=>'var(--brown)'],
        ['label'=>'Confirmed','value'=>$statMap['confirmed']['cnt']??0,'icon'=>'✅','color'=>'#92400e'],
        ['label'=>'Shipped','value'=>$statMap['shipped']['cnt']??0,'icon'=>'🚚','color'=>'#1d4ed8'],
        ['label'=>'Revenue','value'=>money($totalRev),'icon'=>'💰','color'=>'var(--orange)'],
      ];
      foreach ($cards as $c):
    ?>
    <div style="background:var(--white);border-radius:20px;padding:24px 22px;box-shadow:0 2px 12px rgba(61,35,20,.06);">
      <div style="font-size:1.8rem;margin-bottom:8px;"><?= $c['icon'] ?></div>
      <div style="font-size:1.5rem;font-family:'Playfair Display',serif;font-weight:900;color:<?= $c['color'] ?>;"><?= $c['value'] ?></div>
      <div style="font-size:.78rem;color:var(--brown-md);"><?= $c['label'] ?></div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Filter bar -->
  <div style="display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap;align-items:center;">
    <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap;">
      <input type="text" name="q" value="<?= h($search) ?>" placeholder="Search by name, email or order #"
             style="flex:1;min-width:180px;padding:10px 16px;border:1.5px solid var(--warm);border-radius:12px;font-family:'DM Sans',sans-serif;background:var(--white);"/>
      <?php foreach (['all'=>'All','confirmed'=>'Confirmed','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled'] as $k=>$v): ?>
        <button type="submit" name="status" value="<?= $k ?>"
          style="padding:9px 18px;border-radius:20px;border:1.5px solid <?= $filterStatus===$k?'var(--orange)':'transparent' ?>;
                 background:<?= $filterStatus===$k?'var(--orange)':'var(--white)' ?>;
                 color:<?= $filterStatus===$k?'#fff':'var(--brown-md)' ?>;
                 font-size:.83rem;font-weight:500;cursor:pointer;"><?= $v ?></button>
      <?php endforeach; ?>
    </form>
  </div>

  <!-- Orders table -->
  <div style="background:var(--white);border-radius:20px;overflow:hidden;box-shadow:0 2px 12px rgba(61,35,20,.06);">
    <div class="table-responsive">
      <table style="width:100%;border-collapse:collapse;font-size:.88rem;">
        <thead>
          <tr style="background:var(--warm);">
            <th style="padding:14px 20px;text-align:left;font-weight:700;color:var(--brown);white-space:nowrap;">#</th>
            <th style="padding:14px 20px;text-align:left;font-weight:700;color:var(--brown);">Customer</th>
            <th style="padding:14px 20px;text-align:left;font-weight:700;color:var(--brown);">Date</th>
            <th style="padding:14px 20px;text-align:left;font-weight:700;color:var(--brown);">Items</th>
            <th style="padding:14px 20px;text-align:left;font-weight:700;color:var(--brown);">Total</th>
            <th style="padding:14px 20px;text-align:left;font-weight:700;color:var(--brown);">Status</th>
            <th style="padding:14px 20px;text-align:left;font-weight:700;color:var(--brown);">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
            <tr><td colspan="7" style="text-align:center;padding:48px;color:var(--brown-md);">No orders found.</td></tr>
          <?php else: ?>
            <?php foreach ($orders as $o):
              $sc = $statusColors[$o['status']] ?? ['bg'=>'#f3f4f6','color'=>'#374151','label'=>ucfirst($o['status'])];
            ?>
            <tr style="border-bottom:1px solid var(--warm);">
              <td style="padding:16px 20px;font-weight:700;color:var(--orange);">#<?= (int)$o['id'] ?></td>
              <td style="padding:16px 20px;">
                <div style="font-weight:600;"><?= h($o['name']) ?></div>
                <div style="font-size:.78rem;color:var(--brown-md);"><?= h($o['email']) ?></div>
              </td>
              <td style="padding:16px 20px;white-space:nowrap;color:var(--brown-md);"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
              <td style="padding:16px 20px;"><?= (int)$o['item_count'] ?></td>
              <td style="padding:16px 20px;font-weight:700;"><?= money((float)$o['total']) ?></td>
              <td style="padding:16px 20px;">
                <span style="background:<?= $sc['bg'] ?>;color:<?= $sc['color'] ?>;border-radius:20px;padding:4px 12px;font-size:.75rem;font-weight:700;"><?= h($sc['label']) ?></span>
              </td>
              <td style="padding:16px 20px;">
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                  <a href="<?= h(base_url('shop/order_confirmation.php?id=' . $o['id'])) ?>"
                     style="background:var(--warm);color:var(--brown);border-radius:14px;padding:6px 14px;font-size:.78rem;font-weight:600;text-decoration:none;">View</a>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="order_id"  value="<?= (int)$o['id'] ?>">
                    <select name="status" onchange="this.form.submit()"
                      style="padding:6px 10px;border-radius:12px;border:1.5px solid var(--warm);font-size:.78rem;background:var(--white);cursor:pointer;font-family:'DM Sans',sans-serif;">
                      <?php foreach (['confirmed','shipped','delivered','cancelled'] as $st): ?>
                        <option value="<?= $st ?>" <?= $o['status']===$st?'selected':'' ?>><?= ucfirst($st) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
