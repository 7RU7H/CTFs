

## Recon

```bash
ping -c 3 192.168.80.161
PING 192.168.80.161 (192.168.80.161) 56(84) bytes of data.
64 bytes from 192.168.80.161: icmp_seq=1 ttl=63 time=42.2 ms
64 bytes from 192.168.80.161: icmp_seq=2 ttl=63 time=39.6 ms
64 bytes from 192.168.80.161: icmp_seq=3 ttl=63 time=39.9 ms

--- 192.168.80.161 ping statistics ---
3 packets transmitted, 3 received, 0% packet loss, time 2002ms
rtt min/avg/max/mdev = 39.587/40.575/42.201/1.158 ms

nmap -Pn -sC -sV -p- 192.168.80.161 --min-rate 5000
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-06 17:03 BST
Nmap scan report for 192.168.80.161
Host is up (0.13s latency).
Not shown: 65527 closed tcp ports (reset)
PORT     STATE SERVICE       VERSION
22/tcp   open  ssh           OpenSSH 7.9p1 Debian 10+deb10u2 (protocol 2.0)
| ssh-hostkey: 
|   2048 74:ba:20:23:89:92:62:02:9f:e7:3d:3b:83:d4:d9:6c (RSA)
|   256 54:8f:79:55:5a:b0:3a:69:5a:d5:72:39:64:fd:07:4e (ECDSA)
|_  256 7f:5d:10:27:62:ba:75:e9:bc:c8:4f:e2:72:87:d4:e2 (ED25519)
25/tcp   open  smtp          Exim smtpd
| smtp-commands: muddy Hello nmap.scanme.org [192.168.49.80], SIZE 52428800, 8BITMIME, PIPELINING, CHUNKING, PRDR, HELP
|_ Commands supported: AUTH HELO EHLO MAIL RCPT DATA BDAT NOOP QUIT RSET HELP
80/tcp   open  http          Apache httpd 2.4.38 ((Debian))
|_http-server-header: Apache/2.4.38 (Debian)
|_http-title: Did not follow redirect to http://muddy.ugc/
111/tcp  open  rpcbind       2-4 (RPC #100000)
| rpcinfo: 
|   program version    port/proto  service
|   100000  2,3,4        111/tcp   rpcbind
|   100000  2,3,4        111/udp   rpcbind
|   100000  3,4          111/tcp6  rpcbind
|_  100000  3,4          111/udp6  rpcbind
443/tcp  open  https?
808/tcp  open  ccproxy-http?
908/tcp  open  unknown
8888/tcp open  http          WSGIServer 0.1 (Python 2.7.16)
|_http-server-header: WSGIServer/0.1 Python/2.7.16
|_http-title: Ladon Service Catalog
Service Info: OS: Linux; CPE: cpe:/o:linux:linux_kernel

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 71.98 seconds

nmap --script discovery -p 22,25,80,111,443,808,908,8888 192.168.80.161
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-06 17:18 BST
Pre-scan script results:
| targets-asn: 
|_  targets-asn.asn is a mandatory parameter
| targets-ipv6-multicast-mld: 
|   IP: fe80::304d:a0ff:fe05:7e40  MAC: 32:4d:a0:05:7e:40  IFACE: usb0
| 
|_  Use --script-args=newtargets to add the results as targets
|_http-robtex-shared-ns: *TEMPORARILY DISABLED* due to changes in Robtex's API. See https://www.robtex.com/api/
| ipv6-multicast-mld-list: 
|   fe80::304d:a0ff:fe05:7e40: 
|     device: usb0
|     mac: 32:4d:a0:05:7e:40
|     multicast_ips: 
|       ff02::1:ff05:7e40         (NDP Solicited-node)
|       ff02::1:ff00:0            (Solicited-Node Address)
|       ff05::2                   (unknown)
|       ff02::2                   (All Routers Address)
|_      ff02::1:ff00:6a           (Solicited-Node Address)
| targets-ipv6-multicast-invalid-dst: 
|   IP: 2a01:4c8:1401:4bb::6a      MAC: 32:4d:a0:05:7e:40  IFACE: usb0
|   IP: fe80::304d:a0ff:fe05:7e40  MAC: 32:4d:a0:05:7e:40  IFACE: usb0
|_  Use --script-args=newtargets to add the results as targets
| targets-ipv6-multicast-echo: 
|   IP: 2a01:4c8:1401:4bb::6a      MAC: 32:4d:a0:05:7e:40  IFACE: usb0
|   IP: fe80::304d:a0ff:fe05:7e40  MAC: 32:4d:a0:05:7e:40  IFACE: usb0
|_  Use --script-args=newtargets to add the results as targets
|_hostmap-robtex: *TEMPORARILY DISABLED* due to changes in Robtex's API. See https://www.robtex.com/api/
Nmap scan report for 192.168.80.161
Host is up (0.30s latency).

Bug in http-security-headers: no string output.
PORT     STATE  SERVICE
22/tcp   open   ssh
| ssh-hostkey: 
|   2048 74:ba:20:23:89:92:62:02:9f:e7:3d:3b:83:d4:d9:6c (RSA)
|   256 54:8f:79:55:5a:b0:3a:69:5a:d5:72:39:64:fd:07:4e (ECDSA)
|_  256 7f:5d:10:27:62:ba:75:e9:bc:c8:4f:e2:72:87:d4:e2 (ED25519)
|_banner: SSH-2.0-OpenSSH_7.9p1 Debian-10+deb10u2
| ssh2-enum-algos: 
|   kex_algorithms: (10)
|   server_host_key_algorithms: (5)
|   encryption_algorithms: (6)
|   mac_algorithms: (10)
|_  compression_algorithms: (2)
25/tcp   open   smtp
|_smtp-open-relay: SMTP RSET: failed to receive data: connection closed
| smtp-commands: muddy Hello nmap.scanme.org [192.168.49.80], SIZE 52428800, 8BITMIME, PIPELINING, CHUNKING, PRDR, HELP
|_ Commands supported: AUTH HELO EHLO MAIL RCPT DATA BDAT NOOP QUIT RSET HELP
80/tcp   open   http
| http-wordpress-enum: 
| Search limited to top 100 themes/plugins
|   plugins
|     akismet 4.1.9
|_    jetpack 9.4
| http-useragent-tester: 
|   Status for browser useragent: false
|   Redirected To: http://muddy.ugc/
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
| http-sitemap-generator: 
|   Directory structure:
|   Longest directory structure:
|     Depth: 0
|     Dir: /
|   Total files found (by extension):
|_    
| http-headers: 
|   Date: Fri, 06 May 2022 16:19:31 GMT
|   Server: Apache/2.4.38 (Debian)
|   X-Redirect-By: WordPress
|   Location: http://muddy.ugc/
|   Content-Length: 0
|   Connection: close
|   Content-Type: text/html; charset=UTF-8
|   
|_  (Request type: GET)
| http-enum: 
|   /readme.html: Wordpress version: 2 
|   /wp-includes/images/rss.png: Wordpress version 2.2 found.
|   /wp-includes/js/jquery/suggest.js: Wordpress version 2.5 found.
|   /wp-includes/images/blank.gif: Wordpress version 2.6 found.
|   /wp-includes/js/comment-reply.js: Wordpress version 2.7 found.
|   /wp-admin/upgrade.php: Wordpress login page.
|   /readme.html: Interesting, a readme.
|_  /webdav/: Potentially interesting folder (401 Unauthorized)
|_http-feed: Couldn't find any feeds.
|_http-title: Did not follow redirect to http://muddy.ugc/
|_http-comments-displayer: Couldn't find any comments.
|_http-devframework: Couldn't determine the underlying framework or CMS. Try increasing 'httpspider.maxpagecount' value to spider more pages.
|_http-errors: Couldn't find any error pages.
|_http-mobileversion-checker: No mobile version detected.
|_http-chrono: Request times for /; avg: 1.95ms; min: 1.59ms; max: 2.15ms
|_http-referer-checker: Couldn't find any cross-domain scripts.
|_http-date: Fri, 06 May 2022 16:19:28 GMT; +59m59s from local time.
| http-waf-detect: IDS/IPS/WAF detected:
|_192.168.80.161:80/?p4yl04d3=<script>alert(document.cookie)</script>
|_http-xssed: No previously reported XSS vuln.
| http-vhosts: 
|_128 names had status 301
111/tcp  open   rpcbind
| rpcinfo: 
|   program version    port/proto  service
|   100000  2,3,4        111/tcp   rpcbind
|   100000  2,3,4        111/udp   rpcbind
|   100000  3,4          111/tcp6  rpcbind
|_  100000  3,4          111/udp6  rpcbind
443/tcp  closed https
808/tcp  closed ccproxy-http
908/tcp  closed unknown
8888/tcp open   sun-answerbook

Host script results:
|_path-mtu: PMTU == 1500
| qscan: 
| PORT  FAMILY  MEAN (us)  STDDEV     LOSS (%)
| 22    0       51205.00   33836.22   0.0%
| 25    0       103260.90  154213.50  0.0%
| 80    0       87396.40   150738.20  0.0%
| 111   0       40106.50   4045.00    0.0%
| 443   0       99592.80   139828.23  0.0%
|_8888  0       41220.33   4579.10    10.0%
|_fcrdns: FAIL (No PTR record)
|_ipidseq: ERROR: Script execution failed (use -d to debug)
|_dns-brute: Can't guess domain of "192.168.80.161"; use dns-brute.domain script argument.

Nmap done: 1 IP address (1 host up) scanned in 329.84 seconds

nikto -h 192.168.80.161                                                
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          192.168.80.161
+ Target Hostname:    192.168.80.161
+ Target Port:        80
+ Start Time:         2022-05-06 17:26:33 (GMT1)
---------------------------------------------------------------------------
+ Server: Apache/2.4.38 (Debian)
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ Uncommon header 'x-redirect-by' found, with contents: WordPress
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ Root page / redirects to: http://muddy.ugc/
+ Uncommon header 'link' found, with contents: <http://muddy.ugc/>; rel=shortlink
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ Web Server returns a valid response with junk HTTP methods, this may cause false positives.
+ DEBUG HTTP verb may show server debugging information. See http://msdn.microsoft.com/en-us/library/e8z01xdh%28VS.80%29.aspx for details.
+ OSVDB-3233: /icons/README: Apache default file found.
+ /wp-content/plugins/akismet/readme.txt: The WordPress Akismet plugin 'Tested up to' version usually matches the WordPress version
+ /wp-links-opml.php: This WordPress script reveals the installed version.
+ OSVDB-3092: /license.txt: License file found may identify site software.
+ Cookie wordpress_test_cookie created without the httponly flag
+ 8069 requests: 0 error(s) and 12 item(s) reported on remote host
+ End Time:           2022-05-06 17:34:43 (GMT1) (490 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested


      *********************************************************************
      Portions of the server's headers (Apache/2.4.38) are not in
      the Nikto 2.1.6 database or are newer than the known string. Would you like
      to submit this information (*no server specific data*) to CIRT.net
      for a Nikto update (or you may email to sullo@cirt.net) (y/n)


nikto -h http://192.168.80.161:8888
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          192.168.80.161
+ Target Hostname:    192.168.80.161
+ Target Port:        8888
+ Start Time:         2022-05-06 18:55:45 (GMT1)
---------------------------------------------------------------------------
+ Server: WSGIServer/0.1 Python/2.7.16
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ Web Server returns a valid response with junk HTTP methods, this may cause false positives.
+ 7929 requests: 12 error(s) and 4 item(s) reported on remote host
+ End Time:           2022-05-06 19:15:38 (GMT1) (1193 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested

# same server headers...

_______________________________________________________________
         __          _______   _____
         \ \        / /  __ \ / ____|
          \ \  /\  / /| |__) | (___   ___  __ _ _ __ ®
           \ \/  \/ / |  ___/ \___ \ / __|/ _` | '_ \
            \  /\  /  | |     ____) | (__| (_| | | | |
             \/  \/   |_|    |_____/ \___|\__,_|_| |_|

         WordPress Security Scanner by the WPScan Team
                         Version 3.8.22
                               
       @_WPScan_, @ethicalhack3r, @erwan_lr, @firefart
