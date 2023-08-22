# Dynstr Helped-Through

Name: Dynstr
Date:  
Difficulty:  Medium
Goals: 
- Azure DNS 
- Rogue DNS setup 
- VS Code / Codium and Snyk plugin is must to have!
Learnt:
- Automating with the power of x[ar](https://www.youtube.com/watch?v=b9VrnCMhJsQ)gs 
Beyond Root:
- Azure DNS
- Azure Backup
- Make a Log workspace

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

## Exploit

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


https://github.com/Azure/azure-quickstart-templates


## Testing to then design of Vulnerable Machine(s)

OSCP level Windows and Active Directory Jungle Gym

- Make OSCP level 
- Have good theme
- Make the Kubernetes, docker container only for pivoting not for escaping
- Make uber vulnerable switch once completed
- Ascii Art of completion



