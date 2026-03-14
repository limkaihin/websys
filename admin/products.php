<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$pageTitle = 'Manage Products';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
$pdo = db();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'delete') {
    verify_csrf();
    $pdo->prepare('DELETE FROM products WHERE id=?')->execute([(int)post('id')]);
    set_flash('success', 'Product deleted.');
    redirect('admin/products.php');
}

$products = $pdo->query('SELECT * FROM products ORDER BY category, name')->fetchAll();
?>

<div style="display:flex;min-height:80vh;flex-wrap:wrap;">
  <?php include __DIR__ . '/sidebar.php'; ?>

  <section style="flex:1;padding:60px 48px;overflow-x:auto;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;">
      <h1 class="section-title">Products <em>Manager</em></h1>
      <a href="<?= h(base_url('admin/product_form.php')) ?>" class="btn-primary" style="text-decoration:none;">+ Add Product</a>
    </div>

    <table style="width:100%;border-collapse:collapse;background:var(--white);border-radius:20px;overflow:hidden;box-shadow:0 4px 20px rgba(61,35,20,.08);">
      <thead style="background:var(--brown);color:var(--cream);">
        <tr>
          <?php foreach (['#','Name','Category','Price','Featured','Actions'] as $h): ?>
            <th style="padding:14px 20px;text-align:left;font-size:.78rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;"><?= $h ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr style="border-bottom:1px solid var(--warm);">
          <td style="padding:14px 20px;color:var(--brown-md);font-size:.82rem;"><?= $p['id'] ?></td>
          <td style="padding:14px 20px;font-weight:500;color:var(--brown);"><?= h($p['name']) ?></td>
          <td style="padding:14px 20px;color:var(--brown-md);"><?= h($p['category']) ?></td>
          <td style="padding:14px 20px;color:var(--orange);font-weight:700;"><?= money((float)$p['price']) ?></td>
          <td style="padding:14px 20px;"><?= $p['is_featured'] ? '⭐' : '—' ?></td>
          <td style="padding:14px 20px;display:flex;gap:8px;">
            <a href="<?= h(base_url('admin/product_form.php?id=' . $p['id'])) ?>"
               style="background:var(--warm);border-radius:8px;padding:6px 14px;font-size:.8rem;text-decoration:none;color:var(--brown);">Edit</a>
            <form method="POST" onsubmit="return confirm('Delete this product?')">
              <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
              <input type="hidden" name="action"     value="delete">
              <input type="hidden" name="id"         value="<?= $p['id'] ?>">
              <button type="submit" style="background:#fee2e2;border:none;border-radius:8px;padding:6px 14px;font-size:.8rem;cursor:pointer;color:#b91c1c;">Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