_______________________________________________________________

[i] Updating the Database ...
[i] Update completed.

[+] URL: http://muddy.ugc/ [192.168.80.161]
[+] Started: Fri May  6 17:37:09 2022

Interesting Finding(s):

[+] Headers
 | Interesting Entry: Server: Apache/2.4.38 (Debian)
 | Found By: Headers (Passive Detection)
 | Confidence: 100%

[+] XML-RPC seems to be enabled: http://muddy.ugc/xmlrpc.php
 | Found By: Link Tag (Passive Detection)
 | Confidence: 100%
 | Confirmed By: Direct Access (Aggressive Detection), 100% confidence
 | References:
 |  - http://codex.wordpress.org/XML-RPC_Pingback_API
 |  - https://www.rapid7.com/db/modules/auxiliary/scanner/http/wordpress_ghost_scanner/
 |  - https://www.rapid7.com/db/modules/auxiliary/dos/http/wordpress_xmlrpc_dos/
 |  - https://www.rapid7.com/db/modules/auxiliary/scanner/http/wordpress_xmlrpc_login/
 |  - https://www.rapid7.com/db/modules/auxiliary/scanner/http/wordpress_pingback_access/

[+] WordPress readme found: http://muddy.ugc/readme.html
 | Found By: Direct Access (Aggressive Detection)
 | Confidence: 100%

