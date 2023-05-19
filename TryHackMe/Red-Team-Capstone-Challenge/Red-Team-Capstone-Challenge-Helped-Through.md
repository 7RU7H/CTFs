# Red-Team-Capstone-Challenge Helped-Through

Name: Red Team Capstone Challenge 
Date:  
Difficulty:  Hard
Goals:  
- Red Teaming with the any Community 
	- Subbed to 0xTiberious
	- Joined the Work Harder Group
- Get as many feasible perspectives and tricks
- Watch the speedrun 
- Get the badge!
Learnt:
- Limitations and pitfall of Wordlist generators 
- Wordlist generator alternative tools
- Setup Local Email for Red Reasons
- AlH4zr3d's Phishing and Spearphishing SE leafy-bug strat 



![](october.png)

- [[Red-Team-Capstone-Challenge-Notes.md]]
- [[Red-Team-Capstone-Challenge-CMD-by-CMDs.md]]
- [[Red-Team-Capstone-Challenge-Credentials]]

General disclaimer, this is more a documentation of my following along with Streamer and other where could. I am here to learn and have no really possiblity of getting this done solo without alot of time off work and more capabilities. I am hope to earn and learn some here. 

#### Content Creators I watched during this...

- [Tib3rius - Youtube](https://www.youtube.com/@Tib3rius) / []
- [alh4zr3d3 - Youtube](https://www.youtube.com/@alh4zr3d3) / [alh4zr3d3 - Twitch](https://www.twitch.tv/videos/1817405607)
- [TylerRamsbey](https://www.youtube.com/@TylerRamsbey)
	- https://www.youtube.com/watch?v=xrh3g5VjY6Y


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

There is a vpns directory to fuzz. If we have a username format we can fuzz it. I forgot to reset the network and then the vpn key  1:40 tyler

Alh4zr3d goes full sliver and re-introduces me to the wonder of [ScareCrow](https://github.com/optiv/ScareCrow) - need to play with this 

- https://github.com/BishopFox/sliver/wiki/Pivots
- https://dominicbreuker.com/post/learning_sliver_c2_10_sideload/

Tyler references [Orange-Cyberdefense Mindmaps](https://github.com/Orange-Cyberdefense/ocd-mindmaps/blob/main/img/pentest_ad_dark_2023_02.svg), which is a extensive mindmap of AD attacks.


## Perimeter Breach
## Initial Compromise of Active Directory
## Full Compromise of CORP Domain
## Full Compromise of Parent Domain
## Full Compromise of BANK Domain
## Compromise of SWIFT and Payment Transfer
