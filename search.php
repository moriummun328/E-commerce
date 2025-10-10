<?php
require_once __DIR__ . '/admin/dbConfig.php';
require_once __DIR__.'/partials/header.php';
require_once __DIR__.'/partials/navbar.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

$products = [];
if ($q !== '') {
    $sql = "SELECT p.*, c.category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.product_name LIKE :q OR p.description LIKE :q 
            ORDER BY p.id DESC";
    $stmt = $DB_con->prepare($sql);
    $stmt->execute([':q' => "%$q%"]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Results</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    body { background: #f8f9fa; }
    .product-card { border: 1px solid #ddd; border-radius: 10px; overflow: hidden; transition: 0.3s; background: #fff; }
    .product-card:hover { box-shadow: 0px 4px 10px rgba(0,0,0,0.1); }
    .product-card img { width: 100%; height: 180px; object-fit: cover; }
    .p-body { padding: 15px; }
  </style>
</head>
<body>
<div class="container py-4">

  <div class="row">
    <?php if ($products): ?>
      <?php foreach ($products as $p): ?>
        <?php
          $img = (!empty($p['product_image']) && file_exists(__DIR__ . '/admin/uploads/' . $p['product_image']))
            ? "admin/uploads/" . htmlspecialchars($p['product_image'])
            : "assets/images/myImage.png";
        ?>
<div class="col-sm-6 col-md-4 col-lg-3 mb-3">
  <div class="product-card h-100">
    <img src="<?= $img ?>" alt="<?= $p['product_name'] ?>">
    <div class="p-body">
      <h6 class="mb-1"><?= $p['product_name'] ?></h6>
      <div class="text-muted small mb-2">
        <?= $p['category_name'] ?? '--'?>
      </div>

      <div class="d-flex justify-content-between align-items-center">
        <span class="font-weight-bold"><?= (int)$p['selling_price']?>$</span>

        <?php if(!empty($_SESSION['user_id'])): ?>
          <form method="post" action="cart_add.php" class="m-0">
            <input type="hidden" name="product_id" value=" <?= (int)$p['id'] ?>">
            <button type="submit" class="btn btn-outline-primary btn-sm">Add to Cart</button>
          </form>
          <?php else: ?>
            <button type="button" class="btn btn-warning login-alert-btn">Login to Add Cart</button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-warning">No products found for your search!</div>
      </div>
    <?php endif; ?>

  </div>
</div>
</body>
</html>
<?php require_once __DIR__ .'/partials/footer.php'; ?>
