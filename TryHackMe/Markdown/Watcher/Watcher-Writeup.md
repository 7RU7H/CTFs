



```bash
root@ip-10-10-65-248:~# ping -c 3 10.10.75.41
PING 10.10.75.41 (10.10.75.41) 56(84) bytes of data.
64 bytes from 10.10.75.41: icmp_seq=1 ttl=64 time=0.887 ms
64 bytes from 10.10.75.41: icmp_seq=2 ttl=64 time=0.411 ms
64 bytes from 10.10.75.41: icmp_seq=3 ttl=64 time=0.419 ms

--- 10.10.75.41 ping statistics ---
3 packets transmitted, 3 received, 0% packet loss, time 2051ms
rtt min/avg/max/mdev = 0.411/0.572/0.887/0.223 ms
root@ip-10-10-65-248:~# nmap -sC -sV -p- 10.10.75.41 --min-rate 5000

Starting Nmap 7.60 ( https://nmap.org ) at 2022-05-16 19:02 BST
Nmap scan report for ip-10-10-75-41.eu-west-1.compute.internal (10.10.75.41)
Host is up (0.00090s latency).
Not shown: 65532 closed ports
PORT   STATE SERVICE VERSION
21/tcp open  ftp     vsftpd 3.0.3
22/tcp open  ssh     OpenSSH 7.6p1 Ubuntu 4ubuntu0.3 (Ubuntu Linux; protocol 2.0)
| ssh-hostkey: 
|   2048 e1:80:ec:1f:26:9e:32:eb:27:3f:26:ac:d2:37:ba:96 (RSA)
|   256 36:ff:70:11:05:8e:d4:50:7a:29:91:58:75:ac:2e:76 (ECDSA)
|_  256 48:d2:3e:45:da:0c:f0:f6:65:4e:f9:78:97:37:aa:8a (EdDSA)
80/tcp open  http    Apache httpd 2.4.29 ((Ubuntu))
|_http-generator: Jekyll v4.1.1
|_http-server-header: Apache/2.4.29 (Ubuntu)
|_http-title: Corkplacemats
MAC Address: 02:A0:D0:D0:9B:B7 (Unknown)
Service Info: OSs: Unix, Linux; CPE: cpe:/o:linux:linux_kernel

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 33.56 seconds

root@ip-10-10-65-248:~# nmap --script discovery -p- 10.10.75.41 --min-rate 5000

Starting Nmap 7.60 ( https://nmap.org ) at 2022-05-16 19:06 BST
Pre-scan script results:
| targets-asn: 
|_  targets-asn.asn is a mandatory parameter
Nmap scan report for ip-10-10-75-41.eu-west-1.compute.internal (10.10.75.41)
Host is up (0.0045s latency).
Not shown: 65532 closed ports
PORT   STATE SERVICE
21/tcp open  ftp
|_banner: 220 (vsFTPd 3.0.3)
22/tcp open  ssh
|_banner: SSH-2.0-OpenSSH_7.6p1 Ubuntu-4ubuntu0.3
| ssh-hostkey: 
|   2048 e1:80:ec:1f:26:9e:32:eb:27:3f:26:ac:d2:37:ba:96 (RSA)
|   256 36:ff:70:11:05:8e:d4:50:7a:29:91:58:75:ac:2e:76 (ECDSA)
|_  256 48:d2:3e:45:da:0c:f0:f6:65:4e:f9:78:97:37:aa:8a (EdDSA)
| ssh2-enum-algos: 
|   kex_algorithms: (10)
|   server_host_key_algorithms: (5)
|   encryption_algorithms: (6)
|   mac_algorithms: (10)
|_  compression_algorithms: (2)
80/tcp open  http
|_http-chrono: Request times for /; avg: 183.13ms; min: 160.22ms; max: 195.50ms
| http-comments-displayer: 
| Spidering limited to: maxdepth=3; maxpagecount=20; withinhost=ip-10-10-75-41.eu-west-1.compute.internal
|     
|     Path: http://ip-10-10-75-41.eu-west-1.compute.internal/css/bootstrap.min.css
|     Line number: 1
|     Comment: 
|         /*!
|          * Bootstrap v4.5.3 (https://getbootstrap.com/)
|          * Copyright 2011-2020 The Bootstrap Authors
|          * Copyright 2011-2020 Twitter, Inc.
|          * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
|          */
|     
|     Path: http://ip-10-10-75-41.eu-west-1.compute.internal/
|     Line number: 31
|     Comment: 
|         <!-- Custom styles for this template -->
|     
|     Path: http://ip-10-10-75-41.eu-west-1.compute.internal/css/bootstrap.min.css
|     Line number: 7
|     Comment: 
|_        /*# sourceMappingURL=bootstrap.min.css.map */
|_http-date: Mon, 16 May 2022 18:07:29 GMT; 0s from local time.
|_http-devframework: Couldn't determine the underlying framework or CMS. Try increasing 'httpspider.maxpagecount' value to spider more pages.
| http-enum: 
|   /robots.txt: Robots file
|   /css/: Potentially interesting directory w/ listing on 'apache/2.4.29 (ubuntu)'
|_  /images/: Potentially interesting directory w/ listing on 'apache/2.4.29 (ubuntu)'
| http-errors: 
| Spidering limited to: maxpagecount=40; withinhost=ip-10-10-75-41.eu-west-1.compute.internal
|   Found the following error pages: 
|   
|   Error Code: 404
|_  	http://ip-10-10-75-41.eu-west-1.compute.internal/album.css
|_http-feed: Couldn't find any feeds.
|_http-generator: Jekyll v4.1.1
| http-headers: 
|   Date: Mon, 16 May 2022 18:07:30 GMT
|   Server: Apache/2.4.29 (Ubuntu)
|   Connection: close
|   Content-Type: text/html; charset=UTF-8
|   
|_  (Request type: HEAD)
| http-internal-ip-disclosure: 
|_  Internal IP Leaked: 127.0.1.1
|_http-mobileversion-checker: No mobile version detected.
|_http-referer-checker: Couldn't find any cross-domain scripts.
|_http-security-headers: 
| http-sitemap-generator: 
|   Directory structure:
|     /
|       Other: 1; php: 1
|     /css/
|       css: 1
|     /images/
|       jpg: 3
|   Longest directory structure:
|     Depth: 1
|     Dir: /images/
|   Total files found (by extension):
|_    Other: 1; css: 1; jpg: 3; php: 1
|_http-title: Corkplacemats
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
MAC Address: 02:A0:D0:D0:9B:B7 (Unknown)

Host script results:
| dns-brute: 
|   DNS Brute-force hostnames: 
|_    ns0.eu-west-1.compute.internal - 172.16.0.23
|_fcrdns: PASS (ip-10-10-75-41.eu-west-1.compute.internal)
|_ipidseq: All zeros
|_path-mtu: PMTU == 9001
| qscan: 
| PORT  FAMILY  MEAN (us)  STDDEV  LOSS (%)
| 1     0       544.20     38.51   0.0%
| 21    1       453.00     -nan    90.0%
| 22    0       515.00     60.99   0.0%
|_80    0       0.00       -0.00   100.0%

Nmap done: 1 IP address (1 host up) scanned in 55.55 seconds

```
## Web analysis

