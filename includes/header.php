<?php
require_once __DIR__ . '/functions.php';
$flash     = get_flash();
$pageTitle = $pageTitle ?? site_name();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="description" content="MeowMart is a sample cat retail website built with HTML5, Bootstrap, CSS, JavaScript, PHP and MySQL." />
  <title><?= h($pageTitle) ?> – Everything Your Cat Deserves</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= h(base_url('assets/css/style.css')) ?>"/>
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>

<?php if ($flash): ?>
<div role="status" aria-live="polite" style="position:fixed;top:16px;left:50%;transform:translateX(-50%);z-index:10000;
            background:<?= $flash['type']==='error'?'#b91c1c':'#166534' ?>;
            color:#fff;padding:12px 32px;border-radius:12px;font-family:'DM Sans',sans-serif;
            font-size:.88rem;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.2);">
  <?= h($flash['message']) ?>
</div>
<?php endif; ?>

<div class="site-header">
  <div class="announcement text-center" role="note">
    🎉 Free shipping on orders above <span>$60</span> · New arrivals every Friday · Use code <span>MEOW10</span> for 10% off
  </div>
  <?php include __DIR__ . '/navbar.php'; ?>
</div>
<main id="main-content">
