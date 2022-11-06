
Name: Granny
Date:  5/11/2022
Difficulty:  Easy
Goals:  OSCP Prep 
Learnt:
- Being too excited by a new tool can be bad 
- DELETE, MKCOL and EXEC Headers are real
- Local files are not uploaded files
- I try to hard and fast
- Need a Pause button on my skull still. 
- Also the wonders of https://lolbas-project.github.io/#/download 

## Recon

![ping](HackTheBox/Retired-Machines/Granny/Screenshots/ping.png)

Nikto finds that the site accepts PUT requests which would then indicate file upload vulnerability that is also unauthenicated  as idicated by nse script vuln: 
![](vuln.png)

From https://techvomit.net/abusing-http-put/ discovered kali supprted tool `davtest`
![](wonderousWEBdav.png)

OSCP friendly! I continued with method described in https://techvomit.net/abusing-http-put/, but it failed. Tried curl again with --upload-file that uses put by default and played around abit. Microsoft to the rescue  [MKCOL](https://learn.microsoft.com/en-us/previous-versions/office/developer/exchange-server-2003/aa142923(v=exchg.65)) with all the head-to-keyboard smashing "how did this work?" must after the `DELETE` key hit the front cortext managed to then imprint some neurological recursive problem solving connection and the subsequent `ENTER`  may have created connect with light wave through the cracks in my skull to use my brain. Having never seen MKCOL it is no actively maintained...
![](declined.png)

*"The WebDAVMKCOL method creates a new collection at the location specified by the Request-Uniform Resource Identifier (URI). When invoked without a request body, the collection will be created without member resources. When used with a request body, you can create members and properties on the collections or members."*

## Exploit

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

Went on massive research hunt for how to make dynamic C# for a reverse shell executable html page. I tried changin asp webshells. Made a boundary and made it .txt tried to execute the asp.txt webshell but got 

![](txtaspfail.png)

Given time requirements these day to learn and practice everything. I peaked at at around 9 mins just to guess [IT Security labs](https://www.youtube.com/watch?v=7l4KgYsBNyM), and he mention move and copying, which I saw at the bottom of one of the webshells on tennc. I instantly close the video as I want to claim this is a writeup. I am just biased when I look at foreign languages aaaaaaaas I can barely speak English. I also why MOVE and COPY would affect excution of the file if it was already not executable before sprung to mind. Location it is not treated as a upload.

![](errorsongranny.png)

[This webshell has always worked](https://github.com/tennc/webshell/blob/master/asp/webshell.asp) tried making it aspx
![](aspxsad.png)

Went to peak once more [IT Security labs](https://www.youtube.com/watch?v=7l4KgYsBNyM), he use curl not burp. [ASP is not ASPX](https://stackoverflow.com/questions/4462559/difference-between-asp-and-aspx-pages), but I still tested with both. So maybe I made a type in burp or use the boundary on the request with it and screwed with how the raw data is uploaded.

```bash
curl http://10.129.95.234/ -X PUT -T cmd.txt
curl http://10.129.95.234/cmd.txt -X MOVE -H "Destination: http://10.129.95.234/cmd.asp"
```

![](wtf.png)

It is still slow.
![](nastysysteminfo.png)

It an xp box!
![](xp.png)

## Foothold && PrivEsc

I tried the MSFvenom builder from [Hack-Tools](https://github.com/LasCC/Hack-Tools) only issue is prints comma between everything.

```powershell
chdir
c:\windows\system32\inetsrv
```

Certutil(Got the), bitsadmin, wget, curl and powershell all failed. We are just system any way. Some HTB boxes are just weird as tried directories for flags

certreq - no post
diantz.exe no shares I do not know how to make 1dos 

Considered subst.exe remote share but that has never come up ever in anything I have read. Then I fixated too long and peaked again, use aspx shells through the same method. Changing it to helpthrough

![900](fool.png)

With all that I rather eye openingly shocked at myself. I need a pause button. As going back to the video, its all meterpreter and enumerate with metasploit module and that just r another these boxes are unsupported. [Even 0xDF](https://0xdf.gitlab.io/2019/03/06/htb-granny.html) use meterpreter! AAAARGH

![](metasploitable.png)

And I did some cleanup for the sake of being a good person in that regard, might aswell write to remember to remember just in the future. I learn som HTTP and old HTBs that are just... Also the wonders of https://lolbas-project.github.io/#/download 