<div class="col-md-4"></div>
<div class="col-md-4">
	<div class="panel panel-default" id="panel-margin">
		<div class="panel-heading">
			<center><h1 class="panel-title">Student Login</h1></center>
		</div>
		<div class="panel-body">
			<form method="POST">
				<div class="form-group">
					<label for="username">Student no</label>
					<input oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" type="number" maxlength="5" name="stud_no" class="form-control" required="required"/>
				</div>
				<div class="form-group">
					<label for="password">Password</label>
					<input class="form-control" name="password" type="password" required="required" >
				</div>
				<?php include 'login_query.php'?>
				<div class="form-group">
					<button class="btn btn-block btn-primary"  name="login"><span class="glyphicon glyphicon-log-in"></span> Login</button>
				</div>
			</form>
		</div>
	</div>
</div>	