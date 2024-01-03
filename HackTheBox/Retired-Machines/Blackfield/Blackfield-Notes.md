# Blackfield Notes

## Data 

IP: 10.129.229.17
OS:  DC01 Windows 10.0 Build 17763 x64 
Arch:
Hostname: DC01
DNS: BLACKFIELD.local
Domain: BLACKFIELD.local
Domain SID: S-1-5-21-4194615774-2175524697-3563712290
Machine Purpose: Domain Controller 
Services: 53 dns ,88 kerberos ,135 rpc,389,445,593,3268,5985, smb 445 (signing:True) (SMBv1:False), 
Service Languages:
Users: dc01, audit2020,support,svc_backup,lydericlefebvre and lots of generated
Email and Username Formatting:
Credentials:
```
support : #00^BlackKnight
```


#### Mindmap-per Service

- OS detect, run generate noting for nmap - check nmap

- 53
	- no domain transfer
	- `for ns in $(cat /usr/share/seclists/Discovery/DNS/$list.txt); do host $ns.$domain.$tld; done`
	- amass - found nothing..

- 88
- 135
	- NullAuth but not very good
	- CHECK support's access
- 389
- 445
	- Guest get read for `IPC$` and `profile$`
	- forensics share - probably audit2020
	- profile seems like a rabbithole of profiles
	- support has NETLOGON and SYSVOL
	- support spider plus
	- got sysvol share - policy 
- 593
- 3268
- 5985
	- 
-
-
```
ldapsearch -x -H ldap://10.129.229.17 -D 'support@BLACKFIELD.local' -w '#00^BlackKnight' -b "DC=BLACKFIELD,DC=local" | tee -a support-DUMP-entire-domain.ldapsearch

python3 bloodhound.py --dns-tcp -c all -d BLACKFIELD.local -dc DC01.BLACKFIELD.local -ns 10.129.229.17 -u support -p '#00^BlackKnight'

```

- support forcepassword audit2020
- svc_backup is the only member of remote mamagement
-
-
-
-
-



#### Todo List

- rescan tcp done


- password spray
- profile seems like a rabbithole of profiles -check potentially