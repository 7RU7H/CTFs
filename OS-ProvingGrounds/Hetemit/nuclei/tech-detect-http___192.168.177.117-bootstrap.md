### Wappalyzer Technology Detection (tech-detect:bootstrap) found on http://192.168.177.117

----
**Details**: **tech-detect:bootstrap** matched at http://192.168.177.117

**Protocol**: HTTP

**Full URL**: http://192.168.177.117

**Timestamp**: Mon Oct 9 16:39:09 +0100 BST 2023

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
Host: 192.168.177.117
User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 403 Forbidden
Connection: close
Content-Length: 4288
Accept-Ranges: bytes
Content-Language: en-us
Content-Location: index.html.en-US
Content-Type: text/html; charset=UTF-8
Date: Mon, 09 Oct 2023 15:38:51 GMT
Etag: "10c0-58edc922e2400;5b403c649d14f"
Last-Modified: Tue, 30 Jul 2019 02:14:40 GMT
Server: Apache/2.4.37 (centos)
Tcn: choice
Vary: negotiate,accept-language

<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE HTML>
<html lang="en">
  <head>
    <title>Apache HTTP Server Test Page powered by CentOS</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <link rel="shortcut icon" href="http://www.centos.org/favicon.ico"/>
    <link rel="stylesheet" media="all" href="noindex/common/css/bootstrap.min.css"/>
    <link rel="stylesheet" media="all" href="noindex/common/css/styles.css"/>
  </head>
  <body>
    <header class="container">
      <section class="row">
        <div class="header-graphic v3-banner platform-banner centos-banner" role="banner">
          <div class="graphic-inner">
            <div class="graphic-inner2">
              <div class="banner-title"><span>Apache HTTP Server</span></div>
            </div>
          </div>
        </div>
      </section>
    </header>
    <main class="container">
      <h1>Test Page</h1>
      <p class="lead">This page is used to test the proper operation of the <a href="http://apache.org">Apache HTTP server</a> after it has been installed. If you can read this page it means that this site is working properly. This server is powered by <a href="http://centos.org">CentOS</a>.</p>
      <hr/>
      <section class="row">
        <div class="col-md-6">
          <h2>Just visiting?</h2>
			  	<p class="lead">The website you just visited is either experiencing problems or is undergoing routine maintenance.</p>
          <p>If you would like to let the administrators of this website know that you've seen this page instead of the page you expected, you should send them e-mail. In general, mail sent to the name "webmaster" and directed to the website's domain should reach the appropriate person.</p>
          <p>For example, if you experienced problems while visiting www.example.com, you should send e-mail to "webmaster@example.com".</p>
        <h2>Important note:</h2>
        <p class="lead">The CentOS Project has nothing to do with this website or its content, it just provides the software that makes the website run.</p>
        <p>If you have issues with the content of this site, contact the owner of the domain, not the CentOS project. Unless you intended to visit CentOS.org, the CentOS Project does not have anything to do with this website, the content or the lack of it.</p>
        <p>For example, if this website is www.example.com, you would find the owner of the example.com domain at the following WHOIS server: <a href="http://www.internic.net/whois.html">http://www.internic.net/whois.html</a></p>
        </div>
        <div class="col-md-6">
          <h2>Are you the Administrator?</h2>
          <p>You should add your website content to the directory <code>/var/www/html/</code>.</p>
          <p>To prevent this page from ever being used, follow the instructions in the file <code>/etc/httpd/conf.d/welcome.conf</code>.</p>
          <h2>Promoting Apache and CentOS</h2>
          <p>You are free to use the images below on Apache and CentOS Linux powered HTTP servers. Thanks for using Apache and CentOS!</p>
          <p>
            <a href="http://httpd.apache.org/">
              <img src="noindex/common/images/pb-apache.png" alt="[ Powered by Apache ]"/>
            </a>
            <a href="http://www.centos.org/">
              <img src="noindex/common/images/pb-centos.png" alt="[ Powered by CentOS Linux ]"/>
            </a>
          </p>
        </div>
        <div class="col-md-6">
        </div>
        <div class="col-md-6">
          <h2>The CentOS Project</h2>
          <p>The CentOS Linux distribution is a stable, predictable, manageable and reproduceable platform derived from the sources of Red Hat Enterprise Linux (RHEL).</p>
          <p>Additionally to being a popular choice for web hosting, CentOS also provides a rich platform for open source communities to build upon. For more information please visit the <a href="http://www.centos.org/">CentOS website</a>.</p>
        </div>
      </section>
      <hr/>
    </main>
    <footer class="container">
      <p>© 2019 The CentOS Project | <a href="https://www.centos.org/legal/">Legal</a> | <a href="https://www.centos.org/legal/privacy/">Privacy</a></p>
    </footer>
  </body>
</html>

```


**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36' 'http://192.168.177.117'
```

----

Generated by [Nuclei v2.9.11](https://github.com/projectdiscovery/nuclei)