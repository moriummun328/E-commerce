<?php
if(session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);

error_reporting(E_ERROR || E_PARSE);


ob_start();

if(empty($_SESSION['admin_logged_in']))
{
	echo json_encode(['ok'=>false, 'error' => 'Unauthorized']);
	exit;
}

require_once __DIR__ .'./../../config/dbconfig.php';
require_once __DIR__ .'./../../config/class.user.php';

try 
{
	if($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid Request');

	$id = (int)($_POST['id'] ?? 0);
	$text = trim($_POST['reply'] ?? '');

	if($id <= 0 || $text === '') throw new Exception('Missing Data');

	$database = new Database();

	$conn = $database->dbConnection();

	$st = $conn->prepare("SELECT * FROM contact_message WHERE id = ?");
	$st->execute([$id]);
	$msg = $st->fetch(PDO::FETCH_ASSOC);

	if(!$msg) throw new Exception('Message Not Found!');

	$toEmail = $msg['email'];
	$subject = 'Reply: '.($msg['subject'] ?: 'Your Inquery');

	//HTML email

	$html = '

				<div style="font-family: Arial; font-style: 14px; line-height: 1.6; color: #333;">
						<p>Hi '.htmlspecialchars($msg['name']).',</p>
						<p>'.nl2br(htmlspecialchars($text)).'</p>
						<hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
						<p style="font-size: 12px; color: #777;">This is a reply to your message submitted on '.htmlspecialchars($msg['created_at']).'.</p>
				</div>';

	$user = new USER();
	$ok = $user->sendMail($toEmail, $html, $subject);

	if(!$ok)
	{
		$err = $_SESSION['mailError'] ?? 'Email failed';
		throw new Exception($err);
	}

	$up = $conn->prepare("UPDATE contact_message SET is_replied = 1, reply_text = ?, replied_at = NOW() WHERE id = ?");
	$up->execute([$text, $id]);

	ob_end_clean();
	echo json_encode(['ok' => true]);

} 
catch (Throwable $e) 
{
	ob_end_clean();
	echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}

?>