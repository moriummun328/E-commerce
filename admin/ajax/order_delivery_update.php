<?php
if(session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');
if(!isset($_SESSION['admin_logged_in'])) { http_response_code(403); exit(json_encode(['ok'=>false,'msg'=>'Unauthorized']));}

require_once __DIR__ .'/../dbConfig.php';

$gid = trim($_POST['global_order_id'] ?? '');
$manId = (int)($_POST['delivery_man_id'] ?? 0);

if($gid === '' || $manId <=0){
    echo json_encode(['ok'=>false,'msg'=>'Invalid Input']); exit;
}

try {
    $q = $DB_con->prepare("UPDATE orders SET delivery_man_id=? WHERE global_order_id=?");
    $q->execute([$manId,$gid]);
    echo json_encode(['ok'=>true,'msg'=>'Delivery man assigned successfully']);
} catch(Throwable $e){
    echo json_encode(['ok'=>false,'msg'=>'DB/Error: '.$e->getMessage()]);
}
