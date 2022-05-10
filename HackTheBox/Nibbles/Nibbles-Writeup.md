
Name:  
Date:  
Difficulty:  
Description:  
Better Description:  
Goals:  
Learnt: 

```bash
ping -c 3 10.129.96.84  
PING 10.129.96.84 (10.129.96.84) 56(84) bytes of data.
64 bytes from 10.129.96.84: icmp_seq=1 ttl=63 time=49.4 ms
64 bytes from 10.129.96.84: icmp_seq=2 ttl=63 time=50.3 ms
64 bytes from 10.129.96.84: icmp_seq=3 ttl=63 time=41.1 ms

--- 10.129.96.84 ping statistics ---
3 packets transmitted, 3 received, 0% packet loss, time 2003ms
rtt min/avg/max/mdev = 41.066/46.904/50.292/4.145 ms

nmap -sC -sV -p- 10.129.96.84                              
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-10 18:46 BST
Nmap scan report for 10.129.96.84
Host is up (0.071s latency).
Not shown: 65533 closed tcp ports (reset)
PORT   STATE SERVICE VERSION
22/tcp open  ssh     OpenSSH 7.2p2 Ubuntu 4ubuntu2.2 (Ubuntu Linux; protocol 2.0)
| ssh-hostkey: 
|   2048 c4:f8:ad:e8:f8:04:77:de:cf:15:0d:63:0a:18:7e:49 (RSA)
|   256 22:8f:b1:97:bf:0f:17:08:fc:7e:2c:8f:e9:77:3a:48 (ECDSA)
|_  256 e6:ac:27:a3:b5:a9:f1:12:3c:34:a5:5d:5b:eb:3d:e9 (ED25519)
80/tcp open  http    Apache httpd 2.4.18 ((Ubuntu))
|_http-title: Site doesn't have a title (text/html).
|_http-server-header: Apache/2.4.18 (Ubuntu)
Service Info: OS: Linux; CPE: cpe:/o:linux:linux_kernel

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 50.37 seconds

nmap --script discovery -p 80 10.129.96.84                              
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-10 18:47 BST
Pre-scan script results:
| targets-asn: 
|_  targets-asn.asn is a mandatory parameter
| targets-ipv6-multicast-invalid-dst: 
|   IP: 2a01:4c8:1483:76b5::b4     MAC: 22:a8:fe:86:a2:77  IFACE: usb0
|   IP: fe80::20a8:feff:fe86:a277  MAC: 22:a8:fe:86:a2:77  IFACE: usb0
|_  Use --script-args=newtargets to add the results as targets
| targets-ipv6-multicast-echo: 
|   IP: 2a01:4c8:1483:76b5::b4     MAC: 22:a8:fe:86:a2:77  IFACE: usb0
|   IP: fe80::20a8:feff:fe86:a277  MAC: 22:a8:fe:86:a2:77  IFACE: usb0
|_  Use --script-args=newtargets to add the results as targets
| targets-ipv6-multicast-mld: 
|   IP: fe80::20a8:feff:fe86:a277  MAC: 22:a8:fe:86:a2:77  IFACE: usb0
| 
|_  Use --script-args=newtargets to add the results as targets
| ipv6-multicast-mld-list: 
|   fe80::20a8:feff:fe86:a277: 
|     device: usb0
|     mac: 22:a8:fe:86:a2:77
|     multicast_ips: 
|       ff02::1:ff86:a277         (NDP Solicited-node)
|       ff02::1:ff00:0            (Solicited-Node Address)
|       ff05::2                   (unknown)
|       ff02::2                   (All Routers Address)
|_      ff02::1:ff00:b4           (Solicited-Node Address)
|_hostmap-robtex: *TEMPORARILY DISABLED* due to changes in Robtex's API. See https://www.robtex.com/api/
|_http-robtex-shared-ns: *TEMPORARILY DISABLED* due to changes in Robtex's API. See https://www.robtex.com/api/
Nmap scan report for 10.129.96.84
Host is up (0.049s latency).

Bug in http-security-headers: no string output.
PORT   STATE SERVICE
80/tcp open  http
|_http-chrono: Request times for /; avg: 118.96ms; min: 89.16ms; max: 149.06ms
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
|_http-errors: Couldn't find any error pages.
|_http-devframework: Couldn't determine the underlying framework or CMS. Try increasing 'httpspider.maxpagecount' value to spider more pages.
|_http-xssed: No previously reported XSS vuln.
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
| http-comments-displayer: 
| Spidering limited to: maxdepth=3; maxpagecount=20; withinhost=10.129.96.84
|     
|     Path: http://10.129.96.84:80/
|     Line number: 16
|     Comment: 
|_        <!-- /nibbleblog/ directory. Nothing interesting here! -->
| http-headers: 
|   Date: Tue, 10 May 2022 17:47:23 GMT
|   Server: Apache/2.4.18 (Ubuntu)
|   Last-Modified: Thu, 28 Dec 2017 20:19:50 GMT
|   ETag: "5d-5616c3cf7fa77"
|   Accept-Ranges: bytes
|   Content-Length: 93
|   Vary: Accept-Encoding
|   Connection: close
|   Content-Type: text/html
|   
|_  (Request type: HEAD)
|_http-mobileversion-checker: No mobile version detected.
|_http-title: Site doesn't have a title (text/html).
|_http-date: Tue, 10 May 2022 17:47:15 GMT; +1h00m00s from local time.
|_http-feed: Couldn't find any feeds.
| http-vhosts: 
|_128 names had status 200

Host script results:
|_ipidseq: ERROR: Script execution failed (use -d to debug)
|_fcrdns: FAIL (No PTR record)
|_dns-brute: Can't guess domain of "10.129.96.84"; use dns-brute.domain script argument.
|_path-mtu: PMTU == 1500

Nmap done: 1 IP address (1 host up) scanned in 259.93 seconds

nikto -h 10.129.96.84                     
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          10.129.96.84
+ Target Hostname:    10.129.96.84
+ Target Port:        80
+ Start Time:         2022-05-10 19:06:41 (GMT1)
---------------------------------------------------------------------------
+ Server: Apache/2.4.18 (Ubuntu)
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ Server may leak inodes via ETags, header found with file /, inode: 5d, size: 5616c3cf7fa77, mtime: gzip
+ Apache/2.4.18 appears to be outdated (current is at least Apache/2.4.37). Apache 2.2.34 is the EOL for te 2.x branch.
+ Allowed HTTP Methods: GET, HEAD, POST, OPTIONS 
+ OSVDB-3233: /icons/README: Apache default file found.
+ 7916 requests: 0 error(s) and 7 item(s) reported on remote host
+ End Time:           2022-05-10 19:14:21 (GMT1) (460 seconds)
---------------------------------------------------------------------------

gobuster dir -u http://10.129.96.84/ -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt -x php
===============================================================
Gobuster v3.1.0
by OJ Reeves (@TheColonial) & Christian Mehlmauer (@firefart)
===============================================================
[+] Url:                     http://10.129.96.84/
[+] Method:                  GET
[+] Threads:                 10
[+] Wordlist:                /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt
[+] Negative Status codes:   404
[+] User Agent:              gobuster/3.1.0
[+] Extensions:              php
[+] Timeout:                 10s
===============================================================
2022/05/10 19:35:01 Starting gobuster in directory enumeration mode
===============================================================
/server-status        (Status: 403) [Size: 300]
                                               
===============================================================
2022/05/10 20:13:58 Finished
==============================================================

dirb http://10.129.96.84

-----------------
DIRB v2.22    
By The Dark Raver
-----------------

START_TIME: Tue May 10 20:27:41 2022
URL_BASE: http://10.129.96.84/
WORDLIST_FILES: /usr/share/dirb/wordlists/common.txt

-----------------

GENERATED WORDS: 4612                                                          

---- Scanning URL: http://10.129.96.84/ ----
+ http://10.129.96.84/index.html (CODE:200|SIZE:93)                                                      
+ http://10.129.96.84/server-status (CODE:403|SIZE:300)                                                  
                                                                                                         
-----------------
END_TIME: Tue May 10 20:31:38 2022
DOWNLOADED: 4612 - FOUND: 2

```
