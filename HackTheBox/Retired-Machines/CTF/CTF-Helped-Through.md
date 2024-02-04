# CTF Helped-Through

Name: CTF
Date:  
Difficulty:  Insane
Goals:  
- LDAP Injection Attack revised, understood and noted in my Archive
- Adapt the script a team had to suit new attack scenarios
Learnt:
Beyond Root:
- Adapt the script
- LDAP page in Archive complete

- [[CTF-Notes.md]]
- [[CTF-CMD-by-CMDs.md]]

One of the season 4 machine contains this exploit I want to solidify and revise my understanding of this because will trying to do this manually I failed with online resources that were not mine. I will do the usual recon with the exploit in mind for the first hour the follow along. [IppSec](https://www.youtube.com/watch?v=51JQg202csw) is awesome.
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128. This machine has rate limiting protections and blacklisting IP to prevent content discovery tools and scanning 
![ping](Screenshots/ping.png)

`/manual/`  from `feroxbuster`, and the `login.php` and `status.php` pages found by `gospider` look interesting.

Doing the good idea of OSINTing the box creator [0xEA31](https://app.hackthebox.com/users/13340) is Italian. More importantly I have seen looks of GitHub Profiles with Donkeys as profile images and questioned the significance of the animal from an anthropological perspective. According to [Wikipedia: Cultural references to Donkeys](https://en.wikipedia.org/wiki/Cultural_references_to_donkeys) *Italy has several phrases regarding donkeys, including "put your money in the anus of a donkey and they'll call him sir" (meaning, if you're rich, you'll get respect) and "women, donkeys and goats all have heads" (meaning, women are as stubborn as donkeys and goats).* and according to [washingtonpost](https://www.washingtonpost.com/archive/politics/2005/06/19/pinning-a-fairy-tale-on-italys-dwindling-donkeys/170a715a-9c64-45b7-93f4-6897c411188e/) *Donkeys, **a symbol of Italy's impoverished past**, might not seem as important as, say, Venice or family farming, both under threat -- unless you see something profoundly Italian in them, as Princess Nicoletta d'Ardia Caracciolo does. 19 Jun 2005*

Interestingly according to [Wikipedia](https://en.wikipedia.org/wiki/Italian_wolf): *The **Italian wolf** features prominently in Latin and Italian cultures, such as in the legend of the founding of Rome. It is unofficially considered the national animal of Italy.*... to me seems correct given my understanding of Roman History and mythology. Seems weird that they do not use the Wolf as a symbol or maybe it my framing over the last few days or it maybe some kind of joke I do not understand because I am trying to observe something to understand, naive or just some contemporary I lack the resource to care enough or to observe.
## Exploit

```bash
# LDAP-active-directory-attributes.txt
# LDAP-active-directory-classes.txt
# LDAP-openldap-attributes.txt
# LDAP-openldap-classes.txt
# LDAP.Fuzzing.txt

ffuf -u 'http://ctf.htb/param=FUZZ*' -c -w /usr/share/seclists/Fuzzing/chars.txt:FUZZ  -mc all


ffuf -u 'http://ctf.htb/param=*)(FUZZ=*' -c -w /usr/share/seclists/Fuzzing/$LDAPWORDLIST:FUZZ -mc all -x http://127.0.0.1:8080 # remember to apply FFUF filters and have burpsuite running
```


## Foothold

## Privilege Escalation

## Post-Root-Reflection  

![](CTF-map.excalidraw.md)

## Beyond Root


