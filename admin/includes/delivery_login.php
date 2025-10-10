<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../dbConfig.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $DB_con->prepare("SELECT * FROM delivery_men WHERE email = ?");
    $stmt->execute([$email]);
    $delivery = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($delivery && password_verify($password, $delivery['password'])) {
        $_SESSION['delivery_man_id'] = $delivery['id'];
        header("Location: delivery_profile.php");
        exit;
    } else {
        $message = "<div class='alert alert-danger'>Invalid Email or Password</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delivery Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <h4>Delivery Man Login</h4>
    <?= $message ?>

    <form method="POST">
        <div class="form-group mb-2">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="form-group mb-2">
            <label>Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>

</body>
</html>
