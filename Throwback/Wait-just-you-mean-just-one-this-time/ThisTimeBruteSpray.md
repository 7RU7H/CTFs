


```bash
root@kali:~# nmap -sV -sC -oA throwback -p- 10.200.0-255.0/24 --min-rate 5000
Starting Nmap 7.80 ( https://nmap.org ) at 2022-04-06 10:46 UTC
Nmap scan report for ip-10-200-102-138.eu-west-1.compute.internal (10.200.102.138)
Host is up (0.0015s latency).
Not shown: 65531 filtered ports
PORT    STATE SERVICE  VERSION
22/tcp  open  ssh      OpenSSH 7.5 (protocol 2.0)
| ssh-hostkey: 
|_  4096 38:04:a0:a1:d0:e6:ab:d9:7d:c0:da:f3:66:bf:77:15 (RSA)
53/tcp  open  domain   (generic dns response: REFUSED)
80/tcp  open  http     nginx
|_http-title: Did not follow redirect to https://ip-10-200-102-138.eu-west-1.compute.internal/
|_https-redirect: ERROR: Script execution failed (use -d to debug)
443/tcp open  ssl/http nginx
|_http-title: pfSense - Login
| ssl-cert: Subject: commonName=pfSense-5f099cf870c18/organizationName=pfSense webConfigurator Self-Signed Certificate
| Subject Alternative Name: DNS:pfSense-5f099cf870c18
| Not valid before: 2020-07-11T11:05:28
|_Not valid after:  2021-08-13T11:05:28
1 service unrecognized despite returning data. If you know the service/version, please submit the following fingerprint at https://nmap.org/cgi-bin/submit.cgi?new-service :
SF-Port53-TCP:V=7.80%I=7%D=4/6%Time=624D701E%P=x86_64-pc-linux-gnu%r(DNSVe
SF:rsionBindReqTCP,E,"\0\x0c\0\x06\x81\x05\0\0\0\0\0\0\0\0")%r(DNSStatusRe
SF:questTCP,E,"\0\x0c\0\0\x90\x05\0\0\0\0\0\0\0\0");

Nmap scan report for ip-10-200-102-219.eu-west-1.compute.internal (10.200.102.219)
Host is up (0.0014s latency).
Not shown: 65524 filtered ports
PORT      STATE SERVICE       VERSION
22/tcp    open  ssh           OpenSSH for_Windows_7.7 (protocol 2.0)
| ssh-hostkey: 
|   2048 85:b8:1f:80:46:3d:91:0f:8c:f2:f2:3f:5c:87:67:72 (RSA)
|   256 5c:0d:46:e9:42:d4:4d:a0:36:d6:19:e5:f3:ce:49:06 (ECDSA)
|_  256 e2:2a:cb:39:85:0f:73:06:a9:23:9d:bf:be:f7:50:0c (ED25519)
80/tcp    open  http          Microsoft IIS httpd 10.0
| http-methods: 
|_  Potentially risky methods: TRACE
|_http-server-header: Microsoft-IIS/10.0
|_http-title: Throwback Hacks
135/tcp   open  msrpc         Microsoft Windows RPC
139/tcp   open  netbios-ssn   Microsoft Windows netbios-ssn
445/tcp   open  microsoft-ds?
3389/tcp  open  ms-wbt-server Microsoft Terminal Services
| rdp-ntlm-info: 
|   Target_Name: THROWBACK
|   NetBIOS_Domain_Name: THROWBACK
|   NetBIOS_Computer_Name: THROWBACK-PROD
|   DNS_Domain_Name: THROWBACK.local
|   DNS_Computer_Name: THROWBACK-PROD.THROWBACK.local
|   DNS_Tree_Name: THROWBACK.local
|   Product_Version: 10.0.17763
|_  System_Time: 2022-04-06T10:49:45+00:00
| ssl-cert: Subject: commonName=THROWBACK-PROD.THROWBACK.local
| Not valid before: 2022-03-29T21:47:47
|_Not valid after:  2022-09-28T21:47:47
|_ssl-date: 2022-04-06T10:51:18+00:00; -1s from scanner time.
5357/tcp  open  http          Microsoft HTTPAPI httpd 2.0 (SSDP/UPnP)
|_http-server-header: Microsoft-HTTPAPI/2.0
|_http-title: Service Unavailable
5985/tcp  open  http          Microsoft HTTPAPI httpd 2.0 (SSDP/UPnP)
|_http-server-header: Microsoft-HTTPAPI/2.0
|_http-title: Not Found
49668/tcp open  msrpc         Microsoft Windows RPC
49669/tcp open  msrpc         Microsoft Windows RPC
49673/tcp open  msrpc         Microsoft Windows RPC
Service Info: OS: Windows; CPE: cpe:/o:microsoft:windows

Host script results:
| smb2-security-mode: 
|   2.02: 
|_    Message signing enabled but not required
| smb2-time: 
|   date: 2022-04-06T10:49:48
|_  start_date: N/A

Nmap scan report for ip-10-200-102-232.eu-west-1.compute.internal (10.200.102.232)
Host is up (0.0016s latency).
Not shown: 65531 closed ports
PORT    STATE SERVICE  VERSION
22/tcp  open  ssh      OpenSSH 7.6p1 Ubuntu 4ubuntu0.3 (Ubuntu Linux; protocol 2.0)
| ssh-hostkey: 
|   2048 ee:88:ef:46:13:83:da:5e:c5:f4:f1:6a:f0:f4:54:9c (RSA)
|   256 67:1a:ba:0d:f3:81:38:53:61:9f:31:15:e7:b9:e1:30 (ECDSA)
|_  256 6c:43:8f:d3:0a:69:cb:68:4e:19:39:09:6f:cb:14:b2 (ED25519)
80/tcp  open  http     Apache httpd 2.4.29 ((Ubuntu))
|_http-server-header: Apache/2.4.29 (Ubuntu)
| http-title: Throwback Hacks - Login
|_Requested resource was src/login.php
143/tcp open  imap     Dovecot imapd (Ubuntu)
|_imap-capabilities: more IDLE LOGINDISABLEDA0001 IMAP4rev1 LITERAL+ SASL-IR Pre-login OK ENABLE listed ID post-login STARTTLS have capabilities LOGIN-REFERRALS
| ssl-cert: Subject: commonName=ip-10-40-119-232.eu-west-1.compute.internal
| Subject Alternative Name: DNS:ip-10-40-119-232.eu-west-1.compute.internal
| Not valid before: 2020-07-25T15:51:57
|_Not valid after:  2030-07-23T15:51:57
|_ssl-date: TLS randomness does not represent time
993/tcp open  ssl/imap Dovecot imapd (Ubuntu)
|_imap-capabilities: IDLE more IMAP4rev1 LITERAL+ SASL-IR listed OK ENABLE AUTH=PLAINA0001 ID post-login LOGIN-REFERRALS have capabilities Pre-login
| ssl-cert: Subject: commonName=ip-10-40-119-232.eu-west-1.compute.internal
| Subject Alternative Name: DNS:ip-10-40-119-232.eu-west-1.compute.internal
| Not valid before: 2020-07-25T15:51:57
|_Not valid after:  2030-07-23T15:51:57
|_ssl-date: TLS randomness does not represent time
Service Info: OS: Linux; CPE: cpe:/o:linux:linux_kernel

Nmap scan report for ip-10-200-102-250.eu-west-1.compute.internal (10.200.102.250)
Host is up (0.0012s latency).
Not shown: 65533 closed ports
PORT     STATE SERVICE VERSION
22/tcp   open  ssh     OpenSSH 7.6p1 Ubuntu 4ubuntu0.5 (Ubuntu Linux; protocol 2.0)
| ssh-hostkey: 
|   2048 60:e9:1f:7f:32:f6:d7:23:32:d8:0c:d9:89:17:99:83 (RSA)
|   256 fd:5c:26:38:05:3a:d8:5d:3b:34:03:1f:95:e9:de:eb (ECDSA)
|_  256 06:e8:d9:b6:2f:9d:6a:6e:49:4c:cf:55:12:3c:3b:d3 (ED25519)
1337/tcp open  http    Node.js Express framework
|_http-title: Error
Service Info: OS: Linux; CPE: cpe:/o:linux:linux_kernel

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 65536 IP addresses (4 hosts up) scanned in 411.92 seconds
```
## Running Brutespray

`brutespray --file throwback.gnmap --userlist usernames.txt`