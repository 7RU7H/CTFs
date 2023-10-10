# Hetemit Notes

## Data 

IP: 
OS: centos
Arch:
Hostname: hetemit
DNS:
Domain:  / Domain SID:
Machine Purpose: 
Services:
Service Languages:
Users:
Email and Username Formatting:
Credentials:

## Objectives

`\#\# Target Map ![](Hetemit-map.excalidraw.md)`

#### Machine Connects to list and reason:

[[]] - Reason X

## Data Collected

#### Credentials
```
```

#### HUMINT


#### Solution Inventory Map

- cmeeks user

#### Todo 


- 18000 register and upload a web shell - comment on webp exploit
- Versioning of all the ruby packages

- Theere is a web-console somewhere
- rack - potential https://security.snyk.io/vuln/SNYK-RUBY-RACK-2848599 if I find how oto interact with it
- /users/:id(.:format) routes what is this formatting - I jget redirect on /user/:0/edit

#### Timeline of tasks complete

- samba version not vuln 
- ftp rabbit hole human-timeout service with FTP anonymous
- 50000 SSTI - seems very bare? 

- 18000 - is ruby application
	- Cookie is jibberish
	- Invite injection - it check for invite key, but do not have
	- File upload - cant upload without the invite key
	- Redirect on RCE system("whoami3")