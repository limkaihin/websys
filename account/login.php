<?php
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/db.php';

if (is_logged_in()) redirect('index.php');

// ── POST processing BEFORE any output ────────────────────────────────────────
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email    = post('email');
    $password = post('password');

    if (!$email || !$password) {
        $errors['form'] = 'Please enter your email and password.';
    } else {
        $pdo  = db();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);   // safe — no output yet
            $_SESSION['user'] = [
                'id'       => $user['id'],
                'name'     => $user['name'],
                'email'    => $user['email'],
                'cat_name' => $user['cat_name'],
                'role'     => $user['role'],
            ];
            clear_old();
            set_flash('success', 'Welcome back, ' . $user['name'] . '! 🐾');
            redirect('index.php');         // safe — no output yet
        } else {
            $errors['form'] = 'Incorrect email or password.';
            store_old(['email' => $email]);
        }
    }
}

// ── Output starts here ────────────────────────────────────────────────────────
$pageTitle = 'Log In';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="membership" id="membership" style="margin:60px 5%;grid-template-columns:1fr 1fr;">
  <div class="membership-left">
    <h2>Welcome Back to <em>MeowMart</em></h2>
    <p>Log in to access your MeowClub perks, track orders, earn Pawpoints, and shop with your saved details.</p>
    <div class="membership-perks">
      <div class="perk"><div class="icon"><i class="fa-solid fa-gift"></i></div><div class="text"><strong>Your Pawpoints</strong><span>Check your balance &amp; rewards</span></div></div>
      <div class="perk"><div class="icon"><i class="fa-solid fa-box-open"></i></div><div class="text"><strong>Order History</strong><span>Track and reorder easily</span></div></div>
      <div class="perk"><div class="icon"><i class="fa-solid fa-cat"></i></div><div class="text"><strong>Cat Profile</strong><span>Personalised picks for your cat</span></div></div>
    </div>
    <p style="color:var(--blush);font-size:.82rem;margin-top:20px;">
      Demo credentials:<br>
      admin@meowmart.test / password<br>
      member@meowmart.test / password
    </p>
  </div>
  <div class="membership-right">
    <h3>Log In to Your Account</h3>
    <?php if (!empty($errors['form'])): ?>
      <p style="background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);border-radius:10px;
                padding:10px 16px;color:#fca5a5;font-size:.88rem;margin-bottom:16px;" role="alert">
        <?= h($errors['form']) ?>
      </p>
    <?php endif; ?>
    <form method="POST" novalidate>
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <div class="form-field">
        <label for="li-email"><i class="fa-solid fa-envelope fa-xs"></i> Email Address</label>
        <input id="li-email" type="email" name="email" value="<?= old('email') ?>"
               placeholder="you@example.com" required autocomplete="email"/>
      </div>
      <div class="form-field">
        <label for="li-pw"><i class="fa-solid fa-lock fa-xs"></i> Password</label>
        <input id="li-pw" type="password" name="password"
               placeholder="Your password" required autocomplete="current-password"/>
      </div>
      <button class="btn-join" type="submit"><i class="fa-solid fa-right-to-bracket"></i> Log In</button>
      <p class="form-note">Not a member yet?
        <a href="<?= h(base_url('account/register.php')) ?>" style="color:var(--blush);">Join MeowClub free →</a>
      </p>
    </form>
  </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
