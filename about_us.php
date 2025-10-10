<?php 
require_once __DIR__.'/partials/header.php';
require_once __DIR__.'/partials/navbar.php';
require_once __DIR__ .'/config/dbconfig.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - MyShop</title>

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f9f9f9; /* পুরো পেজের হালকা ব্যাকগ্রাউন্ড */
    }
    /* Hero Section */
    .hero-section {
      background: linear-gradient(135deg, #e0f7fa 0%, #f1f8ff 100%);
      color: #333;
      padding: 50px 0; /* কমানো spacing */
    }
    .hero-section h1 {
      font-size: 2.5rem; /* কমানো font-size */
      font-weight: bold;
      margin-bottom: 10px;
    }
    .hero-section p {
      font-size: 1.1rem; /* compact paragraph */
      margin-top: 5px;
    }
    .section-title {
      font-weight: 700;
      margin-bottom: 20px;
      color: #343a40;
    }
    .highlight {
      color: grey;
    }
    .team-member img {
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .team-member h5 {
      margin-top: 10px;
    }
    .card {
      border: none;
      border-radius: 12px;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    }
  </style>
</head>
<body>

<!-- Hero Section -->
<section class="hero-section text-center">
  <div class="container">
    <h1>We Are <span class="highlight">MyShop</span></h1>
    <p class="lead">Redefining the online shopping experience for you</p>
  </div>
</section>

<!-- About Section as Cards -->
<section class="py-5">
  <div class="container">
    <h2 class="section-title text-center mb-5">About <span class="highlight">MyShop</span></h2>
    <div class="row g-4">
      
      <!-- Who We Are Card -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="card-title text-primary">Who We Are</h5>
            <p class="card-text">
              MyShop is one of the fastest-growing e-commerce platforms in the region. Founded in 2020, we are committed to delivering high-quality products at unbeatable prices.
            </p>
          </div>
        </div>
      </div>

      <!-- Our Mission Card -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="card-title text-success">Our Mission</h5>
            <p class="card-text">
              To simplify shopping for everyone and make quality accessible through innovation, speed, and transparency.
            </p>
          </div>
        </div>
      </div>

      <!-- Our Vision Card -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="card-title text-warning">Our Vision</h5>
            <p class="card-text">
              To become the region's most customer-centric online marketplace by combining smart technology with great service.
            </p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Team Section -->
<section class="bg-light py-5">
  <div class="container">
    <h2 class="section-title text-center mb-4">Our Leadership Team</h2>
    <div class="row text-center">
      <div class="col-md-4 mb-4">
        <div class="team-member">
          <img src="assets/images/66.jpg" class="img-fluid rounded-circle mb-3" alt="CEO" style="width:200px; height:200px; object-fit:cover;">
          <h5>Rahim Uddin</h5>
          <p class="text-muted">Founder & CEO</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="team-member">
          <img src="assets/images/65.jpg" class="img-fluid rounded-circle mb-3" alt="CTO" style="width:200px; height:200px; object-fit:cover;">
          <h5>Ayesha Akter</h5>
          <p class="text-muted">Chief Technology Officer</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="team-member">
          <img src="assets/images/68.jpg" class="img-fluid rounded-circle mb-3" alt="Manager" style="width:200px; height:200px; object-fit:cover;">
          <h5>Salman Hossain</h5>
          <p class="text-muted">Operations Manager</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__.'/partials/footer.php'?>
</body>
</html>
