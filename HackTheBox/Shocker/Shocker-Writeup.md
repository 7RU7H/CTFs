
# Shocker
Name: Shocker 
Date:
Difficulty: Easy
Description: 
Goals: OSCP Prep.
Learnt:

![asd](bug.jpg)
## Recon 
Full extensive `nmap -sC -sV -p- and -sU` and `--script discovery`  scans can be found in the `nmap/`

```bash
ping -c 3 10.129.1.175                                                                        
PING 10.129.1.175 (10.129.1.175) 56(84) bytes of data.
64 bytes from 10.129.1.175: icmp_seq=1 ttl=63 time=47.8 ms
64 bytes from 10.129.1.175: icmp_seq=2 ttl=63 time=61.4 ms
64 bytes from 10.129.1.175: icmp_seq=3 ttl=63 time=44.9 ms

--- 10.129.1.175 ping statistics ---
3 packets transmitted, 3 received, 0% packet loss, time 2003ms
rtt min/avg/max/mdev = 44.943/51.410/61.439/7.190 ms

## Stego

Nothing special 

```bash
exiftool bug.jpg
ExifTool Version Number         : 12.41
File Name                       : bug.jpg
Directory                       : .
File Size                       : 36 KiB
File Modification Date/Time     : 2022:05:17 17:39:27+01:00
File Access Date/Time           : 2022:05:17 17:39:27+01:00
File Inode Change Date/Time     : 2022:05:17 17:39:48+01:00
File Permissions                : -rw-r--r--
File Type                       : JPEG
File Type Extension             : jpg
MIME Type                       : image/jpeg
JFIF Version                    : 1.01
Resolution Unit                 : None
X Resolution                    : 1
Y Resolution                    : 1
Comment                         : CREATOR: gd-jpeg v1.0 (using IJG JPEG v62), quality = 90.
Image Width                     : 820
Image Height                    : 420
Encoding Process                : Baseline DCT, Huffman coding
Bits Per Sample                 : 8
Color Components                : 3
Y Cb Cr Sub Sampling            : YCbCr4:2:0 (2 2)
Image Size                      : 820x420
Megapixels                      : 0.344

binwalk -e bug.jpg            

DECIMAL       HEXADECIMAL     DESCRIPTION
--------------------------------------------------------------------------------
0             0x0             JPEG image data, JFIF standard 1.01
```

No vhosts
```bash
gobuster vhost -u http://10.129.1.175/ -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt 
===============================================================
Gobuster v3.1.0
by OJ Reeves (@TheColonial) & Christian Mehlmauer (@firefart)
===============================================================
[+] Url:          http://10.129.1.175/
[+] Method:       GET
[+] Threads:      10
[+] Wordlist:     /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt
[+] User Agent:   gobuster/3.1.0
[+] Timeout:      10s
===============================================================
2022/05/17 17:46:56 Starting gobuster in VHOST enumeration mode
===============================================================
                              
===============================================================
2022/05/17 17:47:50 Finished
===============================================================
```

Nothing for nikto apache users plugin

Weirdly my gobuster did not list cgi bins at all and neither feroxbuster or nikto. Coming back to this box I seclist raft-small-words, which definitely contains `cgi-bin` . Reran nikto

I sneaked at look as it is a retired box, because I tried everything.

![cgibin](cgi-bins-forbidden.png)

![hangs](cgi-bin-hangs.png)

![forbidden](cgi-bins-forbidden.png)
[tried](https://www.infosecarticles.com/exploiting-shellshock-vulnerability/)

![not found](cgi-bin-not-found.png)

Watched the Ippsec Video. HE found it. Using both gobuster and feroxbuster, both by default recording `403`. 

![status-codes](feroxbuster-defaults-status-codes.png)

```bash
ffuf -w /usr/share/seclists/Discovery/Web-Content/directory-list-2.3-small.txt:FUZZ -u  http://10.129.54.129/cgi-bin/FUZZ.sh
```

 `user.sh` is an script in this directory 

![burp](burp-user-sh.png)

Trying the reverse shell from the aforementioned article:

![sss](shellshockshell.png)

## PrivEsc

[We need ask a Perl Programmer how to PrivEsc...](https://www.youtube.com/watch?v=0jK0ytvjv-E)
![perl](sudoperlpwnage.png)

`sudo perl -e 'exec "/bin/sh";'` from the [GTFObins](https://gtfobins.github.io/gtfobins/perl/#reverse-shell)

![rooted](root.png)