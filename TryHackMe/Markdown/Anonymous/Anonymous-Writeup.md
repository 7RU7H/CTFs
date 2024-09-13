# Anonymous Writeup

Name: Anonymous
Date:  25/2/2024
Difficulty:  Easy
Goals:  
- Get back from Patching, Fixing and Building and just do something 
Learnt:
- Need to reconsider how I use `insert-verb-here` search engine `conditional operator` LLMS
Beyond Root:
- Theorising and Archive Notes for Search Engine Dorking and AI

- [[Anonymous-Notes.md]]
- [[Anonymous-CMD-by-CMDs.md]]

Warning this machine contains references to Cowboy Bebop as finally I can use it to describe my [CTF experiences like me waiting to get good](https://www.youtube.com/watch?v=b_K_fu-mPNU).  
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](TryHackMe/Markdown/Anonymous/Screenshots/ping.png)

Answers to the first three questions
![1920](nmap.png)

Answer to fourth question
![](smbmap.png)
Just checking what `crackmapexec` would output to compare with `nmap` 
![](cmesmb.png)

Connect and enjoy a Russian women's Corgi pictures for whatever reason. Anyway because I can finally use this [VIP Corgi Hacks a machine](https://www.youtube.com/watch?v=5YRTY5G8YRs), because we can just replace the clean.sh script with a a malicious clean.sh script to give us a reverse shell. Why? Because there is a cronjob running and we have write access to FTP and therefore can replace the script.
![](smbclientfordogs.png)

`exiftool` the pictures for weird information, my head turned at the idea that the this required a [Cathode-ray_tube](https://en.wikipedia.org/wiki/Cathode-ray_tube)
![](unstrippedexifforpuppos.png)
FTP is also 
![](ftpanon.png)
Exfiled file content
![](ftpfiles.png)

## Foothold

And here comes the foothold
![](renameandfileupload.png)

Then I saw the error. `rw-r--r--` ...woops 
![](nochmodperms.png)

https://stackoverflow.com/questions/30103662/append-data-to-file-on-ftp-server-in-python from Phind 
![1920](appe.png)
Concatenation of the clean.sh on the FTP server complete
![](bettercleaning.png)
And a reverse shell
![](wearethenamelessone.png)

## Privilege Escalation

Ran linpeas and found some weird credentials
![](ec2credentials.png)
There is lxc and namelessone is part of the lxd group, but then ran into a weird linpeas issue with the version I used that I have never encountered. I thought this was capabilities, before I realised it is actually a set uid binary...
![](envcaps.png)
And [root](https://www.youtube.com/watch?v=pghpRi3mJ6A)
![](root.png)
## Post-Root-Reflection  

LLM and Search Engine Dorking is making my brain funky... need to fix this

## Beyond Root

- The Future of Dorking and my brain and how and when to use LLMs...

Finally the [bestest boy](https://www.youtube.com/watch?v=Ml4QlAmogxA)

