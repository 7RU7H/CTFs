
# Paper
Name: Paper
Date:  
Difficulty:  Easy
Goals:  OSCP Preparation
Learnt:
- Kick the head with the password reuse, because has always seemed alien to me 

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

![](cron.png)

![1000](interestingfiles.png)