<?php
require_once __DIR__ .'/config/dbconfig.php';

try 
{
	if($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid');

	$name = trim($_POST['name'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$subject = trim($_POST['subject'] ?? '');
	$message = trim($_POST['message'] ?? '');

	if($name === '' || $email === '' || $message === '') throw new Exception('Missing');

	$database = new Database();
	$conn = $database->dbConnection();

	$st = $conn->prepare("INSERT INTO contact_message (name, email, subject, message) VALUES (?,?,?,?)");

	$st->execute([$name, $email, $subject, $message]);

	header("Location: contact_us.php?msg=ok");
	exit;
} 
catch (Throwable $e) 
{
	header("Location: contact_us.php?msg=err");
	exit;
}

?>