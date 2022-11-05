
Name: Granny
Date:  
Difficulty:  Easy
Goals:  OSCP Prep 
Learnt:
- Being too excited by a new tool can be bad 
- DELETE, MKCOL and EXEC Headers are real

## Recon

![ping](HackTheBox/Retired-Machines/Granny/Screenshots/ping.png)

Nikto finds that the site accepts PUT requests which would then indicate file upload vulnerability that is also unauthenicated  as idicated by nse script vuln: 
![](vuln.png)

From https://techvomit.net/abusing-http-put/ discovered kali supprted tool `davtest`
![](wonderousWEBdav.png)

OSCP friendly! I continued with method described in https://techvomit.net/abusing-http-put/, but it failed. Tried curl again with --upload-file that uses put by default and played around abit. Microsoft to the rescue  [MKCOL](https://learn.microsoft.com/en-us/previous-versions/office/developer/exchange-server-2003/aa142923(v=exchg.65)) with all the head-to-keyboard smashing "how did this work?" must after the `DELETE` key hit the front cortext managed to then imprint some neurological recursive problem solving connection and the subsequent `ENTER`  may have created connect with light wave through the cracks in my skull to use my brain. Having never seen MKCOL it is no actively maintained...
![](declined.png)

*"The WebDAVMKCOL method creates a new collection at the location specified by the Request-Uniform Resource Identifier (URI). When invoked without a request body, the collection will be created without member resources. When used with a request body, you can create members and properties on the collections or members."*

Read some [Portswigger](https://portswigger.net/web-security/file-upload)

![](testdir.png)

It exists
![](testexists.png)

I did not like my test php code page in the test directory, but like my shell.

![](what.png)
Remove the `If-None-Match` and `If-Modified-Since`, because why would want to conditional upload something.

![](ahaaaaaaaaaaah.png)

Then both aren't found:
![](stillsadnopage.png)
Then reading that the only executables are html and txt... I concluding was too excited to read what did twice. So we need to then turn both into html pages. Therefore I must use the most second, after hackronomicon of Javascript that exists somewhere, most horrific and evil manual I know... the [PHP.net](https://www.php.net/manual/en/language.basic-syntax.phpmode.php) 

Bad characters abound as html and php use the same lexical operators for different operability. 

- Make a reverse shell executable html page.



## Exploit

## Foothold

## PrivEsc

      
