<?php
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/db.php';
$pdo = db();
$pdo = db();

$id   = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    set_flash('error', 'Post not found.');
    redirect('blog.php');
}
$pageTitle  = $post['title'];
$tagColors  = ['Nutrition'=>'🥗','Play'=>'🧶','Grooming'=>'✂️','Health'=>'💊','Lifestyle'=>'🏠','Training'=>'🎯'];
$icon       = $tagColors[$post['tag'] ?? ''] ?? '🐱';
?>

<div style="max-width:780px;margin:0 auto;padding:60px 5%;">
  <p style="font-size:.82rem;color:var(--brown-md);margin-bottom:32px;">
    <a href="<?= h(base_url('index.php')) ?>" style="color:var(--brown-md);text-decoration:none;">Home</a>
    &rsaquo;
    <a href="<?= h(base_url('content/blog.php')) ?>" style="color:var(--brown-md);text-decoration:none;">Blog</a>
    &rsaquo;
    <span style="color:var(--orange);"><?= h($post['title']) ?></span>
  </p>

  <div style="aspect-ratio:16/9;background:var(--warm);border-radius:24px;display:flex;align-items:center;justify-content:center;font-size:10rem;margin-bottom:40px;">
    <?= $icon ?>
  </div>

  <span class="blog-tag"><?= h($post['tag'] ?? 'General') ?></span>

  <h1 style="font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,2.8rem);font-weight:900;line-height:1.15;margin:16px 0 12px;color:var(--brown);">
    <?= h($post['title']) ?>
  </h1>

  <div class="blog-meta" style="margin-bottom:36px;">
    <div class="avatar">🧑</div>
    <?= h($post['author']) ?> &nbsp;·&nbsp; <?= date('d M Y', strtotime($post['created_at'])) ?>
  </div>

  <div style="font-size:1rem;color:var(--brown-md);line-height:1.85;white-space:pre-line;">
    <?= h($post['content']) ?>
  </div>

  <div style="margin-top:48px;padding-top:32px;border-top:1.5px solid var(--warm);">
    <a href="<?= h(base_url('content/blog.php')) ?>" class="btn-outline" style="text-decoration:none;display:inline-block;">← Back to Blog</a>
  </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
