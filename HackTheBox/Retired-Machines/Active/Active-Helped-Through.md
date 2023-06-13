
Name: Active
Date:  31/10/2022
Difficulty:  Easy
Goals:  
- OSCP Prep
- 2 day power through! - Oof!
- Use Others resource: RedTeamFieldManual and RTFM, practice google dorking - collect information
Learnt:
- How much more methodically I have become in 6 months
- Limitation of Kali powershell client 
- Openssl really kicks me in the head regularly - noted to fix
- not knowing gpg-decrypt - migitation: search: decrypt "$NOT just the hash" password "tool"
- Ignore unintentional spoilers always find them report them in writeup, but forget about them.
- Pause and evaluate - Use Notes more - I am writing everything up is not helping time constraints or solving problems as affectively. 
- Important of revision and an hour a day to reread notes and connect elements
- Impacket Recall x does y required
- Window DNS configuration
- Non domain joined Bloodhounding!
- More VM Configuration!

## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Active/Screenshots/ping.png)

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

I then ran into setup issues that I better to have now then later. 

![](userandpasswordrunas.png)

I configured DNS, learn different transference methods between VMs, Hosts and Guests and Non Domain-Joined Sharphounding. And strengthening crippled neurons that need to rememebr every impacket script by the end of next week!

![1000](kerberoastable.png)


```
$krb5tgs$23$*Administrator$ACTIVE.HTB$active.htb/Administrator*$bb0f016ca8b3502f9c63766da05b9b68$d75bf3628295fc27eb87893b18dfd4f1233b261d10ccd70033f3f182ff2b64a38c9cde230b5b567a122d780213dc039b761a26b81271119b1d6602d64542e50b9a3a843ed83fd88d3d60e42ac66e2ae5e6ff321742ff71a3c72110bb78d22f22c8e8a99df6250ae983a25abc8a972682315b3343d97fd21ec919f0bd20e03804211204439cfd25f6c2926be66f96bc2780b2f916ed3ead200cfcc52e1838e4c7f71cfd2892158b2291b2e4a2035a0eb0f3dd1aa5cadbbe4f3b64ab54788280a2da1d93f2d4cc6dbf01313620d7cf173c0a9ce686540c5a16bf9f954e3909491205adeedc31f9a979749e0cbd0411ba1740c6cc2b371da0da45af4d3ab1596029a2000328a87b2c36e139fc055d169c1342c01b0783a3637bb08d232dd89134aecce6a4686c49bd94b55a41a3b7c5e90c6ef873edc0613cbdd493066904b8fe112e14093c6d66f221b7640cc2c9cc1eb58e1692f4720cb3dc5413a8994705536db51fbfc874395297f467d928d5e6ac95f13502950c979109484acff1feaaccc89dc43058acc91276005fe344196bf3b4bb97b71b416a1e7e24fe915b4c342dd5e813fd8110b12492124ece8395671b456ba99d3e082c5c08c879ff956fb1705d88407fa9d0b481cc7929aa1dd4bf9a128aeaed274c629d267cae07058546701544d67bb71966768a45253e8196d04d1ce54c6dc75d56f6acb1ce0b285ada11fa2f3c7ca7c0d1e25045f75b8154d4b0ce38f0be00710c0ba953d536d83b4224a67ce98e2974acd04a338d73d86ce1a520099716ccd1ac3c7e3fee8dc099e444759b770200816e9460392390a501283bd8c549abf33f1e6bd33010e67ae02586eca16c68d00b9b7fc23ed7e523b90dd4cbf6e9c9e03302b7e34d32a12c21f819b02f7304a1d938b78d4265e564b8d35d7864802c29d3d9f8e19e169edbf209802571368aee9988ea798ceb353ec9dda7730fb57fac3b4e51c880c7eb17b10d50a04db149af8a218a41ca9af864e5e92224ca56ad36f595d119ee6bf261cbb0bfc6abf8dbb163587d8b0712e3ae720ce7c3d15a0cba5117b8ddab2a8c14d874db6064baf78b5ee2c4a1a67baa94f5ed0b4c49d13ee3704e77130df3b105e663dc44657e32586358a6c0b61e754b8e5d7ffe667c9270133a0290ead468252af260d6127ccbdc3713a9855c2dea8f9c73605fb5b31b9f294262a21bb0b0e2450e7a9e86c4605c4a83e26aefbe327d943d6e5c2ec356ce261957ef1ee3
```


Crack with 13100 Hachcat
![](cracked.png)
And impacket-psexec onto the box:
![](root.png)

Learn alot about myself, my machines, AD, connecting understanding, where weak points are, what can do under pressured without notes and what I need to memorize by the end of next week.