# RedCross CMD-by-CMDs


```bash
sed -i 's///g' *-CMD-by-CMDs.md
```

Fuzzed without returning data 
```bash

ffuf -u https://intra.redcross.htb -c -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-110000.txt -H 'Host: FUZZ.redcross.htb' -fw 20

ffuf -request phpfilter-fuzz.request -request-proto https -w /usr/share/wordlists/seclists/Discovery/Web-Content/raft-medium-files-lowercase.txt -fw 29

# Ran sqlmap for 45 minutes - probabl;y not sqli
sqlmap --url="https://intra.redcross.htb/?page=login" --threads=10 --risk=3 --level=5 -eta

```


