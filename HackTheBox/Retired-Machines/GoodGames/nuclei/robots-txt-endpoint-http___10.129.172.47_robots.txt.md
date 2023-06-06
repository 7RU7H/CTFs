### robots.txt endpoint prober (robots-txt-endpoint) found on http://10.129.172.47
---
**Details**: **robots-txt-endpoint**  matched at http://10.129.172.47

**Protocol**: HTTP

**Full URL**: http://10.129.172.47/robots.txt

**Timestamp**: Tue Jun 6 17:23:49 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | robots.txt endpoint prober |
| Authors | caspergn, pdteam |
| Tags | misc, generic |
| Severity | info |

**Request**
```http
GET /robots.txt HTTP/1.1
Host: 10.129.172.47
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
Content-Type: text/html; charset=utf-8
Date: Tue, 06 Jun 2023 16:23:28 GMT
Server: Werkzeug/2.0.2 Python/3.9.2
Vary: Accept-Encoding

<!DOCTYPE html>

    
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>GoodGames | 404</title>

    <meta name="description" content="GoodGames - Bootstrap template for communities and games store">
    <meta name="keywords" content="game, gaming, template, HTML template, responsive, Bootstrap, premium">
    <meta name="author" content="_nK">

    <link rel="icon" type="image/png" href="static/images/favicon.png">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- START: Styles -->

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700%7cOpen+Sans:400,700" rel="stylesheet" type="text/css">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="static/vendor/bootstrap/dist/css/bootstrap.min.css">

    <!-- FontAwesome -->
    <script defer src="static/vendor/fontawesome-free/js/all.js"></script>
    <script defer src="static/vendor/fontawesome-free/js/v4-shims.js"></script>

    <!-- IonIcons -->
    <link rel="stylesheet" href="static/vendor/ionicons/css/ionicons.min.css">

    <!-- Flickity -->
    <link rel="stylesheet" href="static/vendor/flickity/dist/flickity.min.css">

    <!-- Photoswipe -->
    <link rel="stylesheet" type="text/css" href="static/vendor/photoswipe/dist/photoswipe.css">
    <link rel="stylesheet" type="text/css" href="static/vendor/photoswipe/dist/default-skin/default-skin.css">

    <!-- Seiyria Bootstrap Slider -->
    <link rel="stylesheet" href="static/vendor/bootstrap-slider/dist/css/bootstrap-slider.min.css">

    <!-- Summernote -->
    <link rel="stylesheet" type="text/css" href="static/vendor/summernote/dist/summernote-bs4.css">

    <!-- GoodGames -->
    <link rel="stylesheet" href="static/css/goodgames.css">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="static/css/custom.css">
    
    <!-- END: Styles -->

    <!-- jQuery -->
    <script src="static/vendor/jquery/dist/jquery.min.js"></script>
    
    
</head>


<!--
    Additional Classes:
        .nk-page-boxed
-->
<body>
    
    

    <div class="nk-main">
        

        
<div class="nk-fullscreen-block">
    <div class="nk-fullscreen-block-top">
        <div class="text-center">
            <div class="nk-gap-4"></div>
            <a href="/"><img src="static/images/logo-2.png" alt="GoodGames"></a>
            <div class="nk-gap-2"></div>
        </div>
    </div>
    <div class="nk-fullscreen-block-middle">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-6 offset-md-3 col-lg-4 offset-lg-4">
                    <h1 class="text-main-1" style="font-size: 150px;">404</h1>

                    <div class="nk-gap"></div>
                    <h2 class="h4">You chose the wrong path!</h2>

                    <div>Or such a page just doesn't exist... <br> Would you like to go back to the homepage?</div>
                    <div class="nk-gap-3"></div>

                    <a href="/" class="nk-btn nk-btn-rounded nk-btn-color-white">Return to Homepage</a>
                </div>
            </div>
            <div class="nk-gap-3"></div>
        </div>
    </div>
</div>


        
    </div>

    

    
        <!-- START: Page Background -->

    <div class="nk-page-background-fixed" style="background-image: url('static/images/bg-fixed-2.jpg');"></div>

<!-- END: Page Background -->

    

    
        <!-- START: Search Modal -->
<div class="nk-modal modal fade" id="modalSearch" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="ion-android-close"></span>
                </button>

                <h4 class="mb-0">Search</h4>

                <div class="nk-gap-1"></div>
                <form action="#" class="nk-form nk-form-style-1">
                    <input type="text" value="" name="search" class="form-control" placeholder="Type something and press Enter" autofocus>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END: Search Modal -->
    

    
        <!-- START: Login Modal -->
<div class="nk-modal modal fade" id="modalLogin" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="ion-android-close"></span>
                </button>

                <h4 class="mb-0"><span class="text-main-1">Sign</span> In</h4>

                <div class="nk-gap-1"></div>
                <form action="#" class="nk-form text-whi.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686 on x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2820.59 Safari/537.36' 'http://10.129.172.47/robots.txt'
```
---
Generated by [Nuclei v2.9.4](https://github.com/projectdiscovery/nuclei)