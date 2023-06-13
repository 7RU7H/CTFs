# Squashed Writeup

Name: Squashed
Date:  
Difficulty:  
Goals:  
Learnt:
- Rescan everything - I put this machine on hold for 1 month till I forget 
Beyond Root:

- [[Squashed-Notes.md]]
- [[Squashed-CMD-by-CMD.md]]


## BEWARE DO NOT READ THIS! - WAIT TILL 11th of June!

## FAILS - Learn Rescan this box!


![](Squashed-map.excalidraw.md)



## Recon and the inital fumblings

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

## Exploit

## Foothold

## PrivEsc

![](Squashed-map.excalidraw.md)

## Beyond Root


rossmoistmaker.png
comingformonicasmoistmaker.png

Probably a rabbithole is the that I checked a  writeup to see if the crackable with the [keepassbrute](https://raw.githubusercontent.com/r3nt0n/keepass4brute/master/keepass4brute.sh). I also did not get a port 80 in first scan round! 

remountageathtml.png
A big hey you need to double your scans every box just in case or I would be waiting for eternity for  [keepassbrute](https://raw.githubusercontent.com/r3nt0n/keepass4brute/master/keepass4brute.sh)
And for ver=4
flagfun.png

I a presuming prior to completion there is misconfiguration for not even setting the version of nfs
noflags.png

I went back to [joemcfarland](https://medium.com/@joemcfarland/hack-the-box-squashed-writeup-44291fc2559a), because I was concern that I had access without being either 2017 and www-data, then it did not work.
weirdnessofnfs.png

And was geniunely concerned for my sanity of this and then I realised that I was in /mnt/ as I did not that to the /mnt directory.
geniunelyconcernedformysanity.png

https://book.hacktricks.xyz/network-services-pentesting/nfs-service-pentesting

## My Bad.

Because of this I am going to leave this machine for a month to forget how of accessing this 