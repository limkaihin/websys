<?php
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_login();

// ── POST processing BEFORE any output ────────────────────────────────────────
$errors = [];
$pdo    = db();
$user   = current_user();
$tab    = $_GET['tab'] ?? 'profile';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name     = post('name');
    $cat_name = post('cat_name');
    $phone    = post('phone');
    $address  = post('address');
    $newPass  = post('new_password');
    $conPass  = post('confirm_password');

    if (strlen($name) < 2) $errors['name'] = 'Name must be at least 2 characters.';
    if ($newPass !== '') {
        if (strlen($newPass) < 8)   $errors['new_password']     = 'Password must be at least 8 characters.';
        elseif ($newPass !== $conPass) $errors['confirm_password'] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        if ($newPass !== '') {
            $pdo->prepare('UPDATE users SET name=?, cat_name=?, phone=?, address=?, password=? WHERE id=?')
                ->execute([$name, $cat_name, $phone, $address, password_hash($newPass, PASSWORD_BCRYPT), $user['id']]);
        } else {
            $pdo->prepare('UPDATE users SET name=?, cat_name=?, phone=?, address=? WHERE id=?')
                ->execute([$name, $cat_name, $phone, $address, $user['id']]);
        }
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id=?');
        $stmt->execute([$user['id']]);
        $fresh = $stmt->fetch();
        $_SESSION['user'] = ['id'=>$fresh['id'],'name'=>$fresh['name'],'email'=>$fresh['email'],'cat_name'=>$fresh['cat_name'],'role'=>$fresh['role']];
        set_flash('success', 'Profile updated! 🐾');
        redirect('account/profile.php');
    }
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE id=?');
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();

