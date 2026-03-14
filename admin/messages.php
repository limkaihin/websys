<?php
$pageTitle = 'Contact Messages';
require_once dirname(__DIR__) . '/includes/functions.php';
require_admin();
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/db.php';

$pdo = db();

// Mark as read
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $mid = (int)post('message_id');
    if ($mid > 0) {
        $pdo->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = ?')->execute([$mid]);
    }
    redirect('admin/messages.php');
}

$messages  = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
$unread    = array_filter($messages, fn($m) => !$m['is_read']);
?>

<?php include __DIR__ . '/sidebar.php'; ?>

<div style="margin-left:240px;padding:40px 48px;min-height:100vh;background:var(--cream);">
  <div style="margin-bottom:32px;">
    <div class="section-tag">💬 Admin</div>
    <h1 class="section-title" style="font-size:2rem;">Contact <em>Messages</em></h1>
  </div>

  <?php if (count($unread) > 0): ?>
    <div style="background:var(--orange);color:#fff;border-radius:14px;padding:12px 20px;margin-bottom:24px;font-weight:600;font-size:.9rem;">
      📬 <?= count($unread) ?> unread message<?= count($unread)!=1?'s':'' ?>
    </div>
  <?php endif; ?>

  <?php if (empty($messages)): ?>
    <div style="background:var(--white);border-radius:20px;padding:60px;text-align:center;color:var(--brown-md);">
      <div style="font-size:4rem;margin-bottom:16px;">📭</div>
      No messages yet.
    </div>
  <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:16px;">
      <?php foreach ($messages as $msg): ?>
        <div style="background:var(--white);border-radius:20px;padding:24px 28px;box-shadow:0 2px 12px rgba(61,35,20,.06);
                    border-left:4px solid <?= $msg['is_read'] ? 'var(--warm)' : 'var(--orange)' ?>;">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;margin-bottom:14px;">
            <div>
              <div style="font-weight:700;font-size:.95rem;"><?= h($msg['name']) ?>
                <?php if (!$msg['is_read']): ?><span style="background:var(--orange);color:#fff;border-radius:10px;padding:2px 8px;font-size:.65rem;font-weight:700;margin-left:8px;">NEW</span><?php endif; ?>
              </div>
              <div style="font-size:.82rem;color:var(--brown-md);"><?= h($msg['email']) ?> · <?= date('d M Y, g:i A', strtotime($msg['created_at'])) ?></div>
            </div>
            <?php if (!$msg['is_read']): ?>
            <form method="POST">
              <input type="hidden" name="csrf_token"   value="<?= h(csrf_token()) ?>">
              <input type="hidden" name="message_id"  value="<?= (int)$msg['id'] ?>">
              <button type="submit" style="background:var(--warm);border:none;border-radius:12px;padding:7px 16px;font-size:.78rem;font-weight:600;cursor:pointer;color:var(--brown);">Mark Read</button>
            </form>
            <?php endif; ?>
          </div>
          <div style="font-weight:600;color:var(--orange);margin-bottom:8px;font-size:.88rem;">Re: <?= h($msg['subject']) ?></div>
          <p style="font-size:.9rem;color:var(--brown-md);line-height:1.65;"><?= nl2br(h($msg['message'])) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
