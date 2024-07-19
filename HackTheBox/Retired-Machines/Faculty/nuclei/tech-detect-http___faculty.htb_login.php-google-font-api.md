### Wappalyzer Technology Detection (tech-detect:google-font-api) found on http://10.129.64.49
---
**Details**: **tech-detect:google-font-api**  matched at http://10.129.64.49

**Protocol**: HTTP

**Full URL**: http://faculty.htb/login.php

**Timestamp**: Mon Aug 8 20:27:39 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Wappalyzer Technology Detection |
| Authors | hakluke |
| Tags | tech |
| Severity | info |

**Request**
```http
GET / HTTP/1.1
Host: 10.129.64.49
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1866.237 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Transfer-Encoding: chunked
Cache-Control: no-store, no-cache, must-revalidate
Content-Type: text/html; charset=UTF-8
Date: Mon, 08 Aug 2022 19:27:37 GMT
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Pragma: no-cache
Server: nginx/1.18.0 (Ubuntu)
Set-Cookie: PHPSESSID=bl3uq9hhn1o8nga4k99neaor8i; path=/

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>School Faculty Scheduling System</title>
 	

<meta content="" name="descriptison">
  <meta content="" name="keywords">

  

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link rel="stylesheet" href="admin/assets/font-awesome/css/all.min.css">


  <!-- Vendor CSS Files -->
  <link href="admin/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="admin/assets/vendor/icofont/icofont.min.css" rel="stylesheet">
  <link href="admin/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="admin/assets/vendor/venobox/venobox.css" rel="stylesheet">
  <link href="admin/assets/vendor/animate.css/animate.min.css" rel="stylesheet">
  <link href="admin/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="admin/assets/vendor/owl.carousel/assets/owl.carousel.min.css" rel="stylesheet">
  <link href="admin/assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
  <link href="admin/assets/DataTables/datatables.min.css" rel="stylesheet">
  <link href="admin/assets/css/jquery.datetimepicker.min.css" rel="stylesheet">
  <link href="admin/assets/fullcalendar/main.css" rel="stylesheet">
  <link href="admin/assets/css/select2.min.css" rel="stylesheet">


  <!-- Template Main CSS File -->
  <link href="admin/assets/css/style.css" rel="stylesheet">
  <link type="text/css" rel="stylesheet" href="admin/assets/css/jquery-te-1.4.0.css">
  
  <script src="admin/assets/vendor/jquery/jquery.min.js"></script>
  <script src="admin/assets/DataTables/datatables.min.js"></script>
  <script src="admin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="admin/assets/vendor/jquery.easing/jquery.easing.min.js"></script>
  <script src="admin/assets/vendor/php-email-form/validate.js"></script>
  <script src="admin/assets/vendor/venobox/venobox.min.js"></script>
  <script src="admin/assets/vendor/waypoints/jquery.waypoints.min.js"></script>
  <script src="admin/assets/vendor/counterup/counterup.min.js"></script>
  <script src="admin/assets/vendor/owl.carousel/owl.carousel.min.js"></script>
  <script src="admin/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript" src="admin/assets/js/select2.min.js"></script>
    <script type="text/javascript" src="admin/assets/js/jquery.datetimepicker.full.min.js"></script>
    <script type="text/javascript" src="admin/assets/font-awesome/js/all.min.js"></script>
    <script type="text/javascript" src="admin/assets/fullcalendar/main.js"></script>
  <script type="text/javascript" src="admin/assets/js/jquery-te-1.4.0.min.js" charset="utf-8"></script>




</head>
<style>
	body{
		width: 100%;
	    height: calc(100%);
		position:fixed;
	}
	#main{
		width: calc(100%);
	    height: calc(100%);
		display:flex;
		align-items:center;
		justify-content:center
	}
	#login{
		
	}
	

</style>

<body>


  <main id="main" class=" bg-dark">
  		<div id="login" class="col-md-4">
  			<div class="card">
  				<div class="card-body">
  						
  					<form id="login-form" >
					  <h4><b>Welcome To Faculty Scheduling System</b></h4>
  						<div class="form-group">
  							<label for="id_no" class="control-label">Please enter your Faculty ID No.</label>
  							<input type="text" id="id_no" name="id_no" class="form-control">
  						</div>
  						<center><button class="btn-sm btn-block btn-wave col-md-4 btn-primary">Login</button></center>
  					</form>
  				</div>
  			</div>
  		</div>
   

  </main>

  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>


</body>
<script>
	$('#login-form').submit(function(e){
		e.preventDefault()
		$('#login-form button[type="button"]').attr('disabled',true).html('Logging in...');
		if($(this).find('.alert-danger').length > 0 )
			$(this).find('.alert-danger').remove();
		$.ajax({
			url:'admin/ajax.php?action=login_faculty',
			method:'POST',
			data:$(this).serialize(),
			error:err=>{
				console.log(err)
		$('#login-form button[type="button"]').removeAttr('disabled').html('Login');

			},
			success:function(resp){
				if(resp == 1){
					location.href ='index.php';
				}else{
					$('#login-form').prepend('<div class="alert alert-danger">ID Number is incorrect.</div>')
					$('#login-form button[type="button"]')..... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'Referer: http://faculty.htb' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1866.237 Safari/537.36' 'http://faculty.htb/login.php'
```
---
Generated by [Nuclei 2.7.5](https://github.com/projectdiscovery/nuclei)