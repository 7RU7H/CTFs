# Dynstr Notes

## Data 

IP: 10.129.121.237
OS:
Hostname:
Domain:  dyna.htb
Machine Purpose: 
Services: 53, 22, 80
Service Languages:
Users:
Credentials:

```
dynadns : sndanyd
```

```
[dns@dyna.htb](mailto:#)
```
## Objectives

## Target Map

## Solution Inventory Map

### Todo 




### Done

- List of Actions:
	- Command Injection `hostname=nvm.no-ip.htb&myip=10.10.14.70` 
		- `hostname=nvm.no-ip.htb&myip=10.10.14.70;id` `; & && %26 %26%26 | || \n ` 





Failed script... - Golang tools, 
```bash
#!/bin/bash

# Automate DynStr dynamic hosts while I do other things

DynStrHosts=(dnsalias dynamicdns no-ip)
for host in $DynStrHosts; do
        echo $host | xargs -I {} nikto -h {}.htb -o nikto/{}.txt;
        wait
        echo $host | xargs -I {} ~/go/bin/nuclei -u http://{}.htb -me nuclei-{};
        wait
        echo $host | xargs -I {} gospider -d 0 -s 'http://{}.htb' -a -d 5 -c 5 --sitemap --robots --blacklist jpg,jpeg,gif,css,tif,tiff,png,ttf,woff,woff2,ico,pdf,svg,txt  -o gospider-{};
        wait
        echo $host | xargs -I {} feroxbuster --url 'http://{}.htb' -w /usr/share/seclists/Discovery/Web-Content/raft-medium-words-lowercase.txt --auto-tune -r -A -o feroxbuster/{}-rmwlc;
        wait
done
```

