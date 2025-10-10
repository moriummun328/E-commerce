<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_logged_in'])) exit('not_logged_in');

require_once __DIR__ . '/../dbConfig.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    try {
        $DB_con->beginTransaction();

        // coupon_products থেকে ডিলিট
        $stmt1 = $DB_con->prepare("DELETE FROM coupon_products WHERE coupon_id = ?");
        $stmt1->execute([$id]);

        // coupons থেকে ডিলিট
        $stmt2 = $DB_con->prepare("DELETE FROM coupons WHERE id = ?");
        $stmt2->execute([$id]);

        $DB_con->commit();
        echo "ok";
    } catch (Exception $e) {
        $DB_con->rollBack();
        echo "error: " . $e->getMessage();
    }
}
?>
