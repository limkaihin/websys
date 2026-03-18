<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$_cartCount = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) $_cartCount += (int)$item['qty'];
}
?>
<nav>
  <a href="/index.php" class="nav-logo">
    <div class="paw">🐱</div>
    <div class="brand">Meow<span>Mart</span></div>
  </a>
  <ul class="nav-links">
    <li><a href="/shop/products.php">Shop</a></li>
    <li><a href="/shop/categories.php">Categories</a></li>
    <li><a href="/index.php#membership">Membership</a></li>
    <li><a href="/blog/index.php">Blog</a></li>
    <li><a href="/about.php">About</a></li>
    <li><a href="/contact.php">Contact</a></li>
  </ul>
  <div class="nav-actions">
    <a href="/shop/search.php" class="nav-icon" style="text-decoration:none;" title="Search">🔍</a>
     <a href="/shop/cart.php" class="nav-icon" style="text-decoration:none;" title="Cart">
      🛒 <span class="badge"><?= $_cartCount ?></span>
    </a>
    <?php if (!empty($_SESSION['loggedin'])): ?>
      <span class="nav-icon" style="cursor:default;">👋 <?= htmlspecialchars($_SESSION['fname']) ?></span>
      <a href="/account/logout.php" class="btn-member" style="text-decoration:none;">Logout</a>
    <?php else: ?>
      <a href="/account/login.php"    class="nav-icon"   style="text-decoration:none;">Log In</a>
      <a href="/account/register.php" class="btn-member" style="text-decoration:none;">Join Free</a>
    <?php endif; ?>
  </div>
</nav>