$orders = [];
try {
    $os = $pdo->prepare('SELECT o.*, COUNT(oi.id) AS item_count FROM orders o
        LEFT JOIN order_items oi ON oi.order_id = o.id
        WHERE o.user_id = ? GROUP BY o.id ORDER BY o.created_at DESC LIMIT 10');
    $os->execute([$user['id']]);
    $orders = $os->fetchAll();
} catch (PDOException $e) { if (strpos($e->getMessage(), '1146') === false) throw $e; }

$statusColors = [
    'confirmed'=>['bg'=>'#fef3c7','color'=>'#92400e','label'=>'Confirmed'],
    'shipped'  =>['bg'=>'#dbeafe','color'=>'#1d4ed8','label'=>'Shipped'],
    'delivered'=>['bg'=>'#dcfce7','color'=>'#166534','label'=>'Delivered'],
    'cancelled'=>['bg'=>'#fee2e2','color'=>'#991b1b','label'=>'Cancelled'],
];

// ── Output starts here ────────────────────────────────────────────────────────
$pageTitle = 'My Profile';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<section style="padding:60px 5%;min-height:70vh;">
  <div style="max-width:820px;margin:0 auto;">

    <!-- Header card -->
    <div style="background:var(--brown);border-radius:28px;padding:24px 36px;display:flex;align-items:center;gap:20px;margin-bottom:32px;flex-wrap:wrap;">
      <div style="width:68px;height:68px;border-radius:50%;background:var(--orange);display:flex;align-items:center;justify-content:center;font-size:2rem;flex-shrink:0;">
        <i class="fa-solid fa-cat" style="color:#fff;"></i>
      </div>
      <div style="flex:1;">
        <p style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:700;color:var(--cream);"><?= h($profile['name']) ?></p>
        <p style="font-size:.85rem;color:var(--blush);"><?= h($profile['email']) ?></p>
        <?php if ($profile['cat_name']): ?>
          <p style="font-size:.82rem;color:var(--orange-lt);margin-top:4px;">
            <i class="fa-solid fa-cat fa-xs"></i> <?= h($profile['cat_name']) ?>'s human
          </p>
        <?php endif; ?>
      </div>
      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="?tab=profile" style="background:<?= $tab==='profile'?'var(--orange)':'rgba(255,255,255,.12)' ?>;color:var(--cream);border-radius:20px;padding:8px 20px;font-size:.82rem;font-weight:600;text-decoration:none;">
          <i class="fa-solid fa-user fa-xs"></i> Profile
        </a>
        <a href="?tab=orders" style="background:<?= $tab==='orders'?'var(--orange)':'rgba(255,255,255,.12)' ?>;color:var(--cream);border-radius:20px;padding:8px 20px;font-size:.82rem;font-weight:600;text-decoration:none;">
          <i class="fa-solid fa-box-open fa-xs"></i> My Orders (<?= count($orders) ?>)
        </a>
      </div>
    </div>

    <?php if ($tab === 'profile'): ?>
    <div class="membership-right" style="background:var(--white);border:1.5px solid var(--warm);">
      <h3 style="color:var(--brown);margin-bottom:24px;">Edit Details</h3>
      <form method="POST" novalidate>
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <div class="form-field">
          <label for="pf-name" style="color:var(--brown-md);"><i class="fa-solid fa-user fa-xs"></i> Full Name</label>
          <input id="pf-name" type="text" name="name" value="<?= h($profile['name']) ?>" required
                 style="background:var(--warm);border-color:var(--warm);color:var(--brown);"/>
          <?php if (!empty($errors['name'])): ?><p style="color:#b91c1c;font-size:.78rem;margin-top:4px;"><?= h($errors['name']) ?></p><?php endif; ?>
        </div>
        <div class="form-field">
          <label for="pf-cat" style="color:var(--brown-md);"><i class="fa-solid fa-cat fa-xs"></i> Cat's Name</label>
          <input id="pf-cat" type="text" name="cat_name" value="<?= h($profile['cat_name'] ?? '') ?>"
                 placeholder="e.g. Mochi" style="background:var(--warm);border-color:var(--warm);color:var(--brown);"/>
        </div>
        <div class="form-field">
          <label for="pf-phone" style="color:var(--brown-md);"><i class="fa-solid fa-phone fa-xs"></i> Phone</label>
          <input id="pf-phone" type="tel" name="phone" value="<?= h($profile['phone'] ?? '') ?>"
                 placeholder="+65 9123 4567" style="background:var(--warm);border-color:var(--warm);color:var(--brown);"/>
        </div>
        <div class="form-field">
          <label for="pf-addr" style="color:var(--brown-md);"><i class="fa-solid fa-location-dot fa-xs"></i> Address</label>
          <input id="pf-addr" type="text" name="address" value="<?= h($profile['address'] ?? '') ?>"
                 placeholder="12 Whisker Lane, Singapore" style="background:var(--warm);border-color:var(--warm);color:var(--brown);"/>
        </div>
        <hr style="border:none;border-top:1.5px solid var(--warm);margin:24px 0;">
        <h4 style="color:var(--brown-md);font-size:.9rem;margin-bottom:16px;">
          <i class="fa-solid fa-key fa-xs"></i> Change Password
          <span style="font-weight:400;font-size:.8rem;">(leave blank to keep current)</span>
        </h4>
        <div class="form-field">
          <label for="pf-newpw" style="color:var(--brown-md);">New Password</label>
          <input id="pf-newpw" type="password" name="new_password" placeholder="Min 8 characters"
                 style="background:var(--warm);border-color:var(--warm);color:var(--brown);"/>
          <?php if (!empty($errors['new_password'])): ?><p style="color:#b91c1c;font-size:.78rem;margin-top:4px;"><?= h($errors['new_password']) ?></p><?php endif; ?>
        </div>
        <div class="form-field">
          <label for="pf-conpw" style="color:var(--brown-md);">Confirm New Password</label>
          <input id="pf-conpw" type="password" name="confirm_password" placeholder="Repeat new password"
                 style="background:var(--warm);border-color:var(--warm);color:var(--brown);"/>
          <?php if (!empty($errors['confirm_password'])): ?><p style="color:#b91c1c;font-size:.78rem;margin-top:4px;"><?= h($errors['confirm_password']) ?></p><?php endif; ?>
        </div>
        <button class="btn-primary" type="submit" style="width:100%;display:block;text-align:center;">
          <i class="fa-solid fa-floppy-disk"></i> Save Changes
        </button>
      </form>
    </div>

    <?php else: ?>
    <div style="margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
      <h2 style="font-family:'Playfair Display',serif;font-size:1.3rem;">Recent Orders</h2>
      <a href="<?= h(base_url('shop/orders.php')) ?>" style="color:var(--orange);font-size:.85rem;font-weight:600;text-decoration:none;">View All →</a>
    </div>
    <?php if (empty($orders)): ?>
      <div style="background:var(--white);border-radius:20px;padding:60px 24px;text-align:center;border:1.5px solid var(--warm);">
        <div style="font-size:3rem;margin-bottom:16px;color:var(--orange);"><i class="fa-solid fa-box-open"></i></div>
        <p style="color:var(--brown-md);margin-bottom:20px;">No orders yet.</p>
        <a href="<?= h(base_url('shop/products.php')) ?>" class="btn-primary" style="text-decoration:none;display:inline-block;">Start Shopping →</a>
      </div>
    <?php else: ?>
      <?php foreach ($orders as $o):
        $sc = $statusColors[$o['status']] ?? ['bg'=>'#f3f4f6','color'=>'#374151','label'=>ucfirst($o['status'])];
      ?>
      <div style="background:var(--white);border-radius:20px;margin-bottom:16px;border:1.5px solid var(--warm);overflow:hidden;">
        <div style="padding:14px 22px;background:var(--warm);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
          <div style="display:flex;gap:20px;flex-wrap:wrap;align-items:center;">
            <span style="font-weight:700;color:var(--orange);">Order #<?= (int)$o['id'] ?></span>
            <span style="font-size:.82rem;color:var(--brown-md);"><?= date('d M Y', strtotime($o['created_at'])) ?></span>
            <span style="font-size:.82rem;color:var(--brown-md);"><?= (int)$o['item_count'] ?> item<?= $o['item_count']!=1?'s':'' ?></span>
          </div>
          <span style="background:<?= $sc['bg'] ?>;color:<?= $sc['color'] ?>;border-radius:16px;padding:3px 12px;font-size:.75rem;font-weight:700;"><?= h($sc['label']) ?></span>
        </div>
        <div style="padding:16px 22px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
          <span style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:900;color:var(--orange);"><?= money((float)$o['total']) ?></span>
          <a href="<?= h(base_url('shop/order_confirmation.php?id=' . $o['id'])) ?>"
             style="background:var(--orange);color:#fff;border-radius:16px;padding:7px 18px;font-size:.82rem;font-weight:600;text-decoration:none;">
             View Details →
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
