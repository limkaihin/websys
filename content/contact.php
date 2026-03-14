<?php
$pageTitle = 'Contact Us';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/db.php';

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name    = post('name');
    $email   = post('email');
    $subject = post('subject');
    $message = post('message');

    if (strlen($name) < 2)                          $errors['name']    = 'Please enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email']   = 'Please enter a valid email.';
    if (strlen($subject) < 2)                       $errors['subject'] = 'Please enter a subject.';
    if (strlen($message) < 10)                      $errors['message'] = 'Message must be at least 10 characters.';

    if (empty($errors)) {
        try {
            $pdo = db();
            $pdo->prepare(
                'INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)'
            )->execute([$name, $email, $subject, $message]);
        } catch (Throwable $e) {
            // DB might not have table yet — silently ignore so contact still works
        }
        $success = true;
        store_old([]);
    } else {
        store_old(compact('name', 'email', 'subject', 'message'));
    }
}
?>

<section style="padding:80px 5%;min-height:70vh;">
  <div style="max-width:920px;margin:0 auto;display:grid;grid-template-columns:1fr 1.4fr;gap:60px;align-items:start;">

    <!-- Left info panel -->
    <div>
      <div class="section-tag">💬 Get in Touch</div>
      <h1 class="section-title" style="margin-bottom:20px;">We'd Love to <em>Hear</em> from You</h1>
      <p style="color:var(--brown-md);line-height:1.7;margin-bottom:40px;">
        Whether you have a question about your order, a product query, or just want to share a photo of your cat — our team is here to help.
      </p>
      <div style="display:flex;flex-direction:column;gap:20px;">
        <?php
        $infos = [
          ['📍','Address',  '12 Whisker Lane, Singapore 238823'],
          ['✉️','Email',    'hello@meowmart.com.sg'],
          ['📞','Phone',    '+65 6789 1234'],
          ['🕐','Hours',    'Mon–Fri 9am–6pm SGT'],
        ];
        foreach ($infos as [$ico, $lbl, $val]):
        ?>
        <div class="perk" style="color:var(--brown);">
          <div class="icon" style="background:var(--blush);" aria-hidden="true"><?= $ico ?></div>
          <div class="text">
            <strong style="color:var(--brown);"><?= $lbl ?></strong>
            <span style="color:var(--brown-md);"><?= h($val) ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Contact form -->
    <div class="membership-right" style="background:var(--brown);">
      <?php if ($success): ?>
        <div style="text-align:center;padding:24px 0;" role="status" aria-live="polite">
          <div style="font-size:4rem;margin-bottom:16px;">🐾</div>
          <h3 style="color:var(--cream);margin-bottom:12px;">Message Sent!</h3>
          <p style="color:var(--blush);font-size:.95rem;">We'll get back to you within 1–2 business days.</p>
          <a href="<?= h(base_url('content/contact.php')) ?>" style="display:inline-block;margin-top:20px;color:var(--orange-lt);font-size:.88rem;">Send another message →</a>
        </div>
      <?php else: ?>
        <h3>Send Us a Message</h3>
        <form method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
          <div class="form-field">
            <label for="ct-name">Your Name</label>
            <input id="ct-name" type="text" name="name" value="<?= old('name') ?>" placeholder="Sarah Tan" required autocomplete="name"
                   style="background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.18);color:var(--cream);"/>
            <?php if (!empty($errors['name'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;" role="alert"><?= h($errors['name']) ?></p><?php endif; ?>
          </div>
          <div class="form-field">
            <label for="ct-email">Email Address</label>
            <input id="ct-email" type="email" name="email" value="<?= old('email') ?>" placeholder="you@example.com" required autocomplete="email"
                   style="background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.18);color:var(--cream);"/>
            <?php if (!empty($errors['email'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;" role="alert"><?= h($errors['email']) ?></p><?php endif; ?>
          </div>
          <div class="form-field">
            <label for="ct-subject">Subject</label>
            <input id="ct-subject" type="text" name="subject" value="<?= old('subject') ?>" placeholder="How can we help?" required
                   style="background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.18);color:var(--cream);"/>
            <?php if (!empty($errors['subject'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;" role="alert"><?= h($errors['subject']) ?></p><?php endif; ?>
          </div>
          <div class="form-field">
            <label for="ct-message">Message</label>
            <textarea id="ct-message" name="message" rows="5" placeholder="Tell us everything..."
                      style="width:100%;background:rgba(255,255,255,.08);border:1.5px solid rgba(255,255,255,.18);border-radius:12px;padding:12px 16px;color:var(--cream);font-family:'DM Sans',sans-serif;font-size:.92rem;resize:vertical;outline:none;"
                      required><?= old('message') ?></textarea>
            <?php if (!empty($errors['message'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;" role="alert"><?= h($errors['message']) ?></p><?php endif; ?>
          </div>
          <button class="btn-join" type="submit">Send Message 🐾</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Mobile: stack columns -->
<style>
@media (max-width: 700px) {
  section > div[style*="grid-template-columns:1fr 1.4fr"] {
    grid-template-columns: 1fr !important;
    gap: 32px !important;
  }
}
</style>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
