### Exposed Authentication.asmx (exposed-authentication-asmx) found on http://10.129.1.183
---
**Details**: **exposed-authentication-asmx**  matched at http://10.129.1.183

**Protocol**: HTTP

**Full URL**: http://10.129.1.183/_vti_bin/Authentication.asmx?op=Mode

**Timestamp**: Mon Jun 6 16:46:46 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Exposed Authentication.asmx |
| Authors | dhiyaneshdk |
| Tags | config, exposure |
| Severity | low |

**Request**
```http
GET /_vti_bin/Authentication.asmx?op=Mode HTTP/1.1
Host: 10.129.1.183
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 5964
Cache-Control: private, max-age=0
Content-Type: text/html; charset=utf-8
Date: Mon, 06 Jun 2022 15:46:46 GMT
Microsoftsharepointteamservices: 15.0.0.4420
Request-Id: f5bd44a0-3c00-f075-0000-016c10a5d5fc
Server: Microsoft-IIS/10.0
Spiislatency: 12
Sprequestduration: 372
Sprequestguid: f5bd44a0-3c00-f075-0000-016c10a5d5fc
X-Aspnet-Version: 4.0.30319
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-Ms-Invokeapp: 1; RequireReadOnly
X-Powered-By: ASP.NET
X-Sharepointhealthscore: 5



<html>

    <head><link rel="alternate" type="text/xml" href="/_vti_bin/Authentication.asmx?disco" />

    <style type="text/css">
    
		BODY { color: #000000; background-color: white; font-family: Verdana; margin-left: 0px; margin-top: 0px; }
		#content { margin-left: 30px; font-size: .70em; padding-bottom: 2em; }
		A:link { color: #336699; font-weight: bold; text-decoration: underline; }
		A:visited { color: #6699cc; font-weight: bold; text-decoration: underline; }
		A:active { color: #336699; font-weight: bold; text-decoration: underline; }
		A:hover { color: cc3300; font-weight: bold; text-decoration: underline; }
		P { color: #000000; margin-top: 0px; margin-bottom: 12px; font-family: Verdana; }
		pre { background-color: #e5e5cc; padding: 5px; font-family: Courier New; font-size: x-small; margin-top: -5px; border: 1px #f0f0e0 solid; }
		td { color: #000000; font-family: Verdana; font-size: .7em; }
		h2 { font-size: 1.5em; font-weight: bold; margin-top: 25px; margin-bottom: 10px; border-top: 1px solid #003366; margin-left: -15px; color: #003366; }
		h3 { font-size: 1.1em; color: #000000; margin-left: -15px; margin-top: 10px; margin-bottom: 10px; }
		ul { margin-top: 10px; margin-left: 20px; }
		ol { margin-top: 10px; margin-left: 20px; }
		li { margin-top: 10px; color: #000000; }
		font.value { color: darkblue; font: bold; }
		font.key { color: darkgreen; font: bold; }
		font.error { color: darkred; font: bold; }
		.heading1 { color: #ffffff; font-family: Tahoma; font-size: 26px; font-weight: normal; background-color: #003366; margin-top: 0px; margin-bottom: 0px; margin-left: -30px; padding-top: 10px; padding-bottom: 3px; padding-left: 15px; width: 105%; }
		.button { background-color: #dcdcdc; font-family: Verdana; font-size: 1em; border-top: #cccccc 1px solid; border-bottom: #666666 1px solid; border-left: #cccccc 1px solid; border-right: #666666 1px solid; }
		.frmheader { color: #000000; background: #dcdcdc; font-family: Verdana; font-size: .7em; font-weight: normal; border-bottom: 1px solid #dcdcdc; padding-top: 2px; padding-bottom: 2px; }
		.frmtext { font-family: Verdana; font-size: .7em; margin-top: 8px; margin-bottom: 0px; margin-left: 32px; }
		.frmInput { font-family: Verdana; font-size: 1em; }
		.intro { margin-left: -15px; }
           
    </style>

    <title>
	Authentication Web Service
</title></head>

  <body>

    <div id="content">

      <p class="heading1">Authentication</p><br>

      

      

      <span>
          <p class="intro">Click <a href="Authentication.asmx">here</a> for a complete list of operations.</p>
          <h2>Mode</h2>
          <p class="intro"></p>

          <h3>Test</h3>
          
          The test form is only available for requests from the local machine.
                 <span>
              <h3>SOAP 1.1</h3>
              <p>The following is a sample SOAP 1.1 request and response.  The <font class=value>placeholders</font> shown need to be replaced with actual values.</p>

              <pre>POST /_vti_bin/Authentication.asmx HTTP/1.1
Host: 10.129.1.183
Content-Type: text/xml; charset=utf-8
Content-Length: <font class=value>length</font>
SOAPAction: "http://schemas.microsoft.com/sharepoint/soap/Mode"

&lt;?xml version="1.0" encoding="utf-8"?&gt;
&lt;soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"&gt;
  &lt;soap:Body&gt;
    &lt;Mode xmlns="http://schemas.microsoft.com/sharepoint/soap/" /&gt;
  &lt;/soap:Body&gt;
&lt;/soap:Envelope&gt;</pre>

              <pre>HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8
Content-Length: <font class=value>length</font>

&lt;?xml version="1.0" encoding="utf-8"?&gt;
&lt;soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"&gt;
  &lt;soap:Body&gt;
    &lt;ModeResponse xmlns="http://schemas.microsoft.com/sharepoint/soap/"&gt;
      &lt;ModeResult&gt;<font class=value>None</font> or <font class=value>Windows</font> or <font class=value>Forms</font>&lt;/ModeResult&gt;
    &lt;/ModeResponse&gt;
  &lt;/soap:Body&gt;
&lt;/soap:Envelope&gt;</pre>
          </span>

          <span>
              <h3>SOAP 1.2</h3>
              <p>The following is a sample SOAP 1.2 request and response.  The <font class=value>placeholders</font> shown need to be replaced with .... Truncated ....
```

References: 
- https://www.exploit-db.com/ghdb/6604

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://10.129.1.183/_vti_bin/Authentication.asmx?op=Mode'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)