#!/bin/bash

if [ "$#" -ne 1 ]; then
	echo "Usage: $0 <Text file that is list of potential vulnerable applications found>"
	exit
fi

inputFile=$1
vulnApps=$(cat $inputFile | awk -F/ '{print $1}')
echo "# Vuln Apps Searchsploit Table

Application | Searchploit Output 
--- | ---
" | tee -a Vuln-Apps-Table.md
for app in $vulnApps; do
	result=$(searchsploit --colour $app | grep -v "Exploits: No Results\|Shellcodes: No Results\|Papers: No Results" | awk -F\| '{print $1" "$2}' | tr -d '-')
      	echo "$app | $result" | tee -a Vuln-Apps-Table.md
done
