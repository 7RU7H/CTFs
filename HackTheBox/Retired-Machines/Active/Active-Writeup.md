Name: Active
Date:  
Difficulty:  Easy
Goals:  
- OSCP Prep
- 2 day power through!
- Use Others resource: RedTeamFieldManual and RTFM, practice google dorking - collect information
Learnt:
- How much more methodically I have become in 6 months

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

I have the TGS_SVC credentials, but I have never made a ticket locally. I force reset the Administrator password, without logon!

https://cheatsheet.haax.fr/windows-systems/exploitation/kerberos/



## Exploit

## Foothold

## PrivEsc

![]()

      
