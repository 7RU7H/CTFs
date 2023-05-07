# Heist Notes

## Data 

IP: 10.10.10.149
OS: Windows 
Hostname:
Domain: supportdesk
Machine Purpose: 
Services: 80, 135, 445 
Service Languages: php 
Users: Hazard
Credentials:

## Objectives

## Target Map

![](Heist-map.excalidraw.md)

## Solution Inventory Map


### Todo 

Make Excalidraw

### Done


httponly flag not set
emailsologin.png

host and domain name grab with cme
supportdesk.png

testing how guest is configured given login screen
failuresanddenied.png

Maybe the previous Admin had a backdoor in the cisco router \*jokes\*? 
therearebackdoorsinciscorouters.png
```
hazard
support admin
```

[David Bombal has a decryption webpage](https://davidbombal.com/cisco-type-7-password-decryption/)
```
$1$pdQG$o8nrSzsGXeaduXrjlvKc91
0242114B0E143F015F5D1E161713 : $uperP@ssword
02375012182C1A1D751618034F36415408 : Q4)sJu\Y8qz*A3?d
```

There is a [RCE for 12.2 version](https://github.com/artkond/cisco-rce)
```txt
version 12.2
no service pad
service password-encryption
!
isdn switch-type basic-5ess
!
hostname ios-1
!
security passwords min-length 12
enable secret 5 $1$pdQG$o8nrSzsGXeaduXrjlvKc91
!
username rout3r password 7 0242114B0E143F015F5D1E161713
username admin privilege 15 password 7 02375012182C1A1D751618034F36415408
!
!
ip ssh authentication-retries 5
ip ssh version 2
!
!
router bgp 100
 synchronization
 bgp log-neighbor-changes
 bgp dampening
 network 192.168.0.0 mask 300.255.255.0
 timers bgp 3 9
 redistribute connected
!
ip classless
ip route 0.0.0.0 0.0.0.0 192.168.0.1
!
!
access-list 101 permit ip any any
dialer-list 1 protocol ip list 101
!
no ip http server
no ip http secure-server
!
line vty 0 4
 session-timeout 600
 authorization exec SSH
 transport input ssh

```

I maybe the cisco router is hosted on loopback. There is also ssh 

Also tried the http login with @suppportdesk.htb, but it failed
nonexistentaccounts.png

No Null authentication for RPC or with credentials
norpc.png

And winrm
nowinrm.png