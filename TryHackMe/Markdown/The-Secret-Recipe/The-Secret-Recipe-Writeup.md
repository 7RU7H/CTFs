# The-Secret-Recipe Writeup

Name: The-Secret-Recipe
Date:  09/02/2023
Difficulty:  Medium
Goals: 
- Eric Zimmerman tools Cheatsheet expansion
- Hardcore Red Team Cleanup Nogging Stratching - become the Constipation scGRoup (Surgical Log Removal)
- Columbo level asking the registry one more thing... 
Learnt:
- ChatGTP is not good at the Windows Registry even though it is all documented...
- Load the hives correctly!!  
Beyond Root:
- Was going to Test Smbshare Maze Attack  - took way to long - failed 2nd to last question :(
- https://www.youtube.com/watch?v=dxOvOX-lScM

I might be worth downloading the files as the provisioning and requriements for this CTF are rough. Or do somethign else in the background 

Useful
```powershell
RegisterExplorer -> load System Hive -> Repeat till loaded all the hives
.\RegRipper3.0-master\rip.exe 
```


How many Files are available in the Artifacts folder on the Desktop?

Solution
```powershell
ls Desktop\Artifacts
```

Answer
```powershell
6
```

What is the Computer Name of the Machine found in the registry?

Solution
```powershell
 Search "Name" ControlSet001\Control\ComputerName\ComputerName  
```

Answer
```powershell
JAMES
```
 
When was the **Administrator** account created on this machine? (Format: yyyy-mm-dd hh:mm:ss)  
[REF](https://serverfault.com/questions/588474/how-to-find-the-creation-date-of-a-local-user-account)
Solution
```powershell
# This is not documented and apparently Microsoft MVP do not know the answer..
# ChatGTP did not either!
SAM\Domains\Accounts\User\000001F4\F -> Data Interpreter gives weird output
# User regripper
.\RegRipper3.0-master\rip.exe -r C:\Users\Administrator\Desktop\Artifacts\SAM  -a
```

Answer
```powershell
2021-03-17 14:58:48
```
 
What is the RID associated with the Administrator account?

Solution
```powershell
Default Administrator is always set to this, but in registry for other users
SAM\Domains\Account\Users\Names then account name the hex/decimal stored ats key is the RID
```

Answer
```powershell
500
```
 
How many User accounts were observed on this machine?

Solution
```powershell
SAM\Domains\Account\Users\Names then count
```

Answer
```powershell
7
```
 
There seems to be a suspicious account created as a backdoor with RID 1013. What is the Account Name?

Solution
```powershell
bdoor is a bit of a give away if you performed the solution for the last task
SAM\Domains\Account\Users\Names then account name the hex/decimal stored ats key is the RID
```

Answer
```powershell
bdoor
```
 
What is the VPN connection this host connected to?

Solution
```powershell

Microsoft\Windows NT\CurrentVersion\NetworkList\Profiles

```

Answer
```powershell
ProtonVPN
```
 
When was the first VPN connection observed? (Format: YYYY-MM-DD HH:MM:SS)  

Solution
```powershell
Microsoft\Windows NT\CurrentVersion\NetworkList\Profiles
```

Answer
```powershell
2022-10-12 19:52:36
```
 
There were three shared folders observed on his machine. What is the path of the third share?

Solution
```powershell
System hive
ControlSet001\SErvices\LanmanSErver\Shares
```

Answer
```powershell
C:\RESTRICTED FILES
```
 
What is the Last DHCP IP assigned to this host?

Solution
```powershell
Assigned 
HKLM\SYSTEM\CurrentControlSet\Services\Tcpip\Parameters\Interfaces
Check row: "lease obtained time" colomn: data "field"
```

Answer
```powershell
172.31.2.197
```
 
The suspect seems to have accessed a file containing the secret coffee recipe. What is the name of the file?

Solution
```powershell
.\RegRipper3.0-master\rip.exe -r C:\Users\Administrator\Desktop\Artifacts\NTUSER.DAT -a
Software\Microsoft\Windows\CurrentVersion\Explorer\RecentDocs\.pdf
```

Answer
```powershell
secret-recipe.pdf
```
 
The suspect ran multiple commands in the run windows. What command was run to enumerate the network interfaces?

Solution
```powershell
.\RegRipper3.0-master\rip.exe -r C:\Users\Administrator\Desktop\Artifacts\NTUSER.DAT -a
Software\Microsoft\Windows\CurrentVersion\Explorer\RunMRU
```

Answer
```powershell
pnputil /enum-interfaces\1
```
 
In the file explorer, the user searched for a network utility to transfer files. What is the name of that tool?

Solution
```powershell
.\RegRipper3.0-master\rip.exe -r C:\Users\Administrator\Desktop\Artifacts\NTUSER.DAT -a
Software\Microsoft\Windows\CurrentVersion\Explorer\WordWheelQuery
```

Answer
```powershell
netcat
```
 
What is the recent text file opened by the suspect?

Solution
```powershell
.\RegRipper3.0-master\rip.exe -r C:\Users\Administrator\Desktop\Artifacts\NTUSER.DAT -a
# RecentDocs Section
```

Answer
```powershell
secret-code.txt
```
 
How many times was Powershell executed on this host?

Solution
```powershell
.\RegRipper3.0-master\rip.exe -r C:\Users\Administrator\Desktop\Artifacts\NTUSER.DAT -a
Software\Microsoft\Windows\CurrentVersion\Explorer\UserAssist
  {1AC14E77-02E7-4E5D-B744-2EB1AE5198B7}\WindowsPowerShell\v1.0\powershell.exe (3)
```

Answer
```powershell
3
```
 
The suspect also executed a network monitoring tool. What is the name of the tool?

Solution
```powershell
.\RegRipper3.0-master\rip.exe -r C:\Users\Administrator\Desktop\Artifacts\NTUSER.DAT -a
# Scroll up and find various executions 
```

Answer
```powershell
Wireshark
```
 
Registry Hives also notes the amount of time a process is in focus. Examine the Hives. For how many seconds was ProtonVPN executed?

Solution
```powershell
# ChatGTP: "The amount of time a process is in focus is not stored in the Windows Registry. The Windows Registry is a database that contains configuration settings and options for the operating system and installed applications, but it does not track the amount of time a process is in focus. To track the amount of time a process is in focus, you would need to use a performance monitoring tool or write custom code that captures this information."

# :/ 

{7C5A40EF-A0FB-4BFC-874A-C0F2E0B9FA8E}

LastWrite
2022-10-12 19:47:16Z
  {7C5A40EF-A0FB-4BFC-874A-C0F2E0B9FA8E}\Proton Technologies\ProtonVPN\ProtonVPN.exe (1)
2022-10-12 19:46:47Z

NTUSER.DAT\Software\Microsoft\Windows\Currentversion\Explorer\UserAssist\{GUID}\Count
# 
# YIKEs 
```

Answer
```powershell
- I Failed this answer I used: https://www.youtube.com/watch?v=dxOvOX-lScM
I failed because I loaded the hive incorrectly but looked at the correct place....
343

```
 
Everything.exe is a utility used to search for files in a Windows machine. What is the full path from which everything.exe was executed?

Solution
```powershell
.\RegRipper3.0-master\rip.exe -r C:\Users\Administrator\Desktop\Artifacts\NTUSER.DAT -a
Software\Microsoft\Windows NT\CurrentVersion\AppCompatFlags\Compatibility Assistant\Store
```

Answer
```powershell
C:\Users\Administrator\Downloads\tools\Everything\Everything.exe
```

## Beyond Root

