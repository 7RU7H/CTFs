Name:  
Date:  
Difficulty:  
Description:  
Better Description:  
Goals:  
Learnt: 


```bash
root@ip-10-10-254-124:~# ping -c 3 10.10.93.146
PING 10.10.93.146 (10.10.93.146) 56(84) bytes of data.
64 bytes from 10.10.93.146: icmp_seq=1 ttl=64 time=0.741 ms
64 bytes from 10.10.93.146: icmp_seq=2 ttl=64 time=0.430 ms
64 bytes from 10.10.93.146: icmp_seq=3 ttl=64 time=11.6 ms

--- 10.10.93.146 ping statistics ---
3 packets transmitted, 3 received, 0% packet loss, time 2039ms
rtt min/avg/max/mdev = 0.430/4.279/11.667/5.225 ms
root@ip-10-10-254-124:~# nmap -sC -sV -p- 10.10.93.146 --min-rate 5000

Starting Nmap 7.60 ( https://nmap.org ) at 2022-05-08 10:18 BST
Nmap scan report for ip-10-10-93-146.eu-west-1.compute.internal (10.10.93.146)
Host is up (0.015s latency).
Not shown: 65034 closed ports, 498 filtered ports
PORT   STATE SERVICE VERSION
2/tcp  open  ssh     OpenSSH 7.6p1 Ubuntu 4ubuntu0.3 (Ubuntu Linux; protocol 2.0)
| ssh-hostkey: 
|   2048 f8:8c:1e:07:1d:f3:de:8a:01:f1:50:51:e4:e6:00:fe (RSA)
|   256 e6:5d:ea:6c:83:86:20:de:f0:f0:3a:1e:5f:7d:47:b5 (ECDSA)
|_  256 e9:ef:d3:78:db:9c:47:20:7e:62:82:9d:8f:6f:45:6a (EdDSA)
22/tcp open  ssh     OpenSSH 8.3 (protocol 2.0)
80/tcp open  http    nginx 1.18.0
|_http-server-header: nginx/1.18.0
|_http-title: Error
MAC Address: 02:84:13:60:84:33 (Unknown)
Service Info: OS: Linux; CPE: cpe:/o:linux:linux_kernel

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 97.20 seconds
root@ip-10-10-254-124:~# nikto -h 10.10.93.146 -C  all
- Nikto v2.1.5
---------------------------------------------------------------------------
+ Target IP:          10.10.93.146
+ Target Hostname:    ip-10-10-93-146.eu-west-1.compute.internal
+ Target Port:        80
+ Start Time:         2022-05-08 10:24:37 (GMT1)
---------------------------------------------------------------------------
+ Server: nginx/1.18.0
+ Cookie TestCookie created without the httponly flag
+ Retrieved x-powered-by header: PHP/7.3.19
+ The anti-clickjacking X-Frame-Options header is not present.
+ OSVDB-3233: /phpinfo.php: Contains PHP configuration information
+ OSVDB-3092: /css: This might be interesting...
+ OSVDB-3092: /js: This might be interesting...
+ 6544 items checked: 0 error(s) and 6 item(s) reported on remote host
+ End Time:           2022-05-08 10:25:45 (GMT1) (68 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested
root@ip-10-10-254-124:~# dirb http://10.10.93.146

-----------------
DIRB v2.22    
By The Dark Raver
-----------------

START_TIME: Sun May  8 10:29:39 2022
URL_BASE: http://10.10.93.146/
WORDLIST_FILES: /usr/share/dirb/wordlists/common.txt

-----------------

GENERATED WORDS: 4612                                                          

---- Scanning URL: http://10.10.93.146/ ----
+ http://10.10.93.146/phpinfo.php (CODE:200|SIZE:86538)                        
==> DIRECTORY: http://10.10.93.146/vendor/                                     
                                                                               
---- Entering directory: http://10.10.93.146/vendor/ ----
==> DIRECTORY: http://10.10.93.146/vendor/jquery/                              
                                                                               
---- Entering directory: http://10.10.93.146/vendor/jquery/ ----
                                                                               
-----------------
END_TIME: Sun May  8 10:29:51 2022
DOWNLOADED: 13836 - FOUND: 1

root@ip-10-10-254-124:~# nmap --script discovery -p2,22,80 10.10.93.146

Starting Nmap 7.60 ( https://nmap.org ) at 2022-05-08 10:45 BST
Pre-scan script results:
| targets-asn: 
|_  targets-asn.asn is a mandatory parameter
Nmap scan report for ip-10-10-93-146.eu-west-1.compute.internal (10.10.93.146)
Host is up (0.00023s latency).

PORT   STATE SERVICE
2/tcp  open  compressnet
|_banner: SSH-2.0-OpenSSH_7.6p1 Ubuntu-4ubuntu0.3
22/tcp open  ssh
|_banner: SSH-2.0-OpenSSH_8.3
| ssh2-enum-algos: 
|   kex_algorithms: (9)
|   server_host_key_algorithms: (4)
|   encryption_algorithms: (6)
|   mac_algorithms: (10)
|_  compression_algorithms: (2)
80/tcp open  http
|_http-chrono: Request times for /; avg: 197.82ms; min: 168.57ms; max: 216.57ms
| http-comments-displayer: 
| Spidering limited to: maxdepth=3; maxpagecount=20; withinhost=ip-10-10-93-146.eu-west-1.compute.internal
|     
|     Path: http://ip-10-10-93-146.eu-west-1.compute.internal/vendor/bootstrap/css/bootstrap.min.css
|     Line number: 7
|     Comment: 
|         /*# sourceMappingURL=bootstrap.min.css.map */
|     
|     Path: http://ip-10-10-93-146.eu-west-1.compute.internal/vendor/jquery/v.location.href,t.head.appendChild(r)):t=v),o=!n&&[],(i=D.exec(e))?[t.createElement(i[1])]:(i=xe([e],t,o),o&&o.length&&E(o).remove(),E.merge([],i.childNodes)));var
|     Line number: 63
|     Comment: 
|         <!-- Bootstrap core JavaScript -->
|     
|     Path: http://ip-10-10-93-146.eu-west-1.compute.internal/vendor/jquery/v.location.href,t.head.appendChild(r)):t=v),o=!n&&[],(i=D.exec(e))?[t.createElement(i[1])]:(i=xe([e],t,o),o&&o.length&&E(o).remove(),E.merge([],i.childNodes)));var
|     Line number: 21
|     Comment: 
|         /* Margin bottom by footer height */
|     
|     Path: http://ip-10-10-93-146.eu-west-1.compute.internal/vendor/jquery/v.location.href,t.head.appendChild(r)):t=v),o=!n&&[],(i=D.exec(e))?[t.createElement(i[1])]:(i=xe([e],t,o),o&&o.length&&E(o).remove(),E.merge([],i.childNodes)));var
|     Line number: 12
|     Comment: 
|         <!-- Bootstrap core CSS -->
|     
|     Path: http://ip-10-10-93-146.eu-west-1.compute.internal/vendor/jquery/jquery.slim.min.js
|     Line number: 1
|     Comment: 
|         /*! jQuery v3.4.1 -ajax,-ajax/jsonp,-ajax/load,-ajax/parseXML,-ajax/script,-ajax/var/location,-ajax/var/nonce,-ajax/var/rquery,-ajax/xhr,-manipulation/_evalUrl,-event/ajax,-effects,-effects/Tween,-effects/animatedSelector | (c) JS Foundation and other contributors | jquery.org/license */
|     
|     Path: http://ip-10-10-93-146.eu-west-1.compute.internal/vendor/bootstrap/css/bootstrap.min.css
|     Line number: 1
|     Comment: 
|         /*!
|          * Bootstrap v4.3.1 (https://getbootstrap.com/)
|          * Copyright 2011-2019 The Bootstrap Authors
|          * Copyright 2011-2019 Twitter, Inc.
|          * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
|          */
|     
|     Path: http://ip-10-10-93-146.eu-west-1.compute.internal/vendor/jquery/v.location.href,t.head.appendChild(r)):t=v),o=!n&&[],(i=D.exec(e))?[t.createElement(i[1])]:(i=xe([e],t,o),o&&o.length&&E(o).remove(),E.merge([],i.childNodes)));var
|     Line number: 35
|     Comment: 
|         <!-- Navigation -->
|     
|     Path: http://ip-10-10-93-146.eu-west-1.compute.internal/vendor/jquery/v.location.href,t.head.appendChild(r)):t=v),o=!n&&[],(i=D.exec(e))?[t.createElement(i[1])]:(i=xe([e],t,o),o&&o.length&&E(o).remove(),E.merge([],i.childNodes)));var
|     Line number: 53
|     Comment: 
|         <!-- Page Content -->
|     
|     Path: http://ip-10-10-93-146.eu-west-1.compute.internal/vendor/bootstrap/js/bootstrap.bundle.min.js
|     Line number: 1
|     Comment: 
|         /*!
|           * Bootstrap v4.3.1 (https://getbootstrap.com/)
|           * Copyright 2011-2019 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
|           * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
|           */
|     
|     Path: http://ip-10-10-93-146.eu-west-1.compute.internal/vendor/jquery/v.location.href,t.head.appendChild(r)):t=v),o=!n&&[],(i=D.exec(e))?[t.createElement(i[1])]:(i=xe([e],t,o),o&&o.length&&E(o).remove(),E.merge([],i.childNodes)));var
|     Line number: 27
|     Comment: 
|_        /* Set the fixed height of the footer here */
|_http-date: Sun, 08 May 2022 09:45:46 GMT; 0s from local time.
|_http-devframework: Couldn't determine the underlying framework or CMS. Try increasing 'httpspider.maxpagecount' value to spider more pages.
|_http-errors: Couldn't find any error pages.
|_http-feed: Couldn't find any feeds.
| http-headers: 
|   Server: nginx/1.18.0
|   Date: Sun, 08 May 2022 09:45:46 GMT
|   Content-Type: text/html; charset=UTF-8
|   Connection: close
|   Vary: Accept-Encoding
|   X-Powered-By: PHP/7.3.19
|   Set-Cookie: TestCookie=just+a+test+cookie; expires=Sun, 08-May-2022 10:45:46 GMT; Max-Age=3600; path=/; domain=pwd.harder.local; secure
|   
|_  (Request type: HEAD)
|_http-mobileversion-checker: No mobile version detected.
|_http-php-version: Version from header x-powered-by: PHP/7.3.19
|_http-referer-checker: Couldn't find any cross-domain scripts.
|_http-security-headers: 
| http-sitemap-generator: 
|   Directory structure:
|     /
|       Other: 1
|     /vendor/bootstrap/css/
|       css: 1
|     /vendor/bootstrap/js/
|       js: 1
|     /vendor/jquery/
|       exec(e)): 1; js: 1
|   Longest directory structure:
|     Depth: 3
|     Dir: /vendor/bootstrap/js/
|   Total files found (by extension):
|_    Other: 1; css: 1; exec(e)): 1; js: 2
|_http-title: Error
| http-useragent-tester: 
|   Status for browser useragent: 200
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
| http-vhosts: 
|_127 names had status 200
|_http-xssed: No previously reported XSS vuln.
MAC Address: 02:84:13:60:84:33 (Unknown)

Host script results:
| dns-brute: 
|   DNS Brute-force hostnames: 
|_    ns0.eu-west-1.compute.internal - 172.16.0.23
|_fcrdns: PASS (ip-10-10-93-146.eu-west-1.compute.internal)
|_ipidseq: All zeros
|_path-mtu: PMTU == 9001
| qscan: 
| PORT  FAMILY  MEAN (us)  STDDEV  LOSS (%)
| 2     0       488.17     46.12   40.0%
| 22    0       497.60     109.14  50.0%
|_80    1       544.20     30.11   50.0%
|_sniffer-detect: Unknown (tests: "________")

Nmap done: 1 IP address (1 host up) scanned in 31.40 seconds


```
