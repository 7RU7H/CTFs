### HTTP Missing Security Headers (http-missing-security-headers:access-control-allow-credentials) found on http://10.10.75.48
---
**Details**: **http-missing-security-headers:access-control-allow-credentials**  matched at http://10.10.75.48

**Protocol**: HTTP

**Full URL**: http://10.10.75.48

**Timestamp**: Tue Apr 18 11:00:51 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass |
| Tags | misconfig, headers, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.<br> |

**Request**
```http
GET / HTTP/1.1
Host: 10.10.75.48
User-Agent: Mozilla/5.0 (X11; OpenBSD i386) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36
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
Date: Tue, 18 Apr 2023 10:00:51 GMT
Etag: "4c97-5ce886fbdbcdf-gzip"
Last-Modified: Sun, 17 Oct 2021 08:44:29 GMT
Server: Apache/2.4.41 (Ubuntu)
Vary: Accept-Encoding

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
     <link type="image/x-icon" rel="icon" href="/assets/img/logo_t.png">

    <title>Sky Couriers</title>

    <!-- Bootstrap core CSS -->
    <link href="/assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    
    <!-- Owl Craousel -->
    <link rel="stylesheet" href="/assets/css/swiper.min.css">
        
    <!--FontAwesome -->
    <link rel="stylesheet" href="/assets/css/font-awesome.min.css">

    <!-- Custom styles for this template -->
    <link href="/assets/css/main.css" rel="stylesheet">
		
		<style>
		
			.btnFloatTrack {
					display:block;
					-webkit-box-shadow: 0 3px 12px 0 rgba(0, 0, 0, .3);
					-moz-box-shadow: 0 3px 12px 0 rgba(0, 0, 0, .3);
					box-shadow: 0 3px 12px 0 rgba(0, 0, 0, .3);
					border-radius: 50px;
					cursor: pointer;
					height: 50px;
					min-height: 50px;
					padding: 35px;
					-webkit-transition: box-shadow 0.1s ease 0s;
					-moz-transition: box-shadow 0.1s ease 0s;
					transition: box-shadow 0.1s ease 0s;
					width: 50px;
					min-width: 50px;
					position: fixed;
					top: 100px;
					right: 20px;
					z-index: 1000;
				background:#f5f5f5;
			}
			
			.btnFloatTrack img {
				z-index: 0;
				position: absolute;
				top: 25%;
				left: 25%;
				width: 43px;
				text-align: center;
			}
			
			
			.trackBoxOther {
				position: fixed;
    top: 100px;
    right: 20px;
    background: #f5f5f5;
    border-radius: 0px 15% 4px 4px;
    z-index: 9;
    -webkit-box-shadow: 0 3px 12px 0 rgba(0, 0, 0, .3);
    -moz-box-shadow: 0 3px 12px 0 rgba(0, 0, 0, .3);
    box-shadow: 0 3px 12px 0 rgba(0, 0, 0, .3);
			}



/*****popup****************/
#thover{
  position:fixed;
  background:#000;
  width:100%;
  height:100%;
  opacity: .6
}

#tpopup{
  position:absolute;
  width:600px;
  height:180px;
  background:#fff;
  left:50%;
  top:50%;
  border-radius:5px;
  padding:0px 0;
  margin-left:-320px; /* width/2 + padding-left */
  margin-top:-150px; /* height/2 + padding-top */
  text-align:center;
  box-shadow:0 0 10px 0 #000;
  z-index: 999999999999;
}
#tclose{
  position:absolute;
  background:black;
  color:white;
  right:-15px;
  top:-15px;
  border-radius:50%;
  width:30px;
  height:30px;
  line-height:30px;
  text-align:center;
  font-size:8px;
  font-weight:bold;
  font-family:'Arial Black', Arial, sans-serif;
  cursor:pointer;
  box-shadow:0 0 10px 0 #000;
}
		
		</style>
		
		

		
		
  </head>

  <body>

    <div class="withbg">
     <nav class="navbar navbar-expand-lg navbar-light bg-faded">
        <div class="container">
        <a class="navbar-brand" href="index.html"><img class="logo" src="/assets/img/logo.png"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="navbarNavDropdown" class="navbar-collapse collapse">
            <ul class="navbar-nav mr-auto"></ul>
            <ul class="navbar-nav with-marchant">
                <li class="nav-item"><a class="nav-link" href="index.html">Welcome</a></li>
                <li class="nav-item"><a class="nav-link" href="#service">Service</a></li>
                <li class="nav-item"><a class="nav-link" href="#feature">Feature</a></li>
                <li class="nav-item"><a class="nav-link" href="career.html">Career</a></li>
                <li class="nav-item"><a class="nav-link" href="#blog">Blog</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                <li class="nav-item"><a class="nav-link marchant" href="/v2/admin/login.html">Merchant Central</a></li>
            </ul>
        </div>
     </div>
    </nav>

    <main role="main" class="">
        
        <div class="row">
			<div class="">
				
				
			<div class="track-order-float wow bounceInRight hide" data-wow-duration="1.1s" data-wow-delay="0.1s" style="display: :none;">
				<h4>Track Order</h4>
				<form class="trackform" name="srchAwb" method="get" action="/v2/admin/track_orders">
					<div class="form-group m-b-15"> 
					</div>
					<div class="form-group">
						<div class="input-group">
							<input type="text" class="form-control tracktext" name="awb" id="filter" placeholder="Enter AWB/Waybill number" required> 
						</div> 
						<span class="msg">* Track multiple orders separated by “space”</span> 
					</div> 
					<button class="btn btn-track btn-block" id="TrackShipmnt1" type="submit" name="srchorder"> <span class="txt-srch">Search</span></button> 
				</form>
			</div>
				
				
				
			</div>
		</div>
    <!-- .... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; OpenBSD i386) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36' 'http://10.10.75.48'
```
---
Generated by [Nuclei 2.9.1](https://github.com/projectdiscovery/nuclei)