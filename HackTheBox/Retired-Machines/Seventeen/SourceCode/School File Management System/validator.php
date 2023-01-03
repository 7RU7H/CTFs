<?php
	session_start();
	
	if(!ISSET($_SESSION['student'])){
		header('location:index.php');
	}
?>