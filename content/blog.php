<?php
$pageTitle = 'The MeowMart Blog';
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/db.php';
$pdo = db();
$posts = $pdo->query('SELECT * FROM blog_posts ORDER BY created_at DESC')->fetchAll();
$tagColors = ['Nutrition'=>'🥗','Play'=>'🧶','Grooming'=>'✂️','Health'=>'💊','Lifestyle'=>'🏠','Training'=>'🎯'];
?>

<section class="blog">
  <div class="section-header">
    <div class="section-tag">📖 The MeowMart Blog</div>
    <h1 class="section-title">Tips, Stories & <em>Cat Wisdom</em></h1>
  </div>

  <?php if (empty($posts)): ?>
    <p style="text-align:center;padding:60px;color:var(--brown-md);">No posts yet — check back soon!</p>
  <?php else: ?>
  <div class="blog-grid">
    <?php foreach ($posts as $i => $post):
      $icon = $tagColors[$post['tag'] ?? ''] ?? '🐱';
    ?>
      <article class="blog-card <?= $i === 0 ? 'featured' : '' ?>"><a href="<?= h(base_url('content/blog_post.php?id=' . $post['id'])) ?>" style="text-decoration:none;color:inherit;display:block;">
        <div class="blog-thumb"><?= $icon ?></div>
        <div class="blog-body">
          <span class="blog-tag"><?= h($post['tag'] ?? 'General') ?></span>
          <h3><?= h($post['title']) ?></h3>
          <p><?= h($post['excerpt']) ?></p>
          <div class="blog-meta">
            <div class="avatar">🧑</div>
            <?= h($post['author']) ?> &nbsp;·&nbsp;
            <?= date('d M Y', strtotime($post['created_at'])) ?>
            <a href="<?= h(base_url('content/blog_post.php?id=' . $post['id'])) ?>"
               style="margin-left:auto;color:var(--orange);text-decoration:none;font-size:.78rem;font-weight:600;">
              Read more →
            </a>
          </div>
        </div>
      </a></article>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