[+] The external WP-Cron seems to be enabled: http://muddy.ugc/wp-cron.php
 | Found By: Direct Access (Aggressive Detection)
 | Confidence: 60%
 | References:
 |  - https://www.iplocation.net/defend-wordpress-from-ddos
 |  - https://github.com/wpscanteam/wpscan/issues/1299

[+] WordPress version 5.7 identified (Insecure, released on 2021-03-09).
 | Found By: Rss Generator (Passive Detection)
 |  - http://muddy.ugc/index.php/feed/, <generator>https://wordpress.org/?v=5.7</generator>
 |  - http://muddy.ugc/index.php/comments/feed/, <generator>https://wordpress.org/?v=5.7</generator>
 |
 | [!] 13 vulnerabilities identified:
 |
 | [!] Title: WordPress 5.6-5.7 - Authenticated XXE Within the Media Library Affecting PHP 8
 |     Fixed in: 5.7.1
 |     References:
 |      - https://wpscan.com/vulnerability/cbbe6c17-b24e-4be4-8937-c78472a138b5
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2021-29447
 |      - https://wordpress.org/news/2021/04/wordpress-5-7-1-security-and-maintenance-release/
 |      - https://core.trac.wordpress.org/changeset/29378
 |      - https://blog.wpscan.com/2021/04/15/wordpress-571-security-vulnerability-release.html
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-rv47-pc52-qrhh
 |      - https://blog.sonarsource.com/wordpress-xxe-security-vulnerability/
 |      - https://hackerone.com/reports/1095645
 |      - https://www.youtube.com/watch?v=3NBxcmqCgt4
 |
 | [!] Title: WordPress 4.7-5.7 - Authenticated Password Protected Pages Exposure
 |     Fixed in: 5.7.1
 |     References:
 |      - https://wpscan.com/vulnerability/6a3ec618-c79e-4b9c-9020-86b157458ac5
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2021-29450
 |      - https://wordpress.org/news/2021/04/wordpress-5-7-1-security-and-maintenance-release/
 |      - https://blog.wpscan.com/2021/04/15/wordpress-571-security-vulnerability-release.html
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-pmmh-2f36-wvhq
 |      - https://core.trac.wordpress.org/changeset/50717/
 |      - https://www.youtube.com/watch?v=J2GXmxAdNWs
 |
 | [!] Title: WordPress 3.7 to 5.7.1 - Object Injection in PHPMailer
 |     Fixed in: 5.7.2
 |     References:
 |      - https://wpscan.com/vulnerability/4cd46653-4470-40ff-8aac-318bee2f998d
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2020-36326
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2018-19296
 |      - https://github.com/WordPress/WordPress/commit/267061c9595fedd321582d14c21ec9e7da2dcf62
 |      - https://wordpress.org/news/2021/05/wordpress-5-7-2-security-release/
 |      - https://github.com/PHPMailer/PHPMailer/commit/e2e07a355ee8ff36aba21d0242c5950c56e4c6f9
 |      - https://www.wordfence.com/blog/2021/05/wordpress-5-7-2-security-release-what-you-need-to-know/
 |      - https://www.youtube.com/watch?v=HaW15aMzBUM
 |
 | [!] Title: WordPress 5.4 to 5.8 -  Lodash Library Update
 |     Fixed in: 5.7.3
 |     References:
 |      - https://wpscan.com/vulnerability/5d6789db-e320-494b-81bb-e678674f4199
 |      - https://wordpress.org/news/2021/09/wordpress-5-8-1-security-and-maintenance-release/
 |      - https://github.com/lodash/lodash/wiki/Changelog
 |      - https://github.com/WordPress/wordpress-develop/commit/fb7ecd92acef6c813c1fde6d9d24a21e02340689
 |
 | [!] Title: WordPress 5.4 to 5.8 - Authenticated XSS in Block Editor
 |     Fixed in: 5.7.3
 |     References:
 |      - https://wpscan.com/vulnerability/5b754676-20f5-4478-8fd3-6bc383145811
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2021-39201
 |      - https://wordpress.org/news/2021/09/wordpress-5-8-1-security-and-maintenance-release/
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-wh69-25hr-h94v
 |
 | [!] Title: WordPress 5.4 to 5.8 - Data Exposure via REST API
 |     Fixed in: 5.7.3
 |     References:
 |      - https://wpscan.com/vulnerability/38dd7e87-9a22-48e2-bab1-dc79448ecdfb
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2021-39200
 |      - https://wordpress.org/news/2021/09/wordpress-5-8-1-security-and-maintenance-release/
 |      - https://github.com/WordPress/wordpress-develop/commit/ca4765c62c65acb732b574a6761bf5fd84595706
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-m9hc-7v5q-x8q5
 |
 | [!] Title: WordPress < 5.8.2 - Expired DST Root CA X3 Certificate
 |     Fixed in: 5.7.4
 |     References:
 |      - https://wpscan.com/vulnerability/cc23344a-5c91-414a-91e3-c46db614da8d
 |      - https://wordpress.org/news/2021/11/wordpress-5-8-2-security-and-maintenance-release/
 |      - https://core.trac.wordpress.org/ticket/54207
 |
 | [!] Title: WordPress < 5.8.3 - SQL Injection via WP_Query
 |     Fixed in: 5.7.5
 |     References:
 |      - https://wpscan.com/vulnerability/7f768bcf-ed33-4b22-b432-d1e7f95c1317
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2022-21661
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-6676-cqfm-gw84
 |      - https://hackerone.com/reports/1378209
 |
 | [!] Title: WordPress < 5.8.3 - Author+ Stored XSS via Post Slugs
 |     Fixed in: 5.7.5
 |     References:
 |      - https://wpscan.com/vulnerability/dc6f04c2-7bf2-4a07-92b5-dd197e4d94c8
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2022-21662
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-699q-3hj9-889w
 |      - https://hackerone.com/reports/425342
 |      - https://blog.sonarsource.com/wordpress-stored-xss-vulnerability
 |
 | [!] Title: WordPress 4.1-5.8.2 - SQL Injection via WP_Meta_Query
 |     Fixed in: 5.7.5
 |     References:
 |      - https://wpscan.com/vulnerability/24462ac4-7959-4575-97aa-a6dcceeae722
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2022-21664
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-jp3p-gw8h-6x86
 |
 | [!] Title: WordPress < 5.8.3 - Super Admin Object Injection in Multisites
 |     Fixed in: 5.7.5
 |     References:
 |      - https://wpscan.com/vulnerability/008c21ab-3d7e-4d97-b6c3-db9d83f390a7
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2022-21663
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-jmmq-m8p8-332h
 |      - https://hackerone.com/reports/541469
 |
 | [!] Title: WordPress < 5.9.2 - Prototype Pollution in jQuery
 |     Fixed in: 5.7.6
 |     References:
 |      - https://wpscan.com/vulnerability/1ac912c1-5e29-41ac-8f76-a062de254c09
 |      - https://wordpress.org/news/2022/03/wordpress-5-9-2-security-maintenance-release/
 |
 | [!] Title: WordPress < 5.9.2 / Gutenberg < 12.7.2 - Prototype Pollution via Gutenberg’s wordpress/url package
 |     Fixed in: 5.7.6
 |     References:
 |      - https://wpscan.com/vulnerability/6e61b246-5af1-4a4f-9ca8-a8c87eb2e499
 |      - https://wordpress.org/news/2022/03/wordpress-5-9-2-security-maintenance-release/
 |      - https://github.com/WordPress/gutenberg/pull/39365/files

