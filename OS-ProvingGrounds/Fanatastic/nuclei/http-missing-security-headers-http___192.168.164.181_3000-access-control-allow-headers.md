### HTTP Missing Security Headers (http-missing-security-headers:access-control-allow-headers) found on http://192.168.164.181:3000
---
**Details**: **http-missing-security-headers:access-control-allow-headers**  matched at http://192.168.164.181:3000

**Protocol**: HTTP

**Full URL**: http://192.168.164.181:3000

**Timestamp**: Thu Jun 2 10:12:40 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass |
| Tags | misconfig, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.
 |

**Request**
```http
GET / HTTP/1.1
Host: 192.168.164.181:3000
User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36
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
Cache-Control: no-cache
Content-Type: text/html; charset=UTF-8
Date: Thu, 02 Jun 2022 09:12:40 GMT
Expires: -1
Pragma: no-cache
X-Content-Type-Options: nosniff
X-Frame-Options: deny
X-Xss-Protection: 1; mode=block

<!doctype html><html lang="en"><head><meta charset="utf-8"/><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/><meta name="viewport" content="width=device-width"/><meta name="theme-color" content="#000"/><title>Grafana</title><base href="/"/><link rel="preload" href="public/fonts/roboto/RxZJdnzeo3R5zSexge8UUVtXRa8TVwTICgirnJhmVJw.woff2" as="font" crossorigin/><link rel="icon" type="image/png" href="public/img/fav32.png"/><link rel="apple-touch-icon" sizes="180x180" href="public/img/apple-touch-icon.png"/><link rel="mask-icon" href="public/img/grafana_mask_icon.svg" color="#F05A28"/><link rel="stylesheet" href="public/build/grafana.dark.fab5d6bbd438adca1160.css"/><script nonce="">performance.mark('frontend_boot_css_time_seconds');</script><meta name="apple-mobile-web-app-capable" content="yes"/><meta name="apple-mobile-web-app-status-bar-style" content="black"/><meta name="msapplication-TileColor" content="#2b5797"/><meta name="msapplication-config" content="public/img/browserconfig.xml"/></head><body class="theme-dark app-grafana"><style>.preloader {
        height: 100%;
        flex-direction: column;
        display: flex;
        justify-content: center;
        align-items: center;
      }

      .preloader__enter {
        opacity: 0;
        animation-name: preloader-fade-in;
        animation-iteration-count: 1;
        animation-duration: 0.9s;
        animation-delay: 1.35s;
        animation-fill-mode: forwards;
      }

      .preloader__bounce {
        text-align: center;
        animation-name: preloader-bounce;
        animation-duration: 0.9s;
        animation-iteration-count: infinite;
      }

      .preloader__logo {
        display: inline-block;
        animation-name: preloader-squash;
        animation-duration: 0.9s;
        animation-iteration-count: infinite;
        width: 60px;
        height: 60px;
        background-repeat: no-repeat;
        background-size: contain;
        background-image: url('public/img/grafana_icon.svg');
      }

      .preloader__text {
        margin-top: 16px;
        font-weight: 500;
        font-size: 14px;
        font-family: Sans-serif;
        opacity: 0;
        animation-name: preloader-fade-in;
        animation-duration: 0.9s;
        animation-delay: 1.8s;
        animation-fill-mode: forwards;
      }

      .theme-light .preloader__text {
        color: #52545c;
      }

      .theme-dark .preloader__text {
        color: #d8d9da;
      }

      @keyframes preloader-fade-in {
        0% {
          opacity: 0;
           
          animation-timing-function: cubic-bezier(0, 0, 0.5, 1);
        }
        100% {
          opacity: 1;
        }
      }

      @keyframes preloader-bounce {
        from,
        to {
          transform: translateY(0px);
          animation-timing-function: cubic-bezier(0.3, 0, 0.1, 1);
        }
        50% {
          transform: translateY(-50px);
          animation-timing-function: cubic-bezier(0.9, 0, 0.7, 1);
        }
      }

      @keyframes preloader-squash {
        0% {
          transform: scaleX(1.3) scaleY(0.8);
          animation-timing-function: cubic-bezier(0.3, 0, 0.1, 1);
          transform-origin: bottom center;
        }
        15% {
          transform: scaleX(0.75) scaleY(1.25);
          animation-timing-function: cubic-bezier(0, 0, 0.7, 0.75);
          transform-origin: bottom center;
        }
        55% {
          transform: scaleX(1.05) scaleY(0.95);
          animation-timing-function: cubic-bezier(0.9, 0, 1, 1);
          transform-origin: top center;
        }
        95% {
          transform: scaleX(0.75) scaleY(1.25);
          animation-timing-function: cubic-bezier(0, 0, 0, 1);
          transform-origin: bottom center;
        }
        100% {
          transform: scaleX(1.3) scaleY(0.8);
          transform-origin: bottom center;
          animation-timing-function: cubic-bezier(0, 0, 0.7, 1);
        }
      }

       
      .preloader__text--fail {
        display: none;
      }

       
      .preloader--done .preloader__bounce,
      .preloader--done .preloader__logo {
        animation-name: none;
        display: none;
      }

      .preloader--done .preloader__logo,
      .preloader--done .preloader__text {
        display: none;
        color: #ff5705 !important;
        font-size: 15px;
      }

      .preloader--done .preloader__text--fail {
        display: block;
      }

      [ng\:cloak],
      [ng-cloak],
      .ng-cloak {
        display: none !important;
      }</style><div class="preloader"><div class="preloader__enter"><div class="preloader__bounce"><div class="preloader__logo"></div></div></div><div class="preloader__text">Loading Grafana</div><div class="preloader__text preloader__text--fail"><p><strong>If you're seeing this Grafana has failed to load its applicat.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'Referer: http://192.168.164.181:3000' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36' 'http://192.168.164.181:3000/login'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)