# Beyond Root Todo

Make it work!:
https://pwning.tech/nftables/
https://github.com/Notselwyn/CVE-2024-1086

https://medium.com/@cy1337/first-look-ghidras-10-3-emulator-7f74dd55e12d - pwn with ghidra emulation

learn more about system32 directory 

#### Mimikatz - Finishing AV bypassing but its 2024 

#### List the words to replace in order across all files 

- All filename names for:
	- modules


Remove all comments after replace just to have some chunk and so it does not look very stripped

- Add comments:
- AI generate conversation in comments 
	- `This need fucking fixed {X person}`
	- `WTF does this do {Y person}`
- AI generate junk descriptions

- Remove all frenchness ` coffee =kdbg_coffee`, replace with getTheNuts c stdout silly file 

Replace in mimikatz.c
```
/*	Benjamin DELPY `gentilkiwi`
	https://blog.gentilkiwi.com
	benjamin@gentilkiwi.com
	Licence : https://creativecommons.org/licenses/by/4.0/
*/

		L"  .#####.   " MIMIKATZ_FULL L"\n"
		L" .## ^ ##.  " MIMIKATZ_SECOND L" - (oe.eo)\n"
		L" ## / \\ ##  /*** Benjamin DELPY `gentilkiwi` ( benjamin@gentilkiwi.com )\n"
		L" ## \\ / ##       > https://blog.gentilkiwi.com/mimikatz\n"
		L" '## v ##'       Vincent LE TOUX             ( vincent.letoux@gmail.com )\n"
		L"  '#####'        > https://pingcastle.com / https://mysmartlogon.com ***/\n");

POWERKATZ

mimikatz

MIMIKATZ

powershell

POWERSHELL

```


miimlib
- dll renames

```
/*	Benjamin DELPY `gentilkiwi`
	https://blog.gentilkiwi.com
	benjamin@gentilkiwi.com

	Vincent LE TOUX
	http://pingcastle.com / http://mysmartlogon.com
	vincent.letoux@gmail.com

	Licence : https://creativecommons.org/licenses/by/4.0/
*/


Password
Domain
CREDENTIALS
CREDENTIAL
Credential
Authentication
Authenticate
AUTHENT

// ksub.c
const BYTE myHash[LM_NTLM_HASH_LENGTH] = {0xea, 0x37, 0x0c, 0xb7, 0xb9, 0x44, 0x70, 0x2c, 0x09, 0x68, 0x30, 0xdf, 0xc3, 0x53, 0xe7, 0x02}; // Waza1234/admin

Authent

KIWI

// AFTER FULL WORDS
CRED
Cred

kiwi

Kiwi
LOGON_SESSION
LogonSession

Lsass



	PackageInfo->Name       = L"KiwiSSP";
	PackageInfo->Comment    = L"Kiwi Security Support Provider";

const wchar_t * KUHL_M_SEKURLSA_LOGON_TYPE[] = {
	L"UndefinedLogonType",
	L"Unknown !",
	L"Interactive",
	L"Network",
	L"Batch",
	L"Service",
	L"Proxy",
	L"Unlock",
	L"NetworkCleartext",
	L"NewCredentials",
	L"RemoteInteractive",
	L"CachedInteractive",
	L"CachedRemoteInteractive",
	L"CachedUnlock",
};
```

Add Junk code that will never be called

Add some encrypted to add to entropy but not be a significant percentage

Or be cool and: 
- encrypt all the calls
- Add a function  decrypt and then run them
-  Encrypt all of the 

mimilove
```

	kprintf(L"\n"
		L"  .#####.   " MIMILOVE_FULL L"\n"
		L" .## ^ ##.  " MIMILOVE_SECOND L"\n"
		L" ## / \\ ##  /* * *\n"
		L" ## \\ / ##   Benjamin DELPY `gentilkiwi` ( benjamin@gentilkiwi.com )\n"
		L" '## v ##'   https://blog.gentilkiwi.com/mimikatz             (oe.eo)\n"
		L"  '#####'    " MIMILOVE_SPECIAL L"* * */\n\n");

kull
kuhl


mimilove
love
LOVE

lsasrv
kerberos

	kprintf(L"========================================\n"
		L"LSASRV Credentials (MSV1_0, ...)\n"
		L"========================================\n\n"
		);


Kiwi
LOGON_SESSION
LogonSession

sekurlsa

VeryBasicModuleInformationsForName


miKerberos 
paKerberos
KerbLogonSessionList

PKERB_HASHPASSWORD_5
KERBEROS_KEYS_LIST_5
KERB_HASHPASSWORD_5
KIWI_KERBEROS_KEYS_LIST_5


PCWCHAR mimilove_kerberos_etype(LONG eType)
{
	PCWCHAR type;
	switch(eType)
	{
	case KERB_ETYPE_NULL:							type = L"null             "; break;
	case KERB_ETYPE_DES_PLAIN:						type = L"des_plain        "; break;
	case KERB_ETYPE_DES_CBC_CRC:					type = L"des_cbc_crc      "; break;
	case KERB_ETYPE_DES_CBC_MD4:					type = L"des_cbc_md4      "; break;
	case KERB_ETYPE_DES_CBC_MD5:					type = L"des_cbc_md5      "; break;
	case KERB_ETYPE_DES_CBC_MD5_NT:					type = L"des_cbc_md5_nt   "; break;
	case KERB_ETYPE_RC4_PLAIN:						type = L"rc4_plain        "; break;
	case KERB_ETYPE_RC4_PLAIN2:						type = L"rc4_plain2       "; break;
	case KERB_ETYPE_RC4_PLAIN_EXP:					type = L"rc4_plain_exp    "; break;
	case KERB_ETYPE_RC4_LM:							type = L"rc4_lm           "; break;
	case KERB_ETYPE_RC4_MD4:						type = L"rc4_md4          "; break;
	case KERB_ETYPE_RC4_SHA:						type = L"rc4_sha          "; break;
	case KERB_ETYPE_RC4_HMAC_NT:					type = L"rc4_hmac_nt      "; break;
	case KERB_ETYPE_RC4_HMAC_NT_EXP:				type = L"rc4_hmac_nt_exp  "; break;
	case KERB_ETYPE_RC4_PLAIN_OLD:					type = L"rc4_plain_old    "; break;
	case KERB_ETYPE_RC4_PLAIN_OLD_EXP:				type = L"rc4_plain_old_exp"; break;
	case KERB_ETYPE_RC4_HMAC_OLD:					type = L"rc4_hmac_old     "; break;
	case KERB_ETYPE_RC4_HMAC_OLD_EXP:				type = L"rc4_hmac_old_exp "; break;
	case KERB_ETYPE_AES128_CTS_HMAC_SHA1_96_PLAIN:	type = L"aes128_hmac_plain"; break;
	case KERB_ETYPE_AES256_CTS_HMAC_SHA1_96_PLAIN:	type = L"aes256_hmac_plain"; break;
	case KERB_ETYPE_AES128_CTS_HMAC_SHA1_96:		type = L"aes128_hmac      "; break;
	case KERB_ETYPE_AES256_CTS_HMAC_SHA1_96:		type = L"aes256_hmac      "; break;
	default:										type = L"unknow           "; break;
	}
	return type;
}
```

```
LM_NTLM_HASH
LSA_
Tickets
VERY_BASIC_MODULE_INFORMATION
PKULL_M_MINI_PATTERN
```


#### Other ideas start here

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
