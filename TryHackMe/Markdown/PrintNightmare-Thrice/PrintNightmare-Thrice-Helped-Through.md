# PrintNightmare-Thrice Helped-Through

Name: Print Nightmare Thrice!
Date:  13/2/2024
Difficulty:  Medium
Goals:  
- Continue finishing the unfinished
Learnt:
- Forced Event Viewer lessons
Beyond Root: 
- In reflection I need to learn more about system32 directory so will make that a task for the next couple weeks to get done


- [[PrintNightmare-Thrice-Notes.md]]
- [[PrintNightmare-Thrice-CMD-by-CMDs.md]]


To finish the old and abandoned I followed along with [Ren](https://medium.com/@renbe/tryhackme-printnightmare-thrice-754480df3a49)

#### What remote address did the employee navigate to?

Working out...
```c
// I just scroll, but in hindsight either 
Statists -> Ipv4 Address, toggle count tab:
```

![](2018856147.png)


Answer
```c
20.188.56.147
```


Per the PCAP, which user returns a STATUS_LOGON_FAILURE error?
![](rjonesisnottheanswer.png)

Working out...
```c
ip.addr == 20.188.56.147 and smb2.auth_frame
A better answer is from Ren:
smb2.nt_status == 0x0
To find Session Setup Response that is Successful we need to use the Display Filter Expressions
```

Answer
```c
THM-PRINTNIGHT0\rjones
```

#### This is were I had trouble


Not being very SOC trained in any meaningful sense so hopeful I will learn a lot from [Ren](https://medium.com/@renbe/tryhackme-printnightmare-thrice-754480df3a49). Also I did not have time to learn Brim in the last couple of years. Anything in italics is Ren

###### Which user successfully connects to an SMB share?

Working out... 
```
 _path=smb_mapping OR _path=dce_rpc | sort ts
```

*After that’s done, we’ll want to filter for **smb_mapping** and **dce_rpc**, we want to include DCE/RPC because SMB uses the protocol and we’ll also want to sort by time.* What I did not understand is the `endpoint` in brim means file and needed [Djalil Ayed](https://www.youtube.com/@djalilayed) with his [Writeup to check](https://www.youtube.com/watch?v=mjKlDjYW4ow)
Answer
```c
\\printnightmare.gentilkiwi.com\IPC$,srvsrv,spoolss
```

#### What is the first remote SMB share the endpoint connected to? What was the first filename? What was the second? (**format**: answer,answer,answer)

I accidently pressed this to not knowing about brim and its `endpoint`s
![](fileactivitybutton.png)

Working out...
```
It is the second smb_mapping + the two file incldding the path from file activity
```

Answer
```c
\\printnightmare.gentilkiwi.com\Print$,W32X86\3\mimispool.dll,x64\3\mimispool.dll
```

#### From which remote SMB share was malicious DLL obtained? What was the path to the remote folder for the first DLL? How about the second? (format: answer,answer,answer)

Working out... Wait till Event View loads everything! *in event log view when you start it first time it show only events from last 7 days, so from edit > advance options change drop down menu to show events from all times.*
![](solvingmyeventviewerwoes.png)
Otherwise you are only getting the last 7 days...

```
Search for  in Event Viewer: mimispool.dll 
```

Answer
```c
\\printnightmare.gentilkiwi.com\print$,\x64\3\mimispool.dll,\W32X86\3\mimispool.dll
```

#### What was the first location the malicious DLL was downloaded to on the endpoint? What was the second?

Working out...
```
Find: mimispool.dll
```

Answer - strip the /new/mimispool.dll if you are in Event Viewer, [Adamski](https://www.youtube.com/watch?v=TRVsnCnSZps) demonstrates with ProcessExplorer
```c
C:\Windows\System32\spool\drivers\x64\3\,C:\Windows\System32\spool\drivers\W32X86\3
```

#### What is the folder that has the name of the remote printer server the user connected to? (provide the full folder path)

Working out... as per the hint - but both writeup used file explorer, which I sometimes use as searching for things on Windows can be a nightmare.
![](question7.png)

Answer
```c
c:\Windows\System32\spool\SERVERS\printnightmare.gentilkiwi.com
```

#### What is the name of the printer the DLL added?

Working out...while searching for failing to find the answer to two questions ago I found this

![](fruitbasedprinting.png)
Answer
```c
Kiwi Legit Printer
```

Because learning Windows Event Viewer is a toxic experience I also learnt that you could 
![](ewtisfloodingback.png)
#### What was the process ID for the elevated command prompt? What was its parent process? (**format**: answer,answer)

![](question9.png)

Working out...
```c
Filter by:
Process Name = cmd.exe
Operation = Process Start // As we need the user that process is running at to proove it is elevated
Then filter by only PID: <parent PID> 
```

![](processstartedasntsystem.png)
Answer
```c
5408,spoolsv.exe
```

#### What command did the user perform to elevate privileges?

Working out...

I mistaken stayed within ProcessExplorer.exe
```c
Filter by:
Process Name = cmd.exe
PID = cmd.exe
```

Actually the answer with Event Viewer -> Find -> `ParentProcessId: <PID>`
![](initialrjonescmd.png)

Answer
```c
 net  localgroup administrators rjones /add
```
