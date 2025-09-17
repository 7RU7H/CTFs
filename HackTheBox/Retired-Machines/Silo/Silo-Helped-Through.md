# Silo Helped-Through

Name: Silo
Date:  
Difficulty:  Medium
Goals:  
- Old OSCP like machine - not harder
- Is Guided mode good
Learnt:
Beyond Root:

- [[Silo-Notes.md]]
- [[Silo-CMD-by-CMDs.md]]

Instead of watching along or reading along with a write up to speed up the progress of actual getting to the important thing I need to know and the gotchas I am trying Guided mode on all old OSCP-like HTB pre 2024 except the one considered Harder than OSCP. Also I am not willing to host my own VMs and manage them for awhile till I figure out an actually secure solution - if there will ever be one... These are 3 hour maximum till I cave to a writeup. I will also watch the corresponding IppSec video as a reminder. 

I have already done some of the enumeration, but instead I want to get an idea of what the pwnbox does and does not have that I like.
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Silo/Screenshots/ping.png)

![](nonullauthforrpc.png)

![](cmesmbtest.png)

![](nmap-silo-tcp.png)

Now with the -sC and -sV flags
```bash
nmap -Pn -sC -sV --min-rate 500 -e tun0 -p 80,135,139,445,1521,5985 -oA sc-sv-TCP-ports 10.129.95.188
```
![](nmap-silo-sc-sv.png)

[oracle "TNS Listener Poison Attack"](https://www.oracle.com/security-alerts/alert-cve-2012-1675.html) - without authentication. [PoC for TNS Listener Poison Attack](https://github.com/bongbongco/CVE-2012-1675/blob/master/oracle-tns-poison.nse)

There is SID enumeration tool in `msfconsole`
![](phind-sid-bruteforce.png)

At this point have done this for awhile unsuccessfully (CTFs) - the strictness of old time kicks in. So how was I suppose to find this username?
![](scottfromwhere.png)
running `msf auxiliary/scanner/oracle/oracle_login` we have no scott
![](msfnmapinmsf-no-scott.png)

Presumably this is the machine that would be the machine that would teach you just to make a wordlist for users.

```bash
for i in $(cat /usr/share/metasploit-framework/data/wordlists/namelist.txt); do echo "$i password" | tee -a part; done
```
I then added a scott user as there was not even one in the metasploit usernames list...

So I am going to watch the IppSec video for this
![](itdidnotlikethat.png)

TIGER - scott / tiger is default?
![](TIGER.png)

Also WTF has happened to HackTricks if https://book.hacktricks.wiki/en/network-services-pentesting/1521-1522-1529-pentesting-oracle-listener.html is the mirrored version.

```
# Which glibc does a machine has installed
ldd --version
```
## Exploit

## Foothold

## PrivEsc

![](Silo-map.excalidraw.md)

## Beyond Root


