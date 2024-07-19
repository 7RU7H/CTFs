# Dead-End Helped-Thorough

Name: Dead-End
Date:  
Difficulty:  Hard
Goals:  
- More Windows in my life 
- Testing, Tooling and Labs
- Volatility a desperate return to a rough last use - Python problems
Learnt:
- More registry familiarity
- I really do not like Windows Event viewer
Beyond Root:
- [[Squid-Helped-through]]

- [[Dead-End-Notes.md]]
- [[Dead-End-CMD-by-CMDs.md]]

This Writeup will be split into a just bumbling of how I finished this then the answers at the end. 

## Journey to the end of Dead End?

1. Along with the usual silencing of the AttackBox - `System` -> `Preferences` -> `Hardware` -> `Sound` -> Drop down to silence one of the many horrible sounds on this planet.
2. Increase scrolling just in case: 
`Right-Click` Terminal -> `Profiles` -> `Profile Preferences` ->`Scrolling` -> `UNLIMITED SCROLLING`   

Also `tee` to a file because you finger will not be arthritic and keep the brain fibriously-bran-down the what do I need to find 
```bash
python3 vol.py -f ../RobertMemdump/memdump.mem windows.filescan | tee -a ../fs.txt
```

I went for the old what is not in system32 that exe and rhymes with deranged. 
```bash
cat ../fs.txt | grep '.exe' | grep -v '32'
```
## Answers

#### Memory

What binary gives the most apparent sign of suspicious activity in the given memory image?

Use the full path of the artefact.
```
C:\Tools\svchost.exe
```
The answer above shares the same parent process with another binary that references a .txt file - what is the full path of this .txt file?
```
C:\Users\Bobby\Documents\tmp\part2.txt
```

#### Disk

We [have Troy...Bobby's disk](https://www.youtube.com/watch?v=evuCz5wDESs)

Got hung up on, but learnt about;
- `$130` files in Downloads is a index file; they are everywhere
- rasphone.pbk - I really want the meme about Bill gates on the phone to Ballmer, which I could not remember how funny or the content, ...
- Nothing in `%systemroot%\System32\Tasks` for whether it is downloaded or created as scheduled task

- To me part2.txt is either a file name, arg key to run or a decryption key for the malware source code
- NOTEPAD.exe is not system32 


But in DFIR things this will do [What is love](https://www.youtube.com/watch?v=6XuizTRqEsw), when there is no Autospy. I got a hint from the https://www.youtube.com/watch?v=4iAYaDRWT9A thumbnail which resolved my confusion (very CTFy make a directory in C:\\)regarding the Tools directory, with svchost.exe in it and that not be the suspicious thing...


What binary gives the most apparent sign of suspicious activity in the given disk image? Use the full path of the artefact:
```powershell
# 
C:\Tools\windows-networking-tools-master\windows-networking-tools-master\LatestBuilds\x64\Autoconnector.exe
```

![](BAM.png)

![](samebamnotautoconnect.png)

Took another hint from the thumbnail; event logs then I skipped to the point where he uses the finder in RegExplorer, which I had forgotten about
- https://www.youtube.com/watch?v=4iAYaDRWT9A

https://www.reddit.com/r/computerforensics/comments/k8go5m/event_logs_from_forensic_disk_image/
```powershell
C:\Windows\System32\winevt\Logs

SYSTEM\CurrentControlSet\Services\dam\UserSettings\{SID}
```

![](findthings.png)


What is the full registry path where the existence of the binary above is confirmed?
```powershell
# SYSTEM\ControlSet001\Control\Session Manager\AppCompatCache\AppCompatCache\49
# HKEY_LOCAL_MACHINE\SYSTEM\ControlSet001\Control\Session Manager\AppCompatCache\AppCompatCache
```

What is the content of "Part2"?
```
faDB3XzJfcDF2T1R9  
```

What is the flag?
```

```
## Post-Root-Reflection  

I really do not like Windows Event Viewer.

Virus Total artefacts regardless

My registry extraction did not have svcnotes so I must have done something wrong
![](nosvcnotes.png)

What is the full registry path where the existence of the binary above is confirmed?
```powershell
# SYSTEM\ControlSet001\Control\Session Manager\AppCompatCache\AppCompatCache\49
# HKEY_LOCAL_MACHINE\SYSTEM\ControlSet001\Control\Session Manager\AppCompatCache\AppCompatCache
```

What is the content of "Part2"?
```
faDB3XzJfcDF2T1R9  
```

What is the flag?
```

```

## Beyond Root

https://www.youtube.com/watch?v=4iAYaDRWT9A https://www.youtube.com/@djalilayed

https://blog.onfvp.com/post/volatility-cheatsheet/ of https://github.com/onfvp