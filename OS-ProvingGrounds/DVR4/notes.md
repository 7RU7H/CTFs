Name: DVR4
Date:  
Difficulty: Intermediate
Description: Who surveils the surveillance server?
Better Description:  
Goals: 
Learnt:


```bash
PING 192.168.195.179 (192.168.195.179) 56(84) bytes of data.
64 bytes from 192.168.195.179: icmp_seq=1 ttl=127 time=43.2 ms
64 bytes from 192.168.195.179: icmp_seq=2 ttl=127 time=32.0 ms
64 bytes from 192.168.195.179: icmp_seq=3 ttl=127 time=129 ms

--- 192.168.195.179 ping statistics ---
3 packets transmitted, 3 received, 0% packet loss, time 2002ms
rtt min/avg/max/mdev = 31.971/68.106/129.179/43.426 ms
Starting masscan 1.3.2 (http://bit.ly/14GZzcT) at 2022-05-07 16:50:55 GMT
Initiating SYN Stealth Scan
Scanning 1 hosts [65536 ports/host]
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-07 17:51 BST              
Warning: 192.168.195.179 giving up on port because retransmission cap hit (10).
Nmap scan report for 192.168.195.179
Host is up (0.077s latency).
Not shown: 65472 closed tcp ports (reset), 50 filtered tcp ports (no-response)
PORT      STATE SERVICE       VERSION
22/tcp    open  ssh           Bitvise WinSSHD 8.48 (FlowSsh 8.48; protocol 2.0; non-commercial use)
| ssh-hostkey: 
|   3072 21:25:f0:53:b4:99:0f:34:de:2d:ca:bc:5d:fe:20:ce (RSA)
|_  384 e7:96:f3:6a:d8:92:07:5a:bf:37:06:86:0a:31:73:19 (ECDSA)
135/tcp   open  msrpc         Microsoft Windows RPC
139/tcp   open  netbios-ssn   Microsoft Windows netbios-ssn
445/tcp   open  microsoft-ds?
5040/tcp  open  unknown
7680/tcp  open  pando-pub?
8080/tcp  open  http-proxy
| fingerprint-strings: 
|   GetRequest, HTTPOptions: 
|     HTTP/1.1 200 OK
|     Connection: Keep-Alive
|     Keep-Alive: timeout=15, max=4
|     Content-Type: text/html
|     Content-Length: 985
|     <HTML>
|     <HEAD>
|     <TITLE>
|     Argus Surveillance DVR
|     </TITLE>
|     <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
|     <meta name="GENERATOR" content="Actual Drawing 6.0 (http://www.pysoft.com) [PYSOFTWARE]">
|     <frameset frameborder="no" border="0" rows="75,*,88">
|     <frame name="Top" frameborder="0" scrolling="auto" noresize src="CamerasTopFrame.html" marginwidth="0" marginheight="0"> 
|     <frame name="ActiveXFrame" frameborder="0" scrolling="auto" noresize src="ActiveXIFrame.html" marginwidth="0" marginheight="0">
|     <frame name="CamerasTable" frameborder="0" scrolling="auto" noresize src="CamerasBottomFrame.html" marginwidth="0" marginheight="0"> 
|     <noframes>
|     <p>This page uses frames, but your browser doesn't support them.</p>
|_    </noframes>
|_http-generator: Actual Drawing 6.0 (http://www.pysoft.com) [PYSOFTWARE]
|_http-title: Argus Surveillance DVR
49664/tcp open  msrpc         Microsoft Windows RPC
49665/tcp open  msrpc         Microsoft Windows RPC
49666/tcp open  msrpc         Microsoft Windows RPC
49667/tcp open  msrpc         Microsoft Windows RPC
49668/tcp open  msrpc         Microsoft Windows RPC
49669/tcp open  msrpc         Microsoft Windows RPC
1 service unrecognized despite returning data. If you know the service/version, please submit the following fingerprint at https://nmap.org/cgi-bin/submit.cgi?new-service :
SF-Port8080-TCP:V=7.92%I=7%D=5/7%Time=6276A3B2%P=x86_64-pc-linux-gnu%r(Get
SF:Request,451,"HTTP/1\.1\x20200\x20OK\r\nConnection:\x20Keep-Alive\r\nKee
SF:p-Alive:\x20timeout=15,\x20max=4\r\nContent-Type:\x20text/html\r\nConte
SF:nt-Length:\x20985\r\n\r\n<HTML>\r\n<HEAD>\r\n<TITLE>\r\nArgus\x20Survei
SF:llance\x20DVR\r\n</TITLE>\r\n\r\n<meta\x20http-equiv=\"Content-Type\"\x
SF:20content=\"text/html;\x20charset=ISO-8859-1\">\r\n<meta\x20name=\"GENE
SF:RATOR\"\x20content=\"Actual\x20Drawing\x206\.0\x20\(http://www\.pysoft\
SF:.com\)\x20\[PYSOFTWARE\]\">\r\n\r\n<frameset\x20frameborder=\"no\"\x20b
SF:order=\"0\"\x20rows=\"75,\*,88\">\r\n\x20\x20<frame\x20name=\"Top\"\x20
SF:frameborder=\"0\"\x20scrolling=\"auto\"\x20noresize\x20src=\"CamerasTop
SF:Frame\.html\"\x20marginwidth=\"0\"\x20marginheight=\"0\">\x20\x20\r\n\x
SF:20\x20<frame\x20name=\"ActiveXFrame\"\x20frameborder=\"0\"\x20scrolling
SF:=\"auto\"\x20noresize\x20src=\"ActiveXIFrame\.html\"\x20marginwidth=\"0
SF:\"\x20marginheight=\"0\">\r\n\x20\x20<frame\x20name=\"CamerasTable\"\x2
SF:0frameborder=\"0\"\x20scrolling=\"auto\"\x20noresize\x20src=\"CamerasBo
SF:ttomFrame\.html\"\x20marginwidth=\"0\"\x20marginheight=\"0\">\x20\x20\r
SF:\n\x20\x20<noframes>\r\n\x20\x20\x20\x20<p>This\x20page\x20uses\x20fram
SF:es,\x20but\x20your\x20browser\x20doesn't\x20support\x20them\.</p>\r\n\x
SF:20\x20</noframes>\r")%r(HTTPOptions,451,"HTTP/1\.1\x20200\x20OK\r\nConn
SF:ection:\x20Keep-Alive\r\nKeep-Alive:\x20timeout=15,\x20max=4\r\nContent
SF:-Type:\x20text/html\r\nContent-Length:\x20985\r\n\r\n<HTML>\r\n<HEAD>\r
SF:\n<TITLE>\r\nArgus\x20Surveillance\x20DVR\r\n</TITLE>\r\n\r\n<meta\x20h
SF:ttp-equiv=\"Content-Type\"\x20content=\"text/html;\x20charset=ISO-8859-
SF:1\">\r\n<meta\x20name=\"GENERATOR\"\x20content=\"Actual\x20Drawing\x206
SF:\.0\x20\(http://www\.pysoft\.com\)\x20\[PYSOFTWARE\]\">\r\n\r\n<framese
SF:t\x20frameborder=\"no\"\x20border=\"0\"\x20rows=\"75,\*,88\">\r\n\x20\x
SF:20<frame\x20name=\"Top\"\x20frameborder=\"0\"\x20scrolling=\"auto\"\x20
SF:noresize\x20src=\"CamerasTopFrame\.html\"\x20marginwidth=\"0\"\x20margi
SF:nheight=\"0\">\x20\x20\r\n\x20\x20<frame\x20name=\"ActiveXFrame\"\x20fr
SF:ameborder=\"0\"\x20scrolling=\"auto\"\x20noresize\x20src=\"ActiveXIFram
SF:e\.html\"\x20marginwidth=\"0\"\x20marginheight=\"0\">\r\n\x20\x20<frame
SF:\x20name=\"CamerasTable\"\x20frameborder=\"0\"\x20scrolling=\"auto\"\x2
SF:0noresize\x20src=\"CamerasBottomFrame\.html\"\x20marginwidth=\"0\"\x20m
SF:arginheight=\"0\">\x20\x20\r\n\x20\x20<noframes>\r\n\x20\x20\x20\x20<p>
SF:This\x20page\x20uses\x20frames,\x20but\x20your\x20browser\x20doesn't\x2
SF:0support\x20them\.</p>\r\n\x20\x20</noframes>\r");
Service Info: OS: Windows; CPE: cpe:/o:microsoft:windows

Host script results:
| smb2-time: 
|   date: 2022-05-07T16:54:38
|_  start_date: N/A
| smb2-security-mode: 
|   3.1.1: 
|_    Message signing enabled but not required

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 229.04 seconds

 nmap --script discovery -p 22,135,139,445,5040,7680,8080 192.168.195.179
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-07 20:11 BST
Pre-scan script results:
| targets-ipv6-multicast-invalid-dst: 
|   IP: fe80::60e0:81ff:fe6a:89ac  MAC: 62:e0:81:6a:89:ac  IFACE: usb0
|   IP: 2a01:4c8:1401:4bb::a2      MAC: 62:e0:81:6a:89:ac  IFACE: usb0
|_  Use --script-args=newtargets to add the results as targets
| targets-ipv6-multicast-echo: 
|   IP: fe80::60e0:81ff:fe6a:89ac  MAC: 62:e0:81:6a:89:ac  IFACE: usb0
|   IP: 2a01:4c8:1401:4bb::a2      MAC: 62:e0:81:6a:89:ac  IFACE: usb0
|_  Use --script-args=newtargets to add the results as targets
| targets-ipv6-multicast-mld: 
|   IP: fe80::60e0:81ff:fe6a:89ac  MAC: 62:e0:81:6a:89:ac  IFACE: usb0
| 
|_  Use --script-args=newtargets to add the results as targets
| ipv6-multicast-mld-list: 
|   fe80::60e0:81ff:fe6a:89ac: 
|     device: usb0
|     mac: 62:e0:81:6a:89:ac
|     multicast_ips: 
|       ff02::1:ff6a:89ac         (NDP Solicited-node)
|       ff02::1:ff00:0            (Solicited-Node Address)
|       ff05::2                   (unknown)
|       ff02::2                   (All Routers Address)
|_      ff02::1:ff00:a2           (Solicited-Node Address)
|_hostmap-robtex: *TEMPORARILY DISABLED* due to changes in Robtex's API. See https://www.robtex.com/api/
|_http-robtex-shared-ns: *TEMPORARILY DISABLED* due to changes in Robtex's API. See https://www.robtex.com/api/
| targets-asn: 
|_  targets-asn.asn is a mandatory parameter
Nmap scan report for 192.168.195.179
Host is up (0.049s latency).

PORT     STATE  SERVICE
22/tcp   open   ssh
| ssh-hostkey: 
|   3072 21:25:f0:53:b4:99:0f:34:de:2d:ca:bc:5d:fe:20:ce (RSA)
|_  384 e7:96:f3:6a:d8:92:07:5a:bf:37:06:86:0a:31:73:19 (ECDSA)
| ssh2-enum-algos: 
|   kex_algorithms: (12)
|   server_host_key_algorithms: (4)
|   encryption_algorithms: (5)
|   mac_algorithms: (2)
|_  compression_algorithms: (2)
|_banner: SSH-2.0-8.48 FlowSsh: Bitvise SSH Server (WinSSHD) 8.48: fre...
135/tcp  open   msrpc
139/tcp  open   netbios-ssn
|_smb-enum-services: ERROR: Script execution failed (use -d to debug)
445/tcp  open   microsoft-ds
|_smb-enum-services: ERROR: Script execution failed (use -d to debug)
5040/tcp open   unknown
7680/tcp closed pando-pub
8080/tcp open   http-proxy
|_http-title: Argus Surveillance DVR
| http-headers: 
|   Connection: Keep-Alive
|   Keep-Alive: timeout=15, max=4
|   Content-Type: text/html
|   Content-Length: 985
|   
|_  (Request type: HEAD)
|_http-generator: Actual Drawing 6.0 (http://www.pysoft.com) [PYSOFTWARE]
|_http-chrono: Request times for /; avg: 259.39ms; min: 200.39ms; max: 391.87ms
| http-vhosts: 
| 124 names had status 200
| xml
| mailgate
| lab
|_dmz

Host script results:
|_fcrdns: FAIL (No PTR record)
|_ipidseq: ERROR: Script execution failed (use -d to debug)
|_msrpc-enum: Could not negotiate a connection:SMB: Failed to receive bytes: ERROR
| qscan: 
| PORT  FAMILY  MEAN (us)  STDDEV    LOSS (%)
| 22    0       41550.00   5697.89   0.0%
| 135   0       38898.80   5546.44   0.0%
| 139   0       43374.20   5907.33   0.0%
| 445   0       44546.00   13754.84  0.0%
| 5040  0       41265.60   7485.53   0.0%
| 7680  0       38804.90   2998.29   0.0%
|_8080  0       41186.80   7854.74   0.0%
| smb2-time: 
|   date: 2022-05-07T19:11:37
|_  start_date: N/A
|_dns-brute: Can't guess domain of "192.168.195.179"; use dns-brute.domain script argument.
|_path-mtu: PMTU == 1500
| smb-mbenum: 
|_  ERROR: Failed to connect to browser service: Could not negotiate a connection:SMB: Failed to receive bytes: ERROR
| smb2-capabilities: 
|   2.0.2: 
|     Distributed File System
|   2.1: 
|     Distributed File System
|     Leasing
|     Multi-credit operations
|   3.0: 
|     Distributed File System
|     Leasing
|     Multi-credit operations
|   3.0.2: 
|     Distributed File System
|     Leasing
|     Multi-credit operations
|   3.1.1: 
|     Distributed File System
|     Leasing
|_    Multi-credit operations
| smb-protocols: 
|   dialects: 
|     2.0.2
|     2.1
|     3.0
|     3.0.2
|_    3.1.1
| smb2-security-mode: 
|   3.1.1: 
|_    Message signing enabled but not required

Nmap done: 1 IP address (1 host up) scanned in 69.95 seconds


REDID nikto to force all cgi-directories
nikto -h http://192.168.195.179:8080 -C all            
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          192.168.195.179
+ Target Hostname:    192.168.195.179
+ Target Port:        8080
+ Start Time:         2022-05-07 20:07:40 (GMT1)
---------------------------------------------------------------------------
+ Server: No banner retrieved
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ ERROR: Error limit (20) reached for host, giving up. Last error: 
+ Scan terminated:  0 error(s) and 3 item(s) reported on remote host
+ End Time:           2022-05-07 20:08:40 (GMT1) (60 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested
               
nmap -p 139,445 --script smb-enum-shares.nse,smb-enum-users.nse 192.168.195.179
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-07 20:39 BST
Nmap scan report for 192.168.195.179
Host is up (0.035s latency).

PORT    STATE SERVICE
139/tcp open  netbios-ssn
445/tcp open  microsoft-ds


ALL THE FOLLOW RETURN NOTHING -> LOG poisoning or LFI
enum4linux -a 192.168.195.179
smbmap -H 192.168.195.179
nbtscan -r 192.168.195.179
rpcclient -U "" -N 192.168.195.179

```
## http 8080 

