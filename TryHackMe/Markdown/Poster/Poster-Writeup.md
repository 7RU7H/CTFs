# Poster Writeup

Name: Poster
Date:  28/10/2023
Difficulty:  Easy
Goals:  
- Nice 360 points 
- Postgres and Metasploit reminding of existence and usage 
Learnt:
- Burn the words always look for config file 
- Metasploit PostGres exploit shell cannot change directory!
Beyond Root:
- None
## Recon

Because this is a partly guided by questions room I just went straight to Metasploit to finish the room quickly, while I enumerated it normally in the background. Using my questionable ability to read to then use this module to enumerate a password.
![](postgresisnotaswordfish.png)
The main kick in the head for those new is just - what is the default user for the database. Its `postgres`.

Answers
![](msfpostgresmodules.png)

More answers
![](dumpage.png)

Regular `psql` client usage
![](wecanalsobelegitimatelookingwithpsqlclient.png)

Another way other than `nmap` and metasploit to get the version that may seem more normal in terms of network traffic. 
![](serverversion.png)

Dump those hashes with metasploit
![](hashes.png)

We can readfiles with command execution on the postgres database 
![](readfile.png)

Current person goal is to live in the regexes.
```bash
cat creds.txt | awk '{print $2}' | sed 's/^md5//g'
```
![](nomd5stub.png)

Crackstation the hashes
![](sadhashcatnotinrockyou.png)

## Exploit

Now to use the metasploit exploit to get command execution on the database
![](exploitoclock.png)

Success MSF usage I will leave the reader figure which credentials to use 
![](msfshell.png)

Putting two sets of sunglasses on and then..
![](checkwecanruninmemoryandbackgroundashellfor2is1and1isnone.png)

Touching disk and cloth ...
![](touchingdiskandcloth.png)

## Foothold

YIKES
![](darkscredntials.png)

Lesson do not store passwords in plaintext in your home directory or anywhere
![](sshinasdark.png)

## Privilege Escalation

Alsion is running the web server and config.php contain the credentials to run it
![](configforthepass.png)
With reuse `su alison` from the username named dark we can then `su` to root from alison with this password  

And root
![](root.png)

## Post-Root-Reflection  

Never forget the ease of easy ways in and beware the tools.
## Beyond Root

None 

