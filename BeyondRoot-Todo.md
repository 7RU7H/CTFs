
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

Smbmaze
```powershell
$persistenceUser = "User1"

for ($i=0; $i -lt 26; $i++) {
  $driveLetter = [char]($i + 65) + ":"
  if (!(Get-Volume | Where-Object DriveLetter -eq $driveLetter)) {
    $newDisk = New-VirtualDisk -DriveLetter $driveLetter -Size 10MB
    $acl = Get-Acl -Path $newDisk.DriveLetter
    $persistenceUserRights = [System.Security.AccessControl.FileSystemRights]"FullControl"
    $persistenceUserAccessRule = New-Object System.Security.AccessControl.FileSystemAccessRule($persistenceUser, $persistenceUserRights, "Allow")
    $acl.SetAccessRule($persistenceUserAccessRule)
    $otherUsersRights = [System.Security.AccessControl.FileSystemRights]"FullControl"
    $otherUsersAccessRule = New-Object System.Security.AccessControl.FileSystemAccessRule("Administrator", $otherUsersRights, "Deny")
    $acl.SetAccessRule($otherUsersAccessRule)
    Set-Acl -Path $newDisk.DriveLetter -AclObject $acl
  }
}

```

Create a file share - the longer the path for copy and paste and shell nightmares
```powershell
#

# -ConcurrentUserLimit to prevent hacky breaking into destroy the share 

New-SmbShare -Name MyFileShare -Path $goodPath -FullAccess $persistenceUser -EncryptData $True -ConcurrentUserLimit 1 -ContinuouslyAvailable -Temporary
# You can set up an inital connection to a remote host with output from
#  New-CimSession Get-CimSession
-Cim-Session 


# Add to known adversary accounts -NoAccess Administrator

$Parameters = @{
    Name = '$Name'
    Path = $goodPath
    FullAccess = 'Contoso\Administrator', 'Contoso\Contoso-HV1$'
    EncryptData = $True
    Temporary = $True
    ContinuouslyAvailable = $True
    NoAccess = 'Administrator' # Prevent access administrator - create persistence user to access
}
New-SmbShare @Parameters
```



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