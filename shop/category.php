<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../inc/head.inc.php"; ?>
  <style>
    .sub-pill { display:inline-flex;align-items:center;gap:6px;background:rgba(61,35,20,.08);border:1.5px solid transparent;border-radius:30px;padding:7px 16px;font-size:.8rem;font-weight:600;color:var(--brown-md);cursor:pointer;text-decoration:none;transition:all .2s; }
    .sub-pill:hover,.sub-pill.active { background:var(--orange);color:#fff;border-color:var(--orange); }
    .pg-btn { display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;border:1.5px solid var(--warm);background:#fff;font-size:.88rem;font-weight:700;color:var(--brown);text-decoration:none;transition:all .2s; }
    .pg-btn:hover,.pg-btn.active { background:var(--orange);color:#fff;border-color:var(--orange); }
    @keyframes floatE{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
  </style>
</head>
<body>
<?php include "../inc/nav.inc.php"; ?>
<?php
include "../inc/db.inc.php";

$slug = isset($_GET['slug']) ? strtolower(trim($_GET['slug'])) : '';
$page = max(1, (int)($_GET['page'] ?? 1));
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'name';
$PER_PAGE = 3;

$catDefs = [
  'food'        => ['label'=>'Cat Food',        'icon'=>'🥩', 'tagline'=>'Nourish Every Purr',        'bg'=>'linear-gradient(135deg,#FDE8D0,#F5C4A0)', 'desc'=>'Premium wet food, dry kibble, freeze-dried treats and supplements — sourced for health, loved by cats.', 'subtags'=>['All','Wet Food','Dry Food','Treats','Supplements','Grain-Free']],
  'litter'      => ['label'=>'Litter & Hygiene','icon'=>'🧴', 'tagline'=>'Clean Box, Happy Cat',      'bg'=>'linear-gradient(135deg,#D6E8D8,#B0CEB5)', 'desc'=>'Clumping, crystal, tofu and biodegradable litters. Litter boxes, scoops and odour control for every home.','subtags'=>['All','Clumping','Tofu','Crystal','Silica Gel','Litter Boxes']],
  'toys'        => ['label'=>'Toys & Play',     'icon'=>'🧶', 'tagline'=>'Play is Serious Business',  'bg'=>'linear-gradient(135deg,#D8E0F0,#B0BEE8)', 'desc'=>'Interactive wands, laser toys, tunnels, crinkle balls and electronic auto-play toys for every cat.','subtags'=>['All','Wand Toys','Laser','Electronic','Catnip','Tunnels']],
  'apparel'     => ['label'=>'Cat Apparel',     'icon'=>'👗', 'tagline'=>'Dress to Impress',          'bg'=>'linear-gradient(135deg,#F0D8E8,#E0B8CC)', 'desc'=>'Collars, bow ties, hoodies, raincoats and bandanas for the most stylish cats in Singapore.','subtags'=>['All','Collars','Bow Ties','Hoodies','Raincoats','Harnesses']],
  'accessories' => ['label'=>'Accessories',     'icon'=>'🎀', 'tagline'=>'Everything They Need',      'bg'=>'linear-gradient(135deg,#FFF4D6,#FFE090)', 'desc'=>'Cat trees, beds, carriers, feeders, grooming tools and everything else your cat needs to live well.','subtags'=>['All','Cat Trees','Beds','Carriers','Feeders','Scratchers']],
];

if (!isset($catDefs[$slug])) { header("Location: /shop/categories.php"); exit; }
$catDef = $catDefs[$slug];

$bgCols = ['food'=>'#FDE8D0','litter'=>'#D6E8D8','toys'=>'#D8E0F0','apparel'=>'#F4E8F0','accessories'=>'#FFF4D6'];
$icons  = ['food'=>'🥩','litter'=>'🧴','toys'=>'🧶','apparel'=>'👗','accessories'=>'🎀'];
$icon   = $catDef['icon'];
$bg     = $bgCols[$slug];

// Get all products for this category
$allProducts = filterProducts($catDef['label'], '', $sort);
$total       = count($allProducts);
$totalPages  = max(1, (int)ceil($total / $PER_PAGE));
$page        = min($page, $totalPages);
$offset      = ($page - 1) * $PER_PAGE;
$products    = array_slice($allProducts, $offset, $PER_PAGE);

// Prev/Next category
$slugList = array_keys($catDefs);
$ci       = (int)array_search($slug, $slugList);
$prevSlug = $ci > 0                      ? $slugList[$ci-1] : null;
$nextSlug = $ci < count($slugList)-1     ? $slugList[$ci+1] : null;

function pageUrl(string $s, int $pg, string $so): string {
    return "/shop/category.php?slug=$s&page=$pg&sort=$so";
}
?>
<title><?= htmlspecialchars($catDef['label']) ?> – MeowMart</title>

<!-- BREADCRUMB -->
<div style="background:var(--warm);padding:14px 5%;border-bottom:1.5px solid var(--cream);">
  <p style="font-size:.82rem;color:var(--brown-md);max-width:1200px;margin:0 auto;">
    <a href="/index.php"           style="color:var(--brown-md);text-decoration:none;">Home</a> ›
    <a href="/shop/categories.php" style="color:var(--brown-md);text-decoration:none;">Categories</a> ›
    <span style="color:var(--orange);font-weight:600;"><?= htmlspecialchars($catDef['label']) ?></span>
    <?php if ($page > 1): ?> › <span style="color:var(--brown-md);">Page <?= $page ?></span><?php endif; ?>
  </p>
</div>

<!-- HERO BANNER -->
<div style="background:<?= $catDef['bg'] ?>;padding:70px 5% 50px;display:grid;grid-template-columns:1fr auto;align-items:center;gap:40px;overflow:hidden;">
  <div>
    <div class="hero-eyebrow" style="margin-bottom:14px;"><?= $icon ?> <?= htmlspecialchars($catDef['label']) ?></div>
    <h1 style="font-family:'Playfair Display',serif;font-size:clamp(2.2rem,5vw,3.8rem);font-weight:900;line-height:1.05;color:var(--brown);margin-bottom:14px;">
      <?= htmlspecialchars($catDef['tagline']) ?>
    </h1>
    <p style="font-size:.95rem;color:var(--brown-md);line-height:1.7;max-width:500px;margin-bottom:18px;"><?= htmlspecialchars($catDef['desc']) ?></p>
    <span style="font-size:.88rem;font-weight:600;color:var(--brown);">
      <?= $total ?> product<?= $total!==1?'s':'' ?>
      <?php if ($totalPages > 1): ?> · Page <?= $page ?> of <?= $totalPages ?><?php endif; ?>
    </span>
  </div>
  <div style="font-size:clamp(5rem,10vw,9rem);line-height:1;filter:drop-shadow(0 8px 24px rgba(61,35,20,.12));animation:floatE 3s ease-in-out infinite;">
    <?= $icon ?>
  </div>
</div>

<!-- SUBTAG PILLS (decorative) -->
<div style="background:#fff;border-bottom:1.5px solid var(--warm);padding:16px 5%;overflow-x:auto;">
  <div style="display:flex;gap:8px;flex-wrap:wrap;max-width:1200px;margin:0 auto;">
    <?php foreach ($catDef['subtags'] as $t): ?>
      <span class="sub-pill <?= $t==='All'?'active':'' ?>"><?= htmlspecialchars($t) ?></span>
    <?php endforeach; ?>
  </div>
</div>

<!-- TOOLBAR -->
<div style="max-width:1200px;margin:0 auto;padding:28px 5% 0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
  <p style="font-size:.9rem;color:var(--brown-md);">
    Showing <strong style="color:var(--brown);"><?= count($products) ?></strong>
    of <strong style="color:var(--brown);"><?= $total ?></strong> products
  </p>
  <select onchange="location.href=this.value"
          style="border:1.5px solid var(--warm);border-radius:30px;padding:9px 18px;font-family:inherit;font-size:.85rem;font-weight:600;color:var(--brown);background:#fff;cursor:pointer;outline:none;">
    <?php foreach (['name'=>'Name A–Z','price_asc'=>'Price ↑','price_desc'=>'Price ↓','featured'=>'Featured First'] as $v=>$l): ?>
      <option value="<?= pageUrl($slug,$page,$v) ?>" <?= $sort===$v?'selected':'' ?>><?= $l ?></option>
    <?php endforeach; ?>
  </select>
</div>

<!-- PRODUCTS — 3 per page -->
<div style="max-width:1200px;margin:0 auto;padding:24px 5% 40px;">
  <?php if (empty($products)): ?>
    <div style="text-align:center;padding:80px;background:var(--warm);border-radius:24px;">
      <div style="font-size:4rem;margin-bottom:16px;"><?= $icon ?></div>
      <h3 style="font-family:'Playfair Display',serif;font-size:1.5rem;margin-bottom:10px;">No products in this category yet</h3>
      <a href="/shop/categories.php" class="btn-primary" style="text-decoration:none;display:inline-block;padding:12px 28px;margin-top:12px;">Browse Categories</a>
    </div>
  <?php else: ?>
  <div class="products-grid">
    <?php foreach ($products as $p): ?>
    <div class="product-card">
      <div class="product-img" style="background:<?= $bg ?>;">
        <span><?= $icon ?></span>
        <?php if ($p['is_featured']): ?><div class="ribbon">Featured</div><?php endif; ?>
      </div>
      <div class="product-body">
        <div class="product-brand"><?= htmlspecialchars($p['category']) ?></div>
        <h3 class="product-name"><?= htmlspecialchars($p['name']) ?></h3>
        <div class="product-stars">⭐⭐⭐⭐⭐ <span class="count">(<?= (($p['id']*47+83)%450)+50 ?>)</span></div>
        <?php if (!empty($p['description'])): ?>
          <p style="font-size:.78rem;color:var(--brown-md);line-height:1.5;margin:6px 0 10px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
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

  <!-- PAGINATION -->
  <?php if ($totalPages > 1): ?>
  <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-top:40px;flex-wrap:wrap;">
    <?php if ($page > 1): ?>
      <a href="<?= pageUrl($slug,$page-1,$sort) ?>" class="pg-btn">‹</a>
    <?php endif; ?>
    <?php for ($pg=1; $pg<=$totalPages; $pg++): ?>
      <a href="<?= pageUrl($slug,$pg,$sort) ?>" class="pg-btn <?= $pg===$page?'active':'' ?>"><?= $pg ?></a>
    <?php endfor; ?>
    <?php if ($page < $totalPages): ?>
      <a href="<?= pageUrl($slug,$page+1,$sort) ?>" class="pg-btn">›</a>
    <?php endif; ?>
  </div>
  <p style="text-align:center;margin-top:10px;font-size:.82rem;color:var(--brown-md);">
    Page <?= $page ?> of <?= $totalPages ?> · <?= $total ?> total products
  </p>
  <?php endif; ?>
</div>

<!-- PREV / NEXT CATEGORY NAV -->
<div style="max-width:1200px;margin:0 auto;padding:0 5% 80px;margin-top:20px;">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;padding-top:32px;border-top:1.5px solid var(--warm);">
    <?php if ($prevSlug): ?>
      <a href="/shop/category.php?slug=<?= $prevSlug ?>"
         style="text-decoration:none;display:flex;align-items:center;gap:10px;background:#fff;border:1.5px solid var(--warm);border-radius:16px;padding:13px 20px;color:var(--brown);font-weight:600;font-size:.88rem;transition:border-color .2s;"
         onmouseover="this.style.borderColor='var(--orange)'" onmouseout="this.style.borderColor='var(--warm)'">
        ← <?= $catDefs[$prevSlug]['icon'] ?> <?= htmlspecialchars($catDefs[$prevSlug]['label']) ?>
      </a>
    <?php else: ?><div></div><?php endif; ?>

    <a href="/shop/categories.php" style="font-size:.85rem;color:var(--orange);font-weight:600;text-decoration:none;">All Categories →</a>

    <?php if ($nextSlug): ?>
      <a href="/shop/category.php?slug=<?= $nextSlug ?>"
         style="text-decoration:none;display:flex;align-items:center;gap:10px;background:#fff;border:1.5px solid var(--warm);border-radius:16px;padding:13px 20px;color:var(--brown);font-weight:600;font-size:.88rem;transition:border-color .2s;"
         onmouseover="this.style.borderColor='var(--orange)'" onmouseout="this.style.borderColor='var(--warm)'">
        <?= $catDefs[$nextSlug]['icon'] ?> <?= htmlspecialchars($catDefs[$nextSlug]['label']) ?> →
      </a>
    <?php else: ?><div></div><?php endif; ?>
  </div>
</div>

<?php include "../inc/footer.inc.php"; ?>
</body>
</html>
