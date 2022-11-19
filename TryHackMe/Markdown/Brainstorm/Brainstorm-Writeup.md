# Brainstorm Writeup
Name: Brainstorm
Date:  
Description: Reverse engineer a chat program and write a script to exploit a Windows machine
Difficulty:  Medium
Goals:  
- OSCP Prep
- Brutal 12 hour *examINATION* of ability
Learnt:


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](TryHackMe/Markdown/Brainstorm/Screenshots/ping.png)

- 2 days later I realised I made this error of not using `binary` before transfering a binary file over FTP, as I had compatbility issues possible due to corruption over file transfer... \*facepalm...
![](ftpgrab.png)

Has both strcmp and the safer C function strncmp, due to my radare not being as good I want I will also use ghidra aswell for sake of speed.

![](hasstrcmp.png)

Reading the Task 3 Access section and subsequently testing port 9999 with `ncat` 

![](testing9999.png)

## Exploit

Either:
1. Username vulnerable - It is not
2. Message

Ghidra was not very helpful. Either the bufferover need `Username \x0a` - carriage return, then overflow the message or its the username size. 

Fuzzing username with 100 - got to 4200.. with 400 "A"s  ++400. C knowledge, why have a buffer for username that is that size, Must be the message.
![](useranem400atatime.png)

Fuzzing crashed at 4700 -

I tried testing on my Windows VM, but the application is either 32bit/16bit whereas my VM is 64bit - had trouble getting Windows 7. I checked [TCM Video for his setup](https://www.youtube.com/watch?v=T1-Sds8ZHBU) he has a Windows 7 VM . Found [techpp article with all the links](https://techpp.com/2018/04/16/windows-7-iso-official-direct-download-links/). Windows 7 32 bit immunity debugger does not work.. Window 10 32 bit it is...
2 days later I after research the various issues  my error being that  not using `binary` before transfering a binary file over FTP, as I had compatbility issues possible due to corruption over file transfer... \*facepalm...

## Returning to this box..

For a brutal self assessment after clearing my head I am returning to this box to simulate exam conditions for personally self assessment. Day One of Four, idea being the recon is mostly done, thus shave off 1-2 hours off that 14 hours. 2 - 2.5 hours per box.

Retested Fuzzing crashed at 6200

![](enumip.png)

enumeip.py
```powershell
!mona findmsp -distance 6400
# Right Click -> Copy full line of EIP
```

```
Log data, item 21
 Address=0BADF00D
 Message=    EIP contains normal pattern : 0x31704330 (offset 2012)
```

No bad characters... Jmp eip `0x62501535`, there are multiple address. Not sure what has gone wrong - DLL function ... Not done these. Used mona to find a jmp_esp. 

Failure to return a shell. I did spend twenty minutes fixing my mona and immunity, so that is configured for the exam and noting it for setup script for Windows VM. I stumbled lost time on the fact I kept forgeting the "," in lists again. I improved my speed and my cheatsheet to make it more readable. 


## Foothold

## PrivEsc

      
