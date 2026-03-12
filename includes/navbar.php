<?php $user = current_user(); ?>
<nav class="navbar-shell" aria-label="Main navigation">
  <a href="<?= h(base_url('index.php')) ?>" class="nav-logo text-decoration-none">
    <div class="paw" aria-hidden="true">🐱</div>
    <div class="brand">Meow<span>Mart</span></div>
  </a>
  <ul class="nav-links list-unstyled mb-0">
    <li><a href="<?= h(base_url('shop/products.php')) ?>">Shop</a></li>
    <li><a href="<?= h(base_url('index.php')) ?>#categories">Categories</a></li>
    <li><a href="<?= h(base_url('index.php')) ?>#membership">Membership</a></li>
    <li><a href="<?= h(base_url('content/blog.php')) ?>">Blog</a></li>
    <li><a href="<?= h(base_url('content/about.php')) ?>">About</a></li>
    <li><a href="<?= h(base_url('content/contact.php')) ?>">Contact</a></li>
  </ul>
  <div class="nav-actions">
    <button class="nav-icon" type="button" title="Search" aria-label="Search the site">🔍</button>
    <button class="nav-icon" type="button" title="Wishlist" aria-label="Open wishlist">🤍</button>
    <a href="<?= h(base_url('shop/cart.php')) ?>" class="nav-icon text-decoration-none" title="Cart" aria-label="View shopping cart">
      🛒
      <span class="badge"><?= cart_count() ?: 0 ?></span>
    </a>
    <?php if ($user): ?>
      <?php if (is_admin()): ?>
        <a href="<?= h(base_url('admin/dashboard.php')) ?>" class="btn-member">Admin</a>
      <?php endif; ?>
      <a href="<?= h(base_url('account/profile.php')) ?>" class="btn-member"><?= h($user['name']) ?></a>
      <a href="<?= h(base_url('account/logout.php')) ?>" class="btn-member" style="background:var(--brown-md);">Logout</a>
    <?php else: ?>
      <a href="<?= h(base_url('account/login.php')) ?>" class="btn-member" style="text-decoration:none;background:var(--white);color:var(--brown);">Log In</a>
      <a href="<?= h(base_url('account/register.php')) ?>" class="btn-member" style="text-decoration:none;">Join Free</a>
    <?php endif; ?>
  </div>
</nav>
