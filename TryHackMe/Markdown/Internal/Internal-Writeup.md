# Internal Writeup

Name: Internal
Date:  
Difficulty:  Hard
Goals:  
- GLHF on another of TheMayor boxes.. 
Learnt:
- MORE CLI Cool Commands
Beyond Root:
- Unintend SQL injection to retain Writeup status - CVE is 2022 and box is 2020 and its a Offensive Path box from the TheMayor and the last one was evil. I have spent so much time painfully flailing on this machine. I can only justify do this for completionism

- [[Internal-Notes.md]]
- [[Internal-CMD-by-CMDs.md]]

Starting and restarting this Writeup multiple times, this will be the final time. I am already aware the SQL Injection before this attempt. This going in for this final attempt, the this is a TheMayor Box and the last one I did  was not pretty. I almost wanted to run for Senate in the US and filibuster with infinite jibberish out of frustrated madness till I clone myself a proxy to run for Mayor that is more arbitrary and absurd than myself out the mismatch *real* upbringing to oust him... it was a weird and painful experience - [cue battle music to humiliation](https://www.youtube.com/watch?v=ACULtdKEVdY). It made realise I am bad, the box was bad and digging yourself out of the inverse learning curve hole is (and still sort of is continuously going to be a nightmare. Lots of other really like these boxes, but they could all be of CM tribe online surging and back-patting or I just got unlucky with other user trolling me. It could also be that these boxes were before HTB, PG and THM start standardising what a box is actually suppose to be and what the objective actually is with it. Having been blissful unaware of the nerdy-bullshit of unstrategic-mindmeddling of epeen insecurity honeypot-to-fill-the-creator ego for most of my life, the rude awaking to that kind of bullshit that does not exist outside of most *hardcore* Video Games, Specific subculture-forums and Cartoon-Cults. Internally I am *hoping* this will 3 hours maximum.  I live and cope. The beyond root for this is just finishing this. 
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)


```bash
wpscan --url $url --rua -e --api-token $APIKEY -o zeroauth.wpscan
```

Wordlists to try
```bash
/usr/share/wordlists/seclists/Discovery/Web-Content/URLs/urls-wordpress-3.3.1.txt
/usr/share/wordlists/seclists/Discovery/Web-Content/CMS/wordpress.fuzz.txt
/usr/share/wordlists/seclists/Passwords/Honeypot-Captures/wordpress-attacks-july2014.txt
```

Shells if we need any
```bash
# You can reverse shell by editing templates (404.php, footer.php...)
# Theme Editor use a 404 template as it won't break the site.
# Plugin Editor  then go to /wp-content/plugins/<pluginname.php>
# Beware Theme must no be active 
/usr/share/wordlists/seclists/Web-Shells/WordPress
/usr/share/wordlists/Web-Shells/laudanum-1.0/wordpress/templates/shell.php
```

Short cutting to enumerating just one vulnerability - replaced the original I did with -B 
![](shortcuttoenumeverything.png)
One day I hope the Offensive Security Web App Cert just understand how anyone is suppose to do the job that get for a job does what they do in a week. Tiber1ous and the like boggle my brain sometimes. Grep all the potential vulnerabilities found by `wpscan` and when each was fixed:
```bash
cat zeroauth.wpscan | grep 'Title' -A 1
```

![](justAnotherWPsite.png)

![](domaininthesrcbut.png)

Another weirdness
![](nospider.png)

![](nodomainspider.png)

Terrapin BR?
![](nucleinoetags.png)

admin  user - [[CVE-2017-5487-http___internal.thm_wordpress__rest_route=_wp_v2_users_-.md]]

Regardless, version verification is the current objective, either from:
- Forcing SQL Errors on `WP_Query` or `WP_Meta_Query` 
- Deduction, Abduction, ...
- Version indicator on the website

`SE-dork:`  `wordpress 6.0.3` depending on whether that feature was released prior to 2022.

- https://en.wikipedia.org/wiki/Deductive_reasoning

![](wpversion603releasedate.png)

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
Search for posts 

https://developer.wordpress.org/reference/classes/wp_meta_query/
![](actuallyhassqlrelatedinformationinthedocumentation.png)

*[WP_Meta_Query](https://developer.wordpress.org/reference/classes/wp_meta_query/) is a class defined in wp-includes/meta.php that generates the necessary SQL for meta-related queries. It was introduced in Version 3.2.0 and greatly improved the possibility to query posts by custom fields. In the WP core, it’s used in the [WP_Query](https://developer.wordpress.org/reference/classes/wp_query/ "Class Reference/WP Query") and [WP_User_Query](https://developer.wordpress.org/reference/classes/wp_user_query/ "Class Reference/WP User Query") classes, and since Version 3.5 in the [WP_Comment_Query](https://developer.wordpress.org/reference/classes/wp_comment_query/ "Class Reference/WP Comment Query") class. Unless you’re writing a custom SQL query, you should look in the *Custom Field Parameters* section for the corresponding class.*



1. Version
2. Guess the probable SQL query
3. Force an error 

First stop [HackTricks](https://book.hacktricks.xyz/network-services-pentesting/pentesting-web/wordpress)

Wordpress Version `/license.txt` or `/readme.html`, there is a nuclei template I got jebaited by my brainlessness brain almost into actually be able to contribute to something.

![](exactWordpressVerson.png)


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

#### Intended Path to avoid waste even more time on these *special* boxes

#### Unintended retain Writeup Status path

I would really like to do one of the SQL injections myself.

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

## Exploit

## Foothold

## Privilege Escalation

## Post-Root-Reflection  

## Beyond Root

Deduction, ... 

Unintended SQLi above to reatin Writeup status of this box.

Try to turtle a terrapin
https://en.wikipedia.org/wiki/Terrapin_attack
https://jfrog.com/blog/ssh-protocol-flaw-terrapin-attack-cve-2023-48795-all-you-need-to-know/
https://github.com/RUB-NDS/Terrapin-Artifacts