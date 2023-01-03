<?php
	require_once 'conn.php';
	
	if(ISSET($_POST['edit'])){
		$user_id = $_POST['user_id'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$username = $_POST['username'];
		$password = md5($_POST['password']);
		
		mysqli_query($conn, "UPDATE `user` SET `firstname` = '$firstname', `lastname` = '$lastname', `username` = '$username', `password` = '$password' WHERE `user_id` = '$user_id'") or die(mysqli_error());
		
		echo "<script>alert('Successfully updated!')</script>";
		echo "<script>window.location = 'user.php'</script>";
	}
?>