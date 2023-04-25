### HttpOnly Cookie - Detect (httponly-cookie-detect) found on http://10.129.198.176
---
**Details**: **httponly-cookie-detect**  matched at http://10.129.198.176

**Protocol**: HTTP

**Full URL**: http://10.129.198.176

**Timestamp**: Tue Apr 25 10:21:00 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | HttpOnly Cookie - Detect |
| Authors | mr. bobo hp |
| Tags | misconfig, http, cookie, generic |
| Severity | info |
| Description | Checks whether cookies in the HTTP response contain the HttpOnly attribute. If the HttpOnly flag is set, it means that the cookie is HTTP-only<br> |
| CVSS-Metrics | [CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N](https://www.first.org/cvss/calculator/3.0#CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N) |
| CVSS-Score | 0.00 |

**Request**
```http
GET / HTTP/1.1
Host: 10.129.198.176
User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36
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
Date: Tue, 25 Apr 2023 09:21:00 GMT
Server: Apache/2.4.29 (Ubuntu)
Set-Cookie: XSRF-TOKEN=eyJpdiI6Iko2RzNENURTN3BtaUUrZ0tFQmNUOEE9PSIsInZhbHVlIjoicjlpSjF6S2VaQkFJY2wwWE10R0FuZlpHcFl3K2xQa2VUZ2hUcU8vNGZjNkhBMjR3QWdUZExWYlVuSDE2NldxQUVrSUN0b3RLZ0hndEJ0c1gvTWFERUtIN3VhMEcwbFZnQXIxUGtqNk5FMlFqUTNqdzlBTnNSZjd6VE9TT1FyeUciLCJtYWMiOiIwZmVlM2ZjMjI2NDczNTBmZjExYWE4YjRkODQ3YTJjMmExNDQ0OTkwMTc0NDgwYjhkMzlhYzI4MDM5ZmQ2ZDkxIiwidGFnIjoiIn0%3D; expires=Tue, 25-Apr-2023 11:21:00 GMT; Max-Age=7200; path=/; samesite=lax
Set-Cookie: laravel_session=eyJpdiI6ImQ3OEJMSTZ1Z3dlMnhOd2RicVdqTEE9PSIsInZhbHVlIjoiVjgwQWJaVzFzRUtiUk82cTFGa3BMdzdJQnNYSU9sYncvTmV0RDRXQWc1WlBoT0ZWVkk3Y3h4cHd3cC9wZmFDZm5Wb05XZFZSRkI5MUZhRmpDVVBHc2dlWjd6VWtFbEh3eGxMaFhxeVM2YTlNdHlYVEJFZzFoTGtFRFo1RVlDYlEiLCJtYWMiOiJhMzkyODBjYzY2YzQ1MDU5NmQ4NjgzN2EwNDlkZmJmODFmZWUzYWUyNDFlZGVmM2E4ZDlkNTMzMDA3ZDIxZDFkIiwidGFnIjoiIn0%3D; expires=Tue, 25-Apr-2023 11:21:00 GMT; Max-Age=7200; path=/; httponly; samesite=lax
Vary: Accept-Encoding

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>HTTP Monitoring Tool</title>
    <link href="http://10.129.198.176/css/app.css" rel="stylesheet" type="text/css"/>
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
	    <form method="post" action="http://10.129.198.176/webhook">
		<input type="hidden" name="_token" value="UYO3CMCJDL0391Gyk1y4RRrNrhfAfOdicNjcFC7S">
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
<script src="http://10.129.198.176/js/app.js" type="text/js"></script>


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
- https://stackoverflow.com/questions/4316539/how-do-i-test-httponly-cookie-flag

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36' 'http://10.129.198.176'
```
---
Generated by [Nuclei v2.9.2](https://github.com/projectdiscovery/nuclei)