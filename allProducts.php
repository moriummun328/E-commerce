<?php
require_once __DIR__ .'/config/class.user.php';
require_once 'partials/header.php';
require_once 'partials/navbar.php';


require_once __DIR__ . '/admin/dbConfig.php';

// Get category ID from GET
$cat_id = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;

// Fetch categories for dropdown
$catStmt = $DB_con->prepare("SELECT id, category_name FROM categories ORDER BY category_name");
$catStmt->execute();
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch products
if($cat_id > 0){
    $stmt = $DB_con->prepare("SELECT p.*, c.category_name, a.sizes, a.colors 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN attributes a ON a.product_id = p.id 
        WHERE p.category_id = ? 
        ORDER BY p.id DESC");
    $stmt->execute([$cat_id]);
} else {
    $stmt = $DB_con->query("SELECT p.*, c.category_name, a.sizes, a.colors 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN attributes a ON a.product_id = p.id 
        ORDER BY p.id DESC");
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

function productThumb($row)
{
    if (!empty($row['product_image']) && file_exists(__DIR__ . '/admin/uploads/' . $row['product_image'])) {
        return "admin/uploads/" . htmlspecialchars($row['product_image']);
    }

    if (!empty($row['book_type']) && $row['book_type'] == 'downloadable' && !empty($row['virtual_file'])) {
        return "admin/uploads/pdf-icon.png";
    }

    return "assets/images/myImage.png";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Products</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
<div class="container mt-4">

    <!-- Products Grid -->
    <div class="row">
        <?php if($products): ?>
            <?php foreach($products as $p): ?>
                <?php $img = productThumb($p); ?>
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
                <div class="alert alert-info">No Products Found!</div>
            </div>
        <?php endif; ?>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.min.js"></script>
</body>
</html>



<?php require_once 'partials/footer.php';?>

