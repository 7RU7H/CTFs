# Squid Game Meta Notes

## Pre

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

## Attacker 1 MalDoc

[[Squid-Game-Notes-Attacker-1]]
## Attacker 2 MalDoc

[[Squid-Game-Notes-Attacker-2]]