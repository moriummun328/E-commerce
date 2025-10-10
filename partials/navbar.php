<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$BASE = defined('BASE_URL') ? BASE_URL : ( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/ecommerce' );

$isLoggedIn = !empty($_SESSION['user_id']);
$userName   = $_SESSION['user_name'] ?? 'Account';

$cartCount = 0;
try 
{
    require_once __DIR__ . '/../admin/dbConfig.php';

    $categories=$DB_con->query("SELECT * FROM categories");

    if ($isLoggedIn && isset($DB_con)) 
    {
        $uid = (int)$_SESSION['user_id'];
        $st = $DB_con->prepare("SELECT id FROM carts WHERE user_id = ? AND status = 'open' LIMIT 1");
        $st->execute([$uid]);
        $cartId = $st->fetch(PDO::FETCH_ASSOC)['id'] ?? null;

        if ($cartId) 
        {
            $st = $DB_con->prepare("SELECT COALESCE(SUM(qty),0) AS c FROM cart_items WHERE cart_id = ?");
            $st->execute([$cartId]);
            $cartCount = (int)($st->fetch(PDO::FETCH_ASSOC)['c'] ?? 0);
        }
    } 
    else 
    {
        if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) 
        {
            foreach ($_SESSION['cart'] as $q) $cartCount += (int)$q;
        }
    }
} 
catch (Throwable $e) {}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Navbar with dark background -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <a class="navbar-brand font-weight-bold" href="<?= $BASE ?>/index.php">My Shop</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#topNav"
          aria-controls="topNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="topNav">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>/index.php">Home</a></li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="allProductsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="bi bi-grid mr-1"></i> All Products
        </a>
        <div class="dropdown-menu p-2" aria-labelledby="allProductsDropdown" style="min-width:220px; border-radius:8px;">
          <a class="dropdown-item" href="<?= $BASE ?>/allProducts.php">All Products</a>
          <div class="dropdown-divider"></div>
          <?php foreach ($categories as $cat): ?>
            <a class="dropdown-item" href="<?= $BASE ?>/allProducts.php?cat_id=<?= $cat['id'] ?>">
              <?= htmlspecialchars($cat['category_name']) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </li>

      <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>/about_us.php">About Us</a></li>
      <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>/contact_us.php">Contact</a></li>
    </ul>

    <!-- Search Form -->
    <form class="form-inline my-2 my-lg-0 mr-3" method="get" action="<?= $BASE ?>/search.php">
      <div class="input-group">
        <input class="form-control" type="search" placeholder="Search products..." name="q" aria-label="Search" required>
        <div class="input-group-append">
          <button class="btn btn-success" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </form>

    <ul class="navbar-nav">
      <?php if ($isLoggedIn): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="accMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-user-circle mr-1"></i> <?= htmlspecialchars($userName) ?>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="accMenu" style="border-radius:8px;">
            <a class="dropdown-item" href="#">My Profile</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="<?= $BASE ?>/auth/logout.php">Logout</a>
          </div>
        </li>
      <?php else: ?>
        <li class="nav-item">
          <button class="btn btn-success mr-2" data-toggle="modal" data-target="#loginModal">
            <i class="fas fa-sign-in-alt"></i> Login
          </button>
        </li>
        <li class="nav-item">
          <button class="btn btn-success" data-toggle="modal" data-target="#registerModal">
            <i class="fas fa-user-plus"></i> Register
          </button>
        </li>
      <?php endif; ?>

      <!-- Cart -->
      <li class="nav-item ml-2 position-relative">
        <a class="nav-link" href="<?= $BASE ?>/cart.php" title="View Cart">
          <i class="fas fa-shopping-cart fa-lg"></i>
          <span id="navCartCount" class="badge badge-pill badge-danger"
                style="display: <?= $cartCount > 0 ? 'inline-block' : 'none' ?>;
                       position:absolute; top:0; right:0; transform:translate(50%,-50%);
                       font-size:11px; min-width:20px; padding:2px 6px;">
            <?= (int)$cartCount ?>
          </span>
        </a>
      </li>
    </ul>
  </div>
</nav>

<style>
/* Navbar Custom Styling */
.navbar-nav .nav-link {
  transition: all 0.2s ease;
  border-radius: 6px;
  padding: 8px 12px;
}
.navbar-nav .nav-link:hover {
  background: rgba(255,255,255,0.1); /* subtle white hover for dark navbar */
  color: #ffc107 !important; /* yellow highlight on hover */
}
.dropdown-menu {
  border-radius: 8px;
}
.dropdown-item:hover {
  background: #e6f4ea; /* light green hover */
  color: #28a745; /* success green */
}
</style>

<?php if (!$isLoggedIn): ?>
<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form class="modal-content" method="post" action="<?= $BASE ?>/auth/login.php">
      <div class="modal-header">
        <h5 class="modal-title" id="loginTitle">Login to your account</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Email</label>
          <input name="email" type="email" class="form-control" required autocomplete="email">
        </div>
        <div class="form-group">
          <label>Password</label>
          <input name="password" type="password" class="form-control" required autocomplete="current-password">
        </div>
        <small>
          <a href="<?= $BASE ?>/auth/fpass.php">Forgot password?</a>
        </small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Login</button>
      </div>
    </form>
  </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form class="modal-content" method="post" action="<?= $BASE ?>/auth/register.php">
      <div class="modal-header">
        <h5 class="modal-title" id="registerTitle">Create an account</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Username</label>
          <input name="username" class="form-control" required autocomplete="username">
        </div>
        <div class="form-group">
          <label>Email</label>
          <input name="email" type="email" class="form-control" required autocomplete="email">
        </div>
        <div class="form-group">
          <label>Password</label>
          <input name="password" type="password" class="form-control" required autocomplete="new-password" minlength="6">
        </div>
        <div class="form-group">
          <label>Confirm Password</label>
          <input name="cpassword" type="password" class="form-control" required autocomplete="new-password" minlength="6">
        </div>
        <small class="text-muted">By creating an account, you agree to our terms.</small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Register</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- jQuery + Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>

<script> 
window.updateNavCartBadge = function(totalQty)
{
  var badge = document.getElementById('navCartCount');
  if (!badge) return;
  badge.textContent = (parseInt(totalQty,10) || 0);
};
</script>
