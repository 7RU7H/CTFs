# Dynstr Notes

## Data 

IP: 10.129.121.237
OS:
Hostname:
Domain:  dyna.htb
Machine Purpose: 
Services: 53, 22, 80
Service Languages:
Users:
Credentials:

```
dynadns : sndanyd
```

```
[dns@dyna.htb](mailto:#)
```
## Objectives

## Target Map

## Solution Inventory Map

### Todo 

Services?
```
- dnsalias.htb
- dynamicdns.htb
- no-ip.htb
```

`key=AIzaSyCWDPCiH080dNCTYC-uprmLOn2mt2BMSUk,Password: sndanyd`
### Done

bg1.jpg

dynahostname.png

```bash
echo "10.129.121.237 dyna.htb" | sudo tee -a /etc/hosts
```

dnsreconattempt.png

```bash
dig axfr dyna.htb @10.129.121.237

dnsrecon -t axfr -d dyna.htb -n 10.129.121.237
```

betapassword.png

Reviewing nmap for version of DNS as website is bare, DNS recon is going no where.. There is a vulnerability
https://www.rapid7.com/db/vulnerabilities/dns-bind-cve-2020-8619/

*Unless a nameserver is providing authoritative service for one or more zones and at least one zone contains an empty non-terminal entry containing an asterisk ("\*") character, this defect cannot be encountered. A would-be attacker who is allowed to change zone content could theoretically introduce such a record in order to exploit this condition to cause denial of service, though we consider the use of this vector unlikely because any such attack would require a significant privilege level and be easily traceable.*

We could use it to point to a rogue DNS server 
- Really would rather have BiS tool than anything else

We have credentials, but how to use them? On chance that Dynamic DNS is a thing [Wikipedia Dynamic DNS](https://en.wikipedia.org/wiki/Dynamic_DNS) it does.. so the other hostnames are hosted on the same nameserver...