# Seatbelt output

```powershell
(empireadmin) function Invoke-Seatbelt
{
    [CmdletBinding()]
    Param (
        [String]
        $Command = " " 
 

                        %&&@@@&&                                                                                  
                        &&&&&&&%%%,                       #&&@@@@@@%%%%%%###############%                         
                        &%&   %&%%                        &////(((&%%%%%#%################//((((###%%%%%%%%%%%%%%%
%%%%%%%%%%%######%%%#%%####%  &%%**#                      @////(((&%%%%%%######################(((((((((((((((((((
#%#%%%%%%%#######%#%%#######  %&%,,,,,,,,,,,,,,,,         @////(((&%%%%%#%#####################(((((((((((((((((((
#%#%%%%%%#####%%#%#%%#######  %%%,,,,,,  ,,.   ,,         @////(((&%%%%%%%######################(#(((#(#((((((((((
#####%%%####################  &%%......  ...   ..         @////(((&%%%%%%%###############%######((#(#(####((((((((
#######%##########%#########  %%%......  ...   ..         @////(((&%%%%%#########################(#(#######((#####
###%##%%####################  &%%...............          @////(((&%%%%%%%%##############%#######(#########((#####
#####%######################  %%%..                       @////(((&%%%%%%%################                        
                        &%&   %%%%%      S34tb3lt         %////(((&%%%%%%%%#############*                         
                        &%%&&&%%%%%        v1.1.0         ,(((&%%%%%%%%%%%%%%%%%,                                 
                         #%%%%##,                                                                                 


====== AMSIProviders ======

  GUID                           : {2781761E-28E0-4109-99FE-B9D127C57AFE}
  ProviderPath                   : "C:\ProgramData\Microsoft\Windows Defender\platform\4.18.2007.8-0\MpOav.dll"

====== AntiVirus ======

Cannot enumerate antivirus. root\SecurityCenter2 WMI namespace is not available on Windows Servers
====== AppLocker ======

ERROR:   [!] Terminating exception running command 'AppLocker': System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObjectSearcher.Initialize()
   at System.Management.ManagementObjectSearcher.Get()
   at S34tb3lt.Commands.Windows.AppLockerCommand.<Execute>d__9.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== ARPTable ======



  Interface            :  Loopback Pseudo-Interface 1 (127.0.0.1) --- Index 1
    DNS Servers        :  fec0:0:0:ffff::1%1,fec0:0:0:ffff::2%1,fec0:0:0:ffff::3%1

    Internet Address      Physical Address      Type
    224.0.0.22            00-00-00-00-00-00     Static
    239.255.255.250       00-00-00-00-00-00     Static


  Interface            :  Ethernet (10.200.102.219) --- Index 9
    DNS Servers        :  10.200.102.117

    Internet Address      Physical Address      Type
    10.40.0.1             00-00-00-00-00-00     Invalid
    10.200.102.1          02-DB-69-F8-22-B1     Dynamic
    10.200.102.117        02-A6-29-A6-EF-9B     Dynamic
    10.200.102.232        02-4E-A9-7F-BC-DB     Dynamic
    10.200.102.255        FF-FF-FF-FF-FF-FF     Static
    224.0.0.22            01-00-5E-00-00-16     Static
    224.0.0.251           01-00-5E-00-00-FB     Static
    224.0.0.252           01-00-5E-00-00-FC     Static
    239.255.255.250       01-00-5E-7F-FF-FA     Static
    255.255.255.255       FF-FF-FF-FF-FF-FF     Static
====== AuditPolicies ======

====== AuditPolicyRegistry ======

====== AutoRuns ======


  HKLM:\SOFTWARE\Microsoft\Windows\CurrentVersion\Run :
    C:\Windows\system32\SecurityHealthSystray.exe
====== ChromeBookmarks ======

====== ChromeHistory ======

====== ChromePresence ======

====== CloudCredentials ======

====== CredEnum ======

ERROR:   [!] Terminating exception running command 'CredEnum': System.ComponentModel.Win32Exception (0x80004005): A specified logon session does not exist. It may already have been terminated
   at S34tb3lt.Commands.Windows.CredEnumCommand.<Execute>d__9.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== CredGuard ======

ERROR: System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObjectSearcher.Initialize()
   at System.Management.ManagementObjectSearcher.Get()
   at S34tb3lt.Commands.CredentialGuardCommand.<Execute>d__9.MoveNext()
====== dir ======

  LastAccess LastWrite  Size      Path

  16-06-21   20-06-24   527B      C:\Users\Default\Desktop\EC2 Feedback.website
  16-06-21   20-06-24   554B      C:\Users\Default\Desktop\EC2 Microsoft Windows Guide.website
  18-11-14   18-11-14   0B        C:\Users\Default\Documents\My Music\
  18-11-14   18-11-14   0B        C:\Users\Default\Documents\My Pictures\
  18-11-14   18-11-14   0B        C:\Users\Default\Documents\My Videos\
  20-08-09   20-08-09   37B       C:\Users\petersj\Desktop\user.txt
  20-07-29   20-07-29   0B        C:\Users\petersj\Documents\My Music\
  20-07-29   20-07-29   0B        C:\Users\petersj\Documents\My Pictures\
  20-07-29   20-07-29   0B        C:\Users\petersj\Documents\My Videos\
  18-11-14   18-11-14   0B        C:\Users\Public\Documents\My Music\
  18-11-14   18-11-14   0B        C:\Users\Public\Documents\My Pictures\
  18-11-14   18-11-14   0B        C:\Users\Public\Documents\My Videos\
====== DNSCache ======

ERROR: System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObjectSearcher.Initialize()
   at System.Management.ManagementObjectSearcher.Get()
   at S34tb3lt.Commands.Windows.DNSCacheCommand.<Execute>d__10.MoveNext()
====== DotNet ======

ERROR:   [!] Terminating exception running command 'DotNet': System.FormatException: Input string was not in a correct format.
   at System.Number.StringToNumber(String str, NumberStyles options, NumberBuffer& number, NumberFormatInfo info, Boolean parseDecimal)
   at System.Number.ParseInt32(String s, NumberStyles style, NumberFormatInfo info)
   at S34tb3lt.Commands.Windows.DotNetCommand.<Execute>d__12.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== DpapiMasterKeys ======

  Folder : C:\Users\petersj\AppData\Roaming\Microsoft\Protect\S-1-5-21-3906589501-690843102-3982269896-1202

    LastAccessed              LastModified              FileName
    ------------              ------------              --------
    7/31/2020 3:37:45 AM      7/31/2020 3:37:45 AM      cc892ed1-f771-45bc-9ac3-7769dbc85718


  [*] Use the Mimikatz "dpapi::masterkey" module with appropriate arguments (/pvk or /rpc) to decrypt
  [*] You can also extract many DPAPI masterkeys from memory with the Mimikatz "sekurlsa::dpapi" module
  [*] You can also use SharpDPAPI for masterkey retrieval.
====== EnvironmentPath ======

  Name                           : C:\Program Files\PHP\v7.4
  SDDL                           : O:BAD:AI(A;ID;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;CIIOID;GA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;ID;FA;;;SY)(A;OICIIOID;GA;;;SY)(A;ID;FA;;;BA)(A;OICIIOID;GA;;;BA)(A;ID;0x1200a9;;;BU)(A;OICIIOID;GXGR;;;BU)(A;OICIIOID;GA;;;CO)(A;ID;0x1200a9;;;AC)(A;OICIIOID;GXGR;;;AC)(A;ID;0x1200a9;;;S-1-15-2-2)(A;OICIIOID;GXGR;;;S-1-15-2-2)

  Name                           : C:\Windows\system32
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;OICIIO;GA;;;CO)(A;OICIIO;GA;;;SY)(A;;0x1301bf;;;SY)(A;OICIIO;GA;;;BA)(A;;0x1301bf;;;BA)(A;OICIIO;GXGR;;;BU)(A;;0x1200a9;;;BU)(A;CIIO;GA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)(A;OICIIO;GXGR;;;AC)(A;;0x1200a9;;;S-1-15-2-2)(A;OICIIO;GXGR;;;S-1-15-2-2)

  Name                           : C:\Windows
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;OICIIO;GA;;;CO)(A;OICIIO;GA;;;SY)(A;;0x1301bf;;;SY)(A;OICIIO;GA;;;BA)(A;;0x1301bf;;;BA)(A;OICIIO;GXGR;;;BU)(A;;0x1200a9;;;BU)(A;CIIO;GA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)(A;OICIIO;GXGR;;;AC)(A;;0x1200a9;;;S-1-15-2-2)(A;OICIIO;GXGR;;;S-1-15-2-2)

  Name                           : C:\Windows\System32\Wbem
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;OICIIO;GA;;;CO)(A;OICIIO;GA;;;SY)(A;;0x1301bf;;;SY)(A;OICIIO;GA;;;BA)(A;;0x1301bf;;;BA)(A;OICIIO;GXGR;;;BU)(A;;0x1200a9;;;BU)(A;CIIO;GA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)(A;OICIIO;GXGR;;;AC)(A;;0x1200a9;;;S-1-15-2-2)(A;OICIIO;GXGR;;;S-1-15-2-2)

  Name                           : C:\Windows\System32\WindowsPowerShell\v1.0\
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;OICIIO;GA;;;CO)(A;OICIIO;GA;;;SY)(A;;0x1301bf;;;SY)(A;OICIIO;GA;;;BA)(A;;0x1301bf;;;BA)(A;OICIIO;GXGR;;;BU)(A;;0x1200a9;;;BU)(A;CIIO;GA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)(A;OICIIO;GXGR;;;AC)(A;;0x1200a9;;;S-1-15-2-2)(A;OICIIO;GXGR;;;S-1-15-2-2)

  Name                           : C:\Windows\System32\OpenSSH\
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;OICIIO;GA;;;CO)(A;OICIIO;GA;;;SY)(A;;0x1301bf;;;SY)(A;OICIIO;GA;;;BA)(A;;0x1301bf;;;BA)(A;OICIIO;GXGR;;;BU)(A;;0x1200a9;;;BU)(A;CIIO;GA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)(A;OICIIO;GXGR;;;AC)(A;;0x1200a9;;;S-1-15-2-2)(A;OICIIO;GXGR;;;S-1-15-2-2)

  Name                           : C:\Program Files\Amazon\cfn-bootstrap\
  SDDL                           : O:SYD:AI(A;ID;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;CIIOID;GA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;ID;FA;;;SY)(A;OICIIOID;GA;;;SY)(A;ID;FA;;;BA)(A;OICIIOID;GA;;;BA)(A;ID;0x1200a9;;;BU)(A;OICIIOID;GXGR;;;BU)(A;OICIIOID;GA;;;CO)(A;ID;0x1200a9;;;AC)(A;OICIIOID;GXGR;;;AC)(A;ID;0x1200a9;;;S-1-15-2-2)(A;OICIIOID;GXGR;;;S-1-15-2-2)

  Name                           : C:\Program Files\Microsoft\Web Platform Installer\
  SDDL                           : O:SYD:AI(A;ID;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;CIIOID;GA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;ID;FA;;;SY)(A;OICIIOID;GA;;;SY)(A;ID;FA;;;BA)(A;OICIIOID;GA;;;BA)(A;ID;0x1200a9;;;BU)(A;OICIIOID;GXGR;;;BU)(A;OICIIOID;GA;;;CO)(A;ID;0x1200a9;;;AC)(A;OICIIOID;GXGR;;;AC)(A;ID;0x1200a9;;;S-1-15-2-2)(A;OICIIOID;GXGR;;;S-1-15-2-2)

  Name                           : C:\Users\Administrator\AppData\Local\Microsoft\WindowsApps
  SDDL                           : 

  Name                           : C:\Windows\system32\config\systemprofile\AppData\Local\Microsoft\WindowsApps
  SDDL                           : 

  Name                           : C:\Users\petersj\AppData\Local\Microsoft\WindowsApps
  SDDL                           : O:S-1-5-21-3906589501-690843102-3982269896-1202D:AI(A;OICIID;FA;;;SY)(A;OICIID;FA;;;BA)(A;OICIID;FA;;;S-1-5-21-3906589501-690843102-3982269896-1202)(A;OICIID;FA;;;S-1-5-21-3906589501-690843102-3982269896-1197)

====== EnvironmentVariables ======

  CurrentProcess ALLUSERSPROFILE                    C:\ProgramData
  CurrentProcess APPDATA                            C:\Users\petersj\AppData\Roaming
  CurrentProcess CommonProgramFiles                 C:\Program Files\Common Files
  CurrentProcess CommonProgramFiles(x86)            C:\Program Files (x86)\Common Files
  CurrentProcess CommonProgramW6432                 C:\Program Files\Common Files
  CurrentProcess COMPUTERNAME                       THROWBACK-PROD
  CurrentProcess ComSpec                            C:\Windows\system32\cmd.exe
  CurrentProcess DriverData                         C:\Windows\System32\Drivers\DriverData
  CurrentProcess HOME                               C:\Users\petersj
  CurrentProcess HOMEDRIVE                          C:
  CurrentProcess HOMEPATH                           \Users\petersj
  CurrentProcess LOCALAPPDATA                       C:\Users\petersj\AppData\Local
  CurrentProcess LOGNAME                            throwback\petersj
  CurrentProcess NUMBER_OF_PROCESSORS               2
  CurrentProcess OS                                 Windows_NT
  CurrentProcess Path                               C:\Program Files\PHP\v7.4;C:\Windows\system32;C:\Windows;C:\Windows\System32\Wbem;C:\Windows\System32\WindowsPowerShell\v1.0\;C:\Windows\System32\OpenSSH\;C:\Program Files\Amazon\cfn-bootstrap\;C:\Program Files\Microsoft\Web Platform Installer\;C:\Users\Administrator\AppData\Local\Microsoft\WindowsApps;;C:\Windows\system32\config\systemprofile\AppData\Local\Microsoft\WindowsApps;C:\Users\petersj\AppData\Local\Microsoft\WindowsApps;
  CurrentProcess PATHEXT                            .COM;.EXE;.BAT;.CMD;.VBS;.VBE;.JS;.JSE;.WSF;.WSH;.MSC;.CPL
  CurrentProcess PROCESSOR_ARCHITECTURE             AMD64
  CurrentProcess PROCESSOR_IDENTIFIER               Intel64 Family 6 Model 79 Stepping 1, GenuineIntel
  CurrentProcess PROCESSOR_LEVEL                    6
  CurrentProcess PROCESSOR_REVISION                 4f01
  CurrentProcess ProgramData                        C:\ProgramData
  CurrentProcess ProgramFiles                       C:\Program Files
  CurrentProcess ProgramFiles(x86)                  C:\Program Files (x86)
  CurrentProcess ProgramW6432                       C:\Program Files
  CurrentProcess PROMPT                             throwback\petersj@THROWBACK-PROD $P$G
  CurrentProcess PSExecutionPolicyPreference        Bypass
  CurrentProcess PSModulePath                       C:\Users\petersj\Documents\WindowsPowerShell\Modules;C:\Program Files\WindowsPowerShell\Modules;C:\Windows\system32\WindowsPowerShell\v1.0\Modules;C:\Program Files (x86)\AWS Tools\PowerShell\
  CurrentProcess PUBLIC                             C:\Users\Public
  CurrentProcess SHELL                              c:\windows\system32\cmd.exe
  CurrentProcess SSH_CLIENT                         10.50.96.34 37790 22
  CurrentProcess SSH_CONNECTION                     10.50.96.34 37790 10.200.102.219 22
  CurrentProcess SSH_TTY                            windows-pty
  CurrentProcess SystemDrive                        C:
  CurrentProcess SystemRoot                         C:\Windows
  CurrentProcess TEMP                               C:\Users\petersj\AppData\Local\Temp
  CurrentProcess TERM                               xterm-256color
  CurrentProcess TMP                                C:\Users\petersj\AppData\Local\Temp
  CurrentProcess USER                               throwback\petersj
  CurrentProcess USERDOMAIN                         THROWBACK
  CurrentProcess USERNAME                           petersj
  CurrentProcess USERPROFILE                        C:\Users\petersj
  CurrentProcess windir                             C:\Windows
  System         ComSpec                            C:\Windows\system32\cmd.exe
  System         DriverData                         C:\Windows\System32\Drivers\DriverData
  System         NUMBER_OF_PROCESSORS               2
  System         OS                                 Windows_NT
  System         Path                               C:\Program Files\PHP\v7.4;C:\Windows\system32;C:\Windows;C:\Windows\System32\Wbem;C:\Windows\System32\WindowsPowerShell\v1.0\;C:\Windows\System32\OpenSSH\;C:\Program Files\Amazon\cfn-bootstrap\;C:\Program Files\Microsoft\Web Platform Installer\;C:\Users\Administrator\AppData\Local\Microsoft\WindowsApps;;
  System         PATHEXT                            .COM;.EXE;.BAT;.CMD;.VBS;.VBE;.JS;.JSE;.WSF;.WSH;.MSC
  System         PROCESSOR_ARCHITECTURE             AMD64
  System         PROCESSOR_IDENTIFIER               Intel64 Family 6 Model 79 Stepping 1, GenuineIntel
  System         PROCESSOR_LEVEL                    6
  System         PROCESSOR_REVISION                 4f01
  System         PSModulePath                       C:\Program Files\WindowsPowerShell\Modules;C:\Windows\system32\WindowsPowerShell\v1.0\Modules;C:\Program Files (x86)\AWS Tools\PowerShell\
  System         TEMP                               C:\Windows\TEMP
  System         TMP                                C:\Windows\TEMP
  System         USERNAME                           SYSTEM
  System         windir                             C:\Windows
  User           Path                               C:\Users\petersj\AppData\Local\Microsoft\WindowsApps;
  User           TEMP                               C:\Users\petersj\AppData\Local\Temp
  User           TMP                                C:\Users\petersj\AppData\Local\Temp
====== ExplicitLogonEvents ======

Listing 4648 Explicit Credential Events - A process logged on using plaintext credentials
Output Format:
  --- TargetUser,ProcessResults,SubjectUser,IpAddress ---
  <Dates the credential was used to logon>


ERROR: Unable to collect. Must be an administrator.
====== ExplorerMRUs ======

====== ExplorerRunCommands ======


  S-1-5-21-3906589501-690843102-3982269896-1202 :
    a          :  powershell\1
    MRUList    :  cedab
    b          :  gpedit.msc\1
    c          :  cmd\1
    d          :  services.msc\1
    e          :  powershell.exe\1
====== FileInfo ======

  Comments                       : 
  CompanyName                    : Microsoft Corporation
  FileDescription                : Ancillary Function Driver for WinSock
  FileName                       : C:\Windows\system32\drivers\afd.sys
  FileVersion                    : 10.0.17763.1 (WinBuild.160101.0800)
  InternalName                   : afd.sys
  IsDebug                        : False
  IsDotNet                       : False
  IsPatched                      : False
  IsPreRelease                   : False
  IsPrivateBuild                 : False
  IsSpecialBuild                 : False
  Language                       : English (United States)
  LegalCopyright                 : © Microsoft Corporation. All rights reserved.
  LegalTrademarks                : 
  OriginalFilename               : afd.sys.mui
  PrivateBuild                   : 
  ProductName                    : Microsoft® Windows® Operating System
  ProductVersion                 : 10.0.17763.1
  SpecialBuild                   : 
  Attributes                     : Archive
  CreationTimeUtc                : 3/18/2020 6:43:03 AM
  LastAccessTimeUtc              : 3/18/2020 6:43:03 AM
  LastWriteTimeUtc               : 3/18/2020 6:43:03 AM
  Length                         : 655160
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;;0x1200a9;;;SY)(A;;0x1200a9;;;BA)(A;;0x1200a9;;;BU)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)(A;;0x1200a9;;;S-1-15-2-2)

  Comments                       : 
  CompanyName                    : Microsoft Corporation
  FileDescription                : Microsoft CoreMessaging Dll
  FileName                       : C:\Windows\system32\coremessaging.dll
  FileVersion                    : 10.0.17763.1217
  InternalName                   : CoreMessaging.dll
  IsDebug                        : False
  IsDotNet                       : False
  IsPatched                      : False
  IsPreRelease                   : False
  IsPrivateBuild                 : False
  IsSpecialBuild                 : False
  Language                       : English (United States)
  LegalCopyright                 : © Microsoft Corporation. All rights reserved.
  LegalTrademarks                : 
  OriginalFilename               : CoreMessaging.dll
  PrivateBuild                   : 
  ProductName                    : Microsoft® Windows® Operating System
  ProductVersion                 : 10.0.17763.1217
  SpecialBuild                   : 
  Attributes                     : Archive
  CreationTimeUtc                : 5/13/2020 5:54:09 PM
  LastAccessTimeUtc              : 5/13/2020 5:54:09 PM
  LastWriteTimeUtc               : 5/13/2020 5:54:09 PM
  Length                         : 918296
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;;0x1200a9;;;SY)(A;;0x1200a9;;;BA)(A;;0x1200a9;;;BU)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)(A;;0x1200a9;;;S-1-15-2-2)

  Comments                       : 
  CompanyName                    : Microsoft Corporation
  FileDescription                : Data Sharing Service NT Service DLL
  FileName                       : C:\Windows\system32\dssvc.dll
  FileVersion                    : 10.0.17763.1 (WinBuild.160101.0800)
  InternalName                   : Data Sharing Service NT Service DLL
  IsDebug                        : False
  IsDotNet                       : False
  IsPatched                      : False
  IsPreRelease                   : False
  IsPrivateBuild                 : False
  IsSpecialBuild                 : False
  Language                       : English (United States)
  LegalCopyright                 : © Microsoft Corporation. All rights reserved.
  LegalTrademarks                : 
  OriginalFilename               : dssvc.dll.mui
  PrivateBuild                   : 
  ProductName                    : Microsoft® Windows® Operating System
  ProductVersion                 : 10.0.17763.1
  SpecialBuild                   : 
  Attributes                     : Archive
  CreationTimeUtc                : 3/18/2020 6:42:34 AM
  LastAccessTimeUtc              : 3/18/2020 6:42:34 AM
  LastWriteTimeUtc               : 3/18/2020 6:42:34 AM
  Length                         : 164864
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;;0x1200a9;;;SY)(A;;0x1200a9;;;BA)(A;;0x1200a9;;;BU)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)(A;;0x1200a9;;;S-1-15-2-2)

  Comments                       : 
  CompanyName                    : Microsoft Corporation
  FileDescription                : Microsoft GDI+
  FileName                       : C:\Windows\system32\gdiplus.dll
  FileVersion                    : 10.0.17763.1282 (WinBuild.160101.0800)
  InternalName                   : gdiplus
  IsDebug                        : False
  IsDotNet                       : False
  IsPatched                      : False
  IsPreRelease                   : False
  IsPrivateBuild                 : False
  IsSpecialBuild                 : False
  Language                       : English (United States)
  LegalCopyright                 : © Microsoft Corporation. All rights reserved.
  LegalTrademarks                : 
  OriginalFilename               : gdiplus
  PrivateBuild                   : 
  ProductName                    : Microsoft® Windows® Operating System
  ProductVersion                 : 10.0.17763.1282
  SpecialBuild                   : 
  Attributes                     : Archive
  CreationTimeUtc                : 6/10/2020 6:27:45 AM
  LastAccessTimeUtc              : 6/10/2020 6:27:45 AM
  LastWriteTimeUtc               : 6/10/2020 6:27:45 AM
  Length                         : 1702400
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;;0x1200a9;;;SY)(A;;0x1200a9;;;BA)(A;;0x1200a9;;;BU)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)(A;;0x1200a9;;;S-1-15-2-2)

  Comments                       : 
  CompanyName                    : Microsoft Corporation
  FileDescription                : Group Policy Preference Client
  FileName                       : C:\Windows\system32\gpprefcl.dll
  FileVersion                    : 10.0.17763.1 (WinBuild.160101.0800)
  InternalName                   : gpprefcl
  IsDebug                        : False
  IsDotNet                       : False
  IsPatched                      : False
  IsPreRelease                   : False
  IsPrivateBuild                 : False
  IsSpecialBuild                 : False
  Language                       : English (United States)
  LegalCopyright                 : © Microsoft Corporation. All rights reserved.
  LegalTrademarks                : 
  OriginalFilename               : gpprefcl
  PrivateBuild                   : 
  ProductName                    : Microsoft® Windows® Operating System
  ProductVersion                 : 10.0.17763.1
  SpecialBuild                   : 
  Attributes                     : Archive
  CreationTimeUtc                : 6/10/2020 6:28:05 AM
  LastAccessTimeUtc              : 6/10/2020 6:28:05 AM
  LastWriteTimeUtc               : 6/10/2020 6:28:05 AM
  Length                         : 694784
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;;0x1200a9;;;SY)(A;;0x1200a9;;;BA)(A;;0x1200a9;;;BU)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)(A;;0x1200a9;;;S-1-15-2-2)

ERROR:   [!] Error accessing C:\Windows\system32\drivers\mrxdav.sys

  Comments                       : 
  CompanyName                    : Microsoft Corporation
  FileDescription                : NT Kernel & System
  FileName                       : C:\Windows\system32
toskrnl.exe
  FileVersion                    : 10.0.17763.1282 (WinBuild.160101.0800)
  InternalName                   : ntkrnlmp.exe
  IsDebug                        : False
  IsDotNet                       : False
  IsPatched                      : False
  IsPreRelease                   : False
  IsPrivateBuild                 : False
  IsSpecialBuild                 : False
  Language                       : English (United States)
  LegalCopyright                 : © Microsoft Corporation. All rights reserved.
  LegalTrademarks                : 
  OriginalFilename               : ntkrnlmp.exe
  PrivateBuild                   : 
  ProductName                    : Microsoft® Windows® Operating System
  ProductVersion                 : 10.0.17763.1282
  SpecialBuild                   : 
  Attributes                     : Archive
  CreationTimeUtc                : 6/10/2020 6:27:39 AM
  LastAccessTimeUtc              : 6/10/2020 6:27:40 AM
  LastWriteTimeUtc               : 6/10/2020 6:27:40 AM
  Length                         : 9673016
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;;0x1200a9;;;SY)(A;;0x1200a9;;;BA)(A;;0x1200a9;;;BU)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)

  Comments                       : 
  CompanyName                    : Microsoft Corporation
  FileDescription                : Program Compatibility Assistant Diagnostic Module
  FileName                       : C:\Windows\system32\pcadm.dll
  FileVersion                    : 10.0.17763.1 (WinBuild.160101.0800)
  InternalName                   : 
  IsDebug                        : False
  IsDotNet                       : False
  IsPatched                      : False
  IsPreRelease                   : False
  IsPrivateBuild                 : False
  IsSpecialBuild                 : False
  Language                       : English (United States)
  LegalCopyright                 : © Microsoft Corporation. All rights reserved.
  LegalTrademarks                : 
  OriginalFilename               : pcadm.dll
  PrivateBuild                   : 
  ProductName                    : Microsoft® Windows® Operating System
  ProductVersion                 : 10.0.17763.1
  SpecialBuild                   : 
  Attributes                     : Archive
  CreationTimeUtc                : 9/15/2018 7:12:20 AM
  LastAccessTimeUtc              : 9/15/2018 7:12:20 AM
  LastWriteTimeUtc               : 9/15/2018 7:12:20 AM
  Length                         : 64512
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;;0x1200a9;;;SY)(A;;0x1200a9;;;BA)(A;;0x1200a9;;;BU)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)(A;;0x1200a9;;;S-1-15-2-2)

  Comments                       : 
  CompanyName                    : Microsoft Corporation
  FileDescription                : Remote Procedure Call Runtime
  FileName                       : C:\Windows\system32\rpcrt4.dll
  FileVersion                    : 10.0.17763.1 (WinBuild.160101.0800)
  InternalName                   : rpcrt4.dll
  IsDebug                        : False
  IsDotNet                       : False
  IsPatched                      : False
  IsPreRelease                   : False
  IsPrivateBuild                 : False
  IsSpecialBuild                 : False
  Language                       : English (United States)
  LegalCopyright                 : © Microsoft Corporation. All rights reserved.
  LegalTrademarks                : 
  OriginalFilename               : rpcrt4.dll.mui
  PrivateBuild                   : 
  ProductName                    : Microsoft® Windows® Operating System
  ProductVersion                 : 10.0.17763.1
  SpecialBuild                   : 
  Attributes                     : Archive
  CreationTimeUtc                : 11/13/2019 6:24:32 AM
  LastAccessTimeUtc              : 11/13/2019 6:24:32 AM
  LastWriteTimeUtc               : 11/13/2019 6:24:32 AM
  Length                         : 1180248
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;;0x1200a9;;;SY)(A;;0x1200a9;;;BA)(A;;0x1200a9;;;BU)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)(A;;0x1200a9;;;S-1-15-2-2)

  Comments                       : 
  CompanyName                    : Microsoft Corporation
  FileDescription                : Task Scheduler Service
  FileName                       : C:\Windows\system32\schedsvc.dll
  FileVersion                    : 10.0.17763.1 (WinBuild.160101.0800)
  InternalName                   : Schedule
  IsDebug                        : False
  IsDotNet                       : False
  IsPatched                      : False
  IsPreRelease                   : False
  IsPrivateBuild                 : False
  IsSpecialBuild                 : False
  Language                       : English (United States)
  LegalCopyright                 : © Microsoft Corporation. All rights reserved.
  LegalTrademarks                : 
  OriginalFilename               : schedsvc.dll.mui
  PrivateBuild                   : 
  ProductName                    : Microsoft® Windows® Operating System
  ProductVersion                 : 10.0.17763.1
  SpecialBuild                   : 
  Attributes                     : Archive
  CreationTimeUtc                : 5/13/2020 5:54:32 PM
  LastAccessTimeUtc              : 5/13/2020 5:54:32 PM
  LastWriteTimeUtc               : 5/13/2020 5:54:32 PM
  Length                         : 872448
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;;0x1200a9;;;SY)(A;;0x1200a9;;;BA)(A;;0x1200a9;;;BU)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)(A;;0x1200a9;;;S-1-15-2-2)

  Comments                       : 
  CompanyName                    : Microsoft Corporation
  FileDescription                : Secondary Logon Service DLL
  FileName                       : C:\Windows\system32\seclogon.dll
  FileVersion                    : 10.0.17763.1 (WinBuild.160101.0800)
  InternalName                   : SECLOGON.EXE
  IsDebug                        : False
  IsDotNet                       : False
  IsPatched                      : False
  IsPreRelease                   : False
  IsPrivateBuild                 : False
  IsSpecialBuild                 : False
  Language                       : English (United States)
  LegalCopyright                 : © Microsoft Corporation. All rights reserved.
  LegalTrademarks                : 
  OriginalFilename               : SECLOGON.EXE.MUI
  PrivateBuild                   : 
  ProductName                    : Microsoft® Windows® Operating System
  ProductVersion                 : 10.0.17763.1
  SpecialBuild                   : 
  Attributes                     : Archive
  CreationTimeUtc                : 9/15/2018 7:12:39 AM
  LastAccessTimeUtc              : 9/15/2018 7:12:39 AM
  LastWriteTimeUtc               : 9/15/2018 7:12:39 AM
  Length                         : 31232
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;;0x1200a9;;;SY)(A;;0x1200a9;;;BA)(A;;0x1200a9;;;BU)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)(A;;0x1200a9;;;S-1-15-2-2)

  Comments                       : 
  CompanyName                    : Microsoft Corporation
  FileDescription                : Full/Desktop Multi-User Win32 Driver
  FileName                       : C:\Windows\system32\win32k.sys
  FileVersion                    : 10.0.17763.557 (WinBuild.160101.0800)
  InternalName                   : win32k.sys
  IsDebug                        : False
  IsDotNet                       : False
  IsPatched                      : False
  IsPreRelease                   : False
  IsPrivateBuild                 : False
  IsSpecialBuild                 : False
  Language                       : English (United States)
  LegalCopyright                 : © Microsoft Corporation. All rights reserved.
  LegalTrademarks                : 
  OriginalFilename               : win32k.sys
  PrivateBuild                   : 
  ProductName                    : Microsoft® Windows® Operating System
  ProductVersion                 : 10.0.17763.557
  SpecialBuild                   : 
  Attributes                     : Archive
  CreationTimeUtc                : 6/12/2019 6:43:00 AM
  LastAccessTimeUtc              : 6/12/2019 6:43:00 AM
  LastWriteTimeUtc               : 6/12/2019 6:43:00 AM
  Length                         : 543744
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;;0x1200a9;;;SY)(A;;0x1200a9;;;BA)(A;;0x1200a9;;;BU)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)

  Comments                       : 
  CompanyName                    : Microsoft Corporation
  FileDescription                : Full/Desktop Win32k Kernel Driver
  FileName                       : C:\Windows\system32\win32kfull.sys
  FileVersion                    : 10.0.17763.1282 (WinBuild.160101.0800)
  InternalName                   : win32kfull.sys
  IsDebug                        : False
  IsDotNet                       : False
  IsPatched                      : False
  IsPreRelease                   : False
  IsPrivateBuild                 : False
  IsSpecialBuild                 : False
  Language                       : English (United States)
  LegalCopyright                 : © Microsoft Corporation. All rights reserved.
  LegalTrademarks                : 
  OriginalFilename               : win32kfull.sys
  PrivateBuild                   : 
  ProductName                    : Microsoft® Windows® Operating System
  ProductVersion                 : 10.0.17763.1282
  SpecialBuild                   : 
  Attributes                     : Archive
  CreationTimeUtc                : 6/10/2020 6:27:39 AM
  LastAccessTimeUtc              : 6/10/2020 6:27:39 AM
  LastWriteTimeUtc               : 6/10/2020 6:27:39 AM
  Length                         : 3635712
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;;0x1200a9;;;SY)(A;;0x1200a9;;;BA)(A;;0x1200a9;;;BU)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)

  Comments                       : 
  CompanyName                    : Microsoft Corporation
  FileDescription                : OS Loader
  FileName                       : C:\Windows\system32\winload.exe
  FileVersion                    : 10.0.17763.1217 (WinBuild.160101.0800)
  InternalName                   : osloader.exe
  IsDebug                        : False
  IsDotNet                       : False
  IsPatched                      : False
  IsPreRelease                   : False
  IsPrivateBuild                 : False
  IsSpecialBuild                 : False
  Language                       : English (United States)
  LegalCopyright                 : © Microsoft Corporation. All rights reserved.
  LegalTrademarks                : 
  OriginalFilename               : osloader.exe
  PrivateBuild                   : 
  ProductName                    : Microsoft® Windows® Operating System
  ProductVersion                 : 10.0.17763.1217
  SpecialBuild                   : 
  Attributes                     : Archive
  CreationTimeUtc                : 5/13/2020 5:54:32 PM
  LastAccessTimeUtc              : 5/13/2020 5:54:32 PM
  LastWriteTimeUtc               : 5/13/2020 5:54:32 PM
  Length                         : 1473088
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;;0x1200a9;;;SY)(A;;0x1200a9;;;BA)(A;;0x1200a9;;;BU)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)(A;;0x1200a9;;;S-1-15-2-2)

  Comments                       : 
  CompanyName                    : Microsoft Corporation
  FileDescription                : Multi-User Windows Server DLL
  FileName                       : C:\Windows\system32\winsrv.dll
  FileVersion                    : 10.0.17763.1 (WinBuild.160101.0800)
  InternalName                   : winsrv
  IsDebug                        : False
  IsDotNet                       : False
  IsPatched                      : False
  IsPreRelease                   : False
  IsPrivateBuild                 : False
  IsSpecialBuild                 : False
  Language                       : English (United States)
  LegalCopyright                 : © Microsoft Corporation. All rights reserved.
  LegalTrademarks                : 
  OriginalFilename               : winsrv.dll.mui
  PrivateBuild                   : 
  ProductName                    : Microsoft® Windows® Operating System
  ProductVersion                 : 10.0.17763.1
  SpecialBuild                   : 
  Attributes                     : Archive
  CreationTimeUtc                : 9/15/2018 7:12:25 AM
  LastAccessTimeUtc              : 9/15/2018 7:12:25 AM
  LastWriteTimeUtc               : 9/15/2018 7:12:25 AM
  Length                         : 66048
  SDDL                           : O:S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464D:PAI(A;;0x1200a9;;;SY)(A;;0x1200a9;;;BA)(A;;0x1200a9;;;BU)(A;;FA;;;S-1-5-80-956008885-3418522649-1831038044-1853292631-2271478464)(A;;0x1200a9;;;AC)(A;;0x1200a9;;;S-1-15-2-2)

====== FileZilla ======

====== FirefoxHistory ======

====== FirefoxPresence ======

====== Hotfixes ======

Enumerating Windows Hotfixes. For *all* Microsoft updates, use the 'MicrosoftUpdates' command.

ERROR:   [!] Terminating exception running command 'Hotfixes': System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObjectSearcher.Initialize()
   at System.Management.ManagementObjectSearcher.Get()
   at S34tb3lt.Commands.Windows.HotfixCommand.<Execute>d__10.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== HuntLolbas ======

Path: C:\Windows\System32\advpack.dll
Path: C:\Windows\SysWOW64\advpack.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-advpack_31bf3856ad364e35_11.0.17763.1_none_d082ca37b5d3d7c3\advpack.dll
Path: C:\Windows\WinSxS\wow64_microsoft-windows-advpack_31bf3856ad364e35_11.0.17763.1_none_dad77489ea3499be\advpack.dll
Path: C:\Windows\System32\at.exe
Path: C:\Windows\SysWOW64\at.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-at_31bf3856ad364e35_10.0.17763.1_none_3dc78e4edc0df1b1\at.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-at_31bf3856ad364e35_10.0.17763.1_none_481c38a1106eb3ac\at.exe
Path: C:\Windows\System32\AtBroker.exe
Path: C:\Windows\SysWOW64\AtBroker.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-atbroker_31bf3856ad364e35_10.0.17763.1_none_c06699b6767ea3f0\AtBroker.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-atbroker_31bf3856ad364e35_10.0.17763.1_none_cabb4408aadf65eb\AtBroker.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-lxss-bash_31bf3856ad364e35_10.0.17763.1075_none_8d4f61fe8e43d555\bash.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-lxss-bash_31bf3856ad364e35_10.0.17763.1_none_3061487514bb83f7\bash.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-lxss-bash_31bf3856ad364e35_10.0.17763.1075_none_8d4f61fe8e43d555\f\bash.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-lxss-bash_31bf3856ad364e35_10.0.17763.1075_none_8d4f61fe8e43d555\r\bash.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-lxss-bash_31bf3856ad364e35_10.0.17763.1075_none_8d4f61fe8e43d555\f\bash.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-lxss-bash_31bf3856ad364e35_10.0.17763.1075_none_8d4f61fe8e43d555\r\bash.exe
Path: C:\Windows\System32\bitsadmin.exe
Path: C:\Windows\SysWOW64\bitsadmin.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-bits-bitsadmin_31bf3856ad364e35_10.0.17763.1_none_3dd77ae7649577fa\bitsadmin.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-bits-bitsadmin_31bf3856ad364e35_10.0.17763.1_none_482c253998f639f5\bitsadmin.exe
Path: C:\Windows\System32\certutil.exe
Path: C:\Windows\SysWOW64\certutil.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-certutil_31bf3856ad364e35_10.0.17763.1_none_a64af1d28b85fec8\certutil.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-certutil_31bf3856ad364e35_10.0.17763.1_none_b09f9c24bfe6c0c3\certutil.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-audiodiagnostic_31bf3856ad364e35_10.0.17763.1_none_b14d5ceb47e2e05b\CL_Invocation.ps1
Path: C:\Windows\diagnostics\system\Audio\CL_Invocation.ps1
Path: C:\Windows\WinSxS\amd64_microsoft-windows-videodiagnostic_31bf3856ad364e35_10.0.17763.1_none_6da811f997075340\CL_MutexVerifiers.ps1
Path: C:\Windows\diagnostics\system\Video\CL_MutexVerifiers.ps1
Path: C:\Windows\System32\cmd.exe
Path: C:\Windows\SysWOW64\cmd.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-commandprompt_31bf3856ad364e35_10.0.17763.592_none_ff9c46f1a031aea0\cmd.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-commandprompt_31bf3856ad364e35_10.0.17763.592_none_09f0f143d492709b\cmd.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-commandprompt_31bf3856ad364e35_10.0.17763.592_none_ff9c46f1a031aea0\f\cmd.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-commandprompt_31bf3856ad364e35_10.0.17763.592_none_ff9c46f1a031aea0\r\cmd.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-commandprompt_31bf3856ad364e35_10.0.17763.592_none_09f0f143d492709b\f\cmd.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-commandprompt_31bf3856ad364e35_10.0.17763.592_none_09f0f143d492709b\r\cmd.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-commandprompt_31bf3856ad364e35_10.0.17763.592_none_ff9c46f1a031aea0\f\cmd.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-commandprompt_31bf3856ad364e35_10.0.17763.592_none_ff9c46f1a031aea0\r\cmd.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-commandprompt_31bf3856ad364e35_10.0.17763.592_none_09f0f143d492709b\f\cmd.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-commandprompt_31bf3856ad364e35_10.0.17763.592_none_09f0f143d492709b\r\cmd.exe
Path: C:\Windows\System32\cmdkey.exe
Path: C:\Windows\SysWOW64\cmdkey.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-s..line-user-interface_31bf3856ad364e35_10.0.17763.1_none_cdad5caa35016f49\cmdkey.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-s..line-user-interface_31bf3856ad364e35_10.0.17763.1_none_d80206fc69623144\cmdkey.exe
Path: C:\Windows\System32\cmstp.exe
Path: C:\Windows\SysWOW64\cmstp.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-rascmak.resources_31bf3856ad364e35_10.0.17763.1_en-us_2c0cad1684b8d27b\cmstp.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-rasconnectionmanager_31bf3856ad364e35_10.0.17763.1_none_4fe62956b8aef8eb\cmstp.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-rasconnectionmanager_31bf3856ad364e35_10.0.17763.1_none_5a3ad3a8ed0fbae6\cmstp.exe
Path: C:\Windows\System32\comsvcs.dll
Path: C:\Windows\SysWOW64\comsvcs.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-c..fe-catsrvut-comsvcs_31bf3856ad364e35_10.0.17763.1282_none_c0620d66719ebcad\comsvcs.dll
Path: C:\Windows\WinSxS\wow64_microsoft-windows-c..fe-catsrvut-comsvcs_31bf3856ad364e35_10.0.17763.1282_none_cab6b7b8a5ff7ea8\comsvcs.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-c..fe-catsrvut-comsvcs_31bf3856ad364e35_10.0.17763.1282_none_c0620d66719ebcad\f\comsvcs.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-c..fe-catsrvut-comsvcs_31bf3856ad364e35_10.0.17763.1282_none_c0620d66719ebcad\r\comsvcs.dll
Path: C:\Windows\WinSxS\wow64_microsoft-windows-c..fe-catsrvut-comsvcs_31bf3856ad364e35_10.0.17763.1282_none_cab6b7b8a5ff7ea8\f\comsvcs.dll
Path: C:\Windows\WinSxS\wow64_microsoft-windows-c..fe-catsrvut-comsvcs_31bf3856ad364e35_10.0.17763.1282_none_cab6b7b8a5ff7ea8\r\comsvcs.dll
Path: C:\Windows\System32\control.exe
Path: C:\Windows\SysWOW64\control.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-control_31bf3856ad364e35_10.0.17763.1_none_8a31e32302a74069\control.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-control_31bf3856ad364e35_10.0.17763.1_none_94868d7537080264\control.exe
Path: C:\Windows\WinSxS\amd64_netfx4-csc_exe_b03f5f7f11d50a3a_4.0.15713.0_none_75029b843b5b1af7\csc.exe
Path: C:\Windows\WinSxS\x86_netfx4-csc_exe_b03f5f7f11d50a3a_4.0.15713.0_none_bcafd25b4fd743fd\csc.exe
Path: C:\Windows\Microsoft.NET\Framework\v4.0.30319\csc.exe
Path: C:\Windows\Microsoft.NET\Framework64\v4.0.30319\csc.exe
Path: C:\Windows\System32\cscript.exe
Path: C:\Windows\SysWOW64\cscript.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-scripting_31bf3856ad364e35_10.0.17763.1217_none_96008760d22181ef\cscript.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-scripting_31bf3856ad364e35_10.0.17763.1217_none_a05531b3068243ea\cscript.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-scripting_31bf3856ad364e35_10.0.17763.1217_none_96008760d22181ef\f\cscript.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-scripting_31bf3856ad364e35_10.0.17763.1217_none_96008760d22181ef\r\cscript.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-scripting_31bf3856ad364e35_10.0.17763.1217_none_a05531b3068243ea\f\cscript.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-scripting_31bf3856ad364e35_10.0.17763.1217_none_a05531b3068243ea\r\cscript.exe
Path: C:\Windows\System32\desktopimgdownldr.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-p..-personalizationcsp_31bf3856ad364e35_10.0.17763.1075_none_8ea65054ac5b1d1d\desktopimgdownldr.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-p..-personalizationcsp_31bf3856ad364e35_10.0.17763.1075_none_8ea65054ac5b1d1d\f\desktopimgdownldr.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-p..-personalizationcsp_31bf3856ad364e35_10.0.17763.1075_none_8ea65054ac5b1d1d\r\desktopimgdownldr.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..-personalizationcsp_31bf3856ad364e35_10.0.17763.1075_none_8ea65054ac5b1d1d\f\desktopimgdownldr.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..-personalizationcsp_31bf3856ad364e35_10.0.17763.1075_none_8ea65054ac5b1d1d\r\desktopimgdownldr.exe
Path: C:\Windows\WinSxS\amd64_netfx4-dfsvc_b03f5f7f11d50a3a_4.0.15713.0_none_a43786f92b366cab\dfsvc.exe
Path: C:\Windows\WinSxS\msil_dfsvc_b03f5f7f11d50a3a_4.0.15713.0_none_069772df8b7f60fe\dfsvc.exe
Path: C:\Windows\Microsoft.NET\Framework\v4.0.30319\dfsvc.exe
Path: C:\Windows\Microsoft.NET\Framework64\v4.0.30319\dfsvc.exe
Path: C:\Windows\Microsoft.NET\assembly\GAC_MSIL\dfsvc\v4.0_4.0.0.0__b03f5f7f11d50a3a\dfsvc.exe
Path: C:\Windows\System32\diskshadow.exe
Path: C:\Windows\SysWOW64\diskshadow.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-vssdiskshadow_31bf3856ad364e35_10.0.17763.1_none_262eee7884162127\diskshadow.exe
Path: C:\Windows\WinSxS\x86_microsoft-windows-vssdiskshadow_31bf3856ad364e35_10.0.17763.1_none_ca1052f4cbb8aff1\diskshadow.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-dns-server-dnscmd_31bf3856ad364e35_10.0.17763.1007_none_34e30bed1fd984fa\dnscmd.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-dns-server-dnscmd_31bf3856ad364e35_10.0.17763.1_none_d7fc21f9a64ab1bb\dnscmd.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-dns-server-dnscmd_31bf3856ad364e35_10.0.17763.1007_none_34e30bed1fd984fa\f\dnscmd.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-dns-server-dnscmd_31bf3856ad364e35_10.0.17763.1007_none_34e30bed1fd984fa\r\dnscmd.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-dns-server-dnscmd_31bf3856ad364e35_10.0.17763.1007_none_34e30bed1fd984fa\f\dnscmd.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-dns-server-dnscmd_31bf3856ad364e35_10.0.17763.1007_none_34e30bed1fd984fa\r\dnscmd.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-directx-graphics-tools_31bf3856ad364e35_10.0.17763.1039_none_7d6ec5fb2de693ad\f\dxcap.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-directx-graphics-tools_31bf3856ad364e35_10.0.17763.1039_none_7d6ec5fb2de693ad\r\dxcap.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-directx-graphics-tools_31bf3856ad364e35_10.0.17763.1039_none_87c3704d624755a8\f\dxcap.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-directx-graphics-tools_31bf3856ad364e35_10.0.17763.1039_none_87c3704d624755a8\r\dxcap.exe
Path: C:\Windows\System32\esentutl.exe
Path: C:\Windows\SysWOW64\esentutl.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-e..ageengine-utilities_31bf3856ad364e35_10.0.17763.529_none_4e6e1eac4ad73428\esentutl.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-e..ageengine-utilities_31bf3856ad364e35_10.0.17763.529_none_58c2c8fe7f37f623\esentutl.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-e..ageengine-utilities_31bf3856ad364e35_10.0.17763.529_none_4e6e1eac4ad73428\f\esentutl.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-e..ageengine-utilities_31bf3856ad364e35_10.0.17763.529_none_4e6e1eac4ad73428\r\esentutl.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-e..ageengine-utilities_31bf3856ad364e35_10.0.17763.529_none_58c2c8fe7f37f623\f\esentutl.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-e..ageengine-utilities_31bf3856ad364e35_10.0.17763.529_none_58c2c8fe7f37f623\r\esentutl.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-e..ageengine-utilities_31bf3856ad364e35_10.0.17763.529_none_4e6e1eac4ad73428\f\esentutl.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-e..ageengine-utilities_31bf3856ad364e35_10.0.17763.529_none_4e6e1eac4ad73428\r\esentutl.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-e..ageengine-utilities_31bf3856ad364e35_10.0.17763.529_none_58c2c8fe7f37f623\f\esentutl.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-e..ageengine-utilities_31bf3856ad364e35_10.0.17763.529_none_58c2c8fe7f37f623\r\esentutl.exe
Path: C:\Windows\System32\eventvwr.exe
Path: C:\Windows\SysWOW64\eventvwr.exe
Path: C:\Windows\WinSxS\amd64_eventviewersettings_31bf3856ad364e35_10.0.17763.1_none_e5bdc1ec5bdc8ffe\eventvwr.exe
Path: C:\Windows\WinSxS\wow64_eventviewersettings_31bf3856ad364e35_10.0.17763.1_none_f0126c3e903d51f9\eventvwr.exe
Path: C:\Windows\System32\expand.exe
Path: C:\Windows\SysWOW64\expand.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-expand_31bf3856ad364e35_10.0.17763.1_none_49386661b009dd04\expand.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-expand_31bf3856ad364e35_10.0.17763.1_none_538d10b3e46a9eff\expand.exe
Path: C:\Program Files\internet explorer\ExtExport.exe
Path: C:\Program Files (x86)\Internet Explorer\ExtExport.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-ie-impexp-extexport_31bf3856ad364e35_11.0.17763.1_none_52b5255e8688e021\ExtExport.exe
Path: C:\Windows\WinSxS\x86_microsoft-windows-ie-impexp-extexport_31bf3856ad364e35_11.0.17763.1_none_f69689dace2b6eeb\ExtExport.exe
Path: C:\Windows\System32\extrac32.exe
Path: C:\Windows\SysWOW64\extrac32.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-extrac32_31bf3856ad364e35_10.0.17763.1_none_cbef84845c0ecfaa\extrac32.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-extrac32_31bf3856ad364e35_10.0.17763.1_none_d6442ed6906f91a5\extrac32.exe
Path: C:\Windows\System32\findstr.exe
Path: C:\Windows\SysWOW64\findstr.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-findstr_31bf3856ad364e35_10.0.17763.1_none_17f57547b1de1380\findstr.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-findstr_31bf3856ad364e35_10.0.17763.1_none_224a1f99e63ed57b\findstr.exe
Path: C:\Windows\System32\forfiles.exe
Path: C:\Windows\SysWOW64\forfiles.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-forfiles_31bf3856ad364e35_10.0.17763.1_none_45e9598535b23646\forfiles.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-forfiles_31bf3856ad364e35_10.0.17763.1_none_503e03d76a12f841\forfiles.exe
Path: C:\Windows\System32\ftp.exe
Path: C:\Windows\SysWOW64\ftp.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-ftp_31bf3856ad364e35_10.0.17763.1_none_9db147d5b0b369b2\ftp.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-ftp_31bf3856ad364e35_10.0.17763.1_none_a805f227e5142bad\ftp.exe
Path: C:\Windows\System32\gpscript.exe
Path: C:\Windows\SysWOW64\gpscript.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-grouppolicy-script_31bf3856ad364e35_10.0.17763.1_none_55dd2267c7d5aee9\gpscript.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-grouppolicy-script_31bf3856ad364e35_10.0.17763.1_none_6031ccb9fc3670e4\gpscript.exe
Path: C:\Windows\hh.exe
Path: C:\Windows\SysWOW64\hh.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-htmlhelp_31bf3856ad364e35_10.0.17763.475_none_3cfe15b70a7eb741\hh.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-htmlhelp_31bf3856ad364e35_10.0.17763.475_none_4752c0093edf793c\hh.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-htmlhelp_31bf3856ad364e35_10.0.17763.475_none_3cfe15b70a7eb741\f\hh.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-htmlhelp_31bf3856ad364e35_10.0.17763.475_none_3cfe15b70a7eb741\r\hh.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-htmlhelp_31bf3856ad364e35_10.0.17763.475_none_4752c0093edf793c\f\hh.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-htmlhelp_31bf3856ad364e35_10.0.17763.475_none_4752c0093edf793c\r\hh.exe
Path: C:\Windows\System32\ie4uinit.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-ie-setup-support_31bf3856ad364e35_11.0.17763.652_none_7e438ae20da293eb\ie4uinit.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-ie-setup-support_31bf3856ad364e35_11.0.17763.652_none_7e438ae20da293eb\f\ie4uinit.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-ie-setup-support_31bf3856ad364e35_11.0.17763.652_none_7e438ae20da293eb\r\ie4uinit.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-ie-setup-support_31bf3856ad364e35_11.0.17763.652_none_7e438ae20da293eb\f\ie4uinit.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-ie-setup-support_31bf3856ad364e35_11.0.17763.652_none_7e438ae20da293eb\r\ie4uinit.exe
Path: C:\Windows\System32\IEAdvpack.dll
Path: C:\Windows\SysWOW64\IEAdvpack.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-ie-ieadvpack_31bf3856ad364e35_11.0.17763.1_none_f3a9af329191e4a6\IEAdvpack.dll
Path: C:\Windows\WinSxS\x86_microsoft-windows-ie-ieadvpack_31bf3856ad364e35_11.0.17763.1_none_978b13aed9347370\IEAdvpack.dll
Path: C:\Windows\WinSxS\amd64_netfx4-ilasm_exe_b03f5f7f11d50a3a_4.0.15713.0_none_5dfa66e22bfd5c70\ilasm.exe
Path: C:\Windows\WinSxS\x86_netfx4-ilasm_exe_b03f5f7f11d50a3a_4.0.15713.0_none_a5a79db940798576\ilasm.exe
Path: C:\Windows\Microsoft.NET\Framework\v4.0.30319\ilasm.exe
Path: C:\Windows\Microsoft.NET\Framework64\v4.0.30319\ilasm.exe
Path: C:\Windows\System32\InfDefaultInstall.exe
Path: C:\Windows\SysWOW64\InfDefaultInstall.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-infdefaultinstall_31bf3856ad364e35_10.0.17763.1_none_5d5a6da4f438d5f5\InfDefaultInstall.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-infdefaultinstall_31bf3856ad364e35_10.0.17763.1_none_67af17f7289997f0\InfDefaultInstall.exe
Path: C:\Windows\WinSxS\amd64_installutil_b03f5f7f11d50a3a_4.0.15713.0_none_d4948e9d0f25af26\InstallUtil.exe
Path: C:\Windows\WinSxS\x86_installutil_b03f5f7f11d50a3a_4.0.15713.0_none_1c41c57423a1d82c\InstallUtil.exe
Path: C:\Windows\Microsoft.NET\Framework\v4.0.30319\InstallUtil.exe
Path: C:\Windows\Microsoft.NET\Framework64\v4.0.30319\InstallUtil.exe
Path: C:\Windows\WinSxS\amd64_jsc_b03f5f7f11d50a3a_4.0.15713.0_none_00f10a3ec57c2b75\jsc.exe
Path: C:\Windows\WinSxS\x86_jsc_b03f5f7f11d50a3a_4.0.15713.0_none_489e4115d9f8547b\jsc.exe
Path: C:\Windows\Microsoft.NET\Framework\v4.0.30319\jsc.exe
Path: C:\Windows\Microsoft.NET\Framework64\v4.0.30319\jsc.exe
Path: C:\Windows\System32\makecab.exe
Path: C:\Windows\SysWOW64\makecab.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-makecab_31bf3856ad364e35_10.0.17763.1158_none_3e76707d3afacc5e\makecab.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-makecab_31bf3856ad364e35_10.0.17763.1158_none_48cb1acf6f5b8e59\makecab.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-makecab_31bf3856ad364e35_10.0.17763.1158_none_3e76707d3afacc5e\f\makecab.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-makecab_31bf3856ad364e35_10.0.17763.1158_none_3e76707d3afacc5e\r\makecab.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-makecab_31bf3856ad364e35_10.0.17763.1158_none_48cb1acf6f5b8e59\f\makecab.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-makecab_31bf3856ad364e35_10.0.17763.1158_none_48cb1acf6f5b8e59\r\makecab.exe
Path: C:\Windows\System32\mavinject.exe
Path: C:\Windows\SysWOW64\mavinject.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-appmanagement-appvwow_31bf3856ad364e35_10.0.17763.1192_none_00e544b6c41e8439\mavinject.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-appmanagement-appvwow_31bf3856ad364e35_10.0.17763.1192_none_0b39ef08f87f4634\mavinject.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-appmanagement-appvwow_31bf3856ad364e35_10.0.17763.1192_none_00e544b6c41e8439\f\mavinject.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-appmanagement-appvwow_31bf3856ad364e35_10.0.17763.1192_none_00e544b6c41e8439\r\mavinject.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-appmanagement-appvwow_31bf3856ad364e35_10.0.17763.1192_none_0b39ef08f87f4634\f\mavinject.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-appmanagement-appvwow_31bf3856ad364e35_10.0.17763.1192_none_0b39ef08f87f4634\r\mavinject.exe
Path: C:\Windows\WinSxS\amd64_netfx4-microsoft.workflow.compiler_b03f5f7f11d50a3a_4.0.15713.0_none_582c8c015167cad5\Microsoft.Workflow.Compiler.exe
Path: C:\Windows\WinSxS\msil_microsoft.workflow.compiler_31bf3856ad364e35_4.0.15713.0_none_31438b4fdc7273c6\Microsoft.Workflow.Compiler.exe
Path: C:\Windows\Microsoft.NET\Framework\v4.0.30319\Microsoft.Workflow.Compiler.exe
Path: C:\Windows\Microsoft.NET\Framework64\v4.0.30319\Microsoft.Workflow.Compiler.exe
Path: C:\Windows\Microsoft.NET\assembly\GAC_MSIL\Microsoft.Workflow.Compiler\v4.0_4.0.0.0__31bf3856ad364e35\Microsoft.Workflow.Compiler.exe
Path: C:\Windows\System32\mmc.exe
Path: C:\Windows\SysWOW64\mmc.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-m..-management-console_31bf3856ad364e35_10.0.17763.1282_none_5d12f3494763006a\mmc.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-m..-management-console_31bf3856ad364e35_10.0.17763.1282_none_67679d9b7bc3c265\mmc.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-m..-management-console_31bf3856ad364e35_10.0.17763.1282_none_5d12f3494763006a\f\mmc.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-m..-management-console_31bf3856ad364e35_10.0.17763.1282_none_5d12f3494763006a\r\mmc.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-m..-management-console_31bf3856ad364e35_10.0.17763.1282_none_67679d9b7bc3c265\f\mmc.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-m..-management-console_31bf3856ad364e35_10.0.17763.1282_none_67679d9b7bc3c265\r\mmc.exe
Path: C:\Windows\WinSxS\amd64_msbuild_b03f5f7f11d50a3a_4.0.15713.0_none_da500ddf9f3ce843\MSBuild.exe
Path: C:\Windows\WinSxS\x86_msbuild_b03f5f7f11d50a3a_4.0.15713.0_none_21fd44b6b3b91149\MSBuild.exe
Path: C:\Windows\Microsoft.NET\Framework\v4.0.30319\MSBuild.exe
Path: C:\Windows\Microsoft.NET\Framework64\v4.0.30319\MSBuild.exe
Path: C:\Windows\Microsoft.NET\assembly\GAC_32\MSBuild\v4.0_4.0.0.0__b03f5f7f11d50a3a\MSBuild.exe
Path: C:\Windows\Microsoft.NET\assembly\GAC_64\MSBuild\v4.0_4.0.0.0__b03f5f7f11d50a3a\MSBuild.exe
Path: C:\Windows\System32\msconfig.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-msconfig-exe_31bf3856ad364e35_10.0.17763.1_none_cb402868f5e97c8d\msconfig.exe
Path: C:\Windows\System32\msdt.exe
Path: C:\Windows\SysWOW64\msdt.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-msdt_31bf3856ad364e35_10.0.17763.1_none_96484bd875afe57a\msdt.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-msdt_31bf3856ad364e35_10.0.17763.1_none_a09cf62aaa10a775\msdt.exe
Path: C:\Windows\System32\mshta.exe
Path: C:\Windows\SysWOW64\mshta.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-ie-htmlapplication_31bf3856ad364e35_11.0.17763.1_none_7e1153fecc60d8b3\mshta.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-ie-htmlapplication_31bf3856ad364e35_11.0.17763.1_none_8865fe5100c19aae\mshta.exe
Path: C:\Windows\System32\mshtml.dll
Path: C:\Windows\SysWOW64\mshtml.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-i..tmlrendering-legacy_31bf3856ad364e35_11.0.17763.1282_none_a389d038ad8b9f4b\mshtml.dll
Path: C:\Windows\WinSxS\wow64_microsoft-windows-i..tmlrendering-legacy_31bf3856ad364e35_11.0.17763.1282_none_adde7a8ae1ec6146\mshtml.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-i..tmlrendering-legacy_31bf3856ad364e35_11.0.17763.1282_none_a389d038ad8b9f4b\f\mshtml.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-i..tmlrendering-legacy_31bf3856ad364e35_11.0.17763.1282_none_a389d038ad8b9f4b\r\mshtml.dll
Path: C:\Windows\WinSxS\wow64_microsoft-windows-i..tmlrendering-legacy_31bf3856ad364e35_11.0.17763.1282_none_adde7a8ae1ec6146\f\mshtml.dll
Path: C:\Windows\WinSxS\wow64_microsoft-windows-i..tmlrendering-legacy_31bf3856ad364e35_11.0.17763.1282_none_adde7a8ae1ec6146\r\mshtml.dll
Path: C:\Windows\System32\msiexec.exe
Path: C:\Windows\SysWOW64\msiexec.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-installer-executable_31bf3856ad364e35_10.0.17763.404_none_be7442fb0ba441e4\msiexec.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-installer-executable_31bf3856ad364e35_10.0.17763.404_none_c8c8ed4d400503df\msiexec.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-installer-executable_31bf3856ad364e35_10.0.17763.404_none_be7442fb0ba441e4\f\msiexec.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-installer-executable_31bf3856ad364e35_10.0.17763.404_none_be7442fb0ba441e4\r\msiexec.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-installer-executable_31bf3856ad364e35_10.0.17763.404_none_c8c8ed4d400503df\f\msiexec.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-installer-executable_31bf3856ad364e35_10.0.17763.404_none_c8c8ed4d400503df\r\msiexec.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-installer-executable_31bf3856ad364e35_10.0.17763.404_none_be7442fb0ba441e4\f\msiexec.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-installer-executable_31bf3856ad364e35_10.0.17763.404_none_be7442fb0ba441e4\r\msiexec.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-installer-executable_31bf3856ad364e35_10.0.17763.404_none_c8c8ed4d400503df\f\msiexec.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-installer-executable_31bf3856ad364e35_10.0.17763.404_none_c8c8ed4d400503df\r\msiexec.exe
Path: C:\Windows\System32
etsh.exe
Path: C:\Windows\SysWOW64
etsh.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-netsh_31bf3856ad364e35_10.0.17763.1_none_5066e02350023e4e
etsh.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-netsh_31bf3856ad364e35_10.0.17763.1_none_5abb8a7584630049
etsh.exe
Path: C:\Windows\System32
tdsutil.exe
Path: C:\Windows\SysWOW64
tdsutil.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-d..ommandline-ntdsutil_31bf3856ad364e35_10.0.17763.1007_none_28fdec53014b9b59
tdsutil.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-d..ommandline-ntdsutil_31bf3856ad364e35_10.0.17763.1_none_cc17025f87bcc81a
tdsutil.exe
Path: C:\Windows\WinSxS\x86_microsoft-windows-d..ommandline-ntdsutil_31bf3856ad364e35_10.0.17763.1007_none_ccdf50cf48ee2a23
tdsutil.exe
Path: C:\Windows\WinSxS\x86_microsoft-windows-d..ommandline-ntdsutil_31bf3856ad364e35_10.0.17763.1_none_6ff866dbcf5f56e4
tdsutil.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-d..ommandline-ntdsutil_31bf3856ad364e35_10.0.17763.1007_none_28fdec53014b9b59\f
tdsutil.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-d..ommandline-ntdsutil_31bf3856ad364e35_10.0.17763.1007_none_28fdec53014b9b59\r
tdsutil.exe
Path: C:\Windows\WinSxS\x86_microsoft-windows-d..ommandline-ntdsutil_31bf3856ad364e35_10.0.17763.1007_none_ccdf50cf48ee2a23\f
tdsutil.exe
Path: C:\Windows\WinSxS\x86_microsoft-windows-d..ommandline-ntdsutil_31bf3856ad364e35_10.0.17763.1007_none_ccdf50cf48ee2a23\r
tdsutil.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-d..ommandline-ntdsutil_31bf3856ad364e35_10.0.17763.1007_none_28fdec53014b9b59\f
tdsutil.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-d..ommandline-ntdsutil_31bf3856ad364e35_10.0.17763.1007_none_28fdec53014b9b59\r
tdsutil.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-d..ommandline-ntdsutil_31bf3856ad364e35_10.0.17763.1007_none_ccdf50cf48ee2a23\f
tdsutil.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-d..ommandline-ntdsutil_31bf3856ad364e35_10.0.17763.1007_none_ccdf50cf48ee2a23\r
tdsutil.exe
Path: C:\Windows\System32\odbcconf.exe
Path: C:\Windows\SysWOW64\odbcconf.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-m..s-mdac-odbcconf-exe_31bf3856ad364e35_10.0.17763.1_none_fe3cc4624a46a1fe\odbcconf.exe
Path: C:\Windows\WinSxS\x86_microsoft-windows-m..s-mdac-odbcconf-exe_31bf3856ad364e35_10.0.17763.1_none_a21e28de91e930c8\odbcconf.exe
Path: C:\Windows\System32\pcalua.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-a..atibility-assistant_31bf3856ad364e35_10.0.17763.1131_none_816c138ef4e40f95\pcalua.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-a..atibility-assistant_31bf3856ad364e35_10.0.17763.1131_none_816c138ef4e40f95\f\pcalua.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-a..atibility-assistant_31bf3856ad364e35_10.0.17763.1131_none_816c138ef4e40f95\r\pcalua.exe
Path: C:\Windows\System32\pcwrun.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-pcwdiagnostic_31bf3856ad364e35_10.0.17763.1_none_e5f1b7c957d1804f\pcwrun.exe
Path: C:\Windows\System32\pcwutl.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-pcwdiagnostic_31bf3856ad364e35_10.0.17763.1_none_e5f1b7c957d1804f\pcwutl.dll
Path: C:\Windows\WinSxS\amd64_microsoft.powershell.pester_31bf3856ad364e35_10.0.17763.1_none_c4f85489cbfa475b\Pester.bat
Path: C:\Windows\WinSxS\wow64_microsoft.powershell.pester_31bf3856ad364e35_10.0.17763.1_none_cf4cfedc005b0956\Pester.bat
Path: C:\Program Files\WindowsPowerShell\Modules\Pester\3.4.0\bin\Pester.bat
Path: C:\Program Files (x86)\WindowsPowerShell\Modules\Pester\3.4.0\bin\Pester.bat
Path: C:\Windows\System32\PresentationHost.exe
Path: C:\Windows\SysWOW64\PresentationHost.exe
Path: C:\Windows\WinSxS\amd64_wpf-presentationhostexe_31bf3856ad364e35_10.0.17763.1_none_60ba1d3678474a35\PresentationHost.exe
Path: C:\Windows\WinSxS\x86_wpf-presentationhostexe_31bf3856ad364e35_10.0.17763.1_none_049b81b2bfe9d8ff\PresentationHost.exe
Path: C:\Windows\System32\print.exe
Path: C:\Windows\SysWOW64\print.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-m..ommandlineutilities_31bf3856ad364e35_10.0.17763.1_none_6de2d78cbf7e0077\print.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-m..ommandlineutilities_31bf3856ad364e35_10.0.17763.1_none_783781def3dec272\print.exe
Path: C:\Windows\System32\psr.exe
Path: C:\Windows\SysWOW64\psr.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-a..roblemstepsrecorder_31bf3856ad364e35_10.0.17763.1282_none_289139651cba849e\psr.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-a..roblemstepsrecorder_31bf3856ad364e35_10.0.17763.1282_none_32e5e3b7511b4699\psr.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-a..roblemstepsrecorder_31bf3856ad364e35_10.0.17763.1282_none_289139651cba849e\f\psr.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-a..roblemstepsrecorder_31bf3856ad364e35_10.0.17763.1282_none_289139651cba849e\r\psr.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-a..roblemstepsrecorder_31bf3856ad364e35_10.0.17763.1282_none_32e5e3b7511b4699\f\psr.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-a..roblemstepsrecorder_31bf3856ad364e35_10.0.17763.1282_none_32e5e3b7511b4699\r\psr.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_en-us_83a32950d1d8064b\pubprn.vbs
Path: C:\Windows\WinSxS\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_en-us_8df7d3a30638c846\pubprn.vbs
Path: C:\Windows\WinSxS\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_en-us_27848dcd197a9515\pubprn.vbs
Path: C:\Windows\System32\Printing_Admin_Scripts\en-US\pubprn.vbs
Path: C:\Windows\SysWOW64\Printing_Admin_Scripts\en-US\pubprn.vbs
Path: C:\Windows\WinSxS\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_en-us_83a32950d1d8064b\f\pubprn.vbs
Path: C:\Windows\WinSxS\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_en-us_83a32950d1d8064b\r\pubprn.vbs
Path: C:\Windows\WinSxS\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_en-us_8df7d3a30638c846\f\pubprn.vbs
Path: C:\Windows\WinSxS\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_en-us_8df7d3a30638c846\r\pubprn.vbs
Path: C:\Windows\WinSxS\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_en-us_27848dcd197a9515\f\pubprn.vbs
Path: C:\Windows\WinSxS\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_en-us_27848dcd197a9515\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_cs-cz_404cddf4eadda9ed\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_cs-cz_404cddf4eadda9ed\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_da-dk_dd86be1be123a5ec\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_da-dk_dd86be1be123a5ec\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_de-de_dab25357e2f9fa86\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_de-de_dab25357e2f9fa86\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_el-gr_834880ead20f6314\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_el-gr_834880ead20f6314\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_en-us_83a32950d1d8064b\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_en-us_83a32950d1d8064b\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_es-es_836e8634d1fef7f0\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_es-es_836e8634d1fef7f0\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_fi-fi_22898ae1c718ea1a\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_fi-fi_22898ae1c718ea1a\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_fr-fr_2625fc33c4d10e52\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_fr-fr_2625fc33c4d10e52\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_hu-hu_6d967c7ba930dd6e\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_hu-hu_6d967c7ba930dd6e\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_it-it_104df27a9c02f3d0\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_it-it_104df27a9c02f3d0\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_ja-jp_b27371878f1e05ab\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_ja-jp_b27371878f1e05ab\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_ko-kr_55dd4e3c818eccc1\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_ko-kr_55dd4e3c818eccc1\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_nb-no_3e6fcf7159b3f87d\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_nb-no_3e6fcf7159b3f87d\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_nl-nl_3caf1aaf5ae00252\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_nl-nl_3caf1aaf5ae00252\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_pl-pl_82eb753140027006\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_pl-pl_82eb753140027006\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_pt-br_853f5fd53e8c03ea\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_pt-br_853f5fd53e8c03ea\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_pt-pt_86212f413dfb73c6\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_pt-pt_86212f413dfb73c6\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_ru-ru_ccc4410522dd01f2\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_ru-ru_ccc4410522dd01f2\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_sv-se_68bf2b7a1a060c4d\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_sv-se_68bf2b7a1a060c4d\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_tr-tr_11cc75c108c20e3e\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_tr-tr_11cc75c108c20e3e\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_zh-cn_e32993beb8f9e05d\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_zh-cn_e32993beb8f9e05d\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_zh-tw_e725d114b66abccd\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_zh-tw_e725d114b66abccd\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_cs-cz_4aa188471f3e6be8\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_cs-cz_4aa188471f3e6be8\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_da-dk_e7db686e158467e7\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_da-dk_e7db686e158467e7\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_de-de_e506fdaa175abc81\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_de-de_e506fdaa175abc81\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_el-gr_8d9d2b3d0670250f\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_el-gr_8d9d2b3d0670250f\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_en-us_8df7d3a30638c846\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_en-us_8df7d3a30638c846\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_es-es_8dc33087065fb9eb\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_es-es_8dc33087065fb9eb\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_fi-fi_2cde3533fb79ac15\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_fi-fi_2cde3533fb79ac15\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_fr-fr_307aa685f931d04d\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_fr-fr_307aa685f931d04d\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_hu-hu_77eb26cddd919f69\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_hu-hu_77eb26cddd919f69\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_it-it_1aa29cccd063b5cb\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_it-it_1aa29cccd063b5cb\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_ja-jp_bcc81bd9c37ec7a6\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_ja-jp_bcc81bd9c37ec7a6\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_ko-kr_6031f88eb5ef8ebc\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_ko-kr_6031f88eb5ef8ebc\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_nb-no_48c479c38e14ba78\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_nb-no_48c479c38e14ba78\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_nl-nl_4703c5018f40c44d\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_nl-nl_4703c5018f40c44d\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_pl-pl_8d401f8374633201\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_pl-pl_8d401f8374633201\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_pt-br_8f940a2772ecc5e5\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_pt-br_8f940a2772ecc5e5\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_pt-pt_9075d993725c35c1\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_pt-pt_9075d993725c35c1\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_ru-ru_d718eb57573dc3ed\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_ru-ru_d718eb57573dc3ed\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_sv-se_7313d5cc4e66ce48\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_sv-se_7313d5cc4e66ce48\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_tr-tr_1c2120133d22d039\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_tr-tr_1c2120133d22d039\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_zh-cn_ed7e3e10ed5aa258\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_zh-cn_ed7e3e10ed5aa258\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_zh-tw_f17a7b66eacb7ec8\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_zh-tw_f17a7b66eacb7ec8\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_cs-cz_e42e4271328038b7\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_cs-cz_e42e4271328038b7\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_da-dk_8168229828c634b6\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_da-dk_8168229828c634b6\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_de-de_7e93b7d42a9c8950\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_de-de_7e93b7d42a9c8950\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_el-gr_2729e56719b1f1de\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_el-gr_2729e56719b1f1de\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_en-us_27848dcd197a9515\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_en-us_27848dcd197a9515\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_es-es_274feab119a186ba\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_es-es_274feab119a186ba\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_fi-fi_c66aef5e0ebb78e4\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_fi-fi_c66aef5e0ebb78e4\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_fr-fr_ca0760b00c739d1c\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_fr-fr_ca0760b00c739d1c\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_hu-hu_1177e0f7f0d36c38\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_hu-hu_1177e0f7f0d36c38\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_it-it_b42f56f6e3a5829a\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_it-it_b42f56f6e3a5829a\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_ja-jp_5654d603d6c09475\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_ja-jp_5654d603d6c09475\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_ko-kr_f9beb2b8c9315b8b\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_ko-kr_f9beb2b8c9315b8b\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_nb-no_e25133eda1568747\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_nb-no_e25133eda1568747\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_nl-nl_e0907f2ba282911c\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_nl-nl_e0907f2ba282911c\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_pl-pl_26ccd9ad87a4fed0\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_pl-pl_26ccd9ad87a4fed0\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_pt-br_2920c451862e92b4\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_pt-br_2920c451862e92b4\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_pt-pt_2a0293bd859e0290\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_pt-pt_2a0293bd859e0290\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_ru-ru_70a5a5816a7f90bc\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_ru-ru_70a5a5816a7f90bc\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_sv-se_0ca08ff661a89b17\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_sv-se_0ca08ff661a89b17\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_tr-tr_b5adda3d50649d08\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_tr-tr_b5adda3d50649d08\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_zh-cn_870af83b009c6f27\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_zh-cn_870af83b009c6f27\r\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_zh-tw_8b073590fe0d4b97\f\pubprn.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\x86_microsoft-windows-p..inscripts.resources_31bf3856ad364e35_10.0.17763.107_zh-tw_8b073590fe0d4b97\r\pubprn.vbs
Path: C:\Windows\System32\rasautou.exe
Path: C:\Windows\SysWOW64\rasautou.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-rasautodial_31bf3856ad364e35_10.0.17763.1_none_009fe89bbd7c8b5f\rasautou.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-rasautodial_31bf3856ad364e35_10.0.17763.1_none_0af492edf1dd4d5a\rasautou.exe
Path: C:\Windows\System32\reg.exe
Path: C:\Windows\SysWOW64\reg.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-r..-commandline-editor_31bf3856ad364e35_10.0.17763.1_none_225a1de282d8e4e1\reg.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-r..-commandline-editor_31bf3856ad364e35_10.0.17763.1_none_2caec834b739a6dc\reg.exe
Path: C:\Windows\WinSxS\amd64_regasm_b03f5f7f11d50a3a_4.0.15713.0_none_703119e5038999ca\RegAsm.exe
Path: C:\Windows\WinSxS\x86_regasm_b03f5f7f11d50a3a_4.0.15713.0_none_b7de50bc1805c2d0\RegAsm.exe
Path: C:\Windows\Microsoft.NET\Framework\v4.0.30319\RegAsm.exe
Path: C:\Windows\Microsoft.NET\Framework64\v4.0.30319\RegAsm.exe
Path: C:\Windows\regedit.exe
Path: C:\Windows\SysWOW64\regedit.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-registry-editor_31bf3856ad364e35_10.0.17763.168_none_68e49f8161901b63\regedit.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-registry-editor_31bf3856ad364e35_10.0.17763.168_none_733949d395f0dd5e\regedit.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-registry-editor_31bf3856ad364e35_10.0.17763.168_none_68e49f8161901b63\f\regedit.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-registry-editor_31bf3856ad364e35_10.0.17763.168_none_68e49f8161901b63\r\regedit.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-registry-editor_31bf3856ad364e35_10.0.17763.168_none_733949d395f0dd5e\f\regedit.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-registry-editor_31bf3856ad364e35_10.0.17763.168_none_733949d395f0dd5e\r\regedit.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-registry-editor_31bf3856ad364e35_10.0.17763.168_none_68e49f8161901b63\f\regedit.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-registry-editor_31bf3856ad364e35_10.0.17763.168_none_68e49f8161901b63\r\regedit.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-registry-editor_31bf3856ad364e35_10.0.17763.168_none_733949d395f0dd5e\f\regedit.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-registry-editor_31bf3856ad364e35_10.0.17763.168_none_733949d395f0dd5e\r\regedit.exe
Path: C:\Windows\System32\regini.exe
Path: C:\Windows\SysWOW64\regini.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-regini_31bf3856ad364e35_10.0.17763.1_none_fd1c265411fa4f7a\regini.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-regini_31bf3856ad364e35_10.0.17763.1_none_0770d0a6465b1175\regini.exe
Path: C:\Windows\System32\Register-CimProvider.exe
Path: C:\Windows\SysWOW64\Register-CimProvider.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-w..ter-cimprovider-exe_31bf3856ad364e35_10.0.17763.1_none_540f87ef441f7cc7\Register-CimProvider.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-w..ter-cimprovider-exe_31bf3856ad364e35_10.0.17763.1_none_5e64324178803ec2\Register-CimProvider.exe
Path: C:\Windows\WinSxS\amd64_regsvcs_b03f5f7f11d50a3a_4.0.15713.0_none_434c448b55fc927a\RegSvcs.exe
Path: C:\Windows\WinSxS\x86_regsvcs_b03f5f7f11d50a3a_4.0.15713.0_none_8af97b626a78bb80\RegSvcs.exe
Path: C:\Windows\Microsoft.NET\Framework\v4.0.30319\RegSvcs.exe
Path: C:\Windows\Microsoft.NET\Framework64\v4.0.30319\RegSvcs.exe
Path: C:\Windows\System32\regsvr32.exe
Path: C:\Windows\SysWOW64\regsvr32.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-regsvr32_31bf3856ad364e35_10.0.17763.1_none_691d073687ad042e\regsvr32.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-regsvr32_31bf3856ad364e35_10.0.17763.1_none_7371b188bc0dc629\regsvr32.exe
Path: C:\Windows\System32\replace.exe
Path: C:\Windows\SysWOW64\replace.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-m..ommandlineutilities_31bf3856ad364e35_10.0.17763.1_none_6de2d78cbf7e0077\replace.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-m..ommandlineutilities_31bf3856ad364e35_10.0.17763.1_none_783781def3dec272\replace.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-rpc-ping_31bf3856ad364e35_10.0.17763.404_none_12acdc3ec642e317\f\rpcping.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-rpc-ping_31bf3856ad364e35_10.0.17763.404_none_12acdc3ec642e317\r\rpcping.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-rpc-ping_31bf3856ad364e35_10.0.17763.404_none_1d018690faa3a512\f\rpcping.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-rpc-ping_31bf3856ad364e35_10.0.17763.404_none_1d018690faa3a512\r\rpcping.exe
Path: C:\Windows\System32\RpcPing.exe
Path: C:\Windows\SysWOW64\RpcPing.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-rpc-ping_31bf3856ad364e35_10.0.17763.404_none_12acdc3ec642e317\RpcPing.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-rpc-ping_31bf3856ad364e35_10.0.17763.404_none_1d018690faa3a512\RpcPing.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-rpc-ping_31bf3856ad364e35_10.0.17763.404_none_12acdc3ec642e317\f\RpcPing.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-rpc-ping_31bf3856ad364e35_10.0.17763.404_none_12acdc3ec642e317\r\RpcPing.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-rpc-ping_31bf3856ad364e35_10.0.17763.404_none_1d018690faa3a512\f\RpcPing.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-rpc-ping_31bf3856ad364e35_10.0.17763.404_none_1d018690faa3a512\r\RpcPing.exe
Path: C:\Windows\System32\rundll32.exe
Path: C:\Windows\SysWOW64\rundll32.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-rundll32_31bf3856ad364e35_10.0.17763.1_none_c8cb3b750313fee0\rundll32.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-rundll32_31bf3856ad364e35_10.0.17763.1_none_d31fe5c73774c0db\rundll32.exe
Path: C:\Windows\System32\runonce.exe
Path: C:\Windows\SysWOW64\runonce.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-runonce_31bf3856ad364e35_10.0.17763.1_none_0680be8217315dfc\runonce.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-runonce_31bf3856ad364e35_10.0.17763.1_none_10d568d44b921ff7\runonce.exe
Path: C:\Windows\System32\sc.exe
Path: C:\Windows\SysWOW64\sc.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-s..llercommandlinetool_31bf3856ad364e35_10.0.17763.1_none_653424fe2cd61e8c\sc.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-s..llercommandlinetool_31bf3856ad364e35_10.0.17763.1_none_6f88cf506136e087\sc.exe
Path: C:\Windows\System32\schtasks.exe
Path: C:\Windows\SysWOW64\schtasks.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-sctasks_31bf3856ad364e35_10.0.17763.1_none_7b0561790d7fc67c\schtasks.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-sctasks_31bf3856ad364e35_10.0.17763.1_none_855a0bcb41e08877\schtasks.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-a..nagement-appvclient_31bf3856ad364e35_10.0.17763.1192_none_1a1def8999debb2d\f\scriptrunner.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-a..nagement-appvclient_31bf3856ad364e35_10.0.17763.1192_none_1a1def8999debb2d\r\scriptrunner.exe
Path: C:\Windows\System32\ScriptRunner.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-a..nagement-appvclient_31bf3856ad364e35_10.0.17763.1192_none_1a1def8999debb2d\ScriptRunner.exe
Path: C:\Windows\System32\setupapi.dll
Path: C:\Windows\SysWOW64\setupapi.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-setupapi_31bf3856ad364e35_10.0.17763.404_none_a9e827df4bc83994\setupapi.dll
Path: C:\Windows\WinSxS\wow64_microsoft-windows-setupapi_31bf3856ad364e35_10.0.17763.404_none_b43cd2318028fb8f\setupapi.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-setupapi_31bf3856ad364e35_10.0.17763.404_none_a9e827df4bc83994\f\setupapi.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-setupapi_31bf3856ad364e35_10.0.17763.404_none_a9e827df4bc83994\r\setupapi.dll
Path: C:\Windows\WinSxS\wow64_microsoft-windows-setupapi_31bf3856ad364e35_10.0.17763.404_none_b43cd2318028fb8f\f\setupapi.dll
Path: C:\Windows\WinSxS\wow64_microsoft-windows-setupapi_31bf3856ad364e35_10.0.17763.404_none_b43cd2318028fb8f\r\setupapi.dll
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-onecore-reverseforwarders_31bf3856ad364e35_10.0.17763.1282_none_efe496583e7a9897\f\setupapi.dll
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-onecore-reverseforwarders_31bf3856ad364e35_10.0.17763.1282_none_efe496583e7a9897\r\setupapi.dll
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-setupapi_31bf3856ad364e35_10.0.17763.404_none_a9e827df4bc83994\f\setupapi.dll
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-setupapi_31bf3856ad364e35_10.0.17763.404_none_a9e827df4bc83994\r\setupapi.dll
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-setupapi_31bf3856ad364e35_10.0.17763.404_none_b43cd2318028fb8f\f\setupapi.dll
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-setupapi_31bf3856ad364e35_10.0.17763.404_none_b43cd2318028fb8f\r\setupapi.dll
Path: C:\Windows\System32\shdocvw.dll
Path: C:\Windows\SysWOW64\shdocvw.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-shdocvw_31bf3856ad364e35_10.0.17763.1_none_d83ad76a640f99cc\shdocvw.dll
Path: C:\Windows\WinSxS\wow64_microsoft-windows-shdocvw_31bf3856ad364e35_10.0.17763.1_none_e28f81bc98705bc7\shdocvw.dll
Path: C:\Windows\System32\shell32.dll
Path: C:\Windows\SysWOW64\shell32.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-shell32_31bf3856ad364e35_10.0.17763.1282_none_b9c8d316e3bcaf6f\shell32.dll
Path: C:\Windows\WinSxS\wow64_microsoft-windows-shell32_31bf3856ad364e35_10.0.17763.1282_none_c41d7d69181d716a\shell32.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-shell32_31bf3856ad364e35_10.0.17763.1282_none_b9c8d316e3bcaf6f\f\shell32.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-shell32_31bf3856ad364e35_10.0.17763.1282_none_b9c8d316e3bcaf6f\r\shell32.dll
Path: C:\Windows\WinSxS\wow64_microsoft-windows-shell32_31bf3856ad364e35_10.0.17763.1282_none_c41d7d69181d716a\f\shell32.dll
Path: C:\Windows\WinSxS\wow64_microsoft-windows-shell32_31bf3856ad364e35_10.0.17763.1282_none_c41d7d69181d716a\r\shell32.dll
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-onecore-reverseforwarders_31bf3856ad364e35_10.0.17763.1282_none_efe496583e7a9897\f\shell32.dll
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-onecore-reverseforwarders_31bf3856ad364e35_10.0.17763.1282_none_efe496583e7a9897\r\shell32.dll
Path: C:\Windows\System32\slmgr.vbs
Path: C:\Windows\SysWOW64\slmgr.vbs
Path: C:\Windows\WinSxS\amd64_microsoft-windows-security-spp-tools_31bf3856ad364e35_10.0.17763.652_none_ba5407e944d510da\slmgr.vbs
Path: C:\Windows\WinSxS\wow64_microsoft-windows-security-spp-tools_31bf3856ad364e35_10.0.17763.652_none_c4a8b23b7935d2d5\slmgr.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-security-spp-tools_31bf3856ad364e35_10.0.17763.652_none_ba5407e944d510da\f\slmgr.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-security-spp-tools_31bf3856ad364e35_10.0.17763.652_none_ba5407e944d510da\r\slmgr.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-security-spp-tools_31bf3856ad364e35_10.0.17763.652_none_c4a8b23b7935d2d5\f\slmgr.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-security-spp-tools_31bf3856ad364e35_10.0.17763.652_none_c4a8b23b7935d2d5\r\slmgr.vbs
Path: C:\Windows\WinSxS\amd64_microsoft-windows-wid_31bf3856ad364e35_10.0.17763.1_none_9870f12fb40ec83a\SqlDumper.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-a..nagement-appvclient_31bf3856ad364e35_10.0.17763.1192_none_1a1def8999debb2d\f\syncappvpublishingserver.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-a..nagement-appvclient_31bf3856ad364e35_10.0.17763.1192_none_1a1def8999debb2d\r\syncappvpublishingserver.exe
Path: C:\Windows\System32\SyncAppvPublishingServer.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-a..nagement-appvclient_31bf3856ad364e35_10.0.17763.1192_none_1a1def8999debb2d\SyncAppvPublishingServer.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-a..nagement-appvclient_31bf3856ad364e35_10.0.17763.1192_none_1a1def8999debb2d\f\syncappvpublishingserver.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-a..nagement-appvclient_31bf3856ad364e35_10.0.17763.1192_none_1a1def8999debb2d\r\syncappvpublishingserver.vbs
Path: C:\Windows\System32\SyncAppvPublishingServer.vbs
Path: C:\Windows\WinSxS\amd64_microsoft-windows-a..nagement-appvclient_31bf3856ad364e35_10.0.17763.1192_none_1a1def8999debb2d\SyncAppvPublishingServer.vbs
Path: C:\Windows\System32\syssetup.dll
Path: C:\Windows\SysWOW64\syssetup.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-syssetup_31bf3856ad364e35_10.0.17763.1_none_619675b2efe03756\syssetup.dll
Path: C:\Windows\WinSxS\wow64_microsoft-windows-syssetup_31bf3856ad364e35_10.0.17763.1_none_6beb20052440f951\syssetup.dll
Path: C:\Windows\System32\tttracer.exe
Path: C:\Windows\SysWOW64\tttracer.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-t..eldebugger-recorder_31bf3856ad364e35_10.0.17763.719_none_d9510f729380a075\tttracer.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-t..eldebugger-recorder_31bf3856ad364e35_10.0.17763.719_none_e3a5b9c4c7e16270\tttracer.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-t..eldebugger-recorder_31bf3856ad364e35_10.0.17763.719_none_d9510f729380a075\f\tttracer.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-t..eldebugger-recorder_31bf3856ad364e35_10.0.17763.719_none_d9510f729380a075\r\tttracer.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-t..eldebugger-recorder_31bf3856ad364e35_10.0.17763.719_none_e3a5b9c4c7e16270\f\tttracer.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-t..eldebugger-recorder_31bf3856ad364e35_10.0.17763.719_none_e3a5b9c4c7e16270\r\tttracer.exe
Path: C:\Windows\System32\url.dll
Path: C:\Windows\SysWOW64\url.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-ie-winsockautodialstub_31bf3856ad364e35_11.0.17763.1_none_14e2360420cdaa63\url.dll
Path: C:\Windows\WinSxS\x86_microsoft-windows-ie-winsockautodialstub_31bf3856ad364e35_11.0.17763.1_none_b8c39a806870392d\url.dll
Path: C:\Windows\WinSxS\amd64_netfx4-vbc_exe_b03f5f7f11d50a3a_4.0.15713.0_none_950557bc0844e513\vbc.exe
Path: C:\Windows\WinSxS\x86_netfx4-vbc_exe_b03f5f7f11d50a3a_4.0.15713.0_none_dcb28e931cc10e19\vbc.exe
Path: C:\Windows\Microsoft.NET\Framework\v4.0.30319\vbc.exe
Path: C:\Windows\Microsoft.NET\Framework64\v4.0.30319\vbc.exe
Path: C:\Windows\System32\verclsid.exe
Path: C:\Windows\SysWOW64\verclsid.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-verclsid_31bf3856ad364e35_10.0.17763.1_none_acacbb1b6b9db81c\verclsid.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-verclsid_31bf3856ad364e35_10.0.17763.1_none_b701656d9ffe7a17\verclsid.exe
Path: C:\Program Files\Windows Mail\wab.exe
Path: C:\Program Files (x86)\Windows Mail\wab.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-wab-app_31bf3856ad364e35_10.0.17763.1_none_336f47662fbc0a5e\wab.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-wab-app_31bf3856ad364e35_10.0.17763.1_none_3dc3f1b8641ccc59\wab.exe
Path: C:\Windows\System32\winrm.vbs
Path: C:\Windows\SysWOW64\winrm.vbs
Path: C:\Windows\WinSxS\amd64_microsoft-windows-w..for-management-core_31bf3856ad364e35_10.0.17763.1075_none_181978ce7eb989af\winrm.vbs
Path: C:\Windows\WinSxS\wow64_microsoft-windows-w..for-management-core_31bf3856ad364e35_10.0.17763.1075_none_226e2320b31a4baa\winrm.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-w..for-management-core_31bf3856ad364e35_10.0.17763.1075_none_181978ce7eb989af\f\winrm.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-w..for-management-core_31bf3856ad364e35_10.0.17763.1075_none_181978ce7eb989af\r\winrm.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-w..for-management-core_31bf3856ad364e35_10.0.17763.1075_none_226e2320b31a4baa\f\winrm.vbs
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-w..for-management-core_31bf3856ad364e35_10.0.17763.1075_none_226e2320b31a4baa\r\winrm.vbs
Path: C:\Windows\System32\wbem\WMIC.exe
Path: C:\Windows\SysWOW64\wbem\WMIC.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-w..ommand-line-utility_31bf3856ad364e35_10.0.17763.1_none_926fbf4425005e17\WMIC.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-w..ommand-line-utility_31bf3856ad364e35_10.0.17763.1_none_9cc4699659612012\WMIC.exe
Path: C:\Windows\System32\wscript.exe
Path: C:\Windows\SysWOW64\wscript.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-scripting_31bf3856ad364e35_10.0.17763.1217_none_96008760d22181ef\wscript.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-scripting_31bf3856ad364e35_10.0.17763.1217_none_a05531b3068243ea\wscript.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-scripting_31bf3856ad364e35_10.0.17763.1217_none_96008760d22181ef\f\wscript.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-scripting_31bf3856ad364e35_10.0.17763.1217_none_96008760d22181ef\r\wscript.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-scripting_31bf3856ad364e35_10.0.17763.1217_none_a05531b3068243ea\f\wscript.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-scripting_31bf3856ad364e35_10.0.17763.1217_none_a05531b3068243ea\r\wscript.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-lxss-wsl_31bf3856ad364e35_10.0.17763.1075_none_d0a287cca091dc3f\wsl.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-lxss-wsl_31bf3856ad364e35_10.0.17763.1_none_73b46e4327098ae1\wsl.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-lxss-wsl_31bf3856ad364e35_10.0.17763.1075_none_d0a287cca091dc3f\f\wsl.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-lxss-wsl_31bf3856ad364e35_10.0.17763.1075_none_d0a287cca091dc3f\r\wsl.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-lxss-wsl_31bf3856ad364e35_10.0.17763.1075_none_d0a287cca091dc3f\f\wsl.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-lxss-wsl_31bf3856ad364e35_10.0.17763.1075_none_d0a287cca091dc3f\r\wsl.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-s..e-client-ui-wsreset_31bf3856ad364e35_10.0.17763.592_none_3b077a2c8bd734e1\f\wsreset.exe
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-s..e-client-ui-wsreset_31bf3856ad364e35_10.0.17763.592_none_3b077a2c8bd734e1\r\wsreset.exe
Path: C:\Windows\System32\WSReset.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-s..e-client-ui-wsreset_31bf3856ad364e35_10.0.17763.592_none_3b077a2c8bd734e1\WSReset.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-s..e-client-ui-wsreset_31bf3856ad364e35_10.0.17763.592_none_3b077a2c8bd734e1\f\WSReset.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-s..e-client-ui-wsreset_31bf3856ad364e35_10.0.17763.592_none_3b077a2c8bd734e1\r\WSReset.exe
Path: C:\Windows\System32\xwizard.exe
Path: C:\Windows\SysWOW64\xwizard.exe
Path: C:\Windows\WinSxS\amd64_microsoft-windows-xwizard-host-process_31bf3856ad364e35_10.0.17763.1_none_49b9fab890ad567c\xwizard.exe
Path: C:\Windows\WinSxS\wow64_microsoft-windows-xwizard-host-process_31bf3856ad364e35_10.0.17763.1_none_540ea50ac50e1877\xwizard.exe
Path: C:\Windows\System32\zipfldr.dll
Path: C:\Windows\SysWOW64\zipfldr.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-zipfldr_31bf3856ad364e35_10.0.17763.1075_none_c5ba4dab06fa43f5\zipfldr.dll
Path: C:\Windows\WinSxS\wow64_microsoft-windows-zipfldr_31bf3856ad364e35_10.0.17763.1075_none_d00ef7fd3b5b05f0\zipfldr.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-zipfldr_31bf3856ad364e35_10.0.17763.1075_none_c5ba4dab06fa43f5\f\zipfldr.dll
Path: C:\Windows\WinSxS\amd64_microsoft-windows-zipfldr_31bf3856ad364e35_10.0.17763.1075_none_c5ba4dab06fa43f5\r\zipfldr.dll
Path: C:\Windows\WinSxS\wow64_microsoft-windows-zipfldr_31bf3856ad364e35_10.0.17763.1075_none_d00ef7fd3b5b05f0\f\zipfldr.dll
Path: C:\Windows\WinSxS\wow64_microsoft-windows-zipfldr_31bf3856ad364e35_10.0.17763.1075_none_d00ef7fd3b5b05f0\r\zipfldr.dll
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-zipfldr_31bf3856ad364e35_10.0.17763.1075_none_c5ba4dab06fa43f5\f\zipfldr.dll
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\amd64_microsoft-windows-zipfldr_31bf3856ad364e35_10.0.17763.1075_none_c5ba4dab06fa43f5\r\zipfldr.dll
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-zipfldr_31bf3856ad364e35_10.0.17763.1075_none_d00ef7fd3b5b05f0\f\zipfldr.dll
Path: C:\Windows\servicing\LCU\Package_for_RollupFix~31bf3856ad364e35~amd64~~17763.1282.1.9\wow64_microsoft-windows-zipfldr_31bf3856ad364e35_10.0.17763.1075_none_d00ef7fd3b5b05f0\r\zipfldr.dll
Found: 633 LOLBAS

To see how to use the LOLBAS that were found go to https://lolbas-project.github.io/
====== IdleTime ======

  CurrentUser : THROWBACK\PetersJ
  Idletime    : 00h:07m:33s:234ms (453234 milliseconds)

====== IEFavorites ======

Favorites (petersj):

  http://go.microsoft.com/fwlink/p/?LinkId=255142

====== IETabs ======

ERROR:   [!] Terminating exception running command 'IETabs': System.Reflection.TargetInvocationException: Exception has been thrown by the target of an invocation. ---> System.Runtime.InteropServices.COMException: The server process could not be started because the configured identity is incorrect. Check the username and password. (Exception from HRESULT: 0x8000401A)
   --- End of inner exception stack trace ---
   at System.RuntimeType.InvokeDispMethod(String name, BindingFlags invokeAttr, Object target, Object[] args, Boolean[] byrefModifiers, Int32 culture, String[] namedParameters)
   at System.RuntimeType.InvokeMember(String name, BindingFlags bindingFlags, Binder binder, Object target, Object[] providedArgs, ParameterModifier[] modifiers, CultureInfo culture, String[] namedParams)
   at S34tb3lt.Commands.Browser.InternetExplorerTabCommand.<Execute>d__9.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== IEUrls ======

Internet Explorer typed URLs for the last 90 days


  S-1-5-21-3906589501-690843102-3982269896-1202

====== InstalledProducts ======

  DisplayName                    : Microsoft Visual C++ 2015-2019 Redistributable (x64) - 14.24.28127
  DisplayVersion                 : 14.24.28127.4
  Publisher                      : Microsoft Corporation
  InstallDate                    : 1/1/0001 12:00:00 AM
  Architecture                   : x86

  DisplayName                    : Amazon SSM Agent
  DisplayVersion                 : 2.3.842.0
  Publisher                      : Amazon Web Services
  InstallDate                    : 1/1/0001 12:00:00 AM
  Architecture                   : x86

  DisplayName                    : AWS Tools for Windows
  DisplayVersion                 : 3.15.1034
  Publisher                      : Amazon Web Services Developer Relations
  InstallDate                    : 6/10/2020 12:00:00 AM
  Architecture                   : x86

  DisplayName                    : Python Launcher
  DisplayVersion                 : 3.8.7140.0
  Publisher                      : Python Software Foundation
  InstallDate                    : 7/31/2020 12:00:00 AM
  Architecture                   : x86

  DisplayName                    : Microsoft Visual C++ 2008 Redistributable - x86 9.0.21022
  DisplayVersion                 : 9.0.21022
  Publisher                      : Microsoft Corporation
  InstallDate                    : 6/25/2020 12:00:00 AM
  Architecture                   : x86



  DisplayName                    : aws-cfn-bootstrap
  DisplayVersion                 : 1.4.33
  Publisher                      : Amazon Web Services
  InstallDate                    : 6/10/2020 12:00:00 AM
  Architecture                   : x64

  DisplayName                    : Python 3.8.5 Executables (64-bit)
  DisplayVersion                 : 3.8.5150.0
  Publisher                      : Python Software Foundation
  InstallDate                    : 8/2/2020 12:00:00 AM
  Architecture                   : x64

  DisplayName                    : Microsoft Web Platform Installer 5.1
  DisplayVersion                 : 5.1.51515.0
  Publisher                      : Microsoft Corporation
  InstallDate                    : 6/25/2020 12:00:00 AM
  Architecture                   : x64

  DisplayName                    : Python 3.8.5 Test Suite (64-bit)
  DisplayVersion                 : 3.8.5150.0
  Publisher                      : Python Software Foundation
  InstallDate                    : 8/2/2020 12:00:00 AM
  Architecture                   : x64

  DisplayName                    : Python 3.8.5 Documentation (64-bit)
  DisplayVersion                 : 3.8.5150.0
  Publisher                      : Python Software Foundation
  InstallDate                    : 8/2/2020 12:00:00 AM
  Architecture                   : x64

  DisplayName                    : Microsoft Visual C++ 2019 X64 Minimum Runtime - 14.24.28127
  DisplayVersion                 : 14.24.28127
  Publisher                      : Microsoft Corporation
  InstallDate                    : 6/25/2020 12:00:00 AM
  Architecture                   : x64

  DisplayName                    : Microsoft Visual C++ 2019 X64 Additional Runtime - 14.24.28127
  DisplayVersion                 : 14.24.28127
  Publisher                      : Microsoft Corporation
  InstallDate                    : 6/25/2020 12:00:00 AM
  Architecture                   : x64

  DisplayName                    : Amazon SSM Agent
  DisplayVersion                 : 2.3.842.0
  Publisher                      : Amazon Web Services
  InstallDate                    : 4/15/2020 12:00:00 AM
  Architecture                   : x64

  DisplayName                    : IIS URL Rewrite Module 2
  DisplayVersion                 : 7.2.1993
  Publisher                      : Microsoft Corporation
  InstallDate                    : 6/25/2020 12:00:00 AM
  Architecture                   : x64

  DisplayName                    : Python 3.8.5 Standard Library (64-bit)
  DisplayVersion                 : 3.8.5150.0
  Publisher                      : Python Software Foundation
  InstallDate                    : 8/2/2020 12:00:00 AM
  Architecture                   : x64

  DisplayName                    : Python 3.8.5 pip Bootstrap (64-bit)
  DisplayVersion                 : 3.8.5150.0
  Publisher                      : Python Software Foundation
  InstallDate                    : 8/2/2020 12:00:00 AM
  Architecture                   : x64

  DisplayName                    : Python 3.8.5 Tcl/Tk Support (64-bit)
  DisplayVersion                 : 3.8.5150.0
  Publisher                      : Python Software Foundation
  InstallDate                    : 8/2/2020 12:00:00 AM
  Architecture                   : x64

  DisplayName                    : Python 3.8.5 Utility Scripts (64-bit)
  DisplayVersion                 : 3.8.5150.0
  Publisher                      : Python Software Foundation
  InstallDate                    : 8/2/2020 12:00:00 AM
  Architecture                   : x64

  DisplayName                    : AWS PV Drivers
  DisplayVersion                 : 8.3.3
  Publisher                      : Amazon Web Services
  InstallDate                    : 3/18/2020 12:00:00 AM
  Architecture                   : x64

  DisplayName                    : Python 3.8.5 Core Interpreter (64-bit)
  DisplayVersion                 : 3.8.5150.0
  Publisher                      : Python Software Foundation
  InstallDate                    : 8/2/2020 12:00:00 AM
  Architecture                   : x64

  DisplayName                    : Python 3.8.5 Development Libraries (64-bit)
  DisplayVersion                 : 3.8.5150.0
  Publisher                      : Python Software Foundation
  InstallDate                    : 8/2/2020 12:00:00 AM
  Architecture                   : x64



====== InterestingFiles ======


Accessed      Modified      Path
----------    ----------    -----
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\getpass.py
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\keycert.passwd.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\ssl_key.passwd.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\test_getpass.py
2020-07-31    2020-07-31    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\__pycache__\getpass.cpython-38.pyc
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\test_json\test_pass1.py
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\test_json\test_pass2.py
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\test_json\test_pass3.py
2020-07-02    2020-07-02    C:\Users\All Users\ssh\ssh_host_rsa_key
2020-07-02    2020-07-02    C:\Users\All Users\ssh\ssh_host_rsa_key.pub
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\allsans.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\badcert.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\badkey.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\ffdh3072.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\idnsans.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\keycert.passwd.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\keycert.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\keycert2.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\keycert3.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\keycert4.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\keycertecc.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test
okia.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test
ullbytecert.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test
ullcert.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\pycacert.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\pycakey.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\secp384r1.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\selfsigned_pythontestdotnet.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\ssl_cert.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\ssl_key.passwd.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\ssl_key.pem
2020-07-31    2020-07-20    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\test\talos-2019-0758.pem
2020-07-31    2020-07-31    C:\Users\petersj\AppData\Local\Programs\Python\Python38\Lib\site-packages\pip\_vendor\certifi\cacert.pem
====== InterestingProcesses ======

ERROR:   [!] Terminating exception running command 'InterestingProcesses': System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObjectSearcher.Initialize()
   at System.Management.ManagementObjectSearcher.Get()
   at S34tb3lt.Commands.Windows.InterestingProcessesCommand.<Execute>d__10.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== InternetSettings ======

General Settings
  Hive                               Key : Value

  HKCU          DisableCachingOfSSLPages : 1
  HKCU                IE5_UA_Backup_Flag : 5.0
  HKCU                   PrivacyAdvanced : 1
  HKCU                   SecureProtocols : 2688
  HKCU                        User Agent : Mozilla/4.0 (compatible; MSIE 8.0; Win32)
  HKCU             CertificateRevocation : 1
  HKCU              ZonesSecurityUpgrade : System.Byte[]
  HKCU                WarnonZoneCrossing : 1
  HKCU                   EnableNegotiate : 1
  HKCU                      MigrateProxy : 1
  HKCU                       ProxyEnable : 0
  HKCU                      ActiveXCache : C:\Windows\Downloaded Program Files
  HKCU                CodeBaseSearchPath : CODEBASE
  HKCU                    EnablePunycode : 1
  HKCU                      MinorVersion : 0
  HKCU                    WarnOnIntranet : 1

URLs by Zone
  No URLs configured

Zone Auth Settings
====== LAPS ======

  LAPS Enabled                          : False
  LAPS Admin Account Name               : 
  LAPS Password Complexity              : 
  LAPS Password Length                  : 
  LAPS Expiration Protection Enabled    : 
====== LastShutdown ======

  LastShutdown                   : 4/16/2022 11:00:14 AM

====== LocalGPOs ======

====== LocalGroups ======

All Local Groups (and memberships)


  ** THROWBACK-PROD\Administrators ** (Administrators have complete and unrestricted access to the computer/domain)

  User            THROWBACK-PROD\Administrator             S-1-5-21-1142397155-17714838-1651365392-500
  Group           THROWBACK\Domain Admins                  S-1-5-21-3906589501-690843102-3982269896-512
  User            THROWBACK\WEBService                     S-1-5-21-3906589501-690843102-3982269896-1111
  User            THROWBACK-PROD\admin-petersj             S-1-5-21-1142397155-17714838-1651365392-1010
  User            THROWBACK\BlaireJ                        S-1-5-21-3906589501-690843102-3982269896-1116

  ** THROWBACK-PROD\Guests ** (Guests have the same access as members of the Users group by default, except for the Guest account which is further restricted)

  User            THROWBACK-PROD\Guest                     S-1-5-21-1142397155-17714838-1651365392-501

  ** THROWBACK-PROD\Remote Desktop Users ** (Members in this group are granted the right to logon remotely)

  User            THROWBACK\petersj                        S-1-5-21-3906589501-690843102-3982269896-1202
  User            THROWBACK\BlaireJ                        S-1-5-21-3906589501-690843102-3982269896-1116

  ** THROWBACK-PROD\System Managed Accounts Group ** (Members of this group are managed by the system.)

  User            THROWBACK-PROD\DefaultAccount            S-1-5-21-1142397155-17714838-1651365392-503

  ** THROWBACK-PROD\Users ** (Users are prevented from making accidental or intentional system-wide changes and can run most applications)

  WellKnownGroup  NT AUTHORITY\INTERACTIVE                 S-1-5-4
  WellKnownGroup  NT AUTHORITY\Authenticated Users         S-1-5-11
  Group           THROWBACK\Domain Users                   S-1-5-21-3906589501-690843102-3982269896-513
  User            THROWBACK\petersj                        S-1-5-21-3906589501-690843102-3982269896-1202
  User            THROWBACK\BlaireJ                        S-1-5-21-3906589501-690843102-3982269896-1116

====== LocalUsers ======

  ComputerName                   : localhost
  UserName                       : admin-petersj
  Enabled                        : True
  Rid                            : 1010
  UserType                       : Administrator
  Comment                        : Local admin account for Jon
  PwdLastSet                     : 8/27/2020 4:15:44 AM
  LastLogon                      : 4/14/2022 10:44:22 PM
  NumLogins                      : 103

  ComputerName                   : localhost
  UserName                       : Administrator
  Enabled                        : True
  Rid                            : 500
  UserType                       : Administrator
  Comment                        : Built-in account for administering the computer/domain
  PwdLastSet                     : 8/13/2020 3:15:30 AM
  LastLogon                      : 4/16/2022 11:25:11 AM
  NumLogins                      : 2327

  ComputerName                   : localhost
  UserName                       : DefaultAccount
  Enabled                        : False
  Rid                            : 503
  UserType                       : Guest
  Comment                        : A user account managed by the system.
  PwdLastSet                     : 1/1/1970 12:00:00 AM
  LastLogon                      : 1/1/1970 12:00:00 AM
  NumLogins                      : 0

  ComputerName                   : localhost
  UserName                       : Guest
  Enabled                        : False
  Rid                            : 501
  UserType                       : Guest
  Comment                        : Built-in account for guest access to the computer/domain
  PwdLastSet                     : 1/1/1970 12:00:00 AM
  LastLogon                      : 1/1/1970 12:00:00 AM
  NumLogins                      : 0

  ComputerName                   : localhost
  UserName                       : sshd
  Enabled                        : True
  Rid                            : 1009
  UserType                       : Guest
  Comment                        : 
  PwdLastSet                     : 7/2/2020 1:15:02 AM
  LastLogon                      : 1/1/1970 12:00:00 AM
  NumLogins                      : 0

  ComputerName                   : localhost
  UserName                       : WDAGUtilityAccount
  Enabled                        : False
  Rid                            : 504
  UserType                       : Guest
  Comment                        : A user account managed and used by the system for Windows Defender Application Guard scenarios.
  PwdLastSet                     : 11/15/2018 12:04:12 AM
  LastLogon                      : 1/1/1970 12:00:00 AM
  NumLogins                      : 0

====== LogonEvents ======

ERROR: Unable to collect. Must be an administrator/in a high integrity context.
====== LogonSessions ======

Logon Sessions (via WMI)

ERROR:   [!] Terminating exception running command 'LogonSessions': System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObjectSearcher.Initialize()
   at System.Management.ManagementObjectSearcher.Get()
   at S34tb3lt.Commands.Windows.LogonSessionsCommand.<Execute>d__10.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== LSASettings ======

  auditbasedirectories           : 0
  auditbaseobjects               : 0
  Bounds                         : 00-30-00-00-00-20-00-00
  crashonauditfail               : 0
  fullprivilegeauditing          : 00
  LimitBlankPasswordUse          : 1
  NoLmHash                       : 1
  Security Packages              : ""
  Notification Packages          : rassfm,scecli
  Authentication Packages        : msv1_0
  LsaPid                         : 800
  LsaCfgFlagsDefault             : 0
  SecureBoot                     : 1
  ProductType                    : 8
  disabledomaincreds             : 0
  everyoneincludesanonymous      : 0
  forceguest                     : 0
  restrictanonymous              : 0
  restrictanonymoussam           : 1
====== MappedDrives ======

ERROR:   [!] Terminating exception running command 'MappedDrives': System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObjectSearcher.Initialize()
   at System.Management.ManagementObjectSearcher.Get()
   at S34tb3lt.Commands.Windows.MappedDrivesCommand.<Execute>d__10.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== McAfeeConfigs ======

====== McAfeeSiteList ======

====== MicrosoftUpdates ======

Enumerating *all* Microsoft updates

ERROR:   [!] Terminating exception running command 'MicrosoftUpdates': System.UnauthorizedAccessException: Creating an instance of the COM component with CLSID {B699E5E8-67FF-4177-88B0-3684A3388BFB} from the IClassFactory failed due to the following error: 80070005 Access is denied. (Exception from HRESULT: 0x80070005 (E_ACCESSDENIED)).
   at System.RuntimeTypeHandle.CreateInstance(RuntimeType type, Boolean publicOnly, Boolean noCheck, Boolean& canBeCached, RuntimeMethodHandleInternal& ctor, Boolean& bNeedSecurityCheck)
   at System.RuntimeType.CreateInstanceSlow(Boolean publicOnly, Boolean skipCheckThis, Boolean fillCache, StackCrawlMark& stackMark)
   at System.Activator.CreateInstance(Type type, Boolean nonPublic)
   at System.Activator.CreateInstance(Type type)
   at S34tb3lt.Commands.Windows.MicrosoftUpdateCommand.<Execute>d__9.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== NamedPipes ======

  \\.\pipe\atsvc
  \\.\pipe\Ctx_WinStation_API_service
  \\.\pipe\epmapper
  \\.\pipe\eventlog
    SDDL         : O:LSG:LSD:P(A;;0x12019b;;;WD)(A;;CC;;;OW)(A;;0x12008f;;;S-1-5-80-880578595-1860270145-482643319-2788375705-1540778122)
  \\.\pipe\InitShutdown
  \\.\pipe\lsass
  \\.\pipe\LSM_API_service
  \\.\pipe
etdfs
  \\.\pipe
tsvcs
  \\.\pipe\PIPE_EVENTROOT\CIMV2SCM EVENT PROVIDER
  \\.\pipe\PSHost.132945816575434739.3152.DefaultAppDomain.powershell
  \\.\pipe\PSHost.132945818014155225.664.DefaultAppDomain.powershell
  \\.\pipe\ROUTER
    SDDL         : O:SYG:SYD:P(A;;0x12019b;;;WD)(A;;0x12019b;;;AN)(A;;FA;;;SY)
  \\.\pipe\scerpc
  \\.\pipe\SessEnvPublicRpc
  \\.\pipe\spoolss
  \\.\pipe\srvsvc
  \\.\pipe\TermSrv_API_service
  \\.\pipe\trkwks
  \\.\pipe\W32PosixPipe.000004e8.00000000
  \\.\pipe\W32PosixPipe.000004e8.00000002
  \\.\pipe\W32PosixPipe.000013b0.00000000
  \\.\pipe\W32PosixPipe.000013b0.00000001
  \\.\pipe\W32PosixPipe.000013b0.00000003
  \\.\pipe\W32PosixPipe.000013b0.00000004
  \\.\pipe\W32PosixPipe.000013b0.00000005
  \\.\pipe\W32PosixPipe.000013b0.00000006
  \\.\pipe\W32PosixPipe.000013b0.00000007
  \\.\pipe\W32TIME_ALT
  \\.\pipe\winreg
  \\.\pipe\Winsock2\CatalogChangeListener-280-0
  \\.\pipe\Winsock2\CatalogChangeListener-30c-0
  \\.\pipe\Winsock2\CatalogChangeListener-320-0
  \\.\pipe\Winsock2\CatalogChangeListener-44-0
  \\.\pipe\Winsock2\CatalogChangeListener-5c0-0
  \\.\pipe\Winsock2\CatalogChangeListener-748-0
  \\.\pipe\Winsock2\CatalogChangeListener-990-0
  \\.\pipe\Winsock2\CatalogChangeListener-a2c-0
  \\.\pipe\wkssvc
====== NetworkProfiles ======

ERROR: Unable to collect. Must be an administrator.
====== NetworkShares ======

ERROR:   [!] Terminating exception running command 'NetworkShares': System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObjectSearcher.Initialize()
   at System.Management.ManagementObjectSearcher.Get()
   at S34tb3lt.Commands.Windows.NetworkSharesCommand.<Execute>d__10.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== NTLMSettings ======

  LanmanCompatibilityLevel    : (Send NTLMv2 response only - Win7+ default)

  NTLM Signing Settings
      ClientRequireSigning    : False
      ClientNegotiateSigning  : True
      ServerRequireSigning    : False
      ServerNegotiateSigning  : False
      LdapSigning             : 1 (Negotiate signing)

  Session Security
      NTLMMinClientSec        : 536870912 (Require128BitKey)
      NTLMMinServerSec        : 536870912 (Require128BitKey)


  NTLM Auditing and Restrictions
      InboundRestrictions     : (Not defined)
      OutboundRestrictions    : (Not defined)
      InboundAuditing         : (Not defined)
      OutboundExceptions      : 
====== OfficeMRUs ======

Enumerating Office most recently used files for the last 30 days

  App       User                     LastAccess    FileName
  ---       ----                     ----------    --------
====== OSInfo ======

ERROR:   [!] Terminating exception running command 'OSInfo': System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObjectSearcher.Initialize()
   at System.Management.ManagementObjectSearcher.Get()
   at S34tb3lt.Commands.Windows.OSInfoCommand.IsVirtualMachine()
   at S34tb3lt.Commands.Windows.OSInfoCommand.<Execute>d__9.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== OutlookDownloads ======

====== PoweredOnEvents ======

Collecting kernel boot (EID 12) and shutdown (EID 13) events from the last 30 days

Powered On Events (Time is local time)
ERROR:   [!] Terminating exception running command 'PoweredOnEvents': System.UnauthorizedAccessException: Attempted to perform an unauthorized operation.
   at System.Diagnostics.Eventing.Reader.EventLogException.Throw(Int32 errorCode)
   at System.Diagnostics.Eventing.Reader.NativeWrapper.EvtQuery(EventLogHandle session, String path, String query, Int32 flags)
   at System.Diagnostics.Eventing.Reader.EventLogReader..ctor(EventLogQuery eventQuery, EventBookmark bookmark)
   at S34tb3lt.Commands.Windows.EventLogs.PoweredOnEventsCommand.<Execute>d__9.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== PowerShell ======


  Installed CLR Versions
      4.0.30319

  Installed PowerShell Versions
      2.0
        [!] Version 2.0.50727 of the CLR is not installed - PowerShell v2.0 won't be able to run.
      5.1.17763.1

  Transcription Logging Settings
      Enabled            : False
      Invocation Logging : False
      Log Directory      : 

  Module Logging Settings
      Enabled             : False
      Logged Module Names :

  Script Block Logging Settings
      Enabled            : False
      Invocation Logging : False

  Anti-Malware Scan Interface (AMSI)
      OS Supports AMSI: True
        [!] You can do a PowerShell version downgrade to bypass AMSI.
====== PowerShellEvents ======

Searching script block logs (EID 4104) for sensitive data.

ERROR:   [!] Terminating exception running command 'PowerShellEvents': System.UnauthorizedAccessException: Attempted to perform an unauthorized operation.
   at System.Diagnostics.Eventing.Reader.EventLogException.Throw(Int32 errorCode)
   at System.Diagnostics.Eventing.Reader.NativeWrapper.EvtQuery(EventLogHandle session, String path, String query, Int32 flags)
   at System.Diagnostics.Eventing.Reader.EventLogReader..ctor(EventLogQuery eventQuery, EventBookmark bookmark)
   at S34tb3lt.Commands.Windows.EventLogs.PowerShellEventsCommand.<Execute>d__9.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== PowerShellHistory ======

====== Printers ======

ERROR:   [!] Terminating exception running command 'Printers': System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObjectSearcher.Initialize()
   at System.Management.ManagementObjectSearcher.Get()
   at S34tb3lt.Commands.Windows.PrintersCommand.<Execute>d__9.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== ProcessCreationEvents ======

ERROR: Unable to collect. Must be an administrator.
====== Processes ======

Collecting All Processes (via WMI)

ERROR:   [!] Terminating exception running command 'Processes': System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObjectSearcher.Initialize()
   at System.Management.ManagementObjectSearcher.Get()
   at S34tb3lt.Commands.Windows.ProcessesCommand.<Execute>d__9.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== ProcessOwners ======

ERROR:   [!] Terminating exception running command 'ProcessOwners': System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObjectSearcher.Initialize()
   at System.Management.ManagementObjectSearcher.Get()
   at S34tb3lt.Commands.Windows.ProcessesOwnerCommand.<Execute>d__10.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== PSSessionSettings ======

ERROR: Unable to collect. Must be an administrator.
====== PuttyHostKeys ======

====== PuttySessions ======

====== RDCManFiles ======

====== RDPSavedConnections ======

====== RDPSessions ======

  SessionID                      : 0
  SessionName                    : Services
  UserName                       : 
  DomainName                     : 
  State                          : Disconnected
  SourceIp                       : 

  SessionID                      : 1
  SessionName                    : Console
  UserName                       : 
  DomainName                     : 
  State                          : Connected
  SourceIp                       : 

====== RDPsettings ======

RDP Server Settings:
  NetworkLevelAuthentication: 
  BlockClipboardRedirection:  
  BlockComPortRedirection:    
  BlockDriveRedirection:      
  BlockLptPortRedirection:    
  BlockPnPDeviceRedirection:  
  BlockPrinterRedirection:    
  AllowSmartCardRedirection:  

RDP Client Settings:
  DisablePasswordSaving: True
  RestrictedRemoteAdministration: False
====== RecycleBin ======

Recycle Bin Files Within the last 30 Days

====== reg ======

HKLM\Software ! (default) : 
HKLM\Software\Amazon ! (default) : 
HKLM\Software\Classes ! (default) : 
HKLM\Software\Clients ! (default) : 
HKLM\Software\DefaultUserEnvironment ! (default) : 
HKLM\Software\Google ! (default) : 
HKLM\Software\Intel ! (default) : 
HKLM\Software\Microsoft ! (default) : 
HKLM\Software\ODBC ! (default) : 
HKLM\Software\OpenSSH ! (default) : 
HKLM\Software\Partner ! (default) : 
HKLM\Software\Policies ! (default) : 
HKLM\Software\RegisteredApplications ! (default) : 
HKLM\Software\Setup ! (default) : 
HKLM\Software\WOW6432Node ! (default) : 
====== RPCMappedEndpoints ======

 UUID: d95afe70-a6d5-4259-822e-2c84da1ddb0d
     ncacn_ip_tcp:[49664]
 UUID: 906b0ce0-c70b-1067-b317-00dd010662da
     ncalrpc:[LRPC-e89bc1bfd49550e069]
     ncalrpc:[LRPC-e89bc1bfd49550e069]
     ncalrpc:[LRPC-e89bc1bfd49550e069]
 UUID: b18fbab6-56f8-4702-84e0-41053293a869 (UserMgrCli)
     ncalrpc:[OLEB45F09322D907419ECAE3E5815E9]
     ncalrpc:[LRPC-a912c02fa09d013899]
 UUID: 0d3c7f20-1c8d-4654-a1b3-51563b298bda (UserMgrCli)
     ncalrpc:[OLEB45F09322D907419ECAE3E5815E9]
     ncalrpc:[LRPC-a912c02fa09d013899]
 UUID: 367abb81-9844-35f1-ad32-98f038001003
     ncacn_ip_tcp:[49670]
 UUID: 4b112204-0e19-11d3-b42b-0000f81feb9f
     ncalrpc:[LRPC-952b8c8b40210693c2]
 UUID: 650a7e26-eab8-5533-ce43-9c1dfce11511 (Vpn APIs)
     ncacn_np:[\\PIPE\\ROUTER]
     ncalrpc:[RasmanLrpc]
     ncalrpc:[VpnikeRpc]
     ncalrpc:[LRPC-e2419d62b8cbab6005]
 UUID: 98716d03-89ac-44c7-bb8c-285824e51c4a (XactSrv service)
     ncalrpc:[LRPC-1af28318fd16ab3dd5]
 UUID: 1a0d010f-1c33-432c-b0f5-8cf4e8053099 (IdSegSrv service)
     ncalrpc:[LRPC-1af28318fd16ab3dd5]
 UUID: b58aa02e-2884-4e97-8176-4ee06d794184
     ncalrpc:[LRPC-284a4f83196ebd20d0]
 UUID: 338cd001-2244-31f1-aaaa-900038001003 (RemoteRegistry Interface)
     ncacn_np:[\\PIPE\\winreg]
 UUID: da5a86c5-12c2-4943-ab30-7f74a813d853 (RemoteRegistry Perflib Interface)
     ncacn_np:[\\PIPE\\winreg]
 UUID: 552d076a-cb29-4e44-8b6a-d15e59e2c0af (IP Transition Configuration endpoint)
     ncalrpc:[LRPC-ea77975063fb443e57]
 UUID: 2e6035b2-e8f1-41a7-a044-656b439c4c34 (Proxy Manager provider server endpoint)
     ncalrpc:[LRPC-ea77975063fb443e57]
     ncalrpc:[TeredoDiagnostics]
     ncalrpc:[TeredoControl]
 UUID: c36be077-e14b-4fe9-8abc-e856ef4f048b (Proxy Manager client server endpoint)
     ncalrpc:[LRPC-ea77975063fb443e57]
     ncalrpc:[TeredoDiagnostics]
     ncalrpc:[TeredoControl]
 UUID: c49a5a70-8a7f-4e70-ba16-1e8f1f193ef1 (Adh APIs)
     ncalrpc:[LRPC-ea77975063fb443e57]
     ncalrpc:[TeredoDiagnostics]
     ncalrpc:[TeredoControl]
     ncalrpc:[OLE39FFE30631A510CB17A3BD3C32EB]
 UUID: 12345678-1234-abcd-ef00-0123456789ab
     ncalrpc:[LRPC-dda4d2740fce4c1de9]
     ncacn_ip_tcp:[49669]
 UUID: 0b6edbfa-4a24-4fc6-8a23-942b1eca65d1
     ncalrpc:[LRPC-dda4d2740fce4c1de9]
     ncacn_ip_tcp:[49669]
 UUID: ae33069b-a2a8-46ee-a235-ddfd339be281
     ncalrpc:[LRPC-dda4d2740fce4c1de9]
     ncacn_ip_tcp:[49669]
 UUID: 4a452661-8290-4b36-8fbe-7f4093a94978
     ncalrpc:[LRPC-dda4d2740fce4c1de9]
     ncacn_ip_tcp:[49669]
 UUID: 76f03f96-cdfd-44fc-a22c-64950a001209
     ncalrpc:[LRPC-dda4d2740fce4c1de9]
     ncacn_ip_tcp:[49669]
 UUID: abfb6ca3-0c5e-4734-9285-0aee72fe8d1c
     ncalrpc:[OLE77F0D1D2D66B901665B12DE8FDD6]
     ncalrpc:[LRPC-203c83db8fb49eece2]
 UUID: b37f900a-eae4-4304-a2ab-12bb668c0188
     ncalrpc:[OLE77F0D1D2D66B901665B12DE8FDD6]
     ncalrpc:[LRPC-203c83db8fb49eece2]
 UUID: e7f76134-9ef5-4949-a2d6-3368cc0988f3
     ncalrpc:[OLE77F0D1D2D66B901665B12DE8FDD6]
     ncalrpc:[LRPC-203c83db8fb49eece2]
 UUID: 7aeb6705-3ae6-471a-882d-f39c109edc12
     ncalrpc:[OLE77F0D1D2D66B901665B12DE8FDD6]
     ncalrpc:[LRPC-203c83db8fb49eece2]
 UUID: f44e62af-dab1-44c2-8013-049a9de417d6
     ncalrpc:[OLE77F0D1D2D66B901665B12DE8FDD6]
     ncalrpc:[LRPC-203c83db8fb49eece2]
 UUID: c2d1b5dd-fa81-4460-9dd6-e7658b85454b
     ncalrpc:[OLE77F0D1D2D66B901665B12DE8FDD6]
     ncalrpc:[LRPC-203c83db8fb49eece2]
 UUID: c9ac6db5-82b7-4e55-ae8a-e464ed7b4277 (Impl friendly name)
     ncalrpc:[LRPC-51c9f168dc8d3df680]
 UUID: 29770a8f-829b-4158-90a2-78cd488501f7
     ncalrpc:[LRPC-51c9f168dc8d3df680]
     ncalrpc:[SessEnvPrivateRpc]
     ncacn_np:[\\pipe\\SessEnvPublicRpc]
     ncacn_ip_tcp:[49668]
 UUID: 0b1c2170-5732-4e0e-8cd3-d9b16f3b84d7 (RemoteAccessCheck)
     ncacn_np:[\\pipe\\lsass]
     ncalrpc:[audit]
     ncalrpc:[securityevent]
     ncalrpc:[LSARPC_ENDPOINT]
     ncalrpc:[lsacap]
     ncalrpc:[LSA_IDPEXT_ENDPOINT]
     ncalrpc:[LSA_EAS_ENDPOINT]
     ncalrpc:[lsapolicylookup]
     ncalrpc:[lsasspirpc]
     ncalrpc:[protected_storage]
     ncalrpc:[SidKey Local End Point]
     ncalrpc:[samss lpc]
     ncalrpc:[NETLOGON_LRPC]
     ncacn_ip_tcp:[49667]
     ncacn_np:[\\pipe\\lsass]
     ncalrpc:[audit]
     ncalrpc:[securityevent]
     ncalrpc:[LSARPC_ENDPOINT]
     ncalrpc:[lsacap]
     ncalrpc:[LSA_IDPEXT_ENDPOINT]
     ncalrpc:[LSA_EAS_ENDPOINT]
     ncalrpc:[lsapolicylookup]
     ncalrpc:[lsasspirpc]
     ncalrpc:[protected_storage]
     ncalrpc:[SidKey Local End Point]
     ncalrpc:[samss lpc]
     ncalrpc:[NETLOGON_LRPC]
     ncacn_ip_tcp:[49667]
 UUID: b25a52bf-e5dd-4f4a-aea6-8ca7272a0e86 (KeyIso)
     ncacn_np:[\\pipe\\lsass]
     ncalrpc:[audit]
     ncalrpc:[securityevent]
     ncalrpc:[LSARPC_ENDPOINT]
     ncalrpc:[lsacap]
     ncalrpc:[LSA_IDPEXT_ENDPOINT]
     ncalrpc:[LSA_EAS_ENDPOINT]
     ncalrpc:[lsapolicylookup]
     ncalrpc:[lsasspirpc]
     ncalrpc:[protected_storage]
     ncalrpc:[SidKey Local End Point]
     ncalrpc:[samss lpc]
     ncalrpc:[NETLOGON_LRPC]
     ncacn_ip_tcp:[49667]
 UUID: 8fb74744-b2ff-4c00-be0d-9ef9a191fe1b (Ngc Pop Key Service)
     ncacn_np:[\\pipe\\lsass]
     ncalrpc:[audit]
     ncalrpc:[securityevent]
     ncalrpc:[LSARPC_ENDPOINT]
     ncalrpc:[lsacap]
     ncalrpc:[LSA_IDPEXT_ENDPOINT]
     ncalrpc:[LSA_EAS_ENDPOINT]
     ncalrpc:[lsapolicylookup]
     ncalrpc:[lsasspirpc]
     ncalrpc:[protected_storage]
     ncalrpc:[SidKey Local End Point]
     ncalrpc:[samss lpc]
     ncalrpc:[NETLOGON_LRPC]
     ncacn_ip_tcp:[49667]
 UUID: 51a227ae-825b-41f2-b4a9-1ac9557a1018 (Ngc Pop Key Service)
     ncacn_np:[\\pipe\\lsass]
     ncalrpc:[audit]
     ncalrpc:[securityevent]
     ncalrpc:[LSARPC_ENDPOINT]
     ncalrpc:[lsacap]
     ncalrpc:[LSA_IDPEXT_ENDPOINT]
     ncalrpc:[LSA_EAS_ENDPOINT]
     ncalrpc:[lsapolicylookup]
     ncalrpc:[lsasspirpc]
     ncalrpc:[protected_storage]
     ncalrpc:[SidKey Local End Point]
     ncalrpc:[samss lpc]
     ncalrpc:[NETLOGON_LRPC]
     ncacn_ip_tcp:[49667]
 UUID: 12345778-1234-abcd-ef00-0123456789ac
     ncacn_np:[\\pipe\\lsass]
     ncalrpc:[audit]
     ncalrpc:[securityevent]
     ncalrpc:[LSARPC_ENDPOINT]
     ncalrpc:[lsacap]
     ncalrpc:[LSA_IDPEXT_ENDPOINT]
     ncalrpc:[LSA_EAS_ENDPOINT]
     ncalrpc:[lsapolicylookup]
     ncalrpc:[lsasspirpc]
     ncalrpc:[protected_storage]
     ncalrpc:[SidKey Local End Point]
     ncalrpc:[samss lpc]
     ncalrpc:[NETLOGON_LRPC]
     ncacn_ip_tcp:[49667]
     ncacn_ip_tcp:[49673]
 UUID: 3473dd4d-2e88-4006-9cba-22570909dd10 (WinHttp Auto-Proxy Service)
     ncalrpc:[LRPC-22abc403cb46573265]
     ncalrpc:[52655f27-21fa-49f7-b63d-9ba32fa23936]
 UUID: f2c9b409-c1c9-4100-8639-d8ab1486694a (Witness Client Upcall Server)
     ncalrpc:[LRPC-25a07c10317f58cf37]
 UUID: eb081a0d-10ee-478a-a1dd-50995283e7a8 (Witness Client Test Interface)
     ncalrpc:[LRPC-25a07c10317f58cf37]
 UUID: 7f1343fe-50a9-4927-a778-0c5859517bac (DfsDs service)
     ncalrpc:[LRPC-25a07c10317f58cf37]
     ncacn_np:[\\PIPE\\wkssvc]
 UUID: dd490425-5325-4565-b774-7e27d6c09c24 (Base Firewall Engine API)
     ncalrpc:[LRPC-3738c30cefcece7c6e]
 UUID: 7f9d11bf-7fb9-436b-a812-b2d50c5d4c03 (Fw APIs)
     ncalrpc:[LRPC-3738c30cefcece7c6e]
     ncalrpc:[LRPC-e0d39df9f6f0058528]
 UUID: f47433c3-3e9d-4157-aad4-83aa1f5c2d4c (Fw APIs)
     ncalrpc:[LRPC-3738c30cefcece7c6e]
     ncalrpc:[LRPC-e0d39df9f6f0058528]
     ncalrpc:[LRPC-7a3fe8415b90d93f37]
 UUID: 2fb92682-6599-42dc-ae13-bd2ca89bd11c (Fw APIs)
     ncalrpc:[LRPC-3738c30cefcece7c6e]
     ncalrpc:[LRPC-e0d39df9f6f0058528]
     ncalrpc:[LRPC-7a3fe8415b90d93f37]
     ncalrpc:[LRPC-2ca614317556464185]
 UUID: 30b044a5-a225-43f0-b3a4-e060df91f9c1
     ncalrpc:[LRPC-2e6cd4482fc70dee26]
 UUID: c9ac6db5-82b7-4e55-ae8a-e464ed7b4277 (Impl friendly name)
     ncalrpc:[senssvc]
     ncalrpc:[LRPC-076b80f353c3544e15]
 UUID: 0a74ef1c-41a4-4e06-83ae-dc74fb1cdd53
     ncalrpc:[LRPC-789943ae1d2ad89c76]
 UUID: 1ff70682-0a51-30e8-076d-740be8cee98b
     ncalrpc:[LRPC-789943ae1d2ad89c76]
     ncacn_np:[\\PIPE\\atsvc]
 UUID: 378e52b0-c0a9-11cf-822d-00aa0051e40f
     ncalrpc:[LRPC-789943ae1d2ad89c76]
     ncacn_np:[\\PIPE\\atsvc]
 UUID: 33d84484-3626-47ee-8c6f-e7e98b113be1
     ncalrpc:[LRPC-789943ae1d2ad89c76]
     ncacn_np:[\\PIPE\\atsvc]
     ncalrpc:[ubpmtaskhostchannel]
     ncalrpc:[LRPC-8cae29868102b3dff2]
 UUID: 86d35949-83c9-4044-b424-db363231fd0c
     ncalrpc:[LRPC-789943ae1d2ad89c76]
     ncacn_np:[\\PIPE\\atsvc]
     ncalrpc:[ubpmtaskhostchannel]
     ncalrpc:[LRPC-8cae29868102b3dff2]
     ncacn_ip_tcp:[49666]
 UUID: 3a9ef155-691d-4449-8d05-09ad57031823
     ncalrpc:[LRPC-789943ae1d2ad89c76]
     ncacn_np:[\\PIPE\\atsvc]
     ncalrpc:[ubpmtaskhostchannel]
     ncalrpc:[LRPC-8cae29868102b3dff2]
     ncacn_ip_tcp:[49666]
 UUID: c9ac6db5-82b7-4e55-ae8a-e464ed7b4277 (Impl friendly name)
     ncalrpc:[IUserProfile2]
     ncalrpc:[LRPC-140672f0508c4deb27]
 UUID: 2eb08e3e-639f-4fba-97b1-14f878961076 (Group Policy RPC Interface)
     ncalrpc:[LRPC-cae3e9602471f5fc39]
 UUID: f6beaff7-1e19-4fbb-9f8f-b89e2018337c (Event log TCPIP)
     ncalrpc:[eventlog]
     ncacn_np:[\\pipe\\eventlog]
     ncacn_ip_tcp:[49665]
 UUID: 3c4728c5-f0ab-448b-bda1-6ce01eb0a6d6 (DHCPv6 Client LRPC Endpoint)
     ncalrpc:[dhcpcsvc6]
 UUID: 3c4728c5-f0ab-448b-bda1-6ce01eb0a6d5 (DHCP Client LRPC Endpoint)
     ncalrpc:[dhcpcsvc6]
     ncalrpc:[dhcpcsvc]
 UUID: df4df73a-c52d-4e3a-8003-8437fdf8302a (WM_WindowManagerRPC\Server)
     ncalrpc:[LRPC-2f3839cf345e0678fc]
 UUID: d09bdeb5-6171-4a34-bfe2-06fa82652568
     ncalrpc:[LRPC-6512391732ffe70e37]
 UUID: 5222821f-d5e2-4885-84f1-5f6185a0ec41 (Network Connection Broker server endpoint for NCB Reset module)
     ncalrpc:[LRPC-6512391732ffe70e37]
     ncalrpc:[LRPC-377544ba0901bcb835]
 UUID: 880fd55e-43b9-11e0-b1a8-cf4edfd72085 (KAPI Service endpoint)
     ncalrpc:[LRPC-6512391732ffe70e37]
     ncalrpc:[LRPC-377544ba0901bcb835]
     ncalrpc:[OLE48F0ADB58B1FB3F4F53BAA4FEE75]
     ncalrpc:[LRPC-1338df9bd5264eb40d]
 UUID: e40f7b57-7a25-4cd3-a135-7f7d3df9d16b (Network Connection Broker server endpoint)
     ncalrpc:[LRPC-6512391732ffe70e37]
     ncalrpc:[LRPC-377544ba0901bcb835]
     ncalrpc:[OLE48F0ADB58B1FB3F4F53BAA4FEE75]
     ncalrpc:[LRPC-1338df9bd5264eb40d]
 UUID: d09bdeb5-6171-4a34-bfe2-06fa82652568
     ncalrpc:[LRPC-3a912accc935feb78d]
 UUID: a500d4c6-0dd1-4543-bc0c-d5f93486eaf8
     ncalrpc:[LRPC-3a912accc935feb78d]
     ncalrpc:[LRPC-f9176ec88bce0a0398]
 UUID: 30adc50c-5cbc-46ce-9a0e-91914789e23c (NRP server endpoint)
     ncalrpc:[LRPC-882db96fddcf40e09f]
 UUID: 7ea70bcf-48af-4f6a-8968-6a440754d5fa (NSI server endpoint)
     ncalrpc:[LRPC-0ecd058590d0fdf6cc]
 UUID: f3f09ffd-fbcf-4291-944d-70ad6e0e73bb
     ncalrpc:[LRPC-0f45c79bd4f8091b4e]
 UUID: 76f226c3-ec14-4325-8a99-6a46348418af
     ncalrpc:[WMsgKRpc04E921]
 UUID: c9ac6db5-82b7-4e55-ae8a-e464ed7b4277 (Impl friendly name)
     ncalrpc:[LRPC-0a9240e46b5c416c84]
 UUID: 4bec6bb8-b5c2-4b6f-b2c1-5da5cf92d0d9
     ncalrpc:[umpo]
 UUID: 085b0334-e454-4d91-9b8c-4134f9e793f3
     ncalrpc:[umpo]
 UUID: 8782d3b9-ebbd-4644-a3d8-e8725381919b
     ncalrpc:[umpo]
 UUID: 3b338d89-6cfa-44b8-847e-531531bc9992
     ncalrpc:[umpo]
 UUID: bdaa0970-413b-4a3e-9e5d-f6dc9d7e0760
     ncalrpc:[umpo]
 UUID: 5824833b-3c1a-4ad2-bdfd-c31d19e23ed2
     ncalrpc:[umpo]
 UUID: 0361ae94-0316-4c6c-8ad8-c594375800e2
     ncalrpc:[umpo]
 UUID: 2d98a740-581d-41b9-aa0d-a88b9d5ce938
     ncalrpc:[umpo]
     ncalrpc:[actkernel]
     ncalrpc:[LRPC-74dbf4d700f5ce7c81]
 UUID: 8bfc3be1-6def-4e2d-af74-7c47cd0ade4a
     ncalrpc:[umpo]
     ncalrpc:[actkernel]
     ncalrpc:[LRPC-74dbf4d700f5ce7c81]
 UUID: 1b37ca91-76b1-4f5e-a3c7-2abfc61f2bb0
     ncalrpc:[umpo]
     ncalrpc:[actkernel]
     ncalrpc:[LRPC-74dbf4d700f5ce7c81]
 UUID: c605f9fb-f0a3-4e2a-a073-73560f8d9e3e
     ncalrpc:[umpo]
     ncalrpc:[actkernel]
     ncalrpc:[LRPC-74dbf4d700f5ce7c81]
 UUID: 0d3e2735-cea0-4ecc-a9e2-41a2d81aed4e
     ncalrpc:[umpo]
     ncalrpc:[actkernel]
     ncalrpc:[LRPC-74dbf4d700f5ce7c81]
 UUID: 2513bcbe-6cd4-4348-855e-7efb3c336dd3
     ncalrpc:[umpo]
     ncalrpc:[actkernel]
     ncalrpc:[LRPC-74dbf4d700f5ce7c81]
     ncalrpc:[OLEABDA6931BDC244E9D51872C543A8]
     ncalrpc:[LRPC-44bac7ec8ffb2d6cd6]
 UUID: 20c40295-8dba-48e6-aebf-3e78ef3bb144
     ncalrpc:[umpo]
     ncalrpc:[actkernel]
     ncalrpc:[LRPC-74dbf4d700f5ce7c81]
     ncalrpc:[OLEABDA6931BDC244E9D51872C543A8]
     ncalrpc:[LRPC-44bac7ec8ffb2d6cd6]
 UUID: b8cadbaf-e84b-46b9-84f2-6f71c03f9e55
     ncalrpc:[umpo]
     ncalrpc:[actkernel]
     ncalrpc:[LRPC-74dbf4d700f5ce7c81]
     ncalrpc:[OLEABDA6931BDC244E9D51872C543A8]
     ncalrpc:[LRPC-44bac7ec8ffb2d6cd6]
 UUID: 857fb1be-084f-4fb5-b59c-4b2c4be5f0cf
     ncalrpc:[umpo]
     ncalrpc:[actkernel]
     ncalrpc:[LRPC-74dbf4d700f5ce7c81]
     ncalrpc:[OLEABDA6931BDC244E9D51872C543A8]
     ncalrpc:[LRPC-44bac7ec8ffb2d6cd6]
 UUID: 55e6b932-1979-45d6-90c5-7f6270724112
     ncalrpc:[umpo]
     ncalrpc:[actkernel]
     ncalrpc:[LRPC-74dbf4d700f5ce7c81]
     ncalrpc:[OLEABDA6931BDC244E9D51872C543A8]
     ncalrpc:[LRPC-44bac7ec8ffb2d6cd6]
     ncalrpc:[LRPC-1315eaa1fd8c50c6ec]
 UUID: 76c217bc-c8b4-4201-a745-373ad9032b1a
     ncalrpc:[umpo]
     ncalrpc:[actkernel]
     ncalrpc:[LRPC-74dbf4d700f5ce7c81]
     ncalrpc:[OLEABDA6931BDC244E9D51872C543A8]
     ncalrpc:[LRPC-44bac7ec8ffb2d6cd6]
     ncalrpc:[LRPC-1315eaa1fd8c50c6ec]
 UUID: 88abcbc3-34ea-76ae-8215-767520655a23
     ncalrpc:[umpo]
     ncalrpc:[actkernel]
     ncalrpc:[LRPC-74dbf4d700f5ce7c81]
     ncalrpc:[OLEABDA6931BDC244E9D51872C543A8]
     ncalrpc:[LRPC-44bac7ec8ffb2d6cd6]
     ncalrpc:[LRPC-1315eaa1fd8c50c6ec]
 UUID: 2c7fd9ce-e706-4b40-b412-953107ef9bb0
     ncalrpc:[umpo]
 UUID: c521facf-09a9-42c5-b155-72388595cbf0
     ncalrpc:[umpo]
 UUID: 1832bcf6-cab8-41d4-85d2-c9410764f75a
     ncalrpc:[umpo]
 UUID: 4dace966-a243-4450-ae3f-9b7bcb5315b8
     ncalrpc:[umpo]
 UUID: 178d84be-9291-4994-82c6-3f909aca5a03
     ncalrpc:[umpo]
 UUID: e53d94ca-7464-4839-b044-09a2fb8b3ae5
     ncalrpc:[umpo]
 UUID: fae436b0-b864-4a87-9eda-298547cd82f2
     ncalrpc:[umpo]
 UUID: 082a3471-31b6-422a-b931-a54401960c62
     ncalrpc:[umpo]
 UUID: 6982a06e-5fe2-46b1-b39c-a2c545bfa069
     ncalrpc:[umpo]
 UUID: 0ff1f646-13bb-400a-ab50-9a78f2b7a85a
     ncalrpc:[umpo]
 UUID: 4ed8abcc-f1e2-438b-981f-bb0e8abc010c
     ncalrpc:[umpo]
 UUID: 95406f0b-b239-4318-91bb-cea3a46ff0dc
     ncalrpc:[umpo]
 UUID: 0d47017b-b33b-46ad-9e18-fe96456c5078
     ncalrpc:[umpo]
 UUID: dd59071b-3215-4c59-8481-972edadc0f6a
     ncalrpc:[umpo]
 UUID: d09bdeb5-6171-4a34-bfe2-06fa82652568
     ncalrpc:[umpo]
     ncalrpc:[actkernel]
     ncalrpc:[LRPC-74dbf4d700f5ce7c81]
     ncalrpc:[OLEABDA6931BDC244E9D51872C543A8]
     ncalrpc:[LRPC-44bac7ec8ffb2d6cd6]
     ncalrpc:[LRPC-1315eaa1fd8c50c6ec]
     ncalrpc:[LRPC-2e8d022a10e06befc5]
 UUID: 9b008953-f195-4bf9-bde0-4471971e58ed
     ncalrpc:[umpo]
     ncalrpc:[actkernel]
     ncalrpc:[LRPC-74dbf4d700f5ce7c81]
     ncalrpc:[OLEABDA6931BDC244E9D51872C543A8]
     ncalrpc:[LRPC-44bac7ec8ffb2d6cd6]
     ncalrpc:[LRPC-1315eaa1fd8c50c6ec]
     ncalrpc:[LRPC-2e8d022a10e06befc5]
     ncalrpc:[LRPC-874b9c72afb5b277d9]
 UUID: d09bdeb5-6171-4a34-bfe2-06fa82652568
     ncalrpc:[umpo]
     ncalrpc:[actkernel]
     ncalrpc:[LRPC-74dbf4d700f5ce7c81]
     ncalrpc:[OLEABDA6931BDC244E9D51872C543A8]
     ncalrpc:[LRPC-44bac7ec8ffb2d6cd6]
     ncalrpc:[LRPC-1315eaa1fd8c50c6ec]
     ncalrpc:[LRPC-2e8d022a10e06befc5]
     ncalrpc:[LRPC-874b9c72afb5b277d9]
 UUID: 697dcda9-3ba9-4eb2-9247-e11f1901b0d2
     ncalrpc:[umpo]
     ncalrpc:[actkernel]
     ncalrpc:[LRPC-74dbf4d700f5ce7c81]
     ncalrpc:[OLEABDA6931BDC244E9D51872C543A8]
     ncalrpc:[LRPC-44bac7ec8ffb2d6cd6]
     ncalrpc:[LRPC-1315eaa1fd8c50c6ec]
     ncalrpc:[LRPC-2e8d022a10e06befc5]
     ncalrpc:[LRPC-874b9c72afb5b277d9]
     ncalrpc:[LRPC-72b4bd998dc12c7cbe]
 UUID: d09bdeb5-6171-4a34-bfe2-06fa82652568
     ncalrpc:[umpo]
     ncalrpc:[actkernel]
     ncalrpc:[LRPC-74dbf4d700f5ce7c81]
     ncalrpc:[OLEABDA6931BDC244E9D51872C543A8]
     ncalrpc:[LRPC-44bac7ec8ffb2d6cd6]
     ncalrpc:[LRPC-1315eaa1fd8c50c6ec]
     ncalrpc:[LRPC-2e8d022a10e06befc5]
     ncalrpc:[LRPC-874b9c72afb5b277d9]
     ncalrpc:[LRPC-72b4bd998dc12c7cbe]
     ncalrpc:[csebpub]
 UUID: 76f226c3-ec14-4325-8a99-6a46348418af
     ncalrpc:[WMsgKRpc04CD70]
     ncacn_np:[\\PIPE\\InitShutdown]
     ncalrpc:[WindowsShutdown]
 UUID: d95afe70-a6d5-4259-822e-2c84da1ddb0d
     ncalrpc:[WMsgKRpc04CD70]
     ncacn_np:[\\PIPE\\InitShutdown]
     ncalrpc:[WindowsShutdown]
====== SCCM ======

  Server                         : 
  SiteCode                       : 
  ProductVersion                 : 
  LastSuccessfulInstallParams    : 

====== ScheduledTasks ======

All scheduled tasks (via WMI)

ERROR: System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObjectSearcher.Initialize()
   at System.Management.ManagementObjectSearcher.Get()
   at S34tb3lt.Commands.Windows.ScheduledTasksCommand.<Execute>d__10.MoveNext()
====== SearchIndex ======

ERROR: Unable to query the Search Indexer, Search Index is likely not running.
====== SecPackageCreds ======

  Version                        : NetNTLMv2
  Hash                           : PetersJ::THROWBACK:1122334455667788:5d1cdc589f8349959d7de28d6f2fd5fa:0101000000000000e440a5c58451d801e52806b7418866260000000008003000300000000000000000000000002000000f089e2e99db77e4f348b9aa183f4da2bd6c75667ae707d8d67800234b033a310a00100000000000000000000000000000000000090000000000000000000000

====== SecurityPackages ======

Security Packages


  Name                           : Negotiate
  Comment                        : Microsoft Package Negotiator
  Capabilities                   : INTEGRITY, PRIVACY, CONNECTION, MULTI_REQUIRED, EXTENDED_ERROR, IMPERSONATION, ACCEPT_WIN32_NAME, NEGOTIABLE, GSS_COMPATIBLE, LOGON, RESTRICTED_TOKENS, APPCONTAINER_CHECKS
  MaxToken                       : 48256
  RPCID                          : 9
  Version                        : 1

  Name                           : NegoExtender
  Comment                        : NegoExtender Security Package
  Capabilities                   : INTEGRITY, PRIVACY, CONNECTION, IMPERSONATION, NEGOTIABLE, GSS_COMPATIBLE, LOGON, MUTUAL_AUTH, NEGO_EXTENDER, APPCONTAINER_CHECKS
  MaxToken                       : 12000
  RPCID                          : 30
  Version                        : 1

  Name                           : Kerberos
  Comment                        : Microsoft Kerberos V1.0
  Capabilities                   : 42941375
  MaxToken                       : 48000
  RPCID                          : 16
  Version                        : 1

  Name                           : NTLM
  Comment                        : NTLM Security Package
  Capabilities                   : 42478391
  MaxToken                       : 2888
  RPCID                          : 10
  Version                        : 1

  Name                           : TSSSP
  Comment                        : TS Service Security Package
  Capabilities                   : CONNECTION, MULTI_REQUIRED, ACCEPT_WIN32_NAME, MUTUAL_AUTH, APPCONTAINER_CHECKS
  MaxToken                       : 13000
  RPCID                          : 22
  Version                        : 1

  Name                           : pku2u
  Comment                        : PKU2U Security Package
  Capabilities                   : INTEGRITY, PRIVACY, CONNECTION, IMPERSONATION, GSS_COMPATIBLE, MUTUAL_AUTH, NEGOTIABLE2, APPCONTAINER_CHECKS
  MaxToken                       : 12000
  RPCID                          : 31
  Version                        : 1

  Name                           : CloudAP
  Comment                        : Cloud AP Security Package
  Capabilities                   : LOGON, NEGOTIABLE2
  MaxToken                       : 0
  RPCID                          : 36
  Version                        : 1

  Name                           : WDigest
  Comment                        : Digest Authentication for Windows
  Capabilities                   : TOKEN_ONLY, IMPERSONATION, ACCEPT_WIN32_NAME, APPCONTAINER_CHECKS
  MaxToken                       : 4096
  RPCID                          : 21
  Version                        : 1

  Name                           : Schannel
  Comment                        : Schannel Security Package
  Capabilities                   : INTEGRITY, PRIVACY, CONNECTION, MULTI_REQUIRED, EXTENDED_ERROR, IMPERSONATION, ACCEPT_WIN32_NAME, STREAM, MUTUAL_AUTH, APPCONTAINER_PASSTHROUGH
  MaxToken                       : 24576
  RPCID                          : 14
  Version                        : 1

  Name                           : Microsoft Unified Security Protocol Provider
  Comment                        : Schannel Security Package
  Capabilities                   : INTEGRITY, PRIVACY, CONNECTION, MULTI_REQUIRED, EXTENDED_ERROR, IMPERSONATION, ACCEPT_WIN32_NAME, STREAM, MUTUAL_AUTH, APPCONTAINER_PASSTHROUGH
  MaxToken                       : 24576
  RPCID                          : 14
  Version                        : 1

  Name                           : Default TLS SSP
  Comment                        : Schannel Security Package
  Capabilities                   : INTEGRITY, PRIVACY, CONNECTION, MULTI_REQUIRED, EXTENDED_ERROR, IMPERSONATION, ACCEPT_WIN32_NAME, STREAM, MUTUAL_AUTH, APPCONTAINER_PASSTHROUGH
  MaxToken                       : 24576
  RPCID                          : 14
  Version                        : 1

  Name                           : PWDSSP
  Comment                        : Microsoft Clear Text Password Security Provider
  Capabilities                   : CONNECTION, ACCEPT_WIN32_NAME
  MaxToken                       : 768
  RPCID                          : -1
  Version                        : 1

====== Services ======

All Services (via WMI)

ERROR:   [!] Terminating exception running command 'Services': System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObjectSearcher.Initialize()
   at System.Management.ManagementObjectSearcher.Get()
   at S34tb3lt.Commands.Windows.ServicesCommand.<Execute>d__9.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== SlackDownloads ======

====== SlackPresence ======

====== SlackWorkspaces ======

====== SuperPutty ======

====== Sysmon ======

ERROR: Unable to collect. Must be an administrator.
====== SysmonEvents ======

ERROR: Unable to collect. Must be an administrator.
====== TcpConnections ======

  Local Address          Foreign Address        State      PID   Service         ProcessName
ERROR:   [!] Terminating exception running command 'TcpConnections': System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObjectSearcher.Initialize()
   at System.Management.ManagementObjectSearcher.Get()
   at S34tb3lt.Commands.Windows.TcpConnectionsCommand.<Execute>d__9.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== TokenGroups ======

Current Token's Groups

  THROWBACK\Domain Users                   S-1-5-21-3906589501-690843102-3982269896-513
  Everyone                                 S-1-1-0
  BUILTIN\Remote Desktop Users             S-1-5-32-555
  BUILTIN\Users                            S-1-5-32-545
  NT AUTHORITY\NETWORK                     S-1-5-2
  NT AUTHORITY\Authenticated Users         S-1-5-11
  NT AUTHORITY\This Organization           S-1-5-15
  THROWBACK\Tier 1                         S-1-5-21-3906589501-690843102-3982269896-1192
  Authentication authority asserted identity S-1-18-1
====== TokenPrivileges ======

Current Token's Privileges

                      SeChangeNotifyPrivilege:  SE_PRIVILEGE_ENABLED_BY_DEFAULT, SE_PRIVILEGE_ENABLED
                SeIncreaseWorkingSetPrivilege:  SE_PRIVILEGE_ENABLED_BY_DEFAULT, SE_PRIVILEGE_ENABLED
====== UAC ======

  0                              : 1 - No prompting
  EnableLUA (Is UAC enabled?)    : 0
  LocalAccountTokenFilterPolicy  : 
  FilterAdministratorToken       : 
    [*] UAC is disabled.
    [*] Any administrative local account can be used for lateral movement.
====== UdpConnections ======

  Local Address          PID    Service                 ProcessName
ERROR:   [!] Terminating exception running command 'UdpConnections': System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObjectSearcher.Initialize()
   at System.Management.ManagementObjectSearcher.Get()
   at S34tb3lt.Commands.Windows.UdpConnectionsCommand.<Execute>d__9.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== UserRightAssignments ======

Must be an administrator to enumerate User Right Assignments
====== WindowsAutoLogon ======

  DefaultDomainName              : 
  DefaultUserName                : BlaireJ
  DefaultPassword                : 7eQgx6YzxgG3vC45t5k9
  AltDefaultDomainName           : 
  AltDefaultUserName             : 
  AltDefaultPassword             : 

====== WindowsCredentialFiles ======

  Folder : C:\Users\petersj\AppData\Roaming\Microsoft\Credentials\

    FileName     : 29935E3C3E5A2088B32A3A99DB6A681C
    Description  : Enterprise Credential Data

    MasterKey    : cc892ed1-f771-45bc-9ac3-7769dbc85718
    Accessed     : 8/25/2020 2:52:57 AM
    Modified     : 8/25/2020 2:52:57 AM
    Size         : 398

    FileName     : CDBCD6BB4D9AE842039B4F1580FE8727
    Description  : Enterprise Credential Data

    MasterKey    : cc892ed1-f771-45bc-9ac3-7769dbc85718
    Accessed     : 8/25/2020 2:54:55 AM
    Modified     : 8/25/2020 2:54:55 AM
    Size         : 462


====== WindowsDefender ======

Locally-defined Settings:



GPO-defined Settings:
====== WindowsEventForwarding ======

====== WindowsFirewall ======

Collecting all Windows Firewall Rules


Location                     : SOFTWARE\Policies\Microsoft\WindowsFirewall

Location                     : SYSTEM\CurrentControlSet\Services\SharedAccess\Parameters\FirewallPolicy

Domain Profile
    Enabled                  : True
    DisableNotifications     : True
    DefaultInboundAction     : ALLOW
    DefaultOutboundAction    : ALLOW

Public Profile
    Enabled                  : True
    DisableNotifications     : True
    DefaultInboundAction     : ALLOW
    DefaultOutboundAction    : ALLOW

Standard Profile
    Enabled                  : True
    DisableNotifications     : True
    DefaultInboundAction     : ALLOW
    DefaultOutboundAction    : ALLOW

Rules:

  Name                 : @icsvc.dll,-709
  Description          : @icsvc.dll,-710
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :135
  Remote Addr:Port     : :

  Name                 : @icsvc.dll,-701
  Description          : @icsvc.dll,-702
  ApplicationName      : 
  Protocol             : ICMP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @icsvc.dll,-703
  Description          : @icsvc.dll,-704
  ApplicationName      : 
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @icsvc.dll,-705
  Description          : @icsvc.dll,-706
  ApplicationName      : 
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :139
  Remote Addr:Port     : :

  Name                 : @icsvc.dll,-707
  Description          : @icsvc.dll,-708
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @firewallapi.dll,-50327
  Description          : @firewallapi.dll,-50328
  ApplicationName      : %SystemRoot%\system32\snmptrap.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :162
  Remote Addr:Port     : LocalSubnet:

  Name                 : @firewallapi.dll,-50327
  Description          : @firewallapi.dll,-50328
  ApplicationName      : %SystemRoot%\system32\snmptrap.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :162
  Remote Addr:Port     : :

  Name                 : @%windir%\system32\diagtrack.dll,-3001
  Description          : @%windir%\system32\diagtrack.dll,-3003
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :443

  Name                 : @%systemroot%\system32\dosvc.dll,-102
  Description          : @%systemroot%\system32\dosvc.dll,-104
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :7680
  Remote Addr:Port     : :

  Name                 : @%systemroot%\system32\dosvc.dll,-103
  Description          : @%systemroot%\system32\dosvc.dll,-104
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :7680
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36802
  Description          : @FirewallAPI.dll,-36803
  ApplicationName      : %SystemRoot%\system32\NetEvtFwdr.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36804
  Description          : @FirewallAPI.dll,-36805
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC-EPMap
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-32761
  Description          : @FirewallAPI.dll,-32764
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :2869
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-32765
  Description          : @FirewallAPI.dll,-32768
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :2869

  Name                 : @FirewallAPI.dll,-32769
  Description          : @FirewallAPI.dll,-32772
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :137
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-32773
  Description          : @FirewallAPI.dll,-32776
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :137

  Name                 : @FirewallAPI.dll,-32777
  Description          : @FirewallAPI.dll,-32780
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :138
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-32781
  Description          : @FirewallAPI.dll,-32784
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :138

  Name                 : @FirewallAPI.dll,-32813
  Description          : @FirewallAPI.dll,-32814
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :5358
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-32815
  Description          : @FirewallAPI.dll,-32816
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :5358

  Name                 : @FirewallAPI.dll,-32817
  Description          : @FirewallAPI.dll,-32818
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :5357
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-32819
  Description          : @FirewallAPI.dll,-32820
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :5357

  Name                 : @FirewallAPI.dll,-32753
  Description          : @FirewallAPI.dll,-32756
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :1900
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-32757
  Description          : @FirewallAPI.dll,-32760
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:1900

  Name                 : @FirewallAPI.dll,-32821
  Description          : @FirewallAPI.dll,-32822
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:2869

  Name                 : @FirewallAPI.dll,-32785
  Description          : @FirewallAPI.dll,-32788
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :3702
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-32789
  Description          : @FirewallAPI.dll,-32792
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:3702

  Name                 : @FirewallAPI.dll,-32801
  Description          : @FirewallAPI.dll,-32804
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :5355
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-32805
  Description          : @FirewallAPI.dll,-32808
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:5355

  Name                 : @FirewallAPI.dll,-32809
  Description          : @FirewallAPI.dll,-32810
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :3702
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-32811
  Description          : @FirewallAPI.dll,-32812
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:3702

  Name                 : @FirewallAPI.dll,-37003
  Description          : @FirewallAPI.dll,-37004
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :9955
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37005
  Description          : @FirewallAPI.dll,-37006
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37007
  Description          : @FirewallAPI.dll,-37008
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37009
  Description          : @FirewallAPI.dll,-37010
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @netlogon.dll,-1003
  Description          : @netlogon.dll,-1006
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :445
  Remote Addr:Port     : :

  Name                 : @netlogon.dll,-1008
  Description          : @netlogon.dll,-1009
  ApplicationName      : %SystemRoot%\System32\lsass.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @firewallapi.dll,-36753
  Description          : @firewallapi.dll,-36754
  ApplicationName      : %systemroot%\system32\wininit.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @firewallapi.dll,-36755
  Description          : @firewallapi.dll,-36756
  ApplicationName      : %systemroot%\system32\wininit.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC-EPMap
  Remote Addr:Port     : :

  Name                 : @sstpsvc.dll,-35002
  Description          : @sstpsvc.dll,-35003
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :443
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28603
  Description          : @FirewallAPI.dll,-28606
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :5445
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37102
  Description          : @FirewallAPI.dll,-37103
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :10247
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37102
  Description          : @FirewallAPI.dll,-37103
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :10247
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-33253
  Description          : @FirewallAPI.dll,-33256
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-33257
  Description          : @FirewallAPI.dll,-33260
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC-EPMap
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37505
  Description          : @FirewallAPI.dll,-37506
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-30253
  Description          : @FirewallAPI.dll,-30256
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :5985
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-30253
  Description          : @FirewallAPI.dll,-30256
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :5985
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-35001
  Description          : @FirewallAPI.dll,-35002
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :80
  Remote Addr:Port     : :

  Name                 : @peerdistsh.dll,-10000
  Description          : @peerdistsh.dll,-11000
  ApplicationName      : SYSTEM
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :80
  Remote Addr:Port     : :

  Name                 : @peerdistsh.dll,-10001
  Description          : @peerdistsh.dll,-11001
  ApplicationName      : SYSTEM
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :80

  Name                 : @peerdistsh.dll,-10002
  Description          : @peerdistsh.dll,-11002
  ApplicationName      : %systemroot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :3702
  Remote Addr:Port     : LocalSubnet:

  Name                 : @peerdistsh.dll,-10003
  Description          : @peerdistsh.dll,-11003
  ApplicationName      : %systemroot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:3702

  Name                 : @peerdistsh.dll,-10004
  Description          : @peerdistsh.dll,-11004
  ApplicationName      : SYSTEM
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :443
  Remote Addr:Port     : :

  Name                 : @peerdistsh.dll,-10005
  Description          : @peerdistsh.dll,-11005
  ApplicationName      : SYSTEM
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :443
  Remote Addr:Port     : :

  Name                 : @peerdistsh.dll,-10006
  Description          : @peerdistsh.dll,-11006
  ApplicationName      : SYSTEM
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :443

  Name                 : Remote Desktop - User Mode (TCP-In)
  Description          : Inbound rule for the Remote Desktop service to allow RDP traffic. [TCP 3389]
  ApplicationName      : C:\Windows\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :3389
  Remote Addr:Port     : :

  Name                 : Remote Desktop - User Mode (UDP-In)
  Description          : Inbound rule for the Remote Desktop service to allow RDP traffic. [UDP 3389]
  ApplicationName      : C:\Windows\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :3389
  Remote Addr:Port     : :

  Name                 : Remote Desktop - Shadow (TCP-In)
  Description          : Inbound rule for the Remote Desktop service to allow shadowing of an existing Remote Desktop session. (TCP-In)
  ApplicationName      : C:\Windows\system32\RdpSa.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28780
  Description          : @FirewallAPI.dll,-28781
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :3387
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28783
  Description          : @FirewallAPI.dll,-28784
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :3392
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36002
  Description          : @FirewallAPI.dll,-36003
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :10246
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36002
  Description          : @FirewallAPI.dll,-36003
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :10246
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-36002
  Description          : @FirewallAPI.dll,-36003
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :10246
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36004
  Description          : @FirewallAPI.dll,-36005
  ApplicationName      : %SystemRoot%\system32\mdeserver.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36004
  Description          : @FirewallAPI.dll,-36005
  ApplicationName      : %SystemRoot%\system32\mdeserver.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-36004
  Description          : @FirewallAPI.dll,-36005
  ApplicationName      : %SystemRoot%\system32\mdeserver.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36006
  Description          : @FirewallAPI.dll,-36007
  ApplicationName      : %SystemRoot%\system32\mdeserver.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36006
  Description          : @FirewallAPI.dll,-36007
  ApplicationName      : %SystemRoot%\system32\mdeserver.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-36006
  Description          : @FirewallAPI.dll,-36007
  ApplicationName      : %SystemRoot%\system32\mdeserver.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36008
  Description          : @FirewallAPI.dll,-36009
  ApplicationName      : %SystemRoot%\system32\mdeserver.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :23556
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36008
  Description          : @FirewallAPI.dll,-36009
  ApplicationName      : %SystemRoot%\system32\mdeserver.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :23556
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-36008
  Description          : @FirewallAPI.dll,-36009
  ApplicationName      : %SystemRoot%\system32\mdeserver.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :23556
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36104
  Description          : @FirewallAPI.dll,-36105
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36106
  Description          : @FirewallAPI.dll,-36107
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :2869
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36010
  Description          : @FirewallAPI.dll,-36011
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :2177
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36012
  Description          : @FirewallAPI.dll,-36013
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :2177

  Name                 : @FirewallAPI.dll,-36014
  Description          : @FirewallAPI.dll,-36015
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :2177
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36016
  Description          : @FirewallAPI.dll,-36017
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :2177

  Name                 : @FirewallAPI.dll,-33769
  Description          : @FirewallAPI.dll,-33772
  ApplicationName      : System
  Protocol             : GRE
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-33773
  Description          : @FirewallAPI.dll,-33776
  ApplicationName      : System
  Protocol             : GRE
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-33753
  Description          : @FirewallAPI.dll,-33756
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :1701
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-33757
  Description          : @FirewallAPI.dll,-33760
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :1701

  Name                 : @FirewallAPI.dll,-33765
  Description          : @FirewallAPI.dll,-33768
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :1723
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-33761
  Description          : @FirewallAPI.dll,-33764
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :1723

  Name                 : @FirewallAPI.dll,-37507
  Description          : @FirewallAPI.dll,-37508
  ApplicationName      : %SystemRoot%\system32\dmcertinst.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-34502
  Description          : @FirewallAPI.dll,-34503
  ApplicationName      : %SystemRoot%\system32\vds.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-34504
  Description          : @FirewallAPI.dll,-34505
  ApplicationName      : %SystemRoot%\system32\vdsldr.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-34506
  Description          : @FirewallAPI.dll,-34507
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC-EPMap
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28503
  Description          : @FirewallAPI.dll,-28506
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :139
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28507
  Description          : @FirewallAPI.dll,-28510
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :139

  Name                 : @FirewallAPI.dll,-28511
  Description          : @FirewallAPI.dll,-28514
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :445
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28515
  Description          : @FirewallAPI.dll,-28518
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :445

  Name                 : @FirewallAPI.dll,-28519
  Description          : @FirewallAPI.dll,-28522
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :137
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28523
  Description          : @FirewallAPI.dll,-28526
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :137

  Name                 : @FirewallAPI.dll,-28527
  Description          : @FirewallAPI.dll,-28530
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :138
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28531
  Description          : @FirewallAPI.dll,-28534
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :138

  Name                 : @FirewallAPI.dll,-28535
  Description          : @FirewallAPI.dll,-28538
  ApplicationName      : %SystemRoot%\system32\spoolsv.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28539
  Description          : @FirewallAPI.dll,-28542
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :RPC-EPMap
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28543
  Description          : @FirewallAPI.dll,-28547
  ApplicationName      : System
  Protocol             : ICMP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28544
  Description          : @FirewallAPI.dll,-28547
  ApplicationName      : System
  Protocol             : ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28545
  Description          : @FirewallAPI.dll,-28547
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28546
  Description          : @FirewallAPI.dll,-28547
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28548
  Description          : @FirewallAPI.dll,-28549
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :5355
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-28550
  Description          : @FirewallAPI.dll,-28551
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:5355

  Name                 : @FirewallAPI.dll,-29003
  Description          : @FirewallAPI.dll,-29006
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-29007
  Description          : @FirewallAPI.dll,-29010
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-29253
  Description          : @FirewallAPI.dll,-29256
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-29257
  Description          : @FirewallAPI.dll,-29260
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :445
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-29265
  Description          : @FirewallAPI.dll,-29268
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC-EPMap
  Remote Addr:Port     : :

  Name                 : @%SystemRoot%\system32\firewallapi.dll,-36903
  Description          : @%SystemRoot%\system32\firewallapi.dll,-36904
  ApplicationName      : %SystemRoot%\system32\MuxSvcHost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28003
  Description          : @FirewallAPI.dll,-28006
  ApplicationName      : %SystemRoot%\system32\sppextcomobj.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :1688
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37503
  Description          : @FirewallAPI.dll,-37504
  ApplicationName      : %SystemRoot%\system32\omadmclient.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-34753
  Description          : @FirewallAPI.dll,-34754
  ApplicationName      : %systemroot%\system32\plasrv.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-34755
  Description          : @FirewallAPI.dll,-34756
  ApplicationName      : %systemroot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :135
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-34753
  Description          : @FirewallAPI.dll,-34754
  ApplicationName      : %systemroot%\system32\plasrv.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-34755
  Description          : @FirewallAPI.dll,-34756
  ApplicationName      : %systemroot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :135
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36502
  Description          : @FirewallAPI.dll,-36503
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :135
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36504
  Description          : @FirewallAPI.dll,-36505
  ApplicationName      : %SystemRoot%\system32\RmtTpmVscMgrSvr.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36506
  Description          : @FirewallAPI.dll,-36507
  ApplicationName      : %SystemRoot%\system32\RmtTpmVscMgrSvr.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-36502
  Description          : @FirewallAPI.dll,-36503
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :135
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-36504
  Description          : @FirewallAPI.dll,-36505
  ApplicationName      : %SystemRoot%\system32\RmtTpmVscMgrSvr.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-36506
  Description          : @FirewallAPI.dll,-36507
  ApplicationName      : %SystemRoot%\system32\RmtTpmVscMgrSvr.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-25110
  Description          : @FirewallAPI.dll,-25112
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25001
  Description          : @FirewallAPI.dll,-25007
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25002
  Description          : @FirewallAPI.dll,-25007
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25113
  Description          : @FirewallAPI.dll,-25115
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25114
  Description          : @FirewallAPI.dll,-25115
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25116
  Description          : @FirewallAPI.dll,-25118
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25117
  Description          : @FirewallAPI.dll,-25118
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25019
  Description          : @FirewallAPI.dll,-25025
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25020
  Description          : @FirewallAPI.dll,-25025
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25026
  Description          : @FirewallAPI.dll,-25032
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25027
  Description          : @FirewallAPI.dll,-25032
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25012
  Description          : @FirewallAPI.dll,-25018
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25013
  Description          : @FirewallAPI.dll,-25018
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25009
  Description          : @FirewallAPI.dll,-25011
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25008
  Description          : @FirewallAPI.dll,-25011
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25061
  Description          : @FirewallAPI.dll,-25067
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25062
  Description          : @FirewallAPI.dll,-25067
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25068
  Description          : @FirewallAPI.dll,-25074
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25069
  Description          : @FirewallAPI.dll,-25074
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25075
  Description          : @FirewallAPI.dll,-25081
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25076
  Description          : @FirewallAPI.dll,-25081
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25082
  Description          : @FirewallAPI.dll,-25088
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25083
  Description          : @FirewallAPI.dll,-25088
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25251
  Description          : @FirewallAPI.dll,-25257
  ApplicationName      : System
  Protocol             : ICMP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25376
  Description          : @FirewallAPI.dll,-25382
  ApplicationName      : System
  Protocol             : IGMP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25377
  Description          : @FirewallAPI.dll,-25382
  ApplicationName      : System
  Protocol             : IGMP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25301
  Description          : @FirewallAPI.dll,-25303
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :68
  Remote Addr:Port     : :67

  Name                 : @FirewallAPI.dll,-25302
  Description          : @FirewallAPI.dll,-25303
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :68
  Remote Addr:Port     : :67

  Name                 : @FirewallAPI.dll,-25304
  Description          : @FirewallAPI.dll,-25306
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :546
  Remote Addr:Port     : :547

  Name                 : @FirewallAPI.dll,-25305
  Description          : @FirewallAPI.dll,-25306
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :546
  Remote Addr:Port     : :547

  Name                 : @FirewallAPI.dll,-25326
  Description          : @FirewallAPI.dll,-25332
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :Teredo
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25327
  Description          : @FirewallAPI.dll,-25333
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25426
  Description          : @FirewallAPI.dll,-25428
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25427
  Description          : @FirewallAPI.dll,-25429
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25351
  Description          : @FirewallAPI.dll,-25357
  ApplicationName      : System
  Protocol             : IPv6
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25352
  Description          : @FirewallAPI.dll,-25358
  ApplicationName      : System
  Protocol             : IPv6
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25401
  Description          : @FirewallAPI.dll,-25401
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :445

  Name                 : @FirewallAPI.dll,-25403
  Description          : @FirewallAPI.dll,-25404
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-25405
  Description          : @FirewallAPI.dll,-25406
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :53

  Name                 : @FirewallAPI.dll,-25407
  Description          : @FirewallAPI.dll,-25408
  ApplicationName      : %SystemRoot%\system32\lsass.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @%systemroot%\system32\firewallapi.dll,-3401
  Description          : @%systemroot%\system32\firewallapi.dll,-3402
  ApplicationName      : %systemroot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :135
  Remote Addr:Port     : :

  Name                 : @%systemroot%\system32\firewallapi.dll,-3406
  Description          : @%systemroot%\system32\firewallapi.dll,-3407
  ApplicationName      : %systemroot%\system32\dllhost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-33503
  Description          : @FirewallAPI.dll,-33506
  ApplicationName      : %SystemRoot%\system32\msdtc.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-33507
  Description          : @FirewallAPI.dll,-33510
  ApplicationName      : %SystemRoot%\system32\msdtc.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-33511
  Description          : @FirewallAPI.dll,-33512
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-33513
  Description          : @FirewallAPI.dll,-33514
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC-EPMap
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-29503
  Description          : @FirewallAPI.dll,-29506
  ApplicationName      : %SystemRoot%\system32\services.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-29507
  Description          : @FirewallAPI.dll,-29510
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :445
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-29515
  Description          : @FirewallAPI.dll,-29518
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC-EPMap
  Remote Addr:Port     : :

  Name                 : @%SystemRoot%\system32\firewallapi.dll,-37303
  Description          : @%SystemRoot%\system32\firewallapi.dll,-37304
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :5353
  Remote Addr:Port     : LocalSubnet:

  Name                 : @%SystemRoot%\system32\firewallapi.dll,-37303
  Description          : @%SystemRoot%\system32\firewallapi.dll,-37304
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :5353
  Remote Addr:Port     : :

  Name                 : @%SystemRoot%\system32\firewallapi.dll,-37303
  Description          : @%SystemRoot%\system32\firewallapi.dll,-37304
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :5353
  Remote Addr:Port     : LocalSubnet:

  Name                 : @%SystemRoot%\system32\firewallapi.dll,-37305
  Description          : @%SystemRoot%\system32\firewallapi.dll,-37306
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:5353

  Name                 : @%SystemRoot%\system32\firewallapi.dll,-37305
  Description          : @%SystemRoot%\system32\firewallapi.dll,-37306
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :5353

  Name                 : @%SystemRoot%\system32\firewallapi.dll,-37305
  Description          : @%SystemRoot%\system32\firewallapi.dll,-37306
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:5353

  Name                 : @FirewallAPI.dll,-30003
  Description          : @FirewallAPI.dll,-30006
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-30007
  Description          : @FirewallAPI.dll,-30010
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC-EPMap
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-34252
  Description          : @FirewallAPI.dll,-34253
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :135
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-34254
  Description          : @FirewallAPI.dll,-34255
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-34258
  Description          : @FirewallAPI.dll,-34259
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-34256
  Description          : @FirewallAPI.dll,-34257
  ApplicationName      : %systemroot%\system32\wbem\unsecapp.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-31023
  Description          : @FirewallAPI.dll,-31006
  ApplicationName      : %ProgramFiles(x86)%\Windows Media Player\wmplayer.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-31024
  Description          : @FirewallAPI.dll,-31010
  ApplicationName      : %ProgramFiles(x86)%\Windows Media Player\wmplayer.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-31025
  Description          : @FirewallAPI.dll,-31014
  ApplicationName      : %ProgramFiles(x86)%\Windows Media Player\wmplayer.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-31003
  Description          : @FirewallAPI.dll,-31006
  ApplicationName      : %ProgramFiles%\Windows Media Player\wmplayer.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-31007
  Description          : @FirewallAPI.dll,-31010
  ApplicationName      : %ProgramFiles%\Windows Media Player\wmplayer.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-31011
  Description          : @FirewallAPI.dll,-31014
  ApplicationName      : %ProgramFiles%\Windows Media Player\wmplayer.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-31253
  Description          : @FirewallAPI.dll,-31256
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :2177
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-31257
  Description          : @FirewallAPI.dll,-31260
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :2177

  Name                 : @FirewallAPI.dll,-31261
  Description          : @FirewallAPI.dll,-31264
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :2177
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-31265
  Description          : @FirewallAPI.dll,-31268
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :2177

  Name                 : @FirewallAPI.dll,-31285
  Description          : @FirewallAPI.dll,-31288
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :10243
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-31289
  Description          : @FirewallAPI.dll,-31292
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :10243

  Name                 : @FirewallAPI.dll,-31293
  Description          : @FirewallAPI.dll,-31296
  ApplicationName      : %PROGRAMFILES%\Windows Media Player\wmplayer.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-31297
  Description          : @FirewallAPI.dll,-31300
  ApplicationName      : %PROGRAMFILES%\Windows Media Player\wmplayer.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-31301
  Description          : @FirewallAPI.dll,-31304
  ApplicationName      : %PROGRAMFILES%\Windows Media Player\wmplayer.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-31305
  Description          : @FirewallAPI.dll,-31308
  ApplicationName      : %PROGRAMFILES%\Windows Media Player\wmpnetwk.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-31309
  Description          : @FirewallAPI.dll,-31312
  ApplicationName      : %PROGRAMFILES%\Windows Media Player\wmpnetwk.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-31313
  Description          : @FirewallAPI.dll,-31316
  ApplicationName      : %PROGRAMFILES%\Windows Media Player\wmpnetwk.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-31317
  Description          : @FirewallAPI.dll,-31320
  ApplicationName      : %PROGRAMFILES%\Windows Media Player\wmpnetwk.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-31253
  Description          : @FirewallAPI.dll,-31256
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :2177
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-31257
  Description          : @FirewallAPI.dll,-31260
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:2177

  Name                 : @FirewallAPI.dll,-31261
  Description          : @FirewallAPI.dll,-31264
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :2177
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-31265
  Description          : @FirewallAPI.dll,-31268
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:2177

  Name                 : @FirewallAPI.dll,-31269
  Description          : @FirewallAPI.dll,-31272
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :1900
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-31273
  Description          : @FirewallAPI.dll,-31276
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:1900

  Name                 : @FirewallAPI.dll,-31277
  Description          : @FirewallAPI.dll,-31280
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :2869
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-31281
  Description          : @FirewallAPI.dll,-31284
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-31285
  Description          : @FirewallAPI.dll,-31288
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :10243
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-31289
  Description          : @FirewallAPI.dll,-31292
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:10243

  Name                 : @FirewallAPI.dll,-31293
  Description          : @FirewallAPI.dll,-31296
  ApplicationName      : %PROGRAMFILES%\Windows Media Player\wmplayer.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-31297
  Description          : @FirewallAPI.dll,-31300
  ApplicationName      : %PROGRAMFILES%\Windows Media Player\wmplayer.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-31301
  Description          : @FirewallAPI.dll,-31304
  ApplicationName      : %PROGRAMFILES%\Windows Media Player\wmplayer.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-31305
  Description          : @FirewallAPI.dll,-31308
  ApplicationName      : %PROGRAMFILES%\Windows Media Player\wmpnetwk.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-31309
  Description          : @FirewallAPI.dll,-31312
  ApplicationName      : %PROGRAMFILES%\Windows Media Player\wmpnetwk.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-31313
  Description          : @FirewallAPI.dll,-31316
  ApplicationName      : %PROGRAMFILES%\Windows Media Player\wmpnetwk.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-31317
  Description          : @FirewallAPI.dll,-31320
  ApplicationName      : %PROGRAMFILES%\Windows Media Player\wmpnetwk.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-31321
  Description          : @FirewallAPI.dll,-31322
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-31501
  Description          : @FirewallAPI.dll,-31502
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :10245
  Remote Addr:Port     : :

  Name                 : Remote Desktop - User Mode (TCP-In)
  Description          : Inbound rule for the Remote Desktop service to allow RDP traffic. [TCP 3389]
  ApplicationName      : C:\Windows\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :3389
  Remote Addr:Port     : :

  Name                 : Remote Desktop - User Mode (UDP-In)
  Description          : Inbound rule for the Remote Desktop service to allow RDP traffic. [UDP 3389]
  ApplicationName      : C:\Windows\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :3389
  Remote Addr:Port     : :

  Name                 : Remote Desktop - Shadow (TCP-In)
  Description          : Inbound rule for the Remote Desktop service to allow shadowing of an existing Remote Desktop session. (TCP-In)
  ApplicationName      : C:\Windows\system32\RdpSa.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  Description          : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  Description          : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  Description          : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  Description          : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.ShellExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.ShellExperienceHost/resources/PkgDisplayName}
  Description          : @{Microsoft.Windows.ShellExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.ShellExperienceHost/resources/PkgDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.SecHealthUI_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.SecHealthUI/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.SecHealthUI_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.SecHealthUI/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.PeopleExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.PeopleExperienceHost/resources/PkgDisplayName}
  Description          : @{Microsoft.Windows.PeopleExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.PeopleExperienceHost/resources/PkgDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.OOBENetworkCaptivePortal_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.OOBENetworkCaptivePortal/Resources/AppDisplayName}
  Description          : @{Microsoft.Windows.OOBENetworkCaptivePortal_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.OOBENetworkCaptivePortal/Resources/AppDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.NarratorQuickStart_10.0.17763.1_neutral_neutral_8wekyb3d8bbwe?ms-resource://Microsoft.Windows.NarratorQuickStart/Resources/AppDisplayName}
  Description          : @{Microsoft.Windows.NarratorQuickStart_10.0.17763.1_neutral_neutral_8wekyb3d8bbwe?ms-resource://Microsoft.Windows.NarratorQuickStart/Resources/AppDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Apprep.ChxApp_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Apprep.ChxApp/resources/DisplayName}
  Description          : @{Microsoft.Windows.Apprep.ChxApp_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Apprep.ChxApp/resources/DisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/DisplayName}
  Description          : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/Description}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/DisplayName}
  Description          : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/Description}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.LockApp_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.LockApp/resources/AppDisplayName}
  Description          : @{Microsoft.LockApp_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.LockApp/resources/AppDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AccountsControl_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.AccountsControl/Resources/DisplayName}
  Description          : @{Microsoft.AccountsControl_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.AccountsControl/Resources/DisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : Shell Input Application
  Description          : Shell Input Application
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @%SystemRoot%\system32\firewallapi.dll,-38520
  Description          : @%SystemRoot%\system32\firewallapi.dll,-38530
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :80
  Remote Addr:Port     : :

  Name                 : @%SystemRoot%\system32\firewallapi.dll,-38522
  Description          : @%SystemRoot%\system32\firewallapi.dll,-38532
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :443
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  Description          : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  Description          : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  Description          : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  Description          : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.ShellExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.ShellExperienceHost/resources/PkgDisplayName}
  Description          : @{Microsoft.Windows.ShellExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.ShellExperienceHost/resources/PkgDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  Description          : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  Description          : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  Description          : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  Description          : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.ShellExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.ShellExperienceHost/resources/PkgDisplayName}
  Description          : @{Microsoft.Windows.ShellExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.ShellExperienceHost/resources/PkgDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.SecHealthUI_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.SecHealthUI/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.SecHealthUI_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.SecHealthUI/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.PeopleExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.PeopleExperienceHost/resources/PkgDisplayName}
  Description          : @{Microsoft.Windows.PeopleExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.PeopleExperienceHost/resources/PkgDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.OOBENetworkCaptivePortal_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.OOBENetworkCaptivePortal/Resources/AppDisplayName}
  Description          : @{Microsoft.Windows.OOBENetworkCaptivePortal_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.OOBENetworkCaptivePortal/Resources/AppDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.NarratorQuickStart_10.0.17763.1_neutral_neutral_8wekyb3d8bbwe?ms-resource://Microsoft.Windows.NarratorQuickStart/Resources/AppDisplayName}
  Description          : @{Microsoft.Windows.NarratorQuickStart_10.0.17763.1_neutral_neutral_8wekyb3d8bbwe?ms-resource://Microsoft.Windows.NarratorQuickStart/Resources/AppDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Apprep.ChxApp_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Apprep.ChxApp/resources/DisplayName}
  Description          : @{Microsoft.Windows.Apprep.ChxApp_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Apprep.ChxApp/resources/DisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/DisplayName}
  Description          : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/Description}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/DisplayName}
  Description          : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/Description}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.LockApp_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.LockApp/resources/AppDisplayName}
  Description          : @{Microsoft.LockApp_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.LockApp/resources/AppDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AccountsControl_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.AccountsControl/Resources/DisplayName}
  Description          : @{Microsoft.AccountsControl_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.AccountsControl/Resources/DisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : Shell Input Application
  Description          : Shell Input Application
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.SecHealthUI_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.SecHealthUI/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.SecHealthUI_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.SecHealthUI/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.PeopleExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.PeopleExperienceHost/resources/PkgDisplayName}
  Description          : @{Microsoft.Windows.PeopleExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.PeopleExperienceHost/resources/PkgDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.OOBENetworkCaptivePortal_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.OOBENetworkCaptivePortal/Resources/AppDisplayName}
  Description          : @{Microsoft.Windows.OOBENetworkCaptivePortal_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.OOBENetworkCaptivePortal/Resources/AppDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.NarratorQuickStart_10.0.17763.1_neutral_neutral_8wekyb3d8bbwe?ms-resource://Microsoft.Windows.NarratorQuickStart/Resources/AppDisplayName}
  Description          : @{Microsoft.Windows.NarratorQuickStart_10.0.17763.1_neutral_neutral_8wekyb3d8bbwe?ms-resource://Microsoft.Windows.NarratorQuickStart/Resources/AppDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Apprep.ChxApp_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Apprep.ChxApp/resources/DisplayName}
  Description          : @{Microsoft.Windows.Apprep.ChxApp_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Apprep.ChxApp/resources/DisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/DisplayName}
  Description          : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/Description}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/DisplayName}
  Description          : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/Description}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.LockApp_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.LockApp/resources/AppDisplayName}
  Description          : @{Microsoft.LockApp_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.LockApp/resources/AppDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AccountsControl_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.AccountsControl/Resources/DisplayName}
  Description          : @{Microsoft.AccountsControl_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.AccountsControl/Resources/DisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : Shell Input Application
  Description          : Shell Input Application
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-32811
  Description          : @FirewallAPI.dll,-32812
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:3702

  Name                 : @FirewallAPI.dll,-32809
  Description          : @FirewallAPI.dll,-32810
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :3702
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-32805
  Description          : @FirewallAPI.dll,-32808
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:5355

  Name                 : @FirewallAPI.dll,-32801
  Description          : @FirewallAPI.dll,-32804
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :5355
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-32789
  Description          : @FirewallAPI.dll,-32792
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:3702

  Name                 : @FirewallAPI.dll,-32785
  Description          : @FirewallAPI.dll,-32788
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :3702
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-32821
  Description          : @FirewallAPI.dll,-32822
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:2869

  Name                 : @FirewallAPI.dll,-32757
  Description          : @FirewallAPI.dll,-32760
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:1900

  Name                 : @FirewallAPI.dll,-32753
  Description          : @FirewallAPI.dll,-32756
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :1900
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-32819
  Description          : @FirewallAPI.dll,-32820
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :5357

  Name                 : @FirewallAPI.dll,-32817
  Description          : @FirewallAPI.dll,-32818
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :5357
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-32815
  Description          : @FirewallAPI.dll,-32816
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :5358

  Name                 : @FirewallAPI.dll,-32813
  Description          : @FirewallAPI.dll,-32814
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :5358
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-32781
  Description          : @FirewallAPI.dll,-32784
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :138

  Name                 : @FirewallAPI.dll,-32777
  Description          : @FirewallAPI.dll,-32780
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :138
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-32773
  Description          : @FirewallAPI.dll,-32776
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :137

  Name                 : @FirewallAPI.dll,-32769
  Description          : @FirewallAPI.dll,-32772
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :137
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-32765
  Description          : @FirewallAPI.dll,-32768
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :2869

  Name                 : @FirewallAPI.dll,-32761
  Description          : @FirewallAPI.dll,-32764
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :2869
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28550
  Description          : @FirewallAPI.dll,-28551
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:5355

  Name                 : @FirewallAPI.dll,-28548
  Description          : @FirewallAPI.dll,-28549
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :5355
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-28546
  Description          : @FirewallAPI.dll,-28547
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28545
  Description          : @FirewallAPI.dll,-28547
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28544
  Description          : @FirewallAPI.dll,-28547
  ApplicationName      : System
  Protocol             : ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28543
  Description          : @FirewallAPI.dll,-28547
  ApplicationName      : System
  Protocol             : ICMP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28539
  Description          : @FirewallAPI.dll,-28542
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :RPC-EPMap
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28535
  Description          : @FirewallAPI.dll,-28538
  ApplicationName      : %SystemRoot%\system32\spoolsv.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28531
  Description          : @FirewallAPI.dll,-28534
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :138

  Name                 : @FirewallAPI.dll,-28527
  Description          : @FirewallAPI.dll,-28530
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :138
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28523
  Description          : @FirewallAPI.dll,-28526
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :137

  Name                 : @FirewallAPI.dll,-28519
  Description          : @FirewallAPI.dll,-28522
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :137
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28515
  Description          : @FirewallAPI.dll,-28518
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :445

  Name                 : @FirewallAPI.dll,-28511
  Description          : @FirewallAPI.dll,-28514
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :445
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28507
  Description          : @FirewallAPI.dll,-28510
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Domain
  Local Addr:Port      : :
  Remote Addr:Port     : :139

  Name                 : @FirewallAPI.dll,-28503
  Description          : @FirewallAPI.dll,-28506
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Domain
  Local Addr:Port      : :139
  Remote Addr:Port     : :

  Name                 : OpenSSH SSH Server (sshd)
  Description          : Inbound rule for OpenSSH SSH Server (sshd)
  ApplicationName      : %SystemRoot%\system32\OpenSSH\sshd.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :22
  Remote Addr:Port     : :

  Name                 : WinRM-HTTP
  Description          : 
  ApplicationName      : 
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :5985
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  Description          : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  Description          : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  Description          : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  Description          : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.ShellExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.ShellExperienceHost/resources/PkgDisplayName}
  Description          : @{Microsoft.Windows.ShellExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.ShellExperienceHost/resources/PkgDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.SecHealthUI_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.SecHealthUI/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.SecHealthUI_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.SecHealthUI/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.PeopleExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.PeopleExperienceHost/resources/PkgDisplayName}
  Description          : @{Microsoft.Windows.PeopleExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.PeopleExperienceHost/resources/PkgDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.OOBENetworkCaptivePortal_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.OOBENetworkCaptivePortal/Resources/AppDisplayName}
  Description          : @{Microsoft.Windows.OOBENetworkCaptivePortal_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.OOBENetworkCaptivePortal/Resources/AppDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.NarratorQuickStart_10.0.17763.1_neutral_neutral_8wekyb3d8bbwe?ms-resource://Microsoft.Windows.NarratorQuickStart/Resources/AppDisplayName}
  Description          : @{Microsoft.Windows.NarratorQuickStart_10.0.17763.1_neutral_neutral_8wekyb3d8bbwe?ms-resource://Microsoft.Windows.NarratorQuickStart/Resources/AppDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Apprep.ChxApp_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Apprep.ChxApp/resources/DisplayName}
  Description          : @{Microsoft.Windows.Apprep.ChxApp_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Apprep.ChxApp/resources/DisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/DisplayName}
  Description          : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/Description}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/DisplayName}
  Description          : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/Description}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.LockApp_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.LockApp/resources/AppDisplayName}
  Description          : @{Microsoft.LockApp_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.LockApp/resources/AppDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AccountsControl_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.AccountsControl/Resources/DisplayName}
  Description          : @{Microsoft.AccountsControl_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.AccountsControl/Resources/DisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : Shell Input Application
  Description          : Shell Input Application
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  Description          : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  Description          : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  Description          : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  Description          : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.ShellExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.ShellExperienceHost/resources/PkgDisplayName}
  Description          : @{Microsoft.Windows.ShellExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.ShellExperienceHost/resources/PkgDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.SecHealthUI_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.SecHealthUI/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.SecHealthUI_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.SecHealthUI/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.PeopleExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.PeopleExperienceHost/resources/PkgDisplayName}
  Description          : @{Microsoft.Windows.PeopleExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.PeopleExperienceHost/resources/PkgDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.OOBENetworkCaptivePortal_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.OOBENetworkCaptivePortal/Resources/AppDisplayName}
  Description          : @{Microsoft.Windows.OOBENetworkCaptivePortal_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.OOBENetworkCaptivePortal/Resources/AppDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.NarratorQuickStart_10.0.17763.1_neutral_neutral_8wekyb3d8bbwe?ms-resource://Microsoft.Windows.NarratorQuickStart/Resources/AppDisplayName}
  Description          : @{Microsoft.Windows.NarratorQuickStart_10.0.17763.1_neutral_neutral_8wekyb3d8bbwe?ms-resource://Microsoft.Windows.NarratorQuickStart/Resources/AppDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Apprep.ChxApp_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Apprep.ChxApp/resources/DisplayName}
  Description          : @{Microsoft.Windows.Apprep.ChxApp_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Apprep.ChxApp/resources/DisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/DisplayName}
  Description          : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/Description}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/DisplayName}
  Description          : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/Description}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.LockApp_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.LockApp/resources/AppDisplayName}
  Description          : @{Microsoft.LockApp_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.LockApp/resources/AppDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AccountsControl_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.AccountsControl/Resources/DisplayName}
  Description          : @{Microsoft.AccountsControl_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.AccountsControl/Resources/DisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : Shell Input Application
  Description          : Shell Input Application
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28550
  Description          : @FirewallAPI.dll,-28551
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:5355

  Name                 : @FirewallAPI.dll,-28548
  Description          : @FirewallAPI.dll,-28549
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :5355
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-28546
  Description          : @FirewallAPI.dll,-28547
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28545
  Description          : @FirewallAPI.dll,-28547
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28544
  Description          : @FirewallAPI.dll,-28547
  ApplicationName      : System
  Protocol             : ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28543
  Description          : @FirewallAPI.dll,-28547
  ApplicationName      : System
  Protocol             : ICMP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28539
  Description          : @FirewallAPI.dll,-28542
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :RPC-EPMap
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28535
  Description          : @FirewallAPI.dll,-28538
  ApplicationName      : %SystemRoot%\system32\spoolsv.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28531
  Description          : @FirewallAPI.dll,-28534
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :138

  Name                 : @FirewallAPI.dll,-28527
  Description          : @FirewallAPI.dll,-28530
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :138
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28523
  Description          : @FirewallAPI.dll,-28526
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :137

  Name                 : @FirewallAPI.dll,-28519
  Description          : @FirewallAPI.dll,-28522
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :137
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28515
  Description          : @FirewallAPI.dll,-28518
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :445

  Name                 : @FirewallAPI.dll,-28511
  Description          : @FirewallAPI.dll,-28514
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :445
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-28507
  Description          : @FirewallAPI.dll,-28510
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :139

  Name                 : @FirewallAPI.dll,-28503
  Description          : @FirewallAPI.dll,-28506
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :139
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-32811
  Description          : @FirewallAPI.dll,-32812
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:3702

  Name                 : @FirewallAPI.dll,-32809
  Description          : @FirewallAPI.dll,-32810
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :3702
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-32805
  Description          : @FirewallAPI.dll,-32808
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:5355

  Name                 : @FirewallAPI.dll,-32801
  Description          : @FirewallAPI.dll,-32804
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :5355
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-32789
  Description          : @FirewallAPI.dll,-32792
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:3702

  Name                 : @FirewallAPI.dll,-32785
  Description          : @FirewallAPI.dll,-32788
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :3702
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-32821
  Description          : @FirewallAPI.dll,-32822
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:2869

  Name                 : @FirewallAPI.dll,-32757
  Description          : @FirewallAPI.dll,-32760
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : LocalSubnet:1900

  Name                 : @FirewallAPI.dll,-32753
  Description          : @FirewallAPI.dll,-32756
  ApplicationName      : %SystemRoot%\system32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :1900
  Remote Addr:Port     : LocalSubnet:

  Name                 : @FirewallAPI.dll,-32819
  Description          : @FirewallAPI.dll,-32820
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :5357

  Name                 : @FirewallAPI.dll,-32817
  Description          : @FirewallAPI.dll,-32818
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :5357
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-32815
  Description          : @FirewallAPI.dll,-32816
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :5358

  Name                 : @FirewallAPI.dll,-32813
  Description          : @FirewallAPI.dll,-32814
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :5358
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-32781
  Description          : @FirewallAPI.dll,-32784
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :138

  Name                 : @FirewallAPI.dll,-32777
  Description          : @FirewallAPI.dll,-32780
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :138
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-32773
  Description          : @FirewallAPI.dll,-32776
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :137

  Name                 : @FirewallAPI.dll,-32769
  Description          : @FirewallAPI.dll,-32772
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :137
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-32765
  Description          : @FirewallAPI.dll,-32768
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :2869

  Name                 : @FirewallAPI.dll,-32761
  Description          : @FirewallAPI.dll,-32764
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :2869
  Remote Addr:Port     : :

  Name                 : @kdcsvc.dll,-1000
  Description          : @kdcsvc.dll,-1004
  ApplicationName      : %systemroot%\System32\lsass.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :88
  Remote Addr:Port     : :

  Name                 : @kdcsvc.dll,-1001
  Description          : @kdcsvc.dll,-1005
  ApplicationName      : %systemroot%\System32\lsass.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :88
  Remote Addr:Port     : :

  Name                 : @kdcsvc.dll,-1002
  Description          : @kdcsvc.dll,-1006
  ApplicationName      : %systemroot%\System32\lsass.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :464
  Remote Addr:Port     : :

  Name                 : @kdcsvc.dll,-1003
  Description          : @kdcsvc.dll,-1007
  ApplicationName      : %systemroot%\System32\lsass.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :464
  Remote Addr:Port     : :

  Name                 : @ntfrsres.dll,-526
  Description          : @ntfrsres.dll,-528
  ApplicationName      : %SystemRoot%\system32\NTFRS.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @ntfrsres.dll,-530
  Description          : @ntfrsres.dll,-531
  ApplicationName      : %systemroot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC-EPMap
  Remote Addr:Port     : :

  Name                 : @%SystemRoot%\system32\firewallapi.dll,-53427
  Description          : @%SystemRoot%\system32\firewallapi.dll,-53425
  ApplicationName      : %systemroot%\ADWS\Microsoft.ActiveDirectory.WebServices.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :9389
  Remote Addr:Port     : :

  Name                 : @%SystemRoot%\system32\firewallapi.dll,-53429
  Description          : @%SystemRoot%\system32\firewallapi.dll,-53428
  ApplicationName      : %systemroot%\ADWS\Microsoft.ActiveDirectory.WebServices.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37635
  Description          : @FirewallAPI.dll,-37613
  ApplicationName      : %systemroot%\System32\lsass.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37636
  Description          : @FirewallAPI.dll,-37614
  ApplicationName      : %systemroot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC-EPMap
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37602
  Description          : @FirewallAPI.dll,-37615
  ApplicationName      : %systemroot%\System32\lsass.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :389
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37603
  Description          : @FirewallAPI.dll,-37616
  ApplicationName      : %systemroot%\System32\lsass.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :389
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37604
  Description          : @FirewallAPI.dll,-37617
  ApplicationName      : %systemroot%\System32\lsass.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :636
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37605
  Description          : @FirewallAPI.dll,-37618
  ApplicationName      : %systemroot%\System32\lsass.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :3268
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37606
  Description          : @FirewallAPI.dll,-37619
  ApplicationName      : %systemroot%\System32\lsass.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :3269
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37607
  Description          : @FirewallAPI.dll,-37620
  ApplicationName      : %systemroot%\System32\lsass.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37608
  Description          : @FirewallAPI.dll,-37621
  ApplicationName      : %systemroot%\System32\lsass.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37609
  Description          : @FirewallAPI.dll,-37622
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :445
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37610
  Description          : @FirewallAPI.dll,-37623
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :445
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37611
  Description          : @FirewallAPI.dll,-37624
  ApplicationName      : System
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :138
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37612
  Description          : @FirewallAPI.dll,-37625
  ApplicationName      : %systemroot%\System32\svchost.exe
  Protocol             : UDP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :123
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37627
  Description          : @FirewallAPI.dll,-37628
  ApplicationName      : System
  Protocol             : ICMP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37629
  Description          : @FirewallAPI.dll,-37630
  ApplicationName      : System
  Protocol             : ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37631
  Description          : @FirewallAPI.dll,-37632
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37633
  Description          : @FirewallAPI.dll,-37634
  ApplicationName      : System
  Protocol             : IPv6-ICMP
  Action               : Allow
  Direction            : Out
  Profiles             : 
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37803
  Description          : @FirewallAPI.dll,-37804
  ApplicationName      : %systemroot%\system32\dfsfrsHost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37805
  Description          : @FirewallAPI.dll,-37806
  ApplicationName      : %systemroot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :135
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37807
  Description          : @FirewallAPI.dll,-37808
  ApplicationName      : %systemroot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37809
  Description          : @FirewallAPI.dll,-37810
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :445
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37703
  Description          : @FirewallAPI.dll,-37704
  ApplicationName      : %SystemRoot%\system32\dfsrs.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @FirewallAPI.dll,-37705
  Description          : @FirewallAPI.dll,-37706
  ApplicationName      : %systemroot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC-EPMap
  Remote Addr:Port     : :

  Name                 : @Kdssvc.dll,-1002
  Description          : @KdsSvc.dll,-1003
  ApplicationName      : %systemroot%\system32\lsass.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC-EPMap
  Remote Addr:Port     : :

  Name                 : @Kdssvc.dll,-1005
  Description          : @kdssvc.dll,-1006
  ApplicationName      : %systemroot%\system32\lsass.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @fssmres.dll,-101
  Description          : @fssmres.dll,-102
  ApplicationName      : %systemroot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :RPC
  Remote Addr:Port     : :

  Name                 : @fssmres.dll,-103
  Description          : @fssmres.dll,-104
  ApplicationName      : %systemroot%\system32\svchost.exe
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :135
  Remote Addr:Port     : :

  Name                 : @fssmres.dll,-105
  Description          : @fssmres.dll,-106
  ApplicationName      : System
  Protocol             : TCP
  Action               : Allow
  Direction            : In
  Profiles             : 
  Local Addr:Port      : :445
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  Description          : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  Description          : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  Description          : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  Description          : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.ShellExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.ShellExperienceHost/resources/PkgDisplayName}
  Description          : @{Microsoft.Windows.ShellExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.ShellExperienceHost/resources/PkgDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.SecHealthUI_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.SecHealthUI/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.SecHealthUI_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.SecHealthUI/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.PeopleExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.PeopleExperienceHost/resources/PkgDisplayName}
  Description          : @{Microsoft.Windows.PeopleExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.PeopleExperienceHost/resources/PkgDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.OOBENetworkCaptivePortal_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.OOBENetworkCaptivePortal/Resources/AppDisplayName}
  Description          : @{Microsoft.Windows.OOBENetworkCaptivePortal_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.OOBENetworkCaptivePortal/Resources/AppDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.NarratorQuickStart_10.0.17763.1_neutral_neutral_8wekyb3d8bbwe?ms-resource://Microsoft.Windows.NarratorQuickStart/Resources/AppDisplayName}
  Description          : @{Microsoft.Windows.NarratorQuickStart_10.0.17763.1_neutral_neutral_8wekyb3d8bbwe?ms-resource://Microsoft.Windows.NarratorQuickStart/Resources/AppDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Apprep.ChxApp_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Apprep.ChxApp/resources/DisplayName}
  Description          : @{Microsoft.Windows.Apprep.ChxApp_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Apprep.ChxApp/resources/DisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/DisplayName}
  Description          : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/Description}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/DisplayName}
  Description          : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/Description}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.LockApp_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.LockApp/resources/AppDisplayName}
  Description          : @{Microsoft.LockApp_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.LockApp/resources/AppDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AccountsControl_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.AccountsControl/Resources/DisplayName}
  Description          : @{Microsoft.AccountsControl_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.AccountsControl/Resources/DisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : Shell Input Application
  Description          : Shell Input Application
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  Description          : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  Description          : @{Microsoft.Windows.CloudExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.CloudExperienceHost/resources/appDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  Description          : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  Description          : @{Microsoft.AAD.BrokerPlugin_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.AAD.BrokerPlugin/resources/PackageDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.ShellExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.ShellExperienceHost/resources/PkgDisplayName}
  Description          : @{Microsoft.Windows.ShellExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.ShellExperienceHost/resources/PkgDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.Cortana_1.11.6.17763_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Cortana/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Private
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.SecHealthUI_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.SecHealthUI/resources/PackageDisplayName}
  Description          : @{Microsoft.Windows.SecHealthUI_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.SecHealthUI/resources/ProductDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.PeopleExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.PeopleExperienceHost/resources/PkgDisplayName}
  Description          : @{Microsoft.Windows.PeopleExperienceHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.PeopleExperienceHost/resources/PkgDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.OOBENetworkCaptivePortal_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.OOBENetworkCaptivePortal/Resources/AppDisplayName}
  Description          : @{Microsoft.Windows.OOBENetworkCaptivePortal_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.Windows.OOBENetworkCaptivePortal/Resources/AppDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.NarratorQuickStart_10.0.17763.1_neutral_neutral_8wekyb3d8bbwe?ms-resource://Microsoft.Windows.NarratorQuickStart/Resources/AppDisplayName}
  Description          : @{Microsoft.Windows.NarratorQuickStart_10.0.17763.1_neutral_neutral_8wekyb3d8bbwe?ms-resource://Microsoft.Windows.NarratorQuickStart/Resources/AppDescription}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Windows.Apprep.ChxApp_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Apprep.ChxApp/resources/DisplayName}
  Description          : @{Microsoft.Windows.Apprep.ChxApp_1000.17763.1.0_neutral_neutral_cw5n1h2txyewy?ms-resource://Microsoft.Windows.Apprep.ChxApp/resources/DisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/DisplayName}
  Description          : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/Description}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/DisplayName}
  Description          : @{Microsoft.Win32WebViewHost_10.0.17763.1_neutral_neutral_cw5n1h2txyewy?ms-resource://Windows.Win32WebViewHost/resources/Description}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : In
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.LockApp_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.LockApp/resources/AppDisplayName}
  Description          : @{Microsoft.LockApp_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.LockApp/resources/AppDisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : @{Microsoft.AccountsControl_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.AccountsControl/Resources/DisplayName}
  Description          : @{Microsoft.AccountsControl_10.0.17763.1_neutral__cw5n1h2txyewy?ms-resource://Microsoft.AccountsControl/Resources/DisplayName}
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

  Name                 : Shell Input Application
  Description          : Shell Input Application
  ApplicationName      : 
  Protocol             : 
  Action               : Allow
  Direction            : Out
  Profiles             : Public
  Local Addr:Port      : :
  Remote Addr:Port     : :

====== WindowsVault ======

ERROR: Unable to enumerate vaults. Error code: 1061
====== WMIEventConsumer ======

ERROR:   [!] Terminating exception running command 'WMIEventConsumer': System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObject.Initialize(Boolean getObject)
   at System.Management.ManagementClass.GetInstances(EnumerationOptions options)
   at S34tb3lt.Commands.Windows.WMIEventConsumerCommand.<Execute>d__9.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== WMIEventFilter ======

ERROR:   [!] Terminating exception running command 'WMIEventFilter': System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObject.Initialize(Boolean getObject)
   at System.Management.ManagementClass.GetInstances(EnumerationOptions options)
   at S34tb3lt.Commands.Windows.WMIEventFilterCommand.<Execute>d__9.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== WMIFilterBinding ======

ERROR:   [!] Terminating exception running command 'WMIFilterBinding': System.Management.ManagementException: Access denied 
   at System.Management.ThreadDispatch.Start()
   at System.Management.ManagementScope.Initialize()
   at System.Management.ManagementObject.Initialize(Boolean getObject)
   at System.Management.ManagementClass.GetInstances(EnumerationOptions options)
   at S34tb3lt.Commands.Windows.WMIFilterToConsumerBindingCommand.<Execute>d__9.MoveNext()
   at S34tb3lt.Runtime.ExecuteCommand(CommandBase command, String[] commandArgs)
====== WSUS ======

  UseWUServer                    : False
  Server                         : 
  AlternateServer                : 
  StatisticsServer               : 



[*] Completed collection in 145.595 seconds
```