
Name: Vault
Date:  
Difficulty:  
Goals:  
Learnt:

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Vault/Screenshots/ping.png)

![](enumfourlinuxshares.png)
smbmap show permissions
![](readwriteShare.png)

Bang on the [Tamil music to stay culturally enriched](https://www.youtube.com/watch?v=V8R1aZf1-AU). Anirudh is a tamil name on first google. 
![](bruteridsanirudh.png)

We have a writable share, a username - need a password and means of execution. After considering everything I went for both enumeration hint - it is ballbuster because it declare the the share is writable and the second is that there is a scripted user that will click shortlinks.

"Mount a client-side attack by uploading a shortcut file to the SMB share. A user will view the share and render file icons in it."

After some checking. This is definately not a OSCP like machine. So I am going to use metasploit or some C2  as I thought that this would not involve fake social engineering practice. Basically to keep the process clean and not mental fazzle myself doing this machine. Also [pentesterlab](https://pentestlab.blog/2019/10/08/persistence-shortcut-modification/) suggests we can use Empire which is OSCP related.

Edited from the article
```python
usemodule powershell/persistence/userland/backdoor_lnk
usestager windows/launcher_lnk
set Listener http
execute
```

Did not get call back, but I also did not remove it from the share, so it is not considered malicious. So it might that want something really specific. For my 12 hour assessment - I am happy I enumerated well, will give myself the hour remaining to finish HTB Acute. 

## Exploit

## Foothold

## PrivEsc

      
We can put a shell on smbclient -N ///DocumentsShare -U guest%<put empty quotes here>
