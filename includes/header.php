<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';

_init_session(db());
ensure_user_collection_state_loaded();

$flash = get_flash();
$pageTitle = $pageTitle ?? site_name();
$pageDesc = $pageDesc ?? "MeowMart — Singapore's favourite destination for premium cat products.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= h($pageDesc) ?>">
  <title><?= h($pageTitle) ?> – MeowMart</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= h(base_url('assets/css/style.css')) ?>">
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>

<?php if ($flash): ?>
<div class="flash-toast flash-toast-<?= $flash['type'] === 'error' ? 'error' : 'success' ?>" role="<?= $flash['type'] === 'error' ? 'alert' : 'status' ?>" aria-live="polite" data-flash-toast>
  <div class="flash-toast__text"><?= h($flash['message']) ?></div>
  <button type="button" class="flash-toast__close" aria-label="Dismiss message" data-flash-close>&times;</button>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var toast = document.querySelector('[data-flash-toast]');
  if (!toast) return;
  var closed = false;
  function removeToast() {
    if (closed) return;
    closed = true;
    toast.classList.add('is-hiding');
    window.setTimeout(function () {
      if (toast.parentNode) toast.parentNode.removeChild(toast);
    }, 240);
  }
  var closeBtn = toast.querySelector('[data-flash-close]');
  if (closeBtn) closeBtn.addEventListener('click', removeToast);
  window.setTimeout(removeToast, 10000);
});
</script>
<?php endif; ?>

<div class="site-header">
  <div class="announcement text-center" role="note">
    🎉 Free shipping on orders above <span>$60</span> · Use code <span>MEOW10</span> for 10% off
  </div>
  <?php include __DIR__ . '/navbar.php'; ?>
</div>

<main id="main-content">
