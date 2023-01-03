<?php
	session_start();
	unset($_SESSION['student']);
	header("location: index.php");
?>