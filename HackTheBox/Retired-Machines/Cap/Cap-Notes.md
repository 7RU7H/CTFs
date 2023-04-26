# Cap Notes

## Data 

IP: 10.129.198.137
OS: Ubuntu
Hostname:
Domain:  / Domain SID:
Machine Purpose: 
Services: 
- SSH-2.0-OpenSSH_8.2p1 Ubuntu-4ubuntu0.2
- https://github.com/benoitc/gunicorn
- https://colorlib.com/wp/themes/ 
Service Languages:
Users: Nathan
Credentials:

## Objectives

## Target Map

![](Cap-map.excalidraw.md)

## Solution Inventory Map


`ifconfig` - cmd
```
eth0: flags=4163<UP,BROADCAST,RUNNING,MULTICAST>  mtu 1500
        inet 10.129.198.137  netmask 255.255.0.0  broadcast 10.129.255.255
        inet6 fe80::250:56ff:fe96:37af  prefixlen 64  scopeid 0x20<link>
        inet6 dead:beef::250:56ff:fe96:37af  prefixlen 64  scopeid 0x0<global>
        ether 00:50:56:96:37:af  txqueuelen 1000  (Ethernet)
        RX packets 5345  bytes 414573 (414.5 KB)
        RX errors 0  dropped 0  overruns 0  frame 0
        TX packets 1908  bytes 889982 (889.9 KB)
        TX errors 0  dropped 0 overruns 0  carrier 0  collisions 0

lo: flags=73<UP,LOOPBACK,RUNNING>  mtu 65536
        inet 127.0.0.1  netmask 255.0.0.0
        inet6 ::1  prefixlen 128  scopeid 0x10<host>
        loop  txqueuelen 1000  (Local Loopback)
        RX packets 3701  bytes 291201 (291.2 KB)
        RX errors 0  dropped 0  overruns 0  frame 0
        TX packets 3701  bytes 291201 (291.2 KB)
        TX errors 0  dropped 0 overruns 0  carrier 0  collisions 0
```

netstat shows connections that were present at time of configuration - NOT Modified
```c
Proto Recv-Q Send-Q Local Address           Foreign Address         State       User       Inode      PID/Program name     Timer
tcp        0      0 127.0.0.53:53           0.0.0.0:*               LISTEN      101        34329      -                    off (0.00/0/0)
tcp        0      0 0.0.0.0:22              0.0.0.0:*               LISTEN      0          37158      -                    off (0.00/0/0)
tcp        0      0 0.0.0.0:80              0.0.0.0:*               LISTEN      1001       36211      -                    off (0.00/0/0)
tcp        0      1 10.129.198.137:33164    8.8.8.8:53              SYN_SENT    101        43719      -                    on (7.76/3/0)
tcp        0      0 10.129.198.137:80       10.10.14.40:60706       ESTABLISHED 1001       43720      -                    off (0.00/0/0)
tcp6       0      0 :::21                   :::*                    LISTEN      0          36883      -                    off (0.00/0/0)
tcp6       0      0 :::22                   :::*                    LISTEN      0          37160      -                    off (0.00/0/0)
udp        0      0 127.0.0.1:56867         127.0.0.53:53           ESTABLISHED 102        43718      -                    off (0.00/0/0)
udp        0      0 127.0.0.53:53           0.0.0.0:*                           101        34328      -                    off (0.00/0/0)
udp        0      0 0.0.0.0:68              0.0.0.0:*                           0          32598      -                    off (0.00/0/0)

...
lots of :
...
tcp        0      0 10.129.198.137:80       10.10.14.40:58344       TIME_WAIT   0          0          -
tcp6       0      0 10.129.198.137:21       10.10.14.40:36472       ESTABLISHED 0          85986      -


```

http://osr600doc.sco.com/en/man/html.ADMN/dhcpc.ADMN.html /UDP 68 is running as root, but the pcap is no referencing any UDP connections

#### 1.PCAP

10.10.14.40 
gunicorn

- Try interact with website with wireshark open to TSVal, TSecr
[How TCP Works - Chris Greer](https://www.youtube.com/watch?v=4EFEdAyxemk)
- TCP does not know architecture
Timestamps option can reduce retransmission timer 
SACK_PERM TSVal, TSecr - Timestamp Value and Timestamp Echo Reply - do not confused with [[Wireshark]] Timestamping
- Some increment by the miliseconds or network latency

NOT Modified
https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/304
Everything it loads a page for this user the server runs through every js and ccs file indictated by the packet length



#### WP?

https://colorlib.com/wp/themes/ - but cant find wp directory and wpscan report nowordpress
https://wpscan.com/plugin/owl-carousel - is for wordpress
#### Screenshots

webroot.png
moreusers.png
freepcap.png
ifconfigforfree.png
intriguingports.png
tsvalandtsecr.png
alotofnotmodified.png
nolfiondownload.png
404whereiswp.png
resolvinghowdataisthere.png
feroxbusterwherewp.png
noinjections.png

### Todo 

Find credentials used somewhere in the 
- Not the pcap

### Done

Password sprayed ftp with `hydra -l nathan darknet-2017-top1000.txt` no success


```bash
cat webroot.html |  grep email | sed 's/<h4>//g' | uniq | grep -v '<p>\|<h5>' | awk '{print $1}' > users.txt
echo "Ratul Hamba" >> fullname-users.txt
echo "Kaji Patha" >> fullname-users.txt
echo "Nathan" >> users.txt
echo "Kaji" >> users.txt
```

