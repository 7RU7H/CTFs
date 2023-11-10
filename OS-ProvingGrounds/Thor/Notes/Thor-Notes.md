# Thor Notes

## Data 

IP: 192.168.198.208
OS: Linux
Arch:
Hostname:
DNS: Lite, offs.ec
Domain:  / Domain SID:
Machine Purpose: 
Services: http 80,10000 : webmin 1.962 , 7080 litespeed
Service Languages:
Users: jfoster - probably
Email and Username Formatting:
Credentials:

## Objectives

`\#\# Target Map ![](Thor-map.excalidraw.md)`

#### Machine Connects to list and reason:

[[]] - Reason X

## Data Collected

#### Credentials
```
```

#### HUMINT


#### Solution Inventory Map


#### Todo 


#### Timeline of tasks complete
      

liteerror.png
jsdisabled.png
janefoster.png
litespeedwebserver.png
lite10000gobuster.png
webminmsfexploit.png

https://www.exploit-db.com/exploits/49318

```bash 
echo "192.168.198.208 Lite offs.ec" | sudo tee -a /etc/hosts
```

webminwebmin.png

jfoster : webmin, foster, jfoster, litespeed (plus varients)
admin :admin

ohgreatblockforauth.png

blocked.png

Importing exploits from `searchsploit` into `metasploit` - adapted from [Daniel Redfern video](https://www.youtube.com/watch?v=eWdfr1CcmJc)
```bash
mkdir ~/.msf4/modules/exploits
sudo cp $pathToSearchsploitExploit ~/.msf4/modules/exploits/
sudo chown kali:kali -R ~/.msf4/modules/exploits/
```

importingssexploit.png

ifihavetoguessthepasswordIwilljustmakethisawriteup.png

rescanfound7080.png

morelogins.png

```
-b $statusCodeBlacklist
--exclude-length 
```

Another auth for 7080

https://www.exploit-db.com/exploits/49556


rtfm.png

No simply sqli ib logins

nobruteforcingthe7080passwd.png

infiniteconnectionresetfromwebmin.png

infiiniteresetfrom7080.png

Hint 1 taken: *A webservice has a login page that can be brute forced*

- It is not 10000, because that IP blocks and 7080 10k passwords from rockyou.txt is more than enough time
- Password managling jfoster and other words - todo
- Try jfoster as the account for 7080 - todo