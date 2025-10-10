<?php
if(session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['admin_logged_in'])) { 
    http_response_code(403); 
    exit('Forbidden');
}

require_once __DIR__ .'/../dbConfig.php';

try {
    $sql = "
        SELECT 
            o.global_order_id,
            GROUP_CONCAT(CONCAT(p.product_name, ' (', o.quantity, ')') ORDER BY o.id SEPARATOR ', ') AS items,
            MIN(o.user_name)       AS user_name,
            MIN(o.phone)           AS phone,
            MIN(o.area)            AS area,
            MIN(o.address)         AS address,
            MIN(o.status)          AS status,
            MIN(o.order_date)      AS order_date,
            SUM(o.total_amount)    AS total_amount,
            SUM(o.coupon_discount) AS coupon_discount,
            SUM(o.payable_amount)  AS payable_amount,
            MIN(o.delivery_man_id) AS delivery_man_id
        FROM orders o
        JOIN products p ON p.id = o.product_id
        WHERE o.status IN ('pending','delivered','completed','canceled')
        GROUP BY o.global_order_id
        ORDER BY order_date DESC
    ";
    $st   = $DB_con->query($sql);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    echo "<div class='alert alert-danger'>DB Error: ".$e->getMessage()."</div>";
    exit;
}
?>

<?php if(!$rows): ?>
    <div class="alert alert-info">No orders found</div>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Order ID</th>
                <th>User</th>
                <th>Products (qty)</th>
                <th>Address</th>
                <th class="text-end">Total</th>
                <th class="text-end">Discount</th>
                <th class="text-end">Payable</th>
                <th>Delivery Man</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($rows as $r): ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($r['global_order_id']) ?></strong><br>
                    <small class="text-muted"><?= htmlspecialchars($r['order_date']) ?></small>
                </td>
                <td>
                    <?= htmlspecialchars($r['user_name']) ?><br>
                    <small class="text-muted"><?= htmlspecialchars($r['phone']) ?></small>
                </td>
                <td><?= htmlspecialchars($r['items']) ?></td>
                <td><?= htmlspecialchars($r['address']) ?>, <?= htmlspecialchars($r['area']) ?></td>
                <td class="text-end">$<?= number_format((float)$r['total_amount'],2) ?></td>
                <td class="text-end">$<?= number_format((float)$r['coupon_discount'],2) ?></td>
                <td class="text-end">$<?= number_format((float)$r['payable_amount'],2) ?></td>
                <td>
                    <div class="d-flex">
                        <select class="form-control delivery-assign me-1" style="min-width:160px;">
                            <option value="">-- Select --</option>
                            <?php
                                $men = $DB_con->query("SELECT id, name FROM delivery_men ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
                                foreach($men as $m){
                                    $sel = ($r['delivery_man_id'] == $m['id']) ? 'selected' : '';
                                    echo "<option value='{$m['id']}' {$sel}>".htmlspecialchars($m['name'])."</option>";
                                }
                            ?>
                        </select>
                        <button 
                            class="btn btn-sm btn-primary assign-delivery-btn" 
                            data-gid="<?= htmlspecialchars($r['global_order_id']) ?>"
                        >Assign</button>
                    </div>
                </td>
                <td>
                    <select 
                        class="form-control form-control-sm order-status" 
                        data-gid="<?= htmlspecialchars($r['global_order_id']) ?>"
                    >
                        <option value="pending"   <?= ($r['status']==='pending')   ? 'selected':'' ?>>Pending</option>
                        <option value="delivered" <?= ($r['status']==='delivered') ? 'selected':'' ?>>Delivered</option>
                        <option value="completed" <?= ($r['status']==='completed') ? 'selected':'' ?>>Completed</option>
                        <option value="canceled"  <?= ($r['status']==='canceled')  ? 'selected':'' ?>>Canceled</option>
                    </select>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
