# Gatekeeper Writeup
Name: Gatekeeper
Date:  
Difficulty:  Medium
Description: Can you get past the gate and through the fire?
Goals:  OSCP Prep 
Learnt:

## Recon

Nmap and `file` to the recon-rescue!:
- OS: Windows 7 Professional 7601 Service Pack 1 (Windows 7 Professional 6.1) - 32 bit! 
- Arch: Intel 80386 - 32bit
- Hostname: GATEKEEPER
- gatekeeper.exe: PE32 executable (console) Intel 80386, for MS Windows

I tried fuzzing but I thought 100 is a tad low.
Why not just smash a `msf-pattern_create -l $largenum`? 

Offset Enumeration

```python
Log data, item 24
 Address=0BADF00D
 Message=    EIP contains normal pattern : 0x39654138 (offset 146)
```

But also 146, why did the the fuzz not return 200? Tried  `msf-pattern_create -l 200` and measure twice cut once. I just noticed: `0BADF00D` - Eat healthy., but how I am going to fit a shellcode in this and do/can I need to make more space somehow?

Weirdness abounds!
```python
mona Memory comparison results, item 0
 Address=0x007a1a74
 Status=Corruption after 0 bytes
 BadChars=00 01 02 03 04 05 06 07 08 09 0a 0b 0c 0d 0e
 Type=normal
 Location=Stack
```

Weirdly my offset was two off?

![](eh.png)

Also 0A is not corrupting the next byte.

https://shell-storm.org/shellcode/files/shellcode-627.html - 61 byte up to command prompt, maybe just add a administrator user, but guessing by the description there is fire afterward if we did get a shell it would be machine or service account. Therefore really we would have either learn to use memory elsewhere like assembly version of malloc me from x memory from y point or use a powershell in memory download and run IEX reverse shell. 

![](oof.png)

## Exploit

## Foothold

## PrivEsc

      
