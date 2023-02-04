

# Giddy-RunForWriteUp Notes

## Data 

IP: 10.129.96.140
OS: Windows 10.0.14393
Hostname:
Domain:  / Domain SID:
Machine Purpose: 
- Testing PowerShellWebAccess
Services:
- 443 - IIS -  PowerShellWebAccessTestWebSite
- 5985
- 3389
- 80 
Service Languages: 
Users:
Credentials:

## Objectives

## Solution Inventory Map

### Todo | Done
`--- Done` for copying

![](newping.png)

`PowerShellWebAccessTestWebSite`

![](testpowershell.png)

![](nmapunderstanding.png)

[[HackTheBox/Retired-Machines/Giddy/RunForWriteup/nuclei-80/rdp-detect-10.129.96.140_3389-win2016]]

Gobuster with common.txt
![](gbtotheremote.png)

![](furtherinspection.png)

I may have been spoiled/throw off looking for exploits for this version - writeup mentioned sqli, but no database

[Powershell WebAccess Documention](https://learn.microsoft.com/en-us/previous-versions/windows/it-pro/windows-server-2012-r2-and-2012/hh831611(v=ws.11) - powershell version 3 or 4

This should just be a persistence mechanism
```powershell
Install-WindowsFeature –Name WindowsPowerShellWebAccess -ComputerName <computer_name> -IncludeManagementTools -Restart
```
For offline VHD
```
Install-WindowsFeature –Name WindowsPowerShellWebAccess –VHD <path> -ComputerName <computer_name> -IncludeManagementTools -Restart
```

http://10.129.96.140/mvc/ is home page


![](mvc.png)

jquery 1.(7|8) - https://www.tenable.com/plugins/was/112432 XXS

Product.aspx?ProductSubCategoryId=`$INT`

Search.apx payloads
```
& whoami
'
```

![1080](sqliinjection.png)

```sql
[SqlException (0x80131904): Unclosed quotation mark after the character string ''. Incorrect syntax near ''.]    System.Data.SqlClient.SqlConnection.OnError(SqlException exception, Boolean breakConnection, Action`1 wrapCloseInAction) +3180428    System.Data.SqlClient.TdsParser.ThrowExceptionAndWarning(TdsParserStateObject stateObj, Boolean callerHasConnectionLock, Boolean asyncClose) +332    System.Data.SqlClient.TdsParser.TryRun(RunBehavior runBehavior, SqlCommand cmdHandler, SqlDataReader dataStream, BulkCopySimpleResultSet bulkCopyHandler, TdsParserStateObject stateObj, Boolean& dataReady) +4224    System.Data.SqlClient.SqlDataReader.TryConsumeMetaData() +87    System.Data.SqlClient.SqlDataReader.get_MetaData() +99    System.Data.SqlClient.SqlCommand.FinishExecuteReader(SqlDataReader ds, RunBehavior runBehavior, String resetOptionsString, Boolean isInternal, Boolean forDescribeParameterEncryption) +584    System.Data.SqlClient.SqlCommand.RunExecuteReaderTds(CommandBehavior cmdBehavior, RunBehavior runBehavior, Boolean returnStream, Boolean async, Int32 timeout, Task& task, Boolean asyncWrite, Boolean inRetry, SqlDataReader ds, Boolean describeParameterEncryptionRequest) +3069    System.Data.SqlClient.SqlCommand.RunExecuteReader(CommandBehavior cmdBehavior, RunBehavior runBehavior, Boolean returnStream, String method, TaskCompletionSource`1 completion, Int32 timeout, Task& task, Boolean& usedCache, Boolean asyncWrite, Boolean inRetry) +674    System.Data.SqlClient.SqlCommand.RunExecuteReader(CommandBehavior cmdBehavior, RunBehavior runBehavior, Boolean returnStream, String method) +83    System.Data.SqlClient.SqlCommand.ExecuteReader(CommandBehavior behavior, String method) +301    System.Data.SqlClient.SqlCommand.ExecuteReader() +137    _1_Injection.Search.Button1_Click(Object sender, EventArgs e) in C:\Users\jnogueira\Downloads\owasp10\1-owasp-top10-m1-injection-exercise-files\before\1-Injection\Search.aspx.cs:30    System.Web.UI.WebControls.Button.OnClick(EventArgs e) +11764989    System.Web.UI.WebControls.Button.RaisePostBackEvent(String eventArgument) +150    System.Web.UI.Page.ProcessRequestMain(Boolean includeStagesBeforeAsyncPoint, Boolean includeStagesAfterAsyncPoint) +1665   ``
```

Easy to miss - jnogueira is practicing injection techniques
```powershell
C:\Users\jnogueira\Downloads\owasp10\1-owasp-top10-m1-injection-exercise-files\before\1-Injection\Search.aspx.cs:30  
```

In Burpsuite
![1080](bigrequest.png)

```sql
' --
```
Returning the database
![](andcommentreturnseverything.png)