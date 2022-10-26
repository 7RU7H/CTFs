#!/bin/bash


#files=$(find $1 -type name $2 2>/dev/null)
files=$(cat failthroughlist)
for file in $files; do
	new_name=$(echo $file | awk -F-Walkthrough '{print $1"-Helped-through.md"}')
	mv $file $new_name
done	
