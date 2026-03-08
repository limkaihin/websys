<aside style="width:220px;background:var(--brown);padding:40px 24px;display:flex;flex-direction:column;gap:8px;flex-shrink:0;">
  <p style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:900;color:var(--cream);margin-bottom:20px;">Admin Panel</p>
  <?php
  $links = [
    ['dashboard.php',    '📊 Dashboard'],
    ['products.php',     '🛒 Products'],
    ['product_form.php', '➕ Add Product'],
    ['blog_posts.php',   '📖 Blog Posts'],
    ['blog_form.php',    '➕ Add Post'],
  ];
  $current = basename($_SERVER['PHP_SELF']);
  foreach ($links as [$file, $label]):
  ?>
  <a href="<?= h(base_url('admin/' . $file)) ?>"
     style="text-decoration:none;padding:10px 16px;border-radius:12px;font-size:.88rem;font-weight:500;
            color:<?= $current === $file ? '#fff' : 'rgba(240,196,168,.7)' ?>;
            background:<?= $current === $file ? 'var(--orange)' : 'transparent' ?>;
            transition:background .2s;"
     onmouseover="if('<?= $current ?>'!=='<?= $file ?>') this.style.background='rgba(255,255,255,.08)'"
     onmouseout="if('<?= $current ?>'!=='<?= $file ?>') this.style.background='transparent'">
    <?= $label ?>
  </a>
  <?php endforeach; ?>
  <div style="flex:1;"></div>
  <a href="<?= h(base_url('index.php')) ?>" style="text-decoration:none;padding:10px 16px;border-radius:12px;font-size:.82rem;color:rgba(240,196,168,.5);">← Back to Site</a>
</aside>
