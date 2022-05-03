

init-pingnmap.png

```bash
nmap -sC -sV -T 4 -p- 192.168.66.77
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-02 14:18 EDT
Nmap scan report for 192.168.66.77
Host is up (0.00024s latency).
Not shown: 65531 closed tcp ports (conn-refused)
PORT      STATE SERVICE VERSION
21/tcp    open  ftp     ProFTPD
22/tcp    open  ssh     OpenSSH 8.2p1 Ubuntu 4 (Ubuntu Linux; protocol 2.0)
| ssh-hostkey: 
|   3072 d2:f6:53:1b:5a:49:7d:74:8d:44:f5:46:e3:93:29:d3 (RSA)
|   256 a6:83:6f:1b:9c:da:b4:41:8c:29:f4:ef:33:4b:20:e0 (ECDSA)
|_  256 a6:5b:80:03:50:19:91:66:b6:c3:98:b8:c4:4f:5c:bd (ED25519)
80/tcp    open  http    Apache httpd 2.4.41 ((Ubuntu))
|_http-server-header: Apache/2.4.41 (Ubuntu)
|_http-title: Did not follow redirect to http://funbox.fritz.box/
| http-robots.txt: 1 disallowed entry 
|_/secret/
33060/tcp open  mysqlx?
| fingerprint-strings: 
|   DNSStatusRequestTCP, LDAPSearchReq, NotesRPC, SSLSessionReq, TLSSessionReq, X11Probe, afp: 
|     Invalid message"
|_    HY000
1 service unrecognized despite returning data. If you know the service/version, please submit the following fingerprint at https://nmap.org/cgi-bin/submit.cgi?new-service :
SF-Port33060-TCP:V=7.92%I=7%D=5/2%Time=6270208A%P=x86_64-pc-linux-gnu%r(NU
SF:LL,9,"\x05\0\0\0\x0b\x08\x05\x1a\0")%r(GenericLines,9,"\x05\0\0\0\x0b\x
SF:08\x05\x1a\0")%r(GetRequest,9,"\x05\0\0\0\x0b\x08\x05\x1a\0")%r(HTTPOpt
SF:ions,9,"\x05\0\0\0\x0b\x08\x05\x1a\0")%r(RTSPRequest,9,"\x05\0\0\0\x0b\
SF:x08\x05\x1a\0")%r(RPCCheck,9,"\x05\0\0\0\x0b\x08\x05\x1a\0")%r(DNSVersi
SF:onBindReqTCP,9,"\x05\0\0\0\x0b\x08\x05\x1a\0")%r(DNSStatusRequestTCP,2B
SF:,"\x05\0\0\0\x0b\x08\x05\x1a\0\x1e\0\0\0\x01\x08\x01\x10\x88'\x1a\x0fIn
SF:valid\x20message\"\x05HY000")%r(Help,9,"\x05\0\0\0\x0b\x08\x05\x1a\0")%
SF:r(SSLSessionReq,2B,"\x05\0\0\0\x0b\x08\x05\x1a\0\x1e\0\0\0\x01\x08\x01\
SF:x10\x88'\x1a\x0fInvalid\x20message\"\x05HY000")%r(TerminalServerCookie,
SF:9,"\x05\0\0\0\x0b\x08\x05\x1a\0")%r(TLSSessionReq,2B,"\x05\0\0\0\x0b\x0
SF:8\x05\x1a\0\x1e\0\0\0\x01\x08\x01\x10\x88'\x1a\x0fInvalid\x20message\"\
SF:x05HY000")%r(Kerberos,9,"\x05\0\0\0\x0b\x08\x05\x1a\0")%r(SMBProgNeg,9,
SF:"\x05\0\0\0\x0b\x08\x05\x1a\0")%r(X11Probe,2B,"\x05\0\0\0\x0b\x08\x05\x
SF:1a\0\x1e\0\0\0\x01\x08\x01\x10\x88'\x1a\x0fInvalid\x20message\"\x05HY00
SF:0")%r(FourOhFourRequest,9,"\x05\0\0\0\x0b\x08\x05\x1a\0")%r(LPDString,9
SF:,"\x05\0\0\0\x0b\x08\x05\x1a\0")%r(LDAPSearchReq,2B,"\x05\0\0\0\x0b\x08
SF:\x05\x1a\0\x1e\0\0\0\x01\x08\x01\x10\x88'\x1a\x0fInvalid\x20message\"\x
SF:05HY000")%r(LDAPBindReq,9,"\x05\0\0\0\x0b\x08\x05\x1a\0")%r(SIPOptions,
SF:9,"\x05\0\0\0\x0b\x08\x05\x1a\0")%r(LANDesk-RC,9,"\x05\0\0\0\x0b\x08\x0
SF:5\x1a\0")%r(TerminalServer,9,"\x05\0\0\0\x0b\x08\x05\x1a\0")%r(NCP,9,"\
SF:x05\0\0\0\x0b\x08\x05\x1a\0")%r(NotesRPC,2B,"\x05\0\0\0\x0b\x08\x05\x1a
SF:\0\x1e\0\0\0\x01\x08\x01\x10\x88'\x1a\x0fInvalid\x20message\"\x05HY000"
SF:)%r(JavaRMI,9,"\x05\0\0\0\x0b\x08\x05\x1a\0")%r(WMSRequest,9,"\x05\0\0\
SF:0\x0b\x08\x05\x1a\0")%r(oracle-tns,9,"\x05\0\0\0\x0b\x08\x05\x1a\0")%r(
SF:ms-sql-s,9,"\x05\0\0\0\x0b\x08\x05\x1a\0")%r(afp,2B,"\x05\0\0\0\x0b\x08
SF:\x05\x1a\0\x1e\0\0\0\x01\x08\x01\x10\x88'\x1a\x0fInvalid\x20message\"\x
SF:05HY000")%r(giop,9,"\x05\0\0\0\x0b\x08\x05\x1a\0");
Service Info: OS: Linux; CPE: cpe:/o:linux:linux_kernel

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 23.35 seconds

nikto -h 192.168.66.77
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          192.168.66.77
+ Target Hostname:    192.168.66.77
+ Target Port:        80
+ Start Time:         2022-05-02 14:19:00 (GMT-4)
---------------------------------------------------------------------------
+ Server: Apache/2.4.41 (Ubuntu)
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ Uncommon header 'x-redirect-by' found, with contents: WordPress
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ Root page / redirects to: http://funbox.fritz.box/
+ Uncommon header 'link' found, with multiple values: (<http://funbox.fritz.box/index.php/wp-json/>; rel="https://api.w.org/",<http://funbox.fritz.box/>; rel=shortlink,)
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ Entry '/secret/' in robots.txt returned a non-forbidden or redirect HTTP code (200)
+ "robots.txt" contains 1 entry which should be manually viewed.
+ Multiple index files found: /default.htm, /index.php
+ Web Server returns a valid response with junk HTTP methods, this may cause false positives.
+ OSVDB-3092: /secret/: This might be interesting...
+ /wp-content/plugins/akismet/readme.txt: The WordPress Akismet plugin 'Tested up to' version usually matches the WordPress version
+ /wp-links-opml.php: This WordPress script reveals the installed version.
+ OSVDB-3092: /license.txt: License file found may identify site software.
+ Cookie wordpressi_test_cookie created without the httponly flag
+ OSVDB-3268: /wp-content/uploads/: Directory indexing found.
+ /wp-content/uploads/: Wordpress uploads directory is browsable. This may reveal sensitive information
+ /wp-login.php: Wordpress login found
+ 7916 requests: 0 error(s) and 17 item(s) reported on remote host
+ End Time:           2022-05-02 14:19:49 (GMT-4) (49 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested


      *********************************************************************
      Portions of the server's headers (Apache/2.4.41) are not in
      the Nikto 2.1.6 database or are newer than the known string. Would you like
      to submit this information (*no server specific data*) to CIRT.net
      for a Nikto update (or you may email to sullo@cirt.net) (y/n)? n

```

