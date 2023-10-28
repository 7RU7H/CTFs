# Zipper Writeup

Name: Zipper
Date:  
Difficulty:  
Goals:  
Learnt:
Beyond Root:

- [[Zipper-Notes.md]]
- [[Zipper-CMD-by-CMDs.md]]


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

![](gospiderrecon.png)

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

The problem is we  need to then get the link out of each response to then download and check unzip the file and views its contents, which all means script fool script. Given that I think I may have a way of doing this I will not click to Write up button for atleast another hour additionally just to push myself with Python3 and not bash. If I do not do it in a hour then it is just admit defeat. 

No phind or chatgpt
```python
#!/usr/bin/python3

import requests
import re

# Globals TODO - localise!
uploads_upload_pattern = 'uploads/uploads_^[0-9]{10}\.zip'
wordlist =

# Load a file for payloads
with open(wordlist, "r") as f:
	payloads = f.read()
	for i,payload in enumerate(payloads):

# Send request of the correct format replace FUZZ with a line from the payload file
	r = requests.get()
	
# Take the request and extract the link for line containing 'uploads/upload_'
	payload_zipped_path_line = re.findall(uploads_upload_pattern, r.text)
	
# some f string function to cut out the noise for just uploads/upload_NUMS.zip
	
	payload_zipped_path = #  payload_zipped_path_line 
# download the file 
	url_concat_with_payload_path = f"{URL}{payload_zipped_path}"

# get the name in the response
	payload_zip_filename

# TODO set a path of /tmp
	r = requests.get('url_concat_with_payload_path')

# unzip and cat content to stdin
	unzippable_filepath = f"tmp/{payload_zip_filename}"
	# unzip some how and getting the name
exfil_filename = # TODO ?
exfil_filename_with_path
	with open(exfil_filename_with_path, "r") as f:
	    exfil_data = f.read()	
		print(f"ZIP name - Exfil Filename - Payload")
		print(f"{payload_zip_filename} - {} {}")
		print(exfil_data)

exit

```

## Exploit


## Foothold

## PrivEsc

![](Zipper-map.excalidraw.md)

## Beyond Root

Python scripting 

