# Notes

## Data 

IP: 
OS:
Hostname:
Domain:  / Domain SID:
Machine Purpose: 
Services:
Service Languages:
Users:
Credentials:

## Objectives

## Solution Inventory Map

### Todo 



Encrypted version with chroot jail
```bash
#!/bin/bash

# Need DNS to for MX records, certbot for encryption 
sudo apt install pure-ftpd certbot bind9 dnsutils -y
wait
# Configure DNS server - systranbox
echo "forwarders {  
8.8.8.8;  
8.8.4.4;  
};  
dnssec-enable yes;  
dnssec-validation yes;  
dnssec-lookaside . trust-anchor.ds;" | sudo tee -a /etc/bind/named.conf.options
# Configure DNS Zones
echo "zone “$domainPlusTLD” {  
type master;  
file “/etc/bind/db.$domainPlusTLD”;  
};  
zone “1.168.192.in-addr.arpa” {  
type master;  
file “/etc/bind/db.192”;  
};" | sudo tee -a /etc/bind/named.conf.local
# Create DNS Zone file and add contents
echo "$TTL 86400
@ IN SOA ns.$domainPlusTLD. root.$domainPlusTLD. (
1 ; Serial
604800 ; Refresh
86400 ; Retry
2419200 ; Expire
604800 ) ; Negative Cache TTL
;
@ IN NS ns
@ IN A 192.168.1.1
ns IN A 192.168.1.1 " | sudo tee -a /etc/bind/db.$domainPlusTLD
echo "$TTL 86400  
@ IN SOA ns.$domainPlusTLD.  
1 ; Serial  
604800 ; Refresh  
86400 ; Retry  
2419200 ; Expire  
604800 ) ; Negative Cache TTL  
;  
@ IN NS ns  
1 IN PTR ns.$domainPlusTLD" | sudo tee -a /etc/bind/db.192

# Change DNS on router by using Kali's default DHCP server 
# default will no use resolvconf serveice

# Secure File Permissions
sudo chown root:root /etc/bind/named.conf.options /etc/bind/named.conf.local /etc/bind/db.$domainPlusTLD /etc/bind/db.192
sudo chmod 744 /etc/bind/named.conf.options /etc/bind/named.conf.local /etc/bind/db.$domainPlusTLD /etc/bind/db.192

# Enable TLS only no plain text - LinuxBabe.com
echo 2 | sudo tee /etc/pure-ftpd/conf/TLS

sudo certbot certonly --standalone --preferred-challenges http --agree-tos --email $validemail -d ftp.$emaildomain

sudo groupadd ftpgroup
sudo useradd -g ftpgroup -d /dev/null -s /usr/sbin/nologin ftpuser
# Make and link the directory to the ftpsuer
mkdir 
sudo pure-pw useradd $user -u ftpuser # -d /ftp -m

# Chroot Jail the directory

sudo pure-pw mkdb
wait

# Make symbolic links for pure-ftp files - fuzzysecurity
ln -s /etc/pure-ftpd/pureftpd.passwd /etc/pureftpd.passwd
ln -s /etc/pure-ftpd/pureftpd.pdb /etc/pureftpd.pdb
ln -s /etc/pure-ftpd/conf/PureDB /etc/pure-ftpd/auth/PureDB

sudo mkdir -p /home/ftpuser
sudo chown -R ftpuser:ftpgroup /home/ftpuser



sudo systemctl restart pure-ftpd
```

#### FTP SFTP Battleplan


#### Objectives
- Dirty `python -m http.server` equivalient - test https://pypi.org/project/pyftpdlib/ more.. 
- KOTH secure sftp

####  Dirty option
Authbind addition from: https://0xdf.gitlab.io/2021/05/19/htb-kotarak.html#alternative-root-via-disk

```bash
# Some OSes may require authbin to allow binding at low ports without root
# sudo apt install authbind 
# wait
# FTP python module
pip3 install pyftpdlib
wait 
echo "Run with: authbind python3 -m  pyftpdlib -p21 <module flags for permissions>"
echo "Check python3 -m pyftpdlib -h for updated complete list"
echo "Usage: python -m pyftpdlib [options]

Start a stand alone anonymous FTP server.

Options:
  -h, --help
     show this help message and exit

  -i ADDRESS, --interface=ADDRESS
     specify the interface to run on (default all interfaces)

  -p PORT, --port=PORT
     specify port number to run on (default 2121)

  -w, --write
     grants write access for logged in user (default read-only)

  -d FOLDER, --directory=FOLDER
     specify the directory to share (default current directory)

  -n ADDRESS, --nat-address=ADDRESS
     the NAT address to use for passive connections

  -r FROM-TO, --range=FROM-TO
     the range of TCP ports to use for passive connections (e.g. -r 8000-9000)

  -D, --debug
     enable DEBUG logging level

  -v, --version
     print pyftpdlib version and exit

  -V, --verbose
     activate a more verbose logging

  -u USERNAME, --username=USERNAME
     specify username to login with (anonymous login will be disabled and password required if supplied)

  -P PASSWORD, --password=PASSWORD
     specify a password to login with (username required to be useful)

"
```


DNS and DNS server
TLS/SSL cert
User & Group
Database Linking
Chroot Jail for the directory
