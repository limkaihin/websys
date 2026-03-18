<?php
/**
 * Contact Page
 * Integrates PHPMailer (Open Source Project #2) to send contact enquiries via SMTP.
 */
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/mail.php';

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
        // Save to DB
        try {
            $pdo = db();
            $pdo->prepare(
                'INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)'
            )->execute([$name, $email, $subject, $message]);
        } catch (Throwable $e) {
            // Table might not exist yet — silently continue
        }

        // Send confirmation to enquirer via PHPMailer (Open Source Project #2)
        $html = "
            <p>Hi <strong>" . h($name) . "</strong>,</p>
            <p>Thanks for reaching out to MeowMart! We've received your message and will get back to you within 1–2 business days.</p>
            <hr style='border:none;border-top:1px solid #F2E8D9;margin:20px 0'>
            <p><strong>Your message:</strong><br>" . nl2br(h($message)) . "</p>
            <p>In the meantime, feel free to browse our shop:</p>
            <a class='btn' href='" . base_url('shop/products.php') . "'>Shop Now →</a>
            <p style='margin-top:24px'>Warm regards,<br><strong>The MeowMart Team</strong> 🐾</p>
        ";
        send_mail($email, $name, 'We received your message — MeowMart', $html);

        // Also notify admin
        $cfg = config();
        $adminHtml = "
            <p>New contact form submission:</p>
            <ul>
                <li><strong>From:</strong> " . h($name) . " &lt;" . h($email) . "&gt;</li>
                <li><strong>Subject:</strong> " . h($subject) . "</li>
            </ul>
            <p><strong>Message:</strong><br>" . nl2br(h($message)) . "</p>
        ";
        send_mail($cfg['mail_username'] ?? $cfg['mail_from'] ?? '', 'Admin', 'New Contact: ' . $subject, $adminHtml);

        $success = true;
        store_old([]);
    } else {
        store_old(compact('name', 'email', 'subject', 'message'));
    }
}

// ── Output starts here ────────────────────────────────────────────────────────
$pageTitle = 'Contact Us';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<section style="padding:80px 5%;min-height:70vh;">
  <div style="max-width:920px;margin:0 auto;display:grid;grid-template-columns:1fr 1.4fr;gap:60px;align-items:start;">

    <div>
      <div class="section-tag"><i class="fa-solid fa-comments"></i> Get in Touch</div>
      <h1 class="section-title" style="margin-bottom:20px;">We'd Love to <em>Hear</em> from You</h1>
      <p style="color:var(--brown-md);line-height:1.7;margin-bottom:40px;">
        Whether you have a question about your order, a product query, or just want to share a photo of your cat — our team is here to help.
      </p>
      <div style="display:flex;flex-direction:column;gap:20px;">
        <?php
        $infos = [
          ['fa-location-dot','Address','12 Whisker Lane, Singapore 238823'],
          ['fa-envelope',    'Email',  'hello@meowmart.com.sg'],
          ['fa-phone',       'Phone',  '+65 6789 1234'],
          ['fa-clock',       'Hours',  'Mon–Fri 9am–6pm SGT'],
        ];
        foreach ($infos as [$icon, $lbl, $val]):
        ?>
        <div class="perk" style="color:var(--brown);">
          <div class="icon" style="background:var(--blush);"><i class="fa-solid <?= $icon ?>"></i></div>
          <div class="text">
            <strong style="color:var(--brown);"><?= $lbl ?></strong>
            <span style="color:var(--brown-md);"><?= h($val) ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="membership-right" style="background:var(--brown);">
      <?php if ($success): ?>
        <div style="text-align:center;padding:24px 0;" role="status" aria-live="polite">
          <div style="font-size:3rem;margin-bottom:16px;"><i class="fa-solid fa-circle-check" style="color:var(--orange-lt);"></i></div>
          <h3 style="color:var(--cream);margin-bottom:12px;">Message Sent!</h3>
          <p style="color:var(--blush);font-size:.95rem;">We'll get back to you within 1–2 business days.<br>Check your inbox for a confirmation email.</p>
          <a href="<?= h(base_url('content/contact.php')) ?>" style="display:inline-block;margin-top:20px;color:var(--orange-lt);font-size:.88rem;">Send another message →</a>
        </div>
      <?php else: ?>
        <h3>Send Us a Message</h3>
        <form method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
          <div class="form-field">
            <label for="ct-name"><i class="fa-solid fa-user fa-xs"></i> Your Name</label>
            <input id="ct-name" type="text" name="name" value="<?= old('name') ?>" placeholder="Sarah Tan" required autocomplete="name"
                   style="background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.18);color:var(--cream);"/>
            <?php if (!empty($errors['name'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;" role="alert"><?= h($errors['name']) ?></p><?php endif; ?>
          </div>
          <div class="form-field">
            <label for="ct-email"><i class="fa-solid fa-envelope fa-xs"></i> Email Address</label>
            <input id="ct-email" type="email" name="email" value="<?= old('email') ?>" placeholder="you@example.com" required autocomplete="email"
                   style="background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.18);color:var(--cream);"/>
            <?php if (!empty($errors['email'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;" role="alert"><?= h($errors['email']) ?></p><?php endif; ?>
          </div>
          <div class="form-field">
            <label for="ct-subject"><i class="fa-solid fa-tag fa-xs"></i> Subject</label>
            <input id="ct-subject" type="text" name="subject" value="<?= old('subject') ?>" placeholder="How can we help?" required
                   style="background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.18);color:var(--cream);"/>
            <?php if (!empty($errors['subject'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;" role="alert"><?= h($errors['subject']) ?></p><?php endif; ?>
          </div>
          <div class="form-field">
            <label for="ct-message"><i class="fa-solid fa-message fa-xs"></i> Message</label>
            <textarea id="ct-message" name="message" rows="5" placeholder="Tell us everything..."
                      style="width:100%;background:rgba(255,255,255,.08);border:1.5px solid rgba(255,255,255,.18);border-radius:12px;
                             padding:12px 16px;color:var(--cream);font-family:'DM Sans',sans-serif;font-size:.92rem;resize:vertical;outline:none;"
                      required><?= old('message') ?></textarea>
            <?php if (!empty($errors['message'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;" role="alert"><?= h($errors['message']) ?></p><?php endif; ?>
          </div>
          <button class="btn-join" type="submit"><i class="fa-solid fa-paper-plane"></i> Send Message</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</section>

<style>
@media (max-width:700px) {
  section > div[style*="grid-template-columns:1fr 1.4fr"] { grid-template-columns:1fr !important; gap:32px !important; }
}
</style>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
