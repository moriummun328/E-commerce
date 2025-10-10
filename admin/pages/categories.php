<?php
include 'dbConfig.php';


//Fetch all categories

$stmt = $DB_con->prepare("SELECT * FROM categories ORDER BY id DESC");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
	<title>All Products</title>
	

	<style type="text/css">
		.thumbnail-img
		{
			width: 80px;
			height: 80px;
			object-fit: cover;
		}

		.color-box
		{
			display: inline-block;
			width: 20px;
			height: 20px;
			border: 10px solid #000;
			margin-right: 4px;
		}
	</style>
</head>
<body>
	<div class="container mt-5">
		<h2 class="mb-4">All Categories</h2>

		<a href="?page=add_categories" class="btn btn-success mb-2">ADD Categories</a>
		<p></p>
		<table class="table table-bordered table-hover">
			<thead class="thead-dark">
				<tr>
					<th>Name</th>
					<th>Action</th>
				</tr>
			</thead>
			<?php

				if($categories):?>
					<?php foreach($categories as $row):?>
						<?php 
						$encrypted_id = urlencode(base64_encode($row['id']));
						 ?>
						
				<tr>


					<td>
						<?php echo htmlspecialchars($row['category_name']);?>
					</td>


				<td>
						<a href="?page=edit_categories&id=<?php echo $encrypted_id; ?>" class="btn btn-sm btn-warning">Edit</a>

						<a href="?page=products&delete_id=<?php echo $encrypted_id; ?>" class="btn btn-sm btn-danger">Delete</a>
					</td>
				</tr>


			<?php endforeach;?>
			<?php else: ?>
				<tr>
					<td colspan="8" class="text-center">No Category found found!
						
					</td>
				</tr>
			<?php endif; ?>
		</table>
	</div>

</body>
</html>