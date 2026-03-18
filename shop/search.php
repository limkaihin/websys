<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../inc/head.inc.php"; ?>
  <title>Search – MeowMart</title>
</head>
<body>
<?php include "../inc/nav.inc.php"; ?>
<?php
include "../inc/db.inc.php";

$q    = isset($_GET['q'])    ? trim($_GET['q'])    : '';
$cat  = isset($_GET['cat'])  ? trim($_GET['cat'])  : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'name';

$products = $q !== '' ? filterProducts($cat, $q, $sort) : [];

$icons  = ['food'=>'🥩','litter'=>'🧴','toys'=>'🧶','apparel'=>'👗','accessories'=>'🎀'];
$bgCols = ['food'=>'#FDE8D0','litter'=>'#D6E8D8','toys'=>'#D8E0F0','apparel'=>'#F4E8F0','accessories'=>'#FFF4D6'];

$categories = [
  ['slug'=>'food','label'=>'Food','icon'=>'🥩'],
  ['slug'=>'litter','label'=>'Litter','icon'=>'🧴'],
  ['slug'=>'toys','label'=>'Toys','icon'=>'🧶'],
  ['slug'=>'apparel','label'=>'Apparel','icon'=>'👗'],
  ['slug'=>'accessories','label'=>'Accessories','icon'=>'🎀'],
];
?>

<!-- SEARCH HEADER -->
<div style="background:var(--warm);padding:60px 5% 50px;text-align:center;">
  <div class="hero-eyebrow" style="margin:0 auto 16px;">🔍 Search</div>
  <h1 class="section-title" style="font-size:clamp(2rem,4vw,3rem);margin-bottom:28px;">
    Find Your Cat's <em>Next Favourite</em>
  </h1>

  <!-- Big search bar -->
  <form method="GET" action="/shop/search.php" style="max-width:600px;margin:0 auto;">
    <div style="display:flex;gap:0;background:#fff;border:2px solid var(--brown);border-radius:50px;overflow:hidden;box-shadow:4px 4px 0 var(--brown);">
      <input type="text" name="q" value="<?= htmlspecialchars($q) ?>"
             placeholder="Search products, categories…"
             autofocus
             style="flex:1;border:none;padding:16px 24px;font-family:inherit;font-size:1rem;color:var(--brown);background:transparent;outline:none;">
      <button type="submit"
              style="background:var(--orange);color:#fff;border:none;padding:16px 28px;cursor:pointer;font-size:.95rem;font-weight:700;letter-spacing:.04em;white-space:nowrap;">
        Search 🔍
      </button>
    </div>
    <?php if ($cat): ?>
      <input type="hidden" name="cat" value="<?= htmlspecialchars($cat) ?>">
    <?php endif; ?>
  </form>

  <!-- Popular searches -->
  <?php if (!$q): ?>
  <div style="margin-top:24px;display:flex;gap:8px;flex-wrap:wrap;justify-content:center;">
    <span style="font-size:.82rem;color:var(--brown-md);font-weight:600;align-self:center;">Try:</span>
    <?php foreach (['salmon','litter box','feather wand','hoodie','cat tree','treats'] as $hint): ?>
      <a href="/shop/search.php?q=<?= urlencode($hint) ?>"
         style="background:#fff;border:1.5px solid var(--warm);border-radius:30px;padding:6px 14px;font-size:.8rem;font-weight:600;color:var(--brown);text-decoration:none;transition:all .2s;"
         onmouseover="this.style.borderColor='var(--orange)';this.style.color='var(--orange)'"
         onmouseout="this.style.borderColor='var(--warm)';this.style.color='var(--brown)'"><?= $hint ?></a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<?php if ($q !== ''): ?>
<!-- FILTER BAR -->
<div style="background:#fff;border-bottom:1.5px solid var(--warm);padding:14px 5%;">
  <div style="max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
      <span style="font-size:.82rem;font-weight:600;color:var(--brown-md);">Filter:</span>
      <a href="/shop/search.php?q=<?= urlencode($q) ?>"
         style="display:inline-flex;align-items:center;padding:6px 14px;border-radius:30px;font-size:.8rem;font-weight:600;text-decoration:none;border:1.5px solid <?= !$cat?'var(--orange)':'var(--warm)' ?>;background:<?= !$cat?'var(--orange)':'#fff' ?>;color:<?= !$cat?'#fff':'var(--brown-md)' ?>;">All</a>
      <?php foreach ($categories as $c): ?>
        <a href="/shop/search.php?q=<?= urlencode($q) ?>&cat=<?= $c['slug'] ?>"
           style="display:inline-flex;align-items:center;gap:4px;padding:6px 14px;border-radius:30px;font-size:.8rem;font-weight:600;text-decoration:none;border:1.5px solid <?= $cat===$c['slug']?'var(--orange)':'var(--warm)' ?>;background:<?= $cat===$c['slug']?'var(--orange)':'#fff' ?>;color:<?= $cat===$c['slug']?'#fff':'var(--brown-md)' ?>;">
          <?= $c['icon'].' '.$c['label'] ?>
        </a>
      <?php endforeach; ?>
    </div>
    <select onchange="location.href=this.value"
            style="border:1.5px solid var(--warm);border-radius:30px;padding:8px 16px;font-family:inherit;font-size:.82rem;font-weight:600;color:var(--brown);background:#fff;cursor:pointer;outline:none;">
      <?php foreach (['name'=>'Name A–Z','price_asc'=>'Price ↑','price_desc'=>'Price ↓','featured'=>'Featured'] as $v=>$l): ?>
        <option value="/shop/search.php?q=<?= urlencode($q) ?><?= $cat?"&cat=$cat":'' ?>&sort=<?= $v ?>"
                <?= $sort===$v?'selected':'' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
  </div>
