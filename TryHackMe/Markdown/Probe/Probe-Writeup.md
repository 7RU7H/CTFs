# Probe Writeup

Name: Probe
Date:  13/2/2024
Difficulty:  Easy
Goals:  
- Points
- Test my speed
Learnt:
Beyond Root:
- None

- [[Probe-Notes.md]]
- [[Probe-CMD-by-CMDs.md]]

I needed [sahilmalvi1806](https://medium.com/@sahilmalvi1806/tryhackme-room-probe-by-sahil-malvi-1be94ed54d90) writeup to verify answers given that my nikto database provable differs from the machine expected answers
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Added the domains to my /etc/hosts file
![](updatingetchosts.png)

What is the version of the Apache server?
```
2.4.41
```
What is the port number of the FTP service?
```
1338
```
What is the FQDN for the website hosted using a self-signed certificate and contains critical server information as the homepage?
```
dev.probe.thm
```
What is the email address associated with the SSL certificate used to sign the website mentioned in Q3?
![](question4.png)
```
probe@probe.thm
```
What is the value of the **PHP Extension Build** on the server?
![](question5.png)
```
API20190902,NTS
```
What is the banner for the FTP service?
![](question6.png)
```
THM{WELCOME_101113}
```


What software is used for managing the database on the server? [I found this but even though I have done a PG machine with phpmyadmin were their is a SQLi (so has  database access for some reason I needed this..)](https://medium.com/@sahilmalvi1806/tryhackme-room-probe-by-sahil-malvi-1be94ed54d90)
```
phpmyadmin
```


What is the Content Management System (CMS) hosted on the server?
![](question7and8.png)
```
wordpress
```
What is the version number of the CMS hosted on the server?
```
6.2.2
```
What is the username for the admin panel of the CMS?
```bash
# You need to disable tls checking or wpscan wont scan the site: 
wpscan --url https://myblog.thm:9007/wp-login.php -e --api-token $(cat $wpscanAPIkey) --disable-tls-checks
```
I thought this was the wrong answer, but that is actually a post by a user joomla
![](wrongansforcms.png)
```bash
joomla
# I tried wpscan to brute force the login to: joomla / raihan Time: 00:16:46 <> (18456 / 14344392)  0.12% - before giving up
```

During vulnerability scanning, **OSVDB-3092** detects a file that may be used to identify the blogging site software. What is the name of the file?
```bash
# grep -r /var/lib/nikto 'OSVDB' | grep 3092 
# My version of nikto cannot find this!!'
# OSVDB is stup down!
# Dorked into the oblivion to brute force this answer
license.txt
```

![](myniktodatabase.png)
And the Writeup 
![](sahilmalvisnitkodb.png)

What is the name of the software being used on the standard HTTP port?
![](question12.png)
```
lighttpd
```

What is the flag value associated with the web page hosted on port 8000?
```
I am too jade by other content creators rabbit holes that this was just missed out of presuming it to be a rabbithole.
- Use gobuster on port 8000
```

## Post-Root-Reflection

- Manual review is very important
- Worrying about an Insane machine of season 4 HTB 
- Scanning is never ever pentesting
## Beyond Root

No time just points and testing speed