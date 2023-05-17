# Red-Team-Capstone-Challenge Helped-Through

Name: Red Team Capstone Challenge 
Date:  
Difficulty:  Hard
Goals:  
- Red Teaming with the any Community 
- Get the badge!
Learnt:
- Limitations and pitfall of Wordlist generators 
- Wordlist generator alternative tools
- Setup Local Email for Red Reasons
- AlH4zr3d's  Phishing and Spearphishing SE leafy-bug strat 



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




## Perimeter Breach
## Initial Compromise of Active Directory
## Full Compromise of CORP Domain
## Full Compromise of Parent Domain
## Full Compromise of BANK Domain
## Compromise of SWIFT and Payment Transfer
