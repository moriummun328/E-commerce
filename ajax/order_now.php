<?php
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__.'/../config/dbConfig.php';
require_once __DIR__ .'/../config/class.user.php';

$database = new Database();
$conn = $database->dbConnection();
$user = new USER();

// User login check
if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success'=>false, 'message'=>'User not logged in']);
    exit;
}

// User input
$user_id = $_SESSION['user_id'];
$user_name = $_POST['user_name'] ?? '';
$phone = $_POST['phone'] ?? '';
$area = $_POST['area'] ?? '';
$address = $_POST['address'] ?? '';

// Global Order ID
$global_order_id = strtoupper(uniqid("ORD"));

// Coupon handling: safe defaults if no coupon
$coupon_code = $_SESSION['applied_coupon'] ?? '';
$coupon_discount = isset($_SESSION['coupon_discount']) ? floatval($_SESSION['coupon_discount']) : 0.00;

// Fetch user's open cart
$stmtCart = $conn->prepare("SELECT id FROM carts WHERE user_id = ? AND status = 'open'");
$stmtCart->execute([$user_id]);
$cartRow = $stmtCart->fetch(PDO::FETCH_ASSOC);

if(!$cartRow) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

$cart_id = $cartRow['id'];

// Fetch cart items
$stmtItems = $conn->prepare("SELECT * FROM cart_items WHERE cart_id = ?");
$stmtItems->execute([$cart_id]);
$cart_items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

if(empty($cart_items)) {
    echo json_encode(['success' => false, 'message' => 'Cart has no items']);
    exit;
}

// Begin transaction
$total = 0;
$conn->beginTransaction();

try {
    $item_count = count($cart_items);
    $per_item_discount = $item_count > 0 ? $coupon_discount / $item_count : 0;

    foreach ($cart_items as $item) {
        $product_id = $item['product_id'];
        $qty = (int)$item['qty'];
        $unit_price = (float)$item['unit_price'];
        $subtotal = $qty * $unit_price;
        $total += $subtotal;

        // Payable per item, safe if no coupon
        $payable = max(0, $subtotal - $per_item_discount);

        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders 
            (global_order_id, user_id, user_name, phone, area, address, product_id, quantity, order_date, coupon_code, coupon_discount, total_amount, payable_amount, status) 
            VALUES (?,?,?,?,?,?,?,?,NOW(),?,?,?,?,'pending')");

        $stmt->execute([
            $global_order_id,
            $user_id,
            $user_name,
            $phone,
            $area,
            $address,
            $product_id,
            $qty,
            $coupon_code,
            $per_item_discount,
            $subtotal,
            $payable
        ]);
    }

    // Clear cart
    $conn->prepare("DELETE FROM cart_items WHERE cart_id = ?")->execute([$cart_id]);
    $conn->prepare("UPDATE carts SET status = 'ordered' WHERE id = ?")->execute([$cart_id]);

    $conn->commit();

    // Send email if user email exists
    $userEmail = $_SESSION['user_email'] ?? '';
    if($userEmail) {
        $payable_total = $total - $coupon_discount;
        $msg = "
            <h3>Thank You, {$user_name}!</h3>
            <p>Your order has been placed successfully</p>
            <p><strong>Global Order ID:</strong> {$global_order_id}</p>
            <p><strong>Total Amount:</strong> ".number_format($total,2)." Tk</p>
            <p><strong>Coupon Discount:</strong> ".number_format($coupon_discount,2)." Tk</p>
            <p><strong>Payable Amount:</strong> ".number_format($payable_total,2)." Tk</p>
            <p>Delivery Address: {$address}, {$area}</p>";
        $user->sendMail($userEmail, $msg, "Order Confirmation-{$global_order_id}");
    }

    // Clear coupon session
    unset($_SESSION['applied_coupon']);
    unset($_SESSION['coupon_discount']);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Failed to place order']);
}
?>
