# Networked Writeup
Name: Networked
Date:  
Difficulty:  Easy
Goals:  OSCP Prep
Learnt:

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

This is thje orginal 
![](originalreq.png)

Amusing attempt - "Give me  file upload"
![](kalibackgrounduploadattempt.png)

Page source code pull from: "Where is the file upload"
![](whereitisfileupload.png)

This picture...lmao. [Injecting php into jpegs](https://onestepcode.com/injecting-php-code-to-jpg/) vould be possible if the logic on extension filtering allows for .jpg.php or force it to store it as .php. 

1. Bypass client side mime 
1. Change the mime_content_type, channge the 




## Exploit

## Foothold

## PrivEsc

      
