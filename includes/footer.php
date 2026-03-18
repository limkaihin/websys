</main>
<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <div class="logo">
        <div class="paw">🐱</div>
        <div class="name">MeowMart</div>
      </div>
      <p>Singapore's favourite destination for everything cats. Quality products, happy cats, delighted owners.</p>
      <div class="social-links">
        <a class="social-btn" href="#" aria-label="Visit our Facebook page"><i class="fa-brands fa-facebook-f"></i></a>
        <a class="social-btn" href="#" aria-label="Visit our Instagram page"><i class="fa-brands fa-instagram"></i></a>
        <a class="social-btn" href="#" aria-label="Visit our TikTok page"><i class="fa-brands fa-tiktok"></i></a>
        <a class="social-btn" href="#" aria-label="Chat with customer support"><i class="fa-solid fa-comment-dots"></i></a>
      </div>
    </div>

    <div class="footer-col">
      <h4>Shop</h4>
      <ul>
        <li><a href="<?= h(base_url('shop/products.php?cat=food')) ?>">Cat Food</a></li>
        <li><a href="<?= h(base_url('shop/products.php?cat=litter')) ?>">Litter & Hygiene</a></li>
        <li><a href="<?= h(base_url('shop/products.php?cat=toys')) ?>">Toys & Play</a></li>
        <li><a href="<?= h(base_url('shop/products.php?cat=apparel')) ?>">Cat Apparel</a></li>
        <li><a href="<?= h(base_url('shop/products.php?cat=accessories')) ?>">Accessories</a></li>
        <li><a href="<?= h(base_url('shop/products.php')) ?>">New Arrivals</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>MeowClub</h4>
      <ul>
        <li><a href="<?= h(base_url('account/register.php')) ?>">Join Free</a></li>
        <li><a href="<?= h(base_url('content/about.php')) ?>">How It Works</a></li>
        <li><a href="#">Pawpoints</a></li>
        <li><a href="#">Referrals</a></li>
        <li><a href="#">Member Deals</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Help</h4>
      <ul>
        <li><a href="<?= h(base_url('shop/orders.php')) ?>">Track My Order</a></li>
        <li><a href="#">Returns & Refunds</a></li>
        <li><a href="#">Shipping Info</a></li>
        <li><a href="#">FAQ</a></li>
        <li><a href="<?= h(base_url('content/contact.php')) ?>">Contact Us</a></li>
      </ul>
    </div>
  </div>

  <div class="footer-bottom">
    <p>© <?= date('Y') ?> MeowMart Pte. Ltd. · Singapore · All rights reserved.</p>
    <p>Privacy Policy · Terms of Service · Cookie Settings</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="<?= h(base_url('assets/js/main.js')) ?>"></script>
</body>
</html>
