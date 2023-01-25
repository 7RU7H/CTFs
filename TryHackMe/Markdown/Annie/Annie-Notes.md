# Notes

These are just part of my goals for this box to improve my automation and code in one window 


exec-naabu-allports.sh
```bash
naabuRate=1000
workers=25
nmapRate=# None
# naabu -i and nmap -e to specify interface
# Reduce idiocy - ie no vpn up, no scan
interface=tun0
# add -v flag for naabu verbosity - does not do anything in 2.0.5
# add -stats for time, PPS, packets percentage 
```

```bash

#!/bin/bash

if [ "$#" -ne 3 ]; then
        echo "Usage: $0 <IP address> <interface> <rate integer>"
        exit
fi

dash_delimited_ip=$(echo $1 | tr -s '.' '-')

sudo naabu -host 10.10.10.10 -p 0-65535 -i $interface -c $workers -rate $naabuRate -nc -o naabu/10-10-10-10-allports -nmap-cli 'nmap -sV -sC --min-rate $nmapRate -e $interface -oN nmap/from-naabu-some-ports-sc-sv'
```


Issues naabu output !!
`> ` works but `-o` eh?



```bash
#!/bin/bash

if [ "$#" -ne 3 ]; then
        echo "Usage: $0  <rate integer> <interface> <IP address>"
        exit
fi

sudo nmap -F -n --min-rate $3 -e $2 $1 2>&1 > /tmp/nmap-err
sn_output=$(cat /tmp/nmap-err)
if [[ "$sn_output" =~ ."try -Pn". ]]; then
        nmap_pn_flag="-Pn"
        echo ""
        echo "Host: $1 requires -Pn flag!"
        echo ""
else
        nmap_pn_flag=""
fi
rm /tmp/nmap-err
dash_delimited_ip=$(echo $1 | tr -s '.' '-')
list=$(cat naabu/$dash_delimited_ip-allports-naabu | awk -F: '{print $2}' | tr -s '\n' ',' )
ports=${list:0:${list} - 1 }
sudo nmap $nmap_pn_flag -sC -sV -oA nmap/$dash_delimited_ip-Extensive-Found-Ports --min-rate $1 -e $2 -p $ports $3
wait
sudo nmap $nmap_pn_flag --script discovery -oA nmap/$dash_delimited_ip-Discovery --min-rate $1 -e $2 -p $ports $3
wait
sudo nmap $nmap_pn_flag --script vuln -oA nmap/$dash_delimited_ip-Vuln --min-rate $1 -e $2 -p $ports $3
wait
echo ""
echo "Nmap has finished all 3 Scans against $1"
echo ""
exit
```
