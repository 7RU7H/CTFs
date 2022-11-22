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

## Exploit & Foothold & PrivEsc

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

Had issues with cheatsheet usage and manual bad character enumerated ... Jmp eip `0x62501535`, there are multiple address. Not sure what has gone wrong - DLL function ... Not done these. Used mona to find a jmp_esp. 

Failure to return a shell. I did spend twenty minutes fixing my mona and immunity, so that is configured for the exam and noting it for setup script for Windows VM. I stumbled lost time on the fact I kept forgeting the "," in lists again. I improved my speed and my cheatsheet to make it more readable. 

I unfortunately failed my 12 Hour test particial because I had family over and I could not and sort felt like I should not fit this around it. I failed the two hours on this box because of not knowing to put  `\n` character after the `\r`. I peaked at the TCM video my python script did not have `\r\n`. Next hint and this becomes a helpthrough. Retrying this I also failed to put a `s.recv(1024)` between sending `cmd` and `payload`. It is now giving me an offset of 2013! Re checked bad characters with mona and manually. Mona reported 2013, but actually was 2012. And ran into the same issue with final buffer overflow with shell code then it worked after I 

![1000](itworks.png)
Then it did not work on the live machine. I was unsure what I have done wrong, I picked a dll function that was not protected, it has to be called. I wait for the machine. I broke the machine. I added another s.recv between s.connect() and s.send(cmd). Double checked the shellcode.

I reduced the nop sled size!

![](system.png)

