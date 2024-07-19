# Analysis Notes

## Data

IP:
OS: Windows 10.0 Build 17763 x64
Arch:
Hostname:  DC-ANALYSIS 
DNS:
Domain: DC-ANALYSIS.analysis.htb, analysis.htb, internal.analysis.htb
Domain SID: S-1-5-21-916175351-3772503854-3498620144
Machine Purpose:
Services:
(signing:True) (SMBv1:False)
Service Languages:
Users:
- technician
Email and Username Formatting:
Credentials:



#### Mindmap-per Service

- OS detect, run generate noting for nmap
- 53
	-  DC-ANALYSIS.analysis.htb, analysis.htb, internal.analysis.htb no domain transfers
- 80 
	- ffuf for vhosts
	- images exiftool - no usernames
	- internal.analysis.htb
		- dashboard, users,employees
- 88
- 135 
	- zeroauth no cmd
- 139 
- 389 
	- dumped all object - zero auth
- 445
	- cme added hostname
- 464
- 593
- 636
- 3268
- 3269
- 3306
- 5985
- 9389
- 33060
- 47001
- 49664
- 49665
- 49666
- 49667
- 49669
- 49670
- 49671
- 49672
- 49683
- 49696
- 49709
- 49711


#### Todo List
#### Done