```bash
root@ip-10-10-65-248:~# nikto -h 10.10.75.41
- Nikto v2.1.5
---------------------------------------------------------------------------
+ Target IP:          10.10.75.41
+ Target Hostname:    ip-10-10-75-41.eu-west-1.compute.internal
+ Target Port:        80
+ Start Time:         2022-05-16 19:08:36 (GMT1)
---------------------------------------------------------------------------
+ Server: Apache/2.4.29 (Ubuntu)
+ The anti-clickjacking X-Frame-Options header is not present.
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ Server leaks inodes via ETags, header found with file /robots.txt, fields: 0x45 0x5b5857db1f4ad 
+ File/dir '/flag_1.txt' in robots.txt returned a non-forbidden or redirect HTTP code (200)
+ "robots.txt" contains 2 entries which should be manually viewed.
+ IP address found in the 'location' header. The IP is "127.0.1.1".
+ OSVDB-630: IIS may reveal its internal or real IP in the Location header via a request to the /images directory. The value is "http://127.0.1.1/images/".
+ DEBUG HTTP verb may show server debugging information. See http://msdn.microsoft.com/en-us/library/e8z01xdh%28VS.80%29.aspx for details.
+ OSVDB-3268: /images/: Directory indexing found.
+ OSVDB-3268: /images/?pattern=/etc/*&sort=name: Directory indexing found.
+ OSVDB-3233: /icons/README: Apache default file found.
+ 6544 items checked: 0 error(s) and 10 item(s) reported on remote host
+ End Time:           2022-05-16 19:08:46 (GMT1) (10 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested

root@ip-10-10-65-248:~# curl http://10.10.75.41/robots.txt
User-agent: *
Allow: /flag_1.txt
Allow: /secret_file_do_not_read.txt

```


