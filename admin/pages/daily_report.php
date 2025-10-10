<?php
include 'dbConfig.php';

// Currency Config
$currency = "৳";

// ================== Date Filters ==================
$from_date = $_GET['from_date'] ?? date('Y-m-01');
$to_date   = $_GET['to_date'] ?? date('Y-m-d');

// Validate Date
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
if (!validateDate($from_date) || !validateDate($to_date)) {
    echo "<div class='alert alert-danger'>Invalid date format!</div>";
    exit;
}

// ================== Pagination Setup ==================
$limit  = $_GET['limit'] ?? 10;
$page   = max(1, (int)($_GET['page_num'] ?? 1));
$offset = ($page - 1) * $limit;

// Count distinct days
$countSql = "SELECT COUNT(DISTINCT DATE(order_date)) 
             FROM orders 
             WHERE DATE(order_date) BETWEEN :from_date AND :to_date";
$stmtCount = $DB_con->prepare($countSql);
$stmtCount->execute(['from_date' => $from_date, 'to_date' => $to_date]);
$totalDays  = $stmtCount->fetchColumn();
$totalPages = ceil($totalDays / $limit);

// ================== Fetch Data ==================
$sql = "SELECT 
            DATE(order_date) AS order_day,
            COUNT(DISTINCT global_order_id) AS total_orders,
            SUM(total_amount) AS total_revenue,
            SUM(coupon_discount) AS total_coupon_discount
        FROM orders
        WHERE DATE(order_date) BETWEEN :from_date AND :to_date
        GROUP BY order_day
        ORDER BY order_day DESC
        LIMIT :limit OFFSET :offset";

$stmt = $DB_con->prepare($sql);
$stmt->bindValue(':from_date', $from_date);
$stmt->bindValue(':to_date', $to_date);
$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$report_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ================== Overall Totals (Full Range) ==================
$sumSql = "SELECT 
              COUNT(DISTINCT global_order_id) AS all_orders,
              SUM(total_amount) AS all_revenue,
              SUM(coupon_discount) AS all_coupon_discount
           FROM orders
           WHERE DATE(order_date) BETWEEN :from_date AND :to_date";
$stmtSum = $DB_con->prepare($sumSql);
$stmtSum->execute(['from_date'=>$from_date,'to_date'=>$to_date]);
$overall = $stmtSum->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Daily Report</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body class="container py-4">

<h2 class="mb-4">📊 Daily Report</h2>

<!-- Filter Form -->
<form method="GET" class="row g-2 mb-3" id="filterForm">
    <div class="col-auto">
      <label for="from_date">From:</label>
      <input type="date" id="from_date" name="from_date" value="<?= htmlspecialchars($from_date) ?>" class="form-control" onchange="this.form.submit()">
    </div>
    <div class="col-auto">
      <label for="to_date">To:</label>
      <input type="date" id="to_date" name="to_date" value="<?= htmlspecialchars($to_date) ?>" class="form-control" onchange="this.form.submit()">
    </div>
    <div class="col-auto">
      <label for="limit">Per Page:</label>
      <select class="form-select" name="limit" onchange="this.form.submit()">
          <option <?= $limit==5?'selected':'' ?>>5</option>
          <option <?= $limit==10?'selected':'' ?>>10</option>
          <option <?= $limit==20?'selected':'' ?>>20</option>
          <option <?= $limit==50?'selected':'' ?>>50</option>
      </select>
    </div>
</form>

<?php if ($report_data): ?>
<div class="d-flex gap-2 mb-3">
  <button class="btn btn-success" onclick="exportExcel()">⬇ Excel</button>
  <button class="btn btn-danger" onclick="exportPDF()">⬇ PDF</button>
</div>

