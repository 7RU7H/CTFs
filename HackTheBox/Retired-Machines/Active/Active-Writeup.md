Name: Active
Date:  
Difficulty:  Easy
Goals:  
- OSCP Prep
- 2 day power through!
- Use Others resource: RedTeamFieldManual and RTFM, practice google dorking - collect information
Learnt:
- How much more methodically I have become in 6 months
- Limitation of Kali powershell client 
- Openssl really kicks me in the head regularly - noted to fix
- not knowing gpg-decrypt - migitation: search: decrypt "$NOT just the hash" password "tool"
- Ignore unintentional spoilers always find them report them in writeup, but forget about them.
- Pause and evaluate - Use Notes more - I am writing everything up is not helping time constraints or solving problems as affectively.  
 
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Enum4Linux Windows OS information
![](enum4linux-os.png)

Enum4Linux Share information:
![](enum4linux-shares.png)
Smbmap confirmation:
![](smbmap-default.png)
Rpcclient is limited:
![](nothingrpcclient.png)

Cpassword in SYSVOL 
![](cpassword.png)

```powershell
# SVC_TGS password cpassword
"edBSHOwhZLTjt/QS9FeIcJ83mjWA98gw9guKOhJOdcqh+ZGMeXOsQbCpZ3xUjTLfCuNH8pG5aSVYdYw/NglVmQ"
```

Tried rpcclient authenicating with credentials found and null authenication with errors. Rest machine and tried again.
![](cantnullrpc.png)
Double checked the share.
![](retestsmb.png)
Nothing in either scripts and DfsrPrivate 
![](nothinghere.png)

https://adsecurity.org/?p=2288

From https://adsecurity.org/?p=2362
![](adseccpassword.png)

AES Key research begins, we need to be on the machine to decrypt to use PowerSploit, but 
![](passwordencryption.png)

Checking if it is a rabbit hole - no its a 2008 windows server! Tried to use Kali local powershell and powersploit to decrypt. Read the script - does look up on various files. Regardless Powersploit prepped for later. for possible PrivEsc. There must something else in the Group Policy Replication.

![](gptTmplPssword.png)

We can bruteforce, REQUIRED LOGON TO CHANGE PASSWORD 0 as long as it is not clear text.

![](SePrivs.png)

While checking ports I got spoiled.
![](spoiledahere.png)

I have the TGS_SVC credentials, but I have never made a ticket locally. I force reset the Administrator password, without logon! `smbpasswd` cannot use aeskeys. So after trying not to look the the above image, create a ticket with getTGT for Administrator. 

After my 1 hour time limit plus 30 minutes extension to fix.

Improvements I need to make:
- Need to revise impacket - I am not pieces together information to do x quick enough
	- Print off and memorize a list impacket tool does x
- gpg-decrypt - group policy preferences decrypter 
- openssl still kicks me in the head - cpassword is encrypted - I assumed service account would be a randomised password, crackstation could not identify
	- ACTUALLY NEEDED to use gpp-decrypt
	- I have evidence of myself knowing that it is encrypted and then trying locally to decrypt it.

## Exploit && Foothold && PrivEsc

![](ggpdecrypttorescue.png)

Look up taskes by tool.
![](lookuptaskesmore.png) 

`GPPstillStandingStrong2k18`

Stopped Video at 13:43  - Close tab [Ippsec](https://www.youtube.com/watch?v=jUc1J31DNdw)

Back to impacket, there is also `Get-GPPPassword`. 

![](learningimpacketmoreindepthone.png)

Atfer going for a run. I remember that I made it extra hard not using my notes to test my recall after pushing extra stress on myself. I think I was worth it to observe where my stronger connections around solving AD quickly are.

![](getadusers.png)

`impacket-GetADUsers -all  -dc-ip 10.129.7.213 active.htb/svc_tgs`

![](smbmapwithtgssvc.png)



I then ran into setup issues that I beeter to have now then later.