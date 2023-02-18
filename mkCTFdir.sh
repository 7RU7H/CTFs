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
	mkdir -p $site/$name/{nmap,nikto,feroxbuster,masscan,gobuster,ffuf,Screenshots};
	echo "# $name Writeup

Name: $name
Date:  
Difficulty:  
Goals:  
Learnt:
Beyond Root:

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)
	
## Exploit

## Foothold

## PrivEsc

## Beyond Root

      "	> $name-Writeup.md
echo "# Notes

## Data 

IP: 
OS:
Hostname:
Domain:  / Domain SID:
Machine Purpose: 
Services:
Service Languages:
Users:
Credentials:

## Objectives

## Solution Inventory Map

### Todo 

### Done


      
      " > $name-Notes.md

else
	echo $HELP
	echo "Error Site or Name is empty $1 and $2"
	exit
fi

