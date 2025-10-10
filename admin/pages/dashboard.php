<?php
// Session & DB config
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__.'/../dbConfig.php';

// Counts
$totalCupons   = $DB_con->query("SELECT COUNT(*) FROM coupons")->fetchColumn();
$totalUsers    = $DB_con->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalProducts = $DB_con->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders   = $DB_con->query("SELECT COUNT(*) FROM orders")->fetchColumn();

$totalDeliveredOrders = $DB_con->query("SELECT COUNT(*) FROM orders WHERE status='delivered'")->fetchColumn();
$totalCompleteOrders  = $DB_con->query("SELECT COUNT(*) FROM orders WHERE status='completed'")->fetchColumn();
$totalCancelOrders    = $DB_con->query("SELECT COUNT(*) FROM orders WHERE status='canceled'")->fetchColumn();
$totalPendingOrders   = $DB_con->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn();

$totalMessages = $DB_con->query("SELECT COUNT(*) FROM contact_message")->fetchColumn();

// Latest Data
$latestOrders = $DB_con->query("SELECT id,global_order_id,user_id,total_amount,status,order_date FROM orders ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$latestUsers  = $DB_con->query("SELECT id,username,email,create_at FROM users ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$latestMsgs   = $DB_con->query("SELECT id,name,email,subject,created_at FROM contact_message ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Chart Data
$orderStats = $DB_con->query("SELECT DATE_FORMAT(order_date,'%b') as month, COUNT(*) as c FROM orders GROUP BY month ORDER BY order_date ASC")->fetchAll(PDO::FETCH_ASSOC);
$months = array_column($orderStats, 'month');
$orderCounts = array_column($orderStats, 'c');

$userStats = $DB_con->query("SELECT DATE_FORMAT(create_at,'%b') as month, COUNT(*) as c FROM users GROUP BY month ORDER BY create_at ASC")->fetchAll(PDO::FETCH_ASSOC);
$userMonths = array_column($userStats, 'month');
$userCounts = array_column($userStats, 'c');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body { background-color: #f5f6fa; }
    .card { border-radius: 12px; }
    .summary-card i { font-size: 2rem; }
    .table thead { background: #212529; color: #fff; }
    .navbar { box-shadow: 0 2px 5px rgba(0,0,0,.1); }
  </style>
</head>
<body>

<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#"><i class="fas fa-tachometer-alt"></i> Admin Panel</a>
    <div class="d-flex">
      <a href="logout.php" class="btn btn-sm btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </div>
</nav>

<div class="container-fluid p-4">
  <!-- Summary Cards -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3">
        <i class="fas fa-users text-primary"></i>
        <h6 class="text-muted mt-2">Users</h6>
        <h4><?= $totalUsers ?></h4>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3">
        <i class="fas fa-box text-success"></i>
        <h6 class="text-muted mt-2">Products</h6>
        <h4><?= $totalProducts ?></h4>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3">
        <i class="fas fa-shopping-cart text-info"></i>
        <h6 class="text-muted mt-2">Orders</h6>
        <h4><?= $totalOrders ?></h4>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3">
        <i class="fas fa-clock text-warning"></i>
        <h6 class="text-muted mt-2">Pending Orders</h6>
        <h4><?= $totalPendingOrders ?></h4>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3">
        <i class="fas fa-truck text-info"></i>
        <h6 class="text-muted mt-2">Delivered</h6>
        <h4><?= $totalDeliveredOrders ?></h4>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3">
        <i class="fas fa-check-circle text-success"></i>
        <h6 class="text-muted mt-2">Completed</h6>
        <h4><?= $totalCompleteOrders ?></h4>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3">
        <i class="fas fa-times-circle text-danger"></i>
        <h6 class="text-muted mt-2">Canceled</h6>
        <h4><?= $totalCancelOrders ?></h4>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3">
        <i class="fas fa-ticket-alt text-secondary"></i>
        <h6 class="text-muted mt-2">Coupons</h6>
        <h4><?= $totalCupons ?></h4>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3">
        <i class="fas fa-envelope text-danger"></i>
        <h6 class="text-muted mt-2">Messages</h6>
        <h4><?= $totalMessages ?></h4>
      </div>
    </div>
  </div>

  <!-- Latest Orders -->
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white"><i class="fas fa-receipt"></i> Latest Orders</div>
    <div class="card-body table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr><th>ID</th><th>User</th><th>Total</th><th>Status</th><th>Date</th></tr>
        </thead>
        <tbody>
          <?php foreach($latestOrders as $o): ?>
          <tr>
            <td><?= $o['global_order_id'] ?></td>
            <td><?= $o['user_id'] ?></td>
            <td>$<?= $o['total_amount'] ?></td>
            <td><span class="badge bg-info"><?= $o['status'] ?></span></td>
            <td><?= $o['order_date'] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Recent Users -->
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white"><i class="fas fa-user-plus"></i> Recent Users</div>
    <div class="card-body table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr><th>ID</th><th>Username</th><th>Email</th><th>Joined</th></tr>
        </thead>
        <tbody>
          <?php foreach($latestUsers as $u): ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= $u['create_at'] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Latest Messages -->
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white"><i class="fas fa-envelope-open"></i> Latest Messages</div>
    <div class="card-body table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr><th>ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Date</th></tr>
        </thead>
        <tbody>
          <?php foreach($latestMsgs as $m): ?>
          <tr>
            <td><?= $m['id'] ?></td>
            <td><?= htmlspecialchars($m['name']) ?></td>
            <td><?= htmlspecialchars($m['email']) ?></td>
            <td><?= htmlspecialchars($m['subject']) ?></td>
            <td><?= $m['created_at'] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Charts -->
  <div class="row g-3">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-dark text-white"><i class="fas fa-chart-line"></i> Orders per Month</div>
        <div class="card-body"><canvas id="ordersChart"></canvas></div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-dark text-white"><i class="fas fa-chart-bar"></i> User Registrations</div>
        <div class="card-body"><canvas id="usersChart"></canvas></div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Orders Chart
new Chart(document.getElementById('ordersChart'), {
  type: 'line',
  data: {
    labels: <?= json_encode($months) ?>,
    datasets: [{
      label: 'Orders',
      data: <?= json_encode($orderCounts) ?>,
      borderColor: 'rgba(75,192,192,1)',
      backgroundColor: 'rgba(75,192,192,0.2)',
      fill: true,
      tension: 0.3
    }]
  }
});

// Users Chart
new Chart(document.getElementById('usersChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($userMonths) ?>,
    datasets: [{
      label: 'New Users',
      data: <?= json_encode($userCounts) ?>,
      backgroundColor: 'rgba(54,162,235,0.6)'
    }]
  }
});
</script>
</body>
</html>
