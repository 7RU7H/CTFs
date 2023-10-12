### HTTP Missing Security Headers (http-missing-security-headers:cross-origin-embedder-policy) found on http://192.168.205.61:8081/

----
**Details**: **http-missing-security-headers:cross-origin-embedder-policy** matched at http://192.168.205.61:8081/

**Protocol**: HTTP

**Full URL**: http://192.168.205.61:8081/

**Timestamp**: Thu Oct 12 11:37:28 +0100 BST 2023

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
Host: 192.168.205.61:8081
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2656.18 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 10196
Cache-Control: no-cache, no-store, max-age=0, must-revalidate, post-check=0, pre-check=0
Content-Type: text/html
Date: Thu, 12 Oct 2023 10:37:07 GMT
Expires: 0
Last-Modified: Thu, 12 Oct 2023 10:37:07 GMT
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
    <script>(new Image).src="http://192.168.205.61:8081/static/rapture/resources/favicon.ico?_v=3.21.0-05&_e=OSS"</script>
  <![endif]-->
  <link rel="icon" type="image/png" href="http://192.168.205.61:8081/static/rapture/resources/favicon-32x32.png?_v=3.21.0-05&_e=OSS" sizes="32x32">
  <link rel="mask-icon" href="http://192.168.205.61:8081/static/rapture/resources/safari-pinned-tab.svg?_v=3.21.0-05&_e=OSS" color="#5bbad5">
  <link rel="icon" type="image/png" href="http://192.168.205.61:8081/static/rapture/resources/favicon-16x16.png?_v=3.21.0-05&_e=OSS" sizes="16x16">
  <link rel="shortcut icon" href="http://192.168.205.61:8081/static/rapture/resources/favicon.ico?_v=3.21.0-05&_e=OSS">
  <meta name="msapplication-TileImage" content="http://192.168.205.61:8081/static/rapture/resources/mstile-144x144.png?_v=3.21.0-05&_e=OSS">
  <meta name="msapplication-TileColor" content="#00a300">

  
  
            <link rel="stylesheet" type="text/css" href="http://192.168.205.61:8081/static/rapture/resources/loading-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://192.168.205.61:8081/static/rapture/resources/baseapp-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://192.168.205.61:8081/static/rapture/resources/nexus-rapture-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://192.168.205.61:8081/static/rapture/resources/nexus-proximanova-plugin-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://192.168.205.61:8081/static/rapture/resources/nexus-coreui-plugin-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://192.168.205.61:8081/static/rapture/resources/nexus-proui-plugin-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://192.168.205.61:8081/static/rapture/resources/nexus-repository-helm-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://192.168.205.61:8081/static/rapture/resources/nexus-repository-p2-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://192.168.205.61:8081/static/rapture/resources/nexus-repository-r-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://192.168.205.61:8081/static/rapture/resources/nexus-onboarding-plugin-prod.css?_v=3.21.0-05&_e=OSS">
            <link rel="stylesheet" type="text/css" href="http://192.168.205.61:8081/static/nexus-security-bundle.css?_v=3.21.0-05&_e=OSS">
    
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
    <img id="loading-logo" src="http://192.168.205.61:8081/static/rapture/resources/images/nxrm-logo-white.svg?_v=3.21.0-05&_e=OSS" alt="Product Logo" width='116' height='116' />
    <img id="loading-product" src="http://192.168.205.61:8081/static/rapture/resources/images/loading-product.png?_v=3.21.0-05&_e=OSS" alt="Nexus Repository Manager"/>
    <div class="loading-indicator">
      <img id="loading-spinner" src="http://192.168.205.61:8081/static/rapture/resources/images/loading-spinner.gif?_v=3.21.0-05&_e=OSS" alt="Loading Spinner"/>
      <span id="loading-msg">Loading ...</span>
    </div>
  </div>

    <div id="code-load" class="x-hide-display">
    
                  <script type="text/javascript">progressMessage('Loading baseapp-prod.js');</script>
              <script type="text/javascript" src="http://192.168.205.61:8081/static/rapture/baseapp-prod.js?_v=3.21.0-05&_e=OSS"></script>
                  <script type="text/javascript">progressMessage('Loading extdirect-prod.js');</script>
              <script type="text/javascript" src="http://192.168.205.61:8081/static/rapture/extdirect-prod.js?_v=3.21.0-05&_e=OSS"></script>
                  <script type="text/javascript">progressMessage('Loading bootstrap.js');</script>
              <script type="text/javascript" src="http://192.168.205.61:8081/static/rapture/bootstrap.js?_v=3.21.0-05&_e=OSS"></script>
                  <script type="text/javascript">progressMessa.... Truncated ....
```


**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2656.18 Safari/537.36' 'http://192.168.205.61:8081/'
```

----

Generated by [Nuclei v2.9.11](https://github.com/projectdiscovery/nuclei)