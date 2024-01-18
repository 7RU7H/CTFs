#!/bin/bash

if [ "$#" -ne 1 ]; then
	echo "Usage: $0 <binary>"
	exit
fi

echo "# $1 Enumeration Data" | tee -a $1.enumdata
echo "" | tee -a $1.enumdata
echo "#### \`file\`" | tee -a $1.enumdata
file $1 | tee -a $1.enumdata
echo "" | tee -a $1.enumdata
echo "#### \`strings -e s\`" | tee -a $1.enumdata
strings -e s $1 | tee -a $1.enumdata
echo "" | tee -a $1.enumdata
echo "#### \`strings -e S\`" | tee -a $1.enumdata
strings -e S $1 | tee -a $1.enumdata
echo "" | tee -a $1.enumdata
echo "#### \`strings -e b\`" | tee -a $1.enumdata
strings -e b $1 | tee -a $1.enumdata
echo "" | tee -a $1.enumdata
echo "#### \`strings -e l\`" | tee -a $1.enumdata
strings -e l $1 | tee -a $1.enumdata
echo "" | tee -a $1.enumdata
echo "#### \`checksec\`" | tee -a $1.enumdata
checksec $1 | tee -a $1.enumdata
