# PayDay Notes

## Data 

IP: 192.168.177.39
OS:
Arch:
Hostname:
DNS:
Domain:  / Domain SID:
Machine Purpose: 
Services:
Service Languages:
Users:
Email and Username Formatting:
Credentials:

## Objectives

`\#\# Target Map ![](PayDay-map.excalidraw.md)`

#### Machine Connects to list and reason:

[[]] - Reason X

## Data Collected

#### Credentials
```
```

#### HUMINT


#### Solution Inventory Map


#### Todo 


#### Timeline of tasks completed


Beyond root - turn off nuclei auto exploitation of LFIs and also manual enumerate this LFI
TODO: get the password from the config file of the port 80 webserver
[[cs-cart-unauthenticated-lfi]]
nikto/http.txt is utter nuts - add to write up

cmethepayday.png
thanksnuclei.png
www-root.png
adminadmin.png

alreadyregisteredadmin.png

findingthevulninthefirstplacewithoutnuclei.png

There are supported versions up to 4.17.X - but the copyright is 2006 on PayDay
https://docs.cs-cart.com/latest/

versionfoundmanually.png


https://www.exploit-db.com/exploits/48891 and https://www.exploit-db.com/exploits/48890

Authenticated RCE
https://www.exploit-db.com/exploits/48891
- more explanation https://gist.github.com/momenbasel/ccb91523f86714edb96c871d4cf1d05c

`admin : admin`
adminadminadminpanels.png


1.3.4 test LFI, # CS-Cart 1.3.3 - 'classes_dir' LFI
https://www.exploit-db.com/exploits/48890

> [F-Masood](https://gist.github.com/F-Masood) - The above explanation is lacking some information, so here is a better explanation:
> 1. Visit "cs-cart" /admin.php and login (Remember: You need to login on **ADMIN** section not on the regular **USER** section).
> 2. Under **Look and Feel** section click on "**template editor**".
> 3. And under that section, upload your malicious **.php** file, make sure you rename it to **.phtml** before you upload.
> 4. If successful, you should be able to get a **RCE**.
> 5. For example, grab this file -> [https://raw.githubusercontent.com/F-Masood/php-backdoors/main/whoami.php](https://raw.githubusercontent.com/F-Masood/php-backdoors/main/whoami.php) and rename it to whoami.phtml
> 6. Now, visit http://[victim]/skins/whoami.phtml
> 7. And you should see '**www-data**' or '**apache**' etc as the output.

alltheshellspoc.png

fromthereverseshell.png

- /.rnd  
- credentials 
	- config.php
		- db_user root : db_password root
		- YOUSVERYSECRETKEY blowfish crypt key for db_type
		- auth_code 1NJC5M2E 
There  is no /dev/tcp file on the box

negthessh.png
```
Unable to negotiate with 192.168.177.39 port 22: no matching host key type found. Their offer: ssh-rsa,ssh-dss
```

1NJC5M2Enotpatricks.png

apache2 config
opt

searchablerootfiles.png


```
ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEAzx6C2kxbb2qPx9eRyW072CYpMhpa2zAlzgdBcElRS49cvTJlDcjqvC8DlpZL9FplzcfpCmD2xisb0VdHUtG2iteYQG5WaxUEeHd4t9XRqA9zCU3QjKq4jIDoT1A54HYLoEBk/jTxjUbaczfoFSgcZEOivBIZEM6usJW4gDgbpok1UoxHfmn7rRs43rgBKxKMpFZyp0+MsDlvKMZUie6F0mY60E2YSlwoyLAJKi0q1/oWB5Kmd3YtP20LIsVqvmbX7zcMXwXgztff0Wxj1dps0x6i1StYx1l14sU84comlceyZjzeYpqMoL+4OtWt4goqTqpiQasnXfv2vhNvCQXQaQ== root@explorer
```

127.0.0.1 3306 - mysql

filezillacapture.png

`brett : ilovesecuritytoo`
brett-ilovesecuritytoo.png

nobinexecfromdevshm.png