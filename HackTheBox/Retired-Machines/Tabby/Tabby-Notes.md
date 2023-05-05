# Tabby Notes

## Data 

IP: 10.10.10.194
OS: Ubuntu 20.04 LTS
Hostname: tabby
Domain:  http://megahosting.htb/
Machine Purpose: 
Services: 22,80,8080
Service Languages: php
Users: ash / clive 
Credentials:

## Objectives

## Target Map

![](Tabby-map.excalidraw.md)

## Solution Inventory Map

databreachandrootaccess.png

tomcat9 - Version 9.0.31 `nuclei` [[tomcat-exposed-docs]] 
defaulttomcathomepage.png

`http://megahosting.htb/news.php?file=../../../../etc/passwd`
lfi.png
ash:x:1000:1000:clive:/home/ash:/bin/bash

tabby 
tabby.png

Ubuntu2004LTS.png

`Linux version 5.4.0-31-generic (buildd@lgw01-amd64-059) (gcc version 9.3.0 (Ubuntu 9.3.0-10ubuntu2)) #35-Ubuntu SMP Thu May 7 20:20:34 UTC 2020`
kernelversion.png

9.0.30
https://www.rapid7.com/db/vulnerabilities/apache-tomcat-cve-2020-13943/
https://www.infosecmatter.com/nessus-plugin-library/?id=132419



### Todo 

Make Excalidraw

### Done
      

