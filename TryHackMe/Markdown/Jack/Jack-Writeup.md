



## Recon
```bash
ping -c 3 10.10.113.121    
PING 10.10.113.121 (10.10.113.121) 56(84) bytes of data.
64 bytes from 10.10.113.121: icmp_seq=1 TTL=63 time=62.1 ms
64 bytes from 10.10.113.121: icmp_seq=2 TTL=63 time=52.0 ms
64 bytes from 10.10.113.121: icmp_seq=3 TTL=63 time=62.0 ms

--- 10.10.113.121 ping statistics ---
3 packets transmitted, 3 received, 0% packet loss, time 2002ms
rtt min/avg/max/mdev = 52.035/58.709/62.107/4.719 ms

nmap -sC -sV -T 4 -p- 10.10.113.121 
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-05 17:40 BST
Warning: 10.10.113.121 giving up on port because retransmission cap hit (6).
Nmap scan report for 10.10.113.121
Host is up (0.055s latency).
Not shown: 65438 closed tcp ports (conn-refused), 95 filtered tcp ports (no-response)
PORT   STATE SERVICE VERSION
22/tcp open  ssh     OpenSSH 7.2p2 Ubuntu 4ubuntu2.7 (Ubuntu Linux; protocol 2.0)
| ssh-hostkey: 
|   2048 3e:79:78:08:93:31:d0:83:7f:e2:bc:b6:14:bf:5d:9b (RSA)
|   256 3a:67:9f:af:7e:66:fa:e3:f8:c7:54:49:63:38:a2:93 (ECDSA)
|_  256 8c:ef:55:b0:23:73:2c:14:09:45:22:ac:84:cb:40:d2 (ED25519)
80/tcp open  http    Apache httpd 2.4.18 ((Ubuntu))
| http-robots.txt: 1 disallowed entry 
|_/wp-admin/
|_http-generator: WordPress 5.3.2
|_http-title: Jack&#039;s Personal Site &#8211; Blog for Jacks writing adven...
|_http-server-header: Apache/2.4.18 (Ubuntu)
Service Info: OS: Linux; CPE: cpe:/o:linux:linux_kernel

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 538.27 seconds

nikto -h 10.10.113.121 
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          10.10.113.121
+ Target Hostname:    10.10.113.121
+ Target Port:        80
+ Start Time:         2022-05-05 18:56:39 (GMT1)
---------------------------------------------------------------------------
+ Server: Apache/2.4.18 (Ubuntu)
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ Uncommon header 'link' found, with contents: <http://jack.thm/index.php/wp-json/>; rel="https://api.w.org/"
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ Uncommon header 'x-redirect-by' found, with contents: WordPress
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ Entry '/wp-admin/' in robots.txt returned a non-forbidden or redirect HTTP code (302)
+ "robots.txt" contains 2 entries which should be manually viewed.
+ Apache/2.4.18 appears to be outdated (current is at least Apache/2.4.37). Apache 2.2.34 is the EOL for the 2.x branch.
+ Web Server returns a valid response with junk HTTP methods, this may cause false positives.
+ OSVDB-3233: /icons/README: Apache default file found.
+ /wp-content/plugins/akismet/readme.txt: The WordPress Akismet plugin 'Tested up to' version usually matches the WordPress version
+ /wp-links-opml.php: This WordPress script reveals the installed version.
+ OSVDB-3092: /license.txt: License file found may identify site software.
+ /wp-app.log: Wordpress' wp-app.log may leak application/system details.
+ /wordpresswp-app.log: Wordpress' wp-app.log may leak application/system details.
+ /: A Wordpress installation was found.
+ /wordpress: A Wordpress installation was found.
+ Cookie wordpress_test_cookie created without the httponly flag
+ /wp-content/plugins/portable-phpmyadmin/wp-pma-mod/db_sql.php: phpMyAdmin (portable) found which may allow DB access.
+ /wordpresswp-content/plugins/portable-phpmyadmin/wp-pma-mod/db_sql.php: phpMyAdmin (portable) found which may allow DB access.
+ OSVDB-3268: /wp-content/uploads/: Directory indexing found.
+ /wp-content/uploads/: Wordpress uploads directory is browsable. This may reveal sensitive information
+ /wp-login.php: Wordpress login found
+ 7893 requests: 0 error(s) and 23 item(s) reported on remote host
+ End Time:           2022-05-05 19:08:25 (GMT1) (706 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested

nmap --script discovery -p 80 10.10.113.121
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-05 19:12 BST
Pre-scan script results:
|_http-robtex-shared-ns: *TEMPORARILY DISABLED* due to changes in Robtex's API. See https://www.robtex.com/api/
| targets-asn: 
|_  targets-asn.asn is a mandatory parameter
|_hostmap-robtex: *TEMPORARILY DISABLED* due to changes in Robtex's API. See https://www.robtex.com/api/
Nmap scan report for 10.10.113.121
Host is up (0.063s latency).

Bug in http-security-headers: no string output.
PORT   STATE SERVICE
80/tcp open  http
| http-wordpress-enum: 
| Search limited to top 100 themes/plugins
|   plugins
|     akismet 3.1.7
|_    user-role-editor 4.24
|_http-devframework: Wordpress detected. Found common traces on /
|_http-chrono: Request times for /; avg: 256.61ms; min: 225.60ms; max: 278.91ms
|_http-title: Jack&#039;s Personal Site &#8211; Blog for Jacks writing adven...
|_http-mobileversion-checker: No mobile version detected.
| http-enum: 
|   /wp-login.php: Possible admin folder
|   /wp-json: Possible admin folder
|   /robots.txt: Robots file
|   /readme.html: Wordpress version: 2 
|   /: WordPress version: 5.3.2
|   /wp-includes/images/rss.png: Wordpress version 2.2 found.
|   /wp-includes/js/jquery/suggest.js: Wordpress version 2.5 found.
|   /wp-includes/images/blank.gif: Wordpress version 2.6 found.
|   /wp-includes/js/comment-reply.js: Wordpress version 2.7 found.
|   /wp-login.php: Wordpress login page.
|   /wp-admin/upgrade.php: Wordpress login page.
|   /readme.html: Interesting, a readme.
|_  /0/: Potentially interesting folder
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
|_    Wget/1.13.4 (linux-gnu)
|_http-errors: Couldn't find any error pages.
|_http-feed: Couldn't find any feeds.
|_http-date: Thu, 05 May 2022 18:12:39 GMT; +1h00m00s from local time.
| http-headers: 
|   Date: Thu, 05 May 2022 18:12:37 GMT
|   Server: Apache/2.4.18 (Ubuntu)
|   Link: <http://jack.thm/index.php/wp-json/>; rel="https://api.w.org/"
|   Connection: close
|   Content-Type: text/html; charset=UTF-8
|   
|_  (Request type: HEAD)
| http-sitemap-generator: 
|   Directory structure:
|     /
|       Other: 1
|   Longest directory structure:
|     Depth: 0
|     Dir: /
|   Total files found (by extension):
|_    Other: 1
|_http-referer-checker: Couldn't find any cross-domain scripts.
| http-robots.txt: 1 disallowed entry 
|_/wp-admin/
| http-comments-displayer: 
| Spidering limited to: maxdepth=3; maxpagecount=20; withinhost=10.10.113.121
|     
|     Path: http://10.10.113.121:80/
|     Line number: 149
|     Comment: 
|         <!-- Start primary content area -->
|     
|     Path: http://10.10.113.121:80/
|     Line number: 199
|     Comment: 
|         <!-- #primary -->
|     
|     Path: http://10.10.113.121:80/
|     Line number: 102
|     Comment: 
|         <!-- <a href="#intro"><img src="img/logo.png" alt="" title="" /></a>-->
|     
|     Path: http://10.10.113.121:80/
|     Line number: 85
|     Comment: 
|         <!--==========================
|           Header
|         ============================-->
|     
|     Path: http://10.10.113.121:80/
|     Line number: 188
|     Comment: 
|         <!-- .entry-content -->
|     
|     Path: http://10.10.113.121:80/
|     Line number: 228
|     Comment: 
|         <!-- #secondary -->
|     
|     Path: http://10.10.113.121:80/
|     Line number: 198
|     Comment: 
|         <!-- #main -->
|     
|     Path: http://10.10.113.121:80/
|     Line number: 101
|     Comment: 
|         <!-- Uncomment below if you prefer to use an image logo -->
|     
|     Path: http://10.10.113.121:80/
|     Line number: 250
|     Comment: 
|         <!-- #footer -->
|     
|     Path: http://10.10.113.121:80/
|     Line number: 113
|     Comment: 
|         <!-- #header -->
|     
|     Path: http://10.10.113.121:80/
|     Line number: 109
|     Comment: 
|         <!-- #nav-menu-container -->
|     
|     Path: http://10.10.113.121:80/
|     Line number: 179
|     Comment: 
|         <!-- .entry-header -->
|     
|     Path: http://10.10.113.121:80/
|     Line number: 178
|     Comment: 
|_        <!-- .entry-meta -->
|_http-xssed: No previously reported XSS vuln.
|_http-generator: WordPress 5.3.2
| http-vhosts: 
|_128 names had status 200

Host script results:
|_dns-brute: Can't guess domain of "10.10.113.121"; use dns-brute.domain script argument.
|_fcrdns: FAIL (No PTR record)

Nmap done: 1 IP address (1 host up) scanned in 399.76 seconds

_______________________________________________________________
         __          _______   _____
         \ \        / /  __ \ / ____|
          \ \  /\  / /| |__) | (___   ___  __ _ _ __ ®
           \ \/  \/ / |  ___/ \___ \ / __|/ _` | '_ \
            \  /\  /  | |     ____) | (__| (_| | | | |
             \/  \/   |_|    |_____/ \___|\__,_|_| |_|

         WordPress Security Scanner by the WPScan Team
                         Version 3.8.22
       Sponsored by Automattic - https://automattic.com/
       @_WPScan_, @ethicalhack3r, @erwan_lr, @firefart
