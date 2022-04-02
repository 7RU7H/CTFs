# Powershell101-And-Bonuses
```powershell
# Powershell is an object-oriented scripting language

$varOne = "power"
$varTwo = "shell"
$varOne.GetType()

[int]$varOne #type conver

$Error #contains an array of error objects.
$Host #contains information about the current hosting application.
$Profile #contains the path to the current user profile for PowerShell.
$PID #contains the process ID of current PowerShell session.
$PSUICulture #contains the UI culture or the regional language of the user interface.
$NULL #contains the value of NULL.
$False #contains the value of False.
$True #contains the value of True.

$cmdletArray = "Start", "Stop", "Read", "Write", "New", "Out"

operators = "-eq", "-ne", "-le", "-ge" , "-gt", "-lt", "-and"
# c equivalents: ==, !=, <=, >=, >, <, &&, 
# -is is a boolean operator
# | is pipe operator like bash


Get-Verb        # List all verbs
Get-Command     # List all commands
Get-Help $Command-Name
Get-Command Verb-* 
Get-Command *-Noun
# Commands and Cmdlets are both executables in ps, Command= compiled, can be solo-executed cmdlets
# Light-weight Commands - No output formatting, parsing or error presentation
# Piped OBJECT from one cmdlet to the next, like Bash BUT OBJECTS NOT OUTPUT!
# Add all formatting, etc at the end of the chain
#
Get-PSProvider
# A provider is .NET program to access data stores
# Typicall includ in modules and are accessible after the module has been loaded into the current session
Get-Item
Get-Item alias: # list all the aliases, i.e foreach -> ForEach-Object
Get-Modules
# Modules are packages that contain additional cmdlets, functions, providers, etc
# Can be imported into the current session
Get-ExecutionPolicy
Set-ExecutionPolicy RemoteSigned
powershell.exe -exec bypass

# -Flags
# -Name
# -ListAvailable


Get-ChildItem ENV:      # Display all environment variables
Set-Location LOCAL:		# set a location 
Get-ChildItem -Hidden	# Show all files in a directory

## Finding Files 
Get-ChildItem
#Find all finds of a particular filetype:
Get-ChildItem -Path C:\ -Include *.doc,*.[FILETYPE] -File -Recurse -ErrorAction SilentlyContinue
# Find all find that include *"..."* BUT also exclude wildcard.Filetype
Get-ChildItem –Path C:\ -Include *HSG* -Exclude *.JPG,*.MP3,*.TMP -File -Recurse -ErrorAction SilentlyContinue
# Display pdirectory listing under a parent file, flag: recursive for recursive directory scrapping
Get-Childitem -Path C:\ -Recurse
Get-ChildItem C:\ -Recurse | Select-String -Pattern "password"


## Networking ##

# Test connection
Test-NetConnection -ComputerName 127.0.0.1 -Port 80
# Check the test connection with:
(New-Object System.Net.Sockets.TcpClient("127.0.0.1", "80")).Connected

## Processes, tasks and scheduled tasks ##
Get-Process
schtasks
schtasks /query /fo /LIST /v


## Host Security Detection ##

Get-CimInstance -Namespace root/SecurityCenter2 -ClassName AntivirusProduct     # Windows servers may not have SecurityCenter2
Get-Service WinDefend
Get-MpComputerStatus                                                            # Get the status of security solution elements
Get-MpThreat                                                                    # History of detected threats
Get-NetFirewallProfile | Format-Table Name, Enabled                             # Firewall details
Get-NetFirewallRule | select DisplayName, Enabled, Description                  # Show all enable firewall rules
Get-NetFirewallRule | findstr "rulename"                                        # Find specific rule with findstr
Set-NetFirewallProfile -Profile Domain, Public, Private -Enabled False          # disable a firewall


## Active Directory ##

Get-ADUser -Filter *
Get-ADUser -Filter * -SearchBase "CN=Admin,OU=THM,DC=redteam,DC=com" # CN=Common Name, DC=Domain Component, OU=OrganizationalUnitName, et
Get-ADForest | Select-Object Domains
Get-ADDomain | Select-Object NetBIOSName, DNSRoot, InfrastructureMaster
Get-ADTrust -Filter * | Select-Object Direction,Source,Target
Get-NetDomain
Get-NetDomainController
Get-NetForest
Get-NetDomainTrust

```
