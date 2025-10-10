<?php
require_once __DIR__ .'/../config/class.user.php';

$user = new USER();

$msg = '';

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
	try 
	{
		$email = trim($_POST['email'] ?? '');
		$pass = $_POST['password'] ?? '';
		$user->login($email, $pass);
		$user->redirect($user->baseUrl ?? '/');

	} 
	catch (Exception $e) 
	{
		$msg = '<div class="alert alert-danger">'.htmlspecialchars($e->getMessage()).'</div>';
	}
}
?>

<?php require_once __DIR__.'/../partials/header.php'?>
<?php require_once __DIR__.'/../partials/navbar.php'?>

<div class="container mt-4">
	<h3>Login</h3>
	<?= $msg ?>
	<form method="post" class="col-md-4 p-0">
		<div class="form-group">
			<label>Email:</label>
			<input type="email" name="email" class="form-control" required>
		</div>

		<div class="form-group">
			<label>Password:</label>
			<input type="password" name="password" class="form-control" required>
		</div>

		<button class="btn btn-dark">Login</button>
		<a href="register.php" class="btn btn-link">Register</a>
		<a href="fpass.php" class="btn btn-link">Forgot password?</a>
	</form>
</div>

<?php require_once __DIR__.'/../partials/footer.php'?>