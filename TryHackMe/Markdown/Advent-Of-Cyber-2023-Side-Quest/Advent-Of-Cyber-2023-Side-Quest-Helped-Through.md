# Advent-Of-Cyber-2023-Side-Quest Through=

Name: Advent-Of-Cyber-2023-Side-Quest
Date:  
Difficulty: Insane  
Goals:  
- Learn from the amazing people that actually did this in few weeks available
- Points of the THM eternal point maw
- A reason to fix cheatsheets
- A reason to learn more OSINT and fix my paralysis in some areas
- How to approach these types of puzzle
- Every piece of new information I learn I provide a bonus  one up or alternative or something helpful too
Learnt:
Beyond Root:
- Read all the writeups and learn all the things

- [[Advent-Of-Cyber-2023-Side-Quest-Notes.md]]
- [[Advent-Of-Cyber-2023-Side-Quest-CMD-by-CMDs.md]]

During December of 2023 I realised I could not do this puzzle. The rabbithole would ruin my plans and figuring out what I needed to do. I really wanted to do it, but I also did not have a team. The only thing I did was type:

Yeti, Bandit and Advent in the search terms to find three of the rooms:

[Side Quest Room](https://tryhackme.com/room/adventofcyber23sidequest)
[Day 1](https://tryhackme.com/room/adv3nt0fdbopsjcap)
[Day 2](https://tryhackme.com/room/armageddon2r)
[Day 3](https://tryhackme.com/room/busyvimfrosteau)
https://tryhackme.com/room/surfingyetiiscomingtotown


## Writeups Used and Profiles Listed

Profile names as links to Writeup - What I learnt from them
- [bakiba](https://github.com/bakiba/AOC-2023-SideQuest/tree/main/First%20Side%20Quest%20-%20The%20Story), [nouman404](https://nouman404.github.io/CTFs/TryHackMe/AdventOfCyber2023/SideQuest_Day1) and [pL4sTiC1337](https://github.com/pL4sTiC1337/ctf-writeups/blob/main/thm/aoc2023-sidequests/returnoftheyeti.md) used [https://github.com/GoSecure/pyrdp](https://github.com/GoSecure/pyrdp) - amazing tool!
- [nouman404](https://nouman404.github.io/CTFs/TryHackMe/AdventOfCyber2023/SideQuest_Day1) for 
	- `aircrack-ng` alternative with hashcat from [cyberark](https://www.cyberark.com/resources/threat-research-blog/cracking-wifi-at-scale-with-one-simple-trick)
- [0xb0b](https://0xb0b.gitbook.io/writeups/tryhackme/2023/advent-of-cyber-23-side-quest/the-return-of-the-yeti), for `aircrack-ng` to crack wifi passwords we need a .pcap format
- [Gh05t-1337](https://github.com/Gh05t-1337/CTF-Writeups/tree/main/TryHackMe/AoC23%20Sidequest/1%20The%20Return%20of%20the%20Yeti), decrypting using Wireshark
-  [0xb0b](https://0xb0b.gitbook.io/writeups/tryhackme/2023/advent-of-cyber-23-side-quest/frosteau-busy-with-vim#getting-to-sq3-the-qr-code): [Wikipedia - ACropalypse](https://en.wikipedia.org/wiki/ACropalypse) 
#### [The Return of the Yeti](https://tryhackme.com/room/adv3nt0fdbopsjcap)

I have never done any WIFI capture so I guessed the first answer:
![](tyoy-1.png)
What's the name of the WiFi network in the PCAP?
```
FreeWifiBFC
```

Now we need to look for a authentication packet. I have not done enough of this so time for learning. From [0xb0b](https://0xb0b.gitbook.io/writeups/tryhackme/2023/advent-of-cyber-23-side-quest/the-return-of-the-yeti), for `aircrack-ng` to crack wifi passwords we need a .pcap format

[Gh05t-1337](https://github.com/Gh05t-1337/CTF-Writeups/tree/main/TryHackMe/AoC23%20Sidequest/1%20The%20Return%20of%20the%20Yeti) points out we can do the same with WireShark
-  `Edit > Preferences > Protocols > IEEE 802.11, and Edit Decryption keys`

```bash
# Crack wifi passwords from pcap
aircrack-ng $file.pcap -w /usr/share/wordlists/rockyou.txt 
# Decrypt encrypted wifi traffic
airdecap-ng $file.pcap -e <SSID> -p <PASSWORD>
# Creates a $file-dec.pcap
```

![](keyfound.png)

![](decapng.png)
We get `VanSpy-dec.pcap` file to view in Wieshark with everything decrypted

![](toolanswer.png)

[unit42.paloaltonetwork decryption rdp traffic](https://unit42.paloaltonetworks.com/wireshark-tutorial-decrypting-rdp-traffic/)

```powershell
[Convert]::ToBase64String([IO.File]::ReadAllBytes("/users/administrator/LOCAL_MACHINE_Remote Desktop_0_INTERN-PC.pfx"))
```

```powershell
$base64String = "<INSERT Base64 ENCODED PFX>"
# Convert the Base64 string back to bytes
$bytes = [System.Convert]::FromBase64String($base64String)

# Save the bytes to a file
Set-Content -Path "<YOUR PATH>\file.pfx" -Value $bytes -Encoding Byte
```

Or you could to save time like I did :`https://gchq.github.io/CyberChef/#recipe=From_Base64('A-Za-z0-9%252B/%253D',false,false)&input=` and then save file

[seguranca - Extraction certs/private keys from Windows using mimikatz and intercepting calls with burpsuite](https://gitbook.seguranca-informatica.pt/credentials-exfiltration/extracting-certs-private-keys-from-windows-using-mimikatz-and-intercepting-calls-with-burpsuite)

```bash
openssl pkcs12 -in $file.pfx -nocerts -out $key.pem -nodes
openssl rsa 0in $key.pem -out $key.key
```
In WireShark:`Edit -> Preferences→Protocols -> TLS→Edit… (RSA keys list)`
- IP 
- Set protocol to `tpkt` and the port to `3389`
- Add the `$key.key` file above

![](rdptraffic.png)

Replay RDP traffic:
`File -> Export PDUs to File...`, under `Display filter:` type `rdp` and select `OSI layer 7`

[bakiba](https://github.com/bakiba/AOC-2023-SideQuest/tree/main/First%20Side%20Quest%20-%20The%20Story), [nouman404](https://nouman404.github.io/CTFs/TryHackMe/AdventOfCyber2023/SideQuest_Day1) and [pL4sTiC1337](https://github.com/pL4sTiC1337/ctf-writeups/blob/main/thm/aoc2023-sidequests/returnoftheyeti.md) used [https://github.com/GoSecure/pyrdp](https://github.com/GoSecure/pyrdp) which is replay RDP traffic from `$file.pcap` tool.

```bash
python3 -m venv 
source 
cd pyrdp/
pip install .
cd ../
python pyrdp-convert.py --list-only ../rdp_pdus.pcap
python3 pyrdp/pyrdp/bin/convert.py -o py_rdp_output rdp_pdu.pcap
# Then use docker to view the rdp playback
sudo docker run -v "$PWD/py_rdp_output:/pwd" -e DISPLAY=$DISPLAY -e QT_X11_NO_MITSHM=1 --net=host gosecure/pyrdp pyrdp-player`
```

I noticed there were slightly less packets for some reason... and my py_rdp_output did not work. I used [mabsimmo](https://medium.com/@mabsimmo/thm-advent-of-cyber-2023-side-quest-1-7f3a7fb5d6be) to finish this off. 
- Packet 17870 for the case id
- Packet 35547 I copy the dump to a file and remember that it starts with 1- 

```
cat yetikey1.txt | awk -F. '{print $2$3$4$5$6$7$8$9$10$11$12}' | tr -d '\n'
```

## [Snowy ARMageddon](https://tryhackme.com/room/armageddon2r)

[GrepMoreCoffee](https://github.com/JoanneBiltz/CTF-Writeups/tree/main/2023_THM_AOC_Side_Quests/Snowy_ARMageddon) and [0xb0b](https://0xb0b.gitbook.io/writeups/tryhackme/2023/advent-of-cyber-23-side-quest/snowy-armageddon) point out that previous requirement to gain the QR code from my favourite room from the AoC 2023 Game hacking room which this year was a Buffer Overflow. For my the very visual and new context of seeing a buffer overflow was very satisfying to me. But, this is 2024. I am doing this to learn from and highlight others to get the badge, experience different Hacking and InfoSec domains while I finish some projects and not get burnt out doing the HTB Season 4.  

Port scanning the machine we have ssh, telnet, a HTTP web server with instant 403 forbidden and a 50628 tcp port for...
![](nmapforARMingyouday2.png)

Trivision:
![](trivisionconnectprotectandbufferoverflow.png)
A quick `searchsploit`
![](day2searchsploitnoexploit.png)
Dorking it - key takeaway being trying different variations
![](dorkingtheexploitforthecamera.png)
[sumanrox](https://github.com/sumanrox/sidequest-exploits/tree/main/sidequest_2) has a automated exploit for this so I will test it only after I have read everyone else writeups that are GitHub. This exploit is very sexy. Good code and the nice visuals.
![](coolexploit.png)

Because they have made this so easy. I wanted to review and finish [nosqlinjectiontutorial](https://tryhackme.com/room/nosqlinjectiontutorial) from THM. After doing this.

```asm
mov r1, #0xc1   // store '193' in R1
lsl r1, #8      // shift by 8 bits to the left
add r1, #0x03   // add '3' to R1
lsl r1, #8      // shift by 8 bits to the left
add r1, #0x0b   // add '11' to R1
lsl r1, #8      // shift by 8 bits to the left
add r1, #0x08   // add '8' and '2' to R1 (since we cannot pass the hex value for 10 '0x0a')
add r1, #0x02
push {r1}
```
[beta-j](https://github.com/beta-j/TryHackMe-Rooms/blob/main/AoC%202023%20-%20SideQuest%202%20-%20Snowy%20ARMageddon.md) discusses the use of [https://shell-storm.org/online/Online-Assembler-and-Disassembler/](https://shell-storm.org/online/Online-Assembler-and-Disassembler)
```plaintext
\xc1\x10\xa0\xe3\x01\x14\xa0\xe1\x03\x10\x81\xe2\x01\x14\xa0\xe1\x0b\x10\x81\xe2\x01\x14\xa0\xe1\x08\x10\x81\xe2\x04\x10\x2d\xe5
```

Assembling my own version with [https://shell-storm.org/online/Online-Assembler-and-Disassembler/](https://shell-storm.org/online/Online-Assembler-and-Disassembler):
![](nicetoolonlinetool.png)

After issues, concentration dip and time management concerns I decide over lunch to, given how good this auto-exploits are, analyse them as the Beyond Root and figure out and copy some of the cool tricks they utilise for H4dd1xb4dg3r recon tool wrapper   

## [Frosteau's Busy with Vim](https://tryhackme.com/room/busyvimfrosteau)

While testing nuclei to see if it return any information if any about the Day 2 Web Camera exploit I stumbled on [0xb0b](https://0xb0b.gitbook.io/writeups/tryhackme/2023/advent-of-cyber-23-side-quest/frosteau-busy-with-vim#getting-to-sq3-the-qr-code) explanation of day 3, which I was missing. Apparently Day 3 is a secret challenge inside of Day 17's AoC 2023.


![](admindesktopday3.png)

![](chatlogs.png)

![](gimpforwindows.png)

![](snipandsketchvuln.png)

[Wikipedia - ACropalypse](https://en.wikipedia.org/wiki/ACropalypse) *(CVE 2023-21036) was a vulnerability in Markup, a [screenshot](https://en.wikipedia.org/wiki/Screenshot "Screenshot") editing tool introduced in [Google Pixel](https://en.wikipedia.org/wiki/Google_Pixel "Google Pixel") phones with the release of [Android Pie](https://en.wikipedia.org/wiki/Android_Pie "Android Pie"). The vulnerability, discovered in 2023 by security researchers Simon Aarons and David Buchanan, allows an attacker to view an [uncropped](https://en.wikipedia.org/wiki/Cropping_(image) "Cropping (image)") and unaltered version of a screenshot. Following aCropalypse's discovery, a similar [zero-day](https://en.wikipedia.org/wiki/Zero-day_(computing) "Zero-day (computing)")[[1]](https://en.wikipedia.org/wiki/ACropalypse#cite_note-1) vulnerability was also discovered, affecting [Snip & Sketch](https://en.wikipedia.org/wiki/Snip_%26_Sketch "Snip & Sketch") for [Windows 10](https://en.wikipedia.org/wiki/Windows_10 "Windows 10") and [Snipping Tool](https://en.wikipedia.org/wiki/Snipping_Tool "Snipping Tool") for [Windows 11](https://en.wikipedia.org/wiki/Windows_11 "Windows 11").*

[frankthetank-music - Acropalypse-Multi-Tool](https://github.com/frankthetank-music/Acropalypse-Multi-Tool) can *easily detect and restore Acropalypse vulnerable PNG and GIF files with simple Python GUI.*

![1080](0xb0brecroppingtoreveal.png)

![](nmapday3.png)

![](ftpanon.png)

For some reason `wget -r ` did not work.
![](flag2isaenv.png)
frostling_base
![](frostling_base.png)

frostling_five
![](frostling_five.png)

yeti_footage
![](yeti_footage.png)

yeti_mugshot
![](yeti_mugshot.png)

The wonderful Christmas Joke of escaping from Vi
![](telnettoaviproc.png)
I tried
```
:set shell=/bin/sh
:shell
```

BEfore losing my mind and rabbitholing in dev week:
![](beforerabbithioleindevweek.png)

[GrepMoreCoffee](https://github.com/JoanneBiltz/CTF-Writeups/tree/main/2023_THM_AOC_Side_Quests/frosteau_busy_with_vim) found that 
```
-If you exit vim you are kicked out of the connection.
-You can see the directories inside vim using `:e /` then `:e /directory_name`.
-Trying to run commands using :! from vim gives an error `Cannot execute shell /tmp/sh`. 
-After looking through many files and directories we determined that /tmp/sh was empty and not executable. 
-There is also a user frosty that has /usr/frosty/sh that is empty but is executable. We might be able to use that to get a shell.
-If you go to the /home directory you will find the user ubuntu.
-We tried all kinds of shells in vim but didn't have any luck.
-We can set permissions with `:call setfperm("/tmp/sh","rwxrwxrwx")`.
-`:e /etc/shells` gives us `/usr/busybox/sh`.
-It seems the 8065 port points to `/usr/frosty/sh`.
```

She then creates a msfvenom shell and uploads via FTP
![](grepnomorerabbitholes.png)

BatBato's solution was to upload Linux binaries  
![](batosolutiontotheshellday3issue.png)

![](changeofname.png)


```
:call setfperm("/tmp/ftp/sh","rwxrwxrwx")
```
## Post-Completion-Reflection  

- The people that unknowingly contributed to my fast-track exposure to these topics are awesome!
- The first day I could not really add anything useful or helpful I knew basically nothing even though I have done well in packet analysis challenges - LEARNT A LOT

## Beyond Root

- https://tryhackme.com/room/nosqlinjectiontutorial

- [sumanrox](https://github.com/sumanrox/sidequest-exploits/tree/main/sidequest_2) has a automated exploits - they look cool, work well, not many dependencies!