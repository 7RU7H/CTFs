# Blackfield Writeup

Name: Blackfield
Date:  
Difficulty:  Hard
Goals:  
- Four hours then Helped-Through, but it will probably be a Helped-Through
- General review of my AD methodology 
Learnt:
- Do not look at the HTB Machine Info tab for retired machines. 
- Sometimes I am correct 
- DOWNLOAD THE SYMBOL TABLES IDIOT 
Beyond Root:
- Active-Directory-Recon improvements

- [[Blackfield-Notes.md]]
- [[Blackfield-CMD-by-CMDs.md]]


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)
	
![](cmebasic.png)
![](improvementsandconcise.png)
![](nodigdomaintransfer.png)
![](sadamass.png)
![](bigguestcmerid.png)
![](cmeshareguest.png)
![](cmeguestnopassword.png)
![](cmeendofguestridmoreusers.png)
![](smbmapguestrec1.png)
![](smbmapguestrec2-profiles.png.png)
![](rpcclientnullauth.png)
![](manldap1.png)
![](ldapsallobjs.png)
![](ldapsallobjs2.png)
![](ldapsallobjsmid.png)
![](ldapsearchwithnmap.png)
![](nobasicpasswordsfromcmespray.png)
![](getnpusers-support.png)
![](cracked-supportasrep23.png)
![](nosupportpassreuse.png)
![](supporthasmoresmbread.png)
![](suppport-bloodhoundPY.png)
![](dumpedtheentireldapdomain.png)
![](supportcanchangetheaudit2020password.png)
![](svcbackupcanpsremote.png)
![](theonlyremotinguser.png)
![](passwordpolicyfromsysvol.png)
![](hurrayforhacktrickontheforcechangepassword.png)
![](audit2020thoseforensicshares.png)
![](forensic-systeminfo.png)
![](domainusershasbeenpwned.png)
![](pwnedthedas.png)
![](getthelsass.png)
![](netstatpwnagereveal.png)
![](timeoutagainwithsmbmap.png)
![](lsassdumpwithcurl.png)
![](smbclientfailedtogetlsass.png)

After being jaded into the proving-ground-rabbithole-mindset-of-absolute-brick-through-a-window-when-the-allegorical-egg-timer-of-doom-goes-PING, I decided to reconsider the files and maybe the Ipwnyourcompany user is soft deleted, but would still appear in LDAP.  
![](ldapsearchingforIpwnyourcompany.png)


Microsoft Windows Server 2019 Standard ( 10.0.17763 N/A Build 17763 ) - WD / BL Evasion - Priv Esc Lateral Move
- https://gist.github.com/dualfade/48c45fb47ff273a3996c9a4f10ac9d72

Then the `wesng` call of nightmare text file of doom started given we can just pass it systeminfo.txt. After consider how I was going to deal with wesng in 2024. I thought that maybe if I check when the box was released maybe it would at least narrow down that potential rabbit holes.

Two things
![](htbupdatethatIspoiled.png)

1. It is basically a walkthrough of the machine
2. I was correct the direction of the machine I just thought I just did not have a way to get the lsass.zip
3. I noted the svc_backup in my notes so I am actually compelled to call this a walkthrough
4. Also the About Blackfield description is technical incorrect.

Well 
![1080](smbplaysveryveryverynice.png)

 
## Post-Root-Reflection  

## Beyond Root


