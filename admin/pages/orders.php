<?php
if(session_status() == PHP_SESSION_NONE) session_start();
if(empty($_SESSION['admin_logged_in']))
{
    echo json_encode(['ok' => false, 'msg' =>'Unauthorised']);
    exit;
}
require_once __DIR__.'/../dbConfig.php';
?>

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h3 class="mb-0">Orders</h3>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-tabs" id="orderTabs">
        <li class="nav-item">
            <a class="nav-link active" data-status="pending" href="#">Pending</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-status="delivered" href="#">Delivered</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-status="completed" href="#">Completed</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-status="canceled" href="#">Canceled</a>
        </li>
    </ul>

    <div class="mt-3 d-flex justify-content-end">
        <button id="refreshOrders" class="btn btn-sm btn-outline-secondary">Refresh</button>
    </div>

    <div id="ordersTableWrap" class="mt-2"></div>
</div>

<script type="text/javascript">
let currentStatus = "pending";

document.addEventListener('DOMContentLoaded', function(){
    fetchOrders(currentStatus);
});

// ---------- Fetch Orders ----------
function fetchOrders(status) {
    currentStatus = status || currentStatus;
    fetch('ajax/order_table.php?status=' + currentStatus, {credentials: 'same-origin'})
    .then(r => r.text())
    .then(html => { 
        document.getElementById('ordersTableWrap').innerHTML = html; 
    })
    .catch(()=> {
        document.getElementById('ordersTableWrap').innerHTML = "<div class='alert alert-danger'>Failed to load orders</div>"; 
    });
}

document.getElementById('refreshOrders').addEventListener('click', function(){
    fetchOrders(currentStatus);
});

// ---------- Tabs Switch ----------
document.getElementById('orderTabs').addEventListener('click', function(e){
    e.preventDefault();
    const link = e.target.closest('a[data-status]');
    if(!link) return;

    document.querySelectorAll('#orderTabs .nav-link').forEach(el=> el.classList.remove('active'));
    link.classList.add('active');

    fetchOrders(link.getAttribute('data-status'));
});

// ----------------------
// Status update (mail only on delivered)
// ----------------------
document.addEventListener('change', function(e){
    const sel = e.target.closest('.order-status');
    if(!sel) return;

    const gid = sel.getAttribute('data-gid');
    const val = sel.value;

    const body = new URLSearchParams();
    body.append('global_order_id', gid);
    body.append('status', val);

    fetch('ajax/order_status_update.php',{
        method: 'POST',
        headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
        credentials: 'same-origin',
        body: body.toString()
    })
    .then(r => r.json())
    .then(d => {
        if(!d || !d.ok){
            alert(d && d.msg ? d.msg : 'Failed to update'); 
            return;
        }
        fetchOrders(currentStatus);
    })
    .catch(() => alert('Error updating status'));
});

// ----------------------
// Delivery assign (mail will NOT send on assign)
// ----------------------
document.addEventListener('click', function(e){
    const btn = e.target.closest('.assign-delivery-btn');
    if(!btn) return;

    const gid = btn.getAttribute('data-gid');
    const sel = btn.parentElement.querySelector('.delivery-assign');
    const manId = sel.value;

    if(!manId){
        alert("Please select a delivery man first!");
        return;
    }

    fetch("ajax/order_delivery_update.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        credentials: 'same-origin',
        body: new URLSearchParams({ global_order_id: gid, delivery_man_id: manId })
    })
    .then(res => res.json())
    .then(d => {
        if(d.ok){
            alert(d.msg);
            fetchOrders(currentStatus);
        } else {
            alert("Failed: " + d.msg);
        }
    })
    .catch(()=> alert('Error updating delivery man'));
});
</script>
