### HTTP Missing Security Headers (http-missing-security-headers:cross-origin-resource-policy) found on https://10.129.163.144/
---
**Details**: **http-missing-security-headers:cross-origin-resource-policy**  matched at https://10.129.163.144/

**Protocol**: HTTP

**Full URL**: https://10.129.163.144/

**Timestamp**: Sat Jun 17 20:18:18 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass, jub0bs |
| Tags | misconfig, headers, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.<br> |

**Request**
```http
GET / HTTP/1.1
Host: 10.129.163.144
User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F
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
Content-Type: text/html; charset=utf-8
Date: Sat, 17 Jun 2023 19:17:47 GMT
Server: nginx/1.18.0 (Ubuntu)

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
	<title>Secret Spy Agency | Secret Security Service</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="/static/favicon.ico"/>
        <!-- Bootstrap icons-->
	<link rel="stylesheet" href="/static/bootstrap-icons2.css">
	<link href="/static/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="/static/styles.css" rel="stylesheet" />
    </head>
    <body class="d-flex flex-column min-vh-100">
	    
<!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container px-lg-5">
		    <img class="logo" src="/static/circleLogo2.png"></img>
		    <a class="navbar-brand" href="/">Secret Spy Agency</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link active" aria-current="page" href="#!">Home</a></li>
			<li class="nav-item"><a class="nav-link" href="/about">About</a></li>
			<li class="nav-item"><a class="nav-link" href="/contact">Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Header-->
        <header class="py-5">
            <div class="container px-lg-5">
                <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
                    <div class="m-4 m-lg-5">
                        <h1 class="display-5 fw-bold">Our Mission</h1>
                        <p class="fs-4">We leverage our advantages in technology and cybersecurity consistent with our authorities to strengthen national defense and secure national security systems.</p>
			<a class="btn btn-primary btn-lg" href="/about">EXPERIENCE SSA</a>
                    </div>
                </div>
            </div>
        </header>
        <!-- Page Content-->
        <section class="pt-4">
            <div class="container px-lg-5">
                <!-- Page Features-->
                <div class="row gx-lg-5">
                    <div class="col-lg-6 col-xxl-4 mb-5">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-lightbulb"></i></div>
                                <h2 class="fs-4 fw-bold">Research</h2>
                                <p class="mb-0">SSA invests in a world-class workforce and partnerships with academia and industry to deliver capabilities that secure the nation’s future.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xxl-4 mb-5">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-fingerprint"></i></div>
                                <h2 class="fs-4 fw-bold">Signals Intelligence</h2>
                                <p class="mb-0">SSA provides foreign signals intelligence (SIGINT) to our nation's policymakers and military forces. SIGINT plays a vital role in our national security by providing our nation's leaders with critical information they need to defend our country, save lives, and advance our goals and alliances globally.                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xxl-4 mb-5">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-book"></i></div>
                                <h2 class="fs-4 fw-bold">Academics</h2>
                                <p class="mb-0">SSA partners with schools to help cultivate the next generation of experts in science, technology, engineering, math, language and analysis to protect the nation.</p>
                            </div>
                  .... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F' 'https://10.129.163.144/'
```
---
Generated by [Nuclei v2.9.4](https://github.com/projectdiscovery/nuclei)