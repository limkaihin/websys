<?php
require_once dirname(__DIR__) . '/includes/db.php';

// Wishlist toggle (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $pid   = (int)post('product_id');
    $added = wishlist_toggle($pid);
    set_flash('success', $added ? 'Saved to wishlist! 🤍' : 'Removed from wishlist.');
    $back  = trim((string)($_POST['return_to'] ?? 'shop/products.php'));
    redirect($back !== '' ? $back : 'shop/products.php');
}

$pageTitle = 'Shop All Products';
require_once dirname(__DIR__) . '/includes/header.php';

$pdo    = db();
$cat    = trim($_GET['cat']  ?? '');
$search = trim($_GET['q']    ?? '');
$sort   = $_GET['sort']      ?? 'name';

$allowed_sorts = ['name' => 'name ASC', 'price_asc' => 'price ASC', 'price_desc' => 'price DESC', 'newest' => 'created_at DESC'];
$order_sql = $allowed_sorts[$sort] ?? 'name ASC';

$params = [];
$where  = ['1=1'];
if ($cat !== '') {
    $where[]  = 'LOWER(category) = ?';
    $params[] = strtolower($cat);
}
if ($search !== '') {
    $where[]  = '(name LIKE ? OR description LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql  = 'SELECT * FROM products WHERE ' . implode(' AND ', $where) . ' ORDER BY ' . $order_sql;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Stable fake reviews: hash of product ID so they don't change on reload
function stable_rating(int $id): array {
    $seed  = ($id * 6271 + 1009) % 1000;
    $count = 50 + ($seed % 450);          // 50–499
    $stars = 4 + (($id * 31) % 2);       // 4 or 5
    return [$stars, $count];
}

$categories = [
    ['slug' => 'food',        'label' => 'Food',        'icon' => '🥩'],
    ['slug' => 'litter',      'label' => 'Litter',      'icon' => '🧴'],
    ['slug' => 'toys',        'label' => 'Toys',        'icon' => '🧶'],
    ['slug' => 'apparel',     'label' => 'Apparel',     'icon' => '👗'],
    ['slug' => 'accessories', 'label' => 'Accessories', 'icon' => '🎀'],
];
$emojiMap = ['food' => '🥩', 'litter' => '🧴', 'toys' => '🧶', 'apparel' => '👗', 'accessories' => '🎀'];

// Build return_to for wishlist redirects
$returnTo = 'shop/products.php?' . http_build_query(['cat' => $cat, 'q' => $search, 'sort' => $sort]);
?>

<section class="products" style="min-height:70vh;">
  <!-- Toolbar -->
  <div class="products-toolbar" style="flex-direction:column;align-items:stretch;gap:20px;">

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
      <div class="section-header" style="text-align:left;margin-bottom:0;">
        <div class="section-tag">🛒 Browse</div>
        <h1 class="section-title">Shop <em><?= $cat ? ucfirst($cat) : 'Everything' ?></em></h1>
      </div>

      <!-- Search + Sort -->
      <form method="GET" role="search" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <?php if ($cat): ?><input type="hidden" name="cat" value="<?= h($cat) ?>"><?php endif; ?>
        <div style="position:relative;">
          <label for="prod-search" class="visually-hidden">Search products</label>
          <input id="prod-search" type="search" name="q" value="<?= h($search) ?>"
                 placeholder="Search products…"
                 style="padding:9px 14px 9px 36px;border:1.5px solid var(--warm);border-radius:20px;
                        font-family:'DM Sans',sans-serif;font-size:.88rem;background:var(--white);
                        outline:none;min-width:180px;color:var(--brown);"
                 aria-label="Search products"/>
          <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:.9rem;pointer-events:none;">🔍</span>
        </div>
        <label for="prod-sort" class="visually-hidden">Sort by</label>
        <select id="prod-sort" name="sort" onchange="this.form.submit()"
                style="padding:9px 14px;border:1.5px solid var(--warm);border-radius:20px;
                       font-family:'DM Sans',sans-serif;font-size:.88rem;background:var(--white);
                       color:var(--brown);cursor:pointer;">
          <option value="name"       <?= $sort==='name'      ?'selected':'' ?>>Name A–Z</option>
          <option value="price_asc"  <?= $sort==='price_asc' ?'selected':'' ?>>Price: Low→High</option>
          <option value="price_desc" <?= $sort==='price_desc'?'selected':'' ?>>Price: High→Low</option>
          <option value="newest"     <?= $sort==='newest'    ?'selected':'' ?>>Newest</option>
        </select>
        <button type="submit" class="btn-primary" style="padding:9px 20px;font-size:.88rem;">Search</button>
      </form>
    </div>

    <!-- Category pills -->
    <div class="filter-pills" role="list" aria-label="Filter by category">
      <a class="pill <?= !$cat ? 'active' : '' ?>" href="<?= h(base_url('shop/products.php?' . http_build_query(['q'=>$search,'sort'=>$sort]))) ?>" role="listitem">All</a>
      <?php foreach ($categories as $c): ?>
        <a class="pill <?= $cat === $c['slug'] ? 'active' : '' ?>"
           href="<?= h(base_url('shop/products.php?' . http_build_query(['cat'=>$c['slug'],'q'=>$search,'sort'=>$sort]))) ?>"
           style="text-decoration:none;" role="listitem">
          <?= $c['icon'] . ' ' . h($c['label']) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Result count -->
    <p style="font-size:.82rem;color:var(--brown-md);">
      <?php if ($search): ?>"<?= h($search) ?>" · <?php endif ?>
      <?= count($products) ?> product<?= count($products) !== 1 ? 's' : '' ?> found
    </p>
  </div>

  <?php if (empty($products)): ?>
    <div style="text-align:center;padding:80px 20px;">
      <div style="font-size:5rem;margin-bottom:20px;">🔍</div>
      <h2 style="font-family:'Playfair Display',serif;margin-bottom:12px;">No products found</h2>
      <p style="color:var(--brown-md);margin-bottom:28px;">Try a different search or category.</p>
      <a href="<?= h(base_url('shop/products.php')) ?>" class="btn-primary" style="text-decoration:none;display:inline-block;">Clear Filters</a>
    </div>
  <?php else: ?>
    <div class="products-grid">
      <?php foreach ($products as $p):
        $icon           = $emojiMap[strtolower($p['category'])] ?? '🐾';
        [$stars, $count] = stable_rating((int)$p['id']);
        $starStr        = str_repeat('⭐', $stars);
      ?>
        <article class="product-card" aria-label="<?= h($p['name']) ?>">
          <a href="<?= h(base_url('shop/product.php?id=' . $p['id'])) ?>" class="product-img-link" style="text-decoration:none;display:block;color:inherit;">
            <div class="product-img">
              <span aria-hidden="true"><?= $icon ?></span>
              <?php if ($p['is_featured']): ?><div class="ribbon" aria-label="Featured product">Featured</div><?php endif; ?>
              <form method="POST" style="position:absolute;top:12px;right:12px;z-index:2;" onclick="event.stopPropagation()">
                <input type="hidden" name="csrf_token"  value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="product_id"  value="<?= (int)$p['id'] ?>">
                <input type="hidden" name="return_to"   value="<?= h($returnTo) ?>">
                <button class="wishlist <?= wishlist_has((int)$p['id']) ? 'active' : '' ?>" type="submit"
                        aria-label="<?= wishlist_has((int)$p['id']) ? 'Remove from wishlist' : 'Add to wishlist' ?>">
                  <?= wishlist_has((int)$p['id']) ? '❤️' : '🤍' ?>
                </button>
              </form>
            </div>
          </a>
          <a href="<?= h(base_url('shop/product.php?id=' . $p['id'])) ?>" style="text-decoration:none;color:inherit;display:block;">
            <div class="product-body">
              <div class="product-brand"><?= h($p['category']) ?></div>
              <h2 class="product-name"><?= h($p['name']) ?></h2>
              <div class="product-stars" aria-label="<?= $stars ?> out of 5 stars, <?= $count ?> reviews">
                <?= $starStr ?> <span class="count">(<?= $count ?>)</span>
              </div>
              <div class="product-footer">
                <div class="product-price"><?= money((float)$p['price']) ?></div>
                <span class="btn-cart" role="img" aria-hidden="true">🛒</span>
              </div>
            </div>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
