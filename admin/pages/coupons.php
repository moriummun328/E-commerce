
<?php
if(session_status() == PHP_SESSION_NONE) session_start();

if(!isset($_SESSION['admin_logged_in'])) { header("Location: ../login.php"); exit; }

require_once __DIR__ .'/../dbConfig.php';

$cats = $DB_con->query("SELECT id, category_name FROM categories ORDER BY category_name ASC")->fetchAll(PDO::FETCH_ASSOC);


$coupons = $DB_con->query("SELECT id, code, discount_percent, scope, status, start_date, end_date, created_at FROM coupons ORDER BY id DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT c.*, 
               GROUP_CONCAT(p.product_name SEPARATOR ', ') as products
        FROM coupons c
        LEFT JOIN coupon_products cp ON c.id = cp.coupon_id
        LEFT JOIN products p ON cp.product_id = p.id
        GROUP BY c.id
        ORDER BY c.created_at DESC";
$stmt = $DB_con->prepare($sql);
$stmt->execute();
$coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<div class="container-fluid">
<h4 class="mb-3">Coupon Management</h4>

<div class="row">
<div class="col-lg-6">
<div class="card mb-3">
<div class="card-header">Create Coupon</div>
<div class="card-body">
<div class="form-group">
<label>Apply to</label>
<div>
	<div class="form-check form-check-inline">
		<input type="radio" name="apply_to" class="form-check-input" id="optAll" value="all" checked>
		<label class="form-check-label">All Categories</label>					
	</div>
	<div class="form-check form-check-inline">
		<input type="radio" name="apply_to" class="form-check-input" id="optSelected" value="selected">
		<label class="form-check-label">Selected Categories</label>
	</div>
</div>	
</div>

<!--All Categories-->

<form id="formAll" class="border rounded p-3">
<div class="form-row">
	<div class="form-group col-md-3">
		<label>Coupon Code</label>
		<input type="text" name="code" class="form-control" placeholder="e.g. SALE10" required>
	</div>
	<div class="form-group col-md-3">
		<label>Discount %</label>
		<input type="number" name="discount_percent" class="form-control" min="1" max="95" step="0.01" placeholder="10" required>
	</div>

	<div class="form-group col-md-3">
		<label>Usage Limit</label>
		<input type="number" name="usage_limit" id="usage_limit
		" class="form-control" min="1"placeholder="e.g 100">
	</div>
	<div class="form-group col-md-3">
		<label>Status</label>
		<select name="status" class="form-control">
			<option value="active" selected>Active</option>
			<option value="inactive">Inactive</option>
		</select>
	</div>
</div>

<div class="form-row">
	<div class="form-group col-md-6">
		<label>Start Date</label>
		<input type="date" name="start_date" class="form-control">
	</div>

	<div class="form-group col-md-6">
		<label>End Date</label>
		<input type="date" name="end_date" class="form-control">
	</div>
</div>
<button type="submit" class="btn btn-success">Create Coupon for All Products</button>
<small class="text-muted ml-2">This will create a coupon scoped to All products</small>
</form>

<!-- Selected Category-->

<form id="formSelected" class="border rounded p-3 mt-3 d-none">
<div class="form-row">
	<div class="form-group col-md-6">
		<label>Select Category</label>
		<select id="selCategory" class="form-group">
			<option value="">--choose category--</option>
			<?php
				foreach($cats as $c):?>
					<option value="<?= (int)$c['id']?>"><?= $c['category_name'] ?></option>
				<?php endforeach; ?>
		</select>
	</div>
	<div class="form-group col-md-6">
		<label>Select Product</label>
		<select id="selProduct" class="form-control" disabled>
			<option value="">---choose Product--</option>
		</select>
	</div>
</div>

<div class="form-row">
	<div class="form-group col-md-3">
		<label>Coupon Code</label>
		<input type="text" name="sp_code" id="sp_code" class="form-control" placeholder="e.g. SAVE15" required>
	</div>
	<div class="form-group col-md-3">
		<label>Discount %</label>
		<input type="number" name="sp_discount" id="sp_discount" class="form-control" min="1" max="95" step="0.01" placeholder="15" required>
	</div>

	<div class="form-group col-md-3">
		<label>Usage Limit</label>
		<input type="number" name="usage_limit" id="usage_limit" class="form-control" min="1" placeholder="e.g 100">
	</div>

	<div class="form-group col-md-3">
		<label>Status</label>
		<select id="sp_status" class="form-control">
			<option value="active" selected>Active</option>
			<option value="inactive">Inactive</option>
			
		</select>
	</div>
</div>

<div class="form-row">
	<div class="form-group col-md-6">
		<label>Start Date</label>
		<input type="date" name="sp_start" id="sp_start" class="form-control">
	</div>

	<div class="form-group col-md-6">
		<label>End Date</label>
		<input type="date" name="sp_end" id="sp_end" class="form-control">
	</div>
</div>

<button type="button" id="btnCreateSelected" class="btn btn-primary">Create Coupon for SELECTED Product </button>
<small class="text-muted ml-2">This coupon will be attached to the chosen product only.</small>						
</form>

<div id="cpnMsg" class="mt-3"></div>
</div>
</div>
</div>

<div class="col-lg-6">
<div class="card">
<div class="card-header">
Recent Coupons || <span class="badge badge-info"> Total: <?= count($coupons) ?></span>
</div>
<div class="card-body p-0">
<div class="table-responsive mb-0">
<table class="table table-striped table-hover mb-0">
	<thead class="thead-light">
		<tr>
			<th>Code</th>
			<th>%</th>
			<th>Scope</th>
			<th>Status</th>
			<th>Valid</th>
			<th>Action</th>

		</tr>
	</thead>
	<tbody>
    <?php if(!$coupons): ?>
        <tr>
            <td colspan="6" class="text-center text-muted">No Coupons Found!</td>
        </tr>
    <?php else: foreach($coupons as $cp): ?>
        <tr>
            <td><?= $cp['code'] ?></td>
            <td><?= number_format((float)$cp['discount_percent'], 2) ?></td>
            
            <td>
                <span class="badge badge-<?= $cp['scope'] === 'all' ? 'primary': 'info' ?>">
                    <?= $cp['scope'] ?>
                </span>
                <?php if($cp['scope'] === 'product'): ?>
                    <div class="small text-muted">
                        <?= $cp['products'] ?: 'No Product Linked' ?>
                    </div>
                <?php endif; ?>
            </td>
            
            <td>
                <span class="badge badge-<?= $cp['status'] === 'active' ? 'success' : 'secondary' ?>">
                    <?= $cp['status'] ?>
                </span>
            </td>
            
            <td>
                <?php
                    $sv = $cp['start_date'] ? date('d M Y', strtotime($cp['start_date'])): '_';
                    $ev = $cp['end_date'] ? date('d M Y', strtotime($cp['end_date'])): '_';
                    echo $sv . ' → ' .$ev;
                ?>
            </td>
            
            <td>
                <a href="?page=edit_coupon&id=<?= $cp['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                <button class="btn btn-sm btn-danger delete-coupon mt-2" data-id="<?= $cp['id'] ?>">Delete</button>
            </td>
        </tr>
    <?php endforeach; endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</div>

<script type="text/javascript">
	document.querySelectorAll('input[name="apply_to"]').forEach(r => {

		r.addEventListener('change', function(){
			if(this.value == 'all')
			{
				document.getElementById('formAll').classList.remove('d-none');
				document.getElementById('formSelected').classList.add('d-none');
			}

			else
			{
				document.getElementById('formAll').classList.add('d-none');
				document.getElementById('formSelected').classList.remove('d-none');
			}
		});
	});

	//All scope Data Submit (server)
	document.getElementById('formAll').addEventListener('submit', function(e){

		e.preventDefault();
		const fd = new FormData(this);
		fd.append('mode','all');

		fetch('ajax/save_coupon.php',{

			method: 'POST',
			body: fd,
			credentials: 'same-origin'
		}).then(r => r.json()).then(d => {

			showMsg(d);
			if(d.ok) this.reset();
		}).catch(()=> showMsg({ok: false, msg: 'Unexpected Error!'}));
	});

	//Selected Categories
	document.getElementById('selCategory').addEventListener('change', function(){

		const cid = this.value;
		const sel = document.getElementById('selProduct');
		sel.innerHTML = '<option value="">Loading...</option>';
		sel.disabled = true;

		fetch('ajax/get_products_by_category.php?category_id='+encodeURIComponent(cid)).then(r => r.json()).then(d => {
					sel.innerHTML = '<option value="">Choose Product</option>';
					if(d && d.items && d.items.length)
					{
						d.items.forEach(it => {
							const opt = document.createElement('option');
							opt.value = it.id; opt.textContent = it.name;
							sel.appendChild(opt);
						});

						sel.disabled = false;
					}

					else
					{
						sel.innerHTML = '<option value="">No products found!</option>';
					}

		}).catch(()=> {
			sel.innerHTML = '<option value="">Error Loading</option>';
		});
	});

	//Selected Scope

	document.getElementById('btnCreateSelected').addEventListener('click', function(){

		const cid = document.getElementById('selCategory').value;
		const pid = document.getElementById('selProduct').value;
		const code = document.getElementById('sp_code').value.trim();
		const disc = document.getElementById('sp_discount').value;
		const status = document.getElementById('sp_status').value;
		const sd = document.getElementById('sp_start').value;
		const ed = document.getElementById('sp_end').value;
		const ul = document.getElementById('usage_limit').value;

		if(!cid) { return showMsg({ok: false, msg: 'Please choose a category'});}
		if(!pid) { return showMsg({ok: false, msg: 'Please choose a product'});}
		if(!code) { return showMsg({ok: false, msg: 'Please enter a coupon code'});}
		if(!disc || parseFloat(disc) <= 0) { return showMsg({ok: false, msg: 'Invalid discount percent'});}

		const fd = new FormData();

		fd.append('mode','selected');
		fd.append('product_id',pid);
		fd.append('code',code);
		fd.append('discount_percent',disc);
		fd.append('status',status);
		fd.append('start_date',sd);
		fd.append('end_date',ed);
		fd.append('usage_limit',ul);

		fetch('ajax/save_coupon.php',{

			method: 'POST',
			body: fd,
			credentials: 'same-origin'
		}).then(r => r.json()).then(d => {

			showMsg(d);

			if(d.ok)
			{
				document.getElementById('sp_code').value = '';
				document.getElementById('sp_discount').value = '';
			}
		}).catch(()=> showMsg({ok: false, msg: 'Unexpected Error for selected coupon'}));

	});

	function showMsg(d)
	{
		const el = document.getElementById('cpnMsg');

		if(!el) return;

		el.innerHTML = '';

		const div = document.createElement('div');
		div.className = 'alert alert-' +(d.ok ? 'success' : 'warning') + 'alert-dismissible fade show';
		div.innerHTML = (d.msg || (d.ok ? 'Success' : 'Failed')) + '<button type="button" class="close" data-dismiss= "alert"><span>&times</span></button>';
		el.appendChild(div);
	}

/*coupon delete*/
	$(document).on('click', '.delete-coupon', function() {
    if(!confirm('Are you sure you want to delete this coupon?')) return;

    let btn = $(this); 
    let id = btn.data('id');

    $.ajax({
        url: 'ajax/delete_coupon.php', // ✅ path ঠিক করো
        type: 'POST',
        data: { id: id },
        success: function(response) {
            if(response.trim() === 'ok') {
                // সফল হলে রো রিমুভ হবে
                btn.closest('tr').fadeOut(400, function(){ $(this).remove(); });
            } else {
                alert('Delete failed: ' + response);
            }
        },
        error: function(xhr, status, error) {
            alert('AJAX Error: ' + error);
        }
    });
});

</script>


