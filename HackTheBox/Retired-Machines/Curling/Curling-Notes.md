# Curling Notes

## Data 

IP: 10.10.10.150
OS: Ubuntu Bionic
Hostname:
Machine Purpose: Web server
Services: 80 - Joomla 3.8
Service Languages: php 
Users: Floris 
Credentials:

## Objectives

## Target Map

![](Curling-map.excalidraw.md)

## Solution Inventory Map


### Todo 

Joomla version
Injectables


### Done

 SSH-2.0-OpenSSH_7.6p1 Ubuntu-4ubuntu0.5 = Bionic

Root Page - curling2018 is password like
cewlcurlings.png
      

Url controllable component
printthis.png
stdoutfromprint.png

CVE2004-2047 - nikto is false positive

Trouble scrapping data and manual enumeration for an extract version from Joomla.xml it is either 3.6 or 3.8.8, but it seems like 3.8.8 as it is the version tag [REF](https://docs.joomla.org/Manifest_files). Then I reread the Joomla.xml to find:
foundit.png

I tried along :
```
admin  : curling2018 
floris : curling2018 
```

```bash
# Kali-Rolling
sudo apt install joomscan
# Scan using random agent through burpsuite proxy while trying to enumerate components
joomscan -u http://target.com -ec -r- --proxy 127.0.0.1:8080
```
Joomscan declares we are not vulnerable, nothing from searchplsoit

[HackTricks](https://book.hacktricks.xyz/network-services-pentesting/pentesting-web/joomla) -  once we have Administrator credentials we have RCE potential

Make the "Cewl" webserver is a ctf hint for use cewl 
```bash
# To spider a site for a given depth and minimum word length
cewl -d 3 -m 5 http://10.10.10.150 --with-numbers 
```