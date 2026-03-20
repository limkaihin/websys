<?php
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/db.php';

if (is_logged_in()) redirect('index.php');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = post('email');
    $password = post('password');

    if (!$email || !$password) {
        $errors['form'] = 'Please enter your email and password.';
    } else {
        $pdo = db();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'cat_name' => $user['cat_name'],
                'role' => $user['role'],
            ];
            unset($_SESSION['_user_collection_state_loaded']);
            ensure_user_collection_state_loaded();
            persist_user_collection_state();
            clear_old();
            set_flash('success', 'Welcome back, ' . $user['name'] . '! 🐾');
            redirect('index.php');
        } else {
            $errors['form'] = 'Incorrect email or password.';
            store_old(['email' => $email]);
        }
    }
}

$pageTitle = 'Log In';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<section class="auth-page container">
  <div class="row g-4 align-items-stretch">
    <div class="col-lg-6">
      <div class="auth-info-card h-100">
        <p class="auth-eyebrow">MeowClub</p>
        <h1>Welcome back</h1>
        <p>Log in to track orders, view your wishlist, keep your cart, and enjoy your MeowClub perks.</p>

        <div class="auth-feature-list" role="list">
          <div class="auth-feature-item" role="listitem">
            <div class="auth-feature-icon"><i class="fa-solid fa-gift"></i></div>
            <div><strong>Your Pawpoints</strong><span>Check your rewards and member perks.</span></div>
          </div>
          <div class="auth-feature-item" role="listitem">
            <div class="auth-feature-icon"><i class="fa-solid fa-box-open"></i></div>
            <div><strong>Order history</strong><span>Review past purchases and reorder faster.</span></div>
          </div>
          <div class="auth-feature-item" role="listitem">
            <div class="auth-feature-icon"><i class="fa-solid fa-heart"></i></div>
            <div><strong>Wishlist and cart</strong><span>Pick up where you left off on any device.</span></div>
          </div>
        </div>

        <div class="auth-demo-box">
          <strong>Demo accounts</strong><br>
          admin@meowmart.test / password<br>
          member@meowmart.test / password
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="auth-form-card h-100">
        <h2>Log In</h2>
        <p class="auth-helper-text">Use your registered email address and password.</p>

        <?php if (!empty($errors['form'])): ?>
          <div class="alert alert-danger" role="alert"><?= h($errors['form']) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">

          <div class="mb-3">
            <label for="li-email" class="form-label">Email Address</label>
            <input id="li-email" type="email" name="email" value="<?= old('email') ?>" class="form-control" placeholder="you@example.com" required autocomplete="email">
          </div>

          <div class="mb-3">
            <label for="li-pw" class="form-label">Password</label>
            <input id="li-pw" type="password" name="password" class="form-control" placeholder="Your password" required autocomplete="current-password">
          </div>

          <button class="btn meow-btn-primary w-100" type="submit"><i class="fa-solid fa-right-to-bracket me-2"></i>Log In</button>
          <p class="auth-note">Not a member yet? <a href="<?= h(base_url('account/register.php')) ?>">Join MeowClub free →</a></p>
        </form>
      </div>
    </div>
  </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
