<?php
/**
 * MeowMart — Site Header
 * Integrates:
 *   - HTML5 Boilerplate (Open Source Project #3): meta tags, viewport, og tags, theme-color
 *   - Font Awesome Free (Open Source Project #4): icon CDN with SRI hash
 */
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';

// Initialise session (Zebra_Session or default) — must happen after DB
_init_session(db());

$flash     = get_flash();
$pageTitle = $pageTitle ?? site_name();
$pageDesc  = $pageDesc  ?? 'MeowMart — Singapore\'s favourite destination for premium cat products. Food, litter, toys, apparel and more.';
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
  <!-- ── HTML5 Boilerplate: essential meta (Open Source Project #3) ── -->
  <meta charset="UTF-8" />
  <meta name="viewport"    content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="description" content="<?= h($pageDesc) ?>" />
  <meta name="theme-color" content="#3D2314" />

  <!-- Open Graph / Social sharing — HTML5 Boilerplate recommendation -->
  <meta property="og:type"        content="website" />
  <meta property="og:title"       content="<?= h($pageTitle) ?> – MeowMart" />
  <meta property="og:description" content="<?= h($pageDesc) ?>" />
  <meta property="og:site_name"   content="MeowMart" />
  <meta property="og:locale"      content="en_SG" />

  <title><?= h($pageTitle) ?> – Everything Your Cat Deserves</title>

  <!-- Preconnect for performance (HTML5 Boilerplate guidance) -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" />

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous" />

  <!-- Font Awesome Free 6.5.1 (Open Source Project #4) — with SRI hash for supply-chain security -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer" />

  <!-- Site CSS -->
  <link rel="stylesheet" href="<?= h(base_url('assets/css/style.css')) ?>" />
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>

<!-- Flash messages -->
<?php if ($flash): ?>
<div role="status" aria-live="polite"
     style="position:fixed;top:16px;left:50%;transform:translateX(-50%);z-index:10000;
            background:<?= $flash['type']==='error'?'#b91c1c':'#166534' ?>;
            color:#fff;padding:12px 32px;border-radius:12px;
            font-family:'DM Sans',sans-serif;font-size:.88rem;font-weight:600;
            box-shadow:0 4px 20px rgba(0,0,0,.2);">
  <?= h($flash['message']) ?>
</div>
<?php endif; ?>

<div class="site-header">
  <div class="announcement text-center" role="note">
    🎉 Free shipping on orders above <span>$60</span> · Use code <span>MEOW10</span> for 10% off
  </div>
  <?php include __DIR__ . '/navbar.php'; ?>
</div>

<main id="main-content">
