<?php
require_once __DIR__ .'/../config/class.user.php';

$user = new USER();

$BASE = defined('BASE_URL') ? BASE_URL : ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http'). '://'.($_SERVER['HTTP_HOST'] ?? 'localhost').'/ecommerce');

$msg = '';

$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

try 
{
	if(!$token || !$email)
	{
		throw new Exception('Invalid verification link');
	}

	//Verify
	$user->verify($email, $token);
	$msg = '<div classs="alert alert-success mb-3">Your account has been verified. You can now log in.</div>';
} 
catch (Exception $e) 
{
	$msg = '<div classs="alert alert-danger mb-3">'.htmlspecialchars($e->getMessage()).'</div>';
}
?>

<?php require_once __DIR__.'/../partials/header.php'?>
<?php require_once __DIR__.'/../partials/navbar.php'?>

<div class="container mt-4" style="max-width: 720px;">
	<div class="card">
		<div class="card-body">
			<h4 class="mb-3">Email Verification</h4>
			<?= $msg ?>
			<a class="btn btn-primary" href="<?= $BASE ?>/auth/login.php">Go to Login</a>
			<a href="<?= $BASE ?>/index.php" class="btn btn-link">Home</a>
		</div>
	</div>
</div>

<?php require_once __DIR__.'/../partials/footer.php'?>
