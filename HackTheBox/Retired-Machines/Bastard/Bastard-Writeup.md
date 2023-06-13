
Name: Bastard
Date:  
Difficulty:  Medium
Goals:  
	- OSCP Prep
	- No Archive Notes! Use Red Team Field Manual and Google
Learnt:
- Drupal requires a apache, php and a database - either MySQL, MariaDB, Percona, PostgreSQL, SQLite
- XPath injection is like sql injection for XML [owasp](https://wiki.owasp.org/index.php/Testing_for_XPath_Injection_(OTG-INPVAL-010)

## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Bastard/Screenshots/ping.png)
Nikto had issues.
![](nikto-error-limit-reached.png)
Reattempted later on return to this box after enumerating months and months ago, just in case my heavy handed enumeration was the problem. The result:

Be aware of potential content-discovery migitation. Feroxbuster found: /node on 200, which just the same login page
![](doesnotlikedirbusting.png)
Gospider found more than nmap
![](gospider.png)

Can't Null authenicated with rpcclient on either port. Drupal 7, has durpalgeddon1 and 3 SQL injection and remote code executions. Finding the exact drupal version, both tools I know of are not maintained and there are no Kali tools that are Drupal enumeration tools.

Test differences with /node and /0/ all point of wwwroot login - potetnial rabbithole
/LICENSE.txt /MAINTAINERS.txt. Cannot traverse with `http://10.129.130.177/node?destination=CHANGELOG.txt`

As you would expect if you can view it /CHANGELOG.txt will contain the version number or potential decimal of which version it might be as the developments may be sharing the change log, but updating. It is not greater than 7.54, so no RCE - unless it is 7.58, but also no sql injection.  
![](drupalversion.png)
It could be a plugin or module or website misconfiguration though..
![](searchsploitdrupal.png)
 /cron.php - is forbidden like the other .txt files. The /install.php access 403 on update.php which could of potentially been used. These are rabbit holes /INSTALL.mysql.txt,/ INSTALL.pgsql.txt and /INSTALL.sqlite.txt of documentation as services arent externall facing. But does require a db internal facing so potential credentiual dumping  post exploit.
 ![](requiresadb.png) 

The xmlrpc.php found in robots.txt. [PHP manual](https://www.php.net/manual/en/book.xmlrpc.php) TL;DR xmlrpc are functions can be used to write XML-RPC servers and clients
![](xmlrpc.png)

[ExploitDB IIS 7.5 and Php misconfigurations](https://www.exploit-db.com/exploits/19033), which would be nice if we upload xml that made a php page to then execute, we PHP/5.3.28 which would use the the **--enable-libxml**, configuration flag. [parsing](https://www.php.net/manual/en/function.xmlrpc-parse-method-descriptions.php) [create an xmlrcp server](https://www.php.net/manual/en/function.xmlrpc-server-create.php) , documentation does not state what resource type we can actually create.  
[XXE](https://github.com/swisskyrepo/PayloadsAllTheThings/tree/master/XXE%20Injection#exploiting-xxe-to-retrieve-files)?

I could not find any initial 7.5 asp authenication bypasses or code disclosures.
```php
:$i30:$INDEX_ALLOCATION
:$i30:$INDEX_ALLOCATION/phppage.php
```

## Exploit

## Foothold

## PrivEsc

      
