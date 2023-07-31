# Dynstr Helped-Through

Name: Dynstr
Date:  
Difficulty:  Medium
Goals: 
- Azure DNS 
- Rogue DNS setup 
- VS Code / Codium and Snyk plugin is must to have!
Learnt:
Beyond Root:
- Azure DNS
- Azure Backup
- Make a Log workspace

- [[Dynstr-Notes.md]]
- [[Dynstr-CMD-by-CMDs.md]]


Tripletted with [[Response-Helped-Through]] and [[Absolute-Helped-Through]] to secure my understanding of Azure Backup and DNS implementations.

Having never managed a DNS server and looking into here and there especially from the [[Kotarak-Helped-Through]] - beyond root of making a DNS server and once required a Rogue DNS server at some point for another box. From what I have read and can assume seems like a good idea from the point on study the AZ-104 that is much easier to manage. No record management without some kind of versioning like `git` for software seems like a nightmare. Especially from what I understand that incorrect record keeping can then expose domains that are suppose be in some network perimeter and not accessible by the public internet.


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Nuclei: [[generic-tokens-http___10.129.154.73_]]
`key=AIzaSyCWDPCiH080dNCTYC-uprmLOn2mt2BMSUk,Password: sndanyd`
Nuclei states that the same key is a Google API keys wtf..is..`AIzaSyCWDPCiH080dNCTYC-uprmLOn2mt2BMSUk` from Nuclei [[google-api-key-http___10.129.154.73_]]

## Exploit

## Foothold

## Privilege Escalation

## Beyond Root

Rehost DNS web application utility from Dynstr various ways in Azure.


- Azure DNS
- Azure Backup
- Make a Log workspace


https://github.com/Azure/azure-quickstart-templates


## Testing to then design of Vulnerable Machine(s)

OSCP level Windows and Active Directory Jungle Gym

- Make OSCP level 
- Have good theme
- Make the Kubernetes, docker container only for pivoting not for escaping
- Make uber vulnerable switch once completed
- Ascii Art of completion



