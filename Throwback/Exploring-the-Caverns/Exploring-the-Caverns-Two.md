# Exploring the Caverns Part Two

After some execise and some python writing I considered trying to installing [nuclei](https://github.com/projectdiscovery/nuclei)
Nuclei is a "Fast and customizable vulnerability scanner based on simple YAML based DSL"

On a Tryhackme Kali VM I ran Nikto in parallel to prevent throttling the VPN connection. 

## Nikto
On a Kali VM from Tryhackme I ran Nikto, here are the results:
For Throwback-PROD:
```bash
root@kali:~# nikto -h 10.200.102.219 
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          10.200.102.219
+ Target Hostname:    10.200.102.219
+ Target Port:        80
+ Start Time:         2022-03-31 20:09:44 (GMT0)
---------------------------------------------------------------------------
+ Server: Microsoft-IIS/10.0
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ Allowed HTTP Methods: OPTIONS, TRACE, GET, HEAD, POST 
+ Public HTTP Methods: OPTIONS, TRACE, GET, HEAD, POST 
+ Retrieved x-powered-by header: PHP/7.4.1
+ /phpinfo.php: Output from the phpinfo() function was found.
+ OSVDB-3233: /phpinfo.php: PHP is installed, and a test script which runs phpinfo() was found. This gives a lot of system information.
+ 7921 requests: 6 error(s) and 8 item(s) reported on remote host
+ End Time:           2022-03-31 20:12:15 (GMT0) (151 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested
```
And for Throwback-MAIL:
```bash
root@kali:~# nikto -h 10.200.102.232
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          10.200.102.232
+ Target Hostname:    10.200.102.232
+ Target Port:        80
+ Start Time:         2022-03-31 20:09:43 (GMT0)
---------------------------------------------------------------------------
+ Server: Apache/2.4.29 (Ubuntu)
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ Root page / redirects to: src/login.php
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ Apache/2.4.29 appears to be outdated (current is at least Apache/2.4.37). Apache 2.2.34 is the EOL for the 2.x branch.
+ OSVDB-48: /doc/: The /doc/ directory is browsable. This may be /usr/doc.
+ OSVDB-3268: /css/: Directory indexing found.
+ OSVDB-3092: /css/: This might be interesting...
+ OSVDB-3268: /data/: Directory indexing found.
+ OSVDB-3092: /data/: This might be interesting...
+ OSVDB-3268: /pics/: Directory indexing found.
+ OSVDB-3092: /pics/: This might be interesting...
+ Cookie SQMSESSID created without the httponly flag
+ Retrieved x-powered-by header: SquirrelMail
+ Uncommon header 'x-dns-prefetch-control' found, with contents: off
+ OSVDB-3092: /README: README file found.
+ OSVDB-3233: /icons/README: Apache default file found.
+ 7922 requests: 6 error(s) and 16 item(s) reported on remote host
+ End Time:           2022-03-31 20:12:43 (GMT0) (180 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested
```

## Nuclei
On another machine I ran Nuclei, firstly I made a list of expose webservers:
```bash
cat list.txt               
http://10.200.102.219:80
http://10.200.102.232:80
```
Already before the scans had finished there were two CVE findings!  
Then I remember I should have set a output directory and had to start again. There is a mark down format 
```bash
nuclei -list list.txt -me throwback.md   
```
Breakdown of commands
```bash
-list string   
path to file containing a list of target URLs/hosts to scan (one per line) 

-me, -markdown-export string  
directory to export results in markdown format
```

```bash
                     __     _
   ____  __  _______/ /__  (_)
  / __ \/ / / / ___/ / _ \/ /
 / / / / /_/ / /__/ /  __/ /
/_/ /_/\__,_/\___/_/\___/_/   2.6.5

                projectdiscovery.io

[WRN] Use with caution. You are responsible for your actions.
[WRN] Developers assume no liability and are not responsible for any misuse or damage.
[ERR] Could not read nuclei-ignore file: open /home/nvm/.config/nuclei/.nuclei-ignore: no such file or directory
[INF] Using Nuclei Engine 2.6.5 (latest)
[INF] Using Nuclei Templates 8.9.3 (latest)
[INF] Templates added in last update: 8
[INF] Templates loaded for scan: 3124
[INF] Templates clustered: 527 (Reduced 970 HTTP Requests)
[2022-03-31 21:15:37] [CVE-2020-9490] [http] [high] http://10.200.102.232:80
[2022-03-31 21:15:37] [email-extractor] [http] [info] http://10.200.102.219:80 [hello@TBHSecurity.com]
[2022-03-31 21:15:37] [microsoft-iis-version] [http] [info] http://10.200.102.219:80 [Microsoft-IIS/10.0]
[2022-03-31 21:15:38] [fingerprinthub-web-fingerprints:squirrelmail] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:39] [tech-detect:font-awesome] [http] [info] http://10.200.102.219:80
[2022-03-31 21:15:39] [tech-detect:google-font-api] [http] [info] http://10.200.102.219:80
[2022-03-31 21:15:39] [tech-detect:ms-iis] [http] [info] http://10.200.102.219:80
[INF] Using Interactsh Server: oast.me
[2022-03-31 21:15:44] [phpinfo-files] [http] [low] http://10.200.102.219:80/phpinfo.php [7.4.1]
[2022-03-31 21:15:57] [http-missing-security-headers:x-frame-options] [http] [info] http://10.200.102.219:80
[2022-03-31 21:15:57] [http-missing-security-headers:referrer-policy] [http] [info] http://10.200.102.219:80
[2022-03-31 21:15:57] [http-missing-security-headers:access-control-allow-origin] [http] [info] http://10.200.102.219:80
[2022-03-31 21:15:57] [http-missing-security-headers:access-control-allow-methods] [http] [info] http://10.200.102.219:80
[2022-03-31 21:15:57] [http-missing-security-headers:x-permitted-cross-domain-policies] [http] [info] http://10.200.102.219:80
[2022-03-31 21:15:57] [http-missing-security-headers:clear-site-data] [http] [info] http://10.200.102.219:80
[2022-03-31 21:15:57] [http-missing-security-headers:cross-origin-opener-policy] [http] [info] http://10.200.102.219:80
[2022-03-31 21:15:57] [http-missing-security-headers:access-control-expose-headers] [http] [info] http://10.200.102.219:80
[2022-03-31 21:15:57] [http-missing-security-headers:strict-transport-security] [http] [info] http://10.200.102.219:80
[2022-03-31 21:15:57] [http-missing-security-headers:content-security-policy] [http] [info] http://10.200.102.219:80
[2022-03-31 21:15:57] [http-missing-security-headers:permission-policy] [http] [info] http://10.200.102.219:80
[2022-03-31 21:15:57] [http-missing-security-headers:access-control-allow-credentials] [http] [info] http://10.200.102.219:80
[2022-03-31 21:15:57] [http-missing-security-headers:x-content-type-options] [http] [info] http://10.200.102.219:80
[2022-03-31 21:15:57] [http-missing-security-headers:cross-origin-embedder-policy] [http] [info] http://10.200.102.219:80
[2022-03-31 21:15:57] [http-missing-security-headers:cross-origin-resource-policy] [http] [info] http://10.200.102.219:80
[2022-03-31 21:15:57] [http-missing-security-headers:access-control-max-age] [http] [info] http://10.200.102.219:80
[2022-03-31 21:15:58] [http-missing-security-headers:strict-transport-security] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:content-security-policy] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:cross-origin-embedder-policy] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:cross-origin-opener-policy] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:x-content-type-options] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:clear-site-data] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:cross-origin-resource-policy] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:access-control-allow-origin] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:access-control-allow-credentials] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:x-permitted-cross-domain-policies] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:referrer-policy] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:access-control-allow-methods] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:permission-policy] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:access-control-expose-headers] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:access-control-max-age] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:permission-policy] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:x-frame-options] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:x-permitted-cross-domain-policies] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:access-control-allow-methods] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:x-content-type-options] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:cross-origin-resource-policy] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:strict-transport-security] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:content-security-policy] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:referrer-policy] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:clear-site-data] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:cross-origin-embedder-policy] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:access-control-allow-origin] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:cross-origin-opener-policy] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:access-control-allow-credentials] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:access-control-expose-headers] [http] [info] http://10.200.102.232:80
[2022-03-31 21:15:58] [http-missing-security-headers:access-control-max-age] [http] [info] http://10.200.102.232:80
[2022-03-31 21:16:00] [rdp-detect:win2016] [network] [info] 10.200.102.219:3389
[2022-03-31 21:16:13] [iis-shortname] [http] [info] http://10.200.102.219:80/*~1*/a.aspx'
[2022-03-31 21:16:13] [CVE-2018-15473] [network] [medium] 10.200.102.232:22 [SSH-2.0-OpenSSH_7.6p1 Ubuntu-4ubuntu0.3]
```

I the moved `mv throwback.md Throwback/Introductory-to-AD-and-PowerShell-Section/nuclei-scans-one.md` . Looked in my obsidian Throwback vault and was awestruck at the results.