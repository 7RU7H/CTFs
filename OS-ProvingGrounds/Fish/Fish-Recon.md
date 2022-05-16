# Recon

```bash

ping -c 3 192.168.202.168
PING 192.168.202.168 (192.168.202.168) 56(84) bytes of data.
64 bytes from 192.168.202.168: icmp_seq=1 ttl=127 time=34.9 ms
64 bytes from 192.168.202.168: icmp_seq=2 ttl=127 time=43.0 ms
64 bytes from 192.168.202.168: icmp_seq=3 ttl=127 time=49.2 ms

--- 192.168.202.168 ping statistics ---
3 packets transmitted, 3 received, 0% packet loss, time 2003ms
rtt min/avg/max/mdev = 34.861/42.347/49.224/5.879 ms

nmap -sC -sV -p- 192.168.202.168 --min-rate 5000
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-14 07:38 BST
Nmap scan report for 192.168.202.168
Host is up (0.048s latency).
Not shown: 65516 closed tcp ports (reset)
PORT      STATE SERVICE              VERSION
135/tcp   open  msrpc                Microsoft Windows RPC
139/tcp   open  netbios-ssn          Microsoft Windows netbios-ssn
445/tcp   open  microsoft-ds?
3389/tcp  open  ms-wbt-server        Microsoft Terminal Services
| ssl-cert: Subject: commonName=Fishyyy
| Not valid before: 2021-10-28T07:30:11
|_Not valid after:  2022-04-29T07:30:11
|_ssl-date: 2021-10-30T06:05:16+00:00; -196d00h36m29s from scanner time.
| rdp-ntlm-info: 
|   Target_Name: FISHYYY
|   NetBIOS_Domain_Name: FISHYYY
|   NetBIOS_Computer_Name: FISHYYY
|   DNS_Domain_Name: Fishyyy
|   DNS_Computer_Name: Fishyyy
|   Product_Version: 10.0.19041
|_  System_Time: 2021-10-30T06:05:04+00:00
3700/tcp  open  giop                 CORBA naming service
4848/tcp  open  http                 Sun GlassFish Open Source Edition  4.1
|_http-server-header: GlassFish Server Open Source Edition  4.1 
|_http-title: Login
5040/tcp  open  unknown
6060/tcp  open  x11?
| fingerprint-strings: 
|   GetRequest: 
|     HTTP/1.1 200 
|     Accept-Ranges: bytes
|     ETag: W/"425-1267803922000"
|     Last-Modified: Fri, 05 Mar 2010 15:45:22 GMT
|     Content-Type: text/html
|     Content-Length: 425
|     Date: Sat, 30 Oct 2021 06:02:34 GMT
|     Connection: close
|     Server: Synametrics Web Server v7
|     <html>
|     <head>
|     <META HTTP-EQUIV="REFRESH" CONTENT="1;URL=app">
|     </head>
|     <body>
|     <script type="text/javascript">
|     <!--
|     currentLocation = window.location.pathname;
|     if(currentLocation.charAt(currentLocation.length - 1) == "/"){
|     window.location = window.location + "app";
|     }else{
|     window.location = window.location + "/app";
|     //-->
|     </script>
|     Loading Administration console. Please wait...
|     </body>
|     </html>
|   HTTPOptions: 
|     HTTP/1.1 403 
|     Cache-Control: private
|     Expires: Thu, 01 Jan 1970 00:00:00 GMT
|     Set-Cookie: JSESSIONID=3D007722AF36C2485C2D26D93B2BEAC0; Path=/
|     Content-Type: text/html;charset=ISO-8859-1
|     Content-Length: 5028
|     Date: Sat, 30 Oct 2021 06:02:35 GMT
|     Connection: close
|     Server: Synametrics Web Server v7
|     <!DOCTYPE html>
|     <html>
|     <head>
|     <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
|     <title>
|     SynaMan - Synametrics File Manager - Version: 5.1 - build 1595 
|     </title>
|     <meta NAME="Description" CONTENT="SynaMan - Synametrics File Manager" />
|     <meta NAME="Keywords" CONTENT="SynaMan - Synametrics File Manager" />
|     <meta http-equiv="X-UA-Compatible" content="IE=10" />
|     <link rel="icon" type="image/png" href="images/favicon.png">
|     <link type="text/css" rel="stylesheet" href="images/AjaxFileExplorer.css">
|     <link rel="stylesheet" type="text/css"
|   JavaRMI: 
|     HTTP/1.1 400 
|     Content-Type: text/html;charset=utf-8
|     Content-Length: 145
|     Date: Sat, 30 Oct 2021 06:02:29 GMT
|     Connection: close
|     Server: Synametrics Web Server v7
|_    <html><head><title>Oops</title><body><h1>Oops</h1><p>Well, that didn't go as we had expected.</p><p>This error has been logged.</p></body></html>
7676/tcp  open  java-message-service Java Message Service 301
7680/tcp  open  pando-pub?
8080/tcp  open  http                 Sun GlassFish Open Source Edition  4.1
| http-methods: 
|_  Potentially risky methods: PUT DELETE TRACE
|_http-title: Data Web
|_http-server-header: GlassFish Server Open Source Edition  4.1 
|_http-open-proxy: Proxy might be redirecting requests
8181/tcp  open  ssl/http             Sun GlassFish Open Source Edition  4.1
|_ssl-date: TLS randomness does not represent time
|_http-server-header: GlassFish Server Open Source Edition  4.1 
| http-methods: 
|_  Potentially risky methods: PUT DELETE TRACE
|_http-title: Data Web
| ssl-cert: Subject: commonName=localhost/organizationName=Oracle Corporation/stateOrProvinceName=California/countryName=US
| Not valid before: 2014-08-21T13:30:10
|_Not valid after:  2024-08-18T13:30:10
8686/tcp  open  java-rmi             Java RMI
| rmi-dumpregistry: 
|   jmxrmi
|     javax.management.remote.rmi.RMIServerImpl_Stub
|     @169.254.122.155:8686
|     extends
|       java.rmi.server.RemoteStub
|       extends
|_        java.rmi.server.RemoteObject
49664/tcp open  msrpc                Microsoft Windows RPC
49665/tcp open  msrpc                Microsoft Windows RPC
49666/tcp open  msrpc                Microsoft Windows RPC
49667/tcp open  msrpc                Microsoft Windows RPC
49668/tcp open  msrpc                Microsoft Windows RPC
49669/tcp open  msrpc                Microsoft Windows RPC
1 service unrecognized despite returning data. If you know the service/version, please submit the following fingerprint at https://nmap.org/cgi-bin/submit.cgi?new-service :
SF-Port6060-TCP:V=7.92%I=7%D=5/14%Time=627F4E82%P=x86_64-pc-linux-gnu%r(Ja
SF:vaRMI,139,"HTTP/1\.1\x20400\x20\r\nContent-Type:\x20text/html;charset=u
SF:tf-8\r\nContent-Length:\x20145\r\nDate:\x20Sat,\x2030\x20Oct\x202021\x2
SF:006:02:29\x20GMT\r\nConnection:\x20close\r\nServer:\x20Synametrics\x20W
SF:eb\x20Server\x20v7\r\n\r\n<html><head><title>Oops</title><body><h1>Oops
SF:</h1><p>Well,\x20that\x20didn't\x20go\x20as\x20we\x20had\x20expected\.<
SF:/p><p>This\x20error\x20has\x20been\x20logged\.</p></body></html>")%r(Ge
SF:tRequest,2A4,"HTTP/1\.1\x20200\x20\r\nAccept-Ranges:\x20bytes\r\nETag:\
SF:x20W/\"425-1267803922000\"\r\nLast-Modified:\x20Fri,\x2005\x20Mar\x2020
SF:10\x2015:45:22\x20GMT\r\nContent-Type:\x20text/html\r\nContent-Length:\
SF:x20425\r\nDate:\x20Sat,\x2030\x20Oct\x202021\x2006:02:34\x20GMT\r\nConn
SF:ection:\x20close\r\nServer:\x20Synametrics\x20Web\x20Server\x20v7\r\n\r
SF:\n<html>\r\n<head>\r\n<META\x20HTTP-EQUIV=\"REFRESH\"\x20CONTENT=\"1;UR
SF:L=app\">\r\n</head>\r\n<body>\r\n\r\n<script\x20type=\"text/javascript\
SF:">\r\n<!--\r\n\r\nvar\x20currentLocation\x20=\x20window\.location\.path
SF:name;\r\nif\(currentLocation\.charAt\(currentLocation\.length\x20-\x201
SF:\)\x20==\x20\"/\"\){\r\n\twindow\.location\x20=\x20window\.location\x20
SF:\+\x20\"app\";\r\n}else{\r\n\twindow\.location\x20=\x20window\.location
SF:\x20\+\x20\"/app\";\r\n}\x20\r\n//-->\r\n</script>\r\n\r\nLoading\x20Ad
SF:ministration\x20console\.\x20Please\x20wait\.\.\.\r\n</body>\r\n</html>
SF:")%r(HTTPOptions,14D3,"HTTP/1\.1\x20403\x20\r\nCache-Control:\x20privat
SF:e\r\nExpires:\x20Thu,\x2001\x20Jan\x201970\x2000:00:00\x20GMT\r\nSet-Co
SF:okie:\x20JSESSIONID=3D007722AF36C2485C2D26D93B2BEAC0;\x20Path=/\r\nCont
SF:ent-Type:\x20text/html;charset=ISO-8859-1\r\nContent-Length:\x205028\r\
SF:nDate:\x20Sat,\x2030\x20Oct\x202021\x2006:02:35\x20GMT\r\nConnection:\x
SF:20close\r\nServer:\x20Synametrics\x20Web\x20Server\x20v7\r\n\r\n<!DOCTY
SF:PE\x20html>\r\n\r\n\r\n<html>\r\n<head>\r\n<meta\x20http-equiv=\"conten
SF:t-type\"\x20content=\"text/html;\x20charset=UTF-8\"\x20/>\r\n<title>\r\
SF:nSynaMan\x20-\x20Synametrics\x20File\x20Manager\x20-\x20Version:\x205\.
SF:1\x20-\x20build\x201595\x20\r\n</title>\r\n\r\n\r\n<meta\x20NAME=\"Desc
SF:ription\"\x20CONTENT=\"SynaMan\x20-\x20Synametrics\x20File\x20Manager\"
SF:\x20/>\r\n<meta\x20NAME=\"Keywords\"\x20CONTENT=\"SynaMan\x20-\x20Synam
SF:etrics\x20File\x20Manager\"\x20/>\r\n\r\n\r\n<meta\x20http-equiv=\"X-UA
SF:-Compatible\"\x20content=\"IE=10\"\x20/>\r\n\r\n\r\n\r\n<link\x20rel=\"
SF:icon\"\x20type=\"image/png\"\x20href=\"images/favicon\.png\">\r\n\x20\r
SF:\n\x20\r\n\r\n<link\x20type=\"text/css\"\x20rel=\"stylesheet\"\x20href=
SF:\"images/AjaxFileExplorer\.css\">\r\n\r\n\r\n\r\n<link\x20rel=\"stylesh
SF:eet\"\x20type=\"text/css\"\x20");
Service Info: OS: Windows; CPE: cpe:/o:microsoft:windows

Host script results:
|_clock-skew: mean: -196d00h36m29s, deviation: 0s, median: -196d00h36m29s
| smb2-security-mode: 
|   3.1.1: 
|_    Message signing enabled but not required
| smb2-time: 
|   date: 2021-10-30T06:05:01
|_  start_date: N/A

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 193.34 seconds

nmap --script discovery -p- 192.168.202.168 --min-rate 5000
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-14 07:42 BST
Pre-scan script results:
| targets-ipv6-multicast-invalid-dst: 
|   IP: fe80::e88b:6cff:fe7c:8e  MAC: ea:8b:6c:7c:00:8e  IFACE: usb0
|   IP: 2a01:4c8:1426:2ede::b7   MAC: ea:8b:6c:7c:00:8e  IFACE: usb0
|_  Use --script-args=newtargets to add the results as targets
|_hostmap-robtex: *TEMPORARILY DISABLED* due to changes in Robtex's API. See https://www.robtex.com/api/
| targets-ipv6-multicast-echo: 
|   IP: 2a01:4c8:1426:2ede::b7   MAC: ea:8b:6c:7c:00:8e  IFACE: usb0
|   IP: fe80::e88b:6cff:fe7c:8e  MAC: ea:8b:6c:7c:00:8e  IFACE: usb0
|_  Use --script-args=newtargets to add the results as targets
|_http-robtex-shared-ns: *TEMPORARILY DISABLED* due to changes in Robtex's API. See https://www.robtex.com/api/
| ipv6-multicast-mld-list: 
|   fe80::e88b:6cff:fe7c:8e: 
|     device: usb0
|     mac: ea:8b:6c:7c:00:8e
|     multicast_ips: 
|       ff02::1:ff7c:8e           (NDP Solicited-node)
|       ff02::1:ff00:0            (Solicited-Node Address)
|       ff05::2                   (unknown)
|       ff02::2                   (All Routers Address)
|_      ff02::1:ff00:b7           (Solicited-Node Address)
| targets-asn: 
|_  targets-asn.asn is a mandatory parameter
| targets-ipv6-multicast-mld: 
|   IP: fe80::e88b:6cff:fe7c:8e  MAC: ea:8b:6c:7c:00:8e  IFACE: usb0
| 
|_  Use --script-args=newtargets to add the results as targets
Nmap scan report for 192.168.202.168
Host is up (0.041s latency).
Not shown: 65510 closed tcp ports (reset)
PORT      STATE SERVICE
135/tcp   open  msrpc
139/tcp   open  netbios-ssn
|_smb-enum-services: ERROR: Script execution failed (use -d to debug)
445/tcp   open  microsoft-ds
|_smb-enum-services: ERROR: Script execution failed (use -d to debug)
3389/tcp  open  ms-wbt-server
|_ssl-date: 2021-10-30T06:06:06+00:00; -196d00h36m29s from scanner time.
| ssl-cert: Subject: commonName=Fishyyy
| Not valid before: 2021-10-28T07:30:11
|_Not valid after:  2022-04-29T07:30:11
| ssl-enum-ciphers: 
|   TLSv1.0: 
|     ciphers: 
|       TLS_RSA_WITH_AES_256_CBC_SHA (rsa 2048) - A
|       TLS_RSA_WITH_AES_128_CBC_SHA (rsa 2048) - A
|       TLS_RSA_WITH_3DES_EDE_CBC_SHA (rsa 2048) - C
|       TLS_ECDHE_RSA_WITH_AES_256_CBC_SHA (secp384r1) - A
|       TLS_ECDHE_RSA_WITH_AES_128_CBC_SHA (ecdh_x25519) - A
|     compressors: 
|       NULL
|     cipher preference: server
|     warnings: 
|       64-bit block cipher 3DES vulnerable to SWEET32 attack
|   TLSv1.1: 
|     ciphers: 
|       TLS_RSA_WITH_AES_256_CBC_SHA (rsa 2048) - A
|       TLS_RSA_WITH_AES_128_CBC_SHA (rsa 2048) - A
|       TLS_RSA_WITH_3DES_EDE_CBC_SHA (rsa 2048) - C
|       TLS_ECDHE_RSA_WITH_AES_256_CBC_SHA (secp384r1) - A
|       TLS_ECDHE_RSA_WITH_AES_128_CBC_SHA (ecdh_x25519) - A
|     compressors: 
|       NULL
|     cipher preference: server
|     warnings: 
|       64-bit block cipher 3DES vulnerable to SWEET32 attack
|   TLSv1.2: 
|     ciphers: 
|       TLS_RSA_WITH_AES_256_GCM_SHA384 (rsa 2048) - A
|       TLS_RSA_WITH_AES_128_GCM_SHA256 (rsa 2048) - A
|       TLS_RSA_WITH_AES_256_CBC_SHA256 (rsa 2048) - A
|       TLS_RSA_WITH_AES_128_CBC_SHA256 (rsa 2048) - A
|       TLS_RSA_WITH_AES_256_CBC_SHA (rsa 2048) - A
|       TLS_RSA_WITH_AES_128_CBC_SHA (rsa 2048) - A
|       TLS_RSA_WITH_3DES_EDE_CBC_SHA (rsa 2048) - C
|       TLS_ECDHE_RSA_WITH_AES_256_GCM_SHA384 (secp384r1) - A
|       TLS_ECDHE_RSA_WITH_AES_128_GCM_SHA256 (ecdh_x25519) - A
|       TLS_DHE_RSA_WITH_AES_256_GCM_SHA384 (dh 2048) - A
|       TLS_DHE_RSA_WITH_AES_128_GCM_SHA256 (dh 2048) - A
|       TLS_ECDHE_RSA_WITH_AES_256_CBC_SHA384 (secp384r1) - A
|       TLS_ECDHE_RSA_WITH_AES_128_CBC_SHA256 (ecdh_x25519) - A
|       TLS_ECDHE_RSA_WITH_AES_256_CBC_SHA (secp384r1) - A
|       TLS_ECDHE_RSA_WITH_AES_128_CBC_SHA (ecdh_x25519) - A
|     compressors: 
|       NULL
|     cipher preference: server
|     warnings: 
|       64-bit block cipher 3DES vulnerable to SWEET32 attack
|_  least strength: C
| rdp-ntlm-info: 
|   Target_Name: FISHYYY
|   NetBIOS_Domain_Name: FISHYYY
|   NetBIOS_Computer_Name: FISHYYY
|   DNS_Domain_Name: Fishyyy
|   DNS_Computer_Name: Fishyyy
|   Product_Version: 10.0.19041
|_  System_Time: 2021-10-30T06:06:02+00:00
| rdp-enum-encryption: 
|   Security layer
|     CredSSP (NLA): SUCCESS
|     CredSSP with Early User Auth: SUCCESS
|     RDSTLS: SUCCESS
|     SSL: SUCCESS
|_  RDP Protocol Version: Unknown
3700/tcp  open  lrs-paging
3820/tcp  open  scp
|_ssl-date: TLS randomness does not represent time
| ssl-cert: Subject: commonName=localhost/organizationName=Oracle Corporation/stateOrProvinceName=California/countryName=US
| Not valid before: 2014-08-21T13:30:10
|_Not valid after:  2024-08-18T13:30:10
| ssl-enum-ciphers: 
|   TLSv1.2: 
|     ciphers: 
|       TLS_DHE_RSA_WITH_AES_128_CBC_SHA (dh 2048) - A
|       TLS_DHE_RSA_WITH_AES_128_CBC_SHA256 (dh 2048) - A
|       TLS_DHE_RSA_WITH_AES_128_GCM_SHA256 (dh 2048) - A
|       TLS_DHE_RSA_WITH_AES_256_CBC_SHA (dh 2048) - A
|       TLS_DHE_RSA_WITH_AES_256_CBC_SHA256 (dh 2048) - A
|       TLS_DHE_RSA_WITH_AES_256_GCM_SHA384 (dh 2048) - A
|       TLS_ECDHE_RSA_WITH_AES_128_CBC_SHA (secp256r1) - A
|       TLS_ECDHE_RSA_WITH_AES_128_CBC_SHA256 (secp256r1) - A
|       TLS_ECDHE_RSA_WITH_AES_128_GCM_SHA256 (secp256r1) - A
|       TLS_ECDHE_RSA_WITH_AES_256_CBC_SHA (secp256r1) - A
|       TLS_ECDHE_RSA_WITH_AES_256_CBC_SHA384 (secp256r1) - A
|       TLS_ECDHE_RSA_WITH_AES_256_GCM_SHA384 (secp256r1) - A
|       TLS_RSA_WITH_AES_128_CBC_SHA (rsa 2048) - A
|       TLS_RSA_WITH_AES_128_CBC_SHA256 (rsa 2048) - A
|       TLS_RSA_WITH_AES_128_GCM_SHA256 (rsa 2048) - A
|       TLS_RSA_WITH_AES_256_CBC_SHA (rsa 2048) - A
|       TLS_RSA_WITH_AES_256_CBC_SHA256 (rsa 2048) - A
|       TLS_RSA_WITH_AES_256_GCM_SHA384 (rsa 2048) - A
|     compressors: 
|       NULL
|     cipher preference: client
|   TLSv1.3: 
|     ciphers: 
|       TLS_AKE_WITH_AES_128_GCM_SHA256 (secp256r1) - A
|       TLS_AKE_WITH_AES_256_GCM_SHA384 (secp256r1) - A
|     cipher preference: client
|_  least strength: A
3920/tcp  open  exasoftport1
|_ssl-date: TLS randomness does not represent time
| ssl-cert: Subject: commonName=localhost/organizationName=Oracle Corporation/stateOrProvinceName=California/countryName=US
| Not valid before: 2014-08-21T13:30:10
|_Not valid after:  2024-08-18T13:30:10
| ssl-enum-ciphers: 
|   TLSv1.2: 
|     ciphers: 
|       TLS_DHE_RSA_WITH_AES_128_CBC_SHA (dh 2048) - A
|       TLS_DHE_RSA_WITH_AES_128_CBC_SHA256 (dh 2048) - A
|       TLS_DHE_RSA_WITH_AES_128_GCM_SHA256 (dh 2048) - A
|       TLS_DHE_RSA_WITH_AES_256_CBC_SHA (dh 2048) - A
|       TLS_DHE_RSA_WITH_AES_256_CBC_SHA256 (dh 2048) - A
|       TLS_DHE_RSA_WITH_AES_256_GCM_SHA384 (dh 2048) - A
|       TLS_ECDHE_RSA_WITH_AES_128_CBC_SHA (secp256r1) - A
|       TLS_ECDHE_RSA_WITH_AES_128_CBC_SHA256 (secp256r1) - A
|       TLS_ECDHE_RSA_WITH_AES_128_GCM_SHA256 (secp256r1) - A
|       TLS_ECDHE_RSA_WITH_AES_256_CBC_SHA (secp256r1) - A
|       TLS_ECDHE_RSA_WITH_AES_256_CBC_SHA384 (secp256r1) - A
|       TLS_ECDHE_RSA_WITH_AES_256_GCM_SHA384 (secp256r1) - A
|       TLS_RSA_WITH_AES_128_CBC_SHA (rsa 2048) - A
|       TLS_RSA_WITH_AES_128_CBC_SHA256 (rsa 2048) - A
|       TLS_RSA_WITH_AES_128_GCM_SHA256 (rsa 2048) - A
|       TLS_RSA_WITH_AES_256_CBC_SHA (rsa 2048) - A
|       TLS_RSA_WITH_AES_256_CBC_SHA256 (rsa 2048) - A
|       TLS_RSA_WITH_AES_256_GCM_SHA384 (rsa 2048) - A
|     compressors: 
|       NULL
|     cipher preference: client
|   TLSv1.3: 
|     ciphers: 
|       TLS_AKE_WITH_AES_128_GCM_SHA256 (secp256r1) - A
|       TLS_AKE_WITH_AES_256_GCM_SHA384 (secp256r1) - A
|     cipher preference: client
|_  least strength: A
4848/tcp  open  appserv-http
5040/tcp  open  unknown
6060/tcp  open  x11
7676/tcp  open  imqbrokerd
|_banner: 101 imqbroker 301\x0Aportmapper tcp PORTMAPPER 7676 [session...
7776/tcp  open  unknown
8080/tcp  open  http-proxy
| http-vhosts: 
|_128 names had status 200
|_http-title: Data Web
| http-headers: 
|   Server: GlassFish Server Open Source Edition  4.1 
|   X-Powered-By: Servlet/3.1 JSP/2.3 (GlassFish Server Open Source Edition  4.1  Java/AdoptOpenJDK/1.8)
|   Accept-Ranges: bytes
|   ETag: W/"12113-1621220744000"
|   Last-Modified: Mon, 17 May 2021 03:05:44 GMT
|   Content-Length: 12113
|   Content-Type: text/html
|   Date: Sat, 30 Oct 2021 06:06:12 GMT
|   Connection: close
|   
|_  (Request type: HEAD)
| http-enum: 
|   /sdk/../../../../../../../etc/vmware/hostd/vmInventory.xml: Possible path traversal in VMWare (CVE-2009-3733)
|   /sdk/%2E%2E/%2E%2E/%2E%2E/%2E%2E/%2E%2E/%2E%2E/%2E%2E/etc/vmware/hostd/vmInventory.xml: Possible path traversal in VMWare (CVE-2009-3733)
|   /../../../../../../../../../../etc/passwd: Possible path traversal in URI
|   /../../../../../../../../../../boot.ini: Possible path traversal in URI
|_  ..%2f..%2f..%2f..%2f..%2f..%2f..%2f..%2f/var/mobile/Library/AddressBook/AddressBook.sqlitedb: Possible iPhone/iPod/iPad generic file sharing app Directory Traversal (iOS)
|_http-chrono: Request times for /; avg: 745.20ms; min: 204.05ms; max: 2799.52ms
|_http-date: Sat, 30 Oct 2021 06:06:02 GMT; -195d23h36m30s from local time.
8181/tcp  open  intermapper
| ssl-cert: Subject: commonName=localhost/organizationName=Oracle Corporation/stateOrProvinceName=California/countryName=US
| Not valid before: 2014-08-21T13:30:10
|_Not valid after:  2024-08-18T13:30:10
|_ssl-date: TLS randomness does not represent time
| ssl-enum-ciphers: 
|   TLSv1.2: 
|     ciphers: 
|       TLS_DHE_RSA_WITH_AES_128_CBC_SHA (dh 2048) - A
|       TLS_DHE_RSA_WITH_AES_128_CBC_SHA256 (dh 2048) - A
|       TLS_DHE_RSA_WITH_AES_128_GCM_SHA256 (dh 2048) - A
|       TLS_DHE_RSA_WITH_AES_256_CBC_SHA (dh 2048) - A
|       TLS_DHE_RSA_WITH_AES_256_CBC_SHA256 (dh 2048) - A
|       TLS_DHE_RSA_WITH_AES_256_GCM_SHA384 (dh 2048) - A
|       TLS_ECDHE_RSA_WITH_AES_128_CBC_SHA (secp256r1) - A
|       TLS_ECDHE_RSA_WITH_AES_128_CBC_SHA256 (secp256r1) - A
|       TLS_ECDHE_RSA_WITH_AES_128_GCM_SHA256 (secp256r1) - A
|       TLS_ECDHE_RSA_WITH_AES_256_CBC_SHA (secp256r1) - A
|       TLS_ECDHE_RSA_WITH_AES_256_CBC_SHA384 (secp256r1) - A
|       TLS_ECDHE_RSA_WITH_AES_256_GCM_SHA384 (secp256r1) - A
|       TLS_RSA_WITH_AES_128_CBC_SHA (rsa 2048) - A
|       TLS_RSA_WITH_AES_128_CBC_SHA256 (rsa 2048) - A
|       TLS_RSA_WITH_AES_128_GCM_SHA256 (rsa 2048) - A
|       TLS_RSA_WITH_AES_256_CBC_SHA (rsa 2048) - A
|       TLS_RSA_WITH_AES_256_CBC_SHA256 (rsa 2048) - A
|       TLS_RSA_WITH_AES_256_GCM_SHA384 (rsa 2048) - A
|     compressors: 
|       NULL
|     cipher preference: client
|_  least strength: A
8686/tcp  open  sun-as-jmxrmi
49664/tcp open  unknown
49665/tcp open  unknown
49666/tcp open  unknown
49667/tcp open  unknown
49668/tcp open  unknown
49669/tcp open  unknown
49724/tcp open  unknown
49727/tcp open  unknown
49728/tcp open  unknown
49729/tcp open  unknown

Host script results:
| qscan: 
| PORT  FAMILY  MEAN (us)  STDDEV    LOSS (%)
| 1     0       39374.70   5196.08   0.0%
| 135   0       49073.00   23624.11  0.0%
| 139   0       45384.90   10278.39  0.0%
| 445   0       46607.80   19283.36  0.0%
| 3389  0       46077.40   23604.85  0.0%
| 3700  0       41924.20   4827.89   0.0%
| 3820  0       37953.20   4158.06   0.0%
| 3920  0       43608.80   8893.84   0.0%
|_4848  0       39431.70   6328.07   0.0%
| smb2-time: 
|   date: 2021-10-30T06:06:18
|_  start_date: N/A
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
|_msrpc-enum: Could not negotiate a connection:SMB: Failed to receive bytes: ERROR
| smb-protocols: 
|   dialects: 
|     2.0.2
|     2.1
|     3.0
|     3.0.2
|_    3.1.1
|_dns-brute: Can't guess domain of "192.168.202.168"; use dns-brute.domain script argument.
| smb2-security-mode: 
|   3.1.1: 
|_    Message signing enabled but not required
|_fcrdns: FAIL (No PTR record)
| smb-mbenum: 
|_  ERROR: Failed to connect to browser service: Could not negotiate a connection:SMB: Failed to receive bytes: ERROR
|_ipidseq: ERROR: Script execution failed (use -d to debug)
|_path-mtu: PMTU == 1500

Nmap done: 1 IP address (1 host up) scanned in 214.74 seconds

nikto -h http://192.168.202.168:4848                       
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          192.168.202.168
+ Target Hostname:    192.168.202.168
+ Target Port:        4848
+ Start Time:         2022-05-14 07:46:55 (GMT1)
---------------------------------------------------------------------------
+ Server: GlassFish Server Open Source Edition  4.1 
+ Retrieved x-powered-by header: Servlet/3.1 JSP/2.3 (GlassFish Server Open Source Edition  4.1  Java/AdoptOpenJDK/1.8)
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ Multiple index files found: /index.php5, /index.shtml, /default.aspx, /index.do, /index.html, /index.php3, /index.php4, /index.htm, /index.php7, /index.php, /default.htm, /index.pl, /index.jsp, /index.jhtml, /default.asp, /index.asp, /index.cgi, /index.aspx, /index.xml, /index.cfm
+ /kboard/: KBoard Forum 0.3.0 and prior have a security problem in forum_edit_post.php, forum_post.php and forum_reply.php
+ /lists/admin/: PHPList pre 2.6.4 contains a number of vulnerabilities including remote administrative access, harvesting user info and more. Default login to admin interface is admin/phplist
+ /splashAdmin.php: Cobalt Qube 3 admin is running. This may have multiple security problems as described by www.scan-associates.net. These could not be tested remotely.
+ /ssdefs/: Siteseed pre 1.4.2 has 'major' security problems.
+ /sshome/: Siteseed pre 1.4.2 has 'major' security problems.
+ /tiki/: Tiki 1.7.2 and previous allowed restricted Wiki pages to be viewed via a 'URL trick'. Default login/pass could be admin/admin
+ /tiki/tiki-install.php: Tiki 1.7.2 and previous allowed restricted Wiki pages to be viewed via a 'URL trick'. Default login/pass could be admin/admin
+ /scripts/samples/details.idc: See RFP 9901; www.wiretrip.net
+ OSVDB-396: /_vti_bin/shtml.exe: Attackers may be able to crash FrontPage by requesting a DOS device, like shtml.exe/aux.htm -- a DoS was not attempted.
+ OSVDB-637: /~root/: Allowed to browse root's home directory.
+ /cgi-bin/wrap: comes with IRIX 6.2; allows to view directories
+ /forums//admin/config.php: PHP Config file may contain database IDs and passwords.
+ /forums//adm/config.php: PHP Config file may contain database IDs and passwords.
+ /forums//administrator/config.php: PHP Config file may contain database IDs and passwords.
+ /forums/config.php: PHP Config file may contain database IDs and passwords.
+ /guestbook/guestbookdat: PHP-Gastebuch 1.60 Beta reveals sensitive information about its configuration.
+ /guestbook/pwd: PHP-Gastebuch 1.60 Beta reveals the md5 hash of the admin password.
+ /help/: Help directory should not be accessible
+ OSVDB-2411: /hola/admin/cms/htmltags.php?datei=./sec/data.php: hola-cms-1.2.9-10 may reveal the administrator ID and password.
+ OSVDB-8103: /global.inc: PHP-Survey's include file should not be available via the web. Configure the web server to ignore .inc files or change this to global.inc.php
+ OSVDB-59620: /inc/common.load.php: Bookmark4U v1.8.3 include files are not protected and may contain remote source injection by using the 'prefix' variable.
+ OSVDB-59619: /inc/config.php: Bookmark4U v1.8.3 include files are not protected and may contain remote source injection by using the 'prefix' variable.
+ OSVDB-59618: /inc/dbase.php: Bookmark4U v1.8.3 include files are not protected and may contain remote source injection by using the 'prefix' variable.
+ OSVDB-2703: /geeklog/users.php: Geeklog prior to 1.3.8-1sr2 contains a SQL injection vulnerability that lets a remote attacker reset admin password.
+ OSVDB-8204: /gb/index.php?login=true: gBook may allow admin login by setting the value 'login' equal to 'true'.
+ /guestbook/admin.php: Guestbook admin page available without authentication.
+ /getaccess: This may be an indication that the server is running getAccess for SSO
+ /cfdocs/expeval/openfile.cfm: Can use to expose the system/server path.
+ /tsweb/: Microsoft TSAC found. http://www.dslwebserver.com/main/fr_index.html?/main/sbs-Terminal-Services-Advanced-Client-Configuration.html
+ /vgn/performance/TMT: Vignette CMS admin/maintenance script available.
+ /vgn/performance/TMT/Report: Vignette CMS admin/maintenance script available.
+ /vgn/performance/TMT/Report/XML: Vignette CMS admin/maintenance script available.
+ /vgn/performance/TMT/reset: Vignette CMS admin/maintenance script available.
+ /vgn/ppstats: Vignette CMS admin/maintenance script available.
+ /vgn/previewer: Vignette CMS admin/maintenance script available.
+ /vgn/record/previewer: Vignette CMS admin/maintenance script available.
+ /vgn/stylepreviewer: Vignette CMS admin/maintenance script available.
+ /vgn/vr/Deleting: Vignette CMS admin/maintenance script available.
+ /vgn/vr/Editing: Vignette CMS admin/maintenance script available.
+ /vgn/vr/Saving: Vignette CMS admin/maintenance script available.
+ /vgn/vr/Select: Vignette CMS admin/maintenance script available.
+ /scripts/iisadmin/bdir.htr: This default script shows host info, may allow file browsing and buffer a overrun in the Chunked Encoding data transfer mechanism, request /scripts/iisadmin/bdir.htr??c:\<dirs> . https://docs.microsoft.com/en-us/security-updates/securitybulletins/2002/MS02-028. http://www.cert.org/advisories/CA-2002-09.html.
+ /scripts/iisadmin/ism.dll: Allows you to mount a brute force attack on passwords
+ /scripts/tools/ctss.idc: This CGI allows remote users to view and modify SQL DB contents, server paths, docroot and more.
+ /bigconf.cgi: BigIP Configuration CGI
+ OSVDB-28260: /_vti_bin/shtml.dll/_vti_rpc?method=server+version%3a4%2e0%2e2%2e2611: Gives info about server settings. http://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2000-0413, http://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2000-0709, http://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2000-0710, http://www.securityfocus.com/bid/1608, http://www.securityfocus.com/bid/1174.
+ OSVDB-3092: /_vti_bin/_vti_aut/author.dll?method=list+documents%3a3%2e0%2e2%2e1706&service%5fname=&listHiddenDocs=true&listExplorerDocs=true&listRecurse=false&listFiles=true&listFolders=true&listLinkInfo=true&listIncludeParent=true&listDerivedT=false&listBorders=false: We seem to have authoring access to the FrontPage web.
+ 7896 requests: 0 error(s) and 52 item(s) reported on remote host
+ End Time:           2022-05-14 07:59:01 (GMT1) (726 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested


nikto -h http://192.168.202.168:8080
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          192.168.202.168
+ Target Hostname:    192.168.202.168
+ Target Port:        8080
+ Start Time:         2022-05-14 08:02:43 (GMT1)
---------------------------------------------------------------------------
+ Server: GlassFish Server Open Source Edition  4.1 
+ Retrieved x-powered-by header: Servlet/3.1 JSP/2.3 (GlassFish Server Open Source Edition  4.1  Java/AdoptOpenJDK/1.8)
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ Allowed HTTP Methods: GET, HEAD, POST, PUT, DELETE, TRACE, OPTIONS 
+ OSVDB-397: HTTP method ('Allow' Header): 'PUT' method could allow clients to save files on the web server.
+ OSVDB-5646: HTTP method ('Allow' Header): 'DELETE' may allow clients to remove files on the web server.
+ 7892 requests: 0 error(s) and 7 item(s) reported on remote host
+ End Time:           2022-05-14 08:13:57 (GMT1) (674 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested


gospider -d 0 -s "http://192.168.202.168:4848" -c 5 -t 100 -d 5 --blacklist jpg,jpeg,gif,css,tif,tiff,png,ttf,woff,woff2,ico,pdf,svg,txt | grep -Eo '(http|https)://[^/"]+' | anew
http://192.168.202.168:4848
http://www.JSON.org
http://www.dojotoolkit.org
http://www.w3.org
http://www.xml-cml.org
http://www.mozilla.org
http://icl.com
http://xml.apache.org
http://purl.org
http://schemas.xmlsoap.org
http://ns.adobe.com

gospider -d 0 -s "http://192.168.202.168:4848" -c 5 -t 100 -d 5 --blacklist jpg,jpeg,gif,css,tif,tiff,png,ttf,woff,woff2,ico,pdf,svg,txt                                           
[url] - [code-200] - http://192.168.202.168:4848
[form] - http://192.168.202.168:4848
[javascript] - http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js
[javascript] - http://192.168.202.168:4848/theme/META-INF/json/json.js
[javascript] - http://192.168.202.168:4848/theme/META-INF/prototype/prototype.js
[javascript] - http://192.168.202.168:4848/theme/META-INF/com_sun_faces_ajax.js
[javascript] - http://192.168.202.168:4848/resource/js/cj.js
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/json/json.js] - http://www.JSON.org/license.html
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/prototype/prototype.js] - application/x-www-form-urlencoded
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - debug.js
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - browser_debug.js
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - text/javascript
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - text/plain
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://www.dojotoolkit.org/2004/dojoml
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://www.w3.org/2000/svg
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://www.w3.org/2001/SMIL20/
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://www.w3.org/1998/Math/MathML
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://www.xml-cml.org
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://www.w3.org/1999/xlink
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://www.w3.org/1999/xhtml
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://www.mozilla.org/xbl
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://www.w3.org/1999/XSL/Format
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://www.w3.org/1999/XSL/Transform
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://www.w3.org/2001/XInclude
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://www.w3.org/2002/01/xforms
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://icl.com/saxon
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://xml.apache.org/xslt
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://www.w3.org/2001/XMLSchema
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://www.w3.org/2001/XMLSchema-datatypes
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://www.w3.org/2001/XMLSchema-instance
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://www.w3.org/1999/02/22-rdf-syntax-ns#
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://www.w3.org/2000/01/rdf-schema#
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://purl.org/dc/elements/1.1/
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://purl.org/dc/qualifiers/1.0
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://schemas.xmlsoap.org/soap/envelope/
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://schemas.xmlsoap.org/wsdl/
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - text/xml
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - iframe_history.html
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - iframe_history.html?
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - text/html
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - text/json
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - application/json
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - application/xml
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - application/octet-stream
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - application/x-www-form-urlencoded
[linkfinder] - [from: http://192.168.202.168:4848/theme/META-INF/dojo/dojo.js] - text/css


gospider -d 0 -s "http://192.168.202.168:8080" -c 5 -t 100 -d 5 --blacklist jpg,jpeg,gif,css,tif,tiff,png,ttf,woff,woff2,ico,pdf,svg,txt | grep -Eo '(http|https)://[^/"]+' | anew 
http://192.168.202.168:8080
https://api.instagram.com
https://www.facebook.com
http://www.w3.org
https://twitter.com
https://www.pinterest.com

 gospider -d 0 -s "http://192.168.202.168:8080" -c 5 -t 100 -d 5 --blacklist jpg,jpeg,gif,css,tif,tiff,png,ttf,woff,woff2,ico,pdf,svg,txt                                           
[url] - [code-200] - http://192.168.202.168:8080
[javascript] - http://192.168.202.168:8080/js/jquery.min.js
[javascript] - http://192.168.202.168:8080/js/popper.min.js
[javascript] - http://192.168.202.168:8080/js/bootstrap.bundle.min.js
[javascript] - http://192.168.202.168:8080/js/jquery-3.0.0.min.js
[javascript] - http://192.168.202.168:8080/js/plugin.js
[javascript] - http://192.168.202.168:8080/js/jquery.mCustomScrollbar.concat.min.js
[javascript] - http://192.168.202.168:8080/js/custom.js
[url] - [code-200] - http://192.168.202.168:8080/index.html
[url] - [code-200] - http://192.168.202.168:8080/
[linkfinder] - [from: http://192.168.202.168:8080/js/jquery.min.js] - text/xml
[linkfinder] - [from: http://192.168.202.168:8080/js/jquery.min.js] - text/plain
[linkfinder] - [from: http://192.168.202.168:8080/js/jquery.min.js] - text/html
[linkfinder] - [from: http://192.168.202.168:8080/js/jquery.min.js] - application/x-www-form-urlencoded
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - //img.youtube.com/vi/
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - /hqdefault.jpg
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - //vimeo.com/api/v2/video/
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - //vzaar.com/api/videos/
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - //www.youtube.com/embed/
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - //player.vimeo.com/video/
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - //view.vzaar.com/
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - /player?autoplay=true
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - media/popular
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - /media/recent
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - https://api.instagram.com/v1/
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - text/css
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - //www.youtube.com/embed/$4
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - //img.youtube.com/vi/$4/hqdefault.jpg
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - //player.vimeo.com/video/$2
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - //maps.google.
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - /?ll=
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - /maps?q=
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - https://www.facebook.com/sharer/sharer.php?u={{url}}
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - http://www.w3.org/2000/svg
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - https://twitter.com/intent/tweet?url={{url}}&text={{descr}}
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - https://www.pinterest.com/pin/create/button/?url={{url}}&description={{descr}}&media={{media}}
[linkfinder] - [from: http://192.168.202.168:8080/js/plugin.js] - mm/dd/yy

nmap -sU -p- 192.168.202.168 --min-rate 5000
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-14 08:58 BST
Warning: 192.168.202.168 giving up on port because retransmission cap hit (10).
Nmap scan report for 192.168.202.168
Host is up (0.17s latency).
All 65535 scanned ports on 192.168.202.168 are in ignored states.
Not shown: 65388 open|filtered udp ports (no-response), 147 closed udp ports (port-unreach)

Nmap done: 1 IP address (1 host up) scanned in 148.05 seconds

nmap --script vuln -p- 192.168.202.168 --min-rate 5000
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-14 08:51 BST
Warning: 192.168.202.168 giving up on port because retransmission cap hit (10).
Nmap scan report for 192.168.202.168
Host is up (0.039s latency).
Not shown: 65508 closed tcp ports (reset)
PORT      STATE SERVICE
135/tcp   open  msrpc
139/tcp   open  netbios-ssn
445/tcp   open  microsoft-ds
3389/tcp  open  ms-wbt-server
3700/tcp  open  lrs-paging
3820/tcp  open  scp
| ssl-dh-params: 
|   VULNERABLE:
|   Diffie-Hellman Key Exchange Insufficient Group Strength
|     State: VULNERABLE
|       Transport Layer Security (TLS) services that use Diffie-Hellman groups
|       of insufficient strength, especially those using one of a few commonly
|       shared groups, may be susceptible to passive eavesdropping attacks.
|     Check results:
|       WEAK DH GROUP 1
|             Cipher Suite: TLS_DHE_RSA_WITH_AES_128_CBC_SHA256
|             Modulus Type: Safe prime
|             Modulus Source: RFC2409/Oakley Group 2
|             Modulus Length: 1024
|             Generator Length: 8
|             Public Key Length: 1024
|     References:
|_      https://weakdh.org
3920/tcp  open  exasoftport1
| ssl-dh-params: 
|   VULNERABLE:
|   Diffie-Hellman Key Exchange Insufficient Group Strength
|     State: VULNERABLE
|       Transport Layer Security (TLS) services that use Diffie-Hellman groups
|       of insufficient strength, especially those using one of a few commonly
|       shared groups, may be susceptible to passive eavesdropping attacks.
|     Check results:
|       WEAK DH GROUP 1
|             Cipher Suite: TLS_DHE_RSA_WITH_AES_128_CBC_SHA256
|             Modulus Type: Safe prime
|             Modulus Source: RFC2409/Oakley Group 2
|             Modulus Length: 1024
|             Generator Length: 8
|             Public Key Length: 1024
|     References:
|_      https://weakdh.org
4848/tcp  open  appserv-http
5040/tcp  open  unknown
6060/tcp  open  x11
7676/tcp  open  imqbrokerd
7680/tcp  open  pando-pub
7776/tcp  open  unknown
8080/tcp  open  http-proxy
| http-enum: 
|   /sdk/../../../../../../../etc/vmware/hostd/vmInventory.xml: Possible path traversal in VMWare (CVE-2009-3733)
|   /sdk/%2E%2E/%2E%2E/%2E%2E/%2E%2E/%2E%2E/%2E%2E/%2E%2E/etc/vmware/hostd/vmInventory.xml: Possible path traversal in VMWare (CVE-2009-3733)
|   /../../../../../../../../../../etc/passwd: Possible path traversal in URI
|   /../../../../../../../../../../boot.ini: Possible path traversal in URI
|_  ..%2f..%2f..%2f..%2f..%2f..%2f..%2f..%2f/var/mobile/Library/AddressBook/AddressBook.sqlitedb: Possible iPhone/iPod/iPad generic file sharing app Directory Traversal (iOS)
| http-slowloris-check: 
|   VULNERABLE:
|   Slowloris DOS attack
|     State: LIKELY VULNERABLE
|     IDs:  CVE:CVE-2007-6750
|       Slowloris tries to keep many connections to the target web server open and hold
|       them open as long as possible.  It accomplishes this by opening connections to
|       the target web server and sending a partial request. By doing so, it starves
|       the http server's resources causing Denial Of Service.
|       
|     Disclosure date: 2009-09-17
|     References:
|       https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2007-6750
|_      http://ha.ckers.org/slowloris/
| http-litespeed-sourcecode-download: 
| Litespeed Web Server Source Code Disclosure (CVE-2010-2333)
|_/index.php source code:
8181/tcp  open  intermapper
| ssl-dh-params: 
|   VULNERABLE:
|   Diffie-Hellman Key Exchange Insufficient Group Strength
|     State: VULNERABLE
|       Transport Layer Security (TLS) services that use Diffie-Hellman groups
|       of insufficient strength, especially those using one of a few commonly
|       shared groups, may be susceptible to passive eavesdropping attacks.
|     Check results:
|       WEAK DH GROUP 1
|             Cipher Suite: TLS_DHE_RSA_WITH_AES_128_CBC_SHA256
|             Modulus Type: Safe prime
|             Modulus Source: RFC2409/Oakley Group 2
|             Modulus Length: 1024
|             Generator Length: 8
|             Public Key Length: 1024
|     References:
|_      https://weakdh.org
8686/tcp  open  sun-as-jmxrmi
49664/tcp open  unknown
49665/tcp open  unknown
49666/tcp open  unknown
49667/tcp open  unknown
49668/tcp open  unknown
49669/tcp open  unknown
49724/tcp open  unknown
49727/tcp open  unknown
49728/tcp open  unknown
49729/tcp open  unknown
49812/tcp open  unknown

Host script results:
|_smb-vuln-ms10-054: false
|_samba-vuln-cve-2012-1182: Could not negotiate a connection:SMB: Failed to receive bytes: ERROR
|_smb-vuln-ms10-061: Could not negotiate a connection:SMB: Failed to receive bytes: ERROR

Nmap done: 1 IP address (1 host up) scanned in 213.35 seconds

```
