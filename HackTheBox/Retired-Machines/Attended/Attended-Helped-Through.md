# Attended Helped-Through

Name: Attended
Date:  
Difficulty:  Insane
Original Goals:  
- Understand what it takes to be this good
- Analyse xct workflow
- Buffer overflow revision, but also glean some tips
- Try smallest amount of recon possible and not any of my automated data collection for massive data crunching at some point.
- Perform "racing mind practice" -  a term that I likened to practising play music at speed - it is fluid continuous attempt to remain at at most peak for endurance  - 
- Finish in hour and a half. - SPOIL it did not happen I know it could have if naabu had worked, I was not correctly second guessing XCT on the smtp-user-enum "solution", also got impatient on the email call back from the attended.htb - then I reset the box incase I spammed the swaks too much
Learnt:
- Better SMTP TTPs!
- When I put capability pieces together I can actually achieve stuff!
Beyond Root:
- Patch the machine to prevent a buffer overflow
- Test all my Linux Battleground counter measures as VulnNet node shells would just take more time to configure 



[XCT is current top number one on HTB](https://www.youtube.com/watch?v=uAvvrBO7zlk) - *"We will solve Attended, a 50-point machine on HackTheBox. For user, we will be sending some emails back and forth and then append a payload that exploits a Vim RCE, followed by adding a malicious ssh config. For root, we will exploit a custom OpenBSD binary that is used as an AuthorizedKeysCommand for SSH"*. [XCT](https://app.hackthebox.com/users/13569) is currently ranked 1 on HTB with 14 user FBs, 28 system FBs - 248 solved machiens, 7 FB on challenges with 266 total solved, also end games and fortress completions. This guy is awesome. My hope is learn from maybe 10 boxes completed by XCT for the next 5 months or so. Along with Snowscan and few other I want to try be like the best as early as possible and be more than the average Ippsec viewer - Ippsec is still awesome, I want to surpass the average ASAP. 

Returning to finish this due to watching the Ippsec introduction to his video after being reminded how awesome this machine would be at the current point of time. I realised that I need to watch both as I am currently cleaning up my github projects one is a C2 framework - Ippsec makes his own. I also need to replace Empire with Sliver usage. Iptables defensive setup and usage for Battlegrounds and KOTH has been on my beyond roots. 

Because as part of testing my Helped-Through discourse the only written writeup that I trust is [0xdf](https://0xdf.gitlab.io/2021/05/08/htb-attended.html)

- Revised Goals
	- Sliver Usage
	- Improve my C2 project
	- Understand what it takes to be this good - Analyse XCT's tactics and workflow
	- Learn ROP-Chains improve my understanding of binary exploitation beyond old-OSCP stackbased buffer overflow
	- Write a Iptables defence 
	- SMTP

As of 2023 I have got a formula for Helped-Throughs down , after much testing and realised that Helped-Throughs should either be:
1. Stop and Start; Push till stuck and another 30 minutes, then stop and peak
	1. Beyond root should be box related and one or two tasks that take a maximum of 1 hour each 
2. Like a full on-project:
	- Multiple Writeups both video and written
	- One section must be challenge to try without support in an area of weakness - 1 hour maximum
	- It must document what you have learnt about your weaknesses and mistakes 
	- It must takes at atleast 12 hours on one machine and its topics
	- Research of surrounding relevant information must occur at depth
	- Beyond root should be extensive

Will be twinned with [[Attended-Helped-Through]], and other phishing boxes for phishing-for-2-weeks-24-7 banaza.
## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128. The TTL is  < 255 meaning this a freeBSD box.
![ping](HackTheBox/Retired-Machines/Attended/Screenshots/ping.png)

I also desparately tried naabu to no end as at the time Naabu was going through some development issues.  I got it to work with:
```bash
# Beware there is both the golang-go and official way to install go
# only way would be manually and granular control everything, but I think golang-go is ok I am just not sure which is actually the best - golang-go is not current most current pre dev branch of go 
sudo apt install -y libpcap-dev # naabu
# go install -v github.com/projectdiscovery/naabu/v2/cmd/naabu@latest
echo "\n#Naabu portscanner alias\nalias naabu='sudo ~/go/bin/naabu'" >> ~/.zshrc
source ~/.zshrc
```

Knowing that only port 22 and 25 are open on this machine helps in testing.
```bash
naabu -host 10.129.194.221 -p 0-25 -silent -o naabu-test
```

I also tried the golang-go version with `apt install naabu`. I also found it is network dependent, Naabu is not good when the rate limit has to drop to say 200. 
```bash
sudo naabu -host 10.129.176.146 -p 0-65535 -i tun0 -nmap-cli 'nmap -sV -sC -e tun0'
```


[Ippsec and viewers make tmux and clipboard copy and paste more affective](https://www.youtube.com/watch?v=ABVR8EgXsQU) 

```bash
cat $file | xsel --clipboard
cat $file | xclip -selection clipboard
# Tmux workflow Ippsec
```


Discovery nmap script displays the banner for SMTP including 
![](attendedhostname.png)
Ippsec also points out the bugs@openbsd.org! 

```bash
echo "10.129.176.146 attended.htb" | sudo tee -a /etc/hosts
```

Ippsec discusses Iptables - XCT used tcpdump to check connections back
```bash
# Show when box initiates a connection to our box
sudo iptables -A INPUT -p tcp -m state --state NEW -j LOG --log-prefix "IPTables New-Connection: " -i tun0
# Check the rules 
sudo iptables -L
# Check any messages 
sudo grep iptables /var/log/messages
```

Ippsec explains that the way that the email is sent directly to our IP is strange.
```bash
sudo python3 -m smtpd -n -c DebuggingServer $htbTun0:25 
```

We can enumerate manually with exchange of commands and data:
```bash
EHLO nvm
MAIL FROM:<>
# Manual user enumeration with RCPT as the email server will indicate if the user exists
# Test if we can send mail
RCPT TO:<root@attended.htb>
DATA
Subject: Provide a subject line with a new line space 

Message goes here
# end with "." on a line by itself
.
```

Emailing the hard way.
![](emailingthehardway.png)

- Tools dont work use wireshark or tcpdump to capture and visualize traffic. 
- Secondly as I am newer to insane machines were there is actual automated interaction of pseudo users these tools become even more vital as we need to check if automate user has connected back.

smpt-user-enum cannot enumerate the users as the

Vim motions
`244 j` and change `$username` with `<$username\@attended.htb>` or you could just write the user list correctly with cli, without have to ruin the source code.. or so I thought
```bash
cat users.txt | awk '{print $1"@attended.htb"}' > betterusers.txt
```

![](badsmtpenum.png)
Just missing a few things

![](almost.png)
You can supply the domain name with -D flag and the address from flag with -f so actually the tool is not broken at all my solution is better:
```bash
cat users.txt | awk '{print "<"$1"@attended.htb>"}' > betterusers.txt
# we do not need domain flag as it willl append to our custom users issue
smtp-user-enum -M RCPT -U betterusers.txt -f "<user@attended.htb>" -t 10.129.176.146
```

![](IjustneedtorecognisemycapabilitiesthatIknowaretheresometimeswithknown.png)

Target IP:
Attack IP:
guly  - is a username from a banner for some reason I thought this is somekind of service weird...it is not.
attended.htb - actual one of the box creators is the [Networked](https://app.hackthebox.com/machines/203) creator, which also had a guly

What is stated is that as the workflow leap for walkthrough video constraits for good content etc, is that we only have two ports open. So as ssh is not vulnerable and it is a CTF it is about how the enumerated users on the box interact and enumerating that. There is also nothing open on udp ports either.

Listener for emails and use SWAKS
```bash
# XCT has some kind of custom arch setup so uses sh and sudo for whatever reason
# sudo sh -c 'rlwrap nc -lvnp 25'
rlwrap nc -lvnp 25
# use swaks to automate the email sending
swaks --from 'root@attended.htb' --to 'guly@attended.htb' --header "Subject: Hello there" --body 'XCT, Ippsec and I are coming for your emails hide your user and root' --server attended.htb
```
Swaks - Swiss Army Knife SMTP, the all-purpose SMTP transaction tester. Returning to this with the Ippsec video,  I instead decided the python3 smtpd much like the  [[Kotarak-Helped-Through]] dev-rabbit-hole of wonder of ftp and servers general that the python3 modules are very robust and simple.  

```bash
sudo python3 -m smtpd -n -c DebuggingServer $htbTun0:25 

```

I  did not get a response back 
![1080](swaksnotincoming.png)

For linux reasons we need to have service running as root?
![](thxforemail.png)

```
220 proudly setup by guly for attended.htb ESMTP OpenSMTPD
250-proudly setup by guly for attended.htb Hello kali.kali [10.10.14.91], pleased to meet you
250 2.0.0: Ok
250 2.1.5 Destination address valid: Recipient ok
354 Enter mail, end with "." on a line by itself
```

New host name! - attendedgw.htb

Ippsec discovers through the full email exchange, whereas 0xdf used the OSINT part of the brain and remembered the `freshness` is the other author of this bopx. 

https://www.youtube.com/watch?v=ABVR8EgXsQU
https://0xdf.gitlab.io/2021/05/08/htb-attended.html
https://www.youtube.com/watch?v=uAvvrBO7zlk

## Exploit

## Foothold

## PrivEsc

## Beyond Root

#### XCT Workflow

- Always visible notes
- Simplist possible way first to objective 
 
 
 - How can I much my display better?
	- Where is the healthiest place for your head and neck?
	- Can you see you notes?

#### Things I need to do more

Log commands and clipboard dump them

```bash

tcpdump 
exiftool

```

#### Test HvH countermeasures

```bash
mount -o remount,noexec,rw,nosuid,relatime /dev/shm
```

Remove the webshells and its process Holo or a HTB ctf did this but did not kill the process.
```bash
* * * * * * root /bin/sleep 10 && for f in `/bin/ls /var/www/html | grep -v .html`; do p=$(ps -aux | grep $f | awk '{print $2}') && kill $p && rm /var/www/html/$f; done
```

Kill ptys
```bash
* * * * * root /bin/sleep 1  && for f in `/bin/ls /dev/pts`; do /usr/bin/echo nope > /dev/pts/$f && /usr/bin/pkill  -9 -t pts/$f; done
* * * * * root /bin/sleep 11 && for f in `/bin/ls /dev/pts`; do /usr/bin/echo nope > /dev/pts/$f && /usr/bin/pkill  -9 -t pts/$f; done
* * * * * root /bin/sleep 21 && for f in `/bin/ls /dev/pts`; do /usr/bin/echo nope > /dev/pts/$f && /usr/bin/pkill  -9 -t pts/$f; done
* * * * * root /bin/sleep 31 && for f in `/bin/ls /dev/pts`; do /usr/bin/echo nope > /dev/pts/$f && /usr/bin/pkill  -9 -t pts/$f; done
* * * * * root /bin/sleep 41 && for f in `/bin/ls /dev/pts`; do /usr/bin/echo nope > /dev/pts/$f && /usr/bin/pkill  -9 -t pts/$f; done
* * * * * root /bin/sleep 51 && for f in `/bin/ls /dev/pts`; do /usr/bin/echo nope > /dev/pts/$f && /usr/bin/pkill -9 -t pts/$f; done
```


#### Operation 8008Y 7R4P

adminuser only essential nc ping, vim, emac, nano, tmux=
docker container for ssh for admin, not builtin ssh

Triad Sandbox
One sandbox escape leads to another then another and then back to the orginal sandbox.
SELinux -> Python -> 

stty is set 1 row 1 cols
```bash
stty rows 1 cols 1
```


The circle of doom 
```
alias 'ls'=/bin/echo'
alias 'echo'=/bin/cd'
alias 'cat'=/bin/ls'
alias 'cd'=/bin/cat'
```

Return to hell aliasing - cd to a empty very blank named directory and parent directory
```
$PATH 
$ENV
$HOME
mkdir # massive directory 
alias 'cd //'
alias '~'
alias '/'
/directories

```

Randomise stdout that is base64 encoded, split base64 encoded again removed any `=` and conv to hex 

#### Probably well-known  Found Additions

Alerts
```
id,whoami, sudo,ss,netstat,cat /etc/passwd,groups, chatter,lsattr,curl,wget,passwd
```


https://michaelkoczwara.medium.com/sliver-c2-implant-analysis-62773928097f