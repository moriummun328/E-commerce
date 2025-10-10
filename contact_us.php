<?php
require_once __DIR__.'/partials/header.php';
require_once __DIR__.'/partials/navbar.php';
require_once __DIR__ .'/config/dbconfig.php';

$flag = $_GET['msg'] ?? '';
?>

<div class="container py-5">
  <div class="row g-4">
    
    <!-- Contact Form -->
    <div class="col-lg-7">
      <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body p-4">
          <h3 class="mb-4 text-dark"><i class="bi bi-envelope-fill me-2"></i> Contact Us</h3>
          
          <?php if($flag === 'ok'): ?>
            <div class="alert alert-success">✅ Thanks! Your message has been submitted.</div>
          <?php elseif($flag === 'err'): ?>
            <div class="alert alert-danger">❌ Something went wrong. Please try again.</div>
          <?php endif; ?>

          <form method="POST" action="save_contact.php" class="needs-validation" novalidate>
            <div class="mb-3">
              <label class="form-label">Your Name</label>
              <input type="text" name="name" class="form-control" required>
              <div class="invalid-feedback">Name is required.</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required>
              <div class="invalid-feedback">Valid email is required.</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Subject</label>
              <input type="text" name="subject" class="form-control" required>
              <div class="invalid-feedback">Enter your subject.</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Message</label>
              <textarea name="message" rows="5" class="form-control" required></textarea>
              <div class="invalid-feedback">Message cannot be empty.</div>
            </div>
<button class="btn btn-success w-100 custom-green">Send Message</button>


          </form>
        </div>
      </div>
    </div>

    <!-- Contact Info -->
    <div class="col-lg-5">
      <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body p-4">
          <h4 class="mb-3 text-dark"><i class="bi bi-geo-alt-fill me-2"></i> Our Office</h4>
          <p><strong>Address:</strong> 29 Purana Paltan, Noorjahan Sharif Plaza</p>
          <p><strong>Phone:</strong> +88 01768990060</p>
          <p><strong>Email:</strong> info@cogent.com</p>
        </div>
      </div>
    </div>

  </div>
</div>

<script type="text/javascript">
(function () {
  'use strict';
  var forms = document.getElementsByClassName('needs-validation');
  Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
</script>

<?php require_once __DIR__ .'/partials/footer.php'; ?>
