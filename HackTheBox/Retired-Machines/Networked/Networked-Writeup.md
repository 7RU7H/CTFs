# Networked Writeup
Name: Networked
Date:  
Difficulty:  Easy
Goals:  OSCP Prep
Learnt:
- try all alternatives

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Apex/Screenshots/ping.png)

![](ferox.png)

[Nuclei detected a WAF](obsidian://open?vault=CTFs&file=HackTheBox%2FRetired-Machines%2FNetworked%2Fnuclei%2Fwaf-detect-http___10.129.4.156_-apachegeneric)

![](facemash.png)

![](backupdottar.png)

```bash
tar xvf backup.tar
```

![](insidethebackup.png)

lib.php and upload.php a
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

This picture...lmao. [Injecting php into jpegs](https://onestepcode.com/injecting-php-code-to-jpg/) vould be possible if the logic on extension filtering allows for .jpg.php or force it to store it as .php. I really want to be regex with sed. I did, but the regex is . And [this THM room that I learnt regex in cli from](https://tryhackme.com/room/catregex) has been dead for last year, going check my old notes. It used  this https://regex101.com/

`'/^([a-z\-]+\/[a-z0-9\-\.\+]+)(;\s.+)?$/'`
1. `^` matches at beginning of the line 
	1. matches one or more with `+`  chars of  `\-` and a-z all lowercase 
2. Then  matches one or more with `[a-z0-9] and  -.+` with `+`
3. `?` with  `(;\s.+)` match zero or one of the one-character regular expression

https://www.tutorialspoint.com/unix/unix-regular-expressions.htm
https://regex101.com/
https://users.cs.cf.ac.uk/Dave.Marshall/Internet/NEWS/regexp.html

3. Bypass client side mime 
4. Change the mime_content_type, channge the 

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
1. Changed myfile; but possible that I am getting false positives, but if it is not empty is automatic changes it back to "myFile"
![](weird.png)

Been another hour and time is precious. I watched the [Ippsec](https://www.youtube.com/watch?v=H3t3G70bakM) video. I am confused as I place the extension inside the other an it replaced it.
![](imgoingmadted.png)

So research
[explode](https://www.php.net/manual/en/function.explode.php) - Split a string by a string
[array_shift](https://www.php.net/manual/en/function.array-shift.php) - Shift an element off the beginning of array
[implode](https://www.php.net/manual/en/function.implode.php) - Join array elements with a string
![](itplonksitbackontheend.png)

The extension are being compared to the database.

![](aaaaaaaaaaaaaargh.png)

## Exploit

## Foothold

## PrivEsc

      
