# Memory-Forensics Writeup

Name: Memory-Forensics
Date:  
Difficulty: Easy  
Goals:  
- Review my volatity cheatsheet 
- DFIR CTF FTW
Learnt:


[Room](https://tryhackme.com/room/memoryforensics)

https://book.hacktricks.xyz/generic-methodologies-and-resources/basic-forensic-methodology/memory-dump-analysis/volatility-cheatsheet#hashes-passwords

https://blog.cyberhacktics.com/carving-files-from-memory-with-volatility/

## Snapshot 6

```bash
# OS info
sudo python3 vol.py -f ~/Downloads/Snapshot6.vmem windows.info
```
Output
```powershell
Variable        Value

Kernel Base     0xf80002a59000
DTB     0x187000
Symbols file:///opt/volatility3/volatility3/symbols/windows/ntkrnlmp.pdb/3844DBB920174967BE7AA4A2C20430FA-2.json.xz
Is64Bit True
IsPAE   False
layer_name      0 WindowsIntel32e
memory_layer    1 FileLayer
KdDebuggerDataBlock     0xf80002c4a0a0
NTBuildLab      7601.17514.amd64fre.win7sp1_rtm.
CSDVersion      1
KdVersionBlock  0xf80002c4a068
Major/Minor     15.7601
MachineType     34404
KeNumberProcessors      1
SystemTime      2020-12-27 06:20:05
NtSystemRoot    C:\Windows
NtProductType   NtProductWinNt
NtMajorVersion  6
NtMinorVersion  1
PE MajorOperatingSystemVersion  6
PE MinorOperatingSystemVersion  1
PE Machine      34404
PE TimeDateStamp        Sat Nov 20 09:30:02 2010
```

## Snapshot

## Snapshot 