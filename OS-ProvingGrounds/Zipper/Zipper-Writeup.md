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

I need to discover the LFI. Almost certianly given the way the application retrieve files must be vulnerable some how.
## Exploit

## Foothold

## PrivEsc

![](Zipper-map.excalidraw.md)

## Beyond Root


