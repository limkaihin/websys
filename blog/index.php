<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../inc/head.inc.php"; ?>
  <title>Blog – MeowMart</title>
</head>
<body>
<?php include "../inc/nav.inc.php"; ?>
<?php
include "../inc/db.inc.php";
$tag   = isset($_GET['tag']) ? trim($_GET['tag']) : '';
$posts = getBlogPosts($tag);
$allTags = ['Nutrition','Play','Grooming','Lifestyle'];
$tagIcons = ['Nutrition'=>'🥗','Play'=>'🧶','Grooming'=>'✂️','Lifestyle'=>'🏠'];
?>

<div style="background:var(--warm);padding:80px 5% 60px;text-align:center;">
  <div class="hero-eyebrow" style="margin:0 auto 20px;">📖 The MeowMart Blog</div>
  <h1 class="section-title" style="font-size:clamp(2.2rem,4vw,3.5rem);margin-bottom:16px;">Tips, Stories & <em>Cat Wisdom</em></h1>
  <p style="color:var(--brown-md);font-size:1rem;max-width:500px;margin:0 auto;">Expert advice, cat care guides and stories from Singapore's most devoted cat lovers.</p>
</div>

<div style="padding:32px 5% 0;display:flex;gap:10px;flex-wrap:wrap;max-width:1200px;margin:0 auto;">
  <a class="pill <?= !$tag?'active':'' ?>" href="/blog/index.php" style="text-decoration:none;">All Posts</a>
  <?php foreach ($allTags as $t): ?>
    <a class="pill <?= strtolower($tag)===strtolower($t)?'active':'' ?>"
       href="/blog/index.php?tag=<?= urlencode($t) ?>" style="text-decoration:none;">
      <?= ($tagIcons[$t]??'').' '.$t ?>
    </a>
  <?php endforeach; ?>
</div>

<section class="blog" style="padding:32px 5% 80px;max-width:1200px;margin:0 auto;">
  <div class="blog-grid">
    <?php foreach ($posts as $i=>$post): ?>
    <div class="blog-card <?= $i===0?'featured':'' ?>">
      <div class="blog-thumb"><?= $post['icon'] ?></div>
      <div class="blog-body">
        <span class="blog-tag"><?= htmlspecialchars($post['tag']) ?></span>
        <h3><a href="/blog/post.php?id=<?= $post['id'] ?>" style="color:inherit;text-decoration:none;transition:color .2s;"
               onmouseover="this.style.color='var(--orange)'" onmouseout="this.style.color='inherit'">
          <?= htmlspecialchars($post['title']) ?>
        </a></h3>
        <p><?= htmlspecialchars($post['excerpt']) ?></p>
        <div class="blog-meta">
          <div class="avatar">🧑</div>
          <?= htmlspecialchars($post['author']) ?> &nbsp;·&nbsp; 5 min read
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($posts)): ?>
      <p style="color:var(--brown-md);padding:40px 0;">No posts found. <a href="/blog/index.php" style="color:var(--orange);">View all posts →</a></p>
    <?php endif; ?>
  </div>
</section>

<?php include "../inc/footer.inc.php"; ?>
</body>
</html>
