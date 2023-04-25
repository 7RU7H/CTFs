### FingerprintHub Technology Fingerprint (fingerprinthub-web-fingerprints:laravel) found on http://10.129.199.134
---
**Details**: **fingerprinthub-web-fingerprints:laravel**  matched at http://10.129.199.134

**Protocol**: HTTP

**Full URL**: http://10.129.199.134

**Timestamp**: Mon Apr 24 13:03:46 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | FingerprintHub Technology Fingerprint |
| Authors | pdteam, righettod |
| Tags | tech |
| Severity | info |
| Description | FingerprintHub Technology Fingerprint tests run in nuclei. |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 0.00 |

**Request**
```http
GET / HTTP/1.1
Host: 10.129.199.134
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Cache-Control: no-cache, private
Content-Type: text/html; charset=UTF-8
Date: Mon, 24 Apr 2023 12:03:45 GMT
Server: Apache/2.4.29 (Ubuntu)
Set-Cookie: XSRF-TOKEN=eyJpdiI6IldHV1FlSkxpenpDZml3aU9GTkwrYWc9PSIsInZhbHVlIjoiYkR5TENOdExzZC81ZFI5M3MreDVISTA4RUhRdXZ0ZzBOOU9sZk90ajJ6L2pCbG9TanlmaUp2c3NkQVNEdGxtT0k2bEZmNHk3REpwNklnQVVVVWZkQ3I0K2pWTEtuU1BDZXkwWnM1UDlXdm0xTlIwMnZTdGEwOTFMNlZUSUNMSTMiLCJtYWMiOiJjZmQxY2RmMGVlOWY5YzZkMDM2ZmUwNDc4MWMxMWIzN2Y1ZTgyYjgwNTYzOTJkNTkwZjUzNDRiZjY1ZTQwNmU4IiwidGFnIjoiIn0%3D; expires=Mon, 24-Apr-2023 14:03:45 GMT; Max-Age=7200; path=/; samesite=lax
Set-Cookie: laravel_session=eyJpdiI6IjFwUDB0YmlRQWh3M3JqbXFoZEkyVVE9PSIsInZhbHVlIjoieVFzR0sxbC9TajY5ODc0L0FUUGZoM1Q4NWNheHFESGkrWTN5K3ZCcGNoRFpSeVlGRlZnTVFxU1B0K3IweGpPUXFtd0YrREJTNVRGZUl1OE94UzRLUnhOQVNlaWhZMVB5VTQ4cURTd2FHMG4ybGRZTXlpVmZkMmdIazBvWWw5bVciLCJtYWMiOiJkNjFlOGY4MTExMjYzMzkxZTM2ZjIxYWVmYzQxOTk2YTMyMmFmZDQ5YjczNmQ0YmViMjQwYWU4ZDI5MmNiY2JmIiwidGFnIjoiIn0%3D; expires=Mon, 24-Apr-2023 14:03:45 GMT; Max-Age=7200; path=/; httponly; samesite=lax
Vary: Accept-Encoding

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>HTTP Monitoring Tool</title>
    <link href="http://10.129.199.134/css/app.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<div class="container">
        <div class="container" style="padding: 150px">

	<h1 class="text-center">health.htb</h1>
	<h4 class="text-center">Simple health checks for any URL</h4>

	<hr>




	<p>This is a free utility that allows you to remotely check whether an http service is available. It is useful if you want to check whether the server is correctly running or if there are any firewall issues blocking access.</p>

	<div class="card-header">
	    Configure Webhook
	</div>


	
	
	
	<div class="mx-auto" style="width: 700px; padding: 20px 0 70px 0">
	    <form method="post" action="http://10.129.199.134/webhook">
		<input type="hidden" name="_token" value="9FFN3Wkj0EwUbOAWPIG7XlweM7hMpbL9yAKj2EdM">
		<div class="pt-2 form-group">
		    <label for="webhookUrl">Payload URL:</label>
		    <input type="text" class="form-control" name="webhookUrl"
			   placeholder="http://example.com/postreceive"/>
		</div>

		<div class="pt-2 form-group">
		    <label for="monitoredUrl">Monitored URL:</label>
		    <input type="text" class="form-control" name="monitoredUrl" placeholder="http://example.com"/>
		</div>

		<div class="pt-2 form-group">
		    <label for="frequency">Interval:</label>
		    <input type="text" class="form-control" name="frequency" placeholder="*/5 * * * *"/>
		    <small class="form-text text-muted">Please make use of cron syntax, see <a
			    href="https://crontab.guru/">here</a> for reference.</small>
		</div>

		<p class="pt-3">Under what circumstances should the webhook be sent?</p>

		<select class="form-select" name="onlyError">
		    <option value="1" selected>Only when Service is not available</option>
		    <option value="0">Always</option>
		</select>

		<div class="pt-2">
		    <input type="submit" class="btn btn-primary float-end" name="action"
			   value="Create"/>
		    <input type="submit" class="btn btn-success float-end" style="margin-right: 2px" name="action"
			   value="Test"/>
		</div>

	    </form>
	</div>

	<h4>About:</h4>
<p>This is a free utility that allows you to remotely check whether an http service is available. It is useful if you want to check whether the server is correctly running or if there are any firewall issues blocking access.</p>
	<h4>For Developers:</h4>
<p>Once the webhook has been created, the webhook recipient is periodically informed about the status of the monitored application by means of a post request containing various details about the http service.</p>
	<h4>Its simple:</h4>
	<p>No authentication is required. Once you create a monitoring job, a UUID is generated which you can share
	    with
	    others to manage the job easily.</p>

    </div>
</div>
<script src="http://10.129.199.134/js/app.js" type="text/js"></script>


<!-- Footer -->
<footer class="text-center text-lg-start bg-light text-muted">
    <!-- Section: Social media -->
    <section
        class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom"
    >

        <!-- Left -->

        <!-- Right -->
        <div>
            <a href="" class="me-4 text-reset">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="" class="me-4 text-reset">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="" class="me-4 text-reset">
                <i class="fab fa-google"></i>
            </a>
            <a href="" class="me-4 text-reset">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="" class="me-4 text-reset">
                <i class="fab fa-linkedin"></i>
            </a>
            <a href="" class="me-4 text-reset">
                <i class="fab fa-github"><.... Truncated ....
```

References: 
- https://github.com/0x727/fingerprinthub

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36' 'http://10.129.199.134'
```
---
Generated by [Nuclei v2.9.2](https://github.com/projectdiscovery/nuclei)