<?php
if(session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');
if(!isset($_SESSION['admin_logged_in'])) { http_response_code(403); exit('Forbidden');}

require_once __DIR__ .'/../dbConfig.php';
require_once __DIR__ . '/../../config/class.user.php';  // USER class

$gid = trim($_POST['global_order_id'] ?? '');
$st = trim($_POST['status'] ?? '');
$allowed = ['pending','delivered','completed','canceled'];

if($gid === '' || !in_array($st,$allowed,true)){
    echo json_encode(['ok'=>false,'msg'=>'Invalid Input']); exit;
}

try {
    // 1️⃣ Update order status
    $q = $DB_con->prepare("UPDATE orders SET status = ? WHERE global_order_id = ?");
    $q->execute([$st, $gid]);

    // 2️⃣ Only send mail if status = delivered
    if($st === 'delivered'){
        // fetch delivery man info
        $stmt = $DB_con->prepare("SELECT dm.name, dm.email FROM delivery_men dm JOIN orders o ON o.delivery_man_id=dm.id WHERE o.global_order_id=? LIMIT 1");
        $stmt->execute([$gid]);
        $man = $stmt->fetch(PDO::FETCH_ASSOC);

        if($man){
            // fetch order info
            $ost = $DB_con->prepare("SELECT global_order_id, user_name, phone, address, area FROM orders WHERE global_order_id=? LIMIT 1");
            $ost->execute([$gid]);
            $order = $ost->fetch(PDO::FETCH_ASSOC);

            if($order){
                $mail = new USER();
                $subject = "Order Delivered (Order #{$order['global_order_id']})";
                $message = "
                <html><body>
                <p>Hello {$man['name']},</p>
                <p>The order has been marked as <strong>Delivered</strong>.</p>
                <p>
                <strong>Order ID:</strong> {$order['global_order_id']}<br>
                <strong>Customer:</strong> {$order['user_name']}<br>
                <strong>Phone:</strong> {$order['phone']}<br>
                <strong>Address:</strong> {$order['address']}, {$order['area']}<br>
                </p>
                <p>Regards,<br>Admin Panel</p>
                </body></html>";
                $mail->sendMail($man['email'],$message,$subject);
            }
        }
    }

    echo json_encode(['ok'=>true,'msg'=>'Status updated successfully']);
} catch(Throwable $e){
    echo json_encode(['ok'=>false,'msg'=>'DB/Error: '.$e->getMessage()]);
}
