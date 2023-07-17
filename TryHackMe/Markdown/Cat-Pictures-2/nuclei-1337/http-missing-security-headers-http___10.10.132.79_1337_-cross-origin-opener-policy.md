### HTTP Missing Security Headers (http-missing-security-headers:cross-origin-opener-policy) found on http://10.10.132.79:1337/

----
**Details**: **http-missing-security-headers:cross-origin-opener-policy** matched at http://10.10.132.79:1337/

**Protocol**: HTTP

**Full URL**: http://10.10.132.79:1337/

**Timestamp**: Sat Jul 1 18:49:49 +0100 BST 2023

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
Host: 10.10.132.79:1337
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
Content-Length: 3858
Accept-Ranges: bytes
Content-Type: text/html; charset=utf-8
Date: Sat, 01 Jul 2023 17:49:04 GMT
Last-Modified: Wed, 19 Oct 2022 15:30:49 GMT

<!DOCTYPE html>

<html>
	<head>

		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>OliveTin</title>
		<link rel = "stylesheet" type = "text/css" href = "style.css" />
		<link rel = "shortcut icon" type = "image/png" href = "OliveTinLogo.png" />

		<link rel = "apple-touch-icon" sizes="57x57" href="OliveTinLogo-57px.png" />
		<link rel = "apple-touch-icon" sizes="120x120" href="OliveTinLogo-120px.png" />
		<link rel = "apple-touch-icon" sizes="180x180" href="OliveTinLogo-180px.png" />
	</head>

	<body>
		<main title = "main content">
			<fieldset id = "section-switcher" title = "Sections">
				<button id = "showActions">Actions</button>
				<button id = "showLogs">Logs</button>
			</fieldset>

			<section id = "contentLogs" title = "Logs" hidden>
				<table title = "Logs">
					<thead>
						<tr>
							<th>Timestamp</th>
							<th>Log</th>
							<th>Exit Code</th>
						</tr>
					</thead>
					<tbody id = "logTableBody" />
				</table>
			</section>

			<section id = "contentActions" title = "Actions" hidden >
				<fieldset id = "root-group" title = "Dashboard of buttons">
				</fieldset>
			</section>

			<noscript>
				<div class = "error">Sorry, JavaScript is required to use OliveTin.</div>
			</noscript>
		</main>

		<footer title = "footer">
			<p><img title = "application icon" src = "OliveTinLogo.png" height = "1em" class = "logo" /> OliveTin</p>
			<p>	
				<a href = "https://docs.olivetin.app" target = "_new">Documentation</a> | 
				<a href = "https://github.com/OliveTin/OliveTin/issues/new/choose" target = "_new">Raise an issue on GitHub</a> | 
				<span id = "currentVersion">Version: ?</p>  
				<a id = "available-version" href = "http://olivetin.app" target = "_blank" hidden>?</a>
			</p>
		</footer>

		<template id = "tplArgumentForm">
			<form class = "action-arguments">
				<div class = "wrapper">
					<div>
						<span class = "icon" role = "icon"></span>
						<h2>Argument form</h2>
					</div>

					<div class = "arguments"></div>

					<div class = "buttons">
						<input name = "start" type = "submit" value = "Start">
						<button name = "cancel">Cancel</button>
					</div>
				</div>
			<form>
		</template>

		<template id = "tplActionButton">
			<button>
				<span role = "icon" title = "button icon" class = "icon">&#x1f4a9;</span>
				<p role = "title" class = "title">Untitled Button</p>
			</button>
		</template>

		<template id = "tplLogRow">
			<tr class = "log-row">
				<td class = "timestamp">?</td> 
				<td>
					<span class = "icon" role = "icon"></span>
					<span class = "content">?</span>
				
					<details>
						<summary>stdout</summary>
						<pre class = "stdout">
							?
						</pre>
					</details>

					<details>
						<summary>stderr</summary>
						<pre class = "stderr">
							?
						</pre>
					</details>

				</td>
				<td class = "exit-code">?</td>
			</tr>
		</template>

		<script type = "text/javascript">
			/** 
			This is the bootstrap code, which relies on very simple, old javascript
		  	to at least display a helpful error message if we can't use OliveTin.
			*/
			function showBigError (type, friendlyType, message) {
			  clearInterval(window.buttonInterval)

			  console.error('Error ' + type + ': ', message)

			  const domErr = document.createElement('div')
			  domErr.classList.add('error')
			  domErr.innerHTML = '<h1>Error ' + friendlyType + '</h1><p>' + message + "</p><p><a href = 'http://docs.olivetin.app/troubleshooting.html' target = 'blank'/>OliveTin Documentation</a></p>"

			  document.getElementById('root-group').appendChild(domErr)
			}
		</script>

		<script type = "text/javascript" nomodule>
			showBigError("js-modules-not-supported", "Sorry, your browser does not support JavaScript modules.", null)
		</script>

		<script type = "module" src = "main.js"></script>
	</body>
</html>

```


**CURL command**
```sh
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2117.157 Safari/537.36' 'http://10.10.132.79:1337/'
```

----

Generated by [Nuclei v2.9.7](https://github.com/projectdiscovery/nuclei)