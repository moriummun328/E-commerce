<?php
session_start();
$_SESSION = [];

session_destroy();

if(defined('BASE_URL'))
{
	header('Location: ' .BASE_URL . "/index.php");
}

else
{
	header("Location: /ecommerce/index.php");
}


?>