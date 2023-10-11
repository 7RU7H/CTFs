# Dynstr Helped-Through

Name: Dynstr
Date:  
Difficulty:  Medium
Goals: 
- Azure DNS 
- Rogue DNS setup 
- VS Code / Codium and Snyk plugin is must to have!
- DNS general
Learnt:
- Automating with the power of x[ar](https://www.youtube.com/watch?v=b9VrnCMhJsQ)gs 
Beyond Root:
- Azure DNS
- Azure Backup
- Make a Log workspace

![](bg1.jpg)

- [[Dynstr-Notes.md]]
- [[Dynstr-CMD-by-CMDs.md]]

Tripletted with [[Response-Helped-Through]] and [[Absolute-Helped-Through]] to secure my understanding of Azure Backup and DNS implementations.

Having never managed a DNS server and looking into here and there especially from the [[Kotarak-Helped-Through]] - beyond root of making a DNS server and once required a Rogue DNS server at some point for another box. From what I have read and can assume seems like a good idea from the point on study the AZ-104 that is much easier to manage. No record management without some kind of versioning like `git` for software seems like a nightmare. Especially from what I understand that incorrect record keeping can then expose domains that are suppose be in some network perimeter and not accessible by the public internet.
## Recon

The time to live (TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Nuclei: [[generic-tokens-http___10.129.154.73_]]
`key=AIzaSyCWDPCiH080dNCTYC-uprmLOn2mt2BMSUk,Password: sndanyd`
Nuclei states that the same key is a Google API keys wtf..is..`AIzaSyCWDPCiH080dNCTYC-uprmLOn2mt2BMSUk` from Nuclei [[google-api-key-http___10.129.154.73_]]

[DT's xargs video is a must watch!](https://www.youtube.com/watch?v=rp7jLi_kgPg)

The hostname on the web page
![](dynahostname.png)

add to `/etc/hosts`
```bash
echo "10.129.121.237 dyna.htb" | sudo tee -a /etc/hosts
```

Trying out `dnsrecon`
![](dnsreconattempt.png)

DNS Recon including the manual usage of `dig`
```bash
dig axfr dyna.htb @10.129.121.237

dnsrecon -t axfr -d dyna.htb -n 10.129.121.237
```

Default passwords?
![](betapassword.png)

Thisd `key=AIzaSyCWDPCiH080dNCTYC-uprmLOn2mt2BMSUk,Password: sndanyd` was found by [[google-api-key-http___10.129.154.73_]]


Reviewing nmap for version of DNS as website is bare, DNS recon is going no where.. There is a vulnerability - https://www.rapid7.com/db/vulnerabilities/dns-bind-cve-2020-8619/

*Unless a nameserver is providing authoritative service for one or more zones and at least one zone contains an empty non-terminal entry containing an asterisk ("\*") character, this defect cannot be encountered. A would-be attacker who is allowed to change zone content could theoretically introduce such a record in order to exploit this condition to cause denial of service, though we consider the use of this vector unlikely because any such attack would require a significant privilege level and be easily traceable.*

We could use it to point to a rogue DNS server 
- Really would rather have BiS tool than anything else

We have credentials, but how to use them? On chance that Dynamic DNS is a thing [Wikipedia Dynamic DNS](https://en.wikipedia.org/wiki/Dynamic_DNS) it does.. so the other hostnames are hosted on the same nameserver...

Services?
```
- dnsalias.htb
- dynamicdns.htb
- no-ip.htb
```

Services?
```bash
# Add each domain to our /etc/hosts file with sed 
sudo sed -i 's/10.129.121.237 dyna.htb/10.129.120.38 dyna.htb dnsalias.htb dynamicdns.htb no-ip.htb/g' /etc/hosts
```

## From this point...

I had finished my Beyond Root of Azure DNS revision and continued with Writeups to learn as much DNS information I could. Basically using this machine to justify doing some HackTheBox sent the correct direction of learn Azure. What I missed (from inference of note taking)

- no-ip.com API sharing -> API (dork: `noip.com api`)
![](apiipno.png)
From this page:
```
http://username:password@dynupdate.no-ip.com/nic/update?hostname=mytest.example.com&myip=192.0.2.25
```
Modifying it to match box information
```
curl http://dynadns:sndanyd@no-ip.htb/nic/update?hostname=nvm.no-ip.htb&myip=10.10.14.70
```
It works
![](good.png)

Test what happens if it does not - changed the password
![](badpassword.png)

Consider implementation:
- `hostname=nvm.no-ip.htb&myip=10.10.14.70` is pass to a program that then updates the DNS on the DynStr machine

```
curl http://dynadns:sndanyd@no-ip.htb/nic/update?hostname=nvm.no-ip.htb&myip=10.10.14.70 --proxy http://127.0.0.1:8080
```

## Exploit

```bash
# base64 cookie:
ZHluYWRuczpzbmRhbnlk : dynadns:sndanyd
```
![](toburpsuite.png)

CMDi is suggest by XCT - I paused the video I tried for CMDi
![](miliseconuponnewline.png)
I could not replicate the the same temporal delay on the `\n` to inject.


Not being methodical enough - STOP: 
- Create List
- Question How it works
	- it is doing to concatenate both parameters and append to a file resolv.conf is  

... 

Went down the list of how it could be added to a file
![](somelinuxdnsfiles.png)

[/etc/bind.conf files](https://mirror.apps.cam.ac.uk/pub/doc/redhat/redhat7.3/rhl-rg-en-7.3/s1-bind-configuration.html)

```bash
# Same line
$ip  $hostname

# Multi-line as variables
hostname $hostname
address $ip

# Bind conf 
zone "domain.com" {
  type slave;
  file "domain.com.zone";
  masters { 192.168.0.1; };
};

# records 
server1      IN     A       10.0.1.5
www          IN     CNAME   server1
```

- Input 
	- Software that modifies records
	- Either as sets of Linux commands:
		- Inject input into Template
		- Two commands to modify the file 
		- Concatenation of input -> to modify the file  
- Checks hostname


![](eh.png)

At the front
![](nsupdatefailed.png)

At the back 
![](wrongdomain.png)

I decided the list format is not good as a entirely text based.

- List of Actions:
	- Command Injection `hostname=nvm.no-ip.htb&myip=10.10.14.70` 
	- Methods list:
		- METHOD X - picture

Becomes:

- List of Actions:
	- Command Injection `hostname=nvm.no-ip.htb&myip=10.10.14.70` 
	- Methods list:
		- Append to hostname input  -  picture.png

Decided to improve my CMDi Cheatsheet before continuing


Improving my CMDi cheatsheet, setup and thinking

What I have tried not to do over the years is just use these with a request with `ffuf` with https://github.com/payloadbox/command-injection-payload-list and not understand. There is a niggling feeling of am I missing things and also HOW AM I GOING TO KEEP track of what I have done in visual media that does not take forever to add on to limited time factors....  

if so concatenate - https://gabb4r.gitbook.io/oscp-notes/cheatsheet/command-injection-cheatsheet


https://portswigger.net/web-security/os-command-injection

https://github.com/payloadbox - for each ?
https://github.com/swisskyrepo/PayloadsAllTheThings

https://www.cobalt.io/blog/a-pentesters-guide-to-command-injection
https://github.com/carlospolop/hacktricks/blob/master/pentesting-web/command-injection.md
https://portswigger.net/kb/issues/00100100_os-command-injection

https://portswigger.net/web-security/os-command-injection

https://www.youtube.com/watch?v=ppYNkvlR9jM
## Foothold

## Privilege Escalation

## Beyond Root

Get VS Codium for the zero telemetry on Kali
```bash
#!/bin/bash

wget -qO - https://gitlab.com/paulcarroty/vscodium-deb-rpm-repo/raw/master/pub.gpg \
    | gpg --dearmor \
    | sudo dd of=/usr/share/keyrings/vscodium-archive-keyring.gpg
wait
echo 'deb [ signed-by=/usr/share/keyrings/vscodium-archive-keyring.gpg ] https://download.vscodium.com/debs vscodium main' \
    | sudo tee /etc/apt/sources.list.d/vscodium.list
wait
sudo apt update && sudo apt install codium
```

#### Azure

Original Goal - Rehost DNS web application utility from Dynstr various ways in Azure.
- Make a Log workspace
- Azure DNS
- Azure Backup

Syllabus Revision: 

Assumptions:
- We are operating on East Coast US 
- We are hosting `dnsalias dynamicdns no-ip` and not worrying about considering configuring DynStr as PaaS
- Not using Azure App services as I have already revised that with [[Traverse-Writeup]]
- We have an Azure Gateway for VPN access

Firstly as the main security vulnerability on DynStr is the password for Dynamic DNS and the outdated DNS BIND service moving to Azure DNS to manage keys with Keys Vaults and using some option regarding hosting DNS for Azure. If DynStr.org was hosting three differing internal websites in the cloud with sub companies dnsalias, dynamicdns and no-ip then Azure Private Resolver would be useful for centralising applications and allow Azure WAN cross-region access via DNS resolution. 

It would also be a lot for work to maintain three different domains with custom DNS, but if that ws required we would need to make:

- Three resource groups
- Three VNets
- Three DNS VMs 
- Four Traffic Manage Load Balancers ( DNS Load Balancers )
	- One Parent DNS Load to route to each domain
	- One each for preventing DoS via DNS request-ad-infinitum 
- Business Logic VMS
- 2 Private Links per network 
	- One for Backup via Private Link
	- One that Links internal access from Azure Gateway (if these internally hosted)
	
If we are hosting for the Public then Public DNS otherwise Custom DNS or Private DNS managed by Azure.

Key Vault is would be in the Manage access keys section for [[Absolute-Helped-Through]], but for the security vulnerability. For more Azure Storage revision view that page.

On the syllabus changes the one line of Azure DNS does not really do justice to the amount of DNS related configurations and options there are as well as requiring a good fundamental understanding of DNS at the System Administrator level
- Configure Azure DNS
	- Private DNS
		- Add Virtual Network Linking (add VNet to a Zone): `Resource groups -> $resourceGroup -> $domain -> select Virtual Network Links` - provide VNet, Sub and a `Link name`  
		- Create an additional DNS Record in the correct DNS Zone `Search -> Private DNS Zones -> Create`
		- Subscription, Resource Group and Instance Name
		- Link VNet Name `$PrivateDNSZone -> Settings -> Virtual Network Links -> Add` Link name, Sub, Vnet
		- Then Registration and Resolution - can auto- registrate
	- Public DNS
	- Azure Private Resolver is for resolve domains in Hybrid cloud

If we are directly hosting their sites and data for small businesses that do not have an IT team we could segment Vnets and subnets based on resource access by the VM(s) hosting the site to backend services and infrastructure for backups.   

- Configure service endpoints for Azure platform as a service (PaaS)
	- Change subnet settings
	- Create outbound NSG and associate with subnet

For the alternative to meet my revision syllabus requirements and the most real world use of cloud for Dynamic DNS in relvance to the Dynstr machine - Dynstr setup is a Platform as a service. Organisations ask Dynstr.org to host their and manage their DNS and routing to their assets.  If for example it was collaborative effort between child companies of parent company for some larger goal the use of Private Endpoint as a PaaS (Self-Service I suppose) could then be used to connect VNets across regions - If Dynstr was the parent and the subdomains were the child company all requiring decentralisation of information, but cross-company access to some employees with shipping sensitive data to the cloud. An Addition VPN gateway and VPN for employees involved would reduce attack surface and creating gaps between other on-premise and cloud activities.

- Configure private endpoints for Azure PaaS
	- `Private Link Center -> Private Links`;
	- `Private Link Center -> Private Endpoints`;
		- Vnet configuration`:
			- Beware of Dynamic | Static IP allocation
			- Beware Private Endpoint DNS if required!
	
Additional Load balancers 

Troubleshoot Load balancers
- VM health probes
- Load balancer health
- Azure Resource Explorer for failed state
- Resource Health Cehck for connectivity

- Configure name resolution and load balancing
	- Traffic Manager to Load balncer and route DNS 
		- `Search -> Traffic Manager -> Create a `
		- Name, Routing Method, Subscription, Resource Group
			- Routing Methods:
				- Performance
				- Weighted
				- Priority
				- Geographic
				- MultiValue
				- Subnet


Backup related
- Create a Recovery Services vault
	- `Search -> Recovery Services vaults -> + Create`
- Create an Azure Backup vault
	- `Search -> Backup vaults -> + Create`
- Create and configure a backup policy
	- `Search Backup Center -> Policies`
	- `Search -> Policy -> Definitions -> Category Drop -> untick All and retick Backup Policies`
- Perform backup and restore operations by using Azure Backup
	- `Search -> Backup center Alerts`
- Configure Azure Site Recovery for Azure resources
	- Continuous availability during outages 
		- Local, Zonal, Regional redundancy - also do need only read access  
- Perform a failover to a secondary region by using Site Recovery
	`$SiteRecoveryvault -> select Recovery Plans -> $recoveryplan_name -> Failover`
	`$SiteRecoveryvault -> select Recovery Plans -> $recoveryplan_name -> Reprotect`
- Configure and interpret reports and alerts for backups
	- `Search -> Backup center Alerts`

`Search -> Recovery Services vaults -> + Create`
- West Coast for our East Coast services of dnsalias dynamicdns no-ip
	- GRS
- Immutability for the RSV would be restrictive as the web applications would be continuous updated - hopefully.
- Deny Public Access to the RSV
- Creating a Private Endpoint for the backup policy and update - policy initiative 

Failover to West US in a Disaster and reprotect back to East US. In a reprotect scenario we may want custom instead of last processed if there is a webshell in the recovery vault!


- Update and Fault domain revision calculation 20 VMs
	- 2 Fault
	- 8 Update Domains
How many will run on update and on fault
- 10 on Fault
- Max 3 down on update


