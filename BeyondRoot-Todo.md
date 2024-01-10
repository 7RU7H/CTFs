# Beyond Root Todo



Setup MFA service

Stick my head in the boot and slam my idiot brain till I feel those deep waters of way out of my depth till NASM is at least approachable.
- https://www.ired.team/miscellaneous-reversing-forensics/windows-kernel-internals/writing-a-custom-bootloader

- ssh server for sliver setup and multiplayer 
	- fail2ban setup 


int0x80 and savant seem very good - packet economics was discuss in this video as if it is potential savants idea 
https://www.irongeek.com/i.php?page=videos/derbycon7/t113-full-contact-recon-int0x80-of-dual-core-savant
https://www.youtube.com/watch?v=XBqmvpzrNfs
#### Ideas List


Do grey-box and white box SAST testing

https://github.com/leebaird/discover use them ffs on a CTF

Write ups from top 50 to consider
https://app.hackthebox.com/users/357237

```powershell
netsh trace start capturetype=physical capture=yes maxSize=1024 overwrite=yes tracefile=C:\Windows\Temp\rite-NetTrace.etl filemode=single ipv4.address=$beaconingIpv4Addr
```

Use in context https://www.youtube.com/watch?v=o_XaJdDqQA0

Cleaning up in a more professional manner and a too professional manner
Windows/Linux - Go?

- try arbitrarily write data to disk over all locations of where the ADDecrypt.zip, ADDecrypt and its contents are stored as data for OS record keeping and the disk and how as DFIR would you counter that if you could hold any VMs or Live system in a suspended state or have a holographic file system where there is session layer of previous session and current session for dynamic analysis in Malware Analysis. 

TrustedSec Tool Lab
https://github.com/trustedsec/social-engineer-toolkit
Unicorn - https://github.com/trustedsec/unicorn

- https://github.com/trustedsec/trevorc2
- https://github.com/trustedsec/ptf
- https://github.com/trustedsec/hate_crack

Seatbelt compile and used

Host Vulnhub box and do both Red and Blue Teaming 

pwnkit
lsa dump

https://github.com/iphelix/dnschef or modern alternative

Create and configure a DNS server  `dnscmd`
https://learn.microsoft.com/en-us/windows-server/networking/dns/quickstart-install-configure-dns-server?tabs=powershell


- enabling RDP with cme and impacket kerboros tickets 
	- Harden and or implement AMSI with powershell 
- Create an alert based on .exe and .ps1 from PowerUP, Winpeas 
	- Remote interaction with box that would no lead to compromise
- Open RDP for a new user and Get Sysinternals on box
	- procdump
	- procmon


https://github.com/dubs3c/sudo_sniff/blob/master/sudo_sniff.c


webp exploit

https://github.com/0vercl0k - looks insane https://github.com/0vercl0k/clairvoyance


Procmon something and follow along

Atomic wannabe APT 
https://github.com/redcanaryco/invoke-atomicredteam


Bootkit

A C polymorphic code
https://github.com/m0nad/PSG/blob/master/psg.c

Rust RootKit xkit framework

