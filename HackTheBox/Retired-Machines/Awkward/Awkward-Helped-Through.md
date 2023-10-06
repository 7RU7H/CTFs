# Awkward Helped-Through

Name: Awkward
Date:  
Difficulty:  Medium + COVID 
Goals: 
- Learn API hacking Methodology
Learnt:
Beyond Root:
- Do a hour on the prototype of Archive API
- InsiderPHD 

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

This is a weird web application were client side is loading alot the middle-backend code
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

JavaScript skips conditional operator if what is pass is null. Utter madness.
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
```
echo -n $hash | wc -l 

echo -n  |shasum -a $ALG

```
![](crackstation.png)

![](hashlengthtest.png)
Line break account for one of the characters
![](onebecauseoflinebreak.png)

![](christ123no.png)


![](hichris.png)

![](validusertimings.png)

![](54to66.png)

This is another vulnerability - does not require cookie from the refresh button.
![](potentialbruteforceparam.png)

```bash
# Treat as a file
<( seq 0 65535)
```

#### Taking a break..Break in my understanding of APIs with Insider PHD Playlist and making improving a prototype of my own from a couple of years ago

[To learn APIs and Golang I followed along with video](https://www.youtube.com/watch?v=2v11Ym6Ct9Q) and [repository is here](https://github.com/kubucation/go-rollercoaster-api)

## Exploit


## Foothold

## Privilege Escalation

## Beyond Root


