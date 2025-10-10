<footer class="bg-light text-dark pt-5 pb-4 mt-5 shadow-sm">
	<div class="container">
		<div class="row">
			
			<!--Column-->
			<div class="col-md-4 col-sm-12 mb-4">
				<h5 class="text-uppercase mb-3">About Us</h5>
				<p>
					Welcome to our eCommerce store. We provide the best quality products at affordable price. Your satisfaction is our top priority.
				</p>
			</div>

			<!--Column-->
			<div class="col-md-4 col-sm-12 mb-4">
				<h5 class="text-uppercase mb-3">Quick Links</h5>
				<ul class="list-unstyled">
					<li><a href="index.php" class="text-dark text-decoration-none">Home</a></li>
					<li><a href="all_products.php" class="text-dark text-decoration-none">All Products</a></li>
					<li><a href="about.php" class="text-dark text-decoration-none">About Us</a></li>
					<li><a href="contact.php" class="text-dark text-decoration-none">Contact Us</a></li>
					<li><a href="login.php" class="text-dark text-decoration-none">Login</a></li>
				</ul>
			</div>

			<!--Column 3-->
			<div class="col-md-4 col-sm-12 mb-4">
				<h5 class="text-uppercase mb-3">Contact Us</h5>
				<p><i class="fa fa-map-marker-alt text-danger mr-2"></i>29, Purana Paltan, Noorjahan Sharif Plaza</p>
				<p><i class="fa fa-phone text-success mr-2"></i>+88-01711542258</p>
				<p><i class="fa fa-envelope text-primary mr-2"></i>info@cogent.com</p>

				<div class="mt-3">
					<a href="#" class="mr-3"><i class="fab fa-facebook fa-lg" style="color:#1877F2;"></i></a>
					<a href="#" class="mr-3"><i class="fab fa-twitter fa-lg" style="color:#1DA1F2;"></i></a>
					<a href="#" class="mr-3"><i class="fab fa-instagram fa-lg" style="color:#E1306C;"></i></a>
					<a href="#" class="mr-3"><i class="fab fa-linkedin fa-lg" style="color:#0077B5;"></i></a>
				</div>
			</div>
		</div>

		<hr>
		<div class="text-center">
			<p class="mb-0 text-secondary">&copy; <?php echo date("Y"); ?> Cogent. All Rights Reserved.</p>
		</div>
	</div>
</footer>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js" integrity="sha512-igl8WEUuas9k5dtnhKqyyld6TzzRjvMqLC79jkgT3z02FvJyHAuUtyemm/P/jYSne1xwFI06ezQxEwweaiV7VA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- Product Load Script -->
<script>
	function loadProducts(catIds = [])
	{
		$.ajax({
			url: 'fetch_products.php',
			method: 'POST',
			data: { categories: catIds },
			success: function(html)
			{
				$('#productGrid').html(html);
			}
		});
	}

	$(function()
	{
		const checked = [];

		$('.cat-check:checked').each(function(){checked.push($(this).val()); });

		loadProducts(checked);

		$(document).on('change','.cat-check', function(){
			const ids = [];
			$('.cat-check:checked').each(function(){ids.push($(this).val()); });
			loadProducts(ids);	
		});

		$('#clearFilter').on('click', function(){
			$('.cat-check').prop('checked', false);
			loadProducts([]);
		});

		const qp = new URLSearchParams(window.location.search);
		if(qp.get('view') == 'all'){loadProducts([]);}
	});
</script>

<!-- Login Alert Script -->
<script type="text/javascript">
	document.addEventListener('click', function(e){
       var btn = e.target.closest('.login-alert-btn');
       if(!btn) return;
       e.preventDefault();
       e.stopPropagation();

       var card = btn.closest('.product-card') || btn.closest('.card') || document;
       var old = card.querySelector('.login-alert-inline');
       if(old) old.remove();

       var div = document.createElement('div');
       div.className = 'alert alert-warning alert-dismissable fade show login-alert-inline';
       div.setAttribute('role','alert');
       div.style.margin = '8px 8px 0';

       div.innerHTML = 'Please login first to add items to cart.' + 
       	'<button type="button" class="close" data-dismiss="alert" aria-label="Close">' + 
       	'<span>&times;</span>' +'</button>';

       if(card.firstChild){
       	  card.insertBefore(div, card.firstChild);
       }
       else{
       	  card.appendChild(div);
       }
	}, true);
</script>