_______________________________________________________________

[+] URL: http://10.10.113.121/ [10.10.113.121]
[+] Started: Thu May  5 18:19:09 2022

Interesting Finding(s):

[+] Headers
 | Interesting Entry: Server: Apache/2.4.18 (Ubuntu)
 | Found By: Headers (Passive Detection)
 | Confidence: 100%

[+] robots.txt found: http://10.10.113.121/robots.txt
 | Interesting Entries:
 |  - /wp-admin/
 |  - /wp-admin/admin-ajax.php
 | Found By: Robots Txt (Aggressive Detection)
 | Confidence: 100%

[+] XML-RPC seems to be enabled: http://10.10.113.121/xmlrpc.php
 | Found By: Direct Access (Aggressive Detection)
 | Confidence: 100%
 | References:
 |  - http://codex.wordpress.org/XML-RPC_Pingback_API
 |  - https://www.rapid7.com/db/modules/auxiliary/scanner/http/wordpress_ghost_scanner/
 |  - https://www.rapid7.com/db/modules/auxiliary/dos/http/wordpress_xmlrpc_dos/
 |  - https://www.rapid7.com/db/modules/auxiliary/scanner/http/wordpress_xmlrpc_login/
 |  - https://www.rapid7.com/db/modules/auxiliary/scanner/http/wordpress_pingback_access/

