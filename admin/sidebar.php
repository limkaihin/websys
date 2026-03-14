<?php $current = basename($_SERVER['PHP_SELF']); ?>
<aside class="admin-sidebar" role="navigation" aria-label="Admin navigation">
  <div class="admin-sidebar-inner">
    <p class="admin-sidebar-title">Admin Panel</p>
    <?php
    $links = [
      ['dashboard.php',    '📊 Dashboard'],
      ['orders.php',       '📦 Orders'],
      ['users.php',        '👤 Members'],
      ['products.php',     '🛒 Products'],
      ['product_form.php', '➕ Add Product'],
      ['blog_posts.php',   '📖 Blog Posts'],
      ['blog_form.php',    '➕ Add Post'],
      ['messages.php',     '💬 Messages'],
    ];
    foreach ($links as [$file, $label]):
      $active = $current === $file;
    ?>
    <a href="<?= h(base_url('admin/' . $file)) ?>"
       class="admin-nav-link <?= $active ? 'active' : '' ?>"
       aria-current="<?= $active ? 'page' : 'false' ?>">
      <?= $label ?>
    </a>
    <?php endforeach; ?>
    <div style="flex:1;"></div>
    <a href="<?= h(base_url('index.php')) ?>" class="admin-nav-link" style="opacity:.55;margin-top:8px;">← Back to Site</a>
  </div>
</aside>
