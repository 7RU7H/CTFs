
Name: Twiggy
Date:  
Difficulty:  
Description:  An easy machine, but a tad sneaky.
Better Description:  
Goals:  
Learnt:

```bash
 ping -c 3 192.168.80.62 
PING 192.168.80.62 (192.168.80.62) 56(84) bytes of data.
64 bytes from 192.168.80.62: icmp_seq=1 ttl=63 time=33.7 ms
64 bytes from 192.168.80.62: icmp_seq=2 ttl=63 time=42.0 ms
64 bytes from 192.168.80.62: icmp_seq=3 ttl=63 time=43.8 ms

--- 192.168.80.62 ping statistics ---
3 packets transmitted, 3 received, 0% packet loss, time 2003ms
rtt min/avg/max/mdev = 33.656/39.813/43.760/4.411 ms

nmap -sC -sV -p- 192.168.80.62 --min-rate 5000            
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-09 20:15 BST
Nmap scan report for 192.168.80.62
Host is up (0.047s latency).
Not shown: 65529 filtered tcp ports (no-response)
PORT     STATE SERVICE VERSION
22/tcp   open  ssh     OpenSSH 7.4 (protocol 2.0)
| ssh-hostkey: 
|   2048 44:7d:1a:56:9b:68:ae:f5:3b:f6:38:17:73:16:5d:75 (RSA)
|   256 1c:78:9d:83:81:52:f4:b0:1d:8e:32:03:cb:a6:18:93 (ECDSA)
|_  256 08:c9:12:d9:7b:98:98:c8:b3:99:7a:19:82:2e:a3:ea (ED25519)
53/tcp   open  domain  NLnet Labs NSD
80/tcp   open  http    nginx 1.16.1
|_http-title: Home | Mezzanine
|_http-server-header: nginx/1.16.1
4505/tcp open  zmtp    ZeroMQ ZMTP 2.0
4506/tcp open  zmtp    ZeroMQ ZMTP 2.0
8000/tcp open  http    nginx 1.16.1
|_http-title: Site doesn't have a title (application/json).
|_http-open-proxy: Proxy might be redirecting requests
|_http-server-header: nginx/1.16.1

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 46.64 seconds

nmap --script discovery -p- 192.168.80.62 
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-09 20:23 BST
Pre-scan script results:
|_hostmap-robtex: *TEMPORARILY DISABLED* due to changes in Robtex's API. See https://www.robtex.com/api/
| targets-asn: 
|_  targets-asn.asn is a mandatory parameter
| targets-ipv6-multicast-mld: 
|   IP: fe80::8818:a0ff:fef0:6eff  MAC: 8a:18:a0:f0:6e:ff  IFACE: usb0
| 
|_  Use --script-args=newtargets to add the results as targets
| ipv6-multicast-mld-list: 
|   fe80::8818:a0ff:fef0:6eff: 
|     device: usb0
|     mac: 8a:18:a0:f0:6e:ff
|     multicast_ips: 
|       ff02::1:fff0:6eff         (NDP Solicited-node)
|       ff02::1:ff00:0            (Solicited-Node Address)
|       ff05::2                   (unknown)
|       ff02::2                   (All Routers Address)
|_      ff02::1:ff00:1a           (Solicited-Node Address)
|_http-robtex-shared-ns: *TEMPORARILY DISABLED* due to changes in Robtex's API. See https://www.robtex.com/api/
| targets-ipv6-multicast-invalid-dst: 
|   IP: fe80::8818:a0ff:fef0:6eff  MAC: 8a:18:a0:f0:6e:ff  IFACE: usb0
|   IP: 2a01:4c8:1483:76b5::1a     MAC: 8a:18:a0:f0:6e:ff  IFACE: usb0
|_  Use --script-args=newtargets to add the results as targets
| targets-ipv6-multicast-echo: 
|   IP: 2a01:4c8:1483:76b5::1a     MAC: 8a:18:a0:f0:6e:ff  IFACE: usb0
|   IP: fe80::8818:a0ff:fef0:6eff  MAC: 8a:18:a0:f0:6e:ff  IFACE: usb0
|_  Use --script-args=newtargets to add the results as targets
Nmap scan report for 192.168.80.62
Host is up (0.045s latency).
Not shown: 65529 filtered tcp ports (no-response)
PORT     STATE SERVICE
22/tcp   open  ssh
|_banner: SSH-2.0-OpenSSH_7.4
| ssh-hostkey: 
|   2048 44:7d:1a:56:9b:68:ae:f5:3b:f6:38:17:73:16:5d:75 (RSA)
|   256 1c:78:9d:83:81:52:f4:b0:1d:8e:32:03:cb:a6:18:93 (ECDSA)
|_  256 08:c9:12:d9:7b:98:98:c8:b3:99:7a:19:82:2e:a3:ea (ED25519)
| ssh2-enum-algos: 
|   kex_algorithms: (12)
|   server_host_key_algorithms: (5)
|   encryption_algorithms: (12)
|   mac_algorithms: (10)
|_  compression_algorithms: (2)
53/tcp   open  domain
|_dns-nsec-enum: Can't determine domain for host 192.168.80.62; use dns-nsec-enum.domains script arg.
|_dns-nsec3-enum: Can't determine domain for host 192.168.80.62; use dns-nsec3-enum.domains script arg.
80/tcp   open  http
| http-vhosts: 
|_128 names had status 200
| http-enum: 
|   /blog/: Blog
|   /contact/: Potentially interesting folder
|_  /search/: Potentially interesting folder
|_http-title: Home | Mezzanine
| http-sitemap-generator: 
|   Directory structure:
|     /
|       Other: 1
|     /about/
|       Other: 1
|     /admin/
|       Other: 1
|     /blog/feeds/rss/
|       Other: 1
|     /static/css/
|       css: 2
|     /static/js/
|       js: 1
|   Longest directory structure:
|     Depth: 3
|     Dir: /blog/feeds/rss/
|   Total files found (by extension):
|_    Other: 4; css: 2; js: 1
|_http-errors: Couldn't find any error pages.
|_http-chrono: Request times for /; avg: 437.03ms; min: 246.08ms; max: 910.68ms
| http-comments-displayer: 
| Spidering limited to: maxdepth=3; maxpagecount=20; withinhost=192.168.80.62
|     
|     Path: http://192.168.80.62:80/static/js/respond.min.js
|     Line number: 1
|     Comment: 
|         /*! matchMedia() polyfill - Test a CSS media type/query in JS. Authors & copyright (c) 2012: Scott Jehl, Paul Irish, Nicholas Zakas. Dual MIT/BSD license */
|     
|     Path: http://192.168.80.62:80/static/css/mezzanine.css
|     Line number: 472
|     Comment: 
|         /* Display dropdowns on hover only in desktops (md and lg classes) */
|     
|     Path: http://192.168.80.62:80/admin/
|     Line number: 84
|     Comment: 
|         <!-- HEADER -->
|     
|     Path: http://192.168.80.62:80/static/css/mezzanine.css
|     Line number: 176
|     Comment: 
|         /*========================================
|                     MEZZANINE BLOG
|         ==========================================*/
|     
|     Path: http://192.168.80.62:80/admin/
|     Line number: 10
|     Comment: 
|         <!-- STYLESHEETS / EXTRASTYLE -->
|     
|     Path: http://192.168.80.62:80/admin/
|     Line number: 61
|     Comment: 
|          // https://github.com/stephenmcd/grappelli-safe/pull/56/files occurring.
|     
|     Path: http://192.168.80.62:80/static/css/mezzanine.css
|     Line number: 128
|     Comment: 
|         /*========================================
|                   MULTI-LEVEL DROP-DOWN NAV
|         ==========================================*/
|     
|     Path: http://192.168.80.62:80/static/css/mezzanine.css
|     Line number: 430
|     Comment: 
|         /*========================================
|                   MEZZANINE TWEETS
|         ==========================================*/
|     
|     Path: http://192.168.80.62:80/static/css/mezzanine.css
|     Line number: 226
|     Comment: 
|         /*========================================
|                     MEZZANINE FORMS
|         ==========================================
|           These rules mirror the rules for .form-control included with
|           Bootstrap. They are needed because we can not directly apply
|           CSS classes to the form fields when rendering them in the template.
|           They also cover special cases for date and date/time inputs.
|         */
|     
|     Path: http://192.168.80.62:80/static/css/mezzanine.css
|     Line number: 400
|     Comment: 
|         /*========================================
|                   MEZZANINE GENERIC
|         ==========================================*/
|     
|     Path: http://192.168.80.62:80/static/css/mezzanine.css
|     Line number: 389
|     Comment: 
|         /*========================================
|                   MEZZANINE GALLERY
|         ==========================================*/
|     
|     Path: http://192.168.80.62:80/static/js/respond.min.js
|     Line number: 2
|     Comment: 
|         /*! NOTE: If you're already including a window.matchMedia polyfill via Modernizr or otherwise, you don't need this part */
|     
|     Path: http://192.168.80.62:80/about/
|     Line number: 34
|     Comment: 
|         <!--[if lt IE 9]>
|         <script src="/static/js/html5shiv.js"></script>
|         <script src="/static/js/respond.min.js"></script>
|         <![endif]-->
|     
|     Path: http://192.168.80.62:80/admin/
|     Line number: 51
|     Comment: 
|          // Ensures jQuery does not pollute the global namespace
|     
|     Path: http://192.168.80.62:80/static/css/mezzanine.css
|     Line number: 2
|     Comment: 
|         /*========================================
|                 MEZZANINE GENERAL STYLES
|         ==========================================*/
|     
|     Path: http://192.168.80.62:80/admin/
|     Line number: 7
|     Comment: 
|         <!-- LOADING -->
|     
|     Path: http://192.168.80.62:80/admin/
|     Line number: 26
|     Comment: 
|         <!-- JAVASCRIPTS / EXTRAHEAD -->
|     
|     Path: http://192.168.80.62:80/admin/
|     Line number: 97
|     Comment: 
|         <!-- BREADCRUMBS -->
|     
|     Path: http://192.168.80.62:80/static/css/mezzanine.css
|     Line number: 468
|     Comment: 
|         /*========================================
|                   RESPONSIVE TWEAKS
|         ==========================================*/
|     
|     Path: http://192.168.80.62:80/admin/
|     Line number: 81
|     Comment: 
|         <!-- CONTAINER -->
|     
|     Path: http://192.168.80.62:80/static/css/mezzanine.css
|     Line number: 460
|     Comment: 
|         /*========================================
|                   MEZZANINE ACCOUNTS
|         ==========================================*/
|     
|     Path: http://192.168.80.62:80/static/css/mezzanine.css
|     Line number: 106
|     Comment: 
|         /* Style error messages as danger alerts */
|     
|     Path: http://192.168.80.62:80/admin/
|     Line number: 100
|     Comment: 
|         <!-- MESSAGES -->
|     
|     Path: http://192.168.80.62:80/admin/
|     Line number: 60
|     Comment: 
|          // This line can be removed after a decent amount of time has passed since
|     
|     Path: http://192.168.80.62:80/static/css/bootstrap-theme.css
|     Line number: 1
|     Comment: 
|         /*!
|          * Bootstrap v3.2.0 (http://getbootstrap.com)
|          * Copyright 2011-2014 Twitter, Inc.
|          * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
|          */
|     
|     Path: http://192.168.80.62:80/static/css/mezzanine.css
|     Line number: 377
|     Comment: 
|         /* We apply the clearfix hack to .form-actions because we often
|         float the buttons inside it. This prevents collapsing. */
|     
|     Path: http://192.168.80.62:80/admin/
|     Line number: 152
|     Comment: 
|         <!-- FOOTER -->
|     
|     Path: http://192.168.80.62:80/admin/
|     Line number: 90
|     Comment: 
|         <!-- Title -->
|     
|     Path: http://192.168.80.62:80/admin/
|     Line number: 107
|     Comment: 
|         <!-- CONTENT -->
|     
|     Path: http://192.168.80.62:80/admin/
|     Line number: 17
|     Comment: 
|         /* These are set in PageAdmin's view methods, and mezzanine.utils.admin.SingletonAdmin */
|     
|     Path: http://192.168.80.62:80/static/js/respond.min.js
|     Line number: 5
|     Comment: 
|         /*! Respond.js v1.3.0: min/max-width media query polyfill. (c) Scott Jehl. MIT/GPLv2 Lic. j.mp/respondjs  */
|     
|     Path: http://192.168.80.62:80/static/css/bootstrap-theme.css
|     Line number: 442
|     Comment: 
|_        /*# sourceMappingURL=bootstrap-theme.css.map */
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
|_http-referer-checker: Couldn't find any cross-domain scripts.
| http-auth-finder: 
| Spidering limited to: maxdepth=3; maxpagecount=20; withinhost=192.168.80.62
|   url                             method
|_  http://192.168.80.62:80/admin/  FORM
| http-headers: 
|   Server: nginx/1.16.1
|   Date: Mon, 09 May 2022 19:27:01 GMT
|   Content-Type: text/html; charset=utf-8
|   Content-Length: 6927
|   Connection: close
|   X-Frame-Options: SAMEORIGIN
|   Vary: Cookie
|   
|_  (Request type: HEAD)
|_http-devframework: Django detected. Found Django admin login page on /admin/
|_http-date: Mon, 09 May 2022 19:26:34 GMT; +59m59s from local time.
|_http-mobileversion-checker: No mobile version detected.
| http-security-headers: 
|   X_Frame_Options: 
|     Header: X-Frame-Options: SAMEORIGIN
|_    Description: The browser must not display this content in any frame from a page of different origin than the content itself.
|_http-xssed: No previously reported XSS vuln.
| http-feed: 
| Spidering limited to: maxpagecount=40; withinhost=192.168.80.62
|   Found the following feeds: 
|     Atom: /blog/feeds/atom/
|     RSS (version 2.0): /blog/feeds/rss/
|_    RSS (version 2.0): http://192.168.80.62:80/blog/feeds/rss/
4505/tcp open  unknown
|_banner: \xFF\x00\x00\x00\x00\x00\x00\x00\x01\x7F
4506/tcp open  unknown
|_banner: \xFF\x00\x00\x00\x00\x00\x00\x00\x01\x7F
8000/tcp open  http-alt
| http-enum: 
|   /login/: Login page
|   /index/: Potentially interesting folder
|_  /stats/: Potentially interesting folder (401 Unauthorized)
|_http-date: Mon, 09 May 2022 19:26:34 GMT; +59m59s from local time.
|_http-title: Site doesn't have a title (application/json).
| http-headers: 
|   Server: nginx/1.16.1
|   Date: Mon, 09 May 2022 19:26:34 GMT
|   Content-Type: application/json
|   Content-Length: 146
|   Connection: close
|   Access-Control-Expose-Headers: GET, POST
|   Vary: Accept-Encoding
|   Allow: GET, HEAD, POST
|   Access-Control-Allow-Credentials: true
|   Access-Control-Allow-Origin: *
|   X-Upstream: salt-api/3000-1
|   
|_  (Request type: HEAD)
| http-waf-detect: IDS/IPS/WAF detected:
|_192.168.80.62:8000/?p4yl04d3=<script>alert(document.cookie)</script>
|_http-chrono: Request times for /; avg: 191.15ms; min: 153.09ms; max: 218.16ms
|_http-open-proxy: Proxy might be redirecting requests
| http-vhosts: 
|_128 names had status 200

Host script results:
|_ipidseq: ERROR: Script execution failed (use -d to debug)
|_dns-brute: Can't guess domain of "192.168.80.62"; use dns-brute.domain script argument.
| qscan: 
| PORT  FAMILY  MEAN (us)  STDDEV    LOSS (%)
| 22    0       43649.50   7526.28   0.0%
| 53    0       50106.50   22059.82  0.0%
| 80    0       43204.30   4432.38   0.0%
| 4505  0       43460.90   3605.51   0.0%
| 4506  0       41754.90   4864.24   0.0%
|_8000  0       41228.40   6283.60   0.0%
|_path-mtu: PMTU == 1500
|_fcrdns: FAIL (No PTR record)

Nmap done: 1 IP address (1 host up) scanned in 425.85 seconds


nikto -h 192.168.80.62                   
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          192.168.80.62
+ Target Hostname:    192.168.80.62
+ Target Port:        80
+ Start Time:         2022-05-09 20:48:46 (GMT1)
---------------------------------------------------------------------------
+ Server: nginx/1.16.1
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ OSVDB-3092: /sitemap.xml: This gives a nice listing of the site content.
+ 7916 requests: 0 error(s) and 3 item(s) reported on remote host
+ End Time:           2022-05-09 20:56:28 (GMT1) (462 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested


nikto -h 192.168.80.62:8000 
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          192.168.80.62
+ Target Hostname:    192.168.80.62
+ Target Port:        8000
+ Start Time:         2022-05-09 20:57:20 (GMT1)
---------------------------------------------------------------------------
+ Server: nginx/1.16.1
+ Retrieved access-control-allow-origin header: *
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ Uncommon header 'x-upstream' found, with contents: salt-api/3000-1
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ Cookie session_id created without the httponly flag
+ Allowed HTTP Methods: GET, HEAD, POST 
+ OSVDB-3092: /login/: This might be interesting...
+ 7917 requests: 0 error(s) and 8 item(s) reported on remote host
+ End Time:           2022-05-09 21:04:37 (GMT1) (437 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested

```
