<!DOCTYPE html>
<?php 
	require 'validator.php';
	require_once 'conn.php'
?>
<html lang = "en">
	<head>
		<title>School File Management System</title>
		<meta charset = "utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel = "stylesheet" type = "text/css" href = "css/bootstrap.css" />
		<link rel = "stylesheet" type = "text/css" href = "css/jquery.dataTables.css" />
		<link rel = "stylesheet" type = "text/css" href = "css/style.css" />
	</head>
<body>
	<nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
		<div class="container-fluid">
			<label class="navbar-brand">School File Management System</label>
			<?php 
				$query = mysqli_query($conn, "SELECT * FROM `user` WHERE `user_id` = '$_SESSION[user]'") or die(mysqli_error());
				$fetch = mysqli_fetch_array($query);
			?>
			<ul class = "nav navbar-right">	
				<li class = "dropdown">
					<a class = "user dropdown-toggle" data-toggle = "dropdown" href = "#">
						<span class = "glyphicon glyphicon-user"></span>
						<?php 
							echo $fetch['firstname']." ".$fetch['lastname'];
						?>
						<b class = "caret"></b>
					</a>
				<ul class = "dropdown-menu">
					<li>
						<a href = "logout.php"><i class = "glyphicon glyphicon-log-out"></i> Logout</a>
					</li>
				</ul>
				</li>
			</ul>
		</div>
	</nav>
	<?php include 'sidebar.php'?>
	<div id = "content">
		<br /><br /><br />
		<div class="alert alert-info"><h3>Accounts / Students</h3></div> 
		<button class="btn btn-success" data-toggle="modal" data-target="#form_modal"><span class="glyphicon glyphicon-plus"></span> Add Student</button>
		<br /><br />
		<table id = "table" class="table table-bordered">
			<thead>
				<tr>
					<th>Student no</th>
					<th>Firstname</th>
					<th>Lastname</th>
					<th>Gender</th>
					<th>Year/Section</th>
					<th>Password</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$query = mysqli_query($conn, "SELECT * FROM `student`") or die(mysqli_error());
					while($fetch = mysqli_fetch_array($query)){
				?>
					<tr class="del_student<?php echo $fetch['stud_id']?>">
						<td><?php echo $fetch['stud_no']?></td>
						<td><?php echo $fetch['firstname']?></td>
						<td><?php echo $fetch['lastname']?></td>
						<td><?php echo $fetch['gender']?></td>
						<td><?php echo $fetch['yr&sec']?></td>
						<td>********</td>
						<td><center><button class="btn btn-warning" data-toggle="modal" data-target="#edit_modal<?php echo $fetch['stud_id']?>"><span class="glyphicon glyphicon-edit"></span> Edit</button> 
						<button class="btn btn-danger btn-delete" id="<?php echo $fetch['stud_id']?>" type="button"><span class="glyphicon glyphicon-trash"></span> Delete</button></center></td>
					</tr>
	<div class="modal fade" id="edit_modal<?php echo $fetch['stud_id']?>" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<form method="POST" action="update_student.php">	
					<div class="modal-header">
						<h4 class="modal-title">Update Student</h4>
					</div>
					<div class="modal-body">
						<div class="col-md-3"></div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Student no</label>
								<input type="hidden" name="stud_id" value="<?php echo $fetch['stud_id']?>" class="form-control"/>
								<input type="number" name="stud_no" value="<?php echo $fetch['stud_no']?>" class="form-control" readonly="readonly"/>
							</div>
							<div class="form-group">
								<label>Firstname</label>
								<input type="text" name="firstname" value="<?php echo $fetch['firstname']?>" class="form-control" required="required"/>
							</div>
							<div class="form-group">
								<label>Lastname</label>
								<input type="text" name="lastname" value="<?php echo $fetch['lastname']?>" class="form-control" required="required"/>
							</div>
							<div class="form-group">
								<label>Gender</label>
								<select name="gender" class="form-control" required="required">
									<option value=""></option>
									<option value="Male">Male</option>
									<option value="Female">Female</option>
								</select>
							</div>
							<div class="form-inline">
								<label>Year</label>
								<select name="year" class="form-control" required="required">
									<option value=""></option>
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
								</select>
								<label>Section</label>
								<select name="section" class="form-control" required="required">
									<option value=""></option>
									<option value="A">A</option>
									<option value="B">B</option>
									<option value="C">C</option>
									<option value="D">D</option>
								</select>
							</div>
							<br />
							<div class="form-group">
								<label>Password</label>
								<input type="password" name="password" class="form-control" required="required"/>
							</div>
						</div>
					</div>
					<div style="clear:both;"></div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Close</button>
						<button name="update" class="btn btn-warning" ><span class="glyphicon glyphicon-save"></span> Update</button>
					</div>
				</form>
			</div>
		</div>
	</div>
				<?php
					}
				?>
			</tbody>
		</table>
	</div>
	<div class="modal fade" id="modal_confirm" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title">System</h3>
				</div>
				<div class="modal-body">
					<center><h4 class="text-danger">All files of the student will also be deleted.</h4></center>
					<center><h3 class="text-danger">Are you sure you want to delete this data?</h3></center>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-success" id="btn_yes">Yes</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="form_modal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<form method="POST" action="save_student.php">	
					<div class="modal-header">
						<h4 class="modal-title">Add Student</h4>
					</div>
					<div class="modal-body">
						<div class="col-md-3"></div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Student no</label>
								<input oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" type="number" maxlength="5" name="stud_no" class="form-control" required="required"/>
							</div>
							<div class="form-group">
								<label>Firstname</label>
								<input type="text" name="firstname" class="form-control" required="required"/>
							</div>
							<div class="form-group">
								<label>Lastname</label>
								<input type="text" name="lastname" class="form-control" required="required"/>
							</div>
							<div class="form-group">
								<label>Gender</label>
								<select name="gender" class="form-control" required="required">
									<option value=""></option>
									<option value="Male">Male</option>
									<option value="Female">Female</option>
								</select>
							</div>
							<div class="form-inline">
								<label>Year</label>
								<select name="year" class="form-control" required="required">
									<option value=""></option>
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
								</select>
								<label>Section</label>
								<select name="section" class="form-control" required="required">
									<option value=""></option>
									<option value="A">A</option>
									<option value="B">B</option>
									<option value="C">C</option>
									<option value="D">D</option>
								</select>
							</div>
							<br />
							<div class="form-group">
								<label>Password</label>
								<input type="password" name="password" class="form-control" required="required"/>
							</div>
						</div>
					</div>
					<div style="clear:both;"></div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Close</button>
						<button name="save" class="btn btn-success" ><span class="glyphicon glyphicon-save"></span> Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div id = "footer">
		<label class = "footer-title">&copy; Copyright School File Management System <?php echo date("Y", strtotime("+8 HOURS"))?></label>
	</div>
<?php include 'script.php'?>
<script type="text/javascript">
$(document).ready(function(){
	$('.btn-delete').on('click', function(){
		var stud_id = $(this).attr('id');
		$("#modal_confirm").modal('show');
		$('#btn_yes').attr('name', stud_id);
	});
	$('#btn_yes').on('click', function(){
		var id = $(this).attr('name');
		$.ajax({
			type: "POST",
			url: "delete_student.php",
			data:{
				stud_id: id
			},
			success: function(){
				$("#modal_confirm").modal('hide');
				$(".del_student" + id).empty();
				$(".del_student" + id).html("<td colspan='6'><center class='text-danger'>Deleting...</center></td>");
				setTimeout(function(){
					$(".del_student" + id).fadeOut('slow');
				}, 1000);
			}
		});
	});
});
</script>	
</body>
</html>