Possible LFI?
IP=tun0 

http://192.168.195.179:8080/MessagesLog.html
 	
Time: 	Description:
	2022/05/07 09:49:02 	Program started 7 May 2022 09:49:02
	2022/05/07 09:52:04 	Security alert: user from IP address: $IP is trying to read file: sip:nm
	2022/05/07 09:54:34 	Security alert: user from IP address: $IP is trying to read file: http:\www.google.com
	2022/05/07 09:54:34 	Security alert: user from IP address: $IP is trying to read file: http:\www.google.com
	2022/05/07 09:54:34 	Security alert: user from IP address: $IP is trying to read file: www.google.com:80
	2022/05/07 11:03:45 	Security alert: user from IP address: $IP is trying to read file: zEqgdKSl.bas:ShowVolume
	2022/05/07 11:03:58 	Security alert: user from IP address: $IP is trying to read file: zEqgdKSl.10:100
	2022/05/07 11:05:15 	Security alert: user from IP address: $IP is trying to read file: C:\ProgramData\PY_Software\Argus Surveillance DVR\Images\..\..\..\..\..\..\..\..\..\..\..\..\etc\shadow 

http://192.168.195.179:8080/LogSystem.html

Watchdog program 
```bash
 searchsploit watchdog
------------------------------------------------------------------------------------------- ---------------------------------
 Exploit Title                                                                             |  Path
------------------------------------------------------------------------------------------- ---------------------------------
Geist WatchDog Console 3.2.2 - Multiple Vulnerabilities                                    | xml/webapps/44493.txt
Watchdog Development Anti-Malware / Online Security Pro - NULL Pointer Dereference         | windows/dos/43058.c
Webcam Corp Webcam Watchdog 1.0/1.1/3.63 Web Server - Remote Buffer Overflow               | windows/remote/23514.pl
Webcam Corp Webcam Watchdog 4.0.1 - 'sresult.exe' Cross-Site Scripting                     | cgi/remote/24342.txt
------------------------------------------------------------------------------------------- ---------------------------------
Shellcodes: No Results
Papers: No Results
```

User manual
http://192.168.195.179:8080/UserManual.html

Clear log file after usei


Forgetton password leads to:
Not Found

The requested URL /cgi-bin/reminder.cgi was not found on this server.

Change Administrator user password to admin

403 Forbidden
The requested URL /C:/WINDOWS/Repair/SAM is forbidden on this server. 

\Images\..\..\..\..\..\..\..\..\..\..\..\..\



LOG injection try!
