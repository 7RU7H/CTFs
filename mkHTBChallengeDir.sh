#!/bin/bash
# Script to make HTB challange writeup and walkthroughs and notes directories.

USAGE="Usage: $0 <Challenge Name> <Challenge Type>"
if [ "$#" -ne 2 ]; then
	echo "Error invalid argument total "
	echo $USAGE
	exit
fi
blank=""
if [ "$1" != "$blank" ] || [ "$2" != "$blank" ];
then
	mkdir HackTheBox/Retired-Challenges/$2/$1/Screenshots -p;
	cd HackTheBox/Retired-Challenges/$2/$1;
	echo "# $1 Write-up

Name: $1
Type: $2
Date:  
Difficulty:  
Goals:  
Learnt:

## Post-Completion Reflection

	"	> $1-Writeup.md
else
	echo $HELP
	echo "Error Name: $1 or Type: $2 is empty"
	exit
fi

