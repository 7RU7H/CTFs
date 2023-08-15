### Wappalyzer Technology Detection (tech-detect:bootstrap) found on http://dnsalias.htb

----
**Details**: **tech-detect:bootstrap** matched at http://dnsalias.htb

**Protocol**: HTTP

**Full URL**: http://dnsalias.htb

**Timestamp**: Tue Aug 15 13:49:09 +0100 BST 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | Wappalyzer Technology Detection |
| Authors | hakluke |
| Tags | tech |
| Severity | info |

**Request**
```http
GET / HTTP/1.1
Host: dnsalias.htb
User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686 on x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2820.59 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Accept-Ranges: bytes
Content-Type: text/html
Date: Tue, 15 Aug 2023 12:49:08 GMT
Etag: "2a9d-5bd688dfd7600-gzip"
Last-Modified: Sat, 13 Mar 2021 10:34:00 GMT
Server: Apache/2.4.41 (Ubuntu)
Vary: Accept-Encoding

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="assets/img/favicon.ico">
    <title>Dyna DNS</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
 	<link href="assets/css/animate.css" rel="stylesheet">
  	<link href="assets/css/prettyPhoto.css" rel="stylesheet">
  	<link href="assets/css/pe-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Cabin:300,400,700,300italic,400italic' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Lato:400,300,700' rel='stylesheet' type='text/css'>
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	  <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->    
	<script src="assets/js/jquery.js"></script>  
	<script src="assets/js/modernizr.custom.js"></script>   
	<script type="text/javascript">
	$(document).ready(function(){
	  	jQuery('#headerwrap').backstretch([
	      "assets/img/bg/bg1.jpg",
	      "assets/img/bg/bg2.jpg",
	      "assets/img/bg/bg3.jpg"
	    ], {duration: 8000, fade: 500});

		// Map Position And Settings
	    $("#mapwrapper").gMap({ controls: false,
	        scrollwheel: false,
	        markers: [{     
	            latitude:40.7566,
	            longitude: -73.9863,
	        icon: { image: "assets/img/marker.png",
	            iconsize: [44,44],
	            iconanchor: [12,46],
	            infowindowanchor: [12, 0] } }],
	        icon: { 
	            image: "assets/img/marker.png", 
	            iconsize: [26, 46],
	            iconanchor: [12, 46],
	            infowindowanchor: [12, 0] },
	        latitude:40.7566,
	        longitude: -73.9863,
	        zoom: 14 
	    });
	});
	</script>
</head>

<body> 

    <!-- END NAV -->
    <nav class="menu" id="theMenu">
        <div class="menu-wrap">
            <i class="fa fa-bars menu-close"></i>
            <div id="menu-logo">
                <h2><span class="pe-7s-box2 logo-icon"></span> Dyna DNS</h2>
            </div>

            <ul id="main-menu">
            	<li><a href="#headerwrap">Home <i class="fa fa-home menu-icon"></i></a></li>
            	<li><a href="#services">Services <i class="fa fa-gears menu-icon"></i></a></li>
            </ul>

            <ul id="social-icons">
                <li class="facebook"><a href="#"><i class="fa fa-facebook"></i></a></li>
                <li class="twitter"><a href="#"><i class="fa fa-twitter"></i></a></li>
                <li class="dribbble"><a href="#"><i class="fa fa-dribbble"></i></a></li>
            </ul>
        </div>
    </nav>
    <!-- END NAV -->
	
	<!-- MAIN IMAGE SECTION -->
	<div id="headerwrap">
		<div class="container">
			<div class="row">
				<div id="bannertext" class="col-lg-8 col-lg-offset-2">
					<h1 class="fade-down gap"><span class="pe-7s-box2"></span> Dyna DNS</h1>
					<h2 class="fade-up">Awesome Dynamic DNS</h2>
					<div class="spacer"></div>			
				</div>
			</div><!-- row -->
		</div><!-- /container -->
		<span class="headerwrap-btn-wrap">
			<a href="#content-wrapper" class="headerwrap-btn"><i class="fade-up fa fa-angle-down"></i></a>
		</span>	
	</div><!-- /headerwrap -->

		<section id="services" class="divider-wrapper">
			<div class="container">
	        	<div class="centered gap fade-down section-heading">
	                <h2 class="main-title">Our Services</h2>
	                <hr>
	                <p>She evil face fine calm have now. Separate screened he outweigh of distance landlord.</p>
	            </div>
				<div class="row">
					<div class="col-lg-1 col-md-1 col-sm-1 centered bounce-in">
						<span class="pe-7s-share icon"></span>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 fade-up">
						<h3>Quality Dynamic DNS</h3>
						<p>We are providing dynamic DNS for anyone with the same API as no-ip.com has. Maintaining API conformance helps make clients work properly.</p>
					</div>

					<div class="col-lg-1 col-md-1 col-sm-1 centered bounce-in">
						<i class="fa fa-thumbs-up"></i>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 fade-up">
						<h3>Awesome Domains</h3>
						<p>We are providing Dynamic DNS for a number of domains:<ul><li>dnsalias.htb</li><li>dynamicdns.htb</li><li>no-ip.htb</li></ul></p>
					</div>			
				
					<div class="col-lg-1 col-md-1 col-sm-1 centered bounce-in">
						<i class="fa fa-wrench"></i>
					</div>
					<div class="col-lg-3 col-md-3 col-s.... Truncated ....
```


**CURL command**
```sh
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686 on x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2820.59 Safari/537.36' 'http://dnsalias.htb'
```

----

Generated by [Nuclei v2.9.8](https://github.com/projectdiscovery/nuclei)