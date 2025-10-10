<?php
if(session_status() === PHP_SESSION_NONE) session_start();

$msg = $_SESSION['flash'] ?? 'Thank you';

unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Thank You</title>
	<link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
	<div class="container">
		<div class="alert alert-success">
			<?= $msg ?>
			<a href="index.php" class="btn btn-primary">Back to Home</a>
		</div>
	</div>
</body>
</html>