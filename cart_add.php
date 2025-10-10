<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id'])) 
{
  $_SESSION['flash'] = 'Please login to add items to cart.';
  header("Location: auth/login.php");
  exit;
}

require_once __DIR__ . '/admin/dbConfig.php'; 

$pid = (int)($_POST['product_id'] ?? $_GET['product_id'] ?? 0);
$qty = 1;/*(int)($_POST['qty'] ?? $_GET['qty'] ?? 1);*/
if ($qty <= 0) $qty = 1;
if ($pid <= 0) 
{
  header("Location: cart.php"); exit;
}

$userId = (int)$_SESSION['user_id'];

$st = $DB_con->prepare("SELECT selling_price FROM products WHERE id = ? LIMIT 1");
$st->execute([$pid]);
$prod = $st->fetch(PDO::FETCH_ASSOC);
if (!$prod) { header("Location: cart.php"); exit; }
$unitPrice = (float)$prod['selling_price'];

$st = $DB_con->prepare("SELECT id FROM carts WHERE user_id = ? AND status = 'open' LIMIT 1");
$st->execute([$userId]);
$cartId = ($st->fetch(PDO::FETCH_ASSOC)['id'] ?? null);
if (!$cartId) 
{
  $ins = $DB_con->prepare("INSERT INTO carts (user_id, status) VALUES (?, 'open')");
  $ins->execute([$userId]);
  $cartId = (int)$DB_con->lastInsertId();
}

$st = $DB_con->prepare("SELECT id, qty FROM cart_items WHERE cart_id=? AND product_id=? LIMIT 1");
$st->execute([$cartId, $pid]);
$it = $st->fetch(PDO::FETCH_ASSOC);

if ($it) 
{
  $newQty = (int)$it['qty'] + $qty;
  $up = $DB_con->prepare("UPDATE cart_items SET qty=? WHERE id=?");
  $up->execute([$newQty, $it['id']]);
} 
else 
{
  $ins = $DB_con->prepare("INSERT INTO cart_items (cart_id, product_id, qty, unit_price) VALUES (?,?,?,?)");
  $ins->execute([$cartId, $pid, $qty, $unitPrice]);
}


$back = $_SERVER['HTTP_REFERER'] ?? 'cart.php';
header("Location: $back");
exit;