[+] WordPress theme in use: shapely
 | Location: http://muddy.ugc/wp-content/themes/shapely/
 | Last Updated: 2021-08-26T00:00:00.000Z
 | Readme: http://muddy.ugc/wp-content/themes/shapely/readme.txt
 | [!] The version is out of date, the latest version is 1.2.14
 | Style URL: http://muddy.ugc/wp-content/themes/shapely/style.css?ver=5.7
 | Style Name: Shapely
 | Style URI: https://colorlib.com/wp/themes/shapely
 | Description: Shapely is a powerful and versatile one page WordPress theme with pixel perfect design and outstandi...
 | Author: colorlib
 | Author URI: https://colorlib.com/
 |
 | Found By: Css Style In Homepage (Passive Detection)
 |
 | Version: 1.2.11 (80% confidence)
 | Found By: Style (Passive Detection)
 |  - http://muddy.ugc/wp-content/themes/shapely/style.css?ver=5.7, Match: 'Version: 1.2.11'

[+] Enumerating Vulnerable Plugins (via Passive Methods)
[+] Checking Plugin Versions (via Passive and Aggressive Methods)

[i] No plugins Found.

[+] Enumerating Vulnerable Themes (via Passive and Aggressive Methods)
 Checking Known Locations - Time: 00:00:04 <======================> (468 / 468) 100.00% Time: 00:00:04
