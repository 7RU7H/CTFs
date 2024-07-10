# Dead-End Writeup

Name: Dead-End
Date:  
Difficulty:  Hard
Goals:  
- More Windows in my life 
- Testing, Tooling and Labs
- Volatility a desperate return to a rough last use - Python problems
Learnt:
Beyond Root:

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


But in DFIR things this will do [What is love](https://www.youtube.com/watch?v=6XuizTRqEsw)


What binary gives the most apparent sign of suspicious activity in the given disk image?
```

```
Use the full path of the artefact.
```

```
What is the full registry path where the existence of the binary above is confirmed?
```

```
What is the content of "Part2"?
```
faDB3XzJfcDF2T1R9  
```
What is the flag?
```

```
## Post-Root-Reflection  

## Beyond Root


https://blog.onfvp.com/post/volatility-cheatsheet/ of https://github.com/onfvp