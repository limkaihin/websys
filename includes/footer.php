</main>
<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <div class="logo">
        <div class="paw">🐱</div>
        <div class="name">MeowMart</div>
      </div>
      <p>Singapore's favourite destination for everything cats. Quality products, happy cats, delighted owners.</p>
      <p style="max-width:340px;">Need help with an order, your membership perks, or a product question? <a href="<?= h(base_url('content/contact.php')) ?>" style="color:var(--orange-lt);text-decoration:none;font-weight:600;">Contact us here</a>.</p>
      <div class="social-links" aria-label="MeowMart social platforms">
        <span class="social-btn" aria-label="Facebook" title="Facebook"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></span>
        <span class="social-btn" aria-label="Instagram" title="Instagram"><i class="fa-brands fa-instagram" aria-hidden="true"></i></span>
        <span class="social-btn" aria-label="TikTok" title="TikTok"><i class="fa-brands fa-tiktok" aria-hidden="true"></i></span>
        <span class="social-btn" aria-label="Community chat" title="Community chat"><i class="fa-solid fa-comments" aria-hidden="true"></i></span>
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
        <li><a href="<?= h(base_url('shop/products.php?sort=newest')) ?>">New Arrivals</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>MeowClub</h4>
      <ul>
        <li><a href="<?= h(base_url('account/register.php')) ?>">Join Free</a></li>
        <li><a href="<?= h(base_url('content/meowclub.php#how-it-works')) ?>">How It Works</a></li>
        <li><a href="<?= h(base_url('content/meowclub.php#pawpoints')) ?>">Pawpoints</a></li>
        <li><a href="<?= h(base_url('content/meowclub.php#referrals')) ?>">Referrals</a></li>
        <li><a href="<?= h(base_url('content/meowclub.php#member-deals')) ?>">Member Deals</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Help</h4>
      <ul>
        <li><a href="<?= h(base_url('content/help.php#track-order')) ?>">Track My Order</a></li>
        <li><a href="<?= h(base_url('content/help.php#returns')) ?>">Returns & Refunds</a></li>
        <li><a href="<?= h(base_url('content/help.php#shipping')) ?>">Shipping Info</a></li>
        <li><a href="<?= h(base_url('content/help.php#faq')) ?>">FAQ</a></li>
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
