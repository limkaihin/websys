<?php $user = current_user(); ?>
  <!-- NAVIGATION -->
  <nav>
    <a href="<?= h(base_url('index.php')) ?>" class="nav-logo">
      <div class="paw">🐱</div>
      <div class="brand">Meow<span>Mart</span></div>
    </a>
    <ul class="nav-links">
      <li><a href="<?= h(base_url('products.php')) ?>">Shop</a></li>
      <li><a href="<?= h(base_url('index.php')) ?>#categories">Categories</a></li>
      <li><a href="<?= h(base_url('index.php')) ?>#membership">Membership</a></li>
      <li><a href="<?= h(base_url('blog.php')) ?>">Blog</a></li>
      <li><a href="<?= h(base_url('about.php')) ?>">About</a></li>
    </ul>
    <div class="nav-actions">
      <button class="nav-icon" title="Search">🔍</button>
      <button class="nav-icon" title="Wishlist">🤍</button>
      <a href="<?= h(base_url('cart.php')) ?>" class="nav-icon" style="text-decoration:none;" title="Cart">
        🛒
        <span class="badge"><?= cart_count() ?: 0 ?></span>
      </a>
      <?php if ($user): ?>
        <?php if (is_admin()): ?>
          <a href="<?= h(base_url('admin/dashboard.php')) ?>" class="btn-member">Admin</a>
        <?php endif; ?>
        <a href="<?= h(base_url('profile.php')) ?>" class="btn-member"><?= h($user['name']) ?></a>
        <a href="<?= h(base_url('logout.php')) ?>" class="btn-member" style="background:var(--brown-md);">Logout</a>
      <?php else: ?>
        <a href="<?= h(base_url('register.php')) ?>" class="btn-member" style="text-decoration:none;">Join Free</a>
      <?php endif; ?>
    </div>
  </nav>
