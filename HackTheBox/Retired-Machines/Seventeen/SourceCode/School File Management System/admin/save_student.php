<?php
	require_once 'conn.php';
	
	if(ISSET($_POST['save'])){
		$stud_no = $_POST['stud_no'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$gender = $_POST['gender'];
		$yrsec = $_POST['year']."".$_POST['section'];
		$password = md5($_POST['password']);
		
		mysqli_query($conn, "INSERT INTO `student` VALUES('', '$stud_no', '$firstname', '$lastname', '$gender', '$yrsec', '$password')") or die(mysqli_error());
		
		header('location: student.php');
	}
?>