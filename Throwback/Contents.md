# Contents

**Task 5 AD Basics (Reading): **  

For this I provide my activeDirectoryDefined.md as collection everything learnt thus far in its current state.

**Task 6 Let’s Get Offensive (Reading) **

For this I provide a Powershell101.md, but I suggest:  
[x in y Minutes](https://learnxinyminutes.com/docs/powershell/)  
I a good references for starting and returning too, but there are a few other resources that really help with the nitty-gritty of powershell:  
[Microsoft Scripting Blog](https://devblogs.microsoft.com/scripting/)  

Also I will provide my current very very limit ADPrivEsc CheatSheet and some useful Powershell stuff in its current state, but please check out my [Archive repository](https://github.com/7RU7H/Archive)  
It also contains both regular Linux and Windows PrivEsc cheetsheats also! 

**Task 7 Entering the Breach**

This section was more of a directional spring board to then try new things, please see the TryThisStuff section to see a list of tools, techniques, etc tried. 

**Task 8 Exploring the Caverns**

Finally figured out how to use those nice screenshots I see on writeups and walk through that seem be nicely cropped. I also use nikto and nuclei awesome tools that I am using in my bug bounty tool I am making called the [H4dd1xB4gd3r](https://github.com/7RU7H/H4dd1xB4dg3r). Tried and failed to reconftw to run, will try again at later enuemration stage.

**Task 9  Webshells and you !**

After some trail and error on a pFsense firewall, of different shells check out some my [Archive repository](https://github.com/7RU7H/Archive/shells.md) that will be added over time a condense cheatsheet on shells. 

**Task 10 First Contact**

This was black-box from this point tried various shells and stabilising them and began to realise how predefined route through Throwback was, as everything seem one way only. After getting some credentials thought I try to do some smb enumeration, which went no-where. 

**Task 11 Wait, just you mean just one this time?**

Showcased Mentalist Wordlist generator and bruteforced the MAIL web login with hydra the default way. Alot went wrong on the github documentation end, please see documentation of this [facepalm](Wait-just-you-mean-just-one-this-time/Waitwhathappened). Tried Brutespray that worked but given the time its was taking and the embedded Medusa tool does not have timeout it hung infinitely for icmp services. I tried it got onto a account I was not suppose to that lead to some enumeration taht also lead nowhere, and then my lack of github commands means that in one of the commits is this trail to nowhere. It did not provide much value to this piece other than document a failure to use github correctly - I have learnt much, even the simple project management stuff over the course of writing and completely Throwback. I also incorrectly thought that HumpreyW was a backdoor.

**Task 12 Gone Phishing**

White-boxed this section but showcased some `awk` to get the correct field of the hashes and `echo` them into a new file to crack.

**Task 13 Just a Drop Will Do** 

Tick Responder usage off the checklist and followed the instructions performed a LLMNR attack that is simulated by the Throwback network.

**Task 14 We Will, We Will, Rockyou**

Then cracked the hash from LLMNR attack to gain access to PROD workstation.

**Tasks 15 - 18 Building Your Own Dark... er Deathstar to SEATBELT CHECK!**

The section contains "Deploy the Grunts!", "Get-Help Invoke-WorldDomination", "SEATBELT CHECK!" until I failed to read that you need to perform this over a rdp connection! so some progress was halted by this.

**Task 19-20  Dump It Like It's Hot to Not the soft and fluffy kind**

Usage of MimiKatz and the resulting output avaliable in this directory. 

**Task 21 Yo Dawg, I heard you like proxies.**

Setup of Metasploit and Proxychains, showcased the usage of mfspc a tool for automating payload creation using the msfvenom. Ran into a problem with the Attackbox and the next section so switched back to the THM Kali VM. 

**Task 22 Good Intentions, Courtesy of Microsoft**

With good intentions for my time I skipped through the next task and updated and upgrade the THM Kali VM, then installed bloodhound, crackmapexec, pip3 and impacket. This took like 3 hours. Then Sharphound is broken! Had to resort to John Hammond's video linked in the task for to see One flag and Bloodhound information.

**Task 23-24  Wallace and Gromit and With three heads you'd think they'd at least agree once**

From here progress went vastly quicker, I had lots of connection issues managed to get this one done myself and gained alot of momentuum as the troubleshooting and connection issues really hurt.

**Task 25 You're Five Minutes Late...**

Things really picked up pace and all the demotivation burnt away. Ran an additional smbmap and nmap scan just because it was on the TryThisStuff list


**Task 26 Word to your Mother**
**Task 27 Meterpreter session 1 closed. Reason: World-Domination**
**Task 28 We gotta drop the load!**
**Task 29 So we're doing this again...**
**Task 30 SYNCHRONIZE**
**Task 31 This forest has trust issues**
**Task 32 r/badcode would like a word**
**Task 33 Identity Theft is not a Joke Jim**
**Task 34 So anyways, I just started hiring...**
**Task 35 Lost and Found**
**Task 36 Kerberoasting II Electric Boogaloo**
**Task 37 Game Over **
