<?php
	session_start();
	
	if(!ISSET($_SESSION['user'])){
		header("location: index.php");
	}
?>