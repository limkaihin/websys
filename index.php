<?php
$pageTitle = 'Home';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

// Wishlist toggle from homepage
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    verify_csrf();
    $pid   = (int)post('product_id');
    $added = wishlist_toggle($pid);
    set_flash('success', $added ? 'Saved to wishlist! 🤍' : 'Removed from wishlist.');
    redirect('index.php#shop');
}

$pdo = db();

// Featured products from DB
$featuredStmt = $pdo->query('SELECT * FROM products WHERE is_featured = 1 ORDER BY id LIMIT 8');
$featured     = $featuredStmt->fetchAll();

// All products for category filter (show up to 8)
$allStmt = $pdo->query('SELECT * FROM products ORDER BY is_featured DESC, id LIMIT 8');
$allProds = $allStmt->fetchAll();

// Blog posts
$blogStmt = $pdo->query('SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 3');
$posts    = $blogStmt->fetchAll();

function stable_rating_home(int $id): array {
    $seed  = ($id * 6271 + 1009) % 1000;
    return [4 + (($id * 31) % 2), 50 + ($seed % 450)];
}
?>

<!-- HERO -->
<section class="hero" aria-label="Hero banner">
  <div class="hero-left">
    <div class="hero-eyebrow">🐾 Singapore's #1 Cat Store</div>
    <h1 class="hero-title">
      Everything<br>Your Cat<br><em>Deserves</em>
    </h1>
    <p class="hero-desc">
      Premium cat food, litter, toys, and apparel — curated with love for the discerning feline and their devoted human.
    </p>
    <div class="hero-ctas">
      <a class="btn-primary" href="<?= h(base_url('shop/products.php')) ?>" style="text-decoration:none;">Shop Now</a>
      <a class="btn-outline" href="<?= h(base_url('account/register.php')) ?>" style="text-decoration:none;">Join MeowClub</a>
    </div>
    <div class="hero-stats">
      <div class="hero-stat"><strong>2,400+</strong><span>Products</span></div>
      <div class="hero-stat"><strong>98%</strong><span>Happy Cats</span></div>
      <div class="hero-stat"><strong>Free</strong><span>Membership</span></div>
    </div>
  </div>
  <div class="hero-right">
    <div class="hero-blob"></div>
    <div class="hero-visual" aria-hidden="true">🐈</div>
    <div class="hero-badge">
      <div class="icon" aria-hidden="true">⭐</div>
      <div>
        <div class="value">4.9 / 5.0</div>
        <div class="label">Over 8,000 reviews</div>
      </div>
    </div>
  </div>
</section>

<!-- CATEGORIES -->
<section class="categories" id="categories" aria-labelledby="cat-heading">
  <div class="section-header">
    <div class="section-tag">🐾 Browse by Category</div>
    <h2 class="section-title" id="cat-heading">Shop by <em>Your Cat's</em> Mood</h2>
  </div>
  <div class="cat-grid">
    <?php
    $cats = [
      ['food',        '🥩', 'Cat Food',       '340+ products'],
      ['litter',      '🧴', 'Litter & Hygiene','120+ products'],
      ['toys',        '🧶', 'Toys & Play',     '200+ products'],
      ['apparel',     '👗', 'Cat Apparel',     '80+ products'],
    ];
    foreach ($cats as [$slug, $icon, $label, $sub]):
    ?>
    <a href="<?= h(base_url('shop/products.php?cat=' . $slug)) ?>"
       class="cat-card" style="text-decoration:none;" aria-label="Browse <?= $label ?>">
      <div class="bg" aria-hidden="true"><?= $icon ?></div>
      <div class="info">
        <h3><?= $label ?></h3>
        <span><?= $sub ?></span>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- FEATURED PRODUCTS from DB -->
<section class="products" id="shop" aria-labelledby="shop-heading">
  <div class="products-toolbar">
    <div class="section-header" style="text-align:left;margin-bottom:0;">
      <div class="section-tag">🛒 Featured</div>
      <h2 class="section-title" id="shop-heading">Top <em>Picks</em></h2>
    </div>
    <div class="filter-pills" role="list" aria-label="Filter by category">
      <a class="pill active" href="<?= h(base_url('shop/products.php')) ?>" role="listitem">All</a>
      <a class="pill" href="<?= h(base_url('shop/products.php?cat=food')) ?>"        role="listitem">🥩 Food</a>
      <a class="pill" href="<?= h(base_url('shop/products.php?cat=litter')) ?>"      role="listitem">🧴 Litter</a>
      <a class="pill" href="<?= h(base_url('shop/products.php?cat=toys')) ?>"        role="listitem">🧶 Toys</a>
      <a class="pill" href="<?= h(base_url('shop/products.php?cat=apparel')) ?>"     role="listitem">👗 Apparel</a>
    </div>
  </div>

  <?php if (!empty($featured)): ?>
  <div class="products-grid">
    <?php foreach ($featured as $p):
      $icon = product_icon_for($p);
      [$stars, $count] = stable_rating_home((int)$p['id']);
    ?>
    <article class="product-card" aria-label="<?= h($p['name']) ?>">
      <form method="POST" style="display:contents;">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
        <input type="hidden" name="return_to"  value="index.php">
        <a href="<?= h(base_url('shop/product.php?id=' . $p['id'])) ?>" style="text-decoration:none;color:inherit;display:block;">
          <div class="product-img">
            <span aria-hidden="true"><?= $icon ?></span>
            <?php if ($p['is_featured']): ?><div class="ribbon" aria-label="Featured">Best Seller</div><?php endif; ?>
            <button class="wishlist <?= wishlist_has((int)$p['id']) ? 'active' : '' ?>" type="submit"
                    style="position:absolute;top:12px;right:12px;z-index:2;"
                    aria-label="<?= wishlist_has((int)$p['id']) ? 'Remove from wishlist' : 'Add to wishlist' ?>"
                    onclick="event.preventDefault();this.closest('form').submit();">
              <i class="fa-<?= wishlist_has((int)$p['id']) ? 'solid' : 'regular' ?> fa-heart"></i>
            </button>
          </div>
          <div class="product-body">
            <div class="product-brand"><?= h($p['category']) ?></div>
            <h3 class="product-name"><?= h($p['name']) ?></h3>
            <div class="product-stars" aria-label="<?= $stars ?> out of 5 stars, <?= $count ?> reviews">
              <?= str_repeat('⭐', $stars) ?> <span class="count">(<?= $count ?>)</span>
            </div>
            <div class="product-footer">
              <div class="product-price"><?= money((float)$p['price']) ?></div>
              <span class="btn-cart" aria-hidden="true"><i class="fa-solid fa-cart-shopping"></i></span>
            </div>
          </div>
        </a>
      </form>
    </article>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div style="text-align:center;margin-top:48px;">
    <a href="<?= h(base_url('shop/products.php')) ?>" class="btn-outline" style="text-decoration:none;display:inline-block;">
      View All Products →
    </a>
  </div>
