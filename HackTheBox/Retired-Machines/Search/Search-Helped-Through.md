# Search Helped-Trhough

Name: Search
Date:  
Difficulty: Hard  
Goals:  
- Connect RBAC and AD for AZ104 
- Real-World-Like AD box
- Bring my total daily workload back to were it should follow winter ralted festivities
- Keep me headspace in a hackery-space to continue with everything for 2023 
- Test Windows decoding and piping a reverse shell and test way to do 
Windows
```powershell
$args = [string] '(& echo "$base64EncodedString" | certutil -decode)'
& powershell.exe $args

echo "$base64EncodedString" | certutil -decode | powershell.exe $input
echo "$base64EncodedString" | certutil -decode | pwsh -Command "$input"

```

Learnt:
Beyond Root:
- Harden the box with powershell
	- Research hardening, but do atleast (prior to research):
		- Lock down port conectivity per user - check connectivity group 
		- Harden and or implement AMSI with powershell
		- Configure the password policy 
		- Create an alert based on .exe and .ps1 from PowerUP, Winpeas 
		- Remote interaction with box that would no lead to compromise
		- Open RDP for a new user to use Sysmon, ProcMon
		- Get Sysinternals on box
	
[Funday Sunday: HacktheBox's Search and Active Directory Gone Wild!](https://www.youtube.com/watch?v=OEu3sXFUCP0)



## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)
	
## Exploit

## Foothold

## PrivEsc

## Beyond Root

      
