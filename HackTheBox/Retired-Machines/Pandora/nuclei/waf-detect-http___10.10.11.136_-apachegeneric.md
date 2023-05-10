### WAF Detection (waf-detect:apachegeneric) found on http://10.10.11.136
---
**Details**: **waf-detect:apachegeneric**  matched at http://10.10.11.136

**Protocol**: HTTP

**Full URL**: http://10.10.11.136/

**Timestamp**: Wed May 10 18:12:04 +0100 BST 2023

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
Host: 10.10.11.136
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2866.71 Safari/537.36
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
Date: Wed, 10 May 2023 17:12:03 GMT
Etag: "8318-5d23e548bc656-gzip"
Last-Modified: Fri, 03 Dec 2021 14:00:31 GMT
Server: Apache/2.4.41 (Ubuntu)
Vary: Accept-Encoding

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Play | Landing</title>

    <!-- Primary Meta Tags -->
<meta name="title" content="Play - Free Open Source HTML Bootstrap Template by UIdeck">
<meta name="description" content="Play - Free Open Source HTML Bootstrap Template by UIdeck Team">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="https://uideck.com/play/">
<meta property="og:title" content="Play - Free Open Source HTML Bootstrap Template by UIdeck">
<meta property="og:description" content="Play - Free Open Source HTML Bootstrap Template by UIdeck Team">
<meta property="og:image" content="https://uideck.com/wp-content/uploads/2021/09/play-meta-bs.jpg">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="https://uideck.com/play/">
<meta property="twitter:title" content="Play - Free Open Source HTML Bootstrap Template by UIdeck">
<meta property="twitter:description" content="Play - Free Open Source HTML Bootstrap Template by UIdeck Team">
<meta property="twitter:image" content="https://uideck.com/wp-content/uploads/2021/09/play-meta-bs.jpg">

    <!--====== Favicon Icon ======-->
    <link
      rel="shortcut icon"
      href="assets/images/favicon.svg"
      type="image/svg"
    />

    <!-- ===== All CSS files ===== -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="assets/css/lineicons.css" />
    <link rel="stylesheet" href="assets/css/ud-styles.css" />
  </head>
  <body>
    <!-- ====== Header Start ====== -->
    <header class="ud-header">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <nav class="navbar navbar-expand-lg">
              <a class="navbar-brand" href="index.html">
                <img src="assets/images/logo/logo.svg" alt="Logo" />
              </a>
              <button class="navbar-toggler">
                <span class="toggler-icon"> </span>
                <span class="toggler-icon"> </span>
                <span class="toggler-icon"> </span>
              </button>

              <div class="navbar-collapse">
                <ul id="nav" class="navbar-nav mx-auto">
                  <li class="nav-item">
                    <a class="ud-menu-scroll" href="#home">Home</a>
                  </li>

                  <li class="nav-item">
                    <a class="ud-menu-scroll" href="#about">About</a>
                  </li>
                  <li class="nav-item">
                    <a class="ud-menu-scroll" href="#pricing">Pricing</a>
                  </li>
                  <li class="nav-item">
                    <a class="ud-menu-scroll" href="#contact">Contact</a>
                  </li>
                </ul>
              </div>
            </nav>
          </div>
        </div>
      </div>
    </header>
    <!-- ====== Header End ====== -->

    <!-- ====== Hero Start ====== -->
    <section class="ud-hero" id="home">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="ud-hero-content wow fadeInUp" data-wow-delay=".2s">
              <h1 class="ud-hero-title">
                PLAY
              </h1>
              <p class="ud-hero-desc">
		PLAY is an extention of Panda.HTB, bringing network monitoring solutions to your doorstep.
		<br><br><br><br>
              </p>
            </div>
            <div
              class="ud-hero-brands-wrapper wow fadeInUp"
              data-wow-delay=".3s"
            >
              <img src="assets/images/hero/brand.svg" alt="brand" />
            </div>
            <div class="ud-hero-image wow fadeInUp" data-wow-delay=".25s">
              <img
                src="assets/images/hero/dotted-shape.svg"
                alt="shape"
                class="shape shape-1"
              />
              <img
                src="assets/images/hero/dotted-shape.svg"
                alt="shape"
                class="shape shape-2"
              />
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- ====== Hero End ====== -->

    <!-- ====== Features Start ====== -->
    <section id="features" class="ud-features">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="ud-section-title">
              <span>Features</span>
              <h2>Main Features of Play</h2>
              <p>
		Working together with Panda.HTB we provide delivery, installation and usage on network monitoring applic.... Truncated ....
```

References: 
- https://github.com/ekultek/whatwaf

**CURL Command**
```
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 10.10.11.136' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2866.71 Safari/537.36' 'http://10.10.11.136/'
```
---
Generated by [Nuclei v2.9.3](https://github.com/projectdiscovery/nuclei)