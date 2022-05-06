





## Recon

```bash
nmap -Pn -sC -sV --min-rate 5000 -p- 192.168.58.40
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-06 06:53 EDT
Nmap scan report for 192.168.58.40
Host is up (0.0010s latency).
Not shown: 65522 closed tcp ports (conn-refused)
PORT      STATE SERVICE            VERSION
53/tcp    open  domain             Microsoft DNS 6.0.6001 (17714650) (Windows Server 2008 SP1)
| dns-nsid: 
|_  bind.version: Microsoft DNS 6.0.6001 (17714650)
135/tcp   open  msrpc              Microsoft Windows RPC
139/tcp   open  netbios-ssn        Microsoft Windows netbios-ssn
445/tcp   open  microsoft-ds       Windows Server (R) 2008 Standard 6001 Service Pack 1 microsoft-ds (workgroup: WORKGROUP)
3389/tcp  open  ssl/ms-wbt-server?
| rdp-ntlm-info: 
|   Target_Name: INTERNAL
|   NetBIOS_Domain_Name: INTERNAL
|   NetBIOS_Computer_Name: INTERNAL
|   DNS_Domain_Name: internal
|   DNS_Computer_Name: internal
|   Product_Version: 6.0.6001
|_  System_Time: 2022-05-06T10:54:41+00:00
| ssl-cert: Subject: commonName=internal
| Not valid before: 2021-09-21T02:21:05
|_Not valid after:  2022-03-23T02:21:05
|_ssl-date: 2022-05-06T10:54:49+00:00; 0s from scanner time.
5357/tcp  open  http               Microsoft HTTPAPI httpd 2.0 (SSDP/UPnP)
|_http-title: Service Unavailable
|_http-server-header: Microsoft-HTTPAPI/2.0
49152/tcp open  msrpc              Microsoft Windows RPC
49153/tcp open  msrpc              Microsoft Windows RPC
49154/tcp open  msrpc              Microsoft Windows RPC
49155/tcp open  msrpc              Microsoft Windows RPC
49156/tcp open  msrpc              Microsoft Windows RPC
49157/tcp open  msrpc              Microsoft Windows RPC
49158/tcp open  msrpc              Microsoft Windows RPC
Service Info: Host: INTERNAL; OS: Windows; CPE: cpe:/o:microsoft:windows_server_2008::sp1, cpe:/o:microsoft:windows, cpe:/o:microsoft:windows_server_2008:r2

Host script results:
| smb2-time: 
|   date: 2022-05-06T10:54:41
|_  start_date: 2021-09-22T02:21:01
| smb-os-discovery: 
|   OS: Windows Server (R) 2008 Standard 6001 Service Pack 1 (Windows Server (R) 2008 Standard 6.0)
|   OS CPE: cpe:/o:microsoft:windows_server_2008::sp1
|   Computer name: internal
|   NetBIOS computer name: INTERNAL\x00
|   Workgroup: WORKGROUP\x00
|_  System time: 2022-05-06T03:54:41-07:00
|_nbstat: NetBIOS name: INTERNAL, NetBIOS user: <unknown>, NetBIOS MAC: 00:50:56:bf:8f:cb (VMware)
|_clock-skew: mean: 1h23m59s, deviation: 3h07m49s, median: 0s
| smb-security-mode: 
|   account_used: guest
|   authentication_level: user
|   challenge_response: supported
|_  message_signing: disabled (dangerous, but default)
| smb2-security-mode: 
|   2.0.2: 
|_    Message signing enabled but not required

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 87.72 seconds


nikto -h 192.168.58.40 -p 5357                    
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          192.168.58.40
+ Target Hostname:    192.168.58.40
+ Target Port:        5357
+ Start Time:         2022-05-06 07:00:42 (GMT-4)
---------------------------------------------------------------------------
+ Server: Microsoft-HTTPAPI/2.0
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ 7916 requests: 0 error(s) and 3 item(s) reported on remote host
+ End Time:           2022-05-06 07:00:51 (GMT-4) (9 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested

enum4linux -a 192.168.58.40

# Useful emun4linux initial scan output

 ============================================= 
|    Nbtstat Information for 192.168.58.40    |
 ============================================= 
Looking up status of 192.168.58.40
        INTERNAL        <00> -         B <ACTIVE>  Workstation Service
        WORKGROUP       <00> - <GROUP> B <ACTIVE>  Domain/Workgroup Name
        INTERNAL        <20> -         B <ACTIVE>  File Server Service

        MAC Address = 00-50-56-BF-8F-CB


 =================================( Share Enumeration on 192.168.80.40 )=================================                                                                                                   
                                                                                                      
do_connect: Connection to 192.168.80.40 failed (Error NT_STATUS_RESOURCE_NAME_NOT_FOUND)              

        Sharename       Type      Comment
        ---------       ----      -------
Reconnecting with SMB1 for workgroup listing.
Unable to connect with SMB1 -- no workgroup available

[+] Attempting to map shares on 192.168.80.40

nbtscan -r 192.168.80.40
Doing NBT name scan for addresses from 192.168.80.40

IP address       NetBIOS Name     Server    User             MAC address      
------------------------------------------------------------------------------
192.168.80.40    INTERNAL         <server>  <unknown>        00:50:56:ba:f1:e6

rpcclient -U "" -N 192.168.80.40
# Need creds! Nothing here...

nmap -p 139,445 --script=smb-enum-shares.nse,smb-enum-users.nse 192.168.80.40
Starting Nmap 7.92 ( https://nmap.org ) at 2022-05-06 20:27 BST
Nmap scan report for INTERNAL.local (192.168.80.40)
Host is up (0.045s latency).

PORT    STATE SERVICE
139/tcp open  netbios-ssn
445/tcp open  microsoft-ds

Host script results:
| smb-enum-shares: 
|   note: ERROR: Enumerating shares failed, guessing at common ones (NT_STATUS_ACCESS_DENIED)
|   account_used: <blank>
|   \\192.168.80.40\ADMIN$: 
|     warning: Couldn't get details for share: NT_STATUS_ACCESS_DENIED
|     Anonymous access: <none>
|   \\192.168.80.40\C$: 
|     warning: Couldn't get details for share: NT_STATUS_ACCESS_DENIED
|     Anonymous access: <none>
|   \\192.168.80.40\IPC$: 
|     warning: Couldn't get details for share: NT_STATUS_ACCESS_DENIED
|     Anonymous access: READ
|   \\192.168.80.40\PUBLIC: 
|     warning: Couldn't get details for share: NT_STATUS_ACCESS_DENIED
|_    Anonymous access: <none>

Nmap done: 1 IP address (1 host up) scanned in 40.51 seconds


```
