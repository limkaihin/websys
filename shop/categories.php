<?php
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$pageTitle = 'Shop by Category';
$pdo = db();
$counts = [];
foreach ($pdo->query('SELECT LOWER(category) AS slug, COUNT(*) AS total FROM products GROUP BY LOWER(category)') as $row) {
    $counts[$row['slug']] = (int)$row['total'];
}

$categories = [
    [
        'slug'  => 'food',
        'label' => 'Cat Food',
        'icon'  => '🥩',
        'desc'  => 'Premium wet food, dry kibble, freeze-dried treats and supplements for every life stage.',
        'color' => 'linear-gradient(145deg,#FDE8D0,#F5C4A0)',
        'tags'  => ['Wet Food','Dry Food','Treats','Supplements'],
    ],
    [
        'slug'  => 'litter',
        'label' => 'Litter & Hygiene',
        'icon'  => '🧴',
        'desc'  => 'Clumping, tofu and odour-control essentials for a cleaner, happier home.',
        'color' => 'linear-gradient(145deg,#D6E8D8,#B8D4BC)',
        'tags'  => ['Clumping','Tofu','Odour Control','Litter Boxes'],
    ],
    [
        'slug'  => 'toys',
        'label' => 'Toys & Play',
        'icon'  => '🧶',
        'desc'  => 'Interactive play, solo toys and boredom-busting picks to keep cats active.',
        'color' => 'linear-gradient(145deg,#D8E0F0,#B8CAE8)',
        'tags'  => ['Wands','Laser','Puzzle Feeders','Catnip'],
    ],
    [
        'slug'  => 'apparel',
        'label' => 'Cat Apparel',
        'icon'  => '👗',
        'desc'  => 'Bow ties, hoodies and playful outfits for stylish photo days and special occasions.',
        'color' => 'linear-gradient(145deg,#F0D8E8,#E0B8CC)',
        'tags'  => ['Collars','Bow Ties','Hoodies','Raincoats'],
    ],
    [
        'slug'  => 'accessories',
        'label' => 'Accessories',
        'icon'  => '🎀',
        'desc'  => 'Beds, feeders, cat trees and daily essentials your cat will actually use.',
        'color' => 'linear-gradient(145deg,#FFF4D6,#FFE4A0)',
        'tags'  => ['Cat Trees','Beds','Feeders','Grooming'],
    ],
];

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div style="background:var(--warm);padding:80px 5% 60px;text-align:center;">
  <div class="hero-eyebrow" style="margin:0 auto 20px;">🐾 Browse by Category</div>
  <h1 class="section-title" style="font-size:clamp(2.2rem,4vw,3.5rem);margin-bottom:16px;">Shop by <em>Your Cat's</em> Mood</h1>
  <p style="color:var(--brown-md);font-size:1rem;max-width:560px;margin:0 auto;">
    This page comes from the older websys build and is now merged into the newer MeowMart codebase.
    Use it to jump straight into the category your cat cares about most.
  </p>
</div>

<section style="padding:56px 5% 100px;">
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:24px;max-width:1200px;margin:0 auto;">
    <?php foreach ($categories as $cat): ?>
      <a href="<?= h(base_url('shop/products.php?cat=' . urlencode($cat['slug']))) ?>" style="text-decoration:none;color:inherit;display:block;">
        <article style="background:<?= $cat['color'] ?>;border-radius:28px;padding:36px 28px;height:100%;box-shadow:0 4px 20px rgba(61,35,20,.08);transition:transform .25s, box-shadow .25s;"
                 onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 20px 48px rgba(61,35,20,.16)'"
                 onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 20px rgba(61,35,20,.08)'">
          <div style="font-size:4rem;margin-bottom:18px;"><?= $cat['icon'] ?></div>
          <h2 style="font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:900;color:var(--brown);margin-bottom:8px;"><?= h($cat['label']) ?></h2>
          <p style="font-size:.78rem;font-weight:700;color:var(--orange);letter-spacing:.1em;text-transform:uppercase;margin-bottom:14px;">
            <?= ($counts[$cat['slug']] ?? 0) ?> products available
          </p>
          <p style="font-size:.92rem;color:var(--brown-md);line-height:1.65;margin-bottom:18px;"><?= h($cat['desc']) ?></p>
          <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;">
            <?php foreach ($cat['tags'] as $tag): ?>
              <span class="pill" style="background:rgba(61,35,20,.08);"><?= h($tag) ?></span>
            <?php endforeach; ?>
          </div>
          <span class="btn-outline" style="display:inline-block;text-decoration:none;">Browse <?= h($cat['label']) ?> →</span>
        </article>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
