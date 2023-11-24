# Thor Notes

## Data 

IP: 192.168.198.208
OS: Linux
Arch:
Hostname:
DNS: Lite, offs.ec
Domain:  / Domain SID:
Machine Purpose: 
Services: http 80,10000 : webmin 1.962 , 7080 litespeed
Service Languages:
Users: jfoster - probably
Email and Username Formatting:
Credentials:

## Objectives

`\#\# Target Map ![](Thor-map.excalidraw.md)`

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
      

liteerror.png
jsdisabled.png
janefoster.png
litespeedwebserver.png
lite10000gobuster.png
webminmsfexploit.png

https://www.exploit-db.com/exploits/49318

```bash 
echo "192.168.198.208 Lite offs.ec" | sudo tee -a /etc/hosts
```

webminwebmin.png

jfoster : webmin, foster, jfoster, litespeed (plus varients)
admin :admin

ohgreatblockforauth.png

blocked.png

Importing exploits from `searchsploit` into `metasploit` - adapted from [Daniel Redfern video](https://www.youtube.com/watch?v=eWdfr1CcmJc)
```bash
mkdir ~/.msf4/modules/exploits
sudo cp $pathToSearchsploitExploit ~/.msf4/modules/exploits/
sudo chown kali:kali -R ~/.msf4/modules/exploits/
```

importingssexploit.png

ifihavetoguessthepasswordIwilljustmakethisawriteup.png

rescanfound7080.png

morelogins.png

```
-b $statusCodeBlacklist
--exclude-length 
```

Another auth for 7080

https://www.exploit-db.com/exploits/49556


rtfm.png

No simply sqli ib logins

nobruteforcingthe7080passwd.png

infiniteconnectionresetfromwebmin.png

infiiniteresetfrom7080.png

Hint 1 taken: *A webservice has a login page that can be brute forced*

- It is not 10000, because that IP blocks and 7080 10k passwords from rockyou.txt is more than enough time
- Password managling jfoster and other words - todo
- Try jfoster as the account for 7080 - todo

Jane Foster as password password list
```bash
mp32 --custom-charset1='!@#$%^' janefoster?d?1 > mp32-passwd.lst
mp32 --custom-charset1='!@#$%^' jfoster?d?1 >> mp32-passwd.lst
mp32 --custom-charset1='!@#$%^' janefoster?d?1 >> mp32-passwd.lst
mp32 --custom-charset1='!@#$%^' Janefoster?d?1 >> mp32-passwd.lst
mp32 --custom-charset1='!@#$%^' Jfoster?d?1 >> mp32-passwd.lst
mp32 --custom-charset1='!@#$%^' JFoster?d?1 >> mp32-passwd.lst
mp32 --custom-charset1='!@#$%^' JaneFoster?d?1 >> mp32-passwd.lst
cat mp32-passwd.lst | wc -l
```

9k~ password attempt on rockyou with jfoster
hydraverysadrockyou.png

nojanefosterpasswordswriteupitisthen.png

`admin:Foster2020` and the writeup state version name of webserver `Openlitespeed WebServer 1.7.8`. Close 

slappedbythepasswordgenerationandguessfortheopenlitespeedadminpanel.png

- Can host vhosts, listeners (web servers listening on some port), web socket proxy
- VHOST template
	- File must be located `$SERVER_ROOT/conf/templates/ with a ".conf"` or be created within panel
		-  A listener-to-virtual host mapping required - so remember if you turned it off/on in backdoor jitters or dormancy 
	- *When you delete the template, the entry will be removed from your configurations, but the actual template config file will not be deleted.*
	- Beware context dependent DNS artefacts to host per context  
	- Instantiated .conf file
		- `$VH_ROOT/public_html/ or $SERVER_ROOT/$VH_NAME/public_html`
- **Beware Admin Console `Allow List` accepts wildcard IP or sub-networking is allowed!** Easy rogue servers 
-  Script handlers
	- *Suffixes must be unique.*
	- *Comma delimited list with period "." character prohibited.*
	- Documentation advise configure MIME settings 
		- But default *The server will automatically add a special MIME type ("application/x-httpd-[suffix]")*
- Tools
	- `Compile php` just means Interpret to intermediary language then compile Machine code.. 
	- Log viewer
[Cloud images](https://docs.litespeedtech.com/cloud/images/?utm_source=Open&utm_medium=WebAdmin) deploy LiteSpeed images to most cloud providers
php version in the *interpret* my php does not host the same version and want to download another! Intended path...
phpversionintheinterpretmyphpisdoesnothostthesameversion.png

runningasalinuxdaemon.png

Enjoying not remember I already enumerated version and exploit by rewriting my behaviour to RTFM and reusing `xargs` again.
```bash
searchsploit openlitespeed | grep '1.7.8' | awk -F\| '{print $2}' | xargs -I {} searchsploit -m {}
```

Do both .txt and scripted for the bad system admin experience 

Slow down not too excited

- Script
	- Changed https -> http
	- data { .. "path" = "", .. } 

Beyond root

Cupp Cheatsheet added - although I wont use it. Just no. The important lesson is that wordlist generator questions for custom wordlists, which I rarely do because I do not profile users like the various real world scenarios. Improve the original attempt to account for  

Password guessing - calculate Time if automated by number of attempt to remembering to do something that is probably trained out people years after it was an issue in pre-2022
- `NameYEAR`
- `NameSEASON`

Wordlist generation questions
- Profiling
	- Are you accounting for the stupid inclusive of positional possibilities 
		- `WORD{TEMPORARIAL-WORD}` - Year, Season, etc
		- Project 
		- 
	- OSINT-able
		- Hobby
		- Pet names


- Future considerations based off advise from security professionals
	- Regional specific syntax  > 3, < 8 words 
	- Culture 
	- a, the, is, 
	- Predictable white space - one white space or two white space depending on regular use 
	- Tech-Level specific
		- Hedging to clever: `eRrm BLAH.. normal words`
		- Developers 
			- Tab or spaces 
			- Commonly used applications affecting typing behaviours 
	- Application / Hardware
		- Multi-Lingualism in English speaking countries is encouraged with applications - preferable words
	- Account for normal people using l33t speak whether they know it or not 
	- Expletives are easier to remember 
	- Expletives add length - user incentive with long strong password to add more words that are more memorable 

```bash
mp32 --custom-charset1='!@#$%^' janefoster?d?1 > mp32-passwd.lst
mp32 --custom-charset1='!@#$%^' jfoster?d?1 >> mp32-passwd.lst
mp32 --custom-charset1='!@#$%^' janefoster?d?1 >> mp32-passwd.lst
mp32 --custom-charset1='!@#$%^' Janefoster?d?1 >> mp32-passwd.lst
mp32 --custom-charset1='!@#$%^' Jfoster?d?1 >> mp32-passwd.lst
mp32 --custom-charset1='!@#$%^' JFoster?d?1 >> mp32-passwd.lst
mp32 --custom-charset1='!@#$%^' JaneFoster?d?1 >> mp32-passwd.lst
cat mp32-passwd.lst | wc -l
```
