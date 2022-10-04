### XSS-Protection Header - Cross-Site Scripting (xss-deprecated-header) found on http://notes.pg
---
**Details**: **xss-deprecated-header**  matched at http://notes.pg

**Protocol**: HTTP

**Full URL**: http://notes.pg

**Timestamp**: Tue Oct 4 18:32:52 +0100 BST 2022

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
Host: notes.pg
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1866.237 Safari/537.36
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
Etag: W/"c43ec0043fb812372e221139b3521b5f"
Link: </packs/js/application-0a98d1e1abc2fd44621c.js>; rel=preload; as=script; nopush
Referrer-Policy: strict-origin-when-cross-origin
Set-Cookie: _simple_rails_session=b6Wks9al0ucZMp9KCU75HXOngi2tMyQ6CVs9GAEY2jX0XqOxuV%2FMmT3zKZpZCt%2BILISRFbZIlmbz0DG4e7LRIhoFZJ6CFSyaHfhIGQDB3u8DsMyVaufUL%2FPvfBe%2BG42I4ffaqsSFfuAcWHvKZSxtFvr%2B%2BVzxCofeEYJnAOR6LFJPHiMrBdrxGnVEDTdKINZjqPwdK2FT8JbaL1T1ko%2Bj2TnbQvGkBqJIkVY6TVrC%2Bv6vtt2fvw%2BPYUXmsSSfbajW96f9JqCcYY3nipsqpdO4mDD4VJEWSk9zgIVyIvA%3D--Ee%2F14IV6LUfIMLYV--v%2FXeKtfpeS2sEOK1JC%2B93Q%3D%3D; path=/; HttpOnly; SameSite=Lax
Vary: Accept
X-Content-Type-Options: nosniff
X-Download-Options: noopen
X-Frame-Options: SAMEORIGIN
X-Permitted-Cross-Domain-Policies: none
X-Request-Id: c4158a5e-0969-46b7-a518-fbfe04a1715a
X-Runtime: 0.005175
X-Xss-Protection: 1; mode=block

<!DOCTYPE html>
<html>
<head>
  <title>notes.pg</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-param" content="authenticity_token" />
<meta name="csrf-token" content="QzLwwhy4V9KbaBxHf774bcpPA52UkF2xdLvVWCAaHToze5aVR-uM0voHKRpJ3SJreYD-3o6DKRd_5Sf--NLdsw" />
  

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
            <a href="#!" class="text-reset">Orders</a.... Truncated ....
```

**Extra Information**

**Extracted results**:

- 1; mode=block


References: 
- https://developer.mozilla.org/en-us/docs/web/http/headers/x-xss-protection
- https://owasp.org/www-project-secure-headers/#x-xss-protection

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1866.237 Safari/537.36' 'http://notes.pg'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)