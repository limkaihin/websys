<?php
$pageTitle = 'Contact Us';
require_once dirname(__DIR__) . '/includes/header.php';

$errors  = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name    = post('name');
    $email   = post('email');
    $subject = post('subject');
    $message = post('message');

    if (strlen($name) < 2)                             $errors['name']    = 'Please enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))    $errors['email']   = 'Please enter a valid email.';
    if (!$subject)                                     $errors['subject'] = 'Please enter a subject.';
    if (strlen($message) < 10)                         $errors['message'] = 'Message must be at least 10 characters.';

    if (empty($errors)) {
        // In production: send email via mail() or SMTP
        $success = true;
        store_old([]);
    } else {
        store_old(compact('name','email','subject','message'));
    }
}
?>

<section style="padding:80px 5%;min-height:70vh;">
  <div style="max-width:900px;margin:0 auto;display:grid;grid-template-columns:1fr 1.4fr;gap:60px;align-items:start;">

    <!-- Left info -->
    <div>
      <div class="section-tag">💬 Get in Touch</div>
      <h1 class="section-title" style="margin-bottom:20px;">We'd Love to <em>Hear</em> from You</h1>
      <p style="color:var(--brown-md);line-height:1.7;margin-bottom:40px;">
        Whether you have a question about your order, a product query, or just want to share a photo of your cat — our team is here to help.
      </p>
      <div style="display:flex;flex-direction:column;gap:20px;">
        <div class="perk" style="color:var(--brown);">
          <div class="icon" style="background:var(--blush);">📍</div>
          <div class="text"><strong style="color:var(--brown);">Address</strong><span style="color:var(--brown-md);">12 Whisker Lane, Singapore 238823</span></div>
        </div>
        <div class="perk" style="color:var(--brown);">
          <div class="icon" style="background:var(--blush);">✉️</div>
          <div class="text"><strong style="color:var(--brown);">Email</strong><span style="color:var(--brown-md);">hello@meowmart.com.sg</span></div>
        </div>
        <div class="perk" style="color:var(--brown);">
          <div class="icon" style="background:var(--blush);">📞</div>
          <div class="text"><strong style="color:var(--brown);">Phone</strong><span style="color:var(--brown-md);">+65 6789 1234</span></div>
        </div>
        <div class="perk" style="color:var(--brown);">
          <div class="icon" style="background:var(--blush);">🕐</div>
          <div class="text"><strong style="color:var(--brown);">Hours</strong><span style="color:var(--brown-md);">Mon–Fri 9am–6pm SGT</span></div>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="membership-right" style="background:var(--brown);">
      <?php if ($success): ?>
        <div style="text-align:center;padding:20px 0;">
          <div style="font-size:4rem;margin-bottom:16px;">🐾</div>
          <h3 style="color:var(--cream);margin-bottom:12px;">Message Sent!</h3>
          <p style="color:var(--blush);font-size:.95rem;">We'll get back to you within 1–2 business days.</p>
        </div>
      <?php else: ?>
        <h3>Send Us a Message</h3>
        <form method="POST" id="contactForm">
          <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
          <div class="form-field">
            <label>Your Name</label>
            <input type="text" name="name" value="<?= old('name') ?>" placeholder="Sarah Tan" required/>
            <?php if (!empty($errors['name'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;"><?= h($errors['name']) ?></p><?php endif; ?>
          </div>
          <div class="form-field">
            <label>Email Address</label>
            <input type="email" name="email" value="<?= old('email') ?>" placeholder="you@example.com" required/>
            <?php if (!empty($errors['email'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;"><?= h($errors['email']) ?></p><?php endif; ?>
          </div>
          <div class="form-field">
            <label>Subject</label>
            <input type="text" name="subject" value="<?= old('subject') ?>" placeholder="How can we help?" required/>
            <?php if (!empty($errors['subject'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;"><?= h($errors['subject']) ?></p><?php endif; ?>
          </div>
          <div class="form-field">
            <label>Message</label>
            <textarea name="message" rows="5" placeholder="Tell us everything..."
                      style="width:100%;background:rgba(255,255,255,.08);border:1.5px solid rgba(255,255,255,.18);border-radius:12px;padding:12px 16px;color:var(--cream);font-family:'DM Sans',sans-serif;font-size:.92rem;resize:vertical;outline:none;"><?= old('message') ?></textarea>
            <?php if (!empty($errors['message'])): ?><p style="color:#f87171;font-size:.78rem;margin-top:4px;"><?= h($errors['message']) ?></p><?php endif; ?>
          </div>
          <button class="btn-join" type="submit">Send Message 🐾</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
