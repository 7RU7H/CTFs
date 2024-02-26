# Reg Write-up

Name: Reg
Type: Pwn
Date:  
Difficulty:  Easy
Goals:  
- Begin the learn to Pwn all the things with Retired Box with CryptoCat, Active box 
- See how far I can get in an hour
Learnt:

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

Then Gef and gdb went weird so I had to stop

## Post-Completion Reflection


	
