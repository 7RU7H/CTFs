### HTTP Missing Security Headers (http-missing-security-headers:x-permitted-cross-domain-policies) found on https://10.200.96.200/

----
**Details**: **http-missing-security-headers:x-permitted-cross-domain-policies** matched at https://10.200.96.200/

**Protocol**: HTTP

**Full URL**: https://10.200.96.200/

**Timestamp**: Tue Aug 22 11:46:34 +0100 BST 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass, jub0bs |
| Tags | misconfig, headers, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.<br> |

**Request**
```http
GET / HTTP/1.1
Host: 10.200.96.200
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1944.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 15383
Accept-Ranges: bytes
Content-Type: text/html; charset=UTF-8
Date: Tue, 22 Aug 2023 10:46:31 GMT
Etag: "3c17-5b38ba949f993"
Last-Modified: Sat, 07 Nov 2020 22:15:05 GMT
Server: Apache/2.4.37 (centos) OpenSSL/1.1.1c

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Thomas Wreath | Developer</title>

    <!-- favicon -->
    <link href="favicon.png" rel=icon>

    <!-- web-fonts -->
    <link href="https://fonts.googleapis.com/css?family=Hind:300,400,500,600,700" rel="stylesheet">

    <!-- font-awesome -->
    <link href="css/font-awesome.min.css" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Style CSS -->
    <link href="css/style.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body id="page-top" data-spy="scroll" data-target=".navbar">
<div id="main-wrapper">
<!-- Page Preloader -->
<div id="preloader">
    <div id="status">
        <div class="status-mes"></div>
    </div>
</div>

<div class="columns-block">
<div class="left-col-block blocks">
    <header class="header">
        <div class="content text-center">
            <h1>Hi, I'm Thomas Wreath</h1>

            <p class="lead">Developer and Sysadmin</p>
            <ul class="social-icon">
                <li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                <li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                <li><a href="#"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
                <li><a href="#"><i class="fa fa-github" aria-hidden="true"></i></a></li>
            </ul>
        </div>
        <div class="profile-img"></div>
    </header>
    <!-- .header-->
</div>


<div class="right-col-block blocks">
<section class="intro section-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title">
                    <h2>What I am all about.</h2>
                </div>
            </div>
            <div class="col-md-12">
                <p>
			I am a sysadmin and developer with a passion for tech! My specialisms are full-stack web development and software dev. I have a track record for providing fast, efficient and dynamic solutions for my clients -- both recently in my freelance work, and previously as the team lead of a software development team in Solihull, UK. 
                </p>
		<p>
		Please find my CV below.<br>I look forward to hearing from you!
		</p>
            </div>
        </div>
    </div>
</section>


<section class="expertise-wrapper section-wrapper gray-bg">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title">
                    <h2>Expertise</h2>
                </div>
            </div>
        </div>
        <!-- .row -->

        <div class="row">
            <div class="col-md-6">
                <div class="expertise-item">
                    <h3>Full-Stack Web Development</h3>

                    <p>
		    	10 years on-and-off experience as a full-stack web developer, specialising in CentOS LAMP installations. Preference for PHP development, but with extensive knowledge of full-stack development in Python, Node.js and Golang.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="expertise-item">
                    <h3>Network Design and Architecture</h3>

                    <p>
		    	Interested in how networks work from a young age. Worked as a systems administrator for 5 years. Experienced at designing, implementing and maintaining networks comprised of Windows, Linux and BSD hosts (as well as any necessary embedded systems).
                    </p>
                </div>
            </div>
        </div>
        <!-- .row -->
        <div class="row">
            <div class="col-md-6">
                <div class="expertise-item">
                    <h3>Software Development</h3>

                    <p>
		    	Started developing simple programs as a child and maintained the skill as a hobby until learning formally at university, resulting in 25 years of software development experience. Seven of these were working professionally as a software developer.
                    </p>
                </div>
            </div>

            <div class="col-md-6">
   .... Truncated ....
```


**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1944.0 Safari/537.36' 'https://10.200.96.200/'
```

----

Generated by [Nuclei v2.9.11](https://github.com/projectdiscovery/nuclei)