# Entering the Breach
We are tasked with running initial reconnaissance on Throwback Hacks Security. There are three publicly facing machines:
1. THROWBACK-PROD 
1. THROWBACK-FW01 
1. THROWBACK-MAIL

Removing the -v for verbose output, I scanned the network using:
```
nmap -sV -sC -p- 10.200.x.0/24 --min-rate 5000
```
A breakdown of the results:

```
nmap -sV -sC -p- 10.200.102.0/24 --min-rate 5000                                                 
Starting Nmap 7.92 ( https://nmap.org ) at 2022-03-29 19:39 BST
Nmap scan report for 10.200.102.138
Host is up (0.11s latency).
Not shown: 65531 filtered tcp ports (no-response)
PORT    STATE SERVICE  VERSION
22/tcp  open  ssh      OpenSSH 7.5 (protocol 2.0)
| ssh-hostkey: 
|_  4096 38:04:a0:a1:d0:e6:ab:d9:7d:c0:da:f3:66:bf:77:15 (RSA)
53/tcp  open  domain   (generic dns response: REFUSED)
80/tcp  open  http     nginx
|_http-title: Did not follow redirect to https://10.200.102.138/
443/tcp open  ssl/http nginx
| ssl-cert: Subject: commonName=pfSense-5f099cf870c18/organizationName=pfSense webConfigurator Self-Signed Certificate
| Subject Alternative Name: DNS:pfSense-5f099cf870c18
| Not valid before: 2020-07-11T11:05:28
|_Not valid after:  2021-08-13T11:05:28
|_http-title: pfSense - Login
1 service unrecognized despite returning data. If you know the service/version, please submit the following fingerprint at https://nmap.org/cgi-bin/submit.cgi?new-service :
SF-Port53-TCP:V=7.92%I=7%D=3/29%Time=6243532F%P=x86_64-pc-linux-gnu%r(DNSV
SF:ersionBindReqTCP,E,"\0\x0c\0\x06\x81\x05\0\0\0\0\0\0\0\0");
```

THROWBACK-PROD:  
```
Nmap scan report for 10.200.102.219
Host is up (0.091s latency).
Not shown: 65529 filtered tcp ports (no-response)
PORT     STATE SERVICE       VERSION
22/tcp   open  ssh           OpenSSH for_Windows_7.7 (protocol 2.0)
| ssh-hostkey: 
|   2048 85:b8:1f:80:46:3d:91:0f:8c:f2:f2:3f:5c:87:67:72 (RSA)
|   256 5c:0d:46:e9:42:d4:4d:a0:36:d6:19:e5:f3:ce:49:06 (ECDSA)
|_  256 e2:2a:cb:39:85:0f:73:06:a9:23:9d:bf:be:f7:50:0c (ED25519)
80/tcp   open  http          Microsoft IIS httpd 10.0
|_http-title: Throwback Hacks
| http-methods: 
|_  Potentially risky methods: TRACE
|_http-server-header: Microsoft-IIS/10.0
135/tcp  open  msrpc         Microsoft Windows RPC
139/tcp  open  netbios-ssn   Microsoft Windows netbios-ssn
445/tcp  open  microsoft-ds?
3389/tcp open  ms-wbt-server Microsoft Terminal Services
|_ssl-date: 2022-03-29T18:46:23+00:00; +1m51s from scanner time.
| ssl-cert: Subject: commonName=THROWBACK-PROD.THROWBACK.local
| Not valid before: 2022-03-25T18:59:56
|_Not valid after:  2022-09-24T18:59:56
| rdp-ntlm-info: 
|   Target_Name: THROWBACK
|   NetBIOS_Domain_Name: THROWBACK
|   NetBIOS_Computer_Name: THROWBACK-PROD
|   DNS_Domain_Name: THROWBACK.local
|   DNS_Computer_Name: THROWBACK-PROD.THROWBACK.local
|   DNS_Tree_Name: THROWBACK.local
|   Product_Version: 10.0.17763
|_  System_Time: 2022-03-29T18:44:53+00:00
Service Info: OS: Windows; CPE: cpe:/o:microsoft:windows

Host script results:
| smb2-security-mode: 
|   3.1.1: 
|_    Message signing enabled but not required
| smb2-time: 
|   date: 2022-03-29T18:44:55
|_  start_date: N/A
|_clock-skew: mean: 1m50s, deviation: 0s, median: 1m50s

```

