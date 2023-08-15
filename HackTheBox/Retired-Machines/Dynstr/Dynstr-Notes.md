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

Failed script... - Golang tools, 
```bash
#!/bin/bash

# Automate DynStr dynamic hosts while I do other things

DynStrHosts=(dnsalias dynamicdns no-ip)
for host in $DynStrHosts; do
        echo $host | xargs -I {} nikto -h {}.htb -o nikto/{}.txt;
        wait
        echo $host | xargs -I {} ~/go/bin/nuclei -u http://{}.htb -me nuclei-{};
        wait
        echo $host | xargs -I {} gospider -d 0 -s 'http://{}.htb' -a -d 5 -c 5 --sitemap --robots --blacklist jpg,jpeg,gif,css,tif,tiff,png,ttf,woff,woff2,ico,pdf,svg,txt  -o gospider-{};
        wait
        echo $host | xargs -I {} feroxbuster --url 'http://{}.htb' -w /usr/share/seclists/Discovery/Web-Content/raft-medium-words-lowercase.txt --auto-tune -r -A -o feroxbuster/{}-rmwlc;
        wait
done
```

