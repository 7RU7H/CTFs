### HTTP Missing Security Headers (http-missing-security-headers:clear-site-data) found on http://192.168.198.224
---
**Details**: **http-missing-security-headers:clear-site-data**  matched at http://192.168.198.224

**Protocol**: HTTP

**Full URL**: http://192.168.198.224

**Timestamp**: Tue Oct 4 17:52:37 +0100 BST 2022

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
Host: 192.168.198.224
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
Transfer-Encoding: chunked
Cache-Control: max-age=0, private, must-revalidate
Content-Type: text/html; charset=utf-8
Etag: W/"49bf20702340e1922602d8fe6b0bbf90"
Link: </packs/js/application-0a98d1e1abc2fd44621c.js>; rel=preload; as=script; nopush
Referrer-Policy: strict-origin-when-cross-origin
Set-Cookie: _simple_rails_session=CBZeexS7fwaIfECEQBRgcJtFsoShDEuGUD0JbY6ahg7NkYzYsjItIqrLl2k8iwFF2bYPqHNbXAQON3li7qDF%2FO9GKEp27jJeMlteVog6MqXmLVlSmwrmc%2BClu5kwF5%2BzTLs8MoLboH0mBBjENvVYsu%2BsdXz9WXkaZyxhfXlIt8hlN4ljpwY0PnrVEXsm7fOIiTYS5dsKF%2FUhwUjyfLhm5ax20k0tAA4vYSPIkOq8VY3kRPuKV7pR7S0rRLvzrwQoXIG3s3b3%2Bg8hil1%2FvhB6BEs9c6cUlw1ehRq2CGs%3D--iUkGn8fU5QdQK7nR--%2FPZrnewQEoRaWbSI9Bcm7g%3D%3D; path=/; HttpOnly; SameSite=Lax
Vary: Accept
X-Content-Type-Options: nosniff
X-Download-Options: noopen
X-Frame-Options: SAMEORIGIN
X-Permitted-Cross-Domain-Policies: none
X-Request-Id: 46ab6c31-a423-4dc0-8fdd-f083a4ca8421
X-Runtime: 0.008348
X-Xss-Protection: 1; mode=block

<!DOCTYPE html>
<html>
<head>
  <title>notes.pg</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-param" content="authenticity_token" />
<meta name="csrf-token" content="rJUuQ8mjRNkkZmV3jHlYA1UjAus0ot7UqD2vkk8-jyiF4wLQ1ea8-xFNHn1TIvVl-0Gvs4t_fhcxpItPVG-Hzg" />
  

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
            <a href="#!" class="text-reset">Orders</a>
      .... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36' 'http://192.168.198.224'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)