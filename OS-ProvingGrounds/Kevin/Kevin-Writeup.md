Name:  
Date:  
Difficulty:  
Description:  
Better Description:  
Goals:  
Learnt:


## Recon

```bash
ping -c 3 192.168.54.45  
PING 192.168.54.45 (192.168.54.45) 56(84) bytes of data.
64 bytes from 192.168.54.45: icmp_seq=1 ttl=127 time=42.3 ms
64 bytes from 192.168.54.45: icmp_seq=2 ttl=127 time=41.2 ms
64 bytes from 192.168.54.45: icmp_seq=3 ttl=127 time=48.9 ms

--- 192.168.54.45 ping statistics ---
3 packets transmitted, 3 received, 0% packet loss, time 2003ms
rtt min/avg/max/mdev = 41.208/44.158/48.932/3.406 ms

nmap -Pn -sC -sV -p- 192.168.54.45 --min-rate 5000
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-08 10:19 BST
Warning: 192.168.54.45 giving up on port because retransmission cap hit (10).
Nmap scan report for 192.168.54.45
Host is up (0.078s latency).
Not shown: 65514 closed tcp ports (reset)
PORT      STATE    SERVICE            VERSION
80/tcp    open     http               GoAhead WebServer
| http-title: HP Power Manager
|_Requested resource was http://192.168.54.45/index.asp
|_http-server-header: GoAhead-Webs
135/tcp   open     msrpc              Microsoft Windows RPC
139/tcp   open     netbios-ssn        Microsoft Windows netbios-ssn
445/tcp   open     microsoft-ds       Windows 7 Ultimate N 7600 microsoft-ds (workgroup: WORKGROUP)
3389/tcp  open     ssl/ms-wbt-server?
| rdp-ntlm-info: 
|   Target_Name: KEVIN
|   NetBIOS_Domain_Name: KEVIN
|   NetBIOS_Computer_Name: KEVIN
|   DNS_Domain_Name: kevin
|   DNS_Computer_Name: kevin
|   Product_Version: 6.1.7600
|_  System_Time: 2022-05-08T09:20:50+00:00
|_ssl-date: 2022-05-08T09:20:57+00:00; -1s from scanner time.
| ssl-cert: Subject: commonName=kevin
| Not valid before: 2022-02-14T16:29:03
|_Not valid after:  2022-08-16T16:29:03
3573/tcp  open     tag-ups-1?
11827/tcp filtered unknown
15526/tcp filtered unknown
16411/tcp filtered unknown
22715/tcp filtered unknown
28079/tcp filtered unknown
40360/tcp filtered unknown
43127/tcp filtered unknown
49152/tcp open     msrpc              Microsoft Windows RPC
49153/tcp open     msrpc              Microsoft Windows RPC
49154/tcp open     msrpc              Microsoft Windows RPC
49155/tcp open     msrpc              Microsoft Windows RPC
49158/tcp open     msrpc              Microsoft Windows RPC
49160/tcp open     msrpc              Microsoft Windows RPC
64590/tcp filtered unknown
64697/tcp filtered unknown
Service Info: Host: KEVIN; OS: Windows; CPE: cpe:/o:microsoft:windows

Host script results:
| smb2-security-mode: 
|   2.1: 
|_    Message signing enabled but not required
| smb-security-mode: 
|   account_used: guest
|   authentication_level: user
|   challenge_response: supported
|_  message_signing: disabled (dangerous, but default)
|_nbstat: NetBIOS name: KEVIN, NetBIOS user: <unknown>, NetBIOS MAC: 00:50:56:ba:1b:b3 (VMware)
| smb-os-discovery: 
|   OS: Windows 7 Ultimate N 7600 (Windows 7 Ultimate N 6.1)
|   OS CPE: cpe:/o:microsoft:windows_7::-
|   Computer name: kevin
|   NetBIOS computer name: KEVIN\x00
|   Workgroup: WORKGROUP\x00
|_  System time: 2022-05-08T02:20:50-07:00
|_clock-skew: mean: 1h23m59s, deviation: 3h07m50s, median: 0s
| smb2-time: 
|   date: 2022-05-08T09:20:49
|_  start_date: 2022-05-08T09:17:56

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 108.67 seconds

nmap -Pn --script discovery -p 80,135,139,445,3389,3573,11827,15526,22715,40360,49127,49152,49153,49154,49155,49158,49160,64590,64697 192.168.54.45 --min-rate 5000
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-08 11:39 BST
Pre-scan script results:
| targets-asn: 
|_  targets-asn.asn is a mandatory parameter
| targets-ipv6-multicast-invalid-dst: 
|   IP: 2a01:4c8:1401:4bb::1c     MAC: ce:f4:0a:4d:f5:02  IFACE: usb0
|   IP: fe80::ccf4:aff:fe4d:f502  MAC: ce:f4:0a:4d:f5:02  IFACE: usb0
|_  Use --script-args=newtargets to add the results as targets
|_http-robtex-shared-ns: *TEMPORARILY DISABLED* due to changes in Robtex's API. See https://www.robtex.com/api/
| targets-ipv6-multicast-mld: 
|   IP: fe80::ccf4:aff:fe4d:f502  MAC: ce:f4:0a:4d:f5:02  IFACE: usb0
| 
|_  Use --script-args=newtargets to add the results as targets
| targets-ipv6-multicast-echo: 
|   IP: 2a01:4c8:1401:4bb::1c     MAC: ce:f4:0a:4d:f5:02  IFACE: usb0
|   IP: fe80::ccf4:aff:fe4d:f502  MAC: ce:f4:0a:4d:f5:02  IFACE: usb0
|_  Use --script-args=newtargets to add the results as targets
|_hostmap-robtex: *TEMPORARILY DISABLED* due to changes in Robtex's API. See https://www.robtex.com/api/
| ipv6-multicast-mld-list: 
|   fe80::ccf4:aff:fe4d:f502: 
|     device: usb0
|     mac: ce:f4:0a:4d:f5:02
|     multicast_ips: 
|       ff02::1:ff4d:f502         (NDP Solicited-node)
|       ff02::1:ff00:0            (Solicited-Node Address)
|       ff05::2                   (unknown)
|       ff02::2                   (All Routers Address)
|_      ff02::1:ff00:1c           (Solicited-Node Address)
Nmap scan report for 192.168.54.45
Host is up (0.10s latency).

PORT      STATE  SERVICE
80/tcp    open   http
|_http-xssed: No previously reported XSS vuln.
| http-comments-displayer: 
| Spidering limited to: maxdepth=3; maxpagecount=20; withinhost=192.168.54.45
|     
|     Path: http://192.168.54.45:80/CStyle/Theme.css
|     Line number: 8
|     Comment: 
|         /* 1st we cover the background color of a page for our three major display frames */
|     
|     Path: http://192.168.54.45:80/index.asp
|     Line number: 120
|     Comment: 
|         <!-- *** dialog box content *** -->
|     
|     Path: http://192.168.54.45:80/CStyle/Theme.css
|     Line number: 2
|     Comment: 
|         /* default values for selected tags, Note: without background color defined! */
|     
|     Path: http://192.168.54.45:80/CStyle/Theme.css
|     Line number: 16
|     Comment: 
|         /* a unique set for dialog type displays, used most often */
|     
|     Path: http://192.168.54.45:80/CStyle/Theme.css
|     Line number: 25
|     Comment: 
|         /* a unique set for error type displays */
|     
|     Path: http://192.168.54.45:80/index.asp
|     Line number: 106
|     Comment: 
|         <!-- *** header bar *** -->
|     
|     Path: http://192.168.54.45:80/index.asp
|     Line number: 92
|     Comment: 
|         <!-- *** login page ****************************************************** -->
|     
|     Path: http://192.168.54.45:80/index.asp
|     Line number: 113
|     Comment: 
|         <!-- System Login -->
|     
|     Path: http://192.168.54.45:80/index.asp
|     Line number: 15
|     Comment: 
# THIS IS JUST AN EMPTY COMMENT         
|                       //-->
|     
|     Path: http://192.168.54.45:80/index.asp
|     Line number: 102
|     Comment: 
|         <!-- *** begin dialog box *** -->
|     
|     Path: http://192.168.54.45:80/CStyle/Theme.css
|     Line number: 33
|     Comment: 
|_        /* a unique set for tab navigation displays */
| http-vhosts: 
|_128 names had status 302
| http-security-headers: 
|   Cache_Control: 
|     Header: Cache-Control: no-cache
|   Pragma: 
|_    Header: Pragma: no-cache
|_http-referer-checker: Couldn't find any cross-domain scripts.
| http-backup-finder: 
| Spidering limited to: maxdepth=3; maxpagecount=20; withinhost=192.168.54.45
|   http://192.168.54.45:80/CStyle/Theme.bak
|   http://192.168.54.45:80/CStyle/Theme.css~
|   http://192.168.54.45:80/CStyle/Theme copy.css
|   http://192.168.54.45:80/CStyle/Copy of Theme.css
|   http://192.168.54.45:80/CStyle/Copy (2) of Theme.css
|   http://192.168.54.45:80/CStyle/Theme.css.1
|_  http://192.168.54.45:80/CStyle/Theme.css.~1~
|_http-feed: Couldn't find any feeds.
| http-useragent-tester: 
|   Status for browser useragent: 200
|   Redirected To: http://192.168.54.45/index.asp
|   Allowed User Agents: 
|     Mozilla/5.0 (compatible; Nmap Scripting Engine; https://nmap.org/book/nse.html)
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
|_    WWW-Mechanize/1.34
|_http-chrono: Request times for /index.asp; avg: 264.37ms; min: 244.86ms; max: 303.36ms
|_http-errors: Couldn't find any error pages.
| http-sitemap-generator: 
|   Directory structure:
|     /
|       asp: 1
|     /CStyle/
|       css: 1
|   Longest directory structure:
|     Depth: 1
|     Dir: /CStyle/
|   Total files found (by extension):
|_    asp: 1; css: 1
|_http-date: Sun May 08 03:41:18 2022; -6h00m01s from local time.
| http-waf-detect: IDS/IPS/WAF detected:
|_192.168.54.45:80/?p4yl04d3=<script>alert(document.cookie)</script>
| http-headers: 
|   Date: Sun May 08 03:41:18 2022
|   Server: GoAhead-Webs
|   Pragma: no-cache
|   Cache-Control: no-cache
|   Content-type: text/html
|   
|_  (Request type: HEAD)
| http-title: HP Power Manager
|_Requested resource was http://192.168.54.45/index.asp
|_http-devframework: Couldn't determine the underlying framework or CMS. Try increasing 'httpspider.maxpagecount' value to spider more pages.
| http-enum: 
|   /cgi-bin/mj_wwwusr: Majordomo2 Mailing List
|   /cgi-bin/vcs: Mitel Audio and Web Conferencing (AWC)
|   /cgi-bin/ffileman.cgi?: Ffileman Web File Manager
|   /cgi-bin/ck/mimencode: ContentKeeper Web Appliance
|   /cgi-bin/masterCGI?: Alcatel-Lucent OmniPCX Enterprise
|   /cgi-bin/awstats.pl: AWStats
|   /cgi-bin/image/shikaku2.png: TeraStation PRO RAID 0/1/5 Network Attached Storage
|   /cgi-bin2/: Potentially interesting folder
|_  /cgi-bin/: Potentially interesting folder
|_http-mobileversion-checker: No mobile version detected.
| http-auth-finder: 
| Spidering limited to: maxdepth=3; maxpagecount=20; withinhost=192.168.54.45
|   url                                method
|_  http://192.168.54.45:80/index.asp  FORM
135/tcp   open   msrpc
139/tcp   open   netbios-ssn
|_smb-enum-services: ERROR: Script execution failed (use -d to debug)
445/tcp   open   microsoft-ds
|_smb-enum-services: ERROR: Script execution failed (use -d to debug)
3389/tcp  open   ms-wbt-server
| ssl-cert: Subject: commonName=kevin
| Not valid before: 2022-02-14T16:29:03
|_Not valid after:  2022-08-16T16:29:03
3573/tcp  open   tag-ups-1
11827/tcp closed unknown
15526/tcp closed unknown
22715/tcp closed unknown
40360/tcp closed unknown
49127/tcp closed unknown
49152/tcp open   unknown
49153/tcp open   unknown
49154/tcp open   unknown
49155/tcp open   unknown
49158/tcp open   unknown
49160/tcp open   unknown
64590/tcp closed unknown
64697/tcp closed unknown

Host script results:
|_fcrdns: FAIL (No PTR record)
| smb-security-mode: 
|   account_used: <blank>
|   authentication_level: user
|   challenge_response: supported
|_  message_signing: disabled (dangerous, but default)
| smb-mbenum: 
|   Master Browser
|     KEVIN  6.1  
|   Potential Browser
|     KEVIN  6.1  
|   Server service
|     KEVIN  6.1  
|   Windows NT/2000/XP/2003 server
|     KEVIN  6.1  
|   Workstation
|_    KEVIN  6.1  
| smb2-security-mode: 
|   2.1: 
|_    Message signing enabled but not required
|_nbstat: NetBIOS name: KEVIN, NetBIOS user: <unknown>, NetBIOS MAC: 00:50:56:ba:1b:b3 (VMware)
| smb-enum-shares: 
|   note: ERROR: Enumerating shares failed, guessing at common ones (NT_STATUS_ACCESS_DENIED)
|   account_used: <blank>
|   \\192.168.54.45\ADMIN$: 
|     warning: Couldn't get details for share: NT_STATUS_ACCESS_DENIED
|     Anonymous access: <none>
|   \\192.168.54.45\C$: 
|     warning: Couldn't get details for share: NT_STATUS_ACCESS_DENIED
|     Anonymous access: <none>
|   \\192.168.54.45\IPC$: 
|     warning: Couldn't get details for share: NT_STATUS_ACCESS_DENIED
|     Anonymous access: READ
|   \\192.168.54.45\USERS: 
|     warning: Couldn't get details for share: NT_STATUS_ACCESS_DENIED
|_    Anonymous access: <none>
| qscan: 
| PORT   FAMILY  MEAN (us)  STDDEV     LOSS (%)
| 80     0       50238.50   17539.75   0.0%
| 135    0       43726.50   10084.26   0.0%
| 139    0       92878.10   113824.06  0.0%
| 445    0       45180.70   11251.53   0.0%
| 3389   0       44796.70   8099.54    0.0%
| 3573   0       76931.40   102066.05  0.0%
| 11827  0       126162.00  209147.52  0.0%
| 49152  0       54756.80   37525.25   0.0%
|_49153  0       48124.40   18957.70   0.0%
| smb-os-discovery: 
|   OS: Windows 7 Ultimate N 7600 (Windows 7 Ultimate N 6.1)
|   OS CPE: cpe:/o:microsoft:windows_7::-
|   Computer name: kevin
|   NetBIOS computer name: KEVIN\x00
|   Workgroup: WORKGROUP\x00
|_  System time: 2022-05-08T03:39:28-07:00
| smb2-capabilities: 
|   2.0.2: 
|     Distributed File System
|   2.1: 
|     Distributed File System
|     Leasing
|_    Multi-credit operations
|_msrpc-enum: NT_STATUS_ACCESS_DENIED
|_dns-brute: Can't guess domain of "192.168.54.45"; use dns-brute.domain script argument.
| smb2-time: 
|   date: 2022-05-08T10:39:30
|_  start_date: 2022-05-08T09:17:56
|_ipidseq: ERROR: Script execution failed (use -d to debug)
| smb-protocols: 
|   dialects: 
|     NT LM 0.12 (SMBv1) [dangerous, but default]
|     2.0.2
|_    2.1
|_path-mtu: PMTU == 1500

Nmap done: 1 IP address (1 host up) scanned in 373.90 seconds
```
There was alot of xss vulnerabilities found so [nikto output](nikto.md) can be found by following the link.