Added funbox.fritz.box to /etc/hosts with ip, started poking aroudn with burp suite. 
Ran wpscan 

```bash
[+] URL: http://funbox.fritz.box/ [192.168.66.77]
[+] Started: Mon May  2 14:42:43 2022

# Remembered that I forgot to get an api-token !! rescanned

```

dirb.png

```bash
nmap --script discovery -p 80 192.168.66.77 
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-02 15:05 EDT
Pre-scan script results:
| targets-asn: 
|_  targets-asn.asn is a mandatory parameter
|_http-robtex-shared-ns: *TEMPORARILY DISABLED* due to changes in Robtex's API. See https://www.robtex.com/api/
|_hostmap-robtex: *TEMPORARILY DISABLED* due to changes in Robtex's API. See https://www.robtex.com/api/
Nmap scan report for funbox.fritz.box (192.168.66.77)
Host is up (0.0013s latency).

Bug in http-security-headers: no string output.
PORT   STATE SERVICE
80/tcp open  http
| http-sitemap-generator: 
|   Directory structure:
|   Longest directory structure:
|     Depth: 0
|     Dir: /
|   Total files found (by extension):
|_    
|_http-mobileversion-checker: No mobile version detected.
| http-vhosts: 
|_128 names had status 301
| http-wordpress-enum: 
| Search limited to top 100 themes/plugins
|   themes
|     twentysixteen 2.1
|     twentyseventeen 2.3
|   plugins
|_    akismet 4.1.6
| http-waf-detect: IDS/IPS/WAF detected:
|_funbox.fritz.box:80/?p4yl04d3=<script>alert(document.cookie)</script>
| http-headers: 
|   Date: Mon, 02 May 2022 19:05:55 GMT
|   Server: Apache/2.4.41 (Ubuntu)
|   Link: <http://funbox.fritz.box/index.php/wp-json/>; rel="https://api.w.org/"
|   Link: <http://funbox.fritz.box/>; rel=shortlink
|   Connection: close
|   Content-Type: text/html; charset=UTF-8
|   
|_  (Request type: HEAD)
|_http-chrono: ERROR: Script execution failed (use -d to debug)
|_http-date: Mon, 02 May 2022 19:05:46 GMT; -1s from local time.
| http-enum: 
|   /wp-login.php: Possible admin folder
|   /robots.txt: Robots file
|   /readme.html: Wordpress version: 2 
|   /: WordPress version: 5.4.2
|   /wp-includes/images/rss.png: Wordpress version 2.2 found.
|   /wp-includes/js/jquery/suggest.js: Wordpress version 2.5 found.
|   /wp-includes/images/blank.gif: Wordpress version 2.6 found.
|   /wp-includes/js/comment-reply.js: Wordpress version 2.7 found.
|   /wp-login.php: Wordpress login page.
|   /wp-admin/upgrade.php: Wordpress login page.
|   /readme.html: Interesting, a readme.
|_  /secret/: Potentially interesting folder
|_http-referer-checker: Couldn't find any cross-domain scripts.
|_http-errors: Couldn't find any error pages.
|_http-devframework: Wordpress detected. Found common traces on /
| http-robots.txt: 1 disallowed entry 
|_/secret/
|_http-comments-displayer: Couldn't find any comments.
|_http-feed: Couldn't find any feeds.
|_http-generator: WordPress 5.4.2
|_http-xssed: ERROR: Script execution failed (use -d to debug)
|_http-title: Funbox &#8211; Have fun&#8230;.
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

Host script results:
| dns-brute: 
|_  DNS Brute-force hostnames: No results.
|_fcrdns: FAIL (No PTR record)

Nmap done: 1 IP address (1 host up) scanned in 25.12 seconds
```

