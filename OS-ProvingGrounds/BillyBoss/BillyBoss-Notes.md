# BillyBoss Notes

## Data 

IP: 192.168.205.61
OS: Windows 10 Build 18362
Arch: x64 
Hostname: billyboss
DNS:
Domain: billyboss
Machine Purpose: 
Services:
- IIS 10,
- FTP
- smb 139,445 
- SANS STorm center port 5040
- 8081 Nexus 3.21.0-05 
Service Languages:
Users:
Email and Username Formatting:
Credentials:

## Objectives

`\#\# Target Map ![](BillyBoss-map.excalidraw.md)`

#### Machine Connects to list and reason:

[[]] - Reason X

## Data Collected

#### Credentials
```
```

#### HUMINT


#### Solution Inventory Map


#### Todo 

- BaGet version
- Enum 
	- v1,v2,v3,v4
	- API


- Get every single baget version and compare everything?
	- There are is one v2 and five v3 in 0.X.0 versioning nomenclature.  
	- Cant be v2 - so 5 v3 probably the earlier ones 
API Vulnerability that leads to credentials?


RCE Auth on 8081 - https://www.exploit-db.com/exploits/49385
#### Timeline of tasks complete
      
- Nikto get crazy output
- Found the api from upload 
- https://loic-sharma.github.io/BaGet/
- Nexus enumerate
- FTP requires ssl, but no 990
- API man enumerations
- Check the js running on  80 - obfuscated js is webpack dependecy bundler 
- Did not Ffuf for vhosts billyboss is a domain? - no tld
- favicon enumeration requires python mm3, codecs and shodan - or github  
- gospidered is good
- No /v2|3/readme