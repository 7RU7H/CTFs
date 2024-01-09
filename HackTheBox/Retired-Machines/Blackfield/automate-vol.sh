#!/bin/bash


echo "Move the dumped memory file(s) into the tmp directory!"
echo "Have you updated the cache? `cd /opt/volatility3; python3 vol.py --clear-cache windows.info"

mkdir /tmp/$projectName-vol-output


# Replace with linux for seperate script


python3 /opt/volatility3/vol.py -f /tmp/$FILE  -o /tmp/$projectName-vol-output

