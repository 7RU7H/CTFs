@echo off
set cwd=%cd%
mode con:cols=160 lines=9999
cd c:\

echo ====================
echo WINDOWS PRIV CHECKER
echo ====================
echo.



echo [*] GETTING BASIC SYSTEM INFO
echo.

echo [+] systeminfo (use output with https://github.com/bitsadmin/wesng)
systeminfo
echo.

echo [+] Patch Information
wmic qfe get Caption,Description,HotFixID,InstalledOn
echo.

echo [+] Processor Information
SET Processor
echo.

echo [+] Domain Information
set user
echo.

echo [+] PATH INFORMATION
echo %path%
echo.



echo [*] NETWORK INFORMATION
echo.

echo [+] Interfaces
ipconfig /all
echo.

echo [+] Netstat
netstat -ano
echo.

echo [+] FIREWALL
netsh firewall show state
netsh firewall show config
netsh advfirewall firewall dump
echo.

echo [+] Route
route print
echo.

echo [+] ARP
arp -A
echo.



echo [*] GETTING FILESYSTEM INFO
echo.

echo [+] Enumerating Additional Drives
for %%i in (a b d e f g h i j k l m n o p q r s t u v w x y z) do @dir %%i: 2>nul
echo.

echo [+] Network shares
echo.
net share
echo.

echo [+] Scheduled Tasks
schtasks /query /fo LIST /v | findstr "TaskName Author: Run: User:"
echo.



echo [*] ENUMERATING USER AND ENVIRONMENTAL INFO...
echo.

echo [+] Administrators
net localgroup administrators
echo.

echo [+] Environment
set
echo.

echo [+] All Users / Accounts / Groups
net users
net accounts
net localgroup
echo.

echo [+] Current User
echo Current User: %username%
whoami /all
echo.



echo [*] ENUMERATING FILE AND DIRECTORY PERMISSIONS / CONTENTS
reg.exe ADD "HKCU\Software\Sysinternals\AccessChk" /v EulaAccepted /t REG_DWORD /d 1 /f
echo.
cd %cwd%

echo [+] World Writable Files and Directories
accesschk.exe -uwdqs "Users" "c:\*" /accepteula
accesschk.exe -uwdqs "Authenticated Users" "c:\*" /accepteula
accesschk.exe -uwdqs "Everyone" "c:\*" /accepteula
accesschk.exe -uwqs "Users" * /accepteula
accesschk.exe -uwqs "Authenticated Users" * /accepteula
accesschk.exe -uwqs "Everyone" * /accepteula
cd C:\
echo.

echo [+] World Writable Program Files and User Directories (icacls)
icacls "C:\Program Files\*" 2>nul | findstr "(F)" | findstr "Everyone"
icacls "C:\Program Files (x86)\*" 2>nul | findstr "(F)" | findstr "Everyone"
icacls "C:\Program Files\*" 2>nul | findstr "(F)" | findstr "BUILTIN\Users"
icacls "C:\Program Files (x86)\*" 2>nul | findstr "(F)" | findstr "BUILTIN\Users"
icacls "C:\Program Files\*" 2>nul | findstr "(M)" | findstr "Everyone"
icacls "C:\Program Files (x86)\*" 2>nul | findstr "(M)" | findstr "Everyone"
icacls "C:\Program Files\*" 2>nul | findstr "(M)" | findstr "BUILTIN\Users"
icacls "C:\Program Files (x86)\*" 2>nul | findstr "(M)" | findstr "BUILTIN\Users"
icacls "C:\Documents and Settings\*" 2>nul | findstr "(F)" | findstr "Everyone"
icacls "C:\Documents and Settings\*" 2>nul | findstr "(M)" | findstr "Everyone"
icacls "C:\Documents and Settings\*" 2>nul | findstr "(F)" | findstr "BUILTIN\Users"
icacls "C:\Documents and Settings\*" 2>nul | findstr "(M)" | findstr "BUILTIN\Users"
icacls "C:\Users\*" 2>nul | findstr "(F)" | findstr "Everyone"
icacls "C:\Users\*" 2>nul | findstr "(F)" | findstr "BUILTIN\Users"
icacls "C:\Users\*" 2>nul | findstr "(M)" | findstr "Everyone"
icacls "C:\Users\*" 2>nul | findstr "(M)" | findstr "BUILTIN\Users"
icacls "C:\Documents and Settings\*" /T 2>nul | findstr ":F" | findstr "BUILTIN\Users"
icacls "C:\Users\*" /T 2>nul | findstr ":F" | findstr "BUILTIN\Users"
echo.

echo [+] World Writable Program Files and User Directories (cacls for older versions of Windows)
cacls "C:\Program Files\*" 2>nul | findstr "(F)" | findstr "Everyone"
cacls "C:\Program Files (x86)\*" 2>nul | findstr "(F)" | findstr "Everyone"
cacls "C:\Program Files\*" 2>nul | findstr "(F)" | findstr "BUILTIN\Users"
cacls "C:\Program Files (x86)\*" 2>nul | findstr "(F)" | findstr "BUILTIN\Users"
cacls "C:\Program Files\*" 2>nul | findstr "(M)" | findstr "Everyone"
cacls "C:\Program Files (x86)\*" 2>nul | findstr "(M)" | findstr "Everyone"
cacls "C:\Program Files\*" 2>nul | findstr "(M)" | findstr "BUILTIN\Users"
cacls "C:\Program Files (x86)\*" 2>nul | findstr "(M)" | findstr "BUILTIN\Users"
cacls "C:\Documents and Settings\*" 2>nul | findstr "(F)" | findstr "Everyone"
cacls "C:\Documents and Settings\*" 2>nul | findstr "(M)" | findstr "Everyone"
cacls "C:\Documents and Settings\*" 2>nul | findstr "(F)" | findstr "BUILTIN\Users"
cacls "C:\Documents and Settings\*" 2>nul | findstr "(M)" | findstr "BUILTIN\Users"
cacls "C:\Users\*" 2>nul | findstr "(F)" | findstr "Everyone"
cacls "C:\Users\*" 2>nul | findstr "(F)" | findstr "BUILTIN\Users"
cacls "C:\Users\*" 2>nul | findstr "(M)" | findstr "Everyone"
cacls "C:\Users\*" 2>nul | findstr "(M)" | findstr "BUILTIN\Users"
cacls "C:\Documents and Settings\*" /T 2>nul | findstr ":F" | findstr "BUILTIN\Users"
cacls "C:\Users\*" /T 2>nul | findstr ":F" | findstr "BUILTIN\Users"
echo.

echo [+] Checking if Administrator's directory is accessible
dir "C:\Users\Administrator"
dir "C:\Documents and Settings\Administrator"
echo.

echo [+] Contents of User Directories
dir "C:\Users\" /a /b /s 2>nul | findstr /v /i "Favorites\\" | findstr /v /i "AppData\\" | findstr /v /i "Microsoft\\" |  findstr /v /i "Application Data\\"
dir "C:\Documents and Settings\" /a /b /s 2>nul | findstr /v /i "Favorites\\" | findstr /v /i "AppData\\" | findstr /v /i "Microsoft\\" |  findstr /v /i "Application Data\\"
echo.

echo [+] Contents of C:\
dir "C:\" /b
echo.

echo [+] Contents of C:\Program Files
dir "C:\Program Files" /b
echo.

echo [+] Contents of C:\Program Files (x86)
dir "C:\Program Files (x86)" /b
echo.

echo [+] Contents of C:\inetpub\
dir /a /b C:\inetpub\
echo.



echo [*] SEARCHING FOR CONFIGURATION AND SENSITIVE FILES
echo.

echo [+] Searching for common config files
dir /s /b php.ini httpd.conf httpd-xampp.conf my.ini my.cnf web.config
echo.

echo [+] Contents of applicationHost.config
type C:\Windows\System32\inetsrv\config\applicationHost.config 2>nul
echo.

echo [+] Searching for unattend / sysprep files
dir /b /s unattended.xml* sysprep.xml* sysprep.inf* unattend.xml*
echo.

echo [+] Enumerating stored passwords
cmdkey /list
echo.

echo [+] Checking for accessible SAM / SYSTEM files
dir %SYSTEMROOT%\repair\SAM 2>nul
dir %SYSTEMROOT%\System32\config\RegBack\SAM 2>nul
dir %SYSTEMROOT%\System32\config\SAM 2>nul
dir %SYSTEMROOT%\repair\system 2>nul
dir %SYSTEMROOT%\System32\config\SYSTEM 2>nul
dir %SYSTEMROOT%\System32\config\RegBack\system 2>nul
dir /a /b /s SAM.b*
echo.

echo [+] Searching for vnc, kdbx, and rdp files
dir /a /s /b *.kdbx *vnc.ini *.rdp
echo.

echo [+] Searching for possible password files
dir /s /b *pass* *cred* *vnc* *.config*
echo.

echo [+] Searching for files containing passwords
start /b findstr /sim password *.xml *.ini *.txt *.config *.bak 2>nul
echo.


echo [*] REGISTRY CHECKS
echo.

echo [+] Searching registry for passwords
reg query "HKLM\SOFTWARE\Microsoft\Windows NT\Currentversion\Winlogon" 2>nul | findstr "DefaultUserName DefaultDomainName DefaultPassword"
reg query HKLM /f password /t REG_SZ /s /k
reg query HKCU /f password /t REG_SZ /s /k
reg query "HKCU\Software\ORL\WinVNC3\Password"
reg query "HKLM\SYSTEM\Current\ControlSet\Services\SNMP"
reg query "HKCU\Software\SimonTatham\PuTTY\Sessions"
echo.

echo [+] Checking for AlwaysInstallElevated
reg query HKCU\SOFTWARE\Policies\Microsoft\Windows\Installer\AlwaysInstallElevated
reg query HKLM\SOFTWARE\Policies\Microsoft\Windows\Installer\AlwaysInstallElevated
echo.



echo [*] ENUMERATING PROCESSES AND APPLICATIONS
echo.

echo [+] Powershell version check
REG QUERY "HKLM\SOFTWARE\Microsoft\PowerShell\1\PowerShellEngine" /v PowerShellVersion
echo.

echo [+] Enumerating startup programs
wmic startup get caption,command
echo.

echo [+] Current processes
tasklist /v
echo.

echo [+] Current processes with services
tasklist /SVC
echo.

echo [+] Searching for Apache / Xampp
dir /s /b apache* xampp*
echo.



echo [*] SERVICE CHECKS
echo.
cd %cwd%

echo [+] Finding Unquoted Service Paths
wmic service get name,displayname,pathname,startmode 2>nul |findstr /i "Auto" 2>nul |findstr /i /v "C:\Windows\\" 2>nul |findstr /i /v """
sc query state= all > scoutput.txt
findstr "SERVICE_NAME:" scoutput.txt > Servicenames.txt
FOR /F "tokens=2 delims= " %%i in (Servicenames.txt) DO @echo %%i >> services.txt
FOR /F %%i in (services.txt) DO @sc qc %%i | findstr "BINARY_PATH_NAME" >> path.txt
find /v """" path.txt > unquotedpaths.txt
sort unquotedpaths.txt|findstr /i /v C:\WINDOWS
del /f Servicenames.txt
del /f services.txt
del /f path.txt
del /f scoutput.txt
del /f unquotedpaths.txt
echo.

echo [+] Finding services with weak permissions
accesschk.exe -uwcqv "Authenticated Users" * /accepteula
accesschk.exe -uwcqv "Everyone" * /accepteula
accesschk.exe -uwcqv "Users" * /accepteula
echo.

echo [+] Finding services with modifiable registry values
accesschk.exe -kvqwsu "Authenticated Users" hklm\system\currentcontrolset\services /accepteula
accesschk.exe -kvqwsu "Users" hklm\system\currentcontrolset\services /accepteula
accesschk.exe -kvqwsu "Everyone" hklm\system\currentcontrolset\services /accepteula
echo.

echo [+] Currently running services
net start
echo.
