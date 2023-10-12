### Host Header Injection (host-header-injection) found on http://192.168.205.61:8081/

----
**Details**: **host-header-injection** matched at http://192.168.205.61:8081/

**Protocol**: HTTP

**Full URL**: http://192.168.205.61:8081/

**Timestamp**: Thu Oct 12 11:38:33 +0100 BST 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | Host Header Injection |
| Authors | princechaddha |
| Tags | hostheader-injection, generic |
| Severity | info |
| Description | HTTP header injection is a general class of web application security vulnerability which occurs when Hypertext Transfer Protocol headers are dynamically generated based on user input. |

**Request**
```http
GET / HTTP/1.1
Host: 2Wexqsc0IdaihAScsPz9ZfEUxXF.tld
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 10712
Cache-Control: no-cache, no-store, max-age=0, must-revalidate, post-check=0, pre-check=0
Content-Type: text/html
Date: Thu, 12 Oct 2023 10:38:12 GMT
Expires: 0
Last-Modified: Thu, 12 Oct 2023 10:38:12 GMT
Pragma: no-cache
Server: Nexus/3.21.0-05 (OSS)
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-Xss-Protection: 1; mode=block


<!DOCTYPE html>
<html lang="en">
<head>
  <title>Nexus Repository Manager</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="description" content="Nexus Repository Manager"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>


  <!--[if lt IE 9]>
    <script>(new Image).src="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/favicon.ico?_v=3.21.0-05&_e=OSS"</script>
  <![endif]-->
  <link rel="icon" type="image/png" href="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/favicon-32x32.png?_v=3.21.0-05&_e=OSS" sizes="32x32">
  <link rel="mask-icon" href="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/safari-pinned-tab.svg?_v=3.21.0-05&_e=OSS" color="#5bbad5">
  <link rel="icon" type="image/png" href="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/favicon-16x16.png?_v=3.21.0-05&_e=OSS" sizes="16x16">
  <link rel="shortcut icon" href="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/favicon.ico?_v=3.21.0-05&_e=OSS">
  <meta name="msapplication-TileImage" content="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/mstile-144x144.png?_v=3.21.0-05&_e=OSS">
  <meta name="msapplication-TileColor" content="#00a300">

  
  
            <link rel="stylesheet" type="text/css" href="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/loading-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/baseapp-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/nexus-rapture-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/nexus-proximanova-plugin-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/nexus-coreui-plugin-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/nexus-proui-plugin-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/nexus-repository-helm-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/nexus-repository-p2-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/nexus-repository-r-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/nexus-onboarding-plugin-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/nexus-security-bundle.css?_v=3.21.0-05&_e=OSS">
    
    <script type="text/javascript">
    function progressMessage(msg) {
      if (console && console.log) {
        console.log(msg);
      }
      document.getElementById('loading-msg').innerHTML=msg;
    }
  </script>
  </head>
<body class="x-border-box">

<div id="loading-mask"></div>
<div id="loading">
  <div id="loading-background">
    <img id="loading-logo" src="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/images/nxrm-logo-white.svg?_v=3.21.0-05&_e=OSS" alt="Product Logo" width='116' height='116' />
    <img id="loading-product" src="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/images/loading-product.png?_v=3.21.0-05&_e=OSS" alt="Nexus Repository Manager"/>
    <div class="loading-indicator">
      <img id="loading-spinner" src="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/resources/images/loading-spinner.gif?_v=3.21.0-05&_e=OSS" alt="Loading Spinner"/>
      <span id="loading-msg">Loading ...</span>
    </div>
  </div>

    <div id="code-load" class="x-hide-display">
    
                  <script type="text/javascript">progressMessage('Loading baseapp-prod.js');</script>
              <script type="text/javascript" src="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/baseapp-prod.js?_v=3.21.0-05&_e=OSS"></script>
                  <script type="text/javascript">progressMessage('Loading extdirect-prod.js');</script>
              <script type="text/javascript" src="http://2Wexqsc0IdaihAScsPz9ZfEUxXF.tld/static/rapture/extdirect-prod.js?_v=3.21.0-05&_e=OSS"></script>
                  <script type="t.... Truncated ....
```

References: 
- https://portswigger.net/web-security/host-header
- https://portswigger.net/web-security/host-header/exploiting
- https://www.acunetix.com/blog/articles/automated-detection-of-host-header-attacks/

**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'Host: 2Wexqsc0IdaihAScsPz9ZfEUxXF.tld' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36' 'http://192.168.205.61:8081/'
```

----

Generated by [Nuclei v2.9.11](https://github.com/projectdiscovery/nuclei)