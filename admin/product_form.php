<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$pageTitle = 'Product Form';
require_once __DIR__ . '/../includes/db.php';
$pdo = db();
$pdo = db();

$id = (int)($_GET['id'] ?? 0);
$p  = $id ? $pdo->prepare('SELECT * FROM products WHERE id=?') : null;
if ($p) { $p->execute([$id]); $p = $p->fetch(); }
if ($id && !$p) { set_flash('error','Product not found.'); redirect('admin/products.php'); }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name        = post('name');
    $category    = post('category');
    $price       = (float)post('price');
    $description = post('description');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    if (!$name)          $errors['name']     = 'Product name is required.';
    if (!$category)      $errors['category'] = 'Category is required.';
    if ($price <= 0)     $errors['price']    = 'Price must be greater than 0.';

    if (empty($errors)) {
        if ($id) {
            $pdo->prepare('UPDATE products SET name=?,category=?,price=?,description=?,is_featured=? WHERE id=?')
                ->execute([$name,$category,$price,$description,$is_featured,$id]);
        } else {
            $pdo->prepare('INSERT INTO products (name,category,price,description,is_featured) VALUES (?,?,?,?,?)')
                ->execute([$name,$category,$price,$description,$is_featured]);
        }
        set_flash('success', 'Product saved!');
        redirect('admin/products.php');
    }
    store_old(compact('name','category','price','description'));
}
$cats = ['Food','Litter','Toys','Accessories','Apparel'];
// ── Output starts here ─────────────────────────────────────────────────────
require_once __DIR__ . '/../includes/header.php';

?>

<div style="display:flex;min-height:80vh;">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <section style="flex:1;padding:60px 48px;">
    <h1 class="section-title" style="margin-bottom:32px;"><?= $id ? 'Edit' : 'Add' ?> <em>Product</em></h1>

    <div class="membership-right" style="max-width:580px;background:var(--white);border:1.5px solid var(--warm);">
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <div class="form-field">
          <label style="color:var(--brown-md);">Product Name</label>
          <input type="text" name="name" value="<?= old('name', $p['name'] ?? '') ?>" required
                 style="background:var(--warm);border-color:var(--warm);color:var(--brown);"/>
          <?php if (!empty($errors['name'])): ?><p style="color:#b91c1c;font-size:.78rem;margin-top:4px;"><?= h($errors['name']) ?></p><?php endif; ?>
        </div>
        <div class="form-field">
          <label style="color:var(--brown-md);">Category</label>
          <select name="category" style="width:100%;background:var(--warm);border:1.5px solid var(--warm);border-radius:12px;padding:12px 16px;color:var(--brown);font-family:'DM Sans',sans-serif;font-size:.92rem;">
            <?php foreach ($cats as $c): ?>
              <option value="<?= h($c) ?>" <?= (old('category', $p['category'] ?? '')===$c)?'selected':'' ?>><?= h($c) ?></option>
            <?php endforeach; ?>
          </select>
          <?php if (!empty($errors['category'])): ?><p style="color:#b91c1c;font-size:.78rem;margin-top:4px;"><?= h($errors['category']) ?></p><?php endif; ?>
        </div>
        <div class="form-field">
          <label style="color:var(--brown-md);">Price (SGD)</label>
          <input type="number" name="price" step="0.01" min="0" value="<?= old('price', $p['price'] ?? '') ?>" required
                 style="background:var(--warm);border-color:var(--warm);color:var(--brown);"/>
          <?php if (!empty($errors['price'])): ?><p style="color:#b91c1c;font-size:.78rem;margin-top:4px;"><?= h($errors['price']) ?></p><?php endif; ?>
        </div>
        <div class="form-field">
          <label style="color:var(--brown-md);">Description</label>
          <textarea name="description" rows="4"
                    style="width:100%;background:var(--warm);border:1.5px solid var(--warm);border-radius:12px;padding:12px 16px;color:var(--brown);font-family:'DM Sans',sans-serif;font-size:.92rem;resize:vertical;"><?= old('description', $p['description'] ?? '') ?></textarea>
        </div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
          <input type="checkbox" name="is_featured" id="is_featured" value="1" <?= ($p['is_featured'] ?? 0) ? 'checked' : '' ?>
                 style="width:18px;height:18px;accent-color:var(--orange);">
          <label for="is_featured" style="color:var(--brown-md);font-size:.88rem;cursor:pointer;">Feature this product on homepage</label>
        </div>
        <div style="display:flex;gap:12px;">
          <button class="btn-primary" type="submit">Save Product</button>
          <a href="<?= h(base_url('admin/products.php')) ?>" class="btn-outline" style="text-decoration:none;">Cancel</a>
        </div>
      </form>
    </div>
  </section>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
