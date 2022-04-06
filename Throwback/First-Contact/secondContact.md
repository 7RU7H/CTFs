# Return to Firewall

## Crediental to try
HumphreyW:1c13639dba96c7b53d26f7d00956a364
securitycenter

But, firstly I need  the log flag and presumming it is in a .log of sort I used:
```bash
grep /var/log/ -r -e "TBH{"
```

## SMB analysis
I thought I try all the default tools on th Kali VM as I had not tried NBSTscan. I did not anything at all here but more experience using each tool. I tried with and without the "W" on all IPs avaliable. With no useful output I deduced that HumpreyW account is further indication of  compromise, as backdown account.

## Answers
What log file was found that is not a default log?
```{toggle}
login.log
```
What user was found within the log?
```{toggle}
HumphreyW
```
What is the hash of the user?
```{toggle}
1c13639dba96c7b53d26f7d00956a364
```






