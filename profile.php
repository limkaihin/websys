<?php
$pageTitle = 'My Profile';
require_once __DIR__ . '/includes/header.php';
require_login();

$pdo    = db();
$user   = current_user();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name     = post('name');
    $cat_name = post('cat_name');
    $phone    = post('phone');
    $address  = post('address');

    if (strlen($name) < 2) $errors['name'] = 'Name must be at least 2 characters.';

    if (empty($errors)) {
        $pdo->prepare('UPDATE users SET name=?, cat_name=?, phone=?, address=? WHERE id=?')
            ->execute([$name, $cat_name, $phone, $address, $user['id']]);
        // refresh session
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id=?');
        $stmt->execute([$user['id']]);
        $fresh = $stmt->fetch();
        $_SESSION['user'] = ['id'=>$fresh['id'],'name'=>$fresh['name'],'email'=>$fresh['email'],'cat_name'=>$fresh['cat_name'],'role'=>$fresh['role']];
        set_flash('success', 'Profile updated! 🐾');
        redirect('profile.php');
    }
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE id=?');
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();
?>

<section style="padding:80px 5%;min-height:70vh;">
  <div style="max-width:700px;margin:0 auto;">
    <div class="section-header" style="text-align:left;margin-bottom:40px;">
      <div class="section-tag">🐱 MeowClub Member</div>
      <h1 class="section-title">Your <em>Profile</em></h1>
    </div>

    <div style="background:var(--brown);border-radius:28px;padding:20px 32px;display:flex;align-items:center;gap:20px;margin-bottom:40px;">
      <div style="width:64px;height:64px;border-radius:50%;background:var(--orange);display:flex;align-items:center;justify-content:center;font-size:2rem;flex-shrink:0;">🐱</div>
      <div>
        <p style="font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:700;color:var(--cream);"><?= h($profile['name']) ?></p>
        <p style="font-size:.85rem;color:var(--blush);"><?= h($profile['email']) ?></p>
        <?php if ($profile['cat_name']): ?>
          <p style="font-size:.82rem;color:var(--orange-lt);margin-top:4px;">🐱 <?= h($profile['cat_name']) ?>'s human</p>
        <?php endif; ?>
      </div>
    </div>

    <div class="membership-right" style="background:var(--white);border:1.5px solid var(--warm);">
      <h3 style="color:var(--brown);margin-bottom:24px;">Edit Details</h3>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <div class="form-field" style="--blush:var(--brown-md);">
          <label style="color:var(--brown-md);">Full Name</label>
          <input type="text" name="name" value="<?= h($profile['name']) ?>" required
                 style="background:var(--warm);border-color:var(--warm);color:var(--brown);"/>
          <?php if (!empty($errors['name'])): ?><p style="color:#b91c1c;font-size:.78rem;margin-top:4px;"><?= h($errors['name']) ?></p><?php endif; ?>
        </div>
        <div class="form-field">
          <label style="color:var(--brown-md);">Your Cat's Name 🐱</label>
          <input type="text" name="cat_name" value="<?= h($profile['cat_name'] ?? '') ?>" placeholder="e.g. Mochi"
                 style="background:var(--warm);border-color:var(--warm);color:var(--brown);"/>
        </div>
        <div class="form-field">
          <label style="color:var(--brown-md);">Phone Number</label>
          <input type="text" name="phone" value="<?= h($profile['phone'] ?? '') ?>" placeholder="+65 9123 4567"
                 style="background:var(--warm);border-color:var(--warm);color:var(--brown);"/>
        </div>
        <div class="form-field">
          <label style="color:var(--brown-md);">Delivery Address</label>
          <input type="text" name="address" value="<?= h($profile['address'] ?? '') ?>" placeholder="12 Whisker Lane, Singapore"
                 style="background:var(--warm);border-color:var(--warm);color:var(--brown);"/>
        </div>
        <button class="btn-primary" type="submit" style="width:100%;justify-content:center;">Save Changes</button>
      </form>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
