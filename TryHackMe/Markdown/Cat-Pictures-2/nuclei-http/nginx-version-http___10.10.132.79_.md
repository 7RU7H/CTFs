### Nginx version detect (nginx-version) found on http://10.10.132.79/

----
**Details**: **nginx-version** matched at http://10.10.132.79/

**Protocol**: HTTP

**Full URL**: http://10.10.132.79/

**Timestamp**: Sat Jul 1 18:23:22 +0100 BST 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | Nginx version detect |
| Authors | philippedelteil, daffainfo |
| Tags | tech, nginx |
| Severity | info |
| Description | Some nginx servers have the version on the response header. Useful when you need to find specific CVEs on your targets. |

**Request**
```http
GET / HTTP/1.1
Host: 10.10.132.79
User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686 on x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2820.59 Safari/537.36
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
Date: Sat, 01 Jul 2023 17:22:37 GMT
Last-Modified: Sun, 05 Jun 2016 14:51:28 GMT
Server: nginx/1.4.6 (Ubuntu)

<!DOCTYPE HTML>
<html>
	<head>

		<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
		<title>Lychee</title>

		<meta name="author" content="Tobias Reich">

		<link type="text/css" rel="stylesheet" href="dist/main.css">

		<link rel="shortcut icon" href="favicon.ico">
		<link rel="apple-touch-icon" href="src/images/apple-touch-icon-ipad.png" sizes="120x120">
		<link rel="apple-touch-icon" href="src/images/apple-touch-icon-iphone.png" sizes="152x152">
		<link rel="apple-touch-icon" href="src/images/apple-touch-icon-iphone-plus.png" sizes="180x180">

		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, maximum-scale=1.0">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="apple-mobile-web-app-capable" content="yes">

	</head>
	<body>

	<!-- inject:svg -->
	<svg class="svgsprite" version="1.1" xmlns="http://www.w3.org/2000/svg"><defs><symbol id="account-login" viewBox="0 0 8 8"><path d="M3 0v1h4v5h-4v1h5v-7h-5zm1 2v1h-4v1h4v1l2-1.5-2-1.5z"/></symbol><symbol id="account-logout" viewBox="0 0 8 8"><path d="M3 0v1h4v5h-4v1h5v-7h-5zm-1 2l-2 1.5 2 1.5v-1h4v-1h-4v-1z"/></symbol><symbol id="action-redo" viewBox="0 0 8 8"><path d="M3.5 0c-1.93 0-3.5 1.57-3.5 3.5 0-1.38 1.12-2.5 2.5-2.5s2.5 1.12 2.5 2.5v.5h-1l2 2 2-2h-1v-.5c0-1.93-1.57-3.5-3.5-3.5z" transform="translate(0 1)"/></symbol><symbol id="action-undo" viewBox="0 0 8 8"><path d="M4.5 0c-1.93 0-3.5 1.57-3.5 3.5v.5h-1l2 2 2-2h-1v-.5c0-1.38 1.12-2.5 2.5-2.5s2.5 1.12 2.5 2.5c0-1.93-1.57-3.5-3.5-3.5z" transform="translate(0 1)"/></symbol><symbol id="align-center" viewBox="0 0 8 8"><path d="M0 0v1h8v-1h-8zm1 2v1h6v-1h-6zm-1 2v1h8v-1h-8zm1 2v1h6v-1h-6z"/></symbol><symbol id="align-left" viewBox="0 0 8 8"><path d="M0 0v1h8v-1h-8zm0 2v1h6v-1h-6zm0 2v1h8v-1h-8zm0 2v1h6v-1h-6z"/></symbol><symbol id="align-right" viewBox="0 0 8 8"><path d="M0 0v1h8v-1h-8zm2 2v1h6v-1h-6zm-2 2v1h8v-1h-8zm2 2v1h6v-1h-6z"/></symbol><symbol id="aperture" viewBox="0 0 8 8"><path d="M4 0c-.69 0-1.336.19-1.906.5l3.219 2.344.719-2.25c-.59-.36-1.281-.594-2.031-.594zm-2.75 1.125c-.76.73-1.25 1.735-1.25 2.875 0 .25.022.489.063.719l3.094-2.219-1.906-1.375zm5.625.125l-1.219 3.75h2.219c.08-.32.125-.65.125-1 0-1.07-.435-2.03-1.125-2.75zm-4.719 3.188l-1.75 1.281c.55 1.13 1.595 1.989 2.875 2.219l-1.125-3.5zm1.563 1.563l.625 1.969c1.33-.11 2.454-.879 3.094-1.969h-3.719z"/></symbol><symbol id="arrow-bottom" viewBox="0 0 8 8"><path d="M2 0v5h-2l2.531 3 2.469-3h-2v-5h-1z" transform="translate(1)"/></symbol><symbol id="arrow-circle-bottom" viewBox="0 0 8 8"><path d="M4 0c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm-1 1h2v3h2l-3 3-3-3h2v-3z"/></symbol><symbol id="arrow-circle-left" viewBox="0 0 8 8"><path d="M4 0c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 1v2h3v2h-3v2l-3-3 3-3z"/></symbol><symbol id="arrow-circle-right" viewBox="0 0 8 8"><path d="M4 0c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 1l3 3-3 3v-2h-3v-2h3v-2z"/></symbol><symbol id="arrow-circle-top" viewBox="0 0 8 8"><path d="M4 0c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 1l3 3h-2v3h-2v-3h-2l3-3z"/></symbol><symbol id="arrow-left" viewBox="0 0 8 8"><path d="M3 0l-3 2.531 3 2.469v-2h5v-1h-5v-2z" transform="translate(0 1)"/></symbol><symbol id="arrow-right" viewBox="0 0 8 8"><path d="M5 0v2h-5v1h5v2l3-2.531-3-2.469z" transform="translate(0 1)"/></symbol><symbol id="arrow-thick-bottom" viewBox="0 0 8 8"><path d="M2 0v5h-2l3.031 3 2.969-3h-2v-5h-2z" transform="translate(1)"/></symbol><symbol id="arrow-thick-left" viewBox="0 0 8 8"><path d="M3 0l-3 3.031 3 2.969v-2h5v-2h-5v-2z" transform="translate(0 1)"/></symbol><symbol id="arrow-thick-right" viewBox="0 0 8 8"><path d="M5 0v2h-5v2h5v2l3-3.031-3-2.969z" transform="translate(0 1)"/></symbol><symbol id="arrow-thick-top" viewBox="0 0 8 8"><path d="M2.969 0l-2.969 3h2v5h2v-5h2l-3.031-3z" transform="translate(1)"/></symbol><symbol id="arrow-top" viewBox="0 0 8 8"><path d="M2.469 0l-2.469 3h2v5h1v-5h2l-2.531-3z" transform="translate(1)"/></symbol><symbol id="audio-spectrum" viewBox="0 0 8 8"><path d="M4 0v8h1v-8h-1zm-2 1v6h1v-6h-1zm4 1v4h1v-4h-1zm-6 1v2h1v-2h-1z"/></symbol><symbol id="audio" viewBox="0 0 8 8"><path d="M1.188 0c-.734.722-1.188 1.748-1.188 2.844 0 1.095.454 2.09 1.188 2.813l.688-.719c-.546-.538-.875-1.269-.875-2.094s.329-1.587.875-2.125l-.688-.719zm5.625 0l-.688.719c.552.552.875 1.289.875 2.125 0 .836-.327 1.554-.875 2.094l.688.719c.732-.72 1.188-1.708 1.188-2.813 0-1.104-.459-2.115-1.188-2.844zm-4.219 1.406c-.362.362-.594.889-.594 1.438 0 .548.232 1.045.594 1.406l.688-.719c-.178-.178-.281-.416-.281-.688 0-.272.103-.54.281-.719l-.688-.719zm2.813 0l-.688.719c.183.183.281.434.281.719s-.099.505-.281.688l.688.719c.357-.357.594-.851.594-1.406 0-.555-.236-1.08-.594-1.438z" transform="translate(0 1)"/></symbol><symbol id="badge" viewBox="0 0 8 8"><path d="M2 0c-1.105 0-2 .895-2 2s.895 2 2 2 2-.89.... Truncated ....
```

**Extra Information**

**Extracted results:**

- nginx/1.4.6



**CURL command**
```sh
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686 on x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2820.59 Safari/537.36' 'http://10.10.132.79/'
```

----

Generated by [Nuclei v2.9.7](https://github.com/projectdiscovery/nuclei)