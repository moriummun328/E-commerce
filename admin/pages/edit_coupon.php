<?php
require_once __DIR__ . '/../dbConfig.php';

if(!isset($_GET['id'])) die("Invalid request!");
$id = (int)$_GET['id'];

// Coupon data আনব
$stmt = $DB_con->prepare("SELECT * FROM coupons WHERE id = ?");
$stmt->execute([$id]);
$coupon = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$coupon) die("Coupon not found!");

// Coupon attached products
$stmt2 = $DB_con->prepare("SELECT product_id FROM coupon_products WHERE coupon_id = ?");
$stmt2->execute([$id]);
$attached_products = $stmt2->fetchAll(PDO::FETCH_COLUMN);

// Categories
$cats = $DB_con->query("SELECT id, category_name FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// All Products
$products = $DB_con->query("SELECT id, product_name, category_id FROM products")->fetchAll(PDO::FETCH_ASSOC);

// Update handle
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code          = $_POST['code'];
    $discount      = (float)$_POST['discount_percent'];
    $status        = $_POST['status'];
    $scope         = $_POST['scope'];
    $start_date    = $_POST['start_date'] ?: null;
    $end_date      = $_POST['end_date'] ?: null;
    $usage_limit   = (int)$_POST['usage_limit'];
    $usage_count   = (int)$_POST['usage_count'];

    $stmt = $DB_con->prepare("UPDATE coupons 
        SET code=?, discount_percent=?, status=?, scope=?, start_date=?, end_date=?, usage_limit=?, usage_count=? 
        WHERE id=?");
    $stmt->execute([$code, $discount, $status, $scope, $start_date, $end_date, $usage_limit, $usage_count, $id]);

    // coupon_products টেবিল আপডেট
    $DB_con->prepare("DELETE FROM coupon_products WHERE coupon_id=?")->execute([$id]);

    if($scope === 'product' && !empty($_POST['products'])) {
        $stmtP = $DB_con->prepare("INSERT INTO coupon_products (coupon_id, product_id) VALUES (?, ?)");
        foreach($_POST['products'] as $pid) {
            $stmtP->execute([$id, $pid]);
        }
    }

    header("Location: ?page=coupons&msg=updated");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Coupon</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="container mt-4">
    <h2>Edit Coupon</h2>

    <form method="POST">
        <div class="form-group">
            <label>Coupon Code</label>
            <input type="text" name="code" value="<?= htmlspecialchars($coupon['code']) ?>" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Discount (%)</label>
            <input type="number" step="0.01" name="discount_percent" value="<?= $coupon['discount_percent'] ?>" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Scope</label>
            <select name="scope" id="scope" class="form-control">
                <option value="all" <?= $coupon['scope'] === 'all' ? 'selected' : '' ?>>All Products</option>
                <option value="product" <?= $coupon['scope'] === 'product' ? 'selected' : '' ?>>Specific Products</option>
            </select>
        </div>

        <!-- product scope -->
        <div id="product-options" style="display: <?= $coupon['scope']==='product'?'block':'none' ?>;">
            <div class="form-group">
                <label>Select Category</label>
                <select id="category" class="form-control">
                    <option value="">-- Select Category --</option>
                    <?php foreach($cats as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= $c['category_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Select Products</label>
                <select name="products[]" id="products" class="form-control" multiple>
                    <?php foreach($products as $p): ?>
                        <option value="<?= $p['id'] ?>" data-cat="<?= $p['category_id'] ?>"
                            <?= in_array($p['id'], $attached_products) ? 'selected' : '' ?>>
                            <?= $p['product_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">Hold CTRL (Windows) / CMD (Mac) for multiple select</small>
            </div>
        </div>

        <div class="form-group">
            <label>Usage Limit</label>
            <input type="number" name="usage_limit" value="<?= $coupon['usage_limit'] ?>" class="form-control">
        </div>

        <div class="form-group">
            <label>Usage Count (used so far)</label>
            <input type="number" name="usage_count" value="<?= $coupon['usage_count'] ?>" class="form-control">
        </div>

        <div class="form-group">
            <label>Start Date</label>
            <input type="date" name="start_date" value="<?= $coupon['start_date'] ?>" class="form-control">
        </div>

        <div class="form-group">
            <label>End Date</label>
            <input type="date" name="end_date" value="<?= $coupon['end_date'] ?>" class="form-control">
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active" <?= $coupon['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $coupon['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Update Coupon</button>
        <a href="?page=coupons" class="btn btn-secondary">Cancel</a>
    </form>

    <script>
        // scope অনুযায়ী প্রোডাক্ট দেখাবে
        $("#scope").change(function(){
            if($(this).val() === 'product') {
                $("#product-options").show();
            } else {
                $("#product-options").hide();
            }
        });

        // category অনুযায়ী product filter
        $("#category").change(function(){
            let cid = $(this).val();
            $("#products option").each(function(){
                if(cid === "" || $(this).data("cat") == cid) {
                    $(this).show();
                } else {
                    $(this).hide().prop("selected", false);
                }
            });
        });
    </script>
</body>
</html>
