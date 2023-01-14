
Name: Support
Date:  
Difficulty:  Easy
Goals:  OSCP Prep - Brutal 12 hour *examINATION* of ability
Learnt:
- Thinking fast and slow
- Past work must be a guide not a path

For a brutal self assessment after clearing my head I am returning to this box to simulate exam conditions for personally self assessment. Day One of Four, idea being the recon is mostly done, thus shave off 1-2 hours off that 14 hours. 2 - 2.5 hours per box.

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

![](ping-confirms-more-stealth.png)

![](smb-now-filtered-questionmark.png)

![](internaldns.png)

![](e4l-domainname.png)

![](rpcclient-check.png)

![](archandbuild.png)

![](smbclienttotherescue.png)

![](readableguestshares.png)

![](ridbrute.png)

Noted researching a way to get crackmap output into a users.txt!! 

![](supporttoolsmbshare.png)

![](publickeeytoken-userconfig.png)

```
publicKeyToken="b03f5f7f11d50a3a"
```

There is function that gets a password
![](stringofuserinfoexedoespassstuff.png)

![](fileUserInfo-exe.png)
Fumbling around with Radare and Ghidra. False positive? Probably
![](cromwellpublickey.png)

![](erroronpassfalsepositive.png)
Decided to brute force with userlist in the background

Confirmed false postive.
![](deniedrpc.png)
:(
![](anypasswordwilldo.png)

Returning to this, I reviewed the information. Refreshed, I went back ghidra knowing that I certain there must be a password in here. Found in the debug data that there is 0xdf user on the box.
![](0xdfusers.png)
I remember think this was  weird it `$5a280d0b-9fd0-4701-8f96-82e2f1ea9dfb`. It is the 
![1000](guid.png)

Got Ilspy after melting my brain with "I" "L" "l" madness of naming C sharp decompilers. 
![](passwords.png)
User and Password! 
![](checkpass.png)
![](passpass.png)
But I cant list shares or RID Brute.
`armando : 0Nv32PTwgYjzg9/8j5TbmvPd3e7WhtWWyuPsyO76/Y+U193E`
![](doublehtehashcheck.png)

Reenumeration continues, personal takeways:
- Ldapsearch notes need tuning
- Forums are good and bad 
	- I did not check for encryption as my decompilation attempts did not show any encrypting functions - except I did with `enc_password`

[No dpapi though](https://learn.microsoft.com/en-us/windows/win32/api/dpapi/nf-dpapi-cryptprotectdata?redirectedfrom=MSDN), custom encryption, because why would..

There is alot of ldap related activity in the UserInfo.exe decompile

![](hereitistheresourguycongratstony.png)

![](Ohdeargoditis.png)

Extracting and [learning some C sharp](https://learnxinyminutes.com/docs/csharp/)  and [compilation](https://learn.microsoft.com/en-us/visualstudio/get-started/csharp/run-program?view=vs-2022)

![](thatsabingo.png)

`nvEfEK16^1aM4$e7AclUf8x$tRWxPWO1%lmz`

![](support.png)

Is the ldap user account's password
![](gettingsomewhere.png)


![](supportisaremotemanager.png)

Spider_plus with ldap users, 
```bash
cat 10.129.227.255.json | grep Policies | awk -F\" '{print $2}'
```

![](thisquery.png)

![](directorysearchclass.png)

```
ldapsearch -x -H ldap://10.129.227.255 -D "support\ldap" -w 'nvEfEK16^1aM4$e7AclUf8x$tRWxPWO1%lmz' -b "dc=support,dc=htb"  "(giveName=support)(&(givenName=support)(sn=))(sn=)" "(objectClass=*)"

```

https://docs.teradata.com/r/AhOAsYfub6iLoEGgDSmXOQ/ZRrv14mH6Kijem0tWlOZ0Q
https://devconnected.com/how-to-search-ldap-using-ldapsearch-examples/
https://github.com/ropnop/go-windapsearch

## Exploit

## Foothold

## PrivEsc

      
