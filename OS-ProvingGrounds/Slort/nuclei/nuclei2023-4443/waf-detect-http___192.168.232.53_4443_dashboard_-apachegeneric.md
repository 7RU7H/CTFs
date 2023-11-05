### WAF Detection (waf-detect:apachegeneric) found on http://192.168.232.53:4443/dashboard/

----
**Details**: **waf-detect:apachegeneric** matched at http://192.168.232.53:4443/dashboard/

**Protocol**: HTTP

**Full URL**: http://192.168.232.53:4443/dashboard/

**Timestamp**: Fri Nov 3 21:02:59 +0000 GMT 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | WAF Detection |
| Authors | dwisiswant0, lu4nx |
| Tags | waf, tech, misc |
| Severity | info |
| Description | A web application firewall was detected. |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 0.00 |

**Request**
```http
POST /dashboard/ HTTP/1.1
Host: 192.168.232.53:4443
User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F
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
Content-Length: 7576
Accept-Ranges: bytes
Content-Type: text/html
Date: Fri, 03 Nov 2023 21:02:45 GMT
Etag: "1d98-5a5e6a6bcb780"
Last-Modified: Mon, 18 May 2020 06:55:42 GMT
Server: Apache/2.4.43 (Win64) OpenSSL/1.1.1g PHP/7.4.6

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">

    <!-- Always force latest IE rendering engine or request Chrome Frame -->
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Use title if it's in the page YAML frontmatter -->
    <title>Welcome to XAMPP</title>

    <meta name="description" content="XAMPP is an easy to install Apache distribution containing MariaDB, PHP and Perl." />
    <meta name="keywords" content="xampp, apache, php, perl, mariadb, open source distribution" />

    <link href="/dashboard/stylesheets/normalize.css" rel="stylesheet" type="text/css" /><link href="/dashboard/stylesheets/all.css" rel="stylesheet" type="text/css" />
    <link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/3.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />

    <script src="/dashboard/javascripts/modernizr.js" type="text/javascript"></script>


    <link href="/dashboard/images/favicon.png" rel="icon" type="image/png" />


  </head>

  <body class="index">
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=277385395761685";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
    <div class="contain-to-grid">
      <nav class="top-bar" data-topbar>
        <ul class="title-area">
          <li class="name">
            <h1><a href="/dashboard/index.html">Apache Friends</a></h1>
          </li>
          <li class="toggle-topbar menu-icon">
            <a href="#">
              <span>Menu</span>
            </a>
          </li>
        </ul>

        <section class="top-bar-section">
          <!-- Right Nav Section -->
          <ul class="right">
              <li class=""><a href="/applications.html">Applications</a></li>
              <li class=""><a href="/dashboard/faq.html">FAQs</a></li>
              <li class=""><a href="/dashboard/howto.html">HOW-TO Guides</a></li>
              <li class=""><a target="_blank" href="/dashboard/phpinfo.php">PHPInfo</a></li>
              <li class=""><a href="/phpmyadmin/">phpMyAdmin</a></li>
          </ul>
        </section>
      </nav>
    </div>

    <div id="wrapper">
      <div class="hero">
  <div class="row">
    <div class="large-12 columns">
      <h1><img src="/dashboard/images/xampp-logo.svg" />XAMPP <span>Apache + MariaDB + PHP + Perl</span></h1>
    </div>
  </div>
</div>
<div class="row">
  <div class="large-12 columns">
    <h2>Welcome to XAMPP for Windows 7.4.6</h2>
  </div>
</div>
<div class="row">
  <div class="large-12 columns">
    <p>
      You have successfully installed XAMPP on this system! Now you can start using Apache, MariaDB, PHP and other components.
      You can find more info in the <a href="/dashboard/faq.html">FAQs</a> section or check the <a href="/dashboard/howto.html">HOW-TO Guides</a> for getting started with PHP applications.
    </p>
    <p>
      XAMPP is meant only for development purposes. It has certain configuration settings that make it easy to develop locally but that are insecure if you want to have your installation accessible to others.
      If you want have your XAMPP accessible from the internet, make sure you understand the implications and you checked the <a href="/dashboard/faq.html">FAQs</a> to learn how to protect your site. Alternatively you can use <a href="https://bitnami.com/stack/wamp">WAMP</a>, <a href="https://bitnami.com/stack/mamp">MAMP</a> or <a href="https://bitnami.com/stack/lamp">LAMP</a> which are similar packages which are more suitable for production.
    </p>
    <p>
      Start the XAMPP Control Panel to check the server status.
    </p>
  </div>
</div>
<div class="row">
  <div class="large-12 columns">
    <h3>Community</h3>
  </div>
</div>
<div class="row">
  <div class="large-12 columns">
    <p>
      XAMPP has been around for more than 10 years &ndash; there is a huge community behind it. You can get involved by joining our <a href="https://community.apachefriends.org">Forums</a>, adding yourself to the <a href="https://www.apachefriends.org/community.html#mailing_list">Mailing List</a>, and liking us on <a href="https://www.facebook.com/we.are.xampp">Facebook</a>, following our exploits on <a href="https://twitter.com/apachefriends">Twitter</a>, or adding us to your <a href="https://plus.google.com/+xampp/posts">Google+</a> circles.
    </p>
  </div>
</div>
<div class="row">
  <div class="large-12 columns">
    <h3>Contribute to XAMPP translation at <a href="https://translate.apachefriends.org/">translate.apachefriends.org</a>.</h3>
  <.... Truncated ....
```

References: 
- https://github.com/ekultek/whatwaf

**CURL command**
```sh
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 192.168.232.53:4443' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F' 'http://192.168.232.53:4443/dashboard/'
```

----

Generated by [Nuclei v2.9.11](https://github.com/projectdiscovery/nuclei)