# Trick CMD-by-CMDs

```bash
sed -i 's///g' *-CMD-by-CMDs.md
```

```bash
# Do this properly 
nslookup
> server $ip
> trick.htb # guess it is called trick.htb
# Add inital record 
echo "10.10.11.166 trick.htb" | sudo tee -a /etc/hosts
# Zone Transfer
dig axfr trick.htb @10.10.11.166
# More info
dig -x trick.htb @10.10.11.166
# Add preprod-payroll.trick.htb root.trick.htb to /etc/hosts


feroxbuster --url 'http://preprod-payroll.trick.htb/' -w /usr/share/wordlists/dirbuster/directory-list-lowercase-2.3-small.txt  --auto-tune -r -A -o feroxbuster/preprod-payroll-dll23sm -x php
```


