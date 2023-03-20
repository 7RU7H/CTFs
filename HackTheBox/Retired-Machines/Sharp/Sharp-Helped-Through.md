# Sharp Helped-Through

Name: Sharp
Date:  19/3/2023
Difficulty:  Hard
Goals:  
- Azure Revision 
- Follow along and push head were possible

Learnt:
- C\#  - BinaryFormatter is insecure 
Beyond Root:
- Turn Sharp into a Azure AD setup for [[PhotoBomb-Helped-Through]] and [[Watcher-Helped-Through]]
	- Governance Contextualization
	- Azure AD
- Finish off some of the most relevant [[BeyondRoot-Todo]]s

[Alh4zr3d](https://www.twitch.tv/alh4zr3d)

https://0xdf.gitlab.io/2021/05/01/htb-sharp.html#shell-as-system
https://www.youtube.com/watch?v=lxjAZELJ96Q
https://www.youtube.com/watch?v=HBERg8jsx4U

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Nmap port 8888:
- 8888/tcp open  storagecraft-image StorageCraft Image Manager
- 8888/tcp open  sun-answerbook
- 8888/tcp open  msexchange-logcopier Microsoft Exchange 2010 log copier

![](smbkaban.png)

https://en.wikipedia.org/wiki/Kanban
https://kanbanize.com/kanban-resources/getting-started/what-is-kanban
https://www.exploit-db.com/exploits/49409

Static key 

```bash
pip3 install des
```

![](goodpyscript.png)

Fine additions to my collection
```
Administrator:G2@$btRSHJYTarg
lars:G123HHrth234gRG
```

![](cmepasswordchecking.png)

![](sharesandipcfortheridbrute.png)


`debug : SharpApplicationDebugUserPassword123!` 

![](debugcreds.png)

We RPC enumerations - test

## Exploit


Redo the C\# solo at a later point once I have my nice reversing box setup again... 

Get [dnSpy 6.1.8](https://github.com/dnSpy/dnSpy/releases)

[BinaryFormatter](https://learn.microsoft.com/en-us/dotnet/api/system.runtime.serialization.formatters.binary.binaryformatter?view=net-7.0) is [deprecated](https://learn.microsoft.com/en-us/dotnet/standard/serialization/binaryformatter-security-guide)! `.Deserialize` is to deserialize payload and execute it!

https://github.com/tyranid/ExploitRemotingService - did not work for Alh4zr3d, https://github.com/parteeksingh005/ExploitRemotingService_Compiled

https://beebom.com/how-change-powershell-color-scheme-windows-10/

VSCELicense git



Kris Nova raid and I check her out wann watch this soon
https://www.youtube.com/watch?v=tUA3EyUM278

https://labs.withsecure.com/advisories/milestone-xprotect-net-deserialization-vulnerability

ysoserial.net - https://github.com/pwntester/ysoserial.net
0xTib3rious: `TypeConfuseDelegate`



## Foothold

Exploit Application

## PrivEsc

Create a user

## Beyond Root

Write  C\# called Progam.exe that pops a message box and says 0day found with some funny GUI stuff.
https://stackoverflow.com/questions/31930452/are-cortana-apis-available-for-desktop-applications

Azure Recontextualization AD 

