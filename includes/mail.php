<?php
/**
 * MeowMart Mail Helper — wraps PHPMailer (Integration: Open Source Project #2).
 * Usage:  send_mail('to@example.com', 'Subject', '<p>HTML body</p>', 'Plain text');
 * Falls back gracefully if PHPMailer is not available or mail is disabled.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

function _load_phpmailer(): bool
{
    // Try Composer autoload first
    $autoload = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($autoload)) {
        require_once $autoload;
        return true;
    }
    // Fall back to bundled files
    $shim = __DIR__ . '/../vendor/phpmailer/phpmailer/PHPMailer.php';
    if (file_exists($shim)) {
        require_once $shim;
        return true;
    }
    return false;
}

/**
 * Send an email via PHPMailer / SMTP.
 *
 * @param string $to        Recipient email address
 * @param string $name      Recipient display name
 * @param string $subject   Email subject
 * @param string $htmlBody  HTML email body
 * @param string $plainBody Plain-text fallback body
 * @return bool             True on success, false on failure
 */
function send_mail(string $to, string $name, string $subject, string $htmlBody, string $plainBody = ''): bool
{
    $cfg = config();

    if (empty($cfg['mail_enabled'])) {
        error_log("MeowMart mail: disabled in config. Would have sent '$subject' to $to");
        return true; // Return true so callers don't show an error
    }

    if (!_load_phpmailer()) {
        error_log('MeowMart mail: PHPMailer not found. Run composer install in the project root.');
        return false;
    }

    try {
        $mail = new PHPMailer(true);

        // SMTP settings (secure — avoids PHPMailer CVE-2016-10033)
        $mail->isSMTP();
        $mail->Host       = $cfg['mail_host']     ?? 'localhost';
        $mail->SMTPAuth   = true;
        $mail->Username   = $cfg['mail_username']  ?? '';
        $mail->Password   = $cfg['mail_password']  ?? '';
        $mail->Port       = (int)($cfg['mail_port'] ?? 587);

        $enc = strtolower($cfg['mail_encryption'] ?? 'tls');
        $mail->SMTPSecure = ($enc === 'ssl')
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;

        // Sender
        $mail->setFrom($cfg['mail_from'] ?? 'noreply@meowmart.com.sg', $cfg['mail_from_name'] ?? 'MeowMart');
        $mail->addReplyTo($cfg['mail_from'] ?? 'noreply@meowmart.com.sg', $cfg['mail_from_name'] ?? 'MeowMart');

        // Recipient
        $mail->addAddress($to, $name);

        // Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject  = $subject;
        $mail->Body     = _mail_wrap_html($subject, $htmlBody);
        $mail->AltBody  = $plainBody ?: strip_tags(str_replace(['<br>', '<br/>'], "\n", $htmlBody));

        $mail->send();
        return true;

    } catch (MailException $e) {
        error_log('MeowMart mail error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Wrap HTML body in a branded email template.
 */
function _mail_wrap_html(string $subject, string $body): string
{
    return '<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>' . htmlspecialchars($subject) . '</title>
<style>
  body{font-family:\'Helvetica Neue\',Arial,sans-serif;background:#FAF5EE;margin:0;padding:0}
  .wrap{max-width:600px;margin:40px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(61,35,20,.1)}
  .head{background:#3D2314;padding:28px 36px;text-align:center}
  .head h1{font-size:1.6rem;color:#fff;margin:0}
  .head span{color:#E8651A}
  .body{padding:36px;color:#3D2314;font-size:.95rem;line-height:1.7}
  .footer{background:#FAF5EE;padding:20px 36px;text-align:center;font-size:.78rem;color:#9a7c6a;border-top:1px solid #F2E8D9}
  .btn{display:inline-block;background:#E8651A;color:#fff;padding:12px 28px;border-radius:50px;text-decoration:none;font-weight:700;margin-top:16px}
</style>
</head>
<body>
  <div class="wrap">
    <div class="head"><h1>Meow<span>Mart</span></h1></div>
    <div class="body">' . $body . '</div>
    <div class="footer">© ' . date('Y') . ' MeowMart Pte. Ltd. · Singapore<br>
      Everything Your Cat Deserves 🐾
    </div>
  </div>
</body>
</html>';
}
