# Relevant Notes

## Data 

IP: 10.10.15.133
OS: Windows Server 2016 Standard Evaluation 14393 x64 
Arch: x64
Hostname: Relevant
DNS:
Domain:  RELEVANT
Domain SID:
Machine Purpose: 
Services: rpc,smbv1, http, rdp
Service Languages: ASP (x-aspnet-version header: 4.0.30319)
Users:
Email and Username Formatting:
Credentials:

- https://steflan-security.com/tryhackme-relevant-walkthrough/
- till `nmap -p 139,445 -Pn --script smb-enum* 10.10.14.1`
- Strike one of wtf I am even doing this machine is behaving so weird for over 3 hours.
#### Mindmap-per Service

- OS detect, run generate noting for nmap
	- OH god the re-scanning finally got there just to fuck with my wanting to script nmap notes... 
	- [[10-Relevant-NMAP-Notes]]
	- weird.cert
	- wtfisgoingonwiththismachine.png 
	- This is the first machine I have ever have nmap fail so bad
	- `nmap -p 139,445 -Pn --script smb-enum* 10.10.14.1` worked but everything else did not?
	- cme share 
-
-
-
-
-
-
-
-
-
-

```
xsel -b  | sed 's/|.//g' > weird.cert
```

#### Todo List


Note - Nothing in this room requires Metasploit

```bash
echo Qm9iIC0gIVBAJCRXMHJEITEyMw== | base64 -d
Bob - !P@$$W0rD!123

echo QmlsbCAtIEp1dzRubmFNNG40MjA2OTY5NjkhJCQk | base64 -d
Bill - Juw4nnaM4n420696969!$$$
```


```
echo Qm9iIC0gIVBAJCRXMHJEITEyMw== | base64 -d && echo "" && echo QmlsbCAtIEp1dzRubmFNNG40MjA2OTY5NjkhJCQk | base64 -d

Bob - !P@$$W0rD!123
Bill - Juw4nnaM4n420696969!$$$

```


```
rpcclient $> lookupnames bob
bob S-1-5-21-3981879597-1135670737-2718083060-1002 (User: 1)
rpcclient $> lookupnames administrator
administrator S-1-5-21-3981879597-1135670737-2718083060-500 (User: 1)
```

Windows Server 2016 Standard Evaluation 14393 x64 (name:RELEVANT) (domain:Relevant) (signing:False) (SMBv1:True)