[+] Checking Theme Versions (via Passive and Aggressive Methods)

[i] No themes Found.

[+] Enumerating Timthumbs (via Passive and Aggressive Methods)
 Checking Known Locations - Time: 00:00:26 <====================> (2575 / 2575) 100.00% Time: 00:00:26

[i] No Timthumbs Found.

[+] Enumerating Config Backups (via Passive and Aggressive Methods)
 Checking Config Backups - Time: 00:00:01 <=======================> (137 / 137) 100.00% Time: 00:00:01

[i] No Config Backups Found.

[+] Enumerating DB Exports (via Passive and Aggressive Methods)
 Checking DB Exports - Time: 00:00:00 <=============================> (71 / 71) 100.00% Time: 00:00:00

[i] No DB Exports Found.

[+] Enumerating Medias (via Passive and Aggressive Methods) (Permalink setting must be set to "Plain" for those to be detected)
 Brute Forcing Attachment IDs - Time: 00:00:02 <==================> (100 / 100) 100.00% Time: 00:00:02

[i] No Medias Found.

[+] Enumerating Users (via Passive and Aggressive Methods)
 Brute Forcing Author IDs - Time: 00:00:00 <========================> (10 / 10) 100.00% Time: 00:00:00

[i] No Users Found.

[+] WPScan DB API OK
 | Plan: free
 | Requests Done (during the scan): 3
 | Requests Remaining: 72

