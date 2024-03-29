### WAF Detection (waf-detect:apachegeneric) found on http://192.168.226.58
---
**Details**: **waf-detect:apachegeneric**  matched at http://192.168.226.58

**Protocol**: HTTP

**Full URL**: http://192.168.226.58/

**Timestamp**: Tue Sep 6 11:37:52 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | WAF Detection |
| Authors | dwisiswant0, lu4nx |
| Tags | waf, tech, misc |
| Severity | info |
| Description | A web application firewall was detected. |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 0.00 |

**Request**
```http
POST / HTTP/1.1
Host: 192.168.226.58
User-Agent: Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/44.0.2403.155 Safari/537.36
Connection: close
Content-Length: 27
Content-Type: application/x-www-form-urlencoded
Accept-Encoding: gzip

_=<script>alert(1)</script>
```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 2730
Content-Type: text/html; charset=UTF-8
Date: Tue, 06 Sep 2022 10:38:16 GMT
Server: Apache/2.4.6 (CentOS) PHP/5.4.16
X-Powered-By: PHP/5.4.16

<!doctype html>
<html lang="en-us">
<head>

	<meta charset="utf-8">
	<title>Simple PHP Photo Gallery</title>
	<link rel="stylesheet" href="css/screen.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/lightbox.css" type="text/css" media="screen" />

  <link href='http://fonts.googleapis.com/css?family=Fredoka+One|Open+Sans:400,700' rel='stylesheet' type='text/css'>

</head>
<body>
<div id="content">
<div class="section" id="example">


	<h2>Simple PHP Photo Gallery</h2>

	<div class="imageRow">
  	<div class="set">
	

<div class="single"><div class="wrap">
	<a href="images/examples/image-1.jpg" rel="lightbox[plants]"><img src="images/examples/thumb-1.jpg" /></a>
</div></div>

<div class="single"><div class="wrap">
	<a href="images/examples/image-2.jpg" rel="lightbox[plants]"><img src="images/examples/thumb-2.jpg" /></a>
</div></div>

<div class="single"><div class="wrap">
	<a href="images/examples/image-3.jpg" rel="lightbox[plants]"><img src="images/examples/thumb-3.jpg" /></a>
</div></div>

<div class="single"><div class="wrap">
	<a href="images/examples/image-4.jpg" rel="lightbox[plants]"><img src="images/examples/thumb-4.jpg" /></a>
</div></div>

<div class="single"><div class="wrap">
	<a href="images/examples/image-5.jpg" rel="lightbox[plants]"><img src="images/examples/thumb-5.jpg" /></a>
</div></div>

<div class="single"><div class="wrap">
	<a href="images/examples/image-6.jpg" rel="lightbox[plants]"><img src="images/examples/thumb-6.jpg" /></a>
</div></div>

		
  	</div>
  </div>
	
</div>

<hr />
</div>
<div style="margin-top: 100px; text-align: center;">Simple PHP Photo Gallery v0.8</div>
<!-- end #content -->

<script src="js/jquery-1.7.2.min.js"></script>
<script src="js/jquery-ui-1.8.18.custom.min.js"></script>
<script src="js/jquery.smooth-scroll.min.js"></script>
<script src="js/lightbox.js"></script>

<script>
  jQuery(document).ready(function($) {
      $('a').smoothScroll({
        speed: 1000,
        easing: 'easeInOutCubic'
      });

      $('.showOlderChanges').on('click', function(e){
        $('.changelog .old').slideDown('slow');
        $(this).fadeOut();
        e.preventDefault();
      })
  });

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-2196019-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>

```

References: 
- https://github.com/ekultek/whatwaf

**CURL Command**
```
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 192.168.226.58' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/44.0.2403.155 Safari/537.36' 'http://192.168.226.58/'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)