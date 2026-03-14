<?php
require_once dirname(__DIR__) . '/includes/functions.php';
if (is_logged_in()) redirect('index.php');
$pageTitle = 'Join MeowClub';
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name     = post('name');
    $email    = post('email');
    $cat_name = post('cat_name');
    $password = post('password');
    $confirm  = post('confirm');

    if (strlen($name) < 2)                             $errors['name']     = 'Name must be at least 2 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))    $errors['email']    = 'Please enter a valid email.';
    if (strlen($password) < 8)                         $errors['password'] = 'Password must be at least 8 characters.';
    if ($password !== $confirm)                        $errors['confirm']  = 'Passwords do not match.';

    if (empty($errors)) {
        $pdo = db();
        $exists = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $exists->execute([$email]);
        if ($exists->fetch()) {
            $errors['email'] = 'An account with this email already exists.';
        } else {
            $pdo->prepare('INSERT INTO users (name, email, cat_name, password, role) VALUES (?,?,?,?,?)')
                ->execute([$name, $email, $cat_name, password_hash($password, PASSWORD_BCRYPT), 'member']);
            set_flash('success', 'Welcome to MeowClub, ' . $name . '! Please log in.');
            clear_old();
            redirect('account/login.php');
        }
    }
    store_old(compact('name','email','cat_name'));
}
?>

<div class="membership" id="membership" style="margin:60px 5%;">
  <div class="membership-left">
    <h2>Join the <em>MeowClub</em> & Save Every Day</h2>
    <p>Free membership with exclusive perks, early sale access, birthday treats for your cat, and more — all with no fees, ever.</p>
    <div class="membership-perks">
      <div class="perk"><div class="icon">🎁</div><div class="text"><strong>Earn Pawpoints</strong><span>Redeem rewards on every purchase</span></div></div>
      <div class="perk"><div class="icon">🚚</div><div class="text"><strong>Free Delivery</strong><span>On all orders for members</span></div></div>
      <div class="perk"><div class="icon">🎂</div><div class="text"><strong>Birthday Surprise</strong><span>A free gift for your cat each year</span></div></div>
      <div class="perk"><div class="icon">⚡</div><div class="text"><strong>Early Access</strong><span>Shop new arrivals & sales first</span></div></div>
    </div>
  </div>
  <div class="membership-right">
    <h3>Create Your Free Account</h3>
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <div class="form-field">
        <label for="reg-name">Your Name</label>
        <input type="text" id="reg-name" name="name" value="<?= old('name') ?>" placeholder="e.g. Sarah Tan" required/>
        <?php if (!empty($errors['name'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;"><?= h($errors['name']) ?></p><?php endif; ?>
      </div>
      <div class="form-field">
        <label for="reg-email">Email Address</label>
        <input type="email" id="reg-email" name="email" value="<?= old('email') ?>" placeholder="you@example.com" required/>
        <?php if (!empty($errors['email'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;"><?= h($errors['email']) ?></p><?php endif; ?>
      </div>
      <div class="form-field">
        <label>Your Cat's Name 🐱</label>
        <input type="text" name="cat_name" value="<?= old('cat_name') ?>" placeholder="e.g. Mochi"/>
      </div>
      <div class="form-field">
        <label>Password</label>
        <input type="password" name="password" placeholder="At least 8 characters" minlength="8" required/>
        <?php if (!empty($errors['password'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;"><?= h($errors['password']) ?></p><?php endif; ?>
      </div>
      <div class="form-field">
        <label>Confirm Password</label>
        <input type="password" name="confirm" placeholder="Repeat your password" required/>
        <?php if (!empty($errors['confirm'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;"><?= h($errors['confirm']) ?></p><?php endif; ?>
      </div>
      <button class="btn-join" type="submit">Join MeowClub – It's Free!</button>
      <p class="form-note">Already a member? <a href="<?= h(base_url('account/login.php')) ?>" style="color:var(--blush);">Log in →</a></p>
    </form>
  </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
