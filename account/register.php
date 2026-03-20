<?php
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/mail.php';

if (is_logged_in()) redirect('index.php');

$prefillRef = sanitize_referral_code((string)($_GET['ref'] ?? ''));
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = post('name');
    $email = post('email');
    $cat_name = post('cat_name');
    $password = post('password');
    $confirm = post('confirm');
    $referral_code = sanitize_referral_code(post('referral_code'));

    if (strlen($name) < 2) $errors['name'] = 'Name must be at least 2 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email.';
    if (strlen($password) < 8) $errors['password'] = 'Password must be at least 8 characters.';
    if ($password !== $confirm) $errors['confirm'] = 'Passwords do not match.';
    if ($referral_code !== '' && strlen($referral_code) < 4) $errors['referral_code'] = 'Please enter a valid referral code.';

    if (empty($errors)) {
        $pdo = db();
        $exists = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $exists->execute([$email]);

        if ($exists->fetch()) {
            $errors['email'] = 'An account with this email already exists.';
        } else {
            $columns = ['name', 'email', 'cat_name', 'password', 'role'];
            $values = [$name, $email, $cat_name, password_hash($password, PASSWORD_BCRYPT), 'member'];

            if (db_has_column('users', 'referred_by')) {
                $columns[] = 'referred_by';
                $values[] = $referral_code !== '' ? $referral_code : null;
            }

            $placeholders = implode(',', array_fill(0, count($columns), '?'));
            $pdo->prepare('INSERT INTO users (' . implode(',', $columns) . ') VALUES (' . $placeholders . ')')->execute($values);

            $refLine = $referral_code !== ''
                ? '<p>Your account was created with referral code <strong>' . h($referral_code) . '</strong>.</p>'
                : '';
            $catLine = $cat_name
                ? '<p>We can\'t wait to help you find the best for <strong>' . h($cat_name) . '</strong>! 🐱</p>'
                : '';

            $html = "<p>Hi <strong>" . h($name) . "</strong>, welcome to the MeowClub family! 🐾</p>"
                . $catLine
                . $refLine
                . "<ul style='line-height:2'>"
                . "<li>🚚 Free delivery on orders above $60</li>"
                . "<li>🎁 Exclusive member-only deals</li>"
                . "<li>⭐ Pawpoints on every purchase</li>"
                . "</ul>"
                . "<a class='btn' href='" . base_url('shop/products.php') . "'>Start Shopping →</a>"
                . "<p style='margin-top:24px'>Purrs &amp; headbumps,<br><strong>The MeowMart Team</strong></p>";
            send_mail($email, $name, 'Welcome to MeowClub! 🐾', $html);

            set_flash('success', 'Welcome to MeowClub, ' . $name . '! Please log in.');
            clear_old();
            redirect('account/login.php');
        }
    }

    store_old(compact('name', 'email', 'cat_name', 'referral_code'));
}

$pageTitle = 'Join MeowClub';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<section class="auth-page container">
  <div class="row g-4 align-items-stretch">
    <div class="col-lg-6">
      <div class="auth-info-card h-100">
        <p class="auth-eyebrow">Free membership</p>
        <h1>Join MeowClub</h1>
        <p>Create your free account to earn Pawpoints, unlock deals, and enjoy a smoother checkout.</p>

        <div class="auth-feature-list" role="list">
          <div class="auth-feature-item" role="listitem">
            <div class="auth-feature-icon"><i class="fa-solid fa-star"></i></div>
            <div><strong>Earn Pawpoints</strong><span>Collect points on every order.</span></div>
          </div>
          <div class="auth-feature-item" role="listitem">
            <div class="auth-feature-icon"><i class="fa-solid fa-truck"></i></div>
            <div><strong>Free delivery</strong><span>Orders above $60 qualify automatically.</span></div>
          </div>
          <div class="auth-feature-item" role="listitem">
            <div class="auth-feature-icon"><i class="fa-solid fa-gift"></i></div>
            <div><strong>Member deals</strong><span>Get first access to selected promotions.</span></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="auth-form-card h-100">
        <h2>Create Your Account</h2>
        <p class="auth-helper-text">Fields marked below follow the same clear, responsive form style used in the lab exercises.</p>

        <form method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">

          <div class="mb-3">
            <label for="rg-name" class="form-label">Your Name</label>
            <input id="rg-name" type="text" name="name" value="<?= old('name') ?>" class="form-control" placeholder="e.g. Sarah Tan" required autocomplete="name">
            <?php if (!empty($errors['name'])): ?><div class="form-error-text"><?= h($errors['name']) ?></div><?php endif; ?>
          </div>

          <div class="mb-3">
            <label for="rg-email" class="form-label">Email Address</label>
            <input id="rg-email" type="email" name="email" value="<?= old('email') ?>" class="form-control" placeholder="you@example.com" required autocomplete="email">
            <?php if (!empty($errors['email'])): ?><div class="form-error-text"><?= h($errors['email']) ?></div><?php endif; ?>
          </div>

          <div class="mb-3">
            <label for="rg-cat" class="form-label">Your Cat's Name <span class="text-muted">(optional)</span></label>
            <input id="rg-cat" type="text" name="cat_name" value="<?= old('cat_name') ?>" class="form-control" placeholder="e.g. Mochi">
          </div>

          <div class="mb-3">
            <label for="rg-ref" class="form-label">Referral Code <span class="text-muted">(optional)</span></label>
            <input id="rg-ref" type="text" name="referral_code" value="<?= old('referral_code', $prefillRef) ?>" class="form-control" placeholder="Enter your friend's code" autocomplete="off" style="text-transform:uppercase;">
            <div class="form-help-text">Joining from a referral link will fill this in automatically.</div>
            <?php if (!empty($errors['referral_code'])): ?><div class="form-error-text"><?= h($errors['referral_code']) ?></div><?php endif; ?>
          </div>

          <div class="mb-3">
            <label for="rg-pw" class="form-label">Password</label>
            <input id="rg-pw" type="password" name="password" class="form-control" placeholder="At least 8 characters" required minlength="8" autocomplete="new-password">
            <?php if (!empty($errors['password'])): ?><div class="form-error-text"><?= h($errors['password']) ?></div><?php endif; ?>
          </div>

          <div class="mb-3">
            <label for="rg-cpw" class="form-label">Confirm Password</label>
            <input id="rg-cpw" type="password" name="confirm" class="form-control" placeholder="Repeat your password" required autocomplete="new-password">
            <?php if (!empty($errors['confirm'])): ?><div class="form-error-text"><?= h($errors['confirm']) ?></div><?php endif; ?>
          </div>

          <button class="btn meow-btn-primary w-100" type="submit"><i class="fa-solid fa-user-plus me-2"></i>Create Account</button>
          <p class="auth-note">Already a member? <a href="<?= h(base_url('account/login.php')) ?>">Log in →</a></p>
        </form>
      </div>
    </div>
  </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
