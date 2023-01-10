### WAF Detection (waf-detect:apachegeneric) found on http://10.10.66.205
---
**Details**: **waf-detect:apachegeneric**  matched at http://10.10.66.205

**Protocol**: HTTP

**Full URL**: http://10.10.66.205/

**Timestamp**: Tue Jan 10 19:35:03 +0000 GMT 2023

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
Host: 10.10.66.205
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
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
Content-Type: text/html
Date: Tue, 10 Jan 2023 19:35:03 GMT
Etag: "19a8-5d940fff601c0-gzip"
Last-Modified: Wed, 02 Mar 2022 19:07:59 GMT
Server: Apache/2.4.48 (Ubuntu)
Vary: Accept-Encoding

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>CyberHeros : Index</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet"> -->

  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <link href="assets/css/style.css" rel="stylesheet">

</head>


<body>
  <i class="bi bi-list mobile-nav-toggle d-xl-none"></i>
  <header id="header">
    <div class="d-flex flex-column">

      <div class="profile">
        <img src="assets/img/profile-img.png" alt="" class="img-fluid">
        <h1 class="text-light"><a href="index.html">CyberHeros</a></h1>
      </div>

      <nav id="navbar" class="nav-menu navbar">
        <ul>
          <li><a href="#hero" class="nav-link scrollto active"><i class="bx bx-home"></i> <span>Home</span></a></li>
          <li><a href="#about" class="nav-link scrollto"><i class="bx bx-user"></i> <span>About</span></a></li>
          <li><a href="login.html" class="nav-link scrollto"><i class="bx bx-server"></i> <span>Login</span></a></li>
        </ul>
      </nav>
    </div>
  </header>

  <section id="hero" class="d-flex flex-column justify-content-center align-items-center">
    <div class="hero-container" data-aos="fade-in">
      <br><br><br><br><br><br><br>
      <h1>Welcome Heros</h1>
      <p>We are <span class="typed" data-typed-items="Hackers, Developers, Bug Hunters, Cyber Warriors, Cyber Gaurdians"></span></p>
    </div>
  </section>

  <main id="main">
    <section id="about" class="about facts">
      <div class="container">

        <div class="section-title">
          <h2>About Us</h2>
          <p>We are a group of young Hackers, Developers, Bug Hunters, Cyber Warriors, Cyber Gaurdians. We find vulnerabilities in the website legally and save them from getting hacked, Join our forces, find the vuln on our login page and login to join us. :D</p>
          <br><h5>Heros are not born, They work hard and become one ! :D</h5>
        </div>

        <div class="section-title">
          <h2>Facts</h2>
          <p>Some Awesome Facts about our CyberHeros are team.</p>
        </div>

        <div class="row no-gutters">

          <div class="col-lg-3 col-md-6 d-md-flex align-items-md-stretch" data-aos="fade-up">
            <div class="count-box">
              <i class="bi bi-emoji-smile"></i>
              <span data-purecounter-start="0" data-purecounter-end="738" data-purecounter-duration="1" class="purecounter"></span>
              <p><strong>Internet Users</strong><br>Protected</p>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 d-md-flex align-items-md-stretch" data-aos="fade-up" data-aos-delay="100">
            <div class="count-box">
              <i class="bi bi-journal-richtext"></i>
              <span data-purecounter-start="0" data-purecounter-end="521" data-purecounter-duration="1" class="purecounter"></span>
              <p><strong>Websites</strong></br>Protected</p>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 d-md-flex align-items-md-stretch" data-aos="fade-up" data-aos-delay="200">
            <div class="count-box">
              <i class="bi bi-headset"></i>
              <span data-purecounter-start="0" data-purecounter-end="1453" data-purecounter-duration="1" class="purecounter"></span>
              <p><strong>Support</strong><br>Members Supported</p>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 d-md-flex align-items-md-stretch" data-aos="fade-up" data-aos-delay="300">
            <div class="count-box">
              <i class="bi bi-people"></i>
              <span data-purecounter-start="0" data-purecounter-end="80" data-purecounter-duration="1" class="purecounter"></span>
              <p><strong>CyberHeros</strong><br>Our team members</p>
            </div>
          </div>


          <div class="section-title">
            <h2>Join Us</h2>
            <p>A.... Truncated ....
```

References: 
- https://github.com/ekultek/whatwaf

**CURL Command**
```
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 10.10.66.205' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://10.10.66.205/'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)