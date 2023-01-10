# Biblioteca Helped-Through

Name: Biblioteca
Date:  10/01/2023
Difficulty:  Medium
Goals:  
- Warm up brain after work
- Have fun
- Fundementals
Learnt:
Beyond Root:
- Explain the OSI model for each layer a aspect of how the machine works and if there is no active aspect I must interact with the box on a layer that the box does intentional want external interaction 
- Caveat One do not look at the [THM](https://tryhackme.com/room/osimodelzi)
	- Write what I think the OSI model is
	- Check and correct myself
	- Then test myself with the room.

Fundementals are important and warming up and having fun while prepping for arduious Hacking and Learning later. I thought I would start evening with another newbie tuesday from [Alh4zr3d](https://www.youtube.com/watch?v=Uz4iv7kHxpI). Also I need to finished patching [[Agent-T-Writeup]] and this might prompt it to occur and swiftly. Also I need to exercise and finish [[Bucket-Helped-Through]] before going on a massive Azure related educative spree. 0xTiberious and H3l3N_0F_t0r join the stream along with all the regulars.

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

I register a user but it did not provide much functionality. I tried command inject, sqli but got: 
![](whileupsetabouttopgun.png)
`admin'1' OR '1'='1'-- -`, because I assumed the you would store usernames as strings for some reason...but it instead want `admin' OR 1=1-- -`

![](butitlikeregular.png)

Awhile got my One punchman straight 100s in minus the 10k.  
![](hydrasmokey.png)
3416 tries probably not the intended path. Al mentions sqlmap, which I seem to hardwired out of my brain as OSCP does not allow SQLmap.
![](sqlmapoutput.png)

## Exploit

## Foothold

## PrivEsc

## Beyond Root

OSI model, Raw Sockets, ICMP

- Ping has  raw sockets in Linux 
![](getcapping.png)
```bash 
getcap OTHER
```

```powershell
getcap variant
```


