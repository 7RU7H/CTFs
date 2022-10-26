### WAF Detection (waf-detect:ats) found on http://10.129.65.36
---
**Details**: **waf-detect:ats**  matched at http://10.129.65.36

**Protocol**: HTTP

**Full URL**: http://10.129.65.36/

**Timestamp**: Wed Oct 26 14:40:15 +0100 BST 2022

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
Host: 10.129.65.36
User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Content-Length: 27
Content-Type: application/x-www-form-urlencoded
Accept-Encoding: gzip

_=<script>alert(1)</script>
```

**Response**
```http
HTTP/1.1 200 OK
Accept-Ranges: bytes
Cache-Control: no-cache, no-store, must-revalidate, max-age=-1
Content-Type: text/html
Server: HFS 2.3
Set-Cookie: HFS_SID=0.13622642471455; path=/;

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title>HFS /</title>
	<link rel="stylesheet" href="/?mode=section&id=style.css" type="text/css">
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.js"></script>
    <script> if (typeof jQuery == "undefined") document.write('<script type="text/javascript" src="/?mode=jquery"></'+'script>'); </script>
	<link rel="shortcut icon" href="/favicon.ico">
	<style class='trash-me'>
	.onlyscript, button[onclick] { display:none; }
	</style>
    <script>
    // this object will store some %symbols% in the javascript space, so that libs can read them
    HFS = { folder:'/', number:0, paged:1 }; 
    </script>
	<script type="text/javascript" src="/?mode=section&id=lib.js"></script>
</head>
<body>
<!-- -->
<div id='panel'>
    	<fieldset id='msgs'>
		<legend><img src="/~img10"> Messages</legend>
		<ul style='padding-left:2em'>
		</ul>
	</fieldset>

    	<fieldset id='login'>
		<legend><img src="/~img27"> User</legend>
		<center>
		<a href="~login">Login</a>
		</center>
	</fieldset>                                       

    	<fieldset id='folder'>
		<legend><img src="/~img8"> Folder</legend>

       <div style='float:right; position:relative; top:-1em; font-weight:bold;'>
		
		</div>

		<div id='breadcrumbs'>
		
		<a href="/"  /> <img src="/~img1"> Home</a>
       </div>
        
		<div id='folder-stats'>0 folders, 0 files, 0 bytes
		</div>
		
		
	</fieldset>

    	<fieldset id='search'>
		<legend><img src="/~img3"> Search</legend>
		<form style='text-align:center'>
			<input name='search' size='15' value="">
			<input type='submit' value="go">
		</form>
		<div style='margin-top:0.5em;' class='hidden popup'>
			<fieldset>
				<legend>Where to search</legend>
					<input type='radio' name='where' value='fromhere' checked='true' />  this folder and sub-folders
					<br><input type='radio' name='where' value='here' />  this folder only
					<br><input type='radio' name='where' value='anywhere' />  entire server
			</fieldset>
		</div>
	</fieldset>

    	<fieldset id='select' class='onlyscript'>
		<legend><img src="/~img15"> Select</legend>
		<center>
    	<button onclick="
            var x = $('#files .selector');
            if (x.size() > x.filter(':checked').size())
                x.attr('checked', true).closest('tr').addClass('selected');
			else
                x.attr('checked', false).closest('tr').removeClass('selected');
			selectedChanged();
			">All</button>
    	<button onclick="
            $('#files .selector').attr('checked', function(i,v){ return !v }).closest('tr').toggleClass('selected');
			selectedChanged();
            ">Invert</button>
    	<button onclick='selectionMask.call(this)'>Mask</button>
		<p style='display:none; margin-top:1em;'><span id='selected-number'>0</span> items selected</p>
		</center>
	</fieldset>

    	

    	<fieldset id='actions'>
		<legend><img src="/~img18"> Actions</legend>
		<center>
		
		
		
		
		<button id='archiveBtn' onclick='if (confirm("Are you sure?")) submit({}, "/?mode=archive&recursive")'>Archive</button>
		<a href="/?tpl=list&folders-filter=\&recursive">Get list</a>
		</center>
	</fieldset>

    	<fieldset id='serverinfo'>
		<legend><img src="/~img0"> Server information</legend>
		<a href="http://www.rejetto.com/hfs/">HttpFileServer 2.3</a>
		<br />Server time: 2/11/2022 12:38:19 πμ
		<br />Server uptime: 00:00:52
	</fieldset>


</div>

<div id='files_outer'>
	<div style='height:1.6em;'></div>  
	 <div style='font-size:200%; padding:1em;'>No files in this folder</div> 
</div>

</body>
</html>
<!-- Build-time: 0.000 -->

```

References: 
- https://github.com/ekultek/whatwaf

**CURL Command**
```
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 10.129.65.36' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://10.129.65.36/'
```
---
Generated by [Nuclei 2.7.8](https://github.com/projectdiscovery/nuclei)