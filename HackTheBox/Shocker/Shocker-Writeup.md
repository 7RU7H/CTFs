

## Recon 
Full extensive `nmap -sC -sV -p- and -sU and discovery script scans` can be found in the `nmap/`

```bash
ping -c 3 10.129.1.175                                                                        
PING 10.129.1.175 (10.129.1.175) 56(84) bytes of data.
64 bytes from 10.129.1.175: icmp_seq=1 ttl=63 time=47.8 ms
64 bytes from 10.129.1.175: icmp_seq=2 ttl=63 time=61.4 ms
64 bytes from 10.129.1.175: icmp_seq=3 ttl=63 time=44.9 ms

--- 10.129.1.175 ping statistics ---
3 packets transmitted, 3 received, 0% packet loss, time 2003ms
rtt min/avg/max/mdev = 44.943/51.410/61.439/7.190 ms


nikto -h 10.129.1.175 -C all                                                  
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          10.129.1.175
+ Target Hostname:    10.129.1.175
+ Target Port:        80
+ Start Time:         2022-05-16 19:46:47 (GMT1)
---------------------------------------------------------------------------
+ Server: Apache/2.4.18 (Ubuntu)
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ Server may leak inodes via ETags, header found with file /, inode: 89, size: 559ccac257884, mtime: gzip
+ Apache/2.4.18 appears to be outdated (current is at least Apache/2.4.37). Apache 2.2.34 is the EOL for the 2.x branch.
+ Allowed HTTP Methods: OPTIONS, GET, HEAD, POST 
+ OSVDB-3233: /icons/README: Apache default file found.
+ 26522 requests: 0 error(s) and 7 item(s) reported on remote host                                                  
+ End Time:           2022-05-16 20:10:51 (GMT1) (1444 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested
```

## Stego


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

Nothing for nikto apacheusers plugin


