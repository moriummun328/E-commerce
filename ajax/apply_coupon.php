<?php
if(session_status()=== PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');

if(empty($_SESSION['user_id'])) { echo json_encode(['ok' => false, 'msg' => 'Login Required']); exit; }

require_once __DIR__ .'/../admin/dbConfig.php';

$code = trim($_POST['code'] ?? ''); 
if($code === '') { echo json_encode(['ok' => flase, 'msg' => 'No coupon code provuded']); exit; }

try 
{
	$uid = (int)$_SESSION['user_id'];

	$st = $DB_con->prepare("SELECT id FROM carts WHERE user_id = ? AND status = 'open' LIMIT 1");
	$st->execute([$uid]);
	$cartId = $st->fetch(PDO::FETCH_ASSOC)['id'] ?? null;
	if(!$cartId)
	{
		echo json_encode(['ok' => false, 'msg' => 'Cart not fount']);
		exit;
	}

	$sql = "SELECT ci.product_id, ci.qty, ci.unit_price FROM cart_items ci WHERE ci.cart_id = ?";
	$st = $DB_con->prepare($sql);
	$st->execute([$cartId]);
	$items = $st->fetchAll(PDO::FETCH_ASSOC);

	if(!$items)
	{
		echo  json_encode(['ok' => false, 'msg' => 'Cart is empty']);
		exit;
	}

	$subtotal = 0.00;
	foreach ($items as $it) 
	{
		$subtotal += ((float)$it['unit_price'] * (int)$it['qty']);		
	}

	$today = date('Y-m-d');
	$q = $DB_con->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active' AND (start_date IS NULL OR start_date <= ?) AND (end_date IS NULL OR end_date >=?) ORDER BY id DESC LIMIT 1");

	$q->execute([$code, $today, $today]);
	$cp = $q->fetch(PDO::FETCH_ASSOC);

	if(!$cp)
	{
		echo  json_encode(['ok' => false, 'msg' => 'Coupon not valid or expired']);
		exit;
	}

	$percent = (float)$cp['discount_percent'];
	if($percent <= 0)
	{
		echo  json_encode(['ok' => false, 'msg' => 'invlid Discount']);
		exit;
	}

	$discountBase = 0.00;

	if($cp['scope'] === 'all')
	{
		$discountBase = $subtotal;
	}

	else
	{
		$map = $DB_con->prepare("SELECT product_id FROM coupon_products WHERE coupon_id = ?");
		$map->execute([(int)$cp['id']]);
		$pids = $map->fetchAll(PDO::FETCH_COLUMN,0);

		if($pids)
		{
			$pidSet = array_map('intval',$pids);
			foreach($items as $it)
			{
				if(in_array((int)$it['product_id'], $pidSet, true))
				{
					$discountBase += ((float)$it['unit_price'] * (int)$it['qty']);
				}
			}
		}	
	}

	if($discountBase <= 0)
	{
		echo  json_encode(['ok' => false, 'msg' => 'Coupon not applicable to this item']);
		exit;
	}

	$discount = round($discountBase * ($percent/100), 2);
	if($discount > $subtotal) $discount = $subtotal;
	$net = round($subtotal - $discount, 2);

	echo json_encode([
			'ok' => true,
			'percent' => $percent,
			'discount' => $discount,
			'net' => $net,
			'msg' => 'Coupon Applied'	
	]);
} 
catch (Throwable $e) 
{
	echo json_encode(['ok'=>false, 'msg'=>'Server Error']);
}


$_SESSION['applied_coupon'] =$code;
$_SESSION['coupon_discount']=	$discount;

?>