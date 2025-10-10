<?php
require_once __DIR__ .'/../config/class.user.php';
$user = new USER();

$msg = '';

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
	try 
	{
		$uname = $_POST['username'];
		$email = $_POST['email'];
		$pass = $_POST['password'];
		$cpass = $_POST['cpassword'];

		if($pass !== $cpass)
		{
			throw new Exception("Password does not match");
		}

		$user->register($uname, $email, $pass);
		$msg = '<div classs="alert alert-success">Registration Successfull. Please check your email to verify.</div>';

	} 
	catch (Exception $e) 
	{
		$msg = '<div classs="alert alert-danger">'.htmlspecialchars($e->getMessage()).'</div>';
	}
}


?>

<?php require_once __DIR__.'/../partials/header.php'?>
<?php require_once __DIR__.'/../partials/navbar.php'?>

<div class="container mt-4">
	<h3>Create an Account</h3>
	<?= $msg; ?>

	<form method="post" class="col-md-5 p-0">
		<div class="form-group">
			<label>Username</label>
			<input name="username" class="form-control" required>
		</div>

		<div class="form-group">
			<label>Email</label>
			<input type="email" name="email" class="form-control" required>
		</div>
		<div class="form-group">
			<label>Password</label>
			<input type="password" name="password" class="form-control" required>
		</div>
		<div class="form-group">
			<label>Confirm Password</label>
			<input type="password" name="cpassword" class="form-control" required>
		</div>
		<button class="btn btn-primary">Register</button>
		<a href="login.php" class="btn btn-link">Login</a>
	</form>
</div>

<?php require_once __DIR__.'/../partials/footer.php'; ?>