[+] WordPress readme found: http://10.10.113.121/readme.html
 | Found By: Direct Access (Aggressive Detection)
 | Confidence: 100%

[+] Upload directory has listing enabled: http://10.10.113.121/wp-content/uploads/
 | Found By: Direct Access (Aggressive Detection)
 | Confidence: 100%

[+] The external WP-Cron seems to be enabled: http://10.10.113.121/wp-cron.php
 | Found By: Direct Access (Aggressive Detection)
 | Confidence: 60%
 | References:
 |  - https://www.iplocation.net/defend-wordpress-from-ddos
 |  - https://github.com/wpscanteam/wpscan/issues/1299

[+] WordPress version 5.3.2 identified (Insecure, released on 2019-12-18).
 | Found By: Emoji Settings (Passive Detection)
 |  - http://10.10.113.121/, Match: 'wp-includes\/js\/wp-emoji-release.min.js?ver=5.3.2'
 | Confirmed By: Meta Generator (Passive Detection)
 |  - http://10.10.113.121/, Match: 'WordPress 5.3.2'
 |
 | [!] 21 vulnerabilities identified:
 |
 | [!] Title: WordPress < 5.4.1 - Password Reset Tokens Failed to Be Properly Invalidated
 |     Fixed in: 5.3.3
 |     References:
 |      - https://wpscan.com/vulnerability/7db191c0-d112-4f08-a419-a1cd81928c4e
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2020-11027
 |      - https://wordpress.org/news/2020/04/wordpress-5-4-1/
 |      - https://core.trac.wordpress.org/changeset/47634/
 |      - https://www.wordfence.com/blog/2020/04/unpacking-the-7-vulnerabilities-fixed-in-todays-wordpress-5-4-1-security-update/
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-ww7v-jg8c-q6jw
 |
 | [!] Title: WordPress < 5.4.1 - Unauthenticated Users View Private Posts
 |     Fixed in: 5.3.3
 |     References:
 |      - https://wpscan.com/vulnerability/d1e1ba25-98c9-4ae7-8027-9632fb825a56
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2020-11028
 |      - https://wordpress.org/news/2020/04/wordpress-5-4-1/
 |      - https://core.trac.wordpress.org/changeset/47635/
 |      - https://www.wordfence.com/blog/2020/04/unpacking-the-7-vulnerabilities-fixed-in-todays-wordpress-5-4-1-security-update/
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-xhx9-759f-6p2w
 |
 | [!] Title: WordPress < 5.4.1 - Authenticated Cross-Site Scripting (XSS) in Customizer
 |     Fixed in: 5.3.3
 |     References:
 |      - https://wpscan.com/vulnerability/4eee26bd-a27e-4509-a3a5-8019dd48e429
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2020-11025
 |      - https://wordpress.org/news/2020/04/wordpress-5-4-1/
 |      - https://core.trac.wordpress.org/changeset/47633/
 |      - https://www.wordfence.com/blog/2020/04/unpacking-the-7-vulnerabilities-fixed-in-todays-wordpress-5-4-1-security-update/
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-4mhg-j6fx-5g3c
 |
 | [!] Title: WordPress < 5.4.1 - Authenticated Cross-Site Scripting (XSS) in Search Block
 |     Fixed in: 5.3.3
 |     References:
 |      - https://wpscan.com/vulnerability/e4bda91b-067d-45e4-a8be-672ccf8b1a06
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2020-11030
 |      - https://wordpress.org/news/2020/04/wordpress-5-4-1/
 |      - https://core.trac.wordpress.org/changeset/47636/
 |      - https://www.wordfence.com/blog/2020/04/unpacking-the-7-vulnerabilities-fixed-in-todays-wordpress-5-4-1-security-update/
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-vccm-6gmc-qhjh
 |
 | [!] Title: WordPress < 5.4.1 - Cross-Site Scripting (XSS) in wp-object-cache
 |     Fixed in: 5.3.3
 |     References:
 |      - https://wpscan.com/vulnerability/e721d8b9-a38f-44ac-8520-b4a9ed6a5157
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2020-11029
 |      - https://wordpress.org/news/2020/04/wordpress-5-4-1/
 |      - https://core.trac.wordpress.org/changeset/47637/
 |      - https://www.wordfence.com/blog/2020/04/unpacking-the-7-vulnerabilities-fixed-in-todays-wordpress-5-4-1-security-update/
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-568w-8m88-8g2c
 |
 | [!] Title: WordPress < 5.4.1 - Authenticated Cross-Site Scripting (XSS) in File Uploads
 |     Fixed in: 5.3.3
 |     References:
 |      - https://wpscan.com/vulnerability/55438b63-5fc9-4812-afc4-2f1eff800d5f
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2020-11026
 |      - https://wordpress.org/news/2020/04/wordpress-5-4-1/
 |      - https://core.trac.wordpress.org/changeset/47638/
 |      - https://www.wordfence.com/blog/2020/04/unpacking-the-7-vulnerabilities-fixed-in-todays-wordpress-5-4-1-security-update/
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-3gw2-4656-pfr2
 |      - https://hackerone.com/reports/179695
 |
 | [!] Title: WordPress < 5.4.2 - Authenticated XSS in Block Editor
 |     Fixed in: 5.3.4
 |     References:
 |      - https://wpscan.com/vulnerability/831e4a94-239c-4061-b66e-f5ca0dbb84fa
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2020-4046
 |      - https://wordpress.org/news/2020/06/wordpress-5-4-2-security-and-maintenance-release/
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-rpwf-hrh2-39jf
 |      - https://pentest.co.uk/labs/research/subtle-stored-xss-wordpress-core/
 |      - https://www.youtube.com/watch?v=tCh7Y8z8fb4
 |
 | [!] Title: WordPress < 5.4.2 - Authenticated XSS via Media Files
 |     Fixed in: 5.3.4
 |     References:
 |      - https://wpscan.com/vulnerability/741d07d1-2476-430a-b82f-e1228a9343a4
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2020-4047
 |      - https://wordpress.org/news/2020/06/wordpress-5-4-2-security-and-maintenance-release/
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-8q2w-5m27-wm27
 |
 | [!] Title: WordPress < 5.4.2 - Open Redirection
 |     Fixed in: 5.3.4
 |     References:
 |      - https://wpscan.com/vulnerability/12855f02-432e-4484-af09-7d0fbf596909
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2020-4048
 |      - https://wordpress.org/news/2020/06/wordpress-5-4-2-security-and-maintenance-release/
 |      - https://github.com/WordPress/WordPress/commit/10e2a50c523cf0b9785555a688d7d36a40fbeccf
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-q6pw-gvf4-5fj5
 |
 | [!] Title: WordPress < 5.4.2 - Authenticated Stored XSS via Theme Upload
 |     Fixed in: 5.3.4
 |     References:
 |      - https://wpscan.com/vulnerability/d8addb42-e70b-4439-b828-fd0697e5d9d4
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2020-4049
 |      - https://www.exploit-db.com/exploits/48770/
 |      - https://wordpress.org/news/2020/06/wordpress-5-4-2-security-and-maintenance-release/
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-87h4-phjv-rm6p
 |      - https://hackerone.com/reports/406289
 |
 | [!] Title: WordPress < 5.4.2 - Misuse of set-screen-option Leading to Privilege Escalation
 |     Fixed in: 5.3.4
 |     References:
 |      - https://wpscan.com/vulnerability/b6f69ff1-4c11-48d2-b512-c65168988c45
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2020-4050
 |      - https://wordpress.org/news/2020/06/wordpress-5-4-2-security-and-maintenance-release/
 |      - https://github.com/WordPress/WordPress/commit/dda0ccdd18f6532481406cabede19ae2ed1f575d
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-4vpv-fgg2-gcqc
 |
 | [!] Title: WordPress < 5.4.2 - Disclosure of Password-Protected Page/Post Comments
 |     Fixed in: 5.3.4
 |     References:
 |      - https://wpscan.com/vulnerability/eea6dbf5-e298-44a7-9b0d-f078ad4741f9
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2020-25286
 |      - https://wordpress.org/news/2020/06/wordpress-5-4-2-security-and-maintenance-release/
 |      - https://github.com/WordPress/WordPress/commit/c075eec24f2f3214ab0d0fb0120a23082e6b1122
 |
 | [!] Title: WordPress 4.7-5.7 - Authenticated Password Protected Pages Exposure
 |     Fixed in: 5.3.7
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
 |     Fixed in: 5.3.8
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
 | [!] Title: WordPress < 5.8.2 - Expired DST Root CA X3 Certificate
 |     Fixed in: 5.3.10
 |     References:
 |      - https://wpscan.com/vulnerability/cc23344a-5c91-414a-91e3-c46db614da8d
 |      - https://wordpress.org/news/2021/11/wordpress-5-8-2-security-and-maintenance-release/
 |      - https://core.trac.wordpress.org/ticket/54207
 |
 | [!] Title: WordPress < 5.8 - Plugin Confusion
 |     Fixed in: 5.8
 |     References:
 |      - https://wpscan.com/vulnerability/95e01006-84e4-4e95-b5d7-68ea7b5aa1a8
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2021-44223
 |      - https://vavkamil.cz/2021/11/25/wordpress-plugin-confusion-update-can-get-you-pwned/
 |
 | [!] Title: WordPress < 5.8.3 - SQL Injection via WP_Query
 |     Fixed in: 5.3.11
 |     References:
 |      - https://wpscan.com/vulnerability/7f768bcf-ed33-4b22-b432-d1e7f95c1317
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2022-21661
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-6676-cqfm-gw84
 |      - https://hackerone.com/reports/1378209
 |
 | [!] Title: WordPress < 5.8.3 - Author+ Stored XSS via Post Slugs
 |     Fixed in: 5.3.11
 |     References:
 |      - https://wpscan.com/vulnerability/dc6f04c2-7bf2-4a07-92b5-dd197e4d94c8
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2022-21662
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-699q-3hj9-889w
 |      - https://hackerone.com/reports/425342
 |      - https://blog.sonarsource.com/wordpress-stored-xss-vulnerability
 |
 | [!] Title: WordPress 4.1-5.8.2 - SQL Injection via WP_Meta_Query
 |     Fixed in: 5.3.11
 |     References:
 |      - https://wpscan.com/vulnerability/24462ac4-7959-4575-97aa-a6dcceeae722
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2022-21664
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-jp3p-gw8h-6x86
 |
 | [!] Title: WordPress < 5.8.3 - Super Admin Object Injection in Multisites
 |     Fixed in: 5.3.11
 |     References:
 |      - https://wpscan.com/vulnerability/008c21ab-3d7e-4d97-b6c3-db9d83f390a7
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2022-21663
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-jmmq-m8p8-332h
 |      - https://hackerone.com/reports/541469
 |
 | [!] Title: WordPress < 5.9.2 - Prototype Pollution in jQuery
 |     Fixed in: 5.3.12
 |     References:
 |      - https://wpscan.com/vulnerability/1ac912c1-5e29-41ac-8f76-a062de254c09
 |      - https://wordpress.org/news/2022/03/wordpress-5-9-2-security-maintenance-release/

