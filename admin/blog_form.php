<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$pageTitle = 'Blog Post Form';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
$pdo = db();
$pdo = db();

$id   = (int)($_GET['id'] ?? 0);
$post = null;
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE id=?');
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    if (!$post) { set_flash('error','Post not found.'); redirect('admin/blog_posts.php'); }
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $title   = post('title');
    $tag     = post('tag');
    $author  = post('author');
    $excerpt = post('excerpt');
    $content = post('content');

    if (!$title)   $errors['title']   = 'Title is required.';
    if (!$author)  $errors['author']  = 'Author is required.';
    if (!$content) $errors['content'] = 'Content is required.';

    if (empty($errors)) {
        if ($id) {
            $pdo->prepare('UPDATE blog_posts SET title=?,tag=?,author=?,excerpt=?,content=? WHERE id=?')
                ->execute([$title,$tag,$author,$excerpt,$content,$id]);
        } else {
            $pdo->prepare('INSERT INTO blog_posts (title,tag,author,excerpt,content) VALUES (?,?,?,?,?)')
                ->execute([$title,$tag,$author,$excerpt,$content]);
        }
        set_flash('success','Post saved!');
        redirect('admin/blog_posts.php');
    }
    store_old(compact('title','tag','author','excerpt','content'));
}
$tags = ['Nutrition','Play','Grooming','Health','Lifestyle','Training'];
?>

<div style="display:flex;min-height:80vh;">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <section style="flex:1;padding:60px 48px;">
    <h1 class="section-title" style="margin-bottom:32px;"><?= $id ? 'Edit' : 'New' ?> <em>Blog Post</em></h1>

    <div class="membership-right" style="max-width:640px;background:var(--white);border:1.5px solid var(--warm);">
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <div class="form-field">
          <label style="color:var(--brown-md);">Title</label>
          <input type="text" name="title" value="<?= old('title', $post['title'] ?? '') ?>" required
                 style="background:var(--warm);border-color:var(--warm);color:var(--brown);"/>
          <?php if (!empty($errors['title'])): ?><p style="color:#b91c1c;font-size:.78rem;margin-top:4px;"><?= h($errors['title']) ?></p><?php endif; ?>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
          <div class="form-field">
            <label style="color:var(--brown-md);">Tag / Category</label>
            <select name="tag" style="width:100%;background:var(--warm);border:1.5px solid var(--warm);border-radius:12px;padding:12px 16px;color:var(--brown);font-family:'DM Sans',sans-serif;font-size:.92rem;">
              <?php foreach ($tags as $t): ?>
                <option value="<?= h($t) ?>" <?= (old('tag',$post['tag']??'')===$t)?'selected':'' ?>><?= h($t) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-field">
            <label style="color:var(--brown-md);">Author</label>
            <input type="text" name="author" value="<?= old('author', $post['author'] ?? '') ?>" required
                   style="background:var(--warm);border-color:var(--warm);color:var(--brown);"/>
            <?php if (!empty($errors['author'])): ?><p style="color:#b91c1c;font-size:.78rem;margin-top:4px;"><?= h($errors['author']) ?></p><?php endif; ?>
          </div>
        </div>
        <div class="form-field">
          <label style="color:var(--brown-md);">Excerpt (shown on blog listing)</label>
          <textarea name="excerpt" rows="2"
                    style="width:100%;background:var(--warm);border:1.5px solid var(--warm);border-radius:12px;padding:12px 16px;color:var(--brown);font-family:'DM Sans',sans-serif;font-size:.92rem;"><?= old('excerpt', $post['excerpt'] ?? '') ?></textarea>
        </div>
        <div class="form-field">
          <label style="color:var(--brown-md);">Full Content</label>
          <textarea name="content" rows="10" required
                    style="width:100%;background:var(--warm);border:1.5px solid var(--warm);border-radius:12px;padding:12px 16px;color:var(--brown);font-family:'DM Sans',sans-serif;font-size:.92rem;"><?= old('content', $post['content'] ?? '') ?></textarea>
          <?php if (!empty($errors['content'])): ?><p style="color:#b91c1c;font-size:.78rem;margin-top:4px;"><?= h($errors['content']) ?></p><?php endif; ?>
        </div>
        <div style="display:flex;gap:12px;">
          <button class="btn-primary" type="submit">Save Post</button>
          <a href="<?= h(base_url('admin/blog_posts.php')) ?>" class="btn-outline" style="text-decoration:none;">Cancel</a>
        </div>
      </form>
    </div>
  </section>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
