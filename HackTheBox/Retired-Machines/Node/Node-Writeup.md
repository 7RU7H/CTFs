
Name: Node
Date:  
Difficulty:  Medium
Goals:  OSCP Prep
Learnt:

![](troll.png)

## Recon



The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Node/Screenshots/ping.png)

Somewhere in my brain after learn about apis in basic sense gospider found the `/api/` endpoints

![1000](apiusers.png)
the /latest just include the last three, all non admins:
```
[{"_id":"59a7365b98aa325cc03ee51c","username":"myP14ceAdm1nAcc0uNT","password":"dffc504aa55359b9265cbebe1e4032fe600b64475ae3fd29c07d23223334d0af","is_admin":true},{"_id":"59a7368398aa325cc03ee51d","username":"tom","password":"f0e2e750791171b0391b682ec35835bd6a5c3f7c8d1d0191451ec77b4d75f240","is_admin":false},{"_id":"59a7368e98aa325cc03ee51e","username":"mark","password":"de5a1adf4fedcce1533915edc60177547f1057b61b7119fd130e1f7428705f73","is_admin":false},{"_id":"59aa9781cced6f1d1490fce9","username":"rastating","password":"5065db2df0d4ee53562c650c29bacf55b97e231e3fe88570abc9edd8b78ac2f0","is_admin":false}]
```

```
f0e2e750791171b0391b682ec35835bd6a5c3f7c8d1d0191451ec77b4d75f240

sha256

spongebob

de5a1adf4fedcce1533915edc60177547f1057b61b7119fd130e1f7428705f73

sha256

snowflake

5065db2df0d4ee53562c650c29bacf55b97e231e3fe88570abc9edd8b78ac2f0

Unknown

Not found.
```

![](apiadminbackup.png)

![1000](sha256.png)

`myP14ceAdm1nAcc0uNT : manchester`

There is a backup to download. 
```bash
file myplace.backup
myplace.backup: ASCII text, with very long lines (65536), with no line terminators
```

![](decodebase64.png)

```
cat myplace.backup| base64 -d > myplace.decode
file myplace.decode
myplace.decode: Zip archive data, at least v1.0 to extract, compression method=store
```


![](cracked.png)

`magicword`

![](appjs.png)

![](hashescom.png)

SHA256

`45fac180e9eee72f4fd2d9386ea7033e52b7c740afc3d98a8d0230167104d474` - is a dudls


hashcat mode `1400`

secret 
`the boundless tendency initiates the law.`
`const url 'mongodb://mark:5AYRft73VtFpc84k@localhost:27017/myplace?authMechanism=DEFAULT&authSource=myplace';`

![](versioningjs.png)

![](mongodbversion.png)

## Exploit

## Foothold

## PrivEsc

      
