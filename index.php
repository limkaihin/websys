<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "inc/head.inc.php"; ?>
</head>
<body>

<?php include "inc/nav.inc.php"; ?>

  <!-- HERO -->
  <section class="hero">
    <div class="hero-left">
      <div class="hero-eyebrow">🐾 Singapore's #1 Cat Store</div>
      <h1 class="hero-title">
        Everything<br>Your Cat<br><em>Deserves</em>
      </h1>
      <p class="hero-desc">
        Premium cat food, litter, toys, and apparel — curated with love for the discerning feline and their devoted human.
      </p>
      <div class="hero-ctas">
        <button class="btn-primary" onclick="document.getElementById('shop').scrollIntoView({behavior:'smooth'})">Shop Now</button>
        <button class="btn-outline" onclick="document.getElementById('membership').scrollIntoView({behavior:'smooth'})">Join MeowClub</button>
      </div>
      <div class="hero-stats">
        <div class="hero-stat"><strong>2,400+</strong><span>Products</span></div>
        <div class="hero-stat"><strong>98%</strong><span>Happy Cats</span></div>
        <div class="hero-stat"><strong>Free</strong><span>Membership</span></div>
      </div>
    </div>
    <div class="hero-right">
      <div class="hero-blob"></div>
      <div class="hero-visual">🐈</div>
      <div class="hero-badge">
        <div class="icon">⭐</div>
        <div>
          <div class="value">4.9 / 5.0</div>
          <div class="label">Over 8,000 reviews</div>
        </div>
      </div>
    </div>
  </section>

  <!-- CATEGORIES -->
  <section class="categories" id="categories">
    <div class="section-header">
      <div class="section-tag">🐾 Browse by Category</div>
      <h2 class="section-title">Shop by <em>Your Cat's</em> Mood</h2>
    </div>
    <div class="cat-grid">
      <div class="cat-card">
        <div class="bg">🥩</div>
        <div class="info"><h3>Cat Food</h3><span>340+ products</span></div>
      </div>
      <div class="cat-card">
        <div class="bg">🧴</div>
        <div class="info"><h3>Litter & Hygiene</h3><span>120+ products</span></div>
      </div>
      <div class="cat-card">
        <div class="bg">🧶</div>
        <div class="info"><h3>Toys & Play</h3><span>200+ products</span></div>
      </div>
      <div class="cat-card">
        <div class="bg">👗</div>
        <div class="info"><h3>Cat Apparel</h3><span>80+ products</span></div>
      </div>
    </div>
  </section>

  <!-- FEATURED PRODUCTS -->
  <section class="products" id="shop">
    <div class="products-toolbar">
      <div class="section-header" style="text-align:left;margin-bottom:0;">
        <div class="section-tag">🛒 Featured</div>
        <h2 class="section-title">Top <em>Picks</em></h2>
      </div>
      <div class="filter-pills">
        <button class="pill active" onclick="setFilter(this)">All</button>
        <button class="pill" onclick="setFilter(this)">Food</button>
        <button class="pill" onclick="setFilter(this)">Litter</button>
        <button class="pill" onclick="setFilter(this)">Toys</button>
        <button class="pill" onclick="setFilter(this)">Apparel</button>
      </div>
    </div>
    <div class="products-grid">

      <div class="product-card">
        <div class="product-img">
          <span>🥩</span>
          <div class="ribbon">Best Seller</div>
          <button class="wishlist">🤍</button>
        </div>
        <div class="product-body">
          <div class="product-brand">PurreFit</div>
          <h3 class="product-name">Grain-Free Salmon Pâté for Adult Cats</h3>
          <div class="product-stars">⭐⭐⭐⭐⭐ <span class="count">(412)</span></div>
          <div class="product-footer">
            <div class="product-price">$14.90 <span class="old">$18.00</span></div>
            <button class="btn-cart" onclick="addToCart()">🛒</button>
          </div>
        </div>
      </div>

      <div class="product-card">
        <div class="product-img" style="background:#E8F2EA;">
          <span>🧴</span>
          <button class="wishlist">🤍</button>
        </div>
        <div class="product-body">
          <div class="product-brand">ClearPaw</div>
          <h3 class="product-name">Ultra Clumping Lavender Cat Litter 8kg</h3>
          <div class="product-stars">⭐⭐⭐⭐⭐ <span class="count">(289)</span></div>
          <div class="product-footer">
            <div class="product-price">$22.50</div>
            <button class="btn-cart" onclick="addToCart()">🛒</button>
          </div>
        </div>
      </div>

      <div class="product-card">
        <div class="product-img" style="background:#E8E0F0;">
          <span>🧶</span>
          <div class="ribbon">New</div>
          <button class="wishlist">🤍</button>
        </div>
        <div class="product-body">
          <div class="product-brand">PlayPaws</div>
          <h3 class="product-name">Interactive Feather Wand & Refill Set</h3>
          <div class="product-stars">⭐⭐⭐⭐⭐ <span class="count">(176)</span></div>
          <div class="product-footer">
            <div class="product-price">$9.90 <span class="old">$13.00</span></div>
            <button class="btn-cart" onclick="addToCart()">🛒</button>
          </div>
        </div>
      </div>

      <div class="product-card">
        <div class="product-img" style="background:#F4E8F0;">
          <span>👗</span>
          <button class="wishlist">🤍</button>
        </div>
        <div class="product-body">
          <div class="product-brand">KittyKouture</div>
          <h3 class="product-name">Reversible Floral Bow Tie & Collar Set</h3>
          <div class="product-stars">⭐⭐⭐⭐☆ <span class="count">(88)</span></div>
          <div class="product-footer">
            <div class="product-price">$12.00</div>
            <button class="btn-cart" onclick="addToCart()">🛒</button>
          </div>
        </div>
      </div>

      <div class="product-card">
        <div class="product-img" style="background:#FFF4E0;">
          <span>🍗</span>
          <div class="ribbon">Sale</div>
          <button class="wishlist">🤍</button>
        </div>
        <div class="product-body">
          <div class="product-brand">PurreFit</div>
          <h3 class="product-name">Freeze-Dried Chicken Treats 100g</h3>
          <div class="product-stars">⭐⭐⭐⭐⭐ <span class="count">(531)</span></div>
          <div class="product-footer">
            <div class="product-price">$11.50 <span class="old">$15.00</span></div>
            <button class="btn-cart" onclick="addToCart()">🛒</button>
          </div>
        </div>
      </div>

      <div class="product-card">
        <div class="product-img" style="background:#E0F0EA;">
          <span>🏠</span>
          <button class="wishlist">🤍</button>
        </div>
        <div class="product-body">
          <div class="product-brand">CozyCat</div>
          <h3 class="product-name">Enclosed Self-Cleaning Litter Box</h3>
          <div class="product-stars">⭐⭐⭐⭐⭐ <span class="count">(204)</span></div>
          <div class="product-footer">
            <div class="product-price">$89.00</div>
            <button class="btn-cart" onclick="addToCart()">🛒</button>
          </div>
        </div>
      </div>

      <div class="product-card">
        <div class="product-img" style="background:#E8EEF8;">
          <span>🎯</span>
          <div class="ribbon">New</div>
          <button class="wishlist">🤍</button>
        </div>
        <div class="product-body">
          <div class="product-brand">PlayPaws</div>
          <h3 class="product-name">Electronic Laser Chase Auto Toy</h3>
          <div class="product-stars">⭐⭐⭐⭐⭐ <span class="count">(143)</span></div>
          <div class="product-footer">
            <div class="product-price">$34.90</div>
            <button class="btn-cart" onclick="addToCart()">🛒</button>
          </div>
        </div>
      </div>

      <div class="product-card">
        <div class="product-img" style="background:#FDE8DC;">
          <span>🎀</span>
          <button class="wishlist">🤍</button>
        </div>
        <div class="product-body">
          <div class="product-brand">KittyKouture</div>
          <h3 class="product-name">Velvet Holiday Hoodie – Multiple Sizes</h3>
          <div class="product-stars">⭐⭐⭐⭐☆ <span class="count">(62)</span></div>
          <div class="product-footer">
            <div class="product-price">$19.90 <span class="old">$24.00</span></div>
            <button class="btn-cart" onclick="addToCart()">🛒</button>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- MEMBERSHIP -->
  <div class="membership" id="membership">
    <div class="membership-left">
      <h2>Join the <em>MeowClub</em> & Save Every Day</h2>
      <p>Free membership with exclusive perks, early sale access, birthday treats for your cat, and more — all with no fees, ever.</p>
      <div class="membership-perks">
        <div class="perk">
          <div class="icon">🎁</div>
          <div class="text"><strong>Earn Pawpoints</strong><span>Redeem rewards on every purchase</span></div>
        </div>
        <div class="perk">
          <div class="icon">🚚</div>
          <div class="text"><strong>Free Delivery</strong><span>On all orders for members</span></div>
        </div>
        <div class="perk">
          <div class="icon">🎂</div>
          <div class="text"><strong>Birthday Surprise</strong><span>A free gift for your cat each year</span></div>
        </div>
        <div class="perk">
          <div class="icon">⚡</div>
          <div class="text"><strong>Early Access</strong><span>Shop new arrivals & sales first</span></div>
        </div>
      </div>
    </div>
    <div class="membership-right">
      <h3>Create Your Free Account</h3>
      <div class="form-field">
        <label>Your Name</label>
        <input type="text" placeholder="e.g. Sarah Tan" />
      </div>
      <div class="form-field">
        <label>Email Address</label>
        <input type="email" placeholder="you@example.com" />
      </div>
      <div class="form-field">
        <label>Your Cat's Name 🐱</label>
        <input type="text" placeholder="e.g. Mochi" />
      </div>
      <button class="btn-join">Join MeowClub – It's Free!</button>
      <p class="form-note">No credit card needed. Unsubscribe anytime.</p>
    </div>
  </div>

  <!-- BLOG -->
  <section class="blog" id="blog">
    <div class="section-header">
      <div class="section-tag">📖 The MeowMart Blog</div>
      <h2 class="section-title">Tips, Stories & <em>Cat Wisdom</em></h2>
    </div>
    <div class="blog-grid">

      <div class="blog-card featured">
        <div class="blog-thumb">🐱</div>
        <div class="blog-body">
          <span class="blog-tag">Nutrition</span>
          <h3>The Ultimate Guide to Feeding Your Cat a Balanced Diet in 2025</h3>
          <p>From raw diets to premium kibble, we break down everything you need to know to keep your cat healthy, happy, and well-fed every single day.</p>
          <div class="blog-meta">
            <div class="avatar">🧑</div>
            Dr. Lee Jun Wei &nbsp;·&nbsp; 8 min read
          </div>
        </div>
      </div>

      <div class="blog-card">
        <div class="blog-thumb">🧶</div>
        <div class="blog-body">
          <span class="blog-tag">Play</span>
          <h3>10 Toys That Actually Keep Cats Entertained (Tested!)</h3>
          <p>Our team tested 30+ toys with real cats. Here are the clear winners.</p>
          <div class="blog-meta">
            <div class="avatar">🧑</div>
            Priya N. &nbsp;·&nbsp; 5 min read
          </div>
        </div>
      </div>

      <div class="blog-card">
        <div class="blog-thumb">✂️</div>
        <div class="blog-body">
          <span class="blog-tag">Grooming</span>
          <h3>How to Groom Your Cat at Home Without the Drama</h3>
          <p>Step-by-step guide for nail trimming, brushing, and bathing a reluctant cat.</p>
          <div class="blog-meta">
            <div class="avatar">🧑</div>
            Mei Lin &nbsp;·&nbsp; 4 min read
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- CART DRAWER -->
  <div class="cart-overlay" id="cartOverlay" onclick="closeCart()"></div>
  <div class="cart-drawer" id="cartDrawer">
    <div class="cart-header">
      <h2>Your Cart 🛒</h2>
      <button class="cart-close" onclick="closeCart()">✕</button>
    </div>
    <div class="cart-items" id="cartItems">
      <div class="cart-item">
        <div class="thumb">🥩</div>
        <div class="details">
          <h4>Grain-Free Salmon Pâté</h4>
          <div class="variant">PurreFit · 400g</div>
          <div class="qty-row">
            <div class="qty-ctrl">
              <button class="qty-btn">−</button>
              <span>1</span>
              <button class="qty-btn">+</button>
            </div>
            <strong>$14.90</strong>
          </div>
        </div>
      </div>
      <div class="cart-item">
        <div class="thumb">🧶</div>
        <div class="details">
          <h4>Feather Wand Set</h4>
          <div class="variant">PlayPaws · Standard</div>
          <div class="qty-row">
            <div class="qty-ctrl">
              <button class="qty-btn">−</button>
              <span>2</span>
              <button class="qty-btn">+</button>
            </div>
            <strong>$19.80</strong>
          </div>
        </div>
      </div>
    </div>
    <div class="cart-footer">
      <div class="cart-subtotal">
        <span>Subtotal</span>
        <span class="val" id="cartTotal">$34.70</span>
      </div>
      <button class="btn-join">Proceed to Checkout →</button>
    </div>
  </div>

<?php include "inc/footer.inc.php"; ?>
</body>
</html>
