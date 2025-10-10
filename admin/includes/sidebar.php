<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      overflow-x: hidden;
    }
    #sidebar {
      width: 250px;
      height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      background-color: #212529;
      color: white;
      overflow-y: auto;
    }
    #sidebar .nav-link {
      color: #fff;
    }
    #sidebar .nav-link:hover {
      background-color: rgba(255,255,255,.1);
      border-radius: 4px;
    }
    #content {
      margin-left: 250px;
      padding: 20px;
    }
    /* Mobile responsive */
    @media (max-width: 992px) {
      #sidebar {
        margin-left: -250px;
        transition: margin .3s;
      }
      #sidebar.active {
        margin-left: 0;
      }
      #content {
        margin-left: 0;
      }
    }
    /* Scrollbar style */
    #sidebar::-webkit-scrollbar {
      width: 6px;
    }
    #sidebar::-webkit-scrollbar-thumb {
      background: rgba(255,255,255,.3);
      border-radius: 10px;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div id="sidebar" class="bg-dark p-3">
  <div class="text-center mb-4">
    <?php
      if(!isset($_SESSION)) session_start();
      require_once __DIR__.'/../dbConfig.php';
      $admin_id = $_SESSION['admin_logged_in'] ?? null;

      $adminPhoto = 'default.jpg';
      $adminName = 'Admin';

      if($admin_id) {
        $stmt = $DB_con->prepare("SELECT * FROM admins WHERE id =?");
        $stmt->execute([$admin_id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if($admin) {
          $adminPhoto = !empty($admin['photo']) ? $admin['photo'] : 'default.jpg';
          $adminName = $admin['username'];
        }
      }
    ?>
    <img src="../uploads/admins/<?= htmlspecialchars($adminPhoto) ?>" 
         class="rounded-circle mb-2" width="80" height="80" alt="Admin Photo">
    <h5><?= htmlspecialchars($adminName) ?></h5>
  </div>

  <h4 class="mb-3">Admin Panel</h4>
  <ul class="nav flex-column">
    <li class="nav-item"><a href="index.php?page=dashboard" class="nav-link">🏠 Dashboard</a></li>
    <li class="nav-item"><a href="index.php?page=products" class="nav-link">📦 Products</a></li>
    <li class="nav-item"><a href="index.php?page=categories" class="nav-link">📂 Categories</a></li>
    <li class="nav-item"><a href="index.php?page=attributes" class="nav-link">⚙ Attributes</a></li>
    <li class="nav-item"><a href="index.php?page=coupons" class="nav-link">🎟 Coupons</a></li>

    <!-- Inventory Dropdown -->
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#inventoryMenu" role="button">
        📊 Inventory
      </a>
      <div class="collapse" id="inventoryMenu">
        <ul class="nav flex-column ms-3">
          <li><a href="index.php?page=stock_in" class="nav-link">Stock In</a></li>
          <li><a href="index.php?page=stock_out" class="nav-link">Stock Out</a></li>
          <li><a href="index.php?page=stock_by_products" class="nav-link">Stock by Products</a></li>
          <li><a href="index.php?page=inventory_report" class="nav-link">Reports</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item d-flex align-items-center justify-content-between">
      <a href="index.php?page=orders" class="nav-link">🛒 Orders</a>
      <span id="orderBadge" class="badge bg-danger me-3" style="display:none;">0</span>
    </li>

    <li class="nav-item"><a href="index.php?page=delivery_men" class="nav-link">🚚 Delivery Men</a></li>
     <li class="nav-item"><a href="index.php?page=delivery_profile" class="nav-link">👤 Delivery Profile</a></li>
    <li class="nav-item d-flex align-items-center justify-content-between">
      <a href="index.php?page=feedback" class="nav-link">💬 User Feedback</a>
      <span id="fbCount" class="badge bg-danger me-3">0</span>
    </li>
    <li class="nav-item"><a href="index.php?page=admin_profile" class="nav-link">👤 Change Profile</a></li>
    <li class="nav-item"><a href="index.php?page=daily_report" class="nav-link">📅 Daily Report</a></li>
    <li class="nav-item"><a href="logout.php" class="nav-link text-danger">🚪 Logout</a></li>
  </ul>
</div>

<!-- Toggle button for mobile -->
<button class="btn btn-dark d-lg-none m-2" id="menu-toggle">☰ Menu</button>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  // Sidebar toggle
  document.getElementById("menu-toggle").addEventListener("click", function() {
    document.getElementById("sidebar").classList.toggle("active");
  });

  // Feedback Poll
  (function pollFedback(){
    $.ajax({
      url: 'ajax/feedback_count.php',
      method: 'get',
      dataType: 'json'
    }).done(function(d){
      $('#fbCount').text((d && d.count) ? d.count : 0);
    }).always(function(){
      setTimeout(pollFedback, 2000);
    });
  })();
</script>
</body>
</html>