</section>

<!-- MEMBERSHIP -->
<div class="membership" id="membership" aria-labelledby="membership-heading">
  <div class="membership-left">
    <h2 id="membership-heading">Join the <em>MeowClub</em> & Save Every Day</h2>
    <p>Free membership with exclusive perks, early sale access, birthday treats for your cat, and more — all with no fees, ever.</p>
    <div class="membership-perks">
      <?php
      $perks = [
        ['🎁','Earn Pawpoints',  'Redeem rewards on every purchase'],
        ['🚚','Free Delivery',   'On all orders for members'],
        ['🎂','Birthday Surprise','A free gift for your cat each year'],
        ['⚡','Early Access',    'Shop new arrivals & sales first'],
      ];
      foreach ($perks as [$ico,$title,$sub]):
      ?>
      <div class="perk">
        <div class="icon" aria-hidden="true"><?= $ico ?></div>
        <div class="text"><strong><?= $title ?></strong><span><?= $sub ?></span></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="membership-right">
    <?php if (is_logged_in()): ?>
      <div style="text-align:center;padding:20px 0;">
        <div style="font-size:4rem;margin-bottom:16px;">🐾</div>
        <h3 style="color:var(--brown);margin-bottom:12px;">You're a MeowClub Member!</h3>
        <p style="color:var(--brown-md);margin-bottom:24px;">Enjoy your exclusive perks and discounts.</p>
        <a href="<?= h(base_url('account/profile.php')) ?>" class="btn-join" style="text-decoration:none;display:inline-block;">My Account →</a>
      </div>
    <?php else: ?>
      <h3>Create Your Free Account</h3>
      <a href="<?= h(base_url('account/register.php')) ?>" class="btn-join" style="text-decoration:none;display:block;text-align:center;margin-bottom:16px;">Join MeowClub – It's Free!</a>
      <p style="text-align:center;font-size:.82rem;color:var(--brown-md);">
        Already a member? <a href="<?= h(base_url('account/login.php')) ?>" style="color:var(--orange);font-weight:600;">Log in →</a>
      </p>
      <div style="margin-top:28px;padding-top:24px;border-top:1.5px solid var(--warm);">
        <?php foreach ($perks as [$ico,$title,$sub]): ?>
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
          <span style="font-size:1.2rem;" aria-hidden="true"><?= $ico ?></span>
          <div>
            <div style="font-size:.88rem;font-weight:600;color:var(--brown);"><?= $title ?></div>
            <div style="font-size:.78rem;color:var(--brown-md);"><?= $sub ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- BLOG -->
<section class="blog" id="blog" aria-labelledby="blog-heading">
  <div class="section-header">
    <div class="section-tag">📖 The MeowMart Blog</div>
    <h2 class="section-title" id="blog-heading">Tips, Stories & <em>Cat Wisdom</em></h2>
  </div>

  <?php if (!empty($posts)): ?>
  <div class="blog-grid">
    <?php
    $blogIcons = ['Nutrition'=>'🐱','Play'=>'🧶','Grooming'=>'✂️','Health'=>'💊','Lifestyle'=>'🏠'];
    foreach ($posts as $i => $post):
      $icon = $blogIcons[$post['tag']] ?? '📖';
      $isFeatured = $i === 0;
    ?>
    <article class="blog-card <?= $isFeatured ? 'featured' : '' ?>">
      <a href="<?= h(base_url('content/blog_post.php?id=' . $post['id'])) ?>" style="text-decoration:none;color:inherit;display:block;">
        <div class="blog-thumb" aria-hidden="true"><?= $icon ?></div>
        <div class="blog-body">
          <span class="blog-tag"><?= h($post['tag'] ?? 'Article') ?></span>
          <h3><?= h($post['title']) ?></h3>
          <?php if ($post['excerpt']): ?>
            <p><?= h(strlen($post['excerpt']) > ($isFeatured ? 160 : 100) ? substr($post['excerpt'], 0, $isFeatured ? 160 : 100) . '...' : $post['excerpt']) ?></p>
          <?php endif; ?>
          <div class="blog-meta">
            <div class="avatar" aria-hidden="true">🧑</div>
            <?= h($post['author']) ?> &nbsp;·&nbsp; <?= date('d M Y', strtotime($post['created_at'])) ?>
          </div>
        </div>
      </a>
    </article>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div style="text-align:center;margin-top:40px;">
    <a href="<?= h(base_url('content/blog.php')) ?>" class="btn-outline" style="text-decoration:none;display:inline-block;">
      Read More Articles →
    </a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
