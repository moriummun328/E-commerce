<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../dbConfig.php';

if (!isset($_SESSION['delivery_man_id'])) {
    header('Location: delivery_login.php');
    exit;
}

// Fetch delivery man info
$delivery_id = $_SESSION['delivery_man_id'];
$stmt = $DB_con->prepare("SELECT * FROM delivery_men WHERE id = ?");
$stmt->execute([$delivery_id]);
$delivery = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$delivery) {
    die("Delivery man not found.");
}

// এখন sidebar include করতে পারো
// include '../includes/sidebar.php';
?>
