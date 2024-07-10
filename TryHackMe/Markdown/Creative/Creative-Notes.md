# Creative Notes

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
```
Matthew Davis
Barbara Ross
Karen Perry
Ashley Diaz
Edward Harris
Brian Scott
```
Credentials:



#### Mindmap-per Service

```
sudo tcpdump -nvvvXi tun0 tcp port 80
ls -1tr Screenshots | grep -v ping | awk '{print "![]("THM")"}\' | xsel -b
```

- OS detect, run generate noting for nmap
	- 22 - terrapin cve; 
	- 80
		- usernames
		- devcrud

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
-
-
-

time issues with regex expression but try again
```
# get usernames
curl http://creative.thm/  -s | grep card-title | awk -F\> '{print $2}' | cut -d\< -f 1

```

#### Todo List


