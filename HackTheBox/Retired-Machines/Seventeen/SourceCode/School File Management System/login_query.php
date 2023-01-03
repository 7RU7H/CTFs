<?php
	session_start();
	require 'admin/conn.php';
	
	if(ISSET($_POST['login'])){
		$stud_no = $_POST['stud_no'];
		$password = md5($_POST['password']);
		
		$query = mysqli_query($conn, "SELECT * FROM `student` WHERE `stud_no` = '$stud_no' && `password` = '$password'") or die(mysqli_error());
		$fetch = mysqli_fetch_array($query);
		$row = $query->num_rows;
		
		if($row > 0){
			$_SESSION['student'] = $fetch['stud_id'];
			header("location:student_profile.php");
		}else{
			echo "<center><label class='text-danger'>Invalid username or password</label></center>";
		}
	}
?>