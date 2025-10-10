<?php
if(session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=uft-8');

if(empty($_SESSION['user_id']))
{
	echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
	exit;
}

require_once __DIR__ .'/../admin/dbConfig.php';

$userId = (int)$_SESSION['user_id'];

$itemId = (int)($_POST['item_id'] ?? 0);
$qty = (int)($_POST['qty'] ?? 1);

if($qty < 1) $qty = 1;

$sql = "SELECT ci.id, ci.cart_id FROM cart_items ci JOIN carts c ON c.id = ci.cart_id AND c.user_id = ? AND c.status = 'open' WHERE ci.id = ? LIMIT 1";

$st = $DB_con->prepare($sql);
$st->execute([$userId, $itemId]);
$row = $st->fetch(PDO::FETCH_ASSOC);

if(!$row)
{
	echo json_encode(['ok' => false, 'error' =>'Not Found']);
	exit;
}

$up = $DB_con->prepare("UPDATE cart_items SET qty = ? WHERE id = ?");
$up->execute([$qty, $itemId]);
echo json_encode(['ok' => true]);
?>