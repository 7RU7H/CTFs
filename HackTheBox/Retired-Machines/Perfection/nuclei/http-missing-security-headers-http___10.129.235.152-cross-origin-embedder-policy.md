### HTTP Missing Security Headers (http-missing-security-headers:cross-origin-embedder-policy) found on 10.129.235.152

----
**Details**: **http-missing-security-headers:cross-origin-embedder-policy** matched at 10.129.235.152

**Protocol**: HTTP

**Full URL**: http://10.129.235.152

**Timestamp**: Mon Mar 4 14:22:02 +0000 GMT 2024

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
Host: 10.129.235.152
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36 Edg/113.0.1774.35
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
Content-Type: text/html;charset=utf-8
Date: Mon, 04 Mar 2024 14:22:02 GMT
Server: nginx
Server: WEBrick/1.7.0 (Ruby/3.0.2/2021-07-07)
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-Xss-Protection: 1; mode=block

<html lang="en">
<head>
<title>Weighted Grade Calculator</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/css/w3.css">
<link rel="stylesheet" href="/css/lato.css">
<link rel="stylesheet" href="/css/montserrat.css">
<link rel="stylesheet" href="/css/font-awesome.min.css">
<style>
body,h1,h2,h3,h4,h5,h6 {font-family: "Lato", sans-serif}
.w3-bar,h1,button {font-family: "Montserrat", sans-serif}
.fa-anchor,.fa-coffee {font-size:200px}
</style>
</head>
<body>

<!-- Navbar -->
<div class="w3-top">
  <div class="w3-bar w3-blue w3-card w3-left-align w3-large">
    <a class="w3-bar-item w3-button w3-hide-medium w3-hide-large w3-right w3-padding-large w3-hover-white w3-large w3-red" href="javascript:void(0);" onclick="myFunction()" title="Toggle Navigation Menu"><i class="fa fa-bars"></i></a>
    <a href="/" class="w3-bar-item w3-button w3-padding-large w3-white">Home</a>
    <a href="/about" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white">About Us</a>
    <a href="/weighted-grade" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white">Calculate your weighted grade</a>
  </div>

  <!-- Navbar on small screens -->
  <div id="navDemo" class="w3-bar-block w3-white w3-hide w3-hide-large w3-hide-medium w3-large">
    <a href="/about" class="w3-bar-item w3-button w3-padding-large">About Us</a>
    <a href="/weighted-grade" class="w3-bar-item w3-button w3-padding-large">Calculate your weighted grade</a>
  </div>
</div>

<!-- Header -->
<header class="w3-container w3-blue w3-center" style="padding:128px 16px">
  <h1 class="w3-margin w3-jumbo">Weighted Grade Calculator</h1>
  <p class="w3-xlarge">A tool to calculate the total grade in a class based on category scores and percentage weights.</p>
</header>

<!-- First Grid -->
<div class="w3-row-padding w3-padding-64 w3-container">
  <div class="w3-content">
    <div class="w3-twothird">
      <h1>Why we made this</h1>
      <h5 class="w3-padding-32">Here at Secure Student Tools, we know that calculating grades based on complicated weighting can be a bit of a pain.</h5>

      <p class="w3-text-grey">So we sat down and thought: instead of letting students suffer through the headache 
        of calculating weighted grades, why not make a little tool to make life a little bit easier for hard-working students? You're welcome :)
      </p>
    </div>

    <div class="w3-third w3-center">
      <img src="/images/checklist.jpg"></img>
    </div>
  </div>
</div>

<!-- Second Grid -->
<div class="w3-row-padding w3-light-grey w3-padding-64 w3-container">
  <div class="w3-content">
    <div class="w3-third w3-center">
      <img src="/images/lightning.png"></img>
    </div>

    <div class="w3-twothird">
      <h1>How to use</h1>
      <h5 class="w3-padding-32">It's simple!</h5>

      <p class="w3-text-grey">Just enter the names of your desired categories, the weight (or impact) on your grade, and your score for each of them; it'll return your total (or desired) grade in less than a second. It's quick and easy!</p>
    </div>
  </div>
</div>

<div class="w3-container w3-black w3-center w3-opacity w3-padding-64">
    <h1 class="w3-margin w3-xlarge">Made by Secure Student Tools</h1>
</div>

<style>
  .copyright {
    text-align: center;
  }
</style>
<!-- Footer -->
<footer>
<p class="copyright">Copyright © Secure Student Tools. All rights reserved<br><b>Powered by WEBrick 1.7.0</b></p>
</footer>

<script>
// Used to toggle the menu on small screens when clicking on the menu button
function myFunction() {
  var x = document.getElementById("navDemo");
  if (x.className.indexOf("w3-show") == -1) {
    x.className += " w3-show";
  } else { 
    x.className = x.className.replace(" w3-show", "");
  }
}
</script>

</body>
</html>

```


**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36 Edg/113.0.1774.35' 'http://10.129.235.152'
```

----

Generated by [Nuclei v3.1.10](https://github.com/projectdiscovery/nuclei)