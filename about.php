<?php
$pageTitle = 'About MeowMart';
require_once __DIR__ . '/includes/header.php';
?>

<!-- HERO-STYLE HEADER -->
<div style="background:var(--warm);padding:100px 5% 80px;text-align:center;">
  <div class="hero-eyebrow" style="margin:0 auto 24px;">🐾 Our Story</div>
  <h1 class="section-title" style="font-size:clamp(2.5rem,5vw,4rem);margin-bottom:20px;">
    Made with Love, for <em>Every Cat</em>
  </h1>
  <p style="color:var(--brown-md);font-size:1.05rem;max-width:620px;margin:0 auto;line-height:1.75;">
    MeowMart was born from a simple idea: every cat deserves the best. We're Singapore's favourite destination for premium cat products — curated, tested, and loved by cats and their humans.
  </p>
</div>

<!-- MISSION / VISION -->
<section style="padding:80px 5%;background:var(--cream);">
  <div style="max-width:1100px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:40px;">
    <div style="background:var(--white);border-radius:28px;padding:48px 40px;">
      <div style="font-size:3rem;margin-bottom:20px;">🎯</div>
      <h2 style="font-family:'Playfair Display',serif;font-size:1.8rem;font-weight:900;margin-bottom:16px;color:var(--brown);">Our Mission</h2>
      <p style="color:var(--brown-md);line-height:1.75;">
        To make premium cat care accessible to every Singapore household — through thoughtfully curated products, honest advice, and a community that puts cats first.
      </p>
    </div>
    <div style="background:var(--brown);border-radius:28px;padding:48px 40px;">
      <div style="font-size:3rem;margin-bottom:20px;">✨</div>
      <h2 style="font-family:'Playfair Display',serif;font-size:1.8rem;font-weight:900;margin-bottom:16px;color:var(--cream);">Our Vision</h2>
      <p style="color:var(--blush);line-height:1.75;">
        A Singapore where every cat lives a long, healthy, joyful life — and every cat owner feels confident, supported, and part of something bigger.
      </p>
    </div>
  </div>
</section>

<!-- VALUES -->
<section style="padding:80px 5% 100px;background:var(--warm);">
  <div class="section-header">
    <div class="section-tag">💛 What We Stand For</div>
    <h2 class="section-title">Our <em>Values</em></h2>
  </div>
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:24px;max-width:1100px;margin:0 auto;">
    <?php
    $values = [
      ['🐾','Cat First', 'Every decision we make starts with one question: is this good for the cat?'],
      ['🌿','Quality',   'We only stock products we would use with our own cats. No compromises.'],
      ['💬','Community', 'MeowMart is more than a shop — it\'s a home for cat lovers across Singapore.'],
      ['♻️','Sustainability','We\'re committed to eco-friendly packaging and responsible sourcing.'],
    ];
    foreach ($values as [$icon, $title, $desc]):
    ?>
    <div style="background:var(--white);border-radius:24px;padding:36px 28px;text-align:center;">
      <div style="font-size:3rem;margin-bottom:16px;"><?= $icon ?></div>
      <h3 style="font-family:'Playfair Display',serif;font-size:1.2rem;margin-bottom:12px;color:var(--brown);"><?= $title ?></h3>
      <p style="font-size:.875rem;color:var(--brown-md);line-height:1.65;"><?= $desc ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
