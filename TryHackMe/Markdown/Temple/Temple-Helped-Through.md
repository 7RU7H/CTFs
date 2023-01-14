# Temple Helped-Through

Name: Temple
Date:  13/01/2023
Difficulty:  Hard
Goals:  
- Web Filter Bypass Research
- Flask Research
- Patch an SSTI 
- Test Naabu 
- Finish my other Beyond Roots that are THM machines [[Biblioteca-Helped-Through]], [[Agent-T-Writeup]]
Learnt:
Beyond Root:
- Patch a SSTI
- Patch the Filter and try another bypass

I really enjoy Al's streams. The communal atmosphere where I always learn something and have a laugh at the memes. It also makes the process of hacking machines more fun and real. I think watching Ippsec or reading write ups has some the hardship you face in hacking removed to make it relevant and accessible, but that does not normalise the malding it makes it a self centric phenomena that anomolous  to other when you are trying. I wanted to make sure I finished all my Beyond Roots of the last two boxes because of colds and what better way than one more box on the same site. [Alh4zr3d's Funday Sunday:Temple](https://www.youtube.com/watch?v=-NhqAaRfxEU&list=PLi4eaGO3umboaOqaot7Oi7WszkSWRc4Pi&index=11) interested me as I wanted to re-equant with Flask web apps, patch a SSTI for a Beyond Root. There is also container exploitation and web-filter bypassing and I need to research both as neither are common in encounted activities. 

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

The echo ports 
![](echoechoecho.png)

Ubuntu 18.04.6 LTS OS version disclosure
![](telnetattempt.png)

The fast naabu scan played off.
![](61337webserverroot.png)

Hacking Detected!
![](illegalchars.png)

Al introduces me to [Huntress](https://github.com/huntresslabs) Founded by former NSA Cyber Operators and sell a managed endpoint detection and response (EDR) capabilities as a service. And in relevance to [detect log4shell](https://log4shell.huntress.com/) if it is on the Temple machine. 

[CVE-2021-44228](https://www.huntress.com/blog/rapid-response-critical-rce-vulnerability-is-affecting-java) - Log4Shell
```js
${jndi:ldap://log4shell.huntress.com:1389/$unique_identifier}
```
[Source: Huntress](https://log4shell.huntress.com/))

The robots.txt jebait.
![](tryharderrobot.png)

Sometimes double URL encoding can bypass some filters.

While I was trying understand how to understand [Web filtering](https://en.wikipedia.org/wiki/Web_filtering_in_schools) by reading this wikpedia article,  Al gets help from chat. 
![1000](algetshelpsfromchat.png)
It was one of those machines that I am happy I did not have to learn the painful lesson of infinite directory scanning. I think my "ok" personal policy on doing this mostly because I just do these CTFs and not Bug Bounty is I need to learn stuff and this is not learning. Recon in the back is awesome, but I think I need to adjust to a more Bug Bounty Hunter continous discovery method. Also Al has had probably with feroxbuster missing pages and that it also can basically DDoS a site really quickly. Given these relevations and other relevation:

Be more Tacticool - do not fear the 403 and 302
- Go back to gobuster for inital directories
- Stop, observe and note potential targets to start feroxbuster and ffuf from 
	- What is actually a good target for this and what are likely middle directories that are good to recurse -  Pushing beyond the 
		- Anything where
			- dev, git, backup, bak
- Use ffuf more ffuf vhosts, extensions, POTENTIAL pages
- Do a slow set of feroxbusters scan over to collect and double verify 

I think Al gripes with feroxbuster reflect my own, but I think it more likely that I am not taking advantage of feroxbuster options 

```bash
feroxbuster 
-A # Random agent
-r # follow redirect
# Want to try this:
--smart # Set --extract-links, --auto-tune, --collect-words, and --collect-backups to true
-f 
--rate-limit # default is zero so it will potential dos a server by default.
-g # collecting words sounds awesome 
```

I need to go back to content discovery hellscape box that is [[Bart-Writeup]] and try with a update brain, methodology and less stressed. 

`newacc` does not feature in raft.

![](hurrayfordashboards.png)

The takeaway is doing some heavy compute, golang programming and create a couple of OneWordLists to rule them all. Seclists and AssetNote (the latter I am going off people who make real money) are great, but I actually want not have to run 4-5 scan with differing list and have the troll face from a box creator in my mind while 4 to 5 hours passed away. The box creator is also OSCE3.

User data is reflected on the page.
![](usernamereflectedonpage.png)

How is authenicated user data reflected on the page?
	- Framework?
	- Database?
	- Session-related?

[Return soon](https://www.youtube.com/watch?v=-NhqAaRfxEU&list=PLi4eaGO3umboaOqaot7Oi7WszkSWRc4Pi&index=11)

Before I start the video I want to aleast go the extra mile and test for other vulnerabilities that can occur under reflect input for Question-Based Methodology

## Exploit

## Foothold

## PrivEsc

## Beyond Root

Patch the SSTI vulnerability for the newacc 


Make a wordlists creator in golang for the serious string compute.

OneSeclistDirectoryBustingWordlistToRuleThemAll 