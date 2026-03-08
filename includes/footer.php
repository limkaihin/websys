  <!-- FOOTER -->
  <footer>
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="logo">
          <div class="paw">🐱</div>
          <div class="name">MeowMart</div>
        </div>
        <p>Singapore's favourite destination for everything cats. Quality products, happy cats, delighted owners.</p>
        <div class="social-links">
          <a class="social-btn" href="#">📘</a>
          <a class="social-btn" href="#">📸</a>
          <a class="social-btn" href="#">🎵</a>
          <a class="social-btn" href="#">💬</a>
        </div>
      </div>

      <div class="footer-col">
        <h4>Shop</h4>
        <ul>
          <li><a href="<?= h(base_url('products.php?cat=food')) ?>">Cat Food</a></li>
          <li><a href="<?= h(base_url('products.php?cat=litter')) ?>">Litter & Hygiene</a></li>
          <li><a href="<?= h(base_url('products.php?cat=toys')) ?>">Toys & Play</a></li>
          <li><a href="<?= h(base_url('products.php?cat=apparel')) ?>">Cat Apparel</a></li>
          <li><a href="<?= h(base_url('products.php?cat=accessories')) ?>">Accessories</a></li>
          <li><a href="<?= h(base_url('products.php')) ?>">New Arrivals</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>MeowClub</h4>
        <ul>
          <li><a href="<?= h(base_url('register.php')) ?>">Join Free</a></li>
          <li><a href="<?= h(base_url('about.php')) ?>">How It Works</a></li>
          <li><a href="#">Pawpoints</a></li>
          <li><a href="#">Referrals</a></li>
          <li><a href="#">Member Deals</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>Help</h4>
        <ul>
          <li><a href="#">Track My Order</a></li>
          <li><a href="#">Returns & Refunds</a></li>
          <li><a href="#">Shipping Info</a></li>
          <li><a href="#">FAQ</a></li>
          <li><a href="<?= h(base_url('contact.php')) ?>">Contact Us</a></li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <p>© <?= date('Y') ?> MeowMart Pte. Ltd. · Singapore · All rights reserved.</p>
      <p>Privacy Policy · Terms of Service · Cookie Settings</p>
    </div>
  </footer>

  <script src="<?= h(base_url('assets/js/main.js')) ?>"></script>
</body>
</html>
