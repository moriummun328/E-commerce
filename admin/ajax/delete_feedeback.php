<?php
if(session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

ini_set('display_errors', 0);

error_reporting(E_ERROR | E_PARSE);

if(empty($_SESSION['admin_logged_in']))
{
	echo json_encode(['ok' => false, 'error' => 'Unauthorised']);
	exit;
}

require_once __DIR__.'/../../config/dbconfig.php';

try 
{
	$raw = file_get_contents('php://input');
	$data = json_decode($raw, true);

	if(!is_array($data)) $data = [];

	$ids = $data['ids'] ?? [];
	if(!is_array($ids) || empty($ids))
	{
		throw new Exception('No Messages Selected');
	}

	$ids = array_values(array_filter(array_map('intval', $ids), function($v){

		return $v > 0;
	}));

	if(empty($ids))
	{
		throw new Exception('Invalid IDs');
	}

	$database = new Database();
	$conn = $database->dbConnection();

	$placeholders = implode(',', array_fill(0, count($ids), '?'));
	$sql = "DELETE FROM contact_message WHERE id IN($placeholders)";
	$stmt = $conn->prepare($sql);
	$stmt->execute($ids);

	echo json_encode(['ok' => true, 'deleted' => $stmt->rowCount()]);
} 
catch (Throwable $e) 
{
	echo json_encode(['ok' => false, 'error'=> $e->getMessage()]);
}

?>