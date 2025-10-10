<?php
require_once __DIR__ . '/../admin/dbConfig.php';
?>

<!DOCTYPE html>
<html>
<head>
	<title>My Product Store</title>


	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" integrity="sha512-rt/SrQ4UNIaGfDyEXZtNcyWvQeOq0QLygHluFQcSjaGB04IxWhal71tKuzP6K8eYXYB6vJV4pHkXcmFGGQ1/0w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	<link rel="stylesheet" type="text/css" href="asstes/css/style.css">

	<!-- Font Awesome CDN (Add in <head> section normally) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

	<style>
		body
		{
			background: #f7f8fa;
		}
		.main-wrap
		{
			display: flex;
		}

		.left-sidebar
		{
			width: 260px;
			min-width: 260px;
			background: #fff;
			border-right: 1px solid #e9ecef;
		}

		.content-area
		{
			flex: 1;
		}

		.cat-item
		{
			display: flex;
			align-items: center;
			margin-bottom: 8px;
		}

		.cat-item input
		{
			margin-right: 8px;
		}
		
		.product-card
		{
			border: 1px solid #e9ecef;
			border-radius: 8px;
			overflow: hidden;
			background-color: #fff;
		}

		.product-card img
		{
			width: 100%;
			height: 180px;
			object-fit: cover;
		}

		.product-card .p-body
		{
			padding: 12px;
		}

		.sticky-sidebar
		{
			position: sticky;
			top: 20px;
		}
	</style>
</head>
<body>

</body>
</html>