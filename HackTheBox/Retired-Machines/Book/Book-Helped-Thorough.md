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
## Foothold

## Privilege Escalation

## Post-Root-Reflection  

- And all of this to do a section of the HTB academy and the road there was full of new things!
## Beyond Root



WTF is SQL truncation!?!:
[Medium - r3d-buck3t: bypass-authentication-with-sql-truncation-attack](https://medium.com/r3d-buck3t/bypass-authentication-with-sql-truncation-attack-25a0c33ab87f)