```bash
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

[+] URL: http://funbox.fritz.box/ [192.168.55.77]
[+] Started: Mon May  2 20:50:36 2022

Interesting Finding(s):

[+] Headers
 | Interesting Entry: Server: Apache/2.4.41 (Ubuntu)
 | Found By: Headers (Passive Detection)
 | Confidence: 100%

[+] robots.txt found: http://funbox.fritz.box/robots.txt
 | Found By: Robots Txt (Aggressive Detection)
 | Confidence: 100%

[+] XML-RPC seems to be enabled: http://funbox.fritz.box/xmlrpc.php
 | Found By: Direct Access (Aggressive Detection)
 | Confidence: 100%
 | References:
 |  - http://codex.wordpress.org/XML-RPC_Pingback_API
 |  - https://www.rapid7.com/db/modules/auxiliary/scanner/http/wordpress_ghost_scanner/
 |  - https://www.rapid7.com/db/modules/auxiliary/dos/http/wordpress_xmlrpc_dos/
 |  - https://www.rapid7.com/db/modules/auxiliary/scanner/http/wordpress_xmlrpc_login/
 |  - https://www.rapid7.com/db/modules/auxiliary/scanner/http/wordpress_pingback_access/

[+] WordPress readme found: http://funbox.fritz.box/readme.html
 | Found By: Direct Access (Aggressive Detection)
 | Confidence: 100%

[+] Upload directory has listing enabled: http://funbox.fritz.box/wp-content/uploads/
 | Found By: Direct Access (Aggressive Detection)
 | Confidence: 100%

[+] The external WP-Cron seems to be enabled: http://funbox.fritz.box/wp-cron.php
 | Found By: Direct Access (Aggressive Detection)
 | Confidence: 60%
 | References:
 |  - https://www.iplocation.net/defend-wordpress-from-ddos
 |  - https://github.com/wpscanteam/wpscan/issues/1299

[+] WordPress version 5.4.2 identified (Insecure, released on 2020-06-10).
 | Found By: Rss Generator (Passive Detection)
 |  - http://funbox.fritz.box/index.php/feed/, <generator>https://wordpress.org/?v=5.4.2</generator>
 |  - http://funbox.fritz.box/index.php/comments/feed/, <generator>https://wordpress.org/?v=5.4.2</generator>
 |
 | [!] 12 vulnerabilities identified:
 |
 | [!] Title: WordPress 4.7-5.7 - Authenticated Password Protected Pages Exposure
 |     Fixed in: 5.4.5
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
 |     Fixed in: 5.4.6
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
 |     Fixed in: 5.4.7
 |     References:
 |      - https://wpscan.com/vulnerability/5d6789db-e320-494b-81bb-e678674f4199
 |      - https://wordpress.org/news/2021/09/wordpress-5-8-1-security-and-maintenance-release/
 |      - https://github.com/lodash/lodash/wiki/Changelog
 |      - https://github.com/WordPress/wordpress-develop/commit/fb7ecd92acef6c813c1fde6d9d24a21e02340689
 |
 | [!] Title: WordPress 5.4 to 5.8 - Authenticated XSS in Block Editor
 |     Fixed in: 5.4.7
 |     References:
 |      - https://wpscan.com/vulnerability/5b754676-20f5-4478-8fd3-6bc383145811
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2021-39201
 |      - https://wordpress.org/news/2021/09/wordpress-5-8-1-security-and-maintenance-release/
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-wh69-25hr-h94v
 |
 | [!] Title: WordPress 5.4 to 5.8 - Data Exposure via REST API
 |     Fixed in: 5.4.7
 |     References:
 |      - https://wpscan.com/vulnerability/38dd7e87-9a22-48e2-bab1-dc79448ecdfb
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2021-39200
 |      - https://wordpress.org/news/2021/09/wordpress-5-8-1-security-and-maintenance-release/
 |      - https://github.com/WordPress/wordpress-develop/commit/ca4765c62c65acb732b574a6761bf5fd84595706
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-m9hc-7v5q-x8q5
 |
 | [!] Title: WordPress < 5.8.2 - Expired DST Root CA X3 Certificate
 |     Fixed in: 5.4.8
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
 |     Fixed in: 5.4.9
 |     References:
 |      - https://wpscan.com/vulnerability/7f768bcf-ed33-4b22-b432-d1e7f95c1317
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2022-21661
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-6676-cqfm-gw84
 |      - https://hackerone.com/reports/1378209
 |
 | [!] Title: WordPress < 5.8.3 - Author+ Stored XSS via Post Slugs
 |     Fixed in: 5.4.9
 |     References:
 |      - https://wpscan.com/vulnerability/dc6f04c2-7bf2-4a07-92b5-dd197e4d94c8
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2022-21662
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-699q-3hj9-889w
 |      - https://hackerone.com/reports/425342
 |      - https://blog.sonarsource.com/wordpress-stored-xss-vulnerability
 |
 | [!] Title: WordPress 4.1-5.8.2 - SQL Injection via WP_Meta_Query
 |     Fixed in: 5.4.9
 |     References:
 |      - https://wpscan.com/vulnerability/24462ac4-7959-4575-97aa-a6dcceeae722
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2022-21664
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-jp3p-gw8h-6x86
 |
 | [!] Title: WordPress < 5.8.3 - Super Admin Object Injection in Multisites
 |     Fixed in: 5.4.9
 |     References:
 |      - https://wpscan.com/vulnerability/008c21ab-3d7e-4d97-b6c3-db9d83f390a7
 |      - https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2022-21663
 |      - https://github.com/WordPress/wordpress-develop/security/advisories/GHSA-jmmq-m8p8-332h
 |      - https://hackerone.com/reports/541469
 |
 | [!] Title: WordPress < 5.9.2 - Prototype Pollution in jQuery
 |     Fixed in: 5.4.10
 |     References:
 |      - https://wpscan.com/vulnerability/1ac912c1-5e29-41ac-8f76-a062de254c09
 |      - https://wordpress.org/news/2022/03/wordpress-5-9-2-security-maintenance-release/

[+] WordPress theme in use: twentyseventeen
 | Location: http://funbox.fritz.box/wp-content/themes/twentyseventeen/
 | Last Updated: 2022-01-25T00:00:00.000Z
 | Readme: http://funbox.fritz.box/wp-content/themes/twentyseventeen/readme.txt
 | [!] The version is out of date, the latest version is 2.9
 | Style URL: http://funbox.fritz.box/wp-content/themes/twentyseventeen/style.css?ver=20190507
 | Style Name: Twenty Seventeen
 | Style URI: https://wordpress.org/themes/twentyseventeen/
 | Description: Twenty Seventeen brings your site to life with header video and immersive featured images. With a fo...
 | Author: the WordPress team
 | Author URI: https://wordpress.org/
 |
 | Found By: Css Style In Homepage (Passive Detection)
 |
 | Version: 2.3 (80% confidence)
 | Found By: Style (Passive Detection)
 |  - http://funbox.fritz.box/wp-content/themes/twentyseventeen/style.css?ver=20190507, Match: 'Version: 2.3'

[+] Enumerating Vulnerable Plugins (via Passive Methods)

[i] No plugins Found.

[+] Enumerating Vulnerable Themes (via Passive and Aggressive Methods)
 Checking Known Locations - Time: 00:00:04 <============================================================================================================================================================> (468 / 468) 100.00% Time: 00:00:04
[+] Checking Theme Versions (via Passive and Aggressive Methods)

[i] No themes Found.

[+] Enumerating Timthumbs (via Passive and Aggressive Methods)
 Checking Known Locations - Time: 00:00:25 <==========================================================================================================================================================> (2575 / 2575) 100.00% Time: 00:00:25

[i] No Timthumbs Found.

[+] Enumerating Config Backups (via Passive and Aggressive Methods)
 Checking Config Backups - Time: 00:00:01 <=============================================================================================================================================================> (137 / 137) 100.00% Time: 00:00:01

[i] No Config Backups Found.

[+] Enumerating DB Exports (via Passive and Aggressive Methods)
 Checking DB Exports - Time: 00:00:00 <===================================================================================================================================================================> (80 / 80) 100.00% Time: 00:00:00

[i] No DB Exports Found.

[+] Enumerating Medias (via Passive and Aggressive Methods) (Permalink setting must be set to "Plain" for those to be detected)
 Brute Forcing Attachment IDs - Time: 00:00:01 <========================================================================================================================================================> (100 / 100) 100.00% Time: 00:00:01

[i] No Medias Found.

[+] Enumerating Users (via Passive and Aggressive Methods)
 Brute Forcing Author IDs - Time: 00:00:00 <==============================================================================================================================================================> (10 / 10) 100.00% Time: 00:00:00

[i] User(s) Identified:

[+] admin
 | Found By: Author Posts - Author Pattern (Passive Detection)
 | Confirmed By:
 |  Rss Generator (Passive Detection)
 |  Wp Json Api (Aggressive Detection)
 |   - http://funbox.fritz.box/index.php/wp-json/wp/v2/users/?per_page=100&page=1
 |  Author Id Brute Forcing - Author Pattern (Aggressive Detection)
 |  Login Error Messages (Aggressive Detection)

[+] joe
 | Found By: Author Id Brute Forcing - Author Pattern (Aggressive Detection)
 | Confirmed By: Login Error Messages (Aggressive Detection)

[+] WPScan DB API OK
 | Plan: free
 | Requests Done (during the scan): 2
 | Requests Remaining: 23

[+] Finished: Mon May  2 20:51:18 2022
[+] Requests Done: 3422
[+] Cached Requests: 11
[+] Data Sent: 975.237 KB
[+] Data Received: 1.036 MB
[+] Memory used: 278.523 MB
[+] Elapsed time: 00:00:42
```

