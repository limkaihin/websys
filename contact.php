<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "inc/head.inc.php"; ?>
  <title>Contact – MeowMart</title>
</head>
<body>

<?php include "inc/nav.inc.php"; ?>

<?php
$success = false;
$errorMsg = "";
$name = $email = $subject = $message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = htmlspecialchars(trim($_POST["name"]    ?? ""));
    $email   = htmlspecialchars(trim($_POST["email"]   ?? ""));
    $subject = htmlspecialchars(trim($_POST["subject"] ?? ""));
    $message = htmlspecialchars(trim($_POST["message"] ?? ""));

    if (!$name)                                      $errorMsg .= "Name is required.<br>";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))  $errorMsg .= "Valid email is required.<br>";
    if (!$subject)                                   $errorMsg .= "Subject is required.<br>";
    if (strlen($message) < 10)                       $errorMsg .= "Message must be at least 10 characters.<br>";

    if (!$errorMsg) {
        // In production: send email via mail() or SMTP
        $success = true;
    }
}
?>

<section style="padding:80px 5%;min-height:70vh;">
  <div style="max-width:900px;margin:0 auto;display:grid;grid-template-columns:1fr 1.4fr;gap:60px;align-items:start;">

    <div>
      <div class="section-tag">💬 Get in Touch</div>
      <h1 class="section-title" style="margin-bottom:20px;">We'd Love to <em>Hear</em> from You</h1>
      <p style="color:var(--brown-md);line-height:1.7;margin-bottom:40px;">
        Whether you have a question about your order, a product query, or just want to share
        a photo of your cat — we're here to help.
      </p>
      <div style="display:flex;flex-direction:column;gap:20px;">
        <div class="perk">
          <div class="icon" style="background:var(--blush);">📍</div>
          <div class="text">
            <strong style="color:var(--brown);">Address</strong>
            <span style="color:var(--brown-md);">12 Whisker Lane, Singapore 238823</span>
          </div>
        </div>
        <div class="perk">
          <div class="icon" style="background:var(--blush);">✉️</div>
          <div class="text">
            <strong style="color:var(--brown);">Email</strong>
            <span style="color:var(--brown-md);">hello@meowmart.com.sg</span>
          </div>
        </div>
        <div class="perk">
          <div class="icon" style="background:var(--blush);">📞</div>
          <div class="text">
            <strong style="color:var(--brown);">Phone</strong>
            <span style="color:var(--brown-md);">+65 6789 1234</span>
          </div>
        </div>
      </div>
    </div>

    <div class="membership-right" style="background:var(--brown);">
      <?php if ($success): ?>
        <div style="text-align:center;padding:20px 0;">
          <div style="font-size:4rem;margin-bottom:16px;">🐾</div>
          <h3 style="color:var(--cream);margin-bottom:12px;">Message Sent!</h3>
          <p style="color:var(--blush);">We'll get back to you within 1–2 business days.</p>
        </div>
      <?php else: ?>
        <h3>Send Us a Message</h3>
        <?php if ($errorMsg): ?>
          <p style="background:rgba(239,68,68,.2);border-radius:10px;padding:10px 16px;
                    color:#fca5a5;font-size:.85rem;margin-bottom:16px;">
            <?= $errorMsg ?>
          </p>
        <?php endif; ?>
        <form method="POST">
          <div class="form-field">
            <label>Your Name</label>
            <input type="text" name="name" value="<?= $name ?>" placeholder="Sarah Tan" required/>
          </div>
          <div class="form-field">
            <label>Email Address</label>
            <input type="email" name="email" value="<?= $email ?>" placeholder="you@example.com" required/>
          </div>
          <div class="form-field">
            <label>Subject</label>
            <input type="text" name="subject" value="<?= $subject ?>" placeholder="How can we help?" required/>
          </div>
          <div class="form-field">
            <label>Message</label>
            <textarea name="message" rows="5" placeholder="Tell us everything..."
                      style="width:100%;background:rgba(255,255,255,.08);border:1.5px solid rgba(255,255,255,.18);
                             border-radius:12px;padding:12px 16px;color:var(--cream);
                             font-family:'DM Sans',sans-serif;font-size:.92rem;resize:vertical;outline:none;"><?= $message ?></textarea>
          </div>
          <button class="btn-join" type="submit">Send Message 🐾</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include "inc/footer.inc.php"; ?>
</body>
</html>
