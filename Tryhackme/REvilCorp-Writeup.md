Name:
Date: FIND!
Difficulty:
Description: You are involved in an incident response engagement and need to analyze an infected host using Redline.
Better Description: N/A
Goals: Finish this and the Cyber Defense path room
Learnt: Redline related 


What is the compromised employee's full name?

System Information: 
John Coleman

What is the operating system of the compromised host?

System Information:
Windows 7 Home Premium 7601 Service Pack 1


What is the name of the malicious executable that the user opened?

File Download History:
WinRAR201.exe

What is the full URL that the user visited to download the malicious binary? (include the binary as well)

File Download History:
//remove !! for answer
http!!://192.168.75.129:4748/Documents/WinRAR2021.exe

What is the MD5 hash of the binary?

File System \Downloads\...
890a58f200dfff23165df9e1b088e58f

What is the size of the binary in kilobytes?

DETAILS!
164

What is the extension to which the user's files got renamed?

File System
..CreditCardInfo.txt
.t48s39la

What is the number of files that got renamed and changed to that extension?

This and the last question I struggled with as I still very inexperienced.
I got too fixated with solvin this problem without external research.
I learnt to do more background or meta-cognitive tasking to researching.

used:
https://infosecwriteups.com/revil-incident-response-with-redline-fe7853699216
Filtering on answering is bad so:
C:\Users\John Coleman\AppData\Local\Temp\.bmp


Put the extension above in the search, untick fields for file created created 
48

What is the full path to the wallpaper that got changed by an attacker, including the image name? 

.bmp extension in search field 
Show only Events Associated with John Coleman

The attacker left a note for the user on the Desktop; provide the name of the note with the extension. 

t48s39la-readme.txt

The attacker created a folder "Links for United States" under C:\Users\John Coleman\Favorites\ and left a file there. Provide the name of the file. 

Remove some ticks from the John Coleman Home directory in filters
GobiernoUSA.gov.url.t48s39la

There is a hidden file that was created on the user's Desktop that has 0 bytes. Provide the name of the hidden file. 

d60dff40.lock

The user downloaded a decryptor hoping to decrypt all the files, but he failed. Provide the MD5 hash of the decryptor file. 

File System -> Only file that is 0 bytes in size
d60dff40.lock

File System -> d.e.c.r.yp.tor.exe -> show details(in blue, next to hide whitelist)
f617af8c0d276682fdf528bb3e72560b

In the ransomware note, the attacker provided a URL that is accessible through the normal browser in order to decrypt one of the encrypted files for free. The user attempted to visit it. Provide the full URL path. 

search for decrypt in URL browser history
//remove !! for points
http!!://decryptor.top/644E7C8EFA02FBB7

What are some three names associated with the malware which infected this host? (enter the names in alphabetical order)
The hint and question aren't great. Names "of family that the malware which infected this host"

https://infosecwriteups.com/revil-incident-response-with-redline-fe7853699216
Used Attack&Mitre 
https://attack.mitre.org/software/S0496/
REvil, Sodin, Sodinokibi
