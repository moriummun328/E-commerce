<?php if(!defined('BOOTSTRAP_JS_LOADED')):?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<?php endif; ?>

<script type="text/javascript">
	(function (){

		function ensureBadge()
		{
			var $link = $('#menuOrders');
			var $b = $('#orderBadge');

			if($b.length === 0 && $link.length)
			{
				$b = $('<span>',{

					id: 'orderBadge',
					class: 'badge badge-danger badge-pill ml-auto',
					text: '0'
				}).css({

					display: 'none',
					fontSize: '.70rem',
					lineHeight: 1,
					padding: '.15rem, .35rem'
				});
				$link.append($b);
			}

			return $b;
		}

		function renderBadge(n)
		{
			var $b = ensureBadge();
			if(!$b.length) return;

			if(Number(n) > 0)
			{
				$b.text(n).css('display','inline-block');
			}

			else
			{
				$b.text('0').hide();
			}
		}

		function pollOrderBadge()
		{
			$.ajax({

				url: '/ecommerce/admin/ajax/order_count.php',
				method: 'GET',
				dataType: 'json'
			}).done(function(res){
				if(res && res.ok)
				{
					renderBadge(res.new || 0);
				}

				else
				{
					renderBadge(0);
				}
			}).fail(function(xhr){

				console.error('Order_count.php failed', xhr.status, xhr.responseText);
				renderBadge(0);
			}).always(function(){

				setTimeout(pollOrderBadge, 8000);
			});
		}

		$(pollOrderBadge);

		$(document).on('click', '#menuOrders', function(){

			$.post('ajax/mark_orders_seen.php').always(function(){ renderBadge(0); })
		});
	})();
</script>

</body>
</html>