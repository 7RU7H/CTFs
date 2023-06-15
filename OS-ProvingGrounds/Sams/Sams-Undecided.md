# Sams 

Name: Sams
Date:  2/5/2023
Difficulty:  Easy - User Very Hard
Goals:  
- Alh4zr3d newbie tuesday stream
Learnt:
- Feroxbuster for content discovery is still partial must
Beyond Root:

- [[Sams-Notes.md]]
- [[Sams-CMD-by-CMDs.md]]

This started as a newbie tuesday Proving Ground play Helped-Through enjoying a refreshed Alh4zr3d and co, when this box seems like accoding to many in chat a very OSCP like box. 


![](Sams-map.excalidraw.md)

## Recon


The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Sams/Screenshots/ping.png)


161  misconfigurations abound!

![](samselloit.png)

![](testingdirectory.png)

![](samspcsmb.png)

![](SCHLIXCMS.png)



https://packetstormsecurity.com/files/162751/Schlix-CMS-2.2.6-6-Shell-Upload-Directory-Traversal.html

https://www.exploit-db.com/exploits/49838

https://www.exploit-db.com/exploits/49837

But 0x9d finds https://192.168.181.248/testing/web/main/data/private

Feroxbuster for the win - having flash backs to [[Bart-Writeup]]

Then it broke or is not intended
![](sadscriptandhappyal.png)

I was paying attention to Al installed the software locally to find the database name. I think I going to continue this and not watch the vod.

## Exploit

## Foothold

## PrivEsc

![](Sams-map.excalidraw.md)

## Beyond Root


