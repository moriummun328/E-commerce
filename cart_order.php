<?php
if(session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=uft-8');

if(empty($_SESSION['user_id']))
{
	echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
	exit;
}

require_once __DIR__ .'/admin/dbConfig.php';

$userId = (int)$_SESSION['user_id'];

$st = $DB_con->prepare("SELECT id FROM carts WHERE user_id = ? AND status = 'open' LIMIT 1");

$st->execute([$userId]);

$cartId = ($st->fetch(PDO::FETCH_ASSOC)['id'] ?? null);

if($cartId)
{
	$DB_con->prepare("DELETE FROM cart_items WHERE cart_id = ?")->execute([$cartId]);
	$DB_con->prepare("UPDATE carts SET status = 'ordered' WHERE id = ?")->execute([$cartId]);
}

$_SESSION['flash'] = 'Your order has benn placed successfully';
header('Location: thankyou.php');

?>