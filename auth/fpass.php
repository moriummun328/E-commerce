<?php
require_once __DIR__ .'/../config/class.user.php';

$user = new USER();

$msg = '';

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$email = trim($_POST['email'] ?? '');
	$user->requestPasswordReset($email);
	$msg = '<div class="alert alert-success">If the email exists, a reset link has been sent.</div>';
}

?>

<?php require_once __DIR__.'/../partials/header.php'?>
<?php require_once __DIR__.'/../partials/navbar.php'?>

<div class="container mt-4">
	<h3>Forgot password</h3>
	<?= $msg ?>

	<form method="POST" class="col-md-5 p-0">
		<div class="form-group">
			<label>Email:</label>
			<input type="email" name="email" class="form-control" required>
		</div>
		<button class="btn btn-primary">Send Reset Link</button>
	</form>
</div>

<?php require_once __DIR__.'/../partials/footer.php'?>