## SMB Enumeration

```bash
enum4linux -a 192.168.80.45                            
Starting enum4linux v0.9.1 ( http://labs.portcullis.co.uk/application/enum4linux/ ) on Tue May 10 22:11:02 2022

 =========================================( Target Information )=========================================

Target ........... 192.168.80.45
RID Range ........ 500-550,1000-1050
Username ......... ''
Password ......... ''
Known Usernames .. administrator, guest, krbtgt, domain admins, root, bin, none


 ===========================( Enumerating Workgroup/Domain on 192.168.80.45 )===========================


[+] Got domain/workgroup name: WORKGROUP


 ===============================( Nbtstat Information for 192.168.80.45 )===============================

Looking up status of 192.168.80.45
        KEVIN           <00> -         B <ACTIVE>  Workstation Service
        WORKGROUP       <00> - <GROUP> B <ACTIVE>  Domain/Workgroup Name
        KEVIN           <20> -         B <ACTIVE>  File Server Service
        WORKGROUP       <1e> - <GROUP> B <ACTIVE>  Browser Service Elections
        WORKGROUP       <1d> -         B <ACTIVE>  Master Browser

        MAC Address = 00-50-56-BA-AF-FE

 ===================================( Session Check on 192.168.80.45 )===================================


[+] Server 192.168.80.45 allows sessions using username '', password ''


 ================================( Getting domain SID for 192.168.80.45 )================================

do_cmd: Could not initialise lsarpc. Error was NT_STATUS_ACCESS_DENIED

[+] Can't determine if host is part of domain or part of a workgroup


 ==================================( OS information on 192.168.80.45 )==================================


[E] Can't get OS info with smbclient


[+] Got OS info for 192.168.80.45 from srvinfo: 
do_cmd: Could not initialise srvsvc. Error was NT_STATUS_ACCESS_DENIED


 =======================================( Users on 192.168.80.45 )=======================================


[E] Couldn't find users using querydispinfo: NT_STATUS_ACCESS_DENIED

                                                                                                          

[E] Couldn't find users using enumdomusers: NT_STATUS_ACCESS_DENIED                                       
                                                                                                          
                                                                                                          
 =================================( Share Enumeration on 192.168.80.45 )=================================
                                                                                                          
do_connect: Connection to 192.168.80.45 failed (Error NT_STATUS_RESOURCE_NAME_NOT_FOUND)                  

        Sharename       Type      Comment
        ---------       ----      -------
Reconnecting with SMB1 for workgroup listing.
Unable to connect with SMB1 -- no workgroup available

[+] Attempting to map shares on 192.168.80.45                                                             
                                                                                                          
                                                                                                          
 ===========================( Password Policy Information for 192.168.80.45 )===========================
                                                                                                          
                                                                                                          
[E] Unexpected error from polenum:                                                                        
                                                                                                          
                                                                                                          

[+] Attaching to 192.168.80.45 using a NULL share

[+] Trying protocol 139/SMB...

        [!] Protocol failed: Cannot request session (Called Name:192.168.80.45)

[+] Trying protocol 445/SMB...

        [!] Protocol failed: SMB SessionError: STATUS_ACCESS_DENIED({Access Denied} A process has requested access to an object but has not been granted those access rights.)



[E] Failed to get password policy with rpcclient                                                          
                                                                                                          
                                                                                                          

 ======================================( Groups on 192.168.80.45 )======================================
                                                                                                          
                                                                                                          
[+] Getting builtin groups:                                                                               
                                                                                                          
                                                                                                          
[+]  Getting builtin group memberships:                                                                   
                                                                                                          
                                                                                                          
[+]  Getting local groups:                                                                                
                                                                                                          
                                                                                                          
[+]  Getting local group memberships:                                                                     
                                                                                                          
                                                                                                          
[+]  Getting domain groups:                                                                               
                                                                                                          
                                                                                                          
[+]  Getting domain group memberships:                                                                    
                                                                                                          
                                                                                                          
 ==================( Users on 192.168.80.45 via RID cycling (RIDS: 500-550,1000-1050) )==================
                                                                                                          
                                                                                                          
[E] Couldn't get SID: NT_STATUS_ACCESS_DENIED.  RID cycling not possible.                                 
                                                                                                          
                                                                                                          
 ===============================( Getting printer info for 192.168.80.45 )===============================
                                                                                                          
do_cmd: Could not initialise spoolss. Error was NT_STATUS_ACCESS_DENIED                                   


enum4linux complete on Tue May 10 22:11:09 2022


nbtscan -r 192.168.80.45  
Doing NBT name scan for addresses from 192.168.80.45

IP address       NetBIOS Name     Server    User             MAC address      
------------------------------------------------------------------------------
192.168.80.45    KEVIN            <server>  <unknown>        00:50:56:ba:af:fe

```

