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

- [[Book-Notes.md]]
- [[Book-CMD-by-CMDs.md]]

This helped-through was made by reading [ivanitlearning](https://ivanitlearning.wordpress.com/2021/04/17/hackthebox-book/) and following along. Due to it being only about the privilege escalation vector for this box I will put a good 2 hours of practice into hacking the web application
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

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

And now I remember why this is a minefield in noting everything that I have tried.=
## Exploit

## Foothold

## Privilege Escalation

## Post-Root-Reflection  

## Beyond Root


