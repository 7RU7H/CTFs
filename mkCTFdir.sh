#!/bin/bash
# Script to building out my CTF writeup and walkthroughs and notes.
ARGTOTAL=2
USAGE="Usage: $0 <OS/THM/HTB> <Boxname>"
if [ "$#" -ne $ARGTOTAL ]; then
	echo "Error invalid argument total "
	echo $USAGE
	exit
fi
OS="OS-ProvingGrounds"
THM="TryHackMe/Markdown"
HTB="HackTheBox/Retired-Machines"
if [ "$site" != $1 ] || [ "$name" != $2 ];
then
	case "$1" in
		("HTB") site=$HTB; name=$2;;
		("OS") site=$OS; name=$2;;
		("THM") site=$THM; name=$2;;
		*) echo $USAGE; echo "Site shorthand invalid"; exit;;

	esac 
	mkdir -p $site/$name/{data,Notes,nmap,nikto,feroxbuster,masscan,gobuster,ffuf,Screenshots}
	echo "# 0 - TEMPLATE" > $site/$name/Notes/0-$name-Notes.md
	echo "# $name Writeup

Name: $name
Date:  
Difficulty:  
Goals:  
Learnt:
Beyond Root:

- [[$name-Notes.md]]
- [[$name-CMD-by-CMDs.md]]


![]($name-map.excalidraw.md)

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)
	
## Exploit

## Foothold

## Privilege Escalation

## Post-Root-Reflection  

![]($name-map.excalidraw.md)

## Beyond Root

" > $site/$name/$name-Writeup.md
echo "# $name Notes

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

\`\#\# Target Map ![]($name-map.excalidraw.md)\`

#### Machine Connects to list and reason:

[[]] - Reason X

## Data Collected

#### Credentials
\`\`\`
\`\`\`

#### HUMINT


#### Solution Inventory Map


#### Todo 


#### Timeline of tasks completed
      
" > $site/$name/Notes/$name-Notes.md
echo "# $name CMD-by-CMDs

\`\`\`bash
sed -i 's/$oldip/$newip/g' *-CMD-by-CMDs.md

ls -1 Screenshots | awk '{print\"![](\"\$1\")\"}'
\`\`\`

\`\`\`
\`\`\`

" > $site/$name/$name-CMD-by-CMDs.md

else
	echo $HELP
	echo "Error Site or Name is empty $1 and $2"
	exit
fi
exit