</div>

<!-- RESULTS -->
<div style="max-width:1200px;margin:0 auto;padding:32px 5% 80px;">
  <p style="font-size:.9rem;color:var(--brown-md);margin-bottom:24px;">
    <strong style="color:var(--brown);"><?= count($products) ?></strong>
    result<?= count($products)!==1?'s':'' ?> for
    "<strong style="color:var(--orange);"><?= htmlspecialchars($q) ?></strong>"
    <?= $cat ? ' in '.htmlspecialchars(ucfirst($cat)) : '' ?>
  </p>

  <?php if (empty($products)): ?>
    <div style="text-align:center;padding:80px 40px;background:var(--warm);border-radius:24px;">
      <div style="font-size:4rem;margin-bottom:16px;">😿</div>
      <h3 style="font-family:'Playfair Display',serif;font-size:1.6rem;margin-bottom:10px;">No results found</h3>
      <p style="color:var(--brown-md);margin-bottom:24px;">Try a different search term or browse our categories.</p>
      <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="/shop/products.php" class="btn-primary"  style="text-decoration:none;padding:12px 24px;">Browse All Products</a>
        <a href="/shop/categories.php" class="btn-outline" style="text-decoration:none;padding:11px 24px;display:inline-flex;align-items:center;">View Categories</a>
      </div>
    </div>
  <?php else: ?>
  <div class="products-grid">
    <?php foreach ($products as $p):
      $pSlug = strtolower($p['category']);
      $pIcon = $icons[$pSlug]  ?? '🐾';
      $pBg   = $bgCols[$pSlug] ?? '#F2E8D9';
    ?>
    <div class="product-card">
      <div class="product-img" style="background:<?= $pBg ?>;">
        <span><?= $pIcon ?></span>
        <?php if ($p['is_featured']): ?><div class="ribbon">Featured</div><?php endif; ?>
      </div>
      <div class="product-body">
        <div class="product-brand"><?= htmlspecialchars($p['category']) ?></div>
        <h3 class="product-name"><?= htmlspecialchars($p['name']) ?></h3>
        <div class="product-stars">⭐⭐⭐⭐⭐ <span class="count">(<?= (($p['id']*47+83)%450)+50 ?>)</span></div>
        <?php if (!empty($p['description'])): ?>
          <p style="font-size:.78rem;color:var(--brown-md);line-height:1.5;margin:5px 0 10px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
            <?= htmlspecialchars($p['description']) ?>
          </p>
        <?php endif; ?>
        <div class="product-footer">
          <div class="product-price">$<?= number_format($p['price'],2) ?></div>
          <a href="/shop/product.php?id=<?= $p['id'] ?>"
             class="btn-cart" style="text-decoration:none;display:flex;align-items:center;justify-content:center;">🛒</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<?php else: ?>
<!-- BROWSE CATEGORIES when no search yet -->
<div style="max-width:1200px;margin:0 auto;padding:48px 5% 80px;">
  <div class="section-header" style="text-align:left;margin-bottom:28px;">
    <div class="section-tag">🐾 Browse</div>
    <h2 class="section-title">Shop by <em>Category</em></h2>
  </div>
  <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;">
    <?php
    $catCards = [
      'food'        => ['bg'=>'#FDE8D0','count'=>3],
      'litter'      => ['bg'=>'#D6E8D8','count'=>3],
      'toys'        => ['bg'=>'#D8E0F0','count'=>3],
      'apparel'     => ['bg'=>'#F4E8F0','count'=>3],
      'accessories' => ['bg'=>'#FFF4D6','count'=>3],
    ];
    foreach ($categories as $c):
      $cc = $catCards[$c['slug']];
    ?>
    <a href="/shop/category.php?slug=<?= $c['slug'] ?>"
       style="text-decoration:none;background:<?= $cc['bg'] ?>;border-radius:20px;padding:28px 20px;text-align:center;transition:transform .2s,box-shadow .2s;"
       onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(61,35,20,.12)'"
       onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
      <div style="font-size:2.8rem;margin-bottom:10px;"><?= $c['icon'] ?></div>
      <h3 style="font-family:'Playfair Display',serif;font-size:.95rem;font-weight:900;color:var(--brown);margin-bottom:4px;"><?= $c['label'] ?></h3>
      <span style="font-size:.75rem;color:var(--orange);font-weight:600;"><?= $cc['count'] ?>+ products</span>
    </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<?php include "../inc/footer.inc.php"; ?>
</body>
</html>
