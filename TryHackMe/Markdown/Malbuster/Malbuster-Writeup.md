# Malbuster Writeup

Name: Malbuster
Date:  
Difficulty:  Medium
Goals:  
- Practice Malware Analysis and Writing
- Finish without explainantions as to the setup - they have not release the static analysis room with tools demonstration
Learnt:
- Use new tools and brain and accomplish much in 30 minutes.
Beyond Root:
- [Watch Alh4zr3d's The Black Magicks of Malware: Early-Bird QueueUserAPC Injection](https://www.youtube.com/watch?v=aMkMkkClXVc)
- Write some simple C based malware and detections for it

## Helpful information

- Synsinternals is on the box use it 

- Start -> Flare -> TOOLS 

Performing capa.exe provides nice overview of sample include ATT&CK Tactic - the blue on blue in powershell is facepalm
```powershell
capa.exe .\Samples\malbuster_1 
```

## Answers

Based on the ARCHITECTURE of the binary, is malbuster_1 a 32-bit or a 64-bit application? (32-bit/64-bit)

Solution
```powershell
sigcheck .\Samples\malbuster_1 
```

Answer
```
32-bit
```
 
What is the MD5 hash of malbuster_1?

Solution
```
md5sum malbuster_1
```

Answer
```
4348da65e4aeae6472c7f97d6dd8ad8f
```
 
Using the hash, what is the number of detections of malbuster_1 in VirusTotal?  

Solution
```
https://www.virustotal.com/gui/file/000415d1c7a7a838ba2ef00874e352e8b43a57e2f98539b5908803056f883176
```

Answer
```
51
```
 
Based on VirusTotal detection, what is the malware signature of malbuster_2 according to Avira?  

Solution
```
md5sum .\Samples\malbuster_2
1d7ebed1baece67a31ce0a17a0320cb2
https://www.virustotal.com/gui/file/ace3a5e5849c1c00760dfe67add397775f5946333357f5f8dee25cd4363e36b6
CTRL+F AVIRA
```

Answer
```
HEUR/AGEN.1202219
```
 
malbuster_2 imports the function _CorExeMain. From which DLL file does it import this function?  

Solution
```powershell
capa.exe .\Samples\malbuster_2 -vv
# the format for import calls dll._func
```

Answer
```
mscoree.dll
```
 
Based on the VS_VERSION_INFO header, what is the original name of malbuster_2?  

Solution
```powershell
PPEE -> File info -> InternlName: 
```

Answer
```
7JYpE.exe
```
 
Using the hash of malbuster_3, what is its malware signature based on abuse.ch?  

Solution
```powershell
md5sum .\Samples\malbuster_3
47ba62ce119f28a55f90243a4dd8d324
# MalwareBazaar Search syntax
md5:47ba62ce119f28a55f90243a4dd8d324

https://bazaar.abuse.ch/browse.php?search=md5%3A47ba62ce119f28a55f90243a4dd8d324
```

Answer
```
trickbot
```
 
Using the hash of malbuster_4, what is its malware signature based on abuse.ch?  

Solution
```powershell
md5sum .\Samples\malbuster_4
061057161259e3df7d12dccb363e56f9
# MalwareBazaar Search syntax
md5:061057161259e3df7d12dccb363e56f9
https://bazaar.abuse.ch/browse.php?search=md5%3A061057161259e3df7d12dccb363e56f9
```

Answer
```
ZLoader
```
 
What is the message found in the DOS_STUB of malbuster_4?

Solution
```
PPEE
Strings in file

```

Answer
```
!This Salfram cannot be run in DOS mode.
```
 
malbuster_4 imports the function ShellExecuteA. From which DLL file does it import this function?  

Solution
```
Search ShellExecuteA -> shell32.dll
```

Answer
```
shell32.dllv
```
 
Using capa, how many anti-VM instructions were identified in malbuster_1?

Solution
```
capa.exe .\Samples\malbuster_1
Virtual Machine Detection::Instruction Testing [B0009.029]                    |
Virtual Machine Detection [B0009]
Virtualization/Sandbox Evasion::System Checks [T1497.001]

```

Answer
```
3
```
 
Using capa, which binary can log keystrokes?  

Solution
```
capa all the files
```

Answer
```
malbuster_3
```
 
Using capa, what is the MITRE ID of the DISCOVERY technique used by malbuster_4?

Solution
```
As with the previous question capa.exe all the files is useful for overview
```

Answer
```
T1083
```
 
Which binary contains the string GodMode?  

Solution
```
strings .\Samples\malbuster_2 | findstr "GodMode"
```

Answer
```
malbuster_2
```
 
Which binary contains the string **Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)**?

Solution
```
strings .\Samples\malbuster_1 | findstr "Mozilla"
```

Answer
```
malbuster_1
```

## Beyond Root

[Notes from Al's Video](https://www.youtube.com/watch?v=aMkMkkClXVc)


## Malware Writing

C version
```c
```

Go version - because 
```
```


```
```