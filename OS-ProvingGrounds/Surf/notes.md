
```bash
ping -c 3 192.168.195.171 
PING 192.168.195.171 (192.168.195.171) 56(84) bytes of data.
64 bytes from 192.168.195.171: icmp_seq=1 ttl=63 time=43.0 ms
64 bytes from 192.168.195.171: icmp_seq=2 ttl=63 time=43.3 ms
64 bytes from 192.168.195.171: icmp_seq=3 ttl=63 time=47.6 ms

--- 192.168.195.171 ping statistics ---
3 packets transmitted, 3 received, 0% packet loss, time 2002ms
rtt min/avg/max/mdev = 43.049/44.621/47.560/2.079 ms

nmap -sC -sV -p- 192.168.195.171 --min-rate 5000           
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-07 20:44 BST
Warning: 192.168.195.171 giving up on port because retransmission cap hit (10).
Nmap scan report for 192.168.195.171
Host is up (0.11s latency).
Not shown: 64656 closed tcp ports (reset), 877 filtered tcp ports (no-response)
PORT   STATE SERVICE VERSION
22/tcp open  ssh     OpenSSH 7.9p1 Debian 10+deb10u2 (protocol 2.0)
| ssh-hostkey: 
|   2048 74:ba:20:23:89:92:62:02:9f:e7:3d:3b:83:d4:d9:6c (RSA)
|   256 54:8f:79:55:5a:b0:3a:69:5a:d5:72:39:64:fd:07:4e (ECDSA)
|_  256 7f:5d:10:27:62:ba:75:e9:bc:c8:4f:e2:72:87:d4:e2 (ED25519)
80/tcp open  http    Apache httpd 2.4.38 ((Debian))
|_http-title: Surfing blog
|_http-server-header: Apache/2.4.38 (Debian)
Service Info: OS: Linux; CPE: cpe:/o:linux:linux_kernel

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 64.12 seconds


 nikto -h 192.168.195.171 -C all                 
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          192.168.195.171
+ Target Hostname:    192.168.195.171
+ Target Port:        80
+ Start Time:         2022-05-07 20:47:34 (GMT1)
---------------------------------------------------------------------------
+ Server: Apache/2.4.38 (Debian)
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ Server may leak inodes via ETags, header found with file /, inode: 195f, size: 5c49022754a00, mtime: gzip
+ Allowed HTTP Methods: GET, POST, OPTIONS, HEAD 
+ Cookie PHPSESSID created without the httponly flag
+ OSVDB-3268: /css/: Directory indexing found.
+ OSVDB-3092: /css/: This might be interesting...
+ OSVDB-3233: /icons/README: Apache default file found.
+ 26525 requests: 0 error(s) and 9 item(s) reported on remote host
+ End Time:           2022-05-07 21:13:09 (GMT1) (1535 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested

gobuster dir -u http://192.168.195.171/ -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt -x php

```