THROWBACK-MAIL:
```
Nmap scan report for 10.200.102.232
Host is up (0.099s latency).
Not shown: 62538 filtered tcp ports (no-response), 2993 closed tcp ports (conn-refused)
PORT    STATE SERVICE  VERSION
22/tcp  open  ssh      OpenSSH 7.6p1 Ubuntu 4ubuntu0.3 (Ubuntu Linux; protocol 2.0)
| ssh-hostkey: 
|   2048 27:39:d1:4b:8d:35:fa:20:10:6a:fa:af:32:79:66:92 (RSA)
|   256 f2:58:92:0b:b2:52:95:5a:c1:2d:46:c9:33:0f:cc:50 (ECDSA)
|_  256 f2:fa:80:6a:77:ce:21:b5:b8:8b:53:0a:ae:bd:70:62 (ED25519)
80/tcp  open  http     Apache httpd 2.4.29 ((Ubuntu))
|_http-server-header: Apache/2.4.29 (Ubuntu)
| http-title: Throwback Hacks - Login
|_Requested resource was src/login.php
143/tcp open  imap     Dovecot imapd (Ubuntu)
|_ssl-date: TLS randomness does not represent time
| ssl-cert: Subject: commonName=ip-10-40-119-232.eu-west-1.compute.internal
| Subject Alternative Name: DNS:ip-10-40-119-232.eu-west-1.compute.internal
| Not valid before: 2020-07-25T15:51:57
|_Not valid after:  2030-07-23T15:51:57
|_imap-capabilities: ID capabilities ENABLE Pre-login LITERAL+ post-login STARTTLS OK have listed IMAP4rev1 more LOGIN-REFERRALS LOGINDISABLEDA0001 IDLE SASL-IR
993/tcp open  ssl/imap Dovecot imapd (Ubuntu)
| ssl-cert: Subject: commonName=ip-10-40-119-232.eu-west-1.compute.internal
| Subject Alternative Name: DNS:ip-10-40-119-232.eu-west-1.compute.internal
| Not valid before: 2020-07-25T15:51:57
|_Not valid after:  2030-07-23T15:51:57
|_ssl-date: TLS randomness does not represent time
|_imap-capabilities: ID ENABLE Pre-login LITERAL+ post-login capabilities OK have listed IMAP4rev1 more LOGIN-REFERRALS AUTH=PLAINA0001 IDLE SASL-IR
Service Info: OS: Linux; CPE: cpe:/o:linux:linux_kernel
```

```
Nmap scan report for 10.200.102.250
Host is up (0.099s latency).
Not shown: 62545 filtered tcp ports (no-response), 2989 closed tcp ports (conn-refused)
PORT   STATE SERVICE VERSION
22/tcp open  ssh     OpenSSH 7.6p1 Ubuntu 4ubuntu0.5 (Ubuntu Linux; protocol 2.0)
| ssh-hostkey: 
|   2048 60:e9:1f:7f:32:f6:d7:23:32:d8:0c:d9:89:17:99:83 (RSA)
|   256 fd:5c:26:38:05:3a:d8:5d:3b:34:03:1f:95:e9:de:eb (ECDSA)
|_  256 06:e8:d9:b6:2f:9d:6a:6e:49:4c:cf:55:12:3c:3b:d3 (ED25519)
Service Info: OS: Linux; CPE: cpe:/o:linux:linux_kernel

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 256 IP addresses (4 hosts up) scanned in 318.59 seconds
```


## Answers
What is the domain name?
```{toggle}
THROWBACK.local
```
What is the HTTP title of the web server running on THROWBACK-PROD?
```{toggle}
Throwback Hacks
```
How many ports are open on THROWBACK-MAIL?
```{toggle}
4
```
What service is running on THROWBACK-FW01?
```{toggle}
pfSense
```
What version of Apache is running on THROWBACK-MAIL?
```{toggle}
Apache/2.4.29
```

I felt that with the prelimary task out the way and and direction to go boldly in I began testing new tools,, my methodology and myself for the long haul in the Exploring the Caverns completely blackboxing it. The next section will have multiple parts given trail and error combined with trying to make the follow discourse narratological coherent. 