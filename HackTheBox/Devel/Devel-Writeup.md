
Name: Devel
Date:  
Difficulty:  
Description:  
Better Description:  
Goals:  
Learnt:

## Recon

![ping](Screenshots/ping.png)
	
## Exploit

From [[nmap/Discovery.nmap]] scan IIS 7.5 server disclosured, searchsploit-ed it; link for [exploitdb](www.exploit-db.com/exploits/19033)

The image links to a microsoft page so that probably indicates that there is no `/images` directory.

The [[feroxbuster/feroxbuster-common]] reveals `aspnet_client` 

![aspnet-regular](Screenshots/aspnetclient-regular.png)

And with `:$i30:$INDEX_ALLOCATION` appended to the directory request gets:

```html
HTTP/1.1 404 Not Found
Content-Type: text/html
Server: Microsoft-IIS/7.5
X-Powered-By: ASP.NET
Date: Tue, 21 Jun 2022 21:36:07 GMT
Connection: close
Content-Length: 1245

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<title>404 - File or directory not found.</title>
<style type="text/css">
<!--
body{margin:0;font-size:.7em;font-family:Verdana, Arial, Helvetica, sans-serif;background:#EEEEEE;}
fieldset{padding:0 15px 10px 15px;} 
h1{font-size:2.4em;margin:0;color:#FFF;}
h2{font-size:1.7em;margin:0;color:#CC0000;} 
h3{font-size:1.2em;margin:10px 0 0 0;color:#000000;} 
#header{width:96%;margin:0 0 0 0;padding:6px 2% 6px 2%;font-family:"trebuchet MS", Verdana, sans-serif;color:#FFF;
background-color:#555555;}
#content{margin:0 0 0 2%;position:relative;}
.content-container{background:#FFF;width:96%;margin-top:8px;padding:10px;position:relative;}
-->
</style>
</head>
<body>
<div id="header"><h1>Server Error</h1></div>
<div id="content">
 <div class="content-container"><fieldset>
  <h2>404 - File or directory not found.</h2>
  <h3>The resource you are looking for might have been removed, had its name changed, or is temporarily unavailable.</h3>
 </fieldset></div>
</div>
</body>
</html>


```
[payloadallthethings](https://github.com/swisskyrepo/PayloadsAllTheThings/blob/ba2c02cc3ef3f63df6351aa55509bdac137fb3b8/Upload%20Insecure%20Files/Extension%20ASP/shell.aspx)


## Foothold

## PrivEsc

    
