<?php
if(session_status() == PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');

if(empty($_SESSION['admin_logged_in']))
{
	echo json_encode(['ok' => false, 'msg' =>'Unauthorised']);
	exit;
}

require_once __DIR__ .'/../dbConfig.php';

$sql = "SELECT COUNT(DISTINCT global_order_id) AS c FROM orders WHERE status = 'pending'";
$crow = $DB_con->query($sql)->fetch(PDO::FETCH_ASSOC);
$currentCount = (int)($crow['c'] ?? 0);
$_SESSION['orders_last_seen_count'] = $currentCount;

echo json_encode(['ok' => true, 'seen' => $currentCount]);

?>