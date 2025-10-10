<?php 
include 'dbConfig.php';

$errmsg = '';
$successmsg = '';

if (isset($_POST['Add'])) {
    $category_name = $_POST['category_name'];

    if (!empty($category_name)) {
        $stmt = $DB_con->prepare("INSERT INTO categories(category_name) VALUES (?)");
        $inserted = $stmt->execute([$category_name]);

        if ($inserted) {
            $successmsg = "Category '$category_name' added successfully.";
            header("Refresh:3; URL=?page=products");


        } else {
            $errmsg = "Failed to add category.";
        }
    } else {
        $errmsg = "Category name is required.";
    }
   
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Category</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5" style="max-width: 400px;">
  <?php if (!empty($successmsg)): ?>
    <div class="alert alert-success"><?php echo $successmsg; ?></div>
  <?php elseif (!empty($errmsg)): ?>
    <div class="alert alert-danger"><?php echo $errmsg; ?></div>
  <?php endif; ?>

  <form action="#" method="POST" class="border p-4 rounded shadow-sm bg-light">
    <h5 class="mb-3 text-center">Add Category</h5>

    <div class="mb-3">
      <input type="text" name="category_name" class="form-control" placeholder="Category name" required>
    </div>

    <div class="d-grid">
      <button type="submit" name="Add" class="btn btn-primary">Add</button>
    </div>
  </form>
</div>

</body>
</html>
