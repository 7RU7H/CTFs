# Warzone-One Writeup

Name: Warzone-One
Date:  26/01/2023
Difficulty:  Medium
Goals:  
- Improve my Wireshark, Brim,  Network Miner cheatsheets
- Malware C2 netowkr traffic detection
- Exposure to Brim and Network miner through hints; just click around
Learnt:
- Brim is ok for contextualising 
Beyond Root:
- Just improve the cheatsheet

This CTF really wants you to use BRIM, but I think it is best generally to try replicate in Wireshark for the packet analysis as it is easy to lose track of "what and how" of data of these attacks if spend your time in these SIEMs. 

What was the alert signature for **Malware Command and Control Activity Detected**?

Solution:
```c
# Brim
`Suricata Alerts by Category` 
# There is C2 detected, it will beacon somewhere find src ip to make transvering main page easier
`Suricata Alerts by Source and Destination` 

```
Answer:
```c
ET MALWARE MirrorBlast CnC Activity M3
```
 
What is the source IP address? Enter your answer in a **defanged** format. 

Solution:
```c
# Brim
`Suricata Alerts by Source and Destination` 
```
Answer:
```c
172[.]16[.]1[.]102
```
 
What IP address was the destination IP in the alert? Enter your answer in a **defanged** format. 

Solution:
```c
# Brim
`Suricata Alerts by Source and Destination` 
```
Answer:
```c
169[.]239[.]128[.]11
```
 
Inspect the IP address in VirsusTotal. Under **Relations > Passive DNS Replication**, which domain has the most detections? Enter your answer in a **defanged** format. 

Solution:

```
# GoTo Virus Total -> Relations -> Passive DNS Replication
```
Answer:
```
fidufagios[.]com
```
 
Still in VirusTotal, under **Community**, what threat group is attributed to this IP address?

Solution:
```
VirtusTotal previous search -> Community Tab
```
Answer:
```
TA505
```
 
What is the malware family?

Solution:
```c
// Either Virustotal, the Suricata signature
```
Answer:
```
mirrorblast
```
 
Do a search in VirusTotal for the domain from question 4. What was the majority file type listed under **Communicating Files**?

Solution:
```c
//VirusTotal Serach for: fidufagios[.]com
// Relations Tab -> Communicating Files
```
Answer:
```
Windows Installer
```
 
Inspect the web traffic for the flagged IP address; what is the **user-agent** in the traffic?

Solution:
```c
# Wireshark
http.user_agent
# Open a packet from the address beginning 169 -> Hypertext Transfer Protocol -> User Agent
```
Answer:
```
REBOL View 2.7.8.3.1
```

 
Retrace the attack; there were multiple IP addresses associated with this attack. What were two other IP addresses? Enter the IP addressed **defanged** and in numerical order. (**format: IPADDR,IPADDR**)

Solution:
```c
# You do not need anything from the hint, just some what is normal and would the attacker do x 
# Brim:
# _path=="http"` -> Scroll left and do the same as below, but it is not as informative 

# Do not use the hint is bad - Wireshark is your friend
Packets: 762 is the filter.msi and packet 769 ius 10opd3r_load.msi
# The defanged IP sends a x-msi a microsoft installion executable
# Ask yourself why in all that is the internet would oyu send a microsoft installer over the wire to a webserver
# Alarms bells should be ringing - also even metapsloit's msfvenom has builtin msi 
185[.]10[.]68[.]235
192[.]36[.]27[.]92
# Inspect the packets!
# Saves loads of time on this CTF
```
Answer:
```
185[.]10[.]68[.]235,192[.]36[.]27[.]92
```
 
What were the file names of the downloaded files? Enter the answer in the order to the IP addresses from the previous question. (**format: file.xyz,file.xyz**)

Solution:
```c
# Again Wireshark is awesome for those that understand that packets are Locard principle you can never scrub unless you have the Wiper or Sugergical Logs Capability
# If you are using Wireshark this is easy
Packet 762 - filter.msi # That should be a massive ALARM BELLS 
Packet 769 - 10opd3r_load.msi
```
Answer:
```
 filter.msi,10opd3r_load.msi
```

Inspect the traffic for the first downloaded file from the previous question. Two files will be saved to the same directory. What is the full file path of the directory and the name of the two files? (**format: C:\path\file.xyz,C:\path\file.xyz**)

Solution:
```c
# I was look at this like HOW and Why are the files names 
# ChatGPT was no help lol
# Use wireshark
Follow HTTP stream -> Find: "C:\"
# C:\ProgramData\001\arab.bin is the first one
# Deduction: the "001yhcj5x6n|CommonAppDataFoldert19mu-pt|" are obsucated strings
# The other is named arab.exe where the former is used maybe to bypass a signature on extension
```
Answer:

```
C:\ProgramData\001\arab.exe, C:\ProgramData\001\arab.bin
```
 
Now do the same and inspect the traffic from the second downloaded file. Two files will be saved to the same directory. What is the full file path of the directory and the name of the two files? (format: C:\path\file.xyz,C:\path\file.xyz)

Solution:
```
Same as the above, .rb is ruby extension
```
Answer:
```
C:\ProgramData\Local\Google\rebol-view-278-3-1.exe, C:\ProgramData\Local\Google\exemple.rb
```

