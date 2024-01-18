# Binary-Heaven Helped-Through

Name: Binary-Heaven
Date:  
Difficulty:  Easy
Goals:  
- Gentle steps into Binary Exploitation Mountain
- Learn some:
	- BinEx
	- ASM
	- C
	- Methodology
	- Tools: `r2`, `gdb+gef`
Learnt:
- 
Beyond Root:
- Pre-Watching Video past the introduction - recite the exact way to exploit this as if it is a PWK200+ machine 
- Watch the [CryptoCat intro binary Exploitation series](https://www.youtube.com/watch?v=wa3sMSdLyHw&list=PLHUKi1UlEgOIc07Rfk2Jgb5fZbxDPec94)
- `GBD + GEF` + `r2` cheatsheet updated slightly

- [[Binary-Heaven-Notes.md]]
- [[Binary-Heaven-CMD-by-CMDs.md]]


## Stack Based BufferOverflow Recital

 - [THM - Buffer Overflow Prep](https://tryhackme.com/room/bufferoverflowprep), but use this to follow along [VOD](https://www.youtube.com/watch?v=eLIRjcI5eYU)
 - [Alh4zr3d OSCP x86 Buffer Overflow](https://www.youtube.com/watch?v=Z2pQuGmFNrM) - older but still very relevant and there are keys points of stages of exploit development that is easy to remember from this video as Al has a talent for the big picture and the helping you remember the mental and domain specific weird issues. 

- If this exam like OSCP firstly you would want to nmap all ports 
- The indicator you are looking for is a SMB or NFS share and a custom port or a port of known vulnerable application that is binary hosted on that share.
- If there is a share either there will be a source code or a binary
	- If binary: use `checksec` from pwntools on the binary if there is a binary as OSCP will require absolute no evasion in memory from the various defences developed over the decades if there are defences then it is a rabbit hole relative to the scope of OSCP.
	- If source code use static analysis looking for vulnerable c function like `strcopy` or a `scanf` that is passed to `[]char` buffer without input validation ( checking the size of the string passed to the buffer)
		- Yes `printf("%p")` and other are vulnerable, but it is best to try not rabbit hole in memorising all the vulnerable C functions when it comes to static analysis - it more about understand the control flow first as maybe there is a rabbit hole where that code does not ever get executed. Summary methodical in control flow analysis just..
		- Dork the functions - with experience some functions will naturally be remembered as being vulnerable, BUT IF THEY ARE NOT IN THE CONTROL FLOW OFFSEC MACHINE DEVELOPER ARE MASTER TROLLS IN THE RABBIT HOLE DEPARTMENT
	- Double check the same port is being opened! that from your nmap scan - rescan with `-sS and -Pn  and -p PORT` and do something else to be time efficient  
- If it 99.9% vulnerable run you hypervisor of choice with 2 VMs: target Windows or Linux VM and Kali Linux VM on a internal network so you do not accidentally exploit the exam machine
	- Debuggers:
		- Windows - Mona and Immunity are the best choice (mona is installed with Immunity)
		- Linux -  GBD and GEF 
		- As generally good John Hammond advise it is wise to do a couple of machines without using the automated bad character enumeration for the sake of learning to read the stack, hex, understanding where your code is actually doing to go and do and how bad characters can be guessed based on the application to save some time.
- The best methodology for exploit development from content creators that I have used myself is the one used in the [THM - Buffer Overflow Prep](https://tryhackme.com/room/bufferoverflowprep), Tiberious is awesome , but John Hammond is the king of methodology here follow along with this [VOD](https://www.youtube.com/watch?v=eLIRjcI5eYU). I am guessing this methodology is used in the EXP-30X+ that John has passed so it is battle tested and potentially not John's, but he explains it so clearly and simply that nothing really compares. Essential this methodology is the following:
	- Have separate template scripts for every step
	- Use pwntools for endianess or his `struct` function - sometimes compatibility and updates break packages alternatives are vital. Similar to reading the hex manually getting the experience both ways will cut through the course material, other people explanations, and the complexity of this subject matter. 
- If compiling from source and there .so or .dlls on the share compile using those library files and any instructions given **do not create a different puzzle than intended to then solve**  
- Fuzz the buffer - if you are not compiling from source if you are skip use brain! 
- Enumerate EIP - instruction pointer where our address we will point jmp to on the stack will either execute a nopsled to our code or our shellcode mostly likely the former
	- `msf-pattern_create` and `msf-pattern_offset` to create the pattern and then get the address
	- Mona and GEF can get the address also this for you!Just use the `msf-pattern_create` in the second script 
- Now we enumerate bad characters - John Hammond video has the best method for this
- DO THE MATHES
- Shell code 
	- Mathmatics of (Information on AV/AMSI do you actually have or get) x Size of the Buffer   
	- Context:
		- Is snmp running and you know they is AV on the box, because snmp will declare it in the mibs
		- Is there some information in the share about running the service that gives clues as to how it is being run
	- Do you really want to risk it there are only so many reset, but still chill out and HAVE ALTERNATIVES - you have lots of alternatives:
		- Stagers  
			- Custom LOLBIN or powershell to download and execute
			- C2/metasploit - beware some C2 stagers are large 
		- msfvenom still works, but meterpreter is not a good idea - more signatures and burns you meterpreter
		- packetstorm has windows shellcode for adding a user
		- We can always add a user if the service is running as NT SYSTEM or Administrator account AV cant block `net` command running what has been configured to be a 
		- AMSI exists, but not on older Machines 
		- Force a new process with if some weird crash 
 
## XCT as a Blueprint and Parasocial Mentor

[XCT](https://www.youtube.com/watch?v=UnZj5zzcBG4) is awesome.

I decided to start by laying the foundation for get data with some automation

Then `gdb` and `gef` or `r2` 
![1080](pdfatmain.png)


![1080](readthedecompilationlikepossiblywell.png)

`r2` command syntax is `letter` then `letter`
```c
d // debug commands
ds // step one instruction
db 0x55dc2082e21f// breakpoint at address
dr //show registers

```
Demonstrating register changes:  
![](registerobservationtesting.png)

- How to deal with scanf in `r2` and `gef`
## Exploit

## Foothold

## Privilege Escalation

## Post-Root-Reflection  

## Beyond Root

See Recital section
