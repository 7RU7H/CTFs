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
		<div class="alert alert-info"><h3>Mission</h3></div> 
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus et est aliquam, faucibus nulla vel, pharetra sapien. Quisque in ante ac orci feugiat facilisis. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam sodales ex sit amet nisi pellentesque vehicula. Nunc condimentum sapien ut semper facilisis. Duis faucibus ex ut tristique venenatis. Etiam at consectetur sem. Integer volutpat tellus eu lorem aliquet, id euismod mi scelerisque. Pellentesque ex diam, euismod quis elit eget, tempor fermentum lectus. Mauris ex tortor, vehicula quis ipsum et, interdum interdum magna. Duis rhoncus ornare diam. Integer porttitor maximus arcu. Integer interdum tellus elit, vitae luctus dolor sollicitudin sit amet. Nam et eleifend mi, non molestie massa. Pellentesque arcu enim, hendrerit ut leo id, venenatis pharetra leo.</p>
		<div class="alert alert-info"><h3>Vision</h3></div> 
		<p>Pellentesque vitae ullamcorper nunc. Ut malesuada arcu quis rhoncus sodales. Phasellus eu felis id est tincidunt gravida a sit amet mauris. Nulla mi eros, molestie quis mi eget, placerat dictum dolor. Quisque consectetur at sapien nec faucibus. Phasellus sed lorem elit. Cras ut pharetra arcu. Suspendisse rhoncus felis non justo ultricies varius. Curabitur aliquet, orci tristique iaculis laoreet, nibh ligula luctus turpis, non imperdiet nisi diam et nibh. Mauris auctor nibh risus, quis dignissim diam vestibulum sit amet. Sed sollicitudin risus quis tortor suscipit vulputate. Ut a vulputate turpis. Aenean ullamcorper tempus vulputate. Donec scelerisque, sem eget volutpat semper, dolor diam ullamcorper augue, eu facilisis nunc massa ac lectus.</p>
	</div>
	</div>
	<div id = "footer">
		<label class = "footer-title">&copy; Copyright School File Management System <?php echo date("Y", strtotime("+8 HOURS"))?></label>
	</div>
<?php include 'script.php'?>	
</body>
</html>