[i] The main theme could not be detected.

[+] Enumerating Vulnerable Plugins (via Passive Methods)

[i] No plugins Found.

[+] Enumerating Vulnerable Themes (via Passive and Aggressive Methods)
 Checking Known Locations - Time: 00:00:10 <============================================================================================================================================================> (468 / 468) 100.00% Time: 00:00:10

[i] No themes Found.

[+] Enumerating Timthumbs (via Passive and Aggressive Methods)
 Checking Known Locations - Time: 00:00:54 <==========================================================================================================================================================> (2568 / 2568) 100.00% Time: 00:00:54

[i] No Timthumbs Found.

[+] Enumerating Config Backups (via Passive and Aggressive Methods)
 Checking Config Backups - Time: 00:00:03 <=============================================================================================================================================================> (137 / 137) 100.00% Time: 00:00:03

[i] No Config Backups Found.

[+] Enumerating DB Exports (via Passive and Aggressive Methods)
 Checking DB Exports - Time: 00:00:01 <===================================================================================================================================================================> (71 / 71) 100.00% Time: 00:00:01

[i] No DB Exports Found.

[+] Enumerating Medias (via Passive and Aggressive Methods) (Permalink setting must be set to "Plain" for those to be detected)
 Brute Forcing Attachment IDs - Time: 00:00:02 <========================================================================================================================================================> (100 / 100) 100.00% Time: 00:00:02