A memory safe rust rootkits - [Diamorphine](https://github.com/m0nad/Diamorphine/blob/master/README.md) and [Kris Nova's boopkit](https://github.com/krisnova/boopkit)

```rust
// Embed
// Evade
// Shell <shellname>
// Delete <self,shell pid and artificats>
// Timebomb-Delete <secs> <delete-type>
```

Check this out: https://github.com/m0nad/awesome-privilege-escalation

https://github.com/codecrafters-io/build-your-own-x

https://blog.holbertonschool.com/hack-the-virtual-memory-c-strings-proc/
https://beej.us/guide/bgnet/
https://github.com/EmilHernvall/dnsguide/blob/master/README.md
https://kasvith.me/posts/lets-create-a-simple-lb-go/
https://mattgathu.github.io/2017/08/29/writing-cli-app-rust.html
https://rust-cli.github.io/book/index.html
https://flaviocopes.com/go-tutorial-lolcat/ - rainbox cli
http://www.saminiir.com/lets-code-tcp-ip-stack-1-ethernet-arp/


### Monolith 

Starting point
https://www.youtube.com/playlist?list=PL9IEJIKnBJjFNNfpY6fHjVzAwtgRYjhPw
https://www.youtube.com/playlist?list=PL9IEJIKnBJjH_zM5LnovnoaKlXML5qh17


## Putty Backdoor and Tunnel 

#### The Chisel-Shadow Network

Requires the the Go HTTPS + backdoor server

Revision
![1080](reddish-experiemental-chisel-shadow-network.excalidraw)


## Temple Ideas

Make a wordlists creator in golang for the serious string compute.

OneSeclistDirectoryBustingWordlistToRuleTheCTFs

- Go back to gobuster for inital directories
- Stop, observe and note potential targets to start feroxbuster and ffuf from 
	- What is actually a good target for this and what are likely middle directories that are good to recurse -  Pushing beyond the 
		- Anything wherehttps://www.patreon.com/xct
			- dev, git, backup, bak, etc...objectives!
- Use ffuf more ffuf vhosts, extensions, POTENTIAL pages
- Do a slow set of feroxbusters scan over to collect and double verify 


#### Mad Idea 2 - Flow: One to take control of all network routes from inside a network  

How?
```
```



[How to build a tcp proxy](https://robertheaton.com/2018/08/31/how-to-build-a-tcp-proxy-1/)


Watch this xct do this on seperate machines - https://www.patreon.com/xct 8.50 not including VAT
- https://www.youtube.com/watch?v=FYWGhdaDcZo&list=PLPBVZbjvnjVkIgFavcRiBKbDSricFJeoD
To prep for dante -> and beyond!https://www.patreon.com/xct

- Pay for patreon and do Shinra in a years time 





Dockerise one of my github projects
https://tryhackme.com/room/introtodockerk8pdqk


go full cyber 
https://github.com/hlldz/Phant0m


Start stuff in the backgound on the cli and then make stealthier 

Start a process in the background
```powershell
Invoke-CimMethod -ClassName win32_process -MethodName create
 -Arguments @{ commandline = $YOURXGOESHERENO$; ProcessStartupInformation = New-CimInstance -CimClass ( Get-CimClass Win32_ProcessStartup) -Property @{ShowWindow=0} -Local;     CurrentDirectory = $null}
```


Add a backdoor to custom source code in a weird language swift backdoor



https://owasp.org/www-community/attacks/Clickjacking
https://portswigger.net/web-security/clickjacking


No phind or chatgpt
```python
#!/usr/bin/python3

import requests
import re

# Globals TODO - localise!
uploads_upload_pattern = 'uploads/uploads_^[0-9]{10}\.zip'
wordlist =

# Load a file for payloads
with open(wordlist, "r") as f:
	payloads = f.read()
	for i,payload in enumerate(payloads):

# Send request of the correct format replace FUZZ with a line from the payload file
	r = requests.get()
	
# Take the request and extract the link for line containing 'uploads/upload_'
	payload_zipped_path_line = re.findall(uploads_upload_pattern, r.text)
	
# some f string function to cut out the noise for just uploads/upload_NUMS.zip
	
	payload_zipped_path = #  payload_zipped_path_line 
# download the file 
	url_concat_with_payload_path = f"{URL}{payload_zipped_path}"

# get the name in the response
	payload_zip_filename

# TODO set a path of /tmp
	r = requests.get('url_concat_with_payload_path')

# unzip and cat content to stdin
	unzippable_filepath = f"tmp/{payload_zip_filename}"
	# unzip some how and getting the name
exfil_filename = # TODO ?
exfil_filename_with_path
	with open(exfil_filename_with_path, "r") as f:
	    exfil_data = f.read()	
		print(f"ZIP name - Exfil Filename - Payload")
		print(f"{payload_zip_filename} - {} {}")
		print(exfil_data)
exit
```


https://ppn.snovvcrash.rocks/pentest/infrastructure/ad/av-edr-evasion/uac-bypass


#### Backlog - Proving Ground Someday...

[[XposedAPI-Writeup]] + [[BillyBoss-Writeup]] + [[Slort-Helped-Through]] + [[Sybaris-Helped-Through]] (C) related