Note: 
Direct access to /wp-content/uploads


Default mysql credentials changed: 
mysql -u root -h 192.168.55.77 -P 33060
ERROR: 

hakrawler 
```bash
echo http://funbox.fritz.box/ | hakrawler
http://funbox.fritz.box/
http://funbox.fritz.box/
http://funbox.fritz.box/index.php/about/
http://funbox.fritz.box/index.php/blog/
http://funbox.fritz.box/index.php/contact/
http://funbox.fritz.box/index.php/2020/06/19/hello-world/
http://funbox.fritz.box/index.php/2020/06/19/hello-world/
https://www.yelp.com/
https://www.facebook.com/wordpress
https://twitter.com/wordpress
https://www.instagram.com/explore/tags/wordcamp/
mailto:wordpress@example.com
https://wordpress.org/
http://funbox.fritz.box/wp-includes/js/jquery/jquery.js?ver=1.12.4-wp
http://funbox.fritz.box/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.4.1
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/skip-link-focus-fix.js?ver=20161114
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/navigation.js?ver=20161203
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/global.js?ver=20190121
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/jquery.scrollTo.js?ver=2.1.2
http://funbox.fritz.box/wp-includes/js/wp-embed.min.js?ver=5.4.2
http://funbox.fritz.box/
http://funbox.fritz.box/
http://funbox.fritz.box/
http://funbox.fritz.box/index.php/about/
http://funbox.fritz.box/index.php/blog/
http://funbox.fritz.box/index.php/contact/
https://www.yelp.com/
https://www.facebook.com/wordpress
https://twitter.com/wordpress
https://www.instagram.com/explore/tags/wordcamp/
mailto:wordpress@example.com
https://wordpress.org/
http://funbox.fritz.box/wp-includes/js/jquery/jquery.js?ver=1.12.4-wp
http://funbox.fritz.box/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.4.1
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/skip-link-focus-fix.js?ver=20161114
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/navigation.js?ver=20161203
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/global.js?ver=20190121
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/jquery.scrollTo.js?ver=2.1.2
http://funbox.fritz.box/wp-includes/js/wp-embed.min.js?ver=5.4.2
http://funbox.fritz.box/
http://funbox.fritz.box/
http://funbox.fritz.box/
http://funbox.fritz.box/index.php/about/
http://funbox.fritz.box/index.php/blog/
http://funbox.fritz.box/index.php/contact/
https://www.yelp.com/
https://www.facebook.com/wordpress
https://twitter.com/wordpress
https://www.instagram.com/explore/tags/wordcamp/
mailto:wordpress@example.com
https://wordpress.org/
http://funbox.fritz.box/wp-includes/js/jquery/jquery.js?ver=1.12.4-wp
http://funbox.fritz.box/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.4.1
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/skip-link-focus-fix.js?ver=20161114
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/navigation.js?ver=20161203
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/global.js?ver=20190121
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/jquery.scrollTo.js?ver=2.1.2
http://funbox.fritz.box/wp-includes/js/wp-embed.min.js?ver=5.4.2
http://funbox.fritz.box/
http://funbox.fritz.box/
http://funbox.fritz.box/
http://funbox.fritz.box/index.php/about/
http://funbox.fritz.box/index.php/blog/
http://funbox.fritz.box/index.php/contact/
http://funbox.fritz.box/index.php/2020/06/19/hello-world/
http://funbox.fritz.box/index.php/author/admin/
https://wordpress.org/
http://funbox.fritz.box/index.php/2020/06/19/hello-world/#comment-1
https://gravatar.com/
http://funbox.fritz.box/index.php/2020/06/19/hello-world/?replytocom=1#respond
http://funbox.fritz.box/index.php/2020/06/19/hello-world/#respond
https://www.yelp.com/
https://www.facebook.com/wordpress
https://twitter.com/wordpress
https://www.instagram.com/explore/tags/wordcamp/
mailto:wordpress@example.com
https://wordpress.org/
http://funbox.fritz.box/wp-includes/js/jquery/jquery.js?ver=1.12.4-wp
http://funbox.fritz.box/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.4.1
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/skip-link-focus-fix.js?ver=20161114
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/navigation.js?ver=20161203
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/global.js?ver=20190121
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/jquery.scrollTo.js?ver=2.1.2
http://funbox.fritz.box/wp-includes/js/comment-reply.min.js?ver=5.4.2
http://funbox.fritz.box/wp-includes/js/wp-embed.min.js?ver=5.4.2
http://funbox.fritz.box/wp-comments-post.php
http://funbox.fritz.box/
http://funbox.fritz.box/
http://funbox.fritz.box/
http://funbox.fritz.box/
http://funbox.fritz.box/index.php/about/
http://funbox.fritz.box/index.php/blog/
http://funbox.fritz.box/index.php/contact/
http://funbox.fritz.box/index.php/2020/06/19/hello-world/
http://funbox.fritz.box/index.php/2020/06/19/hello-world/
https://www.yelp.com/
https://www.facebook.com/wordpress
https://twitter.com/wordpress
https://www.instagram.com/explore/tags/wordcamp/
mailto:wordpress@example.com
https://wordpress.org/
http://funbox.fritz.box/wp-includes/js/jquery/jquery.js?ver=1.12.4-wp
http://funbox.fritz.box/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.4.1
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/skip-link-focus-fix.js?ver=20161114
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/navigation.js?ver=20161203
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/global.js?ver=20190121
http://funbox.fritz.box/wp-content/themes/twentyseventeen/assets/js/jquery.scrollTo.js?ver=2.1.2
http://funbox.fritz.box/wp-includes/js/wp-embed.min.js?ver=5.4.2
http://funbox.fritz.box/
http://funbox.fritz.box/
```
