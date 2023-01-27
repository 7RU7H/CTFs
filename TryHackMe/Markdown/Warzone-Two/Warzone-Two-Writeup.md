# Warzone-Two Writeup

Name: Warzone-Two
Date:  27/01/2023
Difficulty:  Medium
Goals:  
- Complete in under an hour and half
- Apparently it is *the common best practice is handling medium-sized pcaps with Wireshark, creating logs and correlating events with Zeek, and processing multiple logs in Brim.* - But by the [chart](https://tryhackme.com/room/brim) - why not just use only Zeek and Wireshark. - Possiblely because it is easier to train so look into Zeek for the future and try to use Wireshark as much as possible. 
Learnt:

Beyond Root:
- Improve the cheatsheets

What was the alert signature for **A Network Trojan was Detected**?

Solution
```c
event_type=="alert" and scroll right 
```
Answer
```
ET MALWARE Likely Evil EXE download from MSXMLHTTP non-exe extension M2
```
 
What was the alert signature for **Potential Corporate Privacy Violation**?

Solution
```c
event_type=="alert" and scroll right 
```
Answer
```
ET POLICY PE EXE or DLL Windows file download HTTP
```
 
What was the IP to trigger either alert? Enter your answer in a **defanged** format. 

Solution
```c
event_type=="alert" -> src ip field 
```
Answer
```
185[.]118[.]164[.]8
```
 
Provide the full URI for the malicious downloaded file. In your answer, **defang** the URI. 

Solution
```c
// Beware type into wireshark manually with the defang
ip.addr == 185[.]118[.]164[.]8  && http
packet 6: -> HTTP packet section -> Full request URI -> defang with Cyberchef
// The answer just wants escaped dots and http
```
Answer
```
hxxp://awh93dhkylps5ulnq-be[.]com/czwih/fxla[.]php?l=gap1[.]cab
```
 
What is the name of the payload within the cab file? 

Solution
```c
packet 6: -> Right click ->  follow tcp stream -> File -> Export Object -> HTTP -> save to a directory
# from cli
cd <directoryYouSaveTheExportedObjects>;
md5sum *.cab 
# Go to VirusTotal -> input hash -> under the 3769a84dbe7ba74ad7b0b355a864483d3562888a67806082ff094a56ce73bf7e is the answer
```
Answer
```
draw.dll
```
 
What is the user-agent associated with this network traffic?

Solution
```c
packet 6: -> HTTP packet section -> User Agent
```
Answer
```
Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 10.0; WOW64; Trident/8.0; .NET4.0C; .NET4.0E)
```
 
What other domains do you see in the network traffic that are labelled as malicious by VirusTotal? Enter the domains **defanged** and in alphabetical order. (**format: domain[.]zzz,domain[.]zzz**)

Solution
```c
# Solve the previous question; plus in brim:
_path=="dns" | count() by query | sort -r
# look for the patterns 
```
Answer
```
a-zcorner[.]com,knockoutlights[.]com
```
 
There are IP addresses flagged as **Not Suspicious Traffic**. What are the IP addresses? Enter your answer in numerical order and **defanged**. (format: IPADDR,IPADDR)

Solution
```c
event_type=="alert" | alerts := union(alert.category) by src_ip, dest_ip
```
Answer
```
64[.]225[.]65[.]166,142[.]93[.]211[.]176
```
 
For the first IP address flagged as Not Suspicious Traffic. According to VirusTotal, there are several domains associated with this one IP address that was flagged as malicious. What were the domains you spotted in the network traffic associated with this IP address? Enter your answer in a **defanged** format. Enter your answer in alphabetical order, in a defanged format. (**format: domain[.]zzz,domain[.]zzz,etc**)  

Solution
```c
# Lost ten minues from THM accept an incorrect answer...
# from previous solutions
# VirtusTotal and defang with Cyberchef
a-zcorner[.]com
d0d0abee1d18255e[.]com
d0d0f3d189430[.]com
knockoutlights[.]com
msnbot-207-46-194-33[.]search[.]msn[.]com
# Brim
_path=="dns" | count() by query | sort -r
# VirusTotal and defang with Cyberchef
nl-1[.]nodes[.]skey[.]network
fridomcoin[.]com
dagynalch[.]pw
antivarevare[.]top
ulcertification[.]xyz - found
tocsicambar[.]xyz - found
safebanktest[.]top - found
ns2[.]parcellsafebox[.]com
ns1[.]parcellsafebox[.]com
cadinstitute[.]com
```
Answer
```
safebanktest[.]top,tocsicambar[.]xyz,ulcertification[.]xyz
```
 
Now for the second IP marked as Not Suspicious Traffic. What was the domain you spotted in the network traffic associated with this IP address? Enter your answer in a **defanged** format. (format: domain[.]zzz)

Solution
```c
# VirtusTotal and defang with Cyberchef
# there are alot of false falsities
# there are few of false positives
# by process of what looked weird and elimination
_path=="dns" | count() by query | sort -r
```

Answer
```
2partscow[.]top
```

Conclusion Brim is useful. I will consider how so once I try Zeek.