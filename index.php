<?php
require_once 'partials/header.php';
require_once 'partials/navbar.php';
?>
<style>
  /* পুরো পেজের জন্য light gradient background */
  body {
    background: linear-gradient(to right, #f8f9fa, #e3f2fd);
    min-height: 100vh;
  }

  /* Carousel image fix */
  .slider-img {
    height: 450px;
    object-fit: cover;
    border-radius: 10px;
  }

  /* Carousel caption background */
  .carousel-caption {
    background: rgba(0, 0, 0, 0.5);
    border-radius: 8px;
    padding: 10px 15px;
  }

  /* Product card design */
  .product-card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    background: #ffffff;
  }

  .product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.15);
  }

  .product-card img {
    height: 220px;
    object-fit: cover;
    border-bottom: 1px solid #ddd;
  }

  .product-card .card-body {
    text-align: center;
  }
</style>

<div class="container-fluid mt-4">
  <div class="row">
    <!--Sidebar-->
    <div class="col-md-3 col-lg-2">
      <?php require_once 'partials/sidebar.php'; ?>
    </div>

    <!--Main content-->
    <div class="col-md-9 col-lg-10">

      <!--Carousel-->
      <div id="carouselExampleIndicators" class="carousel slide mb-4" data-ride="carousel">
        <ol class="carousel-indicators">
          <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
          <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
          <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner rounded shadow">
          <div class="carousel-item active">
            <img src="assets/images/3.jpg" class="d-block w-100 slider-img" alt="slide1">
            <div class="carousel-caption d-none d-md-block">
              <h4 class="fw-bold text-white">Welcome to My Shop</h4>
              <p>Best Products, Best Prices</p>
            </div>
          </div>
          <div class="carousel-item">
            <img src="assets/images/2.jpg" class="d-block w-100 slider-img" alt="slide2">
            <div class="carousel-caption d-none d-md-block">
              <h4 class="fw-bold text-white">Fresh Arrivals</h4>
              <p>Check out the Latest!</p>
            </div>
          </div>
          <div class="carousel-item">
            <img src="assets/images/7.jpg" class="d-block w-100 slider-img" alt="slide3">
            <div class="carousel-caption d-none d-md-block">
              <h4 class="fw-bold text-white">Hot Deals!</h4>
              <p>Don't miss out Today</p>
            </div>
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-target="#carouselExampleIndicators" data-slide="prev">
          <span class="carousel-control-prev-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
          <span class="sr-only">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-target="#carouselExampleIndicators" data-slide="next">
          <span class="carousel-control-next-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
          <span class="sr-only">Next</span>
        </button>
      </div>

      <!--Products-->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 fw-bold">Products</h4>
        <small class="text-muted">Filter by categories from left</small>
      </div>

      <div id="productGrid" class="row">
        <!-- Example Product Card -->
        <div class="col-md-4 col-lg-3 mb-4">
          <div class="card product-card">
            <img src="assets/images/3.jpg" class="card-img-top" alt="Product 1">
            <div class="card-body">
              <h6 class="card-title">Product Name</h6>
              <p class="text-muted mb-1">$120</p>
              <button class="btn btn-sm btn-primary">Add to Cart</button>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-lg-3 mb-4">
          <div class="card product-card">
            <img src="assets/images/2.jpg" class="card-img-top" alt="Product 2">
            <div class="card-body">
              <h6 class="card-title">Another Product</h6>
              <p class="text-muted mb-1">$95</p>
              <button class="btn btn-sm btn-primary">Add to Cart</button>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-lg-3 mb-4">
          <div class="card product-card">
            <img src="assets/images/7.jpg" class="card-img-top" alt="Product 3">
            <div class="card-body">
              <h6 class="card-title">Hot Deal</h6>
              <p class="text-muted mb-1">$75</p>
              <button class="btn btn-sm btn-primary">Add to Cart</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once 'partials/footer.php'; ?>
