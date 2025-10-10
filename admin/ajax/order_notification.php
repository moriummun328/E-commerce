<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../dbConfig.php';

$delivery_id = (int)($_POST['delivery_man_id'] ?? 0);

if($delivery_id <= 0){
    echo json_encode(['orders'=>[]]);
    exit;
}

try {
    $sql = "
        SELECT 
            o.global_order_id,
            o.user_name,
            o.phone,
            o.address,
            o.area,
            o.status,
            GROUP_CONCAT(CONCAT(p.product_name,' (',o.quantity,')') SEPARATOR ', ') AS items
        FROM orders o
        JOIN products p ON p.id = o.product_id
        WHERE o.delivery_man_id = ?
        GROUP BY o.global_order_id
        ORDER BY o.order_date DESC
    ";
    $stmt = $DB_con->prepare($sql);
    $stmt->execute([$delivery_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['orders'=>$orders]);
} catch (Throwable $e){
    echo json_encode(['orders'=>[], 'error'=>$e->getMessage()]);
}
