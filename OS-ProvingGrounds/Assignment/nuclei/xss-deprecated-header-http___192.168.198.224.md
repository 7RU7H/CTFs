### XSS-Protection Header - Cross-Site Scripting (xss-deprecated-header) found on http://192.168.198.224
---
**Details**: **xss-deprecated-header**  matched at http://192.168.198.224

**Protocol**: HTTP

**Full URL**: http://192.168.198.224

**Timestamp**: Tue Oct 4 17:52:34 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | XSS-Protection Header - Cross-Site Scripting |
| Authors | joshlarsen |
| Tags | xss, misconfig, generic |
| Severity | info |
| Description | Setting the XSS-Protection header is deprecated. Setting the header to anything other than `0` can actually introduce an XSS vulnerability. |
| CVSS-Metrics | [CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N](https://www.first.org/cvss/calculator/3.0#CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N) |
| CVSS-Score | 0.00 |

**Request**
```http
GET / HTTP/1.1
Host: 192.168.198.224
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36
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
Cache-Control: max-age=0, private, must-revalidate
Content-Type: text/html; charset=utf-8
Etag: W/"a92afc4472cdeaea9337bc628f2c0bd2"
Link: </packs/js/application-0a98d1e1abc2fd44621c.js>; rel=preload; as=script; nopush
Referrer-Policy: strict-origin-when-cross-origin
Set-Cookie: _simple_rails_session=Obvvlg4%2BSZLGyV5YG3qXXoJnXfGzUhsFsGMMGJM%2BX7TeW3JHiUD1kT4gK%2BdhfRBVpCV30R2ySxuSjXBTDUUCbdIE6GRMDuIb%2FhJsIH%2FOy%2F4IS5D%2FDoYioJVlQaTwYbyhlInI2NTiYD2gxPns4uUs8B37TuvKgBJ7%2BMXKX5XM2TEkHf3Gq9hwtC6A%2ByS3JIXIlwHvTXZxdWsaH%2F0PQhqu%2FqGg2yj0jBBHKwq8lxnaxSJK6QaSX%2BXewHeXnqUk0jeQjBY7nWGkoTZUNSFQRoruvPjfL%2Fs7BzRMe8acMq8%3D--3saYhYOcGMo7Bfws--7BZBIH2YOayTtY2c9%2Bp1aQ%3D%3D; path=/; HttpOnly; SameSite=Lax
Vary: Accept
X-Content-Type-Options: nosniff
X-Download-Options: noopen
X-Frame-Options: SAMEORIGIN
X-Permitted-Cross-Domain-Policies: none
X-Request-Id: b8ccfbee-f55f-4178-beba-1a0bc787277e
X-Runtime: 0.084415
X-Xss-Protection: 1; mode=block

<!DOCTYPE html>
<html>
<head>
  <title>notes.pg</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-param" content="authenticity_token" />
<meta name="csrf-token" content="A8WocNf7bW414CP3UVW6kNDE2ssTyAXAcXeJcqBh3GaeBDBC0q8kqnQwaqYLyQF4W43Ejos_98w4ngPLqVKNZg" />
  

  <!-- Warning !! ensure that "stylesheet_pack_tag" is used, line below -->
  
  <script src="/packs/js/application-0a98d1e1abc2fd44621c.js" data-turbolinks-track="reload"></script>
</head>

<body>

<div class="collapse" id="navbarToggleExternalContent">
  <div class="bg-dark p-4">
    <h5 class="text-white h4">notes.pg</h5>
    <span class="text-muted">Nothing to see here.</span>
  </div>
</div>
<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div>

        <a class="navbar-toggler" href="/login" style="text-decoration: none;">
          Login
        </a>
        <a class="navbar-toggler" href="/register" style="text-decoration: none;">
          Sign Up
        </a>


    </div>

  </div>
</nav>

<div class="container p-4">

  <main>
  <section class="py-5 text-center container">
    <div class="row py-lg-5">
      <div class="col-lg-6 col-md-8 mx-auto">
        <h1 class="fw-light">notes.pg</h1>
        <p class="lead text-muted">This is a free online service that allows you to manage notes online, and
          securely.</p>
        <p>
          <a href="/login" class="btn btn-primary my-2">Login</a>
          <a href="/register" class="btn btn-secondary my-2">Register</a>
        </p>
      </div>
    </div>
  </section>
</main>


</div>

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
        <i class="fab fa-github"></i>
      </a>
    </div>
    <!-- Right -->
  </section>
  <!-- Section: Social media -->

  <!-- Section: Links  -->
  <section class="">
    <div class="container text-center text-md-start mt-5">
      <!-- Grid row -->
      <div class="row mt-3">
        <!-- Grid column -->
        <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
          <!-- Content -->
          <h6 class="text-uppercase fw-bold mb-4">
            <i class="fas fa-gem me-3"></i>notes.pg
          </h6>
        </div>
        <!-- Grid column -->

        <!-- Grid column -->
        <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
          <!-- Links -->
          <h6 class="text-uppercase fw-bold mb-4">
            Products
          </h6>
          <p>
            <a href="#!" class="text-reset">Bootstrap</a>
          </p>
          <p>
            <a href="#!" class="text-reset">Rails</a>
          </p>
        </div>
        <!-- Grid column -->

        <!-- Grid column -->
        <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
          <!-- Links -->
          <h6 class="text-uppercase fw-bold mb-4">
            Useful links
          </h6>
          <p>
            <a href="#!" class="text-reset">Pricing</a>
          </p>
          <p>
            <a href="#!" class="text-reset">Settings</a>
          </p>
          <p>
            <a href="#!" class="text-reset">Order.... Truncated ....
```

**Extra Information**

**Extracted results**:

- 1; mode=block


References: 
- https://developer.mozilla.org/en-us/docs/web/http/headers/x-xss-protection
- https://owasp.org/www-project-secure-headers/#x-xss-protection

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36' 'http://192.168.198.224'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)