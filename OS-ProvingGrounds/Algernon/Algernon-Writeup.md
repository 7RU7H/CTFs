
Name:  
Date:  
Difficulty:  
Description:  
Better Description:  
Goals:  
Learnt: 

## Recon

```
ping -c 3 192.168.64.65 
PING 192.168.64.65 (192.168.64.65) 56(84) bytes of data.
64 bytes from 192.168.64.65: icmp_seq=1 ttl=127 time=34.9 ms
64 bytes from 192.168.64.65: icmp_seq=2 ttl=127 time=37.8 ms
64 bytes from 192.168.64.65: icmp_seq=3 ttl=127 time=40.5 ms

--- 192.168.64.65 ping statistics ---
3 packets transmitted, 3 received, 0% packet loss, time 2003ms
rtt min/avg/max/mdev = 34.919/37.741/40.511/2.283 ms

nmap -sC -sV -p- 192.168.64.65 --min-rate 5000
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-10 20:30 BST
Nmap scan report for 192.168.64.65
Host is up (0.049s latency).
Not shown: 65521 closed tcp ports (reset)
PORT      STATE SERVICE       VERSION
21/tcp    open  ftp           Microsoft ftpd
| ftp-anon: Anonymous FTP login allowed (FTP code 230)
| 04-29-20  10:31PM       <DIR>          ImapRetrieval
| 05-10-22  10:41AM       <DIR>          Logs
| 04-29-20  10:31PM       <DIR>          PopRetrieval
|_04-29-20  10:32PM       <DIR>          Spool
| ftp-syst: 
|_  SYST: Windows_NT
80/tcp    open  http          Microsoft IIS httpd 10.0
|_http-title: IIS Windows
| http-methods: 
|_  Potentially risky methods: TRACE
|_http-server-header: Microsoft-IIS/10.0
135/tcp   open  msrpc         Microsoft Windows RPC
139/tcp   open  netbios-ssn   Microsoft Windows netbios-ssn
445/tcp   open  microsoft-ds?
5040/tcp  open  unknown
9998/tcp  open  http          Microsoft IIS httpd 10.0
| uptime-agent-info: HTTP/1.1 400 Bad Request\x0D
| Content-Type: text/html; charset=us-ascii\x0D
| Server: Microsoft-HTTPAPI/2.0\x0D
| Date: Tue, 10 May 2022 19:33:00 GMT\x0D
| Connection: close\x0D
| Content-Length: 326\x0D
| \x0D
| <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">\x0D
| <HTML><HEAD><TITLE>Bad Request</TITLE>\x0D
| <META HTTP-EQUIV="Content-Type" Content="text/html; charset=us-ascii"></HEAD>\x0D
| <BODY><h2>Bad Request - Invalid Verb</h2>\x0D
| <hr><p>HTTP Error 400. The request verb is invalid.</p>\x0D
|_</BODY></HTML>\x0D
|_http-server-header: Microsoft-IIS/10.0
| http-title: Site doesn't have a title (text/html; charset=utf-8).
|_Requested resource was /interface/root
17001/tcp open  remoting      MS .NET Remoting services
49664/tcp open  msrpc         Microsoft Windows RPC
49665/tcp open  msrpc         Microsoft Windows RPC
49666/tcp open  msrpc         Microsoft Windows RPC
49667/tcp open  msrpc         Microsoft Windows RPC
49668/tcp open  msrpc         Microsoft Windows RPC
49669/tcp open  msrpc         Microsoft Windows RPC
Service Info: OS: Windows; CPE: cpe:/o:microsoft:windows

Host script results:
| smb2-time: 
|   date: 2022-05-10T19:33:02
|_  start_date: N/A
|_clock-skew: -2s
| smb2-security-mode: 
|   3.1.1: 
|_    Message signing enabled but not required

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 192.52 seconds

nikto -h 192.168.64.65                        
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          192.168.64.65
+ Target Hostname:    192.168.64.65
+ Target Port:        80
+ Start Time:         2022-05-10 20:35:34 (GMT1)
---------------------------------------------------------------------------
+ Server: Microsoft-IIS/10.0
+ Retrieved x-powered-by header: ASP.NET
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ Retrieved x-aspnet-version header: 4.0.30319
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ Allowed HTTP Methods: OPTIONS, TRACE, GET, HEAD, POST 
+ Public HTTP Methods: OPTIONS, TRACE, GET, HEAD, POST 
+ 7915 requests: 0 error(s) and 7 item(s) reported on remote host
+ End Time:           2022-05-10 20:42:54 (GMT1) (440 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested

nikto -h 192.168.64.65:9998
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          192.168.64.65
+ Target Hostname:    192.168.64.65
+ Target Port:        9998
+ Start Time:         2022-05-10 20:59:07 (GMT1)
---------------------------------------------------------------------------
+ Server: Microsoft-IIS/10.0
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ Root page / redirects to: /interface/root
+ Uncommon header 'request-id' found, with contents: 7384d93f-b73b-4bcf-ad64-76c7f1adf7b8
+ OSVDB-3092: /reports/: This might be interesting...
+ 8881 requests: 0 error(s) and 5 item(s) reported on remote host
+ End Time:           2022-05-10 21:08:01 (GMT1) (534 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested

```


gobuster and dicovery and enum4linux and smb todo

