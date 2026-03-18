<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../inc/head.inc.php"; ?>
  <title>Categories – MeowMart</title>
</head>
<body>

<?php include "../inc/nav.inc.php"; ?>

<!-- PAGE HEADER -->
<div style="background:var(--warm);padding:80px 5% 60px;text-align:center;">
  <div class="hero-eyebrow" style="margin:0 auto 20px;">🐾 Browse by Category</div>
  <h1 class="section-title" style="font-size:clamp(2.2rem,4vw,3.5rem);margin-bottom:16px;">
    Shop by <em>Your Cat's</em> Mood
  </h1>
  <p style="color:var(--brown-md);font-size:1rem;max-width:500px;margin:0 auto;">
    Find exactly what your cat needs — from gourmet food to stylish apparel.
  </p>
</div>

<!-- CATEGORY CARDS -->
<section class="categories" style="padding:60px 5% 100px;">

  <?php
  $categories = [
    [
      'slug'    => 'food',
      'label'   => 'Cat Food',
      'icon'    => '🥩',
      'count'   => '340+',
      'desc'    => 'Premium wet food, dry kibble, freeze-dried treats and supplements for every life stage.',
      'color'   => 'linear-gradient(145deg,#FDE8D0,#F5C4A0)',
      'tags'    => ['Wet Food','Dry Food','Treats','Supplements'],
    ],
    [
      'slug'    => 'litter',
      'label'   => 'Litter & Hygiene',
      'icon'    => '🧴',
      'count'   => '120+',
      'desc'    => 'Clumping, crystal, tofu and biodegradable litters. Litter boxes, scoops and odour control.',
      'color'   => 'linear-gradient(145deg,#D6E8D8,#B8D4BC)',
      'tags'    => ['Clumping','Tofu','Crystal','Litter Boxes'],
    ],
    [
      'slug'    => 'toys',
      'label'   => 'Toys & Play',
      'icon'    => '🧶',
      'count'   => '200+',
      'desc'    => 'Interactive wands, laser toys, tunnels, crinkle balls and electronic auto-play toys.',
      'color'   => 'linear-gradient(145deg,#D8E0F0,#B8CAE8)',
      'tags'    => ['Wands','Laser','Tunnels','Puzzle Feeders'],
    ],
    [
      'slug'    => 'apparel',
      'label'   => 'Cat Apparel',
      'icon'    => '👗',
      'count'   => '80+',
      'desc'    => 'Collars, bow ties, hoodies, raincoats and bandanas for the most stylish cats in Singapore.',
      'color'   => 'linear-gradient(145deg,#F0D8E8,#E0B8CC)',
      'tags'    => ['Collars','Bow Ties','Hoodies','Raincoats'],
    ],
    [
      'slug'    => 'accessories',
      'label'   => 'Accessories',
      'icon'    => '🎀',
      'count'   => '150+',
      'desc'    => 'Cat trees, beds, carriers, feeders, grooming tools and everything else your cat needs.',
      'color'   => 'linear-gradient(145deg,#FFF4D6,#FFE4A0)',
      'tags'    => ['Cat Trees','Beds','Carriers','Feeders'],
    ],
  ];
  ?>

  <!-- TOP ROW — 3 cards -->
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;max-width:1200px;margin:0 auto 24px;">
    <?php foreach (array_slice($categories, 0, 3) as $cat): ?>
    <a href="/shop/category.php?slug=<?= $cat['slug'] ?>"
       style="text-decoration:none;display:block;"
       class="cat-page-card">
      <div style="background:<?= $cat['color'] ?>;border-radius:28px;padding:48px 36px;
                  height:100%;transition:transform .3s,box-shadow .3s;cursor:pointer;
                  box-shadow:0 4px 20px rgba(61,35,20,.08);"
           onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 20px 48px rgba(61,35,20,.16)'"
           onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 20px rgba(61,35,20,.08)'">
        <div style="font-size:4rem;margin-bottom:20px;"><?= $cat['icon'] ?></div>
        <h2 style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:900;
                   color:var(--brown);margin-bottom:8px;"><?= $cat['label'] ?></h2>
        <p style="font-size:.78rem;font-weight:700;color:var(--orange);
                  letter-spacing:.1em;text-transform:uppercase;margin-bottom:14px;">
          <?= $cat['count'] ?> products
        </p>
        <p style="font-size:.9rem;color:var(--brown-md);line-height:1.65;margin-bottom:20px;">
          <?= $cat['desc'] ?>
        </p>
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:24px;">
          <?php foreach ($cat['tags'] as $tag): ?>
            <span style="background:rgba(61,35,20,.1);border-radius:20px;padding:4px 12px;
                         font-size:.75rem;font-weight:600;color:var(--brown);">
              <?= $tag ?>
            </span>
          <?php endforeach; ?>
        </div>
        <span style="display:inline-flex;align-items:center;gap:6px;
                     background:var(--brown);color:var(--cream);
                     border-radius:30px;padding:10px 22px;font-size:.85rem;font-weight:600;">
          Browse <?= $cat['label'] ?> →
        </span>
      </div>
    </a>
    <?php endforeach; ?>
  </div>

  <!-- BOTTOM ROW — 2 cards wider -->
  <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:24px;max-width:1200px;margin:0 auto;">
    <?php foreach (array_slice($categories, 3) as $cat): ?>
    <a href="/shop/category.php?slug=<?= $cat['slug'] ?>"
       style="text-decoration:none;display:block;">
      <div style="background:<?= $cat['color'] ?>;border-radius:28px;padding:48px 36px;
                  display:flex;align-items:center;gap:36px;
                  transition:transform .3s,box-shadow .3s;cursor:pointer;
                  box-shadow:0 4px 20px rgba(61,35,20,.08);"
           onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 20px 48px rgba(61,35,20,.16)'"
           onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 20px rgba(61,35,20,.08)'">
        <div style="font-size:5rem;flex-shrink:0;"><?= $cat['icon'] ?></div>
        <div>
          <h2 style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:900;
                     color:var(--brown);margin-bottom:6px;"><?= $cat['label'] ?></h2>
          <p style="font-size:.78rem;font-weight:700;color:var(--orange);
                    letter-spacing:.1em;text-transform:uppercase;margin-bottom:10px;">
            <?= $cat['count'] ?> products
          </p>
          <p style="font-size:.9rem;color:var(--brown-md);line-height:1.65;margin-bottom:16px;">
            <?= $cat['desc'] ?>
          </p>
          <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:18px;">
            <?php foreach ($cat['tags'] as $tag): ?>
              <span style="background:rgba(61,35,20,.1);border-radius:20px;padding:4px 12px;
                           font-size:.75rem;font-weight:600;color:var(--brown);">
                <?= $tag ?>
              </span>
            <?php endforeach; ?>
          </div>
          <span style="display:inline-flex;align-items:center;gap:6px;
                       background:var(--brown);color:var(--cream);
                       border-radius:30px;padding:10px 22px;font-size:.85rem;font-weight:600;">
            Browse <?= $cat['label'] ?> →
          </span>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<?php include "../inc/footer.inc.php"; ?>
</body>
</html>