## Content Discovery

```bash
root@ip-10-10-65-248:~# gobuster dir -u http://10.10.75.41/ -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt -x php
===============================================================
Gobuster v3.0.1
by OJ Reeves (@TheColonial) & Christian Mehlmauer (@_FireFart_)
===============================================================
[+] Url:            http://10.10.75.41/
[+] Threads:        10
[+] Wordlist:       /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt
[+] Status codes:   200,204,301,302,307,401,403
[+] User Agent:     gobuster/3.0.1
[+] Extensions:     php
[+] Timeout:        10s
===============================================================
2022/05/16 19:18:21 Starting gobuster
===============================================================
/index.php (Status: 200)
/images (Status: 301)
/post.php (Status: 200)
/css (Status: 301)
/round.php (Status: 200)
/bunch.php (Status: 200)
/server-status (Status: 403)
===============================================================
2022/05/16 19:19:20 Finished
===============================================================
```

```bash
root@ip-10-10-65-248:~# curl http://10.10.75.41/post.php
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.1.1">
    <title>Corkplacemats</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/album/">

<link href="css/bootstrap.min.css" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="album.css" rel="stylesheet">
  </head>
  <body>
    <header>
  <div class="collapse bg-dark" id="navbarHeader">
    <div class="container">
      <div class="row">
        <div class="col-sm-8 col-md-7 py-4">
          <h4 class="text-white">About</h4>
        </div>
        <div class="col-sm-4 offset-md-1 py-4">
          <h4 class="text-white">Contact</h4>
          <ul class="list-unstyled">
            <li><a href="#" class="text-white">Follow on Twitter</a></li>
            <li><a href="#" class="text-white">Like on Facebook</a></li>
            <li><a href="#" class="text-white">Email me</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container d-flex justify-content-between">
      <a href="/" class="navbar-brand d-flex align-items-center">
        <strong>Corkplacemats</strong>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </div>
</header>

<main role="main">

<div class="row">
 <div class="col-2"></div>
 <div class="col-8">
   </div>
</div>

</main>

<footer class="text-muted">
  <div class="container">
    <p class="float-right">
      <a href="#">Back to top</a>
    </p>
    <p>&copy; Corkplacemats 2020</p>
  </div>
</footer>
</html>
root@ip-10-10-65-248:~# curl -I http://10.10.75.41/post.php 
HTTP/1.1 200 OK
Date: Mon, 16 May 2022 18:21:44 GMT
Server: Apache/2.4.29 (Ubuntu)
Content-Type: text/html; charset=UTF-8
```
