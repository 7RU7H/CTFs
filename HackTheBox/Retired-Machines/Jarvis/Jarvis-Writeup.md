# Jarvis Writeup
Name: Jarvis
Date:  
Difficulty:  
Goals:  
Learnt:

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

![](domainname.png)
Recursive recon and vhost enumeration 

## Exploit


[[phpmyadmin-setup-http___10.129.227.147_phpmyadmin_setup_index.php]] 
Metasploit module- https://www.rapid7.com/db/modules/exploit/unix/webapp/phpmyadmin_config/
```
use exploit/unix/webapp/phpmyadmin_config
```
![](phpmyadminsetup.png)


[[CVE-2019-12616-http___10.129.227.147_phpmyadmin_]]
A vulnerability was found that allows an attacker to trigger a CSRF attack against a phpMyAdmin user. The attacker can trick the user, for instance through a broken  tag pointing at the victim's phpMyAdmin database, and the attacker can potentially deliver a payload (such as a specific INSERT or DELETE statement) through the victim.

## Foothold

## PrivEsc

      
