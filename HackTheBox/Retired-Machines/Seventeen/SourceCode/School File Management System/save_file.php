<?php
	require_once 'admin/conn.php';
	
	if(ISSET($_POST['save'])){
		$stud_no = $_POST['stud_no'];
		$file_name = $_FILES['file']['name'];
		$file_type = $_FILES['file']['type'];
		$file_temp = $_FILES['file']['tmp_name'];
		$location = "files/".$stud_no."/".$file_name;
		$date = date("Y-m-d, h:i A", strtotime("+8 HOURS"));
		if(!file_exists("files/".$stud_no)){
			mkdir("files/".$stud_no);
		}
		
		if(move_uploaded_file($file_temp, $location)){
			mysqli_query($conn, "INSERT INTO `storage` VALUES('', '$file_name', '$file_type', '$date', '$stud_no')") or die(mysqli_error());
			header('location: student_profile.php');
		}
	}
?>