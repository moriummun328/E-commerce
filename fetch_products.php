<?php
if(session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/admin/dbConfig.php';

$catIds = isset($_POST['categories']) ? $_POST['categories'] : [];
$catIds = is_array($catIds) ? array_filter($catIds) : [];

if($catIds)
{
	$in = implode(',', array_fill(0, count($catIds), '?'));
	$sql = "SELECT p.*, c.category_name, a.sizes, a.colors FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN attributes a ON a.product_id = p.id WHERE p.category_id IN ($in) ORDER BY p.id DESC";

	$stmt = $DB_con->prepare($sql);
	$stmt->execute($catIds);
}

else
{
	$sql = "SELECT p.*, c.category_name, a.sizes, a.colors FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN attributes a ON a.product_id = p.id ORDER BY p.id DESC";
	$stmt = $DB_con->query($sql);
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

function productThumb($row)
{
	$fsBase = __DIR__."/admin/uploads/";
	$urlBase = "admin/uploads/";

	if(!empty($row['product_image']) && file_exists($fsBase .$row['product_image']))
	{
		return $urlBase.htmlspecialchars($row['product_image']);
	}

	if($row['book_type'] == 'downloadable' && !empty($row['virtual_file']))
	{
		$pdfIcon = 'pdf-icon.png';
		if(file_exists($fsBase .$pdfIcon))
		{
			return $urlBase.$pdfIcon;
		}
	}

	return "assets/images/myImage.png";
}

ob_start();

if(!$products)
{
	echo '<div class="col-12"><div class="alert alert-info">No Products Found!</div></div>';
}

else
{
	foreach($products as $p)
	{
		$img = productThumb($p);
	
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
<?php
}
}

echo ob_get_clean();

?>