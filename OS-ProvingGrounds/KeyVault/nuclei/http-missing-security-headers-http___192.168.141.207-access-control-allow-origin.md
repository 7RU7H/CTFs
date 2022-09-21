### HTTP Missing Security Headers (http-missing-security-headers:access-control-allow-origin) found on http://192.168.141.207
---
**Details**: **http-missing-security-headers:access-control-allow-origin**  matched at http://192.168.141.207

**Protocol**: HTTP

**Full URL**: http://192.168.141.207

**Timestamp**: Wed Sep 21 20:22:39 +0100 BST 2022

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
Host: 192.168.141.207
User-Agent: Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/44.0.2403.155 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Accept-Ranges: bytes
Content-Type: text/html
Date: Wed, 21 Sep 2022 19:22:38 GMT
Etag: "64cb-5d9b84f961900-gzip"
Last-Modified: Tue, 08 Mar 2022 17:28:36 GMT
Server: Apache/2.4.41 (Ubuntu)
Vary: Accept-Encoding

<!DOCTYPE html>
<!-- saved from url=(0025)#www.KeyVault.com/ -->
<html class="no-js" lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="cleartype" content="on">
        


<iframe src="javascript:void(0)" title="" style="width: 0px; height: 0px; border: 0px; display: none;" src="./keyvault_files/saved_resource(5).html"></iframe><script type="text/javascript">
    window.useAleroApiJSON=true
</script>

        
        <meta name="msvalidate.01" content="2B12B4CCABC21F8773D59D5009484A0F">
<meta name="google-site-verification" content="_vBOw7EHSUUi-MOcXAZmM9Ub-U6WQ1ABAWgL-60t2yI">
<script>window.resourceBaseUrl = '/dist/';</script>
<link rel="preload" as="style" href="./keyvault_files/doy8hqb.css">
<link rel="stylesheet" type="text/css" href="./keyvault_files/doy8hqb.css">
<link rel="preload" as="style" href="./keyvault_files/styles.css">
<link rel="stylesheet" type="text/css" href="./keyvault_files/styles.css">
<script defer="" src="./keyvault_files/analyticsjs.js"></script>
<script defer="" src="./keyvault_files/main.js"></script>
<script defer="" src="./keyvault_files/lmi-ma-min.js"></script>



<title>KeyVault Password Manager &amp; Vault App with Single-Sign On &amp; MFA Solutions | KeyVault</title>
<meta name="description" content="Go beyond saving passwords with the best password manager! Generate strong passwords and store them in a secure vault. Now with single-sign on (SSO) and adaptive MFA solutions that integrate with over 1,200 apps.">


<link rel="canonical" href="#www.KeyVault.com/">

    <link rel="shortcut icon" href="#www.KeyVault.com/-/media/43c6c6862a08410a8ef34ab46a3a750b.ico">
            <meta name="viewport" content="width=device-width, initial-scale=1">

<script type="text/javascript">
    window.appInsights.queue.push(function () {
        appInsights.context.addTelemetryInitializer(function (envelope) {

            var telemetryItem = envelope.data.baseData;
                
            if (telemetryItem.url && telemetryItem.url.includes("?email")) {
                telemetryItem.url = maskEmailAddress(telemetryItem.url);
            }
        });
    });
    function maskEmailAddress (emailAddress) {
        function mask(str) {
            var strLen = str.length;
            if (strLen > 4) {
                return str.substr(0, 1) + str.substr(1, strLen - 1).replace(/\w/g, '*') + str.substr(-1,1);
            } 
            return str.replace(/\w/g, '*');
        }
        return emailAddress.replace(/([\w.]+)@([\w.]+)(\.[\w.]+)/g, function (m, p1, p2, p3) {      
            return mask(p1) + '@' + mask(p2) + p3;
        });
     
        return emailAddress;
    }
</script>        <!--Info Start-->
<script type="text/javascript">
var clientData = {"registry_company_name":"Hathway IP Over Cable Internet","registry_city":"Mumbai","registry_state":"MH","registry_zip_code":"400070","registry_area_code":null,"registry_dma_code":null,"registry_country":"India","registry_country_code":"IN","registry_country_code3":null,"registry_latitude":19.07,"registry_longitude":72.89,"company_name":"DXC Technology Company","demandbase_sid":81204,"marketing_alias":"DXC Technology","industry":"Software & Technology","sub_industry":"Data & Technical Services","employee_count":70000,"isp":false,"primary_sic":"7374","primary_naics":"518210","street_address":"1775 Tysons Blvd","city":"Mc Lean","state":"VA","zip":"22102","country":"US","country_name":"United States","phone":"703-876-1000","stock_ticker":"DXC","web_site":"dxc.com","annual_sales":7607000000,"revenue_range":"Over $5B","employee_range":"Xlarge","b2b":true,"b2c":false,"traffic":"Low","latitude":38.92,"longitude":-77.22,"fortune_1000":true,"forbes_2000":true,"information_level":"Detailed","audience":"Enterprise Business","audience_segment":"Software & Technology","ip":"27.4.39.133","region_name":"Maharashtra"};
</script>
<!--Info End-->        
<meta name="VIcurrentDateTime" content="637823564690020990">
<script type="text/javascript" src="./keyvault_files/VisitorIdentification.js"></script>

    
                              <script>!function(a){var e="#s.go-mpulse.net/boomerang/",t="addEventListener";if("False"=="True")a.BOOMR_config=a.BOOMR_config||{},a.BOOMR_config.PageParams=a.BOOMR_config.PageParams||{},a.BOOMR_config.PageParams.pci=!0,e="#s2.go-mpulse.net/boomerang/";if(window.BOOMR_API_key="WH2TM-VVP9E-KZ9SR-39YA8-GGXQ9",function(){function n(e){a.BOOMR_onload=e&&e.timeStamp||(new Date).getTime()}if(!a.BOOMR||!a.BOOMR.version&&!a.BOOMR.snippetExecuted){a.BOOMR=a.BOOMR||{},a.BOOMR.snippetExecuted=!0;var i,_,o,r=document.createElement("iframe");if(a[t])a[t]("load",n,!1);else if(a.attachEvent)a.attachEvent("onload",n);r.src="javascript:void(0)",r.title="",r.role="presentatio.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/44.0.2403.155 Safari/537.36' 'http://192.168.141.207'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)