<div class="table-responsive" id="reportTable">
<table class="table table-bordered table-striped table-hover">
  <thead class="table-dark">
    <tr>
      <th>Date</th>
      <th>Total Orders</th>
      <th>Total Revenue</th>
      <th>Total Coupon Discount</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $page_orders=0;$page_revenue=0;$page_coupon=0;
    foreach ($report_data as $row):
      $page_orders  += $row['total_orders'];
      $page_revenue += $row['total_revenue'];
      $page_coupon  += $row['total_coupon_discount'];
    ?>
      <tr>
        <td><?= $row['order_day'] ?></td>
        <td><?= $row['total_orders'] ?></td>
        <td><?= number_format($row['total_revenue'],2) . " $currency" ?></td>
        <td><?= number_format($row['total_coupon_discount'],2) . " $currency" ?></td>
      </tr>
    <?php endforeach ?>
  </tbody>
  <tfoot class="fw-bold table-light">
    <tr>
      <td>Page Total</td>
      <td><?= $page_orders ?></td>
      <td><?= number_format($page_revenue,2) . " $currency" ?></td>
      <td><?= number_format($page_coupon,2) . " $currency" ?></td>
    </tr>
    <tr>
      <td>Overall Total</td>
      <td><?= $overall['all_orders'] ?></td>
      <td><?= number_format($overall['all_revenue'],2) . " $currency" ?></td>
      <td><?= number_format($overall['all_coupon_discount'],2) . " $currency" ?></td>
    </tr>
  </tfoot>
</table>
</div>

<!-- Pagination -->
<nav>
  <ul class="pagination justify-content-center">
    <li class="page-item <?= $page<=1?'disabled':'' ?>">
      <a class="page-link" href="?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>&limit=<?= $limit ?>&page_num=1">First</a>
    </li>
    <li class="page-item <?= $page<=1?'disabled':'' ?>">
      <a class="page-link" href="?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>&limit=<?= $limit ?>&page_num=<?= $page-1 ?>">«</a>
    </li>
    <li class="page-item disabled"><span class="page-link">Page <?= $page ?>/<?= $totalPages ?></span></li>
    <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>">
      <a class="page-link" href="?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>&limit=<?= $limit ?>&page_num=<?= $page+1 ?>">»</a>
    </li>
    <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>">
      <a class="page-link" href="?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>&limit=<?= $limit ?>&page_num=<?= $totalPages ?>">Last</a>
    </li>
  </ul>
</nav>

<!-- Summary + Chart -->
<div class="row mt-4">
  <div class="col-md-4">
    <div class="card p-3">
      <h5>Summary</h5>
      <p>Range: <strong><?= $from_date ?> → <?= $to_date ?></strong></p>
      <p>Total Days: <strong><?= $totalDays ?></strong></p>
      <p>Total Orders: <strong><?= $overall['all_orders'] ?></strong></p>
      <p>Total Revenue: <strong><?= number_format($overall['all_revenue'],2) . " $currency" ?></strong></p>
      <p>Total Discount: <strong><?= number_format($overall['all_coupon_discount'],2) . " $currency" ?></strong></p>
    </div>
  </div>
  <div class="col-md-8">
    <canvas id="lineChart" height="120"></canvas>
  </div>
</div>

<?php else: ?>
<div class="alert alert-info">No orders found for the selected date range.</div>
<?php endif; ?>

<script>
// Chart.js with multiple datasets
const labels = <?= json_encode(array_column($report_data,'order_day')) ?>;
const orders = <?= json_encode(array_column($report_data,'total_orders')) ?>;
const revenue = <?= json_encode(array_column($report_data,'total_revenue')) ?>;
const discount = <?= json_encode(array_column($report_data,'total_coupon_discount')) ?>;

new Chart(document.getElementById('lineChart'), {
  type:'line',
  data:{
    labels:labels,
    datasets:[
      {label:'Orders',data:orders,borderColor:'blue',tension:0.3},
      {label:'Revenue',data:revenue,borderColor:'green',tension:0.3},
      {label:'Discount',data:discount,borderColor:'red',tension:0.3}
    ]
  },
  options:{responsive:true,maintainAspectRatio:false}
});

// Excel Export
function exportExcel(){
  let table = document.querySelector('#reportTable table');
  let wb = XLSX.utils.table_to_book(table,{sheet:"Report"});
  XLSX.writeFile(wb,"daily-report.xlsx");
}

// PDF Export
async function exportPDF(){
  let elem = document.getElementById('reportTable');
  let canvas = await html2canvas(elem);
  let imgData = canvas.toDataURL('image/png');
  let pdf = new jspdf.jsPDF('l','pt','a4');
  let width = pdf.internal.pageSize.getWidth();
  let height = canvas.height * width / canvas.width;
  pdf.addImage(imgData,'PNG',0,0,width,height);
  pdf.save('daily-report.pdf');
}
</script>
</body>
</html>       