<?php
/**
 * Admin Sidebar
 * Integrates Font Awesome Free icons (Open Source Project #4)
 */
?>
<aside style="width:220px;background:var(--brown);padding:32px 20px;display:flex;flex-direction:column;gap:4px;flex-shrink:0;min-height:80vh;">
  <p style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:900;color:var(--cream);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
    <i class="fa-solid fa-cat" style="color:var(--orange);"></i> Admin Panel
  </p>
  <?php
  $links = [
    ['dashboard.php',    'fa-gauge-high',   'Dashboard'],
    ['orders.php',       'fa-box-open',     'Orders'],
    ['users.php',        'fa-users',        'Members'],
    ['products.php',     'fa-store',        'Products'],
    ['product_form.php', 'fa-plus-circle',  'Add Product'],
    ['blog_posts.php',   'fa-newspaper',    'Blog Posts'],
    ['blog_form.php',    'fa-pen-to-square','Add Post'],
    ['messages.php',     'fa-envelope',     'Messages'],
  ];
  $current = basename($_SERVER['PHP_SELF']);
  foreach ($links as [$file, $icon, $label]):
    $active = $current === $file;
  ?>
  <a href="<?= h(base_url('admin/' . $file)) ?>"
     style="text-decoration:none;padding:10px 14px;border-radius:12px;font-size:.875rem;font-weight:500;
            display:flex;align-items:center;gap:10px;transition:background .2s;
            color:<?= $active ? '#fff' : 'rgba(240,196,168,.75)' ?>;
            background:<?= $active ? 'var(--orange)' : 'transparent' ?>;"
     onmouseover="if(!<?= $active ? 'true' : 'false' ?>) this.style.background='rgba(255,255,255,.08)'"
     onmouseout="if(!<?= $active ? 'true' : 'false' ?>) this.style.background='transparent'">
    <i class="fa-solid <?= $icon ?>" style="width:16px;text-align:center;"></i> <?= $label ?>
  </a>
  <?php endforeach; ?>
  <div style="flex:1;"></div>
  <a href="<?= h(base_url('index.php')) ?>"
     style="text-decoration:none;padding:10px 14px;border-radius:12px;font-size:.82rem;
            color:rgba(240,196,168,.5);display:flex;align-items:center;gap:8px;">
    <i class="fa-solid fa-arrow-left"></i> Back to Site
  </a>
</aside>
