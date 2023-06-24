
Write ups from top 50 to consider
https://app.hackthebox.com/users/357237



Use in context https://www.youtube.com/watch?v=o_XaJdDqQA0

Cleaning up in a more professional manner and a too professional manner
Windows/Linux - Go?

- try arbitrarily write data to disk over all locations of where the ADDecrypt.zip, ADDecrypt and its contents are stored as data for OS record keeping and the disk and how as DFIR would you counter that if you could hold any VMs or Live system in a suspended state or have a holographic file system where there is session layer of previous session and current session for dynamic analysis in Malware Analysis. 

TrustedSec Tool Lab
https://github.com/trustedsec/social-engineer-toolkit
Unicorn - https://github.com/trustedsec/unicorn

- https://github.com/trustedsec/trevorc2
- https://github.com/trustedsec/ptf
- https://github.com/trustedsec/hate_crack

Seatbelt compile and used

Host Vulnhub box and do both Red and Blue Teaming 

Alternative to nohup on Windows preferable dos and powershell - [For Windows consider](https://learn.microsoft.com/en-US/troubleshoot/windows-client/deployment/create-user-defined-service)

Socat proxy

pwnkit
lsa dump

https://github.com/iphelix/dnschef or modern alternative




- enabling RDP with cme and impacket kerboros tickets 
	- Harden and or implement AMSI with powershell 
- Create an alert based on .exe and .ps1 from PowerUP, Winpeas 
	- Remote interaction with box that would no lead to compromise
- Open RDP for a new user and Get Sysinternals on box
	- procdump
	- procmon


https://github.com/dubs3c/sudo_sniff/blob/master/sudo_sniff.c




https://github.com/0vercl0k - looks insane https://github.com/0vercl0k/clairvoyance


Procmon something and follow along

Atomic wannabe APT 
https://github.com/redcanaryco/invoke-atomicredteam


Bootkit

A C polymorphic code
https://github.com/m0nad/PSG/blob/master/psg.c

Rust RootKit xkit framework

A memory safe rust rootkits - [Diamorphine](https://github.com/m0nad/Diamorphine/blob/master/README.md) and [Kris Nova's boopkit](https://github.com/krisnova/boopkit)

```rust
// Embed
// Evade
// Shell <shellname>
// Delete <self,shell pid and artificats>
// Timebomb-Delete <secs> <delete-type>
```

Check this out: https://github.com/m0nad/awesome-privilege-escalation

https://github.com/codecrafters-io/build-your-own-x

https://blog.holbertonschool.com/hack-the-virtual-memory-c-strings-proc/
https://beej.us/guide/bgnet/
https://github.com/EmilHernvall/dnsguide/blob/master/README.md
https://kasvith.me/posts/lets-create-a-simple-lb-go/
https://mattgathu.github.io/2017/08/29/writing-cli-app-rust.html
https://rust-cli.github.io/book/index.html
https://flaviocopes.com/go-tutorial-lolcat/ - rainbox cli
http://www.saminiir.com/lets-code-tcp-ip-stack-1-ethernet-arp/


### Monolith 

Starting point
https://www.youtube.com/playlist?list=PL9IEJIKnBJjFNNfpY6fHjVzAwtgRYjhPw
https://www.youtube.com/playlist?list=PL9IEJIKnBJjH_zM5LnovnoaKlXML5qh17


## Putty Backdoor and Tunnel 

#### The Chisel-Shadow Network

Requires the the Go HTTPS + backdoor server

Revision
![1080](reddish-experiemental-chisel-shadow-network.excalidraw)


## Temple Ideas

Make a wordlists creator in golang for the serious string compute.

OneSeclistDirectoryBustingWordlistToRuleTheCTFs

- Go back to gobuster for inital directories
- Stop, observe and note potential targets to start feroxbuster and ffuf from 
	- What is actually a good target for this and what are likely middle directories that are good to recurse -  Pushing beyond the 
		- Anything wherehttps://www.patreon.com/xct
			- dev, git, backup, bak, etc...objectives!
- Use ffuf more ffuf vhosts, extensions, POTENTIAL pages
- Do a slow set of feroxbusters scan over to collect and double verify 


#### Mad Idea 2 - Flow: One to take control of all network routes from inside a network  

How?
```
```



[How to build a tcp proxy](https://robertheaton.com/2018/08/31/how-to-build-a-tcp-proxy-1/)


Watch this xct do this on seperate machines - https://www.patreon.com/xct 8.50 not including VAT
- https://www.youtube.com/watch?v=FYWGhdaDcZo&list=PLPBVZbjvnjVkIgFavcRiBKbDSricFJeoD
To prep for dante -> and beyond!https://www.patreon.com/xct

- Pay for patreon and do Shinra in a years time 





Dockerise one of my github projects
https://tryhackme.com/room/introtodockerk8pdqk


go full cyber 
https://github.com/hlldz/Phant0m


Start stuff in the backgound on the cli and then make stealthier 

Start a process in the background
```powershell
Invoke-CimMethod -ClassName win32_process -MethodName create
 -Arguments @{ commandline = $YOURXGOESHERENO$; ProcessStartupInformation = New-CimInstance -CimClass ( Get-CimClass Win32_ProcessStartup) -Property @{ShowWindow=0} -Local;     CurrentDirectory = $null}
```


Add a backdoor to custom source code in a weird language swift backdoor


#### Firewall fun with netsh for legacy Windows and Powershell for modern Windows

Firewall fun has been sitting in my to do list for beyond root.

####  Netsh for Windows 7, Server 2008 (R2), Vista

[Netsh command is deprecated for - Windows 7, Server 2008 (R2), Vista](https://learn.microsoft.com/en-us/previous-versions/windows/it-pro/windows-server-2008-R2-and-2008/cc771920(v=ws.10))
```powershell
# Check Profile
netsh advfirewall show currentprofile

# Turn off the Firewall
NetSh Advfirewall set allprofiles state off

# Modify a Firewall Rule for a program
netsh advfirewall firewall add rule name="<Rule Name>" program="<FilePath>" protocol=tcp dir=in enable=yes action=allow profile=Private
netsh advfirewall firewall add rule name="<Rule Name>" program="<FilePath>" protocol=tcp dir=out enable=yes action=allow profile=Private
# Delete a rule
netsh.exe advfirewall firewall delete rule "<Rule Name>"
```
[test](https://www.itninja.com/blog/view/how-to-add-firewall-rules-using-netsh-exe-advanced-way)

#### Powershell Window 10 and Server 2016 onwards

```powershell
powershell -c Get-NetFirewallRule -Direction Outbound -Enabled True -Action Block 
```

```powershell
powershell -c "Get-NetFirewallRule -Direction Outbound -Enabled True -Action Block | Format-Table -Property 
DisplayName, @{Name='Protocol';Expression={($PSItem | Get-NetFirewallPortFilter).Protocol}}, @{Name='LocalPort';Expression={($PSItem | Get-NetFirewallPortFilter).LocalPort}}, @{Name='RemotePort';Expression={($PSItem | Get-NetFirewallPortFilter).RemotePort}}, @{Name='RemoteAddress';Expression={($PSItem | Get-NetFirewallAddressFilter).RemoteAddress}}, Enabled, Profile, Direction, Action"
```

```powershell
powershell -c "Get-NetFirewallRule -Direction Outbound -Enabled True -Action Block | Format-Table -Property DisplayName,@{Name='Protocol';Expression={($PSItem | Get-NetFirewallPortFilter).Protocol}},@{Name='LocalPort';Expression={($PSItem | Get-NetFirewallPortFilter).LocalP#### Firewall fun with netsh for legacy Windows and Powershell for modern Windows

Firewall fun has been sitting in my to do list for beyond root.

####  Netsh for Windows 7, Server 2008 (R2), Vista

[Netsh command is deprecated for - Windows 7, Server 2008 (R2), Vista](https://learn.microsoft.com/en-us/previous-versions/windows/it-pro/windows-server-2008-R2-and-2008/cc771920(v=ws.10))
```powershell
# Check Profile
netsh advfirewall show currentprofile

# Turn off the Firewall
NetSh Advfirewall set allprofiles state off

# Modify a Firewall Rule for a program
netsh advfirewall firewall add rule name="<Rule Name>" program="<FilePath>" protocol=tcp dir=in enable=yes action=allow profile=Private
netsh advfirewall firewall add rule name="<Rule Name>" program="<FilePath>" protocol=tcp dir=out enable=yes action=allow profile=Private
# Delete a rule
netsh.exe advfirewall firewall delete rule "<Rule Name>"
```
[test](https://www.itninja.com/blog/view/how-to-add-firewall-rules-using-netsh-exe-advanced-way)

#### Powershell Window 10 and Server 2016 onwards

```powershell
powershell -c Get-NetFirewallRule -Direction Outbound -Enabled True -Action Block 
```

```powershell
powershell -c "Get-NetFirewallRule -Direction Outbound -Enabled True -Action Block | Format-Table -Property 
DisplayName, @{Name='Protocol';Expression={($PSItem | Get-NetFirewallPortFilter).Protocol}}, @{Name='LocalPort';Expression={($PSItem | Get-NetFirewallPortFilter).LocalPort}}, @{Name='RemotePort';Expression={($PSItem | Get-NetFirewallPortFilter).RemotePort}}, @{Name='RemoteAddress';Expression={($PSItem | Get-NetFirewallAddressFilter).RemoteAddress}}, Enabled, Profile, Direction, Action"
```

```powershell
powershell -c "Get-NetFirewallRule -Direction Outbound -Enabled True -Action Block | Format-Table -Property DisplayName,@{Name='Protocol';Expression={($PSItem | Get-NetFirewallPortFilter).Protocol}},@{Name='LocalPort';Expression={($PSItem | Get-NetFirewallPortFilter).LocalPort}},@{Name='RemotePort';Expression={($PSItem | Get-NetFirewallPortFilter).RemotePort}},@{Name='RemoteAddress';Expression={($PSItem | Get-NetFirewallAddressFilter).RemoteAddress}}, Enabled, Profile,Direction,Action"

```

```powershell
powershell -c Get-NetFirewallRule -Direction Outbound -Enabled True -Action Allow
```

Enable Window Defender Firewall
```powershell
Set-NetFirewallProfile -Profile Domain,Public,Private -Enabled True
```
Disable Window Defender Firewall
```powershell
Set-NetFirewallProfile -Profile Domain,Public,Private -Enabled False
```


```powershell
Set-NetFirewallProfile -DefaultInboundAction Block -DefaultOutboundAction Allow –NotifyOnListen True -AllowUnicastResponseToMulticast True –LogFileName %SystemRoot%\System32\LogFiles\Firewall\pfirewall.log
````ort}},@{Name='RemotePort';Expression={($PSItem | Get-NetFirewallPortFilter).RemotePort}},@{Name='RemoteAddress';Expression={($PSItem | Get-NetFirewallAddressFilter).RemoteAddress}}, Enabled, Profile,Direction,Action"

```

```powershell
powershell -c Get-NetFirewallRule -Direction Outbound -Enabled True -Action Allow
```

Enable Window Defender Firewall
```powershell
Set-NetFirewallProfile -Profile Domain,Public,Private -Enabled True
```
Disable Window Defender Firewall
```powershell
Set-NetFirewallProfile -Profile Domain,Public,Private -Enabled False
```


```powershell
Set-NetFirewallProfile -DefaultInboundAction Block -DefaultOutboundAction Allow –NotifyOnListen True -AllowUnicastResponseToMulticast True –LogFileName %SystemRoot%\System32\LogFiles\Firewall\pfirewall.log
```