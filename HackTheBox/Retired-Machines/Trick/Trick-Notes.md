# Trick Notes

## Data 

IP: 10.10.11.166 
OS:
Hostname:
Machine Purpose: 
Services: 22,25,53,80 - nginx 1.14.2 (no vulnerable)
Service Languages: 
Users:Enemigosss, John C Smith
Credentials:

## Objectives



## Target Map

![](Trick-map.excalidraw.md)

## Solution Inventory Map


### Todo 

Make Excalidraw

### Done

Inital DNS and stmp
nslookup.png
smtptelnetcheck.png


webroot.png

https://nvd.nist.gov/vuln/detail/CVE-2019-20372 - I have never done http request smuggling and it seems like something that is not the intended easy HTB machine path. 

No worries on js side
blankscripts.png

Submission validation is client-side
submission-cs.png

Out of scope
offsite.png

There is metasploit auxiliary module, but OSCP - and I then am just guess users with smtp-enum-users

Confirmation of trick.htb
confirmationoftrickhtb.png

Finally 
dnsrecon.png

And a domain transfer
zonetransfers.png

preprod-payroll.trick.htb root.trick.htb

DNS cookies
trick.htb : `c46160daceb0a392fd1e3411645e23e0cc692b0823635997`
root.trick.htb : `a33122c71b3aff6eebae44da645e2679912b4480fddca419`
preprod-payroll.trick.htb : `baf5bc3e97b9762a96d81f20645e263c1322a48f932041ef`


http://preprod-payroll.trick.htb/login.php 
loginformtopreprodpayroll.png


somethingisone.png

somethingisthree.png

readme.txt is a deadend
readmenot.png

Ferxobuster to the rescue
feroxbustertotherescue.png

department.png

Finally John C Smith
terriblebeerterriblesec.png

Employee No | Firstname |  Middlename |  Lastname | Department | Position 
--- | --- | --- | --- | --- | --- 
2020-9838 | John | C | Smith | IT Department | Programmer

[Username Generation](https://github.com/soxoj/username-generation-guide) 

SSH does not support authenication

Then I went to try the login and then
nocomprehendo.png

wtfisthis.png
The administrator is `Enemigosss`

thenitriedagain.png

It is not the session cookie...

Walking through the chain how that happened in burp
65.png
Then I went login 
66.png
But it [302](https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/302) somehow and then bypassed that login page... 
67.png

onlydifference.png

Headers
<iframe width="560" height="315" src="https://www.youtube.com/embed/GD6qtc2_AQA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>

..but then removing the headers did nothing
<iframe width="560" height="315" src="https://www.youtube.com/embed/YKNCEyQNlEs" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>