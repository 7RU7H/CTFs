#!/bin/bash
# TODO NEEDS FIXING
ARGTOTAL=2
USAGE="Usage: $0 <OS/THM/HTB> <Boxname>"
if [ "$#" -ne $ARGTOTAL ]; then
	echo $USAGE
	exit
fi
OS="OS-ProvingGrounds"
THM="TryHackMe/Markdown"
HTB="HackTheBox"
if [$site != $1] && [$name != $2];
then
	if [$1 == "OS"] || [$1 == "THM"] || [$1 == "HTB"];
	then
		site=$1
		name=$2
	else 
		echo $USAGE
		exit
	fi
	mkdir $site/$name/Screenshots -p
	cd $name
	mkdir nmap && mkdir nikto && mkdir feroxbuster 
	echo "
	Name: $name
       	Date:  
	Difficulty:  
	Description:  
	Better Description:  
	Goals:  
	Learnt:



	## Recon
	
	## Exploit

	## Foothold

	## PrivEsc

      "	> $name-Writeup.md
else
	echo $HELP
	exit
fi

