
## Recon
```bash
ping -c 3 192.168.80.140
PING 192.168.80.140 (192.168.80.140) 56(84) bytes of data.
64 bytes from 192.168.80.140: icmp_seq=1 ttl=127 time=53.9 ms
64 bytes from 192.168.80.140: icmp_seq=2 ttl=127 time=54.7 ms
64 bytes from 192.168.80.140: icmp_seq=3 ttl=127 time=50.5 ms

--- 192.168.80.140 ping statistics ---
3 packets transmitted, 3 received, 0% packet loss, time 2004ms
rtt min/avg/max/mdev = 50.508/53.008/54.657/1.797 ms

nmap -sC -sV -p- 192.168.80.140 --min-rate 5000 
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-09 12:45 BST
Warning: 192.168.80.140 giving up on port because retransmission cap hit (10).
Nmap scan report for 192.168.80.140
Host is up (0.079s latency).
Not shown: 65198 closed tcp ports (reset), 314 filtered tcp ports (no-response)
PORT      STATE SERVICE        VERSION
25/tcp    open  smtp           Mercury/32 smtpd (Mail server account Maiser)
|_smtp-commands: localhost Hello nmap.scanme.org; ESMTPs are:, TIME
79/tcp    open  finger         Mercury/32 fingerd
| finger: Login: Admin         Name: Mail System Administrator\x0D
| \x0D
|_[No profile information]\x0D
105/tcp   open  ph-addressbook Mercury/32 PH addressbook server
106/tcp   open  pop3pw         Mercury/32 poppass service
110/tcp   open  pop3           Mercury/32 pop3d
|_pop3-capabilities: USER UIDL TOP APOP EXPIRE(NEVER)
135/tcp   open  msrpc          Microsoft Windows RPC
139/tcp   open  netbios-ssn    Microsoft Windows netbios-ssn
143/tcp   open  imap           Mercury/32 imapd 4.62
|_imap-capabilities: CAPABILITY X-MERCURY-1A0001 IMAP4rev1 AUTH=PLAIN OK complete
443/tcp   open  ssl/http       Apache httpd 2.4.46 ((Win64) OpenSSL/1.1.1g PHP/7.3.23)
|_http-server-header: Apache/2.4.46 (Win64) OpenSSL/1.1.1g PHP/7.3.23
| ssl-cert: Subject: commonName=localhost
| Not valid before: 2009-11-10T23:48:47
|_Not valid after:  2019-11-08T23:48:47
|_ssl-date: TLS randomness does not represent time
| tls-alpn: 
|_  http/1.1
|_http-title: Time Travel Company Page
| http-methods: 
|_  Potentially risky methods: TRACE
445/tcp   open  microsoft-ds?
2224/tcp  open  http           Mercury/32 httpd
|_http-title: Mercury HTTP Services
5040/tcp  open  unknown
7680/tcp  open  pando-pub?
8000/tcp  open  http           Apache httpd 2.4.46 ((Win64) OpenSSL/1.1.1g PHP/7.3.23)
|_http-server-header: Apache/2.4.46 (Win64) OpenSSL/1.1.1g PHP/7.3.23
|_http-open-proxy: Proxy might be redirecting requests
| http-methods: 
|_  Potentially risky methods: TRACE
|_http-title: Time Travel Company Page
11100/tcp open  vnc            VNC (protocol 3.8)
| vnc-info: 
|   Protocol version: 3.8
|   Security types: 
|_    Unknown security type (40)
20001/tcp open  ftp            FileZilla ftpd 0.9.41 beta
| ftp-syst: 
|_  SYST: UNIX emulated by FileZilla
|_ftp-bounce: bounce working!
| ftp-anon: Anonymous FTP login allowed (FTP code 230)
| -r--r--r-- 1 ftp ftp            312 Oct 20  2020 .babelrc
| -r--r--r-- 1 ftp ftp            147 Oct 20  2020 .editorconfig
| -r--r--r-- 1 ftp ftp             23 Oct 20  2020 .eslintignore
| -r--r--r-- 1 ftp ftp            779 Oct 20  2020 .eslintrc.js
| -r--r--r-- 1 ftp ftp            167 Oct 20  2020 .gitignore
| -r--r--r-- 1 ftp ftp            228 Oct 20  2020 .postcssrc.js
| -r--r--r-- 1 ftp ftp            346 Oct 20  2020 .tern-project
| drwxr-xr-x 1 ftp ftp              0 Oct 20  2020 build
| drwxr-xr-x 1 ftp ftp              0 Oct 20  2020 config
| -r--r--r-- 1 ftp ftp           1376 Oct 20  2020 index.html
| -r--r--r-- 1 ftp ftp         425010 Oct 20  2020 package-lock.json
| -r--r--r-- 1 ftp ftp           2454 Oct 20  2020 package.json
| -r--r--r-- 1 ftp ftp           1100 Oct 20  2020 README.md
| drwxr-xr-x 1 ftp ftp              0 Oct 20  2020 src
| drwxr-xr-x 1 ftp ftp              0 Oct 20  2020 static
|_-r--r--r-- 1 ftp ftp            127 Oct 20  2020 _redirects
33006/tcp open  unknown
| fingerprint-strings: 
|   GenericLines, Help, LANDesk-RC, NULL, SIPOptions, SMBProgNeg, TLSSessionReq: 
|_    Host '192.168.49.80' is not allowed to connect to this MariaDB server
49664/tcp open  msrpc          Microsoft Windows RPC
49665/tcp open  msrpc          Microsoft Windows RPC
49666/tcp open  msrpc          Microsoft Windows RPC
49667/tcp open  msrpc          Microsoft Windows RPC
49668/tcp open  msrpc          Microsoft Windows RPC
49669/tcp open  msrpc          Microsoft Windows RPC
1 service unrecognized despite returning data. If you know the service/version, please submit the following fingerprint at https://nmap.org/cgi-bin/submit.cgi?new-service :
SF-Port33006-TCP:V=7.92%I=7%D=5/9%Time=6278FF08%P=x86_64-pc-linux-gnu%r(NU
SF:LL,4C,"H\0\0\x01\xffj\x04Host\x20'192\.168\.49\.80'\x20is\x20not\x20all
SF:owed\x20to\x20connect\x20to\x20this\x20MariaDB\x20server")%r(GenericLin
SF:es,4C,"H\0\0\x01\xffj\x04Host\x20'192\.168\.49\.80'\x20is\x20not\x20all
SF:owed\x20to\x20connect\x20to\x20this\x20MariaDB\x20server")%r(Help,4C,"H
SF:\0\0\x01\xffj\x04Host\x20'192\.168\.49\.80'\x20is\x20not\x20allowed\x20
SF:to\x20connect\x20to\x20this\x20MariaDB\x20server")%r(TLSSessionReq,4C,"
SF:H\0\0\x01\xffj\x04Host\x20'192\.168\.49\.80'\x20is\x20not\x20allowed\x2
SF:0to\x20connect\x20to\x20this\x20MariaDB\x20server")%r(SMBProgNeg,4C,"H\
SF:0\0\x01\xffj\x04Host\x20'192\.168\.49\.80'\x20is\x20not\x20allowed\x20t
SF:o\x20connect\x20to\x20this\x20MariaDB\x20server")%r(SIPOptions,4C,"H\0\
SF:0\x01\xffj\x04Host\x20'192\.168\.49\.80'\x20is\x20not\x20allowed\x20to\
SF:x20connect\x20to\x20this\x20MariaDB\x20server")%r(LANDesk-RC,4C,"H\0\0\
SF:x01\xffj\x04Host\x20'192\.168\.49\.80'\x20is\x20not\x20allowed\x20to\x2
SF:0connect\x20to\x20this\x20MariaDB\x20server");
Service Info: Host: localhost; OS: Windows; CPE: cpe:/o:microsoft:windows

Host script results:
|_clock-skew: -1s
| smb2-time: 
|   date: 2022-05-09T11:49:01
|_  start_date: N/A
| smb2-security-mode: 
|   3.1.1: 
|_    Message signing enabled but not required

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 242.71 seconds

nmap --script discovery -p- 192.168.80.140 --min-rate 5000
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-09 12:53 BST
Pre-scan script results:
| targets-asn: 
|_  targets-asn.asn is a mandatory parameter
| targets-ipv6-multicast-mld: 
|   IP: fe80::7888:fff:fe91:692b  MAC: 7a:88:0f:91:69:2b  IFACE: usb0
| 
|_  Use --script-args=newtargets to add the results as targets
| ipv6-multicast-mld-list: 
|   fe80::7888:fff:fe91:692b: 
|     device: usb0
|     mac: 7a:88:0f:91:69:2b
|     multicast_ips: 
|       ff02::1:ff91:692b         (NDP Solicited-node)
|       ff02::1:ff00:0            (Solicited-Node Address)
|       ff05::2                   (unknown)
|       ff02::2                   (All Routers Address)
|_      ff02::1:ff00:37           (Solicited-Node Address)
| targets-ipv6-multicast-invalid-dst: 
|   IP: fe80::7888:fff:fe91:692b  MAC: 7a:88:0f:91:69:2b  IFACE: usb0
|   IP: 2a01:4c8:1483:76b5::37    MAC: 7a:88:0f:91:69:2b  IFACE: usb0
|_  Use --script-args=newtargets to add the results as targets
| targets-ipv6-multicast-echo: 
|   IP: 2a01:4c8:1483:76b5::37    MAC: 7a:88:0f:91:69:2b  IFACE: usb0
|   IP: fe80::7888:fff:fe91:692b  MAC: 7a:88:0f:91:69:2b  IFACE: usb0
|_  Use --script-args=newtargets to add the results as targets
|_http-robtex-shared-ns: *TEMPORARILY DISABLED* due to changes in Robtex's API. See https://www.robtex.com/api/
|_hostmap-robtex: *TEMPORARILY DISABLED* due to changes in Robtex's API. See https://www.robtex.com/api/
Warning: 192.168.80.140 giving up on port because retransmission cap hit (10).
Nmap scan report for 192.168.80.140
Host is up (0.35s latency).
Not shown: 65196 closed tcp ports (reset), 316 filtered tcp ports (no-response)
PORT      STATE SERVICE
25/tcp    open  smtp
|_banner: 220 localhost ESMTP server ready.
|_smtp-commands: localhost Hello nmap.scanme.org; ESMTPs are:, TIME
|_smtp-open-relay: Server is an open relay (2/16 tests)
79/tcp    open  finger
| finger: Login: Admin         Name: Mail System Administrator\x0D
| \x0D
|_[No profile information]\x0D
105/tcp   open  csnet-ns
106/tcp   open  pop3pw
|_banner: 200 localhost MercuryW PopPass server ready.
110/tcp   open  pop3
|_banner: +OK <878968.26932@localhost>, POP3 server ready.
|_pop3-capabilities: UIDL APOP TOP EXPIRE(NEVER) USER
135/tcp   open  msrpc
139/tcp   open  netbios-ssn
|_smb-enum-services: ERROR: Script execution failed (use -d to debug)
143/tcp   open  imap
|_banner: * OK localhost IMAP4rev1 Mercury/32 v4.62 server ready.
|_ssl-cert: ERROR: Script execution failed (use -d to debug)
443/tcp   open  https
|_http-referer-checker: Couldn't find any cross-domain scripts.
|_http-title: Time Travel Company Page
| http-headers: 
|   Date: Mon, 09 May 2022 11:55:26 GMT
|   Server: Apache/2.4.46 (Win64) OpenSSL/1.1.1g PHP/7.3.23
|   Last-Modified: Tue, 20 Oct 2020 20:34:20 GMT
|   ETag: "32e8-5b22027cd0b00"
|   Accept-Ranges: bytes
|   Content-Length: 13032
|   Connection: close
|   Content-Type: text/html
|   
|_  (Request type: HEAD)
| ssl-enum-ciphers: 
|   TLSv1.0: 
|     ciphers: 
|       TLS_ECDHE_RSA_WITH_AES_256_CBC_SHA (ecdh_x25519) - F
|       TLS_DHE_RSA_WITH_AES_256_CBC_SHA (dh 1024) - F
|       TLS_DHE_RSA_WITH_CAMELLIA_256_CBC_SHA (dh 1024) - F
|       TLS_ECDHE_RSA_WITH_AES_128_CBC_SHA (ecdh_x25519) - F
|       TLS_DHE_RSA_WITH_AES_128_CBC_SHA (dh 1024) - F
|       TLS_DHE_RSA_WITH_CAMELLIA_128_CBC_SHA (dh 1024) - F
|       TLS_RSA_WITH_AES_256_CBC_SHA (rsa 1024) - F
|       TLS_RSA_WITH_CAMELLIA_256_CBC_SHA (rsa 1024) - F
|       TLS_RSA_WITH_AES_128_CBC_SHA (rsa 1024) - F
|       TLS_RSA_WITH_CAMELLIA_128_CBC_SHA (rsa 1024) - F
|       TLS_DHE_RSA_WITH_SEED_CBC_SHA (dh 1024) - F
|       TLS_RSA_WITH_SEED_CBC_SHA (rsa 1024) - F
|       TLS_RSA_WITH_IDEA_CBC_SHA (rsa 1024) - F
|     compressors: 
|       NULL
|     cipher preference: server
|     warnings: 
|       64-bit block cipher IDEA vulnerable to SWEET32 attack
|       Insecure certificate signature (SHA1), score capped at F
|   TLSv1.1: 
|     ciphers: 
|       TLS_ECDHE_RSA_WITH_AES_256_CBC_SHA (ecdh_x25519) - F
|       TLS_DHE_RSA_WITH_AES_256_CBC_SHA (dh 1024) - F
|       TLS_DHE_RSA_WITH_CAMELLIA_256_CBC_SHA (dh 1024) - F
|       TLS_ECDHE_RSA_WITH_AES_128_CBC_SHA (ecdh_x25519) - F
|       TLS_DHE_RSA_WITH_AES_128_CBC_SHA (dh 1024) - F
|       TLS_DHE_RSA_WITH_CAMELLIA_128_CBC_SHA (dh 1024) - F
|       TLS_RSA_WITH_AES_256_CBC_SHA (rsa 1024) - F
|       TLS_RSA_WITH_CAMELLIA_256_CBC_SHA (rsa 1024) - F
|       TLS_RSA_WITH_AES_128_CBC_SHA (rsa 1024) - F
|       TLS_RSA_WITH_CAMELLIA_128_CBC_SHA (rsa 1024) - F
|       TLS_DHE_RSA_WITH_SEED_CBC_SHA (dh 1024) - F
|       TLS_RSA_WITH_SEED_CBC_SHA (rsa 1024) - F
|       TLS_RSA_WITH_IDEA_CBC_SHA (rsa 1024) - F
|     compressors: 
|       NULL
|     cipher preference: server
|     warnings: 
|       64-bit block cipher IDEA vulnerable to SWEET32 attack
|       Insecure certificate signature (SHA1), score capped at F
|   TLSv1.2: 
|     ciphers: 
|       TLS_ECDHE_RSA_WITH_AES_256_GCM_SHA384 (ecdh_x25519) - F
|       TLS_DHE_RSA_WITH_AES_256_GCM_SHA384 (dh 1024) - F
|       TLS_ECDHE_RSA_WITH_CHACHA20_POLY1305_SHA256 (ecdh_x25519) - F
|       TLS_DHE_RSA_WITH_CHACHA20_POLY1305_SHA256 (dh 1024) - F
|       TLS_DHE_RSA_WITH_AES_256_CCM_8 (dh 1024) - F
|       TLS_DHE_RSA_WITH_AES_256_CCM (dh 1024) - F
|       TLS_ECDHE_RSA_WITH_ARIA_256_GCM_SHA384 (ecdh_x25519) - F
|       TLS_DHE_RSA_WITH_ARIA_256_GCM_SHA384 (dh 1024) - F
|       TLS_ECDHE_RSA_WITH_AES_128_GCM_SHA256 (ecdh_x25519) - F
|       TLS_DHE_RSA_WITH_AES_128_GCM_SHA256 (dh 1024) - F
|       TLS_DHE_RSA_WITH_AES_128_CCM_8 (dh 1024) - F
|       TLS_DHE_RSA_WITH_AES_128_CCM (dh 1024) - F
|       TLS_ECDHE_RSA_WITH_ARIA_128_GCM_SHA256 (ecdh_x25519) - F
|       TLS_DHE_RSA_WITH_ARIA_128_GCM_SHA256 (dh 1024) - F
|       TLS_ECDHE_RSA_WITH_AES_256_CBC_SHA384 (ecdh_x25519) - F
|       TLS_DHE_RSA_WITH_AES_256_CBC_SHA256 (dh 1024) - F
|       TLS_ECDHE_RSA_WITH_CAMELLIA_256_CBC_SHA384 (ecdh_x25519) - F
|       TLS_DHE_RSA_WITH_CAMELLIA_256_CBC_SHA256 (dh 1024) - F
|       TLS_ECDHE_RSA_WITH_AES_128_CBC_SHA256 (ecdh_x25519) - F
|       TLS_DHE_RSA_WITH_AES_128_CBC_SHA256 (dh 1024) - F
|       TLS_ECDHE_RSA_WITH_CAMELLIA_128_CBC_SHA256 (ecdh_x25519) - F
|       TLS_DHE_RSA_WITH_CAMELLIA_128_CBC_SHA256 (dh 1024) - F
|       TLS_ECDHE_RSA_WITH_AES_256_CBC_SHA (ecdh_x25519) - F
|       TLS_DHE_RSA_WITH_AES_256_CBC_SHA (dh 1024) - F
|       TLS_DHE_RSA_WITH_CAMELLIA_256_CBC_SHA (dh 1024) - F
|       TLS_ECDHE_RSA_WITH_AES_128_CBC_SHA (ecdh_x25519) - F
|       TLS_DHE_RSA_WITH_AES_128_CBC_SHA (dh 1024) - F
|       TLS_DHE_RSA_WITH_CAMELLIA_128_CBC_SHA (dh 1024) - F
|       TLS_RSA_WITH_AES_256_GCM_SHA384 (rsa 1024) - F
|       TLS_RSA_WITH_AES_256_CCM_8 (rsa 1024) - F
|       TLS_RSA_WITH_AES_256_CCM (rsa 1024) - F
|       TLS_RSA_WITH_ARIA_256_GCM_SHA384 (rsa 1024) - F
|       TLS_RSA_WITH_AES_128_GCM_SHA256 (rsa 1024) - F
|       TLS_RSA_WITH_AES_128_CCM_8 (rsa 1024) - F
|       TLS_RSA_WITH_AES_128_CCM (rsa 1024) - F
|       TLS_RSA_WITH_ARIA_128_GCM_SHA256 (rsa 1024) - F
|       TLS_RSA_WITH_AES_256_CBC_SHA256 (rsa 1024) - F
|       TLS_RSA_WITH_CAMELLIA_256_CBC_SHA256 (rsa 1024) - F
|       TLS_RSA_WITH_AES_128_CBC_SHA256 (rsa 1024) - F
|       TLS_RSA_WITH_CAMELLIA_128_CBC_SHA256 (rsa 1024) - F
|       TLS_RSA_WITH_AES_256_CBC_SHA (rsa 1024) - F
|       TLS_RSA_WITH_CAMELLIA_256_CBC_SHA (rsa 1024) - F
|       TLS_RSA_WITH_AES_128_CBC_SHA (rsa 1024) - F
|       TLS_RSA_WITH_CAMELLIA_128_CBC_SHA (rsa 1024) - F
|       TLS_DHE_RSA_WITH_SEED_CBC_SHA (dh 1024) - F
|       TLS_RSA_WITH_SEED_CBC_SHA (rsa 1024) - F
|     compressors: 
|       NULL
|     cipher preference: server
|     warnings: 
|       Insecure certificate signature (SHA1), score capped at F
|   TLSv1.3: 
|     ciphers: 
|       TLS_AKE_WITH_AES_256_GCM_SHA384 (ecdh_x25519) - A
|       TLS_AKE_WITH_CHACHA20_POLY1305_SHA256 (ecdh_x25519) - A
|       TLS_AKE_WITH_AES_128_GCM_SHA256 (ecdh_x25519) - A
|     cipher preference: server
|_  least strength: F
|_http-chrono: Request times for /; avg: 1788.53ms; min: 744.67ms; max: 4114.50ms
| http-grep: 
|   (1) https://192.168.80.140:443/: 
|     (1) email: 
|_      + myemail@something.com
| http-enum: 
|_  /icons/: Potentially interesting folder w/ directory listing
|_http-mobileversion-checker: No mobile version detected.
| http-comments-displayer: 
| Spidering limited to: maxdepth=3; maxpagecount=20; withinhost=192.168.80.140
|     
|     Path: https://192.168.80.140:443/
|     Line number: 316
|     Comment: 
|          // The optional number (900) specifies the number of milliseconds it takes to scroll to the specified area
|     
|     Path: https://192.168.80.140:443/
|     Line number: 113
|     Comment: 
|         <!-- Container (Portfolio Section) - thispersondoesntexist -->
|     
|     Path: https://192.168.80.140:443/bootstrap.min.css
|     Line number: 6
|     Comment: 
|         /*# sourceMappingURL=bootstrap.min.css.map */
|     
|     Path: https://192.168.80.140:443/
|     Line number: 315
|     Comment: 
|          // Using jQuery's animate() method to add smooth page scroll
|     
|     Path: https://192.168.80.140:443/
|     Line number: 187
|     Comment: 
|         <!-- Left and right controls -->
|     
|     Path: https://192.168.80.140:443/bootstrap.min.css
|     Line number: 1
|     Comment: 
|         /*!
|          * Bootstrap v3.4.1 (https://getbootstrap.com/)
|          * Copyright 2011-2019 Twitter, Inc.
|          * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
|          */
|     
|     Path: https://192.168.80.140:443/
|     Line number: 167
|     Comment: 
|         <!-- Indicators -->
|     
|     Path: https://192.168.80.140:443/
|     Line number: 43
|     Comment: 
|         <!-- Container (About Section) -->
|     
|     Path: https://192.168.80.140:443/bootstrap.min.css
|     Line number: 5
|     Comment: 
|         /*! normalize.css v3.0.3 | MIT License | github.com/necolas/normalize.css */
|     
|     Path: https://192.168.80.140:443/
|     Line number: 4
|     Comment: 
|         <!-- Theme Made By www.w3schools.com -->
|     
|     Path: https://192.168.80.140:443/
|     Line number: 324
|     Comment: 
|          // End if
|     
|     Path: https://192.168.80.140:443/
|     Line number: 321
|     Comment: 
|          // Add hash (#) to URL when done scrolling (default click behavior)
|     
|     Path: https://192.168.80.140:443/
|     Line number: 174
|     Comment: 
|         <!-- Wrapper for slides -->
|     
|     Path: https://192.168.80.140:443/
|     Line number: 266
|     Comment: 
|         <!-- Container (Contact Section) -->
|     
|     Path: https://192.168.80.140:443/bootstrap.min.js
|     Line number: 1
|     Comment: 
|         /*!
|          * Bootstrap v3.4.1 (https://getbootstrap.com/)
|          * Copyright 2011-2019 Twitter, Inc.
|          * Licensed under the MIT license
|          */
|     
|     Path: https://192.168.80.140:443/
|     Line number: 307
|     Comment: 
|          // Make sure this.hash has a value before overriding default behavior
|     
|     Path: https://192.168.80.140:443/
|     Line number: 199
|     Comment: 
|         <!-- Container (Pricing Section) -->
|     
|     Path: https://192.168.80.140:443/
|     Line number: 305
|     Comment: 
|          // Add smooth scrolling to all links in navbar + footer link
|     
|     Path: https://192.168.80.140:443/
|     Line number: 71
|     Comment: 
|         <!-- Container (Services Section) -->
|     
|     Path: https://192.168.80.140:443/
|     Line number: 309
|     Comment: 
|          // Prevent default anchor click behavior
|     
|     Path: https://192.168.80.140:443/
|     Line number: 312
|     Comment: 
|          // Store hash
|     
|     Path: https://192.168.80.140:443/bootstrap.min.css
|     Line number: 5
|     Comment: 
|_        /*! Source: https://github.com/h5bp/html5-boilerplate/blob/master/src/css/main.css */
| http-sitemap-generator: 
|   Directory structure:
|     /
|       Other: 1; css: 2; js: 1
|     /fonts/
|       css: 1
|     /team/
|       jpeg: 4
|   Longest directory structure:
|     Depth: 1
|     Dir: /team/
|   Total files found (by extension):
|_    Other: 1; css: 3; jpeg: 4; js: 1
|_http-date: Mon, 09 May 2022 11:55:21 GMT; +1h00m00s from local time.
| tls-alpn: 
|_  http/1.1
| http-useragent-tester: 
|   Status for browser useragent: 200
|   Allowed User Agents: 
|     libwww
|     lwp-trivial
|     libcurl-agent/1.0
|     PHP/
|     Python-urllib/2.5
|     GT::WWW
|     Snoopy
|     MFC_Tear_Sample
|     HTTP::Lite
|     PHPCrawl
|     URI::Fetch
|     Zend_Http_Client
|     http client
|     PECL::HTTP
|     Wget/1.13.4 (linux-gnu)
|     WWW-Mechanize/1.34
|   Change in Status Code: 
|_    Mozilla/5.0 (compatible; Nmap Scripting Engine; https://nmap.org/book/nse.html): 400
|_http-aspnet-debug: ERROR: Script execution failed (use -d to debug)
| ssl-cert: Subject: commonName=localhost
| Not valid before: 2009-11-10T23:48:47
|_Not valid after:  2019-11-08T23:48:47
| http-security-headers: 
|   Strict_Transport_Security: 
|_    HSTS not configured in HTTPS Server
|_http-xssed: No previously reported XSS vuln.
|_http-errors: Couldn't find any error pages.
| http-vhosts: 
| 45 names had status 400
| info
| mta
|_81 names had status 200
|_http-devframework: Couldn't determine the underlying framework or CMS. Try increasing 'httpspider.maxpagecount' value to spider more pages.
|_http-feed: Couldn't find any feeds.
|_ssl-date: TLS randomness does not represent time
445/tcp   open  microsoft-ds
|_smb-enum-services: ERROR: Script execution failed (use -d to debug)
2224/tcp  open  efi-mg
5040/tcp  open  unknown
7680/tcp  open  pando-pub
8000/tcp  open  http-alt
| http-vhosts: 
| citrix
| whois
| alerts
| download
|_124 names had status 200
|_http-chrono: Request times for /; avg: 4955.49ms; min: 357.07ms; max: 22282.89ms
| http-enum: 
|_  /icons/: Potentially interesting folder w/ directory listing
|_http-date: Mon, 09 May 2022 11:54:54 GMT; +59m59s from local time.
|_http-trace: TRACE is enabled
| http-grep: 
|   (1) http://192.168.80.140:8000/: 
|     (1) email: 
|_      + myemail@something.com
|_http-title: Time Travel Company Page
| http-headers: 
|   Date: Mon, 09 May 2022 11:54:53 GMT
|   Server: Apache/2.4.46 (Win64) OpenSSL/1.1.1g PHP/7.3.23
|   Last-Modified: Tue, 20 Oct 2020 20:34:20 GMT
|   ETag: "32e8-5b22027cd0b00"
|   Accept-Ranges: bytes
|   Content-Length: 13032
|   Connection: close
|   Content-Type: text/html
|   
|_  (Request type: HEAD)
|_http-open-proxy: Proxy might be redirecting requests
11100/tcp open  unknown
|_banner: RFB 003.008
20001/tcp open  microsan
|_banner: 220-FileZilla Server version 0.9.41 beta
33006/tcp open  unknown
|_banner: H\x00\x00\x01\xFFj\x04Host '192.168.49.80' is not allowed to...
49664/tcp open  unknown
49665/tcp open  unknown
49666/tcp open  unknown
49667/tcp open  unknown
49668/tcp open  unknown
49669/tcp open  unknown

Host script results:
| qscan: 
| PORT  FAMILY  MEAN (us)  STDDEV     LOSS (%)
| 1     0       133166.70  152966.97  0.0%
| 25    0       90208.70   119269.36  0.0%
| 79    0       124896.40  144901.45  0.0%
| 105   0       132252.00  162609.73  0.0%
| 106   0       261279.70  478503.67  0.0%
| 110   0       115160.80  168479.80  0.0%
| 135   0       103724.60  123887.08  0.0%
| 139   0       63828.50   49158.30   0.0%
|_143   0       73983.00   52977.15   0.0%
| smb2-capabilities: 
|   2.0.2: 
|     Distributed File System
|   2.1: 
|     Distributed File System
|     Leasing
|     Multi-credit operations
|   3.0: 
|     Distributed File System
|     Leasing
|     Multi-credit operations
|   3.0.2: 
|     Distributed File System
|     Leasing
|     Multi-credit operations
|   3.1.1: 
|     Distributed File System
|     Leasing
|_    Multi-credit operations
| smb2-time: 
|   date: 2022-05-09T11:55:24
|_  start_date: N/A
|_ipidseq: ERROR: Script execution failed (use -d to debug)
|_dns-brute: Can't guess domain of "192.168.80.140"; use dns-brute.domain script argument.
|_msrpc-enum: Could not negotiate a connection:SMB: Failed to receive bytes: ERROR
| smb-mbenum: 
|_  ERROR: Failed to connect to browser service: Could not negotiate a connection:SMB: Failed to receive byes: ERROR
|_path-mtu: PMTU == 1500
|_fcrdns: FAIL (No PTR record)
| smb2-security-mode: 
|   3.1.1: 
|_    Message signing enabled but not required
| smb-protocols: 
|   dialects: 
|     2.0.2
|     2.1
|     3.0
|     3.0.2
|_    3.1.1

Nmap done: 1 IP address (1 host up) scanned in 279.80 seconds



