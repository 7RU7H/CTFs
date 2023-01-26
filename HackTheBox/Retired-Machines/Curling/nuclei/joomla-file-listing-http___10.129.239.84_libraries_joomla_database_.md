### Joomla! Database File List (joomla-file-listing) found on http://10.129.239.84
---
**Details**: **joomla-file-listing**  matched at http://10.129.239.84

**Protocol**: HTTP

**Full URL**: http://10.129.239.84/libraries/joomla/database/

**Timestamp**: Wed Jan 25 14:18:39 +0000 GMT 2023

**Template Information**

| Key | Value |
|---|---|
| Name | Joomla! Database File List |
| Authors | iampritam |
| Tags | exposure, joomla, listing, database, edb |
| Severity | medium |
| Description | A Joomla! database directory /libraries/joomla/database/ was found exposed and has directory indexing enabled. |
| Remediation | Disable directory indexing on the /libraries/joomla/database/ directory or remove the content from the web root. If the databases can be download, rotate any credentials contained in the databases. |
| CVSS-Metrics | [CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:N/A:N](https://www.first.org/cvss/calculator/3.0#CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:N/A:N) |
| CWE-ID | [CWE-548](https://cwe.mitre.org/data/definitions/548.html) |
| CVSS-Score | 5.30 |

**Request**
```http
GET /libraries/joomla/database/ HTTP/1.1
Host: 10.129.239.84
User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Type: text/html;charset=UTF-8
Date: Wed, 25 Jan 2023 14:18:37 GMT
Server: Apache/2.4.29 (Ubuntu)
Vary: Accept-Encoding

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
 <head>
  <title>Index of /libraries/joomla/database</title>
 </head>
 <body>
<h1>Index of /libraries/joomla/database</h1>
  <table>
   <tr><th valign="top"><img src="/icons/blank.gif" alt="[ICO]"></th><th><a href="?C=N;O=D">Name</a></th><th><a href="?C=M;O=A">Last modified</a></th><th><a href="?C=S;O=A">Size</a></th><th><a href="?C=D;O=A">Description</a></th></tr>
   <tr><th colspan="5"><hr></th></tr>
<tr><td valign="top"><img src="/icons/back.gif" alt="[PARENTDIR]"></td><td><a href="/libraries/joomla/">Parent Directory</a></td><td>&nbsp;</td><td align="right">  - </td><td>&nbsp;</td></tr>
<tr><td valign="top"><img src="/icons/unknown.gif" alt="[   ]"></td><td><a href="database.php">database.php</a></td><td align="right">2018-05-22 12:39  </td><td align="right">5.2K</td><td>&nbsp;</td></tr>
<tr><td valign="top"><img src="/icons/unknown.gif" alt="[   ]"></td><td><a href="driver.php">driver.php</a></td><td align="right">2018-05-22 12:39  </td><td align="right"> 55K</td><td>&nbsp;</td></tr>
<tr><td valign="top"><img src="/icons/folder.gif" alt="[DIR]"></td><td><a href="driver/">driver/</a></td><td align="right">2022-08-02 14:44  </td><td align="right">  - </td><td>&nbsp;</td></tr>
<tr><td valign="top"><img src="/icons/folder.gif" alt="[DIR]"></td><td><a href="exception/">exception/</a></td><td align="right">2022-08-02 14:44  </td><td align="right">  - </td><td>&nbsp;</td></tr>
<tr><td valign="top"><img src="/icons/unknown.gif" alt="[   ]"></td><td><a href="exporter.php">exporter.php</a></td><td align="right">2018-05-22 12:39  </td><td align="right">4.4K</td><td>&nbsp;</td></tr>
<tr><td valign="top"><img src="/icons/folder.gif" alt="[DIR]"></td><td><a href="exporter/">exporter/</a></td><td align="right">2022-08-02 14:44  </td><td align="right">  - </td><td>&nbsp;</td></tr>
<tr><td valign="top"><img src="/icons/unknown.gif" alt="[   ]"></td><td><a href="factory.php">factory.php</a></td><td align="right">2018-05-22 12:39  </td><td align="right">5.3K</td><td>&nbsp;</td></tr>
<tr><td valign="top"><img src="/icons/unknown.gif" alt="[   ]"></td><td><a href="importer.php">importer.php</a></td><td align="right">2018-05-22 12:39  </td><td align="right">4.8K</td><td>&nbsp;</td></tr>
<tr><td valign="top"><img src="/icons/folder.gif" alt="[DIR]"></td><td><a href="importer/">importer/</a></td><td align="right">2022-08-02 14:44  </td><td align="right">  - </td><td>&nbsp;</td></tr>
<tr><td valign="top"><img src="/icons/unknown.gif" alt="[   ]"></td><td><a href="interface.php">interface.php</a></td><td align="right">2018-05-22 12:39  </td><td align="right">549 </td><td>&nbsp;</td></tr>
<tr><td valign="top"><img src="/icons/unknown.gif" alt="[   ]"></td><td><a href="iterator.php">iterator.php</a></td><td align="right">2018-05-22 12:39  </td><td align="right">3.6K</td><td>&nbsp;</td></tr>
<tr><td valign="top"><img src="/icons/folder.gif" alt="[DIR]"></td><td><a href="iterator/">iterator/</a></td><td align="right">2022-08-02 14:44  </td><td align="right">  - </td><td>&nbsp;</td></tr>
<tr><td valign="top"><img src="/icons/unknown.gif" alt="[   ]"></td><td><a href="query.php">query.php</a></td><td align="right">2018-05-22 12:39  </td><td align="right"> 44K</td><td>&nbsp;</td></tr>
<tr><td valign="top"><img src="/icons/folder.gif" alt="[DIR]"></td><td><a href="query/">query/</a></td><td align="right">2022-08-02 14:44  </td><td align="right">  - </td><td>&nbsp;</td></tr>
   <tr><th colspan="5"><hr></th></tr>
</table>
<address>Apache/2.4.29 (Ubuntu) Server at 10.129.239.84 Port 80</address>
</body></html>

```

References: 
- https://www.exploit-db.com/ghdb/6377

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36' 'http://10.129.239.84/libraries/joomla/database/'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)