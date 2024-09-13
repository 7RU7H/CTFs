# Postfish Writeup

Name: Postfish
Date:  8/11/2023
Difficulty:  Intermediate
Goals:  
- TJNull box
- Is a phishing box, my first phishing box
- Departments have email accounts!?!
Learnt:
Beyond Root:

- [[Postfish-Notes.md]]
- [[Postfish-CMD-by-CMDs.md]]

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Postfish/Screenshots/ping.png)

HTTP on port 80 redirects to http://postfish.off/ where team.html contains a list of Users
```
Claire Madison
Mike Ross
Brian Moore
Sarah Lorem
```
Valid emails:
![](smtpuserenumwithpentestmonkey.png)

Will be twinned with [[Attended-Helped-Through]], and other phishing boxes for phishing-for-2-weeks-24-7 banaza.
![](requiresphishing.png)
Hail-mary-ed brute forcing passwords Hydra to testing top 2000 on all four valid email addresses
![](checkerror.png)

No valid passwords
![](2000krockyouonvalidemailsfailed.png)

Got the write for this box as I have never done any phishing so I am bound not know a large amount of the considerations and methodology. At an initial glance of the Walkthrough - department emails 

departments.txt 
```
hr@postfish.off
it@postfish.off
sales@postfish.off
legal@postfish.off
```
![](departmentemailsexist.png)

```bash
smtp-user-enum -M RCPT -U departments.txt -t postfish.off
```

![](emailreading.png)

![](thanksBrian.png)

![](cyberchefurldecode.png)

![](wearebrian.png)

![](nosudofourotherusers.png)

![](boxchecks.png)

![](saleshasmaildir.png)

![](rootdir.pngwehi)

![](routesfirewallsanddns.png)

![](nonexistentandactualusers.png)

![](checkifwemaybephishinganotheruser.png)

![](nocrons.png)

![](unattendwhat.png)

![](salesmail.png)

![](www-baitwtf.png)

![](disclaimerfile.png)

![](rootsshbadmkay.png)

![](nextlevelsomeday.png)

![](pwnkitandpolkit.png)

![](scriptseemslikeitspartofautomateprocess.png)

![](trapcmdonlinux.png)

![](addedshell.png)

![](pspylikeabrian.png)

![](moreactiononpspy.png)

![](noshell.png)

![](violatingthescriptbecauseitsoverwritten.png)

![](tomuchgoof.png)

![](weareFILTER.png)

![](root.png)

![](disclaimerscript.png)

![](themailscripttoemailthepassword.png)



## Exploit

## Foothold

## PrivEsc

## Beyond Root

Finish the [[Attended-Helped-Through]]

https://github.com/HanzCoder/Offensive-Security-OSCP-Cheatsheets/blob/master/offensive-security/red-team-infrastructure/smtp.md