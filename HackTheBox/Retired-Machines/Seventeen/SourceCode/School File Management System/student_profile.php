<!DOCTYPE html>
<?php 
	session_start();
	if(!ISSET($_SESSION['student'])){
		header("location: index.php");
	}
	require_once 'admin/conn.php'
?>
<html lang="en">
	<head>
		<title>School File Management System</title>
		<meta charset = "utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" type="text/css" href="admin/css/bootstrap.css" />
		<link rel="stylesheet" type="text/css" href="admin/css/jquery.dataTables.css" />
		<link rel="stylesheet" type="text/css" href="admin/css/style.css" />
	</head>
<body>
	<nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
		<div class="container-fluid">
			<label class="navbar-brand">School File Management System</label>
		</div>
	</nav>
	<div class="col-md-8">
		<div class="alert alert-info" style="margin-top:100px;">File List</div>
		<div class="panel panel-default">
			<div class="panel-body alert-success" >
				
				<table id="table" class="table table-bordered">
					<thead>
						<tr>
							<th>Filename</th>
							<th>File Type</th>
							<th>Date Uploaded</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$query = mysqli_query($conn, "SELECT * FROM `student` WHERE `stud_id` = '$_SESSION[student]'") or die(mysqli_error());
							$fetch = mysqli_fetch_array($query);
							$stud_no = $fetch['stud_no'];
							$query1 = mysqli_query($conn, "SELECT * FROM `storage` WHERE `stud_no` = '$stud_no'") or die(mysqli_error());
							while($fetch1 = mysqli_fetch_array($query1)){
						?>
						<tr class="del_file<?php echo $fetch1['store_id']?>">
							<td><?php echo substr($fetch1['filename'], 0 ,30)."..."?></td>
							<td><?php echo $fetch1['file_type']?></td>
							<td><?php echo $fetch1['date_uploaded']?></td>
							<td><a href="download.php?store_id=<?php echo $fetch1['store_id']?>" class="btn btn-success"><span class="glyphicon glyphicon-download"></span> Download</a> | <button class="btn btn-danger btn_remove" type="button" id="<?php echo $fetch1['store_id']?>"><span class="glyphicon glyphicon-trash"></span> Remove</button></td>
						</tr>
						<?php
							}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-primary" style="margin-top:20%;">
			<div class="panel-heading">
				<h1 class="panel-title">Student Information</h1>
			</div>
			<div class="panel-body">
				<h4>Student no: <label class="pull-right"><?php echo $fetch['stud_no']?></label></h4>
				<hr style="border-top:1px dotted #ccc;"/>
				<h4>Name: <label class="pull-right"><?php echo $fetch['firstname']." ".$fetch['lastname']?></label></h4>
				<hr style="border-top:1px dotted #ccc;"/>
				<h4>Gender: <label class="pull-right"><?php echo $fetch['gender']?></label></h4>
				<hr style="border-top:1px dotted #ccc;"/>
				<h4>Year & Section: <label class="pull-right"><?php echo $fetch['yr&sec']?></label></h4>
				<hr style="border-top:1px dotted #ccc;"/>
				<h3>File</h3>
				<form method="POST" enctype="multipart/form-data" action="save_file.php">
					<input type="file" name="file" size="4" style="background-color:#fff;" required="required" />
					<br />
					<input type="hidden" name="stud_no" value="<?php echo $fetch['stud_no']?>"/>
					<button class="btn btn-success btn-sm" name="save"><span class="glyphicon glyphicon-plus"></span> Add File</button>
				</form>
				<br style="clear:both;"/>
				<div class="dropdown pull-right">
					<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><span class="glyphicon glyphicon-cog">&nbsp;</span><span class="caret"></span></button>
					<ul class="dropdown-menu">
						<li><a href="student_update.php">Update Account</a></li>
						<li><a href="#" data-toggle="modal" data-target="#modal_confirm">Logout</a></li>
					</ul>
				</div>
			</div>
		</div>
		
	</div>
	<div id = "footer">
		<label class = "footer-title">&copy; Copyright School File Management System <?php echo date("Y", strtotime("+8 HOURS"))?></label>
	</div>
	<div class="modal fade" id="modal_confirm" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title">System</h3>
				</div>
				<div class="modal-body">
					<center><h4 class="text-danger">Are you sure you want to logout?</h4></center>
				</div>
				<div class="modal-footer">
					<a type="button" class="btn btn-success" data-dismiss="modal">Cancel</a>
					<a href="logout.php" class="btn btn-danger">Logout</a>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modal_remove" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title">System</h3>
				</div>
				<div class="modal-body">
					<center><h4 class="text-danger">Are you sure you want to remove this file?</h4></center>
				</div>
				<div class="modal-footer">
					<a type="button" class="btn btn-success" data-dismiss="modal">No</a>
					<button type="button" class="btn btn-danger" id="btn_yes">Yes</button>
				</div>
			</div>
		</div>
	</div>
<?php include 'script.php'?>
<script type="text/javascript">
$(document).ready(function(){
	$('.btn_remove').on('click', function(){
		var store_id = $(this).attr('id');
		$("#modal_remove").modal('show');
		$('#btn_yes').attr('name', store_id);
	});
	
	$('#btn_yes').on('click', function(){
		var id = $(this).attr('name');
		$.ajax({
			type: "POST",
			url: "remove_file.php",
			data:{
				store_id: id
			},
			success: function(data){
				$("#modal_remove").modal('hide');
				$(".del_file" + id).empty();
				$(".del_file" + id).html("<td colspan='4'><center class='text-danger'>Deleting...</center></td>");
				setTimeout(function(){
					$(".del_file" + id).fadeOut('slow');
				}, 1000);
				
			}
		});
	});
});
</script>	
</body>
</html>