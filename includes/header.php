<?php
require_once __DIR__ . '/db.php';
$flash     = get_flash();
$pageTitle = $pageTitle ?? site_name();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= h($pageTitle) ?> – Everything Your Cat Deserves</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="<?= h(base_url('assets/css/style.css')) ?>"/>
</head>
<body>

<?php if ($flash): ?>
<div style="position:fixed;top:16px;left:50%;transform:translateX(-50%);z-index:10000;
            background:<?= $flash['type']==='error'?'#b91c1c':'#166534' ?>;
            color:#fff;padding:12px 32px;border-radius:12px;font-family:'DM Sans',sans-serif;
            font-size:.88rem;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.2);">
  <?= h($flash['message']) ?>
</div>
<?php endif; ?>

  <!-- ANNOUNCEMENT BAR -->
  <div class="announcement">
    🎉 Free shipping on orders above <span>$60</span> · New arrivals every Friday · Use code <span>MEOW10</span> for 10% off
  </div>

<?php include __DIR__ . '/navbar.php'; ?>
