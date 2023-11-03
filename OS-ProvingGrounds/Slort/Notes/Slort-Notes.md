# Slort Notes

## Data 

IP: 192.168.232.53
OS: Windows 10 Build 19041
Arch: x64 amd64
Hostname: SLORT
Domain: slort
Domain SID:
Machine Purpose: web server with mysql
Services: ftp, mysql, smb-135+9,  
Service Languages:
Users:
Email and Username Formatting:
Credentials:

## Objectives

`\#\# Target Map ![](Slort-map.excalidraw.md)`

#### Machine Connects to list and reason:

[[]] - Reason X

## Data Collected

#### Credentials
```
rupert
```

#### HUMINT


#### Solution Inventory Map

username


#### Todo 

- Contextulise bf for xammp

Check 
- Filezilla
- windows/dos/1336.cpp
#### Timeline of tasks complete

- Years around recon
- ftp no rupert : rupert
- no mysql remote login
- reestablishing the wtf of where I and this machine is actual at 
- smb 445 found on the rescan
- phpinfo.php - prior not that the 8080 is different...
	- Apache/2.4.43 (Win64) OpenSSL/1.1.1g PHP/7.4.6
	- apache is running 80 localhost...
	- ftp support enables
	- curl version mismatch?
		- 8.3? and 7.69.1
			- 8.3 CVE-2023-38546 - 
				- https://www.intruder.io/blog/curl-high-rated-cve-2023-38545
				- The curl vulnerability is a heap-based [buffer overflow](https://owasp.org/www-community/vulnerabilities/Buffer_Overflow) within hostnames of SOCKS5 proxies via the command-line flag. This happens due to curl switching to a local resolve mode if the name is too long. However, there are some caveats/requirements to make exploitation possible:
					- The attacker must be able to point curl at a malicious server they control
					- curl must be using a SOCKS5 proxy using proxy-resolver mode
					- curl must be configured to automatically follow redirects
					- An overflow is only possible in applications that do not set [CURLOPT_BUFFERSIZE](https://curl.se/libcurl/c/CURLOPT_BUFFERSIZE.html) when using libcurl, or set it smaller than 65541. Since curl sets [CURLOPT_BUFFERSIZE](https://curl.se/libcurl/c/CURLOPT_BUFFERSIZE.html) to 100kB by default it is not vulnerable in its default state
					- The SOCKS5 handshake to trigger the local variable bug needs to be "slow enough" to trigger the local variable bug. This is not defined, however they do state "typical server latency is likely slow enough"
- gobustering dashboard/
- searchsploit oclock
	- zurb-foundation - 5.0.0 - xxs captions < 6
	- Apache/2.4.43 (Win64) 
	- OpenSSL/1.1.1g 
	- PHP/7.4.6
	- curl 8.3 - VULN possiblely not intended
 - all.js analysis 
 - found more links from looking at [[tech-detect-https___192.168.122.55_dashboard_-zurb-foundation]]
 - filezilla
	 - https://github.com/NeoTheCapt/FilezillaExploit/blob/master/FuckFilezilla_0_9_41.php
	 - https://www.exploit-db.com/exploits/1336