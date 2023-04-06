### WAF Detection (waf-detect:ats) found on http://10.10.112.119
---
**Details**: **waf-detect:ats**  matched at http://10.10.112.119

**Protocol**: HTTP

**Full URL**: http://10.10.112.119/

**Timestamp**: Thu Apr 6 13:51:19 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | WAF Detection |
| Authors | dwisiswant0, lu4nx |
| Tags | waf, tech, misc |
| Severity | info |
| Description | A web application firewall was detected. |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 0.00 |

**Request**
```http
POST / HTTP/1.1
Host: 10.10.112.119
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1866.237 Safari/537.36
Connection: close
Content-Length: 27
Content-Type: application/x-www-form-urlencoded
Accept-Encoding: gzip

_=<script>alert(1)</script>
```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Accept-Ranges: bytes
Content-Type: text/html; charset=UTF-8
Date: Thu, 06 Apr 2023 12:51:18 GMT
Etag: "64f4-5e4e195780a80-gzip"
Last-Modified: Thu, 28 Jul 2022 18:20:10 GMT
Server: nginx/1.23.1
Vary: Accept-Encoding

<!DOCTYPE html>
<!--[if IE 8 ]><html class="no-js oldie ie8" lang="en"> <![endif]-->
<!--[if IE 9 ]><html class="no-js oldie ie9" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html class="no-js" lang="en"> <!--<![endif]-->
<head>

   <!--- basic page needs
   ================================================== -->
   <meta charset="utf-8">
	<title>Infinity</title>
	<meta name="description" content="">  
	<meta name="author" content="">

   <!-- mobile specific metas
   ================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

 	<!-- CSS
   ================================================== -->
   <link rel="stylesheet" href="css/base.css">
   <link rel="stylesheet" href="css/vendor.css">  
   <link rel="stylesheet" href="css/main.css">  

   <!-- script
   ================================================== -->
	<script src="js/modernizr.js"></script>
	<script src="js/pace.min.js"></script>

   <!-- favicons
	================================================== -->
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<link rel="icon" href="favicon.ico" type="image/x-icon">

</head>

<body id="top">

	<!-- header 
   ================================================== -->
   <header> 

   	<div class="header-logo">
	      <a href="index.html">Infinity</a>
	   </div> 

		<a id="header-menu-trigger" href="#0">
		 	<span class="header-menu-text">Menu</span>
		  	<span class="header-menu-icon"></span>
		</a> 

		<nav id="menu-nav-wrap">

			<a href="#0" class="close-button" title="close"><span>Close</span></a>	

	   	<h3>Infinity.</h3>  

			<ul class="nav-list">
				<li class="current"><a class="smoothscroll" href="#home" title="">Home</a></li>
				<li><a class="smoothscroll" href="#about" title="">About</a></li>
				<li><a class="smoothscroll" href="#services" title="">Services</a></li>
				<li><a class="smoothscroll" href="#portfolio" title="">Works</a></li>
				<li><a class="smoothscroll" href="#contact" title="">Contact</a></li>						
			</ul>	

			<p class="sponsor-text">
				Looking for an awesome and reliable webhosting? Try <a href="http://www.dreamhost.com/r.cgi?287326|STYLESHOUT">DreamHost</a>.
				Get <span>$50 off</span> when you sign up with the promocode <span>styleshout</span>. 
				<!-- Simply type	the promocode in the box labeled “Promo Code” when placing your order. -->
			</p>			

			<ul class="header-social-list">
	         <li>
	         	<a href="#"><i class="fa fa-facebook-square"></i></a>
	         </li>
	         <li>
	         	<a href="#"><i class="fa fa-twitter"></i></a>
	         </li>
	         <li>
	         	<a href="#"><i class="fa fa-instagram"></i></a>
	         </li>
            <li>
            	<a href="#"><i class="fa fa-behance"></i></a>
            </li>
	         <li>
	         	<a href="#"><i class="fa fa-dribbble"></i></a>
	         </li>	         
	      </ul>		

		</nav>  <!-- end #menu-nav-wrap -->

	</header> <!-- end header -->  


   <!-- home
   ================================================== -->
   <section id="home">

   	<div class="overlay"></div>

   	<div class="home-content-table">	
		   <div class="home-content-tablecell">
		   	<div class="row">
		   		<div class="col-twelve">		   			
			  		
			  				<h3 class="animate-intro">We Are Infinity.</h3>
				  			<h1 class="animate-intro">
							We Craft Stunning  <br>
							Digital Experiences.
				  			</h1>	

				  			<div class="more animate-intro">
				  				<a class="smoothscroll button stroke" href="#about">
				  					Learn More
				  				</a>
				  			</div>							

			  		</div> <!-- end col-twelve --> 
		   	</div> <!-- end row --> 
		   </div> <!-- end home-content-tablecell --> 		   
		</div> <!-- end home-content-table -->

		<ul class="home-social-list">
	      <li class="animate-intro">
	        	<a href="#"><i class="fa fa-facebook-square"></i></a>
	      </li>
	      <li class="animate-intro">
	        	<a href="#"><i class="fa fa-twitter"></i></a>
	      </li>
	      <li class="animate-intro">
	        	<a href="#"><i class="fa fa-instagram"></i></a>
	      </li>
         <li class="animate-intro">
           	<a href="#"><i class="fa fa-behance"></i></a>
         </li>
	      <li class="animate-intro">
	        	<a href="#"><i class="fa fa-dribbble"></i></a>
	      </li>	      
	   </ul> <!-- end home-social-list -->	

		<div class="scrolldown">
			<a href="#about" class="scroll-icon smoothscroll">		
		   	Scroll Down		   	
		   	<i class="fa fa-long-arrow-right" aria-hidden="true"></i>
			</a>
		</div>			
   
   </section> <!-- end home -->


   <!-- about
   ================================================== -->
   <section id="about">

   	<div class="row about-wrap">
   		<div class="col-full">

   			<div class="about-profile-bg"></d.... Truncated ....
```

References: 
- https://github.com/ekultek/whatwaf

**CURL Command**
```
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 10.10.112.119' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1866.237 Safari/537.36' 'http://10.10.112.119/'
```
---
Generated by [Nuclei 2.9.0](https://github.com/projectdiscovery/nuclei)