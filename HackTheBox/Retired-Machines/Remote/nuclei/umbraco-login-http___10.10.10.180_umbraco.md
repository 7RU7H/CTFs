### Umbraco Login Panel - Detect (umbraco-login) found on http://10.10.10.180
---
**Details**: **umbraco-login**  matched at http://10.10.10.180

**Protocol**: HTTP

**Full URL**: http://10.10.10.180/umbraco

**Timestamp**: Wed May 10 20:03:32 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | Umbraco Login Panel - Detect |
| Authors | ola456 |
| Tags | panel, umbraco |
| Severity | info |
| Description | Umbraco login panel was detected. |
| CVSS-Metrics | [CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N](https://www.first.org/cvss/calculator/3.0#CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N) |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 0.00 |
| shodan-query | http.title:"Umbraco" |

**Request**
```http
GET /umbraco HTTP/1.1
Host: 10.10.10.180
User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 4040
Cache-Control: no-cache, no-store, must-revalidate
Content-Type: text/html; charset=utf-8
Date: Wed, 10 May 2023 19:03:32 GMT
Expires: -1
Pragma: no-cache


<!DOCTYPE html>
<html lang="en">
<head>
    <base href="/umbraco/" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="noindex, nofollow">
    <meta name="pinterest" content="nopin" />

    <title ng-bind="$root.locationTitle">Umbraco</title>

    <link href="/DependencyHandler.axd?s=L3VtYnJhY28vYXNzZXRzL2Nzcy91bWJyYWNvLmNzczsvdW1icmFjb19jbGllbnQvdHJlZS90cmVlaWNvbnMuY3NzOy91bWJyYWNvL2xpYi9ib290c3RyYXAtc29jaWFsL2Jvb3RzdHJhcC1zb2NpYWwuY3NzOy91bWJyYWNvL2xpYi9mb250LWF3ZXNvbWUvY3NzL2ZvbnQtYXdlc29tZS5taW4uY3NzOw&amp;t=Css&amp;cdv=1" type="text/css" rel="stylesheet"/>
</head>
<body ng-class="{'touch':touchDevice, 'emptySection':emptySection, 'umb-drawer-is-visible':drawer.show}" ng-controller="Umbraco.MainController" id="umbracoMainPageBody">

    <div ng-hide="!authenticated" ng-cloak>

        <!-- help dialog controller by the help button - this also forces the backoffice UI to shift 400px  -->
        <umb-drawer data-element="drawer" ng-if="drawer.show" model="drawer.model" view="drawer.view"></umb-drawer>

        <div id="mainwrapper" class="clearfix" ng-click="closeDialogs($event)">

            <umb-navigation></umb-navigation>

            <section id="contentwrapper">
                <div id="contentcolumn" ng-view>
                    <noscript>
                        <div>
                            <h3><img src="assets/img/application/logo.png" alt="Umbraco logo" style="vertical-align: text-bottom;" /> Umbraco</h3>
                            <p>For full functionality of Umbraco CMS it is necessary to enable JavaScript.</p>
                            <p>Here are the <a href="https://www.enable-javascript.com/" target="_blank" style="text-decoration: underline;">instructions how to enable JavaScript in your web browser</a>.</p>
                        </div>
                    </noscript>
                </div>
            </section>

            <umb-tour
                ng-if="tour.show"
                model="tour">
            </umb-tour>

            <umb-notifications></umb-notifications>

        </div>

    </div>

    <umb-backdrop
        ng-if="backdrop.show"
        backdrop-opacity="backdrop.opacity"
        highlight-element="backdrop.element"
        highlight-prevent-click="backdrop.elementPreventClick"
        disable-events-on-click="backdrop.disableEventsOnClick">
    </umb-backdrop>

    <umb-overlay
        ng-if="ysodOverlay.show"
        model="ysodOverlay"
        position="right"
        view="ysodOverlay.view">
    </umb-overlay>

    <script type="text/javascript">
                var Umbraco = {};
                Umbraco.Sys = {};
                Umbraco.Sys.ServerVariables = {"umbracoUrls":{"externalLoginsUrl":"/umbraco/ExternalLogin","serverVarsJs":"/umbraco/ServerVariables","authenticationApiBaseUrl":"/umbraco/backoffice/UmbracoApi/Authentication/","currentUserApiBaseUrl":"/umbraco/backoffice/UmbracoApi/CurrentUser/"},"umbracoSettings":{"imageFileTypes":"jpeg,jpg,gif,bmp,png,tiff,tif","maxFileSize":"51200","allowPasswordReset":true,"loginBackgroundImage":"assets/img/installer.jpg"},"isDebuggingEnabled":false,"application":{"cacheBuster":"52370f201e0ca426f00534ed31f892e7","applicationPath":"/"},"features":{"disabledFeatures":{"disableTemplates":false}}};
            </script>

    <script>
        document.angularReady = function(app) {
            
var errors = [];
app.value("externalLoginInfo", {
errors: errors,
providers: []
});

            
var errors = [];
app.value("resetPasswordCodeInfo", {
errors: errors,
resetCodeModel: null
});

        }
    </script>

    <script src="lib/rgrove-lazyload/lazyload.js"></script>
    <script src="/umbraco/Application?umb__rnd=aca82ec1695f1f7b87d0e870bad99a0b"></script>

</body>
</html>

```

References: 
- https://our.umbraco.com/documentation/fundamentals/backoffice/login/

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36' 'http://10.10.10.180/umbraco'
```
---
Generated by [Nuclei v2.9.3](https://github.com/projectdiscovery/nuclei)