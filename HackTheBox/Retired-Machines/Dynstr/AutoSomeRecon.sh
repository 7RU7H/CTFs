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
