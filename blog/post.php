<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../inc/head.inc.php"; ?>
</head>
<body>
<?php include "../inc/nav.inc.php"; ?>
<?php
include "../inc/db.inc.php";
$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = $id ? getBlogPost($id) : null;
if (!$post) { header("Location: /blog/index.php"); exit; }
$allPosts = getBlogPosts();
$others   = array_filter($allPosts, fn($p) => $p['id'] !== $id);
?>
<title><?= htmlspecialchars($post['title']) ?> – MeowMart Blog</title>

<div style="background:var(--warm);padding:14px 5%;border-bottom:1.5px solid var(--cream);">
  <p style="font-size:.82rem;color:var(--brown-md);max-width:900px;margin:0 auto;">
    <a href="/index.php" style="color:var(--brown-md);text-decoration:none;">Home</a> ›
    <a href="/blog/index.php" style="color:var(--brown-md);text-decoration:none;">Blog</a> ›
    <span style="color:var(--orange);font-weight:600;"><?= htmlspecialchars($post['title']) ?></span>
  </p>
</div>

<article style="max-width:800px;margin:0 auto;padding:60px 5% 80px;">
  <span style="display:inline-block;background:var(--blush);color:var(--orange);border:1.5px solid var(--orange);border-radius:30px;padding:5px 14px;font-size:.78rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;margin-bottom:20px;"><?= htmlspecialchars($post['tag']) ?></span>
  <h1 style="font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,2.8rem);font-weight:900;line-height:1.15;color:var(--brown);margin-bottom:18px;"><?= htmlspecialchars($post['title']) ?></h1>
  <div style="display:flex;align-items:center;gap:12px;margin-bottom:36px;padding-bottom:28px;border-bottom:1.5px solid var(--warm);">
    <div style="width:38px;height:38px;background:var(--warm);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">🧑</div>
    <div>
      <p style="font-weight:600;font-size:.88rem;color:var(--brown);margin-bottom:2px;"><?= htmlspecialchars($post['author']) ?></p>
      <p style="font-size:.78rem;color:var(--brown-md);"><?= date('F j, Y', strtotime($post['created_at'])) ?> · 5 min read</p>
    </div>
  </div>
  <div style="background:var(--warm);border-radius:20px;padding:32px;margin-bottom:32px;font-size:1.3rem;text-align:center;"><?= $post['icon'] ?></div>
  <div style="font-size:.98rem;color:var(--brown-md);line-height:1.85;">
    <?php foreach (explode("\n\n", $post['content']) as $para): ?>
      <p style="margin-bottom:18px;"><?= nl2br(htmlspecialchars($para)) ?></p>
    <?php endforeach; ?>
  </div>
  <div style="margin-top:40px;padding-top:32px;border-top:1.5px solid var(--warm);display:flex;gap:12px;flex-wrap:wrap;">
    <a href="/blog/index.php" class="btn-outline" style="text-decoration:none;padding:11px 22px;display:inline-flex;align-items:center;">← All Posts</a>
    <a href="/blog/index.php?tag=<?= urlencode($post['tag']) ?>" class="btn-primary" style="text-decoration:none;display:inline-block;padding:12px 22px;">More <?= htmlspecialchars($post['tag']) ?> →</a>
  </div>
</article>

<?php if (!empty($others)): ?>
<section style="background:var(--warm);padding:48px 5%;">
  <div style="max-width:1200px;margin:0 auto;">
    <h2 style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:900;color:var(--brown);margin-bottom:24px;">More from the Blog</h2>
    <div class="blog-grid">
      <?php foreach (array_slice(array_values($others),0,3) as $op): ?>
      <div class="blog-card">
        <div class="blog-thumb"><?= $op['icon'] ?></div>
        <div class="blog-body">
          <span class="blog-tag"><?= htmlspecialchars($op['tag']) ?></span>
          <h3><a href="/blog/post.php?id=<?= $op['id'] ?>" style="color:inherit;text-decoration:none;" onmouseover="this.style.color='var(--orange)'" onmouseout="this.style.color='inherit'"><?= htmlspecialchars($op['title']) ?></a></h3>
          <p><?= htmlspecialchars($op['excerpt']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include "../inc/footer.inc.php"; ?>
</body>
</html>
