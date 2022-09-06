### HTTP Missing Security Headers (http-missing-security-headers:x-content-type-options) found on https://sup3rs3cr3t.brainfuck.htb
---
**Details**: **http-missing-security-headers:x-content-type-options**  matched at https://sup3rs3cr3t.brainfuck.htb

**Protocol**: HTTP

**Full URL**: https://sup3rs3cr3t.brainfuck.htb

**Timestamp**: Wed Aug 17 20:52:10 +0100 BST 2022

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
Host: sup3rs3cr3t.brainfuck.htb
User-Agent: Mozilla/5.0 (Windows NT 6.4; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36
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
Content-Type: text/html; charset=UTF-8
Date: Wed, 17 Aug 2022 19:52:12 GMT
Server: nginx/1.10.0 (Ubuntu)
Set-Cookie: flarum_session=5shmj49grsmpl3ngftc2hrktf0; Path=/; HttpOnly
X-Csrf-Token: wTgQAv0a6gmCq61QuzpSXGUkcZCgbHsGvWOZLQAd

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Super Secret Forum</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <meta name="theme-color" content="#4D698E">

          <link rel="stylesheet" href="https://sup3rs3cr3t.brainfuck.htb/assets/forum-3dcf9a18.css">
    
    
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700,600">
  </head>

  <body>
    <div id="app" class="App">

  <div id="app-navigation" class="App-navigation"></div>

  <div id="drawer" class="App-drawer">

    <header id="header" class="App-header">
      <div id="header-navigation" class="Header-navigation"></div>
      <div class="container">
        <h1 class="Header-title">
          <a href="https://sup3rs3cr3t.brainfuck.htb" id="home-link">
                                      Super Secret Forum
                      </a>
        </h1>
        <div id="header-primary" class="Header-primary"></div>
        <div id="header-secondary" class="Header-secondary"></div>
      </div>
    </header>

  </div>

  <main class="App-content">
    <div id="content"></div>

    <div id="flarum-loading" style="display: none">
  Loading...
</div>

  <noscript>
    <div class="Alert">
      <div class="container">
        This site is best viewed in a modern browser with JavaScript enabled.
      </div>
    </div>

    <div class="container">
    <h2>All Discussions</h2>

    <ul>
                    <li>
                <a href="https://sup3rs3cr3t.brainfuck.htb/d/1-development">
                    Development
                </a>
            </li>
            </ul>

    <a href="https://sup3rs3cr3t.brainfuck.htb/all?page=2">Next Page &raquo;</a>
</div>

  </noscript>


    <div class="App-composer">
      <div class="container">
        <div id="composer"></div>
      </div>
    </div>
  </main>

</div>


    <div id="modal"></div>
    <div id="alerts"></div>

          <script>
        document.getElementById('flarum-loading').style.display = 'block';
      </script>

              <script src="https://sup3rs3cr3t.brainfuck.htb/assets/forum-22a6c33d.js"></script>
              <script src="https://sup3rs3cr3t.brainfuck.htb/assets/forum-en-df77f08d.js"></script>
      
      <script>
        document.getElementById('flarum-loading').style.display = 'none';
                  var app = System.get('flarum/app').default;
          var modules = ["locale","flarum\/approval\/main","flarum\/emoji\/main","flarum\/flags\/main","flarum\/likes\/main","flarum\/lock\/main","flarum\/mentions\/main","flarum\/sticky\/main","flarum\/subscriptions\/main","flarum\/suspend\/main","flarum\/tags\/main"];

          for (var i in modules) {
            var module = System.get(modules[i]);
            if (module.default) module.default(app);
          }

          app.boot({"resources":[{"type":"forums","id":"1","attributes":{"title":"Super Secret Forum","description":"","baseUrl":"https:\/\/sup3rs3cr3t.brainfuck.htb","basePath":"","debug":true,"apiUrl":"https:\/\/sup3rs3cr3t.brainfuck.htb\/api","welcomeTitle":"Welcome to Super Secret Forum","welcomeMessage":"Please rely on your own encryption methods for sensitive material.","themePrimaryColor":"#4D698E","themeSecondaryColor":"#4D698E","logoUrl":null,"faviconUrl":null,"headerHtml":null,"allowSignUp":true,"defaultRoute":"\/all","canViewDiscussions":true,"canStartDiscussion":false,"canViewFlags":false,"guidelinesUrl":null,"minPrimaryTags":"1","maxPrimaryTags":"1","minSecondaryTags":"0","maxSecondaryTags":"3"},"relationships":{"groups":{"data":[{"type":"groups","id":"1"},{"type":"groups","id":"2"},{"type":"groups","id":"3"},{"type":"groups","id":"4"},{"type":"groups","id":"5"}]},"tags":{"data":[{"type":"tags","id":"1"}]}}},{"type":"groups","id":"1","attributes":{"nameSingular":"Admin","namePlural":"Admins","color":"#B72A2A","icon":"wrench"}},{"type":"groups","id":"2","attributes":{"nameSingular":"Guest","namePlural":"Guests","color":null,"icon":null}},{"type":"groups","id":"3","attributes":{"nameSingular":"Member","namePlural":"Members","color":null,"icon":null}},{"type":"groups","id":"4","attributes":{"nameSingular":"Mod","namePlural":"Mods","color":"#80349E","icon":"bolt"}},{"type":"groups","id":"5","attributes":{"nameSingular":"Privileged","namePlural":"Privileged","color":"","icon":"key"}},{"type":"tags","id":"1","attributes":{"name":"General","description":null,"slug":"general","color":"#888","backgroundUrl":null,"backgroundMode":null,"iconUrl":null,"discussionsCount":3,"position":0,"defaultSort":null,"isChild":false,"isHidden":false,"lastTime":"2017-04-29T11:35:19+00:00","canStartDiscussion":false,"canAddToDiscussion":false},"relationships":{"lastDiscussi.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.4; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36' 'https://sup3rs3cr3t.brainfuck.htb'
```
---
Generated by [Nuclei 2.7.4](https://github.com/projectdiscovery/nuclei)