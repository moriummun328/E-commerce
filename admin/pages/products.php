
<?php
include 'dbConfig.php';

// Handle Delete Operation
error_reporting(0);
if (isset($_GET['delete_id'])) {
    $delete_id = (int)base64_decode(urldecode($_GET['delete_id']));

    // Delete Image
    $stmtImg = $DB_con->prepare("SELECT product_image FROM products WHERE id = ?");
    $stmtImg->execute([$delete_id]);
    $productImg = $stmtImg->fetchColumn();

    if ($productImg && file_exists("uploads/$productImg")) {
        unlink("uploads/$productImg");
    }

    // Delete attributes and product
    $stmtAttr = $DB_con->prepare("DELETE FROM attributes WHERE product_id = ?");
    $stmtAttr->execute([$delete_id]);

    $stmtDel = $DB_con->prepare("DELETE FROM products WHERE id = ?");
    $stmtDel->execute([$delete_id]);

}

// Fetch all products
$stmt = $DB_con->prepare("SELECT * FROM products ORDER BY id DESC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Products</title>
    
    <style>
        .thumbnail-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }
        .color-box {
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-right: 4px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">All Products</h2>
    <a href="?page=addNew" class="btn btn-success mb-2">Add New Products</a>


    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Description</th>
            <th>Stock</th>
            <th>Unit Price</th>
            <th>Selling Price</th>
            <th>Category</th>
            <th>Size</th>
            <th>Colors</th>
            <th colspan="3">Actions</th>
        </tr>
        </thead>

        <?php if ($products): ?>
            <?php foreach ($products as $row): ?>
                <?php
                $product_id = $row['id'];
                $encrypted_id = urlencode(base64_encode($product_id));

                // Fetch attributes
                $attrStmt = $DB_con->prepare("SELECT sizes, colors FROM attributes WHERE product_id = ?");
                $attrStmt->execute([$product_id]);
                $attrubute = $attrStmt->fetch(PDO::FETCH_ASSOC);
                $sizes = $attrubute['sizes'] ?? '';
                $colors = $attrubute['colors'] ?? '';
                $colorArray = explode(',', $colors);
                ?>

                <tr>
                    <td><img src="uploads/<?php echo htmlspecialchars($row['product_image']); ?>" class="thumbnail-img"></td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo (int)$row['stock_amount']; ?></td>
                    <td><?php echo (int)$row['unit_price']; ?></td>
                    <td><?php echo (int)$row['selling_price']; ?></td>
                    <td>
                        <?php
                        if (!empty($row['category_id'])) {
                            $catStmt = $DB_con->prepare("SELECT category_name FROM categories WHERE id = ?");
                            $catStmt->execute([$row['category_id']]);
                            echo htmlspecialchars($catStmt->fetchColumn());
                        } else {
                            echo "N/A";
                        }
                        ?>
                    </td>
                    <td><?php echo $sizes ? htmlspecialchars($sizes) : 'N/A'; ?></td>
                    <td>
                        <?php if ($colors): ?>
                            <?php foreach ($colorArray as $color): ?>
                                <span class="color-box" style="background-color: <?php echo htmlspecialchars($color); ?>;" title="<?php echo $color; ?>"></span>
                            <?php endforeach; ?>
                        <?php else: ?>---
                        <?php endif; ?>
                    </td>
                    <td><a href="?page=edit_product&id=<?php echo $encrypted_id; ?>" class="btn btn-sm btn-warning">Edit</a></td>
                    <td><a href="?page=products&delete_id=<?php echo $encrypted_id; ?>" class="btn btn-sm btn-danger">Delete</a></td>
                    <td><a href="#productModal<?php echo $product_id; ?>" class="btn btn-sm btn-info" data-toggle="modal">View Details</a></td>
                </tr>

                <!-- Modal Product Details -->
                <div class="modal fade" id="productModal<?php echo $product_id; ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel<?php echo $product_id; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel<?php echo $product_id; ?>">
                                    <?php echo htmlspecialchars($row['product_name']); ?>
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="text-center mb-3">
                                    <img src="uploads/<?php echo htmlspecialchars($row['product_image']); ?>" class="img-fluid" style="max-height: 300px;">
                                </div>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                                
                               
                                <p><strong>Price:</strong> <?php echo (int)$row['selling_price']; ?></p>
                                <p><strong>Category:</strong>
                                    <?php
                                    if (!empty($row['category_id'])) {
                                        $catStmt = $DB_con->prepare("SELECT category_name FROM categories WHERE id = ?");
                                        $catStmt->execute([$row['category_id']]);
                                        echo htmlspecialchars($catStmt->fetchColumn());
                                    } else {
                                        echo "N/A";
                                    }
                                    ?>
                                </p>
                                <p><strong>Size:</strong> <?php echo $sizes ? htmlspecialchars($sizes) : 'N/A'; ?></p>
                                <p><strong>Colors:</strong>
                                    <?php if ($colors): ?>
                                        <?php foreach ($colorArray as $color): ?>
                                            <span class="color-box" style="background-color: <?php echo htmlspecialchars($color); ?>;" title="<?php echo $color; ?>"></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>---
                                    <?php endif; ?>
                                </p>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- End of Modal -->

            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="12" class="text-center">No products found!</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
</body>
</html>
