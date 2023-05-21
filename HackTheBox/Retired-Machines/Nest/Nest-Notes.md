# Nest Notes

## Data 

IP: 10.10.10.178
OS: Windows 6.1 Build 7601
Hostname:  HTB-NEST
Domain:  HTB-NEST
Domain SID:
Machine Purpose: 
Services:  445 ((SMBv1:False) (signing:False)), 4386 - HQK Reporting Service V1.2
Service Languages:
Users:
Credentials:

```
Username: TempUser
Password: welcome2019
```


## Objectives

## Target Map

![](Nest-map.excalidraw.md)

## Solution Inventory Map


### Todo 

Make Excalidraw

### Done

smbmapworks.png

nicespider.png

```bash
smb: \> prompt off 
smb: \> recurse on 
smb: \> mget * # Download everything instead of manually 
```


tempuserandpassword.png


secureshareaccess.png


allnowinsecure.png 

Sort of... we lack access 
```json
{
  "Data": {
    "IT/Configs/Adobe/Options.txt": {
      "atime_epoch": "2019-08-07 20:20:09",
      "ctime_epoch": "2019-08-07 20:20:09",
      "mtime_epoch": "2019-08-07 20:20:09",
      "size": "0 Bytes"
    },
    "IT/Configs/Adobe/editing.xml": {
      "atime_epoch": "2019-08-07 20:20:09",
      "ctime_epoch": "2019-08-07 20:20:09",
      "mtime_epoch": "2021-07-21 19:47:12",
      "size": "246 Bytes"
    },
    "IT/Configs/Adobe/projects.xml": {
      "atime_epoch": "2019-08-07 20:20:09",
      "ctime_epoch": "2019-08-07 20:20:09",
      "mtime_epoch": "2019-08-07 20:20:09",
      "size": "258 Bytes"
    },
    "IT/Configs/Adobe/settings.xml": {
      "atime_epoch": "2019-08-07 20:20:09",
      "ctime_epoch": "2019-08-07 20:20:09",
      "mtime_epoch": "2019-08-07 20:20:09",
      "size": "1.24 KB"
    },
    "IT/Configs/Atlas/Temp.XML": {
      "atime_epoch": "2011-10-10 12:33:41",
      "ctime_epoch": "2003-06-11 08:38:21",
      "mtime_epoch": "2021-07-21 19:47:04",
      "size": "1.34 KB"
    },
    "IT/Configs/Microsoft/Options.xml": {
      "atime_epoch": "2019-08-07 20:23:26",
      "ctime_epoch": "2019-08-07 20:23:26",
      "mtime_epoch": "2019-08-07 20:23:26",
      "size": "4.49 KB"
    },
    "IT/Configs/NotepadPlusPlus/config.xml": {
      "atime_epoch": "2019-08-07 20:24:24",
      "ctime_epoch": "2019-08-07 20:24:24",
      "mtime_epoch": "2021-07-21 19:47:13",
      "size": "6.3 KB"
    },
    "IT/Configs/NotepadPlusPlus/shortcuts.xml": {
      "atime_epoch": "2019-08-07 20:31:37",
      "ctime_epoch": "2019-08-07 20:31:37",
      "mtime_epoch": "2021-07-21 19:47:15",
      "size": "2.06 KB"
    },
    "IT/Configs/RU Scanner/RU_config.xml": {
      "atime_epoch": "2019-08-07 21:01:13",
      "ctime_epoch": "2019-08-07 21:01:13",
      "mtime_epoch": "2021-07-21 19:47:14",
      "size": "270 Bytes"
    },
    "Shared/Maintenance/Maintenance Alerts.txt": {
      "atime_epoch": "2019-08-06 00:01:35",
      "ctime_epoch": "2019-08-06 00:01:35",
      "mtime_epoch": "2021-07-21 19:47:05",
      "size": "48 Bytes"
    },
    "Shared/Templates/HR/Welcome Email.txt": {
      "atime_epoch": "2019-08-05 22:59:53",
      "ctime_epoch": "2019-08-05 22:59:53",
      "mtime_epoch": "2021-07-21 19:47:12",
      "size": "425 Bytes"
    }
  },
  "Secure$": {},
  "Users": {
    "TempUser/New Text Document.txt": {
      "atime_epoch": "2019-08-07 23:55:56",
      "ctime_epoch": "2019-08-07 23:55:56",
      "mtime_epoch": "2021-07-21 19:47:15",
      "size": "0 Bytes"
    }
  }
}
```

Xargs is the CLI-wind
```bash
find . -type f -name *.xml 2>/dev/null | xargs -I {} cat {}
```


password.png

From the RU_config.xml, RU scanner is a wireless scanner probably for scanner barcodes on IT assets
```bash
c.smith
# Found:
fTEzAfYDoz1YzkqhQkH6GQFYKp1XY5hm7bjOP86yYxE=
# base64 -d
}13=XJBAX*Wcf?βc
# To uppercase with cyberchef
FTEZAFYDOZ1YZKQHQKH6GQFYKP1XY5HM7BJOP86YYXE=
1V9Xd@X(WcN?Θaq
# To lowercase with cyberchef
ftezafydoz1yzkqhqkh6gqfykp1xy5hm7bjop86yyxe=
~׳i=rJHzq˘fβ

# Considering spray with 2019 append passwords
fTEzAfYDoz1YzkqhQkH6GQFYKp1XY5hm7bjOP86yYxE=
}13=XJBAX*Wcf?βc
FTEZAFYDOZ1YZKQHQKH6GQFYKP1XY5HM7BJOP86YYXE=
# This breaks CME:  1V9Xd@X(WcN?Θaq
ftezafydoz1yzkqhqkh6gqfykp1xy5hm7bjop86yyxe=
# This breaks CME: ~׳i=rJHzq˘fβ

```

The password does not work for smb 
carlsmith.png

usersshare.png

Search Engine Dorking my way out of spoilers ... 
```
RU Scanner passwords -HTB -HackTheBox
```

spoiledmyself.png


hkreportingrabbithole.png

Maybe we can traverse directories or set a betetr directory
setdirtowin.png

We cant actual runquery on anything, probably need a password
wecanlisteverythingthough.png

Documentation for this reporting software seems elusive. The password in either format does not work for smb or HQK.