<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$pdo      = db();
$products = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$posts    = (int)$pdo->query('SELECT COUNT(*) FROM blog_posts')->fetchColumn();
$users    = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
try { $unreadMsgs = (int)$pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn(); }
catch (Throwable $e) { $unreadMsgs = 0; }

// Orders stats (graceful if table doesn't exist yet)
try {
    $totalOrders   = (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
    $pendingOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='confirmed'")->fetchColumn();
    $revenue       = (float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
    $recentOrders  = $pdo->query('SELECT o.*, COUNT(oi.id) AS item_count FROM orders o
        LEFT JOIN order_items oi ON oi.order_id = o.id
        GROUP BY o.id ORDER BY o.created_at DESC LIMIT 5')->fetchAll();
    $hasOrders = true;
} catch (Throwable $e) {
    $hasOrders = false;
    $totalOrders = $pendingOrders = 0;
    $revenue = 0;
    $recentOrders = [];
}
?>

<div style="display:flex;min-height:80vh;flex-wrap:wrap;">
  <?php include __DIR__ . '/sidebar.php'; ?>

  <section style="flex:1;padding:60px 48px;background:var(--cream);">
    <div class="section-tag" style="margin-bottom:12px;">⚙️ Admin Panel</div>
    <h1 class="section-title" style="margin-bottom:40px;">Dashboard <em>Overview</em></h1>

    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:24px;margin-bottom:24px;">
      <?php
      $cards = [
        ['🛒', $products,    'Products',      'products.php',  'var(--orange)'],
        ['📖', $posts,       'Blog Posts',    'blog_posts.php','var(--sage)'],
        ['👤', $users,       'Members',       'users.php',     'var(--gold)'],
        ['💬', $unreadMsgs,  'Unread Messages','messages.php', 'var(--brown)'],
      ];
      foreach ($cards as [$icon, $count, $label, $link, $color]):
      ?>
      <div style="background:var(--white);border-radius:24px;padding:36px;box-shadow:0 4px 20px rgba(61,35,20,.08);">
        <div style="font-size:2.5rem;margin-bottom:12px;"><?= $icon ?></div>
        <div style="font-family:'Playfair Display',serif;font-size:2.5rem;font-weight:900;color:<?= $color ?>;line-height:1;"><?= $count ?></div>
        <div style="font-size:.85rem;color:var(--brown-md);margin:6px 0 20px;"><?= $label ?></div>
        <a href="<?= h(base_url('admin/' . $link)) ?>" class="btn-outline" style="text-decoration:none;padding:8px 20px;font-size:.82rem;">Manage →</a>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Order stats row -->
    <?php if ($hasOrders): ?>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-bottom:40px;">
      <?php
      $ocards = [
        ['📦', $totalOrders,  'Total Orders',   'var(--brown)'],
        ['⏳', $pendingOrders,'To Ship',         '#92400e'],
        ['💰', money($revenue),'Revenue',        'var(--orange)'],
      ];
      foreach ($ocards as [$icon, $val, $lbl, $col]):
      ?>
      <div style="background:var(--white);border-radius:24px;padding:28px;box-shadow:0 4px 20px rgba(61,35,20,.08);">
        <div style="font-size:1.8rem;margin-bottom:8px;"><?= $icon ?></div>
        <div style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:900;color:<?= $col ?>;line-height:1;"><?= $val ?></div>
        <div style="font-size:.82rem;color:var(--brown-md);margin-top:6px;"><?= $lbl ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Recent orders -->
    <?php if (!empty($recentOrders)): ?>
    <div style="background:var(--white);border-radius:20px;padding:28px 32px;margin-bottom:32px;box-shadow:0 2px 12px rgba(61,35,20,.06);">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h3 style="font-family:'Playfair Display',serif;font-size:1.1rem;">Recent Orders</h3>
        <a href="<?= h(base_url('admin/orders.php')) ?>" style="color:var(--orange);font-size:.82rem;font-weight:600;text-decoration:none;">View All →</a>
      </div>
      <div class="table-responsive">
        <table style="width:100%;border-collapse:collapse;font-size:.85rem;">
          <thead>
            <tr style="border-bottom:2px solid var(--warm);">
              <th style="padding:8px 12px;text-align:left;color:var(--brown-md);font-weight:600;">#</th>
              <th style="padding:8px 12px;text-align:left;color:var(--brown-md);font-weight:600;">Customer</th>
              <th style="padding:8px 12px;text-align:left;color:var(--brown-md);font-weight:600;">Total</th>
              <th style="padding:8px 12px;text-align:left;color:var(--brown-md);font-weight:600;">Status</th>
              <th style="padding:8px 12px;text-align:left;color:var(--brown-md);font-weight:600;">Date</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sc = ['confirmed'=>['#fef3c7','#92400e'],'shipped'=>['#dbeafe','#1d4ed8'],'delivered'=>['#dcfce7','#166534'],'cancelled'=>['#fee2e2','#991b1b']];
            foreach ($recentOrders as $o):
              $c = $sc[$o['status']] ?? ['#f3f4f6','#374151'];
            ?>
            <tr style="border-bottom:1px solid var(--warm);">
              <td style="padding:10px 12px;font-weight:700;color:var(--orange);">#<?= $o['id'] ?></td>
              <td style="padding:10px 12px;"><?= h($o['name']) ?></td>
              <td style="padding:10px 12px;font-weight:700;"><?= money((float)$o['total']) ?></td>
              <td style="padding:10px 12px;"><span style="background:<?= $c[0] ?>;color:<?= $c[1] ?>;border-radius:16px;padding:3px 10px;font-size:.72rem;font-weight:700;"><?= ucfirst($o['status']) ?></span></td>
              <td style="padding:10px 12px;color:var(--brown-md);"><?= date('d M', strtotime($o['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <div style="display:flex;gap:16px;flex-wrap:wrap;">
      <a href="<?= h(base_url('admin/product_form.php')) ?>" class="btn-primary" style="text-decoration:none;">+ Add Product</a>
      <a href="<?= h(base_url('admin/blog_form.php')) ?>"   class="btn-outline" style="text-decoration:none;">+ Add Blog Post</a>
      <a href="<?= h(base_url('admin/orders.php')) ?>"      class="btn-outline" style="text-decoration:none;">📦 Manage Orders</a>
    </div>
  </section>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