[+] Finished: Fri May  6 17:37:51 2022
[+] Requests Done: 3427
[+] Cached Requests: 11
[+] Data Sent: 928.269 KB
[+] Data Received: 18.984 MB
[+] Memory used: 283.344 MB
[+] Elapsed time: 00:00:41

gobuster dir -u http://muddy.ugc/ -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt -x php
===============================================================
Gobuster v3.1.0
by OJ Reeves (@TheColonial) & Christian Mehlmauer (@firefart)
===============================================================
[+] Url:                     http://muddy.ugc/
[+] Method:                  GET
[+] Threads:                 10
[+] Wordlist:                /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt
[+] Negative Status codes:   404
[+] User Agent:              gobuster/3.1.0
[+] Extensions:              php
[+] Timeout:                 10s
===============================================================
2022/05/06 17:39:19 Starting gobuster in directory enumeration mode
===============================================================
/index.php            (Status: 301) [Size: 0] [--> http://muddy.ugc/]
/wp-content           (Status: 301) [Size: 311] [--> http://muddy.ugc/wp-content/]
/wp-login.php         (Status: 302) [Size: 0] [--> http://muddy.ugc/404]          
/wp-includes          (Status: 301) [Size: 312] [--> http://muddy.ugc/wp-includes/]
/javascript           (Status: 301) [Size: 311] [--> http://muddy.ugc/javascript/] 
/wp-trackback.php     (Status: 200) [Size: 135]                                    
/wp-admin             (Status: 301) [Size: 309] [--> http://muddy.ugc/wp-admin/]   
/xmlrpc.php           (Status: 405) [Size: 42]                                     
/webdav               (Status: 401) [Size: 456]                                    
/wp-signup.php        (Status: 302) [Size: 0] [--> http://muddy.ugc/wp-login.php?action=register]
/server-status        (Status: 403) [Size: 274]                                                  
                                                                                                 
===============================================================
2022/05/06 18:17:59 Finished
===============================================================

```
## Follow-up Recon

SMTP
```msfconsole
msf6 auxiliary(scanner/smtp/smtp_enum) > options

Module options (auxiliary/scanner/smtp/smtp_enum):

   Name       Current Setting                                                Required  Description
   ----       ---------------                                                --------  -----------
   RHOSTS     192.168.80.161                                                 yes       The target host(s), see https://github.com/rapid7/metasploit-framework/wiki/Using-Metasploit
   RPORT      25                                                             yes       The target port (TCP)
   THREADS    1                                                              yes       The number of concurrent threads (max one per host)
   UNIXONLY   true                                                           yes       Skip Microsoft bannered servers when testing unix users
   USER_FILE  /usr/share/metasploit-framework/data/wordlists/unix_users.txt  yes       The file that contains a list of probable users accounts.

[+] 192.168.80.161:25     - 192.168.80.161:25 Users found: _apt, backup, bin, daemon, games, gnats, irc, list, lp, mail, man, messagebus, mysql, postmaster, proxy, sshd, sync, sys, systemd-coredump, systemd-network, systemd-resolve, systemd-timesync, uucp, www-data

```

NFS on port 111

```bash
showmount -e 192.168.80.161  
clnt_create: RPC: Program not registered
showmount -e 192.168.80.161:111
clnt_create: RPC: Unknown host
```

Wordpress

```bash
curl -X POST "http://muddy.ugc/xmlrpc.php"
<?xml version="1.0" encoding="UTF-8"?>
<methodResponse>
  <fault>
    <value>
      <struct>
        <member>
          <name>faultCode</name>
          <value><int>-32700</int></value>
        </member>
        <member>
          <name>faultString</name>
          <value><string>parse error. not well formed</string></value>
        </member>
      </struct>
    </value>
  </fault>
</methodResponse>
                  
# ghostscanner

msf6 auxiliary(scanner/http/wordpress_ghost_scanner) > exploit

[-] Looks like this site is no WordPress blog
[*] Scanned 1 of 1 hosts (100% complete)
[*] Auxiliary module execution completed

http://muddy.ugc/wp-trackback.php
<response>
<error>1</error>
<message>I really need an ID for this to work.</message>
</response>


```

http://192.168.80.161:8888/
[uses](https://pypi.org/project/ladon/)
