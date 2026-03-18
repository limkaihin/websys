<?php
/**
 * MeowMart Navbar
 * Desktop: Logo | Nav Links | Icons + User Dropdown
 * Mobile:  Logo | Icons | Hamburger → slide-in drawer from right
 */
$user      = current_user();
$firstName = '';
if ($user) {
    $firstName = explode(' ', trim($user['name']))[0];
    if (strlen($firstName) > 10) $firstName = substr($firstName, 0, 9) . '...';
}
?>

<!-- ══ DESKTOP NAVBAR ═══════════════════════════════════════════════ -->
<nav class="navbar-shell" aria-label="Main navigation">

  <a href="<?= h(base_url('index.php')) ?>" class="nav-logo" aria-label="MeowMart home">
    <div class="paw" aria-hidden="true"><i class="fa-solid fa-cat"></i></div>
    <div class="brand">Meow<span>Mart</span></div>
  </a>

  <ul class="nav-links list-unstyled mb-0" role="list">
    <li><a href="<?= h(base_url('shop/products.php')) ?>"><i class="fa-solid fa-store fa-xs"></i> Shop</a></li>
    <li><a href="<?= h(base_url('index.php')) ?>#categories"><i class="fa-solid fa-border-all fa-xs"></i> Categories</a></li>
    <li><a href="<?= h(base_url('index.php')) ?>#membership"><i class="fa-solid fa-crown fa-xs"></i> Membership</a></li>
    <li><a href="<?= h(base_url('content/blog.php')) ?>"><i class="fa-solid fa-newspaper fa-xs"></i> Blog</a></li>
    <li><a href="<?= h(base_url('content/about.php')) ?>"><i class="fa-solid fa-circle-info fa-xs"></i> About</a></li>
    <li><a href="<?= h(base_url('content/contact.php')) ?>"><i class="fa-solid fa-envelope fa-xs"></i> Contact</a></li>
  </ul>

  <div class="nav-actions">

    <a href="<?= h(base_url('shop/wishlist.php')) ?>" class="nav-icon"
       aria-label="Wishlist<?= wishlist_count() > 0 ? ' ('.wishlist_count().' items)' : '' ?>">
      <i class="fa-<?= wishlist_count() > 0 ? 'solid' : 'regular' ?> fa-heart"></i>
      <?php if (wishlist_count() > 0): ?><span class="badge" aria-hidden="true"><?= wishlist_count() ?></span><?php endif; ?>
    </a>

    <a href="<?= h(base_url('shop/cart.php')) ?>" class="nav-icon"
       aria-label="Cart<?= cart_count() > 0 ? ' ('.cart_count().' items)' : '' ?>">
      <i class="fa-solid fa-cart-shopping"></i>
      <?php if (cart_count() > 0): ?><span class="badge" aria-hidden="true"><?= cart_count() ?></span><?php endif; ?>
    </a>

    <!-- Desktop: user dropdown or guest buttons -->
    <div class="nav-user-area">
      <?php if ($user): ?>
        <button class="nav-user-btn" id="userMenuTrigger"
                aria-haspopup="true" aria-expanded="false" aria-controls="userDropdown"
                title="<?= h($user['name']) ?>">
          <i class="fa-solid fa-user fa-xs"></i>
          <span class="nav-username"><?= h($firstName) ?></span>
          <i class="fa-solid fa-chevron-down fa-xs nav-chevron"></i>
        </button>
        <div class="nav-user-dropdown" id="userDropdown" role="menu" aria-hidden="true">
          <div class="nav-dropdown-header">
            <div class="nav-dropdown-name"><?= h($user['name']) ?></div>
            <div class="nav-dropdown-email"><?= h($user['email']) ?></div>
          </div>
          <?php if (is_admin()): ?>
          <a href="<?= h(base_url('admin/dashboard.php')) ?>" class="nav-dropdown-item" role="menuitem">
            <i class="fa-solid fa-gauge-high"></i> Admin Dashboard
          </a>
          <?php endif; ?>
          <a href="<?= h(base_url('shop/orders.php')) ?>" class="nav-dropdown-item" role="menuitem">
            <i class="fa-solid fa-box-open"></i> My Orders
          </a>
          <a href="<?= h(base_url('account/profile.php')) ?>" class="nav-dropdown-item" role="menuitem">
            <i class="fa-solid fa-user"></i> My Profile
          </a>
          <div class="nav-dropdown-divider"></div>
          <a href="<?= h(base_url('account/logout.php')) ?>" class="nav-dropdown-item nav-dropdown-logout" role="menuitem">
            <i class="fa-solid fa-right-from-bracket"></i> Log Out
          </a>
        </div>
      <?php else: ?>
        <a href="<?= h(base_url('account/login.php')) ?>" class="btn-member btn-member-outline btn-member-sm">
          <i class="fa-solid fa-right-to-bracket fa-xs"></i> Log In
        </a>
        <a href="<?= h(base_url('account/register.php')) ?>" class="btn-member btn-member-sm">
          <i class="fa-solid fa-user-plus fa-xs"></i> Join Free
        </a>
      <?php endif; ?>
    </div>

    <!-- Hamburger — mobile only -->
    <button class="nav-hamburger" id="navToggle" type="button"
            aria-label="Open navigation menu" aria-expanded="false" aria-controls="mobileDrawer">
      <span></span><span></span><span></span>
    </button>
  </div>

