# Safe Helpthrough
Name: Safe
Date:  28/10/2022
Difficulty:  Easy
Goals:  OSCP prep, ROP chain (re?)discovery, 
Learnt:
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

Learnt about gef wonders, with a built in pattern creat and pattern search.
[guyinatuxedo](https://guyinatuxedo.github.io/index.html)
[gef commands](https://hugsy.github.io/gef/commands/aliases/)
![](gefisgreat.png)

## Foothold

## PrivEsc

      
