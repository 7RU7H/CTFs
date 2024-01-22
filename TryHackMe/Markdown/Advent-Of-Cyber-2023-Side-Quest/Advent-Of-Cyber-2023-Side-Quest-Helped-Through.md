# Advent-Of-Cyber-2023-Side-Quest Through

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

https://tryhackme.com/room/adventofcyber23sidequest
https://tryhackme.com/room/adv3nt0fdbopsjcap
https://tryhackme.com/room/armageddon2r
3?
https://tryhackme.com/room/surfingyetiiscomingtotown


## Writeups Used and Profiles Listed

Profile names as links to Writeup - What I learnt from them
- [bakiba](https://github.com/bakiba/AOC-2023-SideQuest/tree/main/First%20Side%20Quest%20-%20The%20Story), [nouman404](https://nouman404.github.io/CTFs/TryHackMe/AdventOfCyber2023/SideQuest_Day1) and [pL4sTiC1337](https://github.com/pL4sTiC1337/ctf-writeups/blob/main/thm/aoc2023-sidequests/returnoftheyeti.md) used [https://github.com/GoSecure/pyrdp](https://github.com/GoSecure/pyrdp) - amazing tool!
- [nouman404](https://nouman404.github.io/CTFs/TryHackMe/AdventOfCyber2023/SideQuest_Day1) for 
	- `aircrack-ng` alternative with hashcat from [cyberark](https://www.cyberark.com/resources/threat-research-blog/cracking-wifi-at-scale-with-one-simple-trick)
- [0xb0b](https://0xb0b.gitbook.io/writeups/tryhackme/2023/advent-of-cyber-23-side-quest/the-return-of-the-yeti), for `aircrack-ng` to crack wifi passwords we need a .pcap format
- [Gh05t-1337](https://github.com/Gh05t-1337/CTF-Writeups/tree/main/TryHackMe/AoC23%20Sidequest/1%20The%20Return%20of%20the%20Yeti), decrypting using Wireshark

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


## Post-Completion-Reflection  

- The people that unknowingly contributed to my fast-track exposure to these topics are awesome!

## Beyond Root



