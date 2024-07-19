### Wappalyzer Technology Detection (tech-detect:owl-carousel) found on runner.htb

----
**Details**: **tech-detect:owl-carousel** matched at runner.htb

**Protocol**: HTTP

**Full URL**: http://runner.htb

**Timestamp**: Sun Apr 21 11:06:04 +0100 BST 2024

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
Host: runner.htb
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Safari/605.1.15 OPX/1.6.1
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
Date: Sun, 21 Apr 2024 10:06:04 GMT
Etag: W/"660d6aad-420e"
Last-Modified: Wed, 03 Apr 2024 14:41:49 GMT
Server: nginx/1.18.0 (Ubuntu)

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <meta name="copyright" content="MACode ID, https://macodeid.com">

  <title>Runner - CI/CD Specialists</title>

  <link rel="stylesheet" href="assets/vendor/animate/animate.css">

  <link rel="stylesheet" href="assets/css/bootstrap.css">

  <link rel="stylesheet" href="assets/css/maicons.css">

  <link rel="stylesheet" href="assets/vendor/owl-carousel/css/owl.carousel.css">

  <link rel="stylesheet" href="assets/css/theme.css">

</head>
<body>

  <!-- Back to top button -->
  <div class="back-to-top"></div>

  <header>
    <nav class="navbar navbar-expand-lg navbar-light navbar-float">
      <div class="container">
        <a href="index.html" class="navbar-brand"><span class="text-primary">Runner</span></a>

        <button class="navbar-toggler" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="navbar-collapse collapse" id="navbarContent">
          <ul class="navbar-nav ml-lg-4 pt-3 pt-lg-0">
            <li class="nav-item active">
              <a href="#home" class="nav-link">Home</a>
            </li>
            <li class="nav-item">
              <a href="#about" class="nav-link">About</a>
            </li>
            <li class="nav-item">
              <a href="#services" class="nav-link">Services</a>
            </li>
          </ul>

          <div class="ml-auto">
            <a href="mailto:sales@runner.htb" class="btn btn-outline rounded-pill">Get a Quote</a>
          </div>
        </div>
      </div>
    </nav>

    <div id="home" class="page-banner home-banner">
      <div class="container h-100">
        <div class="row align-items-center h-100">
          <div class="col-lg-6 py-3 wow fadeInUp">
            <h1 class="mb-4">Welcome to Runner</h1>
            <p class="text-lg mb-5">Welcome to Runner, where we specialize in seamless CI/CD solutions, ensuring your code journeys from development to deployment with speed and reliability.</p>
          </div>
          <div class="col-lg-6 py-3 wow zoomIn">
            <div class="img-place">
              <img src="assets/img/bg_image_1.png" alt="">
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <main>
    <div class="page-section features">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6 col-lg-4 py-3 wow fadeInUp">
            <div class="d-flex flex-row">
              <div class="img-fluid mr-3">
                <img src="assets/img/icon_pattern.svg" alt="">
              </div>
              <div>
                <h5>Automation and Integration</h5>
                <p>Runner provides automated testing, continuous integration, and version control integration services to ensure smooth collaboration and efficient code integration.</p>
              </div>
            </div>
          </div>
  
          <div class="col-md-6 col-lg-4 py-3 wow fadeInUp">
            <div class="d-flex flex-row">
              <div class="img-fluid mr-3">
                <img src="assets/img/icon_pattern.svg" alt="">
              </div>
              <div>
                <h5>Deployment and Pipeline Configuration</h5>
                <p>We specialize in continuous deployment and pipeline configuration, enabling seamless transitions from development to deployment environments.</p>
              </div>
            </div>
          </div>
  
          <div class="col-md-6 col-lg-4 py-3 wow fadeInUp">
            <div class="d-flex flex-row">
              <div class="img-fluid mr-3">
                <img src="assets/img/icon_pattern.svg" alt="">
              </div>
              <div>
                <h5>Monitoring and Optimization</h5>
                <p>Runner offers monitoring tools and optimization services to enhance performance, detect issues early, and optimize the CI/CD pipeline for maximum efficiency.</p>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- .container -->
    </div> <!-- .page-section -->
  
    <div class="page-section">
      <div id="about" class="container">
        <div class="row">
          <div class="col-lg-6 py-3 wow zoomIn">
            <div class="img-place text-center">
              <img src="assets/img/bg_image_2.png" alt="">
            </div>
          </div>
          <div class="col-lg-6 py-3 wow fadeInRight">
            <h2 class="title-section">Meet the <span class="marked".... Truncated ....
```


**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Safari/605.1.15 OPX/1.6.1' 'http://runner.htb'
```

----

Generated by [Nuclei v3.2.2](https://github.com/projectdiscovery/nuclei)