</nav>

<!-- ══ MOBILE OVERLAY ════════════════════════════════════════════════ -->
<div class="mobile-overlay" id="mobileOverlay" aria-hidden="true"></div>

<!-- ══ MOBILE DRAWER — single flat list, no nested navs ═════════════ -->
<div class="mobile-drawer" id="mobileDrawer" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Navigation menu">

  <!-- Drawer header -->
  <div class="drawer-header">
    <a href="<?= h(base_url('index.php')) ?>" class="drawer-logo">
      <div class="paw" style="width:28px;height:28px;font-size:.8rem;flex-shrink:0;"><i class="fa-solid fa-cat"></i></div>
      <span class="drawer-logo-text">Meow<span>Mart</span></span>
    </a>
    <button class="drawer-close" id="drawerClose" aria-label="Close menu">
      <i class="fa-solid fa-xmark"></i>
    </button>
  </div>

  <!-- Scrollable body — ONE wrapper, no nested flex containers -->
  <div class="drawer-body">

    <!-- User card OR login buttons -->
    <?php if ($user): ?>
    <div class="drawer-user-card">
      <div class="drawer-user-avatar"><i class="fa-solid fa-cat"></i></div>
      <div class="drawer-user-info">
        <div class="drawer-user-name"><?= h($user['name']) ?></div>
        <div class="drawer-user-email"><?= h($user['email']) ?></div>
      </div>
    </div>
    <?php else: ?>
    <div class="drawer-guest-btns">
      <a href="<?= h(base_url('account/login.php')) ?>" class="drawer-guest-btn drawer-guest-outline">
        <i class="fa-solid fa-right-to-bracket"></i> Log In
      </a>
      <a href="<?= h(base_url('account/register.php')) ?>" class="drawer-guest-btn drawer-guest-filled">
        <i class="fa-solid fa-user-plus"></i> Join Free
      </a>
    </div>
    <?php endif; ?>

    <!-- Section: Browse -->
    <div class="drawer-section-label">Browse</div>
    <a href="<?= h(base_url('shop/products.php')) ?>" class="drawer-link">
      <span class="drawer-icon"><i class="fa-solid fa-store"></i></span>Shop
    </a>
    <a href="<?= h(base_url('index.php')) ?>#categories" class="drawer-link">
      <span class="drawer-icon"><i class="fa-solid fa-border-all"></i></span>Categories
    </a>
    <a href="<?= h(base_url('index.php')) ?>#membership" class="drawer-link">
      <span class="drawer-icon"><i class="fa-solid fa-crown"></i></span>Membership
    </a>
    <a href="<?= h(base_url('content/blog.php')) ?>" class="drawer-link">
      <span class="drawer-icon"><i class="fa-solid fa-newspaper"></i></span>Blog
    </a>
    <a href="<?= h(base_url('content/about.php')) ?>" class="drawer-link">
      <span class="drawer-icon"><i class="fa-solid fa-circle-info"></i></span>About
    </a>
    <a href="<?= h(base_url('content/contact.php')) ?>" class="drawer-link">
      <span class="drawer-icon"><i class="fa-solid fa-envelope"></i></span>Contact
    </a>

    <?php if ($user): ?>
    <!-- Section: My Account -->
    <div class="drawer-section-sep"></div>
    <div class="drawer-section-label">My Account</div>
    <?php if (is_admin()): ?>
    <a href="<?= h(base_url('admin/dashboard.php')) ?>" class="drawer-link drawer-link-admin">
      <span class="drawer-icon drawer-icon-orange"><i class="fa-solid fa-gauge-high"></i></span>Admin Dashboard
    </a>
    <?php endif; ?>
    <a href="<?= h(base_url('shop/orders.php')) ?>" class="drawer-link">
      <span class="drawer-icon"><i class="fa-solid fa-box-open"></i></span>My Orders
    </a>
    <a href="<?= h(base_url('account/profile.php')) ?>" class="drawer-link">
      <span class="drawer-icon"><i class="fa-solid fa-user"></i></span>My Profile
    </a>
    <a href="<?= h(base_url('account/logout.php')) ?>" class="drawer-link drawer-link-logout">
      <span class="drawer-icon drawer-icon-red"><i class="fa-solid fa-right-from-bracket"></i></span>Log Out
    </a>
    <?php endif; ?>

  </div><!-- /.drawer-body -->
</div>
