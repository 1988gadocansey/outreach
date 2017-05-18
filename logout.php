<?php 
ob_start();

  			
$_SESSION = array();
session_destroy();
$redirect = "<script>window.location='index.php';</script>";

echo $redirect;
ob_flush();
?>
