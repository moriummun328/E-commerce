<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id'])) { header("Location: auth/login.php"); exit; }

require_once __DIR__ . '/admin/dbConfig.php';

$BASE = defined('BASE_URL') ? BASE_URL : ( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/ecommerce' );


$userId = (int)$_SESSION['user_id'];
$st = $DB_con->prepare("SELECT id FROM carts WHERE user_id = ? AND status = 'open' LIMIT 1");
$st->execute([$userId]);
$cartId = ($st->fetch(PDO::FETCH_ASSOC)['id'] ?? null);

$items = [];
$grand = 0.00;

if ($cartId) 
{
  $sql = "SELECT ci.id AS item_id, ci.product_id, ci.qty, ci.unit_price,
                 p.product_name, p.product_image
          FROM cart_items ci
          JOIN products p ON p.id = ci.product_id
          WHERE ci.cart_id = ?";

  $st = $DB_con->prepare($sql);
  $st->execute([$cartId]);
  $items = $st->fetchAll(PDO::FETCH_ASSOC);

  foreach ($items as $it) 
  {
    $grand += ((float)$it['unit_price'] * (int)$it['qty']);
  }
}


if (file_exists(__DIR__ . '/partials/header.php')) include __DIR__ . '/partials/header.php';
if (file_exists(__DIR__ . '/partials/navbar.php')) include __DIR__ . '/partials/navbar.php';
?>
<!doctype html>
<html lang="en">
<head>
<?php if (!defined('BOOTSTRAP_LOADED')): // fallback if header didn't load CSS ?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<?php endif; ?>
  <title>My Cart</title>
  <style>
    .cart-page { padding: 24px 0; }
    .cart-card { border: 1px solid #e9ecef; border-radius: .5rem; overflow: hidden; }
    .cart-head { background: #f8f9fa; padding: .75rem 1rem; font-weight: 600; }
    .cart-img { width: 64px; height: 64px; object-fit: cover; border-radius: .25rem; }
    .qty { width: 96px; }
    .summary-card { position: sticky; top: 16px; }
    .empty-card { border: 1px dashed #ced4da; }
    .table td, .table th { vertical-align: middle; }
    .remove { width: 36px; height: 36px; line-height: 1; }
  </style>
</head>
<body>

<div class="container cart-page">
  <div class="row">
    <div class="col-lg-8 mb-4">
      <div class="cart-card">
        <div class="cart-head d-flex align-items-center justify-content-between">
          <div>Shopping Cart</div>
          <a href="<?= htmlspecialchars($BASE) ?>/index.php" class="btn btn-sm btn-outline-secondary">Continue Shopping</a>
        </div>

        <?php if (empty($items)): ?>
          <div class="p-4">
            <div class="card empty-card text-center p-5">
              <h5 class="mb-2">Your cart is empty</h5>
              <p class="text-muted mb-4">Add some products to see them here.</p>
              <a class="btn btn-primary" href="<?= htmlspecialchars($BASE) ?>/index.php">Browse Products</a>
            </div>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table mb-0">
              <thead class="thead-light">
                <tr>
                  <th style="width:88px;">Image</th>
                  <th>Product</th>
                  <th class="text-right" style="width:130px;">Unit Price</th>
                  <th style="width:140px;">Qty</th>
                  <th class="text-right" style="width:140px;">Total</th>
                  <th class="text-center" style="width:80px;">Remove</th>
                </tr>
              </thead>
              <tbody id="cartBody">
                <?php foreach ($items as $it): 
                  $img = (!empty($it['product_image']) && file_exists(__DIR__ . '/admin/uploads/' . $it['product_image']))
                          ? 'admin/uploads/' . $it['product_image']
                          : 'assets/images/placeholder.png';
                  $line = (float)$it['unit_price'] * (int)$it['qty'];
                ?>
                <tr data-item-id="<?= (int)$it['item_id'] ?>">
                  <td><img src="<?= htmlspecialchars($img) ?>" class="cart-img" alt=""></td>
                  <td>
                    <div class="font-weight-500"><?= htmlspecialchars($it['product_name']) ?></div>
                  </td>
                  <td class="text-right">$ <span class="unit"><?= number_format($it['unit_price'],2) ?></span></td>
                  <td>
                    <input type="number" class="form-control form-control-sm qty" min="1"
                           value="<?= (int)$it['qty'] ?>">
                  </td>
                  <td class="text-right">$ <span class="line-total"><?= number_format($line,2) ?></span></td>
                  <td class="text-center">
                    <button class="btn btn-sm btn-outline-danger remove" title="Remove">&times;</button>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="4" class="text-right">Grand Total</th>
                  <th class="text-right">$ <span id="grandTotal"><?= number_format($grand,2) ?></span></th>
                  <th></th>
                </tr>
              </tfoot>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card summary-card">
        <div class="card-header bg-white"><strong>Order Summary</strong></div>
        <div class="card-body">
          <div class="d-flex justify-content-between mb-2">
            <span>Subtotal</span>
            <strong>$ <span id="sumSubtotal"><?= number_format($grand,2) ?></span></strong>
          </div>

          <!--Coupon-->
          <div class="coupon-wrap mb-2">
            <div class="custom-control custom-checkbox mb-2">
              <input type="checkbox" class="custom-control-input" id="chkCoupon">
              <label class="custom-control-label" for="chkCoupon">Have Coupon?</label>
            </div>

            <div id="couponArea" class="d-none">
              <div class="form-inline">
                <input type="text" name="couponCode" id="couponCode" class="form-control form-control-sm mr2 coupon-input" placeholder="Enter Coupon Code">
                <button type="button" id="btnApplyCoupon" class="btn btn-sm btn-outline-primary">Apply</button>
              </div>
              <small id="couponMsg" class="text-muted d-block mt-1"></small>
              <div id="couponRow" class="d-none mt-2">
                <div class="d-flex justify-content-between">
                 <span>Discount (<span id ="couponPercent">0</span>%)</span>
                 <strong>-$<span id="couponAmount">0.00</span></strong>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-between mb-2 text-muted">
            <span>Shipping</span>
            <span>Calculated at checkout</span>
          </div>
          <hr>
          <div class="d-flex justify-content-between h5">
            <span>Total</span>
            <strong>$ <span id="sumGrand"><?= number_format($grand,2) ?></span></strong>
          </div>
        </div>
        <div class="card-footer bg-white">
          <?php if (!empty($items)): ?>

            <!--  order user info  -->

            <form id="orderForm" class="mb-2">
              <input type="text" name="user_name" class="form-control mb-2"placeholder="Your Name" value="<?= $_SESSION['user_name']?>" required>
               <input type="text" name="phone" class="form-control mb-2"placeholder="Your Phnone Number" value="<?= $_SESSION['user_phone']?>">
              <input type="text" name="area" class="form-control mb-2" placeholder="Input Your Area" required>
              <textarea name="address" class="form-control mb-2" placeholder="Full Address"> </textarea>

            <button class="btn btn-success btn-block">Order Now</button>

            </form>



          <?php endif; ?>
          <a href="<?= htmlspecialchars($BASE) ?>/index.php" class="btn btn-outline-secondary btn-block">
            Continue Shopping
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php

if (file_exists(__DIR__ . '/partials/footer.php')) include __DIR__ . '/partials/footer.php';
?>

<?php if (!defined('BOOTSTRAP_JS_LOADED')): // fallback if footer didn't load JS ?>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<?php endif; ?>

<script>

var __coupon = { code: '', percent: 0, amount: 0.00 };

function recalcTotals() 
{
  let sum = 0, totalQty = 0;
  document.querySelectorAll('#cartBody tr').forEach(function(tr)
  {
    const unit = parseFloat((tr.querySelector('.unit')?.textContent || '0').replace(/,/g,''));
    let qty = parseInt(tr.querySelector('.qty')?.value || '0', 10) || 0;
    const line = unit * qty;
    if (!isNaN(line)) sum += line;
    totalQty += qty;
  });

  //If coupon then subtotal adjusted

  let discount = parseFloat(__coupon.amount || 0);
  if(discount > sum ) discount = sum;

  const net = ( sum - discount).toFixed(2);

  //const grand = sum.toFixed(2);
  const g1 = document.getElementById('grandTotal');
  const g2 = document.getElementById('sumSubtotal');
  const g3 = document.getElementById('sumGrand');
  if (g1) g1.textContent = sum.toFixed(2);
  if (g2) g2.textContent = sum.toFixed(2);
  if (g3) g3.textContent = net;

 
  if (typeof window.updateNavCartBadge === 'function') 
  {
    window.updateNavCartBadge(totalQty);
  } 
  else 
  {
    var badge = document.getElementById('navCartCount');
    if (badge) badge.textContent = totalQty;
  }
}

// Qty change → AJAX update + UI recalc
document.addEventListener('input', function(e)
{
  const qtyInput = e.target.closest('#cartBody .qty');
  if (!qtyInput) return;

  const tr = qtyInput.closest('tr');
  const itemId = tr?.getAttribute('data-item-id');
  let qty = parseInt(qtyInput.value, 10);
  if (!qty || qty < 1) qty = 1;
  qtyInput.value = qty;

  qtyInput.disabled = true;
  fetch('ajax/cart_update_qty.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    credentials: 'same-origin',
    body: 'item_id=' + encodeURIComponent(itemId) + '&qty=' + encodeURIComponent(qty)
  })
  .then(r => r.json())
  .then(d => {
    const unit = parseFloat((tr.querySelector('.unit')?.textContent || '0').replace(/,/g,''));
    tr.querySelector('.line-total').textContent = (unit * qty).toFixed(2);

    if(__coupon.code) applyCoupon(__coupon.code); else  recalcTotals();   
   
  })
  .catch(()=>{ qtyInput.disabled = false })
  .finally(()=> { qtyInput.disabled = false; });
});

// Remove item → AJAX + UI remove + totals
document.addEventListener('click', function(e)
{
  const btn = e.target.closest('#cartBody .remove');
  if (!btn) return;

  const tr = btn.closest('tr');
  const itemId = tr?.getAttribute('data-item-id');

  fetch('ajax/cart_remove_item.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    credentials: 'same-origin',
    body: 'item_id=' + encodeURIComponent(itemId)
  })
  .then(r => r.json())
  .then(d => {
    tr.parentNode.removeChild(tr);
    
    if(__coupon.code) applyCoupon(__coupon.code); else {
       recalcTotals();
        if (!document.querySelector('#cartBody tr')) location.reload();
    }  
    
  })
  .catch(()=>{});
});

//Checkbox toggle

$('#chkCoupon').on('change', function()
{
  if(this.checked)
  {
    $('#couponArea').removeClass('d-none');
    $('#couponCode').focus();
  }

  else
  {
    __coupon = { code: '', percent: 0, amount: 0.00 };
    $('#couponArea').addClass('d-none');
    $('#couponRow').addClass('d-none');
    $('#couponMsg').text('');
    recalcTotals();
  }
 });

  $('#btnApplyCoupon').on('click', function(){
    const code = ($('#couponCode').val() || '').trim();
  if(!code)
  {
    $('#couponMsg').text('Please enter a coupon code');
    return;
  }
  applyCoupon(code);
  });
  

$('#couponCode').on('keyup', function(e){
  if(e.key === 'Enter') $('#btnApplyCoupon').click();
});

function applyCoupon(code)
{
  const payload = new URLSearchParams();
  payload.append('code', code);

  fetch('ajax/apply_coupon.php',{

    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    credentials: 'same-origin',
    body: payload.toString()
  }).then(r => r.json()).then(d => {
      if(!d || !d.ok)
      {
        __coupon = { code: '', percent:0, amount: 0.00 };
        
        $('#couponRow').addClass('d-none');
        $('#couponMsg').removeClass('text-success').addClass('text-danger').text(d && d.msg ? d.msg : 'Invalid Coupon.');
        recalcTotals();
        return;
      }

      __coupon = { code: code, percent: parseFloat(d.percent || 0), amount: parseFloat(d.discount || 0) };

      $('#couponPercent').text(__coupon.percent.toFixed(2));
      $('#couponAmount').text(__coupon.amount.toFixed(2));
      $('#couponRow').removeClass('d-none');
      $('#couponMsg').removeClass('text-danger').addClass('text-success').text('Coupon applied  successfully');
      recalcTotals();
  }).catch(()=>{
      $('#couponMsg').removeClass('text-success').addClass('text-danger').text('Error Applying Coupon');
  });

}
 recalcTotals();

 /* order Sendingg*/
 $('#orderForm').submit(function(e){
  e.preventDefault();
  $.ajax({
    url:'ajax/order_now.php',
    method:'POST',
    data:$(this).serialize(),
    dataType:'json',
    success: function(res)
    {
      if(res.success)
      {
        alert('Order Places Sucessfully');
        window.location.href="thankyou.php";
      }
      else {
        alert(res.message || "Failed to order");
      }
    }

  });
 });
</script>
</body>
</html>
