<?php
if(session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');

if(!isset($_SESSION['admin_logged_in']))
{
	echo json_encode(['items' => []]);
	exit;
}

require_once __DIR__ .'/../dbConfig.php';

$cid = (int)($_GET['category_id'] ?? 0);

if($cid <= 0) { echo json_encode(['items' => []]); exit; }

$st = $DB_con->prepare("SELECT id, product_name FROM products WHERE category_id = ? ORDER BY product_name ASC");
$st->execute([$cid]);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

$items = [];
foreach($rows as $r)
{
	$items[] = ['id' => (int)$r['id'], 'name' => $r['product_name']];
}

echo json_encode(['items' => $items]);


?>