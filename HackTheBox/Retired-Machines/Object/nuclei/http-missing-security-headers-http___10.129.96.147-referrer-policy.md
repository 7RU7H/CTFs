### HTTP Missing Security Headers (http-missing-security-headers:referrer-policy) found on http://10.129.96.147
---
**Details**: **http-missing-security-headers:referrer-policy**  matched at http://10.129.96.147

**Protocol**: HTTP

**Full URL**: http://10.129.96.147

**Timestamp**: Fri Feb 10 18:32:03 +0000 GMT 2023

**Template Information**

| Key | Value |
|---|---|
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass |
| Tags | misconfig, headers, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.
 |

**Request**
```http
GET / HTTP/1.1
Host: 10.129.96.147
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2117.157 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Accept-Ranges: bytes
Content-Type: text/html
Date: Fri, 10 Feb 2023 18:32:02 GMT
Etag: "0fe0b831cad71:0"
Last-Modified: Tue, 26 Oct 2021 06:21:32 GMT
Server: Microsoft-IIS/10.0
Vary: Accept-Encoding



<!DOCTYPE html>
<html lang="en" >

<head>

  <meta charset="UTF-8">
 
  <title>Mega Engines</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">

  
  
<style>
html {
  background: #6c7367;
  /* Old browsers */
  background: -moz-radial-gradient(center, ellipse cover, #6c7367 0%, #3f3b40 77%, #261f26 100%);
  /* FF3.6+ */
  background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, #6c7367), color-stop(77%, #3f3b40), color-stop(100%, #261f26));
  /* Chrome,Safari4+ */
  background: -webkit-radial-gradient(center, ellipse cover, #6c7367 0%, #3f3b40 77%, #261f26 100%);
  /* Chrome10+,Safari5.1+ */
  background: -o-radial-gradient(center, ellipse cover, #6c7367 0%, #3f3b40 77%, #261f26 100%);
  /* Opera 12+ */
  background: -ms-radial-gradient(center, ellipse cover, #6c7367 0%, #3f3b40 77%, #261f26 100%);
  /* IE10+ */
  background: radial-gradient(ellipse at center, #6c7367 0%, #3f3b40 77%, #261f26 100%);
  /* W3C */
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr="#6c7367", endColorstr="#261f26",GradientType=1 );
  width: 100vw;
  height: 100vh;
}

.container {
  width: 100vw;
  height: 300px;
  background: url("https://s3-us-west-2.amazonaws.com/s.cdpn.io/28963/sd-bk4.svg") repeat-x;
}

svg {
  position: relative;
  opacity: 0.8;
  margin: 10px auto;
  display: table;
  width: 400px;
}

.no-svg .container {
  background: url("https://s3-us-west-2.amazonaws.com/s.cdpn.io/28963/steampunk5.png");
  width: 472px;
  height: 374px;
  margin: 0 auto;
  display: table;
}

#firefly, #firefly_1_, #firefly_2_, #glow, #big-glow {
  visibility: hidden;
  opacity: 0;
}

.s0 {
  stop-color: #FFFFFF;
}

.s1 {
  stop-color: #FCEE91;
  stop-opacity: 0;
}

.s2 {
  stop-color: #FEFBE4;
  stop-opacity: 0.8;
}

.s3 {
  stop-color: #FDF5C0;
  stop-opacity: 0.6;
}

.s4 {
  stop-color: #FDF1A6;
  stop-opacity: 0.4;
}

.s5 {
  stop-color: #FCEF97;
  stop-opacity: 0.2;
}

.ff-glow {
  animation: stutter 4s linear infinite;
}

@keyframes faucet {
  25% {
    transform: scaleX(0);
    transform-origin: 50% 50%;
  }
  50% {
    transform: scaleX(1);
    transform-origin: 50% 50%;
  }
  75% {
    transform: scaleX(0);
    transform-origin: 50% 50%;
  }
}
@keyframes stutter {
  20% {
    opacity: 0.3;
  }
  30% {
    opacity: 1;
  }
  40% {
    opacity: 0.4;
  }
  50% {
    opacity: 0.7;
  }
  70% {
    opacity: 0.1;
  }
  80% {
    opacity: 0.5;
  }
}
</style>

  <script>
  window.console = window.console || function(t) {};
</script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>

  
  <script>
  if (document.location.search.match(/type=embed/gi)) {
    window.parent.postMessage("resize", "*");
  }
</script>


</head>

<body translate="no" >
  <center><h1 style="color:white;">Mega Engines</h1><br/><p style="color:white">We are open to receive innovative automation ideas. Login and submit your code at our <a style="color:white" href="http://object.htb:8080">automation</a> server</p><br/><br/><br/><br/><br/></center>
  <div class="container">

<svg version="1.1" x="0" y="0" viewBox="0 0 500 374" enable-background="new 0 0 500 374" xml:space="preserve">
<g id="Layer_1">
    <path fill-rule="evenodd" clip-rule="evenodd" fill="#161216" d="M101.1 300.3v1.4h-4v-1.4h-2.2v-6.3h2.2v-1.4h-2.2v-6.3h2.2v-1.4h4v1.4h2.5v-14.6h-5.2c-3.3 10-11.4 17.3-24 17.3 -12.6 0-25.2-11.1-25.2-24.9 0-13.7 12.6-24.9 25.2-24.9 12.6 0 20.7 7.2 24 17.3h5.3v-12.9h-2.5v1.4h-4v-1.4h-2.2v-6.3h2.2v-1.4h-2.2v-6.3h2.2v-1.4h4v1.4c2.3 0 29.4 0 32.1 0v-1.4h4v1.4h2.3v6.3h-2.3v1.4h2.3v6.3h-2.3v1.4h-4v-1.4h-2.9c0 14.2 0 28.5 0 42.7h2.9v-1.4h4v1.4h2.3v6.3h-2.3v1.4h2.3v6.3h-2.3v1.4h-4v-1.4C125.1 300.3 108.9 300.3 101.1 300.3L101.1 300.3zM130.2 294v-1.4h2.9v1.4H130.2L130.2 294zM130.2 237.2v-1.4h2.9v1.4H130.2L130.2 237.2zM103.6 235.8v1.4h-2.5v-1.4H103.6L103.6 235.8zM103.6 292.6v1.4h-2.5v-1.4H103.6L103.6 292.6zM67.7 267.9c2.1 3.6 6.8 4.9 10.5 2.8 3.7-2.1 4.9-6.8 2.8-10.4 -2.1-3.6-6.8-4.9-10.5-2.8C66.8 259.6 65.6 264.2 67.7 267.9L67.7 267.9zM69.9 266.6c1.4 2.4 4.6 3.3 7 1.9 2.5-1.4 3.3-4.5 1.9-6.9 -1.4-2.4-4.6-3.3-7-1.9C69.3 261.1 68.5 264.2 69.9 266.6L69.9 266.6zM53.8 264.1c0-4.4 1.4-8.4 3.8-11.7 1.6-2.3 4.8-2.8 7.2-1.2 2.3 1.6 2.8 4.7 1.3 7 -1.2 1.7-2 3.7-2 6 0 2.2 0.7 4.3 2 6 1.6 2.3 1 5.4-1.3 7 -2.3 1.6-5.6 0.9-7.2-1.2C55.2 272.5 53.8 268.4 53.8 264.1L53.8 264.1zM84.6 246.5c3.8 2.2 6.7 5.4 8.4 9.1 1.2 2.5 0 5.6-2.5 6.7 -2.5 1.1-5.5 0.1-6.8-2.4 -0.8-1.9-2.3-3.6-4.3-4.7 -2-1.1-4.1-1.5-6.2-1.3 -2.8 0.2-5.2-1.9-5.5-4.6 -0.3-2.8 2-5.2 4.6-5.5C76.5 243.5 80.8 244.3 84.6 246.5L84.6 246.5zM84.6 281.6c-3.8 2.2-8.1 3-12.2 2.6 -2.8-0.3-4.9-2.7-4.6-5.5 0.3-2.7 2.7-4.8 5.5-4.6 2.1 0.2 4.3-0.2 6.2-1.3 2-1.1 3.4-2.8 4.3-4.7 1.2-2.5 4.2-3.5 6.8-2.4 2.6 1.2 3.6 4.3 2.5 6.7C91.3 276.2 88.5 279.4 84.6 281.6L84.6.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2117.157 Safari/537.36' 'http://10.129.96.147'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)