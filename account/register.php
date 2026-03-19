<?php
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/mail.php';

if (is_logged_in()) redirect('index.php');

$prefillRef = sanitize_referral_code((string)($_GET['ref'] ?? ''));

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name          = post('name');
    $email         = post('email');
    $cat_name      = post('cat_name');
    $password      = post('password');
    $confirm       = post('confirm');
    $referral_code = sanitize_referral_code(post('referral_code'));

    if (strlen($name) < 2)                          $errors['name']          = 'Name must be at least 2 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email']         = 'Please enter a valid email.';
    if (strlen($password) < 8)                      $errors['password']      = 'Password must be at least 8 characters.';
    if ($password !== $confirm)                     $errors['confirm']       = 'Passwords do not match.';
    if ($referral_code !== '' && strlen($referral_code) < 4) $errors['referral_code'] = 'Please enter a valid referral code.';

    if (empty($errors)) {
        $pdo    = db();
        $exists = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $exists->execute([$email]);
        if ($exists->fetch()) {
            $errors['email'] = 'An account with this email already exists.';
        } else {
            $columns = ['name', 'email', 'cat_name', 'password', 'role'];
            $values  = [$name, $email, $cat_name, password_hash($password, PASSWORD_BCRYPT), 'member'];

            if (db_has_column('users', 'referred_by')) {
                $columns[] = 'referred_by';
                $values[]  = $referral_code !== '' ? $referral_code : null;
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

<div class="membership auth-shell" id="membership">
  <div class="membership-left">
    <h2>Join the <em>MeowClub</em> &amp; Save Every Day</h2>
    <p>Free membership with exclusive perks, early sale access, birthday treats for your cat, and more.</p>
    <div class="membership-perks">
      <?php foreach ([
        ['fa-gift',        'Earn Pawpoints',   'Redeem on every purchase'],
        ['fa-truck',       'Free Delivery',    'On orders above $60'],
        ['fa-cake-candles','Birthday Surprise','A free gift for your cat yearly'],
        ['fa-bolt',        'Early Access',     'Shop new arrivals first'],
      ] as [$icon, $title, $sub]): ?>
      <div class="perk">
        <div class="icon"><i class="fa-solid <?= $icon ?>"></i></div>
        <div class="text"><strong><?= $title ?></strong><span><?= $sub ?></span></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="membership-right">
    <h3>Create Your Free Account</h3>
    <form method="POST" novalidate>
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <div class="form-field">
        <label for="rg-name"><i class="fa-solid fa-user fa-xs"></i> Your Name</label>
        <input id="rg-name" type="text" name="name" value="<?= old('name') ?>"
               placeholder="e.g. Sarah Tan" required autocomplete="name"/>
        <?php if (!empty($errors['name'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;"><?= h($errors['name']) ?></p><?php endif; ?>
      </div>
      <div class="form-field">
        <label for="rg-email"><i class="fa-solid fa-envelope fa-xs"></i> Email Address</label>
        <input id="rg-email" type="email" name="email" value="<?= old('email') ?>"
               placeholder="you@example.com" required autocomplete="email"/>
        <?php if (!empty($errors['email'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;"><?= h($errors['email']) ?></p><?php endif; ?>
      </div>
      <div class="form-field">
        <label for="rg-cat"><i class="fa-solid fa-cat fa-xs"></i> Your Cat's Name</label>
        <input id="rg-cat" type="text" name="cat_name" value="<?= old('cat_name') ?>" placeholder="e.g. Mochi"/>
      </div>
      <div class="form-field">
        <label for="rg-ref"><i class="fa-solid fa-user-group fa-xs"></i> Referral Code <span style="font-weight:400;color:var(--brown-md);">(optional)</span></label>
        <input id="rg-ref" type="text" name="referral_code" value="<?= old('referral_code', $prefillRef) ?>"
               placeholder="Enter your friend's code" autocomplete="off" style="text-transform:uppercase;"/>
        <p style="font-size:.78rem;color:var(--brown-md);margin-top:6px;">Joining from a friend's link will prefill this automatically.</p>
        <?php if (!empty($errors['referral_code'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;"><?= h($errors['referral_code']) ?></p><?php endif; ?>
      </div>
      <div class="form-field">
        <label for="rg-pw"><i class="fa-solid fa-lock fa-xs"></i> Password</label>
        <input id="rg-pw" type="password" name="password"
               placeholder="At least 8 characters" required minlength="8" autocomplete="new-password"/>
        <?php if (!empty($errors['password'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;"><?= h($errors['password']) ?></p><?php endif; ?>
      </div>
      <div class="form-field">
        <label for="rg-cpw"><i class="fa-solid fa-lock fa-xs"></i> Confirm Password</label>
        <input id="rg-cpw" type="password" name="confirm"
               placeholder="Repeat your password" required autocomplete="new-password"/>
        <?php if (!empty($errors['confirm'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;"><?= h($errors['confirm']) ?></p><?php endif; ?>
      </div>
      <button class="btn-join" type="submit">
        <i class="fa-solid fa-user-plus"></i> Join MeowClub – It's Free!
      </button>
      <p class="form-note">Already a member?
        <a href="<?= h(base_url('account/login.php')) ?>" style="color:var(--blush);">Log in →</a>
      </p>
    </form>
  </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
