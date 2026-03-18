<?php
$pageTitle = 'Manage Users';
require_once dirname(__DIR__) . '/includes/functions.php';
require_admin();
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/db.php';

$pdo   = db();
$users = [];
try {
    $users = $pdo->query('SELECT u.*, COUNT(o.id) AS order_count FROM users u
        LEFT JOIN orders o ON o.user_id = u.id
        GROUP BY u.id ORDER BY u.created_at DESC')->fetchAll();
} catch (PDOException $e) {
    if (strpos($e->getMessage(), '1146') !== false) {
        // Orders table not yet migrated — show users without order count
        $users = $pdo->query('SELECT *, 0 AS order_count FROM users ORDER BY created_at DESC')->fetchAll();
    } else {
        throw $e;
    }
}
?>

<?php include __DIR__ . '/sidebar.php'; ?>

<div style="margin-left:240px;padding:40px 48px;min-height:100vh;background:var(--cream);">
  <div style="margin-bottom:32px;">
    <div class="section-tag">👤 Admin</div>
    <h1 class="section-title" style="font-size:2rem;">Member <em>Management</em></h1>
  </div>

  <div style="background:var(--white);border-radius:20px;overflow:hidden;box-shadow:0 2px 12px rgba(61,35,20,.06);">
    <div class="table-responsive">
      <table style="width:100%;border-collapse:collapse;font-size:.88rem;">
        <thead>
          <tr style="background:var(--warm);">
            <th style="padding:14px 20px;text-align:left;font-weight:700;color:var(--brown);">ID</th>
            <th style="padding:14px 20px;text-align:left;font-weight:700;color:var(--brown);">Name</th>
            <th style="padding:14px 20px;text-align:left;font-weight:700;color:var(--brown);">Email</th>
            <th style="padding:14px 20px;text-align:left;font-weight:700;color:var(--brown);">Cat</th>
            <th style="padding:14px 20px;text-align:left;font-weight:700;color:var(--brown);">Role</th>
            <th style="padding:14px 20px;text-align:left;font-weight:700;color:var(--brown);">Orders</th>
            <th style="padding:14px 20px;text-align:left;font-weight:700;color:var(--brown);">Joined</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <tr style="border-bottom:1px solid var(--warm);">
            <td style="padding:14px 20px;color:var(--orange);font-weight:700;">#<?= (int)$u['id'] ?></td>
            <td style="padding:14px 20px;font-weight:600;"><?= h($u['name']) ?></td>
            <td style="padding:14px 20px;color:var(--brown-md);"><?= h($u['email']) ?></td>
            <td style="padding:14px 20px;"><?= $u['cat_name'] ? '🐱 ' . h($u['cat_name']) : '—' ?></td>
            <td style="padding:14px 20px;">
              <span style="background:<?= $u['role']==='admin'?'var(--orange)':'var(--warm)' ?>;
                            color:<?= $u['role']==='admin'?'#fff':'var(--brown)' ?>;
                            border-radius:16px;padding:3px 12px;font-size:.75rem;font-weight:700;">
                <?= ucfirst(h($u['role'])) ?>
              </span>
            </td>
            <td style="padding:14px 20px;">
              <?php if ($u['order_count'] > 0): ?>
                <a href="<?= h(base_url('admin/orders.php?q=' . urlencode($u['email']))) ?>"
                   style="color:var(--orange);font-weight:600;text-decoration:none;"><?= (int)$u['order_count'] ?> order<?= $u['order_count']!=1?'s':'' ?></a>
              <?php else: ?>
                <span style="color:var(--brown-md);">0</span>
              <?php endif; ?>
            </td>
            <td style="padding:14px 20px;color:var(--brown-md);"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
