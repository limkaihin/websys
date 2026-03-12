<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
$pdo      = db();
$pdo      = db();
$products = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$posts    = (int)$pdo->query('SELECT COUNT(*) FROM blog_posts')->fetchColumn();
$users    = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
?>

<div style="display:flex;min-height:80vh;">
  <?php include __DIR__ . '/sidebar.php'; ?>

  <section style="flex:1;padding:60px 48px;">
    <div class="section-tag" style="margin-bottom:12px;">⚙️ Admin Panel</div>
    <h1 class="section-title" style="margin-bottom:40px;">Dashboard <em>Overview</em></h1>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-bottom:48px;">
      <?php
      $cards = [
        ['🛒', $products, 'Products',   'products.php',   'var(--orange)'],
        ['📖', $posts,    'Blog Posts',  'blog_posts.php', 'var(--sage)'],
        ['👤', $users,    'Members',     '#',              'var(--gold)'],
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

    <div style="display:flex;gap:16px;">
      <a href="<?= h(base_url('admin/product_form.php')) ?>"  class="btn-primary"  style="text-decoration:none;">+ Add Product</a>
      <a href="<?= h(base_url('admin/blog_form.php')) ?>"     class="btn-outline"  style="text-decoration:none;">+ Add Blog Post</a>
    </div>
  </section>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
