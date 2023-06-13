### Wappalyzer Technology Detection (tech-detect:jsdelivr) found on http://soccer.htb
---
**Details**: **tech-detect:jsdelivr**  matched at http://soccer.htb

**Protocol**: HTTP

**Full URL**: http://soccer.htb

**Timestamp**: Tue Mar 28 08:44:05 +0100 BST 2023

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
Host: soccer.htb
User-Agent: Mozilla/5.0 (Windows NT 4.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36
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
Content-Type: text/html
Date: Tue, 28 Mar 2023 07:44:04 GMT
Etag: W/"6375ebaf-1b05"
Last-Modified: Thu, 17 Nov 2022 08:07:11 GMT
Server: nginx/1.18.0 (Ubuntu)

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>    
        <title>Soccer - Index </title>
    </head>
    <style>
        header {
            position: relative;
            background-color: black;
            background-image: url('football.jpg');
            height: 75vh;
            min-height: 25rem;
            width: 100%;
            overflow: hidden;
        }

        .display-3 {
            color: greenyellow;
        }

        h1 {
            text-align: center;
        }

        header video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: 0;
            -ms-transform: translateX(-50%) translateY(-50%);
            -moz-transform: translateX(-50%) translateY(-50%);
            -webkit-transform: translateX(-50%) translateY(-50%);
            transform: translateX(-50%) translateY(-50%);
        }

        header .container {
            position: relative;
            z-index: 2;
        }

        header .overlay {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            opacity: 0.5;
            z-index: 1;
        }

        @media (pointer: coarse) and (hover: none) {
            header {
                background: url('img/football.jpg');
            }

            header video {
                display: none;
            }
        }
    </style>
    <body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	    <div class="container-fluid">
		<a class="navbar-brand" href="/">Soccer</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
		    <span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarNavAltMarkup">
		    <div class="navbar-nav">
			<a class="nav-link active" aria-current="page" href="/">Home</a>
		    </div>
		</div>
	    </div>
	</nav>                                                                                                 
        <header>
            <div class="container h-100">
                <div class="d-flex h-100 text-center align-items-center">
                    <div class="w-100 text-white">
                        <h1 class="display-3">HTB FootBall Club</h1>
                        <p class="lead mb-0">"We Love Soccer"</p>
                    </div>
                </div>
            </div>
            </style>
        </header>
        <section class="my-5">
            <div class="container">
                <div class="row">
                        <p>Due to the scope and popularity of the sport, professional football clubs carry a significant commercial existence, with fans expecting personal service and interactivity, and stakeholders viewing the field of professional football as a source of significant business advantages. For this reason, expensive player transfers have become an expectable part of the sport. Awards are also handed out to managers or coaches on a yearly basis for excellent performances. The designs, logos and names of professional football clubs are often licensed trademarks. The difference between a football team and a (professional) football club is incorporation, a football club is an entity which is formed and governed by a committee and has members which may consist of supporters in addition to players. </p>
                </div>
            </div>
        </section>
        <!-- Page Content -->
        <h1>Latest News</h1>
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-0 sh.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 4.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36' 'http://soccer.htb'
```
---
Generated by [Nuclei 2.9.0](https://github.com/projectdiscovery/nuclei)