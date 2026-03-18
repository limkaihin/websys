<?php
/**
 * Admin Dashboard
 * Integrates Chart.js (Open Source Project #1) for admin data visualisation.
 */
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

// Orders data for Chart.js
try {
    $totalOrders   = (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
    $pendingOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='confirmed'")->fetchColumn();
    $revenue       = (float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'cancelled'")->fetchColumn();

    // Orders by status for doughnut chart
    $statusCounts = [];
    $statusRows   = $pdo->query("SELECT status, COUNT(*) as cnt FROM orders GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($statusRows as $r) $statusCounts[$r['status']] = (int)$r['cnt'];

    // Revenue last 7 days for line chart
    $revRows = $pdo->query("
        SELECT DATE(created_at) as day, COALESCE(SUM(total),0) as rev
        FROM orders
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND status != 'cancelled'
        GROUP BY DATE(created_at) ORDER BY day ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $chartDays = [];
    $chartRevs = [];
    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i days"));
        $chartDays[] = date('d M', strtotime($d));
        $found = array_filter($revRows, function($r) use ($d) { return $r['day'] === $d; });
        $chartRevs[] = $found ? (float)array_values($found)[0]['rev'] : 0;
    }

    // Products by category for bar chart
    $catRows = $pdo->query("SELECT category, COUNT(*) as cnt FROM products GROUP BY category ORDER BY cnt DESC")->fetchAll(PDO::FETCH_ASSOC);
    $catLabels = array_column($catRows, 'category');
    $catCounts = array_column($catRows, 'cnt');

    // Orders last 7 days count for secondary line
    $orderCountRows = $pdo->query("
        SELECT DATE(created_at) as day, COUNT(*) as cnt
        FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at) ORDER BY day ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
    $chartOrderCounts = [];
    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i days"));
        $found = array_filter($orderCountRows, function($r) use ($d) { return $r['day'] === $d; });
        $chartOrderCounts[] = $found ? (int)array_values($found)[0]['cnt'] : 0;
    }

    $recentOrders = $pdo->query('SELECT o.*, COUNT(oi.id) AS item_count FROM orders o
        LEFT JOIN order_items oi ON oi.order_id = o.id
        GROUP BY o.id ORDER BY o.created_at DESC LIMIT 5')->fetchAll();
    $hasOrders = true;
} catch (Throwable $e) {
    $hasOrders = false; $totalOrders = $pendingOrders = 0; $revenue = 0;
    $statusCounts = []; $chartDays = []; $chartRevs = [];
    $catLabels = []; $catCounts = []; $chartOrderCounts = []; $recentOrders = [];
}
?>

<div style="display:flex;min-height:80vh;">
  <?php include __DIR__ . '/sidebar.php'; ?>

  <section style="flex:1;padding:40px 48px;background:var(--cream);overflow-x:hidden;">
    <div class="section-tag" style="margin-bottom:12px;"><i class="fa-solid fa-gauge-high"></i> Admin Panel</div>
    <h1 class="section-title" style="margin-bottom:32px;">Dashboard <em>Overview</em></h1>

    <!-- Stat cards -->
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:24px;">
      <?php
      $cards = [
        ['fa-box-open',    $products,   'Products',       'products.php', 'var(--orange)'],
        ['fa-newspaper',   $posts,      'Blog Posts',     'blog_posts.php','var(--sage)'],
        ['fa-users',       $users,      'Members',        'users.php',    'var(--gold)'],
        ['fa-envelope',    $unreadMsgs, 'Unread Messages','messages.php', 'var(--brown)'],
      ];
      foreach ($cards as [$icon, $count, $label, $link, $color]):
      ?>
      <div style="background:var(--white);border-radius:20px;padding:28px;box-shadow:0 2px 12px rgba(61,35,20,.06);">
        <div style="font-size:1.8rem;color:<?= $color ?>;margin-bottom:8px;"><i class="fa-solid <?= $icon ?>"></i></div>
        <div style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:900;color:<?= $color ?>;line-height:1;"><?= is_numeric($count) ? $count : $count ?></div>
        <div style="font-size:.8rem;color:var(--brown-md);margin:6px 0 16px;"><?= $label ?></div>
        <a href="<?= h(base_url('admin/' . $link)) ?>" class="btn-outline" style="text-decoration:none;padding:7px 16px;font-size:.78rem;">Manage →</a>
      </div>
      <?php endforeach; ?>
    </div>

    <?php if ($hasOrders): ?>
    <!-- Order stat cards -->
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:32px;">
      <?php
      $ocards = [
        ['fa-boxes-stacked', $totalOrders,    'Total Orders','var(--brown)'],
        ['fa-clock',         $pendingOrders,  'To Ship',     '#92400e'],
        ['fa-dollar-sign',   money($revenue), 'Revenue',     'var(--orange)'],
      ];
      foreach ($ocards as [$icon, $val, $lbl, $col]):
      ?>
      <div style="background:var(--white);border-radius:20px;padding:24px;box-shadow:0 2px 12px rgba(61,35,20,.06);">
        <div style="font-size:1.5rem;color:<?= $col ?>;margin-bottom:8px;"><i class="fa-solid <?= $icon ?>"></i></div>
        <div style="font-family:'Playfair Display',serif;font-size:1.8rem;font-weight:900;color:<?= $col ?>;line-height:1;"><?= $val ?></div>
        <div style="font-size:.8rem;color:var(--brown-md);margin-top:6px;"><?= $lbl ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- ── Chart.js Charts (Open Source Project #1) ── -->
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;margin-bottom:32px;">
      <!-- Revenue & Orders Line Chart -->
      <div style="background:var(--white);border-radius:20px;padding:28px;box-shadow:0 2px 12px rgba(61,35,20,.06);">
        <h3 style="font-family:'Playfair Display',serif;font-size:1rem;margin-bottom:20px;color:var(--brown);">
          <i class="fa-solid fa-chart-line" style="color:var(--orange);"></i> Revenue – Last 7 Days
        </h3>
        <canvas id="revenueChart" height="120" aria-label="Revenue over last 7 days" role="img"></canvas>
      </div>
      <!-- Orders by Status Doughnut -->
      <div style="background:var(--white);border-radius:20px;padding:28px;box-shadow:0 2px 12px rgba(61,35,20,.06);">
        <h3 style="font-family:'Playfair Display',serif;font-size:1rem;margin-bottom:20px;color:var(--brown);">
          <i class="fa-solid fa-chart-pie" style="color:var(--orange);"></i> Orders by Status
        </h3>
        <canvas id="statusChart" aria-label="Orders by status" role="img"></canvas>
      </div>
    </div>

    <!-- Products by Category Bar Chart -->
    <div style="background:var(--white);border-radius:20px;padding:28px;margin-bottom:32px;box-shadow:0 2px 12px rgba(61,35,20,.06);">
      <h3 style="font-family:'Playfair Display',serif;font-size:1rem;margin-bottom:20px;color:var(--brown);">
        <i class="fa-solid fa-chart-bar" style="color:var(--orange);"></i> Products by Category
      </h3>
      <canvas id="categoryChart" height="80" aria-label="Products by category" role="img"></canvas>
    </div>

    <!-- Recent orders table -->
    <?php if (!empty($recentOrders)): ?>
    <div style="background:var(--white);border-radius:20px;padding:28px;margin-bottom:32px;box-shadow:0 2px 12px rgba(61,35,20,.06);">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h3 style="font-family:'Playfair Display',serif;font-size:1rem;"><i class="fa-solid fa-receipt" style="color:var(--orange);"></i> Recent Orders</h3>
        <a href="<?= h(base_url('admin/orders.php')) ?>" style="color:var(--orange);font-size:.82rem;font-weight:600;text-decoration:none;">View All →</a>
      </div>
      <div class="table-responsive">
        <table style="width:100%;border-collapse:collapse;font-size:.85rem;">
          <thead>
            <tr style="border-bottom:2px solid var(--warm);">
              <th style="padding:8px 12px;text-align:left;color:var(--brown-md);">#</th>
              <th style="padding:8px 12px;text-align:left;color:var(--brown-md);">Customer</th>
              <th style="padding:8px 12px;text-align:left;color:var(--brown-md);">Total</th>
              <th style="padding:8px 12px;text-align:left;color:var(--brown-md);">Status</th>
              <th style="padding:8px 12px;text-align:left;color:var(--brown-md);">Date</th>
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
      <a href="<?= h(base_url('admin/product_form.php')) ?>" class="btn-primary" style="text-decoration:none;"><i class="fa-solid fa-plus"></i> Add Product</a>
      <a href="<?= h(base_url('admin/blog_form.php')) ?>"   class="btn-outline" style="text-decoration:none;"><i class="fa-solid fa-pen"></i> Add Blog Post</a>
      <a href="<?= h(base_url('admin/orders.php')) ?>"      class="btn-outline" style="text-decoration:none;"><i class="fa-solid fa-box"></i> Manage Orders</a>
    </div>
  </section>
</div>

<!-- Chart.js 4.x (Open Source Project #1) via CDN with SRI (Font Awesome best practice: use SRI hashes) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"
        integrity="sha256-3pgzFI+zMnMvJu9ku9LnD9GEt6BSdCB2dWRuBqGQCsU="
        crossorigin="anonymous"></script>

<?php if ($hasOrders): ?>
<script>
// ── Chart.js integration (Open Source Project #1) ──────────────────────────
// Global defaults matching MeowMart brand palette
Chart.defaults.font.family = "'DM Sans', sans-serif";
Chart.defaults.color = '#6B3C22';
Chart.defaults.plugins.legend.labels.boxRadius = 4;

// 1. Revenue Line Chart
const revenueCtx = document.getElementById('revenueChart');
if (revenueCtx) {
  new Chart(revenueCtx, {
    type: 'line',
    data: {
      labels: <?= json_encode($chartDays) ?>,
      datasets: [
        {
          label: 'Revenue (SGD)',
          data: <?= json_encode($chartRevs) ?>,
          borderColor: '#E8651A',
          backgroundColor: 'rgba(232,101,26,0.1)',
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#E8651A',
          pointRadius: 4,
          yAxisID: 'y',
        },
        {
          label: 'Orders',
          data: <?= json_encode($chartOrderCounts) ?>,
          borderColor: '#8FA882',
          backgroundColor: 'rgba(143,168,130,0.08)',
          fill: false,
          tension: 0.4,
          pointBackgroundColor: '#8FA882',
          pointRadius: 4,
          yAxisID: 'y1',
        }
      ]
    },
    options: {
      responsive: true,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        tooltip: {
          callbacks: {
            label: ctx => ctx.datasetIndex === 0
              ? ' $' + ctx.parsed.y.toFixed(2)
              : ' ' + ctx.parsed.y + ' orders'
          }
        }
      },
      scales: {
        y:  { type: 'linear', display: true, position: 'left',  title: { display: true, text: 'Revenue ($)' }, beginAtZero: true },
        y1: { type: 'linear', display: true, position: 'right', title: { display: true, text: 'Orders' },      beginAtZero: true, grid: { drawOnChartArea: false } }
      }
    }
  });
}

// 2. Orders by Status Doughnut Chart
const statusCtx = document.getElementById('statusChart');
if (statusCtx) {
  const statusData = <?= json_encode([
    'confirmed' => $statusCounts['confirmed'] ?? 0,
    'shipped'   => $statusCounts['shipped']   ?? 0,
    'delivered' => $statusCounts['delivered'] ?? 0,
    'cancelled' => $statusCounts['cancelled'] ?? 0,
  ]) ?>;
  new Chart(statusCtx, {
    type: 'doughnut',
    data: {
      labels: ['Confirmed', 'Shipped', 'Delivered', 'Cancelled'],
      datasets: [{
        data: Object.values(statusData),
        backgroundColor: ['#fef3c7', '#dbeafe', '#dcfce7', '#fee2e2'],
        borderColor:     ['#92400e',  '#1d4ed8', '#166534',  '#991b1b'],
        borderWidth: 2,
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' },
        tooltip: { callbacks: { label: ctx => ' ' + ctx.label + ': ' + ctx.parsed } }
      },
      cutout: '65%',
    }
  });
}

// 3. Products by Category Bar Chart
const catCtx = document.getElementById('categoryChart');
if (catCtx) {
  new Chart(catCtx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($catLabels) ?>,
      datasets: [{
        label: 'Products',
        data: <?= json_encode(array_map('intval', $catCounts)) ?>,
        backgroundColor: ['#F5924E','#8FA882','#C8941A','#F0C4A8','#6B3C22'],
        borderRadius: 8,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1 } }
      }
    }
  });
}
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
