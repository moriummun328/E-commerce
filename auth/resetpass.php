<?php
require_once __DIR__ .'/../config/class.user.php';

$user = new USER();


$BASE = defined('BASE_URL') ? BASE_URL : ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http').'://'.($_SERVER['HTTP_HOST'] ?? 'localhost').'/ecommerce');

$msg = '';

$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
	try 
	{
		$new = $_POST['password'] ?? '';
		$cnf = $_POST['cpassword'] ?? '';

		if(!$token || !$email)
		{
			throw new Exception('Invalid Reset Link');
		}

		if(strlen($new) < 6)
		{
			throw new Exception('Password Must be at least 6 Characters');
		}

		if($new !== $cnf)
		{
			throw new Exception('Password Do Not Mathch!');
		}

		$user->resetPassword($email, $token, $new);
		$msg = '<div class="alert alert-success mb-3">Password Successfully Re-set. You can now login</div>';
	} 
	catch (Exception $e) 
	{
		$msg = '<div class="alert alert-danger mb-3">'.htmlspecialchars($e->getMessage()).'</div>';
	}
}

?>

<?php require_once __DIR__.'/../partials/header.php'?>
<?php require_once __DIR__.'/../partials/navbar.php'?>

<div class="container mt-4" style="max-width: 720px;">
	<div class="card">
		<div class="card-body">
			<h4 class="mb-3">Reset Password</h4>

			<?php

				if($msg):?>
					<?= $msg ?>
					<?php if(strpos($msg, 'Successfully') !== false):?>
						<a href="<?= $BASE ?>/auth/login.php" class="btn btn-primary">Go to Login</a>
						<a href="<?= $BASE ?>/index.php" class="btn btn-link">Back to Home</a>
					<?php endif; ?>
				<?php endif; ?>

				<?php if(!$msg || strpos($msg, 'Successfully') === false): ?>

					<?php if($token & $email): ?>
						<form method="POST" class="col-md-6 p-0">
							<div class="form-group">
								<label>New Password:</label>
								<input type="password" name="password" class="form-control" required minlength="6" autocomplete="new-password">
							</div>
							<div class="form-group">
								<label>Confirm Password:</label>
								<input type="password" name="cpassword" class="form-control" required minlength="6" autocomplete="new-password">
							</div>

							<button class="btn btn-success">Reset</button>
						</form>
						<?php else: ?>
							<div class="alert alert-warning mb-0">
								Invalid or missing reset link. Please request a new link from the <a href="<?= $BASE ?>/auth/fpass.php">Forgot Password</a>Page.
							</div>
						<?php endif; ?>
					<?php endif; ?>
		</div>
	</div>
</div>

<?php require_once __DIR__.'/../partials/footer.php'?>