[i] Medias(s) Identified:

[+] http://10.10.113.121/?attachment_id=4
 | Found By: Attachment Brute Forcing (Aggressive Detection)

[+] http://10.10.113.121/?attachment_id=5
 | Found By: Attachment Brute Forcing (Aggressive Detection)

[+] Enumerating Users (via Passive and Aggressive Methods)
 Brute Forcing Author IDs - Time: 00:00:00 <==============================================================================================================================================================> (10 / 10) 100.00% Time: 00:00:00

[i] User(s) Identified:

[+] jack
 | Found By: Wp Json Api (Aggressive Detection)
 |  - http://10.10.113.121/wp-json/wp/v2/users/?per_page=100&page=1
 | Confirmed By:
 |  Author Id Brute Forcing - Author Pattern (Aggressive Detection)
 |  Login Error Messages (Aggressive Detection)

[+] danny
 | Found By: Author Id Brute Forcing - Author Pattern (Aggressive Detection)
 | Confirmed By: Login Error Messages (Aggressive Detection)

[+] wendy
 | Found By: Author Id Brute Forcing - Author Pattern (Aggressive Detection)
 | Confirmed By: Login Error Messages (Aggressive Detection)

[+] WPScan DB API OK
 | Plan: free
 | Requests Done (during the scan): 1
 | Requests Remaining: 74

