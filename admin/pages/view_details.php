<?php
include 'dbConfig.php';

$products = [];

if (isset($_GET['view_id'])) {
    $view_id = (int)base64_decode(urldecode($_GET['view_id']));
    $stmt = $DB_con->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$view_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($products)) {
        echo "No product found with this ID.";
        exit;
    }
} else {
    echo "No product ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>View Product Modal</title>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<!-- Button to trigger modal -->
<div class="container mt-5">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
        View Product Details
    </button>
</div>

<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- large modal -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="productModalLabel"><?= htmlspecialchars($products[0]['product_name']) ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-5">
                <img src="uploads/<?= htmlspecialchars($products[0]['product_image']) ?>" alt="Product Image" class="img-fluid rounded" />
            </div>
            <div class="col-md-7">
                <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($products[0]['description'])) ?></p>
                <p><strong>Stock:</strong> <?= htmlspecialchars($products[0]['stock_amount']) ?></p>
                <p><strong>Unit Price:</strong> <?= htmlspecialchars(number_format($products[0]['unit_price'], 2)) ?></p>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
