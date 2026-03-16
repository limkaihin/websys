<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
  <nav>
    <a href="/index.php" class="nav-logo">
      <div class="paw">🐱</div>
      <div class="brand">Meow<span>Mart</span></div>
    </a>
    <ul class="nav-links">
      <li><a href="/shop/products.php">Shop</a></li>
      <li><a href="/index.php#categories">Categories</a></li>
      <li><a href="/index.php#membership">Membership</a></li>
      <li><a href="/index.php#blog">Blog</a></li>
      <li><a href="/about.php">About</a></li>
      <li><a href="/contact.php">Contact</a></li>
    </ul>
    <div class="nav-actions">
      <button class="nav-icon" title="Search">🔍</button>
      <button class="nav-icon" title="Wishlist">🤍</button>
      <a href="/shop/cart.php" class="nav-icon" style="text-decoration:none;" title="Cart">
        🛒 <span class="badge">0</span>
      </a>
      <?php if (!empty($_SESSION["loggedin"])): ?>
        <span class="nav-icon" style="cursor:default;">
          👋 <?= htmlspecialchars($_SESSION["fname"]) ?>
        </span>
        <a href="/account/logout.php" class="btn-member" style="text-decoration:none;">Logout</a>
      <?php else: ?>
        <a href="/account/login.php"    class="nav-icon"   style="text-decoration:none;">Log In</a>
        <a href="/account/register.php" class="btn-member" style="text-decoration:none;">Join Free</a>
      <?php endif; ?>
    </div>
  </nav>