[+] Finished: Thu May  5 18:20:27 2022
[+] Requests Done: 3403
[+] Cached Requests: 5
[+] Data Sent: 935.551 KB
[+] Data Received: 1.234 MB
[+] Memory used: 247.164 MB
[+] Elapsed time: 00:01:18

```

Nuclei Digest
```bash
[2022-05-05 18:00:21] [addeventlistener-detect] [http] [info] http://10.10.113.121
[2022-05-05 18:00:21] [apache-detect] [http] [info] http://10.10.113.121 [Apache/2.4.18 (Ubuntu)]
[INF] Using Interactsh Server: oast.site
[2022-05-05 18:00:23] [tech-detect:animate.css] [http] [info] http://10.10.113.121
[2022-05-05 18:00:23] [tech-detect:google-font-api] [http] [info] http://10.10.113.121
[2022-05-05 18:00:23] [tech-detect:font-awesome] [http] [info] http://10.10.113.121
[2022-05-05 18:00:23] [wordpress-detect] [http] [info] http://10.10.113.121 [5.3.2]
[2022-05-05 18:00:24] [metatag-cms] [http] [info] http://10.10.113.121 [WordPress 5.3.2]
[2022-05-05 18:01:02] [http-missing-security-headers:access-control-max-age] [http] [info] http://10.10.113.121
[2022-05-05 18:01:02] [http-missing-security-headers:access-control-allow-methods] [http] [info] http://10.10.113.121
[2022-05-05 18:01:02] [http-missing-security-headers:cross-origin-resource-policy] [http] [info] http://10.10.113.121
[2022-05-05 18:01:02] [http-missing-security-headers:access-control-expose-headers] [http] [info] http://10.10.113.121
[2022-05-05 18:01:02] [http-missing-security-headers:strict-transport-security] [http] [info] http://10.10.113.121
[2022-05-05 18:01:02] [http-missing-security-headers:referrer-policy] [http] [info] http://10.10.113.121
[2022-05-05 18:01:02] [http-missing-security-headers:cross-origin-embedder-policy] [http] [info] http://10.10.113.121
[2022-05-05 18:01:02] [http-missing-security-headers:access-control-allow-credentials] [http] [info] http://10.10.113.121
[2022-05-05 18:01:02] [http-missing-security-headers:access-control-allow-origin] [http] [info] http://10.10.113.121
[2022-05-05 18:01:02] [http-missing-security-headers:content-security-policy] [http] [info] http://10.10.113.121
[2022-05-05 18:01:02] [http-missing-security-headers:permission-policy] [http] [info] http://10.10.113.121
[2022-05-05 18:01:02] [http-missing-security-headers:x-frame-options] [http] [info] http://10.10.113.121
[2022-05-05 18:01:02] [http-missing-security-headers:x-content-type-options] [http] [info] http://10.10.113.121
[2022-05-05 18:01:02] [http-missing-security-headers:x-permitted-cross-domain-policies] [http] [info] http://10.10.113.121
[2022-05-05 18:01:02] [http-missing-security-headers:clear-site-data] [http] [info] http://10.10.113.121
[2022-05-05 18:01:02] [http-missing-security-headers:cross-origin-opener-policy] [http] [info] http://10.10.113.121
[2022-05-05 18:01:19] [wordpress-directory-listing] [http] [info] http://10.10.113.121/wp-content/uploads/
[2022-05-05 18:02:01] [wordpress-xmlrpc-file] [http] [info] http://10.10.113.121/xmlrpc.php
[2022-05-05 18:04:29] [waf-detect:apachegeneric] [http] [info] http://10.10.113.121/
[2022-05-05 18:04:37] [wordpress-xmlrpc-listmethods] [http] [info] http://10.10.113.121/xmlrpc.php
[2022-05-05 18:05:27] [CVE-2018-15473] [network] [medium] 10.10.113.121:22 [SSH-2.0-OpenSSH_7.2p2 Ubuntu-4ubuntu2.7]
[2022-05-05 18:05:57] [wordpress-login] [http] [info] http://10.10.113.121/wp-login.php
```





